//   public\js\series\crud-registro.js

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

    // Validar que se hayan seleccionado todos los campos
    if (!marca || !modelo || !equipo) {
      Swal.fire({
        title: "Error",
        text: "Por favor, seleccione marca, modelo y equipo",
        icon: "error",
      });
      return false;
    }

    // Crear array de equipos para enviar al servidor
    var equiposData = [];
    for (let i = 0; i < series.length; i++) {
      equiposData.push({
        modelo: modelo,
        marca: marca,
        equipo: equipo,
        numero_serie: series[i],
      });
    }

    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/save/numeroseries",
      method: "POST",
      data: {
        cliente_ruc_dni: $("#cliente_ruc_dni").val(),
        cliente_documento: $("#cliente_documento").val(),
        fecha_creacion: $("#fecha_creacion").val(),
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

    // Verificar que todos los campos estén seleccionados
    let camposIncompletos = false;
    $("#equipos_container .equipo-item").each(function () {
      const marca = $(this).find('select[name$="[marca]"]').val();
      const modelo = $(this).find('select[name$="[modelo]"]').val();
      const equipo = $(this).find('select[name$="[equipo]"]').val();
      const numeroSerie = $(this).find('input[name$="[numero_serie]"]').val();

      if (!marca || !modelo || !equipo || !numeroSerie) {
        camposIncompletos = true;
        return false;
      }
    });

    if (camposIncompletos) {
      Swal.fire({
        title: "Error",
        text: "Por favor, complete todos los campos de los equipos",
        icon: "error",
      });
      return false;
    }

    // Procesar equipos individuales
    var equiposData = [];
    $("#equipos_container .equipo-item").each(function (index) {
      equiposData.push({
        modelo: $(this).find('select[name^="equipos"][name$="[modelo]"]').val(),
        marca: $(this).find('select[name^="equipos"][name$="[marca]"]').val(),
        equipo: $(this).find('select[name^="equipos"][name$="[equipo]"]').val(),
        numero_serie: $(this)
          .find('input[name^="equipos"][name$="[numero_serie]"]')
          .val(),
      });
    });
    console.log("Datos a enviar:", {
      cliente_ruc_dni: $("#cliente_ruc_dni").val(),
      cliente_documento: $("#cliente_documento").val(),
      fecha_creacion: $("#fecha_creacion").val(),
      equipos: JSON.stringify(equiposData),
    });
    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/save/numeroseries",
      method: "POST",
      data: {
        cliente_ruc_dni: $("#cliente_ruc_dni").val(),
        cliente_documento: $("#cliente_documento").val(),
        fecha_creacion: $("#fecha_creacion").val(),
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
        $("#cliente_ruc_dni_u").val(registro.cliente_ruc_dni);
        $("#fecha_creacion_u").val(registro.fecha_creacion);

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
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Marca</label>
                                <div class="input-group">
                                    <select class="form-select" name="equipos_existentes[${index}][marca]" required>
                                        <option value="">Seleccionar Marca</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary btn-seleccionar-marca"
                                        data-bs-toggle="modal" data-bs-target="#modalMarca">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <input type="hidden" name="equipos_existentes[${index}][id]" 
                                        value="${equipo.id || ""}">
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Modelo</label>
                                <div class="input-group">
                                    <select class="form-select" name="equipos_existentes[${index}][modelo]" required>
                                        <option value="">Seleccionar Modelo</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary btn-seleccionar-modelo"
                                        data-bs-toggle="modal" data-bs-target="#modalModelo">
                                        <i class="fa fa-list"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Equipo</label>
                                <div class="input-group">
                                    <select class="form-select" name="equipos_existentes[${index}][equipo]" required>
                                        <option value="">Seleccionar Equipo</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary btn-seleccionar-equipo"
                                        data-bs-toggle="modal" data-bs-target="#modalEquipo">
                                        <i class="fa fa-list"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Número de Serie</label>
                                <input type="text" class="form-control" 
                                    name="equipos_existentes[${index}][numero_serie]" 
                                    value="${
                                      equipo.numero_serie || ""
                                    }" required>
                                <div class="feedback-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            // Después de agregar el HTML, necesitas cargar los datos en los selects
            // y seleccionar el valor correcto para cada equipo
            // Agrega este código justo después del append

            // Cargar datos en los selects
            cargarSelectMarcas();
            cargarSelectModelos();
            cargarSelectEquipos();

            // Seleccionar los valores correctos para este equipo
            // Esto debe ejecutarse después de que los selects se hayan cargado
            setTimeout(function () {
              console.log(
                "Marca del equipo:",
                equipo.marca,
                "tipo:",
                typeof equipo.marca
              );
              console.log(
                "Modelo del equipo:",
                equipo.modelo,
                "tipo:",
                typeof equipo.modelo
              );
              console.log(
                "Opciones de marca disponibles:",
                $(`select[name="equipos_existentes[${index}][marca]"] option`)
                  .map(function () {
                    return { value: $(this).val(), text: $(this).text() };
                  })
                  .get()
              );
              // Seleccionar la marca correcta por ID
              $(
                `select[name="equipos_existentes[${index}][marca]"] option[value="${equipo.marca}"]`
              ).prop("selected", true);

              // Seleccionar el modelo correcto por ID
              $(
                `select[name="equipos_existentes[${index}][modelo]"] option[value="${equipo.modelo}"]`
              ).prop("selected", true);

              // Seleccionar el equipo correcto por ID (si tienes el ID del equipo)
              if (equipo.equipo) {
                $(
                  `select[name="equipos_existentes[${index}][equipo]"] option[value="${equipo.equipo}"]`
                ).prop("selected", true);
              }
            }, 500);
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
        modelo: $(this).find('select[name$="[modelo]"]').val(),
        marca: $(this).find('select[name$="[marca]"]').val(),
        equipo: $(this).find('select[name$="[equipo]"]').val(),
        numero_serie: $(this).find('input[name$="[numero_serie]"]').val(),
      });
    });

    // Luego agregar los nuevos equipos con series masivas
    for (let i = 0; i < series.length; i++) {
      equiposData.push({
        modelo: modelo,
        marca: marca,
        equipo: equipo,
        numero_serie: series[i],
      });
    }

    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/update/numeroseries",
      method: "POST",
      data: {
        id: $("#idRegistro").val(),
        cliente_ruc_dni: $("#cliente_ruc_dni_u").val(),
        cliente_documento: $("#cliente_documento_u").val(),
        fecha_creacion: $("#fecha_creacion_u").val(),
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
        modelo: $(this).find('select[name$="[modelo]"]').val(),
        marca: $(this).find('select[name$="[marca]"]').val(),
        equipo: $(this).find('select[name$="[equipo]"]').val(),
        numero_serie: $(this).find('input[name$="[numero_serie]"]').val(),
      });
    });

    // Agregar nuevos equipos
    $("#equipos_container_u .equipo-item").each(function () {
      equiposData.push({
        modelo: $(this).find('select[name$="[modelo]"]').val(),
        marca: $(this).find('select[name$="[marca]"]').val(),
        equipo: $(this).find('select[name$="[equipo]"]').val(),
        numero_serie: $(this).find('input[name$="[numero_serie]"]').val(),
      });
    });
    // Enviar los datos al servidor
    $.ajax({
      url: _URL + "/ajs/update/numeroseries",
      method: "POST",
      data: {
        id: $("#idRegistro").val(),
        cliente_ruc_dni: $("#cliente_ruc_dni_u").val(),
        cliente_documento: $("#cliente_documento_u").val(),
        fecha_creacion: $("#fecha_creacion_u").val(),
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
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Marca</label>
                    <div class="input-group">
                        <select class="form-select" name="equipos_nuevos[${index}][marca]" required>
                            <option value="">Seleccionar Marca</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-seleccionar-marca"
                            data-bs-toggle="modal" data-bs-target="#modalMarca">
                            <i class="fa fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Modelo</label>
                    <div class="input-group">
                        <select class="form-select" name="equipos_nuevos[${index}][modelo]" required>
                            <option value="">Seleccionar Modelo</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-seleccionar-modelo"
                            data-bs-toggle="modal" data-bs-target="#modalModelo">
                            <i class="fa fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Equipo</label>
                    <div class="input-group">
                        <select class="form-select" name="equipos_nuevos[${index}][equipo]" required>
                            <option value="">Seleccionar Equipo</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-seleccionar-equipo"
                            data-bs-toggle="modal" data-bs-target="#modalEquipo">
                            <i class="fa fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Número de Serie</label>
                    <input type="text" class="form-control" name="equipos_nuevos[${index}][numero_serie]" 
                        placeholder="Número de Serie" required>
                    <div class="feedback-container"></div>
                </div>
            </div>
        </div>
    </div>
`);
  // Cargar los datos en los selects
  cargarSelectMarcas();
  cargarSelectModelos();
  cargarSelectEquipos();
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
