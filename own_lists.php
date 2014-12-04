<?php	
include("conn.php");
session_start();
if(isset($_SESSION['views']))
{
	$session = $_SESSION['views'];
	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE user_id = '$session[0]'");
	while($row = mysqli_fetch_array($result))	
	{
		$ki = $row['id'] ." ".$row['name'];
		echo "$ki</br>";	
	}
}
else
{
	echo "not logged in";
}
mysqli_close($conn);
?>	