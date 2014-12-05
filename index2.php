<?php
require_once('libs/Smarty.class.php');
$smarty = new Smarty;
//echo $_SERVER[REQUEST_URI];
/*$site = substr($_SERVER[REQUEST_URI],22);
if($site == "")
{
	$site = "search.php?type=track&term=";
}*/
//echo $site;
/*ob_start();
$_GET['type'] = "track";
include 'search.php';
$result = ob_get_clean();*/

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

//$smarty->assign('tracks', $result);
//$smarty->assign('toload',$site);
$smarty->display('tpl/index2_.html');

?>
