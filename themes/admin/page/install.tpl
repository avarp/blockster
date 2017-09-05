<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Установка Blockster</title>
    <style>
        <?=file_get_contents(__DIR__.'/assets/flexbox-grid.css')?>
        <?=file_get_contents(__DIR__.'/assets/ui-elements.css')?>
    </style>
</head>
<body>
    <form method="POST">
    <div class="modal-wrap">
        <div class="modal card xs-11 sm-10 md-6 lg-4">
            <div class="card-header">
                <div class="modal-title">Установка CMS Blockster v<?=CMS_VERSION?></div>
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <?php if (!empty($errors)) { ?>
                    <div class="cell xs-12">
                        <div class="alert alert-danger"><?=implode('<br>', $errors)?></div>
                    </div>
                    <?php } ?>
                    <div class="cell xs-6 r">
                        <label>Логин администратора</label>
                    </div>
                    <div class="cell xs-6">
                        <input class="form-control" type="text" name="login" autocomplete="off" value="<?=$login?>">
                    </div>
                    <div class="cell xs-6 r">
                        <label>Пароль</label>
                    </div>
                    <div class="cell xs-6">
                        <input class="form-control" type="password" name="password" autocomplete="off" value="">
                    </div>
                    <div class="cell xs-6 r">
                        <label>Повторите пароль</label>
                    </div>
                    <div class="cell xs-6">
                        <input class="form-control" type="password" name="password2" autocomplete="off" value="">
                    </div>
                </div>
            </div>
            <div class="card-section r">
                <input type="submit" name="install" class="btn btn-success" value="Установить">
            </div>
        </div>
    </div>
    </form>  
</body>
</html>