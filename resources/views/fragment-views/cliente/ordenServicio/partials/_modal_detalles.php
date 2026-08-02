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
