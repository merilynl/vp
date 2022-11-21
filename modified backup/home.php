<?php
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


echo("Sisse logitud: " .$_SESSION["firstname"] ." " .$_SESSION["lastname"])
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

</body>

<?php
require_once "footer.php";
?>