<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Установка Blockster</title>
    <style>
        <?=file_get_contents(__DIR__.'/wolframe/flexbox-grid.css')?>
        <?=file_get_contents(__DIR__.'/wolframe/wolframe.css')?>
        html, body {font-size: 13px;}
    </style>
</head>
<body>
    <div class="modal-wrap">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-10 md-6 lg-4">
            <div class="card-header">
                Установка CMS Blockster v<?=CMS_VERSION?>
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <?php if (!empty($errors)) { ?>
                    <div class="cell xs-12">
                        <div class="alert alert-danger"><?=implode('<br>', $errors)?></div>
                    </div>
                    <?php } ?>
                    <div class="cell xs-6 r">
                        <label>Название сайта</label>
                    </div>
                    <div class="cell xs-6">
                        <input type="text" name="siteName" autocomplete="off" value="<?=$siteName?>">
                    </div>
                    <div class="cell xs-6 r">
                        <label>Адрес установки: <?=$hostUrl?></label>
                    </div>
                    <div class="cell xs-6">
                        <input type="text" name="installUri" autocomplete="off" value="<?=$installUri?>">
                    </div>
                </div>
            </div>
            <div class="card-section r">
                <input type="submit" name="install" class="btn btn-success" value="Установить">
            </div>
        </div>
    </form>
    </div>    
</body>
</html>