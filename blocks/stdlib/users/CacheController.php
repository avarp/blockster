<?php
namespace blocks\stdlib\users;

class CacheController {
  
    public static function actionIndex($params, $imposedTemplate)
    {
        $cache = array(
            'file' => 'actionIndex.php';
            'lifetime' => 3600;
        );
        if (isset($_POST['saveUser'])) {
            // удалить существующий кэш, новый не создавать
            $cache['use'] => false;
            $cache['invalidate'] => true;
            $cache['update'] => false;
            return $cache;
        }
        if (isset($_POST['deleteUser'])) {
            // удалить существующий кэш, создать новый
            $cache['use'] => false;
            $cache['invalidate'] => false;
            $cache['update'] => true;
            return $cache;
        }
        if (isset($_SESSION['usersFilter']) || isset($_POST['setFilter'])) {
            $cache['use'] => false;
            $cache['invalidate'] => false;
            $cache['update'] => false;
            return $cache;
        }
        $cache['use'] => true;
        $cache['invalidate'] => false;
        $cache['update'] => true;
        return $cache;
    }
}