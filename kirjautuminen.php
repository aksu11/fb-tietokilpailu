<?php
	session_set_cookie_params(3600);
	session_start();
	include 'yhteys.php';
	
	$id = $_POST['id'];
	$_SESSION['id'] = $_POST['id'];
	$_SESSION['nimi'] = $_POST['nimi'];
	$_SESSION['kuva'] = $_POST['kuva'];
	if($_SESSION['kuva'] == "" || $_SESSION['kuva'] == null) $_SESSION['kuva'] = "unknown.jpg";

	$stmt = $db->query("SELECT * FROM fbkayttajat WHERE id = '$id'");
	$rivit = $stmt->rowCount();

	if($rivit == 0){
		$stmt = $db->prepare("INSERT INTO fbkayttajat (id, nimi, kuva, liittynyt) VALUES (?, ?, ?, now())");
		$stmt->execute(array($_SESSION['id'], $_SESSION['nimi'], $_SESSION['kuva']));
		lataaKysymykset($_SESSION['id'], $db);
	} else {
		$stmt = $db->prepare("UPDATE fbkayttajat SET nimi=?, kuva=? WHERE id=?");
		$stmt->execute(array($_SESSION['nimi'], $_SESSION['kuva'], $_SESSION['id']));
	}

	$_SESSION['kirjautunut'] = "ok";
	echo "kirjautunut";
	exit();

	function lataaKysymykset($tunnus, $db){

		$taso1 = array();
		$taso2 = array();
		$taso3 = array();

		$stmt = $db->query("SELECT id FROM taso1_kysymykset");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($taso1, $row['id']);
		}
		shuffle($taso1);
		$taso1 = serialize($taso1);

		$stmt = $db->prepare("UPDATE fbkayttajat SET kysyttavat1=? WHERE id =?");
		$stmt->execute(array($taso1, $tunnus));
	 	//-----------------------------------------------------------------------------------

		$stmt = $db->query("SELECT id FROM taso2_kysymykset");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($taso2, $row['id']);
		}
		shuffle($taso2);
		$taso2 = serialize($taso2);

		$stmt = $db->prepare("UPDATE fbkayttajat SET kysyttavat2=? WHERE id =?");
		$stmt->execute(array($taso2, $tunnus));
		//-----------------------------------------------------------------------------------

		$stmt = $db->query("SELECT id FROM taso3_kysymykset");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			array_push($taso3, $row['id']);
		}
		shuffle($taso3);
		$taso3 = serialize($taso3);

		$stmt = $db->prepare("UPDATE fbkayttajat SET kysyttavat3=? WHERE id =?");
		$stmt->execute(array($taso3, $tunnus));
	}
?>