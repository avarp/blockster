<?php
namespace services\eventor;

class Eventor
{

    protected $events;

    public function __construct($eventsFile)
    {
        $this->events = json_decode(file_get_contents($eventsFile), true);
    }

    public function addEvent($event)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = array();
            return true;
        } else {
            return false;
        }
    }

    public function removeEvent($event)
    {
        unset($this->events[$event]);
    }

    public function addEvents($events) {
        foreach ($events as $event) {
            if (isset($this->events[$event])) {
                foreach ($event as $handler) $this->events[$event][] = $handler;
            } else {
                $this->events[$event] = $handler;
            }
        }
    }

    public function attachHandler($event, $handler)
    {
        if (isset($this->events[$event])) {
            $this->events[$event][] = $handler;
            return true;
        }
        return false;
    }

    public function detachHandler($event, $handler)
    {
        if (isset($this->events[$event])) {
            $hkey = array_search($handler, $this->events[$event]);
            if ($hkey !== false) {
                array_splice($this->events[$event], $hkey, 1);
                return true;
            }
        }
        return false;
    }

    public function fire($event, $params=null)
    {
        if (isset($this->events[$event])) for ($i = count($this->events[$event])-1; $i>=0; $i--) {
            $handler = explode('::', $this->events[$event][i]);
            if (count($handler) == 2 && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
                $class = $handler[0];
                $method = $handler[1];
                $result = $class::$method($params);
            } elseif (function_exists($handler[0])) {
                $function = $handler[0];
                $result = $function($params);
            }
            if (!is_null($result)) $params = $result;
        }
        return $params;
    }
}