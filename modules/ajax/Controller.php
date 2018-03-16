<?php
namespace modules\ajax;

class Controller extends \modules\Controller
{
    public $output = array(
        'response' => array(),
        'title' => '',
        'css' => array(),
        'js' => array()
    );

    public function  action_default($params)
    {
        if (!isset($params['moduleName']) || !core()->isModuleExists($params['moduleName'])) error404();
        $this->output['response'] = module($params['moduleName'], $_REQUEST);
        $this->output['css'] = array_unique($this->output['css'], SORT_REGULAR);
        $this->output['js'] = array_unique($this->output['js'], SORT_REGULAR);
        return $this->output;
    }

    public function acceptMessage($message, $param)
    {
        if (method_exists($this, $message) && is_string($param)) $this->$message($param);
    }

    public function setTitle($title)
    {
        if (!empty($title)) $this->output['title'] = $title;
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
}