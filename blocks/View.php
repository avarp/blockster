<?php
namespace blocks;

class View
{
    protected $template;
    protected $tplDir;
    protected $delayedFragments = array();
    public $data = array();

    public function __construct($tplDir)
    {
        $this->tplDir = $tplDir;
        $this->template = '';
    }

    public function setTemplate($template, $override=false) {
        if (empty($this->template) || $override) {
            if (strpos($template, DIRECTORY_SEPARATOR) !== false) {
                $this->template = $template;
            } else {
                $this->template = $this->tplDir.DIRECTORY_SEPARATOR.$template;
            }
        }
    }

    public function render($data=array())
    {
        extract($this->data);
        ob_start();
        if (empty($this->template)) $this->template = $this->tplDir.DIRECTORY_SEPARATOR.'default.tpl';
        require(ROOT_DIR.$this->template);
        $__output = ob_get_clean();

        extract($this->data);
        foreach ($this->delayedFragments as $__n => $__fragment) {
            ob_start();
            $__fragment();
            $__output = str_replace($this->getDelayedMarker($__n), ob_get_clean(), $__output);
        }
        return $__output;
    }

    protected function delayFragment($fragment)
    {
        echo $this->getDelayedMarker(count($this->delayedFragments));
        $this->delayedFragments[] = $fragment;
    }

    protected function getDelayedMarker($n)
    {
        return '~~delayed('.$n.')~~';
    }
}