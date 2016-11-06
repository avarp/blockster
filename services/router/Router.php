<?php
namespace services\router;

class Router
{

    protected $routes;

    public function __construct($filename)
    {
        $this->routes = require(__DIR__.'/'.$filename);
    }


    public function findRoute($query)
    {
        $selectableRoutes = array();
        foreach ($this->routes as $name => $route) if (isset($route['selector'])) {
            if (!isset($route['selector']['method']) || strpos($route['selector']['method'], $_SERVER['REQUEST_METHOD']) !== false) {
                if ($route['selector']['rgxp'] == $query) {
                    return $route['content'];
                } else {
                    $selectableRoutes[] = $route;
                }
            }
        }

        return $this->match($query, $selectableRoutes);
    }


    protected function match($query, $routes)
    {
        $str = $query.';9876543210';
        $chunks = array_chunk($routes, 10);
        foreach ($chunks as $i => $chunk) {
            $pattern = array();
            foreach ($chunk as $j => $route) {
                $pattern[] = $route['selector']['rgxp'].';\\d{'.(9-$j).'}(\\d{'.($j+1).'})';
            }
            $pattern = '~^(?|'.implode('|', $pattern).')$~';
            if (preg_match($pattern, $str, $matches) === 1) {
                $j = end($matches);
                $j = intval($j{0});
                $params = array_slice($matches, 1, count($matches)-2);
                $route = $chunk[$j];
                if (!empty($params)) {
                    if (isset($route['selector']['captureGroupNames'])) {
                        $params = array_combine($route['selector']['captureGroupNames'], $params);
                    }
                    $_GET = array_merge($_GET, $params);
                    $_REQUEST = array_merge($_REQUEST, $params);
                }
                return $route['content'];
            }
        }
        return false;
    }


    public function getRoute($key) {
        if (isset($this->routes[$key])) return $this->routes[$key]['content'];
        else return false;
    }
}