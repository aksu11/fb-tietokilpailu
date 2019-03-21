<?php
 	session_start(); 
	include 'yhteys.php';
	 
	if($_SESSION['kirjautunut'] != 'ok') header("Location: index.php");

 	$lista = array();

 	$stmt = $db->query("SELECT * FROM viikon_tulokset");
	$i = 0;
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	    $lista[$i] = $row;
	    $i++;
	}
?>
<!DOCTYPE HTML>
<html id='osHtml' style="overflow-x: hidden">
	<head><meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
		
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<link href='tyyli.css' rel='stylesheet' type='text/css'><title>Kuluvan viikon parhaat</title>
	</head>
	<body id='omasivu' style="width: 99vw;">
		<div id="ilmoitus">Ei näin päin!<br>Toimii vain vaakatasossa</div>
		<table id='tilastotaulu'>
			<tbody>
				<tr><td colspan="12" id="osOtsikko">Kuluvan viikon parhaat</td></tr>
				<tr class="tilastorivi">
					<td colspan='3'><a href='index.php'><button class='linkkinappi'>Oma sivu</button></a></td>
					<td colspan='3'><a href='viimeViikko.php'><button class='linkkinappi'>Viime viikko</button></a></td>
					<td colspan='3'><a href='top100.php'><button class='linkkinappi'>Top 100</button></a></td>
					<td colspan='3'><button onclick='kirjauduUlos();' id='kirjauduUlos'>Kirjaudu ulos</button></td>
				</tr>
				<tr style="height: 70px; color: blue; vertical-align: bottom;">
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="4"></td>
					<td colspan="1">pisteet</td>
					<td colspan="1">aika</td>
					<td colspan="2">pvm</td>
					<td colspan="2"></td>
				</tr>
				<?php
					for($i = 0; $i < 20; $i++){
						$j = $i + 1;
						$rivi = $lista[$i];
						$rivi[aika] = number_format($rivi[aika], 2);
						$pvm = strtotime($rivi[pvm]); 
						$rivi[pvm] = date( 'Y-m-d', $pvm );
						$vari = "";
						if($rivi[id] == $_SESSION['id']) $vari = " color: gold;";
						if($rivi[kuva] == "" || $rivi[kuva] == null || substr($rivi[kuva],8,8) == "scontent") $rivi[kuva] = "unknown.jpg";
						if($i == 0 && count($lista) > 0){
							$mainokset = "<tr style='height: 62px;".$vari."'> <td colspan='1'>".$j.".</td> <td colspan='1'><img id='tilastokuva' src='".$rivi[kuva]."'> </td> <td colspan='4'>".$rivi[nimi]."</td> <td colspan='1'>".$rivi[pisteet]."</td> <td colspan='1'>".$rivi[aika]."</td> <td colspan='2'>".$rivi[pvm]."</td> <td id='sivumainos' colspan='2' rowspan='20' style='vertical-align: top;'><a href='http://moonbit.co.in/?ref=98fd7908de3f' target='_blank'><img src='Pystykuvat/moonbitcoin.gif' class='pystymainos'></a><a href='http://moondoge.co.in/?ref=814e54ba55ae' target='_blank'><img src='Pystykuvat/moondogecoin.gif' class='pystymainos'></a></td></tr>";
							if($i == 0 && count($lista) < 11) { 
								$mainokset = "<tr style='height: 62px;".$vari."'> <td colspan='1'>".$j.".</td> <td colspan='1'><img id='tilastokuva' src='".$rivi[kuva]."'> </td> <td colspan='4'>".$rivi[nimi]."</td> <td colspan='1'>".$rivi[pisteet]."</td> <td colspan='1'>".$rivi[aika]."</td> <td colspan='2'>".$rivi[pvm]."</td> <td id='sivumainos' colspan='2' rowspan='10' style='vertical-align: top;'><a href='http://moonbit.co.in/?ref=98fd7908de3f' target='_blank'><img src='Pystykuvat/moonbitcoin.gif' class='pystymainos'></a></td></tr>";
							}
							echo $mainokset;
						} else if($rivi[nimi] != null) {
							echo "<tr style='height: 62px;".$vari."'> <td colspan='1'>".$j.".</td> <td colspan='1'><img id='tilastokuva' src='".$rivi[kuva]."'> </td> <td colspan='4'>".$rivi[nimi]."</td> <td colspan='1'>".$rivi[pisteet]."</td> <td colspan='1'>".$rivi[aika]."</td> <td colspan='2'>".$rivi[pvm]."</tr>";
						} 
					}
				?>
				<tr style='height: 120px;'><td colspan='10'><a href='https://freebitco.in/?r=5911319' target="_blank"><img src='https://static1.freebitco.in/banners/728x90-3.png'></a></td></tr>
			</tbody>
		</table>
	</body>
	<script type='text/javascript' src='omanSivunSkripti.js'></script>
</html>