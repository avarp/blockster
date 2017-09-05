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
                if (!empty($params) && isset($route['names'])) {
                    $_REQUEST['params'] = array_combine($route['names'], $params);
                }
                return $route['content'];
            }
        }
        return array();
    }
}