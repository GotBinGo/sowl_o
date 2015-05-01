<?php
set_include_path("..:" . get_include_path());
require_once('db.php');
//require_once("getid3.php");
session_start();
if(!isset($_SESSION["views"]))
	die("not logged in");
$user = $db->users->byID($_SESSION["views"]);
if(count($_FILES) <= 0)
	die("no files");

foreach ($_FILES as $file)
{   
	if($file['name'] == 'blob')
		$file['name'] = $_POST['name'];

	if($file["error"] != 0)
	{
		echo("error: " . $file["error"]);
		continue;
	}

	if($file["type"] != 'audio/mpeg' && $file["type"] != 'audio/mp3' && $file["type"] != "application/x-flac")
	{
		echo("bad type: " . $file["type"]);
		continue;
	}

	$pos = strrpos($file['name'], ".");
	$basename = substr($file['name'], 0, $pos);
	$extension = substr($file['name'], $pos);

	$author = trim(explode('-', str_replace("_", " ", $basename), 2)[0]);
	$title = trim(explode('-', str_replace("_", " ", $basename), 2)[1]);

	if($author == "")
		$author = "Névtelen";
	if($title == "")
		$title = "Névtelen";

	$tmpname = $file["tmp_name"];
/*	$getid3 = new getID3();
	$getid3->option_save_attachments = false;
	$getid3->option_md5_data = false;
	$getid3->option_md5_data_source = false;
	$getid3->option_sha1_data = false;

	$info = $getid3->analyze($tmpname);
	getid3_lib::CopyTagsToComments($info);*/

	if(isset($info["comments"]["artist"][0]) && $info["comments"]["artist"][0] != "")
		$author = $info["comments"]["artist"][0];

	if(isset($info["comments"]["title"][0]) && $info["comments"]["title"][0] != "")
		$title = $info["comments"]["title"][0];

	$length = $info["playtime_seconds"];
	$tags = array_merge(
		$info["comments"]["artist"],
		$info["comments"]["title"],
		($info["comments"]["genre"]) ? $info["comments"]["genre"] : array(),
		$info["comments"]["album"]
		);


	$result = $db->tracks->add($user, $tmpname, $extension, $author, $title, $length, $file["type"], $tags);
	if($result === FALSE)
		die("bad: " . $db->error);

	echo("good, id: $result");
}

?>
