<?php
session_start();
define('ROOT_DIR', __DIR__);
define('CMS_START', microtime(true));
define('CMS_VERSION', '0.5.0');

if (!file_exists('environment.local.php')) require('install.php');
else require('environment.local.php');
require('services/autoload.php');
require('services/functions.php');

exit(block('page'));