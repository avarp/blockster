<?php
namespace blocks\frontend\users;
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

    public function newUser()
    {
        return array(
            'id' => 0,
            'login' => '',
            'password' => '',
            'accessLevel' => 0,
            'email' => 0,
            'isOnline' => 0,
            'trackingTimestamp' => time(),
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
        if ($filter['onlineStatus'] == 'online') {
            $where[] = 'isOnline = 1';
            $where[] = time().' - trackingTimestamp < '.ONLINE_FLAG_LIFETIME;
        } elseif ($filter['onlineStatus'] == 'offline') {            
            $where[] = '(isOnline == 0 OR '.time().' - trackingTimestamp > '.ONLINE_FLAG_LIFETIME.')';
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
            unset($user['id']);
            $user['password'] = md5($user['password']);
            $fields = '('.implode(',', array_keys($user)).')';
            $placeholders = '(:'.implode(',:', array_keys($user)).')';
            $sql = "INSERT INTO users $fields VALUES $placeholders";
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

    public function updateTrackingTimestamp()
    {
        $_SESSION['user']['isOnline'] = 1;
        $_SESSION['user']['trackingTimestamp'] = time();
        $this->dbh->exec("UPDATE users SET isOnline=?, trackingTimestamp=? WHERE id=?", array(1, time(), $_SESSION['user']['id']));
    }
}