<?php
namespace modules\admin\menu;

class Model
{
    protected $dbh;

    public function __construct()
    {
        $this->dbh = core()->dbh;
    }





    /**
     * Get language id for new menu
     * @return string ISO code of language, which is active in system
     */
    protected function detectLanguageId()
    {
        $l = core()->lang['id'];
        $c = $this->dbh->value('SELECT COUNT(id) FROM languages WHERE id=? AND isActive=1', array($l));
        if ($c > 0) return $l;
        return $this->dbh->value('SELECT id FROM languages WHERE isActive=1 LIMIT 1');
    }

    /**
     * Create new menu
     * @uses function detectLanguage
     * @param string $sysname system name of new menu
     * @param string $lang language of new menu
     * @return array new menu
     */
    public function createMenu($sysname='', $langId=0)
    {
        if (empty($sysname)) $sysname = 'menu-'.time();
        if (empty($langId)) $langId = $this->detectLanguageId();

        return array(
            'id' => 0,
            'langId' => $langId,
            'sysname' => $sysname,
            'name' => t('New menu'),
            'children' => array()
        );
    }





    /**
     * Get list of menus
     * @return array list of menus with available translations
     */
    public function getListOfMenu()
    {
        $userLang = core()->lang['id'];
        $menus = $this->dbh->table('SELECT name, sysname FROM menu WHERE langId=?', array($userLang));
        $userLangLabels = array();
        foreach ($menus as $m) {
            $userLangLabels[$m['sysname']] = $m['name'];
        }

        $menus = $this->dbh->table('
            SELECT menu.name, menu.sysname, GROUP_CONCAT(menu.id) as ids, GROUP_CONCAT(languages.isoCode) as langs
            FROM menu
            INNER JOIN languages ON menu.langId=languages.id
            GROUP BY menu.sysname, menu.name
        ');
        foreach ($menus as $n=>$m) {
            $menus[$n]['langs'] = explode(',', $m['langs']);
            $menus[$n]['ids'] = explode(',', $m['ids']);
            if (isset($userLangLabels[$m['sysname']])) $menus[$n]['label'] = $userLangLabels[$m['sysname']];
        }
        return $menus;
    }

    
    /**
     * Get menu by id. Fetch tree and adjust fields of root element
     * @param integer $id id of menu
     * @return array menu
     */
    public function getMenuById($id)
    {
        $menu = $this->dbh->row("SELECT * FROM menu WHERE id=$id LIMIT 1");
        if ($menu) $menu['children'] = json_decode($menu['children'], true);
        return $menu;
    }

    /**
     * Get menu by sysname and language
     * @uses function getMenuById
     * @param string $sysname system name of menu
     * @param string $langId language id of menu
     * @return array menu
     */
    public function getMenuBySysname($sysname, $langId=0)
    {
        if ($langId) {
            $menuId = $this->dbh->value('SELECT id FROM menu WHERE sysname=? AND langId=? LIMIT 1', array($sysname, $langId));
        } else {
            $menuId = $this->dbh->value('SELECT id FROM menu WHERE sysname=? LIMIT 1', array($sysname));
        }
        if ($menuId) {
            return $this->getMenuById($menuId);
        } else {
            return array();
        }
    }

    /**
     * Get list of all active languages in system with information about existence
     * of translation menu with specified sysname
     * @param string $sysname system name of menu
     * @return array list of languages
     */
    public function getLangList($sysname)
    {
        $list = $this->dbh->table('SELECT * FROM languages WHERE isActive=1');
        foreach ($list as $n=>$l) {
            $list[$n]['menuId'] = 
                $this->dbh->value('SELECT id FROM menu WHERE sysname=? AND langId=?', array($sysname, $l['id']));
        }
        return $list;
    }

    



    /**
     * Save menu
     * @param array $menu menu is to be saved
     * @return integer id of saved menu
     */
    public function saveMenu($menu) {
        $menu['children'] = json_encode($menu['children']);
        if ($menu['id'] == 0) {
            $this->dbh->exec('
                INSERT INTO menu
                (langId, sysname, name, children) 
                VALUES (:langId, :sysname, :name, :children)
            ', $menu);
            $menu['id'] = $this->dbh->lastInsertId();
        } else {
            $sysname = $this->dbh->value('SELECT sysname FROM menu WHERE id=?', array($menu['id']));
            if ($sysname != $menu['sysname']) {
                $this->dbh->exec('UPDATE menu SET sysname=? WHERE sysname=?', array($menu['sysname'], $sysname));
            }
            $this->dbh->exec('
                UPDATE menu SET
                langId=:langId, sysname=:sysname, name=:name, children=:children
                WHERE id=:id
            ', $menu);
        }
        return $menu['id'];
    }


    /**
     * Save copy of menu with specified sysname and language 
     * @uses function saveMenu
     * @param array $menu menu is to be copied
     * @param string $sysname new sysname
     * @param string $langId new language id
     * @return integer id of saved menu
     */
    public function saveMenuAs($menu, $sysname, $langId) {
        $menu['sysname'] = $sysname;
        $menu['langId'] = $langId;
        $menu['id'] = 0;
        return $this->saveMenu($menu);
    }





    /**
     * Delete menu by id
     * @param integer $id id of menu
     * @return void
     */
    public function deleteMenuById($id)
    {
        $this->dbh->exec('DELETE FROM menu WHERE id=?', array($id));
    }

    /**
     * Delete menu(s) by sysname
     * @param string $sysname system name of menu
     * @return void
     */
    public function deleteMenuBySysname($sysname)
    {
        $this->dbh->exec('DELETE FROM menu WHERE sysname=?', array($sysname));
    }       
}