<?php
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $db->users->byID($_SESSION['views']);
	$list_id = $_GET["id"];
	$public = $_GET["public"];
	$listhnd = $db->playlists->byID($list_id);
	$list = $listhnd->get();
	if($list->owner_id != $user->id)
		die("You are not allowed to change playlist public state");
	if($listhnd->setPublic($public))
	{
		echo("Playlist is now " . ($public == "true" || $public == 1 ? "public" : "private"));
	}
	else
		echo("Error: " . $db->error);
}
else
{
	echo "You are not logged in";
}


?>
