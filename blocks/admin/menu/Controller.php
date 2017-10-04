<?php
namespace blocks\admin\menu;

class Controller extends \blocks\Controller
{
    public function action_default($params)
    {
        restrictAccessLevel(100);
        switch (count($params)) {
            case 1:
            return $this->showMenus();

            case 2:
            return $this->showMenuitems($params[1]);

            default:
            error404();
        }
    }   

    protected function showMenus()
    {
        $h1 = 'Меню сайта';
        $breadcrumbs = array(
            '> admin' => 'Панель управления',
            '> admin > menu' => 'Меню'
        );

        $deleteMenuSuccess = false;
        if (isset($_POST['deleteMenu'])) {
            $this->model->deleteMenu(intval($_POST['deleteMenu']));
            $deleteMenuSuccess = true;
        }

        $menus = $this->model->getMenus();
        $this->view->data = compact('h1', 'breadcrumbs', 'menus', 'deleteMenuSuccess');
        $this->view->template = 'menus.tpl';
        $this->view->setTitle($h1);
        return $this->view->render();
    }

    protected function showMenuitems($menuId)
    {
        $this->view->template = 'menuitems.tpl';
        if ($menuId == 'new') {
            $menu = $this->model->createMenuitem();
        } else {
            $menu = $this->model->fetchMenuById(intval($menuId));
        }
        if (!$menu) error404();

        $h1 = $menu['name'] ? $menu['name'] : 'Новое меню';
        $breadcrumbs = array(
            '> admin' => 'Панель управления',
            '> admin > menu' => 'Меню',
            '> admin > menu/'.$menuId => $h1
        );

        $this->view->data = compact('h1', 'breadcrumbs', 'menu');
        $this->view->template = 'menuitems.tpl';
        $this->view->setTitle($h1);
        return $this->view->render();
    }
}