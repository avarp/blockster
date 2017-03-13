<?php
namespace blocks\pagination;

class Controller extends \proto\Controller
{
    public function actionIndex($params)
    {
        if (isset($params['url']) && isset($params['thisPage']) && isset($params['numPages'])) {
            return $this->view->render($params);
        } else {
            trigger_error('Block "pagination" requires parameters: url, thisPage, numPages', E_USER_WARNING);
        }
    }
}