<?php
    session_start();
    print_r($_SESSION);
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"]
        );
    }
    session_destroy();
    echo "</br>Tuhottu</br>";
    print_r($_SESSION);
?>