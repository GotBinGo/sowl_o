<?php
include('../pass.php');
$con=mysqli_connect("127.0.0.1","bcophm_music",$pass,"bcophm_music");
if (mysqli_connect_errno()) 
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{

	$filename = md5(microtime().rand()) . ".mp3";
	$author = mysqli_real_escape_string($con, $_POST['author']);
	$title = mysqli_real_escape_string($con, $_POST['title']);
	if(strlen($author) > 1 && strlen($title) > 1 )
	{
		$n = 0;
		$sql="INSERT INTO tracks (file_name, author_name, track_name, adder_id) VALUES ('$filename', '$author', '$title', '$n')";
		if (!mysqli_query($con,$sql)) {
			die('Error: ' . mysqli_error($con));
		}
		echo "1 record added";
	}
	else
	echo "too short";
	mysqli_close($con);
}
?>
