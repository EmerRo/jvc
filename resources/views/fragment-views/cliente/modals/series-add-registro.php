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

                                <!-- NUEVO: Selector de tipo de máquina -->
                                <div class="mb-3 mt-3">
                                    <label class="form-label fw-bold">
                                        <i class="fa fa-cogs me-1"></i> Tipo de Máquina
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline tipo-maquina-option">
                                                <input class="form-check-input" type="radio" name="tipo_maquina" 
                                                       id="tipo_fabricada" value="fabricada" checked>
                                                <label class="form-check-label" for="tipo_fabricada">
                                                    <i class="fa fa-industry text-primary"></i> 
                                                    <strong>Fabricada</strong>
                                                    <small class="d-block text-muted">Máquinas que se fabrican en la empresa (CRIS-TAURO, MASTER GOLDS, etc.)</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline tipo-maquina-option">
                                                <input class="form-check-input" type="radio" name="tipo_maquina" 
                                                       id="tipo_importada" value="importada">
                                                <label class="form-check-label" for="tipo_importada">
                                                    <i class="fa fa-ship text-success"></i> 
                                                    <strong>Importada</strong>
                                                    <small class="d-block text-muted">Máquinas que llegan de importación (SPEED POWER, etc.)</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Mensaje informativo según el tipo seleccionado -->
                                    <div id="mensaje_tipo_fabricada" class="alert alert-info mt-2" style="display: block;">
                                        <i class="fa fa-info-circle me-1"></i>
                                        <strong>Máquinas Fabricadas:</strong> Estas series aparecerán en "Orden de Producción" para agregar productos durante la fabricación.
                                    </div>
                                    <div id="mensaje_tipo_importada" class="alert alert-success mt-2" style="display: none;">
                                        <i class="fa fa-check-circle me-1"></i>
                                        <strong>Máquinas Importadas:</strong> Estas series NO aparecerán en "Orden de Producción" ya que las máquinas llegan listas. Solo se registran los números de serie para las placas.
                                    </div>
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
                                                    <!-- NUEVO: Buscador de producto del almacén -->
                                                    <div class="row mb-2">
                                                        <div class="col-md-12">
                                                            <label class="form-label">
                                                                <i class="fa fa-box me-1 text-rojo"></i>
                                                                Producto del almacén
                                                                <small class="text-muted">(opcional, vincula con kardex)</small>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                                                <input type="text" class="form-control input-buscar-producto"
                                                                    name="equipos[0][producto_busqueda]"
                                                                    placeholder="Buscar por código o nombre..." autocomplete="off">
                                                                <input type="hidden" class="input-id-producto"
                                                                    name="equipos[0][id_producto]" value="">
                                                            </div>
                                                            <small class="text-muted producto-seleccionado-info"></small>
                                                        </div>
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