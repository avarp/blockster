<?php
namespace services\eventor;

class Eventor
{

    protected $events;
    protected $eventsFile;

    public function __construct($eventsFile)
    {
        $this->eventsFile = __DIR__.'/'.$eventsFile;
        $this->events = json_decode(file_get_contents($this->eventsFile), true);
    }

    public function fixState()
    {
        $content = '<?php return '.var_export($this->events, true).';';
        file_put_contents($this->eventsFile, $content);
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

    public function mergeEvents($events) {
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
        } else {
            return false;
        }
    }

    public function detachHandler($event, $handler)
    {
        if (isset($this->events[$event])) {
            $hkey = array_search($handler, $this->events[$event]);
            if ($hkey !== false) {
                unset($this->events[$event][$hkey]);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function fire($event, &$params=null)
    {
        if (isset($this->events[$event])) foreach ($this->events[$event] as $handler) {
            $handler = explode('::', $handler);
            if (count($handler) == 2 && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
                $class = $handler[0];
                $method = $handler[1];
                $class::$method($params);
            }
        }
    }
}