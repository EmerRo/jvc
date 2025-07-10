<!-- resources\views\fragment-views\cliente\numero-series.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/numero-series.css') ?>?v=<?= time() ?>">

<style>
.sin-borde-inferior {
    border-bottom: none !important;
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
        <div class="card" style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);">
            <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                <button onclick="descarFunccc()" class="btn border-rojo me-2">
                    <i class="fa fa-file-excel"></i> Descargar Registros Excel
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar"
                    class="btn bg-rojo text-white">
                    <i class="fa fa-plus"></i> Añadir Registro
                </button>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table nowrap table-sm table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
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
                            <h5 class="modal-title" id="exampleModalLabel">Agregar Registro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="frmClientesAgregar">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
    <label for="cliente_documento" class="form-label">(RUC o DNI)</label>
    <div class="input-group">
        <input id="input_datos_cliente" type="text" placeholder="Ingrese Documento"
            class="form-control" maxlength="11">
        <div class="input-group-prepend">
            <button id="btn_buscar_cliente" class="btn bg-rojo text-white"
                type="button"><i class="fa fa-search"></i></button>
        </div>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label for="cliente_ruc_dni" class="form-label">Cliente</label>
    <input type="text" placeholder="Nombre del cliente" class="form-control"
        autocomplete="off" id="cliente_ruc_dni" name="cliente_ruc_dni" required>
    <input type="hidden" id="cliente_documento" name="cliente_documento">
</div>
                                </div>
                                <!-- Añadir el nuevo campo para el último número de serie -->
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
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="btn_seleccionar_marca" data-bs-toggle="modal"
                                                    data-bs-target="#modalMarca">
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
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="btn_seleccionar_modelo" data-bs-toggle="modal"
                                                    data-bs-target="#modalModelo">
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
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="btn_seleccionar_equipo" data-bs-toggle="modal"
                                                    data-bs-target="#modalEquipo">
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
                                            <!-- Modificar esta parte en numero-series.php -->
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
                                                            <input type="text" class="form-control"
                                                                name="equipos[0][numero_serie]"
                                                                placeholder="Número de Serie" required>
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
                                <div class="row">
                                  <div class="col-md-6 mb-3">
    <label for="cliente_documento_u" class="form-label">(RUC o DNI)</label>
    <div class="input-group">
        <input id="input_datos_cliente_u" type="text"
            placeholder="Ingrese Documento" class="form-control" maxlength="11">
        <div class="input-group-prepend">
            <button id="btn_buscar_cliente_u" class="btn bg-rojo"
                type="button"><i class="fa fa-search"></i></button>
        </div>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label for="cliente_ruc_dni_u" class="form-label">Cliente</label>
    <input type="text" class="form-control" id="cliente_ruc_dni_u"
        name="cliente_ruc_dni" required>
    <input type="hidden" id="cliente_documento_u" name="cliente_documento">
</div>
                                </div>
                                <div class="row">
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
                                                <button type="button" class="btn btn-outline-secondary"
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
                                                <button type="button" class="btn btn-outline-secondary"
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
                                                <button type="button" class="btn btn-outline-secondary"
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
                                            <div class="text-center text-muted py-3" id="no_equipos_nuevos_message">
                                                <i class="fa fa-info-circle fa-2x mb-2"></i>
                                                <p>No hay nuevos equipos agregados.</p>
                                            </div>
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
                                    <button type="button" id="agregar_equipo_diferente_u" class="btn btn-secondary">
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
                        <div class="modal-header">
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
                        <div class="modal-header">
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
    function descarFunccc() {
        window.open(_URL + `/reporte/registros/excel?texto=${$("#buscar_registros").val()}`)
    }

    $(document).ready(function () {
        // Establecer la fecha actual por defecto
        const fechaActual = new Date().toISOString().split('T')[0];
        $('#fecha_creacion').val(fechaActual);

        // Cargar el último número de serie al iniciar
        cargarUltimoNumeroSerie();

        // Evento para cargar el último número de serie al abrir el modal de agregar
        $('[data-bs-target="#modalAgregar"]').on('click', function () {
            cargarUltimoNumeroSerie();
        });

        // Evento para cargar el último número de serie al abrir el modal de editar
        $('#tabla_clientes').on('click', '.btnEditar', function () {
            cargarUltimoNumeroSerie();
        });

        // Inicializar DataTable con la configuración original
        var tabla_clientes = $("#tabla_clientes").DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true, // Habilitar responsividad
            "scrollX": false,   // Deshabilitar scroll horizontal
            "autoWidth": false, // Deshabilitar auto-ancho
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "ajax": {
                "url": _URL + "/ajs/get/numeroseries",
                "dataSrc": "",
                "error": function (xhr, error, thrown) {
                    console.error("Error en la respuesta del servidor:", error, thrown);
                    console.log("Respuesta completa:", xhr.responseText);
                    // Mostrar mensaje de error amigable
                    $("#tabla_clientes tbody").html('<tr><td colspan="5" class="text-center">Error al cargar los datos. Por favor, intente nuevamente.</td></tr>');
                }
            },
            "columns": [
                {
                    "data": null,
                    "render": function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { "data": "cliente_ruc_dni" },
                { "data": "cantidad_equipos" },
                { "data": "fecha_creacion" },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return `
         <div class="text-center">
    <div class="btn-group btn-sm" >
        <button data-id="${Number(row.id)}" class="btn btn-sm btn-info btnVerDetalles" title="Ver detalles" >
            <i class="fa fa-eye " ></i>
        </button>
        <button data-id="${Number(row.id)}" class="btn btn-sm btn-warning btnEditar" title="Editar" >
            <i class="fa fa-edit " ></i>
        </button>
        <button data-id="${Number(row.id)}" class="btn btn-sm btn-danger btnBorrar" title="Eliminar" >
            <i class="fa fa-trash " ></i>
        </button>
        <a data-id="${Number(row.id)}" class="btn btn-sm btnGarantia" title="Crear Garantía" style="margin: 0; padding: 0; background-color: #DBE8F0;">
            <i class="ri-shield-check-line text-danger" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0;font-size: 18px; "></i>
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
            // Eliminar clases y feedback previos
            input.removeClass('is-valid is-invalid');
            input.siblings('.invalid-feedback, .valid-feedback').remove();

            if (existe) {
                // El número de serie ya existe
                input.addClass('is-invalid');
                input.after('<div class="invalid-feedback">Este número de serie ya existe en la base de datos.</div>');
                return false;
            } else if (input.val().trim() !== '') {
                // El número de serie es válido
                input.addClass('is-valid');
                input.after('<div class="valid-feedback">Número de serie disponible.</div>');
                return true;
            }
            return true;
        }



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
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
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

                // Verificar series repetidas en equipos individuales
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

            // Limpiar mensajes anteriores
            $('#series_duplicadas_mensaje').remove();

            typingTimer = setTimeout(function () {
                const series = procesarSeriesMasivas(textarea.val());

                if (series.length > 0) {
                    // Verificar series repetidas
                    const seriesRepetidas = verificarSeriesRepetidas(series);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_mensaje'),
                        $('#series_repetidas_lista'),
                        textarea
                    );

                    // Verificar cada serie en la base de datos
                    let seriesVerificadas = 0;
                    let seriesDuplicadas = [];

                    series.forEach(function (serie) {
                        verificarNumeroSerie(serie, function (existe) {
                            seriesVerificadas++;

                            if (existe) {
                                seriesDuplicadas.push(serie);
                            }

                            // Cuando se han verificado todas las series
                            if (seriesVerificadas === series.length) {
                                if (seriesDuplicadas.length > 0) {
                                    // Mostrar mensaje de error con las series duplicadas
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
            }, 800); // Esperar un poco más para el textarea
        });

        // También para el formulario de edición
        $('#series_masivas_u').on('input', function () {
            const textarea = $(this);
            clearTimeout(typingTimer);

            // Limpiar mensajes anteriores
            $('#series_duplicadas_mensaje_u').remove();

            typingTimer = setTimeout(function () {
                const series = procesarSeriesMasivas(textarea.val());

                if (series.length > 0) {
                    // Verificar series repetidas
                    const seriesRepetidas = verificarSeriesRepetidas(series);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_mensaje_u'),
                        $('#series_repetidas_lista_u'),
                        textarea
                    );

                    // Verificar cada serie en la base de datos
                    let seriesVerificadas = 0;
                    let seriesDuplicadas = [];

                    series.forEach(function (serie) {
                        verificarNumeroSerie(serie, function (existe) {
                            seriesVerificadas++;

                            if (existe) {
                                seriesDuplicadas.push(serie);
                            }

                            // Cuando se han verificado todas las series
                            if (seriesVerificadas === series.length) {
                                if (seriesDuplicadas.length > 0) {
                                    // Mostrar mensaje de error con las series duplicadas
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
            // Obtener el último número de serie registrado
            const ultimoNumero = parseInt($("#ultimo_numero_serie").val());

            if (!isNaN(ultimoNumero)) {
                // Generar series masivas basadas en el último número
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

                // Generar series masivas automáticamente
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

            // Renumerar los equipos
            $('#equipos_container .equipo-item').each(function (index) {
                $(this).find('.card-title').text(`Equipo ${index + 1}`);
                $(this).find('input').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });

            // Actualizar contador
            $('#contador_equipos').text($('#equipos_container .equipo-item').length);

            // Verificar series repetidas después de eliminar un equipo
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
                        $('#detalle_cliente').text(registro.cliente_ruc_dni);
                        $('#detalle_fecha').text(registro.fecha_creacion);

                        $('#detalle_equipos').empty();
                        if (registro.equipos && registro.equipos.length > 0) {
                            registro.equipos.forEach((equipo, index) => {
                                // Determinar el estado y aplicar estilo
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

            // Renumerar los equipos
            $('#equipos_container_u .equipo-item').each(function (index) {
                $(this).find('.card-title').text(`Equipo nuevo ${index + 1}`);
                $(this).find('input').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });

            // Actualizar contador
            const numEquiposNuevos = $('#equipos_container_u .equipo-item').length;
            $('#contador_equipos_nuevos').text(numEquiposNuevos);

            // Mostrar mensaje si no hay equipos nuevos
            if (numEquiposNuevos === 0) {
                $('#no_equipos_nuevos_message').show();
            }

            // Verificar series repetidas después de eliminar un equipo
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

            // Validar cantidad de series
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

    });
    // Agregar este código al final del script en numero-series.php
    $(document).on('click', '.btnGarantia', function () {
        const id = $(this).data('id');
        window.location.href = _URL + '/garantia/add?id=' + id;
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