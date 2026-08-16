<div class="modal fade" id="ventas-text-reporte" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo">
                <h5 class="modal-title" id="exampleModalLabel">Txt Libro de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="txt-generar-resporte-ventas">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anio" class="form-control">
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
                            <select name="mes" class="form-control">
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
                            <button type="submit" class="btn bg-white text-rojo border-rojo">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-rojo text-white" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>