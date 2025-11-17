<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

try {
    $codArt = $_GET['codArt'] ?? '';
    
    if (empty($codArt)) {
        throw new Exception("El código de artículo es obligatorio");
    }
    
    // Consultar y enviar el PDF
    $stmt = $conexion->prepare("SELECT PDF_data, PDF_tipo, PDF_nombre FROM renglones WHERE Cod_articulo = ?");
    
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $codArt);
    
    if (!$stmt->execute()) {
        throw new Exception("Error en ejecución: " . $stmt->error);
    }
    
    $stmt->bind_result($pdfData, $pdfTipo, $pdfNombre);
    
    if ($stmt->fetch()) {
        if ($pdfData !== null && !empty($pdfData)) {
            header("Content-Type: $pdfTipo");
            header("Content-Disposition: inline; filename=\"$pdfNombre\"");
            header("Content-Length: " . strlen($pdfData));
            echo $pdfData;
        } else {
            throw new Exception("No hay PDF almacenado para este artículo");
        }
    } else {
        throw new Exception("No se encontró el artículo");
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    header("HTTP/1.1 404 Not Found");
    echo $e->getMessage();
}

$conexion->close();
?>
