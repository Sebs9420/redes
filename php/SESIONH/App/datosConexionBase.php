<?php
$host = "mysql.hostinger.com";
$user = "u529320198_seba_admin";
$pass = "SebaAdmin1234";
$db = "u529320198_tienda_ropa";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
