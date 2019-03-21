<?php
	session_start();
	if($_SESSION['kirjautunut'] != "ok"){
		header("Location: index.html");
	}
?>

<html>
	<head>
		<meta charset="UTF8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href='tyyli.css' rel='stylesheet' type='text/css'>
		<title>Haluatko kympin</title>
	</head>
	<div id="kilpailuotsikko">
		<span id='nimi'>Aika alkaa painamalla nappia</span><span id='taso'></span><span id='pisteet'></span>
	</div>
	<table id="kilpailutaulu">
		<tr id='oljenkorret'>
			<td class='korsi'><button class='oljenkorsi' id='korsi1'>Poista kaksi</button></td>
			<td class='korsi'><button class='oljenkorsi' id='korsi2'>Poista kaksi</button></td>
			<td class='korsi'><button class='oljenkorsi' id='korsi3'>Ohita</button></td>
			<td class='korsi'><button class='oljenkorsi' id='korsi4'>+20 sekuntia</button></td>
			<td class='korsi'><button class='oljenkorsi' id='korsi5'>+30sek ja haku</button></td>
		</tr>
		<tr>
			<td id="kysymys" colspan="5">
				<span id='ohjeistus'>-Yläriviltä voit valita oljenkorren helpottamaan vaikeimpia kysymyksiä. "Poista kaksi" poistaa kaksi vaihtoehtoa ja jäljelle jää vain kaksi.<br><br>-Miettimisaikaa voit lisätä [+20sek] ja [+30sek ja haku] napeilla, joista jälkimmäinen aukaisee aihetta käsittelevän google-haun uuteen pikku-ikkunaan. Kolmenkymmenen lisäsekunnin turvin kilpailija saattaa ehtiä etsimään vastauksen kysymykseen.<br><br>-Normaali vastausaika on vain 8 sekuntia kysymyksen tulostamisen jälkeen, siinä ajassa pelaaja ei ehdi etsimään vastausta hakukoneella. Peli loppuu ensimmäisestä väärästä vastauksesta.</span>
				<span id='kysymysteksti'></span>
				
			</td>
		</tr>
		<tr>
			<td class="vastaus" id="v1" colspan="2">
				<span class="kirjain" id="a"></span><span class="vastausteksti"></span>
			</td>
			<td id="aikaruutu" rowspan="2" style='height: 16vh;'>
				<button id="aloita" onclick='aloita();'>Aloita</button>
			</td>
			<td class="vastaus" id="v2" colspan="2">
				<span class="kirjain" id="b"></span><span class="vastausteksti"></span>
			</td>
		</tr>
			
		<tr>
			<td class="vastaus" id="v3" colspan="2">
				<span class="kirjain" id="c"></span><span class="vastausteksti"></span>
			</td>
			<td class="vastaus" id="v4" colspan="2">
				<span class="kirjain" id="d"></span><span class="vastausteksti"></span>
			</td>
		</tr>
		<tr>
			<td id="ilmoitus2" colspan="5">
			</td>
		</tr>
		<tr>
			<td id='pb' colspan='5'></td>
		</tr>
	</table>
	<script type="text/javascript" src="kilpailuskripti.js"></script>
</html>
