<?php
	include 'yhteys.php';

	$db->exec("TRUNCATE viime_viikko");
	$db->exec("INSERT INTO viime_viikko (id, nimi, kuva, pisteet, aika, pvm) SELECT id, nimi, kuva, pisteet, aika, pvm FROM viikon_tulokset ORDER BY pisteet DESC, aika ASC LIMIT 10");
	$db->exec("TRUNCATE viikon_tulokset");
	$db->exec("UPDATE fbkayttajat SET viikon_paras = NULL");
	$db->exec("ALTER TABLE viikon_tulokset DROP jarjestysluku");
	$db->exec("ALTER TABLE viime_viikko DROP jarjestysluku");
	$db->exec("ALTER TABLE viikon_tulokset ADD jarjestysluku INT(4) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (jarjestysluku)");
	$db->exec("ALTER TABLE viime_viikko ADD jarjestysluku INT(4) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (jarjestysluku)");

	$nyt = new DateTime('now');
	$vienti = $nyt->format('Y-m-d H:i:s');
	$stmt = $db->prepare("UPDATE viimeksi_paivitetty SET paivitys = ? WHERE id = ?");
	$stmt->execute(array($vienti, 1));

	$lisattavat1 = array();
	$stmt = $db->query("SELECT kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku FROM lisattavat1");
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$lisattavat1[] = $row;
	}
	foreach ($lisattavat1 as $rivi) {
		$stmt = $db->prepare("INSERT INTO taso1_kysymykset (kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku) VALUES (?, ?, ? ,?, ?, ?, ?)");
		$stmt->execute([$rivi[kysymys], $rivi[vastaus1], $rivi[vastaus2], $rivi[vastaus3], $rivi[vastaus4], $rivi[aihealue], $rivi[haku]]);
	}
	$db->exec("TRUNCATE lisattavat1");
	$db->exec("ALTER TABLE taso1_kysymykset DROP id");
	$db->exec("ALTER TABLE taso1_kysymykset ADD id INT(4) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id)");

	$lisattavat2 = array();
	$stmt = $db->query("SELECT kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku FROM lisattavat2");
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$lisattavat2[] = $row;
	}
	foreach ($lisattavat2 as $rivi) {
		$stmt = $db->prepare("INSERT INTO taso2_kysymykset (kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku) VALUES (?, ?, ? ,?, ?, ?, ?)");
		$stmt->execute([$rivi[kysymys], $rivi[vastaus1], $rivi[vastaus2], $rivi[vastaus3], $rivi[vastaus4], $rivi[aihealue], $rivi[haku]]);
	}
	$db->exec("TRUNCATE lisattavat2");
	$db->exec("ALTER TABLE taso2_kysymykset DROP id");
	$db->exec("ALTER TABLE taso2_kysymykset ADD id INT(4) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id)");

	$lisattavat3 = array();
	$stmt = $db->query("SELECT kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku FROM lisattavat3");
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$lisattavat3[] = $row;
	}
	foreach ($lisattavat3 as $rivi) {
		$stmt = $db->prepare("INSERT INTO taso3_kysymykset (kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku) VALUES (?, ?, ? ,?, ?, ?, ?)");
		$stmt->execute([$rivi[kysymys], $rivi[vastaus1], $rivi[vastaus2], $rivi[vastaus3], $rivi[vastaus4], $rivi[aihealue], $rivi[haku]]);
	}
	$db->exec("TRUNCATE lisattavat3");
	$db->exec("ALTER TABLE taso3_kysymykset DROP id");
	$db->exec("ALTER TABLE taso3_kysymykset ADD id INT(4) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id)");
?>