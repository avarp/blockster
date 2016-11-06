<?php
return array(
    'error404' => array(
        'content' => array(
            'template' => '/templates/default/404.tpl',
            'accessLevel' => 0,
            'positions' => array(),
            'events' => array(),
        ),
    ),

    'auth' => array(
        'content' => array(
            'template' => '/templates/admin/auth.tpl',
            'accessLevel' => 0,
            'positions' => array(),
            'events' => array(),
        ),
    ),

    '/admin' => array(
        'selector' => array(
            'rgxp' => '/admin',
        ),
        'content' => array(
            'template' => '/templates/admin/index.tpl',
            'accessLevel' => 100,
            'positions' => array(
                'content' => array(
                    array('stdlib/admin', array(), ''),
                ),
            ),
            'events' => array(),
        ),
    ),

    '/admin/settings' => array(
        'selector' => array(
            'rgxp' => '/admin/settings',
        ),
        'content' => array(
            'template' => '/templates/admin/index.tpl',
            'accessLevel' => 100,
            'positions' => array(
                'content' => array(
                    array('stdlib/admin::actionSettings', array(), ''),
                ),
            ),
            'events' => array(),
        ),
    ),

    '/admin/users' => array(
        'selector' => array(
            'rgxp' => '/admin/users',
        ),
        'content' => array(
            'template' => '/templates/admin/index.tpl',
            'accessLevel' => 100,
            'positions' => array(
                'content' => array(
                    array('stdlib/users', array(), ''),
                ),
            ),
            'events' => array(),
        ),
    ),

    '/admin/menu' => array(
        'selector' => array(
            'rgxp' => '/admin/menu',
        ),
        'content' => array(
            'template' => '/templates/admin/index.tpl',
            'accessLevel' => 100,
            'positions' => array(
                'content' => array(
                    array('stdlib/menu::actionManager', array(), ''),
                ),
            ),
            'events' => array(),
        ),
    ),

    'adminer' => array(
        'selector' => array(
            'rgxp' => '/adminer',
        ),
        'content' => array(
            'template' => '/templates/admin/adminer.php',
            'accessLevel' => 100,
            'positions' => array(),
            'events' => array(),
        ),
    ),

    'root' => array(
        'selector' => array(
            'rgxp' => '/',
        ),
        'content' => array(
            'template' => '/templates/default/index.tpl',
            'accessLevel' => 0,
            'positions' => array(),
            'events' => array(),
        ),
    ),
);