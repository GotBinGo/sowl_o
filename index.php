<?php
require_once('libs/Smarty.class.php');
$smarty = new Smarty;

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

$smarty->display('tpl/index.html');

?>
