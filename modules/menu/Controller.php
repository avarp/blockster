<?php
namespace modules\menu;

class Controller extends \modules\Controller
{
    /**
     * Get menu. All parameters should be compacted into one array.
     * @param string $name system name of menu
     * @param string $lang isoCode of language (optional)
     * @param string $template name of template file (optional)
     * @return string result html of menu
     */
    public function action_default($params)
    {
        extract($params);
        if (!isset($name)) throw new \Exception('Name of menu not specified.');
        if (!isset($lang)) $lang = core()->lang['isoCode'];

        $menu = $this->model->getMenu($name, $lang);
        $menu = core()->eventBus->dispatchEventFilter('onShowMenu', $menu);
        
        if (isset($template)) $this->view->template = $template;
        $this->view->data['menu'] = $menu;
        return $this->view->render();
    }
}