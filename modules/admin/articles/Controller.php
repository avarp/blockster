<?php
namespace modules\admin\articles;

class Controller extends \modules\Controller
{
    public function getBreadcrumbs($articleId)
    {
        $b = array();

        while ($articleId != 0) {
            $article = $this->model->getArticleById($articleId);
            $b[] = array('label' => $article['header'], 'href' => core()->getUrl('route:admin>articles/'.$article['id']));
            $articleId = $article['parentId'];
        }
            
        $b[] = array('label' => t('Articles'), 'href' => core()->getUrl('route:admin>articles'));
        $b[] = array('label' => t('Dashboard'), 'href' => core()->getUrl('route:admin'));

        return array_reverse($b);
    }

    public function action_default($params)
    {
        $parentId = isset($params[1]) ? $params[1] : 0;
        if (isset($_POST['deleteArticle'])) {
            $articlesDeleted = $this->model->deleteArticle($_POST['deleteArticle']);
        } else {
            $articlesDeleted = -1;
        }
        $articles = $this->model->readChildArticles($parentId);
        $breadcrumbs = $this->getBreadcrumbs($parentId);
        $h1 = t('Articles');

        $this->view->data = compact('parentId', 'articles', 'breadcrumbs', 'h1', 'articlesDeleted');
        return $this->view->render();
    }

    public function action_edit($params)
    {
        if (!isset($params[0])) error404();
        $article = $this->model->getArticleById($params[0]);
        if (!$article) error404();
        $h1 = t('Edit article');
        $breadcrumbs = $this->getBreadcrumbs($article['parentId']);
        $breadcrumbs[] = array(
            'label' => $article['header'],
            'href' => core()->getUrl('route:admin>articles/edit/'.$article['id'])
        );
        $this->view->data = compact('breadcrumbs', 'h1');
        return $this->editArticle($article);
    }

    public function action_add($params)
    {
        if (!isset($params[0])) error404();
        $h1 = t('Add article');
        $article = $this->model->createArticle($params[0]);
        $breadcrumbs = $this->getBreadcrumbs($params[0]);
        $breadcrumbs[] = array(
            'label' => t('New article'),
            'href' => core()->getUrl('route:admin>articles/add/'.$params[0])
        );
        $this->view->data = compact('breadcrumbs', 'h1');
        return $this->editArticle($article);
    }

    protected function editArticle($article)
    {
        if (isset($_POST['article'])) {
            $article = array_merge($article, $_POST['article']);
            $this->model->saveArticle($article);
        }

        $this->view->template = 'editor.php';
        $this->view->data['article'] = $article;
        return $this->view->render();
    }
}