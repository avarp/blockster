<?php
namespace blocks;

class Controller
{
    protected $view;
    protected $model;
    protected $parent;
    protected $page;

    public function __construct($view, $model, $parent, $page)
    {
        $this->view = $view;
        $this->model = $model;
        $this->parent = $parent;
    	$this->page = $page;
    }
}