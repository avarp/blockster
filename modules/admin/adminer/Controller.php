<?php
namespace modules\admin\adminer;

class Controller extends \modules\Controller
{
    public function action_default()
    {
        restrictAccessLevel(100);
        for ($i=ob_get_level(); $i>0; $i--) ob_get_clean();
        require(__DIR__.'/adminer.php');
        die();
    }
}