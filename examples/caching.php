<?php
require '..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'class.templify.php';

// Configuration
$t = new Templify();
$t->setCaching(true);
$t->setCacheLifetime(60*60);

// Rendering this page for the first time will take quite some time.
// However, rendering this page for the second, third and quadrillion time within the next hour will be really fast
if(!$t->isCached('print_array.php')){
	// the code inside these curly braces is only executed if there is no cached version
	$data = array('hello', 'everybody', '<b>out</b>', 'there', 'have', 'fun', 0, pi(), 0x12);
	$t->assign('data', $data);
	usleep(1500000); // this simulates complex calculations, database queries etc
}

$t->display('print_array.php');