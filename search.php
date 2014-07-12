<?php	
$term = mysql_escape_string($_GET['term']);
include 'pass.php';
$con=mysqli_connect("127.0.0.1","bcophm_music",$pass,"bcophm_music");
if (mysqli_connect_errno()) 
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{
	$result = mysqli_query($con,"SELECT * FROM tracks WHERE (author_name LIKE '%$term%' OR track_name LIKE '%$term%') AND adder_id=0 AND (file_type='audio/mpeg' OR file_type='audio/mp3')");
	mysqli_close($con);
	$count = 0;
	while($row = mysqli_fetch_array($result)) {
		echo "<div class='List_Item' id='" . $count . "' sid='" . $row['file_name'] . "' onclick='Play_This(id)'>";
		echo $row['author_name'] . " - " . $row['track_name'] . "</div>";
		$count++;	

	}
}
?>	