<?php 
namespace blocks\stdlib\users;
?>
<h1>Пользователи</h1>
<p>
    <?=$haveFilter ? 'Найдено' : 'Всего'?> пользователей: <?=$itemsCount?><br>
    <button
        type="button"
        class="btn btn-success"
        data-wf-actions='{"click":[{"action":"toggleModal","target":"#userEditDialog","modalInfo":-1}]}'
    ><i class="fa fa-user-plus"></i> Новый пользователь</button>
    <button
        type="button"
        class="btn btn-default"
        data-wf-actions='{"click":[{"action":"toggleModal","target":"#userSearchDialog"}]}'
    ><i class="fa fa-filter"></i> Фильтр <?=$haveFilter ? 'активен' : ''?></button>
</p>

<div class="grid split-1 section">
<?php foreach ($users as $key => $u) { ?>
    <div class="cell sm-6 lg-4 xl-3">
        <div class="card">
            <div class="card-header">
                #<?=$u['id']?> <?=$u['login']?>
            </div>
            <div class="card-section">
                E-Mail: <?=$u['email']?><br>
                Уровень доступа : <?=$u['accessLevel']?><br>
                Online: <?=($u['isOnline'] == 1 && time() - $u['trackingTimestamp'] < ONLINE_FLAG_LIFETIME) ? 'да' : 'нет'?>
            </div>
            <div class="card-section r">
                <button
                    type="button"
                    class="btn btn-default"
                    data-wf-actions='{
                        "click":[
                            {
                                "action":"toggleModal",
                                "target":"#userEditDialog",
                                "modalInfo":<?=$key?>
                            }
                        ]
                    }'
                >Изменить</button>
                <button
                    type="button"
                    class="btn btn-danger"
                    data-wf-actions='{
                        "click":[
                            {
                                "action":"toggleModal",
                                "target":"#userDeleteDialog",
                                "modalInfo":{
                                    "uid":<?=$u['id']?>,
                                    "login":"<?=$u['login']?>"
                                }
                            }
                        ]
                    }'
                >Удалить</button>
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
require(__DIR__.'/userDeleteDialog.tpl');

