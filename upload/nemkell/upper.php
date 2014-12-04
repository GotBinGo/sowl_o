<html>
<body>
<?php
include('../conn.php');


	$result = mysqli_query($conn,"SELECT * FROM tracks WHERE track_id=".$_GET['track_id']);
	mysqli_close($conn);
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


echo "<form action='upload_file.php?track_id=". $_GET['track_id'] ."'method='post'
enctype='multipart/form-data'>";
?>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html> 