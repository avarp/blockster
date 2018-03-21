<?php
namespace modules\admin\authorization;

core()->eventBus->addEventHandler('onSystemStart', function() {
    if (isset($_POST['logOut'])) {
        $model = new Model;
        $model->logOut();
        header('Location: '.$_SERVER['REQUEST_URI']);
        die();
    }
});

core()->eventBus->addEventHandler('onPageRender', function() {
    
});
