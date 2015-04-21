<?php	
require_once("db.php");

require_once('smarty.php');
session_start();
$name = isset($_GET['name']) ? $_GET["name"] : "";

if(isset($_SESSION["views"]))
{
	$viewerhnd = $db->users->byID($_SESSION["views"]);
}
else
{
	$viewerhnd = NULL;
}

$viewedhnd = $name == "" ? $viewerhnd : $db->users->byName($name);
$vieweduser = $viewedhnd->get();

echo("<div class=\"list_header\"><img src=\"$vieweduser->avatar\" height=\"42\" width=\"42\">$vieweduser->display_name</div>");

$lists = $viewedhnd->playlistsVisibleTo($viewerhnd);

foreach($lists as $record)
{
	global $user;
	$r = $record->get();
	$smarty->assign('type', "list");
	$smarty->assign('id', $r->id);
	$smarty->assign('name2', $r->name);							
	$smarty->assign('avatar', $r->avatar);
	if($viewerhnd->id == $viewedhnd->id)
		$smarty->assign('pub', $r->isPublic);
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
	$avatar = $user->avatar == "upload/img/default_user.png" ? "upload/img/default_list.png" : $user->avatar;

	$smarty->assign('avatar', $avatar);
	$smarty->display('tpl/list.html');
}
?>	
