<!-- resources\views\fragment-views\cliente\documentos\componentes\modales.php -->
<!-- Modal para ver ficha técnica -->
<div class="modal fade" id="verArchivoModal" tabindex="-1" aria-labelledby="verArchivoModalLabel" aria-hidden="true">
<div class="modal-dialog modal-fullscreen-lg-down modal-xl">

        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="verArchivoModalLabel">Ficha Técnica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="verArchivoModalBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-gris" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn bg-rojo text-white" id="descargarArchivoBtn">
                    <i class="fas fa-download me-2"></i>Descargar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para compartir por WhatsApp -->
<div class="modal fade" id="compartirWhatsAppModal" tabindex="-1" aria-labelledby="compartirWhatsAppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="compartirWhatsAppModalLabel">Compartir por WhatsApp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_archivo_compartir">
                
                <!-- Selección de archivos a compartir -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label text-negro fw-bold mb-0">Archivos a compartir:</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="marcarTodosArchivos()">
                                <i class="fas fa-check-square me-1"></i>Marcar todo
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="desmarcarTodosArchivos()">
                                <i class="fas fa-square me-1"></i>Desmarcar todo
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check_pdf" checked>
                                <label class="form-check-label" for="check_pdf">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check_editable" checked>
                                <label class="form-check-label" for="check_editable">
                                    <i class="fas fa-file-word text-primary me-2"></i>Archivo Editable
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check_imagenes" checked>
                                <label class="form-label" for="check_imagenes">
                                    <i class="fas fa-image text-success me-2"></i>Imágenes (3)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check_youtube" checked>
                                <label class="form-check-label" for="check_youtube">
                                    <i class="fab fa-youtube text-danger me-2"></i>Enlace de YouTube
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Número de teléfono -->
                <div class="mb-3">
                    <label for="telefono" class="form-label text-negro">Número de teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text bg-rojo text-white">+51</span>
                        <input type="text" class="form-control border-rojo" id="telefono" placeholder="Ingrese número (9 dígitos)" maxlength="9">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-gris" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="enviarWhatsApp()">
                    <i class="fab fa-whatsapp me-2"></i>Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de filtros -->
<div class="modal fade" id="filtroModal" tabindex="-1" aria-labelledby="filtroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="filtroModalLabel">Filtrar Fichas Técnicas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="filtro_fecha_desde" class="form-label text-negro">Fecha desde</label>
                    <input type="date" class="form-control border-rojo" id="filtro_fecha_desde">
                </div>
                <div class="mb-3">
                    <label for="filtro_fecha_hasta" class="form-label text-negro">Fecha hasta</label>
                    <input type="date" class="form-control border-rojo" id="filtro_fecha_hasta">
                </div>
                <div class="mb-3">
                    <label for="filtro_id_producto" class="form-label text-negro">Producto</label>
                    <select class="form-select border-rojo" id="filtro_id_producto">
                        <option value="">Todos los productos</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-gris" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="aplicarFiltros()">
                    <i class="fas fa-check me-2"></i>Aplicar filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmacionModalLabel">Confirmar Acción</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmacionModalBody">
                ¿Está seguro de que desea realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarAccionBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>