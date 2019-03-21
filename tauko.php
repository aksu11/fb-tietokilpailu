<?php
	include 'yhteys.php';
	$stmt = $db->prepare("SELECT nimi, kuva, pisteet FROM viime_viikko WHERE jarjestysluku =?");
	$stmt->execute([1]);
	$voittaja = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html id='osHtml'>
	<head><meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
		
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<link href='tyyli.css' rel='stylesheet' type='text/css'><title>Omat tiedot</title>
		<link rel="icon" href="pollo32.png" type="image/png" sizes="32x32"/>
	</head>
	<body id='omasivu'>
		<table id = 'voittaja'>
			<tbody>
				<tr>
					<td colspan="4" style="height: 10vh; font-size: 6vh; color: gold;">Peli on tauolla sunnuntaisin 18.00 - 24.00</td>
				</tr>
				<tr>
					<td colspan="4" style="height: 10vh; font-size: 4vh;"><span style="padding-left: 3vw;">Päättyneen viikon voittaja on:</span></td>
				</tr>
				<tr>
					<td colspan="1"><img style="height: 200px; width:200px;" src=<?php echo $voittaja[kuva];?>></td>
					<td colspan="3" style="text-align: left;"><span style="padding-left: 3vw; font-size: 5vh; color: silver;"><?php echo $voittaja[nimi]; ?></span></td>
				</tr>
				<tr style="height: 10vh;"></tr>
				<tr style="height: 10vh;">
					<td colspan="4">Päättyneen viikon muut parhaat:<a href="viimeViikko.php" style="padding-left: 4vw;"><button class='osNappi' style="background-color: green;">top10</button></a></td>
				</tr>
				<tr style="height: 10vh;">
					<td colspan="4">Uusi peli alkaa vuorokauden vaihduttua</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>