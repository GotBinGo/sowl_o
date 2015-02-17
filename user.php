<?php	
$name = isset($_GET['name']) ? mysql_escape_string($_GET['name']) : "";
require_once("db.php");

require_once('smarty.php');
session_start();
if(isset($_SESSION["views"]))
{
	$userhnd = $db->users->byID($_SESSION['views']);
	$user = $userhnd->get();
	$avatar = $user->avatar;
	echo "<div class=\"list_header\"><img src='$avatar' height='42' width='42'>"; 
	echo "$user->display_name</div>";

	echo "<input type='text' placeholder='Create new playlist' name='cPlaylist' onkeydown='event.stopPropagation(); if(event.keyCode==13){createPlaylist(this.value);}'>";
	//$result2 = mysqli_query($conn,"SELECT * FROM playlists WHERE user_id='$user->id'"); //belépve
	$result2 = $userhnd->getPlaylists();
}
else
{
	$avatar = "upload/img/default_user.png";
	//$result2 = mysqli_query($conn,"SELECT * FROM playlists WHERE user_id='$id' AND public"); //csak a publikus listák
	$result2 = $db->playlists->getPublic();
}

while($row = mysqli_fetch_array($result2))	
{
	$smarty->assign('type', "list");
	$smarty->assign('id', $row['id']);
	$smarty->assign('name2', $row['name']);							
	$avatar = $row['avatar'];
	if($avatar == "")
		$avatar = "upload/img/default_list.png";
	else
		$avatar = "upload/img/list/" . $avatar;			

	$smarty->assign('avatar', $avatar);
	if($name == $user->name)
		$smarty->assign('pub',$row['public'] );
	else
		$smarty->assign('pub',"");
	$smarty->display('tpl/list.html');
		/*
		ob_start();
		$_GET['id'] = $row['id'];
		include 'playlist.php';
		$ki_p = ob_get_clean();
		echo $ki_p;*/
}

if(isset($_SESSION['views']))
{
	$smarty->assign('type', "list");
	$smarty->assign('id',0);
	$smarty->assign('name2', "Uploads");							
	$smarty->assign('pub',"" );
	$user = $db->users->byID($_SESSION["views"])->get();
	$avatar = $user->avatar;
	if($avatar == "")
		$avatar = "upload/img/default_list.png";
	else
		$avatar = "upload/img/list/" . $avatar;			

	$smarty->assign('avatar', $avatar);
	$smarty->display('tpl/list.html');
}
?>	
