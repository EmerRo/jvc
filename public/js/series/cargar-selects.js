/* public\js\series\cargar-selects.js 
Función para cargar las marcas en los selects */
function cargarSelectMarcas() {
    $.ajax({
        url: _URL + "/ajs/get/marcas",
        type: "GET",
        dataType: "json",
        success: function(data) {
            let options = '<option value="">Seleccionar Marca</option>';
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function(marca) {
                options += `<option value="${marca.id}">${marca.nombre}</option>`;
            });
            // Cargar en todos los selects de marca, incluyendo los de equipos existentes
            $('select[name$="[marca]"]').html(options);

            //tambien carga en los selects comunes
            $('#marca_comun, #marca_comun_u').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar marcas:", error);
        }
    });
}

// Haz lo mismo para las otras funciones (cargarSelectModelos y cargarSelectEquipos)
function cargarSelectModelos() {
    $.ajax({
        url: _URL + "/ajs/get/modelos",
        type: "GET",
        dataType: "json",
        success: function(data) {
            let options = '<option value="">Seleccionar Modelo</option>';
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function(modelo) {
                options += `<option value="${modelo.id}">${modelo.nombre}</option>`;
            });
            // Cargar en todos los selects de modelo
            $('select[name$="[modelo]"]').html(options);

            //tambien carga en los selects comunes
            $('#modelo_comun, #modelo_comun_u').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar modelos:", error);
        }
    });
}

function cargarSelectEquipos() {
    $.ajax({
        url: _URL + "/ajs/get/equipos",
        type: "GET",
        dataType: "json",
        success: function(data) {
            let options = '<option value="">Seleccionar Equipo</option>';
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function(equipo) {
                options += `<option value="${equipo.id}">${equipo.nombre}</option>`;
            });
            // Cargar en todos los selects de equipo
            $('select[name$="[equipo]"]').html(options);

            //tambien carga en los selects comunes
            $('#equipo_comun, #equipo_comun_u').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar equipos:", error);
        }
    });
}

// Llamar a las funciones cuando se carga la página
$(document).ready(function() {
    cargarSelectMarcas();
    cargarSelectModelos();
    cargarSelectEquipos();
});

// Modificar la función que agrega nuevos equipos
$('#agregar_equipo_diferente').click(function() {
    const equiposActuales = $("#equipos_container .equipo-item").length;
    const index = equiposActuales;
    
    // Obtener el último número de serie usado
    let ultimoNumeroUsado;
    
    if (equiposActuales > 0) {
      // Obtener el último número de serie del último equipo agregado
      const ultimoInput = $('input[name^="equipos"][name$="[numero_serie]"]').last();
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
              <input type="text" class="form-control" name="equipos[${index}][numero_serie]"
                placeholder="Número de Serie" value="${siguienteNumero}" required>
              <div class="feedback-container">
                <div class="valid-feedback d-block">Número de serie disponible.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `);
    
    // Cargar datos en los selects
    cargarSelectMarcas();
    cargarSelectModelos();
    cargarSelectEquipos();
    
    // Actualizar contador
    $("#contador_equipos").text(index + 1);
  });