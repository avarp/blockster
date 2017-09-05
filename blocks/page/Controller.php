<?php
namespace blocks\page;

class Controller extends \blocks\Controller
{
    public function action_default($httpQuery)
    {    
        return $this->view->render();
    }

    public function acceptMessage($message, $param)
    {
        if (method_exists($this, $message) && is_string($param)) $this->$message($param);
    }

    public function setTitle($title)
    {
        if (!empty($title)) $this->view->data['title'] = $title;
    }

    public function setKeywords($keywords)
    {
        $this->view->data['keywords'] = $keywords;
    } 

    public function setDescription($description)
    {
        $this->view->data['description'] = $description;
    } 

    public function addCssText($css)
    {
        $this->view->data['cssText'] .= $css."\n";
    }

    public function addJsText($js)
    {
        $this->view->data['jsText'] .= $js."\n";
    }

    public function addCssFile($url)
    {
        if (!in_array($url, $this->view->data['cssFiles'])) $this->view->data['cssFiles'][] = $url;
    }

    public function addJsFile($url)
    {
        if (!in_array($url, $this->view->data['jsFiles'])) $this->view->data['jsFiles'][] = $url;
    }

    public function addMetaTag($tag)
    {
        if (!in_array($tag, $this->view->data['metaTags'])) $this->view->data['metaTags'][] = $tag;
    }
}