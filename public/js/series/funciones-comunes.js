//public\js\series\funciones-comunes.js .  Función para procesar series masivas (separadas por coma o por línea)
function procesarSeriesMasivas(texto) {
  if (!texto) return [];
  // Reemplazar saltos de línea por comas
  texto = texto.replace(/\n/g, ",");
  // Dividir por comas y eliminar espacios en blanco
  return texto
    .split(",")
    .map((serie) => serie.trim())
    .filter((serie) => serie !== "");
}

// Función para verificar series repetidas en un array
function verificarSeriesRepetidas(series) {
  let seriesUnicas = new Set();
  let seriesRepetidas = [];

  series.forEach((serie) => {
    if (seriesUnicas.has(serie)) {
      seriesRepetidas.push(serie);
    } else {
      seriesUnicas.add(serie);
    }
  });

  return seriesRepetidas;
}
// Función para mostrar mensaje de series repetidas
function mostrarMensajeSeriesRepetidas(
  seriesRepetidas,
  contenedorMensaje,
  contenedorLista,
  textarea
) {
  if (seriesRepetidas.length > 0) {
    // Mostrar mensaje con las series repetidas
    contenedorLista.html(
      "<strong>Series repetidas:</strong> " + seriesRepetidas.join(", ")
    );
    contenedorMensaje.show();
    if (textarea) {
      textarea.addClass("has-duplicates");
    }
    return true;
  } else {
    contenedorMensaje.hide();
    if (textarea) {
      textarea.removeClass("has-duplicates");
    }
    return false;
  }
}

// Función para validar que la cantidad de series coincida con la cantidad de equipos
function validarCantidadSeries() {
  const series = procesarSeriesMasivas($("#series_masivas").val());
  const cantidadEquipos = parseInt($("#cantidad_equipos").val());

  if (series.length !== cantidadEquipos) {
    $(".series-counter").addClass("error");
    $("#error_series").show();
    return false;
  } else {
    $(".series-counter").removeClass("error");
    $("#error_series").hide();
    return true;
  }
}

// Modificar la función existente para que también prepare el siguiente número
function cargarUltimoNumeroSerie() {
    $.ajax({
      url: _URL + "/ajs/get/ultimonumeroserie",
      method: "GET",
      dataType: "json",
      success: (response) => {
        if (response.success) {
          // Guardar el último número de serie
          const ultimoNumero = response.numero_serie;
          $("#ultimo_numero_serie, #ultimo_numero_serie_u").val(ultimoNumero);
          
          // Calcular el siguiente número y asignarlo al primer equipo
          const siguienteNumero = parseInt(ultimoNumero) + 1;
          
          // Asignar al primer equipo en el formulario de agregar
          $('input[name="equipos[0][numero_serie]"]').val(siguienteNumero);
          $('input[name="equipos[0][numero_serie]"]').addClass('is-valid');
          $('input[name="equipos[0][numero_serie]"]').siblings('.feedback-container').html('<div class="valid-feedback d-block">Número de serie disponible.</div>');
          
          // Preparar para máquinas idénticas
          generarSeriesMasivas(ultimoNumero);
        } else {
          $("#ultimo_numero_serie, #ultimo_numero_serie_u").val("No hay registros previos");
          // Si no hay registros, empezar desde 1
          $('input[name="equipos[0][numero_serie]"]').val("1");
        }
      },
      error: () => {
        $("#ultimo_numero_serie, #ultimo_numero_serie_u").val("Error al cargar");
      },
    });
  }
  
  // Nueva función para generar series masivas automáticamente
 // Modificar esta función en funciones-comunes.js
function generarSeriesMasivas(ultimoNumero) {
    const cantidad = parseInt($("#cantidad_equipos").val() || 1);
    let series = [];
    
    // Convertir a número entero para asegurar operaciones numéricas correctas
    const numeroBase = parseInt(ultimoNumero);
    
    // Generar series correlativas
    for (let i = 1; i <= cantidad; i++) {
      series.push(numeroBase + i);
    }
    
    // Actualizar el textarea con las series generadas
    $("#series_masivas").val(series.join(","));
    $("#contador_series").text(series.length);
    
    // Validar cantidad de series
    validarCantidadSeries();
  }
  // Nueva función para asignar números de serie automáticamente
function asignarNumeroSerieAutomatico() {
    const ultimoNumero = parseInt($("#ultimo_numero_serie").val());
    
    if (isNaN(ultimoNumero)) return; // Si no hay un número válido, salir
    
    // Obtener todos los equipos y asignar números correlativos
    $("#equipos_container .equipo-item").each(function(index) {
      const numeroSerieInput = $(this).find('input[name$="[numero_serie]"]');
      const nuevoNumero = ultimoNumero + index + 1;
      
      // Solo asignar si el campo está vacío o si es el primer equipo
      if (numeroSerieInput.val() === "" || index === 0) {
        numeroSerieInput.val(nuevoNumero);
        numeroSerieInput.addClass('is-valid');
        numeroSerieInput.siblings('.feedback-container').html('<div class="valid-feedback d-block">Número de serie disponible.</div>');
      }
    });
  }
// Función para limpiar el formulario de registro
function limpiarFormularioRegistro() {
  // Limpiar campos de texto
  $("#cliente_ruc_dni").val("");
  $("#input_datos_cliente").val("");

  // Establecer la fecha actual
  const fechaActual = new Date().toISOString().split("T")[0];
  $("#fecha_creacion").val(fechaActual);

  // Desmarcar checkbox de máquinas idénticas
  $("#maquinas_identicas").prop("checked", false);

  // Ocultar sección de máquinas idénticas y mostrar equipos individuales
  $("#seccion_maquinas_identicas").hide();
  $("#seccion_equipos_individuales").show();
  $("#seccion_agregar_equipo").show();

  // Limpiar selects comunes
  $("#marca_comun").val("");
  $("#modelo_comun").val("");
  $("#equipo_comun").val("");

  // Restablecer cantidad de equipos
  $("#cantidad_equipos").val(1);

  // Limpiar textarea de series masivas
  $("#series_masivas").val("");
  $("#contador_series").text("0");
  $("#error_series").hide();
  $("#series_repetidas_mensaje").hide();

  // Limpiar equipos individuales excepto el primero
  const primerEquipo = $("#equipos_container .equipo-item:first");
  $("#equipos_container").empty().append(primerEquipo);

  // Limpiar campos del primer equipo
  primerEquipo.find("select").val("");
  primerEquipo.find('input[type="text"]').val("");

  // Actualizar contador de equipos
  $("#contador_equipos").text("1");

  // Ocultar mensajes de error
  $("#series_repetidas_equipos_mensaje").hide();
  cargarSelectMarcas();
  cargarSelectModelos();
  cargarSelectEquipos();
}
