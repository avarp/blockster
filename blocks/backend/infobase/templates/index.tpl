<div class="card" id="infobase">

    <div class="card-header-big">
        <button
            class="btn btn-primary btn-round"
            style="float:right"
            data-wf-actions='{"click":[{"action":"toggleModal", "target":"#infobaseDescription"}]}'>
            <i class="fa fa-info-circle"></i>
        </button>
        <h1>Имя инфобазы</h1>
        <p>
            <a href="">Имя инфобазы</a> <i class="fa fa-chevron-right"></i>
            <a href="">Имя корневой записи</a> <i class="fa fa-chevron-right"></i>
            <a href="">Имя вложенной записи</a>
        </p>
    </div>

    <ul class="tabs tabs-primary" id="tablesTabsControls">
        <li
            class="active"
            data-wf-actions='{"click":[
                {"action":"toggleClass", "class":"active", "group":"#tablesTabsControls>li"},
                {"action":"toggleClass", "class":"hidden", "target":"#tablesTabs>div:nth-child(1)", "inverse":true, "group":"#tablesTabs>div"}
            ]}'>
            Таблица1
        </li>
        <li
            data-wf-actions='{"click":[
                {"action":"toggleClass", "class":"active", "group":"#tablesTabsControls>li"},
                {"action":"toggleClass", "class":"hidden", "target":"#tablesTabs>div:nth-child(2)", "inverse":true, "group":"#tablesTabs>div"}
            ]}'>
            Таблица2
        </li>
        <li
            data-wf-actions='{"click":[
                {"action":"toggleClass", "class":"active", "group":"#tablesTabsControls>li"},
                {"action":"toggleClass", "class":"hidden", "target":"#tablesTabs>div:nth-child(3)", "inverse":true, "group":"#tablesTabs>div"}
            ]}'>
            Таблица3
        </li>
        <li
            data-wf-actions='{"click":[
                {"action":"toggleClass", "class":"active", "group":"#tablesTabsControls>li"},
                {"action":"toggleClass", "class":"hidden", "target":"#tablesTabs>div:nth-child(4)", "inverse":true, "group":"#tablesTabs>div"}
            ]}'>
            Таблица4
        </li>
    </ul>

    <div id="tablesTabs">
        <div><?=block('backend/infobase::actionShowTable')?></div>
        <div class="hidden"><?=block('backend/infobase::actionShowTable')?></div>
        <div class="hidden"><?=block('backend/infobase::actionShowTable')?></div>
        <div class="hidden"><?=block('backend/infobase::actionShowTable')?></div>
    </div>   

</div>

<div class="modal-wrap hidden" id="infobaseDescription">
    <div class="modal card xs-11 md-9 xl-7">
        <div class="card-header">
            <button class="btn btn-primary btn-square modal-controls" data-wf-actions='{"click":[{"action":"toggleModal", "target":"#infobaseDescription"}]}'>
                <i class="fa fa-times"></i>
            </button>
            <div class="modal-title">
                <i class="fa fa-info-circle"></i>
                Описание инфобазы
            </div>
        </div>
        <div class="card-section">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero non, perferendis! Eaque quae omnis deleniti nobis commodi quos maiores quis doloribus provident in ullam minima pariatur qui aliquam temporibus labore, beatae assumenda quo eligendi, eum cum. Hic dicta ducimus repellendus, asperiores saepe. Corrupti, sed debitis veniam rem voluptatem reprehenderit sit in esse ipsum aliquam commodi laborum totam vero, eveniet odio facilis at rerum obcaecati, maxime doloremque iste quo amet fugiat. Iste repudiandae, commodi eum quos. Iusto adipisci esse perferendis deleniti fugit, doloribus eaque illum eveniet pariatur itaque nulla tenetur aliquid illo sunt quos aliquam officia amet, laudantium, repellendus maxime, numquam.</p>
        </div>
        <div class="card-section r">
            <button class="btn btn-default" data-wf-actions='{"click":[{"action":"toggleModal", "target":"#infobaseDescription"}]}'>Ок</button>
        </div>
    </div>
</div>