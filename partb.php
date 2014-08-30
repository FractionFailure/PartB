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

//The queries for the Dynamic Search boxes
$region_query = mysqli_query($con, "SELECT DISTINCT region_name, region_id FROM region");
$variety_query = mysqli_query($con, "SELECT DISTINCT variety, variety_id FROM grape_variety");
$years_query = mysqli_query($con, "SELECT DISTINCT year FROM wine ORDER BY year");


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

echo "<option value='Unselected'> Any </option>";

	while($row = mysqli_fetch_array($functioned_query)) {
		$queryResults = $row[0];
echo "<option value={$row[0]}>{$queryResults}</option>";
}
echo "</select>";
}
?>


<?php //This is the start of the form. Used functions because i thought it would streamline the process. But when too many deviations
// for each Dynamic query made it not worth the effort of reducing the code

// Used GET due to forseeable use in later parts.i
//Javascript for simple validation, works on onsubmit principle. If invalid, will not submit and display alert error message?>
<script>
function validate() {
var check = 0;
var year_min = document.forms["form"][4].value;
var year_max = document.forms["form"][5].value;
var cost_min = document.forms["form"][8].value;
var cost_max = document.forms["form"][9].value;
var text = "";

if(year_min > year_max && year_max != "Unselected" && year_min != "Unselected"){
text = "Minimum year exceeds Maximum year \n";
check = 1;
}
if(cost_min > cost_max && cost_max != ""){
text += "Minimum cost exceeds Maximum cost";
check = 1;
}
if (check == 1){
alert(text);
return false;
}
return true;
}
</script>

<form id="form" onsubmit="return validate()" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
Wine Name: <input type="text" name="winename"><br>
Winery Name: <input type="text" name="wineryname"><br>
<?php
echo "Region:";
DynamicQueries("region", $region_query);
echo "</br> Variety:";
DynamicQueriesVariety("variety",$variety_query);
echo "</br>Years:";
DynamicYears("years_min", $years_query);
//reseting  $years_query to 0 to allow for repeated use of query with fetch array
mysqli_data_seek( $years_query, 0 );
DynamicYears("years_max", $years_query);
?>
<?php //type number restricts the values to numerals. Step defines the amount of decimal places allowed?>
<br>
Wine Stock: <input type="number" step=".01" name="stock"><br>
Ordered: <input type="number" name="ordered"><br>
Dollar Cost:Min: <input type="number" step=".01" name="mincost">
Max:<input type="number" step=".01" name="maxcost"><br>

<input type="submit" name="submit" value="Run Query">
</form>


<?php

//region will always be set, so it is used to define intiation of get_table
if(isset($_GET['region'])) {

//Would do some data validation here but not asked to do.
//Just putting $GET variables to newly declared variables. Seemed like this would be a good idea in case something required the original info later.
//Wine_name and winery_name went straight in because their data is unchanged regardless
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
$get_ordered = $_GET["ordered"];
if($get_ordered == ""){
$get_ordered = 0;}

$get_min_cost = $_GET["mincost"];
if($get_min_cost == ""){
$get_min_cost = 0;}

$get_max_cost = $_GET["maxcost"];
if($get_max_cost == ""){
$get_max_cost = 100000;}

$get_stock = $_GET["stock"];
if($get_stock == ""){
$get_stock = 0;}



//THE ALL KNOWING SQL QUERY. retreives all rows then is limited by the get variables.
$wineid = mysqli_query($con,"SELECT GROUP_CONCAT(DISTINCT grape_variety.variety SEPARATOR ', ') AS varietyz, wine.wine_id, wine.wine_name, winery.winery_name, wine.year, region.region_name, GROUP_CONCAT(DISTINCT inventory.cost SEPARATOR ', ') AS costz, inventoryz.costzies, inventoryz.on_hand_corrected, itemz.qty, itemz.price
FROM wine, winery, (SELECT DISTINCT wine_id FROM wine_variety WHERE variety_id LIKE '$get_variety') AS wine_varietyz, wine_variety, grape_variety, inventory, region, (SELECT wine_id, SUM(qty) AS qty, SUM(price) AS price FROM items GROUP BY wine_id) AS itemz, (SELECT DISTINCT wine_id, GROUP_CONCAT(DISTINCT inventory.cost SEPARATOR ', ') as costzies, SUM(on_hand) AS on_hand_corrected FROM inventory GROUP BY wine_id) AS inventoryz
WHERE wine.winery_id = winery.winery_id
AND wine.wine_id = itemz.wine_id
AND wine_variety.variety_id = grape_variety.variety_id
AND wine.wine_id = wine_variety.wine_id
AND inventory.wine_id = wine.wine_id
AND inventoryz.wine_id = wine.wine_id
AND winery.region_id = region.region_id
AND inventoryz.on_hand_corrected >= '$get_stock'
AND region.region_id LIKE '$get_region' 
AND wine.wine_id = wine_varietyz.wine_id
AND itemz.qty >= '$get_ordered'
AND wine.year <= '$get_max_year'
AND wine.year >= '$get_min_year'
AND inventory.cost >= '$get_min_cost'
AND inventory.cost <= '$get_max_cost'
AND wine.wine_name LIKE '%" . ($_GET['winename']) . "%'
AND winery.winery_name LIKE '%" . ($_GET['wineryname']) . "%'
GROUP BY wine.wine_id");

// If statment for to check whether anything was queried.
if(mysqli_fetch_array($wineid)>0){
//a reset due to if statement advancing position pointer in $wineid query
mysqli_data_seek( $wineid, 0 );
//intialize the table headers
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

//Filling the table with queried data from $wineid query
while($row = mysqli_fetch_array($wineid)) {
  echo "<tr>";
  echo "<td>" . $row['wine_id'] . "</td>";
  echo "<td>" . $row['wine_name'] . "</td>";
  echo "<td>" . $row['year'] . "</td>";
  echo "<td>" . $row['winery_name'] . "</td>";
  echo "<td>" . $row['varietyz'] . "</td>";
  echo "<td>" . $row['on_hand_corrected'] . "</td>";
  echo "<td>" . $row['region_name'] . "</td>";
  echo "<td>" . $row['costzies'] . " </td>";
  echo "<td>" . $row['qty'] . " </td>";
  echo "<td>" . $row['price'] . " </td>";
  echo "</tr>";
}

echo "</table>";
}
else {
echo "No records match your search criteria";
}
}
mysqli_close($con);
 


?>
</body>
</html>
