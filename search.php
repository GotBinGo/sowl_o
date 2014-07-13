<?php	
require_once('libs/Smarty.class.php');
$smarty = new Smarty;

$term = mysql_escape_string($_GET['term']);
include("conn.php");
$result = mysqli_query($conn,"SELECT * FROM tracks WHERE (author_name LIKE '%$term%' OR track_name LIKE '%$term%') AND (file_type='audio/mpeg' OR file_type='audio/mp3')");
mysqli_close($conn);
$count = 0;
while($row = mysqli_fetch_array($result))
{
//$row = mysqli_fetch_array($result);
//$row = mysqli_fetch_array($result);
	$smarty->assign('count', $count);
	$smarty->assign('file_name', $row['file_name']);
	
	$smarty->assign('author_name', $row['author_name']);
	$smarty->assign('track_name', $row['track_name']);
	/*
	echo "<div class='List_Item' id='" . $count . "' sid='" . $row['file_name'] . "' onclick='Play_This(id)'>";
	echo $row['author_name'] . " - " . $row['track_name'] . "</div>";*/
	$count++;		
	$smarty->display('tpl/playlist_div.html');
}


?>	