<?php
	require_once "../../config_vp2022.php";

    $email = null;
    $password = null;
    $email_error = null;
    $password_error = null;


    //if($_SERVER["REQUEST_METHOD"] == "POST"){
		if (isset($_POST["user_data_submit"]) and !empty($_POST["user_data_submit"])){

            //echo("test1");
            if(isset($_POST["password_input"]) and !empty($_POST["password_input"]) and isset($_POST["email_input"]) and !empty($_POST["email_input"])){
                //$password = password_hash($_POST["password_input"], PASSWORD_DEFAULT);
                $password = $_POST["password_input"];
                $email = $_POST["email_input"];
                    //echo("test2");
                    $conn = new mysqli($server_host, $server_user_name, $server_password, $database);
                    //määran suhtlemisel kasutatava kooditabeli
                    $conn->set_charset("utf8");
                    echo $conn->error;
                    //valmistame ette andmete saamise SQL käsu
                    $stmt = $conn->prepare("SELECT password FROM vp_users_2 WHERE email = ?");
                    
                    $stmt->bind_param("s", $email);
                    //echo($email);
                    //echo("test2");
                    //seome saadavad andmed muutujatega
                    $stmt->bind_result($password_from_db);
                    //täidame käsu
                    $stmt->execute();
    
                    //kui saan ühe kirje
                    if($stmt->fetch()){
                        //echo("test4");
                        //mis selle ühe kirjega teha
                        if(password_verify($password, $password_from_db)){
                            header("Location: home.php");
                            $stmt->close();
                            //andmebaasiühenduse kinni
                            $conn->close();

                        }
                    }else{
                      echo("sisselogimise ebaõnnestus, kasutajatunnus või salasõna oli ebakorrektne!");
                      $stmt->close();
                      //andmebaasiühenduse kinni
                      $conn->close();
                    }

            } else {
                //echo("test3");
            }
    }
?>

<!DOCTYPE html>
<html lang="et">
  <head>
    <meta charset="utf-8">
	
  </head>
  <body>
	
	<hr>
    <h2>Logi sisse</h2>
		
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
	  <br>
	  <label for="email_input">E-mail (kasutajatunnus):</label><br>
	  <input type="email" name="email_input" id="email_input" value=" <?php echo $email; ?>"><span><?php echo $email_error; ?></span><br>
      <br>
	  <label for="password_input">Salasõna (min 8 tähemärki):</label><br>
	  <input name="password_input" id="password_input" type="password"><span><?php echo $password_error; ?></span><br>
      <br>
	  <input name="user_data_submit" type="submit" value="Login">
	</form>
	<hr>
    
  </body>
</html>