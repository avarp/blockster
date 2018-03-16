<?php
namespace core\services;

class Eventor
{
    protected static $handlers = array();

    public function importHandlers($handlers) {
        foreach ($handlers as $event => $handlersList) {
            foreach ($handlersList as $handler) $this->attachHandler($event, $handler);
        }
    }

    public function attachHandler($event, $handler)
    {
        if (!isset(self::$handlers[$event])) self::$handlers[$event] = array();
        if (!in_array($handler, self::$handlers[$event])) {
            self::$handlers[$event][] = $handler;
            return true;
        }
        return false;
    }

    public function detachHandler($event, $handler)
    {
        if (isset(self::$handlers[$event])) {
            $hkey = array_search($handler, self::$handlers[$event]);
            if ($hkey !== false) {
                array_splice(self::$handlers[$event], $hkey, 1);
                return true;
            }
        }
        return false;
    }

    public function fire($event, $input=null)
    {
        $output = $input;
        if (isset(self::$handlers[$event])) {
            for ($i = count(self::$handlers[$event])-1; $i>=0; $i--) {
                $handler = self::$handlers[$event][$i];
                if (is_callable($handler)) {
                    $output = $handler($output);
                } elseif (is_string($handler)) {
                    $h = explode('::', $handler);
                    if (count($h) == 2 && class_exists($h[0]) && method_exists($h[0], $h[1])) {
                        $class = $h[0];
                        $method = $h[1];
                        $output = $class::$method($output);
                    } elseif (function_exists($h[0])) {
                        $function = $h[0];
                        $output = $function($output);
                    } else {
                        throw new \Exception("Event $event error. Unknown handler $handler.");
                    }
                }
                if (is_null($output)) $output = $input;
            }
        }
        return $output;
    }
}