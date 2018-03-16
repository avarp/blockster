<?php
namespace modules\admin\authorization;

class Model
{
    private $dbh;
    public function __construct()
    {
        $this->dbh = core()->getDbh();
    }

    public function logIn($login ,$password)
    {
        $user = $this->dbh->row("SELECT * FROM users WHERE login=?", array($login));
        if (!empty($user) && password_verify($password, $user['password'])) {
            if (isset($_SESSION['user'])) $this->logOut();
            $_SESSION['user'] = $user;
            $this->updateTrackingTimestamp();
            return true;
        } else {
            return false;
        }
    }

    public function logOut()
    {
        if (isset($_SESSION['user'])) unset($_SESSION['user']);
    }

    public function updateTrackingTimestamp()
    {
        $_SESSION['user']['wasOnline'] = time();
        $this->dbh->exec("UPDATE users SET wasOnline=? WHERE id=?", array(time(), $_SESSION['user']['id']));
    }
}