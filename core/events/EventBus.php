<?php
namespace core\events;

class EventBus
{
    protected $handlers = array();

    public function importEventHandlers($handlers)
    {
        foreach ($handlers as $event => $handlersList) {
            foreach ($handlersList as $handler) $this->addEventHandler($event, $handler);
        }
    }

    public function addEventHandler($event, $handler)
    {
        if (!isset($this->handlers[$event])) $this->handlers[$event] = array();
        if (!in_array($handler, $this->handlers[$event])) {
            $this->handlers[$event][] = $handler;
            return true;
        }
        return false;
    }

    public function removeEventHandler($event, $handler)
    {
        if (isset($this->handlers[$event])) {
            $hkey = array_search($handler, $this->handlers[$event]);
            if ($hkey !== false) {
                array_splice($this->handlers[$event], $hkey, 1);
                return true;
            }
        }
        return false;
    }

    protected function callHandler($handler, $input)
    {
        if (is_callable($handler)) {
            $output = $handler($input);
        } elseif (is_string($handler)) {
            $h = explode('::', $handler);
            if (count($h) == 2 && class_exists($h[0]) && method_exists($h[0], $h[1])) {
                $class = $h[0];
                $method = $h[1];
                $output[] = $class::$method($input);
            } elseif (function_exists($h[0])) {
                $function = $h[0];
                $output[] = $function($input);
            } else {
                throw new \Exception("Unknown handler string: $handler.");
            }
        } else {
            throw new \Exception("Unknown handler type: $handler.");
        }
        return $output;
    }

    public function dispatchEvent($event, $input=null)
    {
        if (isset($this->handlers[$event])) {
            for ($i = count($this->handlers[$event])-1; $i>=0; $i--) {
                $this->callHandler($this->handlers[$event][$i], $input);
            }
        }
    }

    public function dispatchEventFilter($event, $input)
    {
        $output = $input;
        if (isset($this->handlers[$event])) {
            for ($i = count($this->handlers[$event])-1; $i>=0; $i--) {
                $output = $this->callHandler($this->handlers[$event][$i], $output);
            }
        }
        return $output;
    }

    public function dispatchEventAccumulator($event, $input)
    {
        $output = array();
        if (isset($this->handlers[$event])) {
            for ($i = count($this->handlers[$event])-1; $i>=0; $i--) {
                $output[] = $this->callHandler($this->handlers[$event][$i], $input);
            }
        }
        return $output;
    }
}