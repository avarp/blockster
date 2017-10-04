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
        <div class="input-group">
            <input type="text" id="menuName" value="<?=$menu['name']?>" class="form-control" placeholder="Название меню" required>
            <button type="button" id="saveMenuNameBtn" class="btn btn-success"><i class="fa fa-save"></i><span class="hidden-xs-only"> Сохранить</span></button>
        </div>
    </div>
    <div class="card-section" id="menuitemsTable">
        <div class="table-wrap">
            <table class="table menuitems-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Структура</th>
                        <th>Вложенность</th>
                        <th>Порядок</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            1
                        </td>
                        <td class="nowrap">
                            <a href="#">Название пункта меню</a>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-right"></i></button>
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-left"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-up"></i></button>
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-down"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-primary">
                                    <i class="fa fa-plus"></i>
                                    <i class="fa fa-arrow-down sub-icon"></i>
                                </button>
                                <button class="btn btn-square btn-success"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-square btn-danger"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            1
                        </td>
                        <td class="nowrap">
                            <span class="nesting-line"></span>
                            <a href="#">Название пункта меню</a>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-right"></i></button>
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-left"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-up"></i></button>
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-down"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-primary">
                                    <i class="fa fa-plus"></i>
                                    <i class="fa fa-arrow-down sub-icon"></i>
                                </button>
                                <button class="btn btn-square btn-success"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-square btn-danger"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            1
                        </td>
                        <td class="nowrap">
                            <span class="nesting-line vert"></span>
                            <span class="nesting-line last"></span>
                            <a href="#">Название пункта меню</a>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-right"></i></button>
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-left"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-up"></i></button>
                                <button class="btn btn-square btn-default"><i class="fa fa-arrow-down"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <button class="btn btn-square btn-primary">
                                    <i class="fa fa-plus"></i>
                                    <i class="fa fa-arrow-down sub-icon"></i>
                                </button>
                                <button class="btn btn-square btn-success"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-square btn-danger"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var menu = <?=json_encode($menu)?>;
</script>

<?php
$this->addCssFile(pathToUrl(__DIR__.DS.'menuitems.css'));
$this->addJsFile(pathToUrl(__DIR__.DS.'menuitems.js'));