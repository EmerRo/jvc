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