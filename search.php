<?php	
$term = mysql_escape_string($_GET['term']);
include("conn.php");
$result = mysqli_query($conn,"SELECT * FROM tracks WHERE (author_name LIKE '%$term%' OR track_name LIKE '%$term%') AND user_id=0 AND (file_type='audio/mpeg' OR file_type='audio/mp3')");
mysqli_close($conn);
$count = 0;
while($row = mysqli_fetch_array($result))
{
	echo "<div class='List_Item' id='" . $count . "' sid='" . $row['file_name'] . "' onclick='Play_This(id)'>";
	echo $row['author_name'] . " - " . $row['track_name'] . "</div>";
	$count++;	

}
?>	