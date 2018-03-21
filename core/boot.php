<?php
class BootException extends Exception {};

function checkServer() {
    if (PHP_VERSION_ID <= 50500) throw new BootException('Blockster requires PHP version 5.5.0 or later');

    $installedExtensions = get_loaded_extensions();
    $requiredExtensions = array('sqlite3', 'PDO', 'curl', 'pdo_sqlite', 'zip' ,'gd' ,'mbstring');
    $notInstalledExtensions = array_diff($requiredExtensions, $installedExtensions);
    if (!empty($notInstalledExtensions)) {
        throw new BootException('Not installed these required extensions of PHP: '.implode(', ', $notInstalledExtensions));
    }

    if (get_magic_quotes_gpc() || get_magic_quotes_runtime()) {
        throw new BootException('Directive "magic_quotes" is enabled. Switch it off.');
    }

    if ($_SERVER['DOCUMENT_ROOT'] != ROOT_DIR) {
        throw new BootException('System is not in root directory of your host.');
    }
}

function error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

function exception_handler(Throwable $e) {
    $errors = array(
        E_ERROR => 'Fatal error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core fatale error',
        E_CORE_WARNING => 'Core warning',
        E_COMPILE_ERROR => 'Compile error',
        E_COMPILE_WARNING => 'Compile warning',
        E_USER_ERROR => 'User-generated error',
        E_USER_WARNING => 'User-generated warning',
        E_USER_NOTICE => 'User-generated notice',
        E_STRICT => 'Strict error',
        E_RECOVERABLE_ERROR => 'Fatal error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'Deprecated'
    );
    if ($e instanceof ErrorException) {
        $errname = $errors[$e->getSeverity()];
    } else {
        $errname = 'Uncaught '.get_class($e);
    }
    echo '<b>'.$errname.': </b>'.$e->getMessage().' in <b>'.$e->getFile().'</b> on line <b>'.$e->getLine().'</b><pre style="padding:1em;background:#f0f0f0;border:1px solid #ccc;border-radius: 3px;">'.$e->getTraceAsString().'</pre>';
}

function bootModules($dir) {
    $names = scandir($dir);
    foreach ($names as $f) if ($f != '.' && $f != '..') if (is_dir($dir.DS.$f)) {
        bootModules($dir.DS.$f);
    } else {
        if ($f == 'events.php') include($dir.DS.$f);
    }
}

checkServer();
set_error_handler('error_handler');
set_exception_handler('exception_handler');
require(file_exists('vendor/autoload.php') ? 'vendor/autoload.php' : 'core/autoload.php');
require('core/functions.php');
bootModules(ROOT_DIR.DS.'modules');