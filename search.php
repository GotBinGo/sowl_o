<?php	
require_once('smarty.php');
require_once('db.php');
session_start();

$term = $_GET["term"];
$type = $_GET["type"];
$limit = isset($_GET['num']) ? $_GET["num"] : 0;

if(isset($_SESSION["views"]))
	$user = $db->users->byID($_SESSION["views"]);
else
	$user = NULL;

function searchUsers($term)
{
	echo "users with \"".$term."\"" ;

	global $db;
	global $limit;
	global $smarty;
	$result = $db->users->search($term, $limit);
	if(!$result)
		return FALSE;

	foreach($result as $current)
	{
		$record = $current->get();
		$smarty->assign('type', "user");
		$smarty->assign('id', $record->id);
		$smarty->assign('name', $record->name);	
		$smarty->assign('name2', $record->display_name);
		$smarty->assign('avatar', $record->avatar);
		$smarty->assign('pub', -1);
		$smarty->display('tpl/list.html');
	}
}

function searchLists($term)
{
	echo "playlists with \"".$term."\"" ;

	global $db;
	global $user;
	global $limit;
	global $smarty;
	$result = $db->playlists->search($user, $term, $limit);
	if(!$result)
		return FALSE;

	foreach($result as $current)
	{
		$record = $current->get();
		$smarty->assign('type', "list");
		$smarty->assign('id', $record->id);
		$smarty->assign('name', $record->name);	
		$smarty->assign('name2', $record->name);
		$smarty->assign('avatar', $record->avatar);
		$smarty->assign('pub', -1);
		$smarty->display('tpl/list.html');
	}
}

function searchTracks($term)
{
	echo "tracks with \"".$term."\"" ;

	global $db;
	global $user;
	global $limit;
	global $smarty;
	$result = $db->tracks->search($user, $term, $limit);
	if(!$result)
		return FALSE;

	$count = 0;
	foreach($result as $current)
	{
		$record = $current->get();

		$smarty->assign('type', "track");
		$smarty->assign('track_id', $record->id);
		$smarty->assign('file_name', $record->file_name);	
		$smarty->assign('author_name', $record->author);
		$smarty->assign('track_name', $record->title);
		$smarty->assign('count', $count);
		$smarty->assign('playlists', array());

		$smarty->display('tpl/playlist_div.html');

		$count++;	
	}
}

switch($type)
{
default:
case "all":
	searchUsers($term);
	searchLists($term);
	searchTracks($term);
	break;
case "user":
	searchUsers($term);
	break;
case "list":
	searchLists($term);
	break;
case "track":
	searchTracks($term);
	break;
}

?>
