
<?php
 	session_start(); 
 	include 'yhteys.php';

	if( isset($_POST['admin']) && isset($_POST['ss']) ) {
		if($_POST['admin'] != 'pekkopelikaani' && $_POST['ss'] != 'hessuhopo'){
			$_SESSION['admin'] = 'eiKirjautunut';
			$kirjautuminen = 'display: table;';
			$lisaaminen = 'display: none;';
			unset($_POST['admin']);
			unset($_POST['ss']);
			exit();
		}
		if($_POST['admin'] == 'pekkopelikaani' && $_POST['ss'] == 'hessuhopo'){
			$_SESSION['admin'] = 'kirjautunut';
			$kirjautuminen = 'display: none;';
			$lisaaminen = 'display: table;';
			unset($_POST['admin']);
			unset($_POST['ss']);
			header("Refresh:0");
		}
	} else {
		$kirjautuminen = 'display: table;';
		$lisaaminen = 'display: none;';
	}

	if( isset($_POST['taso']) && isset($_POST['kysymys']) && isset($_POST['oikeaVastaus']) && isset($_POST['vastaus2']) && isset($_POST['vastaus3']) && isset($_POST['vastaus4']) && isset($_POST['aihe']) && isset($_POST['haku']) && $_SESSION['admin'] == 'kirjautunut' ){

		$stmt = $db->prepare("INSERT INTO lisattavat".$_POST['taso']." (kysymys, vastaus1, vastaus2, vastaus3, vastaus4, aihealue, haku) VALUES (?, ?, ? ,?, ?, ?, ?)");
		$onnistui = $stmt->execute([$_POST['kysymys'], $_POST['oikeaVastaus'], $_POST['vastaus2'], $_POST['vastaus3'], $_POST['vastaus4'], $_POST['aihe'], $_POST['haku']]);

		unset($_POST['taso'], $_POST['kysymys'], $_POST['oikeaVastaus'], $_POST['vastaus2'], $_POST['vastaus3'], $_POST['vastaus4'], $_POST['aihe'], $_POST['haku']);

		if($onnistui) exit("Onnistui");
		else exit("Jotain meni väärin"); 
	}

	if( isset($_POST['kirjauduUlos']) ) {
		if( $_POST['kirjauduUlos'] == 'ok' ) {
			unset( $_POST['kirjauduUlos'] );
			unset( $_SESSION['admin'] );
			echo 'kirjauduit ulos';
			exit();
		}
	}

	if ($_SESSION['admin'] == 'kirjautunut') {
		$kirjautuminen = 'display: none;';
		$lisaaminen = 'display: table;';
	}
?>
<!DOCTYPE HTML>
	<html style="max-width: 100%; overflow-x: hidden;">
	<head>
		<meta charset='UTF8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<!-- <link href='lisaystyyli.css' rel='stylesheet' type='text/css'> -->
		<script type='text/javascript' src='lisaaSkripti.js'></script>
		<link rel="icon" href="pollo32.png" type="image/png" sizes="32x32"/>
	</head>
	<body style="width: 100vw;">
		<table style="margin-top: 5vh; vertical-align: middle; text-align: center; <?php echo $kirjautuminen; ?>">
			<tbody>
				<tr style="width: 100vw; height: 5vh;">
					<td style="width: 50vw;">
						<label for="vastaus2">Kayttajatunnus: </label>
						<input type="text" name="kt" maxlength="20" style="width: 25vw; margin-right: 5vw;" required>
					</td>
					<td style="width: 50vw;">
						<label for="vastaus2">Salasana: </label>
						<input type="text" name="ss" maxlength="20" style="width: 25vw; margin-right: 5vw;" required>
					</td>
				</tr>
				<tr style="height: 10vh;">
					<td colspan="4" style="text-align: right;">
						<button style="height: 5vh; width: 10vw; border-color: green; border-radius: 1vh; border-width: 2px;
						font-size: 2.5vh; background-color: rgb(59, 89, 152); color: white; margin-right: 10vw;" onclick="kirjaudu();">Kirjaudu</button>
					</td>
				</tr>
			</tbody>    
		</table>
		<table style="vertical-align: middle; text-align: center; width: 100vw; margin-top: 2vh; <?php echo $lisaaminen; ?>">
			<tbody>
				<tr style="height: 5vh;">
					<td>
						<h3>Vaikeustaso: </h3>
					</td>
					<td style="text-align: left;">
						<input type="radio" id="taso1" name="taso" value="1" checked />
						<label for="taso1">Taso 1</label>
					</td>
					<td style="text-align: left;">
						<input type="radio" id="taso2" name="taso" value="2"/>
						<label for="taso2">Taso 2</label>
					</td>
					<td style="text-align: left;">
						<input type="radio" id="taso3" name="taso" value="3"/>
						<label for="taso3">Taso 3</label>
					</td>
				</tr>
				<tr style="height: 15vh;">
					<td>
						<h3>Kysymysteksti:</h3>
					</td>
					<td colspan="3" style="text-align: left;">
						<textarea rows="4" cols="120" id="kysymysteksti"></textarea> 
					</td>
				</tr>
				<tr style="height: 8vh;">
					<td rowspan="2">
						<h3>Vastaukset:</h3>
					</td>
					<td colspan="3" style="text-align: left;">
						<div>
							<label for="oikeaVastaus">Oikea vastaus:</label>
							<input type="text" name="oikeaVastaus" maxlength="255" style="width: 30vw; margin-right: 5vw;">
							<label for="vastaus2">Vastaus 2:</label>
							<input type="text" name="vastaus2" maxlength="255" style="width: 30vw;">
						</div>
					</td>
				</tr>
				<tr style="height: 8vh;">
					<td colspan="3" style="text-align: left;">
						<div>
							<label for="vastaus3">Vastaus 3:</label>
							<input type="text" name="vastaus3" maxlength="255" style="width: 30vw; margin-right: 5vw;">
							<label for="vastaus4">Vastaus 4:</label>
							<input type="text" name="vastaus4" maxlength="255" style="width: 30vw;">
						</div>
					</td>
				</tr>
				<tr style="height: 10vh;">
					<td>
						<h3>Aihealue:</h3>
					</td>
					<td colspan="3" style="text-align: left;">
						<div>
							<input type="radio" id="historia" name="aihealue" value="historia" checked/>
							<label for="historia" style="margin-right: 4vw;">Historia</label>
							<input type="radio" id="kulttuuri" name="aihealue" value="kulttuuri"/>
							<label for="kulttuuri" style="margin-right: 4vw;">Kulttuuri</label>
							<input type="radio" id="luonto" name="aihealue" value="luonto"/>
							<label for="luonto" style="margin-right: 4vw;">Luonto</label>
							<input type="radio" id="maantiede" name="aihealue" value="maantiede"/>
							<label for="maantiede" style="margin-right: 4vw;">Maantiede</label>
							<input type="radio" id="yleinen" name="aihealue" value="yleinen"/>
							<label for="yleinen" style="margin-right: 4vw;">Yleistieto</label>
							<input type="radio" id="yhteiskunta" name="aihealue" value="yhteiskunta"/>
							<label for="yhteiskunta" style="margin-right: 4vw;">Yhteiskunta</label>
							<input type="radio" id="urheilu" name="aihealue" value="urheilu"/>
							<label for="urheilu">Urheilu</label>
						</div>
					</td>
				</tr>
				<tr style="height: 5vh">
					<td>
						<h3>Hakusana(t):</h3>
					</td>
					<td colspan="3" style="text-align: left;">
						<input type="text" id="haku" maxlength="255" style="width: 70vw;">
						<!-- <textarea rows="4" cols="120" id="haku" required></textarea>  -->
					</td>
				</tr>
				<tr style="height: 10vh;">
					<td colspan="4" style="text-align: right;">
						<button style="height: 5vh; width: 10vw; border-color: green; border-radius: 1vh; border-width: 2px;
						font-size: 2.5vh; background-color: rgb(59, 89, 152); color: white; margin-right: 10vw;" onclick="laheta();">Lähetä</button>
					</td>
				</tr>
				<tr style="height: 10vh;">
					<td id="viesti" colspan="4" style="width: 100vw; font-size: 5vh;"></td>
				</tr>
			</tbody>
		</table>
	</body>
</html>