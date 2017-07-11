<?php
function block($blockName, $params=array(), $template='')
{
    return \services\core\Blockster::getInstance()->loadBlock($blockName, $params, $template);
}

function preparedBlock($blockName)
{
    return \services\core\Blockster::getInstance()->loadPreparedBlock($blockName);
}

function position($posName)
{
    return \services\core\Blockster::getInstance()->loadPosition($posName);
}

function error403()
{
    \services\core\Blockster::getInstance()->resetOutput();
    exit(block('page', array('route' => 'error403')));
}

function error404()
{
    \services\core\Blockster::getInstance()->resetOutput();
    exit(block('page', array('route' => 'error404')));
}

function rebuildPage()
{
    \services\core\Blockster::getInstance()->resetOutput();
    exit(block('page'));
}

function checkAccessLevel($minLevel, $maxLevel=0)
{
    return (
        isset($_SESSION['user']) &&
        $_SESSION['user']['accessLevel'] >= $minLevel &&
        ($maxLevel == 0 || $_SESSION['user']['accessLevel'] <= $maxLevel)
    );
}

function restrictAccessLevel($minLevel, $maxLevel=0)
{
    if (!checkAccessLevel($minLevel, $maxLevel)) error403();
}

function transcript($str)
{
    $search = array(
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и',
        'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т',
        'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ',
        'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё',
        'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
        'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ',
        'Ь', 'Ы', 'Ъ', 'Э', 'Ю', 'Я'
    );
    $replace = array(
        'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i',
        'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't',
        'u', 'f', 'h', 'ts', 'ch', 'sh', 'shch', '', 'y', '',
        'e', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'E',
        'Zh', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P',
        'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Shch',
        '', 'Y', '', 'E', 'Yu', 'Ya'
    );
    return str_replace($search, $replace, $str);
}

function toUrl($str)
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(transcript($str))), '-');
}

function base58_encode($var, $acceptInteger=false)
{
    $symbols = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $baseIn = 256;
    $baseOut = strlen($symbols);

    if (is_integer($var)) {
        if ($acceptInteger) {
            $str = '';
            do {
                $str .= chr($var % $baseIn);
                $var = intdiv($var, $baseIn);
            } while (!empty($var));
            $var = $str;
        } else {
            $var = (string)$var;
        }
    }

    $result = '';
    do {
        $l = strlen($var);
        $y = ''; //result of division
        $r = 0; //remainder
        for ($i = $l-1; $i >= 0; $i--) {
            $x = ord($var{$i}) + $r*$baseIn;
            $d = intdiv($x, $baseOut);
            $r = $x % $baseOut;
            $y .= chr($d);
        }
        $var = rtrim(strrev($y), chr(0));
        $result .= $symbols{$r};
    } while (!empty($var));
    return $result;
}

function base58_decode($str, $returnInteger=false)
{
    $symbols = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $baseIn = strlen($symbols);
    $baseOut = 256;

    $result = '';
    do {
        $l = strlen($str);
        $y = ''; //result of division
        $r = 0; //remainder
        for ($i = $l-1; $i >= 0; $i--) {
            $x = strpos($symbols, $str{$i}) + $r*$baseIn;
            $d = intdiv($x, $baseOut);
            $r = $x % $baseOut;
            $y .= $symbols{$d};
        }
        $str = rtrim(strrev($y), $symbols{0});
        $result .= chr($r);
    } while (!empty($str));

    if ($returnInteger) {
        $int = 0;
        $l = strlen($result);
        $digit = 1;
        for ($i=0; $i<$l; $i++) {
            $int += $digit*ord($result{$i});
            $digit *= $baseOut;
        }
        $result = $int;
    }

    return $result;
}