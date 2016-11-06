<?php
function block($blockName, $params=array(), $imposedTemplate='')
{
    return \blockster\Core::getInstance()->loadBlock($blockName, $params, $imposedTemplate);
}

function execute($blockName, $params=array())
{
    return \blockster\Core::getInstance()->executeAction($blockName, $params);
}

function position($posName)
{
    return \blockster\Core::getInstance()->fillPosition($posName);
}