<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->data['title'] = '404';
    $this->data['metaTags'][] = '<meta charset="UTF-8">';
    $this->head();
?>
</head>
<body>
	<h1>404 Not Found</h1>
	<p>Страница <?=$_SERVER['REQUEST_URI']?> не существует на данном сайте.</p>
	<hr>
	<address>CMS: Blockster v<?=CMS_VERSION?> на сайте "<?=SITE_NAME?>"</address>
</body>
</html>