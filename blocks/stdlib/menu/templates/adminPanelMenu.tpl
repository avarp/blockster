<?php

function drawMenu($menu, $id, $class)
{
    echo '<ul class="'.$class.'" id="'.$id.'">';
    foreach ($menu as $item) if (isset($item['href'])) {
        echo '<li><a href="'.SITE_URL.$item['href'].'"><i class="menu-icon fa '.$item['icon'].'"></i>'.$item['label'].'</a></li>';
    } elseif (isset($item['submenu'])) {
        $newId = 'submenu-'.rand();
        echo '<li><a data-toggle-height="'.$newId.'"><i class="submenu-mark fa fa-angle-down"></i><i class="menu-icon fa '.$item['icon'].'"></i>'.$item['label'].'</a>';
        drawMenu($item['submenu'], $newId, $class);
        echo '</li>';
    }
}

drawMenu($menu, $id, $class);