<!DOCTYPE html>
<html>
<body>

	<?php
//webadmin because who cares
$con=mysqli_connect("localhost","webadmin","password","winestore");
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$wineid = mysqli_query($con,"SELECT wine.wine_name, winery.winery_name, grape_variety.variety, wine.year
FROM wine, winery, wine_variety, grape_variety
WHERE wine.winery_id = winery.winery_id
AND wine_variety.variety_id = grape_variety.variety_id
AND wine.wine_id = wine_variety.wine_id");

echo "<table border='1'>
<tr>
<th>wine_name</th>
<th>year</th>
<th>winery name</th>
<th>grape variety</th>
</tr>";

while($row = mysqli_fetch_array($wineid)) {
  echo "<tr>";
  echo "<td>" . $row['wine_name'] . "</td>";
  echo "<td>" . $row['year'] . "</td>";
  echo "<td>" . $row['winery_name'] . "</td>";
  echo "<td>" . $row['variety'] . "</td>";
  echo "</tr>";
}

echo "</table>";

mysqli_close($con);
?>
</body>
</html>
