<?php
include('conn.php');
session_start();
if(isset($_SESSION['views']))
{
	$session = $_SESSION['views'];
	$user_id = $session[0];
	$name = mysqli_real_escape_string($conn, $_GET['name']);	
	$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
	if($name != "")
	{
		if (!mysqli_query($conn,"INSERT INTO playlists (user_id, name) VALUES ('$user_id', '$name')"))
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
