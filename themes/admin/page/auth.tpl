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
        '/themes/admin/page/assets/flexbox-grid.css',
        '/themes/admin/page/assets/ui-elements.css',
        '/themes/admin/page/style.css',
        '/themes/admin/page/assets/font-awesome/css/font-awesome.min.css'
    );
    $this->head();
?>
</head>
<body>
    <?=block('admin/authorization')?>
</body>
</html>