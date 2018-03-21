<?php
namespace core;

class SystemException extends \Exception{}

class System
{
    private static $instance;
    private $router;
    private $translator;
    private $eventBus;
    private $dbh;
    private $positions = array();
    private $theme;
    private $themeUrl;
    private $lang;
    private $moduleStack = array();
    private $msgLog = array();


    private function __clone() {}
    private function __wakeup() {}


    public function __get($name)
    {
        if (in_array($name, ['dbh', 'eventBus', 'theme', 'themeUrl', 'lang'])) {
            return $this->$name;
        }
    }


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    private function __construct()
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
        $this->router = new Router('routing.json');
        $this->translator = new Translator;
        $this->eventBus = new EventBus;
    }




    private function trimGetParams($u)
    {
        if (false !== $p = strpos($u, '?')) $u = substr($u, 0, $p);
        if (false !== $p = strpos($u, '#')) $u = substr($u, 0, $p);
        return $u;
    }






    public function exitResponse($httpQuery=array())
    {
        static $recursionCounter = 0;
        $recursionCounter++;

        if ($recursionCounter > 1 && ob_get_level() > 0) {
            ob_end_clean();
            $this->moduleStack = array();
            $this->msgLog = array();
            $this->positions = array();
        } elseif ($recursionCounter > 10) {
            throw new SystemException('Error in function exitResponse. Recursion is too deep.');
        }

        $this->eventBus->dispatchEvent('onSystemStart');

        if (!isset($httpQuery['uri'])) $httpQuery['uri'] = $this->trimGetParams($_SERVER['REQUEST_URI']);
        if (!isset($httpQuery['host'])) $httpQuery['host'] = $_SERVER['HTTP_HOST'];
        if (!isset($httpQuery['method'])) $httpQuery['method'] = $_SERVER['REQUEST_METHOD'];
        if (!isset($httpQuery['status'])) $httpQuery['status'] = 200;

        $route = $this->router->findRoute($httpQuery);
        if (empty($route) && $httpQuery['status'] == 404) {
            throw new SystemException('Error in function exitResponse. Nothing to show. Please check routing map.');
        }
        if (empty($route)) $this->exitResponse(array('status' => 404));

        $route = $this->eventBus->dispatchEventFilter('onRouting', $route);
        if (!isset($route['module'])) {
            throw new SystemException('Error in function exitResponse. Root module is not specified.');
        }
        if (!isset($route['theme']) || !is_dir(ROOT_DIR.DS.'themes'.DS.$route['theme'])) {
            throw new SystemException('Error in function exitResponse. Theme directory is not specified or exists.');
        }
        $this->theme = $route['theme'];
        $this->themeUrl = SITE_URL.'/themes/'.$this->theme;
        
        
        if (isset($route['events'])) $this->eventBus->importEventHandlers($route['events']);

        if (isset($route['data'])) {
            $this->dbh = new Sqlite('data'.DS.$route['data']);
        } else {
            $this->dbh = new Sqlite('data'.DS.'default.db');
        }
        
        if (isset($route['lang'])) {
            $l = $route['lang'];
        } else {
            // get language from browser's info
            preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/i', $_SERVER["HTTP_ACCEPT_LANGUAGE"], $matches);
            $langs = array_combine($matches[1], $matches[2]);
            foreach ($langs as $n => $v) $langs[$n] = $v ? $v : 1;
            arsort($langs);
            $l = key($langs);
        }
        $this->lang = $this->dbh->row('SELECT * FROM languages WHERE isoCode=?', array($l));
        if (!$this->lang) $this->lang = $this->dbh->row('SELECT * FROM languages WHERE isoCode=?', explode('-', $l));
        if (!$this->lang) $this->lang = $this->dbh->row("SELECT * FROM languages WHERE isoCode='en'");

        if (isset($route['positions'])) $this->positions = $route['positions'];

        $this->eventBus->dispatchEvent('onRoutingDone');
        $response = $this->loadModule($route['module']['name'], $route['module']['params']);
        $response = $this->eventBus->dispatchEventFilter('onExit', $response);

        $httpStatusCodes = array(
            404 => 'Not Found',
            403 => 'Forbidden'
        );
        if (isset($httpStatusCodes[$httpQuery['status']])) {
            $sapiName = php_sapi_name();
            $s = $httpQuery['status'].' '.$httpStatusCodes[$httpQuery['status']];
            if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
                header('Status: '.$s);
            } else {
                header($_SERVER['SERVER_PROTOCOL'].' '.$s);
            }
        }

        $t = round(1000*(microtime(true) - CMS_START), 3);
        switch (gettype($response)) {
            case 'string':
            if (strpos($response, '<!DOCTYPE html>') !== false) {
                header('Content-Type: text/html');
                $response .= "\n<!-- Response generation time: $t ms -->";
            } else {
                header('Content-Type: text/plain');
            }
            break;

            case 'array':
            header('Content-Type: text/json');
            $response['responseGenerationTime'] = $t;
            $response = json_encode($response);
            break;
        }
        exit($response);
    }






    public function getUrl($destination)
    {
        return $this->router->getUrl($destination);
    }






    public function isModuleExists($name) {
        $a = explode('::', $name);
        $action = isset($a[1])? 'action_'.$a[1] : 'action_default';
        $namespace = str_replace('/', '\\', DS.'modules'.DS.$a[0]);
        $controllerClass = $namespace.'\\Controller';
        return class_exists($controllerClass) && method_exists($controllerClass, $action);
    }





    public function loadModule($name, $params)
    {
        static $recursionCounter = 0;
        $recursionCounter++;
        if ($recursionCounter > 25) {
            throw new SystemException('Error in function loadModule. Recursion is too deep.');
        }

        $a = explode('::', $name);
        $cacheDir = ROOT_DIR.DS.'temp'.DS.'cache'.DS.$a[0];
        $tplDir = ROOT_DIR.DS.'themes'.DS.$this->theme.DS.$a[0];
        $action = isset($a[1])? 'action_'.$a[1] : 'action_default';
        $namespace = str_replace('/', '\\', DS.'modules'.DS.$a[0]);
        $controllerClass = $namespace.'\\Controller';

        //test on fatal errors
        if (!class_exists($controllerClass)) {
            throw new SystemException('Error in function loadModule. The Controller class of module "'.$name.'" is not defined');
        }
        if (!method_exists($controllerClass, $action)) {
            throw new SystemException('Error in function loadModule. The '.$action.' method is not defined in Controller class of module "'.$name.'"');
        }

        //put the module into stack
        $this->moduleStack[] = array(
            'tplDir' => $tplDir,
            'controller' => null,
            'cacheDir' => $cacheDir,
            'cacheFile' => '',
            'cacheExists' => false        
        );

        //load View
        $viewClass = $namespace.'\\View';
        if (!class_exists($viewClass)) $viewClass = '\\modules\\View';
        $view = new $viewClass($tplDir);
        
        //load Model
        $modelClass = $namespace.'\\Model';
        if (class_exists($modelClass)) {
            $model = new $modelClass();
        } else {
            $model = null;
        }
        
        //load Controller
        $controller = new $controllerClass($view, $model);
        $depth = count($this->moduleStack) - 1;
        $this->moduleStack[$depth]['controller'] = $controller;
        $this->eventBus->dispatchEvent('onModuleLoad', compact('depth', 'name', 'params'));

        //call action of controller
        $output = $controller->$action($params);

        //pop the module from stack
        $m = array_pop($this->moduleStack);

        if (!empty($m['cacheFile'])) if ($m['cacheExists']) {
            // read cache
            $f = $m['cacheDir'].DS.$m['cacheFile'];
            $cache = unserialize(file_get_contents($f));
            $output = $cache['output'];
            foreach ($cache['messages'] as $m) $this->broadcastMessage($m['message'], $m['param']);
        } else {
            // write cache
            $cache = array(
                'output' => $output,
                'messages' => $this->msgLog
            );
            if (!is_dir($m['cacheDir'])) mkdir($m['cacheDir'], 0700, true);
            $f = $m['cacheDir'].DS.$m['cacheFile'];
            file_put_contents($f, serialize($cache));
            $this->msgLog = array();
        }

        $output = $this->eventBus->dispatchEventFilter('onModuleOutput', $output);
        $recursionCounter--;
        return $output;
    }




    public function loadPosition($posName)
    {
        if (isset($this->positions[$posName])) {
            $modules = $this->positions[$posName];
            $output = '';
            foreach ($modules as $module) {
                $output .= $this->loadModule(
                    $module['name'],
                    $module['params']
                );
            }
            return $output;
        }
    }





    public function useCache($file, $lifetime=3600)
    {
        if (empty($file) || $lifetime < 60 || empty($this->moduleStack)) return false;
        $n = count($this->moduleStack) - 1;

        $this->moduleStack[$n]['cacheFile'] = $file;
        $f = $this->moduleStack[$n]['cacheDir'].DS.$file;

        if (file_exists($f) && filemtime($f)+$lifetime > time()) {
            $this->moduleStack[$n]['cacheExists'] = true;
            return true;
        } else {
            $this->moduleStack[$n]['cacheExists'] = false;
            @unlink($f);
            return false;
        }
    }




    public function broadcastMessage($message, $param='')
    {
        $this->msgLog[] = compact('message', 'param');
        for ($i = count($this->moduleStack)-1; $i>=0; $i--) {
            $m = $this->moduleStack[$i];
            if (method_exists($m['controller'], 'acceptMessage')) $m['controller']->acceptMessage($message, $param);
        }
    }





    private function getThisMoFile() {
        if (empty($this->moduleStack)) {
            throw new SystemException('Translation possible only in context of module.');
        }
        $moFile = $this->moduleStack[count($this->moduleStack)-1]['tplDir'].DS.'locale'.DS.$this->lang['isoCode'].'.mo';
        if (!file_exists($moFile)) {
            $l = explode('-', $this->lang['isoCode']);
            $moFile = $this->moduleStack[count($this->moduleStack)-1]['tplDir'].DS.'locale'.DS.$l[0].'.mo';
        }
        return $moFile;
    }




    public function translate($msg1, $msg2=null, $n=null)
    {
        $moFile = $this->getThisMoFile();
        if (is_null($msg2) && is_null($n)) {
            return $this->translator->translateSingular($moFile, $msg1);
        } elseif (is_null($n)) {
            return $this->translator->translateSingularInContext($moFile, $msg1, $msg2);
        } else {
            return $this->translator->translatePlural($moFile, $msg1, $msg2, $n);
        }
    }




    public function getTranslatorForJs() {
        $moFile = $this->getThisMoFile();
        return $this->translator->getTranslatorForJs($moFile);
    }

}