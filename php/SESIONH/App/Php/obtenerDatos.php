<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

try {
    $categoria   = isset($_GET['categoria']) ? $_GET['categoria'] : '';
    $unidad      = isset($_GET['unidad']) ? $_GET['unidad'] : '';
    $nroFactura  = isset($_GET['nroFactura']) ? $_GET['nroFactura'] : '';
    $codArticulo = isset($_GET['codArticulo']) ? $_GET['codArticulo'] : '';
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : '';
    $orden       = isset($_GET['orden']) ? $_GET['orden'] : 'Cod_articulo';

    $sql = "SELECT r.Cod_articulo, r.NroFactura, r.Descripcion, r.Categoria, 
                   r.UnidadDeMedida, r.Cantidad, r.Precio_Unitario, 
                   r.Importe_Renglon, r.FechaAlta,
                   r.PDF_nombre, r.PDF_tipo, r.PDF_size
            FROM renglones r
            WHERE 1=1";

if ($categoria != '') {
    $sql .= " AND r.Categoria = '".$conexion->real_escape_string($categoria)."'";
}

if ($unidad != '') {
    $sql .= " AND r.UnidadDeMedida = '".$conexion->real_escape_string($unidad)."'";
}

if ($nroFactura != '') {
    $sql .= " AND r.NroFactura = '".$conexion->real_escape_string($nroFactura)."'";
}

$colsOrden = array('Cod_articulo','NroFactura','Categoria','UnidadDeMedida','Descripcion','Cantidad','Precio_Unitario','Importe_Renglon','FechaAlta');
if (!in_array($orden, $colsOrden)) { 
    $orden = 'Cod_articulo'; 
}

if ($codArticulo != '') {
        $sql .= " AND r.Cod_articulo LIKE '%".$conexion->real_escape_string($codArticulo)."%'";
}
    
if ($descripcion != '') {
        $sql .= " AND r.Descripcion LIKE '%".$conexion->real_escape_string($descripcion)."%'";
}

$sql .= " ORDER BY $orden";

$resultado = $conexion->query($sql);

if (!$resultado) {
    throw new Exception("Error en consulta: " . $conexion->error);
}

$renglones = array();
while ($fila = $resultado->fetch_assoc()) {
    $renglones[] = $fila;
}

echo json_encode(array("renglones" => $renglones));

} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

$conexion->close();
?>
