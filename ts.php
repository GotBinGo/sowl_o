<?php
include("conn.php");
$result = mysqli_query($conn,"SELECT *, levenshtein_ratio(track_name, 'mount') as lr FROM tracks ORDER BY lr");
while($row = mysqli_fetch_array($result))
{
	echo $row['lr']." ".$row['track_name']."<br>";
}

echo "end";

?>
