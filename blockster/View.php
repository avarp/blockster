<?php
namespace blockster;

class View
{
    protected $tpldir;
    protected $tplfile;
    protected $imposedTemplate;
    protected $output = array(
        'isBlockOutput' => true,
        'html' => '',
        'css' => array('linked' => array(), 'embed' => array()),
        'js' => array('linked' => array(), 'embed' => array()),
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

    public function linkCss($file)
    {
        if (!empty($file)) if (is_array($file)) {
            $this->output['css']['linked'] = array_unique(array_merge($this->output['css']['linked'], $file));
        } else {
            if (!in_array($file, $this->output['css']['linked'])) $this->output['css']['linked'][] = $file;
        }
    }

    public function linkJs($file)
    {
        if (!empty($file)) if (is_array($file)) {
            $this->output['js']['linked'] = array_unique(array_merge($this->output['js']['linked'], $file));
        } else {
            if (!in_array($file, $this->output['js']['linked'])) $this->output['js']['linked'][] = $file;
        }
    }

    public function embedCss($styles)
    {
        if (!empty($styles)) if (is_array($styles)) {
            $this->output['css']['embed'] = array_unique(array_merge($this->output['css']['embed'], $styles));
        } else {
            if (!in_array($styles, $this->output['css']['embed'])) $this->output['css']['embed'][] = $styles;
        }
    }

    public function embedJs($script)
    {
        if (!empty($script)) if (is_array($script)) {
            $this->output['js']['embed'] = array_unique(array_merge($this->output['js']['embed'], $script));
        } else {
            if (!in_array($script, $this->output['js']['embed'])) $this->output['js']['embed'][] = $script;
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