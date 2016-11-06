<?php
header('Content-Type: text/html; charset=utf-8');

if (PHP_VERSION_ID < 50200) die('ERROR #1: You need PHP version 5.3.0 or greater.');

$installedExtensions = get_loaded_extensions();
$requiredExtensions = array('sqlite3', 'PDO', 'curl', 'pdo_sqlite', 'zip' ,'gd' ,'mbstring');
$notInstalledExtensions = array_diff($requiredExtensions, $installedExtensions);
if (!empty($notInstalledExtensions)) die('ERROR #2: Please install PHP extensions: '.implode(', ', $notInstalledExtensions).'.');

if (get_magic_quotes_gpc() || get_magic_quotes_runtime()) die('ERROR #3: magic_quotes directive is enabled. Switch it off.');

$thisDir = __DIR__.'/';
$rootDir = $_SERVER['DOCUMENT_ROOT'];

if ($rootDir.$_SERVER['REQUEST_URI'] != $thisDir) {
    header('Location: '.str_replace($rootDir, '', $thisDir));
    die();
}

$protocol = (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
$siteUrl = $protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if (substr($siteUrl, -1) == '/') $siteUrl = substr($siteUrl, 0, strlen($siteUrl)-1);

if (!file_exists('config.local.php')) {
    file_put_contents('config.local.php',
"<?php
#require('install.php');
define('SITE_URL', '$siteUrl');
define('ROOT_DIR', getcwd());
define('SITE_NAME', 'The Site');
define('CMS_START', microtime(true));
define('CMS_VERSION', 0.1);"
    );
} else {
    $config = file_get_contents('config.php');
    $config = str_replace('~SITE_URL~', $siteUrl, $config);
    $config = str_replace("require('install.php')", "#require('install.php')", $config);
    file_put_contents('config.local.php', $config);
}

header('Location: '.$_SERVER['REQUEST_URI']);
die();


