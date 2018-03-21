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
     * Get language for new menu
     * @return string ISO code of language, which is active in system
     */
    protected function detectLanguage()
    {
        $l = core()->getLang('isoCode');
        $c = $this->dbh->value('SELECT COUNT(isoCode) FROM languages WHERE isoCode=? AND isActive=1', array($l));
        if ($c > 0) return $l;
        return $this->dbh->value('SELECT isoCode FROM languages WHERE isActive=1 LIMIT 1');
    }

    /**
     * Create new menu
     * @uses function detectLanguage
     * @param string $sysname system name of new menu
     * @param string $lang language of new menu
     * @return array new menu
     */
    public function createMenu($sysname='', $lang='')
    {
        if (empty($sysname)) $sysname = 'menu-'.time();
        if (empty($lang)) $lang = $this->detectLanguage();

        return array(
            'id' => 0,
            'label' => t('New menu'),
            'sysname' => $sysname,
            'lang' => $lang,
            'children' => array(),
        );
    }





    /**
     * Get list of menus
     * @return array list of menus with available translations
     */
    public function getListOfMenu()
    {
        $userLang = core()->lang['isoCode'];
        $menus = $this->dbh->table('SELECT label, href as "sysname" FROM menu WHERE parentId=0 AND customField=?', array($userLang));
        $userLangLabels = array();
        foreach ($menus as $m) {
            $userLangLabels[$m['sysname']] = $m['label'];
        }

        $menus = $this->dbh->table('SELECT label, href as "sysname", group_concat(customField) as langs FROM menu WHERE parentId=0 GROUP BY href');
        foreach ($menus as $n=>$m) {
            $menus[$n]['langs'] = explode(',', $m['langs']);
            if (isset($userLangLabels[$m['sysname']])) $menus[$n]['label'] = $userLangLabels[$m['sysname']];
        }
        return $menus;
    }

    /**
     * Get raw tree of menu
     * @uses function fetchTree recursively
     * @param integer $id id of root record (same as id of menu)
     * @return array raw menu tree
     */
    protected function fetchTree($id) {
        $menu = $this->dbh->row('SELECT id, accessLevel, customField, label, href FROM menu WHERE id=?', array($id));
        if (!$menu) return $menu;
        $menu['children'] = array();
        $childIds = $this->dbh->table('SELECT id FROM menu WHERE parentId=? ORDER BY ordNum ASC', array($id));
        foreach ($childIds as $child) {
            $menu['children'][] = $this->fetchTree($child['id']);
        }
        return $menu;
    }
    
    /**
     * Get menu by id. Fetch tree and adjust fields of root element
     * @uses function fetchTree
     * @param integer $id id of menu
     * @return array menu
     */
    public function getMenuById($id)
    {
        $menu = $this->fetchTree($id);
        $menu['sysname'] = $menu['href'];
        $menu['lang'] = $menu['customField'];
        unset($menu['accessLevel']);
        unset($menu['ordNum']);
        unset($menu['parentId']);
        unset($menu['href']);
        unset($menu['customField']);
        return $menu;
    }

    /**
     * Get menu by sysname and language
     * @uses function getMenuById
     * @param string $sysname system name of menu
     * @param string $lang language of menu
     * @return array menu
     */
    public function getMenuBySysname($sysname, $lang)
    {
        $menuId = $this->dbh->value('SELECT id FROM menu WHERE href=? AND customField=? AND parentId=0 LIMIT 1', array($sysname, $lang));
        if ($menuId) {
            return $this->getMenuById($menuId);
        } else {
            return array();
        }
    }

    /**
     * Get info about menu by id
     * @param integer $id id of menu
     * @return array info about menu
     */
    public function getMenuInfoById($id)
    {
        return $this->dbh->row('
            SELECT id, label, href as "sysname", customField as lang
            FROM menu 
            WHERE id=? AND parentId=0
            LIMIT 1',
        array($id));
    }

    /**
     * Get info about menu by sysname and language
     * @uses function getMenuInfoById
     * @param string $sysname system name of menu
     * @param string $lang language of menu
     * @return array info about menu
     */
    public function getMenuInfoBySysname($sysname, $lang='')
    {
        if (!empty($lang)) {
            $menuId = $this->dbh->value('SELECT id FROM menu WHERE href=? AND customField=? AND parentId=0 LIMIT 1', array($sysname, $lang));
        } else {
            $menuId = $this->dbh->value('SELECT id FROM menu WHERE href=? AND parentId=0 LIMIT 1', array($sysname));
        }
        return $this->getMenuInfoById($menuId);
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
            $list[$n]['isTranslated'] = 
                $this->dbh->value('SELECT COUNT(id) FROM menu WHERE href=? AND customField=?', array($sysname, $l['isoCode'])) != 0;
        }
        return $list;
    }

    



    /**
     * Get plain array of ids of children of menu in database
     * @uses function listChildrenIdsFromDb recursively
     * @param integer $parentId id of menu
     * @return array list of ids
     */
    protected function listChildrenIdsFromDb($parentId) {
        $ids = array();
        $children = $this->dbh->table('SELECT id FROM menu WHERE parentId=?', array($parentId));
        foreach ($children as $child) {
            $ids = array_merge($ids, array($child['id']), $this->listChildrenIdsFromDb($child['id']));
        }
        return $ids;
    }

    /**
     * Get plain array of ids of children of menu is to be saved
     * @uses function listChildrenIdsFromArr recursively
     * @param array $menu menu is to be saved
     * @return array list of ids
     */
    protected function listChildrenIdsFromArr($menu) {
        $ids = array();
        foreach ($menu['children'] as $child) {
            $ids = array_merge($ids, array($child['id']), $this->listChildrenIdsFromArr($child));
        }
        return $ids;
    }

    /**
     * Recursive save items of menu
     * @uses function saveChildren recursively
     * @param array $children list of menuitems
     * @param integer $parentId id of their parent
     * @return void
     */
    protected function saveChildren($children, $parentId) {
        foreach ($children as $ordNum => $child) {
            $child['ordNum'] = $ordNum;
            $child['parentId'] = $parentId;
            if ($child['id'] == 0) {
                $this->dbh->exec('INSERT INTO menu (accessLevel, customField, label, href, ordNum, parentId) VALUES (:accessLevel, :customField, :label, :href, :ordNum, :parentId)' , $child);
                $child['id'] = $this->dbh->lastInsertId();
            } else {
                $this->dbh->exec('UPDATE menu SET accessLevel=:accessLevel, customField=:customField, label=:label, href=:href, ordNum=:ordNum, parentId=:parentId WHERE id=:id', $child);
            }
            $this->saveChildren($child['children'], $child['id']);
        }
    }

    /**
     * Save menu
     * @uses function listChildrenIdsFromDb
     * @uses function listChildrenIdsFromArr
     * @uses function saveChildren
     * @param array $menu menu is to be saved
     * @return integer id of saved menu
     */
    public function saveMenu($menu) {
        if ($menu['id'] == 0) {
            $this->dbh->exec('INSERT INTO menu (customField, label, href) VALUES (:lang, :label, :sysname)', $menu);
            $menu['id'] = $this->dbh->lastInsertId();
        } else {
            $sysname = $this->dbh->value('SELECT href FROM menu WHERE id=?', array($menu['id']));
            if ($sysname != $menu['sysname']) {
                $this->dbh->exec('UPDATE menu SET href=? WHERE href=?', array($menu['sysname'], $sysname));
            }
            $this->dbh->exec('UPDATE menu SET customField=:lang, label=:label, href=:sysname WHERE id=:id', $menu);
            $inDb = $this->listChildrenIdsFromDb($menu['id']);
            $inArr = $this->listChildrenIdsFromArr($menu);
            $toDelete = array_diff($inDb, $inArr);
            $this->dbh->exec('DELETE FROM menu WHERE id IN ('.implode(',', $toDelete).')');
        }
        $this->saveChildren($menu['children'], $menu['id']);
        return $menu['id'];
    }

    /**
     * Recursively reset all ids as zero in menu tree
     * @uses function resetIds recursively
     * @return array menu
     */
    protected function resetIds($menu)
    {
        $menu['id'] = 0;
        if (!empty($menu['children'])) foreach ($menu['children'] as $n=>$child) $menu['children'][$n] = $this->resetIds($child);
        return $menu;
    }

    /**
     * Save copy of menu with specified sysname and language 
     * @uses function saveChildren
     * @uses function resetIds
     * @param array $menu menu is to be copied
     * @param string $sysname new sysname
     * @param string $lang new lang
     * @return integer id of saved menu
     */
    public function saveMenuAs($menu, $sysname, $lang) {
        $menu = $this->resetIds($menu);
        $menu['sysname'] = $sysname;
        $menu['lang'] = $lang;
        return $this->saveMenu($menu);
    }





    /**
     * Delete menu by id
     * @uses function deleteMenuById recursively
     * @param integer $id id of menu
     * @return void
     */
    public function deleteMenuById($id)
    {
        $menuitem = $this->dbh->exec('DELETE FROM menu WHERE id=?', array($id));
        $childs = $this->dbh->table('SELECT id FROM menu WHERE parentId=?', array($id));
        foreach ($childs as $child) $this->deleteMenuById($child['id']);        
    }

    /**
     * Delete menu(s) by sysname
     * @uses function deleteMenuById
     * @param string $sysname system name of menu
     * @return void
     */
    public function deleteMenuBySysname($sysname)
    {
        $menus = $this->dbh->table('SELECT id FROM menu WHERE href=?', array($sysname));
        foreach ($menus as $m) $this->deleteMenuById($m['id']);     
    }       
}