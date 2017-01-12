<h1>Меню &quot;<?=$_GET['menuName']?>&quot;</h1><?php

function drawEditableMenu($menu)
{
    echo '<ul class="editable-menu">';
    foreach ($menu as $item) if (isset($item['href'])) {
        echo '<li>'.$item['icon'].' '.$item['label'].'</li>';
    } elseif (isset($item['submenu'])) {
        echo '<li>'.$item['icon'].' '.$item['label'];
        drawEditableMenu($item['submenu']);
        echo '</li>';
    }
}

drawEditableMenu($menu);
