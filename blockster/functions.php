<?php
function block($blockName, $params=array(), $imposedTemplate='')
{
    return \blockster\Core::getInstance()->loadBlock($blockName, $params, $imposedTemplate);
}

function position($posName)
{
    return \blockster\Core::getInstance()->fillPosition($posName);
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
    if (!checkAccessLevel($minLevel, $maxLevel)) \blockster\Core::getInstance()->error403();
}

function toUrl($str)
{
    return $str;
}
