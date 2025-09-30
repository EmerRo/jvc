<?php
// Obtener permisos del usuario según su rol
$puedeEliminar = true; // Por defecto, puede eliminar

// Mostrar notificación si el usuario no tiene permisos de eliminación
if (!$puedeEliminar) {
  echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Nota:</strong> No tienes permisos para eliminar registros.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

// Consultar permisos específicos del rol
if (isset($_SESSION['id_rol'])) {
  $rolId = $_SESSION['id_rol'];
  $conexion = (new Conexion())->getConexion();
  $sql = "SELECT puede_eliminar FROM roles WHERE rol_id = ?";
  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("i", $rolId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $puedeEliminar = (bool) $row['puede_eliminar'];
  }
}
?>
<style>
  .badge-origen {
    width: 100px;
    display: inline-block;
    text-align: center;
  }

  /* Estilo para el modal de edición con scroll */
  #modalEditar .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }

  /* Reducir espacios en formularios */
  #modalEditar .form-group,
  #modalEditar .mb-3 {
    margin-bottom: 0.5rem !important;
  }

  #modalEditar .form-label {
    margin-bottom: 0.25rem;
  }

  /* Hacer más compacta la sección de equipos */
  #modalEditar .border.p-3.mb-3 {
    padding: 0.75rem !important;
    margin-bottom: 0.75rem !important;
  }

  #modalEditar .d-flex.justify-content-between.align-items-center.mb-3 {
    margin-bottom: 0.5rem !important;
  }

  /* Reducir espacio en filas */
  #modalEditar .row {
    margin-bottom: 0.25rem;
  }

  /* Estilos para el menú de acciones */
  .action-menu {
    position: relative;
    display: inline-block;
    width: 30px;
    margin: 0 auto;
  }

  .action-button {
    background: none;
    border: none;
    padding: 6px;
    border-radius: 4px;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
  }

  .action-button:hover {
    color: #4f46e5;
    background-color: #f3f4f6;
  }

  .dropdown-actions {
    position: fixed;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    min-width: 180px;
    display: none;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
  }

  /* Posicionamiento dinámico controlado por JavaScript */

  .dropdown-actions a {
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.2s;
    cursor: pointer;
    white-space: nowrap;
  }

  .dropdown-actions a:hover {
    background-color: #f3f4f6;
  }

  .dropdown-actions i {
    width: 16px;
  }

  .dropdown-actions .text-danger:hover {
    background-color: #fee2e2;
  }

  .dropdown-actions .divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 4px 0;
  }

  /* Muestra el menú cuando tiene la clase show */
  .action-menu.show .dropdown-actions {
    display: block;
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    .dropdown-actions {
      min-width: 160px;
      max-width: calc(100vw - 20px);
    }
  }
</style>
<div class="page-title-box" style="padding: 12px 0;">
  <div class="row align-items-center">
    <div class="col-md-10">
      <h6 class="page-title text-center">REGISTRO DE ORDEN DE TRABAJO Y SERVICIO</h6>
    </div>
    <div class="col-md-2 text-end">
      <!-- Espacio para mantener el layout -->
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card border-0" style="border-radius:12px;">
      <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
        <button type="button" class="btn bg-rojo text-white" data-bs-toggle="modal"
          data-bs-target="#modalReportesInventario">
          <i class="fas fa-download me-1"></i>Reportes de Inventario
        </button>
      </div>
      <div id="conte-vue-modals">
        <div class="card-body p-3">

          <!-- Modal de Reportes de Inventario -->
          <div class="modal fade" id="modalReportesInventario" tabindex="-1"
            aria-labelledby="modalReportesInventarioLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content border-0 ">
                <div class="modal-header py-2 bg-rojo text-white">
                  <h5 class="modal-title" id="modalReportesInventarioLabel">
                    <i class="fas fa-download me-1"></i>Reportes de Inventario
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                <div class="mb-3">
                  <label for="reporteTipoOrden" class="form-label">Tipo de Orden:</label>
                  <select id="reporteTipoOrden" class="form-select">
                    <option value="todos">Todos</option>
                    <option value="ORD TRABAJO">Orden de Trabajo</option>
                    <option value="ORD SERVICIO">Orden de Servicio</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="reportePeriodo" class="form-label">Período:</label>
                  <select id="reportePeriodo" class="form-select">
                    <option value="hoy">Hoy</option>
                    <option value="ayer">Ayer</option>
                    <option value="esta_semana">Esta semana</option>
                    <option value="semana_pasada">Semana pasada</option>
                    <option value="este_mes">Este mes</option>
                    <option value="mes_pasado">Mes pasado</option>
                    <option value="mes_1">Enero</option>
                    <option value="mes_2">Febrero</option>
                    <option value="mes_3">Marzo</option>
                    <option value="mes_4">Abril</option>
                    <option value="mes_5">Mayo</option>
                    <option value="mes_6">Junio</option>
                    <option value="mes_7">Julio</option>
                    <option value="mes_8">Agosto</option>
                    <option value="mes_9">Septiembre</option>
                    <option value="mes_10">Octubre</option>
                    <option value="mes_11">Noviembre</option>
                    <option value="mes_12">Diciembre</option>
                  </select>
                </div>
                <div class="d-grid gap-2">
                  <button id="btnDescargarReportePDF" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                  </button>
                  <button id="btnDescargarReporteExcel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

          <div class="mb-3 d-flex align-items-center" style="width: fit-content;">
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted">Filtrar</span>
              <i class="fas fa-filter text-muted"></i>
              <select id="filtroOrigen" class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                <option value="">Seleccionar</option>
                <option value="ORD TRABAJO">Orden de Trabajo</option>
                <option value="ORD SERVICIO">Orden de Servicio</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table id="tabla_ordenes"
              class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer"
              style="border-collapse: collapse; border-spacing: 0; width: 100%;">
              <thead class="table-light">
                <tr>
                  <th>Item</th>
                  <th>Cliente/Razón Social</th>
                  <th>Documento</th>
                  <th>Técnico</th>
                  <th>Fecha De Ingreso</th>
                  <th>Origen</th>
                  <th>Cotizar</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para mostrar detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 ">
      <div class="modal-header py-2 bg-rojo text-white">
        <h5 class="modal-title" id="modalDetallesLabel">Detalles De La Orden</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <!-- Aquí se cargarán los detalles dinámicamente -->
      </div>
    </div>
  </div>
</div>

<div id="client">

  <!-- Modal Editar -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-0 ">
        <div class="modal-header py-2 bg-rojo text-white">
          <h5 class="modal-title" id="modalEditarLabel">Editar Registro</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-3">
          <form id="frmOrdenesEditar">
                <input type="hidden" id="edit_id_orden" name="id_orden" v-model="editando.id">
                <input type="hidden" id="edit_tipo_orden" name="tipo_orden" v-model="editando.tipo">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="edit_cliente_Rsocial" class="form-label">Cliente (RUC o DNI)</label>
                      <input type="text" class="form-control" id="edit_cliente_Rsocial" name="cliente_razon_social"
                        v-model="editando.cliente_Rsocial" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="edit_atencion_Encargado" class="form-label">Técnico</label>
                      <input type="text" class="form-control" id="edit_atencion_Encargado" name="atencion_encargado"
                        v-model="editando.atencion_Encargado" required>
                    </div>
                  </div>
                </div>

                <!-- Sección de equipos múltiples -->
                <div v-for="(equipo, index) in editando.equipos" :key="index" class="border p-3 mb-3 rounded">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Equipo {{index + 1}}</h6>
                    <button type="button" class="btn btn-danger btn-sm" @click="eliminarEquipoEdicion(index)"
                      v-if="editando.equipos.length > 1">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Marca</label>
                      <div class="input-group">
                        <select class="form-control" v-model="equipo.marca">
                          <option v-for="marca in marcasDisponibles" :value="marca.nombre">
                            {{marca.nombre}}
                          </option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                          data-bs-target="#modalMarca">
                          <i class="fa fa-plus"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Modelo</label>
                      <div class="input-group">
                        <select class="form-control" v-model="equipo.modelo">
                          <option v-for="modelo in modelosDisponibles" :value="modelo.nombre">
                            {{modelo.nombre}}
                          </option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                          data-bs-target="#modalModelo">
                          <i class="fa fa-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Equipo</label>
                      <div class="input-group">
                        <select class="form-control" v-model="equipo.equipo">
                          <option v-for="eq in equiposDisponibles" :value="eq.nombre">
                            {{eq.nombre}}
                          </option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                          data-bs-target="#modalEquipo">
                          <i class="fa fa-plus"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Número de Serie</label>
                      <input type="text" class="form-control" v-model="equipo.numero_serie">
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <button type="button" class="btn border-rojo btn-sm" @click="agregarEquipoEdicion">
                    <i class="fa fa-plus"></i> Agregar Equipo
                  </button>
                </div>

                <div class="mb-3">
                  <label for="edit_fecha_ingreso" class="form-label">Fecha De Ingreso</label>
                  <input type="date" class="form-control" id="edit_fecha_ingreso" name="fecha_ingreso"
                    v-model="editando.fecha_ingreso" required>
                </div>
              </form>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
            <i class="fa fa-times me-1"></i>Cerrar
          </button>
          <button type="button" id="submitEditar" class="btn bg-rojo text-white" @click="guardarEdicion">
            <i class="fa fa-save me-1"></i>Actualizar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<script>
  window.tabla_ordenes = null;
  $(document).ready(() => {
    const app = new Vue({
      el: "#client",
      data: {
        marcasDisponibles: [],
        modelosDisponibles: [],
        equiposDisponibles: [],
        // Datos para edición
        editando: {
          id: null,
          tipo: null,
          cliente_Rsocial: "",
          cliente_ruc: "",
          atencion_Encargado: "",
          fecha_ingreso: "",
          equipos: [],
        },
      },

      methods: {
        cargarCatalogos() {
          $.get(_URL + "/ajs/get/marcas", (data) => {
            this.marcasDisponibles = JSON.parse(data);
          });
          $.get(_URL + "/ajs/get/modelos", (data) => {
            this.modelosDisponibles = JSON.parse(data);
          });
          $.get(_URL + "/ajs/get/equipos", (data) => {
            this.equiposDisponibles = JSON.parse(data);
          });
        },

        cargarDatosEdicion(id, tipo) {
          $("#loader-menor").show();
          $.ajax({
            url: _URL + "/ajs/taller/detalles-unificado",
            type: "POST",
            data: { id: id, tipo: tipo },
            success: (response) => {
              $("#loader-menor").hide();
              try {
                const datos = typeof response === "object" ? response : JSON.parse(response);

                this.editando.id = datos.id_orden_trabajo || datos.id_orden_servicio;
                this.editando.tipo = tipo;
                this.editando.cliente_Rsocial = datos.cliente_razon_social;
                this.editando.cliente_ruc = datos.cliente_ruc;
                this.editando.atencion_Encargado = datos.atencion_encargado;
                this.editando.fecha_ingreso = datos.fecha_ingreso;
                this.editando.equipos = datos.equipos || [];

                $("#modalEditar").modal("show");
              } catch (error) {
                console.error("Error al procesar los datos:", error);
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Error al cargar los datos para edición",
                });
              }
            },
            error: (xhr, status, error) => {
              $("#loader-menor").hide();
              console.error("Error en la petición:", error);
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al obtener los datos del registro",
              });
            },
          });
        },

        guardarEdicion() {
          if (!this.editando.cliente_Rsocial || !this.editando.atencion_Encargado || !this.editando.fecha_ingreso) {
            Swal.fire({
              icon: "warning",
              title: "Advertencia",
              text: "Por favor complete todos los campos requeridos",
            });
            return;
          }

          const equiposValidos = this.editando.equipos.every((equipo) =>
            equipo.marca && equipo.equipo && equipo.modelo && equipo.numero_serie
          );

          if (!equiposValidos) {
            Swal.fire({
              icon: "warning",
              title: "Advertencia",
              text: "Por favor complete todos los datos de los equipos",
            });
            return;
          }

          const endpoint = this.editando.tipo === 'ORD TRABAJO' ?
            "/ajs/orden-trabajo/update" : "/ajs/orden-servicio/update";

          const idField = this.editando.tipo === 'ORD TRABAJO' ?
            'id_orden_trabajo' : 'id_orden_servicio';

          const data = {
            [idField]: this.editando.id,
            cliente_razon_social: this.editando.cliente_Rsocial,
            cliente_ruc: this.editando.cliente_ruc,
            atencion_encargado: this.editando.atencion_Encargado,
            fecha_ingreso: this.editando.fecha_ingreso,
            equipos: this.editando.equipos,
          };

          $("#loader-menor").show();

          $.ajax({
            type: "POST",
            url: _URL + endpoint,
            data: data,
            success: (resp) => {
              $("#loader-menor").hide();
              try {
                const response = typeof resp === "object" ? resp : JSON.parse(resp);
                if (response && response.success) {
                  window.tabla_ordenes.ajax.reload(null, false);
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
                  text: error.message || "Error al procesar la respuesta del servidor",
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
      },

      mounted() {
        this.cargarCatalogos();
      },
    });

    // Variable para almacenar la referencia del filtro
    let tallerFilterFunction = null;
    try {
      // DataTables initialization para vista unificada
      window.tabla_ordenes = $("#tabla_ordenes").DataTable({
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
            url: _URL + "/ajs/taller/render-unificado",
            type: "POST",
            dataType: "json",
            data: function (d) {
                d.filtro_origen = $('#filtroOrigen').val();
                return d;
            },
            dataSrc: function(json) {
                if (json && json.data) {
                    return json.data;
                } else {
                    console.error("La respuesta del servidor no tiene el formato esperado.", json);
                    return [];
                }
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).attr('data-origen', data.origen);
        },
        language: {
          url: "ServerSide/Spanish.json",
        },
        columns: [
          {
            data: null,
            class: "text-center",
            render: (data, type, row, meta) => {
              const numero = meta.row + 1;
              // Verificar si este registro tiene notificación no leída
              const tieneNotificacion = window.registrosConNotificacion &&
                window.registrosConNotificacion.includes(row.id_original);

              if (tieneNotificacion) {
                return `<span class="registro-con-notificacion">
                  <span class="punto-verde">🟢</span> ${numero}
                </span>`;
              }
              return numero;
            },
          },
          { data: "cliente_razon_social", class: "text-center" },
          { data: "cliente_ruc", class: "text-center" },
          { data: "atencion_encargado", class: "text-center" },
          { data: "fecha_ingreso", class: "text-center" },
          {
            data: "origen",
            class: "text-center",
            render: function (data, type, row) {
              if (data === "ORD TRABAJO") {
                return '<span class="badge bg-warning badge-origen">ORD TRABAJO</span>';
              } else if (data === "ORD SERVICIO") {
                return '<span class="badge bg-success badge-origen">ORD SERVICIO</span>';
              } else {
                return data;
              }
            },
          },
          {
            data: null,
            class: "text-center",
            render(data, type, row) {
              if (row.estado === 'CULMINADO') {
                return `<button class="btn btn-secondary btn-sm" disabled title="Trabajo culminado">
              <i class="fa fa-align-justify"></i>
            </button>`;
              } else {
                return `<a href="/taller/cotizaciones?id=${row.id_original}&tipo=${row.origen}" 
              class="btn btn-success btn-sm button-link">
              <i class="fa fa-align-justify"></i>
            </a>`;
              }
            },
          },
          {
            data: null,
            class: "text-center",
            render: function (data, type, row) {
              let botonesHTML = `<div class="d-flex justify-content-center">`;

              if (row.estado !== 'CULMINADO') {
                botonesHTML += `
            <button type="button" class="btn btn-info btn-sm btnCulminar me-1" 
                    data-id="${row.id_original}" data-tipo="${row.origen}" title="Culminar">
                <i class="fas fa-check-circle"></i>
            </button>`;
              } else {
                botonesHTML += `
            <button class="btn btn-success btn-sm me-1" disabled title="Trabajo Culminado">
                <i class="fas fa-check-circle"></i>
            </button>`;
              }

              botonesHTML += `
            <div class="action-menu">
                <button type="button" class="action-button">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="dropdown-actions">
                    <a class="btn-ver-detalles" href="javascript:void(0)"
                       data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fa fa-eye text-info"></i> Ver detalles
                    </a>
                    <a class="btnEditar" href="javascript:void(0)"
                       data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fa fa-edit text-warning"></i> Editar
                    </a>`;

              <?php if ($puedeEliminar): ?>
                botonesHTML += `
                    <a class="btnBorrar" href="javascript:void(0)"
                       data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fa fa-trash text-danger"></i> Eliminar
                    </a>`;
              <?php endif; ?>

              botonesHTML += `
                    <div class="divider"></div>
                    <a class="btn-reporte-pdf" href="javascript:void(0)"
                       data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fas fa-file-pdf text-danger"></i> Reporte PDF
                    </a>
                    <a class="btn-reporte-excel" href="javascript:void(0)"
                       data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fas fa-file-excel text-success"></i> Reporte Excel
                    </a>
                </div>
            </div>
          </div>`;

              return botonesHTML;
            },
          },
        ],
        drawCallback: function () {
          $('[data-bs-toggle="tooltip"]').tooltip();
        }
      });

      // LÓGICA DE FILTRADO ROBUSTA
      $('#filtroOrigen').on('change', function () {
          const filtro = $(this).val();
          console.log("Filtrando tabla por:", filtro);

          // Obtener la instancia de la tabla directamente desde el elemento del DOM.
          // Esto evita el error si la variable global `tabla_ordenes` es nula.
          var table = $('#tabla_ordenes').DataTable();

          // Verificar que la instancia existe antes de usarla
          if ($.fn.DataTable.isDataTable('#tabla_ordenes')) {
              table.column(5).search(filtro).draw();
          } else {
              console.error("El manejador de eventos del filtro no pudo encontrar la instancia de DataTable.");
          }
      });

    } catch (error) {
      console.error('Error al inicializar DataTable:', error);
    }


    // Event handlers
    function mostrarDetalles(id, tipo) {
      $.ajax({
        url: _URL + "/ajs/taller/detalles-unificado",
        type: "POST",
        data: { id: id, tipo: tipo },
        success: function (response) {
          try {
            var detalles = typeof response === "object" ? response : JSON.parse(response);

            var contenidoModal = `
            <div class="card border-danger mb-2">
                <div class="card-header bg-secondary p-2">Información de ${tipo}</div>
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
                            <tbody>`;

            detalles.equipos.forEach(function (equipo, index) {
              contenidoModal += `
              <tr>
                  <td>${index + 1}</td>
                  <td>${equipo.marca}</td>
                  <td>${equipo.modelo}</td>
                  <td>${equipo.equipo}</td>
                  <td>${equipo.numero_serie}</td>
              </tr>`;
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
                    <p class="mb-0">${detalles.observaciones || "Sin observaciones"}</p>
                </div>
            </div>`;

            $("#modalDetalles .modal-body").html(contenidoModal);
            $("#modalDetalles").modal("show");
          } catch (error) {
            console.error("Error al procesar la respuesta:", error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al procesar los detalles",
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los detalles. " + error,
          });
        },
      });
    }

    // Event listeners
    $(document).on("click", ".btn-ver-detalles", function () {
      var id = $(this).data("id");
      var tipo = $(this).data("tipo");
      mostrarDetalles(id, tipo);
    });

    $(document).on("click", ".btnEditar", function () {
      var id = $(this).data("id");
      var tipo = $(this).data("tipo");
      app.cargarDatosEdicion(id, tipo);
    });

    $(document).on("click", ".btnBorrar", function () {
      var id = $(this).data("id");
      var tipo = $(this).data("tipo");

      <?php if (!$puedeEliminar): ?>
        Swal.fire({
          title: "Acceso denegado",
          text: "No tiene permisos para eliminar registros",
          icon: "error",
        });
        return false;
      <?php else: ?>
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
              url: _URL + "/ajs/taller/delete-unificado",
              type: "post",
              data: { id: id, tipo: tipo },
              success: function (resp) {
                const response = typeof resp === 'string' ? JSON.parse(resp) : resp;

                if (response.error) {
                  Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message || "No se pudo eliminar el registro",
                  });
                  return;
                }

                window.tabla_ordenes.ajax.reload(null, false);
                Swal.fire("¡Buen trabajo!", "Registro Borrado Exitosamente", "success");
              },
              error: function (xhr, status, error) {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Ocurrió un error al intentar eliminar el registro",
                });
              }
            });
          }
        });
      <?php endif; ?>
    });

    $(document).on("click", ".btnCulminar", function () {
      const id = $(this).data("id");
      const tipo = $(this).data("tipo");

      Swal.fire({
        title: "¿Confirmar culminación del trabajo?",
        text: "Esta acción marcará el trabajo como completado",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, culminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: _URL + "/ajs/taller/culminar-unificado",
            type: "POST",
            data: { id: id, tipo: tipo },
            success: function (response) {
              const data = JSON.parse(response);
              if (data.success) {
                Swal.fire("¡Completado!", "El trabajo ha sido marcado como culminado", "success");
                window.tabla_ordenes.ajax.reload(null, false);
              } else {
                Swal.fire("Error", data.error || "No se pudo actualizar el estado del trabajo", "error");
              }
            },
            error: function () {
              Swal.fire("Error", "Hubo un problema al comunicarse con el servidor", "error");
            }
          });
        }
      });
    });

    // Manejo del menú de acciones personalizado
    $(document).on("click", (e) => {
      if (!$(e.target).closest(".action-menu").length) {
        $(".action-menu").removeClass("show");
      }
    });

    $(document).on("click", ".action-button", function (e) {
      e.stopPropagation();
      const button = $(this);
      const menu = button.closest(".action-menu");
      const dropdown = menu.find(".dropdown-actions");

      // Cerrar otros menús
      $(".action-menu").not(menu).removeClass("show");

      if (menu.hasClass("show")) {
        menu.removeClass("show");
      } else {
        // Calcular posición para position: fixed
        const buttonOffset = button.offset();
        const buttonHeight = button.outerHeight();
        const buttonWidth = button.outerWidth();
        const dropdownWidth = 180; // min-width definido en CSS

        // Posición por defecto (debajo del botón, alineado a la derecha)
        let top = buttonOffset.top + buttonHeight + 5;
        let left = buttonOffset.left + buttonWidth - dropdownWidth;

        // Verificar si se sale por la derecha de la pantalla
        if (left < 10) {
          left = buttonOffset.left; // Alinear a la izquierda del botón
        }

        // Verificar si se sale por abajo de la pantalla
        const dropdownHeight = 250; // altura estimada
        if (top + dropdownHeight > $(window).height()) {
          top = buttonOffset.top - dropdownHeight - 5; // Mostrar arriba del botón
        }

        // Aplicar posición
        dropdown.css({
          'top': top + 'px',
          'left': left + 'px'
        });

        menu.addClass("show");
      }
    });

    $(document).on("click", ".dropdown-actions a", function () {
      $(this).closest(".action-menu").removeClass("show");
    });

    // Manejadores para reportes
    $(document).on('click', '.btn-reporte-pdf', function () {
      const id = $(this).data('id');
      const tipo = $(this).data('tipo');

      $.ajax({
        url: _URL + "/ajs/taller/verificar-cotizacion",
        type: "POST",
        data: { id: id, tipo: tipo },
        success: function (response) {
          try {
            const data = typeof response === 'string' ? JSON.parse(response) : response;

            if (data.success && data.id_cotizacion) {
              window.open(_URL + '/r/taller/inventario/' + data.id_cotizacion, '_blank');
            } else {
              Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'No se encontró una cotización asociada a esta orden. Primero debe crear una cotización.'
              });
            }
          } catch (error) {
            console.error("Error al procesar la respuesta:", error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al procesar la respuesta del servidor'
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al verificar la cotización'
          });
        }
      });
    });

    $(document).on('click', '.btn-reporte-excel', function () {
      const id = $(this).data('id');
      const tipo = $(this).data('tipo');

      $.ajax({
        url: _URL + "/ajs/taller/verificar-cotizacion",
        type: "POST",
        data: { id: id, tipo: tipo },
        success: function (response) {
          try {
            const data = typeof response === 'string' ? JSON.parse(response) : response;

            if (data.success && data.id_cotizacion) {
              window.open(_URL + '/r/taller/inventario/excel/' + data.id_cotizacion, '_blank');
            } else {
              Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'No se encontró una cotización asociada a esta orden. Primero debe crear una cotización.'
              });
            }
          } catch (error) {
            console.error("Error al procesar la respuesta:", error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al procesar la respuesta del servidor'
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al verificar la cotización'
          });
        }
      });
    });

    // Funciones auxiliares
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

    // Cargar selects iniciales
    Promise.all([
      cargarSelect("tecnicos", "#atencion_Encargado"),
      cargarSelect("tecnicos", "#edit_atencion_encargado"),
    ]).catch((error) => {
      console.error("Error al cargar los selects:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudieron cargar algunos datos. Por favor, recarga la página.",
      });
    });

    // Funciones auxiliares
    function alertAdvertencia(mensaje) {
      Swal.fire({
        icon: "warning",
        title: "Advertencia",
        text: mensaje
      });
    }

    window.limpiarFiltroTaller = function () {
      console.log('Limpiando filtro de taller...');

      // Remover event listeners específicos
      $('#filtroOrigen').off('change.tallerFilter');

      // Buscar y remover el filtro específico del taller
      if (tallerFilterFunction && $.fn.dataTable.ext.search.length > 0) {
        const index = $.fn.dataTable.ext.search.indexOf(tallerFilterFunction);
        if (index > -1) {
          $.fn.dataTable.ext.search.splice(index, 1);
          console.log('Filtro de taller removido exitosamente');
        }
      }

      // Destruir la tabla si existe
      if (window.tabla_ordenes && $.fn.DataTable.isDataTable('#tabla_ordenes')) {
        try {
          window.tabla_ordenes.destroy();
          window.tabla_ordenes = null;
          console.log('DataTable destruida correctamente');
        } catch (error) {
          console.error('Error al destruir DataTable:', error);
        }
      }

      // Limpiar la variable de función de filtro
      tallerFilterFunction = null;
    };


    // Ejecutar limpieza cuando se sale del módulo
    $(window).on('beforeunload', function () {
      if (typeof window.limpiarFiltroTaller === 'function') {
        window.limpiarFiltroTaller();
      }
    });
  });
</script>

<!-- CAMPANA FLOTANTE DE NOTIFICACIONES - COMENTADA TEMPORALMENTE -->

<div class="notification-bell-floating" id="notificationBell" style="
  position: fixed;
  top: 80px;
  right: 20px;
  z-index: 9999;
  cursor: pointer;
  background: white;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border: 3px solid #C1272D;
  transition: all 0.3s ease;
">
  <i class="fa fa-bell" style="
    color: #C1272D;
    font-size: 24px;
    pointer-events: none;
  "></i>
  <span class="notification-badge" id="notificationBadge" style="
    display: none;
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    min-width: 22px;
    height: 22px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    line-height: 1;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  ">0</span>
</div>

DROPDOWN DE NOTIFICACIONES FLOTANTE
<div class="notification-dropdown-floating" id="notificationDropdown" style="
  position: fixed;
  top: 150px;
  right: 20px;
  background: white;
  border: 1px solid #e3e6f0;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  width: 400px;
  max-height: 500px;
  z-index: 9998;
  display: none;
  overflow: hidden;
">
  <div class="notification-header" style="
    padding: 20px;
    border-bottom: 1px solid #e3e6f0;
    background: linear-gradient(135deg, #C1272D, #e63946);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  ">
    <h6 class="mb-0" style="color: white; font-weight: 600;">
      <i class="fa fa-bell me-2"></i>Notificaciones
    </h6>
    <button class="btn btn-sm text-white" id="markAllRead" style="
      font-size: 12px;
      opacity: 0.8;
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 4px;
      padding: 4px 8px;
    ">
      <i class="fa fa-check-double me-1"></i>Marcar todas como leídas
    </button>
  </div>
  <div id="notificationList" class="notification-list" style="
    max-height: 350px;
    overflow-y: auto;
  ">
    <div style="padding: 30px; text-align: center; color: #6c757d;">
      <i class="fa fa-bell-slash fa-2x mb-3" style="opacity: 0.5;"></i>
      <p class="mb-0">No hay notificaciones</p>
    </div>
  </div>
  <div class="notification-footer" style="
    padding: 15px 20px;
    background: #f8f9fc;
    border-top: 1px solid #e3e6f0;
    text-align: center;
  ">
    <small class="text-muted">
      <i class="fa fa-clock me-1"></i>Actualizándose en tiempo real
    </small>
  </div>
</div>


<!-- Script de Notificaciones - Solo para esta página -->
<script src="<?= URL::to('public/js/notificaciones.js') ?>?v=<?= time() ?>"></script>

<!-- Script de Notificaciones Flotantes - COMENTADO TEMPORALMENTE -->

<script>
console.log('=== NOTIFICACIONES FLOTANTES INICIANDO ===');

let isDropdownOpen = false;

// Array global para trackear registros con notificaciones no leídas
window.registrosConNotificacion = [];

// Función para actualizar registros con notificaciones
function actualizarRegistrosConNotificacion(notifications) {
    // Filtrar notificaciones de taller no leídas
    const notificacionesTaller = notifications.filter(n =>
        n.modulo_origen === 'taller' &&
        (n.tipo === 'orden_trabajo' || n.tipo === 'orden_servicio') &&
        n.leida == 0
    );

    // Extraer IDs de los registros
    window.registrosConNotificacion = notificacionesTaller.map(n => n.registro_id);

    console.log('📍 Registros con notificación:', window.registrosConNotificacion);
}

// Función para recargar tabla de forma robusta SIN REFRESCAR PÁGINA
function recargarTablaTaller(forcePageReload = false) {
    console.log('🔄 Intentando recargar tabla de taller...');

    // Método 1: Variable global
    if (typeof window.tabla_ordenes !== 'undefined' && window.tabla_ordenes) {
        console.log('✅ Método 1: Usando variable global');
        window.tabla_ordenes.ajax.reload(null, false);
        return true;
    }

    // Método 2: Instancia desde DOM
    if ($.fn.DataTable.isDataTable('#tabla_ordenes')) {
        console.log('✅ Método 2: Usando instancia desde DOM');
        $('#tabla_ordenes').DataTable().ajax.reload(null, false);
        return true;
    }

    // Método 3: NUNCA REFRESCAR PÁGINA - Solo mostrar mensaje informativo
    console.warn('⚠️ No se pudo recargar la tabla automáticamente');
    console.log('💡 Sugerencia: La tabla se actualizará cuando se recargue manualmente');

    // REMOVIDO: Ya no refresca la página automáticamente
    return false;
}

// Función para cargar notificaciones directamente
function loadNotificationsNow() {
    console.log('🔄 Cargando notificaciones...');

    if (typeof _URL === 'undefined') {
        console.error('❌ _URL no está definido');
        return;
    }

    const notificationList = document.getElementById('notificationList');
    const badge = document.getElementById('notificationBadge');

    // Mostrar loading
    if (notificationList) {
        notificationList.innerHTML = `
            <div style="padding: 30px; text-align: center; color: #6c757d;">
                <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                <p class="mb-0">Cargando notificaciones...</p>
            </div>
        `;
    }

    fetch(_URL + '/ajs/notificaciones/no-leidas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📡 Respuesta:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 Datos recibidos:', data);

        if (Array.isArray(data)) {
            updateNotificationsUI(data);
        } else if (data.error) {
            console.error('Error del servidor:', data.error);
            showErrorInDropdown(data.error);
        } else {
            console.log('Formato de datos inesperado:', data);
            showErrorInDropdown('Formato de datos inesperado');
        }
    })
    .catch(error => {
        console.error('❌ Error cargando notificaciones:', error);
        showErrorInDropdown('Error de conexión: ' + error.message);
    });
}

// Función para actualizar la UI con notificaciones
function updateNotificationsUI(notifications) {
    const notificationList = document.getElementById('notificationList');
    const badge = document.getElementById('notificationBadge');

    console.log('🔔 Actualizando UI con', notifications.length, 'notificaciones');

    // Actualizar registros con notificaciones para mostrar puntos verdes
    actualizarRegistrosConNotificacion(notifications);

    // Verificar si hay nuevas notificaciones de taller y recargar tabla
    const tallerNotifications = notifications.filter(n =>
        n.modulo_origen === 'taller' &&
        (n.tipo === 'orden_trabajo' || n.tipo === 'orden_servicio')
    );

    if (tallerNotifications.length > 0) {
        console.log('🔄 Nueva notificación de taller detectada, recargando tabla...');
        // NO forzar recarga de página en polling automático
        recargarTablaTaller(false);
    }

    // Actualizar badge
    if (badge) {
        if (notifications.length > 0) {
            badge.textContent = notifications.length > 99 ? '99+' : notifications.length;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    // Actualizar lista
    if (notificationList) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div style="padding: 30px; text-align: center; color: #6c757d;">
                    <i class="fa fa-bell-slash fa-2x mb-3" style="opacity: 0.5;"></i>
                    <p class="mb-0">No hay notificaciones</p>
                </div>
            `;
        } else {
            const notificationsHTML = notifications.map(notification => `
                <div class="notification-item" style="
                    padding: 15px 20px;
                    border-bottom: 1px solid #e3e6f0;
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                    background-color: #e3f2fd;
                    border-left: 4px solid #C1272D;
                " onmouseover="this.style.backgroundColor='#f8f9fc'"
                   onmouseout="this.style.backgroundColor='#e3f2fd'"
                   onclick="markNotificationAsRead(${notification.id_notificacion})">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div style="
                            flex-shrink: 0;
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            background: #f8f9fc;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border: 2px solid #e3e6f0;
                        ">
                            <i class="fa fa-${getIconByType(notification.tipo)} text-primary"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="
                                font-size: 14px;
                                line-height: 1.4;
                                color: #5a5c69;
                                margin-bottom: 5px;
                                word-wrap: break-word;
                            ">${notification.mensaje}</div>
                            <div style="display: flex; gap: 10px; font-size: 12px;">
                                <small class="text-muted">
                                    <i class="fa fa-clock me-1"></i>${timeAgo(notification.created_at)}
                                </small>
                                <small class="text-muted">
                                    <i class="fa fa-tag me-1"></i>${notification.modulo_origen}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            notificationList.innerHTML = notificationsHTML;
        }
    }
}

// Función para mostrar errores
function showErrorInDropdown(errorMessage) {
    const notificationList = document.getElementById('notificationList');
    if (notificationList) {
        notificationList.innerHTML = `
            <div style="padding: 30px; text-align: center; color: #dc3545;">
                <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
                <p class="mb-0">${errorMessage}</p>
                <button onclick="loadNotificationsNow()" class="btn btn-sm btn-outline-danger mt-2">
                    <i class="fa fa-refresh me-1"></i>Reintentar
                </button>
            </div>
        `;
    }
}

// Función para marcar como leída
function markNotificationAsRead(id) {
    console.log('✅ Marcando notificación', id, 'como leída');

    fetch(_URL + '/ajs/notificaciones/marcar-leida', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_notificacion=${id}`,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Notificación marcada como leída');
            loadNotificationsNow(); // Recargar lista y actualizar puntos verdes

            // Recargar tabla para actualizar puntos verdes
            setTimeout(() => {
                recargarTablaTaller(false);
            }, 500);
        } else {
            console.error('❌ Error marcando como leída:', data);
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
    });
}

// Funciones auxiliares
function getIconByType(type) {
    const icons = {
        'orden_trabajo': 'wrench',
        'orden_servicio': 'briefcase',
        'cotizacion': 'file-invoice',
        'venta': 'shopping-cart'
    };
    return icons[type] || 'bell';
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Ahora';
    if (diffMins < 60) return `${diffMins}m`;
    if (diffHours < 24) return `${diffHours}h`;
    if (diffDays < 30) return `${diffDays}d`;
    return date.toLocaleDateString();
}

setTimeout(function() {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');

    console.log('Bell encontrado:', !!bell);
    console.log('Dropdown encontrado:', !!dropdown);

    if (bell && dropdown) {
        // Click en la campana
        bell.onclick = function(e) {
            console.log('🔔 CLICK EN CAMPANA FLOTANTE!');
            e.stopPropagation();

            isDropdownOpen = !isDropdownOpen;

            if (isDropdownOpen) {
                dropdown.style.display = 'block';
                dropdown.style.animation = 'slideIn 0.3s ease';
                console.log('Dropdown ABIERTO');

                // Efecto hover en la campana
                bell.style.transform = 'scale(1.1)';
                bell.style.boxShadow = '0 6px 20px rgba(193, 39, 45, 0.3)';

                // Cargar notificaciones DIRECTAMENTE con fetch
                loadNotificationsNow();

                // NUEVO: Recargar tabla de órdenes al abrir notificaciones
                console.log('🔄 Recargando tabla de órdenes al abrir notificaciones...');
                // NUNCA refrescar página, solo recargar tabla
                recargarTablaTaller(false);
            } else {
                dropdown.style.display = 'none';
                bell.style.transform = 'scale(1)';
                bell.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                console.log('Dropdown CERRADO');
            }
        };

        // Click fuera para cerrar
        document.addEventListener('click', function(e) {
            if (isDropdownOpen && !bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
                bell.style.transform = 'scale(1)';
                bell.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                isDropdownOpen = false;
                console.log('Dropdown cerrado por click fuera');
            }
        });

        // Botón marcar todas como leídas
        const markAllBtn = document.getElementById('markAllRead');
        if (markAllBtn) {
            markAllBtn.onclick = function(e) {
                e.stopPropagation();
                console.log('📋 Marcando todas como leídas...');

                fetch(_URL + '/ajs/notificaciones/marcar-todas-leidas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('✅ Todas las notificaciones marcadas como leídas');
                        loadNotificationsNow(); // Recargar lista

                        // Recargar tabla para quitar todos los puntos verdes
                        setTimeout(() => {
                            recargarTablaTaller(false);
                        }, 500);
                    } else {
                        console.error('❌ Error:', data);
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                });
            };
        }

        // Efecto hover en la campana
        bell.addEventListener('mouseenter', function() {
            if (!isDropdownOpen) {
                bell.style.transform = 'scale(1.05)';
                bell.style.boxShadow = '0 6px 16px rgba(193, 39, 45, 0.2)';
            }
        });

        bell.addEventListener('mouseleave', function() {
            if (!isDropdownOpen) {
                bell.style.transform = 'scale(1)';
                bell.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            }
        });

        console.log('✅ Campana flotante configurada correctamente');

        // Cargar notificaciones automáticamente al iniciar
        setTimeout(function() {
            console.log('🚀 Carga inicial de notificaciones...');
            loadNotificationsNow();
        }, 1000);

        // Polling para actualizar cada 30 segundos
        setInterval(function() {
            if (!isDropdownOpen) { // Solo actualizar si el dropdown está cerrado
                console.log('🔄 Actualización automática de notificaciones...');
                loadNotificationsNow();
            }
        }, 10000);
    }

    // Verificar _URL
    if (typeof _URL !== 'undefined') {
        console.log('_URL disponible:', _URL);
    } else {
        console.log('❌ _URL NO está definido');
    }

}, 500);

// CSS para animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notification-bell-floating:hover {
        border-color: #a11f24 !important;
    }

    .notification-dropdown-floating {
        animation: slideIn 0.3s ease;
    }

    /* Estilos para punto verde en tabla */
    .punto-verde {
        font-size: 12px;
        margin-right: 5px;
        animation: pulse-green 2s infinite;
    }

    .registro-con-notificacion {
        font-weight: bold;
        color: #155724;
    }

    @keyframes pulse-green {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
<script src="<?= URL::to('public/js/taller-cotizaciones/taller-reportes.js') ?>?v=<?= time() ?>"></script>