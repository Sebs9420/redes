<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Respuesta del Formulario</title>
</head>
<body>

  <h3>Valores pasados:</h3>

  <?php
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];

    echo "Nombre = " . $nombre . "<br>";
    echo "Apellido = " . $apellido . "<br>";
  ?>

  <br>
  <form action="index.php" method="get">
    <input type="submit" value="Volver">
  </form>

</body>
</html>
