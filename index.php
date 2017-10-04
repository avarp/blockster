<?php
session_start();
define('ROOT_DIR', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
define('CMS_START', microtime(true));
define('CMS_VERSION', '0.7');

if (!file_exists('config.php')) require('install.php');
else require('config.php');
require('services/autoload.php');
require('services/functions.php');
core()->exitResponse();