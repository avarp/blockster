<?php
namespace modules\admin\authorization;
const OFFLINE_DELAY = 900;

core()->eventBus->addEventHandler('onSystemStart', function() {
    if (isset($_POST['logOut'])) {
        $model = new Model;
        $model->logOut();
        header('Location: '.$_SERVER['REQUEST_URI']);
        die();
    }
});

core()->eventBus->addEventHandler('onModuleLoad', function($module) {
    if ($module['depth'] == 0 && isset($_SESSION['user'])) {
        $model = new Model;
        $model->updateTrackingTimestamp();
        core()->broadcastMessage('addJsText',
            "setInterval(function(){".
                "var xhr = new XMLHttpRequest();".
                "xhr.open('GET', '".SITE_URL."/ajax/admin/authorization::trackingUsers', true);".
                "xhr.send()".
            "}, ".(OFFLINE_DELAY*1000).")"
        );
    }
});