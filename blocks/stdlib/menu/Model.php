<?php
namespace blocks\stdlib\menu;

class Model
{
    public function getMenu($menuName)
    {
        $file = __DIR__.'/menuSources/'.$menuName.'.php';
        if (file_exists($file)) return require($file);
        else return false;
    }
}