<div id="menu-editor-container" class="loading-spinner">
    <i class="fa fa-spinner fa-pulse fa-5x fa-fw text-primary"></i>
</div>

<script type="text/vue-template" id="menu-editor-template">
    <div class="card" style="position:relative;">

        <div v-show="onSending" class="loading-spinner" style="background-color:rgba(255,255,255,0.8);">
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw text-primary"></i>
        </div>

        <?php if ($transCnt > 1) { ?>
            <ul class="tabs tabs-primary">
            <?php foreach ($languages as $l) if ($l['isTranslated']) if ($l['isoCode'] == $menu['lang']) { ?>
                <li class="active"><?=$l['nativeName']?></li>
            <?php } else { ?>
                <li><a href="<?=$l['isoCode']?>"><?=$l['nativeName']?></a></li>
            <?php } ?>
            </ul>
        <?php } ?>

        <div class="card-header-big">
            <h1>{{menu.label}}</h1>
            <ul class="breadcrumbs">
                <?php foreach ($breadcrumbs as $b) { ?>
                <li><a href="<?=$b['href']?>"><?=$b['label']?></a></li>
                <?php } ?>
                <li>{{menu.label}}</li>
            </ul>
        </div>

        <div class="card-section toolbar">
            <div class="toolbar-section">
                <div class="section-content">
                    <div class="p-0-5">
                        <input type="text" class="form-control" v-model="menu.sysname">
                        <span v-if="errors.sysname != ''" class="text-danger">{{errors.sysname}}</span>
                        <span v-else><?=t('System name')?></span>
                    </div>
                    <div class="p-0-5">
                        <input type="text" class="form-control" v-model="menu.label">
                        <span v-if="errors.label != ''" class="text-danger">{{errors.label}}</span>
                        <span v-else><?=t('Title')?></span>
                    </div>
                    <button
                        class="btn btn-default"
                        v-on:click="send"
                        :disabled="errors.length != 0"
                    >
                        <img class="btn-icon" src="<?=core()->themeUrl.'/page/images/save.svg'?>" alt="icon">
                        <span class="btn-label"><?=t('Save')?></span>
                    </button>
                </div>
                <div class="section-title">
                    <?=t('Menu')?>
                </div>
            </div>
            <?php if (count($languages > 1)) { ?>
                <div class="toolbar-section">
                    <div class="section-content">
                        <button class="btn btn-default" <?=($transCnt <= 1) ? 'disabled' : ''?> v-on:click="removeTranslation">
                            <img class="btn-icon" src="<?=core()->themeUrl.'/page/images/remove-translation.svg'?>" alt="icon">
                            <span class="btn-label"><?=t('Delete')?></span>
                        </button>
                        <button class="btn btn-default" <?=($transCnt < 1) ? 'disabled' : ''?> v-on:click="openTranslationModal">
                            <img class="btn-icon" src="<?=core()->themeUrl.'/page/images/add-translation.svg'?>" alt="icon">
                            <span class="btn-label"><?=t('Add')?></span>
                        </button>
                    </div>
                    <div class="section-title">
                        <?=t('Translations')?>
                    </div>
                </div>
            <?php } ?>
            <div class="toolbar-section">
                <div class="section-content">
                    <button class="btn btn-default" v-on:click="openItemCreateModal">
                        <img class="btn-icon" src="<?=core()->themeUrl.'/page/images/insert-in-list.svg'?>" alt="icon">
                        <span class="btn-label"><?=t('Add')?></span>
                    </button>
                    <button class="btn btn-default" :disabled="selection.length != 1" v-on:click="openItemEditModal">
                        <img class="btn-icon" src="<?=core()->themeUrl.'/page/images/pencil.svg'?>" alt="icon">
                        <span class="btn-label"><?=t('Edit')?></span>
                    </button>
                    <button class="btn btn-default" :disabled="selection.length == 0" v-on:click="remove">
                        <img class="btn-icon" src="<?=core()->themeUrl.'/page/images/delete-from-list.svg'?>" alt="icon">
                        <span class="btn-label"><?=t('Delete')?></span>
                    </button>
                    <button class="btn btn-default" onclick="Mov.showDropdown({duration:150, target:'#addition-menu', source:this})">
                        <i class="btn-icon text-primary fa fa-ellipsis-v"></i>
                        <span class="btn-label"><?=t('Options')?> <i class="fa fa-caret-down"></i></span>
                    </button>
                </div>
                <div class="section-title">
                    <?=t('Items of menu')?>
                </div>
            </div>
        </div>

        <ul class="dropdown hidden" id="addition-menu">
            <li v-on:click="expandAll"><i class="fa fa-plus-square"></i><?=t('Expand all')?></li>
            <li v-on:click="collapseAll"><i class="fa fa-minus-square"></i><?=t('Collapse all')?></li>
            <li v-on:click="selectAll"><i class="fa fa-check-square-o"></i><?=t('Select all')?></li>
            <li :disabled="selection.length == 0"
                v-on:click="clearSelection"
            >
                <i class="fa fa-square-o"></i><?=t('Clear selection')?>
            </li>
            <li :disabled="!selection.isIndentable"
                v-on:click="indent"
            >
                <i class="fa fa-chevron-right"></i><?=t('Increase nesting level')?>
            </li>
            <li :disabled="!selection.isOutdentable"
                v-on:click="outdent"
            >
                <i class="fa fa-chevron-left"></i><?=t('Decrease nesting level')?>
            </li>
        </ul>

        <div class="card-section">
            <div v-show="menu.children.length > 0" class="table-wrap">
                <tree-view :list="menu.children"></tree-view>
            </div>
            <span v-show="menu.children.length == 0"><?=t('No items')?></span>
        </div>

        <item-editor name="itemEditor" v-on:created="bindComponent" v-on:submit="saveItem"></item-editor>
        <translation-modal name="translationModal" v-on:created="bindComponent" v-on:submit="createTranslation"></translation-modal>
    </div>
</script>

<script type="text/x-template" id="tree-view-template">
    <ul class="tree-view">
        <li v-for="(item, i) in list" class="tree-view-item">
            <div class="item-content">
                <i  v-if="list.length > 1"
                    class="fa fa-arrows drag-handle"
                ></i>
                <span v-else class="no-drag-handle"></span>
                <label><input type="checkbox" hidden v-model="item.selected"><span></span></label>
                <a :href="item.href" class="item-link" target="_blank">
                    <i v-if="item.accessLevel > 0" class="fa fa-lock text-danger" title="<?=t('This item has non-zero access level.')?>"></i>
                    {{item.label}}
                </a>
                <button v-if="item.children.length > 0"
                        class="btn btn-default btn-sm btn-round"
                        v-on:click="item.expanded = !item.expanded"
                >
                    <i :class="{'fa':true, 'fa-minus-square':item.expanded, 'fa-plus-square':!item.expanded}"></i>
                </button>
            </div>
            <transition :css="false" v-on:enter="expand" v-on:leave="collapse">
                <tree-view v-show="item.expanded" v-if="item.children.length > 0" :list="item.children"></tree-view>
            </transition>
        </li>
    </ul>
</script>

<script type="text/x-template" id="item-editor-template">
    <div class="modal-wrap hidden">
        <div class="modal card xs-11 sm-10 md-6 lg-4">
            <div class="card-header">
                <div class="modal-controls">
                    <button class="btn btn-primary btn-square" v-on:click="close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-title">
                    <span v-if="item.saveOption != UPDATE_SELECTED"><i class="fa fa-lg fa-plus"></i> <?=t('New item of menu')?></span>
                    <span v-else><i class="fa fa-lg fa-pencil-square-o"></i> <?=t('Edit item of menu')?></span>
                </div>
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <div v-if="showErrors && errors.length > 0" class="cell xs-12">
                        <div class="alert alert-danger">
                            <div v-for="error in errors">{{error}}</div>
                        </div>
                    </div>
                    <div class="cell xs-4 r">
                        <label><?=t('Label')?> <span class="text-danger">*</span></label>
                    </div>
                    <div class="cell xs-8">
                        <input class="form-control" type="text" v-model="item.label">
                    </div>
                    <div class="cell xs-4 r">
                        <label><?=t('Link')?> <span class="text-danger">*</span></label>
                    </div>
                    <div class="cell xs-8">
                        <input class="form-control" type="text" v-model="item.href">
                    </div>
                    <div class="cell xs-4 r">
                        <label><?=t('Access level')?> <span class="text-danger">*</span></label>
                    </div>
                    <div class="cell xs-8">
                        <input class="form-control" type="number" v-model="item.accessLevel">
                    </div>
                    <div class="cell xs-4 r">
                        <label><?=t('Additional info')?></label>
                    </div>
                    <div class="cell xs-8">
                        <textarea class="form-control" v-model="item.customField"></textarea>
                    </div>
                    <div v-show="item.saveOption != UPDATE_SELECTED" class="cell xs-4 r">
                        <label><?=t('Save to')?></label>
                    </div>
                    <div v-show="item.saveOption != UPDATE_SELECTED" class="cell xs-8">
                        <select class="form-control" v-model="item.saveOption">
                            <option :value="PUSH_TO_ROOT"><?=t('End of menu')?></option>
                            <option :value="INSERT_BEFORE" :disabled="pushToRootOnly"><?=t('Before selected item')?></option>
                            <option :value="INSERT_AFTER" :disabled="pushToRootOnly"><?=t('After selected item')?></option>
                            <option :value="PUSH_TO_CHILDREN" :disabled="pushToRootOnly"><?=t('Inside selected item')?></option>
                        </select>
                        <small v-show="pushToRootOnly" class="text-primary"><?=t('Select only one item for enabling other variants.')?></small>
                    </div>
                </div>
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <div class="cell xs-6 md-4 md-space-l-4">
                        <button class="btn btn-default" v-on:click="close">
                            <?=t('Cancel')?>
                        </button>
                    </div>
                    <div class="cell xs-6 md-4">
                        <button class="btn btn-success" v-on:click="submit">
                            <?=t('Save')?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>


<script type="text/x-template" id="translation-modal-template">
    <div class="modal-wrap hidden">
        <div class="modal card xs-11 sm-10 md-6 lg-4">
            <div class="card-header">
                <div class="modal-controls">
                    <button class="btn btn-primary btn-square" v-on:click="close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-title">
                    <i class="fa fa-lg fa-plus"></i> <?=t('Create translation')?>
                </div>
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <div v-if="showErrors && errors.length > 0" class="cell xs-12">
                        <div class="alert alert-danger">
                            <div v-for="error in errors">{{error}}</div>
                        </div>
                    </div>
                    <div v-if="showInfo && !showErrors" class="cell xs-12">
                        <div class="alert alert-info">
                            <button class="btn btn-primary btn-sm btn-square alert-control" v-on:click="hideInfo"><i class="fa fa-times"></i></button>
                            <?=t('This module is not provide automatic translation. It just create version of menu for specified language.')?>
                        </div>
                    </div>
                    <div class="cell xs-4 r">
                        <label><?=t('Language')?> <span class="text-danger">*</span></label>
                    </div>
                    <div class="cell xs-8">
                        <select class="form-control" v-model="translation.lang">
                        <?php foreach ($languages as $l) if (!$l['isTranslated']) { ?>
                            <option value="<?=$l['isoCode']?>">
                                <?="$l[nativeName] / $l[internationalName] ($l[isoCode])"?>
                            </option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="cell xs-space-l-4">
                        <label><input hidden type="checkbox" v-model="translation.open"><span></span> <?=t('Go editing after creation')?></label>
                    </div>
                    <div class="cell xs-space-l-4">
                        <label><input hidden type="checkbox" v-model="translation.duplicate"><span></span> <?=t('Duplicate from other language')?></label>
                    </div>
                    <div class="cell xs-4 r" v-show="translation.duplicate">
                        <label><?=t('Duplicate from')?> <span class="text-danger">*</span></label>
                    </div>
                    <div class="cell xs-8" v-show="translation.duplicate">
                        <select class="form-control" v-model="translation.duplicateFromLang">
                        <?php foreach ($languages as $l) if ($l['isTranslated']) { ?>
                            <option value="<?=$l['isoCode']?>">
                                <?="$l[nativeName] / $l[internationalName] ($l[isoCode])"?>
                            </option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-section">
                <div class="grid split-1">
                    <div class="cell xs-6 md-4 md-space-l-4">
                        <button class="btn btn-default" v-on:click="close">
                            <?=t('Cancel')?>
                        </button>
                    </div>
                    <div class="cell xs-6 md-4">
                        <button class="btn btn-success" v-on:click="submit">
                            <?=t('Create')?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<style>
    .loading-spinner {
        position: absolute;
        z-index: 10000;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
    }
    .loading-spinner > .fa {
        display: block;
        margin: auto;
    }
    .tree-view {
        list-style: none;
        position: relative;
        z-index: 1;
        margin: 0;
        padding: 0;
    }
    .tree-view-item {
        position: relative;
        transition: top 0.15s ease;
        z-index: 1;
    }
    .tree-view-item.on-drag {
        transition: none;
        z-index: 2;
    }
    .tree-view-item > .item-content {
        display: inline-block;
        border-radius: 3px;
        padding: 0.5em;
    }
    .tree-view-item > .item-content > .drag-handle {
        color:#bbb;
        cursor:move;
        width:2em;
        text-align: center;
        display:inline-block;
        vertical-align: middle;
    }
    .tree-view-item > .item-content > .no-drag-handle {
        width:2em;
        display:inline-block;
        vertical-align: middle;
    }
    .tree-view-item > .item-content > .item-link {
        display: inline-block;
        padding: 0 0.5em;
    }
    .tree-view-item > .tree-view {
        margin-left: 2em;
    }
    @media (min-width: 992px) {
        .tree-view-item.on-drag > .item-content,
        .tree-view-item > .item-content:hover {
            background-color: #f8f8f8;
        }
    }
</style>

<?php
module('js/draggableList');
module('js/vue::dev');
$this->addJsFile(pathToUrl(__DIR__.'/menu-editor.js'));
$this->addJsText('loadMenuEditor(\'#menu-editor-container\', '.json_encode($menu).','.core()->getTranslatorForJs().')');