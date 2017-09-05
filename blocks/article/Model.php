<?php
namespace blocks\article;
use services\database\Database;

class Model
{
    protected $dbh;
    public function __construct()
    {
        $this->dbh = new Database;
    }

    protected $articleTemplate = array(
        'id' => 0,
        'language' => '',
        'url' => '',
        'host' => '',
        'header' => '',
        'overview' => '',
        'photo' => '',
        'content' => '',
        'title' => '',
        'keywords' => '',
        'description' => '',
        'dateCreation' => 0,
        'dateEdition' => 0,
        'datePublish' => 0,
        'parentId' => 0
    );

    public function create($parentId)
    {
        $article = $this->articleTemplate;
        $article['host'] = $_SERVER['HTTP_HOST'];
        $article['dateCreation'] = time();
        $article['dateEdition'] = time();
        $article['datePublish'] = time();
        $article['parentId'] = $parentId;
        return $article;
    }

    public function save($article)
    {
        $keys = array_keys($this->articleTemplate);

        if ($article['id'] == 0) {
            $fields = '('.implode(',', $keys).')';
            $placeholders = '(:'.implode(',:', $keys).')';
            $sql = "INSERT INTO articles $fields VALUES $placeholders";
        } else {
            $article['dateEdition'] = time();
            $set = array();
            foreach ($keys as $key) if ($key != 'id') $set[] = $key.'=:'.$key;
            $set = implode(',', $set);
            $sql = "UPDATE news SET $set WHERE id=:id";
            $this->adjustDatePublishOfChilds($article['id'], $article['datePublish']);
        }

        if ($this->dbh->exec($sql, $article)) {
            return $article['id'] != 0 ? $article['id'] : $this->dbh->lastInsertId();
        } else {
            return 0;
        }
    }

    protected function adjustDatePublishOfChilds($parentId, $time)
    {
        $childs = $this->dbh->query("SELECT * FROM articles WHERE parentId=$parentId");
        foreach ($childs as $child) {
            if ($child['datePublish'] < $time) {
                $this->dbh->exec("UPDATE articles SET datePublish=$time WHERE id=$child[id]");
                $this->adjustDatePublishOfChilds($child['id'], $time);
            }
        }
    }

    public function delete($id)
    {
        $id = intval($id);
        $a = $this->dbh->value("SELECT photo FROM articles WHERE id=$id"); 
        if (!$a) return 0;
        if ($a['photo']) {
            @unlink(ROOT_DIR.'/uploads/article/'.$a['photo']);
            @unlink(ROOT_DIR.'/uploads/article/mini-'.$a['photo']);
            @unlink(ROOT_DIR.'/uploads/article/micro-'.$a['photo']);
        }
        $this->dbh->exec("DELETE FROM articles WHERE id=$id");
        $deleted = 1;
        $childs = $this->dbh->table("SELECT id FROM articles WHERE parentId=$id");
        if ($childs) foreach ($childs as $child) $deleted += $this->delete($child['id']);
        return $deleted;
    }

    public function get($id)
    {
        $id = intval($id);
        return $this->dbh->row("SELECT * FROM articles WHERE id=$id");
    }

    public function readChilds($parentId, $order='', $offset=0, $limit=0)
    {
        $sql = "SELECT * FROM articles WHERE parentId=?";
        $params = array($parentId);
        if ($order) $sql .= " ORDER BY $order";
        if ($offset != 0 && $limit != 0) {
            $params[] = $offset;
            $params[] = $limit;
            $sql .= " LIMIT ?,?";
        }
        return $this->dbh->table($sql, $params);
    }

    public function findByUrl($url, $host)
    {
        if ($host) return $this->dbh->row("SELECT * FROM articles WHERE url=? AND host=? LIMIT 1", array($url, $host));
        else return $this->dbh->row("SELECT * FROM articles WHERE url=? LIMIT 1", array($url));
    }

    public function isAvailableParent($id, $parentId)
    {
        $id = intval($id);
        if ($id == $parentId) return false;
        $childs = $this->dbh->table("SELECT id FROM articles WHERE parentId=$id");
        if ($childs) foreach ($childs as $child) {
            if (!$this->isAvailableParent($child['id'], $parentId)) return false;
        }
        return true;
    }
}