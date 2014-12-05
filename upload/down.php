<?php
$id = $_GET['fn'];
$outname = $_GET['n'];
if (strpos($id, '/') === FALSE)
{
	header('Content-type: audio/mp3');
	header("Content-Disposition: attachment; filename=\"". $outname . ".mp3\"");
	readfile('./uploads/' .$id);
}
else	
{
	echo "not filename";
}
?>
