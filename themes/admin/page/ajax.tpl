<?php
$input = file_get_contents('php://input');
if (isset($_REQUEST['params'][0])) echo block(
    $_REQUEST['params'][0],
    empty($input) ? json_decode($input) : array()
);