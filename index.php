<?php
require_once('smarty.php');
require_once('db/types.php');

session_start();
if(isset($_SESSION['views']))
{
	$user = $_SESSION['views']->get();
	$smarty->assign('username', $user->name);
}
else
{
	$smarty->assign('username', "");
}

$smarty->display('tpl/index.html');

?>
