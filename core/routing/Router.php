<?php
namespace core\routing;

class RouterException extends \Exception{}

class Router
{
    protected $routes = array();

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function findRoute($httpQuery)
    {
        extract($httpQuery);
        $filteredRoutes = array();
        foreach ($this->routes as $routeName => $route) if (isset($route['selector'])) {
            $s = $route['selector'];
            if (
                (!isset($s['method']) || in_array($method, $s['method'])) && 
                (!isset($s['status']) || $s['status'] == $status) && 
                (!isset($s['rgxp']) || $s['rgxp'] == $uri)
            ){
                return array(
                    'name'    => $route['name'],
                    'content' => $route['content']
                );
            } elseif (
                (!isset($s['method']) || in_array($method, $s['method'])) && 
                (!isset($s['status']) || $s['status'] == $status)
            ){
                $filteredRoutes[] = $route;
            }
        }

        return $this->match($uri, $filteredRoutes);
    }

    public function getUrl($href)
    {
        
    }

    protected function match($query, $routes)
    {
        $query = $query.';9876543210';
        $chunks = array_chunk($routes, 10);
        foreach ($chunks as $i => $chunk) {
            $pattern = array();
            foreach ($chunk as $j => $route) {
                $pattern[] = $route['selector']['rgxp'].';\\d{'.(9-$j).'}(\\d{'.($j+1).'})';
            }
            $pattern = '~^(?|'.implode('|', $pattern).')$~';
            if (preg_match($pattern, $query, $matches) === 1) {
                $j = end($matches);
                $j = intval($j{0});
                $params = array_slice($matches, 1, count($matches)-2);
                $route = $chunk[$j];
                if (!empty($params)) {
                    if (count($params) != count($route['selector']['names'])) {
                        throw new RouterException("Wrong count of parameters for route $route[name]");
                    } else {
                        $namedParams = array();
                        foreach ($route['selector']['names'] as $n=>$name) {
                            if (empty($params[$n]) && isset($route['selector']['params'][$name]['default'])) {
                                $namedParams[$name] = $route['selector']['params'][$name]['default'];
                            } else {
                                $namedParams[$name] = $params[$n];
                            }
                        }
                    }
                    $c = json_encode($route['content']);
                    foreach ($namedParams as $n => $param) $c = str_replace('{'.$n.'}', $param, $c);
                    return array(
                        'name'    => $route['name'],
                        'content' => json_decode($c, true)
                    );
                } else {
                    return array(
                        'name'    => $route['name'],
                        'content' => $route['content']
                    );
                }
            }
        }
        return array();
    }
}