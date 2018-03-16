<?php
namespace modules\admin\authorization;
const ONLINE_FLAG_LIFETIME = 900;//15 min

class Controller extends \modules\Controller
{
    public function action_default()
    {
        $login = isset($_POST['login']) ? $_POST['login'] : '';
        $errors = array();

        if (isset($_SESSION['user'])) $errors[] = sprintf(t('You have logged in as %s, but your access level is too low for accessing this page. If you have the account with higher access level, enter login and password from it.'), '<em>'.$_SESSION['user']['login'].'</em>');
        
        if (isset($_POST['authorize'])) {
            if ($this->model->logIn($_POST['login'], $_POST['password'])) {
                header('Location: '.$_SERVER['REQUEST_URI']);
                die();
            } else {
                $errors[] = t('Wrong login or password');
            }
        }
        $this->view->data = array_merge($this->view->data, compact('login', 'errors'));
        $this->view->setTitle(t('Access denied!'));
        return $this->view->render();
    }

    public function action_trackingUsers()
    {
        if (isset($_SESSION['user'])) $this->model->updateTrackingTimestamp();
    }

    public static function logOutByPost()
    {
        if (isset($_POST['logOut'])) {
            $model = new Model;
            $model->logOut();
            header('Location: '.$_SERVER['REQUEST_URI']);
            die();
        } 
    }

    public static function addTrackingScript($module)
    {
        if (isset($_SESSION['user'])) {
            core()->broadcastMessage('addJsText',
                "setInterval(function(){".
                    "var xhr = new XMLHttpRequest();".
                    "xhr.open('GET', '".SITE_URL."/ajax/admin/authorization::trackingUsers', true);".
                    "xhr.send()".
                "}, ".(ONLINE_FLAG_LIFETIME*1000).")"
            );
        }
    }
}