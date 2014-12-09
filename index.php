<?php
require_once('smarty.php');

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
