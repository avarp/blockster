<?php
namespace blocks\backend\settings;

class Controller extends \blockster\Controller
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