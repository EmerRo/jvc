<!-- Modal Período Personalizado Guías -->
<div class="modal fade" id="periodoPersonalizadoGuiasModal" tabindex="-1"
    aria-labelledby="periodoPersonalizadoGuiasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="periodoPersonalizadoGuiasModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Seleccionar Período para Guías
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodoGuias">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaInicioGuias" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicioGuias"
                                    v-model="filtroFechasGuias.inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaFinGuias" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFinGuias"
                                    v-model="filtroFechasGuias.fin" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" @click="aplicarPeriodoPersonalizadoGuias">
                    <i class="fas fa-filter me-1"></i>Aplicar Filtro
                </button>
            </div>
        </div>
    </div>
</div>