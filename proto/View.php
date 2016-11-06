<?php
namespace proto;

class View
{
    protected $tpldir;
    protected $tplfile;
    protected $imposedTemplate;
    protected $output = array(
        'isBlockOutput' => true,
        'html' => '',
        'css' => array(),
        'js' => array(),
        'meta' => array(),
    );

    public function __construct($tpldir, $imposedTemplate='')
    {
        $this->tpldir = $tpldir;
        $this->tplfile = 'index.tpl';
        $this->imposedTemplate = $imposedTemplate;
    }

    public function setTemplate($tplfile) {
        $this->tplfile = $tplfile;
    }

    public function render($scope=array())
    {
        if (!empty($this->imposedTemplate)) {
            if ($this->imposedTemplate{0} == '/') $template = ROOT_DIR.$this->imposedTemplate;
            else $template = ROOT_DIR.$this->tpldir.'/'.$this->imposedTemplate;
        } else {
            $template = ROOT_DIR.$this->tpldir.'/'.$this->tplfile;
        }
        extract($scope);
        ob_start();
        include($template);
        $this->output['html'] = ob_get_clean();
        return $this->output;
    }

    public function addCss($style)
    {
        if (!empty($style)) if (is_array($style)) {
            $this->output['css'] = array_unique(array_merge($this->output['css'], $style));
        } else {
            if (!in_array($style, $this->output['css'])) $this->output['css'][] = $style;
        }
    }

    public function addJs($script)
    {
        if (!empty($script)) if (is_array($script)) {
            $this->output['js'] = array_unique(array_merge($this->output['js'], $script));
        } else {
            if (!in_array($script, $this->output['js'])) $this->output['js'][] = $script;
        }
    }

    public function addMeta($tag)
    {
        if (!empty($tag)) if (is_array($tag)) {
            $this->output['meta'] = array_unique(array_merge($this->output['meta'], $tag));
        } else {
            if (!in_array($tag, $this->output['meta'])) $this->output['meta'][] = $tag;
        }
    }

    public function setTitle($title)
    {
        $this->output['meta']['title'] = $title;
    }

    public function setDescription($description)
    {
        $this->output['meta']['description'] = $description;
    }

    public function setKeywords($keywords)
    {
        $this->output['meta']['keywords'] .= ', '.$keywords;
    }
}