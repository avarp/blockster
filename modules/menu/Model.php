<?php
namespace modules\menu;

class Model
{
    protected $dbh;

    public function __construct()
    {
        $this->dbh = core()->dbh;
    }

    /**
     * Get menu by sysname and ISO code of language
     * @param string $sysname system name of menu
     * @param string $lang ISO code of language used in menu
     * @return array menu items
     */
    public function getMenu($sysname, $lang)
    {
        $menu = $this->dbh->row("
            SELECT menu.* 
            FROM menu
            INNER JOIN languages ON languages.id = menu.langId
            WHERE menu.sysname=? AND languages.isoCode=?
            LIMIT 1
        ", array($sysname, $lang));
        if (isset($menu['children'])) $menu['children'] = json_decode($menu['children'], true);
        return $menu;
    }
}