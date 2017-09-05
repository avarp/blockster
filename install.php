<?php
$errors = array();
if (PHP_VERSION_ID < 50200) $errors[] = 'Blockster требует версии PHP 5.3.0 или выше.';

$installedExtensions = get_loaded_extensions();
$requiredExtensions = array('sqlite3', 'PDO', 'curl', 'pdo_sqlite', 'zip' ,'gd' ,'mbstring');
$notInstalledExtensions = array_diff($requiredExtensions, $installedExtensions);
if (!empty($notInstalledExtensions)) {
    $errors[] = 'Не установлены следующие расширения PHP: '.implode(', ', $notInstalledExtensions).'.';
}

if (get_magic_quotes_gpc() || get_magic_quotes_runtime()) {
    $errors[] = 'Директива "magic_quotes" включена. Выключите её.';
}

if ($_SERVER['DOCUMENT_ROOT'] != ROOT_DIR) {
    $errors[] = 'Вы пытаетесь установить Blockster в поддиректорию хоста. Можно устанавливать только в корневую папку хоста.';
}

$login = isset($_POST['login']) ? trim($_POST['login']) : 'admin';

if (isset($_POST['install'])) {

    if (empty($login)) $errors[] = 'Логин не может быть пустым.';
    if (empty($_POST['password'])) $errors[] = 'Укажите пароль администратра.';
    if ($_POST['password'] != $_POST['password2']) $errors[] = 'Вы неверно повторили пароль.';

    if (empty($errors)) {
        require('services/database/Database.php');
        $dbh = new \services\database\Database;
        $ok = $dbh->exec("UPDATE users SET login=?, password=? WHERE id=1", array($login, md5($_POST['password'])));
        if (!$ok) $errors[] = 'Сбой в работе базы данных SQLite.';
    }

    if (empty($errors)) {
        if (file_exists('reconfig.php')) {
            rename('reconfig.php', 'config.php');
        } else {
            file_put_contents('config.php',
                "<?php\n".
                "define('SITE_THEME', 'default');"
            );
        }
        header('Location: /');
        die();
    }
}

require('themes/admin/templates/page/install.tpl');
die();