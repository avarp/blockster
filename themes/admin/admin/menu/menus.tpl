<div class="card">
    <div class="card-header-big">
        <h1><?=$h1?></h1>
        <ul class="breadcrumbs">
        <?php foreach ($breadcrumbs as $href => $label) { ?>
            <li><a href="<?=core()->getUrl($href)?>"><?=$label?></a></li>
        <?php } ?>
        </ul>
    </div>
    <div class="card-section">
        <a href="<?=core()->getUrl('> admin-panel > menu/new')?>" class="btn btn-success">
            <i class="fa fa-plus"></i> Создать меню
        </a>
    </div>
    <div class="card-section" id="menus-table">
        <?php if ($menus) { ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($menus as $menu) { ?>
                        <tr>
                            <td><?=$menu['id']?></td>
                            <td><?=$menu['name']?></td>
                            <td class="nowrap">
                                <a href="<?=core()->getUrl("> admin-panel > menu/$menu[id]")?>" class="btn btn-default btn-square"><i class="fa fa fa-pencil"></i></a>
                                <button class="btn btn-square btn-danger" onclick='deleteMenu(<?=$menu['id']?>)'><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            Ни одного меню нет.
        <?php } ?> 
    </div>
</div>

<form method="POST">
    <div class="modal-wrap hidden" id="confirmDeleteMenu">
        <div class="modal card xs-8 md-6 lg-3">
            <div class="card-header">
                <div class="modal-controls">
                    <button type="button" class="btn btn-primary btn-square"
                        data-movements='{"when":"click","do":"hideModal","with":{"target":"#confirmDeleteMenu"}}'
                    ><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-title"><i class="fa fa-lg fa-question-circle"></i> Удалить меню?</div>
            </div>

            <div class="card-section r buttons">
                <button type="button" class="btn btn-default"
                    data-movements='{"when":"click","do":"hideModal","with":{"target":"#confirmDeleteMenu"}}'
                >Нет</button>
                <button class="btn btn-danger yes-btn" type="submit" name="deleteMenu">Да</button>
            </div>
        </div>
    </div>
</form>

<?php if ($deleteMenuSuccess) { ?>
<div class="alert alert-success system-message">
    Меню удалено
</div>
<?php } ?>

<script>
function deleteMenu(id) {
    document.querySelector('#confirmDeleteMenu [name="deleteMenu"]').value = id
    movements.showModal({target:'#confirmDeleteMenu'})
}

<?php if ($deleteMenuSuccess) { ?>
setTimeout(function(){
    movements.hideModal({target:'.alert.system-message', animation:'rise'})
}, 3000)
<?php } ?>
</script>