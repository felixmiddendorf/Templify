<?php
require '..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'class.templify.php';

// Configuration
$t = new Templify();

$t->assign('name', 'Mister T');

$t->parse('basic.php');
?>