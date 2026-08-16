<div class="modal fade" id="ventas-pdf-reporte-v-p" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form target="_blank" action="<?= URL::to('/reporte/ventas/producto/lista/pdf/') ?>" method="get">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Reporte por Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Código De Producto</label>
                        <input required type="text" name="codprod" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Desde</label>
                        <input required value="<?= date('Y-m-d') ?>" type="date" name="fecha1" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" name="fecha2" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn bg-rojo text-white">Generar Reporte</button>
                    <button type="button" class="btn border bg-white text-rojo" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Cerrar</button>
                </div>
            </form>

        </div>
    </div>
</div>
