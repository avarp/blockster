<?php
namespace modules\page;

class View extends \modules\View
{
    public $data = array(
        'title' => 'Untitled',
        'keywords' => '',
        'description' => '',
        'css' => array(),
        'js' => array(),
        'metaTags' => array(),
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
            $this->data['metaTags'] = array_unique($this->data['metaTags'], SORT_REGULAR);
            foreach ($this->data['metaTags'] as $tag) {
                echo "\n\t".$tag;
            }
            $this->data['css'] = array_unique($this->data['css'], SORT_REGULAR);
            foreach ($this->data['css'] as $css) {
                if ($css[1] === true) {
                    echo "\n\t".'<link rel="stylesheet" href="'.$css[0].'">';
                } else {
                    echo "\n\t".'<style>'.$css[0].'</style>';
                }
            }
            echo "\n";
        });
    }

    protected function scripts()
    {
        $this->delayFragment(function() {
            $this->data['js'] = array_unique($this->data['js'], SORT_REGULAR);
            foreach ($this->data['js'] as $js) {
                if ($js[1] === true) {
                    echo "\n\t".'<script src="'.$js[0].'"></script>';
                } else {
                    echo "\n\t".'<script>'.$js[0].'</script>';
                }
            }
            echo "\n";
        });
    } 
}