<?php
session_start();
include('libreria.inc');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: formularioDeLogin.html");
    exit();
}

$login = trim($_POST['login'] ?? '');
$clave = $_POST['clave'] ?? '';

if (empty($login) || empty($clave)) {
    header("Location: formularioDeLogin.html?error=campos_vacios");
    exit();
}

$usuario = verificarCredenciales($login, $clave);

if ($usuario === false) {
    header("Location: formularioDeLogin.html?error=credenciales_invalidas");
    exit();
}

incrementarContador($usuario['id']);
$contadorActualizado = obtenerContador($usuario['id']);

$_SESSION['identificativo'] = session_create_id();
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['login'] = $usuario['logindeusuario'];
$_SESSION['contador'] = $contadorActualizado;
$_SESSION['ultima_actividad'] = time();
$_SESSION['ultima_regeneracion'] = time();

header("Location: ingresoAlSistemaOk.php");
exit();
?>
