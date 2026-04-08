<!-- Modal Técnicos (compartido entre Orden de Trabajo y Orden de Servicio) -->
<div class="modal fade" id="modalTecnico" tabindex="-1" aria-labelledby="modalTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="modalTecnicoLabel">
                    <i class="fa fa-user-cog me-1"></i> Técnicos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="tecnico_nombre" placeholder="Nombre del técnico">
                        <button type="button" class="btn bg-rojo text-white" id="btnAgregarTecnico">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaTecnicos">
                        <thead>
                            <tr>
                                <th><i class="fa fa-user-cog me-1"></i> Nombre</th>
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
