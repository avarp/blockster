<?php
namespace core;

class RouterException extends \Exception{}

class Router
{
    protected $routes = array();

    public function __construct($routingMap)
    {
        $this->routes = json_decode(file_get_contents($routingMap), true);
        if ($this->routes == null) throw new RouterException('JSON Syntax error in routing map file "'.$routingMap.'"');
    }

    public function getUrl($destination)
    {
        if (substr($destination, 0, 6) != 'route:') return $destination;
        $params = explode('>', substr($destination, 6));
        $routeName = trim(array_shift($params));
        if (!isset($this->routes[$routeName])) {
            throw new RouterException('Route "'.$routeName.'" is not defined.');
        } elseif (!isset($this->routes[$routeName]['schema'])) {
            throw new RouterException('URL schema of route "'.$routeName.'" is not defined.');
        } else {
            $params['SITE_URL'] = SITE_URL;
            $params['LANG'] = core()->getLang();
            $params['HTTP_HOST'] = $_SERVER['HTTP_HOST'];
            $params['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $params['PROTOCOL'] = (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';

            $format = $this->routes[$routeName]['schema'];
            foreach ($params as $key => $value) {
                if (strpos($format, '{') === false) break;
                $format = str_replace('{'.$key.'}', trim($value), $format);
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

    protected function match($query, $routes)
    {
        $query = $query.';9876543210';
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
            if (preg_match($pattern, $query, $matches) === 1) {
                $j = end($matches);
                $j = intval($j{0});
                $params = array_slice($matches, 1, count($matches)-2);
                $route = $chunk[$j];
                if (!empty($params)) {
                    $c = json_encode($route['content']);
                    foreach ($params as $n => $param) $c = str_replace('{'.$n.'}', $param, $c);
                    return json_decode($c, true);
                } else {
                    return $route['content'];
                }
            }
        }
        return array();
    }
}