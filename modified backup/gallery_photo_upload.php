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

require_once "fnc_photo_upload.php";
require_once "fnc_general.php";
require_once "classes/Photoupload.class.php";

$file_type = null;
$photo_error = null;

//LIIGUTASIN NEED CONFIG FAILI
//$photo_file_size_limit = 1.5 * 1024 * 1024;
//$photo_name_prefix = "vp_";

$photo_file_name = null;

//$normal_photo_max_w = 800;
//$normal_photo_max_h = 450;
//$thumbnail_photo_max_w = 100;
//$thumbnail_photo_max_h = 100;

$alt = null;
$privacy = 1;

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$alt = test_input($_POST["alt_input"]);
	$privacy = filter_var($_POST["privacy_input"], FILTER_VALIDATE_INT);
	if(isset($_POST["photo_submit"])){
		//var_dump($_POST);
		//var_dump($_FILES);
		
		//KODUS: failitüübi kontroll eemaldada siit, suuruse kontroll lisada klassi funktsioonina hoopis (public function), faili nime genereerimine klassi
		
		//kontrollime, kas on sobilik
		if(isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])){
			
			//failitüüp

			//MUUTSIN
			//$file_type = check_file_type($_FILES["photo_input"]["tmp_name"]);

			//$upload = new Photoupload($_FILES["photo_input"]);
			//$upload->check_file_type($_FILES["photo_input"]["tmp_name"]);

			/* if($file_type == 0){
				$photo_error = "Valitud fail pole sobivat tüüpi";
			} */
			
			//failimahu kontroll
			//if(empty($upload->error)){
			if($_FILES["photo_input"]["size"] > $photo_file_size_limit){
				$upload->error = "Valitud fail on liiga suur";
				}
			
			
			//genereerin faili nime
			$photo_file_name = create_filename($photo_name_prefix, $file_type);
			
			
			
			if(empty($upload->error)){
				//teeme pildi väiksemaks
				//loome pikslikogumi (justkui avame PhotoShopis)
				
				//$temp_photo = create_image($_FILES["photo_input"]["tmp_name"], $file_type);

				//KLASS
				$upload = new Photoupload($_FILES["photo_input"]);
				
				//muudame pildi suurust
				//$normal_photo = resize_photo($temp_photo, $normal_photo_max_w, $normal_photo_max_h);
				
				$upload->resize_photo($normal_photo_max_w, $normal_photo_max_h);

				//$photo_error = save_photo($normal_photo, $gallery_photo_normal_folder .$photo_file_name, $file_type);
				$upload->save_photo($gallery_photo_normal_folder .$photo_file_name);
				
				//ajutine fail: $_FILES["photo_input"]["tmp_name"]
				//move_uploaded_file($_FILES["photo_input"]["tmp_name"], "photo_upload_original/" .$photo_file_name);

				//loome pisipildi
				if(empty($upload->error)){
					$upload->resize_photo_thumbnail($thumbnail_photo_max_w, $thumbnail_photo_max_h);
					$upload->save_photo($gallery_photo_thumbnail_folder .$photo_file_name);
				}
				//ajutine fail: $_FILES["photo_input"]["tmp_name"]
				//move_uploaded_file($_FILES["photo_input"]["tmp_name"], "photo_upload_original/" .$photo_file_name);
				
				//function save_photo_db($userid, $photo_file_name, $created, $alttext, $privacy, $deleted)
				if(empty($upload->error)){
					// ajutine fail: $_FILES["photo_input"]["tmp_name"]
						/* if(move_uploaded_file($_FILES["photo_input"]["tmp_name"], $gallery_photo_original_folder .$photo_file_name) == false){
							$photo_error = 1;
						} */
						$upload->move_original_photo($gallery_photo_original_folder .$photo_file_name);
					}
					
					if(empty($upload->error)){
						$upload->error = save_photo_db($photo_file_name, $alt, $privacy);
					}
					if(empty($upload->error)){
						$upload->error = "Pilt edukalt üles laetud!";
						$alt = null;
						$privacy = 1;
					} else {
						$upload->error = "Pildi üleslaadimisel tekkis tõrkeid!";
					}
					unset($upload);
			}
		}
	}//if photo_submit
}//if method=POST

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