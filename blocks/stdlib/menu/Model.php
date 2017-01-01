<?php
namespace blocks\stdlib\menu;

class Model
{
    public function getMenu($menuName)
    {
        $file = __DIR__.'/menuSources/'.$menuName.'.json';
        if (file_exists($file)) return json_decode(file_get_contents($file), true);
        else return false;
    }
}