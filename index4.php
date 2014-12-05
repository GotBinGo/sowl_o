<?php
require_once('libs/Smarty.class.php');
$smarty = new Smarty;

ob_start();
$_GET['type'] = "track";
include 'search.php';
/*
$_GET['id'] = "1";
include'playlist.php';*/
$result = ob_get_clean();

session_start();
if(isset($_SESSION['views']))
{
	$session = $_SESSION['views'];
	$smarty->assign('username', $session[1]);
}
else
{
	$smarty->assign('username', "");
}

$smarty->assign('tracks', $result);
$smarty->display('tpl/index4.html');

?>
