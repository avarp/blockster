<?php

function renderAdminPanelMenu($menu, $id, $class)
{
    echo '<ul class="'.$class.'" id="'.$id.'">';
    foreach ($menu as $item) if (isset($item['href'])) {
        echo '<li><a href="'.SITE_URL.$item['href'].'">'.$item['icon'].' '.$item['label'].'</a></li>';
    } elseif (isset($item['submenu'])) {
        $newId = 'submenu-'.rand();
        echo '<li><a data-wf-actions=\'{"click":[{"action":"toggleHeight", "target":"#'.$newId.'"}]}\'><i class="submenu-mark fa fa-angle-down"></i>'.$item['icon'].' '.$item['label'].'</a>';
        renderAdminPanelMenu($item['submenu'], $newId, $class);
        echo '</li>';
    }
}

renderAdminPanelMenu($menu, $id, $class);