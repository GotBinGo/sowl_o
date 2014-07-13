<?php
include('libs/Smarty.class.php');

// create object
$smarty = new Smarty;

// assign some content. This would typically come from
// a database or other source, but we'll use static
// values for the purpose of this example.

ob_start(); // begin collecting output

include 'search.php';

$result = ob_get_clean();
$smarty->assign('tracks', $result);

// display it
$smarty->display('tpl/index.html');