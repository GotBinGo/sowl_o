<?php
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $db->users->byID($_SESSION['views']);
	$list_id = mysqli_real_escape_string($conn, $_GET['id']);	
	$public = mysqli_real_escape_string($conn, $_GET['public']);	
	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE id='$list_id' AND user_id='$user->id'");
	if(mysqli_num_rows($result) == 1)
	{	
		$val = 0;
		if($public == "true" || $public == "1")
			$val = 1;

		if (!mysqli_query($conn,"UPDATE playlists SET public = '$val' WHERE id='$list_id'"))
		{
			die('Error: ' . mysqli_error($conn));
		}
		else
		{
			echo "updated $val";
		}		
	}
	else
		echo "playlist not yours";

}
else
{
	echo "not logged in";
}


?>
