<?php
namespace modules\admin\authorization;

class Model
{
    private $dbh;
    public function __construct()
    {
        $this->dbh = core()->dbh;
    }

    public function logIn($login ,$password)
    {
        $user = $this->dbh->row("SELECT * FROM users WHERE login=?", array($login));
        if (!empty($user) && password_verify($password, $user['password'])) {
            if (isset($_SESSION['user'])) $this->logOut();
            $_SESSION['user'] = $user;
            $this->updateTrackingTimestamp();
            core()->eventBus->dispatchEvent('onLogIn');
            return true;
        } else {
            core()->eventBus->dispatchEvent('onLogInFail');
            return false;
        }
    }

    public function logOut()
    {
        if (isset($_SESSION['user'])) {
            core()->eventBus->dispatchEvent('onLogOut');
            unset($_SESSION['user']);
        }
    }

    public function updateTrackingTimestamp()
    {
        $_SESSION['user']['wasOnline'] = time();
        $this->dbh->exec("UPDATE users SET wasOnline=FROM_UNIXTIME(?) WHERE id=?", array(time(), $_SESSION['user']['id']));
    }
}