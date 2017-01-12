<?php
namespace blocks\stdlib\menu;

class Model
{
	private $menuDir;

	public function __construct() {
		$this->menuDir = __DIR__.'/menuSources';
	}	

    public function getMenu($menuName)
    {
        $file = $this->menuDir.'/'.$menuName.'.json';
        if (file_exists($file)) return json_decode(file_get_contents($file), true);
        else return false;
    }


    public function createMenu($menuName)
    {
        $file = $this->menuDir.'/'.$menuName.'.json';
    	if (!file_exists($file)) return false !== file_put_contents($file, '[]');
        else return false;
    }


    public function deleteMenu($menuName)
    {
        $file = $this->menuDir.'/'.$menuName.'.json';
    	if (file_exists($file)) return false !== unlink($file);
        else return true;
    }


    public function listMenus()
    {
    	$files = scandir($this->menuDir);
    	$list = array();
    	foreach ($files as $file) if (pathinfo($file, PATHINFO_EXTENSION) == 'json') {
    		$list[] = pathinfo($file, PATHINFO_FILENAME);
    	}
    	return $list;
    }
}