<?php
namespace modules\helpers\reverseRouting;

class Controller extends \modules\Controller
{
    public function action_default($params)
    {
        $url = core()->getUrl($_SERVER['REQUEST_URI']);
        if ($url != $_SERVER['REQUEST_URI']) {
            header('Location: '.$url);
            die();
        } else {
            error404();
        }
    }
}