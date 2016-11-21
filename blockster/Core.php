<?php
namespace blockster;

class Core
{
    private $preparedBlocks;
    private $router;
    private $eventor;
    private $page;
    private $route = array();
    private $accessLevel;

    private function __clone() {}
    private function __wakeup() {}

    private static $instance;
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $url = $this->prepareUrl($_SERVER['REQUEST_URI']);
        if ($url != '/' && substr($url, -1) == '/') {
            header('Location: '.substr($_SERVER['REQUEST_URI'], 0, strlen($_SERVER['REQUEST_URI'])-1), true, 301);
            die();
        }
        session_start();
        $this->preparedBlocks = require(__DIR__.'/preparedBlocks.php');
        $this->router = new \services\router\Router('mainRoutes.php');
        if ($this->router->getRoute('error404') == false) die('The "error404" route does not exists'); 
        if ($this->router->getRoute('error403') == false) die('The "error403" route does not exists'); 
        $this->eventor = new \services\eventor\Eventor('mainEvents.php');
        $this->eventor->fire('onSystemStart');
    }

    private function prepareUrl($URL)
    {
        $u = (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
        $u .= $_SERVER['HTTP_HOST'].$URL;
        if (false !== $p = strpos($u, '?')) $u = substr($u, 0, $p);
        if (false !== $p = strpos($u, '#')) $u = substr($u, 0, $p);
        $u = str_replace(SITE_URL, '', $u);
        if (strpos($u, 'http://') !== false || strpos($u, 'https://') !== false) {
            die('Constant SITE_URL defined wrong.');
        }
        return $u;
    }

    private function getText($routeQuery, $useSearch)
    {
        if ($useSearch) {
            // here it is assumed routeQuery contains URL 
            $this->route = $this->router->findRoute($this->prepareUrl($routeQuery));
        } else {
            // here routeQuery contains key which value is in routes array
            $this->route = $this->router->getRoute($routeQuery);
        }

        if ($this->route === false || !file_exists(ROOT_DIR.$this->route['template'])) {
            $this->error404();
        }

        $this->eventor->mergeEvents($this->route['events']);
        $this->eventor->fire('onRouteSelected', $this->route);
        if (!is_null($this->page)) ob_end_clean();
        $this->page = new Page($this->route['template']);
        $this->eventor->fire('onPageCreated', $this->page);
        $html = $this->page->render();
        $this->eventor->fire('onPageRendered', $html);
        $this->eventor->fire('onSystemExit');
        return $html;
    }


////////////////////////////////////////////////////////////////////////////////
// retrieving HTML methods
////////////////////////////////////////////////////////////////////////////////

    public function printPage()
    {
        exit($this->getText($_SERVER['REQUEST_URI'], true));
    }


    public function error404()
    {
        $sapiName = php_sapi_name();
        if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
            header('Status: 404 Not Found');
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
        exit($this->getText('error404', false));
    }


    public function error403()
    {
        $sapiName = php_sapi_name();
        if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
            header('Status: 403 Forbidden');
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        }
        exit($this->getText('error403', false));
    }


    public function redirect($URL, $useHeader=true)
    {
        if ($useHeader) {
            header('Location: '.$URL);
            die();
        } else {
            exit($this->getText($URL, true));
        }
    }


    public function gotoRoute($routeKey)
    {
        exit($this->getText($routeKey, false));
    }



////////////////////////////////////////////////////////////////////////////////
// loading blocks methods
////////////////////////////////////////////////////////////////////////////////

    public function executeAction($blockName, $params=array())
    {
        $a = explode('::', $blockName);
        $blockDir = '/blocks/'.$a[0];
        $action = isset($a[1])? $a[1] : 'actionIndex';
        $namespace = str_replace('/', '\\', $blockDir);
        $controllerClass = $namespace.'\\Controller';

        if (!class_exists($controllerClass)) {
            trigger_error('The Controller class not defined for block "'.$blockName.'"' , E_USER_WARNING);
            return false;
        }

        if (!method_exists($controllerClass, $action)) {
            trigger_error('The '.$action.' method not defined in Controller class of block "'.$blockName.'"' , E_USER_WARNING);
            return false;
        }

        $modelClass = $namespace.'\\Model';
        $model = class_exists($modelClass) ? new $modelClass() : null;

        $controller = new $controllerClass(null, $model);
        $output = $controller->$action($params);

        $model = null;
        $controller = null;

        return $output;
    }


    public function loadBlock($blockName, $params=array(), $imposedTemplate='')
    {
        $funcArgs = compact('blockName', 'params', 'imposedTemplate');
        $this->eventor->fire('onLoadBlock', $funcArgs);
        extract($funcArgs);

        $a = explode('::', $blockName);
        $blockDir = '/blocks/'.$a[0];
        $cacheDir = '/cache/'.$a[0];
        $action = isset($a[1])? $a[1] : 'actionIndex';
        $namespace = str_replace('/', '\\', $blockDir);
        $controllerClass = $namespace.'\\Controller';

        //test on fatal errors
        if (!class_exists($controllerClass)) {
            trigger_error('The Controller class is not defined for block "'.$blockName.'"' , E_USER_WARNING);
            return false;
        }

        if (!method_exists($controllerClass, $action)) {
            trigger_error('The '.$action.' method is not defined in Controller class of block "'.$blockName.'"' , E_USER_WARNING);
            return false;
        }

        //get info about cache for this block
        $cacheControllerClass = $namespace.'\\CacheController';
        if (class_exists($cacheControllerClass) && method_exists($cacheControllerClass, $action)) {
            $cache = $cacheControllerClass::$action($params, $imposedTemplate);
            $cache['file'] = ROOT_DIR.$cacheDir.'/'.$cache['file'];
        } else {
            $cache = array(
                'file' => '',
                'lifetime' => 0,
                'use' => false,
                'invalidate' => false,
                'update' => false
            );
        }
        
        //read cache file
        if ($cache['use'] && file_exists($cache['file'])) {
            $output = require($cache['file']);
            if (time() > $output['expires']) {
                unlink($cache['file']);
                unset($output);
            }
        }

        //delete cache file
        if ($cache['invalidate']) {
            if (file_exists($cache['file'])) unlink($cache['file']);
        }

        //execute module
        if (!isset($output)) {
            $viewClass = $namespace.'\\View';
            if (!class_exists($viewClass)) $viewClass = '\\proto\\View';
            $view = new $viewClass($blockDir.'/templates', $imposedTemplate);
            
            $modelClass = $namespace.'\\Model';
            $model = class_exists($modelClass) ? new $modelClass() : null;

            $controller = new $controllerClass($view, $model);
            $output = $controller->$action($params);

            $view = null;
            $model = null;
            $controller = null;

            //set cache timestamp
            if ($cache['update'] && is_array($output)) {
                $output['expires'] = time() + $cache['lifetime'];
            }
        }

        $this->eventor->fire('onBlockReturnOutput', $output);

        //write cache
        if ($cache['update'] && isset($output['expires']) && $output['expires'] > time()) {
            if (!file_exists(pathinfo($cache['file'], PATHINFO_DIRNAME))) {
                mkdir(pathinfo($cache['file'], PATHINFO_DIRNAME), 0700, true);
            }
            file_put_contents($cache['file'], '<?php return '.var_export($output, true).';');
        }

        //modify page if output has right structure returned from View class instance.
        if (is_array($output) && isset($output['isBlockOutput']) && $output['isBlockOutput'] === true) {
            $this->page->linkCss($output['css']['linked']);
            $this->page->linkJs($output['js']['linked']);
            $this->page->embedCss($output['css']['embed']);
            $this->page->embedJs($output['js']['embed']);
            $this->page->addMeta($output['meta']);
            return $output['html'];
        } else {
            return $output;
        }
    }


    public function fillPosition($posName)
    {
        $positions = $this->route['positions'];
        $html = '';
        if (isset($positions[$posName])) foreach ($positions[$posName] as $element) {
            if (is_array($element)) {
                $html .= $this->loadBlock($element[0], $element[1], $element[2]);
            } elseif (is_string($element)) {
                if (isset($this->preparedBlocks[$element])) {
                    $element = $this->preparedBlocks[$element];
                    $html .= $this->loadBlock($element[0], $element[1], $element[2]);
                } else {
                    trigger_error('Prepared block is not defined: "'.$element.'"', E_USER_WARNING);
                }
            }
        }
        return $html;
    }
}