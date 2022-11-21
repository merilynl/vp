<?php
    session_start();
    require_once "../../config_vp2022.php";

    
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

    $title = null;
    $title_error = null;
    $year = null;
    $year_error = null;
    $duration = null;
    $duration_error = null;
    $genre = null;
    $genre_error = null;
    $studio = null;
    $studio_error = null;
    $director = null;
    $director_error = null;

	if(isset($_POST["title_input"])){
		if(isset($_POST["title_input"]) and !empty($_POST["title_input"])){
			$title = $_POST["title_input"];
		} else {
			$title_error = "Pealkiri jäi kirjutamata!";
            echo($title_error);
		}
    }
    if(isset($_POST["year_input"])){
        if(isset($_POST["year_input"]) and !empty($_POST["year_input"])){
            $year = $_POST["year_input"];
        } else {
            $year_error = "Aasta jäi kirjutamata!";
            echo($year_error);
        }		
    }
	if(isset($_POST["duration_input"])){
		if(isset($_POST["duration_input"]) and !empty($_POST["duration_input"])){
			$duration = $_POST["duration_input"];
		} else {
			$duration_error = "Filmi kestus jäi kirjutamata!";
            echo($duration_error);
		}
    }    
    if(isset($_POST["genre_input"])){
        if(isset($_POST["genre_input"]) and !empty($_POST["genre_input"])){
            $genre = $_POST["genre_input"];
        } else {
            $genre_error = "Zanr jäi kirjutamata!";
            echo($genre_error);
        }
    }
    if(isset($_POST["studio_input"])){
		if(isset($_POST["studio_input"]) and !empty($_POST["studio_input"])){
			$studio = $_POST["studio_input"];
		} else {
			$studio_error = "Stuudio jäi kirjutamata!";
            echo($studio_error);
		}
    }
    if(isset($_POST["director_input"])){
        if(isset($_POST["director_input"]) and !empty($_POST["director_input"])){
            $director = $_POST["director_input"];
        } else {
            $director_error = "Lavastaja jäi kirjutamata!";
            echo($director_error);
        }
    }    
	if(empty($title_error) and empty($year_error) and empty($duration_error) and empty($genre_error) and empty($studio_error) and empty($director_error)){
		if (isset($title) and isset($year) and isset($duration) and isset($genre) and isset($studio) and isset($director)){
		    //loon andmebaasiga ühenduse
		    //server, kasutaja, parool, andmebaas
		    $conn = new mysqli($server_host, $server_user_name, $server_password, $database);
		    //määran suhtklemisel kasuatatava kooditabeli
		    $conn->set_charset("utf8");
		    //valmistame ette andmete saatmise SQL käsu
		    $stmt = $conn->prepare("INSERT INTO film (pealkiri, aasta, kestus, zanr, tootja, lavastaja) values(?,?,?,?,?,?)");
		    echo $conn->error;
		    //seome SQL käsu õigete andmetega
		    //andmetüübid  i - integer   d - decimal    s - string
		    $stmt->bind_param("siisss", $title, $year, $duration, $genre, $studio, $director);
		    if($stmt->execute()){
                $title = null;
                $year = null;
                $duration = null;
                $genre = null;
                $studio = null;
                $director = null;

		    }
		    //sulgeme käsu
		    $stmt->close();
		    //andmebaasiühenduse kinni
		    $conn->close();
		}
    }
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Filmide lisamine</title>
</head>

<body>

<form method="POST">
        <label for="title_input">Filmi pealkiri</label>
        <input type="text" name="title_input" id="title_input" placeholder="filmi pealkiri">
        <br>
        <br>
        <label for="year_input">Valmimisaasta</label>
        <input type="number" name="year_input" id="year_input" min="1912" max="2022">
        <br>
        <br>
        <label for="duration_input">Kestus</label>
        <input type="number" name="duration_input" id="duration_input" min="1" value="60" max="600">
        <br>
        <br>
        <label for="genre_input">Filmi žanr</label>
        <input type="text" name="genre_input" id="genre_input" placeholder="žanr">
        <br>
        <br>
        <label for="studio_input">Filmi tootja</label>
        <input type="text" name="studio_input" id="studio_input" placeholder="filmi tootja">
        <br>
        <br>
        <label for="director_input">Filmi režissöör</label>
        <input type="text" name="director_input" id="director_input" placeholder="filmi režissöör">
        <br>
        <br>
        <input type="submit" name="film_submit" value="Salvesta">
</form>
<a href="?logout=1">Logi välja</a>
</body>
</html>