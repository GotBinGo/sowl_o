<?php	
$id = mysql_escape_string($_GET['id']);
require_once("db.php");
require_once('smarty.php');

session_start();
if(isset($_SESSION['views']))
{
	$session = $_SESSION['views'];
	$n = $session[0];

	$playlists = array();
	$user_id = $session[0];
	$result = mysqli_query($conn,"SELECT id, name FROM playlists WHERE user_id='$user_id'");
	while($row = mysqli_fetch_array($result))
	{
		array_push($playlists, array($row['id'], $row['name']));
	}

	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE id = '$id' AND  (public = '1' OR user_id = '$n')");
}
else
{
	$playlists = array();
	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE id = '$id' AND public = '1'");
}

if(mysqli_num_rows($result) == 1 || ($id == 0 && isset($_SESSION['views'])))
{

	$row = mysqli_fetch_array($result);		
	$avatar =  $row['avatar'];
	if($avatar == "")
	{
		$avatar = "upload/img/default_list.png";
	}
	else
	{
		//$avatar = "";
		$avatar = "upload/img/list/" . $avatar;
	}
	echo "<div class=\"list_header\">";
	echo "<img src='$avatar' height='42' width='42'>"; 
	$user_id = $row['user_id'];	
	if($id == 0)
	{
		echo "Uploads";	
		$result = mysqli_query($conn,"SELECT * FROM tracks WHERE user_id = '$n' ORDER BY id");			
	}
	else
	{
		echo $row['name']."<a class='download_btn' onclick='event.stopPropagation()' href='upload/down_list.php?id=$id' download></a>";			
		$result = mysqli_query($conn,"SELECT t.id AS id, t.file_name AS file_name , t.author_name AS author_name, t.track_name AS track_name FROM playlist_track AS pt, tracks AS t WHERE t.id = pt.track_id AND pt.playlist_id = '$id' ORDER BY t.id");
	}
	echo "</div>";

	$count = 0;	
	if(mysqli_num_rows($result) > 0)
	{			
		while($row = mysqli_fetch_array($result))
		{
			//echo "</br>" . $row['author_name'] . " " . $row['track_name'];
			$smarty->assign('type', "track");
			$smarty->assign('count', $count);	
			$smarty->assign('track_id', $row['id']);	
			$smarty->assign('file_name', $row['file_name']);	
			$smarty->assign('author_name', $row['author_name']);
			$smarty->assign('track_name', $row['track_name']);
			$smarty->assign('playlists', $playlists);			

			$smarty->display('tpl/playlist_div.html');
			$count++;	
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
