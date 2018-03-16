<?php
namespace modules\admin\dashboard;

class Controller extends \modules\Controller
{
    public function action_default() {
        restrictAccessLevel(100);
        return $this->view->render();
    }

    public function action_settings() {
        restrictAccessLevel(100);
        $this->view->template = 'settings.php';
        return $this->view->render();
    }
}