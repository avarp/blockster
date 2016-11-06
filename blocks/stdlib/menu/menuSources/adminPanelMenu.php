<?php return array(
    array(
        'label' => 'Панель управления',
        'icon' => 'fa-home',
        'href' => '/admin',
    ),
    array(
        'label' => 'Настройка системы',
        'icon' => 'fa-cogs',
        'href' => '/admin/settings',
    ),
    array(
        'label' => 'База данных',
        'icon' => 'fa-database',
        'href' => '/adminer',
    ),
    array(
        'label' => 'Пользователи',
        'icon' => 'fa-users',
        'href' => '/admin/users',
    ),
    array(
        'label' => 'Менеджер меню',
        'icon' => 'fa-list',
        'href' => '/admin/menu',
    ),
    array(
        'label' => 'Выпадающее меню',
        'icon' => 'fa-list',
        'submenu' => array(
            array(
                'label' => 'Панель управления',
                'icon' => 'fa-home',
                'href' => '/admin',
            ),
            array(
                'label' => 'Настройка системы',
                'icon' => 'fa-cogs',
                'href' => '/admin/settings',
            ),
            array(
                'label' => 'База данных',
                'icon' => 'fa-database',
                'href' => '/adminer',
            ),
            array(
                'label' => 'Пользователи',
                'icon' => 'fa-users',
                'href' => '/admin/users',
            ),
            array(
                'label' => 'Менеджер меню',
                'icon' => 'fa-list',
                'href' => '/admin/menu',
            ),

        ),
    ),
);