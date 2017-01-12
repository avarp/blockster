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
        '/templates/admin/wolframe/flexbox-grid.css',
        '/templates/admin/wolframe/wolframe.css',
        '/templates/admin/style.css',
        '/templates/admin/font-awesome/css/font-awesome.min.css'
    ));
    $this->linkJs(
        '/templates/admin/wolframe/wolframe.js'
    );
?>
</head>
<body>
    <div class="grid" id="wrap">
        <header class="cell lg-3 xl-2 hidden-md-down" id="logo">
            <img src="<?=SITE_URL?>/templates/admin/images/logo.svg" style="height:2.6em;width:auto;vertical-align:middle">
            blockster <?=CMS_VERSION?>
        </header>
        <header class="cell lg-9 xl-10" id="header">
            <ul class="btn-list">
                <li>
                    <button
                        class="btn hidden-lg-up"
                        type="button"
                        data-wf-actions='{"click":[{"action":"toggleHeight", "target":"#main-menu"}]}'
                    >
                        <i class="fa fa-lg fa-bars"></i>
                    </button>
                </li>
                <li>
                    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                        <button class="btn" type="submit" name="logOut" title="Выйти из системы">
                            <i class="fa fa-lg fa-sign-out"></i>
                        </button>
                    </form>
                </li>
                <li>
                    <a href="/" class="btn" type="button" data-number="67">
                        <i class="fa fa-lg fa-envelope"></i>
                    </a>
                </li> 
            </ul>
        </header>
        <nav class="cell lg-3 xl-2" id="nav">
            <?=block(
                'stdlib/menu',
                array('menuName' => 'adminPanelMenu'),
                '/templates/admin/adminPanelMenu.tpl'
            )?>
        </nav>
        <main class="cell lg-9 xl-10" id="main">
            <?=position('content')?>
        </main>
    </div>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->