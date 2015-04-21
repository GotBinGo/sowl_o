<?php
require_once("db.php");

if(!isset($_POST) || !isset($_POST["display_name"]) || !isset($_POST["username"]) || !isset($_POST["password"]))
	die("invalid post data");

if($res = $db->users->add($_POST["display_name"], $_POST["username"], $_POST["password"]))
{
	echo("success\n");
	header("Refresh: 0; url=./");
}
else
	echo("FAIL\n");

?>
