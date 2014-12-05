<?php
include("../conn.php");
$files = array();
$id = mysql_escape_string($_GET['id']);
$result = mysqli_query($conn,"SELECT t.id AS id, t.file_name AS file_name , t.author_name AS author_name, t.track_name AS track_name FROM playlist_track AS pt, tracks AS t WHERE t.id = pt.track_id AND pt.playlist_id = '$id' ORDER BY t.id");
if(mysqli_num_rows($result) > 0)
{		
	while($row = mysqli_fetch_array($result))		
	{
		//var_dump($row);
		array_push($files,array("path" => $row['file_name'], "name"=>$row['author_name']." - ".$row['track_name'].".mp3"));

	}
	//var_dump($files);
	$zipname = "down/".$id.".zip";
	$zip = new ZipArchive;
	$zip->open($zipname, ZipArchive::CREATE);
	foreach ($files as $file) {
		$zip->addFile('uploads/'.$file['path'],$file['name'] );
	}
	$zip->close();
	$result = mysqli_query($conn,"SELECT * FROM playlists WHERE id = '$id'");
	$row = mysqli_fetch_array($result);

	header('Content-Type: application/zip');
	header("Content-disposition: attachment; filename=\"".$row['name']."\"");
	header('Content-Length: ' . filesize($zipname));
	readfile($zipname);
}
else
	echo "no result";

//echo "end";
?>
