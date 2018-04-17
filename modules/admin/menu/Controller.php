<?php
namespace modules\admin\menu;

class Controller extends \modules\Controller
{
    protected $breadcrubms;
    public function __construct($view, $model) {
        parent::__construct($view, $model);
        $this->breadcrumbs = array(
            array('label' => t('Dashboard'), 'href' => core()->getUrl('/route:admin/dashboard')),
            array('label' => t('List of menus'), 'href' => core()->getUrl('/route:admin/menu'))
        );
    }

    protected function showMenus()
    {
        $h1 = t('List of menus');
        $breadcrumbs = $this->breadcrumbs;
        $deleteMenuSuccess = false;
        if (isset($_POST['deleteMenu'])) {
            $this->model->deleteMenuBySysname($_POST['deleteMenu']);
            $deleteMenuSuccess = true;
        }

        $menus = $this->model->getListOfMenu();
        $this->view->data = compact('h1', 'breadcrumbs', 'menus', 'deleteMenuSuccess');
        $this->view->template = 'menus.php';
        $this->view->setTitle($h1);
        return $this->view->render();
    }

    protected function showMenueditor($menu)
    {
        $languages = $this->model->getLangList($menu['sysname']);
        $transCnt = 0;
        foreach ($languages as $l) if ($l['menuId']) $transCnt++;
        $breadcrumbs = $this->breadcrumbs;

        $this->view->data = compact('breadcrumbs', 'menuId', 'menu', 'languages', 'transCnt');
        $this->view->template = 'menu-editor.php';
        $this->view->setTitle($menu['name']);
        return $this->view->render();
    }

    public function action_default($params)
    {
        restrictAccessLevel(100);
        if ($params['id'] === '') {
            return $this->showMenus();
        } else {
            $menu = $this->model->getMenuById((int)$params['id']);
            if (!empty($menu)) {
                return $this->showMenueditor($menu);
            }
        }
        error404();
    }

    public function action_new()
    {
        restrictAccessLevel(100);
        $menu = $this->model->createMenu();
        return $this->showMenueditor($menu);
    }
    
    public function action_saveMenu($params) {
        if (!isset($params['menu'])) return 0;
        return $this->model->saveMenu(json_decode($params['menu'], true));
    }

    public function action_deleteMenu($params) {
        if (!isset($params['menuId'])) return '';
        $menu = $this->model->getMenuById($params['menuId']);
        if (!$menu) return 0;
        $this->model->deleteMenuById($params['menuId']);
        $nextMenu = $this->model->getMenuBySysname($menu['sysname']);
        if (!$nextMenu) return 0;
        return $nextMenu['id'];
    }

    public function action_createTranslation($params) {
        extract($params);
        if (!isset($sysname) || !isset($langId)) return 0;
        if (isset($duplicateFromLangId)) {
            $menu = $this->model->getMenuBySysname($sysname, $duplicateFromLangId);
            $id = $this->model->saveMenuAs($menu, $sysname, $langId);
        } else {
            $newMenu = $this->model->createMenu($sysname, $langId);
            $id = $this->model->saveMenu($newMenu);
        }
        return $id;
    }

    public function action_isSysnameUnique($params) {
        extract($params);
        if (!isset($sysname) || !isset($langId)) return false;
        $menu = $this->model->getMenuBySysname($sysname, $langId);
        return empty($menu);
    }
}