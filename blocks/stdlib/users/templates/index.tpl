<h1>Пользователи</h1>
<p>
    <?=$haveFilter ? 'Найдено' : 'Всего'?> пользователей: <?=$itemsCount?><br>
    <button
        type="button"
        class="btn btn-success"
        data-toggle-dialog="userEditDialog"
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
<?php if ($numPages > 1) {
    echo block('stdlib/pagination', array(
        'url' => SITE_URL.'/admin/users?page=%u',
        'numPages' => $numPages,
        'thisPage' => $thisPage
    ));
}

require(__DIR__.'/userSearchDialog.tpl');
require(__DIR__.'/userEditDialog.tpl');

