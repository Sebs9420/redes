<?php
include('manejoSesion.inc');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingreso al Sistema</title>
  <style>
    /* Muy básico, acorde al formulario de login */
    html,body{height:100%;margin:0;padding:0}
    body{background:#3498db;font-family:Arial, Helvetica, sans-serif}

    .panel{width:90%;max-width:760px;margin:60px auto;background:#fff;padding:16px}

    h1{font-size:34px;color:#000;text-align:center;margin-bottom:12px}

    .info{font-size:16px;color:#000;margin:12px 0}

    .actions{margin-top:18px}
    .actions a, .actions button{display:inline-block;padding:8px 14px;margin-right:10px;background:#eee;border:1px solid #000;color:#000;text-decoration:none}

    @media (max-width:600px){.panel{width:95%}.actions a, .actions button{display:block;margin-bottom:8px;width:100%}}
  </style>
</head>
<body>
  <div class="panel">
    <h1>Ingreso</h1>

    <div class="info"><strong>Identificativo de sesión:</strong> <?php echo htmlspecialchars(session_id()); ?></div>
    <div class="info"><strong>Login de usuario:</strong> <?php echo htmlspecialchars($_SESSION['login']); ?></div>
    <div class="info"><strong>Contador de sesión:</strong> <?php echo htmlspecialchars($_SESSION['contador']); ?></div>

    <div class="actions">
      <a href="App/index.php">Ingresar al ABM</a>
      <a href="destruirSesion.php">Terminar sesión</a>
    </div>
  </div>
</body>
</html>
