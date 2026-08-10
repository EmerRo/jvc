  <div class="modal fade" id="modal-historial-stock" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" style="max-width: 1500px; width: 95%;">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-history me-2"></i>Historial de Movimientos de Stock
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-2 g-2 align-items-end">
                                <div class="col-auto">
                                    <label class="form-label mb-1 small fw-semibold">Movimiento</label>
                                    <select id="filtro-movimiento-prod" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        <option value="INGRESO">INGRESO</option>
                                        <option value="EGRESO">EGRESO</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label mb-1 small fw-semibold">Desde</label>
                                    <input type="date" id="filtro-fecha-desde-prod" class="form-control form-control-sm">
                                </div>
                                <div class="col-auto">
                                    <label class="form-label mb-1 small fw-semibold">Hasta</label>
                                    <input type="date" id="filtro-fecha-hasta-prod" class="form-control form-control-sm">
                                </div>
                                <div class="col-auto">
                                    <button id="btn-limpiar-filtros-prod" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-times me-1"></i>Limpiar
                                    </button>
                                </div>
                            </div>
                            <table id="tabla-historial-stock" class="table table-striped table-hover table-sm" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Movimiento</th>
                                            <th>Cantidad</th>
                                            <th>Costo</th>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>