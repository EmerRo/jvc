//   public\js\series\crud-registro.js

// Quitar is-invalid en cuanto el usuario corrija el campo
$(document).on("input change", ".is-invalid", function () {
  if ($(this).val()) $(this).removeClass("is-invalid");
});

// Validar al enviar el formulario
$("#submitRegistro").click(function () {
  // Verificar si hay series repetidas
  let formValido = true;

  // Verificar si hay series repetidas en el formulario
  if ($("#maquinas_identicas").is(":checked")) {
    const series = procesarSeriesMasivas($("#series_masivas").val());
    const seriesRepetidas = verificarSeriesRepetidas(series);

    if (seriesRepetidas.length > 0) {
      formValido = false;
      Swal.fire({
        title: "Error",
        text: "Hay números de serie repetidos. Cada número de serie debe ser único.",
        icon: "error",
      });
      return false;
    }

    if ($("#series_duplicadas_mensaje").length > 0) {
      formValido = false;
      Swal.fire({
        title: "Error",
        text: "Hay números de serie que ya existen en la base de datos. Por favor, corrígelos antes de continuar.",
        icon: "error",
      });
      return false;
    }

    if (!validarCantidadSeries()) {
      Swal.fire({
        title: "Error",
        text: "La cantidad de números de serie debe coincidir con la cantidad de equipos",
        icon: "error",
      });
      return false;
    }

    // Procesar las series masivas y crear un array de equipos
    const cantidadEquipos = parseInt($("#cantidad_equipos").val());
    const marca = $("#marca_comun").val();
    const modelo = $("#modelo_comun").val();
    const equipo = $("#equipo_comun").val();

    // Validar campos del modo máquinas idénticas — resaltar sin alerta
    let primerInvalidoIdenticas = null;

    if (!$("#fecha_creacion").val()) {
      $("#fecha_creacion").addClass("is-invalid");
      if (!primerInvalidoIdenticas) primerInvalidoIdenticas = $("#fecha_creacion");
    } else {
      $("#fecha_creacion").removeClass("is-invalid");
    }

    if (!equipo) {
      $("#equipo_comun").addClass("is-invalid");
      if (!primerInvalidoIdenticas) primerInvalidoIdenticas = $("#equipo_comun");
    } else {
      $("#equipo_comun").removeClass("is-invalid");
    }

    if (primerInvalidoIdenticas) {
      primerInvalidoIdenticas[0].scrollIntoView({ behavior: "smooth", block: "center" });
      primerInvalidoIdenticas.focus();
      return false;
    }

    // Crear array de equipos para enviar al servidor
    // En modo "máquinas idénticas" se usa un único producto común (opcional)
    const idProductoComun = $("#id_producto_comun").val() || "";
    var equiposData = [];
    for (let i = 0; i < series.length; i++) {
      equiposData.push({
        modelo: modelo,
        marca: marca,
        equipo: equipo,
        id_producto: idProductoComun, // NUEVO
        numero_serie: series[i],
      });
    }

    // Preparar datos de cliente (pueden estar vacíos)
    const tieneCliente = $("#tiene_cliente").is(":checked");
    const clienteRucDni = tieneCliente ? $("#cliente_ruc_dni").val() : "";
    const clienteDocumento = tieneCliente ? $("#cliente_documento").val() : "";
    
    // Obtener el tipo de máquina seleccionado
    const tipoMaquina = $('input[name="tipo_maquina"]:checked').val() || 'fabricada';

    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/save/numeroseries",
      method: "POST",
      data: {
        cliente_ruc_dni: clienteRucDni,
        cliente_documento: clienteDocumento,
        fecha_creacion: $("#fecha_creacion").val(),
        tipo_maquina: tipoMaquina,
        equipos: JSON.stringify(equiposData),
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            Swal.fire({
              title: "Éxito",
              text: "Registro agregado correctamente",
              icon: "success",
            });
            limpiarFormularioRegistro();
            $("#modalAgregar").modal("hide");
            $("#tabla_clientes").DataTable().ajax.reload();
            cargarUltimoNumeroSerie();
          } else {
            Swal.fire({
              title: "Error",
              text: data.error || "No se pudo agregar el registro",
              icon: "error",
            });
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e);
          Swal.fire({
            title: "Error",
            text: "Error al procesar la respuesta del servidor",
            icon: "error",
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: "Error",
          text: "No se pudo agregar el registro: " + errorThrown,
          icon: "error",
        });
      },
    });
  } else {
    // Verificar series repetidas en equipos individuales
    let todasLasSeries = [];
    $('input[name$="[numero_serie]"]').each(function () {
      const valor = $(this).val().trim();
      if (valor) {
        todasLasSeries.push(valor);
      }
    });

    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
    if (seriesRepetidas.length > 0) {
      Swal.fire({
        title: "Error",
        text: "Hay números de serie repetidos en los equipos. Cada número de serie debe ser único.",
        icon: "error",
      });
      return false;
    }

    // Verificar cada campo de número de serie individual
    let haySeriesDuplicadas = false;
    $('input[name$="[numero_serie]"]').each(function () {
      if ($(this).hasClass("is-invalid")) {
        haySeriesDuplicadas = true;
        return false; // Salir del bucle each
      }
    });

    if (haySeriesDuplicadas) {
      Swal.fire({
        title: "Error",
        text: "Hay números de serie que ya existen en la base de datos. Por favor, corrígelos antes de continuar.",
        icon: "error",
      });
      return false;
    }

    // Validar campos — resaltar en rojo y scroll al primero inválido
    let primerInvalido = null;

    const marcarInvalido = function ($el) {
      $el.addClass("is-invalid");
      if (!primerInvalido) primerInvalido = $el;
    };

    if (!$("#fecha_creacion").val()) {
      marcarInvalido($("#fecha_creacion"));
    } else {
      $("#fecha_creacion").removeClass("is-invalid");
    }

    if ($("#tiene_cliente").is(":checked")) {
      if (!$("#cliente_documento").val()) {
        marcarInvalido($("#input_datos_cliente"));
      } else {
        $("#input_datos_cliente").removeClass("is-invalid");
      }
      if (!$("#cliente_ruc_dni").val()) {
        marcarInvalido($("#cliente_ruc_dni"));
      } else {
        $("#cliente_ruc_dni").removeClass("is-invalid");
      }
    }

    $("#equipos_container .equipo-item").each(function () {
      const $item = $(this);
      const $inputMarca  = $item.find('input[name$="[marca]"]');
      const $inputModelo = $item.find('input[name$="[modelo]"]');
      const $inputEquipo = $item.find('input[name$="[equipo]"]');
      const $inputSerie  = $item.find('input[name$="[numero_serie]"]');

      if (!$inputMarca.val().trim()) {
        marcarInvalido($inputMarca);
      } else {
        $inputMarca.removeClass("is-invalid");
      }

      if (!$inputModelo.val().trim()) {
        marcarInvalido($inputModelo);
      } else {
        $inputModelo.removeClass("is-invalid");
      }

      if (!$inputEquipo.val().trim()) {
        marcarInvalido($inputEquipo);
      } else {
        $inputEquipo.removeClass("is-invalid");
      }

      if (!$inputSerie.val().trim()) {
        marcarInvalido($inputSerie);
      } else {
        $inputSerie.removeClass("is-invalid");
      }
    });

    if (primerInvalido) {
      primerInvalido[0].scrollIntoView({ behavior: "smooth", block: "center" });
      primerInvalido.focus();
      return false;
    }

    // Procesar equipos individuales
    var equiposData = [];
    $("#equipos_container .equipo-item").each(function (index) {
      equiposData.push({
        modelo: $(this).find('input[name$="[modelo]"]').val().trim(),
        marca:  $(this).find('input[name$="[marca]"]').val().trim(),
        equipo: $(this).find('input[name$="[equipo]"]').val().trim(),
        // NUEVO: id_producto o id_repuesto del almacén (opcional)
        id_producto: $(this).find('input.input-id-producto').val() || "",
        tipo_producto: $(this).find('select.tipo-item-selector').val() || 'producto',
        numero_serie: $(this)
          .find('input[name^="equipos"][name$="[numero_serie]"]')
          .val(),
      });
    });

    // Preparar datos de cliente - si no tiene cliente, usar datos de empresa
    const tieneCliente = $("#tiene_cliente").is(":checked");
    const clienteRucDni = tieneCliente ? $("#cliente_ruc_dni").val() : "COMERCIAL & INDUSTRIAL J. V. C. S.A.C.";
    const clienteDocumento = tieneCliente ? $("#cliente_documento").val() : "20538381978";
    
    // Obtener el tipo de máquina seleccionado
    const tipoMaquina = $('input[name="tipo_maquina"]:checked').val() || 'fabricada';

    console.log("Datos a enviar:", {
      cliente_ruc_dni: clienteRucDni,
      cliente_documento: clienteDocumento,
      fecha_creacion: $("#fecha_creacion").val(),
      tipo_maquina: tipoMaquina,
      equipos: JSON.stringify(equiposData),
    });

    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/save/numeroseries",
      method: "POST",
      data: {
        cliente_ruc_dni: clienteRucDni,
        cliente_documento: clienteDocumento,
        fecha_creacion: $("#fecha_creacion").val(),
        tipo_maquina: tipoMaquina,
        equipos: JSON.stringify(equiposData),
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          console.log("Respuesta del servidor:", data);
          if (data.success) {
            Swal.fire({
              title: "Éxito",
              text: "Registro agregado correctamente",
              icon: "success",
            });
            limpiarFormularioRegistro();
            $("#modalAgregar").modal("hide");
            $("#tabla_clientes").DataTable().ajax.reload();
            cargarUltimoNumeroSerie();
          } else {
            // Marcar campos inválidos sin alerta
            let primeroCampo = null;
            $("#equipos_container .equipo-item").each(function () {
              const $marca  = $(this).find('input[name$="[marca]"]');
              const $modelo = $(this).find('input[name$="[modelo]"]');
              const $equipo = $(this).find('input[name$="[equipo]"]');
              const $ns     = $(this).find('input[name$="[numero_serie]"]');
              if (!$marca.val().trim())  { $marca.addClass("is-invalid");  if (!primeroCampo) primeroCampo = $marca; }
              if (!$modelo.val().trim()) { $modelo.addClass("is-invalid"); if (!primeroCampo) primeroCampo = $modelo; }
              if (!$equipo.val().trim()) { $equipo.addClass("is-invalid"); if (!primeroCampo) primeroCampo = $equipo; }
              if (!$ns.val().trim())     { $ns.addClass("is-invalid");     if (!primeroCampo) primeroCampo = $ns; }
            });
            if (!$("#fecha_creacion").val()) {
              $("#fecha_creacion").addClass("is-invalid");
              if (!primeroCampo) primeroCampo = $("#fecha_creacion");
            }
            if (primeroCampo) {
              primeroCampo[0].scrollIntoView({ behavior: "smooth", block: "center" });
              primeroCampo.focus();
            }
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e);
        }
      },
      error: function () {},
    });
  }
});

// Editar registro
$("#tabla_clientes").on("click", ".btnEditar", function () {
  var idRegistro = $(this).data("id");
  $.ajax({
    url: _URL + "/ajs/getOne/numeroseries",
    method: "POST",
    data: { id: idRegistro },
    dataType: "json",
    success: function (response) {
      if (response.success && response.data && response.data.length > 0) {
        const registro = response.data[0];

        $("#idRegistro").val(registro.id);
        $("#cliente_ruc_dni_u").val(registro.cliente_ruc_dni || "");
        $("#cliente_documento_u").val(registro.cliente_documento || "");
        $("#fecha_creacion_u").val(registro.fecha_creacion);

        // Configurar el checkbox de cliente según si tiene datos de cliente
        const tieneCliente = registro.tiene_cliente && (registro.cliente_ruc_dni && registro.cliente_ruc_dni !== "Sin Cliente" && registro.cliente_ruc_dni !== null);
        $("#tiene_cliente_u").prop("checked", tieneCliente);

        // Configurar el tipo de máquina
        const tipoMaquina = registro.tipo_maquina || 'fabricada';
        $(`input[name="tipo_maquina_u"][value="${tipoMaquina}"]`).prop("checked", true);
        
        // Mostrar/ocultar mensajes según el tipo
        if (tipoMaquina === 'fabricada') {
          $('#mensaje_tipo_fabricada_u').show();
          $('#mensaje_tipo_importada_u').hide();
        } else {
          $('#mensaje_tipo_fabricada_u').hide();
          $('#mensaje_tipo_importada_u').show();
        }

        // Siempre mostrar la sección cliente
        $("#seccion_cliente_u").removeClass("oculta");

        if (tieneCliente) {
          // Datos de cliente externo
          $("#input_datos_cliente_u").val(registro.cliente_documento || "");
          $("#input_datos_cliente_u").prop("readonly", false);
          $("#cliente_ruc_dni_u").prop("readonly", false);
        } else {
          // Datos de la empresa
          $("#input_datos_cliente_u").val("20538381978");
          $("#cliente_documento_u").val("20538381978");
          $("#cliente_ruc_dni_u").val("COMERCIAL & INDUSTRIAL J. V. C. S.A.C.");
          $("#input_datos_cliente_u").prop("readonly", true);
          $("#cliente_ruc_dni_u").prop("readonly", true);
        }

        // Cargar equipos existentes
        $("#equipos_existentes").empty();
        if (registro.equipos && registro.equipos.length > 0) {
          registro.equipos.forEach((equipo, index) => {
            $("#equipos_existentes").append(`
                <div class="equipo-item mb-3 card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Equipo ${index + 1}</h5>
                            <button type="button" class="btn btn-sm btn-danger btn-eliminar-equipo"
                                data-id="${equipo.id || ""}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <div class="row mb-2 producto-repuesto-row">
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fa fa-tags me-1 text-rojo"></i> Tipo
                                </label>
                                <select class="form-select tipo-item-selector"
                                    name="equipos_existentes[${index}][tipo_producto]">
                                    <option value="producto" ${(equipo.tipo_producto || 'producto') === 'producto' ? 'selected' : ''}>Producto</option>
                                    <option value="repuesto" ${equipo.tipo_producto === 'repuesto' ? 'selected' : ''}>Repuesto</option>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">
                                    <i class="fa fa-box me-1 text-rojo"></i>
                                    Producto/Repuesto del almacén
                                    <small class="text-muted">(opcional)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    <input type="text" class="form-control input-buscar-producto"
                                        name="equipos_existentes[${index}][producto_busqueda]"
                                        value="${equipo.producto_codigo ? (equipo.producto_codigo + ' | ' + (equipo.producto_nombre || '')) : ''}"
                                        placeholder="Buscar por código o nombre..." autocomplete="off">
                                    <input type="hidden" class="input-id-producto"
                                        name="equipos_existentes[${index}][id_producto]"
                                        value="${equipo.id_producto || ''}">
                                </div>
                                <small class="text-muted producto-seleccionado-info"></small>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Equipo <span class="text-danger">*</span></label>
                                <select class="form-select select-equipo" name="equipos_existentes[${index}][equipo]" required>
                                    <option value="">Seleccionar equipo...</option>
                                </select>
                                <input type="hidden" name="equipos_existentes[${index}][id]" value="${equipo.id || ''}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label text-muted"><i class="fa fa-tag me-1"></i>Marca / Modelo</label>
                                <input type="text" class="form-control input-marca-modelo" readonly
                                    placeholder="Se completa al elegir equipo"
                                    style="background:#f8f9fa;cursor:default;">
                                <input type="hidden" name="equipos_existentes[${index}][marca]" class="hidden-marca">
                                <input type="hidden" name="equipos_existentes[${index}][modelo]" class="hidden-modelo">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Número de Serie</label>
                                <input type="text" class="form-control"
                                    name="equipos_existentes[${index}][numero_serie]"
                                    value="${equipo.numero_serie || ''}" required>
                                <div class="feedback-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Inicializar select de equipo con valor correcto desde el API
            const $item = $('#equipos_existentes .equipo-item').last();
            inicializarSelectsItem($item, equipo.equipo_id);
          });

          // Actualizar contador
          $("#contador_equipos_existentes").text(registro.equipos.length);
          $("#no_equipos_existentes_message").hide();

          // Verificar series repetidas en equipos existentes
          setTimeout(function () {
            let todasLasSeries = [];
            $('#equipos_existentes input[name$="[numero_serie]"]').each(
              function () {
                const valor = $(this).val().trim();
                if (valor) {
                  todasLasSeries.push(valor);
                }
              }
            );

            const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
            mostrarMensajeSeriesRepetidas(
              seriesRepetidas,
              $("#series_repetidas_equipos_mensaje_u"),
              $("#series_repetidas_equipos_lista_u")
            );
          }, 100);
        } else {
          $("#contador_equipos_existentes").text("0");
          $("#no_equipos_existentes_message").show();
        }

        // Mostrar modal
        $("#updateRegistro").modal("show");
      } else {
        Swal.fire({
          title: "Error",
          text: response.error || "Error al cargar los datos del registro",
          icon: "error",
        });
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Swal.fire({
        title: "Error",
        text: "No se pudieron obtener los datos del registro",
        icon: "error",
      });
    },
  });
});

// Eliminar registro
$("#tabla_clientes").on("click", ".btnBorrar", function () {
  var idRegistro = $(this).data("id");
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: _URL + "/ajs/delete/numeroseries",
        method: "POST",
        data: { id: idRegistro },
        success: function (response) {
          Swal.fire({
            title: "Eliminado",
            text: "El registro ha sido eliminado",
            icon: "success",
          });
          $("#tabla_clientes").DataTable().ajax.reload();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          Swal.fire({
            title: "Error",
            text: "No se pudo eliminar el registro",
            icon: "error",
          });
        },
      });
    }
  });
});

// También modificar la función para actualizar registros
$("#updateRegistroBtn").click(function () {
  // Verificar si hay series repetidas
  let formValido = true;

  if ($("#maquinas_identicas_u").is(":checked")) {
    const series = procesarSeriesMasivas($("#series_masivas_u").val());
    const seriesRepetidas = verificarSeriesRepetidas(series);

    if (seriesRepetidas.length > 0) {
      formValido = false;
      Swal.fire({
        title: "Error",
        text: "Hay números de serie repetidos. Cada número de serie debe ser único.",
        icon: "error",
      });
      return false;
    }

    if ($("#series_duplicadas_mensaje_u").length > 0) {
      formValido = false;
      Swal.fire({
        title: "Error",
        text: "Hay números de serie que ya existen en la base de datos. Por favor, corrígelos antes de continuar.",
        icon: "error",
      });
      return false;
    }

    // Validar series para actualización
    const cantidadEquipos = parseInt($("#cantidad_equipos_nuevos").val());

    if (series.length !== cantidadEquipos) {
      Swal.fire({
        title: "Error",
        text: "La cantidad de números de serie debe coincidir con la cantidad de equipos",
        icon: "error",
      });
      return false;
    }

    const marca = $("#marca_comun_u").val();
    const modelo = $("#modelo_comun_u").val();
    const equipo = $("#equipo_comun_u").val();

    // Crear array de equipos para enviar al servidor
    var equiposData = [];
    // Agregar equipos existentes
    $("#equipos_existentes .equipo-item").each(function () {
      const id = $(this).find('input[name$="[id]"]').val();
      equiposData.push({
        id: id,
        modelo: $(this).find('.hidden-modelo').val(),
        marca: $(this).find('.hidden-marca').val(),
        equipo: $(this).find('.select-equipo').val(),
        id_producto: $(this).find('input.input-id-producto').val() || "",
        tipo_producto: $(this).find('select.tipo-item-selector').val() || 'producto',
        numero_serie: $(this).find('input[name$="[numero_serie]"]').val(),
      });
    });

    // Luego agregar los nuevos equipos con series masivas (un único producto común opcional)
    const idProductoComunU = $("#id_producto_comun_u").val() || "";
    for (let i = 0; i < series.length; i++) {
      equiposData.push({
        modelo: modelo,
        marca: marca,
        equipo: equipo,
        id_producto: idProductoComunU, // NUEVO
        numero_serie: series[i],
      });
    }

    // Preparar datos de cliente - si no tiene cliente, usar datos de empresa
    const tieneCliente = $("#tiene_cliente_u").is(":checked");
    const clienteRucDni = tieneCliente ? $("#cliente_ruc_dni_u").val() : "COMERCIAL & INDUSTRIAL J. V. C. S.A.C.";
    const clienteDocumento = tieneCliente ? $("#cliente_documento_u").val() : "20538381978";
    
    // Obtener el tipo de máquina seleccionado
    const tipoMaquina = $('input[name="tipo_maquina_u"]:checked').val() || 'fabricada';

    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/update/numeroseries",
      method: "POST",
      data: {
        id: $("#idRegistro").val(),
        cliente_ruc_dni: clienteRucDni,
        cliente_documento: clienteDocumento,
        fecha_creacion: $("#fecha_creacion_u").val(),
        tipo_maquina: tipoMaquina,
        equipos: JSON.stringify(equiposData),
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            Swal.fire({
              title: "Éxito",
              text: "Registro actualizado correctamente",
              icon: "success",
            });
            $("#updateRegistro").modal("hide");
            $("#tabla_clientes").DataTable().ajax.reload();
          } else {
            Swal.fire({
              title: "Error",
              text: data.error || "No se pudo actualizar el registro",
              icon: "error",
            });
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e);
          Swal.fire({
            title: "Error",
            text: "Error al procesar la respuesta del servidor",
            icon: "error",
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: "Error",
          text: "No se pudo actualizar el registro: " + errorThrown,
          icon: "error",
        });
      },
    });
  } else {
    // Verificar series repetidas en equipos individuales
    let todasLasSeries = [];
    $(
      '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]'
    ).each(function () {
      const valor = $(this).val().trim();
      if (valor) {
        todasLasSeries.push(valor);
      }
    });

    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
    if (seriesRepetidas.length > 0) {
      Swal.fire({
        title: "Error",
        text: "Hay números de serie repetidos en los equipos. Cada número de serie debe ser único.",
        icon: "error",
      });
      return false;
    }

    // Verificar cada campo de número de serie individual
    let haySeriesDuplicadas = false;
    $(
      '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]'
    ).each(function () {
      if ($(this).hasClass("is-invalid")) {
        haySeriesDuplicadas = true;
        return false; // Salir del bucle each
      }
    });

    if (haySeriesDuplicadas) {
      Swal.fire({
        title: "Error",
        text: "Hay números de serie que ya existen en la base de datos. Por favor, corrígelos antes de continuar.",
        icon: "error",
      });
      return false;
    }

    // Procesar equipos individuales para actualización
    var equiposData = [];

    // Agregar equipos existentes
    $("#equipos_existentes .equipo-item").each(function () {
      const id = $(this).find('input[name$="[id]"]').val();
      equiposData.push({
        id: id,
        modelo: $(this).find('.hidden-modelo').val(),
        marca: $(this).find('.hidden-marca').val(),
        equipo: $(this).find('.select-equipo').val(),
        id_producto: $(this).find('input.input-id-producto').val() || "",
        tipo_producto: $(this).find('select.tipo-item-selector').val() || 'producto',
        numero_serie: $(this).find('input[name$="[numero_serie]"]').val(),
      });
    });

    // Agregar nuevos equipos
    $("#equipos_container_u .equipo-item").each(function () {
      equiposData.push({
        modelo: $(this).find('.hidden-modelo').val(),
        marca: $(this).find('.hidden-marca').val(),
        equipo: $(this).find('.select-equipo').val(),
        id_producto: $(this).find('input.input-id-producto').val() || "",
        tipo_producto: $(this).find('select.tipo-item-selector').val() || 'producto',
        numero_serie: $(this).find('input[name$="[numero_serie]"]').val(),
      });
    });

    // Preparar datos de cliente - si no tiene cliente, usar datos de empresa
    const tieneCliente = $("#tiene_cliente_u").is(":checked");
    const clienteRucDni = tieneCliente ? $("#cliente_ruc_dni_u").val() : "COMERCIAL & INDUSTRIAL J. V. C. S.A.C.";
    const clienteDocumento = tieneCliente ? $("#cliente_documento_u").val() : "20538381978";
    
    // Obtener el tipo de máquina seleccionado
    const tipoMaquina = $('input[name="tipo_maquina_u"]:checked').val() || 'fabricada';

    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/update/numeroseries",
      method: "POST",
      data: {
        id: $("#idRegistro").val(),
        cliente_ruc_dni: clienteRucDni,
        cliente_documento: clienteDocumento,
        fecha_creacion: $("#fecha_creacion_u").val(),
        tipo_maquina: tipoMaquina,
        equipos: JSON.stringify(equiposData),
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            Swal.fire({
              title: "Éxito",
              text: "Registro actualizado correctamente",
              icon: "success",
            });
            $("#updateRegistro").modal("hide");
            $("#tabla_clientes").DataTable().ajax.reload();
          } else {
            Swal.fire({
              title: "Error",
              text: data.error || "No se pudo actualizar el registro",
              icon: "error",
            });
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e);
          Swal.fire({
            title: "Error",
            text: "Error al procesar la respuesta del servidor",
            icon: "error",
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: "Error",
          text: "No se pudo actualizar el registro: " + errorThrown,
          icon: "error",
        });
      },
    });
  }
});

// Función para agregar un equipo diferente (edición)
$("#agregar_equipo_diferente_u").click(function () {
  const index = $("#equipos_container_u .equipo-item").length;

  // Calcular siguiente número de serie (mismo patrón que modal de agregar)
  var ultimoNumero;
  var $allSeries = $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]');
  var lastVal = parseInt($allSeries.last().val());
  if ($allSeries.length > 0 && !isNaN(lastVal)) {
    ultimoNumero = lastVal;
  } else {
    ultimoNumero = parseInt($('#ultimo_numero_serie_u').val()) || 0;
  }
  var siguiente = ultimoNumero + 1;

  // Ocultar el mensaje de "no hay equipos nuevos"
  $("#no_equipos_nuevos_message").hide();

  $("#equipos_container_u").append(`
    <div class="equipo-item card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Equipo nuevo ${index + 1}</h5>
                <button type="button" class="btn btn-sm btn-danger btn-eliminar-equipo-nuevo">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <div class="row mb-2 producto-repuesto-row">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fa fa-tags me-1 text-rojo"></i> Tipo
                    </label>
                    <select class="form-select tipo-item-selector" name="equipos_nuevos[${index}][tipo_producto]">
                        <option value="producto">Producto</option>
                        <option value="repuesto">Repuesto</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label">
                        <i class="fa fa-box me-1 text-rojo"></i>
                        Producto/Repuesto del almacén
                        <small class="text-muted">(opcional)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" class="form-control input-buscar-producto"
                            name="equipos_nuevos[${index}][producto_busqueda]"
                            placeholder="Buscar por código o nombre..." autocomplete="off">
                        <input type="hidden" class="input-id-producto"
                            name="equipos_nuevos[${index}][id_producto]" value="">
                    </div>
                    <small class="text-muted producto-seleccionado-info"></small>
                </div>
            </div>
            <div class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Equipo <span class="text-danger">*</span></label>
                    <select class="form-select select-equipo" name="equipos_nuevos[${index}][equipo]" required>
                        <option value="">Seleccionar equipo...</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label text-muted"><i class="fa fa-tag me-1"></i>Marca / Modelo</label>
                    <input type="text" class="form-control input-marca-modelo" readonly
                        placeholder="Se completa al elegir equipo"
                        style="background:#f8f9fa;cursor:default;">
                    <input type="hidden" name="equipos_nuevos[${index}][marca]" class="hidden-marca">
                    <input type="hidden" name="equipos_nuevos[${index}][modelo]" class="hidden-modelo">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">Número de Serie</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="equipos_nuevos[${index}][numero_serie]"
                            placeholder="Número de Serie" value="${siguiente}" required>
                        <button type="button" class="btn btn-generar-serie" title="Generar número de serie">
                            <i class="fa fa-magic"></i>
                        </button>
                    </div>
                    <div class="feedback-container"></div>
                </div>
            </div>
        </div>
    </div>
`);
  // Inicializar select de equipo con opciones actuales
  const $nuevo = $('#equipos_container_u .equipo-item').last();
  inicializarSelectsItem($nuevo);
  // Actualizar contador
  $("#contador_equipos_nuevos").text(index + 1);

  // Verificar series repetidas después de agregar un nuevo equipo
  setTimeout(function () {
    let todasLasSeries = [];
    $(
      '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]'
    ).each(function () {
      const valor = $(this).val().trim();
      if (valor) {
        todasLasSeries.push(valor);
      }
    });

    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
    mostrarMensajeSeriesRepetidas(
      seriesRepetidas,
      $("#series_repetidas_equipos_mensaje_u"),
      $("#series_repetidas_equipos_lista_u")
    );
  }, 100);
});