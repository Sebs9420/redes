<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario POST</title>
</head>
<body>

  <h3>Ingresa la información:</h3>

  <form method="POST" action="respuesta.php">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre"><br><br>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido"><br><br>

    <input type="submit" value="Enviar">
  </form>

</body>
</html>
