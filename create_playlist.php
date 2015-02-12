<?php
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $_SESSION['views'];
	$name = mysqli_real_escape_string($conn, $_GET['name']);	
	$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
	if($name != "")
	{
		if (!mysqli_query($conn,"INSERT INTO playlists (user_id, name) VALUES ('$user->id', '$name')"))
		{
			die('Error: ' . mysqli_error($conn));
		}
		else
		{
			echo "added";
		}		
	}
	else
		echo "no name";		
}
else
{


	echo "not logged in";
}


?>
