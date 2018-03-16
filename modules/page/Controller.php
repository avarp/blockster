<?php
namespace modules\page;

class Controller extends \modules\Controller
{
    public function action_default($params)
    {    
        $this->view->template = $params['template'];
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
        $this->view->data['css'][] = array($css, false);
    }

    public function addJsText($js)
    {
        $this->view->data['js'][] = array($js, false);
    }

    public function addCssFile($url)
    {
        $this->view->data['css'][] = array($url, true);
    }

    public function addJsFile($url)
    {
        $this->view->data['js'][] = array($url, true);
    }

    public function addMetaTag($tag)
    {
        $this->view->data['metaTags'][] = $tag;
    }
}