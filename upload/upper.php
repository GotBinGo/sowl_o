<html>
<body>
<?php
set_include_path("..:" . get_include_path());
include('pass.php');
$con=mysqli_connect("127.0.0.1","bcophm_music",$pass,"bcophm_music");
if (mysqli_connect_errno()) 
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{

	$result = mysqli_query($con,"SELECT * FROM tracks WHERE track_id=".$_GET['track_id']);
	mysqli_close($con);
	echo "<table border='1'>
		<tr>
		<th>track_id</th>
		<th>file_name</th>
		<th>author_name</th>
		<th>track_name</th>

		</tr>";
while($row = mysqli_fetch_array($result)) {
	echo "<tr>";
	echo "<td>" . $row['track_id'] . "</td>";
	echo "<td>" . $row['file_name'] . "</td>";
	echo "<td>" . $row['author_name'] . "</td>";
	echo "<td>" . $row['track_name'] . "</td>";
	echo "</tr>";
}
echo "</table>";

}
echo "<form action='upload_file.php?track_id=". $_GET['track_id'] ."'method='post'
	enctype='multipart/form-data'>";
?>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html> 
