<style>
    .modal-header.bg-danger {
        background-color: #dc3545 !important;
    }

    .correlativo {
        font-size: 0.9rem;
    }

    .correlativo-grande {
        font-size: 1.2rem;
        margin-bottom: 0;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
    }

    .table th {
        font-weight: 600;
    }

    .modal-body.bg-light {
        background-color: #f8f9fa;
    }

    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: white;
    }

    .badge {
        font-size: 0.875em;
        padding: 0.5em 0.75em;
    }

    .badge.bg-warning {
        color: #000 !important;
    }

    .badge i {
        font-size: 0.875em;
    }

    /* ESTILOS UNIFORMES PARA CONTADOR DE DÍAS */
    .contador-dias {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 60px !important;
        width: 60px !important;
        height: 32px !important;
        padding: 0 !important;
        border-radius: 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-align: center !important;
        border: none !important;
        /* box-shadow: none !important; */
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    /* Estados específicos con colores uniformes */
    .contador-dias.vencido {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .contador-dias.urgente {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    .contador-dias.normal {
        background-color: #007bff !important;
        color: white !important;
    }

    .contador-dias.confirmado {
        background-color: #28a745 !important;
        color: white !important;
    }

    .contador-dias.sin-fecha {
        background-color: #6c757d !important;
        color: white !important;
    }

    /* Animación solo para vencidos */
    .contador-dias.vencido {
        animation: pulse-vencido 2s infinite;
    }

    @keyframes pulse-vencido {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    /* Iconos dentro del contador */
    .contador-dias i {
        font-size: 10px !important;
        margin-right: 2px !important;
    }

    /* ESTILOS MEJORADOS PARA AUTOCOMPLETE */
    .ui-autocomplete {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        font-family: inherit;
        width: auto !important;
        min-width: 300px;
        max-width: 500px;
    }

    .ui-menu-item {
        margin: 0;
        padding: 0;
        border: none;
    }

    .ui-menu-item-wrapper {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f4;
        font-size: 14px;
        line-height: 1.4;
        color: #495057;
        transition: all 0.2s ease;
        display: block;
        text-decoration: none;
        word-wrap: break-word;
        white-space: normal;
    }

    .ui-menu-item:hover .ui-menu-item-wrapper,
    .ui-menu-item.ui-state-active .ui-menu-item-wrapper,
    .ui-menu-item.ui-state-focus .ui-menu-item-wrapper {
        background-color: #f8f9fa;
        color: #495057;
        border-left: 3px solid #dc3545;
    }

    .ui-menu-item:last-child .ui-menu-item-wrapper {
        border-bottom: none;
    }

    /* Z-INDEX HIERARCHY CORREGIDO */
    .modal {
        z-index: 1050;
    }

    .modal.modal-stacked {
        z-index: 1055;
    }

    .ui-autocomplete {
        z-index: 1060 !important;
    }

    .swal2-container {
        z-index: 2000 !important;
    }

    .swal-high-zindex {
        z-index: 3000 !important;
    }

    /* Mejorar el input cuando está activo */
    .ui-autocomplete-input:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Estilo para el contenedor del autocomplete */
    .autocomplete-container {
        position: relative;
    }

    /* ESTILOS PARA TABLA DE MOTIVOS SIN FONDOS DE COLOR */
    #tablaMotivos thead th {
        background-color: transparent !important;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    #tablaMotivos tbody tr {
        background-color: transparent !important;
    }

    #tablaMotivos tbody td {
        background-color: transparent !important;
        border-bottom: 1px solid #dee2e6;
    }

    #tablaMotivos tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    /* Estilos para estado en detalles */
    .estado-oficina {
        color: #28a745;
        font-weight: bold;
    }

    .estado-no-oficina {
        color: #dc3545;
        font-weight: bold;
    }
</style>

<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center h1-style-2">REGISTRO DE GESTIÓN DE ACTIVOS</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="clearfix">
            <ol class="breadcrumb m-0 float-start" style="background: transparent;">
                <li class="breadcrumb-item"><a href="javascript: void(0);" style="color: #718096; text-decoration: none;">Orden Servicio</a></li>
                <li class="breadcrumb-item active " aria-current="page" style="font-weight: 500; color: #CA3438;">Gestion activos</li>
            </ol>
        </div>
    </div>
    <div class="col-12">
        <div class="card" style="border-radius:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            
            <!-- Botones alineados a la derecha -->
            <div class="card-title-desc text-end" style="padding: 10px 10px 0 0;">
                <button id="btnAbrirModalRegistro" type="button" class="btn bg-rojo text-white button-link">
                    <i class="fa fa-plus"></i> Añadir Registro 
                </button>
                <a href="/maquina" class="btn border-rojo button-link">
                    <i class="fa fa-plus"></i> Máquina
                </a>
            </div>

            <div id="conte-vue-modals">
                <!-- Modal de Registro de Activos -->
                <div class="modal fade" id="modalRegistroActivo" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg-custom">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fw-bold" id="modalTitulo">Agregar Nuevo Registro de Activo</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="frmClientesAgregar">
                                    <input type="hidden" name="origen" value="Ord Servicio">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="input_datos_cliente_modal" class="form-label">DNI o RUC <span style="color:red">(*)</span></label>
                                                <div class="input-group">
                                                    <input id="input_datos_cliente_modal" v-model="maquinaSerieModal.num_doc" type="text"
                                                        placeholder="Ingrese Documento" class="form-control" maxlength="11">
                                                    <button @click="buscarDocumentSSModal" class="btn bg-rojo" type="button">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                                <p id="input_datos_cliente_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="input_buscar_Dataseries_modal" class="form-label">
                                                    Buscar Serie<span class="text-danger"> (*)</span>
                                                </label>
                                                <div class="autocomplete-container">
                                                    <input id="input_buscar_Dataseries_modal" v-model="maquinaSerieModal.buscar_serie" type="text"
                                                        placeholder="Ingrese Serie" class="form-control" autocomplete="off">
                                                </div>
                                                <p id="input_buscar_Dataseries_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="marca_modal" class="form-label">Marca</label>
                                                <input type="text" v-model="maquinaSerieModal.marc" class="form-control" id="marca_modal" name="marca_modal" readonly>
                                                <p id="marca_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="modelo_modal" class="form-label">Modelo</label>
                                                <input id="modelo_modal" v-model="maquinaSerieModal.model" name="modelo_modal" class="form-control" readonly>
                                                <p id="modelo_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="equipo_modal" class="form-label">Equipo</label>
                                                <input type="text" v-model="maquinaSerieModal.equipo" class="form-control" id="equipo_modal" name="equipo_modal" readonly>
                                                <p id="equipo_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="cliente_razon_social_modal" class="form-label">Cliente </label>
                                                <input v-model="maquinaSerieModal.cliente_Rsocial" type="text" placeholder="Nombre del cliente"
                                                    class="form-control" autocomplete="off" id="cliente_razon_social_modal" name="cliente_razon_social_modal">
                                                <p id="cliente_razon_social_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="numero_serie_modal" class="form-label">Número De Serie</label>
                                                <input type="text" v-model="maquinaSerieModal.num_serie" class="form-control" id="numero_serie_modal" name="numero_serie_modal">
                                                <p id="numero_serie_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="motivo_modal" class="form-label">Motivo</label>
                                                <div class="input-group">
                                                    <select class="form-select" id="motivo_modal" name="motivo_modal"></select>
                                                    <button class="btn bg-rojo text-white" type="button" onclick="abrirModalMotivos()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p id="motivo_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="fecha_salida_modal" class="form-label">Fecha De Salida</label>
                                                <input type="date" class="form-control" id="fecha_salida_modal" name="fecha_salida_modal">
                                                <p id="fecha_salida_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="fecha_ingreso_modal" class="form-label">Fecha De Ingreso</label>
                                                <input type="date" class="form-control" id="fecha_ingreso_modal" name="fecha_ingreso_modal">
                                                <p id="fecha_ingreso_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="observaciones_modal" class="form-label">Observaciones</label>
                                                <textarea class="form-control" id="observaciones_modal" name="observaciones_modal" rows="3"></textarea>
                                                <p id="observaciones_modal-error" class="text-danger mt-1 mb-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" id="submitRegistroModal" class="btn bg-rojo">Guardar Registro</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de Detalles -->
                <div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo">
                                <div>
                                    <h5 class="modal-title fw-bold mb-1">Detalles del ACTIVO</h5>
                                    <div class="correlativo text-white-50" id="correlativo"></div>
                                </div>
                                <div class="ms-auto d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-light btn-sm me-2" id="btnDescargarPDF">
                                        <i class="fas fa-download me-1"></i> Descargar
                                    </button>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            </div>
                            <div class="modal-body bg-light">
                                <!-- Correlativo grande -->
                                <div class="text-center mb-4">
                                    <h4 class="correlativo-grande text-danger fw-bold" id="correlativo-grande"></h4>
                                </div>

                                <!-- Información Principal -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-user-circle text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Cliente</label>
                                                        <div class="fw-bold" id="detalle-cliente"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-info-circle text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Motivo</label>
                                                        <div class="fw-bold" id="detalle-motivo"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-building text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Estado</label>
                                                        <div class="fw-bold" id="detalle-estado"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalles del Equipo -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-danger text-white py-2">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-cogs me-2"></i>Especificaciones del Equipo
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-danger">
                                                    <tr>
                                                        <th>Marca</th>
                                                        <th>Modelo</th>
                                                        <th>Equipo</th>
                                                        <th>Número de Serie</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="detalle-marca"></td>
                                                        <td id="detalle-modelo"></td>
                                                        <td id="detalle-equipo"></td>
                                                        <td id="detalle-serie"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fechas y Observaciones -->
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-calendar-minus text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Fecha de Salida</label>
                                                        <div class="fw-bold" id="detalle-salida"></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-plus text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Fecha de Ingreso</label>
                                                        <div class="fw-bold" id="detalle-ingreso"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-comment-alt text-danger me-2 fa-lg mt-1"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Observaciones</label>
                                                        <div class="fw-bold" id="detalle-observaciones"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para gestionar motivos -->
                <div class="modal fade modal-stacked" id="modalMotivo" tabindex="-1" aria-labelledby="modalMotivoLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="modalMotivoLabel">
                                    <i class="fa fa-info-circle me-1"></i> Gestión de Motivos
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="motivo_nombre" placeholder="Nombre del motivo">
                                        <button type="button" class="btn bg-rojo" id="btnAgregarMotivo">
                                            <i class="fa fa-save"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                                <div class="table-scroll" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-striped" id="tablaMotivos">
                                        <thead>
                                            <tr>
                                                <th><i class="fa fa-info-circle me-1"></i> Nombre</th>
                                                <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table table-bordered dt-responsive nowrap text-center table-sm">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag me-1"></i> Número</th>
                                        <th>Cliente/Razón Social</th>
                                        <th>Motivo</th>
                                        <th>Fecha De Salida</th>
                                        <th>Fecha De Ingreso</th>
                                        <th><i class="fa fa-clock me-1"></i> Días V.</th>
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
</div>

<script>
    // Variables globales para gestión de modales
    let modoEdicion = false;
    let activoEditandoId = null;

    // Funciones para gestionar modales superpuestos
    function abrirModalRegistro() {
        $("#modalMotivo").modal('hide');
        setTimeout(() => {
            $("#modalRegistroActivo").modal('show');
        }, 300);
    }

    function abrirModalMotivos() {
        $("#modalRegistroActivo").modal('hide');
        setTimeout(() => {
            $("#modalMotivo").modal('show');
        }, 300);
    }

    // FUNCIÓN PARA GENERAR EL CONTADOR DE DÍAS UNIFORME
    function generarContadorDias(row) {
        const fechaIngreso = row.fecha_ingreso ? new Date(row.fecha_ingreso) : null;
        const fechaActual = new Date();
        
        // Si está confirmado
        if (row.estado === 'CONFIRMADO') {
            return '<span class="contador-dias confirmado"><i class="fas fa-check"></i> OK</span>';
        }
        
        // Si no hay fecha de ingreso
        if (!fechaIngreso || row.fecha_ingreso === '0000-00-00') {
            return '<span class="contador-dias sin-fecha">N/A</span>';
        }
        
        // Calcular días restantes
        const diasRestantes = Math.ceil((fechaIngreso - fechaActual) / (1000 * 60 * 60 * 24));
        
        if (diasRestantes < 0) {
            // Vencido
            return `<span class="contador-dias vencido"><i class="fas fa-exclamation-triangle"></i> ${diasRestantes}</span>`;
        } else if (diasRestantes <= 3) {
            // Urgente (3 días o menos)
            return `<span class="contador-dias urgente"><i class="fas fa-clock"></i> ${diasRestantes}</span>`;
        } else {
            // Normal (más de 3 días)
            return `<span class="contador-dias normal">${diasRestantes}</span>`;
        }
    }

    $(document).ready(function () {
        // Instancia de Vue para el modal de registro
        const appModal = new Vue({
            el: "#modalRegistroActivo",
            data: {
                maquinaSerieModal: {
                    cliente_Rsocial: "",
                    buscar_serie: '',
                    num_serie: '',
                    marc: '',
                    model: '',
                    equipo: '',
                    num_doc: "",
                    fecha_salida: new Date().toISOString().split('T')[0]
                }
            },
            mounted() {
                // Configurar fecha por defecto
                document.getElementById('fecha_salida_modal').value = this.maquinaSerieModal.fecha_salida;
                
                // Agregar eventos para ocultar mensajes de error al escribir
                $('#modalRegistroActivo input, #modalRegistroActivo select, #modalRegistroActivo textarea').on('input change', function() {
                    const id = $(this).attr('id');
                    if (id) {
                        $(`#${id}-error`).text('');
                        $(this).removeClass('is-invalid');
                    }
                });

                // INICIALIZAR AUTOCOMPLETE DESPUÉS DE QUE EL MODAL ESTÉ MONTADO
                this.$nextTick(() => {
                    this.initializeAutocomplete();
                });
            },
            methods: {
                buscarDocumentSSModal() {
                    const docLength = this.maquinaSerieModal.num_doc.length;
                    if (docLength === 8 || docLength === 11) {
                        $("#loader-menor").show();
                        this.maquinaSerieModal.dir_pos = 1;

                        _ajax("/ajs/prealerta/doc/cliente", "POST", {
                            doc: this.maquinaSerieModal.num_doc
                        }, (resp) => {
                            $("#loader-menor").hide();
                            console.log(resp);

                            if (docLength === 8) {
                                if (resp.success) {
                                    this.maquinaSerieModal.cliente_Rsocial = `${resp.nombres} ${resp.apellidoPaterno || ''} ${resp.apellidoMaterno || ''}`;
                                } else {
                                    alertAdvertencia("Documento no encontrado");
                                }
                            } else if (docLength === 11) {
                                if (resp.razonSocial) {
                                    this.maquinaSerieModal.cliente_Rsocial = resp.razonSocial;
                                } else {
                                    alertAdvertencia("RUC no encontrado");
                                }
                            }
                        });
                    } else {
                        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
                    }
                },
                limpiarFormularioModal() {
                    this.maquinaSerieModal = {
                        cliente_Rsocial: "",
                        buscar_serie: '',
                        num_serie: '',
                        marc: '',
                        model: '',
                        equipo: '',
                        num_doc: "",
                        fecha_salida: new Date().toISOString().split('T')[0]
                    };
                    $('#motivo_modal').val('');
                    $('#fecha_salida_modal').val(this.maquinaSerieModal.fecha_salida);
                    $('#fecha_ingreso_modal').val('');
                    $('#observaciones_modal').val('');
                    // Limpiar errores
                    $('#modalRegistroActivo .text-danger').text('');
                    $('#modalRegistroActivo .is-invalid').removeClass('is-invalid');
                    
                    // Resetear modo edición
                    modoEdicion = false;
                    activoEditandoId = null;
                    $('#modalTitulo').text('Agregar Nuevo Registro de Activo');
                    $('#submitRegistroModal').text('Guardar Registro');
                },
                // MÉTODO MEJORADO PARA INICIALIZAR EL AUTOCOMPLETE
                initializeAutocomplete() {
                    const self = this;
                    
                    // Destruir autocomplete existente si existe
                    if ($("#input_buscar_Dataseries_modal").hasClass("ui-autocomplete-input")) {
                        $("#input_buscar_Dataseries_modal").autocomplete("destroy");
                    }

                    $("#input_buscar_Dataseries_modal").autocomplete({
                        source: function (request, response) {
                            console.log("Autocomplete source called with term:", request.term);
                            $.ajax({
                                url: _URL + "/ajs/buscar/maquina/datos",
                                type: "GET",
                                data: {
                                    term: request.term || '',
                                    startsWith: true
                                },
                                success: function (data) {
                                    console.log("Autocomplete response:", data);
                                    let results = JSON.parse(data);
                                    if (!request.term) {
                                        response(results);
                                    } else {
                                        results = results.filter(item =>
                                            item.label.toString().toLowerCase().startsWith(request.term.toLowerCase())
                                        );
                                        response(results);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Autocomplete error:", error);
                                }
                            });
                        },
                        minLength: 0,
                        appendTo: ".autocomplete-container",
                        position: { 
                            my: "left top", 
                            at: "left bottom",
                            collision: "flip",
                            within: "#modalRegistroActivo"
                        },
                        select: function (event, ui) {
                            event.preventDefault();
                            console.log("Item selected:", ui.item);
                            self.maquinaSerieModal.buscar_serie = '';
                            self.maquinaSerieModal.num_serie = ui.item.value;
                            self.maquinaSerieModal.marc = ui.item.marca;
                            self.maquinaSerieModal.model = ui.item.modelo;
                            self.maquinaSerieModal.equipo = ui.item.equipo;
                            return false;
                        },
                        open: function() {
                            console.log("Autocomplete opened");
                            $('.ui-autocomplete').css({
                                'z-index': 1060,
                                'max-width': '500px',
                                'min-width': '300px'
                            });
                        },
                        close: function() {
                            console.log("Autocomplete closed");
                        }
                    }).on('focus', function() {
                        console.log("Input focused, triggering search");
                        $(this).autocomplete('search', '');
                    });
                }
            }
        });

        // Abrir modal de registro
        $("#btnAbrirModalRegistro").click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            appModal.limpiarFormularioModal();
            cargarMotivosModal();
            $("#modalRegistroActivo").modal('show');
            
            setTimeout(() => {
                appModal.initializeAutocomplete();
            }, 500);
            
            return false;
        });

        // FUNCIÓN CORREGIDA PARA CARGAR MOTIVOS CON COMPARACIÓN CASE-INSENSITIVE
        function cargarMotivosModal() {
            console.log("Cargando motivos...");
            $.get(_URL + "/ajs/get/motivos", function (data) {
                console.log("Respuesta motivos raw:", data);
                
                let options = '<option value="">Seleccione un motivo</option>';
                let resp;
                
                try {
                    if (typeof data === 'string') {
                        resp = JSON.parse(data);
                    } else {
                        resp = data;
                    }
                    
                    console.log("Motivos parseados:", resp);
                    
                    if (resp.status && resp.data) {
                        resp = resp.data;
                    }
                    
                    if (Array.isArray(resp)) {
                        $.each(resp, function (i, v) {
                            if (v && v.nombre) {
                                options += `<option value="${v.nombre}">${v.nombre}</option>`;
                            }
                        });
                    } else {
                        console.error("La respuesta no es un array:", resp);
                    }
                    
                } catch (e) {
                    console.error("Error al parsear motivos:", e);
                    console.error("Data recibida:", data);
                }
                
                $('#motivo_modal').html(options);
                console.log("Options generadas:", options);
                
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar los motivos: " + textStatus, errorThrown);
                console.error("Response:", jqXHR.responseText);
                alert("No se pudo cargar los motivos. Por favor, intenta nuevamente.");
            });
        }

        // FUNCIÓN PARA CARGAR DATOS EN MODO EDICIÓN CON SELECCIÓN CORRECTA DE MOTIVO
        function cargarDatosParaEdicion(datos) {
            console.log("Cargando datos para edición:", datos);
            
            // Cargar datos básicos
            appModal.maquinaSerieModal.cliente_Rsocial = datos.cliente_razon_social;
            appModal.maquinaSerieModal.marc = datos.marca;
            appModal.maquinaSerieModal.model = datos.modelo;
            appModal.maquinaSerieModal.num_serie = datos.numero_serie;
            appModal.maquinaSerieModal.equipo = datos.equipo;
            
            $('#fecha_salida_modal').val(datos.fecha_salida);
            $('#fecha_ingreso_modal').val(datos.fecha_ingreso);
            $('#observaciones_modal').val(datos.observaciones || '');
            
            // CARGAR MOTIVOS Y LUEGO SELECCIONAR EL CORRECTO
            cargarMotivosModal();
            
            // Esperar a que se carguen los motivos y luego seleccionar
            setTimeout(() => {
                console.log("Intentando seleccionar motivo:", datos.motivo);
                
                // Buscar el motivo de forma case-insensitive
                let motivoEncontrado = false;
                $('#motivo_modal option').each(function() {
                    const optionValue = $(this).val();
                    const optionText = $(this).text();
                    
                    console.log("Comparando:", datos.motivo, "con", optionValue, "y", optionText);
                    
                    // Comparación case-insensitive
                    if (optionValue.toLowerCase() === datos.motivo.toLowerCase() || 
                        optionText.toLowerCase() === datos.motivo.toLowerCase()) {
                        $(this).prop('selected', true);
                        $('#motivo_modal').val(optionValue);
                        motivoEncontrado = true;
                        console.log("Motivo seleccionado:", optionValue);
                        return false; // break del each
                    }
                });
                
                if (!motivoEncontrado) {
                    console.warn("No se encontró el motivo:", datos.motivo);
                    console.log("Opciones disponibles:", $('#motivo_modal option').map(function() { 
                        return $(this).val(); 
                    }).get());
                }
                
                // Trigger change event para asegurar que se actualice
                $('#motivo_modal').trigger('change');
            }, 500);
        }

        function mostrarErrorValidacionModal(campo, mensaje) {
            $(`#${campo}-error`).text(mensaje);
            $(`#${campo}`).addClass('is-invalid');
        }

        function limpiarErroresModal() {
            $('#modalRegistroActivo .text-danger').text('');
            $('#modalRegistroActivo .is-invalid').removeClass('is-invalid');
        }

        // Enviar formulario del modal (AGREGAR O EDITAR)
        $("#submitRegistroModal").click(function () {
            limpiarErroresModal();
            $(this).prop('disabled', true);

            const data = {
                cliente_razon_social: appModal.maquinaSerieModal.cliente_Rsocial,
                marca: appModal.maquinaSerieModal.marc,
                modelo: appModal.maquinaSerieModal.model,
                numero_serie: appModal.maquinaSerieModal.num_serie,
                equipo: appModal.maquinaSerieModal.equipo,
                motivo: $('#motivo_modal').val(),
                fecha_salida: $('#fecha_salida_modal').val(),
                fecha_ingreso: $('#fecha_ingreso_modal').val(),
                observaciones: $('#observaciones_modal').val()
            };

            // Si estamos en modo edición, agregar el ID
            if (modoEdicion && activoEditandoId) {
                data.id = activoEditandoId;
            }

            let errores = {};
            if (!data.cliente_razon_social) errores.cliente_razon_social_modal = "El nombre del cliente es requerido";
            if (!data.marca) errores.marca_modal = "La marca es requerida";
            if (!data.modelo) errores.modelo_modal = "El modelo es requerido";
            if (!data.numero_serie) errores.numero_serie_modal = "El número de serie es requerido";
            if (!data.equipo) errores.equipo_modal = "El equipo es requerido";
            if (!data.motivo) errores.motivo_modal = "El motivo es requerido";

            if (Object.keys(errores).length > 0) {
                for (let campo in errores) {
                    mostrarErrorValidacionModal(campo, errores[campo]);
                }
                $(this).prop('disabled', false);
                return;
            }

            // Determinar URL y mensaje según el modo
            const url = modoEdicion ? 
                _URL + "/ajs/gestion/activos/update" : 
                _URL + "/ajs/gestion/activos/add";
            const successMessage = modoEdicion ? "Activo actualizado correctamente" : "Activo agregado correctamente";

            $.ajax({
                url: url,
                type: "POST", 
                data: data,
                success: function (response) {
                    console.log("Full response:", response);
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.res) {
                            Swal.fire({
                                title: "¡Éxito!",
                                text: jsonResponse.msg || successMessage,
                                icon: "success",
                                confirmButtonText: "OK",
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $("#modalRegistroActivo").modal('hide');
                                    tabla_clientes.ajax.reload(null, false);
                                    appModal.limpiarFormularioModal();
                                }
                            });
                        } else {
                            if (jsonResponse.errores) {
                                for (let campo in jsonResponse.errores) {
                                    mostrarErrorValidacionModal(campo + '_modal', jsonResponse.errores[campo]);
                                }
                            } else {
                                Swal.fire({
                                    title: "¡Error!",
                                    text: jsonResponse.msg,
                                    icon: "error",
                                    customClass: {
                                        container: 'swal-high-zindex'
                                    }
                                });
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        Swal.fire({
                            title: "¡Error!",
                            text: "Hubo un problema al procesar la respuesta del servidor.",
                            icon: "error",
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                    Swal.fire({
                        title: "¡Error!",
                        text: "No se pudo procesar la solicitud. Intenta nuevamente.",
                        icon: "error",
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                },
                complete: function () {
                    $("#submitRegistroModal").prop('disabled', false);
                }
            });
        });

        // DataTable para la gestión de activos
        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            "responsive": true,
            "scrollX": false,   
            "autoWidth": false, 
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip', 
            ajax: {
                url: _URL + "/ajs/gestion/activos/render",
                method: "POST",
                dataSrc: ""
            },
            language: {
                url: "../ServerSide/Spanish.json"
            },
            columns: [
                { data: "numero", class: "text-center" },
                { data: "cliente_razon_social", class: "text-center" },
                { data: "motivo", class: "text-center" },
                { data: "fecha_salida", class: "text-center" },
                { data: "fecha_ingreso", class: "text-center" },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return generarContadorDias(row);
                    }
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        let confirmarBtn = '';
                        let estadoIcon = '';

                        if (row.estado === 'CONFIRMADO') {
                            estadoIcon = `<button class="btn btn-sm btn-success" disabled title="Activo en Oficina">
                                <i class="fas fa-check-circle"></i>
                            </button>`;
                        } else {
                            confirmarBtn = `
                                <button data-id="${row.id}" 
                                        class="btn btn-sm btn-info btnConfirmar" 
                                        title="Confirmar llegada a oficina">
                                    <i class="fas fa-check-circle"></i>
                                </button>`;
                        }

                        return `<div class="btn-group btn-group-sm">
                            <button class="btn btn-info btn-ver" data-id="${row.id}" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button data-id="${row.id}" class="btn btn-warning btnEditar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button data-id="${row.id}" class="btn btn-danger btnBorrar" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            ${confirmarBtn}
                            ${estadoIcon}
                        </div>`;
                    }
                }
            ]
        });

        // Resto del código JavaScript permanece igual...
        // [Incluir todo el resto del código JavaScript existente]
        
        // Manejador para el botón ver detalles
        $("#tabla_clientes").on("click", ".btn-ver", function () {
            const id = $(this).data("id");

            $.ajax({
                url: _URL + "/ajs/gestion/activos/obtener",
                type: "POST",
                data: { id: id },
                success: function (response) {
                    const data = JSON.parse(response);

                    $("#modalDetalles").data("activo-id", id);

                    const year = new Date().getFullYear();
                    const correlativo = `${String(id).padStart(6, '0')}/${year}`;

                    $("#correlativo").text(`N° ${correlativo}`);
                    $("#correlativo-grande").text(`GESTIÓN DE ACTIVOS N° ${correlativo}`);

                    $("#detalle-cliente").text(data.cliente_razon_social);
                    $("#detalle-marca").text(data.marca);
                    $("#detalle-modelo").text(data.modelo);
                    $("#detalle-equipo").text(data.equipo);
                    $("#detalle-serie").text(data.numero_serie);
                    $("#detalle-motivo").text(data.motivo);
                    $("#detalle-ingreso").text(data.fecha_ingreso || 'Pendiente');
                    $("#detalle-salida").text(data.fecha_salida);
                    $("#detalle-observaciones").text(data.observaciones || 'Sin observaciones');

                    // Mostrar estado
                    if (data.estado === 'CONFIRMADO') {
                        $("#detalle-estado").html('<span class="estado-oficina">ESTÁ EN OFICINA</span>');
                    } else {
                        $("#detalle-estado").html('<span class="estado-no-oficina">NO ESTÁ EN OFICINA</span>');
                    }

                    $("#modalDetalles").modal('show');
                }
            });
        });

        // Manejador para el botón EDITAR
        $("#tabla_clientes").on("click", ".btnEditar", function () {
            const id = $(this).data("id");
            
            // Configurar modo edición
            modoEdicion = true;
            activoEditandoId = id;
            
            // Cambiar título y botón
            $('#modalTitulo').text('Editar Registro de Activo');
            $('#submitRegistroModal').text('Actualizar Registro');

            $.ajax({
                url: _URL + "/ajs/gestion/activos/obtener",
                type: "POST",
                data: { id: id },
                success: function (response) {
                    const data = JSON.parse(response);
                    console.log("Datos para editar:", data);
                    
                    // Cargar datos en el formulario
                    cargarDatosParaEdicion(data);
                    
                    // Mostrar modal
                    $("#modalRegistroActivo").modal('show');
                    
                    // Reinicializar autocomplete
                    setTimeout(() => {
                        appModal.initializeAutocomplete();
                    }, 500);
                },
                error: function() {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudieron cargar los datos del activo",
                        icon: "error",
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                }
            });
        });

        // Manejador para el botón de descarga
        $("#btnDescargarPDF").click(function () {
            const id = $("#modalDetalles").data("activo-id");
            if (id) {
                window.location.href = `${_URL}/gestion/activos/descargar-pdf/${id}`;
            } else {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo identificar el activo",
                    icon: "error",
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            }
        });

        // Acción para eliminar registro
        $("#tabla_clientes").on("click", ".btnBorrar", function () {
            const id = $(this).data("id");
            Swal.fire({
                title: "¿Deseas borrar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
                customClass: {
                    container: 'swal-high-zindex'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/gestion/activos/delete",
                        type: "post",
                        data: { idDelete: id },
                        success: function (resp) {
                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire({
                                title: "¡Buen trabajo!",
                                text: "Registro Borrado Exitosamente",
                                icon: "success",
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                        }
                    });
                }
            });
        });

        // Confirmar llegada a oficina
        $("#tabla_clientes").on("click", ".btnConfirmar", function () {
            const id = $(this).data("id");
            const row = tabla_clientes.row($(this).closest('tr')).data();

            const confirmarActivo = (fecha = null) => {
                const datos = { id: id };
                if (fecha) {
                    datos.fecha_ingreso = fecha;
                }

                $.ajax({
                    url: _URL + "/ajs/gestion/activos/confirmar",
                    type: "POST",
                    data: datos,
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire({
                                title: "¡Confirmado!",
                                text: "El activo ha sido marcado como recibido en oficina",
                                icon: "success",
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                            tabla_clientes.ajax.reload(null, false);
                        } else if (data.requiresFechaIngreso) {
                            Swal.fire({
                                title: "Fecha de Ingreso Requerida",
                                html: `
                            <p>Este activo no tiene fecha de ingreso registrada.</p>
                            <input type="date" id="fecha_ingreso" class="swal2-input" value="${new Date().toISOString().split('T')[0]}">
                        `,
                                showCancelButton: true,
                                confirmButtonText: "Confirmar",
                                cancelButtonText: "Cancelar",
                                customClass: {
                                    container: 'swal-high-zindex'
                                },
                                preConfirm: () => {
                                    const fecha = document.getElementById('fecha_ingreso').value;
                                    if (!fecha) {
                                        Swal.showValidationMessage('Por favor seleccione una fecha de ingreso');
                                        return false;
                                    }
                                    return fecha;
                                }
                            }).then((result) => {
                                if (result.isConfirmed && result.value) {
                                    confirmarActivo(result.value);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: data.error || "No se pudo actualizar el estado del activo",
                                icon: "error",
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: "Error",
                            text: "Hubo un problema al comunicarse con el servidor",
                            icon: "error",
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                    }
                });
            };

            Swal.fire({
                title: "¿Confirmar llegada a oficina?",
                text: "Esta acción marcará el activo como recibido en oficina",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, confirmar",
                cancelButtonText: "Cancelar",
                customClass: {
                    container: 'swal-high-zindex'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmarActivo();
                }
            });
        });

        function verificarFechasVencimiento() {
            const tabla = $("#tabla_clientes").DataTable();
            const datos = tabla.data().toArray();
            let alertasMostradas = 0;

            datos.forEach(row => {
                if (row.estado !== 'CONFIRMADO' && row.fecha_ingreso) {
                    const fechaIngreso = new Date(row.fecha_ingreso);
                    const fechaActual = new Date();
                    const diasRestantes = Math.ceil((fechaIngreso - fechaActual) / (1000 * 60 * 60 * 24));

                    if (alertasMostradas < 3) {
                        if (diasRestantes < 0) {
                            Swal.fire({
                                title: '¡Alerta de vencimiento!',
                                html: `El activo de <strong>${row.cliente_razon_social}</strong> tiene la fecha de ingreso vencida.<br>
                                      Fecha programada: ${row.fecha_ingreso}`,
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                            alertasMostradas++;
                        } else if (diasRestantes <= 3) {
                            Swal.fire({
                                title: '¡Próximo vencimiento!',
                                html: `El activo de <strong>${row.cliente_razon_social}</strong> debe ingresar en ${diasRestantes} días.<br>
                                      Fecha programada: ${row.fecha_ingreso}`,
                                icon: 'warning',
                                confirmButtonText: 'Entendido',
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                            alertasMostradas++;
                        }
                    }
                }
            });
        }

        // Ejecutar verificación al cargar
        verificarFechasVencimiento();
        setInterval(verificarFechasVencimiento, 24 * 60 * 60 * 1000);

        // Tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // --- LÓGICA PARA GESTIONAR MOTIVOS ---
        function cargarMotivosEnTabla() {
            $.get(_URL + "/ajs/get/motivos", function (data) {
                let resp = typeof data === 'string' ? JSON.parse(data) : data;
                if (resp.status && resp.data) resp = resp.data;
                let html = '';
                $.each(resp, function (i, v) {
                    html += `<tr data-id="${v.id}">
                        <td class="motivo-nombre">${v.nombre}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btnEditarMotivo" title="Editar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btnEliminarMotivo" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#tablaMotivos tbody').html(html);
            });
        }

        function cargarMotivosEnSelect() {
            $.get(_URL + "/ajs/get/motivos", function (data) {
                let resp = typeof data === 'string' ? JSON.parse(data) : data;
                if (resp.status && resp.data) resp = resp.data;
                let options = '<option value="">Seleccione un motivo</option>';
                $.each(resp, function (i, v) {
                    options += `<option value="${v.nombre}">${v.nombre}</option>`;
                });
                $('#motivo_modal').html(options);
            });
        }

        // Gestión de eventos del modal de motivos
        $('#modalMotivo').on('show.bs.modal', function () {
            cargarMotivosEnTabla();
            $('#motivo_nombre').val('');
        });

        $('#modalMotivo').on('hidden.bs.modal', function () {
            // Limpiar backdrop residual
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            
            // Mostrar modal de registro nuevamente
            setTimeout(() => {
                $("#modalRegistroActivo").modal('show');
                cargarMotivosEnSelect();
            }, 300);
        });

        // Agregar motivo
        $('#btnAgregarMotivo').click(function () {
            let nombre = $('#motivo_nombre').val().trim();
            if (!nombre) {
                Swal.fire({
                    title: 'Error',
                    text: 'Ingrese un nombre de motivo',
                    icon: 'warning',
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
                return;
            }
            $.post(_URL + "/ajs/save/motivos", { nombre }, function (data) {
                let resp = typeof data === 'string' ? JSON.parse(data) : data;
                if (resp.status) {
                    cargarMotivosEnTabla();
                    $('#motivo_nombre').val('');
                    cargarMotivosEnSelect();
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Motivo agregado correctamente',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: resp.message || 'No se pudo guardar',
                        icon: 'error',
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                }
            });
        });

        // Edición inline
        $('#tablaMotivos').on('click', '.btnEditarMotivo', function () {
            let $tr = $(this).closest('tr');
            let id = $tr.data('id');
            let nombreActual = $tr.find('.motivo-nombre').text();

            if ($tr.hasClass('editing')) return;

            $tr.addClass('editing');
            $tr.find('.motivo-nombre').html(`<input type="text" class="form-control form-control-sm inputEditMotivo" value="${nombreActual}">`);
            $(this).hide();
            let $guardar = $(`<button class="btn btn-success btn-sm btnGuardarMotivo" title="Guardar"><i class="fa fa-check"></i></button>`);
            let $cancelar = $(`<button class="btn btn-secondary btn-sm btnCancelarEdicionMotivo" title="Cancelar"><i class="fa fa-times"></i></button>`);
            $(this).after($guardar, $cancelar);
        });

        // Guardar edición inline
        $('#tablaMotivos').on('click', '.btnGuardarMotivo', function () {
            let $tr = $(this).closest('tr');
            let id = $tr.data('id');
            let nuevoNombre = $tr.find('.inputEditMotivo').val().trim();
            if (!nuevoNombre) {
                Swal.fire({
                    title: 'Error',
                    text: 'El nombre no puede estar vacío',
                    icon: 'warning',
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
                return;
            }
            $.post(_URL + "/ajs/update/motivos", { id, nombre: nuevoNombre }, function (data) {
                let resp = typeof data === 'string' ? JSON.parse(data) : data;
                if (resp.status) {
                    cargarMotivosEnTabla();
                    cargarMotivosEnSelect();
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Motivo actualizado correctamente',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: resp.message || 'No se pudo actualizar',
                        icon: 'error',
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                }
            });
        });

        // Cancelar edición inline
        $('#tablaMotivos').on('click', '.btnCancelarEdicionMotivo', function () {
            cargarMotivosEnTabla();
        });

        // Eliminar motivo
        $('#tablaMotivos').on('click', '.btnEliminarMotivo', function () {
            let $tr = $(this).closest('tr');
            let id = $tr.data('id');
            let nombre = $tr.find('.motivo-nombre').text();

            Swal.fire({
                title: '¿Eliminar motivo?',
                text: `¿Estás seguro de eliminar "${nombre}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    container: 'swal-high-zindex'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_URL + "/ajs/delete/motivos", { id }, function (data) {
                        let resp = typeof data === 'string' ? JSON.parse(data) : data;
                        if (resp.status) {
                            cargarMotivosEnTabla();
                            cargarMotivosEnSelect();
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'Motivo eliminado correctamente',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: resp.message || 'No se pudo eliminar',
                                icon: 'error',
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                        }
                    });
                }
            });
        });

        // Cargar motivos en el select al cargar la página
        cargarMotivosEnSelect();
    });
</script>