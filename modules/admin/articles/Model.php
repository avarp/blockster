<?php
namespace modules\admin\articles;
use core\Sqlite;

class Model
{
    protected $dbh;
    protected $articleTemplate;
    public function __construct()
    {
        $this->dbh = core()->dbh;
        $this->articleTemplate = array(
            'id' => 0,
            'langId' => '',
            'translationGroupId' => 0,
            'url' => '',
            'header' => '',
            'overview' => '',
            'template' => 'default.php',
            'photo' => '',
            'content' => '',
            'seoTitle' => '',
            'seoKeywords' => '',
            'seoDescription' => '',
            'dateCreation' => time(),
            'dateEdition' => time(),
            'datePublication' => time(),
            'parentId' => 0
        );
    }




    public function isAvailableParent($id, $parentId)
    {
        if ($id == $parentId) return false;
        $childs = $this->dbh->table("SELECT id FROM articles WHERE parentId=?", array($id));
        if ($childs) foreach ($childs as $child) {
            if (!$this->isAvailableParent($child['id'], $parentId)) return false;
        }
        return true;
    }

    


    public function createArticle($parentId, $langId=0, $translationGroupId=0)
    {
        $article = $this->articleTemplate;
        $article['parentId'] = $parentId;
        $article['langId'] = $langId ? core()->lang['id'] : $langId;
        $article['translationGroupId'] = $translationGroupId;
        return $article;
    }




    public function getArticleById($id)
    {
        $id = intval($id);
        return $this->dbh->row("SELECT * FROM articles WHERE id=$id");
    }




    public function readChildArticles($parentId, $order='', $offset=0, $limit=0)
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




    protected function adjustDatePublicationOfChilds($parentId, $time)
    {
        $childs = $this->dbh->query("SELECT * FROM articles WHERE parentId=$parentId");
        foreach ($childs as $child) {
            if ($child['datePublication'] < $time) {
                $this->dbh->exec("UPDATE articles SET datePublication=$time WHERE id=$child[id]");
                $this->adjustDatePublicationOfChilds($child['id'], $time);
            }
        }
    }




    public function saveArticle($article)
    {
        $keys = array_keys($this->articleTemplate);
        array_splice($keys, array_search('id', $keys), 1);

        if ($article['id'] == 0) {
            $fields = '('.implode(',', $keys).')';
            $placeholders = '(:'.implode(',:', $keys).')';
            $sql = "INSERT INTO articles $fields VALUES $placeholders";
        } else {
            $article['dateEdition'] = time();
            $set = array();
            foreach ($keys as $key) $set[] = $key.'=:'.$key;
            $set = implode(',', $set);
            $sql = "UPDATE news SET $set WHERE id=:id";
            $this->adjustDatePublicationOfChilds($article['id'], $article['datePublication']);
        }

        if ($this->dbh->exec($sql, $article)) {
            return $article['id'] != 0 ? $article['id'] : $this->dbh->lastInsertId();
        } else {
            return 0;
        }
    }




    public function deleteArticle($id)
    {
        $id = intval($id);
        $photo = $this->dbh->value("SELECT photo FROM articles WHERE id=$id");
        if (!empty($photo) && $this->dbh->value("SELECT COUNT(id) FROM articles WHERE photo='$photo'") == 1) {
            @unlink(ROOT_DIR.DS.'uploads'.DS.'articles'.DS.$photo);
            @unlink(ROOT_DIR.DS.'uploads'.DS.'articles'.DS.'mini-'.$photo);
        }
        $this->dbh->exec("DELETE FROM articles WHERE id=$id");
        $deleted = 1;
        $childs = $this->dbh->table("SELECT id FROM articles WHERE parentId=$id");
        if ($childs) foreach ($childs as $child) $deleted += $this->delete($child['id']);
        return $deleted;
    }
}