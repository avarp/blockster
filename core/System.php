<?php
namespace core;
/**
* Definition main class of cms.
* @author Artem Vorobev <artem.v.mailbox@gmail.com>
*/

class SystemException extends \Exception{}

/**
 * Main class. Uses singletone pattern. It's responsible for:
 * - creation response
 * - loading modules and positions
 * - providing access to event bus, database and localization systems
 * @uses \core\Translator
 * @uses \core\EventBus
 * @uses \core\routing\RoutingCompiler
 * @uses \core\routing\Router
 * @uses \core\database\Dbh
 */
class System
{
    /**
     * Singletone instance of system.
     * @static
     */
    private static $instance;


    /**
     * Routers used to define content to show. They are examples of \core\routing\Router class.
     * They works separately, but uses same HTTP query to find what to show.
     * content - finds first module (root module) and optionally positions, theme and language.
     * theme - find theme if it's not defined yet
     * lang - find language if it's not defined yet
     */
    private $routers = array(
        'content' => null,
        'theme' => null,
        'lang' => null
    );


    /**
     * Example of \core\Translator. It uses .mo files and GetText system for translating UI
     */
    private $translator;


    /**
     * Example of \core\EventBus. This is universal event bus.
     * It's private, but System provides read-only access to it.
     */
    private $eventBus;


    /**
     * Example of \core\database\Dbh. Database handler.
     * It's private, but System provides read-only access to it.
     */
    private $dbh;


    /**
     * Array describes content to show:
     * - first module (root module)
     * - positions (modules grouped by named positions)
     * - language
     * - theme
     * System provides read-only access to language and theme.
     */
    private $content = array();


    /**
     * Stack of nested modules. If imagine page as tree of nested modules, this 
     * stack is one branch contains modules executing now.
     * Each element of this stack contains:
     * - name - name of module without action
     * - action - name of called action
     * - controller - instance of module's controller
     * - cache - name of file, which contains cache for this action 
     */
    private $moduleStack = array();


    /**
     * Log of broadcasted messages. It used for creating cache. While system executes
     * modules, they can call function broadcastMessage() and all this messages will be
     * broadcasted immediately. But if you get output of module from cache, no code is
     * executed. That's why all messages is logging and if system creates cache,
     * they will be flushed into cache file. Each message have 2 fields:
     * - message - name of message
     * - params - any parameters required for doing something when accept this message
     */
    private $msgLog = array();


    private function __clone() {}
    private function __wakeup() {}


    /**
     * Open some properties for reading by modules.
     * - dbh - database handler
     * - eventBus - event bus
     * - theme - selected theme
     * - themeUrl - url of selected theme
     * - lang - selected language
     * @param string $name name of property
     * @return mixed property 
     */
    public function __get($name)
    {
        if (in_array($name, ['dbh', 'eventBus'])) {
            return $this->$name;
        } else switch ($name) {
            case 'theme':
            return $this->content['theme'];

            case 'themeUrl':
            return SITE_URL.'/themes/'.$this->content['theme'];

            case 'lang':
            return $this->content['lang'];
        }
    }


    /**
     * Get singletone instance of System
     * @return object instance of \core\System
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * Init the System
     */
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

        $this->translator = new locale\Translator;
        $this->eventBus = new events\EventBus;

        $compiler = new routing\RoutingCompiler;
        $this->routers['content'] = new routing\Router($compiler->compile('core/routing/content.json'));
        $this->routers['theme'] = new routing\Router($compiler->compile('core/routing/theme.json'));
        $this->routers['lang'] = new routing\Router($compiler->compile('core/routing/lang.json'));
    }


    /**
     * Trim GET-parameters and anchor
     * @param string $u URI to trim
     * @return string trimmed URI
     */
    private function trimGetParams($u)
    {
        if (false !== $p = strpos($u, '?')) $u = substr($u, 0, $p);
        if (false !== $p = strpos($u, '#')) $u = substr($u, 0, $p);
        return $u;
    }


    /**
     * Reset system. Check recursion level, stop output buffering and clear module stack
     * and message log. 
     * @staticvar integer $recursionCounter counts depth of recursion
     * @return void
     */
    private function resetSystem()
    {
        static $recursionCounter = 0;
        $recursionCounter++;
        if ($recursionCounter > 1 && ob_get_level() > 0) {
            for ($i=ob_get_level(); $i>0; $i--) ob_end_clean();
            $this->moduleStack = array();
            $this->msgLog = array();
        } elseif ($recursionCounter > 10) {
            throw new SystemException('Error while creating Response. Recursion is too deep.');
        }
    }


    /**
     * Fetch user's language from HTTP header Accept-language.
     * @return string ISO code of language 
     */
    private function getAcceptLanguage()
    {
        preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/i', $_SERVER["HTTP_ACCEPT_LANGUAGE"], $matches);
        $langs = array_combine($matches[1], $matches[2]);
        foreach ($langs as $n => $v) $langs[$n] = $v ? $v : 1;
        arsort($langs);
        return key($langs);
    }


    /**
     * Execute routing by HTTP query. Get information about:
     * - what module is to be executed first (required)
     * - which positions defined for this content (required)
     * - what is selected language (optional)
     * - what theme is to be used for displaying content (optional)
     * @param array $httpQuery - URI, HTTP-method and HTTP-status
     * @return array description of content to be shown
     */
    private function findContent($httpQuery)
    {
        $content = $this->routers['content']->find($httpQuery);
        if (empty($content)) {
            $httpQuery['status'] = 404;
            $content = $this->routers['content']->find($httpQuery);
        }
        if (empty($content)) {
            throw new SystemException('Routing error. Nothing to show. Please check file core/routing/content.json.');
        }
        if (!isset($content['positions'])) $content['positions'] = array();
        return $content;
    }


    /**
     * Execute routing by HTTP query. Find which language system should use.
     * @param array $httpQuery - URI, HTTP-method and HTTP-status
     * @return array all info about language
     */
    private function detectLanguage($httpQuery)
    {
        $l = $this->routers['lang']->find($httpQuery);
        if (empty($l)) $l = $this->getAcceptLanguage();
        
        if (!is_null($this->dbh)) {
            $lang = $this->dbh->row('SELECT * FROM languages WHERE isoCode=?', array($l));
            if (!$lang) $lang = $this->dbh->row('SELECT * FROM languages WHERE isoCode=?', explode('-', $l));
            if (!$lang) $lang = $this->dbh->row("SELECT * FROM languages WHERE isoCode='en'");
        } else {
            $rtl = ['ar', 'arq', 'arz', 'ajp', 'apc', 'ary', 'aeb', 'aii', 'azj-arab',
            'prs', 'he', 'kk-arab', 'ckb-arab', 'ps', 'fa', 'pnb', 'sd', 'ug-arab', 'ur'];
            $lang = [
                'id' => 0,
                'isoCode' => $l,
                'internationalName' => '',
                'nativeName' => '',
                'direction' => in_array($l, $rtl) ? 'rtl' : 'ltr',
                'isActive' => 0
            ];
        }
        return $lang;
    }


    /**
     * Postprocess response before sending. Set HTTP-status and Content-type
     * headers. Add information about system performance.
     * @param array $httpQuery - URI, HTTP-method and HTTP-status
     * @param mixed $response - generated response
     * @return string $response - response is to be sent
     */
    private function postprocessResponse($httpQuery, $response)
    {
        $httpStatusCodes = getHttpStatusCodes();
        $sapiName = php_sapi_name();
        $s = $httpQuery['status'].' '.$httpStatusCodes[$httpQuery['status']];
        if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
            header('Status: '.$s);
        } else {
            header($_SERVER['SERVER_PROTOCOL'].' '.$s);
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
        return $response;
    }


    /**
     * Create and send response. This is main function of this class.
     * IMPORTANT! This function will call exit() at the end.
     * @param array $httpQuery URI, HTTP-method and HTTP-status
     * @return void
     */
    public function getResponse($httpQuery=array())
    {
        $this->resetSystem();

        // fill defaults
        if (!isset($httpQuery['uri'])) $httpQuery['uri'] = SITE_URL.$this->trimGetParams($_SERVER['REQUEST_URI']);
        if (!isset($httpQuery['method'])) $httpQuery['method'] = $_SERVER['REQUEST_METHOD'];
        if (!isset($httpQuery['status'])) $httpQuery['status'] = 200;

        // connect to database if not connected yet
        if (is_null($this->dbh)) {
            try {
                $this->dbh = new database\Dbh;
            } catch (\Exception $e) {
                $httpQuery['method'] = 'INSTALL_SYSTEM';
            }
        }

        $this->eventBus->dispatchEvent('onSystemStart');

        // find content to show
        $content = $this->findContent($httpQuery);

        if (!isset($content['lang']) || empty($content['lang'])) {
            $content['lang'] = $this->detectLanguage($httpQuery);
        }
        if (!isset($content['theme']) || empty($content['theme'])) {
            $content['theme'] = $this->routers['theme']->find($httpQuery);
        }
        $content = $this->eventBus->dispatchEventFilter('onRouting', $content);

        // check content
        if (!isset($content['module'])) {
            throw new SystemException('Wrong content format. Root module is not specified.');
        }
        if (!$this->isModuleExists($content['module']['name'])) {
            throw new SystemException('Wrong content format. Root module is not exists.');
        }
        if (!is_dir(ROOT_DIR.DS.'themes'.DS.$content['theme'])) {
            throw new SystemException("Wrong content format. Theme directory \"$content[theme]\" is not exists.");
        }

        // show content
        $this->content = $content;
        $this->eventBus->dispatchEvent('onRoutingDone');
        $response = $this->loadModule($content['module']['name'], $content['module']['params']);
        $response = $this->eventBus->dispatchEventFilter('onExit', $response);

        exit($this->postprocessResponse($httpQuery, $response));
    }


    /**
     * Reverse routing.
     * @param string $destination string with special format
     * @return string url
     */
    public function getUrl($destination)
    {
        return $this->routers['content']->getUrl($destination);
    }


    /**
     * Check if specified module exists.
     * @param string $name name (and optionally action) of module
     * @return boolean true if exists
     */
    public function isModuleExists($name) {
        $a = explode('::', $name);
        $action = isset($a[1])? 'action_'.$a[1] : 'action_default';
        $namespace = str_replace('/', '\\', DS.'modules'.DS.$a[0]);
        $controllerClass = $namespace.'\\Controller';
        return class_exists($controllerClass) && method_exists($controllerClass, $action);
    }


    /**
     * Load module, execute action and get the output.
     * @staticvar integer $recursionCounter counts depth of recursion
     * @param string $name name (and optionally action) of module
     * @param array $params any params required by module
     * @return mixed output of module
     */
    public function loadModule($name, $params)
    {
        static $recursionCounter = 0;
        $recursionCounter++;
        if ($recursionCounter > 25) {
            throw new SystemException('Error in function loadModule. Recursion is too deep.');
        }

        $a = explode('::', $name);

        $cacheDir        = ROOT_DIR.DS.'temp'.DS.'cache'.DS.$a[0];
        $tplDir          = ROOT_DIR.DS.'themes'.DS.$this->content['theme'].DS.$a[0];
        $moduleName      = $a[0];
        $action          = isset($a[1])? 'action_'.$a[1] : 'action_default';
        $namespace       = str_replace('/', '\\', DS.'modules'.DS.$a[0]);
        $controllerClass = $namespace.'\\Controller';

        //test on fatal errors
        if (!class_exists($controllerClass)) {
            throw new SystemException('The Controller class of module "'.$name.'" is not defined');
        }
        if (!method_exists($controllerClass, $action)) {
            throw new SystemException('The '.$action.' method is not defined in Controller class of module "'.$name.'"');
        }

        //put the module into stack
        $this->moduleStack[] = array(
            'name' => $moduleName,
            'action' => $action,
            'controller' => null,
            'cache' => ''
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

        if (!empty($m['cache'])) {
            $cacheFile = $cacheDir.DS.$m['cache'];
            if (file_exists($cacheFile)) {
                // read cache
                $cache = unserialize(file_get_contents($cacheFile));
                $output = $cache['output'];
                foreach ($cache['messages'] as $msg) $this->broadcastMessage($msg['message'], $msg['param']);
            } else {
                // write cache
                $cache = array(
                    'output' => $output,
                    'messages' => $this->msgLog
                );
                if (!is_dir($cacheDir)) mkdir($cacheDir, 0700, true);
                file_put_contents($cacheFile, serialize($cache));
                $this->msgLog = array();
            }
        }

        $output = $this->eventBus->dispatchEventFilter('onModuleOutput', $output);
        $recursionCounter--;
        return $output;
    }


    /**
     * Load modules in specified position as defined in content.
     * IMPORTANT! All modules should returns string as its output.
     * @param string $posName name of position
     * @return string outputs of module
     */
    public function loadPosition($posName)
    {
        if (isset($this->content['positions'][$posName])) {
            $modules = $this->content['positions'][$posName];
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


    /**
     * Offer to system to use cache instead of executing code of module.
     * @param string $file name of file used to get cache
     * @param integer $lifetime amount of seconds from creation cache file while it is valid
     * @return boolean is system will use cache or not
     */
    public function useCache($file, $lifetime=3600)
    {
        if (empty($file) || $lifetime < 60) return false;
        $n = count($this->moduleStack) - 1;
        if ($n < 0) throw new SystemException('Function useCache called not in context of module.');
        $this->moduleStack[$n]['cache'] = $file;

        $f = ROOT_DIR.DS.'temp'.DS.'cache'.DS.$this->moduleStack[$n]['name'].DS.$file;
        if (file_exists($f) && filemtime($f)+$lifetime > time()) {
            return true;
        } else {
            @unlink($f);
            return false;
        }
    }


    /**
     * Broadcast message to all modules in modules stack.
     * @param string $message name of message
     * @param array $params any parameters required for doing something when accept this message
     */
    public function broadcastMessage($message, $param='')
    {
        $this->msgLog[] = compact('message', 'param');
        for ($i = count($this->moduleStack)-1; $i>=0; $i--) {
            $m = $this->moduleStack[$i];
            if (method_exists($m['controller'], 'acceptMessage')) $m['controller']->acceptMessage($message, $param);
        }
    }


    /**
     * Get .mo file for this module. This files is used by GetText system for
     * translating text messages. Path to file depends on module on the top of
     * the modules stack, language and theme.
     * @return string full path to .mo file
     */
    private function getThisMoFile() {
        if (empty($this->moduleStack)) {
            throw new SystemException('Translation possible only in context of module.');
        }
        $localeDir = 
            ROOT_DIR.
            DS.'themes'.
            DS.$this->content['theme'].
            DS.$this->moduleStack[count($this->moduleStack)-1]['name'].
            DS.'locale';
        $moFile = $localeDir.DS.$this->content['lang']['isoCode'].'.mo';
        if (!file_exists($moFile)) {
            $l = explode('-', $this->content['lang']['isoCode']);
            $moFile = $localeDir.DS.$l[0].'.mo';
        }
        return $moFile;
    }


    /**
     * Provide single interface for Translator.
     * IMPORTANT! You shouldn't use this function itself. Use global function t()
     * for localization.
     * @param string $msg1 message (in singular form, if $msg2 is used)
     * @param string $msg2 (if $n is used it specify plural form, otherwise it specify context of message $msg1)
     * @param integer $n number used for translate plural forms
     */
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


    /**
     * Add js file with Translator function and return javascript expression
     * which binds this function with content of .mo file
     * @return string js expression
     */
    public function getTranslatorForJs() {
        $moFile = $this->getThisMoFile();
        return $this->translator->getTranslatorForJs($moFile);
    }

}