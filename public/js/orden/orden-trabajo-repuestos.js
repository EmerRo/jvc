// public/js/orden/orden-trabajo-repuestos.js
// Funciones globales para manejo de repuestos/productos en Orden de Trabajo
// Requiere: orden-base.js cargado antes

// Variables globales
var ordenTrabajoActual = null;
var maquinasActuales = [];

// ===== ABRIR MODAL DE REPUESTOS =====
function abrirModalAgregarRepuesto() {
  if (!ordenTrabajoActual) {
    Swal.fire("Error", "No hay orden de trabajo seleccionada", "error");
    return;
  }

  $("#modalDetalles").modal("hide");
  $("#modalDetalles").on("hidden.bs.modal", function () {
    cargarMaquinasEnModal();
    $("#modalAgregarRepuestos").modal("show");
    $(this).off("hidden.bs.modal");
  });
}

// ===== CARGAR MÁQUINAS EN PESTAÑAS =====
function cargarMaquinasEnModal() {
  const tabsContainer = document.getElementById("maquinasRepuestosTabs");
  const contentContainer = document.getElementById("maquinasRepuestosTabContent");

  if (!tabsContainer || !contentContainer) return;

  tabsContainer.innerHTML = "";
  contentContainer.innerHTML = "";

  if (maquinasActuales.length === 1) {
    // Una sola máquina: sin pestañas
    tabsContainer.style.display = "none";
    const maquina = maquinasActuales[0];
    const content = document.createElement("div");
    content.className = "tab-pane fade show active";
    content.id = `maquina-${maquina.id_detalle}`;
    content.innerHTML = crearContenidoMaquina(maquina);
    contentContainer.appendChild(content);
  } else {
    // Múltiples máquinas: pestañas con navegación
    tabsContainer.style.display = "flex";
    tabsContainer.style.alignItems = "center";
    tabsContainer.style.justifyContent = "space-between";

    const navContainer = document.createElement("div");
    navContainer.style.display = "flex";
    navContainer.style.alignItems = "center";
    navContainer.style.width = "100%";

    // Botón anterior
    const prevBtn = document.createElement("button");
    prevBtn.type = "button";
    prevBtn.className = "btn bg-rojo btn-sm me-2";
    prevBtn.innerHTML = '<i class="fa fa-chevron-left"></i>';
    prevBtn.id = "prevTabsBtn";
    prevBtn.style.display = maquinasActuales.length > 6 ? "block" : "none";

    // Contenedor de pestañas
    const tabsWrapper = document.createElement("div");
    tabsWrapper.style.display = "flex";
    tabsWrapper.style.flex = "1";
    tabsWrapper.style.overflow = "hidden";
    tabsWrapper.id = "tabsWrapper";

    const tabsList = document.createElement("ul");
    tabsList.className = "nav nav-pills";
    tabsList.style.display = "flex";
    tabsList.style.flexWrap = "nowrap";
    tabsList.style.transition = "transform 0.3s ease";
    tabsList.id = "tabsList";

    // Botón siguiente
    const nextBtn = document.createElement("button");
    nextBtn.type = "button";
    nextBtn.className = "btn bg-rojo btn-sm ms-2";
    nextBtn.innerHTML = '<i class="fa fa-chevron-right"></i>';
    nextBtn.id = "nextTabsBtn";
    nextBtn.style.display = maquinasActuales.length > 6 ? "block" : "none";

    // Crear pestañas
    maquinasActuales.forEach((maquina, index) => {
      const tabId = `maquina-${maquina.id_detalle}`;
      const tab = document.createElement("li");
      tab.className = "nav-item";
      tab.style.minWidth = "120px";
      tab.style.textAlign = "center";
      tab.style.marginRight = "10px";

      tab.innerHTML = `
        <button class="nav-link ${index === 0 ? "active" : ""}"
                id="${tabId}-tab"
                data-bs-toggle="pill"
                data-bs-target="#${tabId}"
                type="button"
                role="tab"
                style="
                  white-space: nowrap;
                  font-size: 0.9rem;
                  padding: 8px 12px;
                  transition: all 0.3s ease;
                  cursor: pointer;
                  background-color: ${index === 0 ? "#dc3545" : "transparent"};
                  color: ${index === 0 ? "white" : "#6c757d"};
                  border: none;
                  border-radius: 4px;
                "
                onmouseover="
                  if(!this.classList.contains('active')) {
                    this.style.color='#dc3545';
                    this.style.textDecoration='underline';
                  }
                "
                onmouseout="
                  if(!this.classList.contains('active')) {
                    this.style.color='#6c757d';
                    this.style.textDecoration='none';
                  }
                "
                onclick="
                  document.querySelectorAll('#tabsList .nav-link').forEach(btn => {
                    btn.classList.remove('active');
                    btn.style.backgroundColor = 'transparent';
                    btn.style.color = '#6c757d';
                    btn.style.textDecoration = 'none';
                  });
                  this.classList.add('active');
                  this.style.backgroundColor = '#dc3545';
                  this.style.color = 'white';
                  this.style.textDecoration = 'none';
                  cargarProductosExistentesMaquina(${maquina.id_detalle});
                ">
          Equipo ${index + 1}
        </button>
      `;
      tabsList.appendChild(tab);

      // Contenido de pestaña
      const content = document.createElement("div");
      content.className = `tab-pane fade ${index === 0 ? "show active" : ""}`;
      content.id = tabId;
      content.innerHTML = crearContenidoMaquina(maquina);
      contentContainer.appendChild(content);
    });

    // Ensamblar estructura
    tabsWrapper.appendChild(tabsList);
    navContainer.appendChild(prevBtn);
    navContainer.appendChild(tabsWrapper);
    navContainer.appendChild(nextBtn);
    tabsContainer.appendChild(navContainer);

    // Navegación por páginas
    let currentPage = 0;
    const tabsPerPage = 6;
    const totalPages = Math.ceil(maquinasActuales.length / tabsPerPage);

    function updateTabsVisibility() {
      const translateX = -(currentPage * (100 / totalPages));
      tabsList.style.transform = `translateX(${translateX}%)`;
      prevBtn.disabled = currentPage === 0;
      nextBtn.disabled = currentPage === totalPages - 1;
      prevBtn.style.opacity = currentPage === 0 ? "0.5" : "1";
      prevBtn.style.cursor = currentPage === 0 ? "not-allowed" : "pointer";
      nextBtn.style.opacity = currentPage === totalPages - 1 ? "0.5" : "1";
      nextBtn.style.cursor = currentPage === totalPages - 1 ? "not-allowed" : "pointer";
    }

    prevBtn.addEventListener("click", () => {
      if (currentPage > 0) { currentPage--; updateTabsVisibility(); }
    });
    nextBtn.addEventListener("click", () => {
      if (currentPage < totalPages - 1) { currentPage++; updateTabsVisibility(); }
    });

    tabsList.style.width = `${(maquinasActuales.length / tabsPerPage) * 100}%`;

    // Cargar productos de la primera máquina
    if (maquinasActuales.length > 0) {
      setTimeout(() => {
        cargarProductosExistentesMaquina(maquinasActuales[0].id_detalle);
      }, 500);
    }

    Array.from(tabsList.children).forEach((tab) => {
      tab.style.width = `${100 / maquinasActuales.length}%`;
    });

    updateTabsVisibility();
  }

  // Inicializar autocomplete y cargar repuestos existentes
  setTimeout(() => {
    inicializarBusquedaRepuestos();
    cargarRepuestosExistentes();
  }, 100);
}

// ===== CONTENIDO HTML DE CADA MÁQUINA =====
function crearContenidoMaquina(maquina) {
  return `
    <div class="row">
      <div class="col-md-12">
        <div class="card border-0 bg-light mb-3">
          <div class="card-body">
            <h6><i class="fa fa-laptop me-1"></i> ${maquina.marca} - ${maquina.modelo}</h6>
            <p class="text-muted mb-0">Equipo: ${maquina.equipo} | Serie: ${maquina.numero_serie}</p>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="fa fa-plus me-1"></i> Agregar Producto</h6>
          </div>
          <div class="card-body">
            <form id="formRepuesto-${maquina.id_detalle}">
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Buscar Producto</label>
                  <input type="text" class="form-control repuesto-search"
                         placeholder="Buscar repuesto..."
                         data-maquina-id="${maquina.id_detalle}"
                         id="repuestoSearch-${maquina.id_detalle}">
                  <input type="hidden" id="repuestoId-${maquina.id_detalle}" name="id_repuesto">
                  <input type="hidden" id="repuestoNombre-${maquina.id_detalle}" name="nombre_repuesto">
                  <input type="hidden" id="repuestoCodigo-${maquina.id_detalle}" name="codigo_repuesto">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Cantidad</label>
                  <input type="number" class="form-control"
                         id="cantidad-${maquina.id_detalle}" name="cantidad" min="1" value="1">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Precio Unit.</label>
                  <input type="number" class="form-control"
                         id="precio-${maquina.id_detalle}" name="precio_unitario" step="0.01" readonly>
                </div>
                <div class="col-md-2">
                  <label class="form-label">&nbsp;</label>
                  <button type="button" class="btn bg-rojo text-white d-block w-100"
                          onclick="agregarRepuestoAMaquina(${maquina.id_detalle})">
                    <i class="fa fa-plus"></i> Agregar
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="card mt-3">
          <div class="card-header">
            <h6 class="mb-0"><i class="fa fa-list me-1"></i> Productos Agregados</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm" id="tablaRepuestos-${maquina.id_detalle}">
                <thead class="table-light">
                  <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Total</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

// ===== BÚSQUEDA AUTOCOMPLETE DE REPUESTOS/PRODUCTOS =====
function inicializarBusquedaRepuestos() {
  $(".repuesto-search").each(function () {
    const maquinaId = $(this).data("maquina-id");

    if ($(this).hasClass("ui-autocomplete-input")) {
      $(this).autocomplete("destroy");
    }

    $(this).autocomplete({
      source: function (request, response) {
        const buscarRepuestos = $.ajax({
          url: _URL + "/ajs/cargar/repuestos/" + window.currentSucursal + "?term=" + request.term,
          type: "GET",
        });
        const buscarProductos = $.ajax({
          url: _URL + "/ajs/cargar/productos/" + window.currentSucursal + "?term=" + request.term,
          type: "GET",
        });

        $.when(buscarRepuestos, buscarProductos)
          .done(function (repuestosData, productosData) {
            try {
              const repuestos = JSON.parse(repuestosData[0]) || [];
              const productos = JSON.parse(productosData[0]) || [];
              const repuestosConTipo = repuestos.map((item) => ({ ...item, tipo: "Repuesto" }));
              const productosConTipo = productos.map((item) => ({ ...item, tipo: "Producto" }));
              response([...repuestosConTipo, ...productosConTipo]);
            } catch (e) {
              response([]);
            }
          })
          .fail(function () { response([]); });
      },
      minLength: 1,
      delay: 300,
      select: function (event, ui) {
        event.preventDefault();
        $(`#repuestoId-${maquinaId}`).val(ui.item.codigo);
        $(`#repuestoCodigo-${maquinaId}`).val(ui.item.codigo_pp);
        $(`#repuestoNombre-${maquinaId}`).val(ui.item.nombre);
        $(`#precio-${maquinaId}`).val(ui.item.precio);
        $(this).val(ui.item.nombre);
      },
      open: function () {
        $(this).autocomplete("widget").css({
          "max-height": "200px",
          "overflow-y": "auto",
          "z-index": "9999",
        });
      },
    });
  });
}

// ===== AGREGAR REPUESTO A MÁQUINA =====
function agregarRepuestoAMaquina(maquinaId) {
  const repuestoId = $(`#repuestoId-${maquinaId}`).val();
  const repuestoNombre = $(`#repuestoNombre-${maquinaId}`).val();
  const repuestoCodigo = $(`#repuestoCodigo-${maquinaId}`).val();
  const cantidad = parseInt($(`#cantidad-${maquinaId}`).val());
  const precioUnitario = parseFloat($(`#precio-${maquinaId}`).val());
  const tipoItem = repuestoCodigo && repuestoCodigo.startsWith("REP-") ? "repuesto" : "producto";

  if (!repuestoId || !repuestoNombre) {
    Swal.fire("Error", "Debe seleccionar un producto", "error");
    return;
  }
  if (!cantidad || cantidad <= 0) {
    Swal.fire("Error", "La cantidad debe ser mayor a 0", "error");
    return;
  }
  if (precioUnitario === null || precioUnitario < 0) {
    Swal.fire("Error", "El precio del producto no puede ser negativo", "error");
    return;
  }

  $.ajax({
    url: _URL + "/ajs/orden-trabajo/repuestos/agregar",
    type: "POST",
    data: {
      id_orden_trabajo: ordenTrabajoActual,
      id_detalle_maquina: maquinaId,
      id_repuesto: repuestoId,
      tipo_item: tipoItem,
      cantidad: cantidad,
      precio_unitario: precioUnitario,
    },
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.success) {
          Swal.fire("Éxito", "Producto agregado correctamente", "success");
          $(`#repuestoSearch-${maquinaId}`).val("");
          $(`#repuestoId-${maquinaId}`).val("");
          $(`#repuestoCodigo-${maquinaId}`).val("");
          $(`#repuestoNombre-${maquinaId}`).val("");
          $(`#cantidad-${maquinaId}`).val("1");
          $(`#precio-${maquinaId}`).val("");
          cargarRepuestosParaMaquina(maquinaId);
        } else {
          Swal.fire("Error", result.message || "Error al agregar producto", "error");
        }
      } catch (e) {
        Swal.fire("Error", "Error al procesar la respuesta", "error");
      }
    },
    error: function () {
      Swal.fire("Error", "Error al comunicarse con el servidor", "error");
    },
  });
}

// ===== CARGAR REPUESTOS EXISTENTES =====
function cargarRepuestosExistentes() {
  if (!ordenTrabajoActual) return;

  $.ajax({
    url: _URL + "/ajs/orden-trabajo/repuestos/obtener",
    type: "POST",
    data: { id_orden_trabajo: ordenTrabajoActual },
    success: function (response) {
      try {
        const repuestos = JSON.parse(response);
        const repuestosPorMaquina = {};
        repuestos.forEach((repuesto) => {
          if (!repuestosPorMaquina[repuesto.id_detalle_maquina]) {
            repuestosPorMaquina[repuesto.id_detalle_maquina] = [];
          }
          repuestosPorMaquina[repuesto.id_detalle_maquina].push(repuesto);
        });
        Object.keys(repuestosPorMaquina).forEach((maquinaId) => {
          cargarRepuestosEnTabla(maquinaId, repuestosPorMaquina[maquinaId]);
        });
      } catch (e) {
        console.error("Error al cargar repuestos existentes:", e);
      }
    },
  });
}

function cargarRepuestosParaMaquina(maquinaId) {
  $.ajax({
    url: _URL + "/ajs/orden-trabajo/repuestos/obtener",
    type: "POST",
    data: { id_orden_trabajo: ordenTrabajoActual },
    success: function (response) {
      try {
        const repuestos = JSON.parse(response);
        const repuestosMaquina = repuestos.filter((r) => r.id_detalle_maquina == maquinaId);
        cargarRepuestosEnTabla(maquinaId, repuestosMaquina);
      } catch (e) {
        console.error("Error al cargar repuestos de máquina:", e);
      }
    },
  });
}

function cargarRepuestosEnTabla(maquinaId, repuestos) {
  const tbody = $(`#tablaRepuestos-${maquinaId} tbody`);
  tbody.empty();

  if (repuestos.length === 0) {
    tbody.append(`
      <tr>
        <td colspan="6" class="text-center text-muted">
          <i class="fa fa-info-circle me-1"></i> No hay productos agregados para esta máquina
        </td>
      </tr>
    `);
    return;
  }

  repuestos.forEach((repuesto) => {
    const total = (repuesto.cantidad * repuesto.precio_unitario).toFixed(2);
    tbody.append(`
      <tr>
        <td>${repuesto.codigo_item || "N/A"}</td>
        <td>${repuesto.nombre_item}</td>
        <td>${repuesto.cantidad}</td>
        <td>S/ ${parseFloat(repuesto.precio_unitario).toFixed(2)}</td>
        <td>S/ ${total}</td>
        <td>
          <button type="button" class="btn btn-sm btn-danger"
                  onclick="eliminarRepuesto(${repuesto.id_repuesto_orden})">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    `);
  });
}

// ===== ELIMINAR REPUESTO =====
function eliminarRepuesto(idRepuestoOrden) {
  Swal.fire({
    title: "¿Está seguro?",
    text: "Esta acción eliminará el repuesto de la orden de trabajo",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: _URL + "/ajs/orden-trabajo/repuestos/eliminar",
        type: "POST",
        data: { id_repuesto_orden: idRepuestoOrden },
        success: function (response) {
          try {
            const result = JSON.parse(response);
            if (result.success) {
              Swal.fire("Eliminado", "Repuesto eliminado correctamente", "success");
              cargarRepuestosExistentes();
              cargarRepuestosEnTab();
            } else {
              Swal.fire("Error", result.message || "Error al eliminar repuesto", "error");
            }
          } catch (e) {
            Swal.fire("Error", "Error al procesar la respuesta", "error");
          }
        },
        error: function () {
          Swal.fire("Error", "Error al comunicarse con el servidor", "error");
        },
      });
    }
  });
}

// ===== CARGAR REPUESTOS EN TAB (vista agrupada por máquina) =====
function cargarRepuestosEnTab() {
  if (!ordenTrabajoActual) return;

  const repuestosContent = document.getElementById("repuestos-content");
  if (!repuestosContent) return;

  repuestosContent.innerHTML = `
    <div class="text-center text-muted">
      <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
      <p>Cargando repuestos...</p>
    </div>
  `;

  $.ajax({
    url: _URL + "/ajs/orden-trabajo/repuestos/obtener",
    type: "POST",
    data: { id_orden_trabajo: ordenTrabajoActual },
    success: function (response) {
      try {
        const repuestos = JSON.parse(response);

        if (repuestos.length === 0) {
          repuestosContent.innerHTML = `
            <div class="text-center text-muted">
              <i class="fa fa-cogs fa-3x mb-3"></i>
              <p>No hay productos agregados a esta orden de trabajo</p>
              <button type="button" class="btn bg-rojo text-white" onclick="abrirModalAgregarRepuesto()">
                <i class="fa fa-plus me-1"></i> Agregar Primer Producto
              </button>
            </div>
          `;
          return;
        }

        // Agrupar por máquina
        const repuestosPorMaquina = {};
        repuestos.forEach((repuesto) => {
          const key = `${repuesto.marca}-${repuesto.equipo}-${repuesto.numero_serie}`;
          if (!repuestosPorMaquina[key]) {
            repuestosPorMaquina[key] = { maquina: repuesto, repuestos: [] };
          }
          repuestosPorMaquina[key].repuestos.push(repuesto);
        });

        let html = "";
        Object.keys(repuestosPorMaquina).forEach((key) => {
          const grupo = repuestosPorMaquina[key];
          const totalMaquina = grupo.repuestos.reduce(
            (sum, r) => sum + r.cantidad * r.precio_unitario, 0
          );

          html += `
            <div class="card mb-3">
              <div class="card-header bg-light">
                <h6 class="mb-0">
                  <i class="fa fa-laptop me-2"></i>
                  ${grupo.maquina.marca} - ${grupo.maquina.equipo}
                  <small class="text-muted">(Serie: ${grupo.maquina.numero_serie})</small>
                  <span class="badge bg-success float-end">Total: S/ ${totalMaquina.toFixed(2)}</span>
                </h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Código</th><th>Producto</th><th>Cantidad</th>
                        <th>Precio Unit.</th><th>Total</th><th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
          `;

          grupo.repuestos.forEach((repuesto) => {
            const total = (repuesto.cantidad * repuesto.precio_unitario).toFixed(2);
            html += `
              <tr>
                <td>${repuesto.codigo_item || "N/A"}</td>
                <td>${repuesto.nombre_item}</td>
                <td>${repuesto.cantidad}</td>
                <td>S/ ${parseFloat(repuesto.precio_unitario).toFixed(2)}</td>
                <td>S/ ${total}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-danger"
                          onclick="eliminarRepuesto(${repuesto.id_repuesto_orden})"
                          title="Eliminar repuesto">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            `;
          });

          html += `</tbody></table></div></div></div>`;
        });

        // Total general
        const totalGeneral = repuestos.reduce(
          (sum, r) => sum + r.cantidad * r.precio_unitario, 0
        );
        html += `
          <div class="card border-success">
            <div class="card-body text-center">
              <h5 class="text-success mb-0">
                <i class="fa fa-calculator me-2"></i>
                Total General: S/ ${totalGeneral.toFixed(2)}
              </h5>
            </div>
          </div>
        `;

        repuestosContent.innerHTML = html;
      } catch (e) {
        repuestosContent.innerHTML = `
          <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle me-2"></i> Error al cargar los repuestos
          </div>
        `;
      }
    },
    error: function () {
      repuestosContent.innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle me-2"></i> Error al comunicarse con el servidor
        </div>
      `;
    },
  });
}

// ===== CARGAR PRODUCTOS EXISTENTES DE UNA MÁQUINA =====
function cargarProductosExistentesMaquina(idDetalleMaquina) {
  if (!ordenTrabajoActual) return;

  const tablaBody = document.querySelector(`#tablaRepuestos-${idDetalleMaquina} tbody`);
  if (!tablaBody) return;

  tablaBody.innerHTML = `
    <tr>
      <td colspan="6" class="text-center">
        <i class="fa fa-spinner fa-spin"></i> Cargando productos...
      </td>
    </tr>
  `;

  $.ajax({
    url: _URL + "/ajs/orden-trabajo/repuestos/obtener",
    type: "POST",
    data: {
      id_orden_trabajo: ordenTrabajoActual,
      id_detalle_maquina: idDetalleMaquina,
    },
    success: function (response) {
      try {
        const productos = JSON.parse(response);
        tablaBody.innerHTML = "";

        if (productos.length === 0) {
          tablaBody.innerHTML = `
            <tr>
              <td colspan="6" class="text-center text-muted">
                No hay productos agregados a esta máquina
              </td>
            </tr>
          `;
          return;
        }

        productos.forEach((producto) => {
          const fila = document.createElement("tr");
          fila.innerHTML = `
            <td>${producto.codigo_item || "N/A"}</td>
            <td>${producto.nombre_item || "N/A"}</td>
            <td>${producto.cantidad || 0}</td>
            <td>S/ ${parseFloat(producto.precio_unitario || 0).toFixed(2)}</td>
            <td>S/ ${parseFloat(producto.precio_total || 0).toFixed(2)}</td>
            <td>
              <button type="button" class="btn btn-danger btn-sm"
                      onclick="eliminarRepuesto(${producto.id_repuesto_orden || 0})">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          `;
          tablaBody.appendChild(fila);
        });
      } catch (error) {
        tablaBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center text-danger">Error al cargar productos</td>
          </tr>
        `;
      }
    },
    error: function () {
      tablaBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-danger">Error al cargar productos</td>
        </tr>
      `;
    },
  });
}
