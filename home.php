<?php
//session_start();
require_once "classes/SessionManager.class.php";
SessionManager::sessionStart("vp", 0, "/~litvmeri/vp/", "greeny.cs.tlu.ee");

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

$last_visitor = "pole teada";
if(isset($_COOKIE["lastvisitor"]) and !empty($_COOKIE["lastvisitor"])){
	$last_visitor = $_COOKIE["lastvisitor"];
}
//küpsised enne veebilehe algust
//cookie nimi, väärtus, aegumine sekundites, kataloog serveris, domeen, kas https, kas juurdepääs ainult üle veebi
//https jaoks saab teha ka nii, kui pole kindel: isset($_SERVER["HTTPS"])
setcookie("lastvisitor", $_SESSION["firstname"] ." " .$_SESSION["lastname"], (86400*7), "/~litvmeri/vp/", "greeny.cs.tlu.ee", true, true);
//cookie kustutamine:
//setcookie aegumine negatiivne time()-3000

require_once "header.php";
require_once "fnc_profile.php";
require_once "../../config_vp2022.php";

echo("Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"] ."\n <br>");
echo("Viimane külastaja: " .$last_visitor ."\n <br>");
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
	<a href="user_profile.php">Vaheta sätteid</a>
</nav>
<br>
<br>
<div class="profile_photo">
	<?php echo read_profile_photo($_SESSION["user_id"]);?>
</div>

</body>

<?php
require_once "footer.php";
?>