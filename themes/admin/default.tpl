<?php if (isset($_POST['logOut'])) block('admin/authorization::logOut')?>
<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->data['metaTags'] = array(
        '<meta charset="UTF-8">',
        '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
        '<meta name="viewport" content="width=device-width, initial-scale=1">'
    );
    $this->data['title'] = 'Панель управления';
    $this->data['cssFiles'] = array(
        '/themes/admin/assets/flexbox-grid.css',
        '/themes/admin/assets/elements.css',
        '/themes/admin/style.css',
        '/themes/admin/assets/font-awesome/css/font-awesome.min.css'
    );
    $this->data['jsFiles'] = array(
        '/themes/admin/assets/movements.js'
    );
    $this->head();
?>
</head>
<body>
    <div class="grid padding-1" id="wrap">
        <header class="cell lg-3 xl-2 hidden-md-down">
            <div class="logo">
                <img src="<?=SITE_URL?>/themes/admin/images/logo.svg">
                Blockster <sup>v<?=CMS_VERSION?></sup>
            </div>
        </header>
        <header class="cell lg-9 xl-10 header-buttons">
            <a href="<?=SITE_URL?>/admin/users/<?=$_SESSION['user']['id']?>" class="header-btn" target="_blank" title="Вы вошли как <?=$_SESSION['user']['login']?>. Открыть профиль пользователя.">
                <i class="fa fa-id-card"></i>
            </a>
            <form method="POST">
                <button type="submit" name="logOut" class="header-btn" title="Выйти"><i class="fa fa-sign-out"></i></button>
            </form>
            <a href="<?=SITE_URL?>" class="header-btn" target="_blank" title="Открыть сайт">
                <i class="fa fa-globe"></i>
            </a>
            <button
                class="header-btn hidden-md-up"
                data-movements='{"when":"click", "do":toggleHeight", "with":{"target":"#adminMenu"}}'>
                <i class="fa fa-bars"></i>
            </button>
        </header>
        <nav class="cell lg-3 xl-2 hidden-md-down" id="adminMenu">
            <ul class="menu">
                <li><a href="<?=SITE_URL?>/admin"><i class="fa fa-cogs"></i>Панель управления</a></li>
                <li><a href="<?=SITE_URL?>/content/infobases"><i class="fa fa-table"></i>Инфобазы</a></li>
                <li><a href="<?=SITE_URL?>/adminer"><i class="fa fa-database"></i>Adminer</a></li>
            </ul>
        </nav>
        <main class="cell lg-9 xl-10" id="main">
            <?=position('content')?>
        </main>
    </div>
<?php $this->scripts(); ?>
</body>
</html>
<!-- Page generation time: <?=1000*(microtime(true)-CMS_START)?> ms -->