<?php
namespace modules\admin\article;

class Controller extends \modules\Controller
{
    public function action_default($params)
    {
        return '<div class="alert alert-info">'.var_export($params, true).'</div>';
    }
}