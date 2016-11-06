<?php
namespace blocks\stdlib\users;
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
            if (isset($_SESSION['login'])) $this->logOut();
            $_SESSION['login'] = $user['login'];
            $_SESSION['userId'] = $user['id'];
            $_SESSION['accessLevel'] = $user['accessLevel'];
            $this->dbh->exec("UPDATE users SET online=? WHERE id=?", array(1, $user['id']));
            return true;
        } else {
            return false;
        }
    }

    public function logOut()
    {
        if (isset($_SESSION['login'])) {
            $this->dbh->exec("UPDATE users SET online=? WHERE login=?", array(0, $_SESSION['login']));
            unset($_SESSION['userId']);
            unset($_SESSION['login']);
            unset($_SESSION['accessLevel']);
        }
    }

    public function newUser()
    {
        return array(
            'id' => 0,
            'login' => '',
            'password' => '',
            'accessLevel' => 0,
            'email' => 0,
            'online' => 0,
        );
    }

    protected function parseFilter($filter)
    {
        $where = array();
        if ($filter['accessLevelFrom'] > 0) {
            $where[] = "accessLevel >= $filter[accessLevelFrom]";
        }
        if ($filter['accessLevelTo'] > 0) {
            $where[] = "accessLevel <= $filter[accessLevelTo]";
        }
        if (!empty($filter['loginLike'])) {
            $where[] = "login LIKE '%$filter[loginLike]%'";
        }
        if (!empty($filter['emailLike'])) {
            $where[] = "email LIKE '%$filter[emailLike]%'";
        }
        if ($filter['onlineStatus'] != 'any') {
            $where[] = "online = $filter[onlineStatus]";
        }
        if (!empty($where)) $where = 'WHERE '.implode(' AND ', $where);
        else $where = '';
        return $where;
    }

    public function readUsers($offset, $limit, $filter)
    {
        $where = $this->parseFilter($filter);
        return $this->dbh->table("SELECT * FROM users $where LIMIT ?,?", array($offset, $limit));
    }

    public function countUsers($filter)
    {
        $where = $this->parseFilter($filter);
        return $this->dbh->value("SELECT COUNT(id) FROM users $where");
    }

    public function saveUser($user)
    {
        if ($user['id'] == 0) {
            $user['password'] = md5($user['password']);
            $sql = "INSERT INTO users (login, password, accessLevel, email, online) VALUES (:login, :password, :accessLevel, :email, :online)";
        } else {
            if (!empty($user['password'])) {
                $user['password'] = md5($user['password']);
                $sql = "UPDATE users SET login=:login, password=:password, accessLevel=:accessLevel, email=:email WHERE id=:id";
            } else {
                $sql = "UPDATE users SET login=:login, accessLevel=:accessLevel, email=:email WHERE id=:id";
            }
        }
        return $this->dbh->exec($sql, $user);
    }

    public function deleteUser($id)
    {
        $this->dbh->exec("DELETE FROM users WHERE id=?", array($id));
    }

    public function getIdByLogin($login)
    {
        return $this->dbh->value("SELECT id FROM users WHERE login=?", array($login));
    }
}