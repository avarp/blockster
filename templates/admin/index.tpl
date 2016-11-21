<?php if (isset($_POST['logOut'])) execute('stdlib/users::actionLogOut')?>
<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->addMeta(array(
        '<meta charset="UTF-8">',
        '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
        '<meta name="viewport" content="width=device-width, initial-scale=1">'
    ));
    $this->setTitle('Панель управления');
    $this->linkCss(array(
        '/templates/admin/css/grid-system.css',
        '/templates/admin/css/admin-panel.css',
        'http://fonts.googleapis.com/css?family=Ubuntu:italic,normal,bold&subset=cyrillic',
        '/templates/admin/font-awesome/css/font-awesome.min.css'
    ));
    $this->linkJs('/templates/admin/js/scripts.js');
?>
</head>
<body>
    <div class="grid" id="wrap">
        <header class="cell xs-12" id="header">
            <ul class="btn-list">
                <li><button class="btn hidden-md-up" type="button" data-toggle-height="main-menu"><i class="fa fa-lg fa-bars"></i></button></li>
                <li><form action="<?=$_SERVER['REQUEST_URI']?>" method="POST"><button class="btn" type="submit" name="logOut" title="Выйти из системы"><i class="fa fa-lg fa-sign-out"></i></button></form></li>
                <li><a href="/" class="btn" type="button" data-number="67"><i class="fa fa-lg fa-envelope"></i></a></li> 
            </ul>
        </header>
        <nav class="cell md-3 lg-2" id="nav">
            <?=block(
                'stdlib/menu',
                array('menuName' => 'adminPanelMenu', 'class' => 'menu', 'id' => 'main-menu'),
                'adminPanelMenu.tpl'
            )?>
        </nav>
        <main class="cell md-9 lg-10" id="main">
            <?=position('content')?>
        </main>
    </div>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->