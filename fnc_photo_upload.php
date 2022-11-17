<?php
	require_once "../../config_vp2022.php";

	/* function check_file_type($image_file){
		$image_type = 0;
		$image_check = getimagesize($image_file);
		if($image_check !== false){
			if($image_check["mime"] == "image/jpeg"){
				$image_type = "jpg";
			}
			if($image_check["mime"] == "image/png"){
				$image_type = "png";
			}
			if($image_check["mime"] == "image/gif"){
				$image_type = "gif";
			}
		}
		return $image_type;
	} */
	
	
	function create_filename($name_prefix, $file_type){
		$timestamp = microtime(1) * 10000;
		return $name_prefix . $timestamp ."." .$file_type;
	}
	
	//lisasin klassi
/* 	function create_image($file, $file_type){
		$temp_image = null;
		if($file_type == "jpg"){
			$temp_image = imagecreatefromjpeg($file);
		}
		if($file_type == "png"){
			$temp_image = imagecreatefrompng($file);
		}
		if($file_type == "gif"){
			$temp_image = imagecreatefromgif($file);
		}
		return $temp_image;
	} */
	
	
	/* function resize_photo($temp_photo, $normal_photo_max_w, $normal_photo_max_h){
		$image_w = imagesx($temp_photo);
		$image_h = imagesy($temp_photo);
		$new_w = $normal_photo_max_w;
		$new_h = $normal_photo_max_h;
		//säilitan originaalproportsioonid
		if($image_w / $normal_photo_max_w > $image_h / $normal_photo_max_h){
			$new_h = round($image_h / ($image_w / $normal_photo_max_w));
	
		}else{
			$new_w = round($image_w / ($image_h / $normal_photo_max_h));
		}
		$temp_image = imagecreatetruecolor($new_w, $new_h);
		//teeme originaalist väiksele koopia
		imagecopyresampled($temp_image, $temp_photo, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
		return $temp_image;
	} */
	
	//KOMMENTEERI SEE PÄRAST VÄLJA
	/* function resize_photo_thumbnail($temp_photo, $w, $h){
		$image_w = imagesx($temp_photo);
		$image_h = imagesy($temp_photo);
		$new_w = $w;
		$new_h = $h;
		//uued muutujad, mis on seotud proportsioonide muutmisega, kärpimisega (crop)
		$cut_x = 0;
		$cut_y = 0;
		$cut_size_w = $image_w;
		$cut_size_h = $image_h;

		if($w == $h){ //ruudukujuline
			if($image_w > $image_h){
				$cut_size_w = $image_h;
				$cut_x = round(($image_w - $cut_size_w) / 2);
			} else {
				$cut_size_h = $image_w;
				$cut_y = round(($image_h - $cut_size_h) / 2);
			}
		} else {
			if($image_w > $image_h){
				$cut_size_w = $image_h;
				$cut_x = round(($image_w - $cut_size_w) / 2);
			} else {
				$cut_size_h = $image_w;
				$cut_y = round(($image_h - $cut_size_h) / 2);
			}
		} 
		$temp_image = imagecreatetruecolor($new_w, $new_h);
		//säilitame vajadusel läbipaistvuse (png ja gif piltide jaoks
		imagesavealpha($temp_image, true);
		$trans_color = imagecolorallocatealpha($temp_image, 0, 0, 0, 127);
		imagefill($temp_image, 0, 0, $trans_color);
		//teeme originaalist väiksele koopia
		imagecopyresampled($temp_image, $temp_photo, 0, 0, $cut_x, $cut_y, $new_w, $new_h, $cut_size_w, $cut_size_h);
		return $temp_image;
	}  
		$image_w = imagesx($temp_photo);
		$image_h = imagesy($temp_photo);
		$new_w_thumbnail = $thumbnail_photo_max_w;
		$new_h_thumbnail = $thumbnail_photo_max_h;
		//säilitan originaalproportsioonid
		if($image_w / $thumbnail_photo_max_w > $image_h / $thumbnail_photo_max_h){
			$new_h_thumbnail = round($image_h / ($image_w / $thumbnail_photo_max_w));
	
		}else{
			$new_w_thumbnail = round($image_w / ($image_h / $thumbnail_photo_max_h));
		}
		$temp_image = imagecreatetruecolor($new_w_thumbnail, $new_h_thumbnail);
		//teeme originaalist väiksele koopia
		imagecopyresampled($temp_image, $temp_photo, 0, 0, 0, 0,$new_w_thumbnail, $new_h_thumbnail, $image_w, $image_h);
		return $temp_image; */
	
		
	/* function save_photo($image, $target, $file_type){
		$error = null;
		if($file_type == "jpg"){
			if(imagejpeg($image, $target, 95) == false){
				$error = 1;
			}
		}
		if($file_type == "png"){
			if(imagepng($image, $target, 6) == false){
				$error = 1;
			}
		}
		if($file_type == "gif"){
			if(imagegif($image, $target) == false){
				$error = 1;
			}
		}
		return $error;
		
	} */ 
	
	function save_photo_db($file_name, $alt, $privacy){
		$notice = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		$stmt = $conn->prepare("INSERT INTO vp_photos_2 (userid, filename, alttext, privacy) VALUES (?, ?, ?, ?)");
		echo $conn->error;
		$stmt->bind_param("issi", $_SESSION["user_id"], $file_name, $alt, $privacy);
		if($stmt->execute() == false){
		  $notice = 1;
		}
		$stmt->close();
		$conn->close();
		return $notice;
	}
?>