<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Variables tipo objeto en PHP - Renglones de Pedido</title>
</head>
<body>
  <h2>Variables tipo objeto en PHP. Objeto Renglón de Pedido</h2>

  <?php
  $objRenglonPedido1 = new stdClass();
  $objRenglonPedido1->NroFactura = 1001;
  $objRenglonPedido1->Cod_articulo = "A001";
  $objRenglonPedido1->Descripcion = "Remera basica blanca";
  $objRenglonPedido1->Categoria = "Torso Superior";
  $objRenglonPedido1->UnidadDeMedida = "UN";
  $objRenglonPedido1->Cantidad = 2;
  $objRenglonPedido1->Precio_Unitario = 32990.00;
  $objRenglonPedido1->Importe_Renglon = 65980.00;
  $objRenglonPedido1->FechaAlta = "20250115";

  echo "<b>Objeto 1:</b><br>";
  echo "Código: {$objRenglonPedido1->Cod_articulo}<br>";
  echo "Descripción: {$objRenglonPedido1->Descripcion}<br>";
  echo "Cantidad: {$objRenglonPedido1->Cantidad}<br>";
  echo "Importe Renglón: {$objRenglonPedido1->Importe_Renglon}<br><br>";

  echo "Tipo de \$objRenglonPedido1: <b>" . gettype($objRenglonPedido1) . "</b><br><br>";

  $objRenglonPedido2 = new stdClass();
  $objRenglonPedido2->NroFactura = 1001;
  $objRenglonPedido2->Cod_articulo = "A002";
  $objRenglonPedido2->Descripcion = "Pantalón jean clásico";
  $objRenglonPedido2->Categoria = "Torso Inferior";
  $objRenglonPedido2->UnidadDeMedida = "UN";
  $objRenglonPedido2->Cantidad = 1;
  $objRenglonPedido2->Precio_Unitario = 39999.00;
  $objRenglonPedido2->Importe_Renglon = 39999.00;
  $objRenglonPedido2->FechaAlta = "20250220";

  echo "<b>Objeto 2:</b><br>";
  echo "Código: {$objRenglonPedido2->Cod_articulo}<br>";
  echo "Descripción: {$objRenglonPedido2->Descripcion}<br>";
  echo "Cantidad: {$objRenglonPedido2->Cantidad}<br>";
  echo "Importe Renglón: {$objRenglonPedido2->Importe_Renglon}<br><br>";

  $renglonesPedido = array($objRenglonPedido1, $objRenglonPedido2);

  echo "<b>Definimos arreglo de pedidos:</b><br>";
  echo "Tipo de \$renglonesPedido: <b>" . gettype($renglonesPedido) . "</b><br><br>";

  echo "<b>Tabula \$renglonesPedido:</b><br>";
  echo "<table border='1' cellpadding='5'>
          <tr>
            <th>NroFactura</th>
            <th>Código</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Importe Renglón</th>
          </tr>";

  foreach ($renglonesPedido as $r) {
    echo "<tr>
            <td>{$r->NroFactura}</td>
            <td>{$r->Cod_articulo}</td>
            <td>{$r->Descripcion}</td>
            <td>{$r->Categoria}</td>
            <td>{$r->Cantidad}</td>
            <td>{$r->Precio_Unitario}</td>
            <td>{$r->Importe_Renglon}</td>
          </tr>";
  }
  echo "</table><br>";

  $cantidadRenglones = count($renglonesPedido);
  echo "Cantidad de renglones: <b>$cantidadRenglones</b><br><br>";

  $objRenglonesPedido = new stdClass();
  $objRenglonesPedido->renglonesPedido = $renglonesPedido;
  $objRenglonesPedido->cantidadDeRenglones = $cantidadRenglones;

  echo "<b>Producción de objeto combinado:</b><br>";
  echo "Tipo: <b>" . gettype($objRenglonesPedido) . "</b><br><br>";

  $jsonRenglones = json_encode($objRenglonesPedido);

  echo "<b>Producción de JSON final:</b><br>";
  echo $jsonRenglones;
  ?>
</body>
</html>
