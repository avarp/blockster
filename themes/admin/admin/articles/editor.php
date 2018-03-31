<div class="card">
    <?php require('header.php');?>
    <div class="card-section">
        <form class="grid split-1" method="POST" enctype="multipart/form-data">
            <div class="cell lg-2 xs-4 r">
                <label><?=t('URL')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <input type="text" name="article[url]" value="<?=$article['url']?>" class="form-control">
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('Header')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <input type="text" name="article[header]" value="<?=$article['header']?>" class="form-control">
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('Main image')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <input type="file" name="photo" class="form-control">
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('Overview')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <textarea type="text" name="article[overview]" class="form-control" cols="15"><?=$article['overview']?></textarea>
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('Content')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <textarea type="text" name="article[content]" class="form-control" cols="35"><?=$article['content']?></textarea>
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('SEO title')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <input type="text" name="article[seoTitle]" value="<?=$article['seoTitle']?>" class="form-control">
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('SEO keywords')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <input type="text" name="article[seoKeywords]" value="<?=$article['seoKeywords']?>" class="form-control">
            </div>
            <div class="cell lg-2 xs-4 r">
                <label><?=t('SEO description')?></label>
            </div>
            <div class="cell lg-10 xs-8">
                <input type="text" name="article[seoDescription]" value="<?=$article['seoDescription']?>" class="form-control">
            </div>
            <div class="cell">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> <?=t('Save article')?>
                </button>
            </div>
        </form>
    </div>
</div>