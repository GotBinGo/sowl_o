<?php	
$id = mysql_escape_string($_GET['id']);
include("conn.php");
$result = mysqli_query($conn,"SELECT * FROM playlists WHERE id = '$id'");

if(mysqli_num_rows($result) == 1)
{
	$row = mysqli_fetch_array($result);		
	if($avatar == "")
	{
		$avatar = "upload/img/default_list.png";
	}
	else
	{
		$avatar = "upload/img/list/" . $avatar;
	}
	echo "<img src='$avatar' height='42' width='42'>"; 
	echo $row['name'];	
	$user_id = $row['user_id'];	
	$result = mysqli_query($conn,"SELECT * FROM playlist_track AS pt, tracks AS t WHERE t.id = pt.track_id AND pt.playlist_id = '$id'");
	if(mysqli_num_rows($result) > 0)
	{	
		
		while($row = mysqli_fetch_array($result))
		{
			echo "</br>" . $row['author_name'] . " " . $row['track_name'];
		}
	}
	else
	{
		echo "</br>empty";
	}
}
else
{
	echo "no playlist with that id";
	
}
mysqli_close($conn);
?>	