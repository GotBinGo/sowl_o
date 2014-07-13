<?php
require_once('libs/Smarty.class.php');
$smarty = new Smarty;

ob_start();
include 'search.php';
$result = ob_get_clean();

$smarty->assign('tracks', $result);
$smarty->display('tpl/index.html');