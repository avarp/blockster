<?php
namespace core;

class Sqlite extends \PDO
{
    public function __construct($dbFile)
    {
        parent::__construct('sqlite:'.$dbFile);
        parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function table($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                if (!$statement->execute($this->cleanArray($sql, $array))) $statement = false;
            } else {
                return false;
            }
        } else {
            $statement = parent::query($sql);
        }
        if ($statement !== false) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function row($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                if (!$statement->execute($this->cleanArray($sql, $array))) $statement = false;
            } else {
                return false;
            }
        } else {
            $statement = parent::query($sql);
        }
        if ($statement !== false) {
            return $statement->fetch(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function value($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                if (!$statement->execute($this->cleanArray($sql, $array))) $statement = false;
            } else {
                return false;
            }
        } else {
            $statement = parent::query($sql);
        }
        if ($statement !== false) {
            $r = $statement->fetch(\PDO::FETCH_NUM);
            return empty($r) ? 0 : $r[0];
        } else {
            return false;
        }
    }

    public function exec($sql, $array=array())
    {
        if (!empty($array)) {
            $statement = parent::prepare($sql);
            if ($statement) {
                return $statement->execute($this->cleanArray($sql, $array));
            } else {
                return false;
            }
        } else {
            return parent::exec($sql);
        }
    }

    private function cleanArray($sql, $array)
    {
        if (0 < $c = substr_count($sql, '?')) {
            return array_slice($array, 0, $c);
        } else {
            foreach ($array as $key => $value) if (strpos($sql, ':'.$key) === false) unset($array[$key]);
            return $array;
        }
    }

    public function escape($string)
    {
        return SQLite3::escapeString($string);
    }
}