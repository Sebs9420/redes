<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

$respuesta = "";

try {
    $codArt = $_POST['codArt'] ?? '';
    
    if (empty($codArt)) {
        throw new Exception("El código de artículo es obligatorio");
    }
    
    $respuesta .= "codArt pasado: $codArt\n";
    $respuesta .= "Conexion exitosa!\n";
    
    $stmt = $conexion->prepare("DELETE FROM renglones WHERE Cod_articulo = ?");
    
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conexion->error);
    }
    
    $respuesta .= "Preparación exitosa!\n";
    
    $stmt->bind_param("s", $codArt);
    
    if (!$stmt->execute()) {
        throw new Exception("Error en ejecución: " . $stmt->error);
    }
    
    $respuesta .= "ejecucion exitosa!\n";
    
    if ($stmt->affected_rows > 0) {
        $respuesta .= "\nEliminación exitosa del artículo: $codArt\n";
    } else {
        $respuesta .= "\nNo se encontró el artículo: $codArt\n";
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $respuesta .= "\nError: " . $e->getMessage() . "\n";
}

$conexion->close();
echo $respuesta;
?>
