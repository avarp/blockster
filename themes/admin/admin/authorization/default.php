<form method="POST">
<div class="modal-wrap" style="background:transparent;">
    <div class="modal card xs-11 sm-10 md-6 lg-4">
        <div class="card-header">
            <div class="modal-controls">
                <a href="<?=SITE_URL?>" class="btn btn-primary btn-square"><i class="fa fa-times"></i></a>
            </div>
            <div class="modal-title"><i class="fa fa-lg fa-sign-in"></i> <?=t('Log in')?></div>
        </div>
        <div class="card-section">
            <div class="grid split-1">
                <?php if (!empty($errors)) { ?>
                <div class="cell xs-12">
                    <div class="alert alert-danger"><?=implode('<br>', $errors)?></div>
                </div>
                <?php } ?>
                <div class="cell xs-4 r">
                    <label><?=t('Login')?></label>
                </div>
                <div class="cell xs-8">
                    <input class="form-control" type="text" name="login" autocomplete="off" value="<?=$login?>">
                </div>
                <div class="cell xs-4 r">
                    <label><?=t('Password')?></label>
                </div>
                <div class="cell xs-8">
                    <input class="form-control" type="password" name="password">
                </div>
            </div>
        </div>
        <div class="card-section r">
            <input type="submit" name="authorize" class="btn btn-primary" value="<?=t('Log in')?>">
        </div>
    </div>
</div>
</form>
