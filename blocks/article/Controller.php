<?php
namespace blocks\article;

class Controller extends \blocks\Controller
{
    public function action_default()
    {
        $article = $this->model->findByUrl($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST']);
    }

    public function action_admin($params)
    {
        return '<div class="alert alert-info">article::admin</div>';
    }

    public function action_edit()
    {
        return '<div class="alert alert-info">article::editArticle</div>';
    }
}