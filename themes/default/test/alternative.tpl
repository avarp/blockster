<strong>This is nested block</strong>
<?php
$r = rand(0, 255);
$g = rand(0, 255);
$b = rand(0, 255);
$this->addCssText("strong{color:rgba($r, $g, $b, 1);}");