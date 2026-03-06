<div class="modal fade" id="modal-prodEreport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte De Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Año</label>
                    <select id='anioreporEFG' class="form-control">
                        <?php
                        $anio = date('Y');
                        for ($i = 0; $i < 10; $i++) {
                            echo "<option value='$anio'>$anio</option>";
                            $anio--;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mes</label>
                    <select id='mesreprEFG' class="form-control">
                        <?php
                        $contador = 1;
                        $meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                        foreach ($meses as $mes) {
                            echo '<option  ' . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
                            $contador++;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Día</label>
                    <input id='diareporEfghg' class="form-control">
                </div>

            </div>
            <div class="modal-footer">
                <button id="generarreporteProd" type="button" class="btn btn-primary">Generar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>