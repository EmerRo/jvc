// public/js/orden-trabajo.js
$(document).ready(() => {
  const app = new Vue({
    el: "#client",
    data: {
      prealerta: {
        num_doc: "",
        cliente_Rsocial: "",
      },
      cantidadEquipos: 1,
      equipos: [
        {
          marca: "",
          modelo: "",
          equipo: "",
          serie: "",
        },
      ],
      marcasDisponibles: [],
      modelosDisponibles: [],
      equiposDisponibles: [],
      // Datos para edición
      editando: {
        id_orden_trabajo: null,
        cliente_Rsocial: "",
        cliente_ruc: "",
        atencion_Encargado: "",
        fecha_ingreso: "",
        fecha_salida: "",
        observaciones: "",
        equipos: [],
      },
      maquinasIdenticas: false,
      cantidadMaquinasIdenticas: 1,
      seriesMultiples: "",

      validationErrors: {
        num_doc: "",
        cliente_Rsocial: "",
        tecnico: "",
        fecha_ingreso: "",
        fecha_salida: "",

        cantidadEquipos: "",
        marca: "",
        modelo: "",
        equipo: "",
        cantidad: "",
        series: "",
      },
      seriesCount: 0,
    },

    methods: {
      buscarDocumentSS() {
        const docLength = this.prealerta.num_doc.length;
        if (docLength === 8 || docLength === 11) {
          $("#loader-menor").show();
          this.prealerta.dir_pos = 1;

          _ajax(
            "/ajs/prealerta/doc/cliente",
            "POST",
            {
              doc: this.prealerta.num_doc,
            },
            (resp) => {
              $("#loader-menor").hide();
              console.log(resp);

              if (docLength === 8) {
                // Para DNI
                if (resp.success) {
                  this.prealerta.cliente_Rsocial =
                    resp.nombres +
                    " " +
                    (resp.apellidoPaterno ? resp.apellidoPaterno : "") +
                    " " +
                    (resp.apellidoMaterno ? resp.apellidoMaterno : "");
                  // Limpiar dirección para DNI
                  $("#direccion").val("");
                } else {
                  alertAdvertencia("Documento no encontrado");
                }
              } else if (docLength === 11) {
                // Para RUC
                if (resp.razonSocial) {
                  this.prealerta.cliente_Rsocial = resp.razonSocial;
                  // Solo mostrar dirección si el RUC empieza con 20
                  if (this.prealerta.num_doc.startsWith("20")) {
                    $("#direccion").val(resp.direccion || "");
                  } else {
                    $("#direccion").val("");
                  }
                } else {
                  alertAdvertencia("RUC no encontrado");
                }
              }
            }
          );
        } else {
          alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
        }
      },

      cargarDatosEdicion(id) {
        // Reiniciar el estado antes de cargar nuevos datos
        this.editando = {
          id_orden_trabajo: null,
          cliente_Rsocial: "",
          cliente_ruc: "",
          atencion_Encargado: "",
          fecha_ingreso: "",
          fecha_salida: "",
          observaciones: "",
          equipos: [],
        };

        $("#loader-menor").show();
        $.ajax({
          url: _URL + "/ajs/orden-trabajo/detalles",
          type: "POST",
          data: { id: id },
          success: (response) => {
            $("#loader-menor").hide();
            try {
              if (
                typeof response === "string" &&
                (response.trim().toLowerCase().startsWith("<!doctype") ||
                  response.trim().toLowerCase().startsWith("<html"))
              ) {
                console.error(
                  "La respuesta del servidor es HTML, no JSON:",
                  response.substring(0, 100) + "..."
                );
                throw new Error(
                  "El servidor devolvió HTML en lugar de JSON. Posible sesión expirada."
                );
              }

              const datos =
                typeof response === "object" ? response : JSON.parse(response);

              // Cargar datos principales
              this.editando.id_orden_trabajo = datos.id_orden_trabajo;
              this.editando.cliente_Rsocial = datos.cliente_razon_social;
              this.editando.cliente_ruc = datos.cliente_ruc;
              this.editando.atencion_Encargado = datos.atencion_encargado;
              this.editando.fecha_ingreso = datos.fecha_ingreso;
              this.editando.fecha_salida = datos.fecha_salida;
              this.editando.observaciones = datos.observaciones || "";

              // Cargar equipos
              this.editando.equipos = datos.equipos || [];

              // Actualizar campos del formulario
              $("#edit_id_orden_trabajo").val(datos.id_orden_trabajo);
              $("#edit_cliente_Rsocial").val(datos.cliente_razon_social);
              $("#edit_cliente_ruc").val(datos.cliente_ruc);
              $("#edit_atencion_Encargado").val(datos.atencion_encargado);
              $("#edit_fecha_ingreso").val(datos.fecha_ingreso);
              $("#edit_fecha_salida").val(datos.fecha_salida);
              $("#edit_observaciones").val(datos.observaciones || "");

              // Mostrar el modal
              $("#modalEditar").modal("show");
            } catch (error) {
              console.error("Error al procesar los datos:", error);
              Swal.fire({
                icon: "error",
                title: "Error",
                text:
                  "Error al cargar los datos para edición. " +
                  (error.message || ""),
                footer:
                  '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            }
          },
          error: (xhr, status, error) => {
            $("#loader-menor").hide();
            console.error("Error en la petición:", { xhr, status, error });

            if (
              xhr.responseText &&
              (xhr.responseText.trim().toLowerCase().startsWith("<!doctype") ||
                xhr.responseText.trim().toLowerCase().startsWith("<html"))
            ) {
              console.error(
                "La respuesta del servidor es HTML, no JSON:",
                xhr.responseText.substring(0, 100) + "..."
              );
              Swal.fire({
                icon: "error",
                title: "Error de sesión",
                text: "Es posible que tu sesión haya expirado. Intenta recargar la página.",
                footer:
                  '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al obtener los datos del registro",
                footer:
                  '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            }
          },
          timeout: 15000,
        });
      },

      agregarEquipoEdicion() {
        this.editando.equipos.push({
          id: null,
          marca: "",
          equipo: "",
          modelo: "",
          numero_serie: "",
        });
      },

      eliminarEquipoEdicion(index) {
        this.editando.equipos.splice(index, 1);
      },

      guardarEdicion() {
        if (
          !this.editando.cliente_Rsocial ||
          !this.editando.atencion_Encargado ||
          !this.editando.fecha_ingreso
        ) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "Por favor complete todos los campos requeridos",
          });
          return;
        }
        // Validar fecha de salida
        if (!this.editando.fecha_salida) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "La fecha de salida es requerida",
          });
          return;
        }

        // Validar que fecha_salida sea posterior a fecha_ingreso
        if (this.editando.fecha_ingreso && this.editando.fecha_salida) {
          const fechaIngreso = new Date(this.editando.fecha_ingreso);
          const fechaSalida = new Date(this.editando.fecha_salida);

          if (fechaSalida <= fechaIngreso) {
            Swal.fire({
              icon: "warning",
              title: "Advertencia",
              text: "La fecha de salida debe ser posterior a la fecha de ingreso",
            });
            return;
          }
        }

        // Validar equipos individuales
        const equiposValidos = this.editando.equipos.every(
          (equipo) =>
            equipo.marca &&
            equipo.equipo &&
            equipo.modelo &&
            equipo.numero_serie
        );

        if (!equiposValidos) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "Por favor complete todos los datos de los equipos",
          });
          return;
        }

        const data = {
          id_orden_trabajo: this.editando.id_orden_trabajo,
          cliente_razon_social: this.editando.cliente_Rsocial,
          cliente_ruc: this.editando.cliente_ruc,
          atencion_encargado: this.editando.atencion_Encargado,
          fecha_ingreso: this.editando.fecha_ingreso,
          fecha_salida: this.editando.salida,
          observaciones: this.editando.observaciones,
          equipos: this.editando.equipos,
        };

        $("#loader-menor").show();

        $.ajax({
          type: "POST",
          url: _URL + "/ajs/orden-trabajo/update",
          data: data,
          success: (resp) => {
            $("#loader-menor").hide();
            try {
              const response =
                typeof resp === "object" ? resp : JSON.parse(resp);
              if (response && response.success) {
                tabla_clientes.ajax.reload(null, false);
                Swal.fire({
                  icon: "success",
                  title: "Éxito",
                  text: "Registro actualizado correctamente",
                });
                $("#modalEditar").modal("hide");
              } else {
                throw new Error(response.error || "Error al actualizar");
              }
            } catch (error) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text:
                  error.message ||
                  "Error al procesar la respuesta del servidor",
              });
            }
          },
          error: (xhr, status, error) => {
            $("#loader-menor").hide();
            console.error("Error en la petición:", error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al intentar actualizar el registro",
            });
          },
        });
      },
    },
  });

  // DataTables initialization para ORDEN DE TRABAJO
  tabla_clientes = $("#tabla_clientes").DataTable({
    paging: true,
    bFilter: true,
    ordering: true,
    searching: true,
    destroy: true,
    responsive: true,
    scrollX: false,
    autoWidth: false,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
    ajax: {
      url: _URL + "/ajs/orden-trabajo/render", // Cambio aquí
      method: "POST",
      dataSrc: "",
    },
    language: {
      url: "ServerSide/Spanish.json",
    },
    columns: [
      // {
      //   data: null,
      //   class: "text-center",
      //   render: (data, type, row, meta) => meta.row + 1,
      // },
      {
        data: "numero", // CAMBIO: usar campo numero en lugar de meta.row + 1
        class: "text-center",
      },
      { data: "cliente_razon_social", class: "text-center" },
      { data: "cliente_ruc", class: "text-center" },
      { data: "atencion_encargado", class: "text-center" },
      { data: "fecha_ingreso", class: "text-center" },
      { data: "fecha_salida", class: "text-center" },

      {
        data: null,
        class: "text-center",
        render: (data, type, row) => {
          // Convertir a número para asegurar una comparación correcta
          const tieneCotizacion = Number.parseInt(row.tiene_cotizacion);

          // Estilo común para ambos badges
          const badgeStyle = `
            display: inline-block;
            width: 130px;
            text-align: center;
            padding: 5px 10px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 0.70rem;
            text-transform: uppercase;
            white-space: nowrap;
          `;

          if (tieneCotizacion === 1) {
            // Badge verde para "COTIZACIÓN LISTA"
            return `
              <span 
                onclick="verCotizacion(${row.id_orden_trabajo})" 
                class="badge" 
                style="${badgeStyle} background-color: #28a745; color: white; cursor: pointer;"
              >
                <i class="fa fa-check-circle" style="margin-right: 5px;"></i>FINALIZADO
              </span>`;
          } else {
            // Badge naranja para "SIN COTIZACIÓN"
            return `
              <span 
                class="badge" 
                style="${badgeStyle} background-color: #ffa500; color: white;"
              >
                <i class="fa fa-clock" style="margin-right: 5px;"></i>EN PRODUCCIÓN
              </span>`;
          }
        },
      },
      {
        data: null,
        class: "text-center",
        render: (data, type, row) => `
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-info btn-ver-detalles" data-id="${row.id_orden_trabajo}" 
                            data-bs-toggle="tooltip" title="Ver detalles">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button data-id="${row.id_orden_trabajo}" class="btn btn-warning btnEditar" 
                            data-bs-toggle="tooltip" title="Editar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button data-id="${row.id_orden_trabajo}" class="btn btn-danger btnBorrar" 
                            data-bs-toggle="tooltip" title="Eliminar">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `,
      },
    ],
    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });

  window.verCotizacion = () => {
    window.location.href = `/taller/coti/view/`;
  };

  function mostrarDetalles(id) {
    $.ajax({
      url: _URL + "/ajs/orden-trabajo/detalles", // Cambio aquí
      type: "POST",
      data: { id: id },
      success: (response) => {
        try {
          if (
            typeof response === "string" &&
            (response.trim().toLowerCase().startsWith("<!doctype") ||
              response.trim().toLowerCase().startsWith("<html"))
          ) {
            console.error(
              "La respuesta del servidor es HTML, no JSON:",
              response.substring(0, 100) + "..."
            );
            throw new Error(
              "El servidor devolvió HTML en lugar de JSON. Posible sesión expirada."
            );
          }

          var detalles =
            typeof response === "object" ? response : JSON.parse(response);

          var contenidoModal = `
                <div class="card border-danger mb-2">
                    <div class="card-header bg-secondary p-2">Información de Orden de Trabajo</div>
                    <div class="card-body p-2">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Cliente:</strong> ${detalles.cliente_razon_social}</p>
                                <p class="mb-1"><strong>Técnico:</strong> ${detalles.atencion_encargado}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Documento:</strong> ${detalles.cliente_ruc}</p>
                                <p class="mb-1"><strong>Fecha de Ingreso:</strong> ${detalles.fecha_ingreso}</p>

                            </div>
                        </div>
                    </div>
                </div>
  
                <div class="card border-danger mb-2">
                    <div class="card-header bg-secondary p-2">
                        Equipos Registrados: ${detalles.equipos.length}
                    </div>
                    <div class="card-body p-2">
                     <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
    <table class="table table-bordered table-striped mb-0">
        <thead class="table-danger sticky-top bg-danger">
            <tr>
                <th>#</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Equipo</th>
                <th>Número de Serie</th>
            </tr>
        </thead>
                                <tbody>
            `;

          detalles.equipos.forEach((equipo, index) => {
            contenidoModal += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${equipo.marca}</td>
                        <td>${equipo.modelo}</td>
                        <td>${equipo.equipo}</td>
                        <td>${equipo.numero_serie}</td>
                    </tr>
                `;
          });

          contenidoModal += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card border-danger">
                    <div class="card-header bg-secondary p-2">
                        Observaciones
                    </div>
                    <div class="card-body p-2">
                        <p class="mb-0">${
                          detalles.observaciones || "Sin observaciones"
                        }</p>
                    </div>
                </div>
            `;

          $("#modalDetalles .modal-body").html(contenidoModal);
          $("#modalDetalles").modal("show");
        } catch (error) {
          console.error("Error al procesar la respuesta:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al procesar los detalles. " + (error.message || ""),
            footer:
              '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        }
      },
      error: (xhr, status, error) => {
        if (
          xhr.responseText &&
          (xhr.responseText.trim().toLowerCase().startsWith("<!doctype") ||
            xhr.responseText.trim().toLowerCase().startsWith("<html"))
        ) {
          console.error(
            "La respuesta del servidor es HTML, no JSON:",
            xhr.responseText.substring(0, 100) + "..."
          );
          Swal.fire({
            icon: "error",
            title: "Error de sesión",
            text: "Es posible que tu sesión haya expirado. Intenta recargar la página.",
            footer:
              '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los detalles. " + error,
            footer:
              '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        }
        console.error("Error fetching details:", error, xhr, status);
      },
      timeout: 15000,
    });
  }

  function cargarTablaTecnicos() {
    $.get(_URL + "/ajs/get/tecnicos", (data) => {
      let html = "";
      JSON.parse(data).forEach((tecnico) => {
        html += `
            <tr data-id="${tecnico.id}">
                <td class="nombre-campo">${tecnico.nombre}</td>
                <td>
                    <button class="btn btn-sm editar-tecnico" style="color: #0d6efd;">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm eliminar-tecnico" style="color: #dc3545;">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
      });
      $("#tablaTecnicos tbody").html(html);
    });
  }

  function habilitarEdicionEnLinea(td, tipo) {
    const texto = td.text();
    const id = td.parent().data("id");
    td.html(`
    <div class="input-group input-group-sm">
        <input type="text" class="form-control" value="${texto}">
        <button class="btn btn-success btn-guardar-${tipo}" data-id="${id}">
            <i class="fa fa-check"></i>
        </button>
        <button class="btn btn-danger btn-cancelar-edicion">
            <i class="fa fa-times"></i>
        </button>
    </div>
`);
  }

  function handleModalBackdrop(modalId) {
    const mainModal = document.getElementById("modalAgregar");

    $(`#${modalId}`).on("show.bs.modal", () => {
      $(mainModal).addClass("blur-background");
      $(".modal-backdrop").addClass("modal-backdrop-blur");
    });

    $(`#${modalId}`).on("hidden.bs.modal", () => {
      $(mainModal).removeClass("blur-background");
      $(".modal-backdrop").removeClass("modal-backdrop-blur");
    });
  }

  ["modalMarca", "modalModelo", "modalEquipo", "modalTecnico"].forEach(
    handleModalBackdrop
  );

  $(document).on("click", ".editar-tecnico", function () {
    const td = $(this).closest("tr").find(".nombre-campo");
    habilitarEdicionEnLinea(td, "tecnico");
  });

  $(document).on("click", ".btn-guardar-tecnico", function () {
    const id = $(this).data("id");
    const td = $(this).closest("td");
    const nuevoNombre = td.find("input").val();

    $.ajax({
      url: _URL + "/ajs/update/tecnicos",
      type: "POST",
      data: { id: id, nombre: nuevoNombre },
      success: (response) => {
        cargarTablaTecnicos();
        cargarSelect("tecnicos", "#atencion_Encargado");
        mostrarAlerta("Éxito", "Técnico actualizado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo actualizar el técnico", "error");
      },
    });
  });

  // ===== FUNCIÓN PARA INICIALIZAR AUTOCOMPLETADO =====
  function inicializarAutocompletadoSeries() {
    console.log("Inicializando autocompletado de series...");

    // Verificar que el elemento existe
    if ($("#input_buscar_serie").length === 0) {
      console.error("Elemento #input_buscar_serie no encontrado");
      return;
    }

    // Destruir autocompletado existente si existe
    if ($("#input_buscar_serie").hasClass("ui-autocomplete-input")) {
      $("#input_buscar_serie").autocomplete("destroy");
    }

    // Configurar autocompletado para series
    $("#input_buscar_serie").autocomplete({
      source: function (request, response) {
        console.log("Buscando series con término:", request.term);
        $.ajax({
          url: _URL + "/ajs/prealerta/buscar/serie/datos",
          type: "GET",
          data: { term: request.term },
          success: function (data) {
            console.log("Datos recibidos:", data);
            try {
              const series = JSON.parse(data);
              console.log("Series parseadas:", series);
              response(series);
            } catch (e) {
              console.error("Error al parsear datos:", e);
              response([]);
            }
          },
          error: function (xhr, status, error) {
            console.error("Error en petición:", error);
            response([]);
          },
        });
      },
      minLength: 0,
      delay: 300,
      select: function (event, ui) {
        event.preventDefault();
        console.log("Serie seleccionada:", ui.item);

        // Llenar los campos del formulario
        app.prealerta.cliente_Rsocial = ui.item.cliente_ruc_dni || "";
        app.prealerta.num_doc =
          ui.item.cliente_documento || ui.item.cliente_ruc_dni || "";

        // También llenar el campo del lado derecho
        $("#cliente_nombre_right").val(ui.item.cliente_ruc_dni || "");

        // Crear equipo con los datos de la serie
        const equipo = {
          marca: ui.item.marca_nombre || "",
          modelo: ui.item.modelo_nombre || "",
          equipo: ui.item.equipo_nombre || "",
          serie: ui.item.value || "",
        };

        // Actualizar equipos
        if (app.equipos.length > 0) {
          app.equipos[0] = equipo;
        } else {
          app.equipos.push(equipo);
        }

        app.cantidadEquipos = 1;

        // Limpiar campo
        $(this).val("");

        // Mostrar mensaje de éxito
        if (typeof Swal !== "undefined") {
          Swal.fire({
            icon: "success",
            title: "Serie seleccionada",
            text: `Serie ${ui.item.value} agregada correctamente`,
            timer: 2000,
            showConfirmButton: false,
          });
        }
      },
      open: function () {
        console.log("Dropdown de autocompletado abierto");
        $(this)
          .autocomplete("widget")
          .css({
            "max-height": "250px",
            "overflow-y": "auto",
            "overflow-x": "hidden",
            width: $(this).outerWidth() + "px",
            "z-index": "9999",
          });
      },
      close: function () {
        console.log("Dropdown de autocompletado cerrado");
      },
    });

    // Eventos para mostrar dropdown al hacer focus/click
    $("#input_buscar_serie")
      .off("focus.autocomplete click.autocomplete")
      .on("focus.autocomplete click.autocomplete", function () {
        console.log("Focus/Click en campo de serie");
        if (!$(this).autocomplete("widget").is(":visible")) {
          $(this).autocomplete("search", "");
        }
      });

    console.log("Autocompletado de series inicializado correctamente");
  }

  function inicializarAutocompletadoCliente() {
    console.log("Inicializando autocompletado de cliente...");

    // Verificar que el elemento existe
    if ($("#input_buscar_cliente_prealerta").length === 0) {
      console.error("Elemento #input_buscar_cliente_prealerta no encontrado");
      return;
    }

    // Destruir autocompletado existente si existe
    if (
      $("#input_buscar_cliente_prealerta").hasClass("ui-autocomplete-input")
    ) {
      $("#input_buscar_cliente_prealerta").autocomplete("destroy");
    }

    // Configurar autocompletado para cliente
    $("#input_buscar_cliente_prealerta").autocomplete({
      source: function (request, response) {
        console.log("Buscando clientes con término:", request.term);
        $.ajax({
          url: _URL + "/ajs/prealerta/buscar/cliente/serie",
          type: "GET",
          data: { term: request.term },
          success: function (data) {
            console.log("Datos de clientes recibidos:", data);
            try {
              const clientes = JSON.parse(data);
              console.log("Clientes parseados:", clientes);
              response(clientes);
            } catch (e) {
              console.error("Error al parsear datos de clientes:", e);
              response([]);
            }
          },
          error: function (xhr, status, error) {
            console.error("Error en petición de clientes:", error);
            response([]);
          },
        });
      },
      minLength: 0,
      delay: 300,
      select: function (event, ui) {
        event.preventDefault();
        console.log("Cliente seleccionado:", ui.item);

        app.prealerta.cliente_Rsocial = ui.item.label || "";
        // app.prealerta.num_doc = ui.item.value || '';
        app.prealerta.num_doc = ui.item.cliente_documento || "";

        // También llenar el campo del lado derecho
        $("#cliente_nombre_right").val(ui.item.label || "");

        // Mostrar modal para seleccionar serie específica
        mostrarModalSeleccionSeriePreAlerta(ui.item.id);
        $(this).val("");
      },
      open: function () {
        console.log("Dropdown de autocompletado de cliente abierto");
        $(this)
          .autocomplete("widget")
          .css({
            "max-height": "250px",
            "overflow-y": "auto",
            "overflow-x": "hidden",
            width: $(this).outerWidth() + "px",
            "z-index": "9999",
          });
      },
      close: function () {
        console.log("Dropdown de autocompletado de cliente cerrado");
      },
    });

    // Eventos para mostrar dropdown al hacer focus/click
    $("#input_buscar_cliente_prealerta")
      .off("focus.autocomplete click.autocomplete")
      .on("focus.autocomplete click.autocomplete", function () {
        console.log("Focus/Click en campo de cliente");
        if (!$(this).autocomplete("widget").is(":visible")) {
          $(this).autocomplete("search", "");
        }
      });

    console.log("Autocompletado de cliente inicializado correctamente");
  }

  function mostrarModalSeleccionSeriePreAlerta(clienteId) {
    $.ajax({
      url: _URL + "/ajs/prealerta/buscar/series/cliente",
      type: "GET",
      data: { cliente_id: clienteId },
      success: function (data) {
        try {
          const series = JSON.parse(data);

          if (series.length === 0) {
            Swal.fire({
              icon: "info",
              title: "Sin series",
              text: "Este cliente no tiene series disponibles para orden de trabajo",
              confirmButtonColor: "#dc3545",
            });
            return;
          }

          const seriesDisponibles = series.filter(
            (serie) =>
              serie.estado_prealerta !== "en_trabajo" &&
              serie.estado_prealerta !== "culminado"
          ).length;

          let seriesHtml = "";
          series.forEach((serie) => {
            const enTrabajo =
              serie.estado_prealerta === "en_trabajo" ||
              serie.estado_prealerta === "culminado";
            const estadoTexto =
              serie.estado_prealerta === "en_trabajo"
                ? "En Trabajo"
                : serie.estado_prealerta === "culminado"
                ? "Culminado"
                : "Disponible";
            const estadoClase =
              serie.estado_prealerta === "en_trabajo"
                ? "bg-warning text-white"
                : serie.estado_prealerta === "culminado"
                ? "bg-secondary text-white"
                : "bg-success text-white";

            seriesHtml += `
                        <tr>
                            <td>${serie.numero_serie}</td>
                            <td>${serie.marca_nombre || "No especificado"}</td>
                            <td>${serie.modelo_nombre || "No especificado"}</td>
                            <td>${serie.equipo_nombre || "No especificado"}</td>
                            <td><span class="badge ${estadoClase} px-2 py-1">${estadoTexto}</span></td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input seleccionar-serie-checkbox-prealerta" type="checkbox" 
                                        value="${serie.numero_serie}" 
                                        data-serie="${serie.numero_serie}"
                                        data-marca="${serie.marca_nombre || ""}"
                                        data-modelo="${
                                          serie.modelo_nombre || ""
                                        }"
                                       data-equipo="${
                                         serie.equipo_nombre || ""
                                       }"
                                        ${enTrabajo ? "disabled" : ""}>
                                    <label class="form-check-label">Seleccionar</label>
                                </div>
                            </td>
                        </tr>
                    `;
          });

          const modalHtml = `
                    <div class="modal fade" id="modalSeleccionSeriePreAlerta" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #dc3545; color: white;">
                                    <h5 class="modal-title">Seleccionar Series para Orden de Trabajo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle me-2"></i>
                                        Seleccione las series disponibles para registrar en orden de trabajo.
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="seleccionar_todas_series_prealerta">
                                            <label class="form-check-label fw-bold">
                                                Seleccionar todas las series disponibles (${seriesDisponibles})
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Número de Serie</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Equipo</th>
                                                    <th>Estado</th>
                                                    <th>Seleccionar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${seriesHtml}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="confirmar_seleccion_series_prealerta">Confirmar Selección</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

          $("body").append(modalHtml);
          $("#modalSeleccionSeriePreAlerta").modal("show");

          $("#seleccionar_todas_series_prealerta").change(function () {
            const seleccionarTodas = $(this).prop("checked");
            $(".seleccionar-serie-checkbox-prealerta:not(:disabled)").prop(
              "checked",
              seleccionarTodas
            );
          });

          $(document).on(
            "change",
            ".seleccionar-serie-checkbox-prealerta",
            function () {
              const todasSeleccionadas =
                $(".seleccionar-serie-checkbox-prealerta:not(:disabled)")
                  .length ===
                $(
                  ".seleccionar-serie-checkbox-prealerta:not(:disabled):checked"
                ).length;
              $("#seleccionar_todas_series_prealerta").prop(
                "checked",
                todasSeleccionadas
              );
            }
          );

          $("#confirmar_seleccion_series_prealerta").click(function () {
            const seriesSeleccionadas = [];

            $(".seleccionar-serie-checkbox-prealerta:checked").each(
              function () {
                const checkbox = $(this);
                seriesSeleccionadas.push({
                  numero_serie: checkbox.data("serie"),
                  marca: checkbox.data("marca"),
                  modelo: checkbox.data("modelo"),
                  tipo: checkbox.data("equipo"),
                });
              }
            );

            if (seriesSeleccionadas.length === 0) {
              Swal.fire({
                icon: "warning",
                title: "Selección vacía",
                text: "Por favor, seleccione al menos una serie",
                confirmButtonColor: "#dc3545",
              });
              return;
            }

            app.equipos = seriesSeleccionadas.map((serie) => ({
              marca: serie.marca,
              modelo: serie.modelo,
              equipo: serie.tipo,
              serie: serie.numero_serie,
            }));

            app.cantidadEquipos = seriesSeleccionadas.length;

            $("#modalSeleccionSeriePreAlerta").modal("hide");
          });

          $("#modalSeleccionSeriePreAlerta").on("hidden.bs.modal", function () {
            $(this).remove();
          });
        } catch (e) {
          console.error("Error al parsear datos de series:", e);
          Swal.fire({
            icon: "error",
            title: "¡Error!",
            text: "Error al cargar las series del cliente",
            confirmButtonColor: "#dc3545",
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: "error",
          title: "¡Error!",
          text: "No se pudieron cargar las series del cliente",
          confirmButtonColor: "#dc3545",
        });
      },
    });
  }

  // ===== INICIALIZAR AUTOCOMPLETADO CUANDO SE ABRE EL MODAL =====
  $("#modalAgregar").on("shown.bs.modal", function () {
    console.log("Modal abierto, inicializando autocompletado...");

    const today = new Date().toISOString().split("T")[0];
    $("#fecha_ingreso").val(today);
    // Establecer fecha de salida por defecto (7 días después)
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    const nextWeekString = nextWeek.toISOString().split("T")[0];
    $("#fecha_salida").val(nextWeekString);

    setTimeout(function () {
      inicializarAutocompletadoSeries();
      inicializarAutocompletadoCliente();
    }, 100);
  });
  // ===== MANEJAR CAMBIO ENTRE MÉTODOS DE BÚSQUEDA CON DROPDOWN =====
  $(document).on("change", "#metodo_busqueda_select", function () {
    const metodoSeleccionado = $(this).val();

    if (metodoSeleccionado === "serie") {
      $("#grupo_buscar_serie_prealerta").show();
      $("#grupo_buscar_cliente_prealerta").hide();
      // Limpiar campo de cliente
      $("#input_buscar_cliente_prealerta").val("");
    } else if (metodoSeleccionado === "cliente") {
      $("#grupo_buscar_serie_prealerta").hide();
      $("#grupo_buscar_cliente_prealerta").show();
      // Limpiar campo de serie
      $("#input_buscar_serie").val("");
    }
  });

  $("#btnAgregarTecnico").click(() => {
    const nombre = $("#tecnico_nombre").val();
    if (!nombre) {
      mostrarAlerta("Error", "Por favor ingrese un nombre de técnico", "error");
      return;
    }

    $.ajax({
      url: _URL + "/ajs/save/tecnicos",
      type: "POST",
      data: { nombre: nombre },
      success: (response) => {
        $("#tecnico_nombre").val("");
        cargarTablaTecnicos();
        cargarSelect("tecnicos", "#atencion_Encargado");
        mostrarAlerta("Éxito", "Técnico agregado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo agregar el técnico", "error");
      },
    });
  });

  $(document).on("click", ".eliminar-tecnico", function () {
    const id = $(this).closest("tr").data("id");
    confirmarEliminacion("¿Está seguro de eliminar este técnico?").then(
      (result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: _URL + "/ajs/delete/tecnicos",
            type: "POST",
            data: { id: id },
            success: () => {
              cargarTablaTecnicos();
              cargarSelect("tecnicos", "#atencion_Encargado");
              mostrarAlerta(
                "Éxito",
                "Técnico eliminado correctamente",
                "success"
              );
            },
            error: () => {
              mostrarAlerta("Error", "No se pudo eliminar el técnico", "error");
            },
          });
        }
      }
    );
  });

  $("#modalTecnico").on("show.bs.modal", cargarTablaTecnicos);

  function cargarSelect(tipo, selector) {
    return new Promise((resolve, reject) => {
      $.get(_URL + `/ajs/get/${tipo}`, (data) => {
        try {
          let options = "";
          const resp = JSON.parse(data);
          $.each(resp, (i, v) => {
            options += `<option value="${v.nombre}">${v.nombre}</option>`;
          });
          $(selector).html(options);
          resolve();
        } catch (error) {
          console.error(`Error al cargar ${tipo}:`, error);
          reject(error);
        }
      }).fail(reject);
    });
  }

  Promise.all([
    cargarSelect("tecnicos", "#atencion_Encargado"),
    cargarSelect("tecnicos", "#edit_atencion_encargado"),
  ]).catch((error) => {
    console.error("Error al cargar los selects:", error);
    mostrarAlerta(
      "Error",
      "No se pudieron cargar algunos datos. Por favor, recarga la página.",
      "error"
    );
  });

  $(document).on("click", ".btn-ver-detalles", function () {
    var id = $(this).data("id");
    mostrarDetalles(id);
  });
  // Event listener para el botón de editar
  $(document).on("click", ".btnEditar", function () {
    var id = $(this).data("id");
    app.cargarDatosEdicion(id);
  });

  $("#modalEditar").on("hidden.bs.modal", function () {
    if (app) {
      app.editando = {
        id_orden_trabajo: null,
        cliente_Rsocial: "",
        cliente_ruc: "",
        atencion_Encargado: "",
        fecha_ingreso: "",
        fecha_salida: "",
        observaciones: "",
        equipos: [],
      };
    }
  });

  // Event listener para el botón Guardar
  $(document).on("click", "#submitRegistro", function () {
    console.log("Botón guardar clickeado");

    // Validar campos requeridos
    if (!app.prealerta.num_doc) {
      Swal.fire("Error", "El documento es requerido", "error");
      return;
    }

    if (!app.prealerta.cliente_Rsocial) {
      Swal.fire("Error", "El nombre del cliente es requerido", "error");
      return;
    }

    if (!$("#atencion_Encargado").val()) {
      Swal.fire("Error", "Debe seleccionar un técnico", "error");
      return;
    }

    if (!$("#fecha_ingreso").val()) {
      Swal.fire("Error", "La fecha de ingreso es requerida", "error");
      return;
    }
    if (!$("#fecha_salida").val()) {
      Swal.fire("Error", "La fecha de salida es requerida", "error");
      return;
    }

    // Validar que fecha_salida sea posterior a fecha_ingreso
    if ($("#fecha_ingreso").val() && $("#fecha_salida").val()) {
      const fechaIngreso = new Date($("#fecha_ingreso").val());
      const fechaSalida = new Date($("#fecha_salida").val());

      if (fechaSalida <= fechaIngreso) {
        Swal.fire(
          "Error",
          "La fecha de salida debe ser posterior a la fecha de ingreso",
          "error"
        );
        return;
      }
    }

    if (app.cantidadEquipos === 0) {
      Swal.fire("Error", "Debe agregar al menos un equipo", "error");
      return;
    }

    // Preparar datos para enviar
    const formData = {
      num_doc: app.prealerta.num_doc,
      cliente_Rsocial: app.prealerta.cliente_Rsocial,
      cliente_documento: app.prealerta.num_doc, // Asignar el mismo valor
      atencion_Encargado: $("#atencion_Encargado").val(),
      fecha_ingreso: $("#fecha_ingreso").val(),
      fecha_salida: $("#fecha_salida").val(),
      observaciones: $("#observaciones").val(),
      equipos: app.equipos.map((equipo) => ({
        marca: equipo.marca,
        modelo: equipo.modelo,
        tipo: equipo.equipo,
        serie: equipo.serie,
      })),
      origen: "Ord Trabajo",
    };

    console.log("Datos a enviar:", formData);
    console.log("Equipos detallados:", app.equipos);
    console.log("Cantidad equipos:", app.cantidadEquipos);

    // Enviar datos al servidor - CAMBIO AQUÍ
    $.ajax({
      url: _URL + "/ajs/orden-trabajo/add",
      type: "POST",
      data: formData,
      beforeSend: function () {
        $("#submitRegistro")
          .prop("disabled", true)
          .html('<i class="fa fa-spinner fa-spin me-1"></i> Guardando...');
      },
      success: function (response) {
        console.log("Respuesta del servidor:", response);

        try {
          // Intentar parsear como JSON
          const jsonResponse = JSON.parse(response);

          // Si es un número (ID), es éxito
          if (typeof jsonResponse === "number" && jsonResponse > 0) {
            Swal.fire(
              "Éxito",
              "Orden de trabajo guardada correctamente",
              "success"
            );
            $("#modalAgregar").modal("hide");
            tabla_clientes.ajax.reload();

            // Limpiar formulario
            app.prealerta.num_doc = "";
            app.prealerta.cliente_Rsocial = "";
            app.equipos = [{ marca: "", modelo: "", equipo: "", serie: "" }];
            app.cantidadEquipos = 1;
            $("#observaciones").val("");
            $("#atencion_Encargado").val("");
            $("#fecha_ingreso").val("");
            $("#fecha_salida").val("");
            $("#cliente_nombre_right").val("");
          } else {
            // Es un mensaje de error
            Swal.fire("Error", jsonResponse || "Error al guardar", "error");
          }
        } catch (e) {
          // Si no es JSON válido, tratar como string
          if (
            response &&
            !response.includes("error") &&
            !response.includes("Error") &&
            !response.includes("Ocurri")
          ) {
            Swal.fire(
              "Éxito",
              "Orden de trabajo guardada correctamente",
              "success"
            );
            $("#modalAgregar").modal("hide");
            tabla_clientes.ajax.reload();

            // Limpiar formulario
            app.prealerta.num_doc = "";
            app.prealerta.cliente_Rsocial = "";
            app.equipos = [{ marca: "", modelo: "", equipo: "", serie: "" }];
            app.cantidadEquipos = 1;
            $("#observaciones").val("");
            $("#atencion_Encargado").val("");
            $("#fecha_ingreso").val("");
            $("#cliente_nombre_right").val("");
          } else {
            Swal.fire("Error", response || "Error al guardar", "error");
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        Swal.fire("Error", "Error al comunicarse con el servidor", "error");
      },
      complete: function () {
        $("#submitRegistro")
          .prop("disabled", false)
          .html('<i class="fa fa-save me-1"></i> Guardar');
      },
    });
  });

  // Eliminar orden de trabajo
  $("#tabla_clientes").on("click", ".btnBorrar", function () {
    var id = $(this).data("id");
    const idData = {
      name: "idDelete",
      value: id,
    };
    Swal.fire({
      title: "¿Deseas borrar el registro?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: _URL + "/ajs/orden-trabajo/delete", // CAMBIO AQUÍ
          type: "post",
          data: idData,
          success: (resp) => {
            tabla_clientes.ajax.reload(null, false);
            Swal.fire(
              "¡Buen trabajo!",
              "Registro Borrado Exitosamente",
              "success"
            );
          },
        });
      }
    });
  });

  // Agregar estas funciones faltantes:
  function mostrarAlerta(titulo, mensaje, tipo) {
    Swal.fire({
      title: titulo,
      text: mensaje,
      icon: tipo,
      confirmButtonText: "Aceptar",
    });
  }

  function confirmarEliminacion(mensaje) {
    return Swal.fire({
      title: "¿Está seguro?",
      text: mensaje,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    });
  }

  function alertAdvertencia(mensaje) {
    Swal.fire({
      icon: "warning",
      title: "Advertencia",
      text: mensaje,
    });
  }
});
