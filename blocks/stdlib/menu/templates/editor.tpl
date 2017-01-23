<h1>Меню &quot;<?=$_GET['menuName']?>&quot;</h1><?php

function drawEditableMenu($menu)
{
    $dropdownMenu = 
    '<button class="btn btn-default fa fa-ellipsis-h" data-wf-actions=\'{"click":[{"action":"showDropdown"}]}\'>
        <ul class="dropdown hidden">
            <li>Редактировать</li>
            <li>Вырезать</li>
            <li>Вставить</li>
            <li>Удалить</li>
        </ul>
    </button>';

    echo '<ul class="editable-menu">';
    foreach ($menu as $n => $item) if (isset($item['href'])) {
        echo '<li><div class="label"><a href="'.SITE_URL.$item['href'].'" target="_blank">'.$item['label'].'</a>'.$dropdownMenu.'</div></li>';
    } elseif (isset($item['submenu'])) {
        echo '<li><div class="label">'.$item['label'].$dropdownMenu.'</div>';
        drawEditableMenu($item['submenu']);
        echo '</li>';
    }
    echo '</ul>';
}

drawEditableMenu($menu);?>


<style>
    .editable-menu {
        list-style: none;
    }
    .editable-menu .editable-menu {
        padding-left: 1.25em;
        margin-left: 1.25em;
        border-left: 1px dashed #ccc;
    }
    .editable-menu > li > .label {
        display: inline-block;
        background-color: #fff;
        padding-left: 1em;
        box-shadow: 0 1px 5px rgba(0,0,0,0.35);
        border-radius: 2px;
        margin: 0.5em 0;
    }
    .editable-menu > li > .label > .btn {
        border-width: 0;
        border-radius: 0;
        border-left-width: 1px;
        margin-left: 1em;
    }
</style>