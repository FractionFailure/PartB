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

$region_query = mysqli_query($con, "SELECT DISTINCT region_name, region_id FROM region");
$variety_query = mysqli_query($con, "SELECT DISTINCT variety, variety_id FROM grape_variety");
$years_query = mysqli_query($con, "SELECT DISTINCT year FROM wine ORDER BY year");
$years_duplicate_query = $years_query;

//for the Dynamic region.
function DynamicQueries($query_name, $functioned_query) {
	echo "<select name={$query_name}>";
	while($row = mysqli_fetch_array($functioned_query)) {
		$queryResults = $row[0];
echo "<option value={$row[1]}>{$queryResults}</option>";
}
echo "</select>";
}

//for the Dynamic variety.
function DynamicQueriesVariety($query_name, $functioned_query) {
	echo "<select name={$query_name}>";
echo "<option value='All'> All </option>";
	while($row = mysqli_fetch_array($functioned_query)) {
		$queryResults = $row[0];
echo "<option value={$row[1]}>{$queryResults}</option>";
}
echo "</select>";
}

//for Dynamic years
function DynamicYears($query_name, $functioned_query) {
	echo "<select name={$query_name}>";

echo "<option value='Unselected'> Year </option>";

	while($row = mysqli_fetch_array($functioned_query)) {
		$queryResults = $row[0];
echo "<option value={$row[0]}>{$queryResults}</option>";
}
echo "</select>";
}
?>



<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
Wine Name: <input type="text" name="winename"><br>
Winery Name: <input type="text" name="wineryname"><br>
<?php
echo "Region:";
DynamicQueries("region", $region_query);
echo "</br> Variety:";
DynamicQueriesVariety("variety",$variety_query);
echo "</br>Years:";
DynamicYears("years_min", $years_query);
mysqli_data_seek( $years_query, 0 );
DynamicYears("years_max", $years_query);
?>
<br>
First name: <input type="text" name="firstname"><br>
First name: <input type="text" name="firstname"><br>
First name: <input type="text" name="firstname"><br>


<input type="submit" name="submit" value="Run Query">
</form>

<?php
if(isset($_GET['region'])) {
echo $_GET["region"];

$get_region = $_GET["region"];
if($get_region == 1){
$get_region = "%";
}


$get_variety = $_GET["variety"];
if($get_variety == "All"){
$get_variety = "%";
}

$get_min_year = $_GET["years_min"];
if($get_min_year == "Unselected"){
$get_min_year = -2000;
}
$get_max_year = $_GET["years_max"];
if($get_max_year == "Unselected"){
$get_max_year = 4000;
}





echo $_GET["years_max"];
echo "<br>";
echo $get_variety;
echo $get_region;
$wineid = mysqli_query($con,"SELECT GROUP_CONCAT(DISTINCT grape_variety.variety SEPARATOR ', ') AS varietyz, wine.wine_id, wine.wine_name, winery.winery_name, wine.year, region.region_name, GROUP_CONCAT(DISTINCT inventory.cost SEPARATOR ', ') AS costz, inventory.on_hand, itemz.qty, itemz.price
FROM wine, winery, wine_variety, grape_variety, inventory, region, (SELECT wine_id, SUM(qty) AS qty, SUM(price) AS price FROM items GROUP BY wine_id) AS itemz
WHERE wine.winery_id = winery.winery_id
AND wine.wine_id = itemz.wine_id
AND wine_variety.variety_id = grape_variety.variety_id
AND wine.wine_id = wine_variety.wine_id
AND inventory.wine_id = wine.wine_id
AND winery.region_id = region.region_id
AND region.region_id LIKE '$get_region' 
AND grape_variety.variety_id LIKE '$get_variety'
AND wine.year <= '$get_max_year'
AND wine.year >= '$get_min_year'
AND wine.wine_name LIKE '%" . ($_GET['winename']) . "%'
AND winery.winery_name LIKE '%" . ($_GET['wineryname']) . "%'
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
}
mysqli_close($con);
 


?>
</body>
</html>
