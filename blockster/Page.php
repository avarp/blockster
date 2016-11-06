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
            $this->writeMeta($output['meta']).$this->writeCss($output['css']).$this->writeJs($output['js']).'</head>',
            $html
        );
        return $html;
    }


    private function scriptsIsLast($output)
    {
        $html = $output['html'];
        $html = str_replace('</head>', $this->writeMeta($output['meta']).$this->writeCss($output['css']).'</head>', $html);
        $html = str_replace('</body>', $this->writeJs($output['js']).'</body>', $html);
        return $html;
    }


    private function googlePageSpeed($output)
    {
        $html = $output['html'];

        $embedCss = array_filter($output['css'], function($file) {
            return (
                substr($file, 0, 2) != '//' &&
                substr($file, 0, 4) != 'http' &&
                substr($file, strlen($file)-10) == '.embed.css'
            );
        });
        $linkedCss = array_diff($output['css'], $embedCss);

        $html = str_replace('</head>', $this->writeMeta($output['meta']).$this->writeCss($embedCss).'</head>', $html);
        $html = str_replace('</body>', $this->writeJs($output['js'], 'async').'</body>', $html);
        $html = str_replace('</html>', "</html>\n".$this->writeCss($linkedCss), $html);
        return $html;
    }


    private function writeCss($css, $attr='')
    {
        $html = '';
        foreach ($css as $file) {
            if (substr($file, 0, 2) == '//' || substr($file, 0, 4) == 'http') {
                $html .= "\t<link $attr rel=\"stylesheet\" type=\"text/css\" href=\"$file\">\n";
            } elseif (file_exists(ROOT_DIR.$file)) {
                if ($this->config['cssEmbedding'] && substr($file, strlen($file)-10) == '.embed.css') {
                    $html .= "\t<style>\n".file_get_contents(ROOT_DIR.$file)."\n\t</style>\n";
                } else {
                    $html .= "\t<link $attr rel=\"stylesheet\" type=\"text/css\" href=\"".SITE_URL.$file."\">\n";
                }
            } else {
                $html .= "\t<!-- css file not exists: $file -->\n";
            }
        }
        return $html;
    }


    private function writeJs($js, $attr='')
    {
        $html = '';
        foreach ($js as $file) {
            if (substr($file, 0, 2) == '//' || substr($file, 0, 4) == 'http') {
                $html .= "\t<script $attr src=\"$file\"></script>\n";
            } elseif (file_exists(ROOT_DIR.$file)) {
                if ($this->config['jsEmbedding'] && substr($file, strlen($file)-9) == '.embed.js') {
                    $html .= "\t<script>\n".file_get_contents(ROOT_DIR.$file)."\n\t</script>";
                } else {
                    $html .= "\t<script $attr src=\"".SITE_URL.$file."\"></script>\n";
                }
            } else {
                $html .= "\t<!-- js file not exists: $file -->\n";
            }
        }
        return $html;
    }


    private function writeMeta($meta)
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