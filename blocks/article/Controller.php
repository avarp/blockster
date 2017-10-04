<?php
namespace blocks\article;

class Controller extends \blocks\Controller
{
    public function action_default($params)
    {
        return '<div class="alert alert-info">article</div>';
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