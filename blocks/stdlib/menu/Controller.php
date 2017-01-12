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
            trigger_error('Menu not found or empty:'.$params['menuName'], E_USER_WARNING);
        }
    }


    public function actionManager()
    {
        restrictAccessLevel(100);

        $errors = array();
        $success = false;
        $menuName = '';

        if (isset($_POST['deleteMenu'])) $this->model->deleteMenu($_POST['deleteMenu']);

        if (isset($_POST['saveMenu'])) {
            $menuName = $_POST['menuName'];
            if (empty($menuName)) {
                $errors[] = 'Системное имя меню не должно быть пустым.';
            } elseif (!preg_match('/^[a-zA-Z0-9\-_]+$/', $menuName)) {
                $errors[] = 'Системное имя меню может содержать только латинские символы, цифры, дефис и нижнее подчеркивание.';
            } elseif ($this->model->createMenu($menuName)) {
                $success = true;
            } else {
                $errors[] = 'Ошибка создания меню. Возможно меню с таким именем уже существует.';
            }
        }

        $menus = $this->model->listMenus();
        $this->view->setTemplate('manager.tpl');
        return $this->view->render(compact('menus', 'menuName', 'errors', 'success'));
    }


    public function actionEditor()
    {
        restrictAccessLevel(100);

        $errors = array();
        $success = false;
        $menu = $this->model->getMenu($_GET['menuName']);
        if ($menu === false) {
            \blockster\Core::getInstance()->error404();
        }

        $this->view->setTemplate('editor.tpl');
        return $this->view->render(compact('menu', 'errors', 'success'));
    }
}