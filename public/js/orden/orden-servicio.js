// public/js/orden/orden-servicio.js
// Lógica específica de Orden de Servicio
// Requiere: orden-base.js cargado antes

$(document).ready(() => {
  const app = new Vue({
    el: "#client",
    data: {
      prealerta: {
        num_doc: "",
        cliente_Rsocial: "",
      },
      cantidadEquipos: 1,
      equipos: [{ marca: "", modelo: "", tipo: "", serie: "", id_producto: null, producto_busqueda: "" }],
      marcasDisponibles: [],
      modelosDisponibles: [],
      equiposDisponibles: [],
      almacenesDisponibles: [],
      idAlmacen: null,
      editando: {
        id_orden_servicio: null,
        cliente_Rsocial: "",
        cliente_ruc: "",
        atencion_Encargado: "",
        fecha_ingreso: "",
        observaciones: "",
        id_almacen: null,
        equipos: [],
      },
      maquinasIdenticas: false,
      cantidadMaquinasIdenticas: 1,
      seriesMultiples: "",
      equipoBase: { marca: "", modelo: "", tipo: "", id_producto: null, producto_busqueda: "" },
      validationErrors: {
        num_doc: "",
        cliente_Rsocial: "",
        tecnico: "",
        fecha_ingreso: "",
        cantidadEquipos: "",
        marca: "",
        modelo: "",
        equipo: "",
        cantidad: "",
        series: "",
      },
      seriesCount: 0,
    },
    mounted() {
      this.inicializarEquipos();
      this.cargarCatalogos();
      this.cargarAlmacenes();
    },
    methods: {
      validateForm() {
        let isValid = true;
        this.validationErrors = {
          num_doc: "", cliente_Rsocial: "", tecnico: "", fecha_ingreso: "",
          cantidadEquipos: "", marca: "", modelo: "", equipo: "", cantidad: "", series: "",
        };

        if (!this.prealerta.num_doc) {
          this.validationErrors.num_doc = "Por favor ingrese un número de documento";
          isValid = false;
        }
        if (!this.prealerta.cliente_Rsocial) {
          this.validationErrors.cliente_Rsocial = "Por favor ingrese el nombre del cliente";
          isValid = false;
        }
        if (!$("#atencion_Encargado").val()) {
          this.validationErrors.tecnico = "Por favor seleccione un técnico";
          isValid = false;
        }
        if (!$("#fecha_ingreso").val()) {
          this.validationErrors.fecha_ingreso = "Por favor seleccione una fecha";
          isValid = false;
        }
        if (!this.cantidadEquipos || this.cantidadEquipos < 1) {
          this.validationErrors.cantidadEquipos = "La cantidad debe ser mayor a 0";
          isValid = false;
        }

        if (this.maquinasIdenticas) {
          if (!this.equipoBase.tipo) { this.validationErrors.equipo = "Por favor seleccione un equipo"; isValid = false; }
          if (!this.cantidadMaquinasIdenticas || this.cantidadMaquinasIdenticas < 1) {
            this.validationErrors.cantidad = "La cantidad debe ser mayor a 0"; isValid = false;
          }
          const series = this.seriesMultiples.split(/[\n,\s]+/).filter((s) => s.trim());
          if (series.length !== this.cantidadMaquinasIdenticas) {
            this.validationErrors.series = `Debe ingresar ${this.cantidadMaquinasIdenticas} números de serie (actualmente: ${series.length})`;
            isValid = false;
          }
        } else {
          const equiposValidos = this.equipos.every((equipo, index) => {
            let equipoValido = true;
            if (!equipo.tipo) { this.validationErrors[`equipo_${index}_tipo`] = "Por favor seleccione un equipo"; equipoValido = false; }
            if (!equipo.serie) { this.validationErrors[`equipo_${index}_serie`] = "Por favor ingrese un número de serie"; equipoValido = false; }
            return equipoValido;
          });
          if (!equiposValidos) isValid = false;
        }

        if (!isValid) {
          Swal.fire({ icon: "warning", title: "Advertencia", text: "Por favor complete todos los campos requeridos" });
        }
        return isValid;
      },

      procesarSeriesMultiples() {
        if (!this.validateForm()) return false;
        return procesarSeriesMultiples(this);
      },

      contarSeries() {
        contarSeries(this);
      },

      buscarDocumentSS() {
        buscarDocumentoCliente(this, { usarDireccion: true });
      },

      inicializarEquipos() {
        this.equipos = [{ marca: "", modelo: "", tipo: "", serie: "", id_producto: null, producto_busqueda: "" }];
      },

      cargarCatalogos() {
        $.get(_URL + "/ajs/get/marcas", (data) => { this.marcasDisponibles = JSON.parse(data); });
        $.get(_URL + "/ajs/get/modelos", (data) => { this.modelosDisponibles = JSON.parse(data); });
        $.get(_URL + "/ajs/get/equipos", (data) => { this.equiposDisponibles = JSON.parse(data); });
      },

      cargarAlmacenes() {
        $.get(_URL + "/ajs/almacenes/listar", (resp) => {
          try {
            const data = typeof resp === "string" ? JSON.parse(resp) : resp;
            if (data && data.estado && Array.isArray(data.almacenes)) {
              this.almacenesDisponibles = data.almacenes;
              // Si aún no hay almacén elegido, default = principal
              if (!this.idAlmacen) {
                const principal = data.almacenes.find(a => Number(a.principal) === 1);
                if (principal) this.idAlmacen = Number(principal.id_almacen);
              }
            }
          } catch (e) {
            console.warn("No se pudieron cargar almacenes:", e);
          }
        });
      },

      onCambiarAlmacen() {
        // Re-wire autocomplete de cada input de producto del modal Agregar
        this.$nextTick(() => this.wireProductosAutocomplete(".input-buscar-producto-prealerta:not([data-edit])", this.equipos, "agregar"));
      },

      onCambiarAlmacenEdit() {
        this.$nextTick(() => this.wireProductosAutocomplete(".input-buscar-producto-prealerta[data-edit]", this.editando.equipos, "editar"));
      },

      wireProductosAutocomplete(selector, items, modo) {
        const vm = this;
        const idAlmacen = modo === "agregar" ? this.idAlmacen : this.editando.id_almacen;
        if (!idAlmacen) return;
        $(selector).each(function () {
          if ($(this).hasClass("ui-autocomplete-input")) {
            $(this).autocomplete("destroy");
          }
          $(this).autocomplete({
            minLength: 1,
            source: function (request, response) {
              $.get(_URL + "/ajs/cargar/productos/" + idAlmacen, { term: request.term }, (data) => {
                try {
                  const arr = typeof data === "string" ? JSON.parse(data) : data;
                  response($.map(arr, function (item) {
                    return {
                      label: item.value || (item.codigo_pp + " | " + item.nombre),
                      value: item.codigo_pp + " | " + item.nombre,
                      item: item,
                    };
                  }));
                } catch (e) { response([]); }
              });
            },
            select: function (event, ui) {
              const $input = $(this);
              const idx = parseInt($input.data("index"), 10);
              const prod = ui.item.item;
              if (!isNaN(idx) && items[idx]) {
                Vue.set(items[idx], "id_producto", prod.codigo || null);
                Vue.set(items[idx], "producto_busqueda", ui.item.value);
                // Autocompletar Equipo con el nombre del producto
                if (prod.nombre) Vue.set(items[idx], "tipo", prod.nombre);
                // Forzar autocompletado de marca/modelo desde el option correspondiente
                vm.$nextTick(() => {
                  const $sel = $input.closest(".equipo-item").find("select.select-equipo, select.v-model-tipo");
                  if ($sel.length && prod.nombre) {
                    $sel.find("option").each(function () {
                      if ($(this).val() === prod.nombre) {
                        const marca = $(this).attr("data-marca-nombre") || "";
                        const modelo = $(this).attr("data-modelo-nombre") || "";
                        Vue.set(items[idx], "marca", marca);
                        Vue.set(items[idx], "modelo", modelo);
                        return false;
                      }
                    });
                  }
                });
              }
              $input.siblings(".producto-seleccionado-info-prealerta").html(
                '<i class="fa fa-check-circle text-success"></i> Producto vinculado al kardex'
              );
              return false;
            },
            open: function () { $(this).autocomplete("widget").css("z-index", 10050); }
          });
        });
      },

      limpiarProductoEquipo(item) {
        item.id_producto = null;
        item.producto_busqueda = "";
        item.marca = "";
        item.modelo = "";
        item.tipo = "";
      },

      actualizarEquipos() {
        const cantidad = Math.max(1, Number.parseInt(this.cantidadEquipos) || 1);
        this.cantidadEquipos = cantidad;
        while (this.equipos.length < cantidad) {
          this.equipos.push({ marca: "", modelo: "", tipo: "", serie: "", id_producto: null, producto_busqueda: "" });
        }
        if (this.equipos.length > cantidad) {
          this.equipos = this.equipos.slice(0, cantidad);
        }
      },

      cargarDatosEdicion(id) {
        this.maquinasIdenticas = false;
        this.seriesMultiples = "";
        this.equipoBase = { marca: "", modelo: "", tipo: "", id_producto: null, producto_busqueda: "" };
        this.cantidadMaquinasIdenticas = 1;
        this.editando = {
          id_orden_servicio: null, cliente_Rsocial: "", cliente_ruc: "",
          atencion_Encargado: "", fecha_ingreso: "", observaciones: "", equipos: [],
        };

        $("#loader-menor").show();
        $.ajax({
          url: _URL + "/ajs/orden-servicio/detalles",
          type: "POST",
          data: { id: id },
          timeout: 15000,
          success: (response) => {
            $("#loader-menor").hide();
            try {
              if (typeof response === "string" &&
                (response.trim().toLowerCase().startsWith("<!doctype") || response.trim().toLowerCase().startsWith("<html"))) {
                throw new Error("El servidor devolvió HTML en lugar de JSON. Posible sesión expirada.");
              }
              const datos = typeof response === "object" ? response : JSON.parse(response);

              this.editando.id_orden_servicio = datos.id_orden_servicio;
              this.editando.cliente_Rsocial = datos.cliente_razon_social;
              this.editando.cliente_ruc = datos.cliente_ruc;
              this.editando.atencion_Encargado = datos.atencion_encargado;
              this.editando.fecha_ingreso = datos.fecha_ingreso;
              this.editando.observaciones = datos.observaciones || "";
              this.editando.id_almacen = datos.id_almacen || null;
              this.editando.equipos = (datos.equipos || []).map((eq) => ({
                ...eq,
                id_producto: eq.id_producto || null,
                producto_busqueda: "",
              }));

              if (this.editando.equipos.length > 1) {
                prepararEdicionEquiposIdenticos(this);
              }

              $("#modalEditar").modal("show");
            } catch (error) {
              Swal.fire({
                icon: "error", title: "Error",
                text: "Error al cargar los datos para edición. " + (error.message || ""),
                footer: '<a href="javascript:location.reload()">Recargar la página</a>',
              });
            }
          },
          error: (xhr) => {
            $("#loader-menor").hide();
            Swal.fire({
              icon: "error", title: "Error",
              text: "Error al obtener los datos del registro",
              footer: '<a href="javascript:location.reload()">Recargar la página</a>',
            });
          },
        });
      },

      agregarEquipoEdicion() {
        this.editando.equipos.push({ id: null, marca: "", equipo: "", modelo: "", numero_serie: "", id_producto: null, producto_busqueda: "" });
      },

      eliminarEquipoEdicion(index) {
        this.editando.equipos.splice(index, 1);
      },

      guardarEdicion() {
        if (!this.editando.cliente_Rsocial || !this.editando.atencion_Encargado || !this.editando.fecha_ingreso) {
          Swal.fire({ icon: "warning", title: "Advertencia", text: "Por favor complete todos los campos requeridos" });
          return;
        }

        if (this.maquinasIdenticas) {
          if (!procesarSeriesMultiplesEdicion(this)) return;
        } else {
          const equiposValidos = this.editando.equipos.every(
            (eq) => eq.equipo && eq.numero_serie
          );
          if (!equiposValidos) {
            Swal.fire({ icon: "warning", title: "Advertencia", text: "Por favor complete equipo y número de serie" });
            return;
          }
        }

        const data = {
          id_orden_servicio: this.editando.id_orden_servicio,
          cliente_razon_social: this.editando.cliente_Rsocial,
          cliente_ruc: this.editando.cliente_ruc,
          atencion_encargado: this.editando.atencion_Encargado,
          fecha_ingreso: this.editando.fecha_ingreso,
          observaciones: this.editando.observaciones,
          id_almacen: this.editando.id_almacen || null,
          equipos: this.editando.equipos,
        };

        $("#loader-menor").show();
        $.ajax({
          type: "POST",
          url: _URL + "/ajs/orden-servicio/update",
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

      detectarEquiposIdenticos() {
        return detectarEquiposIdenticos(this.editando.equipos);
      },

      prepararEdicionEquiposIdenticos() {
        prepararEdicionEquiposIdenticos(this);
      },

      marcaModeloDisplay(equipo) {
        const marca = equipo.marca || "";
        const modelo = equipo.modelo || "";
        if (marca && modelo) return marca + " / " + modelo;
        return marca || modelo || "";
      },

      alCambiarEquipo(item) {
        const idx = this.equipos.indexOf(item);
        if (idx === -1) return;
        const opt = event.target.selectedOptions[0];
        if (!opt) return;
        const marca = opt.getAttribute("data-marca-nombre") || "";
        const modelo = opt.getAttribute("data-modelo-nombre") || "";
        Vue.set(this.equipos[idx], "marca", marca);
        Vue.set(this.equipos[idx], "modelo", modelo);
      },

      alCambiarEquipoEdicion(item) {
        const idx = this.editando.equipos.indexOf(item);
        if (idx === -1) return;
        const opt = event.target.selectedOptions[0];
        if (!opt) return;
        const marca = opt.getAttribute("data-marca-nombre") || "";
        const modelo = opt.getAttribute("data-modelo-nombre") || "";
        Vue.set(this.editando.equipos[idx], "marca", marca);
        Vue.set(this.editando.equipos[idx], "modelo", modelo);
      },

      alCambiarEquipoBase() {
        const opt = event.target.selectedOptions[0];
        if (!opt) return;
        const marca = opt.getAttribute("data-marca-nombre") || "";
        const modelo = opt.getAttribute("data-modelo-nombre") || "";
        Vue.set(this.equipoBase, "marca", marca);
        Vue.set(this.equipoBase, "modelo", modelo);
      },
    },
    watch: {
      cantidadEquipos() { this.actualizarEquipos(); },
      seriesMultiples() { this.contarSeries(); },
    },
  });

  // ===== DATATABLE =====
  tabla_clientes = $("#tabla_clientes").DataTable({
    paging: true, bFilter: true, ordering: true, searching: true,
    destroy: true, responsive: true, scrollX: false, autoWidth: false,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
    ajax: {
      url: _URL + "/ajs/orden-servicio/render",
      method: "POST",
      dataSrc: "",
    },
    language: { url: "../ServerSide/Spanish.json" },
    columns: [
      { data: "numero", class: "text-center" },
      { data: "cliente_razon_social", class: "text-center" },
      { data: "cliente_ruc", class: "text-center" },
      { data: "atencion_encargado", class: "text-center" },
      { data: "fecha_ingreso", class: "text-center" },
      {
        data: null, class: "text-center",
        render: (data, type, row) => {
          const tieneCotizacion = Number.parseInt(row.tiene_cotizacion);
          const badgeStyle = `display:inline-block;width:130px;text-align:center;padding:5px 10px;border-radius:50px;font-weight:bold;font-size:0.70rem;text-transform:uppercase;white-space:nowrap;`;
          if (tieneCotizacion === 1) {
            return `<span onclick="verCotizacion(${row.id_orden_servicio})" class="badge" style="${badgeStyle}background-color:#28a745;color:white;cursor:pointer;">
              <i class="fa fa-check-circle" style="margin-right:5px;"></i>COTIZACIÓN LISTA</span>`;
          }
          return `<span class="badge" style="${badgeStyle}background-color:#ffa500;color:white;">
            <i class="fa fa-clock" style="margin-right:5px;"></i>SIN COTIZACIÓN</span>`;
        },
      },
      {
        data: null, class: "text-center",
        render: (data, type, row) => `
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-info btn-ver-detalles" data-id="${row.id_orden_servicio}"><i class="fa fa-eye"></i></button>
            <button data-id="${row.id_orden_servicio}" class="btn btn-warning btnEditar"><i class="fa fa-edit"></i></button>
            <button data-id="${row.id_orden_servicio}" class="btn btn-danger btnBorrar"><i class="fa fa-trash"></i></button>
          </div>`,
      },
    ],
    drawCallback: function () { $('[data-bs-toggle="tooltip"]').tooltip(); },
  });

  // ===== FUNCIONES ESPECÍFICAS =====
  window.verCotizacion = (id) => {
    window.location.href = `/tallers/coti/view/`;
  };

  function mostrarDetalles(id) {
    $.ajax({
      url: _URL + "/ajs/orden-servicio/detalles",
      type: "POST",
      data: { id: id },
      timeout: 15000,
      success: (response) => {
        try {
          if (typeof response === "string" &&
            (response.trim().toLowerCase().startsWith("<!doctype") || response.trim().toLowerCase().startsWith("<html"))) {
            throw new Error("El servidor devolvió HTML en lugar de JSON.");
          }
          var detalles = typeof response === "object" ? response : JSON.parse(response);
          var equiposHTML = "";
          detalles.equipos.forEach((equipo, index) => {
            equiposHTML += `<tr><td>${index + 1}</td><td>${equipo.marca}</td><td>${equipo.modelo}</td><td>${equipo.equipo}</td><td>${equipo.numero_serie}</td></tr>`;
          });

          var contenidoModal = `
            <div class="card border-danger mb-2">
              <div class="card-header bg-secondary p-2">Información de Orden de Servicio</div>
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
            icon: "error", title: "Error",
            text: "Error al procesar los detalles. " + (error.message || ""),
            footer: '<a href="javascript:location.reload()">Recargar la página</a>',
          });
        }
      },
      error: () => {
        Swal.fire({
          icon: "error", title: "Error",
          text: "No se pudieron cargar los detalles.",
          footer: '<a href="javascript:location.reload()">Recargar la página</a>',
        });
      },
    });
  }

  // ===== EVENT LISTENERS =====
  $("#submitRegistro").click(() => {
    if (!app.validateForm()) return;
    if (app.maquinasIdenticas && !app.procesarSeriesMultiples()) return;

    if (!app.prealerta.cliente_Rsocial || !$("#atencion_Encargado").val() || !$("#fecha_ingreso").val()) {
      Swal.fire({ icon: "error", title: "Error", text: "Por favor complete todos los campos requeridos" });
      return;
    }

    const equiposValidos = app.equipos.every((eq) => eq.tipo && eq.serie);
    if (!equiposValidos) {
      Swal.fire({ icon: "error", title: "Error", text: "Por favor complete equipo y número de serie de cada uno" });
      return;
    }

    const data = {
      cliente_Rsocial: app.prealerta.cliente_Rsocial,
      num_doc: app.prealerta.num_doc,
      atencion_Encargado: $("#atencion_Encargado").val(),
      fecha_ingreso: $("#fecha_ingreso").val(),
      origen: "Ord Servicio",
      direccion: $("#direccion").val(),
      observaciones: $("#observaciones").val(),
      id_almacen: app.idAlmacen || null,
      equipos: app.equipos.map((eq) => ({
        marca: eq.marca || "",
        modelo: eq.modelo || "",
        equipo: eq.tipo,
        numero_serie: eq.serie,
        id_producto: eq.id_producto || null,
      })),
    };

    $("#loader-menor").show();
    $.ajax({
      type: "POST",
      url: _URL + "/ajs/orden-servicio/add",
      data: data,
      success: (resp) => {
        $("#loader-menor").hide();
        try {
          const response = typeof resp === "object" ? resp : JSON.parse(resp);
          if (response && !response.error) {
            tabla_clientes.ajax.reload(null, false);
            Swal.fire({ icon: "success", title: "¡Buen trabajo!", text: "Registro Exitoso" });
            $("#modalAgregar").modal("hide");
            $("#frmClientesAgregar").trigger("reset");

            app.prealerta.num_doc = "";
            app.prealerta.cliente_Rsocial = "";
            $("#direccion").val("");
            app.equipos = [];
            app.cantidadEquipos = 1;
            app.inicializarEquipos();
            app.maquinasIdenticas = false;
            app.seriesMultiples = "";
            app.equipoBase = { marca: "", modelo: "", tipo: "", id_producto: null, producto_busqueda: "" };
            app.idAlmacen = null;
            app.validationErrors = { marca: "", modelo: "", equipo: "", cantidad: "", series: "" };
          } else {
            throw new Error(response.error || "Error al guardar");
          }
        } catch (error) {
          Swal.fire({ icon: "error", title: "Error", text: error.message || "Error al procesar la respuesta" });
        }
      },
      error: () => {
        $("#loader-menor").hide();
        Swal.fire({ icon: "error", title: "Error", text: "Error al intentar guardar el registro" });
      },
    });
  });

  // Eliminar
  $("#tabla_clientes").on("click", ".btnBorrar", function () {
    var id = $(this).data("id");
    Swal.fire({
      title: "¿Deseas borrar el registro?", icon: "warning",
      showCancelButton: true, confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33", confirmButtonText: "Si",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: _URL + "/ajs/orden-servicio/delete",
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

  $(document).on("click", ".btnEditar", function () { app.cargarDatosEdicion($(this).data("id")); });
  $(document).on("click", ".btn-ver-detalles", function () { mostrarDetalles($(this).data("id")); });

  // ===== INICIALIZACIÓN =====
  inicializarCatalogosEventos(["marcas", "modelos", "equipos", "tecnicos"]);

  $("#modalAgregar").on("show.bs.modal", () => {
    const today = new Date().toISOString().split("T")[0];
    $("#fecha_ingreso").val(today);
  });

  Promise.all([
    cargarSelect("modelos", "#modelo"), cargarSelect("marcas", "#marca"),
    cargarSelect("equipos", "#equipo"), cargarSelect("tecnicos", "#atencion_Encargado"),
    cargarSelect("modelos", "#edit_modelo"), cargarSelect("marcas", "#edit_marca"),
    cargarSelect("equipos", "#edit_equipo"), cargarSelect("tecnicos", "#edit_atencion_encargado"),
  ]).catch((error) => {
    mostrarAlerta("Error", "No se pudieron cargar algunos datos. Por favor, recarga la página.", "error");
  });

  // Limpiar estado al cerrar modal edición
  $("#modalEditar").on("hidden.bs.modal", function () {
    if (app) {
      app.maquinasIdenticas = false;
      app.seriesMultiples = "";
      app.equipoBase = { marca: "", modelo: "", tipo: "", id_producto: null, producto_busqueda: "" };
      app.cantidadMaquinasIdenticas = 1;
      app.editando = {
        id_orden_servicio: null, cliente_Rsocial: "", cliente_ruc: "",
        atencion_Encargado: "", fecha_ingreso: "", id_almacen: null, equipos: [],
      };
    }
  });

  // Re-wire autocomplete cuando se abre el modal Agregar
  $("#modalAgregar").on("shown.bs.modal", function () {
    if (!app) return;
    // Si no hay almacén elegido y hay disponibles, default = principal
    if (!app.idAlmacen && app.almacenesDisponibles && app.almacenesDisponibles.length) {
      const principal = app.almacenesDisponibles.find(a => Number(a.principal) === 1);
      if (principal) app.idAlmacen = Number(principal.id_almacen);
    }
    if (app.idAlmacen) app.onCambiarAlmacen();
  });
  $("#modalEditar").on("shown.bs.modal", function () {
    if (app && app.editando.id_almacen) app.onCambiarAlmacenEdit();
  });
});
