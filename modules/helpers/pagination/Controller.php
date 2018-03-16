<?php
namespace modules\helpers\pagination;

class Controller extends \modules\Controller
{
    public function action_default($params)
    {
        if (isset($params['url']) && isset($params['thisPage']) && isset($params['numPages'])) {
            $this->view->data = $params;
            return $this->view->render();
        } else {
            throw new Exception('Module "pagination" requires parameters: url, thisPage, numPages', E_USER_WARNING);
        }
    }
}