<?php
session_start();
include('../manejoSesion.inc');

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Consulta Renglones - Base de Datos</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body>
  <header>
    <h1>Consulta de Renglones</h1>
    <div class="acciones">
      <span style="margin-right:10px">Usuario: <strong><?php echo htmlspecialchars($_SESSION['login']); ?></strong> | Orden: <strong id="ordenActualSpan">Cod_articulo</strong></span>
      <button id="btnCargar">Cargar datos</button>
      <button id="btnVaciar">Vaciar datos</button>
      <button id="btnLimpiarFiltros">Limpiar filtros</button>
      <button id="btnAlta">Alta registro</button>
      <button id="btnCerrarSesion" style="background-color: #d9534f;">Cerrar Sesión</button>
    </div>
  </header>

  <main>
    <div class="tabla-container">
      <table id="tabla">
        <thead>
          <tr>
            <th data-orden="Cod_articulo">Cod Art</th>
            <th data-orden="NroFactura">NroFactura</th>
            <th data-orden="Categoria">Categoría</th>
            <th data-orden="UnidadDeMedida">UM</th>
            <th data-orden="Descripcion">Descripción</th>
            <th data-orden="FechaAlta">Fecha Alta</th>
            <th data-orden="Cantidad">Saldo stock</th>
            <th>PDF</th>
            <th>Modi</th>
            <th>Baja</th>
          </tr>
          <tr>
            <th><input type="text" id="fCodArt" placeholder="Filtrar..." style="width:100%"></th>
            <th><input type="number" id="fNroFactura" placeholder="Filtrar..." style="width:100%"></th>
            <th><input type="text" id="fCategoriaTxt" placeholder="Categoría exacta" style="width:100%"></th>
            <th>
              <select id="fUM" style="width:100%">
                <option value="">Todas</option>
              </select>
            </th>
            <th><input type="text" id="fDescripcion" placeholder="Contiene..." style="width:100%"></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>
  </main>

  <footer>
    Programación en ambiente de redes 2025 - Alumno: Sebastián Jofré
  </footer>

  <div id="modalFormulario" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="tituloModal">Formulario</h2>
      <form id="formArticulo" enctype="multipart/form-data">
        <div class="form-group">
          <label>Código Artículo:</label>
          <input type="text" id="codArt" name="codArt" required>
        </div>
        <div class="form-group">
          <label>Descripción:</label>
          <input type="text" id="descripcion" name="descripcion" required>
        </div>
        <div class="form-group">
          <label>Familia de artículo:</label>
          <select id="familia" name="familia" required>
            <option value="">Seleccionar...</option>
            <option value="Torso Superior">Torso Superior</option>
            <option value="Torso Inferior">Torso Inferior</option>
            <option value="Calzado">Calzado</option>
          </select>
        </div>
        <div class="form-group">
          <label>UM:</label>
          <select id="um" name="um" required>
            <option value="">Seleccionar...</option>
            <option value="UN">UN</option>
            <option value="PAR">PAR</option>
          </select>
        </div>
        <div class="form-group">
          <label>Fecha Alta:</label>
          <input type="date" id="fechaAlta" name="fechaAlta" required>
        </div>
        <div class="form-group">
          <label>Saldo stock:</label>
          <input type="number" id="saldoStock" name="saldoStock" required>
        </div>
        <div class="form-group">
          <label>Precio Unitario:</label>
          <input type="number" step="0.01" id="precioUnitario" name="precioUnitario" required>
        </div>
        <div class="form-group">
          <label>Nro Factura:</label>
          <input type="number" id="nroFactura" name="nroFactura" required>
        </div>
        <div class="form-group full">
          <label>PDF:</label>
          <input type="file" id="pdfFile" name="pdfFile" accept=".pdf">
          <small id="pdfActual"></small>
        </div>
        <div class="form-group full">
          <button type="submit" id="btnEnviar" disabled>Enviar</button>
        </div>
      </form>
    </div>
  </div>

  <div id="modalRespuesta" class="modal">
    <div class="modal-content">
      <span class="close-respuesta">&times;</span>
      <h2>Respuesta del servidor</h2>
      <div id="contenidoRespuesta"></div>
      <button id="btnCerrarRespuesta">Aceptar</button>
    </div>
  </div>

  <script src="Js/funciones.js?v=<?php echo time(); ?>"></script>
</body>
</html>
