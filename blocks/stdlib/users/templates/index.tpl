<h1>Пользователи</h1>
<p>
    <?=$haveFilter ? 'Найдено' : 'Всего'?> пользователей: <?=$itemsCount?><br>
    <button
        type="button"
        class="btn btn-success"
        data-toggle-dialog="userEditorDialog"
        data-dialog-init-param="-1"
    ><i class="fa fa-user-plus"></i> Новый пользователь</button>
    <button
        type="button"
        class="btn btn-default"
        data-toggle-dialog="userSearchDialog"
    ><i class="fa fa-filter"></i> Фильтр <?=$haveFilter ? 'активен' : ''?></button>
</p>

<div class="grid split-1 section">
<?php foreach ($users as $key => $u) { ?>
    <div class="cell sm-6 lg-4">
        <div class="card">
            <div class="card-header">
                #<?=$u['id']?> <?=$u['login']?>
            </div>
            <div class="card-section">
                E-Mail: <?=$u['email']?><br>
                Access level : <?=$u['accessLevel']?><br>
                Online: <?=$u['online']?>
            </div>
            <div class="card-section r">
                <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                    <button
                        type="button"
                        class="btn btn-flat"
                        data-toggle-dialog="userEditorDialog"
                        data-dialog-init-param="<?=$key?>"
                    >Изменить</button>
                    <button
                        type="submit"
                        data-confirm="Удалить пользователя #<?=$u['id']?> c логином <?=$u['login']?>?<br>Отменить удаление будет нельзя."
                        name="deleteUser"
                        value="<?=$u['id']?>"
                        class="btn btn-flat"
                    >Удалить</button>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
</div>

<div class="hidden modal-wrap" id="userSearchDialog">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-10 md-6 lg-4">
            <div class="card-header">
                <i class="fa fa-lg fa-filter"></i> Фильтр поиска пользователей
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <div class="cell xs-4 r">
                        <label>Уровень доступа</label>
                    </div>
                    <div class="cell xs-4">
                        <input type="text" placeholder="от" name="filter[accessLevelFrom]" value="<?=$filter['accessLevelFrom']?>">
                    </div>
                    <div class="cell xs-4">
                        <input type="text" placeholder="до" name="filter[accessLevelTo]" value="<?=$filter['accessLevelTo']?>">
                    </div>
                    <div class="cell xs-4 r">
                        <label>Фильтр по логину</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="text" name="filter[loginLike]" value="<?=$filter['loginLike']?>">
                    </div>
                    <div class="cell xs-4 r">
                        <label>Фильтр по E-Mail</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="text" name="filter[emailLike]" value="<?=$filter['emailLike']?>">
                    </div>
                    <div class="cell xs-4">
                        <label><input type="radio" name="filter[onlineStatus]" value="1" <?=$filter['onlineStatus']==1 ? 'checked' : ''?> hidden><span></span> Онлайн</label>
                    </div>
                    <div class="cell xs-4">
                        <label><input type="radio" name="filter[onlineStatus]" value="0" <?=$filter['onlineStatus']==0 ? 'checked' : ''?> hidden><span></span> Оффлайн</label>
                    </div>
                    <div class="cell xs-4">
                        <label><input type="radio" name="filter[onlineStatus]" value="any" <?=$filter['onlineStatus']=='any' ? 'checked' : ''?> hidden><span></span> Все</label>
                    </div>
                </div>
            </div>
            <div class="card-section r">
                <input type="button" class="btn btn-flat" data-toggle-dialog="userSearchDialog" value="Отмена">
                <?php if ($haveFilter) { ?>
                    <input type="submit" class="btn btn-flat" name="dropFilter" value="Сбросить">
                <?php } ?>
                <input type="submit" class="btn btn-flat" name="setFilter" value="Применить">
            </div>
        </div>
    </form>
</div>

<div class="<?=empty($errors) ? 'hidden ' : ''?>modal-wrap" id="userEditorDialog">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-10 md-6 lg-4">
            <div class="card-header">
                <i class="fa fa-lg fa-user"></i> Параметры пользователя
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <input type="hidden" name="user[id]" value="<?=$user['id']?>">
                    <?php if (!empty($errors)) { ?>
                    <div class="cell xs-12" id="usersEditorDialog-error">
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
                <input type="button" class="btn btn-flat" data-toggle-dialog="userEditorDialog" value="Отмена">
                <input type="submit" name="saveUser" class="btn btn-flat" value="Сохранить">
            </div>
        </div>
    </form>
</div>

<script>
    var users = <?=json_encode($users)?>;

    document.getElementById('userEditorDialog').init = function(key) {
        var errorMsg = document.getElementById('usersEditorDialog-error');
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
<div class="system-message">
    <div class="alert alert-success">
        Профиль пользователя сохранен
    </div>
</div>
<?php } ?>