<?php
include('../conn.php');
	$result = mysqli_query($conn,"SELECT file_name FROM tracks WHERE id=".$_GET['track_id']);
	$name = mysqli_fetch_array($result)['file_name'];
	$type = $_FILES["file"]["type"];
	$sql2="UPDATE tracks SET file_type='".$type."' WHERE id=" . $_GET['track_id'];
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

?> 