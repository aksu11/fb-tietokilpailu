
function kirjaudu(){
    var kayttajatunnus = document.getElementsByName("kt");
    var salasana = document.getElementsByName("ss");
    var kt = kayttajatunnus[0].value;
    var ss = salasana[0].value;
    if (kt === "" || ss === "" || kt === null || ss === null) alert ("Täytä molemmat kentät.");
    else {
        var posti = new FormData();
        posti.append('admin', kt);
        posti.append('ss', ss);
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() {
            location.reload(true);
        }
        xmlHttp.open("post", "lisays.php");
        xmlHttp.send(posti);
    }
}

function laheta() {
    var vaikeustaso = document.getElementsByName("taso");
    var taso;
    for(i = 0; i < vaikeustaso.length; i++) {
        if(vaikeustaso[i].checked){
            taso = vaikeustaso[i].value;
            break;
        } 
    }
    var kysymys = document.getElementById("kysymysteksti").value;
    var oVastaus = document.getElementsByName("oikeaVastaus");
    var oikeaVastaus = oVastaus[0].value;
    var vas2 = document.getElementsByName("vastaus2");
    var vastaus2 = vas2[0].value;
    var vas3 = document.getElementsByName("vastaus3");
    var vastaus3 = vas3[0].value;
    var vas4 = document.getElementsByName("vastaus4");
    var vastaus4 = vas4[0].value;
    var aihealue = document.getElementsByName("aihealue");
    var aihe;
    for(i = 0; i < aihealue.length; i++){
        if(aihealue[i].checked){
            aihe = aihealue[i].value;
            break;
        } 
    }
    var haku = document.getElementById("haku").value;
    if(taso === "" || kysymys === "" || oikeaVastaus === "" || vastaus2 === "" || vastaus3 === "" || vastaus4 === "" || aihe === "" ||
        haku === "" ) {
        alert("Täytä kaikki kentät");
    }
    haku = haku.split(' ').join('+');
    haku = 'https://www.google.fi/search?q='+haku;
    
    var posti = new FormData();
    posti.append('taso', taso);
    posti.append('kysymys', kysymys);
    posti.append('oikeaVastaus', oikeaVastaus);
    posti.append('vastaus2', vastaus2);
    posti.append('vastaus3', vastaus3);
    posti.append('vastaus4', vastaus4);
    posti.append('aihe', aihe)
    posti.append('haku', haku);

    var xmlHttp = new XMLHttpRequest();   
    xmlHttp.onreadystatechange = function() {
        if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            
            var viesti = document.getElementById("viesti");
            var palaute = xmlHttp.responseText.trim();
            viesti.innerHTML = xmlHttp.responseText;
            if( palaute == 'Onnistui') {
                viesti.style.color = "green";
                document.getElementById("kysymysteksti").value = "";
                document.getElementsByName("oikeaVastaus")[0].value = "";
                document.getElementsByName("vastaus2")[0].value = "";
                document.getElementsByName("vastaus3")[0].value = "";
                document.getElementsByName("vastaus4")[0].value= "";
                document.getElementById("haku").value = "";
            } 
            if( palaute === 'Jotain meni väärin') viesti.style.color = "red";
            setTimeout(() => {
                viesti.innerHTML = "";
            }, 3000);
        }
    }
    xmlHttp.open('post', 'lisays.php'); 
    xmlHttp.send(posti);
}