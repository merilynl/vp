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

require_once "fnc_photo_upload.php";
require_once "fnc_general.php";
require_once "classes/Photoupload.class.php";
require_once "../../config_vp2022.php";

$file_type = null;
$photo_error = null;
$photo_file_name = null;
$alt = null;
$privacy = 1;

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$alt = test_input($_POST["alt_input"]);
	$privacy = filter_var($_POST["privacy_input"], FILTER_VALIDATE_INT);
	if(isset($_POST["photo_submit"])){		
		//kas on foto valitud
		if(isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])){
				//KLASS
				$upload = new Photoupload($_FILES["photo_input"]);
				if(empty($upload->error)){
					$upload->check_allowed_type($allowed_photo_types);
					if(empty($upload->error)){
						$upload->check_size($photo_file_size_limit);
						if(empty($upload->error)){
							$upload->create_filename($photo_name_prefix);
							$upload->resize_photo($normal_photo_max_w, $normal_photo_max_h);
							$upload->save_photo($gallery_photo_normal_folder .$upload->file_name);

							if(empty($upload->error)){
								$upload->resize_photo_thumbnail($thumbnail_photo_max_w, $thumbnail_photo_max_h);
								$upload->save_photo($gallery_photo_thumbnail_folder .$upload->file_name);
								if(empty($upload->error)){
									$upload->move_original_photo($gallery_photo_original_folder .$upload->file_name);
									if(empty($upload->error)){
										$upload->error = save_photo_db($upload->file_name, $alt, $privacy);
										if(empty($upload->error)){
											$upload->error = "Pilt edukalt üles laetud!";
											$alt = null;
											$privacy = 1;
										} else {
											$upload->error = "Pildi üleslaadimisel tekkis tõrkeid!";
										}
									}
								}
							}
						}
					}
				}	
				unset($upload);	
			}
		}
	}//if photo_submit


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
	<a href="home.php">Avalehele</a>
	<a href="?logout=1">Logi välja</a>
	
</nav>
<br>
<br>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
	<label for="photo_input">Vali pildifail</label>
	<input type="file" name="photo_input" id="photo_input">
	<br>
	<label for="alt_input">Alternatiivtekst (alt):</label>
	<input type="text" name="alt_input" id="alt_input" placeholder="alternatiivtekst ...">
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_1" value="1"<?php if($privacy == 1){echo " checked";}?>>
	<label for="privacy_input_1">Privaatne (ainult ise näen)</label>
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_2" value="2"<?php if($privacy == 2){echo " checked";}?>>
	<label for="privacy_input_2">Sisseloginud kasutajad näevad</label>
	<br>
	<input type="radio" name="privacy_input" id="privacy_input_3" value="3"<?php if($privacy == 3){echo " checked";}?>>
	<label for="privacy_input_3">Kõik kasutajad näevad</label>
	<br>
	<input type="submit" name="photo_submit" id="photo_submit" value="Lae üles">
	<br>
	<span><?php echo($photo_error);?></span>
</form>
</body>

<?php
require_once "footer.php";
?>