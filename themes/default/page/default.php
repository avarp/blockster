<!DOCTYPE html>
<html lang="<?=core()->getLang('isoCode')?>" dir="<?=core()->getLang('dir')?>">
<head>
<?php
    $this->data['title'] = 'Главная страница';
    $this->data['metaTags'][] = '<meta charset="UTF-8">';
    $this->head();
?>
</head>
<body>
    <h1>Сайт</h1>
    <p>Главная страница сайта</p>
    <?=module('test')?>
    <?php $this->scripts() ?>
</body>
</html>