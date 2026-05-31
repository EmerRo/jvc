<!-- Modal Reporte de Movimientos -->
<div class="modal fade" id="modal-reporte-movimientos" tabindex="-1" aria-labelledby="modalReporteMovimientosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="modalReporteMovimientosLabel">
                    <i class="fa fa-chart-line me-2"></i>Descargar Reporte de Movimientos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Seleccione el tipo de movimiento que desea descargar:</p>
                <div class="list-group">
                    <a href="<?= URL::to('/reporte/historial-stock/excel?tipo=todos') ?>" target="_blank" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-file-excel text-success me-3" style="font-size: 24px;"></i>
                            <div>
                                <h6 class="mb-0">Todos los Movimientos</h6>
                                <small class="text-muted">Incluye ingresos y egresos de todas las fuentes</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= URL::to('/reporte/historial-stock/excel?tipo=INGRESO') ?>" target="_blank" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-arrow-up text-primary me-3" style="font-size: 24px;"></i>
                            <div>
                                <h6 class="mb-0">Solo Ingresos</h6>
                                <small class="text-muted">Compras, producción interna, ajustes positivos</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= URL::to('/reporte/historial-stock/excel?tipo=EGRESO') ?>" target="_blank" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-arrow-down text-danger me-3" style="font-size: 24px;"></i>
                            <div>
                                <h6 class="mb-0">Solo Egresos</h6>
                                <small class="text-muted">Ventas, uso en órdenes, ajustes negativos</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= URL::to('/reporte/historial-stock/excel?tipo_origen=ORDEN_TRABAJO_INTERNA') ?>" target="_blank" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-industry text-info me-3" style="font-size: 24px;"></i>
                            <div>
                                <h6 class="mb-0">Producción Interna</h6>
                                <small class="text-muted">Solo ingresos por órdenes de trabajo internas</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= URL::to('/reporte/historial-stock/excel?tipo_origen=ORDEN_TRABAJO_EXTERNA') ?>" target="_blank" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-tools text-warning me-3" style="font-size: 24px;"></i>
                            <div>
                                <h6 class="mb-0">Uso en Órdenes Externas</h6>
                                <small class="text-muted">Solo egresos por órdenes de trabajo externas</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
