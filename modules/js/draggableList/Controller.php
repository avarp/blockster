<?php
namespace modules\js\draggableList;

class Controller extends \modules\Controller {

    public function action_default()
    {
        $this->view->addJsFile(pathToUrl(__DIR__.'/draggableList.js'));
    }
}