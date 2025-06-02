<!-- resources/views/fragment-views/cliente/documentos/componentes/formularios/editar-plantilla-informe.php -->
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h6 class="page-title">Editor de Plantilla de Informes</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Personalizar Plantilla</h5>
                    <div>
                        <button type="button" id="btn-preview-template" class="btn border-rojo me-2">
                            <i class="fa fa-eye"></i> Vista Previa
                        </button>
                        <button type="button" id="btn-save-template" class="btn bg-rojo text-white">
                            <i class="fa fa-save"></i> Guardar Cambios
                        </button>
                        <button type="button" id="btn-cancel-template" class="btn btn-outline-secondary ms-2">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formInformeTemplate" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="titulo_template" class="form-label">Título por Defecto</label>
                                <input type="text" class="form-control" id="titulo_template" name="titulo_template" value="INFORME">
                            </div>
                        </div>
                        
                        <!-- Secciones para las imágenes -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="header_image_template" class="form-label">Imagen de Encabezado</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="header_image_template" name="header_image_template" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-header-template">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Recomendado: imagen PNG de 210mm x 40mm (ancho completo A4)</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="header-preview-container-template" class="text-center">
                                        <img id="header-preview-template" src="/placeholder.svg" alt="Vista previa del encabezado" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="header-placeholder-template" class="text-muted">No se ha seleccionado ninguna imagen</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="footer_image_template" class="form-label">Imagen de Pie de Página</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="footer_image_template" name="footer_image_template" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-footer-template">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Recomendado: imagen PNG de 210mm x 30mm (ancho completo A4)</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="footer-preview-container-template" class="text-center">
                                        <img id="footer-preview-template" src="/placeholder.svg" alt="Vista previa del pie de página" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="footer-placeholder-template" class="text-muted">No se ha seleccionado ninguna imagen</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Contenido por Defecto</label>
                                <!-- Contenedor para el editor -->
                                <div id="editor-container-template" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
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
<div class="modal fade" id="previewTemplateModal" tabindex="-1" aria-labelledby="previewTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTemplateModalLabel">Vista Previa de la Plantilla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-template" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>