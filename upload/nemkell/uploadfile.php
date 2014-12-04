<?php
include('../conn.php');
session_start();
if(isset($_SESSION['views']))
{
	$session = $_SESSION['views'];
	$filename = md5(microtime().rand()) . ".mp3";
	$author = mysqli_real_escape_string($conn, $_POST['author']);
	$title = mysqli_real_escape_string($conn, $_POST['title']);
	$author = htmlspecialchars($author, ENT_QUOTES, 'UTF-8');
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
	if(strlen($author) > 1 && strlen($title) > 1 )
	{
		$n =  $session[0];
		$sql="INSERT INTO tracks (file_name, author_name, track_name, user_id) VALUES ('$filename', '$author', '$title', '$n')";
		if (!mysqli_query($conn,$sql)) {
			die('Error: ' . mysqli_error($conn));
		}
		$sql="SELECT id FROM tracks WHERE file_name = '$filename'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_array($result);
		
		/*
		echo "1 record added";		*/
		
		
		$result = mysqli_query($conn,"SELECT file_name FROM tracks WHERE id=".$row['id']);
		$name = mysqli_fetch_array($result)['file_name'];
		$type = $_FILES["file"]["type"];
		$sql2="UPDATE tracks SET file_type='".$type."' WHERE id=" . $row['id'];
		
		
		if (!mysqli_query($conn,$sql2)) {
			die('Error: ' . mysqli_error($conn));
		}
		
		mysqli_close($conn);
		
		
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
		}
		else
		{
			echo "Upload: " . $_FILES["file"]["name"] . "<br>";
			echo "Type: " . $_FILES["file"]["type"] . "<br>";
			echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
			echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

			move_uploaded_file($_FILES["file"]["tmp_name"],
			"uploads/" . $name);
			echo "Stored in: " . "uploads/";
			echo $name;		
		}
		
		
		echo "ment";
	}
	else
		echo "too short";
	mysqli_close($conn);
}
else
{
echo "not logged in";
}


?>
