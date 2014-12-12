<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Toys-filter</title>
	<link rel="stylesheet" href="Style/style.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script>
		$(function() {
			$( "#slider-range" ).slider({
				range: true,
				min: 0,
				max: 300,
				values: [ 0, 300 ],
				slide: function( event, ui ) {
					$( "#amount" ).val( ui.values[ 0 ] + " лева - " + ui.values[ 1 ] + " лева");
				}
			});
			$( "#amount" ).val( $( "#slider-range" ).slider( "values", 0 ) +
			" лева - " + $( "#slider-range" ).slider( "values", 1 ) + " лева");
		});
	</script>
</head>
<body>

	<div class="filter">
		<div class="header">ИГРАЧКО - ТЪРСАЧКА</div>
		<form method="GET">
			<fieldset>
			<h2>Възраст:</h2>
			<select name="age">
				<option value="all" class="all">Избери възраст</option>
				<option value="0-2">0-2</option>
				<option value="2-4">2-4</option>
				<option value="4-8">4-8</option>
				<option value="8-12">8-12</option>
				<option value="12">12+</option>
			</select>
			</fieldset>			<fieldset>
			<h2>Пол:</h2>
			<div class="radio">
			<input type="radio" name="gender" value="m"/><label>МОМЧЕ</label>
			<input type="radio" name="gender" value="f"/><label>МОМИЧЕ</label>
			<input type="radio" name="gender" checked="checked" value="m/f"/><label>И ДВЕТЕ</label>
			</div>
			</fieldset>
				<fieldset>
			<h2>Цена:</h2>
			<div id="slider-range"></div>
			<input type="text" name="price" id="amount">
			</fieldset>
			<fieldset><input type="submit" value="ТЪРСИ" class="myButton"/><input type="hidden" name="page" value="1"/></fieldset>
		</form>
	</div>
<div class="toys">
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toys";
$filter = '';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET["age"])) {
	$age = $_GET["age"];
	$gender = $_GET["gender"];
	$price = str_replace("$", "", $_GET["price"]);
	$price = explode("-", $price);
	$minPrice = trim($price[0]);
	$maxPrice = trim($price[1]);
	$genderFilter = '';
	$ageFilter = '';
	if($gender !='m/f'){
		$genderFilter = " AND (gender = '".$gender."' OR gender = 'm/f')";
	}
	if($age !='all'){
		$ageFilter = "age='".$age."' AND ";
	}
	$filter = " WHERE ".$ageFilter."(price > '".$minPrice."' AND price < '".$maxPrice."')".$genderFilter;
}

if(isset($_GET['page'])){
	$page  = $_GET['page'];
} else {
	$page = 1;
	$_GET['page'] = $page + 1;
}

$rs_result = $conn->query("SELECT COUNT(`id`) FROM toys".$filter);
$row = mysqli_fetch_row($rs_result);
$total_records = $row[0];
$total_pages = ceil($total_records / 9);

if(isset($_GET['a']) && $_GET['a'] == "Напред" && (($page + 1) <= $total_pages)){
	$_GET['page']=$page+1;
} else if(isset($_GET['a']) && $_GET['a'] == "Назад" && (($page - 1) > 0)){
	$_GET['page']=$page-1;
}

$start_from = ($page-1) * 9;
$sql = "SELECT * FROM toys".$filter." LIMIT $start_from, 9";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()):
?>

<div class="toy">
	<div class="image"><img src="<?=$row["image"]?>"/></div>
	<div class="name"><?=$row["name"]?></div>
	<div class="price"><?=$row["price"]?> лв.</div>
</div>

<?php
	endwhile;
} else {
	echo "<h1>Няма продукти по посочените критерии.".$sql;
} 
$conn->close();
?>
<form method="get">
<?php
foreach($_GET as $key => $value){
if($key == "age" || $key == "gender" || $key == "price" || $key == "page"){
    echo "<input type=\"hidden\" name=\"$key\" value=\"$value\"/>";
}}?>
<input type="submit" class="myButton" name="a" value="Напред"/>
<input type="submit" class="myButton" name="a" value="Назад"/>
</form>

</div>	













