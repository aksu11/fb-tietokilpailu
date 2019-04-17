
function osallistuminen(){
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			if (xmlHttp.responseText == "sunnuntai"){
				document.getElementById('sunnuntaisin').innerText = "Sunnuntaisin";
				document.getElementById('tauolla').innerText = "tauolla 18.00 - 24.00";
			} else if (xmlHttp.responseText == "pelattu"){
				document.getElementById('sunnuntaisin').innerText = "Olet tänään";
				document.getElementById('tauolla').innerText = "jo pelannut";
			} else if (xmlHttp.responseText == "ok") {
				var params = 'width='+screen.width+',height='+screen.height+',top=0,left=0,fullscreen=yes,titlebar=1,menubar=0,toolbar=0,resizable=1';
				window.open("kilpailu.php", "", params).focus();
			}
		}
	}
	xmlHttp.open("GET", "tarkistaStatus.php?status=tarkista", true);
	xmlHttp.send();
}

function kirjauduUlos(){
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			if(xmlHttp.responseText == "ok"){
				window.location.href = "https://www.facebook.com";
			}
		}
	}
	xmlHttp.open("GET", "tarkistaStatus.php?kirjauduUlos=ulos", true);
	xmlHttp.send();
}

function jaa(){
	FB.ui({
		method: 'share',
		href: 'https://viikkovisa.com',
	}, function(response){
			console.log(response);
	});
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() {
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
			if(xmlHttp.responseText == "poistettu"){
				var jaa = getElementById("jaa");
				jaa.parentChild.removeChild(jaa);
			}
		}
	}
	xmlHttp.open("GET", "tarkistaStatus.php?poistaSession=poista", true);
	xmlHttp.send();
}

function kutsu() {
	FB.ui({method: 'apprequests',
		app_id: '716388772163653',
		redirect_uri: 'https://viikkovisa.com',
		title: 'Oletko minua viisaampi?',
		message: 'Pelaa viikkovisa kerran päivässä.'
	}, function(response){
		console.log(response);
	});
}
