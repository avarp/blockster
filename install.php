<?php
$errors = array();
if (PHP_VERSION_ID < 50200) $errors[] = 'Blockster requires PHP 5.3.0 or greater.';

$installedExtensions = get_loaded_extensions();
$requiredExtensions = array('sqlite3', 'PDO', 'curl', 'pdo_sqlite', 'zip' ,'gd' ,'mbstring');
$notInstalledExtensions = array_diff($requiredExtensions, $installedExtensions);
if (!empty($notInstalledExtensions)) {
    $errors[] = 'Blockster requires PHP extensions: '.implode(', ', $notInstalledExtensions).'.';
}

if (get_magic_quotes_gpc() || get_magic_quotes_runtime()) {
    $errors[] = 'Directive "magic_quotes" is enabled. Please disable it.';
}

if (file_exists('envbackup.local.php')) require('envbackup.local.php');
$siteName = isset($_POST['siteName']) ? $_POST['siteName'] : (defined('SITE_NAME') ? SITE_NAME : 'The Site');
$installUri = isset($_POST['installUri']) ? $_POST['installUri'] : $_SERVER['REQUEST_URI'];
$hostUrl = ((!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://').$_SERVER['HTTP_HOST'];

if (empty($siteName)) $errors[] = 'Имя сайта не указано.';
if (empty($installUri)) $installUri = '/';

if (strpos($installUri, '?') !== false) {
    $errors[] = 'Wrong URL. You specified URL that contains GET parameters.';
} else {
    /* Check installUri - is it points to directory in whish we install Blockster?
    #1 Create txt file in root directory of installation
    #2 Retrieve them using installUri and GET-parameter. If installUri is wrong, .htaccess will redirects us back to this script, and we get a recursion. To prevent it we use GET-parameter (potential recursion is brokes at line 24 of this script).
    #3 Check content of retrieved file. If it is wrong, display error.
    #4 Delete file.
    */
    $key = time();
    #1
    file_put_contents($key.'.txt', $key);
    $url = $hostUrl.rtrim($installUri, '/').'/'.$key.'.txt?getParam=1';
    #2
    if (file_get_contents($url) != $key) {
        #3
        if ($_SERVER['DOCUMENT_ROOT'] != ROOT_DIR) {
            $errors[] = 'Wrong URL. You attempted to install Blockster into subfolder of your host. So URL must point to it.';
        } else {
            $errors[] = 'Wrong URL. You attempted to install Blockster into subfolder of your host. Try empty value or "/".';
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
            "define('INSTALL_URI', '$installUri');\n".
            "define('SITE_NAME', '".str_replace("'", "\\'", $siteName)."');\n".
            "define('SITE_THEME', 'default');"
        );
    }
    header('Location: '.$installUri);
    die();
}

require('themes/admin/install.tpl');
die();