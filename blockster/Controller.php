<?php
namespace blockster;

class Controller
{
    protected $view;
    protected $model;

    public function __construct($view, $model)
    {
        $this->view = $view;
        $this->model = $model;
    }
}