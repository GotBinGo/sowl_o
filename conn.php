<?php
require_once("config.php");
$conn=mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_query($conn, "set names 'utf8'");
if (mysqli_connect_errno()) 
{
	echo("Failed to connect to MySQL: " . mysqli_connect_error());
	return;
}
?>
