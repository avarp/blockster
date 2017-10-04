<?php
namespace blocks;

class View
{
    public $template;
    protected $tplDir;
    protected $delayedFragments = array();
    public $data = array();

    public function __construct($tplDir)
    {
        $this->tplDir = $tplDir;
        $this->template = 'default.tpl';
    }

    public function render()
    {
        extract($this->data);
        ob_start();
        require($this->tplDir.DS.$this->template);
        $__output = ob_get_clean();

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

    public function setTitle($title)
    {
        core()->sendMessage('page', 'setTitle', $title);
    }

    public function setKeywords($keywords)
    {
        core()->sendMessage('page', 'setKeywords', $keywords);
    } 

    public function setDescription($description)
    {
        core()->sendMessage('page', 'setDescription', $description);
    } 

    public function addCssText($css)
    {
        core()->sendMessage('page', 'addCssText', $css);
    }

    public function addJsText($js)
    {
        core()->sendMessage('page', 'addJsText', $js);
    }

    public function addCssFile($url)
    {
        core()->sendMessage('page', 'addCssFile', $url);
    }

    public function addJsFile($url)
    {
        core()->sendMessage('page', 'addJsFile', $url);
    }

    public function addMetaTag($tag)
    {
        core()->sendMessage('page', 'addMetaTag', $tag);
    }
}