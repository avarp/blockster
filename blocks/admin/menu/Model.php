<?php
namespace blocks\admin\menu;

class Model
{
    protected $dbh;
    public function __construct()
    {
        $this->dbh = new \services\database\Sqlite;
    }

    protected $menuitemTemplate = array(
        'id' => 0,
        'ordNum' => 0,
        'parentId' => 0,
        'template' => '',
        'name' => '',
        'href' => ''
    );

    public function createMenuitem($parentId=0)
    {
        $menuitem = $this->menuitemTemplate;
        $menuitem['parentId'] = $parentId;
        return $menuitem;
    }

    public function saveMenuitem($menuitem)
    {
        $keys = array_keys($this->menuitemTemplate);
        array_shift($keys); // remove key "id"
        if ($menuitem['id'] == 0) {
            // set order number
            if ($menuitem['parentId'] != 0) {
                $r = $this->dbh->row("SELECT COUNT(id) AS c FROM menuitems WHERE parentId=?", array($menuitem['parentId']));
                $menuitem['ordNum'] = $r['c'] + 1;
            }
            $fields = '('.implode(',', $keys).')';
            $placeholders = '(:'.implode(',:', $keys).')';
            $sql = "INSERT INTO menuitems $fields VALUES $placeholders";
        } else {
            if ($menuitem['parentId'] != 0) {
                $r = $this->dbh->row("SELECT COUNT(id) AS c FROM menuitems WHERE id=?", array($menuitem['id']));
                // when parentId is changed, need to adjust order number
                if ($menuitem['parentId'] != $r['parentId']) {
                    $this->dbh->exec("UPDATE menuitems SET ordNum=ordNum-1 WHERE ordNum>$r[ordNum] AND parentId=$r[parentId]");
                    $r = $this->dbh->row("SELECT COUNT(id) AS c FROM menuitems WHERE parentId=?", array($menuitem[parentId]));
                    $menuitem['ordNum'] = $r['c'] + 1;
                }
            }
            $set = array();
            foreach ($keys as $key) if ($key != 'id') $set[] = $key.'=:'.$key;
            $set = implode(',', $set);
            $sql = "UPDATE menuitems SET $set WHERE id=:id";
        }

        if ($this->dbh->exec($sql, $menuitem)) {
            return $menuitem['id'] != 0 ? $menuitem['id'] : $this->dbh->lastInsertId();
        } else {
            return 0;
        }
    }

    public function getMenuitem($id)
    {
        return $this->dbh->row("SELECT * FROM menuitems WHERE id=? LIMIT 1", array($id));
    }

    public function getMenus()
    {
        return $this->dbh->table("SELECT * FROM menuitems WHERE parentId=0");
    }

    public function fetchMenuById($parentId)
    {
        $menuitems = $this->dbh->table("SELECT * FROM menuitems WHERE id=? AND parentId=0 LIMIT 1", array($parentId));
        foreach ($menuitems as $n => $menuitem) {
            $menuitems[$n]['childs'] = $this->fetchMenuTree($menuitem['id']);
        }
        return $menuitems;
    }

    public function fetchMenuByName($name)
    {
        $menuitem = $this->dbh->row("SELECT * FROM menuitems WHERE name=? AND parentId=0 LIMIT 1", array($name));
        return $menuitem ? $this->fetchMenuById($menuitem['id']) : array();
    }

    public function isRootMenuitemUnique($menuitem)
    {
        $dublicate = $this->dbh->row("SELECT * FROM menuitems WHERE name=? AND parentId=0 LIMIT 1", array($menuitem['name']));
        if ($dublicate && $dublicate['id'] != $menuitem['id']) {
            return false;
        } else {
            return true;
        }
    } 

    public function deleteMenu($id)
    {
        $menuitem = $this->dbh->exec("DELETE FROM menuitems WHERE id=?", array($id));
        $childs = $this->dbh->table("SELECT id FROM menuitems WHERE parentId=?", array($id));
        foreach ($childs as $child) $this->deleteMenu($child['id']);        
    }
}