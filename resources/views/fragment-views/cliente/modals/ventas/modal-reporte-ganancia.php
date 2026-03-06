<div class="modal fade" id="ventas-pdf-reporteganancia" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo">
                <h5 class="modal-title" id="exampleModalLabel">Reporte de Ventas y Ganancias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas-ganancia">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Día</label>
                            <input type="text" name='dia' class='form-control' oninput="process(this)" maxlength="2">
                        </div>
                        <div class="col-md-12 mb-3">
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
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mes" class="form-control">
                                <?php
                                $contador = 0;
                                $meses = array('TODOS', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                                foreach ($meses as $mes) {
                                    echo '<option  ' . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
                                    $contador++;
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn bg-white text-rojo border-rojo ">Generar</button>
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