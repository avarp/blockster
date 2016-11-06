<!DOCTYPE html>
<html lang="ru">
<head>
<?php
    $this->setTitle('404');
    $this->addMeta('<meta charset="UTF-8">');
?>
</head>
<body>
	<h1>404 Not Found</h1>
	<p>Страница <?=$_SERVER['REQUEST_URI']?> не существует на данном сайте.</p>
	<hr>
	<address>CMS: Blockster v0.1 на сайте "<?=SITE_NAME?>"</address>
</body>
</html>