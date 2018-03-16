<?php
$e = new \core\services\Eventor;
$e->attachHandler('onSystemStart', function() {
    if (isset($_POST['logOut'])) {
        $model = new \modules\admin\authorization\Model;
        $model->logOut();
        header('Location: '.$_SERVER['REQUEST_URI']);
        die();
    }
});
