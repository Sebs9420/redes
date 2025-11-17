<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

$respuesta = "";

try {
    $respuesta .= "Respuesta del servidor al alta. Entradas recibidas en el req http:\n";
    
    $codArt = $_POST['codArt'] ?? '';
    $familia = $_POST['familia'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $um = $_POST['um'] ?? '';
    $fechaAlta = $_POST['fechaAlta'] ?? '';
    $saldoStock = $_POST['saldoStock'] ?? '';
    $precioUnitario = $_POST['precioUnitario'] ?? '';
    $nroFactura = $_POST['nroFactura'] ?? '';
    
    $respuesta .= "codArt: $codArt\n";
    $respuesta .= "familia: $familia\n";
    $respuesta .= "descripcion: $descripcion\n";
    $respuesta .= "um: $um\n";
    $respuesta .= "fechaAlta: $fechaAlta\n";
    $respuesta .= "saldoStock: $saldoStock\n";
    $respuesta .= "precioUnitario: $precioUnitario\n";
    $respuesta .= "nroFactura: $nroFactura\n";
    
    // Validaciones
    if (empty($codArt) || empty($familia) || empty($descripcion) || empty($um) || 
        empty($fechaAlta) || empty($saldoStock) || empty($precioUnitario) || empty($nroFactura)) {
        throw new Exception("Todos los campos son obligatorios");
    }
    
    // Verificar que no exista PK duplicada
    $stmtDup = $conexion->prepare("SELECT 1 FROM renglones WHERE Cod_articulo = ? LIMIT 1");
    if (!$stmtDup) { throw new Exception("Error en preparación verificación duplicado: " . $conexion->error); }
    $stmtDup->bind_param("s", $codArt);
    $stmtDup->execute();
    $resultDup = $stmtDup->get_result();
    if ($resultDup && $resultDup->num_rows > 0) {
        $stmtDup->close();
        throw new Exception("Error: Ya existe un artículo con el código '$codArt'. No se permiten altas con clave primaria duplicada.");
    }
    $stmtDup->close();

    // Verificar que la factura existe
    $stmtCheck = $conexion->prepare("SELECT NroFactura FROM facturas WHERE NroFactura = ?");
    $stmtCheck->bind_param("i", $nroFactura);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows == 0) {
        throw new Exception("Error: La factura Nro $nroFactura NO EXISTE en la base de datos. Debe crear primero la factura.");
    }
    $stmtCheck->close();
    
    $importeRenglon = floatval($saldoStock) * floatval($precioUnitario);
    
    // Procesar PDF si existe
    $pdfNombre = null;
    $pdfTipo = null;
    $pdfSize = 0;
    $pdfData = null;
    
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] == UPLOAD_ERR_OK) {
        $pdfNombre = $_FILES['pdfFile']['name'];
        $pdfTipo = $_FILES['pdfFile']['type'];
        $pdfSize = $_FILES['pdfFile']['size'];
        $pdfData = file_get_contents($_FILES['pdfFile']['tmp_name']);
        
        $respuesta .= "\nParte registra documento PDF:\n";
        $respuesta .= "name: $pdfNombre\n";
        $respuesta .= "type: $pdfTipo\n";
        $respuesta .= "Size: $pdfSize\n";
        $respuesta .= "tmp_name: {$_FILES['pdfFile']['tmp_name']}\n";
        $respuesta .= "error: {$_FILES['pdfFile']['error']}\n";
    } else {
        $respuesta .= "\nNo ha sido seleccionado file para enviar\n";
    }
    
    $respuesta .= "\nConexion exitosa!\n";
    
    $stmt = $conexion->prepare("INSERT INTO renglones 
        (Cod_articulo, NroFactura, Descripcion, Categoria, UnidadDeMedida, Cantidad, 
         Precio_Unitario, Importe_Renglon, FechaAlta, PDF_nombre, PDF_tipo, PDF_size, PDF_data) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conexion->error);
    }
    
    $respuesta .= "Preparación exitosa!\n";
    
    $stmt->bind_param("sisssiddsssib", 
        $codArt, $nroFactura, $descripcion, $familia, $um, 
        $saldoStock, $precioUnitario, $importeRenglon, $fechaAlta,
        $pdfNombre, $pdfTipo, $pdfSize, $pdfData);
    
    if ($pdfData !== null) {
        $stmt->send_long_data(12, $pdfData);
    }
    
    $respuesta .= "bind exitosa\n";
    
    if (!$stmt->execute()) {
        throw new Exception("Error en ejecución: " . $stmt->error);
    }
    
    $respuesta .= "ejecucion exitosa!\n";
    $respuesta .= "\nAlta exitosa de artículo: $codArt\n";
    
    $stmt->close();
    
} catch (Exception $e) {
    $respuesta .= "\nError: " . $e->getMessage() . "\n";
}

$conexion->close();
echo $respuesta;
?>
