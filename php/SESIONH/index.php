<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['login'])) {
    header("Location: formularioDeLogin.html");
    exit();
}

header("Location: App/index.php");
exit();
?>
