<div class="card">
    <div class="card-header-big">
        <h1><?=$h1?></h1>
        <ul class="breadcrumbs">
        <?php foreach ($breadcrumbs as $b) { ?>
            <li><a href="<?=$b['href']?>"><?=$b['label']?></a></li>
        <?php } ?>
        </ul>
    </div>
    <div class="card-section">
        <a href="<?=core()->getUrl('route:admin > menu/new')?>" class="btn btn-success">
            <i class="fa fa-plus"></i> <?=t('Create menu')?>
        </a>
    </div>
    <div class="card-section" id="menus-table">
        <?php if ($menus) { ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?=t('System name')?></th>
                            <th><?=t('Name')?></th>
                            <th><?=t('Actions')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($menus as $menu) { ?>
                        <tr>
                            <td><?=$menu['sysname']?></td>
                            <td><?=$menu['label']?></td>
                            <td class="nowrap">
                                <form method="POST">
                                    <?php foreach ($menu['langs'] as $l) { ?>
                                        <a
                                            href="<?=core()->getUrl("route:admin > menu/$menu[sysname]/$l")?>"
                                            class="btn btn-default <?=count($menu['langs']) > 1 ? '' : 'btn-square'?> btn-sm"
                                        >
                                            <i class="fa fa fa-pencil"></i><?=count($menu['langs']) > 1 ? ' '.$l : ''?>
                                        </a>
                                    <?php } ?>
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-square btn-danger"
                                        name="deleteMenu"
                                        onclick="return confirm('<?=t('Delete menu? Undelete will not be possible.')?>')"
                                        value="<?=$menu['sysname']?>"
                                    >
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <?=t('There is no menu created.')?>
        <?php } ?> 
    </div>
</div>

<?php
if ($deleteMenuSuccess) $this->addJsText("pushNotification('success', '".t('Menu is deleted.')."')");