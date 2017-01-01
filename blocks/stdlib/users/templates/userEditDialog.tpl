<div class="<?=empty($errors) ? 'hidden ' : ''?>modal-wrap" id="userEditDialog">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-9 md-7 lg-5 xl-4">
            <div class="card-header">
                <i class="fa fa-lg fa-user"></i> Параметры пользователя
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <input type="hidden" name="user[id]" value="<?=$user['id']?>">
                    <?php if (!empty($errors)) { ?>
                    <div class="cell xs-12" id="userEditDialog-error">
                        <div class="alert alert-danger">
                            <?=implode('<br>', $errors)?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="cell xs-4 r">
                        <label>Логин</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="text" name="user[login]" value="<?=$user['login']?>">
                    </div>
                    <div class="cell xs-4 r">
                        <label>E-mail</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="text" name="user[email]" value="<?=$user['email']?>">
                    </div>
                    <div class="cell xs-4 r">
                        <label>Уровень доступа</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="text" name="user[accessLevel]" value="<?=$user['accessLevel']?>">
                    </div>
                    <div class="cell xs-4 r">
                        <label>Пароль</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="password" name="user[password]">
                    </div>
                    <div class="cell xs-4 r">
                        <label>Пароль еще раз</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="password" name="passwordConfirm">
                    </div>
                </div>
            </div>
            <div class="card-section r">
                <input 
                    type="button"
                    tabindex="-1"
                    class="btn btn-default"
                    data-wf-actions='{"click":[{"action":"toggleModal","target":"#userEditDialog"}]}'
                    value="Отмена"
                >
                <input type="submit" name="saveUser" class="btn btn-primary" value="Сохранить">
            </div>
        </div>
    </form>
</div>

<script>
    var users = <?=json_encode($users)?>;

    document.getElementById('userEditDialog').onshow = function(key) {
        var errorMsg = document.getElementById('userEditDialog-error');
        if (errorMsg) errorMsg.parentNode.removeChild(errorMsg);
        if (key == -1) {
            var user = {"id":0, "login":"", "email":"", "accessLevel":0}
        } else {
            var user = users[key];
        }
        var inputs = this.getElementsByTagName('INPUT');
        inputs[0].value = user['id'];
        inputs[1].value = user['login'];
        inputs[2].value = user['email'];
        inputs[3].value = user['accessLevel'];
        inputs[4].value = '';
        inputs[5].value = '';
    }
</script>

<?php if ($success) { ?>
<div class="system-message alert alert-success" data-wf-actions='{"mouseover":[{"action":"toggleModal"}]}'>
    Профиль пользователя сохранен
</div>
<?php } ?>