<?php
/**
* Definition of global functions used in system
* @author Artem Vorobev <artem.v.mailbox@gmail.com>
*/


/**
 * Returns instance of core.
 * @return object instance of core.
 */
function core()
{
   return \core\System::getInstance(); 
}

/**
 * Returns translation of given message in modules from its .mo file. This function has three forms:
 * with only one parameter - simple translation,
 * with 2 parameters - translation of message in specified context.
 * with 3 parameters - translation of message contains number, creating plural form if $n > 1
 * 
 * @param string $msg1 message to translate of singular form.
 * @param string $msg2 context of message or plural form.
 * @param integer $n number for creating plural form of message.
 * @return string translated message.
 */
function t($msg1, $msg2=null, $n=null) {
    return core()->translate($msg1, $msg2, $n);
}

/**
 * Load module. This is short alias.
 * 
 * @param string $moduleName name of loaded module
 * @param array $params additional parameters for loading
 * @return mixed result of module's work
 */
function module($moduleName, $params=array())
{
    return core()->loadModule($moduleName, $params);
}

/**
 * Load modules in given position. This is short alias.
 * 
 * @param string $posName name of position
 * @return string result of module's work.
 */
function position($posName)
{
    return core()->loadPosition($posName);
}

/**
 * Set HTTP status to 403 Forbidden & rebuild page
 * @return void
 */
function error403()
{
    core()->exitResponse(array('status' => 403));
}

/**
 * Set HTTP status to 404 Not found & rebuild page
 * @return void
 */
function error404()
{
    core()->exitResponse(array('status' => 404));
}

/**
 * Rebuild whole page
 * @return void
 */
function rebuildPage()
{
    core()->exitResponse();
}

/**
 * Check access level
 * 
 * @param integer $minLevel minimum required value of access level
 * @param integer $maxLevel maximum limitation value of access level
 * @return boolean
 */
function checkAccessLevel($minLevel, $maxLevel=0)
{
    return (
        isset($_SESSION['user']) &&
        $_SESSION['user']['accessLevel'] >= $minLevel &&
        ($maxLevel == 0 || $_SESSION['user']['accessLevel'] <= $maxLevel)
    );
}

/**
 * Check access level and set 403 error if checking fails. 
 * 
 * @param integer $minLevel minimum required value of access level
 * @param integer $maxLevel maximum limitation value of access level
 * @return void
 */
function restrictAccessLevel($minLevel, $maxLevel=0)
{
    if (!checkAccessLevel($minLevel, $maxLevel)) error403();
}

/**
 * Transcript string into latin string. 
 * 
 * @param string $str input string
 * @return string transcrition
 */
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

/**
 * Transcript & leave only URL-compatible symbols (all another symbols will be replaced by "-"). 
 * 
 * @param string $str input string
 * @return string url
 */
function toUrl($str)
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(transcript($str))), '-');
}

/**
 * Convert absolute system path to static resource file into absolute URL
 * 
 * @param string $path absolute path to file
 * @return string url
 */
function pathToUrl($path)
{
    $path = DS != '/' ? str_replace(DS, '/', $path) : $path;
    return str_replace(ROOT_DIR, SITE_URL, $path);
}


if (!function_exists('intdiv')) {
    /**
     * Integer division. This function defined only in php7.
     * 
     * @param $a int - divident.
     * @param $b int - divisor.
     * @return int - integer quotient of the division of dividend by divisor.
     */
    function intdiv($a, $b){
        return ($a - $a % $b) / $b;
    }
}

/**
 * Encode string or integer value into Base58-encoded string. It is similar to
 * Base64 but has been modified to avoid both non-alphanumeric characters and
 * letters which might look ambiguous when printed.
 * 
 * @param mixed $var integer or string which you want to encode.
 * @param integer $pad pad result by n symbols of Base58-encoded zeros.
 * @return string encoded data.
 */
function base58_encode($var, $pad=0)
{
    $symbols = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $baseIn = 256;
    $baseOut = strlen($symbols);

    if (is_integer($var)) {
        $str = '';
        do {
            $str .= chr($var % $baseIn);
            $var = intdiv($var, $baseIn);
        } while (!empty($var));
        $var = $str;
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

    if (strlen($result) < $pad) {
        $result = str_repeat($symbols{0}, $pad - strlen($result)).$result;
    }
    return $result;
}

/**
 * Decode Base58-encoded string.
 * 
 * @param string $str Base58-encoded string.
 * @param boolean $returnInteger set to true if you want to get result as integer value instead of string.
 * @return mixed decoded data.
 */
function base58_decode($str, $returnInteger=false)
{
    $symbols = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $baseIn = strlen($symbols);
    $baseOut = 256;
    $str = ltrim($str, $symbols{0});

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

/**
 * Resize raster image and save. Available formats: JPEG, PNG, GIF.
 * 
 * @param string $src path to source image file
 * @param string $dest path to save resized image
 * @param integer $w width, if 0 - calculated automatically from source image's aspect ratio
 * @param integer $h height, if 0 - calculated automatically from source image's aspect ratio
 * @return boolean is operation done successfully.
 */
function resizeImage($src, $dest, $w=0, $h=0) {
    $i = getimagesize($src);
    $src_w = $i[0];
    $src_h = $i[1];
    if (($w > 0 && $src_w < $w) || ($h > 0 && $src_h < $h)) return copy($src, $dest);
    $save_alpha = strtolower(pathinfo($dest, PATHINFO_EXTENSION)) == 'png';

    switch ($i['mime']) {
        case 'image/jpeg':
        $src_img = imagecreatefromjpeg($src);
        break;

        case 'image/gif':
        $src_img = imagecreatefromgif($src);
        break;

        case 'image/png':
        $src_img = imagecreatefrompng($src);
        break;

        default:
        $src_img = null;
    }

    if (!is_null($src_img)) {
        if ($h == 0 || !is_numeric($h)) {
            $dest_w = $w;
            $dest_h = round($w*$src_h/$src_w);
        } else {
            // Resize in two steps (through $pre_img)
            // 1 Crop part with needed aspect ratio and maximum size from center of source (part placed to $pre_img)
            // 2 Replace $src_img by $pre_img;
            $dest_w = $w;
            $dest_h = $h;
            $src_ar = $src_w/$src_h; //source image aspect ratio
            $dest_ar = $dest_w/$dest_h; //destination image aspect ratio
            // 1
            if ($src_ar > $dest_ar) {
                $pre_h = $src_h;
                $pre_w = round($dest_ar*$pre_h);
                $src_x = round(($src_w-$pre_w)/2);
                $src_y = 0;
            } else {
                $pre_w = $src_w;
                $pre_h = round($pre_w/$dest_ar);
                $src_x = 0;
                $src_y = round(($src_h-$pre_h)/2);
            }
            $pre_img = imagecreatetruecolor($pre_w, $pre_h);
            imagealphablending($pre_img, true);
            $bg = imagecolorallocatealpha($pre_img, 0, 0, 0, 127); 
            imagefill($pre_img, 0, 0, $bg); 
            imagecopy($pre_img, $src_img, 0, 0, $src_x, $src_y, $pre_w, $pre_h);
            // 2
            imagedestroy($src_img);
            $src_img = $pre_img;
            $src_w = $pre_w;
            $src_h = $pre_h;
        }

        $dest_img = imagecreatetruecolor($dest_w, $dest_h);
        if ($save_alpha) {
            imagealphablending($dest_img, true);
            $bg = imagecolorallocatealpha($dest_img, 0, 0, 0, 127); 
            imagefill($dest_img, 0, 0, $bg); 
        } else {
            $bg = imagecolorallocate($dest_img, 0, 0, 0);
            imagefill($dest_img, 0, 0, $bg);
        }
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
        imagedestroy($src_img);

        $ext = strtolower(pathinfo($dest, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'jpeg':
            case 'jpg':
            imagejpeg($dest_img, $dest, 85);
            $ret = true;
            break;

            case 'png':
            imagealphablending($dest_img, false);
            imagesavealpha($dest_img, true);
            imagepng($dest_img, $dest);
            $ret = true;
            break;

            case 'gif':
            imagegif($dest_img, $dest);
            $ret = true;
            break;

            default:
            $ret = false;
        }
        return $ret;
    } else {
        return false;
    }
}