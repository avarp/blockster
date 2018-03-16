<div class="test-module">
    <h3><?php $this->delayFragment(function(){echo $this->data['header'];}) ?></h3>
    Сейчас <?=date('d.m.Y H:i:s')?>
    <?=module('test::nesting_test')?>
</div>
<?php
    $this->setTitle('This is test!');
    $this->setKeywords('test-test-test');
    $this->setDescription('Test of simple module');
    $this->addMetaTag('<meta name="test" content="1234">');
    $this->addCssText('body{background-color:#e0e0e0;}');
    $this->addJsText('console.log("Hi, this is test!")');
?>