<?php
require_once('libs/Smarty.class.php');
$smarty = new Smarty;

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