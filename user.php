<?php	
$name = mysql_escape_string($_GET['name']);
include("conn.php");

require_once('libs/Smarty.class.php');
$smarty = new Smarty;
session_start();
if($name == "")
	$name =  $_SESSION['views'][1];
$result = mysqli_query($conn,"SELECT * FROM users WHERE name = '$name'");
if(mysqli_num_rows($result) == 1)
{
	$row = mysqli_fetch_array($result);
	$user_id = $row['id'];
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
	$ki = $row['display_name'];
	echo "$ki</br>";
	$id = $row['id'];

	if(isset($_SESSION['views']) && $_SESSION['views'][0] == $user_id )
	{

		echo "<input type='text' placeholder='Create new playlist' name='cPlaylist' onkeydown='event.stopPropagation(); if(event.keyCode==13){createPlaylist(this.value);}'>";
		$result2 = mysqli_query($conn,"SELECT * FROM playlists WHERE user_id='$id'"); //belépve
	}
	else
	{
		$result2 = mysqli_query($conn,"SELECT * FROM playlists WHERE user_id='$id' AND public"); //csak a publikus listák
	}
	while($row = mysqli_fetch_array($result2))	
	{
		//$ki = $row['id'] ." ".$row['name'];
		//echo "$ki</br>";	

		$smarty->assign('type', "list");
		$smarty->assign('id', $row['id']);
		$smarty->assign('name2', $row['name']);							
		$avatar = $row['avatar'];
		if($avatar == "")
			$avatar = "upload/img/default_list.png";
		else
			$avatar = "upload/img/list/" . $avatar;			

		$smarty->assign('avatar', $avatar);
		if($name == $_SESSION['views'][1])
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
	if($_SESSION['views'][0] == $user_id)
	{
		$smarty->assign('type', "list");
		$smarty->assign('id',0);
		$smarty->assign('name2', "Uploads");							
		$smarty->assign('pub',"" );
		$avatar = "";
		if($avatar == "")
			$avatar = "upload/img/default_list.png";
		else
			$avatar = "upload/img/list/" . $avatar;			

		$smarty->assign('avatar', $avatar);
		$smarty->display('tpl/list.html');
	}
}
else
{
	echo "no user with that name";
}
mysqli_close($conn);
?>	
