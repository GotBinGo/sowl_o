<?php
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $db->users->byID($_SESSION['views']);
	$track_id = $_GET['track_id'];
	$list_id = $_GET['list_id'];
	$listhnd = $db->playlists->byID($list_id);
	$list = $listhnd->get();
	if($list === FALSE)
		die("Playlist doesn't exist");
	if($list->owner_id != $user->id)
		die("You don't own the playlist");
	if($db->tracks->byID($track_id)->get() === FALSE)
		die("Track doesn't exist");
	if($listhnd->insert($track_id))
		echo "added";
	else
		die("Failed to add track to playlist: " . $db->error);
}
else
{
	echo "not logged in";
}


?>
