<div class="modal fade" id="ventas-xls-reporte" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo">
                <h5 class="modal-title" id="exampleModalLabel">Reporte Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas-xls">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anioExcel" id='anioExcel' class="form-control">
                                <?php
                                $anio = date('Y');
                                for ($i = 0; $i < 10; $i++) {
                                    echo "<option value='$anio'>$anio</option>";
                                    $anio--;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mesExcel" id='mesExcel' class="form-control">
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
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn border-rojo text-rojo bg-white">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-rojo text-white" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>