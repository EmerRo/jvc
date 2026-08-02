 <!-- Modal Ver Detalles -->
            <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="modalDetallesLabel">Detalles del Registro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="detalle-header mb-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong class="d-block text-muted mb-1">Cliente:</strong>
                                        <h6 class="mb-0" id="detalle_cliente"></h6>
                                    </div>
                                    <div class="col-md-4">
                                        <strong class="d-block text-muted mb-1">Fecha de Creación:</strong>
                                        <h6 class="mb-0" id="detalle_fecha"></h6>
                                    </div>
                                    <div class="col-md-4">
                                        <strong class="d-block text-muted mb-1">Estado del Lote:</strong>
                                        <h6 class="mb-0" id="detalle_estado_lote"></h6>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Equipos Registrados</h6>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-bordered">
                                    <thead class="sticky-top bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto Almacén</th>
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