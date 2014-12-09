<?php
require_once('smarty.php');

session_start();
if(isset($_SESSION['views']))
{
	echo "already logged in";
}
else
{
	$smarty->display('tpl/login.html');
}
?>
