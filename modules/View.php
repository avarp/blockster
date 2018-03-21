<?php
namespace modules;

class View
{
    public $template;
    protected $tplDir;
    protected $delayedFragments = array();
    public $data = array();

    public function __construct($tplDir)
    {
        $this->tplDir = $tplDir;
        $this->template = 'default.php';
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
        $this->getDelayedMarker(count($this->delayedFragments));
        $this->delayedFragments[] = $fragment;
        return $this->getDelayedMarker(count($this->delayedFragments)-1);
    }

    protected function getDelayedMarker($n)
    {
        return '{-{'.$n.'}-}';
    }

    public function setTitle($title)
    {
        core()->broadcastMessage('setTitle', $title);
    }

    public function setKeywords($keywords)
    {
        core()->broadcastMessage('setKeywords', $keywords);
    } 

    public function setDescription($description)
    {
        core()->broadcastMessage('setDescription', $description);
    } 

    public function addCssText($css)
    {
        core()->broadcastMessage('addCssText', $css);
    }

    public function addJsText($js)
    {
        core()->broadcastMessage('addJsText', $js);
    }

    public function addCssFile($url)
    {
        core()->broadcastMessage('addCssFile', $url);
    }

    public function addJsFile($url)
    {
        core()->broadcastMessage('addJsFile', $url);
    }

    public function addMetaTag($tag)
    {
        core()->broadcastMessage('addMetaTag', $tag);
    }
}