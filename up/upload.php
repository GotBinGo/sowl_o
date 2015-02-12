<?php
require_once('../db.php');
if(count($_FILES > 0))
{
	foreach ($_FILES as $file)
	{   
		if($file['name'] == 'blob')
			$file['name'] = $_POST['name'];
		if($file['error'] == 0)
		{
			if($file['type'] == 'audio/mpeg' || $file['type'] == 'audio/mp3')
			{
				$pos = strrpos($file['name'], ".");
				$tit = substr($file['name'], 0, $pos);
				//echo $tit;
				//$tit = explode(".",$file['name'],2)[0];
				$au = explode('-', $tit, 2)[0];
				$ti = explode('-', $tit, 2)[1];

				/*var_dump($tit);
				var_dump($au);
				var_dump($ti);*/
				$au = trim($au);
				$ti = trim($ti);
				if(strlen($au) < 1)
					$au = "Névtelen";
				if(strlen($ti) < 1)
					$ti = "Névtelen";
				$opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
				$context = stream_context_create($opts);
				/*
				$au = htmlspecialchars($au, ENT_QUOTES, 'UTF-8');
				$ti = htmlspecialchars($ti, ENT_QUOTES, 'UTF-8');
				 */
				$ti = rawurlencode($ti);
				$au = rawurlencode($au);
				$url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["REQUEST_URI"]) . "/../upload/insert.php?author="
					. $au . "&title=" . $ti;
				$homepage = file_get_contents($url,false,$context);                
				if(move_uploaded_file($file['tmp_name'], "../upload/uploads/" . $homepage))
				{
					$sql="UPDATE tracks SET file_type='".$file['type']."' WHERE file_name='" . $homepage ."'";
					if (!mysqli_query($conn,$sql)) {
						die('Error: ' . mysqli_error($conn));
					}
					else
					{
						echo "good ";
					}
				}
				else
				{
					echo "bad";
				}                    
			}
			else
				echo "bad type";
		}
	}
}
?>
