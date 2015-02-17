<?php	
require_once('smarty.php');
require_once('db.php');
session_start();

$term = mysql_escape_string($_GET['term']);
$type = mysql_escape_string($_GET['type']);
$num = isset($_GET['num']) ? mysql_escape_string($_GET['num']) : 0;
$limit = "";
if($num > 0)
	$limit = "LIMIT ".$num;

if(isset($_SESSION["views"]))
	$user = $db->users->byID($_SESSION["views"]);


if($type == 'all' || $type == '')
{
	//echo "search results";
	ob_start();	
	$_GET['type'] = "user";
	include "search.php";
	$_GET['type'] = "list";
	include "search.php";
	$_GET['type'] = "track";
	include "search.php";

	$result = ob_get_clean();	
	echo $result;

}
else
{
	$terms = explode(" ", $term);
	$srt = array();
	$sru = array();
	$srl = array();
	foreach($terms as $word)
	{
		$srt[] = "author_name LIKE '%$word%' OR track_name LIKE '%$word%'";
		$sru[] = "display_name LIKE '%$word%'";
		$srl[] = "name LIKE '%$word%'";
	}
	$jkt = implode(" OR ", $srt);
	$jku = implode(" OR ", $sru);
	$jkl = implode(" OR ", $srl);

	if($type == 'track')
	{
		$playlists = array();
		if(isset($user))
		{
			$result = $user->getPlaylists();
			foreach($result as $row)
			{
				array_push($playlists, array($row->id, $row->get()->name));
			}
		}

		//$jk = "kay";
		//		echo "\\$jku\\";
		$result = mysqli_query($conn,"SELECT *, levenshtein_ratio(author_name || ' ' || track_name, '$term') AS lr FROM tracks ORDER BY lr ASC");
		if($result === false)
			echo("query failed: " . $conn->error . "\n");
		//		$result = mysqli_query($conn,"SELECT * , levenshtein_ratio(track_name,'$term') AS lr FROM tracks AND (file_type='audio/mpeg' OR file_type='audio/mp3') ORDER BY lr");
		//		$result = mysqli_query($conn,"SELECT *  FROM tracks WHERE $jkt AND (file_type='audio/mpeg' OR file_type='audio/mp3') ORDER BY id $limit");
		//$result = mysqli_query($conn,"SELECT * FROM tracks WHERE (author_name LIKE '%$term%' OR track_name LIKE '%$term%') AND (file_type='audio/mpeg' OR file_type='audio/mp3') ORDER BY id $limit");
		//$result = mysqli_query($conn,"SELECT tracks.id, tracks.file_name, tracks.author_name, tracks.track_name FROM tracks AS t, playlists AS pl, playlist_track AS pt WHERE pl.public AND pl.track_id = p.id AND pt.playlist_id = pl.id AND (t.author_name LIKE '%$term%' OR t.track_name LIKE '%$term%') AND (t.file_type='audio/mpeg' OR t.file_type='audio/mp3') ORDER BY t.id $limit");
		//$result = mysqli_query($conn,"SELECT DISTINCT t.id, t.track_name, t.author_name, t.file_name FROM tracks AS t, playlists AS pl, playlist_track AS pt WHERE (pl.public OR pl.user_id = $user_id ) AND pt.track_id = t.id AND pt.playlist_id = pl.id AND (t.author_name LIKE '%$term%' OR t.track_name LIKE '%$term%') AND (t.file_type='audio/mpeg' OR t.file_type='audio/mp3') ORDER BY t.id $limit");
	}
	elseif($type == 'user')
	{
		//$result = mysqli_query($conn,"SELECT id, name, display_name, avatar FROM users WHERE display_name LIKE '%$term%' ORDER BY id $limit");
		$result = mysqli_query($conn,"SELECT id, name, display_name, avatar FROM users WHERE $jku ORDER BY id $limit");
	}
	elseif($type ='list')
	{
		//$result = mysqli_query($conn,"SELECT id, user_id, name, avatar FROM playlists WHERE name LIKE '%$term%' AND public ORDER BY id $limit");
		$result = mysqli_query($conn,"SELECT id, user_id, name, avatar FROM playlists WHERE $jkl AND public ORDER BY id $limit");
		if($result === false)
			echo("list query failed: " . $conn->error . "\n");
	}

	$count = 0;
	echo $type."s with \"".$term."\"" ;
	while($result && $row = mysqli_fetch_array($result))
	{
		if($type == 'track')
		{	
			$smarty->assign('type', $type);
			$smarty->assign('track_id', $row['id']);
			$smarty->assign('file_name', $row['file_name']);	
			$smarty->assign('author_name', $row['author_name']);
			$smarty->assign('track_name', $row['track_name']);
			$smarty->assign('count', $count);
			if($user_id != 0)
				$smarty->assign('playlists', $playlists);
			else
				$smarty->assign('playlists', '');

			$smarty->display('tpl/playlist_div.html');

			$count++;	
		}
		else
		{
			$smarty->assign('type', $type);
			$smarty->assign('id', $row[0]);
			$smarty->assign('name', $row[1]);	
			$smarty->assign('name2', $row[2]);

			if($type == "user")
			{		
				if($row[3] == "")
					$row[3] = "upload/img/default_user.png";
				else
					$row[3] = "upload/img/user/" . $row[3];
			}
			else
			{
				if($row[3] == "")
					$row[3] = "upload/img/default_list.png";
				else
					$row[3] = "upload/img/list/" . $row[3];
			}

			$smarty->assign('avatar', $row[3]);
			$smarty->assign('pub', -1);
			$smarty->display('tpl/list.html');
		}		
	}
}

?>
