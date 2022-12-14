<?php
//LISA TEST INPUT KA 14.11.2022!
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

require_once "header.php";
require_once "../../config_vp2022.php";
require_once "classes/Photoupload.class.php";
require_once "fnc_photo_upload.php";
require_once "fnc_profile.php";


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


	//$gallery_photo_profile_folder = "photo_upload_profile/";
	//$gallery_photo_original_profile_folder = "photo_upload_profile_original/";
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if(isset($_POST["user_settings"])){		
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
								$upload->resize_photo_thumbnail($profile_photo_max_w, $profile_photo_max_h);
								$upload->save_photo($gallery_photo_profile_folder .$upload->file_name);
								if(empty($upload->error)){
									$upload->move_original_photo($gallery_photo_original_profile_folder .$upload->file_name);
									if(empty($upload->error)){
										$upload->error = save_photo_db_profile($upload->file_name);
										if(empty($upload->error)){
											$upload->error = "Pilt edukalt üles laetud!";
										} else {
											$upload->error = "Pildi üleslaadimisel tekkis tõrkeid!";
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
	<a href="home.php">Avalehele</a>
	<a href="?logout=1">Logi välja</a>
</nav>
<br>
<div class="profile_photo">
	<?php echo read_profile_photo($_SESSION["user_id"]);?>
</div>
<br>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
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
	<label for="photo_input">Vali pildifail</label>
	<input type="file" name="photo_input" id="photo_input">
	<br>
	<input type="submit" id="user_settings" name="user_settings" value="Salvesta sätted">
</form>
</body>

<?php
require_once "footer.php";
?>
