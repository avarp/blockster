<?php
namespace core\services;
use \MoReader\Reader;

class TranslatorException extends \Exception{}

class Translator {

    protected $cache=array();

    protected function readMoFile($moFile) {
        if (!file_exists($moFile)) return array();
        if (isset($this->cache[$moFile])) return $this->cache[$moFile];

        $reader = new Reader;
        $moArray = $reader->load($moFile);

        $header = $moArray[''];
        $start = strpos($header, 'plural=');
        if ($start === false) {
            throw new TranslatorException('Could not find an expression for describing plural forms in file '.$moFile.'<pre>$header='.$header.'</pre>');
        }
        $start += 7;
        $end = strpos($header, ';', $start);
        if ($end === false) {
            throw new TranslatorException('It seems to be an error in description of plural forms in file '.$moFile);
        }
        $expr = substr($header, $start, $end-$start);
        if (preg_match('/[A-ZA-mo-z]/', $expr)) {
            throw new TranslatorException('Plural forms expression contains unexpected characters '.$moFile);
        }
        $expr = '$p='.str_replace('n', '$n', $expr);
        $moArray['pluralExpr'] = $expr;
        unset($moArray['']);
        return $this->cache[$moFile] = $moArray;
    }

    public function translateSingular($moFile, $msg) {
        $moArray = $this->readMoFile($moFile);
        return isset($moArray[$msg]) ? $moArray[$msg] : $msg;
    }

    public function translatePlural($moFile, $msgSingular, $msgPlural, $n) {
        $moArray = $this->readMoFile($moFile);
        if (empty($moArray) || !isset($moArray[$msgSingular])) return $n > 1 ? $msgPlural : $msgSingular;

        $p = 0;
        eval($moArray['pluralExpr']);
        if (!isset($moArray[$msgSingular][$p])) {
            throw new TranslatorException('Plural form #'.$p.' is not defined for phrase "'.$msgSingular.'" in file '.$moFile);
        }
        return $moArray[$msgSingular][$p];
    }

    public function translateSingularInContext($moFile, $msg, $context) {
        $moArray = $this->readMoFile($moFile);
        $key = $context."\4".$msg;
        return isset($moArray[$key]) ? $moArray[$key] : $msg;
    }

    public function getTranslatorForJs($moFile) {
        $moArray = $this->readMoFile($moFile);
        core()->broadcastMessage('addJsFile', pathToUrl(__DIR__.DS.'Translator.js'));
        return 'TranslatorInJs.bind('.json_encode($moArray).')';
    }
}