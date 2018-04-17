<!DOCTYPE html>
<html lang="<?=core()->lang['isoCode']?>" dir="<?=core()->lang['direction']?>">
<head>
 <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    $this->addCssFile(pathToUrl(__DIR__.'/assets/flexbox-grid.css'));
    $this->addCssFile(pathToUrl(__DIR__.'/assets/ui-elements.css'));
    $this->addCssFile(pathToUrl(__DIR__.'/style.css'));
    $this->addCssFile(pathToUrl(__DIR__.'/assets/font-awesome/css/font-awesome.min.css'));
    $this->addJsFile(pathToUrl(__DIR__.'/assets/movements.js'));
    $this->addJsFile(pathToUrl(__DIR__.'/script.js'));
?>
<?=$this->head()?>
</head>
<body>
    <div class="grid padding-1" id="wrap">
        <header class="cell lg-3 xl-2 hidden-md-down">
            <div class="logo">
                <img src="<?=pathToUrl(__DIR__.'/images/logo.svg')?>">
                Blockster <sup>v<?=CMS_VERSION?></sup>
            </div>
        </header>
        <header class="cell lg-9 xl-10 header-buttons">
            <?php if (isset($_SESSION['user'])) { ?>
            <a href="<?=SITE_URL?>/admin/users/<?=$_SESSION['user']['id']?>" class="header-btn" target="_blank" title="Вы вошли как <?=$_SESSION['user']['login']?>. Открыть профиль пользователя.">
                <i class="fa fa-id-card"></i>
            </a>
            <?php } ?>
            <form method="POST">
                <button type="submit" name="logOut" class="header-btn" title="Выйти"><i class="fa fa-sign-out"></i></button>
            </form>
            <a href="<?=SITE_URL?>" class="header-btn" target="_blank" title="Открыть сайт">
                <i class="fa fa-globe"></i>
            </a>
            <button
                class="header-btn hidden-lg-up"
                onclick="Mov.toggleHeight({target:'#adminMenu', hidingClass:'hidden-md-down'})"
            >
                <i class="fa fa-bars"></i>
            </button>
        </header>
        <nav class="cell lg-3 xl-2 hidden-md-down" id="adminMenu">
            <?=module('menu', ['name' => 'admin-menu'])?>
        </nav>
        <main class="cell lg-9 xl-10" id="main">
            <?=position('content')?>
        </main>
    </div>
    <div id="notification-tray">
    </div>
<?=$this->scripts()?>
</body>
</html>