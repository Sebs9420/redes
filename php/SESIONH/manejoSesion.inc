<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['login'])) {
    header("Location: /php/SESION/formularioDeLogin.html");
    exit();
}

if (!isset($_SESSION['ultima_regeneracion'])) {
    $_SESSION['ultima_regeneracion'] = time();
} else if (time() - $_SESSION['ultima_regeneracion'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['ultima_regeneracion'] = time();
}

$_SESSION['ultima_actividad'] = time();
?>
