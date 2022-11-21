<?php
//pealkiri, aasta, kestus, zanr, tootja, lavastaja
session_start();
require_once "../../config_vp2022.php";

//kontrollin, kas oleme sisse loginud
if(!isset($_SESSION["user_id"])){
	header("location: page.php");
	exit();
}

//logime välja
if(isset($_GET["logout"])){
	session_destroy();
	header("location: page.php");
	exit();
}
?>

<!-- 
$title = null;
$year = null;
$duration = null;
$genre = null;
$studio = null;
$director = null; 
-->

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Filmid andmebaasis</title>
</head>

<body>

<?php 
   //loon andmebaasiga ühenduse
   //server, kasutaja, parool, andmebaas
   $dbconnect = new mysqli($server_host, $server_user_name, $server_password, $database);
   //määran suhtklemisel kasuatatava kooditabeli
   $dbconnect->set_charset("utf8");
?>

<table border="1" align="center">
<tr>
  <td>Pealkiri</td>
  <td>Aasta</td>
  <td>Kestus</td>
  <td>Zanr</td>
  <td>Tootja</td>
  <td>Lavastaja</td>
</tr>
<br>
<a href="?logout=1">Logi välja</a>

<?php

$query = mysqli_query($dbconnect, "SELECT * FROM film")
   or die (mysqli_error($dbconnect));

while ($row = mysqli_fetch_array($query)) {
  echo
   "<tr>
    <td>{$row['pealkiri']}</td>
    <td>{$row['aasta']}</td>
    <td>{$row['kestus']}</td>
    <td>{$row['zanr']}</td>
    <td>{$row['tootja']}</td>
    <td>{$row['lavastaja']}</td>
   </tr>";

}

?>

</table>
</body>
</html>
