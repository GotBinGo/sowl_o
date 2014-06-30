<style>


html{
width:80%;
margin:auto;
}
table
{
border:2px solid black;
border-collapse:collapse;
width:100%;
}
.dbutton
{
background-color:#555555; 
color:#eeeeee; 
font-size:20px; 
font-family:Arial;
cursor:pointer;
padding:5 20 5 20;
}
.dbutton:hover
{
background-color:#aaaaaa; 
}
</style>


<?php
include('../pass.php');
$con=mysqli_connect("127.0.0.1","bcophm_music",$pass,"bcophm_music");
if (mysqli_connect_errno()) 
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{

$result = mysqli_query($con,"SELECT * FROM tracks");
echo "<table border='1'>
<tr>
<th>track_id</th>
<th>file_name</th>
<th>author_name</th>
<th>track_name</th>
<th>upload</th>
<th>owner</th>
<th>type</th>
</tr>";
  while($row = mysqli_fetch_array($result)) {
  echo "<tr>";
  echo "<td>" . $row['track_id'] . "</td>";
      if (file_exists("uploads/" .$row['file_name']))
        echo "<td><a href='uploads/" . $row['file_name'] . "'>".$row['file_name']."</a></td>";
	  else  
  echo "<td>" . $row['file_name'] . "</td>";
  echo "<td>" . $row['author_name'] . "</td>";
  echo "<td>" . $row['track_name'] . "</td>";
  echo "<td><a href='upper.php?track_id=" . $row['track_id'] ."'>Link text</a></td>";
  echo "<td>" . $row['adder_id'] . "</td>";
  echo "<td>" . $row['file_type'] . "</td>";
  echo "</tr>";
}
echo "</table>";
mysqli_close($con);
}
?>



<div align="center" style="width:150px" class="dbutton" onclick="location.href='adder.html'">
 + Új szám
</div>
