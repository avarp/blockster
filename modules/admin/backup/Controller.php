<?php
namespace modules\admin\backup;

class Controller extends \modules\Controller
{
    public function action_default()
    {
        $cfg = json_decode(file_get_contents(ROOT_DIR.'/core/database/dbconn.json'), true);
        try {
            $dump = new \Ifsnop\Mysqldump\Mysqldump(
                "mysql:host=$cfg[host];dbname=$cfg[database]",
                $cfg['user'],
                $cfg['password'],
                array('add-drop-table' => true)
            );
            $dump->start(ROOT_DIR.'/dump.sql');
        } catch (\Exception $e) {
            echo 'mysqldump-php error: '.$e->getMessage();
        }
    }


    public function action_installSystem()
    {
        if (!is_null(core()->dbh)) error404();
        return '<h1>Install system</h1>';
    }
}