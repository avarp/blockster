<?php
namespace blocks\page;

class View extends \blocks\View
{
	public $data = array(
		'title' => 'Blockster page',
		'keywords' => '',
		'description' => '',
		'cssText' => '',
		'jsText' => '',
		'metaTags' => array(),
		'cssFiles' => array(),
		'jsFiles' => array()
	);

	protected function head()
	{
		$this->delayFragment(function() {
			echo "\t".'<title>'.$this->data['title'].'</title>';
			if (!empty($this->data['description'])) {
				echo "\n\t".'<meta name="description" content="'.$this->data['description'].'">';
			}
			if (!empty($this->data['keywords'])) {
				echo "\n\t".'<meta name="keywords" content="'.$this->data['keywords'].'">';
			}
			foreach ($this->data['metaTags'] as $tag) {
				echo "\n\t".$tag;
			}
			if (!empty($this->data['cssText'])) {
				echo "\n\t".'<style>'."\n".$this->data['cssText']."\t".'</style>';
			}
			foreach ($this->data['cssFiles'] as $url) {
				echo "\n\t".'<link rel="stylesheet" href="'.$url.'">';
			}
			echo "\n";
		});
	}

	protected function scripts()
	{
		foreach ($this->data['jsFiles'] as $url) {
			echo "\n\t".'<script src="'.$url.'"></script>';
		}
		if (!empty($this->data['jsText'])) {
			echo "\n\t".'<script>'."\n".$this->data['jsText']."\t".'</script>';
		}
		echo "\n";
	} 
}