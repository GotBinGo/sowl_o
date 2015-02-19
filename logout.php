<?php
session_start();
if(isset($_SESSION['views']))
{
	unset($_SESSION['views']);
	echo "logged out";
	header("Refresh: 0; url=./");
}
else
{
	echo "not log in";
}
?>
