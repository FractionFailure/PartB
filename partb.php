<!DOCTYPE html>
<html>
<body>

	<?php
//webadmin because who cares...
$con=mysqli_connect("localhost","webadmin","password","winestore");
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$region_query = mysqli_query($con, "SELECT DISTINCT region_name FROM region");
$variety_query = mysqli_query($con, "SELECT DISTINCT variety FROM grape_variety");
$years_query = mysqli_query($con, "SELECT DISTINCT year FROM wine ORDER BY year");
$years_duplicate_query = $years_query;

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">

<select value="Region"
<?php
	while($row = mysqli_fetch_array($region_query)) {
		$tableName = $row[0];
?>
<option value=<?php echo $tableName;?>><?php echo $tableName;?></option>
<?php
}?>
</select>
</br>


<select value="Variety"
<?php
	while($row = mysqli_fetch_array($variety_query)) {
		$tableName = $row[0];
?>
<option value=<?php echo $tableName;?>><?php echo $tableName;?></option>
<?php
}?>
</select>


</br>
<select value="Years Min"
<?php
	while($row = mysqli_fetch_array($years_query)) {
		$tableName = $row[0];
?>
<option value=<?php echo $tableName;?>><?php echo $tableName;?></option>
<?php
}?>
</select>

<select value="Years Max"
<?php
	while($row = mysqli_fetch_array($years_duplicate_query)) {
		$tableName = $row[0];
?>
<option value=<?php echo $tableName;?>><?php echo $tableName;?></option>
<?php
}?>
</select>



<input type="submit" name="submit" value="Run Query">
</form>

<?php
$wineid = mysqli_query($con,"SELECT GROUP_CONCAT(DISTINCT grape_variety.variety SEPARATOR ', ') AS varietyz, wine.wine_id, wine.wine_name, winery.winery_name, wine.year, region.region_name, GROUP_CONCAT(DISTINCT inventory.cost SEPARATOR ', ') AS costz, inventory.on_hand, itemz.qty, itemz.price
FROM wine, winery, wine_variety, grape_variety, inventory, region, (SELECT wine_id, SUM(qty) AS qty, SUM(price) AS price FROM items GROUP BY wine_id) AS itemz
WHERE wine.winery_id = winery.winery_id
AND wine.wine_id = itemz.wine_id
AND wine_variety.variety_id = grape_variety.variety_id
AND wine.wine_id = wine_variety.wine_id
AND inventory.wine_id = wine.wine_id
AND winery.region_id = region.region_id
GROUP BY wine.wine_id");


/*$wineid = mysqli_query($con,"SELECT wine.wine_id, wine.wine_name, wine.year, winery.winery_name, GROUP_CONCAT(grape_variety.variety SEPARATOR ', ')
FROM wine
INNER JOIN winery
ON wine.winery_id = winery.winery_id
INNER JOIN wine_variety
ON wine.wine_id = wine_variety.wine_id
INNER JOIN grape_variety
ON wine_variety.variety_id = grape_variety.variety_id
GROUP BY wine.wine_id, wine.year, wine.wine_name");*/



echo "<table border='1'>
<tr>
<th>wine_id</th>
<th>wine_name</th>
<th>year</th>
<th>winery name</th>
<th>grape variety</th>
<th>On Hand</th>
<th>Region</th>
<th>Cost</th>
<th>QTY</th>
<th>price</th>
</tr>";
while($row = mysqli_fetch_array($wineid)) {
  echo "<tr>";
  echo "<td>" . $row['wine_id'] . "</td>";
  echo "<td>" . $row['wine_name'] . "</td>";
  echo "<td>" . $row['year'] . "</td>";
  echo "<td>" . $row['winery_name'] . "</td>";
  echo "<td>" . $row['varietyz'] . "</td>";
  echo "<td>" . $row['on_hand'] . "</td>";
  echo "<td>" . $row['region_name'] . "</td>";
  echo "<td>" . $row['costz'] . " </td>";
  echo "<td>" . $row['qty'] . " </td>";
  echo "<td>" . $row['price'] . " </td>";
  echo "</tr>";
}

echo "</table>";

mysqli_close($con);
 


?>
</body>
</html>
