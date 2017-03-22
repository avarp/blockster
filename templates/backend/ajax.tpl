<?php
if (isset($_GET['blockName'])) echo block(
    $_GET['blockName'],
    isset($_POST['params']) ? json_decode($_POST['params']) : array(),
    isset($_POST['imposedTemplate']) ? $_POST['imposedTemplate'] : ''
);