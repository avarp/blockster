<?php
namespace services\database;

class Database extends \PDO
{
    public function __construct()
    {
        parent::__construct('sqlite:'.__DIR__.'/db.sqlite');
    }

    public function getDriverName()
    {
        return 'sqlite';
    }

    public function table($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                if (!$statement->execute($this->prepareArray($sql, $array))) $statement = false;
            } else {
                trigger_error('Bad SQL statement: "'.$sql.'"', E_USER_WARNING);
                return false;
            }
        } else {
            $statement = parent::query($sql);
        }
        if ($statement !== false) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            trigger_error('Bad SQL query: "'.$sql.'"', E_USER_WARNING);
            return false;
        }
    }

    public function row($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                if (!$statement->execute($this->prepareArray($sql, $array))) $statement = false;
            } else {
                trigger_error('Bad SQL statement: "'.$sql.'"', E_USER_WARNING);
                return false;
            }
        } else {
            $statement = parent::query($sql);
        }
        if ($statement !== false) {
            return $statement->fetch(\PDO::FETCH_ASSOC);
        } else {
            trigger_error('Bad SQL query: "'.$sql.'"', E_USER_WARNING);
            return false;
        }
    }

    public function value($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                if (!$statement->execute($this->prepareArray($sql, $array))) $statement = false;
            } else {
                trigger_error('Bad SQL statement: "'.$sql.'"', E_USER_WARNING);
                return false;
            }
        } else {
            $statement = parent::query($sql);
        }
        if ($statement !== false) {
            $r = $statement->fetch(\PDO::FETCH_NUM);
            return empty($r) ? 0 : $r[0];
        } else {
            trigger_error('Bad SQL query: "'.$sql.'"', E_USER_WARNING);
            return false;
        }
    }

    public function exec($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                return $statement->execute($this->prepareArray($sql, $array));
            } else {
                trigger_error('Bad SQL statement: "'.$sql.'"', E_USER_WARNING);
                return false;
            }
        } else {
            return parent::exec($sql);
        }
    }

    private function prepareArray($sql, $array)
    {
        if (0 < $c = substr_count($sql, '?')) {
            return array_slice($array, 0, $c);
        } else {
            foreach ($array as $key => $value) if (strpos($sql, ':'.$key) === false) unset($array[$key]);
            return $array;
        }
    }
}