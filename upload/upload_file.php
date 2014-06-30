<?php
include('../pass.php');
$con=mysqli_connect("127.0.0.1","bcophm_music",$pass,"bcophm_music");
if (mysqli_connect_errno()) 
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{

$result = mysqli_query($con,"SELECT file_name FROM tracks WHERE track_id=".$_GET['track_id']);
$name = mysqli_fetch_array($result)['file_name'];
$type = $_FILES["file"]["type"];
$sql2="UPDATE tracks SET file_type='".$type."' WHERE track_id=" . $_GET['track_id'];
if (!mysqli_query($con,$sql2)) {
  die('Error: ' . mysqli_error($con));
}
mysqli_close($con);
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
/*
    if (file_exists("uploads/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {*/
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "uploads/" . $name);
      echo "Stored in: " . "uploads/";
	  echo $name;
      
    }
}

?> 