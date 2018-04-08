<?php
namespace core\routing;

class RoutingCompilerException extends \Exception{}

class RoutingCompiler
{
    protected function divideUriSelector($u)
    {
        if (substr($u, 0, 4) == '//') {
            $p = strpos($u, '/', 2);
            $host = 'https?:'.substr($u, 0, $p);
            $path = substr($u, $p);
        } elseif (substr($u, 0, 4) == 'http') {
            $p = strpos($u, '/', 8);
            $host = substr($u, 0, $p);
            $path = substr($u, $p);
        } else {
            $host = 'https?://[^/]+';
            $path = $u;
        }
        $host = str_replace('.', "\\.", $host);
        return compact('host', 'path');
    }

    protected function fetchParamsNames($str)
    {
        $names = array();
        preg_match_all('/{([A-Za-z0-9_]+)}/', $str, $names);
        return $names[1];
    }

    protected function paramToRegexp($param, $isHost)
    {
        if (!isset($param['type'])) {
            throw new RoutingCompilerException("Undefined type of parameter in URI selector");
        }
        switch ($param['type']) {
            case 'string':
            $rgxp = $isHost ? '[^.]' : '[^/]';
            break;

            case 'numeric':
            $rgxp = '[0-9]';
            break;

            case 'any':
            $rgxp = '.*';
            break;

            case 'variants':
            $rgxp = implode('|', $param['variants']);
            break;

            default:
            throw new RoutingCompilerException("Unknown parameter type \"$param[type]\" in URI selector");
        }
        if ($param['type'] != 'variants' && $param['type'] != 'any') {
            $rgxp .= isset($param['length']) ? '{'.str_replace('-', ',', (string)$param['length']).'}' : '+';
        }
        return "($rgxp)";
    }

    protected function compileSelector($str, $params, $isHost)
    {
        $names = $this->fetchParamsNames($str);
        foreach ($names as $name) {
            if (isset($params[$name])) {
                $param = $params[$name];
                $rgxp = $this->paramToRegexp($param, $isHost);
                $search = $isHost ? "{".$name."}\\." : '/{'.$name.'}';
                if (isset($param['optional']) && $param['optional'] && strpos($str, $search) !== false) {
                    $rgxp = $isHost ? "(?:$rgxp\\.)?" : "(?:/$rgxp)?";
                } else {
                    $search = '{'.$name.'}';
                    if (isset($param['optional']) && $param['optional']) $rgxp .= '?';
                }
                $str = str_replace($search, $rgxp, $str);
            } else {
                throw new RoutingCompilerException("Unknown parameter \"$name\" in URI selector");
            }
        }
        return $str;
    }

    public function compile($file)
    {
        $map = json_decode(file_get_contents($file), true);
        if (is_null($map)) {
            throw new RoutingCompilerException("JSON syntax error in file $file");
        }

        foreach ($map as $routeName => $route) {
            if (isset($route['selector']['uri'])) {
                $selector = $route['selector'];
                $u = $this->divideUriSelector($selector['uri']);
                if (isset($selector['params'])) {
                    $map[$routeName]['selector']['rgxp'] = 
                        $this->compileSelector($u['host'], $selector['params'], true).
                        $this->compileSelector($u['path'], $selector['params'], false);
                    $map[$routeName]['selector']['names'] = $this->fetchParamsNames($selector['uri']);
                } else {
                    $map[$routeName]['selector']['rgxp'] = $u['host'].$u['path'];
                    $map[$routeName]['selector']['names'] = array();
                }
            }
            $map[$routeName]['name'] = $routeName;
        }

        return $map;
    }
}