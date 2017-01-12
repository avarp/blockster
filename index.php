<?php
define('ROOT_DIR', __DIR__);
define('CMS_START', microtime(true));
define('CMS_VERSION', '0.2.1');

if (!file_exists('environment.local.php')) require('install.php');
else require('environment.local.php');
require('blockster/autoload.php');
require('blockster/shortcuts.php');

\blockster\Core::getInstance()->printPage();