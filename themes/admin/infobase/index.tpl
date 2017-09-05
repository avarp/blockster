<div class="card">

    <div class="card-header-big">
        <?php if (!empty($infobase['description'])) { ?>
        <button
            class="btn btn-primary btn-square"
            style="float:right"
            data-wf-actions='{"click":[{"action":"toggleModal", "target":"#infobaseDescription"}]}'>
            <i class="fa fa-info-circle"></i>
        </button>
        <?php } ?>

        <h1><?=$infobase['name']?></h1>

        <?php if (count($breadcrumbs) > 1) { ?>
        <p>
            <?php $n = 1; foreach ($breadcrumbs as $b) { ?>
                <?=($n != 1) ? '<i class="fa fa-chevron-right"></i>' :''?>
                <a href="<?=$b['href']?>"><?=$b['name']?></a>
            <?php $n++; } ?>
        </p>
        <?php } ?>

    </div>

    <?php if (count($tables) > 1) { ?>
    <ul class="tabs tabs-primary" id="tablesTabsControls">
        <?php $n = 1; foreach ($tables as $sqlName => $tableName) { ?>
        <li
            <?=($n == 1) ? 'class="active"' : ''?>
            data-wf-actions='{"click":[
                {"action":"toggleClass", "class":"active", "group":"#tablesTabsControls>li"},
                {"action":"toggleClass", "class":"hidden", "target":"#tables>div:nth-child(<?=$n?>)", "inverse":true, "group":"#tables>div"}
            ]}'>
            <?=$tableName?>
        </li>
        <?php $n++; } ?>
    </ul>
    <?php } ?>

    <div id="tables">
        <?php $n = 1; foreach ($tables as $sqlName => $tableName) { ?>
        <div
            <?=($n != 1) ? 'class="hidden"' : ''?>
            data-infobase-table="<?=$sqlName?>">
        </div>
        <?php $n++; } ?>
    </div>

</div>

<?php if (!empty($infobase['description'])) { ?>
<div class="modal-wrap hidden" id="infobaseDescription">
    <div class="modal card xs-11 md-9 xl-7">
        <div class="card-header">
            <button class="btn btn-primary btn-square modal-controls" data-wf-actions='{"click":[{"action":"toggleModal", "target":"#infobaseDescription"}]}'>
                <i class="fa fa-times"></i>
            </button>
            <div class="modal-title">
                <i class="fa fa-info-circle"></i>
                Описание инфобазы "<?=$infobase['name']?>"
            </div>
        </div>
        <div class="card-section">
           <?=$infobase['description']?>
        </div>
        <div class="card-section r">
            <button class="btn btn-default" data-wf-actions='{"click":[{"action":"toggleModal", "target":"#infobaseDescription"}]}'>Ок</button>
        </div>
    </div>
</div>
<?php } ?>

<script>
    var SITE_URL = '<?=SITE_URL?>', INFOBASE_NAME = '<?=$infobase['url']?>'
</script>