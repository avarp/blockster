<?php
namespace blocks\frontend\users;
const ONLINE_FLAG_LIFETIME = 900;//15 min

class Controller extends \blockster\Controller
{
    public function actionIndex()
    {
        restrictAccessLevel(100);
        
        if (isset($_POST['deleteUser'])) $this->model->deleteUser(intval($_POST['deleteUser']));

        $errors = '';
        $success = false;
        $user = $this->model->newUser();

        if (isset($_POST['saveUser'])) {
            $user = array_merge($user, $_POST['user']);

            if ($user['login'] == '') $errors[] = 'Укажите логин.';
            $uid = $this->model->getIdByLogin($user['login']);
            if ($uid != 0 && $user['id'] != $uid) $errors[] = 'Логин занят.';
            if ($user['id'] == 0 && empty($user['password'])) $errors[] = 'Укажите пароль.';
            if ($user['email'] == '') $errors[] = 'Укажите E-Mail.';
            elseif (strpos($user['email'], '@') === false) $errors[] = 'E-Mail указан неверно.';
            if ($user['password'] != $_POST['passwordConfirm']) $errors[] = 'Пароль повторен неверно.';
            if ($user['accessLevel'] > $_SESSION['user']['accessLevel'])
                $errors[] = 'Вы не можете назначить уровень доступа выше своего.';

            if (empty($errors)) {
                if (!$this->model->saveUser($user)) {
                    $errors = 'Ошибка записи в базу данных. Попробуйте сохранить еще раз.';
                } else {
                    $success = true;
                }
            }
        }

        if (isset($_POST['dropFilter'])) unset($_SESSION['usersFilter']);

        $haveFilter = isset($_SESSION['usersFilter']);

        $filter = $haveFilter ? $_SESSION['usersFilter'] : array(
            'accessLevelFrom' => 0,
            'accessLevelTo' => 0,
            'loginLike' => '',
            'emailLike' => '',
            'onlineStatus' => 'any',
        );

        if (isset($_POST['setFilter'])) {
            $filter = array_merge($_POST['filter']);
            $filter['accessLevelFrom'] = abs(intval($filter['accessLevelFrom']));
            $filter['accessLevelTo'] = abs(intval($filter['accessLevelTo']));
            if ($filter['accessLevelFrom'] > $filter['accessLevelTo']) {
                $filter['accessLevelFrom'] = 0;
                $filter['accessLevelTo'] = 0;
            }
            $_SESSION['usersFilter'] = $filter;
            $haveFilter = true;
        }

        $itemsPerPage = 18;
        $itemsCount = $this->model->countUsers($filter);
        $numPages = ceil($itemsCount/$itemsPerPage);
        $thisPage = isset($_GET['page']) ? intval($_GET['page']) : 0;
        if ($thisPage < 0) $thisPage = 0;
        elseif ($thisPage > $numPages) $thisPage = $numPages;
        
        $users = $this->model->readUsers(($thisPage - 1)*$itemsPerPage, $itemsPerPage, $filter);
        return $this->view->render(compact(
            'users', 'user', 'itemsCount', 'errors',
            'success', 'filter', 'haveFilter', 'thisPage',
            'numPages'
        ));
    }



    public function actionLogIn()
    {
        $login = isset($_POST['login']) ? $_POST['login'] : '';
        $errors = array();

        if (isset($_SESSION['user'])) $errors[] = 'Вы авторизованы, но ваш уровень доступа недостаточен для просмотра данной страницы. Если у вас есть учетная запись с более высоким уровнем доступа, введите логин и пароль от неё.';
        
        if (isset($_POST['authorize'])) {
            if ($this->model->logIn($_POST['login'], $_POST['password'])) {
                \blockster\Core::getInstance()->redirect($_SERVER['REQUEST_URI']);
            } else {
                $errors[] = 'Неверный логин или пароль';
            }
        }

        $this->view->setTemplate('authForm.tpl');
        return $this->view->render(compact('login', 'errors'));
    }

    

    public function actionLogOut()
    {
        $this->model->logOut();
        \blockster\Core::getInstance()->redirect($_SERVER['REQUEST_URI']);
    }


    public static function addTrackingScript($page)
    {
        if (isset($_SESSION['user'])) {
            $page->embedJs(
                "setInterval(function(){".
                    "var xhr = new XMLHttpRequest();".
                    "xhr.open('GET', '".SITE_URL."/ajax/stdlib/users::ajaxUpdateTrackingTimestamp', true);".
                    "xhr.send()".
                "}, ".(ONLINE_FLAG_LIFETIME*1000).")"
            );
            if (time() - $_SESSION['user']['trackingTimestamp'] > ONLINE_FLAG_LIFETIME) {
                $model = new Model;
                $model->updateTrackingTimestamp();
            }
        }
    }


    public function ajaxUpdateTrackingTimestamp()
    {
        if (isset($_SESSION['user'])) $this->model->updateTrackingTimestamp();
    }
}