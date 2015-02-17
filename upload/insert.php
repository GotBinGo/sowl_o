<?php
set_include_path("..:" . get_include_path());
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $db->users->byID($_SESSION['views']);
	$filename = md5(microtime().rand()) . ".mp3";
	$author = mysqli_real_escape_string($conn, $_GET['author']);
	$title = mysqli_real_escape_string($conn, $_GET['title']);
	$author = htmlspecialchars($author, ENT_QUOTES, 'UTF-8');
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
	if(strlen($author) > 0 && strlen($title) > 0)
	{
		$sql="INSERT INTO tracks (file_name, author_name, track_name, user_id) VALUES ('$filename', '$author', '$title', '$user->id')";
		if (!mysqli_query($conn,$sql)) {
			die('Error: ' . mysqli_error($conn));
		}
		$sql="SELECT id FROM tracks WHERE file_name = '$filename'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_array($result);

		echo $filename;
	}
	else
		echo "too short" ."." . $author .".".$title.".";
	mysqli_close($conn);
}
else
{
	echo "not logged in";
}


?>
