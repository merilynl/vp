<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>vinge veebisüsteem</title>
	<style>
		body {
			background-color: <?php echo $_SESSION["user_bg_color"];?>;
			color: <?php echo $_SESSION["user_txt_color"];?>
			
		}
	</style>
	<?php
		if(isset($style_sheets)){
			echo '<link rel="stylesheet" href="';
			echo $style_sheets;
			echo '">' ."\n";
		}
	?>
</head>
<body>
<img src="https://greeny.cs.tlu.ee/~rinde/vp_2022/vp_banner_gs.png" alt="Banner">
<h1>Vinge veebisüsteem</h1>
<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat sisu!</p>
<p>Õppetöö toimus <a href="https://www.tlu.ee" target="_blank">Tallinna Ülikoolis</a> Digitehnoloogiate instituudis.</p>
<hr>