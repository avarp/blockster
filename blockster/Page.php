<?php
namespace blockster;

class Page extends \proto\View
{

    public function __construct($template)
    {
        $this->tpldir = pathinfo($template, PATHINFO_DIRNAME);
        $this->tplfile = pathinfo($template, PATHINFO_BASENAME);
        $this->config = require(__DIR__.'/pageConfig.php');
    }


    public function render($scope=array())
    {
        $output = parent::render();
        $renderMethod = $this->config['renderMethod'];
        if (method_exists($this, $renderMethod)) return $this->$renderMethod($output);
        else return $this->classicHtml($output);
    }


    private function classicHtml($output)
    {
        $html = $output['html'];
        $html = str_replace(
            '</head>',
            $this->getMeta($output['meta']).
            $this->getEmbedCss($output['css']['embed']).
            $this->getLinkedCss($output['css']['linked']).
            $this->getEmbedJs($output['js']['embed']).
            $this->getLinkedJs($output['js']['linked']).'</head>',
            $html
        );
        return $html;
    }


    private function scriptsIsLast($output)
    {
        $html = $output['html'];
        $html = str_replace(
            '</head>', 
            $this->getMeta($output['meta']).
            $this->getEmbedCss($output['css']['embed']).
            $this->getLinkedCss($output['css']['linked']).'</head>',
            $html
        );
        $html = str_replace('</body>', $this->getEmbedJs($output['js']['embed']).$this->getLinkedJs($output['js']['linked']).'</body>', $html);
        return $html;
    }


    private function googlePageSpeed($output)
    {
        $html = $output['html'];
        $html = str_replace('</head>', $this->getMeta($output['meta']).$this->getEmbedCss($output['css']['embed']).'</head>', $html);
        $html = str_replace('</body>', $this->getEmbedJs($output['js']['embed']).$this->getLinkedJs($output['js']['linked'], 'async').'</body>', $html);
        $html = str_replace('</html>', "</html>\n".$this->getLinkedCss($output['css']['linked']), $html);
        return $html;
    }


    private function getLinkedCss($files)
    {
        $html = '';
        foreach ($files as $file) {
            if (substr($file, 0, 2) == '//' || substr($file, 0, 4) == 'http') {
                $html .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\">\n";
            } elseif (file_exists(ROOT_DIR.$file)) {
                $html .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".SITE_URL.$file."\">\n";
            } else {
                $html .= "\t<!-- css file not exists: $file -->\n";
            }
        }
        return $html;
    }


    private function getLinkedJs($files, $attr='')
    {
        $html = '';
        foreach ($files as $file) {
            if (substr($file, 0, 2) == '//' || substr($file, 0, 4) == 'http') {
                $html .= "\t<script $attr src=\"$file\"></script>\n";
            } elseif (file_exists(ROOT_DIR.$file)) {
                $html .= "\t<script $attr src=\"".SITE_URL.$file."\"></script>\n";
            } else {
                $html .= "\t<!-- js file not exists: $file -->\n";
            }
        }
        return $html;
    }


    private function getEmbedCss($styles)
    {
        if (!empty($styles)) {
            $html = "<style>\n";
            foreach ($styles as $style) $html .= $style."\n";
            $html .= "\n</style>\n";
            return $html;
        } else {
            return '';
        }
    }


    private function getEmbedJs($scripts)
    {
        if (!empty($scripts)) {
            $html = "<script>\n";
            foreach ($scripts as $script) $html .= $script."\n";
            $html .= "\n</script>\n";
            return $html;
        } else {
            return '';
        }
    }


    private function getMeta($meta)
    {
        $html = '';
        foreach ($meta as $key => $tag) {
            if (is_numeric($key)) {
                $html .= "\t$tag\n";
            } else {
                switch ($key) {
                    case 'title':
                    $html .= "\t<title>$tag</title>\n";
                    break;

                    case 'keywords':
                    $html .= "\t<meta name=\"keywords\" content=\"$tag\">\n";
                    break;

                    case 'description':
                    $html .= "\t<meta name=\"description\" content=\"$tag\">\n";
                    break;
                }
            }
        }
        return $html;
    }
}