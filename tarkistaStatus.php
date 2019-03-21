<?php 
	session_start();

	if($_GET['status'] == "tarkista"){
		unset($_GET['status']);
		if($_SESSION['sunnuntai'] == "sunnuntai") {
			exit("sunnuntai");
		}
		if($_SESSION['kirjautunut'] != 'ok'){
			exit("eiKirjautunut");
		}
		if(!$_SESSION['oikeus']){
			exit("pelattu");
		} 
		exit("ok");
	}

	if($_GET['kirjauduUlos'] == "ulos"){
		unset($_GET['kirjauduUlos']);
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		session_destroy();
		echo "ok";
	}

	if($_GET['poistaSession'] == "poista"){
		unset($_GET['poistaSession']);
		$_SESSION['top100'] = false;
		exit("poistettu");
	}
?>