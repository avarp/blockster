<?php

function renderAdminPanelMenu($menu, $id)
{
    echo '<ul class="menu hidden-md-down" id="'.$id.'">';
    foreach ($menu as $item) if (isset($item['href'])) {
        echo '<li><a href="'.SITE_URL.$item['href'].'">'.$item['icon'].' '.$item['label'].'</a></li>';
    } elseif (isset($item['submenu'])) {
        $newId = 'submenu-'.rand();
        echo '<li><a data-wf-actions=\'{"click":[{"action":"toggleHeight", "target":"#'.$newId.'"}]}\'><span class="submenu-mark"><i class="fa fa-angle-down"></i></span>'.$item['icon'].' '.$item['label'].'</a>';
        renderAdminPanelMenu($item['submenu'], $newId);
        echo '</li>';
    }
}

renderAdminPanelMenu($menu, 'main-menu');