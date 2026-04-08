// public/js/orden/orden-base.js
// Lógica compartida entre Orden de Trabajo y Orden de Servicio

// ===== ALERTAS =====
function mostrarAlerta(titulo, mensaje, tipo) {
  Swal.fire({
    title: titulo,
    text: mensaje,
    icon: tipo,
    confirmButtonText: "Aceptar",
    confirmButtonColor: "#3085d6",
    customClass: {
      confirmButton: "btn btn-primary",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });
}

function confirmarEliminacion(mensaje) {
  return Swal.fire({
    title: "¿Está seguro?",
    text: mensaje,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    customClass: {
      confirmButton: "btn btn-danger me-2",
      cancelButton: "btn btn-secondary",
    },
    buttonsStyling: false,
  });
}

function alertAdvertencia(mensaje) {
  Swal.fire({
    icon: "warning",
    title: "Advertencia",
    text: mensaje,
  });
}

// ===== BÚSQUEDA DE DOCUMENTO (DNI/RUC) =====
function buscarDocumentoCliente(vueApp, opciones) {
  const docLength = vueApp.prealerta.num_doc.length;
  if (docLength !== 8 && docLength !== 11) {
    alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
    return;
  }

  $("#loader-menor").show();
  vueApp.prealerta.dir_pos = 1;

  _ajax(
    "/ajs/prealerta/doc/cliente",
    "POST",
    { doc: vueApp.prealerta.num_doc },
    (resp) => {
      $("#loader-menor").hide();
      if (docLength === 8) {
        if (resp.success) {
          vueApp.prealerta.cliente_Rsocial =
            resp.nombres +
            " " +
            (resp.apellidoPaterno || "") +
            " " +
            (resp.apellidoMaterno || "");
          $("#direccion").val("");
        } else {
          alertAdvertencia("Documento no encontrado");
        }
      } else if (docLength === 11) {
        if (resp.razonSocial) {
          vueApp.prealerta.cliente_Rsocial = resp.razonSocial;
          if (
            opciones.usarDireccion &&
            vueApp.prealerta.num_doc.startsWith("20")
          ) {
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
}

// ===== CATÁLOGOS CRUD (marcas, modelos, equipos, técnicos) =====
function cargarTablaCatalogo(tipo, tablaSelector) {
  $.get(_URL + "/ajs/get/" + tipo, (data) => {
    let html = "";
    JSON.parse(data).forEach((item) => {
      html += `
        <tr data-id="${item.id}">
          <td class="nombre-campo">${item.nombre}</td>
          <td>
            <button class="btn btn-sm editar-${tipo.slice(0, -1)}" style="color: #0d6efd;">
              <i class="fa fa-edit"></i>
            </button>
            <button class="btn btn-sm eliminar-${tipo.slice(0, -1)}" style="color: #dc3545;">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>`;
    });
    $(`${tablaSelector} tbody`).html(html);
  });
}

function cargarTablaMarcas() {
  cargarTablaCatalogo("marcas", "#tablaMarcas");
}
function cargarTablaModelos() {
  cargarTablaCatalogo("modelos", "#tablaModelos");
}
function cargarTablaEquipos() {
  cargarTablaCatalogo("equipos", "#tablaEquipos");
}
function cargarTablaTecnicos() {
  cargarTablaCatalogo("tecnicos", "#tablaTecnicos");
}

// Cargar select dropdown con datos de catálogo
function cargarSelect(tipo, selector) {
  return new Promise((resolve, reject) => {
    $.get(`${_URL}/ajs/get/${tipo}`, (data) => {
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

// ===== EDICIÓN EN LÍNEA =====
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

function cancelarEdicion(td, texto) {
  td.html(texto);
}

// ===== MODAL BACKDROP (blur) =====
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

// ===== INICIALIZAR EVENTOS DE CATÁLOGOS =====
function inicializarCatalogosEventos(catalogos) {
  // catalogos = ['marcas', 'modelos', 'equipos', 'tecnicos']
  // o solo ['tecnicos'] para orden-trabajo

  const configCatalogos = {
    marcas: {
      singular: "marca",
      tabla: cargarTablaMarcas,
      inputId: "#marca_nombre",
      selectId: "#marca",
      label: "marca",
    },
    modelos: {
      singular: "modelo",
      tabla: cargarTablaModelos,
      inputId: "#modelo_nombre",
      selectId: "#modelo",
      label: "modelo",
    },
    equipos: {
      singular: "equipo",
      tabla: cargarTablaEquipos,
      inputId: "#equipo_nombre",
      selectId: "#equipo",
      label: "equipo",
    },
    tecnicos: {
      singular: "tecnico",
      tabla: cargarTablaTecnicos,
      inputId: "#tecnico_nombre",
      selectId: "#atencion_Encargado",
      label: "técnico",
    },
  };

  catalogos.forEach((tipo) => {
    const cfg = configCatalogos[tipo];
    if (!cfg) return;

    const singular = cfg.singular;
    const modalId = `modal${singular.charAt(0).toUpperCase() + singular.slice(1)}`;

    // Abrir modal → cargar tabla
    $(`#${modalId}`).on("show.bs.modal", cfg.tabla);

    // Backdrop blur
    handleModalBackdrop(modalId);

    // Editar en línea
    $(document).on("click", `.editar-${singular}`, function () {
      const td = $(this).closest("tr").find(".nombre-campo");
      habilitarEdicionEnLinea(td, singular);
    });

    // Guardar edición
    $(document).on("click", `.btn-guardar-${singular}`, function () {
      const id = $(this).data("id");
      const td = $(this).closest("td");
      const nuevoNombre = td.find("input").val();

      $.ajax({
        url: _URL + `/ajs/update/${tipo}`,
        type: "POST",
        data: { id: id, nombre: nuevoNombre },
        success: () => {
          cfg.tabla();
          cargarSelect(tipo, cfg.selectId);
          mostrarAlerta(
            "Éxito",
            `${cfg.label.charAt(0).toUpperCase() + cfg.label.slice(1)} actualizado correctamente`,
            "success"
          );
        },
        error: () => {
          mostrarAlerta(
            "Error",
            `No se pudo actualizar el ${cfg.label}`,
            "error"
          );
        },
      });
    });

    // Agregar nuevo
    const btnAgregarId = `#btnAgregar${singular.charAt(0).toUpperCase() + singular.slice(1)}`;
    $(btnAgregarId).click(() => {
      const nombre = $(cfg.inputId).val();
      if (!nombre) {
        mostrarAlerta(
          "Error",
          `Por favor ingrese un nombre de ${cfg.label}`,
          "error"
        );
        return;
      }

      $.ajax({
        url: _URL + `/ajs/save/${tipo}`,
        type: "POST",
        data: { nombre: nombre },
        success: () => {
          $(cfg.inputId).val("");
          cfg.tabla();
          cargarSelect(tipo, cfg.selectId);
          mostrarAlerta(
            "Éxito",
            `${cfg.label.charAt(0).toUpperCase() + cfg.label.slice(1)} agregado correctamente`,
            "success"
          );
        },
        error: () => {
          mostrarAlerta(
            "Error",
            `No se pudo agregar el ${cfg.label}`,
            "error"
          );
        },
      });
    });

    // Eliminar
    $(document).on("click", `.eliminar-${singular}`, function () {
      const id = $(this).closest("tr").data("id");
      confirmarEliminacion(
        `¿Está seguro de eliminar este ${cfg.label}?`
      ).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: _URL + `/ajs/delete/${tipo}`,
            type: "POST",
            data: { id: id },
            success: () => {
              cfg.tabla();
              cargarSelect(tipo, cfg.selectId);
              mostrarAlerta(
                "Éxito",
                `${cfg.label.charAt(0).toUpperCase() + cfg.label.slice(1)} eliminado correctamente`,
                "success"
              );
            },
            error: () => {
              mostrarAlerta(
                "Error",
                `No se pudo eliminar el ${cfg.label}`,
                "error"
              );
            },
          });
        }
      });
    });
  });

  // Cancelar edición (genérico)
  $(document).on("click", ".btn-cancelar-edicion", function () {
    const td = $(this).closest("td");
    const textoOriginal = td.parent().find(".nombre-campo").text();
    cancelarEdicion(td, textoOriginal);
  });
}

// ===== MÁQUINAS IDÉNTICAS (compartido) =====
function procesarSeriesMultiples(vueApp) {
  if (!vueApp.maquinasIdenticas || !vueApp.seriesMultiples) return false;

  const series = vueApp.seriesMultiples
    .split(/[\n,\s]+/)
    .filter((serie) => serie.trim() !== "");

  if (series.length !== parseInt(vueApp.cantidadMaquinasIdenticas)) {
    Swal.fire({
      icon: "warning",
      title: "Advertencia",
      text: `La cantidad de series (${series.length}) no coincide con la cantidad de máquinas indicada (${vueApp.cantidadMaquinasIdenticas})`,
    });
    return false;
  }

  vueApp.equipos = [];
  series.forEach((serie) => {
    vueApp.equipos.push({
      marca: vueApp.equipoBase.marca,
      modelo: vueApp.equipoBase.modelo,
      tipo: vueApp.equipoBase.tipo,
      serie: serie.trim(),
    });
  });

  vueApp.cantidadEquipos = series.length;
  return true;
}

function procesarSeriesMultiplesEdicion(vueApp) {
  if (
    !vueApp.equipoBase.marca ||
    !vueApp.equipoBase.modelo ||
    !vueApp.equipoBase.tipo
  ) {
    Swal.fire({
      icon: "warning",
      title: "Advertencia",
      text: "Por favor complete todos los campos de marca, modelo y equipo",
    });
    return false;
  }

  if (!vueApp.seriesMultiples) {
    Swal.fire({
      icon: "warning",
      title: "Advertencia",
      text: "Por favor ingrese los números de serie",
    });
    return false;
  }

  const series = vueApp.seriesMultiples
    .split(/[\n,\s]+/)
    .filter((serie) => serie.trim() !== "");

  if (series.length !== parseInt(vueApp.cantidadMaquinasIdenticas)) {
    Swal.fire({
      icon: "warning",
      title: "Advertencia",
      text: `La cantidad de series (${series.length}) no coincide con la cantidad de máquinas indicada (${vueApp.cantidadMaquinasIdenticas})`,
    });
    return false;
  }

  vueApp.editando.equipos = [];
  series.forEach((serie) => {
    vueApp.editando.equipos.push({
      marca: vueApp.equipoBase.marca,
      modelo: vueApp.equipoBase.modelo,
      equipo: vueApp.equipoBase.tipo,
      numero_serie: serie.trim(),
    });
  });

  return true;
}

function detectarEquiposIdenticos(equipos) {
  if (!equipos || equipos.length <= 1) return false;
  const primero = equipos[0];
  return equipos.every(
    (eq) =>
      eq.marca === primero.marca &&
      eq.modelo === primero.modelo &&
      eq.equipo === primero.equipo
  );
}

function prepararEdicionEquiposIdenticos(vueApp) {
  if (!detectarEquiposIdenticos(vueApp.editando.equipos)) return;

  vueApp.equipoBase = {
    marca: vueApp.editando.equipos[0].marca,
    modelo: vueApp.editando.equipos[0].modelo,
    tipo: vueApp.editando.equipos[0].equipo,
  };

  vueApp.seriesMultiples = vueApp.editando.equipos
    .map((eq) => eq.numero_serie)
    .join("\n");
  vueApp.cantidadMaquinasIdenticas = vueApp.editando.equipos.length;
  vueApp.maquinasIdenticas = true;
}

function contarSeries(vueApp) {
  const series = vueApp.seriesMultiples
    .split(/[\n,\s]+/)
    .filter((s) => s.trim());
  vueApp.seriesCount = series.length;

  if (vueApp.cantidadMaquinasIdenticas > 0) {
    if (series.length !== vueApp.cantidadMaquinasIdenticas) {
      vueApp.validationErrors.series = `Debe ingresar ${vueApp.cantidadMaquinasIdenticas} números de serie (actualmente: ${series.length})`;
    } else {
      vueApp.validationErrors.series = "";
    }
  }
}
