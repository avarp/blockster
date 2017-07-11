<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->data['title'] = 'Главная страница';
    $this->data['metaTags'][] = '<meta charset="UTF-8">';
    $this->head();
?>
</head>
<body>
    <h1><?=SITE_NAME?></h1>
    <p>Главная страница сайта</p>
    <?=block('test', array('header' => 'Заголовок', 'content' => 'Текст блока. текст блока'))?>
    <?=position('test-block')?>
    <?php $this->scripts() ?>
</body>
</html>