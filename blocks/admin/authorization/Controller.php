<?php
namespace blocks\admin\authorization;
const ONLINE_FLAG_LIFETIME = 900;//15 min

class Controller extends \blocks\Controller
{
    public function action_default()
    {
        $login = isset($_POST['login']) ? $_POST['login'] : '';
        $errors = array();

        if (isset($_SESSION['user'])) $errors[] = 'Вы авторизованы под логином <em>'.$_SESSION['user']['login'].'</em>, но ваш уровень доступа недостаточен для просмотра данной страницы. Если у вас есть учетная запись с более высоким уровнем доступа, введите логин и пароль от неё.';
        
        if (isset($_POST['authorize'])) {
            if ($this->model->logIn($_POST['login'], $_POST['password'])) {
                header('Location: '.$_SERVER['REQUEST_URI']);
                die();
            } else {
                $errors[] = 'Неверный логин или пароль.';
            }
        }
        $this->view->data = compact('login', 'errors');
        return $this->view->render();
    }

    public function action_logOut()
    {
        $this->model->logOut();
        header('Location: '.$_SERVER['REQUEST_URI']);
        die();
    }

    public function action_trackingUsers()
    {
        if (isset($_SESSION['user'])) $this->model->updateTrackingTimestamp();
    }

    public static function addTrackingScript($block)
    {
        if ($block['name'] != 'page') return;
        if (isset($_SESSION['user'])) {
            core()->sendMessage('page', 'addJsText',
                "setInterval(function(){".
                    "var xhr = new XMLHttpRequest();".
                    "xhr.open('GET', '".SITE_URL."/ajax/admin/authorization::trackingUsers', true);".
                    "xhr.send()".
                "}, ".(ONLINE_FLAG_LIFETIME*1000).")"
            );
            if (time() - $_SESSION['user']['trackingTimestamp'] > ONLINE_FLAG_LIFETIME) {
                $model = new Model;
                $model->updateTrackingTimestamp();
            }
        }
    }
}