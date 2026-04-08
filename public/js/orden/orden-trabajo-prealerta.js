// public/js/orden/orden-trabajo-prealerta.js
// Funciones de búsqueda de pre-alerta para Orden de Trabajo
// Requiere: orden-base.js, orden-trabajo-repuestos.js (para var globales)

var isSelectingSerie = false;

// ===== AUTOCOMPLETADO POR NÚMERO DE REGISTRO =====
function inicializarAutocompletadoNumeros(app) {
  if ($("#input_buscar_numero").length === 0) return;

  if ($("#input_buscar_numero").hasClass("ui-autocomplete-input")) {
    $("#input_buscar_numero").autocomplete("destroy");
  }

  $("#input_buscar_numero").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: _URL + "/ajs/prealerta/buscar/numero/datos",
        type: "GET",
        data: { term: request.term },
        success: function (data) {
          try {
            response(JSON.parse(data));
          } catch (e) {
            response([]);
          }
        },
        error: function () { response([]); },
      });
    },
    minLength: 0,
    delay: 300,
    select: function (event, ui) {
      event.preventDefault();
      isSelectingSerie = true;

      // Llenar datos del cliente
      if (ui.item.cliente_ruc_dni && ui.item.cliente_ruc_dni !== null) {
        app.prealerta.cliente_Rsocial = ui.item.cliente_ruc_dni || "";
        app.prealerta.num_doc = ui.item.cliente_documento || "";
      } else {
        // Registro interno JVC
        app.prealerta.cliente_Rsocial = "COMERCIAL & INDUSTRIAL J. V. C. S.A.C.";
        app.prealerta.num_doc = "20538381978";
        Swal.fire({
          icon: "info",
          title: "Registro Interno (JVC)",
          text: "Este es un registro interno de la empresa. Se han cargado los datos automáticamente.",
          timer: 3000,
          showConfirmButton: false,
        });
      }

      // Procesar datos agrupados con GROUP_CONCAT
      const numeros_serie = ui.item.numero_serie ? ui.item.numero_serie.split(",").map((s) => s.trim()) : [];
      const marcas_nombres = (ui.item.marca_nombre || "").split(",").map((s) => s.trim()).filter((s) => s);
      const modelos_nombres = (ui.item.modelo_nombre || "").split(",").map((s) => s.trim()).filter((s) => s);
      const equipos_nombres = (ui.item.equipo_nombre || "").split(",").map((s) => s.trim()).filter((s) => s);

      // Crear equipos
      app.equipos = [];
      for (let i = 0; i < numeros_serie.length; i++) {
        app.equipos.push({
          marca: marcas_nombres[i] || marcas_nombres[0] || "Sin marca",
          modelo: modelos_nombres[i] || modelos_nombres[0] || "Sin modelo",
          equipo: equipos_nombres[i] || equipos_nombres[0] || "Sin equipo",
          serie: numeros_serie[i] || "",
        });
      }

      Vue.set(app, "cantidadEquipos", numeros_serie.length);
      app.$forceUpdate();
      $(this).val("");

      Swal.fire({
        icon: "success",
        title: "Registro seleccionado",
        text: `Registro ${ui.item.numero_registro} agregado correctamente`,
        timer: 2000,
        showConfirmButton: false,
      });
    },
    open: function () {
      $(this).autocomplete("widget").css({
        "max-height": "250px",
        "overflow-y": "auto",
        "overflow-x": "hidden",
      });
    },
  });

  $("#input_buscar_numero").on("focus click", function () {
    if (isSelectingSerie) {
      isSelectingSerie = false;
      return;
    }
    if ($(this).val() === "") {
      $(this).autocomplete("search", "");
    }
  });
}

// ===== AUTOCOMPLETADO POR CLIENTE =====
function inicializarAutocompletadoCliente(app) {
  if ($("#input_buscar_cliente_prealerta").length === 0) return;

  if ($("#input_buscar_cliente_prealerta").hasClass("ui-autocomplete-input")) {
    $("#input_buscar_cliente_prealerta").autocomplete("destroy");
  }

  $("#input_buscar_cliente_prealerta").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: _URL + "/ajs/prealerta/buscar/cliente/serie",
        type: "GET",
        data: { term: request.term },
        success: function (data) {
          try {
            response(JSON.parse(data));
          } catch (e) {
            response([]);
          }
        },
        error: function () { response([]); },
      });
    },
    minLength: 0,
    delay: 300,
    select: function (event, ui) {
      event.preventDefault();
      app.prealerta.cliente_Rsocial = ui.item.label || "";
      app.prealerta.num_doc = ui.item.cliente_documento || "";
      $("#cliente_nombre_right").val(ui.item.label || "");
      mostrarModalSeleccionSeriePreAlerta(ui.item.id, app);
      $(this).val("");
    },
    open: function () {
      $(this).autocomplete("widget").css({
        "max-height": "250px",
        "overflow-y": "auto",
        "overflow-x": "hidden",
        width: $(this).outerWidth() + "px",
        "z-index": "9999",
      });
    },
  });

  $("#input_buscar_cliente_prealerta")
    .off("focus.autocomplete click.autocomplete")
    .on("focus.autocomplete click.autocomplete", function () {
      if (!$(this).autocomplete("widget").is(":visible")) {
        $(this).autocomplete("search", "");
      }
    });
}

// ===== MODAL DE SELECCIÓN DE SERIES POR CLIENTE =====
function mostrarModalSeleccionSeriePreAlerta(clienteId, app) {
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
          (serie) => serie.estado_prealerta !== "en_trabajo" && serie.estado_prealerta !== "culminado"
        ).length;

        let seriesHtml = "";
        series.forEach((serie) => {
          const enTrabajo = serie.estado_prealerta === "en_trabajo" || serie.estado_prealerta === "culminado";
          const estadoTexto = serie.estado_prealerta === "en_trabajo" ? "En Trabajo"
            : serie.estado_prealerta === "culminado" ? "Culminado" : "Disponible";
          const estadoClase = serie.estado_prealerta === "en_trabajo" ? "bg-warning text-white"
            : serie.estado_prealerta === "culminado" ? "bg-secondary text-white" : "bg-success text-white";

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
                    data-modelo="${serie.modelo_nombre || ""}"
                    data-equipo="${serie.equipo_nombre || ""}"
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
                          <th>Número de Serie</th><th>Marca</th><th>Modelo</th>
                          <th>Equipo</th><th>Estado</th><th>Seleccionar</th>
                        </tr>
                      </thead>
                      <tbody>${seriesHtml}</tbody>
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

        // Seleccionar todas
        $("#seleccionar_todas_series_prealerta").change(function () {
          $(".seleccionar-serie-checkbox-prealerta:not(:disabled)").prop("checked", $(this).prop("checked"));
        });

        $(document).on("change", ".seleccionar-serie-checkbox-prealerta", function () {
          const todasSeleccionadas =
            $(".seleccionar-serie-checkbox-prealerta:not(:disabled)").length ===
            $(".seleccionar-serie-checkbox-prealerta:not(:disabled):checked").length;
          $("#seleccionar_todas_series_prealerta").prop("checked", todasSeleccionadas);
        });

        // Confirmar selección
        $("#confirmar_seleccion_series_prealerta").click(function () {
          const seriesSeleccionadas = [];
          $(".seleccionar-serie-checkbox-prealerta:checked").each(function () {
            const checkbox = $(this);
            seriesSeleccionadas.push({
              numero_serie: checkbox.data("serie"),
              marca: checkbox.data("marca"),
              modelo: checkbox.data("modelo"),
              tipo: checkbox.data("equipo"),
            });
          });

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

        // Limpiar modal al cerrar
        $("#modalSeleccionSeriePreAlerta").on("hidden.bs.modal", function () {
          $(this).remove();
        });
      } catch (e) {
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
