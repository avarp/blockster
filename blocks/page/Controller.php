<?php
namespace blocks\page;

class Controller extends \blocks\Controller
{
    protected $router;
    protected $eventor;

    public function __construct($view, $model, $parent, $page)
    {
        parent::__construct($view, $model, $parent, $page);

        if (!defined('SITE_URL')) {
            $siteUrl = ((!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].rtrim(INSTALL_URI, '/');
            define('SITE_URL', $siteUrl);
        }

        $this->router = new \services\router\Router('map.json');
        if ($this->router->getRoute('error404') == false) die('The "error404" route does not exists'); 
        if ($this->router->getRoute('error403') == false) die('The "error403" route does not exists'); 
        $this->eventor = new \services\eventor\Eventor('pageEvents.json');
        $this->eventor->fire('onPageControllerStart', $this);
    }

    protected function cleanUrl($u)
    {
        if (false !== $p = strpos($u, '?')) $u = substr($u, 0, $p);
        if (false !== $p = strpos($u, '#')) $u = substr($u, 0, $p);
        if (INSTALL_URI != '/') {
            $u = substr($u, strlen(INSTALL_URI));
            if ($u{0} != '/') $u = '/'.$u;
        }
        return $u;
    }

    public function action_default($params)
    {    
        $url = $this->cleanUrl($_SERVER['REQUEST_URI']);
        if ($url != '/' && substr($url, -1) == '/') {
            header('Location: '.rtrim($_SERVER['REQUEST_URI'], '/'), true, 301);
            die();
        }

        if (isset($params['route'])) {
            if ($params['route'] == 'error404') {
                $sapiName = php_sapi_name();
                if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
                    header('Status: 404 Not Found');
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                }
            } elseif ($params['route'] == 'error404') {
                $sapiName = php_sapi_name();
                if ($sapiName == 'cgi' || $sapiName == 'cgi-fcgi') {
                    header('Status: 403 Forbidden');
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                }
            }
            $route = $this->router->getRoute($params['route']);
        } elseif (isset($params['url'])) {
            $route = $this->router->findRoute($this->cleanUrl($params['url']));
        } else {
            $url = $this->cleanUrl($_SERVER['REQUEST_URI']);
            if ($url != '/' && substr($url, -1) == '/') {
                header('Location: '.rtrim($_SERVER['REQUEST_URI'], '/'), true, 301);
                die();
            }
            $route = $this->router->findRoute($url);
        }

        if ($route === false) error404();

        if (DIRECTORY_SEPARATOR != '/') $route['template'] = str_replace('/', DIRECTORY_SEPARATOR, $route['template']);
        if ($route['template']{0} != DIRECTORY_SEPARATOR) {
            $route['template'] = DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.SITE_THEME.DIRECTORY_SEPARATOR.$route['template'];
        }
        if (!file_exists(ROOT_DIR.$route['template'])) {
            die('Page template not found: '.ROOT_DIR.$route['template']);
        }

        $this->eventor->fire('onRouteSelected', $route);

        $this->eventor->addEvents($route['events']);
        \services\core\Blockster::getInstance()->addPositions($route['positions']);
        $this->view->setTemplate($route['template']);
        $output = $this->view->render();
        $this->eventor->fire('onPageRendered', $output);
        return $output;
    }

    public function setTitle($title)
    {
        if (!empty($title)) $this->view->data['title'] = $title;
    }

    public function setKeywords($keywords)
    {
        $this->view->data['keywords'] = $keywords;
    } 

    public function setDescription($description)
    {
        $this->view->data['description'] = $description;
    } 

    public function addCssText($css)
    {
        $this->view->data['cssText'] .= $css."\n";
    }

    public function addJsText($js)
    {
        $this->view->data['jsText'] .= $js."\n";
    }

    public function addCssFile($url)
    {
        if (!in_array($url, $this->view->data['cssFiles'])) $this->view->data['cssFiles'][] = $url;
    }

    public function addJsFile($url)
    {
        if (!in_array($url, $this->view->data['jsFiles'])) $this->view->data['jsFiles'][] = $url;
    }

    public function addMetaTag($tag)
    {
        if (!in_array($tag, $this->view->data['metaTags'])) $this->view->data['metaTags'][] = $tag;
    }
}