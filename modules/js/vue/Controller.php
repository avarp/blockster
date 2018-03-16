<?php
namespace modules\js\vue;

class Controller extends \modules\Controller {

    public function action_default()
    {
        $this->view->addJsFile(pathToUrl(__DIR__.'/vue.min.js'));
    }

    public function action_dev()
    {
        $this->view->addJsFile(pathToUrl(__DIR__.'/vue.js'));
    }
}