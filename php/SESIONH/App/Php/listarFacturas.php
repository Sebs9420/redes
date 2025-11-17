<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

header('Content-Type: application/json');

try {
    $query = "SELECT NroFactura, Fecha_Factura FROM facturas ORDER BY NroFactura";
    $resultado = $conexion->query($query);
    
    $facturas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $facturas[] = $fila;
    }
    
    echo json_encode(['success' => true, 'facturas' => $facturas]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conexion->close();
?>
