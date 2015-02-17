<?php
set_include_path("..:" . get_include_path());
require_once('db.php');
session_start();
if(isset($_SESSION['views']))
{
	$user = $db->users->byID($_SESSION['views']);
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
	if(strlen($id))
	{
		$sql="SELECT user_id, file_name FROM tracks WHERE id = '$id'";
		$result = mysqli_query($conn,$sql);
		$row = mysqli_fetch_array($result);
		if ($row['user_id'] == $user->id)
		{
			echo "delete {$row['file_name']}";
			$value = "uploads/" . $row['file_name'];
			if (file_exists($value)) {
				unlink($value);
			}
			echo "delete record";

			$sql="DELETE FROM tracks WHERE id='$id'";
			if (!mysqli_query($conn,$sql)) {
				die('Error: ' . mysqli_error($conn));
			}
			else
				echo "deleted";
		}
		else
		{
			echo "track not yours";
		}		
	}
	else
	{
		echo "no id";
	}
	mysqli_close($conn);
}
else
{
	echo "not logged in";
}
?>
