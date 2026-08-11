 <div class="modal fade" id="updateRegistro" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
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

                                <!-- NUEVO: Selector de tipo de máquina (edición) -->
                                <div class="mb-3 mt-3">
                                    <label class="form-label fw-bold">
                                        <i class="fa fa-cogs me-1"></i> Tipo de Máquina
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline tipo-maquina-option">
                                                <input class="form-check-input" type="radio" name="tipo_maquina_u" 
                                                       id="tipo_fabricada_u" value="fabricada" checked>
                                                <label class="form-check-label" for="tipo_fabricada_u">
                                                    <i class="fa fa-industry text-primary"></i> 
                                                    <strong>Fabricada</strong>
                                                    <small class="d-block text-muted">Máquinas que se fabrican en la empresa (CRIS-TAURO, MASTER GOLDS, etc.)</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline tipo-maquina-option">
                                                <input class="form-check-input" type="radio" name="tipo_maquina_u" 
                                                       id="tipo_importada_u" value="importada">
                                                <label class="form-check-label" for="tipo_importada_u">
                                                    <i class="fa fa-ship text-success"></i> 
                                                    <strong>Importada</strong>
                                                    <small class="d-block text-muted">Máquinas que llegan de importación (SPEED POWER, etc.)</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Mensaje informativo según el tipo seleccionado -->
                                    <div id="mensaje_tipo_fabricada_u" class="alert alert-info mt-2" style="display: block;">
                                        <i class="fa fa-info-circle me-1"></i>
                                        <strong>Máquinas Fabricadas:</strong> Estas series aparecerán en "Orden de Producción" para agregar productos durante la fabricación.
                                    </div>
                                    <div id="mensaje_tipo_importada_u" class="alert alert-success mt-2" style="display: none;">
                                        <i class="fa fa-check-circle me-1"></i>
                                        <strong>Máquinas Importadas:</strong> Estas series NO aparecerán en "Orden de Producción" ya que las máquinas llegan listas. Solo se registran los números de serie para las placas.
                                    </div>
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
                                    <div class="row mb-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Equipo <span class="text-danger">*</span></label>
                                            <select class="form-select" id="equipo_comun_u" required>
                                                <option value="">Seleccionar equipo...</option>
                                            </select>
                                            <input type="hidden" id="marca_comun_u">
                                            <input type="hidden" id="modelo_comun_u">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted"><i class="fa fa-tag me-1"></i>Marca / Modelo</label>
                                            <input type="text" class="form-control" id="marca_modelo_comun_display_u" readonly
                                                placeholder="Se completa al elegir equipo"
                                                style="background:#f8f9fa;cursor:default;">
                                        </div>
                                        <div class="col-md-4">
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