<!-- resources\views\fragment-views\cliente\numero-series.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/numero-series.css') ?>?v=<?= time() ?>">

<style>
    /* Estilos para las alertas de garantías */
    .swal2-popup {
        font-size: 14px;
    }
    
    .swal2-popup .text-start {
        text-align: left;
    }
    
    .swal2-popup ul {
        margin: 10px 0;
        padding-left: 20px;
    }
    
    .swal2-popup li {
        margin: 5px 0;
    }
    
    .swal2-popup .text-danger {
        color: #dc3545;
        font-weight: bold;
    }
    
    .swal2-popup .text-muted {
        color: #6c757d;
        font-style: italic;
    }
    
    /* Estilos para el botón de eliminar */
    .btnBorrar {
        transition: all 0.3s ease;
    }
    
    .btnBorrar:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }
    
    /* Estilos para botones de garantía bloqueados */
    .btn-garantia-bloqueado {
        cursor: not-allowed !important;
        opacity: 0.6;
        position: relative;
    }
    
    .btn-garantia-bloqueado:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    
    .btn-garantia-bloqueado::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        pointer-events: none;
    }
    
    /* Tooltip personalizado para botones bloqueados */
    .btn-garantia-bloqueado[title]:hover::before {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 5px;
    }
    
    .btn-garantia-bloqueado[title]:hover::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #333;
        margin-bottom: -5px;
        z-index: 1000;
    }
    
    /* Estilos para el indicador de estado de garantía */
    .indicador-garantia {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        animation: fadeIn 0.3s ease-in;
    }
    
    .indicador-garantia i {
        font-size: 8px;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    
    /* Mejorar la apariencia de los botones de acción */
    .btn-group .btn {
        margin-right: 2px;
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>

<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">REGISTRO DE NÚMERO DE SERIES</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);">
            <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                <button onclick="descarFunccc()" class="btn border-rojo me-2">
                    <i class="fa fa-file-excel"></i> Descargar Registros Excel
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar"
                    class="btn bg-rojo text-white">
                    <i class="fa fa-plus"></i> Añadir Registro
                </button>
            </div>
            <!-- NUEVO: Mensaje informativo sobre eliminación y garantías -->
            <!-- <div class="alert alert-info mx-3 mt-2 mb-0" style="border-radius: 10px;">
                <i class="fa fa-info-circle me-2"></i>
                <strong>Información:</strong> 
                <ul class="mb-0 mt-1" style="padding-left: 20px;">
                    <li>Al eliminar un registro se verificarán automáticamente las garantías relacionadas.</li>
                    <li>Si existen garantías, se mostrará una advertencia antes de proceder con la eliminación.</li>
                    <li>Los botones de garantía se bloquean automáticamente cuando ya existe una garantía registrada.</li>
                    <li>Los botones bloqueados muestran un icono de candado <i class="fa fa-lock text-muted"></i> y no son clickeables.</li>
                </ul>
            </div> -->
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table nowrap table-sm table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>N°</th>
                                        <th>Cliente</th>
                                        <th>Cantidad de Equipos</th>
                                        <th>Fecha De Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Ver Detalles -->
            <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="modalDetallesLabel">Detalles del Registro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="detalle-header mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong class="d-block text-muted mb-1">Cliente:</strong>
                                        <h6 class="mb-0" id="detalle_cliente"></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <strong class="d-block text-muted mb-1">Fecha de Creación:</strong>
                                        <h6 class="mb-0" id="detalle_fecha"></h6>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Equipos Registrados</h6>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-bordered">
                                    <thead class="sticky-top bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Equipo</th>
                                            <th>Número de Serie</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle_equipos">
                                        <!-- Los equipos se agregarán dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar Registro -->
            <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="tituloModalAgregar">Agregar Registro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="frmClientesAgregar">
                            <div class="modal-body">
                                <!-- Checkbox para registrar con cliente -->
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="tiene_cliente" name="tiene_cliente" checked>
                                    <label for="tiene_cliente"><i class="fa fa-user"></i> Registro con cliente externo</label>
                                </div>

                                <!-- Sección de cliente -->
                                <div id="seccion_cliente" class="seccion-cliente">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cliente_documento" class="form-label">(RUC o DNI)</label>
                                            <div class="input-group">
                                                <input id="input_datos_cliente" type="text"
                                                    placeholder="Ingrese Documento"
                                                    class="form-control ui-autocomplete-input" maxlength="11"
                                                    autocomplete="off">
                                                <div class="input-group-prepend">
                                                    <button id="btn_buscar_cliente" class="btn bg-rojo text-white"
                                                        type="button"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cliente_ruc_dni" class="form-label">Cliente</label>
                                            <input type="text" placeholder="Nombre del cliente" class="form-control"
                                                autocomplete="off" id="cliente_ruc_dni" name="cliente_ruc_dni">
                                            <input type="hidden" id="cliente_documento" name="cliente_documento">
                                        </div>
                                    </div>
                                </div>

                                <!-- Añadir el campo para el último número de serie -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ultimo_numero_serie" class="form-label">Último número de serie
                                            registrado</label>
                                        <input type="text" class="form-control" id="ultimo_numero_serie" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_creacion" class="form-label">Fecha De Creación</label>
                                        <input type="date" class="form-control" id="fecha_creacion"
                                            name="fecha_creacion" required>
                                    </div>
                                </div>

                                <!-- Checkbox para máquinas idénticas -->
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="maquinas_identicas" name="maquinas_identicas">
                                    <label for="maquinas_identicas">Máquinas idénticas (solo cambia el número de
                                        serie)</label>
                                </div>

                                <!-- Sección para máquinas idénticas (inicialmente oculta) -->
                                <div id="seccion_maquinas_identicas" class="seccion-maquinas-identicas"
                                    style="display: none;">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Marca</label>
                                            <div class="input-group">
                                                <select class="form-select" id="marca_comun" required>
                                                    <option value="">Seleccionar Marca</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo" id="btn_seleccionar_marca"
                                                    data-bs-toggle="modal" data-bs-target="#modalMarca">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Modelo</label>
                                            <div class="input-group">
                                                <select class="form-select" id="modelo_comun" required>
                                                    <option value="">Seleccionar Modelo</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo" id="btn_seleccionar_modelo"
                                                    data-bs-toggle="modal" data-bs-target="#modalModelo">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Equipo</label>
                                            <div class="input-group">
                                                <select class="form-select" id="equipo_comun" required>
                                                    <option value="">Seleccionar Equipo</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo" id="btn_seleccionar_equipo"
                                                    data-bs-toggle="modal" data-bs-target="#modalEquipo">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad de equipos</label>
                                            <input type="number" class="form-control" id="cantidad_equipos"
                                                name="cantidad_equipos" min="1" value="1" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Números de serie</label>
                                            <textarea class="form-control" id="series_masivas" rows="5"
                                                placeholder="Ingrese los números de serie separados por coma (ej: 001, 002, 003) o uno por línea"></textarea>
                                            <small class="text-muted">Ejemplo: 001, 002, 003 o un número por
                                                línea</small>
                                            <div class="series-counter mt-2">
                                                <i class="fa fa-tag"></i> <span id="contador_series">0</span> números de
                                                serie detectados
                                            </div>
                                            <div class="text-danger mt-2" id="error_series" style="display: none;">
                                                <i class="fa fa-exclamation-triangle"></i> La cantidad de números de
                                                serie debe coincidir con la cantidad de equipos
                                            </div>
                                            <div id="series_repetidas_mensaje" class="series-repetidas mt-2"
                                                style="display: none;">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>¡Atención!</strong> Se han detectado números de serie repetidos.
                                                Cada número de serie debe ser único.
                                                <div id="series_repetidas_lista" class="mt-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor para visualizar los equipos agregados -->
                                <div class="card mt-4" id="seccion_equipos_individuales">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Equipos a registrar</h6>
                                        <span class="badge bg-rojo" id="contador_equipos">1</span>
                                    </div>
                                    <div class="card-body">
                                        <div id="equipos_container" class="equipos-container">
                                            <!-- Por defecto, ya tenemos un equipo -->
                                            <div class="equipo-item card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h5 class="card-title mb-0">Equipo 1</h5>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-eliminar-equipo">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="form-label">Marca</label>
                                                            <div class="input-group">
                                                                <select class="form-select" name="equipos[0][marca]"
                                                                    required>
                                                                    <option value="">Seleccionar Marca</option>
                                                                </select>
                                                                <button type="button" class="btn btn-selector"
                                                                    data-bs-toggle="modal" data-bs-target="#modalMarca">
                                                                    <i class="fa fa-list"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Modelo</label>
                                                            <div class="input-group">
                                                                <select class="form-select" name="equipos[0][modelo]"
                                                                    required>
                                                                    <option value="">Seleccionar Modelo</option>
                                                                </select>
                                                                <button type="button" class="btn btn-selector"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#modalModelo">
                                                                    <i class="fa fa-list"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Equipo</label>
                                                            <div class="input-group">
                                                                <select class="form-select" name="equipos[0][equipo]"
                                                                    required>
                                                                    <option value="">Seleccionar Equipo</option>
                                                                </select>
                                                                <button type="button" class="btn btn-selector"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#modalEquipo">
                                                                    <i class="fa fa-list"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label">Número de Serie</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    name="equipos[0][numero_serie]"
                                                                    placeholder="Número de Serie" required>
                                                                <button type="button" class="btn btn-generar-serie"
                                                                    title="Generar número de serie">
                                                                    <i class="fa fa-magic"></i>
                                                                </button>
                                                            </div>
                                                            <div class="feedback-container"></div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensaje para series repetidas en equipos individuales -->
                                <div id="series_repetidas_equipos_mensaje" class="series-repetidas mt-3"
                                    style="display: none;">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>¡Atención!</strong> Se han detectado números de serie repetidos en los
                                    equipos.
                                    Cada número de serie debe ser único.
                                    <div id="series_repetidas_equipos_lista" class="mt-1"></div>
                                </div>

                                <div class="mt-3" id="seccion_agregar_equipo">
                                    <button type="button" id="agregar_equipo_diferente"
                                        class="btn bg-white text-rojo border-rojo">
                                        <i class="fa fa-plus"></i> Agregar equipo diferente
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-white text-rojo border-rojo"
                                    data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitRegistro"
                                    class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Actualizar Serie -->
            <div class="modal fade" id="updateRegistro" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Series</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="updateRegistroForm">
                            <div class="modal-body">
                                <input type="hidden" id="idRegistro" name="id">

                                <!-- Checkbox para registrar con cliente (edición) -->
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="tiene_cliente_u" name="tiene_cliente_u" checked>
                                    <label for="tiene_cliente_u"><i class="fa fa-user"></i> Registro con cliente externo</label>
                                </div>

                                <!-- Sección de cliente (edición) -->
                                <div id="seccion_cliente_u" class="seccion-cliente">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cliente_documento_u" class="form-label">(RUC o DNI)</label>
                                            <div class="input-group">
                                                <input id="input_datos_cliente_u" type="text"
                                                    placeholder="Ingrese Documento"
                                                    class="form-control ui-autocomplete-input" maxlength="11"
                                                    autocomplete="off">
                                                <div class="input-group-prepend">
                                                    <button id="btn_buscar_cliente_u" class="btn bg-rojo"
                                                        type="button"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cliente_ruc_dni_u" class="form-label">Cliente</label>
                                            <input type="text" class="form-control" id="cliente_ruc_dni_u"
                                                name="cliente_ruc_dni">
                                            <input type="hidden" id="cliente_documento_u" name="cliente_documento">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ultimo_numero_serie_u" class="form-label">Último número de serie
                                            registrado</label>
                                        <input type="text" class="form-control" id="ultimo_numero_serie_u" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_creacion_u" class="form-label">Fecha De Creación</label>
                                        <input type="date" class="form-control" id="fecha_creacion_u"
                                            name="fecha_creacion" required>
                                    </div>
                                </div>

                                <!-- Checkbox para máquinas idénticas (edición) -->
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="maquinas_identicas_u" name="maquinas_identicas_u">
                                    <label for="maquinas_identicas_u">Máquinas idénticas (solo cambia el número de
                                        serie)</label>
                                </div>

                                <!-- Sección para máquinas idénticas (edición - inicialmente oculta) -->
                                <div id="seccion_maquinas_identicas_u" class="seccion-maquinas-identicas"
                                    style="display: none;">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Marca</label>
                                            <div class="input-group">
                                                <select class="form-select" id="marca_comun_u" required>
                                                    <option value="">Seleccionar Marca</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo"
                                                    id="btn_seleccionar_marca_u" data-bs-toggle="modal"
                                                    data-bs-target="#modalMarca">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Modelo</label>
                                            <div class="input-group">
                                                <select class="form-select" id="modelo_comun_u" required>
                                                    <option value="">Seleccionar Modelo</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo"
                                                    id="btn_seleccionar_modelo_u" data-bs-toggle="modal"
                                                    data-bs-target="#modalModelo">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Equipo</label>
                                            <div class="input-group">
                                                <select class="form-select" id="equipo_comun_u" required>
                                                    <option value="">Seleccionar Equipo</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo"
                                                    id="btn_seleccionar_equipo_u" data-bs-toggle="modal"
                                                    data-bs-target="#modalEquipo">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad de equipos</label>
                                            <input type="number" class="form-control" id="cantidad_equipos_nuevos"
                                                min="1" value="1">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Números de serie</label>
                                            <textarea class="form-control" id="series_masivas_u" rows="5"
                                                placeholder="Ingrese los números de serie separados por coma (ej: 001, 002, 003) o uno por línea"></textarea>
                                            <small class="text-muted">Ejemplo: 001, 002, 003 o un número por
                                                línea</small>
                                            <div class="series-counter mt-2">
                                                <i class="fa fa-tag"></i> <span id="contador_series_u">0</span> números
                                                de serie detectados
                                            </div>
                                            <div class="text-danger mt-2" id="error_series_u" style="display: none;">
                                                <i class="fa fa-exclamation-triangle"></i> La cantidad de números de
                                                serie debe coincidir con la cantidad de equipos
                                            </div>
                                            <div id="series_repetidas_mensaje_u" class="series-repetidas mt-2"
                                                style="display: none;">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>¡Atención!</strong> Se han detectado números de serie repetidos.
                                                Cada número de serie debe ser único.
                                                <div id="series_repetidas_lista_u" class="mt-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Equipos existentes -->
                                <div class="card mb-4 mt-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Equipos existentes</h6>
                                        <span class="badge bg-rojo" id="contador_equipos_existentes">0</span>
                                    </div>
                                    <div class="card-body">
                                        <div id="equipos_existentes" class="equipos-container">
                                            <!-- Aquí se cargarán los equipos existentes -->
                                            <div class="text-center text-muted py-3" id="no_equipos_existentes_message">
                                                <i class="fa fa-info-circle fa-2x mb-2"></i>
                                                <p>No hay equipos registrados.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor para visualizar los equipos a agregar -->
                                <div class="card" id="seccion_equipos_individuales_u">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Nuevos equipos a agregar</h6>
                                        <span class="badge bg-rojo" id="contador_equipos_nuevos">0</span>
                                    </div>
                                    <div class="card-body">
                                        <div id="equipos_container_u" class="equipos-container">
                                            <!-- Aquí se mostrarán los equipos a agregar -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensaje para series repetidas en equipos individuales (edición) -->
                                <div id="series_repetidas_equipos_mensaje_u" class="series-repetidas mt-3"
                                    style="display: none;">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>¡Atención!</strong> Se han detectado números de serie repetidos en los
                                    equipos.
                                    Cada número de serie debe ser único.
                                    <div id="series_repetidas_equipos_lista_u" class="mt-1"></div>
                                </div>

                                <div class="mt-3" id="seccion_agregar_equipo_u">
                                    <button type="button" id="agregar_equipo_diferente_u" class="btn bg-rojo text-white">
                                        <i class="fa fa-plus"></i> Agregar equipo diferente
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateRegistroBtn" class="btn bg-rojo">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Marca -->
            <div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
                <div class="modal-dialog modal-stacked">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="modalMarcaLabel">
                                <i class="fa fa-tag me-1"></i> Marcas
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="marca_nombre"
                                        placeholder="Nombre de la marca">
                                    <button type="button" class="btn bg-rojo" id="btnAgregarMarca">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-scroll">
                                <table class="table table-striped" id="tablaMarcas">
                                    <thead>
                                        <tr>
                                            <th><i class="fa fa-tag me-1"></i> Nombre</th>
                                            <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Modelo -->
            <div class="modal fade" id="modalModelo" tabindex="-1" aria-labelledby="modalModeloLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-stacked">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="modalModeloLabel">
                                <i class="fa fa-cube me-1"></i> Modelos
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="modelo_nombre"
                                        placeholder="Nombre del modelo">
                                    <button type="button" class="btn bg-rojo" id="btnAgregarModelo">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-scroll">
                                <table class="table table-striped" id="tablaModelos">
                                    <thead>
                                        <tr>
                                            <th><i class="fa fa-cube me-1"></i> Nombre</th>
                                            <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Equipo -->
            <div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="modalEquipoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-stacked">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="modalEquipoLabel">
                                <i class="fa fa-laptop me-1"></i> Equipos
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="equipo_nombre"
                                        placeholder="Nombre del equipo">
                                    <button type="button" class="btn bg-rojo" id="btnAgregarEquipo">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-scroll">
                                <table class="table table-striped" id="tablaEquipos">
                                    <thead>
                                        <tr>
                                            <th><i class="fa fa-laptop me-1"></i> Nombre</th>
                                            <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FUNCIONES GLOBALES - Deben estar fuera de $(document).ready()
    
    // Función para descargar registros Excel
    function descarFunccc() {
        window.open(_URL + `/reporte/registros/excel?texto=${$("#buscar_registros").val()}`)
    }

    // NUEVO: Función para verificar garantías antes de eliminar (GLOBAL)
    function verificarGarantiasAntesDeEliminar(idRegistro) {
        $.ajax({
            url: _URL + "/ajs/verificar/garantias",
            method: "POST",
            data: { id: idRegistro },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.tiene_garantias) {
                        // Mostrar alerta de confirmación con información de garantías
                        const garantiasInfo = response.garantias_info;
                        Swal.fire({
                            title: '⚠️ ¡Atención!',
                            html: `
                                <div class="text-start">
                                    <p><strong>Este registro tiene garantías relacionadas:</strong></p>
                                    <ul class="text-start">
                                        <li>Total de garantías: <strong>${garantiasInfo.total}</strong></li>
                                        <li>Números de garantía: <strong>${garantiasInfo.numeros.join(', ')}</strong></li>
                                    </ul>
                                    <p class="text-danger mt-3">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>¡ADVERTENCIA!</strong> Al eliminar este registro se eliminarán también todas las garantías relacionadas.
                                    </p>
                                    <p class="text-muted">¿Está seguro que desea continuar?</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar todo',
                            cancelButtonText: 'Cancelar',
                            width: '500px'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Proceder con la eliminación
                                eliminarRegistro(idRegistro);
                            }
                        });
                    } else {
                        // No tiene garantías, proceder directamente
                        Swal.fire({
                            title: '¿Está seguro?',
                            text: 'Esta acción no se puede deshacer.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                eliminarRegistro(idRegistro);
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.error || "Error al verificar garantías",
                        icon: "error"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Error al conectar con el servidor",
                    icon: "error"
                });
            }
        });
    }

    // NUEVO: Función para eliminar el registro (GLOBAL)
    function eliminarRegistro(idRegistro) {
        $.ajax({
            url: _URL + "/ajs/delete/numeroseries",
            method: "POST",
            data: { id: idRegistro },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: "El registro ha sido eliminado exitosamente",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Recargar la tabla
                        $('#tabla_clientes').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.error || "Error al eliminar el registro",
                        icon: "error"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Error al conectar con el servidor",
                    icon: "error"
                });
            }
        });
    }

    $(document).ready(function () {
        // Establecer la fecha actual por defecto
        const fechaActual = new Date().toISOString().split('T')[0];
        $('#fecha_creacion').val(fechaActual);

        // Cargar el último número de serie al iniciar
        cargarUltimoNumeroSerie();

        // Manejar el checkbox de cliente
        $('#tiene_cliente').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_cliente').removeClass('oculta');
                // Limpiar campos cuando se habilita registro con cliente
                $('#cliente_ruc_dni').val('');
                $('#cliente_documento').val('');
                $('#input_datos_cliente').val('');
                // Habilitar campos para edición
                $('#input_datos_cliente').prop('readonly', false);
                $('#cliente_ruc_dni').prop('readonly', false);
            } else {
                // No ocultar la sección, sino llenar con datos de la empresa
                $('#seccion_cliente').removeClass('oculta');
                // Llenar con datos de la empresa
                $('#input_datos_cliente').val('20538381978');
                $('#cliente_documento').val('20538381978');
                $('#cliente_ruc_dni').val('COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');
                // Deshabilitar campos para evitar edición
                $('#input_datos_cliente').prop('readonly', true);
                $('#cliente_ruc_dni').prop('readonly', true);
            }
        });

        // Manejar el checkbox de cliente (edición)
        $('#tiene_cliente_u').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_cliente_u').removeClass('oculta');
                // Limpiar campos cuando se habilita registro con cliente
                $('#cliente_ruc_dni_u').val('');
                $('#cliente_documento_u').val('');
                $('#input_datos_cliente_u').val('');
                // Habilitar campos para edición
                $('#input_datos_cliente_u').prop('readonly', false);
                $('#cliente_ruc_dni_u').prop('readonly', false);
            } else {
                // No ocultar la sección, sino llenar con datos de la empresa
                $('#seccion_cliente_u').removeClass('oculta');
                // Llenar con datos de la empresa
                $('#input_datos_cliente_u').val('20538381978');
                $('#cliente_documento_u').val('20538381978');
                $('#cliente_ruc_dni_u').val('COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');
                // Deshabilitar campos para evitar edición
                $('#input_datos_cliente_u').prop('readonly', true);
                $('#cliente_ruc_dni_u').prop('readonly', true);
            }
        });

        // Evento para cargar el último número de serie al abrir el modal de agregar
        $('[data-bs-target="#modalAgregar"]').on('click', function () {
            cargarUltimoNumeroSerie();
            actualizarTituloModal(); // <CHANGE> Agregar llamada para actualizar título
            // Inicializar estado del checkbox y campos
            inicializarEstadoModal();
        });

        // Evento para cargar el último número de serie al abrir el modal de editar
        $('#tabla_clientes').on('click', '.btnEditar', function () {
            cargarUltimoNumeroSerie();
        });

        // Inicializar DataTable con la configuración original
        var tabla_clientes = $("#tabla_clientes").DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "order": [[3, "desc"]], // Ordenar por fecha de creación (columna 3) en orden descendente
            "ajax": {
                "url": _URL + "/ajs/get/numeroseries",
                "dataSrc": "",
                "error": function (xhr, error, thrown) {
                    console.error("Error en la respuesta del servidor:", error, thrown);
                    console.log("Respuesta completa:", xhr.responseText);
                    $("#tabla_clientes tbody").html('<tr><td colspan="5" class="text-center">Error al cargar los datos. Por favor, intente nuevamente.</td></tr>');
                }
            },
            "columns": [
                {
                    "data": "numero",
                    "render": function (data, type, row) {
                        return 'NS-' + String(data).padStart(2, '0');
                    }
                },
                {
                    "data": "cliente_ruc_dni",
                    "render": function (data, type, row) {
                        // Si no tiene cliente, mostrar "Registro Interno"
                        if (!data || data === '' || data === null) {
                            return '<span class="text-primary"><i class="fa fa-building"></i> Registro Interno (JVC)</span>';
                        }
                        return data;
                    }
                },
                { "data": "cantidad_equipos" },
                { "data": "fecha_creacion" },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return `
                            <div class="text-center">
                                <div class="btn-group btn-sm">
                                    <button data-id="${Number(row.id)}" class="btn btn-sm btn-info btnVerDetalles" title="Ver detalles">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button data-id="${Number(row.id)}" class="btn btn-sm btn-warning btnEditar" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button data-id="${Number(row.id)}" class="btn btn-sm btn-danger btnBorrar" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <a data-id="${Number(row.id)}" class="btn btn-sm btnGarantia" title="Crear Garantía" style="margin: 0; padding: 0; background-color: #DBE8F0;">
                                        <i class="ri-shield-check-line text-danger" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0;font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            "language": {
                "url": "ServerSide/Spanish.json"
            }
        });

        // Función para verificar si un número de serie existe
        function verificarNumeroSerie(numeroSerie, callback) {
            if (!numeroSerie || numeroSerie.trim() === '') {
                callback(false);
                return;
            }

            $.ajax({
                url: _URL + "/ajs/verificar/numeroserie",
                method: "POST",
                data: { numero_serie: numeroSerie },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        callback(response.existe);
                    } else {
                        callback(false);
                    }
                },
                error: function () {
                    callback(false);
                }
            });
        }

        // Función para mostrar feedback visual
        function mostrarFeedbackSerie(input, existe) {
            const feedbackContainer = input.siblings('.feedback-container');
            input.removeClass('is-valid is-invalid numero-serie-generado');
            feedbackContainer.empty();

            if (existe) {
                input.addClass('is-invalid');
                feedbackContainer.html('<div class="invalid-feedback d-block">Este número de serie ya existe en la base de datos.</div>');
                return false;
            } else if (input.val().trim() !== '') {
                input.addClass('is-valid');
                feedbackContainer.html('<div class="valid-feedback d-block">Número de serie disponible.</div>');
                return true;
            }
            return true;
        }

        // Función para generar número de serie único
        function generarNumeroSerie(inputElement) {
            $.ajax({
                url: _URL + "/ajs/generar/numeroserie",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        // Asignar el número generado al input
                        inputElement.val(response.numero_serie);
                        inputElement.addClass('is-valid numero-serie-generado');
                        inputElement.siblings('.feedback-container').html('<div class="valid-feedback d-block">Número de serie generado automáticamente.</div>');
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.error || "No se pudo generar el número de serie",
                            icon: "error"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Error al conectar con el servidor",
                        icon: "error"
                    });
                }
            });
        }

        // Event listener para los botones de generar serie
        $(document).on('click', '.btn-generar-serie', function () {
            const inputElement = $(this).siblings('input[name$="[numero_serie]"]');
            generarNumeroSerie(inputElement);
        });


        // Validación para equipos individuales
        let typingTimer;
        $(document).on('input', 'input[name$="[numero_serie]"]', function () {
            const input = $(this);
            clearTimeout(typingTimer);

            // Eliminar clases y feedback previos mientras se escribe
            input.removeClass('is-valid is-invalid');
            input.siblings('.invalid-feedback, .valid-feedback').remove();

            typingTimer = setTimeout(function () {
                const numeroSerie = input.val().trim();
                if (numeroSerie) {
                    verificarNumeroSerie(numeroSerie, function (existe) {
                        mostrarFeedbackSerie(input, existe);
                    });

                    // Verificar series repetidas en equipos individuales
                    let todasLasSeries = [];
                    $('input[name$="[numero_serie]"]').each(function () {
                        const valor = $(this).val().trim();
                        if (valor) {
                            todasLasSeries.push(valor);
                        }
                    });

                    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje'),
                        $('#series_repetidas_equipos_lista')
                    );
                }
            }, 500);
        });

        // También validar al perder el foco
        $(document).on('blur', 'input[name$="[numero_serie]"]', function () {
            const input = $(this);
            const numeroSerie = input.val().trim();

            if (numeroSerie) {
                verificarNumeroSerie(numeroSerie, function (existe) {
                    mostrarFeedbackSerie(input, existe);
                });

                // Verificar series repetidas en equipos individuales
                let todasLasSeries = [];
                $('input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                mostrarMensajeSeriesRepetidas(
                    seriesRepetidas,
                    $('#series_repetidas_equipos_mensaje'),
                    $('#series_repetidas_equipos_lista')
                );
            }
        });

        // Validación para equipos individuales en edición
        $(document).on('input', '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]', function () {
            const input = $(this);
            clearTimeout(typingTimer);

            input.removeClass('is-valid is-invalid');
            input.siblings('.invalid-feedback, .valid-feedback').remove();

            typingTimer = setTimeout(function () {
                const numeroSerie = input.val().trim();
                if (numeroSerie) {
                    verificarNumeroSerie(numeroSerie, function (existe) {
                        mostrarFeedbackSerie(input, existe);
                    });

                    let todasLasSeries = [];
                    $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]').each(function () {
                        const valor = $(this).val().trim();
                        if (valor) {
                            todasLasSeries.push(valor);
                        }
                    });

                    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje_u'),
                        $('#series_repetidas_equipos_lista_u')
                    );
                }
            }, 500);
        });

        // También validar al perder el foco en edición
        $(document).on('blur', '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]', function () {
            const input = $(this);
            const numeroSerie = input.val().trim();

            if (numeroSerie) {
                verificarNumeroSerie(numeroSerie, function (existe) {
                    mostrarFeedbackSerie(input, existe);
                });

                let todasLasSeries = [];
                $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                mostrarMensajeSeriesRepetidas(
                    seriesRepetidas,
                    $('#series_repetidas_equipos_mensaje_u'),
                    $('#series_repetidas_equipos_lista_u')
                );
            }
        });

        // Validación para máquinas idénticas (textarea)
        $('#series_masivas').on('input', function () {
            const textarea = $(this);
            clearTimeout(typingTimer);

            $('#series_duplicadas_mensaje').remove();

            typingTimer = setTimeout(function () {
                const series = procesarSeriesMasivas(textarea.val());

                if (series.length > 0) {
                    const seriesRepetidas = verificarSeriesRepetidas(series);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_mensaje'),
                        $('#series_repetidas_lista'),
                        textarea
                    );

                    let seriesVerificadas = 0;
                    let seriesDuplicadas = [];

                    series.forEach(function (serie) {
                        verificarNumeroSerie(serie, function (existe) {
                            seriesVerificadas++;

                            if (existe) {
                                seriesDuplicadas.push(serie);
                            }

                            if (seriesVerificadas === series.length) {
                                if (seriesDuplicadas.length > 0) {
                                    $('#series_duplicadas_mensaje').remove();
                                    textarea.after(`
                                        <div id="series_duplicadas_mensaje" class="series-duplicadas">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            Las siguientes series ya existen en la base de datos: <strong>${seriesDuplicadas.join(', ')}</strong>
                                        </div>
                                    `);
                                }
                            }
                        });
                    });
                } else {
                    $('#series_repetidas_mensaje').hide();
                    textarea.removeClass('has-duplicates');
                }
            }, 800);
        });

        // También para el formulario de edición
        $('#series_masivas_u').on('input', function () {
            const textarea = $(this);
            clearTimeout(typingTimer);

            $('#series_duplicadas_mensaje_u').remove();

            typingTimer = setTimeout(function () {
                const series = procesarSeriesMasivas(textarea.val());

                if (series.length > 0) {
                    const seriesRepetidas = verificarSeriesRepetidas(series);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_mensaje_u'),
                        $('#series_repetidas_lista_u'),
                        textarea
                    );

                    let seriesVerificadas = 0;
                    let seriesDuplicadas = [];

                    series.forEach(function (serie) {
                        verificarNumeroSerie(serie, function (existe) {
                            seriesVerificadas++;

                            if (existe) {
                                seriesDuplicadas.push(serie);
                            }

                            if (seriesVerificadas === series.length) {
                                if (seriesDuplicadas.length > 0) {
                                    $('#series_duplicadas_mensaje_u').remove();
                                    textarea.after(`
                                        <div id="series_duplicadas_mensaje_u" class="series-duplicadas">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            Las siguientes series ya existen en la base de datos: <strong>${seriesDuplicadas.join(', ')}</strong>
                                        </div>
                                    `);
                                }
                            }
                        });
                    });
                } else {
                    $('#series_repetidas_mensaje_u').hide();
                    textarea.removeClass('has-duplicates');
                }
            }, 800);
        });

        // Actualizar contador de series en tiempo real
        $('#series_masivas').on('input', function () {
            const series = procesarSeriesMasivas($(this).val());
            $('#contador_series').text(series.length);
            validarCantidadSeries();
        });

        // Actualizar validación cuando cambia la cantidad de equipos
        $('#cantidad_equipos').on('change', function () {
            const ultimoNumero = parseInt($("#ultimo_numero_serie").val());

            if (!isNaN(ultimoNumero)) {
                generarSeriesMasivas(ultimoNumero);
            }
        });

        // Manejar el checkbox de máquinas idénticas
        $('#maquinas_identicas').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_maquinas_identicas').show();
                $('#seccion_equipos_individuales').hide();
                $('#seccion_agregar_equipo').hide();
                $('#series_repetidas_equipos_mensaje').hide();

                const ultimoNumero = parseInt($("#ultimo_numero_serie").val());
                if (!isNaN(ultimoNumero)) {
                    generarSeriesMasivas(ultimoNumero);
                }
            } else {
                $('#seccion_maquinas_identicas').hide();
                $('#seccion_equipos_individuales').show();
                $('#seccion_agregar_equipo').show();
                $('#series_repetidas_mensaje').hide();
            }
        });

        // Manejar el checkbox de máquinas idénticas (edición)
        $('#maquinas_identicas_u').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_maquinas_identicas_u').show();
                $('#seccion_equipos_individuales_u').hide();
                $('#seccion_agregar_equipo_u').hide();
                $('#series_repetidas_equipos_mensaje_u').hide();
            } else {
                $('#seccion_maquinas_identicas_u').hide();
                $('#seccion_equipos_individuales_u').show();
                $('#seccion_agregar_equipo_u').show();
                $('#series_repetidas_mensaje_u').hide();
            }
        });

        // Eliminar equipo
        $(document).on('click', '.btn-eliminar-equipo', function () {
            $(this).closest('.equipo-item').remove();

            $('#equipos_container .equipo-item').each(function (index) {
                $(this).find('.card-title').text(`Equipo ${index + 1}`);
                $(this).find('input').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });

            $('#contador_equipos').text($('#equipos_container .equipo-item').length);

            setTimeout(function () {
                let todasLasSeries = [];
                $('input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                if (seriesRepetidas.length === 0) {
                    $('#series_repetidas_equipos_mensaje').hide();
                } else {
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje'),
                        $('#series_repetidas_equipos_lista')
                    );
                }
            }, 100);
        });

        // Ver detalles
        $('#tabla_clientes').on('click', '.btnVerDetalles', function () {
            var idRegistro = $(this).data('id');
            $.ajax({
                url: _URL + "/ajs/getOne/numeroseries",
                method: "POST",
                data: { id: idRegistro },
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.data && response.data.length > 0) {
                        const registro = response.data[0];

                        // Mostrar cliente o "Registro Interno"
                        const clienteTexto = registro.cliente_ruc_dni && registro.cliente_ruc_dni !== ''
                            ? registro.cliente_ruc_dni
                            : 'Registro Interno (COMERCIAL & INDUSTRIAL J. V. C. S.A.C.)';
                        $('#detalle_cliente').text(clienteTexto);
                        $('#detalle_fecha').text(registro.fecha_creacion);

                        $('#detalle_equipos').empty();
                        if (registro.equipos && registro.equipos.length > 0) {
                            registro.equipos.forEach((equipo, index) => {
                                const estado = equipo.estado || 'disponible';
                                const estadoTexto = estado === 'en_garantia' ? 'En Garantía' : 'Disponible';
                                const estadoClase = estado === 'en_garantia' ? 'bg-danger text-white' : 'bg-success text-white';

                                $('#detalle_equipos').append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${equipo.marca_nombre || equipo.marca || ''}</td>
                                        <td>${equipo.modelo_nombre || equipo.modelo || ''}</td>
                                        <td>${equipo.equipo_nombre || equipo.equipo || ''}</td>
                                        <td>${equipo.numero_serie || ''}</td>
                                        <td><span class="badge ${estadoClase} px-2 py-1">${estadoTexto}</span></td>
                                    </tr>
                                `);
                            });
                        } else {
                            $('#detalle_equipos').append('<tr><td colspan="6" class="text-center">No hay equipos registrados</td></tr>');
                        }

                        $('#modalDetalles').modal('show');
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.error || "Error al cargar los detalles del registro",
                            icon: "error"
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudieron obtener los detalles del registro",
                        icon: "error"
                    });
                }
            });
        });



        // Eliminar equipo nuevo
        $(document).on('click', '.btn-eliminar-equipo-nuevo', function () {
            $(this).closest('.equipo-item').remove();

            $('#equipos_container_u .equipo-item').each(function (index) {
                $(this).find('.card-title').text(`Equipo nuevo ${index + 1}`);
                $(this).find('input').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });

            const numEquiposNuevos = $('#equipos_container_u .equipo-item').length;
            $('#contador_equipos_nuevos').text(numEquiposNuevos);

            if (numEquiposNuevos === 0) {
                $('#no_equipos_nuevos_message').show();
            }

            setTimeout(function () {
                let todasLasSeries = [];
                $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                if (seriesRepetidas.length === 0) {
                    $('#series_repetidas_equipos_mensaje_u').hide();
                } else {
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje_u'),
                        $('#series_repetidas_equipos_lista_u')
                    );
                }
            }, 100);
        });

        // Actualizar contador de series en tiempo real (edición)
        $('#series_masivas_u').on('input', function () {
            const series = procesarSeriesMasivas($(this).val());
            $('#contador_series_u').text(series.length);

            const cantidadEquipos = parseInt($('#cantidad_equipos_nuevos').val());
            if (series.length !== cantidadEquipos) {
                $(this).closest('.row').find('.series-counter').addClass('error');
                $('#error_series_u').show();
            } else {
                $(this).closest('.row').find('.series-counter').removeClass('error');
                $('#error_series_u').hide();
            }
        });

        // Actualizar validación cuando cambia la cantidad de equipos (edición)
        $('#cantidad_equipos_nuevos').on('change', function () {
            const series = procesarSeriesMasivas($('#series_masivas_u').val());
            const cantidadEquipos = parseInt($(this).val());

            if (series.length !== cantidadEquipos) {
                $('.series-counter').addClass('error');
                $('#error_series_u').show();
            } else {
                $('.series-counter').removeClass('error');
                $('#error_series_u').hide();
            }
        });
        // <CHANGE> Función para actualizar el título del modal con el próximo número
        function actualizarTituloModal() {
            $.ajax({
                url: _URL + "/ajs/get/proximonumero/series",
                type: "GET",
                success: function (response) {
                    const data = JSON.parse(response);
                    const proximoNumero = String(data.proximo_numero).padStart(2, '0');
                    $('#tituloModalAgregar').text('Agregar Registro N° ' + proximoNumero);
                },
                error: function () {
                    console.log("Error al obtener próximo número");
                    $('#tituloModalAgregar').text('Agregar Registro'); // Mantener título original si hay error
                }
            });
        }
        // <CHANGE> Resetear título cuando se cierra el modal
        $('#modalAgregar').on('hidden.bs.modal', function () {
            $('#tituloModalAgregar').text('Agregar Registro');
        });

        // Función para inicializar el estado del modal
        function inicializarEstadoModal() {
            const tieneCliente = $('#tiene_cliente').is(':checked');
            if (!tieneCliente) {
                // Si está desmarcado, llenar con datos de empresa
                $('#input_datos_cliente').val('20538381978');
                $('#cliente_documento').val('20538381978');
                $('#cliente_ruc_dni').val('COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');
                $('#input_datos_cliente').prop('readonly', true);
                $('#cliente_ruc_dni').prop('readonly', true);
            } else {
                // Si está marcado, limpiar campos y habilitar edición
                $('#cliente_ruc_dni').val('');
                $('#cliente_documento').val('');
                $('#input_datos_cliente').val('');
                $('#input_datos_cliente').prop('readonly', false);
                $('#cliente_ruc_dni').prop('readonly', false);
            }
        }

    });

            // Agregar este código al final del script en numero-series.php
        $(document).on('click', '.btnGarantia', function () {
            const id = $(this).data('id');
            window.location.href = _URL + '/garantia/add?id=' + id;
        });

        // NUEVO: Evento para el botón eliminar con verificación de garantías
        $('#tabla_clientes').on('click', '.btnBorrar', function () {
            const idRegistro = $(this).data('id');
            verificarGarantiasAntesDeEliminar(idRegistro);
        });

        // NUEVO: Función para verificar y bloquear botones de garantía
        function verificarYBloquearGarantias() {
            $('#tabla_clientes tbody tr').each(function() {
                const row = $(this);
                const idRegistro = row.find('.btnBorrar').data('id');
                const btnGarantia = row.find('.btnGarantia');
                
                if (idRegistro && btnGarantia.length > 0) {
                    $.ajax({
                        url: _URL + "/ajs/verificar/garantias",
                        method: "POST",
                        data: { id: idRegistro },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success && response.tiene_garantias) {
                                // Bloquear el botón de garantía
                                btnGarantia.addClass('btn-garantia-bloqueado');
                                btnGarantia.prop('disabled', true);
                                btnGarantia.attr('title', 'Ya tiene garantía registrada');
                                
                                // Cambiar el icono y estilo
                                btnGarantia.html('<i class="fa fa-lock"></i>');
                                btnGarantia.removeClass('btn-success').addClass('btn-secondary');
                            } else {
                                // Desbloquear el botón si no tiene garantías
                                btnGarantia.removeClass('btn-garantia-bloqueado');
                                btnGarantia.prop('disabled', false);
                                btnGarantia.attr('title', 'Agregar garantía');
                                
                                // Restaurar el icono y estilo original
                                btnGarantia.html('<i class="fa fa-shield"></i>');
                                btnGarantia.removeClass('btn-secondary').addClass('btn-success');
                            }
                        },
                        error: function() {
                            console.log('Error verificando garantías para registro:', idRegistro);
                        }
                    });
                }
            });
        }

        // NUEVO: Función para actualizar un botón específico de garantía
        function actualizarBotonGarantia(idRegistro, tieneGarantia) {
            const row = $(`#tabla_clientes tbody tr:has(.btnBorrar[data-id="${idRegistro}"])`);
            const btnGarantia = row.find('.btnGarantia');
            
            if (btnGarantia.length > 0) {
                if (tieneGarantia) {
                    // Bloquear el botón
                    btnGarantia.addClass('btn-garantia-bloqueado');
                    btnGarantia.prop('disabled', true);
                    btnGarantia.attr('title', 'Ya tiene garantía registrada');
                    btnGarantia.html('<i class="fa fa-lock"></i>');
                    btnGarantia.removeClass('btn-success').addClass('btn-secondary');
                    
                    // Agregar indicador visual de estado
                    let indicadorEstado = row.find('.indicador-garantia');
                    if (indicadorEstado.length === 0) {
                        indicadorEstado = $('<span class="indicador-garantia badge bg-success ms-2" title="Tiene garantía"><i class="fa fa-check"></i></span>');
                        row.find('.btnGarantia').after(indicadorEstado);
                    }
                } else {
                    // Desbloquear el botón
                    btnGarantia.removeClass('btn-garantia-bloqueado');
                    btnGarantia.prop('disabled', false);
                    btnGarantia.attr('title', 'Agregar garantía');
                    btnGarantia.html('<i class="fa fa-shield"></i>');
                    btnGarantia.removeClass('btn-secondary').addClass('btn-success');
                    
                    // Remover indicador visual de estado
                    row.find('.indicador-garantia').remove();
                }
            }
        }

        // NUEVO: Llamar a la verificación después de cargar la tabla
        $('#tabla_clientes').on('draw.dt', function() {
            setTimeout(verificarYBloquearGarantias, 100);
        });

        // NUEVO: También verificar al cargar la página inicialmente
        $(document).ready(function() {
            setTimeout(verificarYBloquearGarantias, 500);
        });

        // NUEVO: Evento para actualizar botones cuando se recarga la tabla
        $('#tabla_clientes').on('xhr.dt', function() {
            setTimeout(verificarYBloquearGarantias, 200);
        });

        // NUEVO: Evento para actualizar botones después de operaciones CRUD
        $(document).on('serieActualizada', function(e, idRegistro) {
            // Verificar garantías para el registro específico
            setTimeout(function() {
                $.ajax({
                    url: _URL + "/ajs/verificar/garantias",
                    method: "POST",
                    data: { id: idRegistro },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            actualizarBotonGarantia(idRegistro, response.tiene_garantias);
                        }
                    }
                });
            }, 300);
        });

</script>
<script src="<?= URL::to('public/js/series/funciones-comunes.js') ?>?v=<?= time() ?>"></script>
<!-- js para buscar cliente por DNI O RUC -->
<script src="<?= URL::to('public/js/series/buscar-cliente.js') ?>?v=<?= time() ?>"></script>
<!-- cargar datos en los selects para marca, modelo y equipo -->
<script src="<?= URL::to('public/js/series/cargar-selects.js') ?>?v=<?= time() ?>"></script>
<!-- cargar datos en las tablas de los modales para marca, modelo y equipo -->
<script src="<?= URL::to('public/js/series/cargar-equipo-tablas.js') ?>?v=<?= time() ?>"></script>
<!-- inicializacion de modales de marca, etc -->
<script src="<?= URL::to('public/js/series/inicializar-modales.js') ?>?v=<?= time() ?>"></script>
<!-- crud para el registro -->
<script src="<?= URL::to('public/js/series/crud-registro.js') ?>?v=<?= time() ?>"></script>
<!-- crud para el registro independiente de marca, modelo y equipo -->
<script src="<?= URL::to('public/js/series/crud-equipos.js') ?>?v=<?= time() ?>"></script>