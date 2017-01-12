<div class="modal-wrap hidden" id="deleteMenuDialog">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-9 md-7 lg-5 xl-4">
            <div class="card-header">
                <i class="fa fa-lg fa-question-circle"></i> Подтвердите удаление меню
            </div>
            <div class="card-section message">
            </div>
            <div class="card-section r">
                <input 
                	type="button"
                	class="btn btn-default"
                	data-wf-actions='{"click":[{"action":"toggleModal","target":"#deleteMenuDialog"}]}'
                	value="Отмена"
                >
                <button type="submit" name="deleteMenu" class="btn btn-danger" value="">Удалить</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('deleteMenuDialog').onshow = function(menuName) {
        document.querySelector('#deleteMenuDialog [type="submit"]').value = menuName;
        document.querySelector('#deleteMenuDialog .message').textContent = 
        'Вы собираетесь удалить меню '+menuName+'. Удаление отменить будет невозможно.';
    }
</script>