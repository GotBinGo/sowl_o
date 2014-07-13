<?php	
$name = mysql_escape_string($_GET['name']);
include("conn.php");
$result = mysqli_query($conn,"SELECT * FROM users WHERE name = '$name'");
if(mysqli_num_rows($result) == 1)
{
	$row = mysqli_fetch_array($result);
	$avatar = $row['avatar'];
	if($avatar == "")
	{
		$avatar = "upload/img/default_user.png";
	}
	else
	{
		$avatar = "upload/img/user/" . $avatar;
	}
	echo "<img src='$avatar' height='42' width='42'>"; 
	$ki = $row['name'];
	echo "$ki</br>";	
	$id = $row['id'];
	
	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE user_id='$id'");
	while($row = mysqli_fetch_array($result))	
	{
		$ki = $row['id'] ." ".$row['name'];
		echo "$ki</br>";	
	}
}
else
{
	echo "no user with that name";
}
mysqli_close($conn);
?>	