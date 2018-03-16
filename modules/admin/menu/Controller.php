<?php
namespace modules\admin\menu;

class Controller extends \modules\Controller
{
    protected $breadcrubms;
    public function __construct($view, $model) {
        parent::__construct($view, $model);
        $this->breadcrumbs = array(
            array('label' => t('Dashboard'), 'href' => core()->getUrl('route:admin')),
            array('label' => t('List of menus'), 'href' => core()->getUrl('route:admin>menu'))
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
        foreach ($languages as $l) if ($l['isTranslated']) $transCnt++;
        $breadcrumbs = $this->breadcrumbs;

        $this->view->data = compact('breadcrumbs', 'menuId', 'menu', 'languages', 'transCnt');
        $this->view->template = 'menu-editor.php';
        $this->view->setTitle($menu['label']);
        return $this->view->render();
    }

    public function action_default($params)
    {
        restrictAccessLevel(100);
        switch (count($params)) {
            case 1:
            return $this->showMenus();

            case 2:
            if ($params[1] == 'new') {
                $menu = $this->model->createMenu();
                return $this->showMenueditor($menu);
            }
            break;

            case 3:
            $menu = $this->model->getMenuBySysname($params[1], $params[2]);
            if (!empty($menu)) {
                return $this->showMenueditor($menu);
            }
            break;
        }
        error404();
    }
    
    public function action_saveMenu($params) {
        if (!isset($params['menu'])) return 0;
        return $this->model->saveMenu(json_decode($params['menu'], true));
    }

    public function action_deleteMenu($params) {
        if (!isset($params['menuId'])) return '';
        $menu = $this->model->getMenuInfoById($params['menuId']);
        if (!$menu) return '';
        $this->model->deleteMenuById($params['menuId']);
        $nextMenu = $this->model->getMenuInfoBySysname($menu['sysname']);
        if (!$nextMenu) return '';
        $urlToNextMenu = core()->getUrl('route:admin>menu/'.$nextMenu['sysname'].'/'.$nextMenu['lang']);
        return $urlToNextMenu;
    }

    public function action_createTranslation($params) {
        extract($params);
        if (!isset($sysname) || !isset($lang)) return false;
        if (isset($duplicateFromLang)) {
            $menu = $this->model->getMenuBySysname($sysname, $duplicateFromLang);
            $id = $this->model->saveMenuAs($menu, $sysname, $lang);
        } else {
            $newMenu = $this->model->createMenu($sysname, $lang);
            $id = $this->model->saveMenu($newMenu);
        }
        return $id != 0;
    }

    public function action_isSysnameUnique($params) {
        extract($params);
        if (!isset($sysname) || !isset($lang)) return false;
        $menuInfo = $this->model->getMenuInfoBySysname($sysname, $lang);
        return empty($menuInfo);
    }
}