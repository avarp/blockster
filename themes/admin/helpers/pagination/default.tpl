<?php
if (!isset($ulClass)) $ulClass = 'pagination';
if (!isset($thisPageClass)) $thisPageClass = 'active';
if ($numPages > 1) {
    echo '<ul class="'.$ulClass.'">';
    //left arrow
    if ($thisPage > 1) {
        echo '<li><a href="'.sprintf($url, ($thisPage-1)).'"><i class="fa fa-arrow-left"></i></a></li>';
    } else {
        echo '<li><span><i class="fa fa-arrow-left"></i></span></li>';
    }
    //if there are a lot of pages
    if ($numPages > 15) {
        //if far from first page
        if ($thisPage > 6) {
            $prefix = '<li><a href="'.sprintf($url, 1).'">1</a></li><li><span>...</span></li>';
            $start = $thisPage - 5;
        } else {
            $prefix = '';
            $start = 1;
        }
        //if far from last page
        if ($thisPage < $numPages - 5) {
            $postfix = '<li><span>...</span></li><li><a href="'.sprintf($url, $numPages).'">'.$numPages.'</a></li>';
        } else {
            $postfix = '';
            $start = $numPages - 10;
        }
        echo $prefix;
        for ($i=$start; $i<$start+11; $i++) if ($i == $thisPage) {
            echo '<li class="'.$thisPageClass.'"><span>'.$i.'</span></li>';
        } else {
            echo '<li><a href="'.sprintf($url, $i).'">'.$i.'</a></li>';
        }
        echo $postfix;
    //if number of pages is less than 15
    } else {
        for ($i=1; $i<=$numPages; $i++) if ($i == $thisPage) {
            echo '<li class="'.$thisPageClass.'"><span>'.$i.'</span></li>';
        } else {
            echo '<li><a href="'.sprintf($url, $i).'">'.$i.'</a></li>';
        }
    }
    //right arrow
    if ($thisPage < $numPages) {
        echo '<li><a href="'.sprintf($url, ($thisPage+1)).'"><i class="fa fa-arrow-right"></i></a></li>';
    } else {
        echo '<li><span><i class="fa fa-arrow-right"></i></span></li>';
    }
    echo '</ul>';
}