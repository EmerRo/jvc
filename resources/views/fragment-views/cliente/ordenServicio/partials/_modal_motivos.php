<!-- Modal para gestionar motivos -->
<div class="modal fade modal-stacked" id="modalMotivo" tabindex="-1" aria-labelledby="modalMotivoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="modalMotivoLabel">
                    <i class="fa fa-info-circle me-1"></i> Gestión de Motivos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="motivo_nombre" placeholder="Nombre del motivo">
                        <button type="button" class="btn bg-rojo" id="btnAgregarMotivo">
                            <i class="fa fa-save"></i> Agregar
                        </button>
                    </div>
                </div>
                <div class="table-scroll" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped" id="tablaMotivos">
                        <thead>
                            <tr>
                                <th><i class="fa fa-info-circle me-1"></i> Nombre</th>
                                <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
