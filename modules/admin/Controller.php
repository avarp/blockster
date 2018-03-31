<?php
namespace modules\admin;

class Controller extends \modules\Controller
{
    public function action_default($params)
    {
        restrictAccessLevel(50);
        if (!isset($params['adminUrl']) || empty($params['adminUrl'])) return module('admin/dashboard');
        
        $actions = explode('/', $params['adminUrl']);
        if ($actions) for ($i=count($actions)-1; $i>=0; $i--) {
            if ($i > 0) {
                $name = 'admin/'.implode('/', array_slice($actions, 0, $i)).'::'.$actions[$i];
                $params = array_slice($actions, $i+1);
            } else {
                $name = 'admin/'.implode('/', array_slice($actions, 0, $i+1));
                $params = array_slice($actions, $i);
            }
            if (core()->isModuleExists($name)) {
                return module($name, $params);
            }
        }

        error404();
    }
}