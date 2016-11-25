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
                <input type="button" tabindex="-1" class="btn btn-default" data-toggle-dialog="userSearchDialog" value="Отмена">
                <?php if ($haveFilter) { ?>
                    <input type="submit" tabindex="-1" class="btn btn-danger" name="dropFilter" value="Сбросить">
                <?php } ?>
                <input type="submit" class="btn btn-primary" name="setFilter" value="Применить">
            </div>
        </div>
    </form>
</div>