<?php
namespace services\blockster;

class Core
{
    protected static $instance;
    protected function __clone() {}
    protected function __wakeup() {}




    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }




    protected $router = array();
    protected $eventor = array();
    protected function __construct()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $host = $_SERVER['HTTP_HOST'];
        if ($uri != '/' && substr($uri, -1) == '/') {
            header('Location: '.rtrim($uri, '/'), true, 301);
            exit();
        }
        if (!defined('SITE_URL')) {
            $siteUrl = ((!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://').$host;
            define('SITE_URL', $siteUrl);
        }
        $this->eventor = new \services\eventor\Eventor(__DIR__.DS.'events.json');
        $this->router = new \services\router\Router(__DIR__.DS.'routing.json');
        $this->eventor->fire('onSystemStart', $this);
    }




    protected function trimGetParams($u)
    {
        if (false !== $p = strpos($u, '?')) $u = substr($u, 0, $p);
        if (false !== $p = strpos($u, '#')) $u = substr($u, 0, $p);
        return $u;
    }




    protected $positions = array();
    protected $theme;
    protected $loadedModels = array();
    protected $blockStack = array();
    protected $messageLog = array();
    public function exitResponse($httpQuery=array())
    {
        for ($i=ob_get_level(); $i>0; $i--) ob_get_clean();
        if (!isset($httpQuery['uri'])) $httpQuery['uri'] = $this->trimGetParams($_SERVER['REQUEST_URI']);
        if (!isset($httpQuery['host'])) $httpQuery['host'] = $_SERVER['HTTP_HOST'];
        if (!isset($httpQuery['method'])) $httpQuery['method'] = $_SERVER['REQUEST_METHOD'];
        if (!isset($httpQuery['status'])) $httpQuery['status'] = 200;

        $route = $this->router->findRoute($httpQuery);
        if (empty($route) && $httpQuery['status'] == 404) exit('Routing error: Nothing to show. Please check routing map.');
        if (empty($route)) $this->exitResponse(array('status' => 404));
        if (!isset($route['theme']) || !is_dir(ROOT_DIR.DS.'themes'.DS.$route['theme'])) exit('Routing error: Theme directory is not exists.');
        $this->theme = $route['theme'];
        if (!isset($route['block'])) exit('Routing error: Main block is not specified.');
        $this->eventor->fire('onRoutingDone', $route);

        $this->positions = $route['positions'];
        $this->eventor->addEvents($route['events']);
        $this->blockStack = array();
        $this->messageLog = array();
        $response = $this->loadBlock($route['block']['name'], $route['block']['params'], $route['block']['template']);
        $this->eventor->fire('onExit', $response);

        $httpStatusCodes = array(
            404 => 'Not Found',
            403 => 'Forbidden'
        );
        if (in_array($httpQuery['status'], $httpStatusCodes)) {
            $sapiName = php_sapi_name();
            $s = $httpQuery['status'].' '.$httpStatusCodes[$httpQuery['status']];
            if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
                header('Status: '.$s);
            } else {
                header($_SERVER['SERVER_PROTOCOL'].' '.$s);
            }
        }
        exit($response);
    }




    public function getThemeName()
    {
        return $this->theme;
    }




    public function getThemeUrl()
    {
        return SITE_URL.'/themes/'.$this->theme;
    }




    public function loadBlock($name, $params, $template)
    {
        $args = compact('name', 'params', 'template');
        $this->eventor->fire('onLoadBlock', $args);

        $a = explode('::', $name);
        $cacheDir = ROOT_DIR.DS.'temp'.DS.'cache'.DS.$a[0];
        $tplDir = ROOT_DIR.DS.'themes'.DS.$this->theme.DS.$a[0];
        $action = isset($a[1])? 'action_'.$a[1] : 'action_default';
        $namespace = str_replace('/', '\\', DS.'blocks'.DS.$a[0]);
        $controllerClass = $namespace.'\\Controller';

        //test on fatal errors
        if (!class_exists($controllerClass)) {
            trigger_error('The Controller class of block "'.$name.'" is not defined ' , E_USER_WARNING);
            return false;
        }
        if (!method_exists($controllerClass, $action)) {
            trigger_error('The '.$action.' method is not defined in Controller class of block "'.$name.'"' , E_USER_WARNING);
            return false;
        }

        //load View
        $viewClass = $namespace.'\\View';
        if (!class_exists($viewClass)) $viewClass = '\\blocks\\View';
        $view = new $viewClass($tplDir);
        if (!empty($template)) $view->setTemplate($template);
        
        //load Model
        $modelClass = $namespace.'\\Model';
        if (isset($this->loadedModels[$modelClass])) {
            $model = $this->loadedModels[$modelClass];
        } elseif (class_exists($modelClass)) {
            $model = $this->loadedModels[$modelClass] = new $modelClass();
        } else {
            $model = null;
        }
        
        //load Controller
        $controller = new $controllerClass($view, $model);

        //put the block into stack
        $this->blockStack[] = array(
            'name' => $name,
            'controller' => $controller,
            'cacheDir' => $cacheDir,
            'cacheFile' => '',
            'cacheExists' => false        
        );

        //call action of controller
        $output = $controller->$action($params);

        //pop the block from stack
        $b = array_pop($this->blockStack);

        if (!empty($b['cacheFile'])) if ($b['cacheExists']) {
            // use cache
            $f = $b['cacheDir'].DS.$b['cacheFile'];
            $cache = json_decode(file_get_contents($f), true);
            $output = $cache['output'];
            foreach ($cache['messages'] as $m) $this->sendMessage($m['destination'], $m['message'], $m['param']);
        } else {
            // write cache
            $cache = array(
                'output' => $output,
                'messages' => $this->messageLog
            );
            if (!is_dir($b['cacheDir'])) mkdir($b['cacheDir'], 0700, true);
            $f = $b['cacheDir'].DS.$b['cacheFile'];
            file_put_contents($f, json_encode($cache));
            $this->messageLog = array();
        }

        $this->eventor->fire('onBlockOutput', $output);
        return $output;
    }




    public function useCache($file, $lifetime=3600)
    {
        if (empty($file) || $lifetime < 60 || empty($this->blockStack)) return false;
        $n = count($this->blockStack) - 1;

        $this->blockStack[$n]['cacheFile'] = $file;
        $f = $this->blockStack[$n]['cacheDir'].DS.$file;

        if (file_exists($f) && filemtime($f)+$lifetime > time()) {
            $this->blockStack[$n]['cacheExists'] = true;
            return true;
        } else {
            $this->blockStack[$n]['cacheExists'] = false;
            @unlink($f);
            return false;
        }
    }




    public function sendMessage($destination, $message, $param='')
    {
        $this->messageLog[] = compact('destination', 'message', 'param');
        foreach ($this->blockStack as $b) if ($b['name'] == $destination) {
            if (method_exists($b['controller'], 'acceptMessage')) $b['controller']->acceptMessage($message, $param);
            return;
        }
    }




    public function loadPosition($posName)
    {
        if (isset($this->positions[$posName])) {
            $blocks = $this->positions[$posName];
            $output = '';
            foreach ($blocks as $block) {
                $output .= $this->loadBlock(
                    $block['name'],
                    $block['params'],
                    $block['template']
                );
            }
            return $output;
        }
    }
}