<?php
session_start();
include("../../manejoSesion.inc");

include("../datosConexionBase.php");

try {
    $sql = "SELECT DISTINCT UnidadDeMedida FROM renglones ORDER BY UnidadDeMedida";
    $res = $conexion->query($sql);

    if (!$res) {
        throw new Exception("Error en consulta: " . $conexion->error);
    }

    $unidades = array();
    while ($row = $res->fetch_assoc()) {
        $unidades[] = array("UnidadDeMedida" => $row["UnidadDeMedida"]);
    }

    echo json_encode(array("unidades" => $unidades));

} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

$conexion->close();
?>
