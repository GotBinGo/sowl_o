<?php
$a = count($_FILES['file']['name']);

echo "$a";

//for($i = 0; i < count($a));

/*
if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){

	if(move_uploaded_file($_FILES['file']['tmp_name'], "uploads/" . $_FILES['file']['name']))
	{
		echo '{"status":"success"}';
		echo $_FILES['file']['tmp_name'] ."" . $_FILES['file']['name'];
		exit;
	}
}
echo '{"status":"error"}';
echo "end";
*/
?>