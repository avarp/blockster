<?php
$errors = array();
if (PHP_VERSION_ID < 50200) $errors[] = 'Необходима версия PHP 5.3.0 или выше.';

$installedExtensions = get_loaded_extensions();
$requiredExtensions = array('sqlite3', 'PDO', 'curl', 'pdo_sqlite', 'zip' ,'gd' ,'mbstring');
$notInstalledExtensions = array_diff($requiredExtensions, $installedExtensions);
if (!empty($notInstalledExtensions)) {
    $errors[] = 'Необходимые расширения PHP не установлены: '.implode(', ', $notInstalledExtensions).'.';
}

if (get_magic_quotes_gpc() || get_magic_quotes_runtime()) {
    $errors[] = 'Директива "magic_quotes" включена. Выключите её.';
}

if (file_exists('envbackup.local.php')) require('envbackup.local.php');
$siteName = isset($_POST['siteName']) ? $_POST['siteName'] : (defined('SITE_NAME') ? SITE_NAME : 'The Site');
$installUri = isset($_POST['installUri']) ? $_POST['installUri'] : $_SERVER['REQUEST_URI'];
$hostUrl = ((!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://').$_SERVER['HTTP_HOST'];

if (empty($siteName)) $errors[] = 'Имя сайта не указано.';
if (empty($installUri)) $installUri = '/';

if (strpos($installUri, '?') !== false) {
    $errors[] = 'Указанный адрес для установки содержит GET параметры.';
} else {
    /* Проверка installUri - указывает ли он на папку, в которую устанавливается проект.
    #1 Создаем в корне проекта тестовый файл с текстом
    #2 Затем запрашиваем его по HTTP, используя installUri и GET-параметр, который в случае неверного installUri предотвратит рекурсию. Если файла по запрошенному URL не будет, .htaccess редиректит нас на index.php и оттуда на install.php, но т.к. мы использовали GET-параметр, то в данный блок else мы не попадем. Т.е. file_get_contents вместо искомого файла вернет HTML формы установки.
    #3 Проверяем полученный текст на соответствие заданному. Если не соответствует, выводим ошибки.
    #4 Удаляем тестовый файл
    */
    $key = time();
    #1
    file_put_contents($key.'.txt', $key);
    $url = $hostUrl.rtrim($installUri, '/').'/'.$key.'.txt?getParam=1';
    #2
    if (file_get_contents($url) != $key) {
        #3
        if ($_SERVER['DOCUMENT_ROOT'] != ROOT_DIR) {
            $errors[] = 'Указанный адрес для установки неверный. Вы пытаетесь установить CMS не в корневую папку хоста, поэтому адрес должен указывать на саму подпапку.';
        } else {
            $errors[] = 'Указанный адрес для установки неверный. Вы пытаетесь установить CMS в корневую папку хоста, поэтому адрес должен быть пустым или "/".';
        }
    }
    #4
    unlink($key.'.txt');
}

if (isset($_POST['install']) && empty($errors)) {
    if (file_exists('envbackup.local.php')) {
        $env = file_get_contents('envbackup.local.php');
        $env = preg_replace('/define[^;]+?INSTALL_URI[^;]+?;/', "define('INSTALL_URI','$installUri');", $env);
        $env = preg_replace('/define[^;]+?SITE_NAME[^;]+?;/', "define('SITE_NAME','".str_replace("'", "\\'", $siteName)."');\n", $env);
        file_put_contents('environment.local.php', $env);
        unlink('envbackup.local.php');
    } else {
        file_put_contents('environment.local.php',
            "<?php\n".
            "define('INSTALL_URI','$installUri');\n".
            "define('SITE_NAME','".str_replace("'", "\\'", $siteName)."');\n"
        );
    }
    header('Location: '.$installUri);
    die();
}

require('templates/backend/install.tpl');
die();