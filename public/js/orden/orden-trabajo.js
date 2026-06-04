// public/js/orden/orden-trabajo.js
// Lógica específica de Orden de Trabajo
// Requiere: orden-base.js, orden-trabajo-repuestos.js, orden-trabajo-prealerta.js

$(document).ready(() => {
  const app = new Vue({
    el: "#client",
    data: {
      prealerta: {
        num_doc: "",
        cliente_Rsocial: "",
      },
      cantidadEquipos: 1,
      equipos: [{ marca: "", modelo: "", equipo: "", serie: "" }],
      marcasDisponibles: [],
      modelosDisponibles: [],
      equiposDisponibles: [],
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
        buscarDocumentoCliente(this, { usarDireccion: true });
      },

      cargarDatosEdicion(id) {
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
          timeout: 15000,
          success: (response) => {
            $("#loader-menor").hide();
            try {
              if (typeof response === "string" &&
                (response.trim().toLowerCase().startsWith("<!doctype") ||
                  response.trim().toLowerCase().startsWith("<html"))) {
                throw new Error("El servidor devolvió HTML en lugar de JSON. Posible sesión expirada.");
              }

              const datos = typeof response === "object" ? response : JSON.parse(response);

              this.editando.id_orden_trabajo = datos.id_orden_trabajo;
              this.editando.cliente_Rsocial = datos.cliente_razon_social;
              this.editando.cliente_ruc = datos.cliente_ruc;
              this.editando.atencion_Encargado = datos.atencion_encargado;
              this.editando.fecha_ingreso = datos.fecha_ingreso;
              this.editando.fecha_salida = datos.fecha_salida;
              this.editando.observaciones = datos.observaciones || "";
              this.editando.equipos = datos.equipos || [];

              $("#edit_id_orden_trabajo").val(datos.id_orden_trabajo);
              $("#edit_cliente_Rsocial").val(datos.cliente_razon_social);
              $("#edit_cliente_ruc").val(datos.cliente_ruc);
              $("#edit_atencion_Encargado").val(datos.atencion_encargado);
              $("#edit_fecha_ingreso").val(datos.fecha_ingreso);
              $("#edit_fecha_salida").val(datos.fecha_salida);
              $("#edit_observaciones").val(datos.observaciones || "");

              $("#modalEditar").modal("show");
            } catch (error) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al cargar los datos para edición. " + (error.message || ""),
                footer: '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            }
          },
          error: (xhr) => {
            $("#loader-menor").hide();
            if (xhr.responseText &&
              (xhr.responseText.trim().toLowerCase().startsWith("<!doctype") ||
                xhr.responseText.trim().toLowerCase().startsWith("<html"))) {
              Swal.fire({
                icon: "error",
                title: "Error de sesión",
                text: "Es posible que tu sesión haya expirado. Intenta recargar la página.",
                footer: '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al obtener los datos del registro",
                footer: '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            }
          },
        });
      },

      agregarEquipoEdicion() {
        this.editando.equipos.push({
          id: null, marca: "", equipo: "", modelo: "", numero_serie: "",
        });
      },

      eliminarEquipoEdicion(index) {
        this.editando.equipos.splice(index, 1);
      },

      guardarEdicion() {
        if (!this.editando.cliente_Rsocial || !this.editando.atencion_Encargado || !this.editando.fecha_ingreso) {
          Swal.fire({ icon: "warning", title: "Advertencia", text: "Por favor complete todos los campos requeridos" });
          return;
        }

        // Validar fechas
        if (this.editando.fecha_ingreso && this.editando.fecha_salida) {
          const fechaIngreso = new Date(this.editando.fecha_ingreso);
          const fechaSalida = new Date(this.editando.fecha_salida);
          if (fechaSalida <= fechaIngreso) {
            Swal.fire({ icon: "warning", title: "Advertencia", text: "La fecha de salida debe ser posterior a la fecha de ingreso" });
            return;
          }
        }

        const equiposValidos = this.editando.equipos.every(
          (eq) => eq.marca && eq.equipo && eq.modelo && eq.numero_serie
        );
        if (!equiposValidos) {
          Swal.fire({ icon: "warning", title: "Advertencia", text: "Por favor complete todos los datos de los equipos" });
          return;
        }

        const data = {
          id_orden_trabajo: this.editando.id_orden_trabajo,
          cliente_razon_social: this.editando.cliente_Rsocial,
          cliente_ruc: this.editando.cliente_ruc,
          atencion_encargado: this.editando.atencion_Encargado,
          fecha_ingreso: this.editando.fecha_ingreso,
          fecha_salida: this.editando.fecha_salida,
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
              const response = typeof resp === "object" ? resp : JSON.parse(resp);
              if (response && response.success) {
                tabla_clientes.ajax.reload(null, false);
                Swal.fire({ icon: "success", title: "Éxito", text: "Registro actualizado correctamente" });
                $("#modalEditar").modal("hide");
              } else {
                throw new Error(response.error || "Error al actualizar");
              }
            } catch (error) {
              Swal.fire({ icon: "error", title: "Error", text: error.message || "Error al procesar la respuesta" });
            }
          },
          error: () => {
            $("#loader-menor").hide();
            Swal.fire({ icon: "error", title: "Error", text: "Error al intentar actualizar el registro" });
          },
        });
      },
    },
  });

  // ===== DATATABLE =====
  tabla_clientes = $("#tabla_clientes").DataTable({
    paging: true,
    bFilter: true,
    ordering: true,
    searching: true,
    destroy: true,
    responsive: true,
    scrollX: false,
    autoWidth: false,
    order: [],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
    ajax: {
      url: _URL + "/ajs/orden-trabajo/render",
      method: "POST",
      dataSrc: "",
    },
    language: {
      url: "ServerSide/Spanish.json",
    },
    columns: [
      { data: "numero", class: "text-center" },
      {
        data: "cliente_razon_social",
        class: "text-center",
        render: function (data, type, row) {
          if (row.cliente_ruc === "20538381978") {
            return '<span class="registro-interno-text">Registro Interno (JVC)</span>';
          }
          return data;
        },
      },
      { data: "cliente_ruc", class: "text-center" },
      { data: "atencion_encargado", class: "text-center" },
      { data: "fecha_ingreso", class: "text-center" },
      { data: "fecha_salida", class: "text-center" },
      {
        data: null,
        class: "text-center",
        render: (data, type, row) => {
          const tieneCotizacion = Number.parseInt(row.tiene_cotizacion);
          const badgeStyle = `display:inline-block;width:130px;text-align:center;padding:5px 10px;border-radius:50px;font-weight:bold;font-size:0.70rem;text-transform:uppercase;white-space:nowrap;`;

          if (tieneCotizacion === 1) {
            return `<span onclick="verCotizacion(${row.id_orden_trabajo})" class="badge"
              style="${badgeStyle}background-color:#28a745;color:white;cursor:pointer;">
              <i class="fa fa-check-circle" style="margin-right:5px;"></i>FINALIZADO</span>`;
          }
          return `<span class="badge" style="${badgeStyle}background-color:#ffa500;color:white;">
            <i class="fa fa-clock" style="margin-right:5px;"></i>EN PRODUCCIÓN</span>`;
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
          </div>`,
        // NOTA: Se quitó el botón "Ver/Agregar Productos" (btn-ver-productos).
        // Razón: no tiene sentido agregar un producto a una OT si la serie no
        // ha sido creada previamente en el módulo de Series NS. La información
        // de los equipos ya se ve en el modal "Ver detalles".
      },
    ],
    createdRow: function (row, data) {
      if (data.cliente_ruc === "20538381978") {
        $(row).addClass("registro-interno-row");
      }
    },
    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });

  window.verCotizacion = () => {
    window.location.href = `/tallers/coti/view/`;
  };

  // ===== MOSTRAR DETALLES =====
  function mostrarDetalles(id) {
    ordenTrabajoActual = id;

    $.ajax({
      url: _URL + "/ajs/orden-trabajo/detalles",
      type: "POST",
      data: { id: id },
      timeout: 15000,
      success: (response) => {
        try {
          if (typeof response === "string" &&
            (response.trim().toLowerCase().startsWith("<!doctype") ||
              response.trim().toLowerCase().startsWith("<html"))) {
            throw new Error("El servidor devolvió HTML en lugar de JSON.");
          }

          var detalles = typeof response === "object" ? response : JSON.parse(response);

          // Guardar máquinas para modal de repuestos
          maquinasActuales = detalles.equipos.map((equipo) => ({
            id_detalle: equipo.id_detalle,
            marca: equipo.marca,
            modelo: equipo.modelo,
            equipo: equipo.equipo,
            numero_serie: equipo.numero_serie,
          }));

          var equiposHTML = "";
          detalles.equipos.forEach((equipo, index) => {
            equiposHTML += `<tr><td>${index + 1}</td><td>${equipo.marca}</td><td>${equipo.modelo}</td><td>${equipo.equipo}</td><td>${equipo.numero_serie}</td></tr>`;
          });

          var contenidoModal = `
            <div class="mb-4">
              <h6 class="text-muted mb-3">Información de Orden de Trabajo</h6>
              <div class="row">
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
            <div class="card border-danger mb-2">
              <div class="card-header bg-secondary p-2">Equipos Registrados: ${detalles.equipos.length}</div>
              <div class="card-body p-2">
                <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                  <table class="table table-bordered table-striped mb-0">
                    <thead class="table-danger sticky-top bg-danger">
                      <tr><th>#</th><th>Marca</th><th>Modelo</th><th>Equipo</th><th>Número de Serie</th></tr>
                    </thead>
                    <tbody>${equiposHTML}</tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="card border-danger">
              <div class="card-header bg-secondary p-2">Observaciones</div>
              <div class="card-body p-2">
                <p class="mb-0">${detalles.observaciones || "Sin observaciones"}</p>
              </div>
            </div>`;

          $("#modalDetalles .modal-body").html(contenidoModal);
          $("#modalDetalles").modal("show");
        } catch (error) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al procesar los detalles. " + (error.message || ""),
            footer: '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        }
      },
      error: (xhr) => {
        if (xhr.responseText &&
          (xhr.responseText.trim().toLowerCase().startsWith("<!doctype") ||
            xhr.responseText.trim().toLowerCase().startsWith("<html"))) {
          Swal.fire({
            icon: "error",
            title: "Error de sesión",
            text: "Es posible que tu sesión haya expirado. Intenta recargar la página.",
            footer: '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los detalles.",
            footer: '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        }
      },
    });
  }

  // ===== MOSTRAR MODAL DE PRODUCTOS =====
  function mostrarModalProductos(id) {
    ordenTrabajoActual = id;

    $.ajax({
      url: _URL + "/ajs/orden-trabajo/detalles",
      type: "POST",
      data: { id: id },
      timeout: 15000,
      success: (response) => {
        try {
          var detalles = typeof response === "object" ? response : JSON.parse(response);
          maquinasActuales = detalles.equipos.map((equipo) => ({
            id_detalle: equipo.id_detalle,
            marca: equipo.marca,
            modelo: equipo.modelo,
            equipo: equipo.equipo,
            numero_serie: equipo.numero_serie,
          }));
          cargarMaquinasEnModal();
          $("#modalAgregarRepuestos").modal("show");
        } catch (error) {
          Swal.fire({ icon: "error", title: "Error", text: "Error al cargar los productos. " + (error.message || "") });
        }
      },
      error: (xhr, status, error) => {
        Swal.fire({ icon: "error", title: "Error", text: "No se pudieron cargar los productos. " + error });
      },
    });
  }

  // ===== EVENT LISTENERS =====
  $(document).on("click", ".btn-ver-detalles", function () {
    mostrarDetalles($(this).data("id"));
  });

  // DESHABILITADO: el botón "Ver/Agregar Productos" fue removido del listado.
  // Se deja comentado por si más adelante se decide recuperar la funcionalidad.
  // $(document).on("click", ".btn-ver-productos", function () {
  //   mostrarModalProductos($(this).data("id"));
  // });

  $(document).on("click", ".btnEditar", function () {
    app.cargarDatosEdicion($(this).data("id"));
  });

  // ===== MODAL AGREGAR: INICIALIZAR AUTOCOMPLETADO =====
  $("#modalAgregar").on("shown.bs.modal", function () {
    const today = new Date().toISOString().split("T")[0];
    $("#fecha_ingreso").val(today);

    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    $("#fecha_salida").val(nextWeek.toISOString().split("T")[0]);

    setTimeout(function () {
      inicializarAutocompletadoNumeros(app);
      inicializarAutocompletadoCliente(app);
    }, 100);
  });

  // Cambio de método de búsqueda
  $(document).on("change", "#metodo_busqueda_select", function () {
    const metodo = $(this).val();
    if (metodo === "serie") {
      $("#grupo_buscar_serie_prealerta").show();
      $("#grupo_buscar_cliente_prealerta").hide();
      $("#input_buscar_cliente_prealerta").val("");
    } else if (metodo === "cliente") {
      $("#grupo_buscar_serie_prealerta").hide();
      $("#grupo_buscar_cliente_prealerta").show();
      $("#input_buscar_serie").val("");
    }
  });

  // ===== SUBMIT REGISTRO =====
  $(document).on("click", "#submitRegistro", function () {
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

    // Validar fechas
    if ($("#fecha_ingreso").val() && $("#fecha_salida").val()) {
      const fechaIngreso = new Date($("#fecha_ingreso").val());
      const fechaSalida = new Date($("#fecha_salida").val());
      if (fechaSalida <= fechaIngreso) {
        Swal.fire("Error", "La fecha de salida debe ser posterior a la fecha de ingreso", "error");
        return;
      }
    }

    if (app.cantidadEquipos === 0) {
      Swal.fire("Error", "Debe agregar al menos un equipo", "error");
      return;
    }

    const formData = {
      num_doc: app.prealerta.num_doc,
      cliente_Rsocial: app.prealerta.cliente_Rsocial,
      cliente_documento: app.prealerta.num_doc,
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

    $.ajax({
      url: _URL + "/ajs/orden-trabajo/add",
      type: "POST",
      data: formData,
      beforeSend: function () {
        $("#submitRegistro").prop("disabled", true)
          .html('<i class="fa fa-spinner fa-spin me-1"></i> Guardando...');
      },
      success: function (response) {
        try {
          const jsonResponse = JSON.parse(response);
          if (typeof jsonResponse === "number" && jsonResponse > 0) {
            Swal.fire("Éxito", "Orden de trabajo guardada correctamente", "success");
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
            Swal.fire("Error", jsonResponse || "Error al guardar", "error");
          }
        } catch (e) {
          if (response && !response.includes("error") && !response.includes("Error") && !response.includes("Ocurri")) {
            Swal.fire("Éxito", "Orden de trabajo guardada correctamente", "success");
            $("#modalAgregar").modal("hide");
            tabla_clientes.ajax.reload();
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
      error: function () {
        Swal.fire("Error", "Error al comunicarse con el servidor", "error");
      },
      complete: function () {
        $("#submitRegistro").prop("disabled", false)
          .html('<i class="fa fa-save me-1"></i> Guardar');
      },
    });
  });

  // ===== ELIMINAR =====
  $("#tabla_clientes").on("click", ".btnBorrar", function () {
    var id = $(this).data("id");
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
          url: _URL + "/ajs/orden-trabajo/delete",
          type: "post",
          data: { name: "idDelete", value: id },
          success: () => {
            tabla_clientes.ajax.reload(null, false);
            Swal.fire("¡Buen trabajo!", "Registro Borrado Exitosamente", "success");
          },
        });
      }
    });
  });

  // ===== LIMPIAR MODAL EDICIÓN =====
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

  // ===== INICIALIZACIÓN =====
  inicializarCatalogosEventos(["tecnicos"]);

  Promise.all([
    cargarSelect("tecnicos", "#atencion_Encargado"),
    cargarSelect("tecnicos", "#edit_atencion_encargado"),
  ]).catch(() => {
    mostrarAlerta("Error", "No se pudieron cargar algunos datos. Por favor, recarga la página.", "error");
  });
});
