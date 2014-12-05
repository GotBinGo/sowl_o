<?php
require_once('libs/Smarty.class.php');
$smarty = new Smarty;
$username = mysql_escape_string($_POST['username']);
$password = mysql_escape_string($_POST['password']);
include("conn.php");
$result = mysqli_query($conn,"SELECT id, name, password , salt FROM users WHERE name='$username'");
mysqli_close($conn);
if(mysqli_num_rows($result) == 1)
{
	$row = mysqli_fetch_array($result);
	if(md5($row['salt'].$password) == $row['password'])
	{
		session_start();
		$_SESSION['views']=array($row[0],$row[1]);
		echo "logged in";
		echo "<meta http-equiv='refresh' content='0; url=./'/>";

	}
	else
	{
		echo "login error";
	}
}


?>
