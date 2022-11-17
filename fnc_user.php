<?php

	require_once "../../config_vp2022.php";
	//kõik muutujad mis deklareeritud väljaspool funktsiooni, on globaalsed muutujad ja kättesaadavad massiivist $GLOBALS
	function sign_up($first_name, $last_name, $birth_date, $gender, $email, $password) {
		$notice = null;
		//salvestame kasutaja
		//loon andmebaasiga ühenduse
		//server, kasutaja, parool, andmebaas
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määran suhtlemisel kasutatava kooditabeli
		$conn->set_charset("utf8");
		echo $conn->error;
		$stmt = $conn->prepare("SELECT id, firstname, lastname FROM vp_users_2 WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		if($stmt->fetch()){
			$notice = "Error, sisestatud emailiga on juba seotud teine kasutaja";
			//sulgeme käsu
			$stmt->close();
			//andmebaasiühenduse kinni
			$conn->close();
			return $notice;
		} else{
			$stmt = $conn->prepare("INSERT INTO vp_users_2 (firstname, lastname, birthdate, gender, email, password) values(?,?,?,?,?,?)");
			//krüpteerime parooli
			$pwd_hash = password_hash($password, PASSWORD_DEFAULT);
			$stmt->bind_param("sssiss", $first_name, $last_name, $birth_date, $gender, $email, $pwd_hash);
							
			if($stmt->execute()){
				$notice = "Uus kasutaja on salvestatud.";
			} else {
				$notice = "error" .$stmt->error;
			}
			//sulgeme käsu
			$stmt->close();
			//andmebaasiühenduse kinni
			$conn->close();
			return $notice;
		}


	}
	
	function sign_in($email, $password){
		    //echo("test1");
            if(isset($password) and !empty($password) and isset($email) and !empty($email)){
                //$password = password_hash($_POST["password_input"], PASSWORD_DEFAULT);
        //$email = $_POST["email_input"];
		//$password = $_POST["password_input"];
                    //echo("test2");
                    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
                    //määran suhtlemisel kasutatava kooditabeli
                    $conn->set_charset("utf8");
                    echo $conn->error;
                    //valmistame ette andmete saamise SQL käsu
                    $stmt = $conn->prepare("SELECT password FROM vp_users_2 WHERE email = ?");
                    //-id, 
                    $stmt->bind_param("s", $email);
                    //echo("test2");
                    //seome saadavad andmed muutujatega
                    $stmt->bind_result($password_from_db);
					//-$id_from_db, 
                    //täidame käsu
                    $stmt->execute();
					//echo ("Test enne passkontrolli");
                    //kui saan ühe kirje
                    if($stmt->fetch()){
                        //echo("test4");
                        //mis selle ühe kirjega teha
                        if(password_verify($password, $password_from_db)){
							//echo("test1");
							//$id_from_db = " test";
							$stmt->close();
							// $conn->close();

							// $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
							// //määran suhtlemisel kasutatava kooditabeli
							// $conn->set_charset("utf8");
							// echo $conn->error;
							//echo($email);

							//SIIN ON VIGA KUSKIL - if stmt fetch oli puudu
							$stmt = $conn->prepare("SELECT id, firstname, lastname FROM vp_users_2 WHERE email = ?");
							echo("Test enne sessioonimuutujaid");

							$stmt->bind_param("s", $email);
							$stmt->bind_result($id_from_db, $first_name_from_db, $last_name_from_db);
							$stmt->execute();

							echo($id_from_db);
							//määran sessioonimuutujad
							if($stmt->fetch()){
								$_SESSION["user_id"] = $id_from_db;
								$_SESSION["firstname"] = $first_name_from_db;
								$_SESSION["lastname"] = $last_name_from_db;
								//lisame lehe välimust
								$stmt->close();
								$stmt = $conn->prepare("SELECT bgcolor, txtcolor FROM vp_userprofiles WHERE userid = ?");
								echo $conn->error;
								$stmt->bind_param("i", $_SESSION["user_id"]);
								$stmt->bind_result($bgcolor, $txtcolor);
								$stmt->execute();

								if($stmt->fetch()){
									$_SESSION["user_bg_color"] = $bgcolor;
									$_SESSION["user_txt_color"] = $txtcolor;
								} else {
									$_SESSION["user_bg_color"] = "#DDDDDD";
									$_SESSION["user_txt_color"] = "#333333";
								}
								header("Location: home.php");
							} else {
								$login_error = "Kasutajatunnus või salasõna oli vale";
							}
							
							$stmt->close();
                            $conn->close();
							exit();
                            

                        }else{
						$login_error = "sisselogimise ebaõnnestus, kasutajatunnus või salasõna oli ebakorrektne!";
						$stmt->close();
						//andmebaasiühenduse kinni
						$conn->close();
						}
                    }else{
                      $login_error = "sisselogimise ebaõnnestus, kasutajatunnus või salasõna oli ebakorrektne!";
                      $stmt->close();
                      //andmebaasiühenduse kinni
                      $conn->close();
                    }

        } else {
			//echo("test3");
			$login_error = "sisselogimise ebaõnnestus, kasutajatunnus või salasõna oli sisestamata!";
            }
	return $login_error;
	}
	
?>
				
				