var aika;
var kuuntelu;
var sek;
var korret = document.getElementsByClassName("korsi");
var vastaulu = document.getElementsByClassName("vastaus");
var korkeus = screen.height / 2;
var leveys = screen.width / 2;
var vihje = null;

function aloita() {
	var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				if (!isJson(xmlHttp.responseText)){
					document.getElementById('ilmoitus2').style.background="yellow";
					document.getElementById('ilmoitus2').innerText = xmlHttp.responseText;
					return;	
				} else {
					for(var i = 0; i < 5; i++){
						korret[i].innerHTML = "";
					}
					sek = 8;
					var pelaaja = JSON.parse(xmlHttp.responseText);
					var elem1 = document.getElementById("aloita");
					elem1.parentNode.removeChild(elem1);
					var elem2 = document.getElementById("ohjeistus");
					elem2.parentNode.removeChild(elem2);
					document.getElementById("nimi").innerHTML = pelaaja.nimi+' -- ';
					tulostaTiedot(pelaaja, tulostaKysymys);
				}
			}
		}
	xmlHttp.open("GET", "peli.php?aloitaPeli=aloita", true);
	xmlHttp.send();
}

function tulostaTiedot(pelaaja, callback){
	document.getElementById("taso").innerHTML = 'Taso: '+pelaaja.taso+' -- ';
	document.getElementById("pisteet").innerHTML = 'Pisteet: '+pelaaja.pisteet;
	callback(pelaaja, tulostaVastaukset);
}

function tulostaKysymys(pelaaja, callback){
	var elem = document.getElementById("kysymysteksti");
	var i = 0;
	setTimeout(function() { delay(elem, i, pelaaja, callback); }, 60);

	function delay(elem, i, pelaaja, callback) {
		elem.innerHTML+=pelaaja.kysymys.charAt(i);
		i++;
		if( i == pelaaja.kysymys.length ){
			callback(pelaaja, kuuntele);
		} else{
			setTimeout(function() { delay(elem, i, pelaaja, callback); }, 60);
		}
	}

}

function tulostaVastaukset(pelaaja, callback) {
	for(var i = 0; i < vastaulu.length; i++){
		vastaulu[i].children[1].innerHTML = pelaaja.vastaukset[i];
		if(i == 0){
			vastaulu[i].children[0].innerHTML = "A. ";
		}
		if(i == 1){
			vastaulu[i].children[0].innerHTML = "B. ";
		}
		if(i == 2){
			vastaulu[i].children[0].innerHTML = "C. ";
		}
		if(i == 3){
			vastaulu[i].children[0].innerHTML = "D. ";
		}
	}
	for(var i = 0; i < 5; i++){
		korret[i].innerHTML = pelaaja.oljenkorret[i];
	}
	callback(laskuri);
}

function kuuntele(callback){
	for(var i = 0; i < 4; i++){
		if(vastaulu[i].children[1].innerText != ""){
			vastaulu[i].addEventListener("click", kuuntelu = function(){
				tarkista(this.children[1].innerText);
			});
			vastaulu[i].onmouseover = function(){
				this.children[1].style.background = "rgb(29,161,242)";
			}
			vastaulu[i].onmouseout = function(){
				this.children[1].style.background = "white";
			}
		}
	}
	callback(tarkista);
}

function laskuri(callback) {
	aika = setInterval(laske, 1000);
	function laske() {
		if (sek == 0) {
			document.getElementById("aikaruutu").innerHTML = sek;
			clearInterval(aika);
				callback("aikaLoppui");
		} else {
				document.getElementById("aikaruutu").innerHTML = sek;
				sek--;
		}
	}
}

function tarkista(vastaus){
	if(vastaus != "" && vastaus != null){
		clearInterval(aika);
		if(vihje != null){
		 	vihje.close();
		 	vihje = null;
		}
		for(var i = 0; i < 5; i++){
			korret[i].innerHTML = "";
		}
		document.getElementById("kysymysteksti").innerHTML = "";
		document.getElementById("aikaruutu").innerHTML = ""
		for (var i = 0; i < vastaulu.length; i++) {
			vastaulu[i].children[0].innerHTML = "";
			vastaulu[i].children[1].innerHTML = "";
		}
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.onreadystatechange = function() {
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				var viesti = xmlHttp.responseText;
				if(isJson(viesti)) {
					var palaute = JSON.parse(viesti);
					document.getElementById('ilmoitus2').style.color = "green";
					document.getElementById('ilmoitus2').innerText = "Oikein";
					setTimeout(function(){
						document.getElementById("ilmoitus2").innerText = "";
						document.getElementById('ilmoitus2').style.background = "white";
					}, 2000	);
					sek = 8;
					tulostaTiedot(palaute, tulostaKysymys);
				} else {
					document.getElementById('ilmoitus2').style.background="yellow";
					document.getElementById('ilmoitus2').style.color = "red";
					document.getElementById('ilmoitus2').innerText = viesti;
					setTimeout(function(){
						window.opener.location.reload();
						close();
					}, 2000);
				}
			}
		}
		xmlHttp.open("GET", "peli.php?vastattu="+vastaus, true);
		xmlHttp.send();
	}
}

function poistaKaksi(nappi){
	clearInterval(aika);
	for(var i = 0; i < 5; i++){
		korret[i].innerHTML = "";
	}
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			var palaute = JSON.parse(xmlHttp.responseText);
			for(var i = 0; i < 4; i++){
				if(vastaulu[i].children[1].innerText == palaute.poistettavat[0] || vastaulu[i].children[1].innerText == palaute.poistettavat[1]){
					vastaulu[i].children[1].textContent = "";
					vastaulu[i].children[0].textContent = "";
					vastaulu[i].removeEventListener("click", kuuntelu);
				}
			}
			for(var i = 0; i < 5; i++){
				korret[i].innerHTML = palaute.oljenkorret[i];
			}
			kuuntele(laskuri);
		}
	}
	xmlHttp.open("GET", "peli.php?" + nappi.id + "=poista", true);
	xmlHttp.send();
}

function ohita(nappi) {
	clearInterval(aika);
	if(vihje != null){
	 	vihje.close();
	 	vihje = null;
	}
	sek = 8;
	for(var i = 0; i < 5; i++){
		korret[i].textContent = "";
	}
	for (var i = 0; i < 4; i++) {
		vastaulu[i].removeEventListener("click", kuuntelu);
		vastaulu[i].children[0].textContent = "";
		vastaulu[i].children[1].textContent = "";
	}
	document.getElementById("kysymysteksti").textContent = "";
	document.getElementById("aikaruutu").textContent = "";
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
		var palaute = JSON.parse(xmlHttp.responseText);
		var elem = document.getElementById("kysymysteksti");
		var i = 0;
		setTimeout(function() { delay(elem, i, palaute); }, 60);
			function delay(elem, i, palaute) {
				elem.innerHTML+=palaute.kysymys.charAt(i);
				i++;
				if( i == palaute.kysymys.length ){
					for(var i = 0; i < vastaulu.length; i++){
						vastaulu[i].children[1].innerHTML = palaute.vastaukset[i];
						if(i == 0){
							vastaulu[i].children[0].innerHTML = "A. ";
						}
						if(i == 1){
							vastaulu[i].children[0].innerHTML = "B. ";
						}
						if(i == 2){
							vastaulu[i].children[0].innerHTML = "C. ";
						}
						if(i == 3){
							vastaulu[i].children[0].innerHTML = "D. ";
						}
					}
					for(var i = 0; i < 5; i++){
						korret[i].innerHTML = palaute.oljenkorret[i];
					}
					kuuntele(laskuri);
				} else{
					setTimeout(function() { delay(elem, i, palaute); }, 60);
				}
			}
		}
	}
	xmlHttp.open("GET", "peli.php?ohita=ohi", true);
	xmlHttp.send();
}

function lisaa20(nappi){
	clearInterval(aika);
	document.getElementById(nappi.id).parentNode.removeChild(nappi);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			var viesti = xmlHttp.responseText;
			if(viesti == "lisatty"){
				sek = sek + 20;
				laskuri(tarkista);
			}
		}
	}
	xmlHttp.open("GET", "peli.php?lisaa20=ok", true);
	xmlHttp.send();
}

function lisaa30(nappi){
	document.getElementById(nappi.id).parentNode.removeChild(nappi);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			var viesti = xmlHttp.responseText;
			if(viesti != "Kaytetty"){
				sek = sek + 30;
				vihje = window.open(viesti, "Vihje", "height="+korkeus+",width="+
					leveys+",left=0,top=0,titlebar=1,menubar=0,toolbar=0,resizable=1,scrollbars=1");
				vihje.focus(); 
			}
		}
	}
	xmlHttp.open("GET", "peli.php?lisaa30=ok", true);
	xmlHttp.send();
}

function isJson(str) {
	try {
			JSON.parse(str);
	} catch (e) {
			return false;
	}
	return true;
}