<!DOCTYPE html>
<html lang="<?=core()->getLang('isoCode')?>" dir="<?=core()->getLang('dir')?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    $this->addCssFile(pathToUrl(__DIR__.'/assets/flexbox-grid.css'));
    $this->addCssFile(pathToUrl(__DIR__.'/assets/ui-elements.css'));
    $this->addCssFile(pathToUrl(__DIR__.'/style.css'));
    $this->addCssFile(pathToUrl(__DIR__.'/assets/font-awesome/css/font-awesome.min.css'));
    $this->head();
?>
</head>
<body>
    <?=module('admin/authorization')?>
</body>
</html>