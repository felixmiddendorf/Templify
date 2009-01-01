<?php
require '..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'class.templify.php';

//Configuration
$t = new Templify();

$t->assign('data', array('hey','ho','hahiho'));

$t->parse('print_array_short_tags.php');
?>