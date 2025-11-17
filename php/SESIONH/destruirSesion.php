<?php
session_start();
include('manejoSesion.inc');

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();

header("Location: formularioDeLogin.html");
exit();
?>
