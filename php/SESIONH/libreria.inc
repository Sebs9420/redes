<?php
$host = "mysql.hostinger.com";
$user = "u529320198_seba_admin";
$pass = "SebaAdmin1234";
$db = "u529320198_tienda_ropa";

function conectarDB() {
    global $host, $user, $pass, $db;
    
    if (!function_exists('mysqli_connect')) {
        die("Error: La extensión MySQLi no está disponible en PHP. Habilítela en php.ini");
    }
    
    $conexion = @new mysqli($host, $user, $pass, $db);
    
    if ($conexion->connect_errno) {
        die("Error de conexión a la base de datos: " . $conexion->connect_error . " (Código: " . $conexion->connect_errno . ")");
    }
    
    if (!$conexion->set_charset("utf8mb4")) {
        die("Error al establecer el charset UTF-8: " . $conexion->error);
    }
    
    return $conexion;
}

function verificarCredenciales($login, $clave) {
    $conexion = conectarDB();
    
    $claveHash = hash('sha256', $clave);
    
    $stmt = $conexion->prepare("SELECT id, logindeusuario, contador FROM usuarios WHERE logindeusuario = ? AND clave = ?");
    $stmt->bind_param("ss", $login, $claveHash);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        $conexion->close();
        return $usuario;
    }
    
    $stmt->close();
    $conexion->close();
    return false;
}

function incrementarContador($userId) {
    $conexion = conectarDB();
    
    $stmt = $conexion->prepare("UPDATE usuarios SET contador = contador + 1 WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $resultado = $stmt->execute();
    
    $stmt->close();
    $conexion->close();
    
    return $resultado;
}

function obtenerContador($userId) {
    $conexion = conectarDB();
    
    $stmt = $conexion->prepare("SELECT contador FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $contador = 0;
    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        $contador = $fila['contador'];
    }
    
    $stmt->close();
    $conexion->close();
    
    return $contador;
}
?>
