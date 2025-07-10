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
</style>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">REGISTRO DE ORDEN DE TRABAJO Y SERVICIO</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div id="conte-vue-modals">
            <div class="card-body">
              <!-- Botón para abrir el modal de reportes -->
              <div class="mb-3 d-flex justify-content-end">
    <button type="button" class="btn bg-rojo text-white" data-bs-toggle="modal" data-bs-target="#modalReportesInventario">
        <i class="fas fa-download"></i> Descargar Reportes de Inventario
    </button>
  </div>

<!-- Modal de Reportes de Inventario -->
<div class="modal fade" id="modalReportesInventario" tabindex="-1" aria-labelledby="modalReportesInventarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalReportesInventarioLabel">Reportes de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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

                <div class="card-title-desc">
                    <div class="table-responsive">
                        <div class="mb-3 d-flex align-items-center" style="width: fit-content;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Filtrar</span>
                                <i class="fas fa-filter text-muted"></i>
                                <select id="filtroOrigen" class="form-select form-select-sm"
                                    style="width: auto; min-width: 150px;">
                                    <option value="">Seleccionar</option>
                                    <option value="ORD TRABAJO">Orden de Trabajo</option>
                                    <option value="ORD SERVICIO">Orden de Servicio</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm">
                        <table id="tabla_ordenes"
                            class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
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

        <!-- Modal para mostrar detalles -->
        <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalDetallesLabel">Detalles De La Orden</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí se cargarán los detalles dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
        <div id="client">

            <!-- Modal Editar -->
            <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="modalEditarLabel">Editar Registro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="frmOrdenesEditar">
                                <input type="hidden" id="edit_id_orden" name="id_orden" v-model="editando.id">
                                <input type="hidden" id="edit_tipo_orden" name="tipo_orden" v-model="editando.tipo">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="edit_cliente_Rsocial" class="form-label">Cliente (RUC o DNI)</label>
                                            <input type="text" class="form-control" id="edit_cliente_Rsocial"
                                                name="cliente_razon_social" v-model="editando.cliente_Rsocial" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_atencion_Encargado" class="form-label">Técnico</label>
                                            <input type="text" class="form-control" id="edit_atencion_Encargado"
                                                name="atencion_encargado" v-model="editando.atencion_Encargado"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de equipos múltiples -->
                                <div v-for="(equipo, index) in editando.equipos" :key="index"
                                    class="border p-3 mb-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Equipo {{index + 1}}</h6>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="eliminarEquipoEdicion(index)" v-if="editando.equipos.length > 1">
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
                        <div class="modal-footer">
                            <button type="button" id="submitEditar" class="btn bg-rojo text-white"
                            @click="guardarEdicion">Actualizar</button>
                            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
                tabla_ordenes.ajax.reload(null, false);
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

  // DataTables initialization para vista unificada
  tabla_ordenes = $("#tabla_ordenes").DataTable({
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
      method: "POST",
      dataSrc: "",
    },
    language: {
      url: "ServerSide/Spanish.json",
    },
    columns: [
      {
        data: null,
        class: "text-center",
        render: (data, type, row, meta) => meta.row + 1,
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
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white btn-sm dropdown-toggle" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-cog"></i> Acciones
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item btn-ver-detalles" href="javascript:void(0)" 
                           data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fa fa-eye text-info"></i> Ver detalles
                    </a></li>
                    <li><a class="dropdown-item btnEditar" href="javascript:void(0)" 
                           data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fa fa-edit text-warning"></i> Editar
                    </a></li>`;
                    
          <?php if ($puedeEliminar): ?>
            botonesHTML += `
                    <li><a class="dropdown-item btnBorrar" href="javascript:void(0)" 
                           data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fa fa-trash text-danger"></i> Eliminar
                    </a></li>`;
          <?php endif; ?>
          
          botonesHTML += `
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Reportes</h6></li>
                    <li><a class="dropdown-item btn-reporte-pdf" href="javascript:void(0)" 
                           data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fas fa-file-pdf text-danger"></i> Reporte PDF
                    </a></li>
                    <li><a class="dropdown-item btn-reporte-excel" href="javascript:void(0)" 
                           data-id="${row.id_original}" data-tipo="${row.origen}">
                        <i class="fas fa-file-excel text-success"></i> Reporte Excel
                    </a></li>
                </ul>
            </div>
          </div>`;
          
          return botonesHTML;
        },
      },
    ],
    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });

  // Filtro por origen
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    var selectedOrigen = $('#filtroOrigen').val();
    var origen = data[5]; // Índice de la columna origen

    if (selectedOrigen === "" || origen === selectedOrigen) {
      return true;
    }
    return false;
  });

  $('#filtroOrigen').change(function () {
    tabla_ordenes.draw();
  });

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

              tabla_ordenes.ajax.reload(null, false);
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
              tabla_ordenes.ajax.reload(null, false);
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
});
</script>
<script src="<?= URL::to('public/js/taller-cotizaciones/taller-reportes.js') ?>?v=<?= time() ?>"></script>