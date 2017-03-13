<?php if (isset($_POST['logOut'])) block('frontend/users::actionLogOut')?>
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
        '/templates/backend/wolframe/flexbox-grid.css',
        '/templates/backend/wolframe/wolframe.css',
        '/templates/backend/style.css',
        '/templates/backend/font-awesome/css/font-awesome.min.css'
    ));
    $this->linkJs(
        '/templates/backend/wolframe/wolframe.js'
    );
?>
</head>
<body>
    <div class="grid padding-1" id="wrap">
        <header class="cell xs-12">
            <div id="header">
                <div class="logo">
                    <img src="<?=SITE_URL?>/templates/backend/images/logo.svg">
                    Blockster <sup>v<?=CMS_VERSION?></sup>
                </div>
                <div class="logo">
                    <img src="<?=SITE_URL?>/templates/backend/images/logo.svg">
                    Blockster <sup>v<?=CMS_VERSION?></sup>
                </div>
                <div class="logo">
                    <img src="<?=SITE_URL?>/templates/backend/images/logo.svg">
                    Blockster <sup>v<?=CMS_VERSION?></sup>
                </div>
            </div>
        </header>
        <nav class="cell lg-3 xl-2" id="nav">
            <ul class="menu">
                <li><a href=""><i class="fa fa-dashboard"></i>Панель управления</a></li>
                <li><a href=""><i class="fa fa-dashboard"></i>Панель управления</a></li>
            </ul>
        </nav>
        <main class="cell lg-9 xl-10" id="main">
            <?=position('content')?>
        </main>
    </div>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->