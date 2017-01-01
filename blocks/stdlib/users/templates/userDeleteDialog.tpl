<div class="modal-wrap hidden" id="userDeleteDialog">
    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
        <div class="modal card xs-11 sm-9 md-7 lg-5 xl-4">
            <div class="card-header">
                <i class="fa fa-lg fa-question-circle"></i> Подтвердите удаление пользователя
            </div>
            <div class="card-section message">
            </div>
            <div class="card-section r">
                <input 
                	type="button"
                	class="btn btn-default"
                	data-wf-actions='{"click":[{"action":"toggleModal","target":"#userDeleteDialog"}]}'
                	value="Отмена"
                >
                <button type="submit" name="deleteUser" class="btn btn-danger" value="">Удалить</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('userDeleteDialog').onshow = function(params) {
        document.querySelector('#userDeleteDialog [type="submit"]').value = params.uid;
        document.querySelector('#userDeleteDialog .message').textContent = 
        'Вы собираетесь удалить пользователя #'+params.uid+' c логином '+params.login+'. Удаление отменить будет невозможно.';
    }
</script>