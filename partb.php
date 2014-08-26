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

/*$wineid = mysqli_query($con,"SELECT wine.wine_id, wine.wine_name, winery.winery_name, grape_variety.variety, wine.year
FROM wine, winery, wine_variety, grape_variety
WHERE wine.winery_id = winery.winery_id
AND wine_variety.variety_id = grape_variety.variety_id
AND wine.wine_id = wine_variety.wine_id");*/

$wineid = mysqli_query($con,"SELECT GROUP_CONCAT(grape_variety.variety SEPARATOR ', ') AS varietyz, wine.wine_id, wine.wine_name, winery.winery_name, wine.year, SUM(DISTINCT inventory.on_hand) AS on_handz, region.region_name, GROUP_CONCAT(DISTINCT inventory.cost SEPARATOR ', ') AS costz, inventory.on_hand
FROM wine, winery, wine_variety, grape_variety, inventory, region
WHERE wine.winery_id = winery.winery_id
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

$winexid = 1;
//$grape = mysqli_query($con,"SELECT grape_variety.variety


$winexid = 1;
//$grape = mysqli_query($con,"SELECT grape_variety.variety
//FROM grape_variety, wine, winery, wine_variety
//WHERE grape_variety.variety_id =$winexid");
//while($rown = mysqli_fetch_array($grape)) {
//  echo "<p>" . $rown['variety'] . "</p>";
//}



echo "<table border='1'>
<tr>
<th>wine_id</th>
<th>wine_name</th>
<th>year</th>
<th>winery name</th>
<th>grape variety</th>
<th>On Hand</th>
<th>On zzzzHand</th>
<th>Region</th>
<th>Cost</th>
</tr>";
while($row = mysqli_fetch_array($wineid)) {
$NAME =  $row['wine_id'];
$chocolate = mysqli_query ($con, "SELECT GROUP_CONCAT(inventory.cost ', ') AS costings, wine.wine_id,
FROM inventory, wine
WHERE wine.wine_id = inventory.wine_id
GROUP BY inventory.wine_id");
  echo "<tr>";
  echo "<td>" . $row['wine_id'] . "</td>";
  //echo "<td>" . $row['wine_name'] . "</td>";
  echo "<td>" . $NAME . "</td>";
  echo "<td>" . $row['year'] . "</td>";
  echo "<td>" . $row['winery_name'] . "</td>";
  echo "<td>" . $row['varietyz'] . "</td>";
  echo "<td>" . $row['on_hand'] . "</td>";
  echo "<td>" . $row['on_handz'] . "</td>";
  echo "<td>" . $row['region_name'] . "</td>";
  echo "<td>" . $row['costz'] . " </td>";
  echo "</tr>";
}

echo "</table>";

mysqli_close($con);
//public function get_inventory($wine_id_input)
//$chocolate = mysqli_query ($con, "SELECT GROUP_CONCAT(cost ', ') AS costings
//FROM inventory
//WHERE wine_id =
//GROUP BY inventory.wine_id");
 


?>
</body>
</html>
