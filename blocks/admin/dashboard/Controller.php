<?php
namespace blocks\admin\dashboard;

class Controller extends \blocks\Controller
{
    public function action_default() {
        restrictAccessLevel(100);
        return $this->view->render();
    }

    public function action_settings() {
        restrictAccessLevel(100);
        $this->view->setTemplate('settings.tpl');
        return $this->view->render();
    }
}