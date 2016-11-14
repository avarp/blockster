<?php
namespace blocks\stdlib\admin;

class Controller extends \proto\Controller
{
    public function actionIndex() {
        restrictAccessLevel(100);
        return $this->view->render();
    }

    public function actionSettings() {
        restrictAccessLevel(100);
        $this->view->setTemplate('settings.tpl');
        return $this->view->render();
    }
}