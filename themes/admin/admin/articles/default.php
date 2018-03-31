<div class="card">
    <?php require('header.php');?>
    <div class="card-section">
        <?php if ($articles) { ?>
            <div class="grid split-1">
                <?php foreach ($articles as $article) { ?>
                    <div class="cell lg-3 md-4 sm-6">
                        <div class="article-preview">
                            <?php if (!empty($article['photo'])) { ?>
                                <img class="article-image" src="/uploads/articles/mini-<?=$article['photo']?>" alt="<?=$article['header']?>">
                            <?php } else { ?>
                                <div class="article-image">
                                    <i class="fa fa-photo"></i>
                                </div>
                            <?php } ?>
                            <div class="article-overview">
                                <h4 class="m-0"><?=$article['header']?></h4>
                                <?=$article['overview']?>
                            </div>
                            <div class="article-controls">
                                <button
                                    type="button"
                                    class="btn btn-square btn-default mr-0-5"
                                >
                                    <i class="fa fa-copy"></i>
                                </button>
                                <button class="btn btn-square btn-default">
                                    <i class="fa fa-cut"></i>
                                </button>
                                <div style="flex-grow:1"></div>
                                <a
                                    class="btn btn-square btn-primary mr-0-5"
                                    href="<?=core()->getUrl('route:admin>articles/'.$article['id'])?>"
                                >
                                    <i class="fa fa-folder-open"></i>
                                </a>
                                <a
                                    class="btn btn-square btn-success mr-0-5"
                                    href="<?=core()->getUrl('route:admin>articles/edit/'.$article['id'])?>"
                                >
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <button
                                    type="submit"
                                    class="btn btn-square btn-danger"
                                    name="deleteArticle"
                                    onclick="return confirm('<?=t('Delete article and all sub-articles?')?>')"
                                    value="<?=$article['id']?>"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <?=t('No articles.')?>
        <?php } ?> 
    </div>
</div>

<style>
    .article-preview {
        border-radius: 2px;
        overflow: hidden;
        border: 1px solid #ddd;
    }
    img.article-image {
        width: 100%;
        border-bottom: 1px solid #ddd;
    }
    div.article-image {
        font-size: 8em;
        color: #f0f0f0;
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    .article-overview {
        padding: 1em;
        padding-top: 0.5em;
        border-bottom: 1px solid #ddd;
    }
    .article-controls {
        padding: 0.5em;
        display: flex;
        align-items: center;
        background-color: #f8f8f8;
    }
</style>

<?php
if ($articlesDeleted == 0) {
    $this->addJsText("pushNotification('error', '".t('Error: can\'t delete article.')."')");
} elseif ($articlesDeleted > 0) {
    $this->addJsText(
        "pushNotification('success', '".sprintf(t('Deleted %d article.', 'Deleted %d articles.', $articlesDeleted))."')"
    );
}