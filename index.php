<?php
if (!file_exists('config.local.php')) require('install.php');
else require('config.local.php');
require('blockster/autoload.php');
require('blockster/shortcuts.php');

\blockster\Core::getInstance()->printPage();
