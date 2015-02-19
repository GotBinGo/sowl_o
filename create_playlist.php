<?php
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $db->users->byID($_SESSION['views']);
	$name = $_GET['name'];
	$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
	if($name == "")
		die("no name");

	$res = $db->playlists->create($user, $name);
	if(!$res)
		die("Error: " . $db->error);
	echo("Playlist \"$name\" created");
}
else
{
	echo "not logged in";
}

?>
