<?php
//LISA TEST INPUT KA 14.11.2022!
session_start();
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

require_once "header.php";
require_once "../../config_vp2022.php";


echo("Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"])
?>

<?php
	$settings_error = null;
	if(isset($_POST["bg_color_input"]) and isset($_POST["txt_color_input"])){
		if(!empty($_POST["bg_color_input"]) and !empty($_POST["txt_color_input"])){
			$_SESSION["user_bg_color"] = $_POST["bg_color_input"];
			$_SESSION["user_txt_color"] = $_POST["txt_color_input"];
			$description = $_POST["user_description"];
		} else {
			$settings_error = "Värvid jäid valimata!";
		}
		
		
		if(empty($settings_error)){
			$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
			$conn->set_charset("utf8");
			$stmt = $conn->prepare("SELECT description, bgcolor, txtcolor FROM vp_userprofiles WHERE userid = ?");
			echo $conn->error;
			$stmt->bind_param("i", $_SESSION["user_id"]);
			//$stmt->bind_result($description, $bgcolor, $txtcolor);
			$stmt->execute();
			if($stmt->fetch()){
				$stmt->close();
				$stmt = $conn->prepare("UPDATE vp_userprofiles SET description = ?, bgcolor = ?, txtcolor = ? WHERE userid = ?");
				echo $conn->error;
				$stmt->bind_param("sssi", $description, $_SESSION["user_bg_color"], $_SESSION["user_txt_color"], $_SESSION["user_id"]);
				$stmt->execute();
				
			}else{
				$stmt->close();
				$stmt = $conn->prepare("INSERT INTO vp_userprofiles (userid, description, bgcolor, txtcolor) VALUES (?, ?, ?, ?)");
				echo $conn->error;
				$stmt->bind_param("isss", $_SESSION["user_id"], $description, $_SESSION["user_bg_color"], $_SESSION["user_txt_color"]);
				$stmt->execute();
			}
			$stmt->close();
			$conn->close();
		}
	}
?>
<head>
	<style>
		nav {
  		background-color: grey;
		float: left;
  		padding: 12px;
		width: 98.8%;
		}
		nav a {
		margin-left: 20px;
		}
	</style>
</head>

<body>
<nav>
	<a href="filmide_sissekanne.php">Filmide sissekanne</a>
  	<a href="filmid.php">Filmid</a>
	<a href="gallery_photo_upload.php">Fotode üleslaadimine</a>
	<a href="gallery_public.php">Avalike fotode galerii</a>
	<a href="gallery_own.php">Oma fotode galerii</a>
	<a href="?logout=1">Logi välja</a>
</nav>
<br>
<br>

<form method="POST">
	<label for="color">Vali taustavärv</label>
	<br>
	<input type="color" name="bg_color_input">
	<br>
	<label for="color">Vali tekstivärv</label>
	<br>
	<input type="color" name="txt_color_input">
	<br>
	<textarea name="user_description" rows="5" cols="51" placeholder="Minu lühikirjeldus"></textarea>
	<br>
	<input type="submit" id="user_settings" name="user_settings" value="Salvesta sätted">
</form>
</body>

<?php
require_once "footer.php";
?>