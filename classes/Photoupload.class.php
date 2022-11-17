<?php
	class Photoupload{
		private $photo_to_upload;
		private $file_type; //alguses saadame klassile, hiljem uurib klass selle ise välja
		private $temp_photo;
		private $new_temp_photo;
		public $error = null;
		
		function __construct($photo){
			$this->photo_to_upload =$photo;
			$this->check_file_type;
			if(empty($this->error)){
				$this->temp_photo = $this->create_image($this->photo_to_upload["tmp_name"], $this->file_type);
			}
		}
		
		function __destruct(){
			@imagedestroy($this->temp_photo);
			@imagedestroy($this->new_temp_photo);
		}
		
		private function check_file_type(){
			$image_check = getimagesize($this->photo_to_upload["tmp_name"]);
			if($image_check !== false){
				if($image_check["mime"] == "image/jpeg"){
					$this->image_type = "jpg";
				}
				if($image_check["mime"] == "image/png"){
					$this->image_type = "png";
				}
				if($image_check["mime"] == "image/gif"){
					$this->image_type = "gif";
				}
			} else {
				$this->error = "See pole pildifail";
			}
			return $this->error;
		}
		
		private function create_image($file, $file_type){
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
		}
		
		public function resize_photo($normal_photo_max_w, $normal_photo_max_h){
			$image_w = imagesx($this->temp_photo);
			$image_h = imagesy($this->temp_photo);
			$new_w = $normal_photo_max_w;
			$new_h = $normal_photo_max_h;
			//säilitan originaalproportsioonid
			if($image_w / $normal_photo_max_w > $image_h / $normal_photo_max_h){
				$new_h = round($image_h / ($image_w / $normal_photo_max_w));
		
			}else{
				$new_w = round($image_w / ($image_h / $normal_photo_max_h));
			}
			$this->new_temp_photo = imagecreatetruecolor($new_w, $new_h);
		//LISA SIIA LÄBIPAISTVUSE OSA
			//teeme originaalist väiksele koopia
			
			//imagecopyresampled($this->new_temp_photo, $this->temp_photo, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
			
			//säilitame vajadusel läbipaistvuse (png ja gif piltide jaoks
			imagesavealpha($this->new_temp_photo, true);
			$trans_color = imagecolorallocatealpha($this->new_temp_photo, 0, 0, 0, 127);
			imagefill($this->new_temp_photo, 0, 0, $trans_color);
			//teeme originaalist väiksele koopia
			imagecopyresampled($this->new_temp_photo, $this->temp_photo, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
			
	}
	    //TÄIDA SEE ISE
		public function resize_photo_thumbnail($w, $h){
			$image_w = imagesx($this->temp_photo);
			$image_h = imagesy($this->temp_photo);
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
			$this->new_temp_photo = imagecreatetruecolor($new_w, $new_h);
			//säilitame vajadusel läbipaistvuse (png ja gif piltide jaoks
			imagesavealpha($this->new_temp_photo, true);
			$trans_color = imagecolorallocatealpha($this->new_temp_photo, 0, 0, 0, 127);
			imagefill($this->new_temp_photo, 0, 0, $trans_color);
			//teeme originaalist väiksele koopia
			imagecopyresampled($this->new_temp_photo, $this->temp_photo, 0, 0, $cut_x, $cut_y, $new_w, $new_h, $cut_size_w, $cut_size_h);
			
	}			
	
		public function save_photo($target){
			$this->error = null;
			if($this->file_type == "jpg"){
				if(imagejpeg($this->new_temp_photo, $target, 95) == false){
					$this->error = "Pildifaili salvestamine ebaõnnestus";
				}
			}
			if($this->file_type == "png"){
				if(imagepng($this->new_temp_photo, $target, 6) == false){
					$this->error = "Pildifaili salvestamine ebaõnnestus";
				}
			}
			if($this->file_type == "gif"){
				if(imagegif($this->new_temp_photo, $target) == false){
					$this->error = "Pildifaili salvestamine ebaõnnestus";
				}
			}
			
			//kustutan pildi, vabastan mälu
			imagedestroy($this->new_temp_photo);
			return $this->error;
			
		}
		
		public function move_original_photo($target){
			$this->error = null;
			if(move_uploaded_file($this->photo_to_upload["tmp_name"], $target) == false){
				$this->error = "Originaalfaili salvestamine ebaõnnestus";
			}
			return $this->error;
		}
	}//class end