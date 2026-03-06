<div class="modal fade" id="ventas-pdf-reporte" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="exampleModalLabel">Reporte de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas">
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
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Metodos</label>
                            <select name="metodo" class="form-control">
                                <option value="0">Todos</option>
                                <option value="1">TRANSFERENCIA BANCO BCP</option>
                                <option value="2">TRANSFERENCIA BANCO NACION</option>
                                <option value="3">TRANSFERENCIA BANCO INTERBANK</option>
                                <option value="4">TRANSFERENCIA BANCO BBVA</option>
                                <option value="5">YAPE</option>
                                <option value="6">PLIN</option>
                                <option value="10">POS</option>
                                <option value="11">TRANSFERENCIA SCOTIABANK</option>
                                <option value="12">EFECTIVO</option>

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