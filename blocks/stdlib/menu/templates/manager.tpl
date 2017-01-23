<h1>Менеджер меню</h1>
<p>
    <button
        type="button"
        class="btn btn-success"
        data-wf-actions='{"click":[{"action":"toggleModal","target":"#createMenuDialog"}]}'
    ><i class="fa fa-plus-circle"></i> Добавить меню</button>
</p>
<div class="grid split-1 section">
<?php foreach ($menus as $m) { ?>
    <div class="cell sm-6 lg-4 xl-3">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> <?=$m?>
            </div>
            <div class="card-section c">
                <a href="<?=$_SERVER['REQUEST_URI']?>/<?=$m?>" class="btn btn-default">Изменить</a>
                <button
                    type="button"
                    class="btn btn-danger"
                    data-wf-actions='{
                        "click":[
                            {
                                "action":"toggleModal",
                                "target":"#deleteMenuDialog",
                                "initParams":"<?=$m?>"
                            }
                        ]
                    }'
                >Удалить</button>
            </div>
        </div>
    </div>
<?php } ?>
</div>

<?php require(__DIR__.'/deleteMenuDialog.tpl') ?>
<?php require(__DIR__.'/createMenuDialog.tpl') ?>