<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->addMeta(array(
        '<meta charset="UTF-8">',
        '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
        '<meta name="viewport" content="width=device-width, initial-scale=1">'
    ));
    $this->setTitle('Доступ ограничен');
    $this->linkCss(array(
        '/templates/admin/wolframe/flexbox-grid.css',
        '/templates/admin/wolframe/wolframe.css',
        '/templates/admin/style.css',
        '/templates/admin/font-awesome/css/font-awesome.min.css'
    ));
?>
</head>
<body>
    <?=block('stdlib/users::actionLogIn')?>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->