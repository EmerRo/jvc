/* public\js\series\cargar-selects.js 
Función para cargar las marcas en los selects */
function cargarSelectMarcas(targetSelect = null) {
    $.ajax({
        url: _URL + "/ajs/get/marcas",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let options = '<option value="">Seleccionar Marca</option>';
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function (marca) {
                options += `<option value="${marca.id}">${marca.nombre}</option>`;
            });
            
            // Si se especifica un select específico, solo cargar en ese
            if (targetSelect) {
                targetSelect.html(options);
            } else {
                // Cargar solo en selects vacíos (nuevos)
                $('select[name$="[marca]"]').each(function() {
                    if ($(this).find('option').length <= 1) {
                        $(this).html(options);
                    }
                });
            }
            
            // También carga en los selects comunes
            $("#marca_comun, #marca_comun_u").html(options);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar marcas:", error);
        },
    });
}

function cargarSelectModelos(targetSelect = null) {
    $.ajax({
        url: _URL + "/ajs/get/modelos",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let options = '<option value="">Seleccionar Modelo</option>';
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function (modelo) {
                options += `<option value="${modelo.id}">${modelo.nombre}</option>`;
            });
            
            // Si se especifica un select específico, solo cargar en ese
            if (targetSelect) {
                targetSelect.html(options);
            } else {
                // Cargar solo en selects vacíos (nuevos)
                $('select[name$="[modelo]"]').each(function() {
                    if ($(this).find('option').length <= 1) {
                        $(this).html(options);
                    }
                });
            }
            
            // También carga en los selects comunes
            $("#modelo_comun, #modelo_comun_u").html(options);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar modelos:", error);
        },
    });
}

function cargarSelectEquipos(targetSelect = null) {
    $.ajax({
        url: _URL + "/ajs/get/equipos",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let options = '<option value="">Seleccionar Equipo</option>';
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function (equipo) {
                options += `<option value="${equipo.id}">${equipo.nombre}</option>`;
            });
            
            // Si se especifica un select específico, solo cargar en ese
            if (targetSelect) {
                targetSelect.html(options);
            } else {
                // Cargar solo en selects vacíos (nuevos)
                $('select[name$="[equipo]"]').each(function() {
                    if ($(this).find('option').length <= 1) {
                        $(this).html(options);
                    }
                });
            }
            
            // También carga en los selects comunes
            $("#equipo_comun, #equipo_comun_u").html(options);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar equipos:", error);
        },
    });
}
// Llamar a las funciones cuando se carga la página
$(document).ready(function () {
  cargarSelectMarcas();
  cargarSelectModelos();
  cargarSelectEquipos();
});

// Modificar la función que agrega nuevos equipos
$("#agregar_equipo_diferente").click(function () {
  const equiposActuales = $("#equipos_container .equipo-item").length;
  const index = equiposActuales;

  // Obtener el último número de serie usado
  let ultimoNumeroUsado;

  if (equiposActuales > 0) {
    // Obtener el último número de serie del último equipo agregado
    const ultimoInput = $(
      'input[name^="equipos"][name$="[numero_serie]"]'
    ).last();
    ultimoNumeroUsado = parseInt(ultimoInput.val()) || 0;
  } else {
    // Si no hay equipos, usar el último número de serie registrado
    ultimoNumeroUsado = parseInt($("#ultimo_numero_serie").val()) || 0;
  }

  // Calcular el siguiente número de serie
  const siguienteNumero = ultimoNumeroUsado + 1;

  // Crear el nuevo equipo con el número de serie correlativo
  $("#equipos_container").append(`
      <div class="equipo-item card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0">Equipo ${index + 1}</h5>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar-equipo">
              <i class="fa fa-trash"></i>
            </button>
          </div>
          <div class="row mb-2">
            <div class="col-md-12">
              <label class="form-label">
                <i class="fa fa-box me-1 text-rojo"></i>
                Producto del almacén <small class="text-muted">(opcional)</small>
              </label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control input-buscar-producto"
                  name="equipos[${index}][producto_busqueda]"
                  placeholder="Buscar por código o nombre..." autocomplete="off">
                <input type="hidden" class="input-id-producto"
                  name="equipos[${index}][id_producto]" value="">
              </div>
              <small class="text-muted producto-seleccionado-info"></small>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <label class="form-label">Marca</label>
              <div class="input-group">
                <select class="form-select" name="equipos[${index}][marca]" required>
                  <option value="">Seleccionar Marca</option>
                </select>
                <button type="button" class="btn btn-selector"
                  data-bs-toggle="modal" data-bs-target="#modalMarca">
                  <i class="fa fa-list"></i>
                </button>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Modelo</label>
              <div class="input-group">
                <select class="form-select" name="equipos[${index}][modelo]" required>
                  <option value="">Seleccionar Modelo</option>
                </select>
                <button type="button" class="btn btn-selector"
                  data-bs-toggle="modal" data-bs-target="#modalModelo">
                  <i class="fa fa-list"></i>
                </button>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Equipo</label>
              <div class="input-group">
                <select class="form-select" name="equipos[${index}][equipo]" required>
                  <option value="">Seleccionar Equipo</option>
                </select>
                <button type="button" class="btn btn-selector"
                  data-bs-toggle="modal" data-bs-target="#modalEquipo">
                  <i class="fa fa-list"></i>
                </button>
              </div>
            </div>
         <div class="col-md-3 mb-2">
  <label class="form-label">Número de Serie</label>
  <div class="input-group">
    <input type="text" class="form-control" name="equipos[${index}][numero_serie]"
      placeholder="Número de Serie" value="${siguienteNumero}" required>
    <button type="button" class="btn btn-generar-serie" title="Generar número de serie">
      <i class="fa fa-magic"></i>
    </button>
  </div>
  <div class="feedback-container">
    <div class="valid-feedback d-block">Número de serie disponible.</div>
  </div>
</div>

          </div>
        </div>
      </div>
    `);

  // Cargar datos solo en los selects del nuevo equipo
const nuevoEquipo = $("#equipos_container .equipo-item").last();
cargarSelectMarcas(nuevoEquipo.find('select[name$="[marca]"]'));
cargarSelectModelos(nuevoEquipo.find('select[name$="[modelo]"]'));
cargarSelectEquipos(nuevoEquipo.find('select[name$="[equipo]"]'));

  // Actualizar contador
  $("#contador_equipos").text(index + 1);
});
