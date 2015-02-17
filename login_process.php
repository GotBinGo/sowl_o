<?php
require_once('smarty.php');
require_once("db.php");
if(($res = $db->users->authenticate($_POST["username"], $_POST["password"])) !== FALSE)
{
	session_start();
	$_SESSION['views'] = $res->id;
	$result = $db->users->login($res);
	header("Refresh: 0; url=./");
	echo("logged in");
}
else
	echo("login error");
?>
