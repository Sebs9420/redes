<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

$respuesta = "";

try {
    $respuesta .= "Parte Modificación simple de datos\n";
    
    $codArt = $_POST['codArt'] ?? '';
    $familia = $_POST['familia'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $um = $_POST['um'] ?? '';
    $fechaAlta = $_POST['fechaAlta'] ?? '';
    $saldoStock = $_POST['saldoStock'] ?? '';
    $precioUnitario = $_POST['precioUnitario'] ?? '';
    $nroFactura = $_POST['nroFactura'] ?? '';
    
    $respuesta .= "Conexion exitosa\n";
    $respuesta .= "Preparación exitosa\n";
    
    // Validaciones
    if (empty($codArt)) {
        throw new Exception("El código de artículo es obligatorio");
    }
    
    $stmtCheck = $conexion->prepare("SELECT NroFactura FROM facturas WHERE NroFactura = ?");
    $stmtCheck->bind_param("i", $nroFactura);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows == 0) {
        throw new Exception("Error: La factura Nro $nroFactura NO EXISTE en la base de datos. Debe crear primero la factura.");
    }
    $stmtCheck->close();
    
    $importeRenglon = floatval($saldoStock) * floatval($precioUnitario);
    
    $stmt = $conexion->prepare("UPDATE renglones SET 
        NroFactura = ?, 
        Descripcion = ?, 
        Categoria = ?, 
        UnidadDeMedida = ?, 
        Cantidad = ?, 
        Precio_Unitario = ?, 
        Importe_Renglon = ?, 
        FechaAlta = ?
        WHERE Cod_articulo = ?");
    
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conexion->error);
    }
    
    $stmt->bind_param("isssiddss", 
        $nroFactura, $descripcion, $familia, $um, 
        $saldoStock, $precioUnitario, $importeRenglon, $fechaAlta, $codArt);
    
    $respuesta .= "bind exitosa\n";
    
    if (!$stmt->execute()) {
        throw new Exception("Error en ejecución: " . $stmt->error);
    }
    
    $respuesta .= "ejecucion exitosa!\n";
    
    $stmt->close();
    
    // Parte de modificación del binario PDF
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] == UPLOAD_ERR_OK) {
        $respuesta .= "\nParte Modificacion simple de datos\n";
        $respuesta .= "Conexion exitosa\n";
        $respuesta .= "Preparación exitosa\n";
        
        $pdfNombre = $_FILES['pdfFile']['name'];
        $pdfTipo = $_FILES['pdfFile']['type'];
        $pdfSize = $_FILES['pdfFile']['size'];
        $pdfData = file_get_contents($_FILES['pdfFile']['tmp_name']);
        
        $respuesta .= "bind exitosa\n";
        
        $stmt2 = $conexion->prepare("UPDATE renglones SET 
            PDF_nombre = ?, 
            PDF_tipo = ?, 
            PDF_size = ?, 
            PDF_data = ?
            WHERE Cod_articulo = ?");
        
        if (!$stmt2) {
            throw new Exception("Error en preparación PDF: " . $conexion->error);
        }
        
        $stmt2->bind_param("ssiss", $pdfNombre, $pdfTipo, $pdfSize, $pdfData, $codArt);
        $stmt2->send_long_data(3, $pdfData);
        
        if (!$stmt2->execute()) {
            throw new Exception("Error en ejecución PDF: " . $stmt2->error);
        }
        
        $respuesta .= "ejecucion exitosa!!!\n";
        $respuesta .= "\nParte registra documento PDF\n";
        $respuesta .= "name: $pdfNombre\n";
        $respuesta .= "type: $pdfTipo\n";
        $respuesta .= "Size: $pdfSize\n";
        $respuesta .= "Contenido de documentoPdf asociado a codArt: $codArt ha sido registrado\n";
        
        $stmt2->close();
    } else {
        $respuesta .= "\nNo ha sido seleccionado file para enviar\n";
    }
    
    $respuesta .= "\nModificación exitosa de artículo: $codArt\n";
    
} catch (Exception $e) {
    $respuesta .= "\nError: " . $e->getMessage() . "\n";
}

$conexion->close();
echo $respuesta;
?>
