<?php
//include("../pass.php");
$conn=mysqli_connect("127.0.0.1","bcophm_music",'123456',"bcophm_music");
mysqli_query($conn, "set names 'utf8'");
if (mysqli_connect_errno()) 
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	return;
}
?>
