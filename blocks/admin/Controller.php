<?php
namespace blocks\admin;

class Controller extends \blocks\Controller
{
    public function action_default()
    {
        restrictAccessLevel(50);
        if (!$_GET['adminUrl']) return block('admin/dashboard');
        $actions = explode('/', $_GET['adminUrl']);
        $block = '';
        $params = array();

        if ($actions) for ($i=count($actions)-1; $i>=0; $i--) {
            if ($i > 0) {
                $slice = array_slice($actions, 0, $i);
                $controller = '\\blocks\\admin\\'.implode('\\', $slice).'\\Controller';
                if (class_exists($controller) && method_exists($controller, 'action_'.$actions[$i])) {
                    $block = 'admin/'.implode('/', $slice).'::'.$actions[$i];
                    $params = array_slice($actions, $i+1);
                    break;
                }
            }
            $slice = array_slice($actions, 0, $i+1);
            $controller = '\\blocks\\admin\\'.implode('\\', $slice).'\\Controller';
            if (class_exists($controller) && method_exists($controller, 'action_default')) {
                $block = 'admin/'.implode('/', $slice);
                $params = array_slice($actions, $i);
                break;
            }
        }

        if (!$block) error404();
        
        return block($block, $params);
    }
}