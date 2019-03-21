<?php
 	session_start(); 
 	include 'yhteys.php';

 	$_SESSION['sunnuntai'] = onkoSunnuntai($db);

 	if($_SESSION['sunnuntai'] == "sunnuntai") {
		header("Location: tauko.php");
    }

    $kirjautuminen = "display: table;";
    $omaSivu = "display: none;";
    
    if(isset($_SESSION['id'])) {

        $kirjautuminen = "display: none;";
        $omaSivu = "display: table;";

        haeTiedot($_SESSION['id'], $db);

        if($_SESSION['oikeus']){
            if($_SESSION['sunnuntai'] == "sunnuntai") {
                $nappiteksti = "Ei peliä tänään";
                $nappivari = "style = 'background-color: red';";
                header("Location: tauko.php");
            } else {
                $nappiteksti = "Pelaa päivän peli";
                $nappivari = "style = 'background-color: green';";
                $_SESSION['oljenkorret'][0] = "<button class='oljenkorsi' id='korsi1' onclick='poistaKaksi(this)'>Poista kaksi</button>";
                $_SESSION['oljenkorret'][1] = "<button class='oljenkorsi' id='korsi2' onclick='poistaKaksi(this)'>Poista kaksi</button>";
                $_SESSION['oljenkorret'][2] = "<button class='oljenkorsi' id='korsi3' onclick='ohita(this)'>Ohita</button>";
                $_SESSION['oljenkorret'][3] = "<button class='oljenkorsi' id='korsi4' onclick='lisaa20(this)'>+20 sekuntia</button>";
                $_SESSION['oljenkorret'][4] = "<button class='oljenkorsi' id='korsi5' onclick='lisaa30(this)'>+30sek ja haku</button>";
            }
        } else {
            $nappiteksti = "Ei peliä tänään";
            $nappivari = "style = 'background-color: red';";
        }

    }

	function haeTiedot ($id, $db) {

		$stmt = $db->prepare("SELECT pelattuja_peleja FROM fbkayttajat WHERE id = ?");
		$stmt->execute([$id]);
		$_SESSION['pp'] = $stmt->fetchColumn();

		if($_SESSION['pp'] > 0){
			$stmt = $db->prepare("SELECT edellinen_peli FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$_SESSION['edPeli'] = unserialize($stmt->fetchColumn());
			$pvm = new dateTime($_SESSION['edPeli'][2]);
			$pvm = $pvm->format('Y-m-d');
			$_SESSION['edPeli'][2] = $pvm;
			$nyt = new dateTime('now');
			$nyt = $nyt->format('Y-m-d');
			$pvm = strtotime($pvm);
			$nyt = strtotime($nyt);
			$erotus = $nyt - $pvm;
			if($erotus > 86399){
				$_SESSION['oikeus'] = true;
                $_SESSION['poistaKaksi'] = 2;
                $_SESSION['ohita'] = "ok";
                $_SESSION['lisaa20'] = "ok";
                $_SESSION['lisaa30'] = "ok";
			} else {
				$_SESSION['oikeus'] = false;
			}

			$stmt = $db->prepare("SELECT viikon_paras FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$tulokset = $stmt->fetchColumn();
			if($tulokset != null){
				$_SESSION['viikonParas'] = unserialize($tulokset);
				$pvm = new dateTime($_SESSION['viikonParas'][2]);
				$pvm = $pvm->format('Y-m-d');
				$_SESSION['viikonParas'][2] = $pvm;
			} 
			
			$stmt = $db->prepare("SELECT kaikkien_aikojen_paras FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$_SESSION['KAparas'] = unserialize($stmt->fetchColumn());
			$pvm = new dateTime($_SESSION['KAparas'][2]);
			$pvm = $pvm->format('Y-m-d');
			$_SESSION['KAparas'][2] = $pvm;
			$stmt = $db->prepare("SELECT yhteispisteet FROM fbkayttajat WHERE id = ?");
			$stmt->execute([$id]);
			$_SESSION['yhteispisteet'] = $stmt->fetchColumn();
			$_SESSION['keskiarvo'] = number_format(($_SESSION['yhteispisteet'] / $_SESSION['pp']), 2);

		} else {
			$_SESSION['oikeus'] = true;
			$_SESSION['poistaKaksi'] = 2;
			$_SESSION['ohita'] = "ok";
			$_SESSION['lisaa20'] = "ok";
			$_SESSION['lisaa30'] = "ok";
		}
	}

	function onkoSunnuntai($db) {

		$sunnuntai2101 = 1516557600;
		$erotus = time() - $sunnuntai2101;
		$jj = $erotus % 604800;

		if($jj < 21600){
			return "sunnuntai";
		} else {
			return "ok";
		}
	}
?>
<!DOCTYPE HTML>
<html id='esHtml' style='background-color: white;'>
	<head>
		<meta charset='UTF8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<meta property="og:url"			  content="https://viikkovisa.com"/>
		<meta property="og:type"          content="website"/>
		<meta property="og:title"         content="Oletko minua viisaampi?"/>
		<meta property="og:image"         content="https://viikkovisa.com/pollo200.png"/>
		<meta property="og:app_id"        content="716388772163653"/>
		<meta property="og:description"	  content="Pääsin viikkovisassa top100-listalle."/>
		<link href='tyyli.css' rel='stylesheet' type='text/css'>
		<link rel="icon" href="pollo32.png" type="image/png" sizes="32x32"/>
		<title>Viikkovisa</title>
	</head>
	<body id='esBody'>
	<script>
			window.fbAsyncInit = function() {
				FB.init({
					appId      : '716388772163653',
					cookie     : true,
					xfbml      : true,
					version    : 'v3.1'
				});
					
				FB.getLoginStatus(function(response) {
					if(response.status === 'connected'){
						document.getElementById('status').innerHTML = 'Olet kirjautunut facebookiin, jatka peliin napista.'
						var solu = document.getElementById('loginsolu');
						solu.removeChild(document.getElementById('login'));
						var nappi = document.createElement("button");
						nappi.innerHTML = "Jatka peliin";
						nappi.id = "jatka";
						solu.appendChild(nappi);
						nappi.addEventListener ("click", function() {
							haeFBTiedot(kirjaudu);
						});
					}
				});  
	
			};
	
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = 'https://connect.facebook.net/fi_FI/sdk.js#xfbml=1&version=v2.11';
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
	
			function checkLoginState() {
				haeFBTiedot(kirjaudu);
			}
	
			function haeFBTiedot(callback) {
				FB.api('/me', 'GET', {fields: 'id, name, picture.width(200).height(200)'}, function(response) {
					console.log('Kirjautumistiedot: ', response)
					var posti = new FormData();
					posti.append('id', response.id);
					posti.append('nimi', response.name);
					posti.append('kuva', response.picture.data.url);
					callback(posti);
				});
			}
	
			function kirjaudu(posti){
				var xmlHttp = new XMLHttpRequest();   
					xmlHttp.onreadystatechange = function(posti) {
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
							var viesti = xmlHttp.responseText;
							if(viesti == "kirjautunut"){
								location.reload(true);
							}
						}
					}
				xmlHttp.open('post', 'kirjautuminen.php'); 
				xmlHttp.send(posti);
			}
		</script>
		<div id="ilmoitus">Ei näin päin!<br>Toimii vain vaakatasossa</div>
		<table id='esTaulu' style="<?php echo $kirjautuminen; ?>">
			<tr><td id='esOtsikko'>Viikkovisa</td></tr>
			<tr style="height: 25vh;">
				<td id='ohjeet'>
					<div id="popup" style="font-size: 3vh;">-Televisiosta tutun kilpailun säännöillä.<br>-Kilpailuun saa osallistua kerran päivässä.<br>-Paras päivätulos voittaa, kilpailu ratkeaa sunnuntai-iltana.<br>-Selaimesi tulee sallia ponnahdusikkunat tässä pelissä.</div>
				</td>
			</tr>
			<tr style="height: 25vh;">
				<td id="loginsolu">
					<div id="login" onlogin="checkLoginState();" class="fb-login-button" data-width="300" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false"></div>
				</td>
			</tr>
			<tr>
				<td id='status' style="height: 4vh; font-size: 3vh; color: rgb(59, 89, 152)"></td>
			</tr>
			<tr>
				<td style="height: 4vh;">
					<a href="privacypolicy.html">Tietosuojaseloste</a>
				</td>
			</tr>
		</table>
		<table id='osTaulu' style='<?php echo $omaSivu; ?>'>
			<tbody>
				<tr>
					<td id="osOtsikko" colspan="8"><?php echo $_SESSION['nimi'].' '; ?>- oma sivu</td>
				</tr>
				<tr id = 'linkkipalkki'>
					<td colspan='2'><a href='viimeViikko.php'><button class='linkkinappi'>Viime viikko</button></a></td>
					<td colspan='2'><a href='top20.php'><button class='linkkinappi'>Kuluvan viikon top20</button></a></td>
					<td colspan='2'><a href='top100.php'><button class='linkkinappi'>Top 100</button></a></td>
					<td colspan='2'><button onclick='kirjauduUlos();' id='kirjauduUlos'>Kirjaudu ulos</button></td>
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='2'></td>
					<td colspan='1'>Pisteet</td>
					<td colspan='1'>Peliaika</td>
					<td colspan='2'>pvm</td>
					<td colspan='2' rowspan='5' id='omakuva'><img id='osKuva' src=<?php echo $_SESSION['kuva'];?> ></td>
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='2' style='text-align: right'>Edellinen:</td>
					<td id='edPisteet' colspan='1'><?php echo $_SESSION['edPeli'][0]; ?></td>
					<td id='edPeliaika' colspan='1'><?php echo $_SESSION['edPeli'][1]; ?></td>
					<td id='edPvm' colspan='2'><?php echo $_SESSION['edPeli'][2]; ?></td>
					
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='2' style='text-align: right; color: gold'>Viikon paras:</td>
					<td id='VPpisteet' colspan='1'><?php echo $_SESSION['viikonParas'][0]; ?></td>
					<td id='VPpeliaika' colspan='1'><?php echo $_SESSION['viikonParas'][1]; ?></td>
					<td id='VPpvm' colspan='2'><?php echo $_SESSION['viikonParas'][2]; ?></td>
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='2' style='text-align: right'>Kaikkien aikojen paras:</td>
					<td id='KApisteet' colspan='1'><?php echo $_SESSION['KAparas'][0]; ?></td>
					<td id='KApeliaika' colspan='1'><?php echo $_SESSION['KAparas'][1]; ?></td>
					<td id='KApvm' colspan='2'><?php echo $_SESSION['KAparas'][2]; ?></td>
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='6'></td>
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='2' style='text-align: right'>Pelattuja pelejä:</td>
					<td id='pp' colspan='1'><?php echo $_SESSION['pp']; ?></td>
					<td colspan='3' id='sunnuntaisin'></td>
					<td colspan='2' rowspan='3' style='vertical-align: top;' id='osallistuKutsuJaa'>
						<button class='osNappi' id='osallistu' onclick='osallistuminen();' <?php echo $nappivari;?> ><?php echo $nappiteksti; ?></button>
						<button class='osNappi' id='jaa' onclick='kutsu();'>Kutsu kaveri</button>
						<?php if($_SESSION['pp'] > 0 && $_SESSION['oikeus'] == false && $_SESSION['top100'] == true) {
							echo "<button class='osNappi' id='jaa' onclick='jaa();'>Jaa tulos</button>";
						} ?>
					</td>
				</tr>
				<tr style='height: 8.2vh;'>
					<td colspan='2' style='text-align: right'>Pelattujen pelien keskiarvo:</td>
					<td id='ppka' colspan='1'><?php echo $_SESSION['keskiarvo']; ?></td>
					<td colspan='3' id='tauolla'></td>
				</tr>
				<tr style='height: 24.2vh'>
					<td colspan='6'><a href='http://cointiply.com/r/4yDLd' target="_blank"><img src='Vaakakuvat/cointiply.png'></a></td>
				</tr>
			</tbody>
		</table>
	</body>
	<script type='text/javascript' src='omanSivunSkripti.js'></script>
</html>