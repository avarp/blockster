<?php
namespace blocks\stdlib\menu;

class Controller extends \proto\Controller
{
    public function actionIndex($params)
    {
        $menu = $this->model->getMenu($params['menuName']);
        if ($menu) {
            $class = isset($params['class']) ? $params['class'] : '';
            $id = isset($params['id']) ? $params['id'] : '';
            return $this->view->render(compact('menu', 'class', 'id'));
        } else {
            trigger_error('Menu not found:'.$params['menuName'], E_USER_WARNING);
        }
    }

    

    public function actionManager()
    {
        $this->view->setTemplate('manager.tpl');
        return $this->view->render();
    }
}