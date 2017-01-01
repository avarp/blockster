<?php
if (!file_exists('environment.local.php')) require('install.php');
else require('environment.local.php');
require('blockster/autoload.php');
require('blockster/shortcuts.php');

\blockster\Core::getInstance()->printPage();