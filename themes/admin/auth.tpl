<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->data['metaTags'] = array(
        '<meta charset="UTF-8">',
        '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
        '<meta name="viewport" content="width=device-width, initial-scale=1">'
    );
    $this->data['title'] = 'Доступ ограничен';
    $this->data['cssFiles'] = array(
        '/themes/admin/assets/flexbox-grid.css',
        '/themes/admin/assets/elements.css',
        '/themes/admin/style.css',
        '/themes/admin/assets/font-awesome/css/font-awesome.min.css'
    );
    $this->head();
?>
</head>
<body>
    <?=block('admin/authorization')?>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->