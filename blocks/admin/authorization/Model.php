<?php
namespace blocks\admin\authorization;
use services\database\Database;

class Model
{
    private $dbh;
    public function __construct()
    {
        $this->dbh = new Database;
    }

    public function logIn($login ,$password)
    {
        $user = $this->dbh->row("SELECT * FROM users WHERE login=? AND password=?", array($login, md5($password)));
        if (!empty($user)) {
            if (isset($_SESSION['user'])) $this->logOut();
            $_SESSION['user'] = $user;
            $this->dbh->exec("UPDATE users SET isOnline=?, trackingTimestamp=? WHERE id=?", array(1, time(), $user['id']));
            return true;
        } else {
            return false;
        }
    }

    public function logOut()
    {
        if (isset($_SESSION['user'])) {
            $this->dbh->exec("UPDATE users SET isOnline=?, trackingTimestamp=? WHERE id=?", array(0, time(), $_SESSION['user']['id']));
            unset($_SESSION['user']);
        }
    }

    public function updateTrackingTimestamp()
    {
        $_SESSION['user']['isOnline'] = 1;
        $_SESSION['user']['trackingTimestamp'] = time();
        $this->dbh->exec("UPDATE users SET isOnline=?, trackingTimestamp=? WHERE id=?", array(1, time(), $_SESSION['user']['id']));
    }
}