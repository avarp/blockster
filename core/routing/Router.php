<?php
namespace core\routing;

class RouterException extends \Exception{}

class Router
{
    protected $routes = array();
    protected $lastFoundKey;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function find($httpQuery)
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
                $this->lastFoundKey = $routeName;
                return $route['response'];
            } elseif (
                (!isset($s['method']) || in_array($method, $s['method'])) && 
                (!isset($s['status']) || $s['status'] == $status)
            ){
                $filteredRoutes[$routeName] = $route;
            }
        }

        return $this->match($uri, $filteredRoutes);
    }

    public function getUrl($href)
    {
        if (substr($href, 0, 7) != '/route:') return $href;
        $a = explode('?', substr($href, 7));
        $routeName = $a[0];
        if (!isset($this->routes[$routeName])) return $href;
        $route = $this->routes[$routeName];
        if (!isset($route['selector']['uri'])) return $href;
        $uri = $route['selector']['uri'];

        $params = array();
        if (isset($a[1])) parse_str($a[1], $params);
        foreach ($params as $name => $param) $uri = str_replace('{'.$name.'}', $param, $uri);
        $uri = preg_replace('/{([A-Za-z0-9_]+)}/', '', $uri);
        $uri = preg_replace('/[\/]{2,}/', '/', $uri);
        $uri = rtrim($uri, '/');
        return $uri;
    }

    public function getLastFoundKey()
    {
        return $this->lastFoundKey;
    }

    protected function match($query, $routes)
    {
        $query = $query.';9876543210';
        $chunks = array_chunk($routes, 10, true);
        foreach ($chunks as $i => $chunk) {
            $pattern = array();
            $routeNames = array();
            $j = 0;
            foreach ($chunk as $routeName => $route) {
                $routeNames[] = $routeName;
                $pattern[] = $route['selector']['rgxp'].';\\d{'.(9-$j).'}(\\d{'.($j+1).'})';
                $j++;
            }
            $pattern = '~^(?|'.implode('|', $pattern).')$~';
            if (preg_match($pattern, $query, $matches) === 1) {
                $j = end($matches);
                $j = intval($j{0});
                $routeName = $routeNames[$j];
                $this->lastFoundKey = $routeName;
                $route = $chunk[$routeName];
                $params = array_slice($matches, 1, count($matches)-2);
                if (!empty($params)) {
                    if (count($params) != count($route['selector']['names'])) {
                        throw new RouterException("Wrong count of parameters for route $routeName");
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
                    $r = json_encode($route['response']);
                    foreach ($namedParams as $n => $param) $r = str_replace('{'.$n.'}', $param, $r);
                    return json_decode($r, true);
                } else {
                    return $route['response'];
                }
            }
        }
        return null;
    }
}