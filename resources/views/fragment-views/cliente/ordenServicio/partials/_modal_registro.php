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
