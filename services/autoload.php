<?php
spl_autoload_register(function($className) {
    $file = str_replace('\\', '/', $className);
    if ($file{0} != '/') $file = '/'.$file;
    $file = ROOT_DIR.$file.'.php';
    if (file_exists($file)) include($file);
});