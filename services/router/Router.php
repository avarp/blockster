<?php
namespace services\router;

class Router
{
    protected $routes = array();

    public function __construct($routingMap)
    {
        $this->routes = json_decode(file_get_contents($routingMap), true);
        if ($this->routes == null) die('JSON Syntax error in file "'.$routingMap.'"');
    }

    public function getUrl($destination)
    {
        if (strpos($destination, '>') === false) return $destination;
        $params = explode('>', trim($destination, ' >'));
        $params = array_map(function($x){return trim($x);}, $params);
        $routeName = array_shift($params);
        if (!isset($this->routes[$routeName])) {
            trigger_error('Route "'.$routeName.'" is not defined.', E_USER_WARNING);
        } elseif (!isset($this->routes[$routeName]['format'])) {
            trigger_error('URL format of route "'.$routeName.'" is not defined.', E_USER_WARNING);
        } else {
            $params['SITE_URL'] = SITE_URL;
            $params['HTTP_HOST'] = $_SERVER['HTTP_HOST'];
            $params['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $params['PROTOCOL'] = (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';

            $format = $this->routes[$routeName]['format'];
            foreach ($params as $key => $value) {
                if (strpos($format, '{') === false) break;
                $format = str_replace('{'.$key.'}', $value, $format);
            }
            if (strpos($format, '{') !== false) $format = preg_replace('/\{[^}]+\}/', '', $format);
            return rtrim($format, '/');
        }
    }

    public function findRoute($httpQuery)
    {
        extract($httpQuery);
        $filteredRoutes = array();
        foreach ($this->routes as $name => $route) if (isset($route['selector'])) {
            $s = $route['selector'];
            if (
                (!isset($s['method']) || $s['method'] == $method) && 
                (!isset($s['host']) || $s['host'] == $host) && 
                (!isset($s['status']) || $s['status'] == $status) && 
                (!isset($s['uri']) || $s['uri'] == $uri)
            ){
                return $route['content'];
            } elseif (
                (!isset($s['method']) || strpos($s['method'], $method) !== false) && 
                (!isset($s['status']) || $s['status'] == $status)
            ){
                $filteredRoutes[] = $route;
            }
        }

        return $this->match($httpQuery['host'].' '.$httpQuery['uri'], $filteredRoutes);
    }

    protected function match($url, $routes)
    {
        $str = $url.';9876543210';
        $chunks = array_chunk($routes, 10);
        foreach ($chunks as $i => $chunk) {
            $pattern = array();
            foreach ($chunk as $j => $route) {
                $s = $route['selector'];
                if (!isset($s['host'])) $s['host'] = '\\S+';
                if (!isset($s['uri'])) $s['uri'] = '\\S+';
                $pattern[] = $s['host'].' '.$s['uri'].';\\d{'.(9-$j).'}(\\d{'.($j+1).'})';
            }
            $pattern = '~^(?|'.implode('|', $pattern).')$~';
            if (preg_match($pattern, $str, $matches) === 1) {
                $j = end($matches);
                $j = intval($j{0});
                $params = array_slice($matches, 1, count($matches)-2);
                $route = $chunk[$j];
                if (!empty($params)) $_REQUEST['params'] = $params;
                return $route['content'];
            }
        }
        return array();
    }
}