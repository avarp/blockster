<?php
namespace modules\admin\adminer;

class Controller extends \modules\Controller
{
    public function action_default()
    {
        restrictAccessLevel(100);
        if (empty($_GET)) {
            $_GET['sqlite'] = '';
            $_GET['username'] = '';
            $db = core()->dbh->row("PRAGMA database_list");
            $_GET['db'] = str_replace(ROOT_DIR.DS, '', $db['file']);
        }
        for ($i=ob_get_level(); $i>0; $i--) ob_get_clean();
        require(__DIR__.'/adminer.php');
    }
}