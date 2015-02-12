<?php
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$session = $_SESSION['views'];
	$user_id = $session[0];
	$track_id = mysqli_real_escape_string($conn, $_GET['track_id']);
	$list_id = mysqli_real_escape_string($conn, $_GET['list_id']);	
	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE id='$list_id' AND user_id='$user_id'");	
	if(mysqli_num_rows($result) == 1)
	{
		$result = mysqli_query($conn,"SELECT * FROM tracks WHERE id='$track_id'");	
		if(mysqli_num_rows($result) == 1)
		{	
			if (!mysqli_query($conn,"INSERT INTO playlist_track (playlist_id, track_id) VALUES ('$list_id', '$track_id')"))
			{
				die('Error: ' . mysqli_error($conn));
			}
			echo "added";


		}	
		else
		{
			echo "track does not exist";
		}
	}
	else
	{
		echo "playlist not yours";
	}
}
else
{
	echo "not logged in";
}


?>
