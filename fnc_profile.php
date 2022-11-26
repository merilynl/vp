<?php 
function read_profile_photo($userid){
        $photo_html = null;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		$stmt = $conn->prepare("SELECT vp_userprofilephotos.filename FROM vp_userprofilephotos WHERE userid = ? ORDER BY created DESC");
        echo $conn->error;
        $stmt->bind_param("i", $userid);
        $stmt->bind_result($filename_from_db);
        $stmt->execute();
        if($stmt->fetch()){
			$photo_html .= '<img src="' .$GLOBALS["gallery_photo_profile_folder"] .$filename_from_db .'" alt="';
            $photo_html .= '">' ."\n";
        }
        $stmt->close();
		$conn->close();
		return $photo_html;
    }