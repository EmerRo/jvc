<!-- Modal Marcas (usado en Orden de Servicio) -->
<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="modalMarcaLabel">
                    <i class="fa fa-tag me-1"></i> Marcas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="marca_nombre" placeholder="Nombre de la marca">
                        <button type="button" class="btn bg-rojo text-white" id="btnAgregarMarca">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaMarcas">
                        <thead>
                            <tr>
                                <th><i class="fa fa-tag me-1"></i> Nombre</th>
                                <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
