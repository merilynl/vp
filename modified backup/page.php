<?php
	session_start();
	require_once "../../config_vp2022.php";
	require_once "fnc_user.php";
	require_once "fnc_gallery.php";
	require_once "classes/Example.class.php";
	//echo $server_host;
	
	
/* 	klassi demo
	$our_variable = new Example(9);
	$my_variable = new Example(4);
	echo $our_variable->public_value;
	$my_variable->add();
	unset($our_variable);
	unset($my_variable);
	echo $our_variable->public_value; */
	

	$author_name = "merx";
	$full_time_now = date("d.m.Y H:i:s");
	$now = DateTime::createFromFormat("U.u", microtime(true));
	$weekday_now = date("N");
	//echo $weekday_now;
	$weekdaynames_et = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	//echo $weekdaynames_et[$weekday_now-1];
	$hours_now = date("H");
	//echo $hours_now;
	$part_of_day = "suvaline päeva osa";
	$sayings_et = ["Harjutamine teeb meistriks.", "Tänasida toimetusi ära viska homse varna, ülehomme on ka päev.", "Lõpp hea, kõik hea.", "Üheksa korda mõõda, üks kord lõika.", "Iga algus on raske."];
	// <  >  >=  <=  ==  !=
	
	//reede
	if ($weekday_now == 5) {
		$part_of_week = "reede";
		if ($hours_now < 7){
			$part_of_day = "tuduaeg";
			}
		if ($hours_now == 7){
			$part_of_day = "ärkamisaeg";
			}
		if ($hours_now >= 8) {
			$part_of_day = "puhkeaeg";
			}
	}
	//laupäev
	if ($weekday_now == 6) {
		$part_of_week = "laupäev";
		if ($hours_now < 7){
			$part_of_day = "tuduaeg";
			}
		if ($hours_now == 7){
			$part_of_day = "ärkamisaeg";
			}
		if ($hours_now >= 8) {
			$part_of_day = "puhkeaeg";
			}
	}
	//pühapäev
	if ($weekday_now == 7) {
		$part_of_week = "pühapäev";
		if ($hours_now < 7){
			$part_of_day = "tuduaeg";
			}
		if ($hours_now == 7){
			$part_of_day = "ärkamisaeg";
			}
		if ($hours_now >= 8) {
			$part_of_day = "puhkeaeg";
			}
	}
	//tööpäevad	
	if ($weekday_now < 5) {
		$part_of_week = "tööpäev";
		if ($hours_now < 7) {
			$part_of_day = "tuduaeg";
		}
		if ($hours_now == 7){
			$part_of_day = "ärkamisaeg";
			}
		if ($hours_now >= 8 and $hours_now < 18) {
			$part_of_day = "koolipäev";
		}
		if ($hours_now >= 18 and $hours_now < 24) {
			$part_of_day = "puhkeaeg";
		}
	}
	//echo $part_of_week;
	//echo $part_of_day;
	//  and  or
	
	//uurime semestri kestmist
	$semester_begin = new DateTime("2022-9-5");
	$semester_end = new DateTime("2022-12-18");
	$semester_duration = $semester_begin->diff($semester_end);
	$semester_duration_days = $semester_duration->format("%r%a");
	$from_semester_begin = $semester_begin->diff(new DateTime("now"));
	$from_semester_begin_days = $from_semester_begin->format("%r%a");
	
	//juhuslik arv
	//küsin massiivi aka array pikkust
	//echo count($weekdaynames_et);
	//echo $weekdaynames_et[mt_rand(0, count($weekdaynames_et) -1)];
	
	//juhuslik foto
	$photo_dir = "Photos";
	//loen kataloogi sisu
	$all_files = array_slice(scandir($photo_dir), 2);
	//kontrollin kas on ikka foto
	$allowed_photo_types = ["image/jpeg", "image/png"];
	
	//tsükkel
	//muutuja väärtuse suurendamine $muutuja = $muutuja + 5 või $muutuja += 5
	//kui on vaja liita 1, siis tehakse $muutuja ++
	//esiteks algväärtus, siis lõppväärtus ning siis kuidas jõutakse sinna
	/*for($i = 0; $i < count($all_files); $i ++){
		echo $all_files[$i];
	}*/
	//tähistan iga all_files elemendi filename muutujana
	$photo_files = [];
	foreach($all_files as $filename){
		//echo $filename;
		$file_info = getimagesize($photo_dir ."/" .$filename);
		//kas on lubatud tüüpide nimekirjas
		if(isset($file_info["mime"])){
			if(in_array($file_info["mime"], $allowed_photo_types)){
				array_push($photo_files, $filename);
			}//if in_array
		}//if isset
	}//foreach
	
	//var_dump($all_files);
	//loon muutuja, mis valib hiljem suvalise pildi
	$photo_number = mt_rand(0, count($photo_files) - 1);
	
	//vaatame, mida vormis sisestati
	//var_dump($_POST);
	//echo $_POST["todays_adjective_input"];
	// !empty käskluses ! on eitus
	$todays_adjective = "pole midagi sisestatud";
	if(isset($_POST["todays_adjective_input"]) and !empty($_POST["todays_adjective_input"])){
		$todays_adjective = $_POST["todays_adjective_input"];
	}

	//echo $_POST["photo_select"];
	//if(isset($_POST["photo_select"]) and !empty($_POST["photo_select"])){
		if(isset($_POST["photo_select"]) and $_POST["photo_select"] >= 0){
			//echo "Valiti pilt nr:" .$_POST["photo_select"];
			$photo_number = $_POST["photo_select"]; 
		}

	//loome rippmenüü valikud
	//	<option value="0">tln_56.JPG</option>
	//	<option value="1">tln_137.JPG</option>
	$select_html = '<option value="" selected disabled>Vali pilt</option>';
	for($i = 0;$i < count($photo_files); $i ++){
		$select_html .= '<option value="' .$i .'"';
		if($i == $photo_number){
			$select_html .= " selected";
		}
		$select_html .= ">";
		$select_html .= $photo_files[$i];
		$select_html .= "</option> \n";
	}

	// <img src="kataloog/fail" alt="tekst">
	$photo_html = '<img src="' .$photo_dir ."/" .$photo_files[$photo_number] .'"';
	$photo_html .= ' alt="Tallinna pilt">';

	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	//if (isset($_POST["photo_select"]) and $_POST["photo_select"] >= 0){
	//if (isset($_POST["photo_select"]) and !empty($_POST["photo_select"])){
	//	echo $_POST["photo_select"];
	//}
	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


	$comment_error = null;
	$grade = 7;
	//kas klikiti päeva kommentaari nuppu
	if(isset($_POST["comment_submit"])){
		if(isset($_POST["comment_input"]) and !empty($_POST["comment_input"])){
			$comment = $_POST["comment_input"];
		} else {
			$comment_error = "Kommentaar jäi kirjutamata!";
		}
		$grade = $_POST["grade_input"];
		
		if(empty($comment_error)){
		
			//loon andmebaasiga ühenduse
			//server, kasutaja, parool, andmebaas
			$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
			//määran suhtklemisel kasuatatava kooditabeli
			$conn->set_charset("utf8");
			//valmistame ette andmete saatmise SQL käsu
			$stmt = $conn->prepare("INSERT INTO vp_daycomment_2 (comment, grade) values(?,?)");
			echo $conn->error;
			//seome SQL käsu õigete andmetega
			//andmetüübid  i - integer   d - decimal    s - string
			$stmt->bind_param("si", $comment, $grade);
			if($stmt->execute()){
				$grade = 7;
				$comment = null;
			}
			//sulgeme käsu
			$stmt->close();
			//andmebaasiühenduse kinni
			$conn->close();
		}
	}
//!!!!!!!!!!!!!!!!!!!!sisselogimine!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!	

    $email = null;
    $password = null;
    $email_error = null;
    $password_error = null;
	$login_error = null;
	//echo("Enne logini");

    //if($_SERVER["REQUEST_METHOD"] == "POST"){
	if (isset($_POST["user_data_submit"]) and !empty($_POST["user_data_submit"])){
		//login sisse
		//echo("test login");
		$login_error = sign_in($_POST["email_input"], $_POST["password_input"]);
    }
	
	$privacy = 3;
	$limit = 1;
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title><?php echo $author_name;?>kekw</title>
</head>

<body>
	<img src="https://greeny.cs.tlu.ee/~rinde/vp_2022/vp_banner_gs.png" alt="Banner">
	<h1>Veebilehe tegi Merilyn</h1>
	<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimus <a href="https://www.tlu.ee" target="_blank">Tallinna Ülikoolis</a></p>
	<a href="https://www.tlu.ee" target="_blank"><img src="pics/tlu_42.jpg" alt="Tallinna Ülikooli õppehoone"></a>
	<p>Praegu on <?php echo $part_of_day;?> ja <?php echo $part_of_week;?>.</p>
	<p>Semestri pikkus on <?php echo $semester_duration_days;?> päeva.</p>
	<p>See on kestnud juba <?php echo $from_semester_begin_days;?> päeva.</p>
	<?php echo $sayings_et[mt_rand(0, count($sayings_et) -1)];?>

	<form method="POST">
	<label for="comment_input">Kommentaar tänase päeva kohta (140 tähemärki)</label>
	<br>
	<textarea id="comment_input" name="comment_input" cols="35" rows="4" 
	placeholder="kommentaar"></textarea>
	<br>
	<label for="grade_input">Hinne tänasele päevale (0-10)</label>
	<input type="number" id="grade_input" name="grade_input" min="0" max="10" step="1"
	value="<?php echo $grade?>">
	 
	<br>
	<input type="submit" id="comment_submit" name="comment_submit" value="Salvesta">
	<span> <?php echo $comment_error ?></span>
	</form>


	<form method="POST">
		<input type="text" id="todays_adjective_input" name="todays_adjective_input" placeholder="Kirjuta siia omadussõna tänase päeva kohta: ">
		<input type="submit" id="todays_adjective_submit" name="todays_adjective_submit" value="Saada omadussõna!">
	</form>
	<p>Omadussõna tänase kohta: <?php echo $todays_adjective; ?></p>
	
<hr>
<form method="POST">
	<select id="photo_select" name="photo_select">
		<?php echo $select_html ?>
	</select>
	<input type="submit" id="photo_submit" name="photo_submit" value="Määra foto:">
</form>
	<?php echo $photo_html;?>
<hr>

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
	  <input name="user_data_submit" type="submit" value="Login"><span><strong><?php echo $login_error; ?></strong></span>
	</form>
	
	<p> Või <a href="add_user.php"> loo endale kasutaja </a> </p>
	

<?php
echo read_public_photo_page($privacy, $limit);
?>

<?php
require_once "footer.php";
print "lehe avamise hetk ";
echo $now->format("m-d-Y H:i:s.u");
?>

