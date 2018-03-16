<?php
session_start();
define('ROOT_DIR', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
define('CMS_START', microtime(true));
define('CMS_VERSION', '0.7');
require('core/boot.php');

core()->exitResponse();