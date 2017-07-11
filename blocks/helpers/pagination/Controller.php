<?php
namespace blocks\helpers\pagination;

class Controller extends \blocks\Controller
{
    public function action_default($params)
    {
        if (isset($params['url']) && isset($params['thisPage']) && isset($params['numPages'])) {
            $this->view->data = $params;
            return $this->view->render();
        } else {
            trigger_error('Block "pagination" requires parameters: url, thisPage, numPages', E_USER_WARNING);
        }
    }
}