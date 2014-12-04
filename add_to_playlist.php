<?php
include('conn.php');
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
	//echo $row['id'];
	
	//echo $track_id . " " . $list_id;

	/*
	if(strlen($author) > 1 && strlen($title) > 1 )
	{
		$n =  $session[0];
		$sql="INSERT INTO tracks (file_name, author_name, track_name, user_id) VALUES ('$filename', '$author', '$title', '$n')";
		if (!mysqli_query($conn,$sql)) {
			die('Error: ' . mysqli_error($conn));
		}
		echo "1 record added";
	}
	else
		echo "too short";
	mysqli_close($conn);*/
}
else
{
	echo "not logged in";
}


?>
