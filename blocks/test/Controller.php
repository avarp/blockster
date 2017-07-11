<?php
namespace blocks\test;

class Controller extends \blocks\Controller
{
	public function action_default($params)
	{
		$this->view->data = $params;
		$this->page->addCssFile(SITE_URL.'/blocks/test/templates/style.css');
		$this->page->addJsFile(SITE_URL.'/blocks/test/templates/script.js');
		$this->page->addCssText('body {background-color:#f0f0f0;}');
		$this->page->addJsText('document.getElementsByTagName("h1")[0].style.color = "#800"');
		$this->page->setTitle('Тест!');
		$this->page->setDescription('Description');
		$this->page->setKeywords('Keywords');
		$this->page->addMetaTag('<meta name="http-equiv" content="Content-type: text/html; charset=UTF-8">');
		return $this->view->render();
	}
}