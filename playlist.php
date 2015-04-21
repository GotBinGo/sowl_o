<?php
$id = $_GET["id"];
require_once("db.php");
require_once('smarty.php');
session_start();

if(isset($_SESSION["views"]))
	$user = $db->users->byID($_SESSION["views"]);
else
	$user = NULL;

$playlisthnd = $id ? $db->playlists->byID($id) : NULL;
$playlist = $playlisthnd ? $playlisthnd->get() : NULL;
$title = $playlist ? $playlist->name : "Uploads";
$tracks = $playlisthnd ? $playlisthnd->tracks() : ($user ? $user->tracks() : NULL);
$avatar = $playlist ? $playlist->avatar : "upload/img/default_list.png";

if($tracks === FALSE)
{
	die("The requested playlists is inaccessible or doesn't exist");
}


echo "<div class=\"list_header\">";
echo "<img src='$avatar' height='42' width='42'>";
echo $title;
if($playlist)
	echo "<a class='download_btn' onclick='event.stopPropagation()' href='upload/down_list.php?id=$id' download></a>";
echo "</div>";

$count = 0;
foreach($tracks as $hnd)
{
	$track = $hnd->get();
	$smarty->assign("type", "track");
	$smarty->assign('count', $count++);
	$smarty->assign('track_id', $track->id);
	$smarty->assign('file_name', $track->file_name);
	$smarty->assign('author_name', $track->author);
	$smarty->assign('track_name', $track->title);
	$smarty->assign('playlists', array());

	$smarty->display('tpl/playlist_div.html');
}
if($count == 0)
	die("<br />empty<br />");

?>
