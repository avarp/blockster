<?php
namespace modules\admin\authorization;
const OFFLINE_DELAY = 900;

core()->eventBus->addEventHandler('onPageRender', function() {
    if (isset($_POST['logOut'])) {
        $model = new Model;
        $model->logOut();
        rebuildPage();
    }
});

core()->eventBus->addEventHandler('onPageRender', function() {
    if (isset($_SESSION['user'])) {
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