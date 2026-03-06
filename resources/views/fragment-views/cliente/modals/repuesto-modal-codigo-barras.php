  <div class="modal fade" id="modalCodigoBarras" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Codigo de Barras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3 text-center">
                        <img id="idCodigoBarras">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Escalar</label>
                        <select id="scalimg" class="form-control">
                            <option value="1">NO</option>
                            <option value="2">SI</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button class="btn bg-rojo text-white" id="btnImprimir" onclick="imprimir()">Imprimir</button>
                        <button class="btn bg-rojo text-white" id="btnImprimir2" onclick="imprimir2()">Imprimir
                            2</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>