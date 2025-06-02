<!-- resources/views/fragment-views/cliente/documentos/componentes/formularios/editar-informe.php -->
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h6 class="page-title" id="titulo-pagina-informe">Nuevo Informe</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Datos del Informe</h5>
                    <div>
                        <button type="button" id="btn-preview-informe" class="btn border-rojo me-2">
                            <i class="fa fa-eye"></i> Vista Previa
                        </button>
                        <button type="button" id="btn-save-informe" class="btn bg-rojo text-white">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" id="btn-cancel-informe" class="btn btn-outline-secondary ms-2">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formInforme" enctype="multipart/form-data">
                        <input type="hidden" id="id_informe" name="id_informe">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_informe" class="form-label">Tipo de Informe</label>
                                <input type="text" class="form-control" id="tipo_informe" name="tipo_informe" placeholder="Ej: TÉCNICO, PAGO, SERVICIO" required>
                                <small class="form-text text-muted">Este campo se usará para filtrar los informes.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select" id="cliente_id" name="cliente_id">
                                    <option value="">Seleccione un cliente</option>
                                    <!-- Se cargará dinámicamente -->
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="titulo_informe" class="form-label">Título del Informe</label>
                                <input type="text" class="form-control" id="titulo_informe" name="titulo_informe" required>
                            </div>
                        </div>
                        
                        <!-- Secciones para las imágenes -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="header_image" class="form-label">Imagen de Encabezado</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="header_image" name="header_image" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-header-informe">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Si no selecciona una imagen, se usará la de la plantilla.</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="header-preview-container-informe" class="text-center">
                                        <img id="header-preview-informe" src="/placeholder.svg" alt="Vista previa del encabezado" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="header-placeholder-informe" class="text-muted">Se usará la imagen de la plantilla</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="footer_image" class="form-label">Imagen de Pie de Página</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="footer_image" name="footer_image" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-footer-informe">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Si no selecciona una imagen, se usará la de la plantilla.</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="footer-preview-container-informe" class="text-center">
                                        <img id="footer-preview-informe" src="/placeholder.svg" alt="Vista previa del pie de página" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="footer-placeholder-informe" class="text-muted">Se usará la imagen de la plantilla</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Contenido del Informe</label>
                                <!-- Contenedor para el editor -->
                                <div id="editor-container-informe" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                    <!-- El editor Quill se cargará aquí -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewInformeModal" tabindex="-1" aria-labelledby="previewInformeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewInformeModalLabel">Vista Previa del Informe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-informe" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>