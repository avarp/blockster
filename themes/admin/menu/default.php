<?php function drawMenu($menu) { ?>
    <?php
        if (isset($menu['children']))
        foreach ($menu['children'] as $item)
        if (isset($item['children']) && !empty($item['children']))
    { ?>
        <li>
            <span onclick="Mov.toggleHeight({target:this.parentNode.querySelector('.menu')})">
                <i class="<?=$item['customField']?>"></i><?=$item['label']?>
                <i class="fa fa-caret-down submenu-mark"></i>
            </span>
            <ul class="menu hidden"><?=drawMenu($item)?></ul>
        </li>
    <?php } else { ?>
        <li><a href="<?=core()->getUrl($item['href'])?>"><i class="<?=$item['customField']?>"></i><?=$item['label']?></a></li>
    <?php } ?>
<?php } ?>

<ul class="menu">
    <?=drawMenu($menu)?>
</ul>