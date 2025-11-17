let ordenActual = "Cod_articulo";
let modoFormulario = "alta"; 
let codArticuloSeleccionado = "";
let modalFormulario, modalRespuesta, spanClose, spanCloseRespuesta, formArticulo, tbody;
let btnEnviar; 
let instantaneaInicialFormulario = {}; 
let formularioModificado = false; 

function updateEnviarState() {
  if (!formArticulo) return;
  if (!btnEnviar) btnEnviar = document.getElementById('btnEnviar');
  try {
    const valido = formArticulo.checkValidity();
    if (modoFormulario === 'modi') {
      btnEnviar.disabled = !(valido && formularioModificado);
    } else {
      btnEnviar.disabled = !valido;
    }

    if (btnEnviar.disabled) {
      try {
        const invalidos = Array.from(formArticulo.querySelectorAll(':invalid'));
        if (invalidos.length > 0) {
          const nombres = invalidos.map(ctrl => {
            let label = '';
            if (ctrl.parentElement) {
              const lab = ctrl.parentElement.querySelector('label');
              if (lab) label = lab.textContent.trim().replace(':','');
            }
            return label || (ctrl.name || ctrl.id || 'campo');
          });
          btnEnviar.title = 'Campos inválidos: ' + nombres.join(', ');
        } else {
          btnEnviar.title = 'Formulario inválido';
        }
      } catch (e) {
        btnEnviar.title = '';
      }
    } else {
      btnEnviar.title = '';
    }
  } catch (e) {
    if (btnEnviar) btnEnviar.disabled = true;
  }
}

// Capturar los valores actuales del formulario para detectar cambios
function capturarInstantaneaInicial() {
  instantaneaInicialFormulario = {};
  if (!formArticulo) return;
  const controles = formArticulo.querySelectorAll('input, select, textarea');
  controles.forEach(function(ctrl){
    const key = ctrl.name || ctrl.id || null;
    if (!key) return;
    if (ctrl.type === 'file') {
      instantaneaInicialFormulario[key] = (ctrl.files && ctrl.files.length) ? ctrl.files[0].name : '';
    } else if (ctrl.type === 'checkbox' || ctrl.type === 'radio') {
      instantaneaInicialFormulario[key] = ctrl.checked ? '1' : '0';
    } else {
      instantaneaInicialFormulario[key] = ctrl.value || '';
    }
  });
  formularioModificado = false;
}

// Comparar los valores del formulario
function formularioHaCambiado() {
  if (!formArticulo) return false;
  const controles = formArticulo.querySelectorAll('input, select, textarea');
  for (let i = 0; i < controles.length; i++) {
    const ctrl = controles[i];
    const key = ctrl.name || ctrl.id || null;
    if (!key) continue;
    let cur;
    if (ctrl.type === 'file') {
      cur = (ctrl.files && ctrl.files.length) ? ctrl.files[0].name : '';
    } else if (ctrl.type === 'checkbox' || ctrl.type === 'radio') {
      cur = ctrl.checked ? '1' : '0';
    } else {
      cur = ctrl.value || '';
    }
    const orig = instantaneaInicialFormulario.hasOwnProperty(key) ? instantaneaInicialFormulario[key] : '';
    if (String(cur) !== String(orig)) {
      return true;
    }
  }
  return false;
}

function alCambiarControl() {
  formularioModificado = formularioHaCambiado();
  updateEnviarState();
}

// resumen de los datos enviados en el formulario
function generarResumenEnvio(formData) {
  let lines = [];
  const campos = [
    {k: 'codArt', label: 'codArt'},
    {k: 'familia', label: 'familia'},
    {k: 'descripcion', label: 'descripcion'},
    {k: 'um', label: 'um'},
    {k: 'fechaAlta', label: 'fechaAlta'},
    {k: 'saldoStock', label: 'saldoStock'},
    {k: 'precioUnitario', label: 'precioUnitario'},
    {k: 'nroFactura', label: 'nroFactura'}
  ];

  campos.forEach(function(c){
    const val = formData.get(c.k);
    if (val !== null && val !== undefined) {
      lines.push(`${c.label}: ${val}`);
    }
  });

  // Información del archivo 
  const fileField = formData.get('pdfFile');
  if (fileField && fileField.name) {
    try {
      lines.push('Archivo seleccionado: ' + fileField.name + ' (bytes: ' + (fileField.size || 0) + ')');
    } catch (e) {
      lines.push('Archivo seleccionado: ' + fileField.name);
    }
  } else {
    lines.push('No ha sido seleccionado file para enviar');
  }

  return lines.join('\n');
}
// Inicia
window.addEventListener('load', function() {
  modalFormulario = document.getElementById('modalFormulario');
  modalRespuesta = document.getElementById('modalRespuesta');
  spanClose = document.getElementsByClassName("close")[0];
  spanCloseRespuesta = document.getElementsByClassName("close-respuesta")[0];
  formArticulo = document.getElementById('formArticulo');
  btnEnviar = document.getElementById('btnEnviar');
  tbody = document.getElementById('tbody');
  
  pintarOrden();
  cargarUnidades();
  configurarEventos();
});

function configurarEventos() {
  document.querySelectorAll('th[data-orden]').forEach(function(th) {
    th.addEventListener('click', function() {
      ordenActual = this.getAttribute('data-orden');
      pintarOrden();
    });
  });

  // Botones 
  document.getElementById('btnCargar').addEventListener('click', () => cargarDatos(false));
  document.getElementById('btnVaciar').addEventListener('click', vaciarTabla);
  document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
  document.getElementById('btnAlta').addEventListener('click', abrirFormularioAlta);
  document.getElementById('btnCerrarSesion').addEventListener('click', cerrarSesion);

  if (spanClose) spanClose.onclick = cerrarModalFormulario;
  if (spanCloseRespuesta) spanCloseRespuesta.onclick = cerrarModalRespuesta;
  document.getElementById('btnCerrarRespuesta').onclick = cerrarModalRespuesta;
  window.onclick = function(event) {
    if (event.target == modalFormulario) {
      cerrarModalFormulario();
    }
    if (event.target == modalRespuesta) {
      cerrarModalRespuesta();
    }
  }

  // Submit del formulario
  formArticulo.addEventListener('submit', enviarFormulario);
  formArticulo.addEventListener('input', alCambiarControl);
  formArticulo.addEventListener('change', alCambiarControl);
  var controles = formArticulo.querySelectorAll('input, select, textarea');
  controles.forEach(function(ctrl){
    ctrl.addEventListener('input', alCambiarControl);
    ctrl.addEventListener('change', alCambiarControl);
    ctrl.addEventListener('keyup', alCambiarControl);
  });
  updateEnviarState();
}

function pintarOrden() {
  document.getElementById('ordenActualSpan').innerHTML = ordenActual;
}

function cargarUnidades() {
  fetch('Php/obtenerUnidades.php')
    .then(response => response.json())
    .then(data => {
      const sel = document.getElementById('fUM');
      if (data && data.unidades) {
        data.unidades.forEach(u => {
          const opt = document.createElement('option');
          opt.value = u.UnidadDeMedida;
          opt.innerHTML = u.UnidadDeMedida;
          sel.appendChild(opt);
        });
        // Alerta con el parametrizable 
        alert(JSON.stringify({ unidades: data.unidades }, null, 2));
      }
    })
    .catch(error => console.error('Error:', error));
}

function cargarDatos(silencioso = false) {
  const nf = document.getElementById('fNroFactura').value;
  const ca = document.getElementById('fCodArt').value;
  const cat = document.getElementById('fCategoriaTxt').value;
  const um = document.getElementById('fUM').value;
  const desc = document.getElementById('fDescripcion').value;
  // Alerta del orden
  if (!silencioso) {
    try {
      alert('orden=' + ordenActual + '&nroFactura=' + nf + '&codArticulo=' + ca +
            '&categoria=' + cat + '&unidad=' + um + '&descripcion=' + desc);
    } catch (e) {}
  }
  
  const url = `Php/obtenerDatos.php?orden=${ordenActual}&nroFactura=${nf}&codArticulo=${ca}&categoria=${cat}&unidad=${um}&descripcion=${desc}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      // Alerta con el JSON
      if (!silencioso) {
        try {
          alert('JSON recibido:\n' + JSON.stringify(data, null, 2));
        } catch (e) {}
      }
      tbody.innerHTML = '';
      if (data && data.renglones) {
        data.renglones.forEach(r => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${r.Cod_articulo}</td>
            <td>${r.NroFactura}</td>
            <td>${r.Categoria}</td>
            <td>${r.UnidadDeMedida}</td>
            <td>${r.Descripcion}</td>
            <td>${r.FechaAlta}</td>
            <td>${r.Cantidad}</td>
            <td><button onclick="verPDF('${r.Cod_articulo}')">PDF</button></td>
            <td><button onclick="abrirFormularioModi('${r.Cod_articulo}')">Modi</button></td>
            <td><button onclick="eliminarRegistro('${r.Cod_articulo}')">Borrar</button></td>
          `;
          tbody.appendChild(tr);
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al cargar los datos');
    });
}

function vaciarTabla() {
  tbody.innerHTML = '';
}

function limpiarFiltros() {
  ordenActual = 'Cod_articulo';
  document.getElementById('ordenActualSpan').textContent = 'Cod_articulo';
}

function abrirFormularioAlta() {
  modoFormulario = "alta";
  document.getElementById('tituloModal').textContent = "Encabezado modal Formulario de alta";
  formArticulo.reset();
  const codArtInput = document.getElementById('codArt');
  const nroFacturaInput = document.getElementById('nroFactura');
  if (codArtInput) {
    codArtInput.disabled = false;
    codArtInput.removeAttribute('readonly');
  }
  if (nroFacturaInput) {
    nroFacturaInput.disabled = false;
    nroFacturaInput.removeAttribute('readonly');
  }
  const hiddenCod = formArticulo.querySelector('input[type="hidden"][name="codArt"]');
  if (hiddenCod) hiddenCod.remove();
  const hiddenNro = formArticulo.querySelector('input[type="hidden"][name="nroFactura"]');
  if (hiddenNro) hiddenNro.remove();
  document.getElementById('pdfActual').textContent = '';
  modalFormulario.classList.add('show');
  capturarInstantaneaInicial();
  updateEnviarState();
}

function abrirFormularioModi(codArt) {
  modoFormulario = "modi";
  codArticuloSeleccionado = codArt;
  document.getElementById('tituloModal').textContent = "Encabezado modal Formulario de modificación";
  
  fetch(`Php/obtenerDatos.php?codArticulo=${codArt}`)
    .then(response => response.json())
    .then(data => {
      if (data && data.renglones && data.renglones.length > 0) {
        const r = data.renglones[0];
        document.getElementById('codArt').value = r.Cod_articulo;
        const codArtVisible = document.getElementById('codArt');
        if (codArtVisible) {
          codArtVisible.disabled = true; 
          codArtVisible.setAttribute('data-disabled-pk', '1');
        }
        document.getElementById('familia').value = r.Categoria;
        document.getElementById('descripcion').value = r.Descripcion;
        document.getElementById('um').value = r.UnidadDeMedida;
        var fechaEl = document.getElementById('fechaAlta');
        if (r.FechaAlta && (r.FechaAlta === '0000-00-00' || r.FechaAlta.startsWith('0000'))) {
          fechaEl.value = '';
        } else {
          fechaEl.value = r.FechaAlta;
        }
        document.getElementById('saldoStock').value = r.Cantidad;
        document.getElementById('precioUnitario').value = r.Precio_Unitario;
        const nroFacturaVisible = document.getElementById('nroFactura');
        if (nroFacturaVisible) {
          nroFacturaVisible.value = r.NroFactura;
          nroFacturaVisible.disabled = true;
          nroFacturaVisible.setAttribute('data-disabled-pk', '1');
        }
        let hiddenCod = formArticulo.querySelector('input[type="hidden"][name="codArt"]');
        if (!hiddenCod) {
          hiddenCod = document.createElement('input');
          hiddenCod.type = 'hidden';
          hiddenCod.name = 'codArt';
          formArticulo.appendChild(hiddenCod);
        }
        hiddenCod.value = r.Cod_articulo;
        let hiddenNro = formArticulo.querySelector('input[type="hidden"][name="nroFactura"]');
        if (!hiddenNro) {
          hiddenNro = document.createElement('input');
          hiddenNro.type = 'hidden';
          hiddenNro.name = 'nroFactura';
          formArticulo.appendChild(hiddenNro);
        }
        hiddenNro.value = r.NroFactura;
        
        if (r.PDF_nombre) {
          document.getElementById('pdfActual').textContent = `Archivo actual: ${r.PDF_nombre}`;
        }
        modalFormulario.classList.add('show');
        capturarInstantaneaInicial();
        updateEnviarState();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al cargar los datos del artículo');
    });
}

function cerrarModalFormulario() {
  modalFormulario.classList.remove('show');
  try { document.body.style.overflow = ''; } catch (e) {}
}

function cerrarModalRespuesta() {
  modalRespuesta.classList.remove('show');
  try { document.body.style.overflow = ''; } catch (e) {}
  try {
    const btn = document.getElementById('btnCerrarRespuesta');
    if (btn) btn.style.display = '';
  } catch (e) {}
}

function enviarFormulario(e) {
  e.preventDefault();
  
  const mensaje = modoFormulario === "alta" 
    ? "¿Está seguro de dar de alta este registro?" 
    : `¿Está seguro de modificar registro: ${codArticuloSeleccionado}?`;
  
  if (!confirm(mensaje)) {
    return;
  }

  const formData = new FormData(formArticulo);
  const url = modoFormulario === "alta" ? "Php/alta.php" : "Php/modificacion.php";
  const resumen = generarResumenEnvio(formData);

  fetch(url, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    const info = {
      status: response.status,
      statusText: response.statusText,
      contentType: response.headers.get('Content-Type') || response.headers.get('content-type') || ''
    };
    return response.text().then(texto => ({ texto, info }));
  })
  .then(({ texto, info }) => {
    const contieneBr = /<br\s*\/?\s*>/i.test(texto || '');
    const textoConBr = contieneBr ? (texto || '') : (texto || '').replace(/\n/g, '<br />');
    try { alert(textoConBr); } catch (e) { console.warn('No se pudo mostrar alert:', e); }
    try { document.body.style.overflow = ''; } catch (e) {}
    cerrarModalFormulario();

    const cont = document.getElementById('contenidoRespuesta');
    if (cont) cont.innerHTML = textoConBr;
    try { const btn = document.getElementById('btnCerrarRespuesta'); if (btn) btn.style.display = 'inline-block'; } catch (e) {}
    modalRespuesta.classList.add('show');
    cargarDatos(true);
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al procesar la solicitud');
  });
    try { document.body.style.overflow = 'hidden'; } catch (e) {}
}

function blobToBase64(blob) {
  return new Promise((resolve, reject) => {
    try {
      const reader = new FileReader();
      reader.onloadend = function() {
        const res = reader.result || '';
        const txt = typeof res === 'string' ? res : '';
        const idx = txt.indexOf(',');
        resolve(idx >= 0 ? txt.slice(idx + 1) : txt);
      };
      reader.onerror = reject;
      reader.readAsDataURL(blob);
    } catch (err) {
      reject(err);
    }
  });
}

function verPDF(codArt) {
  let alertaMostrada = false; 
  fetch(`Php/traerDoc.php?codArt=${codArt}`)
    .then(response => {
      const info = {
        status: response.status,
        statusText: response.statusText,
        contentType: response.headers.get('Content-Type') || response.headers.get('content-type') || '',
        contentLength: response.headers.get('Content-Length') || response.headers.get('content-length') || ''
      };

      if (!response.ok) {
        try {
          alert(`HTTP ${info.status} ${info.statusText}\nContent-Type: ${info.contentType || '-'}\nContent-Length: ${info.contentLength || '-'}\nNo se encontró el PDF.`);
          alertaMostrada = true;
        } catch (e) {}
        throw new Error('No se encontró el PDF');
      }

      return response.blob().then(blob => ({ blob, info }));
    })
    .then(({ blob, info }) => {
      if (blob.size === 0) {
        try {
          alert(`HTTP ${info.status} ${info.statusText}\nContent-Type: ${info.contentType || '-'}\nContent-Length: ${info.contentLength || '-'}\nEl PDF recibido está vacío.`);
          alertaMostrada = true;
        } catch (e) {}
        return;
      }
      return blobToBase64(blob).then(base64 => ({ blob, info, base64 }));
    })
    .then(payload => {
      if (!payload) return; 
      const { blob, info, base64 } = payload;
      // Alerta PDF en base64
      try {
        const jsonVista = JSON.stringify({ documentoPdf: base64 }, null, 2);
        alert(jsonVista);
      } catch (e) {}

      const url = window.URL.createObjectURL(blob);
      document.getElementById('contenidoRespuesta').innerHTML = `
        <embed src="${url}" type="application/pdf" />
      `;

      modalRespuesta.classList.add('show');
      try { document.body.style.overflow = 'hidden'; } catch (e) {}
      try {
        const btn = document.getElementById('btnCerrarRespuesta');
        if (btn) btn.style.display = 'none';
      } catch (e) {}

      setTimeout(() => window.URL.revokeObjectURL(url), 60000);
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

function eliminarRegistro(codArt) {
  if (!confirm(`¿Está seguro de eliminar registro: ${codArt}?`)) {
    return;
  }

  fetch('Php/baja.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `codArt=${codArt}`
  })
  .then(response => response.text())
  .then(respuestaTexto => {
    alert(respuestaTexto);
    document.getElementById('contenidoRespuesta').textContent = respuestaTexto;
    modalRespuesta.classList.add('show');
    try { document.body.style.overflow = 'hidden'; } catch (e) {}
    try {
      const btn = document.getElementById('btnCerrarRespuesta');
      if (btn) btn.style.display = 'inline-block';
    } catch (e) {}
    cargarDatos(true);
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al eliminar el registro');
  });
}
function cerrarSesion() {
  if (confirm('¿Está seguro que desea cerrar la sesión?')) {
    window.location.href = '../destruirSesion.php';
  }
}

