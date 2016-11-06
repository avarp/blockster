<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->addMeta(array(
        '<meta charset="UTF-8">',
        '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
        '<meta name="viewport" content="width=device-width, initial-scale=1">'
    ));
    $this->setTitle('Нужна авторизация');
    $this->addCss(array(
        '/templates/admin/css/grid-system.css',
        '/templates/admin/css/admin-panel.css',
        'http://fonts.googleapis.com/css?family=Ubuntu:italic,normal,bold&subset=cyrillic',
        '/templates/admin/font-awesome/css/font-awesome.min.css'
    ));
    $this->addJs('/templates/admin/js/scripts.js');
?>
</head>
<body>
    <?=block('stdlib/users::actionLogIn')?>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->