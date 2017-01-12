<div class="<?=empty($errors) ? 'hidden ' : ''?>modal-wrap" id="createMenuDialog">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-9 md-7 lg-5 xl-4">
            <div class="card-header">
                <i class="fa fa-lg fa-list"></i> Новое меню
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <?php if (!empty($errors)) { ?>
                    <div class="cell xs-12" id="createMenuDialog-error">
                        <div class="alert alert-danger">
                            <?=implode('<br>', $errors)?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="cell xs-4 r">
                        <label>Системное имя</label>
                    </div>
                    <div class="cell xs-8">
                        <input type="text" name="menuName" value="<?=$menuName?>" placeholder="Например, siteTopMenu">
                    </div>
                </div>
            </div>
            <div class="card-section r">
                <input 
                    type="button"
                    tabindex="-1"
                    class="btn btn-default"
                    data-wf-actions='{"click":[{"action":"toggleModal","target":"#createMenuDialog"}]}'
                    value="Отмена"
                >
                <input type="submit" name="saveMenu" class="btn btn-primary" value="Сохранить">
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('createMenuDialog').onshow = function() {
        var errorMsg = document.getElementById('createMenuDialog-error');
        if (errorMsg) errorMsg.parentNode.removeChild(errorMsg);
        document.querySelector('#createMenuDialog input[name="menuName"]').value = '';
    }
</script>

<?php if ($success) { ?>
<div class="system-message alert alert-success" data-wf-actions='{"mouseover":[{"action":"toggleModal"}]}'>
    Меню создано. Нажмите кнопку <strong>Изменить</strong> чтобы добавить в него пункты.
</div>
<?php } ?>