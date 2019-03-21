<?php
	session_start();
	include 'yhteys.php';
	
	$id = $_SESSION['id'];
	$nimi = $_SESSION['nimi'];
	$kuva = $_SESSION['kuva'];

	if ( isset($_GET['aloitaPeli']) ) {
		unset($_GET['aloitaPeli']);
		if($_SESSION['oikeus']){
			$stmt = $db->prepare("SELECT kysyttavat1 FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$kysyttavat1 = unserialize($stmt->fetchColumn());
			//------------------------------------------------------
			$stmt = $db->prepare("SELECT kysyttavat2 FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$kysyttavat2 = unserialize($stmt->fetchColumn());
			//------------------------------------------------------
			$stmt = $db->prepare("SELECT kysyttavat3 FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$kysyttavat3 = unserialize($stmt->fetchColumn());
			//------------------------------------------------------
			$pelaaja = array (
				"id" => $id,
				"nimi" => $nimi,
				"pisteet" => 0,
				"taso" => 1,
				"kokonaisaika" => 0,
				"lahetetty" => 0,
				"kysymys" => "",
				"oikeaVastaus" => "",
				"vastaukset" => array(),
				"kysyttavat1" => $kysyttavat1,
				"kysyttavat2" => $kysyttavat2,
				"kysyttavat3" => $kysyttavat3
			);
			$pelaaja = haeKysymys($pelaaja, $db);
			$palautus = array();
			$palautus[nimi] = $pelaaja[nimi];
			$palautus[taso] = $pelaaja[taso];
			$palautus[pisteet] = $pelaaja[pisteet];
			$palautus[kysymys] = $pelaaja[kysymys];
			$palautus[vastaukset] = $pelaaja[vastaukset];
			$palautus[oljenkorret] = $_SESSION['oljenkorret'];
			$_SESSION['pelaaja'] = $pelaaja;
			$_SESSION['lahetetty'] = microtime(true) + strlen($pelaaja[kysymys]) * 0.07;
			echo json_encode($palautus);
			exit();
		} else {
			echo "Olet pelannut tänään.";
			exit();
		}
	}

	if(isset($_GET['korsi1']) && $_SESSION['oikeus']){
		unset($_GET['korsi1']);
		$_SESSION['oljenkorret'][0] = "";
		poistaKaksi($db);
	}
	if(isset($_GET['korsi2']) && $_SESSION['oikeus']){
		unset($_GET['korsi2']);
		$_SESSION['oljenkorret'][1] = "";
		poistaKaksi($db);
	}

	if(isset($_GET['ohita'])) {
		unset($_GET['ohita']);
		if($_SESSION['oljenkorret'][2] != "" && $_SESSION['oikeus']){
			$_SESSION['oljenkorret'][2] = "";
			$_SESSION['pelaaja'] = haeKysymys($_SESSION['pelaaja'], $db);
			$palautus = array();
			$palautus[kysymys] = $_SESSION['pelaaja'][kysymys];
			$palautus[vastaukset] = $_SESSION['pelaaja'][vastaukset];
			$palautus[oljenkorret] = $_SESSION['oljenkorret'];
			$_SESSION['lahetetty'] = microtime(true) + strlen($_SESSION['pelaaja'][kysymys]) * 0.07;
			echo json_encode($palautus);
			exit();
		}
	}

	if(isset($_GET['lisaa20'])){
		unset($_GET['lisaa20']);
		if($_SESSION['oljenkorret'][3] != "" && $_SESSION['oikeus']){
			$_SESSION['oljenkorret'][3] = "";
			$_SESSION['lisaAika'] = 20;
			exit("lisatty");
		}
		exit("Kaytetty");
	}

	if(isset($_GET['lisaa30'])){
		unset($_GET['lisaa30']);
		if($_SESSION['oljenkorret'][4] != "" && $_SESSION['oikeus']){
			$_SESSION['oljenkorret'][4] = "";
			$_SESSION['lisaAika'] = 30;
			if($_SESSION['pelaaja'][taso] == 1){
				$stmt = $db->prepare("SELECT haku FROM taso1_kysymykset WHERE id = ?");
				$stmt->execute([$_SESSION['kysnro']]);
				$vihje = $stmt->fetchColumn();
			}
			if($_SESSION['pelaaja'][taso] == 2){
				$stmt = $db->prepare("SELECT haku FROM taso2_kysymykset WHERE id = ?");
				$stmt->execute([$_SESSION['kysnro']]);
				$vihje = $stmt->fetchColumn();
			}
			if($_SESSION['pelaaja'][taso] == 3){
				$stmt = $db->prepare("SELECT haku FROM taso3_kysymykset WHERE id = ?");
				$stmt->execute([$_SESSION['kysnro']]);
				$vihje = $stmt->fetchColumn();
			}
			exit($vihje);
		} 
		exit("Kaytetty");
	}

	if ( isset($_GET['vastattu']) ){
		$vastaus = $_GET['vastattu'];
		unset($_GET['vastattu']);
		$aika = microtime(true) - $_SESSION['lahetetty'];

		if($vastaus == "aikaLoppui"){
			$_SESSION['pelaaja'][kokonaisaika] = $_SESSION['pelaaja'][kokonaisaika] + 8;
			lopeta($_SESSION['pelaaja'], $db);
			echo "Aikasi loppui";
			exit();
		}
		if($aika > 10){
			if(isset($_SESSION['lisaAika'])){

				$sallittu = 10 + $_SESSION['lisaAika']; // !!

				if($aika > $sallittu) {
					$_SESSION['pelaaja'][kokonaisaika] = $_SESSION['pelaaja'][kokonaisaika] + 8 + $_SESSION['lisaAika'];
					unset($_SESSION['lisaAika']);
					lopeta($_SESSION['pelaaja'], $db);
					echo "Aikasi loppui";
					exit();
				} else {
					$_SESSION['pelaaja'][kokonaisaika] = $_SESSION['pelaaja'][kokonaisaika] + $aika;
					tarkista($db, $vastaus);
				}
			} else {
				$_SESSION['pelaaja'][kokonaisaika] = $_SESSION['pelaaja'][kokonaisaika] + 8;
				lopeta($_SESSION['pelaaja'], $db);
				echo "Aikasi loppui";
				exit();
			}	
		} else {
			if($aika < 8) {
				$_SESSION['pelaaja'][kokonaisaika] = $_SESSION['pelaaja'][kokonaisaika] + $aika;
			} else {
				$_SESSION['pelaaja'][kokonaisaika] = $_SESSION['pelaaja'][kokonaisaika] + 8;
			}
			tarkista($db, $vastaus);
		}
	}

	function tarkista($db, $vastaus){
		if ( $vastaus == $_SESSION['pelaaja'][oikeaVastaus] ){
			$_SESSION['pelaaja'][pisteet]++;
			if($_SESSION['pelaaja'][pisteet] % 5 == 0){
				$_SESSION['pelaaja'][taso]++;
				if($_SESSION['pelaaja'][taso] == 4){
					$_SESSION['pelaaja'][taso] = 3;
				}
			}
			$_SESSION['pelaaja'] = haeKysymys($_SESSION['pelaaja'], $db);
			$palautus = array();
			$palautus[taso] = $_SESSION['pelaaja'][taso];
			$palautus[pisteet] = $_SESSION['pelaaja'][pisteet];
			$palautus[kysymys] = $_SESSION['pelaaja'][kysymys];
			$palautus[vastaukset] = $_SESSION['pelaaja'][vastaukset];
			$palautus[oljenkorret] = $_SESSION['oljenkorret'];
			$_SESSION['lahetetty'] = microtime(true) + strlen($_SESSION['pelaaja'][kysymys]) * 0.07;
			echo json_encode($palautus);
		} else {
			lopeta($_SESSION['pelaaja'], $db);
			echo "väärin";
		}
		exit();
	}

	function poistaKaksi($db){
		if($_SESSION['pelaaja'][taso] == 1){
			$stmt = $db->prepare("SELECT vastaus3, vastaus4 FROM taso1_kysymykset WHERE id = ?");
			$stmt->execute([$_SESSION['kysnro']]);
			$taulu = $stmt->fetch(PDO::FETCH_NUM);
		}
		if($_SESSION['pelaaja'][taso] == 2){
			$stmt = $db->prepare("SELECT vastaus3, vastaus4 FROM taso2_kysymykset WHERE id = ?");
			$stmt->execute([$_SESSION['kysnro']]);
			$taulu = $stmt->fetch(PDO::FETCH_NUM);
		}
		if($_SESSION['pelaaja'][taso] == 3){
			$stmt = $db->prepare("SELECT vastaus3, vastaus4 FROM taso3_kysymykset WHERE id = ?");
			$stmt->execute([$_SESSION['kysnro']]);
			$taulu = $stmt->fetch(PDO::FETCH_NUM);
		}
		$palaute[poistettavat] = $taulu;
		$palaute[oljenkorret] = $_SESSION['oljenkorret'];
		echo json_encode($palaute);
		exit();
	}

	function haeKysymys($pelaaja, $db){
		if ($pelaaja[taso] === 1){
			$_SESSION['kysnro'] = array_shift($pelaaja[kysyttavat1]);
			if  (empty($pelaaja[kysyttavat1])) {
				$stmt = $db->prepare("SELECT id FROM taso1_kysymykset");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					array_push($pelaaja[kysyttavat1], $row['id']);
				}
				shuffle($pelaaja[kysyttavat1]);
			}
			$stmt = $db->prepare("SELECT kysymys, vastaus1, vastaus2, vastaus3, vastaus4 FROM taso1_kysymykset WHERE id = ?");
			$stmt->execute([$_SESSION['kysnro']]);
			$lista = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		if ($pelaaja[taso] === 2){
			$_SESSION['kysnro'] = array_shift($pelaaja[kysyttavat2]);
			if  (empty($pelaaja[kysyttavat2])) {
				$stmt = $db->prepare("SELECT id FROM taso2_kysymykset");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					array_push($pelaaja[kysyttavat2], $row['id']);
				}
				shuffle($pelaaja[kysyttavat2]);
			}
			$stmt = $db->prepare("SELECT kysymys, vastaus1, vastaus2, vastaus3, vastaus4 FROM taso2_kysymykset WHERE id = ?");
			$stmt->execute([$_SESSION['kysnro']]);
			$lista = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		if ($pelaaja[taso] === 3){
			$_SESSION['kysnro'] = array_shift($pelaaja[kysyttavat3]);
			if  (empty($pelaaja[kysyttavat3])) {
				$stmt = $db->prepare("SELECT id FROM taso3_kysymykset");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					array_push($pelaaja[kysyttavat3], $row['id']);
				}
				shuffle($pelaaja[kysyttavat3]);
			}
			$stmt = $db->prepare("SELECT kysymys, vastaus1, vastaus2, vastaus3, vastaus4 FROM taso3_kysymykset WHERE id = ?");
			$stmt->execute([$_SESSION['kysnro']]);
			$lista = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		$pelaaja[vastaukset] = array();
		$pelaaja[kysymys] = $lista['kysymys'];
		$pelaaja[oikeaVastaus] = $lista['vastaus1'];
		array_push($pelaaja[vastaukset], $lista['vastaus1']);
		array_push($pelaaja[vastaukset], $lista['vastaus2']);
		array_push($pelaaja[vastaukset], $lista['vastaus3']);
		array_push($pelaaja[vastaukset], $lista['vastaus4']);
		shuffle($pelaaja[vastaukset]);
		return $pelaaja;
	}

	function lopeta($pelaaja, $db){
		
		$id = $pelaaja[id];
		$nimi = $pelaaja[nimi];
		$kys1 = serialize($pelaaja[kysyttavat1]);
		$kys2 = serialize($pelaaja[kysyttavat2]);
		$kys3 = serialize($pelaaja[kysyttavat3]);
		$stmt = $db->prepare("UPDATE fbkayttajat SET kysyttavat1=:kysyttavat1, kysyttavat2=:kysyttavat2, kysyttavat3=:kysyttavat3 WHERE id =:id");
		$stmt->bindParam('kysyttavat1', $kys1, PDO::PARAM_STR);
		$stmt->bindParam('kysyttavat2', $kys2, PDO::PARAM_STR);
		$stmt->bindParam('kysyttavat3', $kys3, PDO::PARAM_STR);
		$stmt->bindParam('id', $id, PDO::PARAM_STR);
		$stmt->execute();

		$tulos = array();
		array_push($tulos, $pelaaja[pisteet]);
		$kai = number_format($pelaaja[kokonaisaika], 2);
		array_push($tulos, $kai);
		$date = new DateTime('now');
		$aika = $date->format('Y-m-d H:i:s');
		array_push($tulos, $aika);
		$vienti = serialize($tulos);
		$stmt = $db->prepare("UPDATE fbkayttajat SET edellinen_peli=:ep WHERE id=:id");
		$stmt->bindParam(':ep', $vienti, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_STR);
		$stmt->execute();

		$viikonParas = false;
		$kaParas = false;

		$stmt = $db->prepare("SELECT viikon_paras, kaikkien_aikojen_paras FROM fbkayttajat WHERE id =?");
		$stmt->execute([$id]);
		$row = $stmt->fetch(PDO::FETCH_NUM);

		if(!is_null($row[0])){
			$edellinenViikko = unserialize($row[0]);
			if($tulos[0] > $edellinenViikko[0]){
				$viikonParas = true;
			}
			if($tulos[0] === $edellinenViikko[0]){
				if($tulos[1] < $edellinenViikko[1]){
					$viikonParas = true;
				}
			}
		}

		if(!is_null($row[1])){
			$edellinenKa = unserialize($row[1]);
			if($tulos[0] > $edellinenKa[0]){
				$kaParas = true;
			}
			if($tulos[0] === $edellinenKa[0]){
				if($tulos[1] < $edellinenKa[1]){
					$kaParas = true;
				}
			}
		}	

		if($viikonParas){
			$stmt = $db->prepare("UPDATE fbkayttajat SET viikon_paras=? WHERE id=?");
			$stmt->execute(array($vienti, $id));
		}
		if($kaParas){
			$stmt = $db->prepare("UPDATE fbkayttajat SET kaikkien_aikojen_paras=? WHERE id=?");
			$stmt->execute(array($vienti, $id));
		}
		if(is_null($row[0])) {
			$stmt = $db->prepare("UPDATE fbkayttajat SET viikon_paras=? WHERE id=?");
			$stmt->execute(array($vienti, $id));
		}
		if(is_null($row[1])){
			$stmt = $db->prepare("UPDATE fbkayttajat SET kaikkien_aikojen_paras=? WHERE id=?");
			$stmt->execute(array($vienti, $id));
		}
		
		$pelatut = $_SESSION['pp'] + 1;
		$yp = $_SESSION['yhteispisteet'] + $_SESSION['pelaaja'][pisteet];
		$_SESSION['oikeus'] = false;

		$stmt = $db->prepare("UPDATE fbkayttajat SET pelattuja_peleja=:pelattuja_peleja, yhteispisteet=:yhteispisteet WHERE id=:id");
		$stmt->bindParam('pelattuja_peleja', $pelatut, PDO::PARAM_INT);
		$stmt->bindParam('yhteispisteet', $yp, PDO::PARAM_INT);
		$stmt->bindParam('id', $id, PDO::PARAM_STR);
		$stmt->execute();
		vieListoille($pelaaja, $db);
	}

	function vieListoille($pelaaja, $db){
		global $kuva;
		$date = new DateTime('now');
		$pvm = $date->format('Y-m-d H:i:s');

		$stmt = $db->prepare("SELECT * FROM viikon_tulokset WHERE id = ?");
		$stmt->execute([$pelaaja[id]]);
		$row = $stmt->rowCount();
		$lista = array();
		
		if($row == 0) {
			$stmt = $db->query("SELECT * FROM viikon_tulokset ORDER BY pisteet DESC, aika ASC");
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			    $lista[] = $row;
			}

			$koko = count($lista);
			if($koko < 20 || $pelaaja[pisteet] > $lista[$koko-1][pisteet] || ($pelaaja[pisteet] == $lista[$koko-1][pisteet] && $pelaaja[kokonaisaika] < $lista[$koko-1][aika])) {
				$lista[$koko][id] = $_SESSION['id'];
				$lista[$koko][nimi] = $_SESSION['nimi'];
				$lista[$koko][kuva] = $_SESSION['kuva'];
				$lista[$koko][pisteet] = $pelaaja[pisteet];
				$lista[$koko][aika] = $pelaaja[kokonaisaika];
				$lista[$koko][pvm] = $pvm;
				foreach ($lista as $rivi) {
					$pisteet[] = $rivi[pisteet];
					$aika[] = $rivi[aika];
				}
				array_multisort($pisteet, SORT_DESC, $aika, SORT_ASC, $lista);
				$vienti = array_slice($lista, 0, 20);
				$db->exec("TRUNCATE viikon_tulokset");
				foreach ($vienti as $rivi) {
					$stmt = $db->prepare("INSERT INTO viikon_tulokset (id, nimi, kuva, pisteet, aika, pvm) VALUES (?, ?, ? ,?, ?, ?)");
					$stmt->execute([$rivi[id], $rivi[nimi], $rivi[kuva], $rivi[pisteet], $rivi[aika], $rivi[pvm]]);
				}
			}
		} else {
			$rivi = $stmt->fetch(PDO::FETCH_ASSOC);
			if($pelaaja[pisteet] > $rivi[pisteet] || ($pelaaja[pisteet] == $rivi[pisteet] && $pelaaja[kokonaisaika] < $rivi[aika])){
				$stmt = $db->prepare("UPDATE viikon_tulokset SET nimi=?, kuva=?, pisteet=?, aika=?, pvm=? WHERE id=?");
				$stmt->execute([$pelaaja[nimi], $_SESSION['kuva'], $pelaaja[pisteet], $pelaaja[kokonaisaika], $pvm, $pelaaja[id]]);
			}
			$stmt = $db->query("SELECT * FROM viikon_tulokset ORDER BY pisteet DESC, aika ASC");
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			    $lista[] = $row;
			}
			$db->exec("TRUNCATE viikon_tulokset");
			foreach ($lista as $rivi) {
				$stmt = $db->prepare("INSERT INTO viikon_tulokset (id, nimi, kuva, pisteet, aika, pvm) VALUES (?, ?, ? ,?, ?, ?)");
				$stmt->execute([$rivi[id], $rivi[nimi], $rivi[kuva], $rivi[pisteet], $rivi[aika], $rivi[pvm]]);
			}
		}
		$lista = array();
		$stmt = $db->query("SELECT * FROM kaikkien_aikojen_parhaat");
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		    $lista[] = $row;
		}
		$koko = count($lista);
		if($koko < 100 || $lista[$koko-1][pisteet] < $pelaaja[pisteet] || ($lista[$koko-1][pisteet] == $pelaaja[pisteet] && $lista[$koko-1][aika] > $pelaaja[kokonaisaika])) {
				$lista[$koko][id] = $_SESSION['id'];
				$lista[$koko][nimi] = $_SESSION['nimi'];
				$lista[$koko][kuva] = $_SESSION['kuva'];
				$lista[$koko][pisteet] = $pelaaja[pisteet];
				$lista[$koko][aika] = $pelaaja[kokonaisaika];
				$lista[$koko][pvm] = $pvm;
				$_SESSION['top100'] = true;
				foreach ($lista as $rivi) {
					$pisteet[] = $rivi[pisteet];
					$aika[] = $rivi[aika];
				}
				array_multisort($pisteet, SORT_DESC, $aika, SORT_ASC, $lista);
				$koko++;
				if($koko > 100) $koko = 100;
				$vienti = array_slice($lista, 0, $koko);
				$db->exec("TRUNCATE kaikkien_aikojen_parhaat");
				foreach ($vienti as $rivi) {
					$stmt = $db->prepare("INSERT INTO kaikkien_aikojen_parhaat (id, nimi, kuva, pisteet, aika, pvm) VALUES (?, ?, ? ,?, ?, ?)");
					$stmt->execute([$rivi[id], $rivi[nimi], $rivi[kuva], $rivi[pisteet], $rivi[aika], $rivi[pvm]]);
				}
		}
	}
?>