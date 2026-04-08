<!-- Modal Equipos (usado en Orden de Servicio) -->
<div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="modalEquipoLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="modalEquipoLabel">
                    <i class="fa fa-laptop me-1"></i> Equipos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="equipo_nombre" placeholder="Nombre del equipo">
                        <button type="button" class="btn bg-rojo text-white" id="btnAgregarEquipo">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaEquipos">
                        <thead>
                            <tr>
                                <th><i class="fa fa-laptop me-1"></i> Nombre</th>
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
