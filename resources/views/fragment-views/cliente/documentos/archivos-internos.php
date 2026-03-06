<!-- resources/views/fragment-views/cliente/documentos/componentes/archivos-internos.php -->
<style>
    /* Estilos generales */
    .image-preview {
        max-width: 100%;
        max-height: 150px;
        display: none;
    }

    .image-placeholder {
        border: 2px dashed #ccc;
        padding: 20px;
        text-align: center;
        background-color: #f9f9f9;
        color: #999;
    }

    .editor-container {
        height: 300px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }

    .vista {
        display: none;
    }

    .vista.active {
        display: block;
    }

    .archivo-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .archivo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .form-header {
        background-color: #dc3545;
        color: white;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .document-preview {
        height: 250px;
        overflow: hidden;
        display: block;
        background-color: white;
        padding: 0;
        margin: 0;
    }

    .pdf-preview-canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 100%;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    .btn-outline-secondary {
        position: relative;
        z-index: 1000;
        pointer-events: auto;
    }
</style>

<!-- Añadir PDF.js para la vista previa de documentos -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    // Configurar el worker de PDF.js
    window.pdfjsLib = window.pdfjsLib || {};
    window.pdfjsLib.GlobalWorkerOptions = window.pdfjsLib.GlobalWorkerOptions || {};
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
</script>

<!-- Botones de acción -->
<div class="mb-4">
    <button class="btn border-rojo" id="btn-lista-archivos-internos">
        <i class="fas fa-list me-2"></i>Lista de Archivos Internos
    </button>
    <button class="btn bg-rojo text-white" id="btn-nuevo-archivoInterno">
        <i class="fas fa-plus me-2"></i>Nuevo Archivo Interno
    </button>
    <button class="btn border-rojo" id="btn-editar-plantilla">
        <i class="fas fa-edit me-2"></i>Editar Plantilla
    </button>
    <button class="btn border-rojo" id="btn-gestionar-membretes">
        <i class="fas fa-image me-2"></i>Gestionar Membretes
    </button>
    <button class="btn bg-rojo hover:bg-white" onclick="window.archivoInternoModuleInstance.reiniciar()">
        <i class="fas fa-sync me-2"></i>Reiniciar Módulo
    </button>
</div>

<!-- Vista de lista de archivos internos -->
<div id="vista-lista-archivos-internos" class="vista active">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Archivos Internos</h3>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control border-rojo" id="buscar-archivoInterno" placeholder="Buscar archivos internos...">
            <button class="btn bg-rojo text-white" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div id="lista-archivos-internos-container">
        <!-- Aquí se cargarán dinámicamente los archivos internos -->
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando archivos internos...</p>
        </div>
    </div>
</div>

<!-- Vista de formulario de nuevo/editar archivo interno -->
<div id="vista-editar-archivoInterno" class="vista">
    <div class="form-header">
        <h3 id="titulo-pagina-archivoInterno" class="m-0">Nuevo Archivo Interno</h3>
        <p class="m-0">Complete la información del archivo interno</p>
    </div>

    <form id="formArchivoInterno" enctype="multipart/form-data">
        <input type="hidden" id="id_archivo_interno" name="id">
        <input type="hidden" id="contenido_archivo_interno" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">
        <input type="hidden" id="imagen1_data" name="imagen1">
        <input type="hidden" id="imagen2_data" name="imagen2">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="titulo_archivo_interno" class="form-label">Título del Archivo Interno</label>
                    <input type="text" class="form-control" id="titulo_archivo_interno" name="titulo" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_archivo_interno" class="form-label">Tipo de Archivo Interno</label>
                    <div class="input-group">
                        <select class="form-select" id="tipo_archivo_interno" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" id="btn-gestionar-tipos-archivoInterno"
                            onclick="abrirModalTiposArchivosInternos()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="form-text text-gris small">Este campo se usará para categorizar los archivos internos.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cliente_search" class="form-label">Cliente</label>
                    <div class="input-group">
                        <input type="text" class="form-control border rounded-start-2 shadow-sm" id="cliente_search"
                            placeholder="Buscar por nombre o documento..." autocomplete="off">
                        <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-search-cliente">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <input type="hidden" id="cliente_id" name="id_cliente">
                    <div class="mt-2" id="cliente_info" style="display: none;">
                        <div class="p-2 border rounded bg-light">
                            <p class="mb-1"><strong id="cliente_nombre"></strong></p>
                            <p class="mb-0 small text-muted" id="cliente_documento"></p>
                            <p class="mb-0 small text-muted" id="cliente_direccion"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="editor-container-archivoInterno" class="form-label">Contenido del Archivo Interno</label>
            <div id="editor-container-archivoInterno" class="editor-container"></div>
        </div>

        <!-- Sección de Imágenes Adicionales -->
        <div class="mb-4">
            <h6 class="fw-medium text-negro mb-3">
                <i class="fas fa-images me-2"></i>Imágenes del Documento (Opcional)
                <small class="text-muted">- Aparecerán en una segunda página</small>
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-negro">Primera Imagen</label>
                    
                    <div class="image-preview-container" id="preview-container-archivo-1" style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <input type="file" class="d-none" id="imagen1_file" name="imagen1" 
                               accept="image/png,image/jpeg,image/gif" onchange="handleImagePreviewArchivo(this, 1)">
                        
                        <div id="upload-area-archivo-1" class="upload-area" onclick="document.getElementById('imagen1_file').click()" style="cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #CA3438; margin-bottom: 15px; opacity: 0.7;"></i>
                            <div class="upload-placeholder">
                                <strong>Haz clic para seleccionar</strong><br>
                                <small>o arrastra una imagen aquí</small>
                            </div>
                        </div>
                        
                        <div id="preview-area-archivo-1" class="preview-area" style="display: none;">
                            <img id="preview-img-archivo-1" class="preview-image" style="max-width: 100%; max-height: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="image-actions mt-2" style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showImageModalArchivo(document.getElementById('preview-img-archivo-1').src)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="clearImagePreviewArchivo(1)">
                                    <i class="fas fa-trash"></i> Quitar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-text text-gris small mt-2">
                        <i class="fas fa-info-circle me-1"></i>Formatos: PNG, JPG, GIF (Máx. 5MB)
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium text-negro">Segunda Imagen</label>
                    
                    <div class="image-preview-container" id="preview-container-archivo-2" style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <input type="file" class="d-none" id="imagen2_file" name="imagen2" 
                               accept="image/png,image/jpeg,image/gif" onchange="handleImagePreviewArchivo(this, 2)">
                        
                        <div id="upload-area-archivo-2" class="upload-area" onclick="document.getElementById('imagen2_file').click()" style="cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #CA3438; margin-bottom: 15px; opacity: 0.7;"></i>
                            <div class="upload-placeholder">
                                <strong>Haz clic para seleccionar</strong><br>
                                <small>o arrastra una imagen aquí</small>
                            </div>
                        </div>
                        
                        <div id="preview-area-archivo-2" class="preview-area" style="display: none;">
                            <img id="preview-img-archivo-2" class="preview-image" style="max-width: 100%; max-height: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="image-actions mt-2" style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showImageModalArchivo(document.getElementById('preview-img-archivo-2').src)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="clearImagePreviewArchivo(2)">
                                    <i class="fas fa-trash"></i> Quitar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-text text-gris small mt-2">
                        <i class="fas fa-info-circle me-1"></i>Formatos: PNG, JPG, GIF (Máx. 5MB)
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" id="btn-cancel-archivoInterno">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn border-rojo" id="btn-preview-archivoInterno">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-archivoInterno">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
</div>

<!-- Modal para Gestionar Tipos de Archivo Interno -->
<div class="modal fade" id="gestionarTiposArchivoInternoModal" tabindex="-1" aria-labelledby="gestionarTiposArchivoInternoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarTiposArchivoInternoModalLabel">Gestionar Tipos de Archivo Interno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario para agregar nuevo tipo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Agregar Nuevo Tipo</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="nuevo-tipo-archivoInterno-nombre" class="form-label">Nombre del Tipo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuevo-tipo-archivoInterno-nombre"
                                    placeholder="Ej: COMERCIAL, FORMAL, NOTIFICACIÓN">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn bg-rojo text-white w-100" onclick="agregarTipoArchivoInterno()">
                                    <i class="fas fa-plus me-2"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de tipos existentes -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Tipos Existentes</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="lista-tipos-archivoInterno">
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <div class="spinner-border spinner-border-sm text-rojo" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Tipo -->
<div class="modal fade" id="editarTipoArchivoInternoModal" tabindex="-1" aria-labelledby="editarTipoArchivoInternoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarTipoArchivoInternoModalLabel">Editar Tipo de Archivo Interno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar-tipo-archivoInterno-id">
                <div class="mb-3">
                    <label for="editar-tipo-archivoInterno-nombre" class="form-label">Nombre del Tipo <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editar-tipo-archivoInterno-nombre">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="guardarTipoArchivoInternoEditado()">Guardar
                    Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewArchivoInternoModal" tabindex="-1" aria-labelledby="previewArchivoInternoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewArchivoInternoModalLabel">Vista Previa del Archivo Interno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-archivoInterno" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-rojo" id="btn-download-pdf">
                    <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="confirmarEliminarArchivoInternoModal" tabindex="-1"
    aria-labelledby="confirmarEliminarArchivoInternoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarArchivoInternoModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este archivo interno? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-archivoInterno">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Gestión de Membretes -->
<div class="modal fade" id="gestionarMembretesModal" tabindex="-1" aria-labelledby="gestionarMembretesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarMembretesModalLabel">
                    <i class="fas fa-image me-2"></i>Gestionar Membretes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Información:</strong> Las imágenes configuradas aquí se aplicarán automáticamente a todos
                    los archivos internos y plantillas.
                </div>

                <form id="formMembretes" enctype="multipart/form-data">
                    <input type="hidden" id="membrete_header_image_data" name="header_image">
                    <input type="hidden" id="membrete_footer_image_data" name="footer_image">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-arrow-up me-1"></i>Imagen de Cabecera
                                </label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="membrete_header_image"
                                        name="header_image_file" accept="image/*">
                                    <button class="btn btn-outline-danger" type="button" id="reset-membrete-header">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="image-placeholder" id="header-placeholder-membrete">
                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                    Sin imagen de cabecera
                                </div>
                                <img id="membrete-header-preview" class="image-preview" alt="Vista previa de cabecera">
                                <small class="text-muted">Recomendado: 800x200 píxeles</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-arrow-down me-1"></i>Imagen de Pie
                                </label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="membrete_footer_image"
                                        name="footer_image_file" accept="image/*">
                                    <button class="btn btn-outline-danger" type="button" id="reset-membrete-footer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="image-placeholder" id="footer-placeholder-membrete">
                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                    Sin imagen de pie
                                </div>
                                <img id="membrete-footer-preview" class="image-preview" alt="Vista previa de pie">
                                <small class="text-muted">Recomendado: 800x100 píxeles</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-outline-primary" id="btn-preview-membretes">
                    <i class="fas fa-eye me-1"></i> Vista Previa
                </button>

                <button type="button" class="btn bg-rojo text-white" id="btn-save-membretes">
                    <i class="fas fa-save me-1"></i> Guardar Membretes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición de Plantilla -->
<div class="modal fade" id="editarPlantillaArchivoInternoModal" tabindex="-1" aria-labelledby="editarPlantillaArchivoInternoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarPlantillaArchivoInternoModalLabel">Editar Plantilla de Archivo Interno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPlantillaArchivoInterno" enctype="multipart/form-data">
                    <input type="hidden" id="id_plantilla" name="id">
                    <input type="hidden" id="contenido_plantilla" name="contenido">
                    <input type="hidden" id="plantilla_header_image_data" name="header_image">
                    <input type="hidden" id="plantilla_footer_image_data" name="footer_image">

                    <div class="mb-3">
                        <label for="titulo_plantilla" class="form-label">Título de la Plantilla</label>
                        <input type="text" class="form-control" id="titulo_plantilla" name="titulo" required>
                    </div>

                    <div class="mb-3">
                        <label for="editor-container-plantilla" class="form-label">Contenido de la Plantilla</label>
                        <div id="editor-container-plantilla" class="editor-container"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-outline-primary" id="btn-preview-plantilla">
                    <i class="fas fa-eye me-1"></i> Vista Previa
                </button>
                <button type="button" class="btn btn-rojo" id="btn-save-plantilla">
                    <i class="fas fa-save me-1"></i> Guardar Plantilla
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cargar utilidades compartidas -->
<script src="<?= URL::to('public/js/modulo-documentos/utils.js') ?>?v=<?= time() ?>"></script>

<script>
    // Configuración específica para archivos internos
    const archivosInternosConfig = {
        tipo: 'archivoInterno',
        urls: {
            render: _URL + "/ajs/archivoInterno/render",
            insertar: _URL + "/ajs/archivoInterno/insertar",
            editar: _URL + "/ajs/archivoInterno/editar",
            borrar: _URL + "/ajs/archivoInterno/borrar",
            getOne: _URL + "/ajs/archivoInterno/getOne",
            generarPDF: _URL + "/ajs/archivoInterno/generarPDF",
            vistaPrevia: _URL + "/ajs/archivoInterno/vista-previa",
            obtenerTemplate: _URL + "/ajs/archivoInterno/obtener-template",
            guardarTemplate: _URL + "/ajs/archivoInterno/guardar-template",
            obtenerMembretes: _URL + "/ajs/archivoInterno/obtener-membretes",
            guardarMembretes: _URL + "/ajs/archivoInterno/guardar-membretes",
            obtenerTipos: _URL + "/ajs/archivoInterno/obtener-tipos-archivoInternos"
        },
        elementos: {
            // Botones principales
            btnLista: "#btn-lista-archivos-internos",
            btnNuevo: "#btn-nuevo-archivoInterno",
            btnEditarPlantilla: "#btn-editar-plantilla",
            btnGestionarMembretes: "#btn-gestionar-membretes",
            
            // Vistas
            vistaLista: "#vista-lista-archivos-internos",
            vistaFormulario: "#vista-editar-archivoInterno",
            contenedorLista: "#lista-archivos-internos-container",
            
            // Formulario principal
            formulario: "#formArchivoInterno",
            idDocumento: "#id_archivo_interno",
            tituloDocumento: "#titulo_archivo_interno",
            tipoDocumento: "#tipo_archivo_interno",
            contenidoDocumento: "#contenido_archivo_interno",
            clienteId: "#cliente_id",
            tituloPagina: "#titulo-pagina-archivoInterno",
            
            // Editor
            editorPrincipal: "#editor-container-archivoInterno",
            
            // Búsqueda
            inputBuscar: "#buscar-archivoInterno",
            
            // Botones de formulario
            btnCancelar: "#btn-cancel-archivoInterno",
            btnGuardar: "#btn-save-archivoInterno",
            btnPreview: "#btn-preview-archivoInterno",
            
            // Modales
            modalEliminar: "#confirmarEliminarArchivoInternoModal",
            btnConfirmarEliminar: "#btn-confirmar-eliminar-archivoInterno",
            modalPreview: "#previewArchivoInternoModal",
            previewFrame: "#preview-frame-archivoInterno",
            btnDownloadPdf: "#btn-download-pdf",
            
            // Plantilla
            modalPlantilla: "#editarPlantillaArchivoInternoModal",
            editorPlantilla: "#editor-container-plantilla",
            formularioPlantilla: "#formPlantillaArchivoInterno",
            btnGuardarPlantilla: "#btn-save-plantilla",
            btnPreviewPlantilla: "#btn-preview-plantilla",
            
            // Membretes
            modalMembretes: "#gestionarMembretesModal",
            formularioMembretes: "#formMembretes",
            headerImageInput: "#membrete_header_image",
            footerImageInput: "#membrete_footer_image",
            resetHeaderBtn: "#reset-membrete-header",
            resetFooterBtn: "#reset-membrete-footer",
            btnGuardarMembretes: "#btn-save-membretes",
            btnPreviewMembretes: "#btn-preview-membretes"
        }
    };

    // Inicializar el módulo de archivos internos
    let archivosInternosUtils;

    $(document).ready(function() {
        archivosInternosUtils = new DocumentosUtils(archivosInternosConfig);
        
        // Exponer funciones globales para compatibilidad
        window.recargarArchivosInternos = () => archivosInternosUtils.cargarDocumentos();
        window.editarArchivoInterno = (id) => archivosInternosUtils.editarDocumento(id);
        window.eliminarArchivoInterno = (id) => archivosInternosUtils.eliminarDocumento(id);
        window.editarPlantillaArchivoInterno = () => archivosInternosUtils.editarPlantilla();
        window.mostrarFormularioNuevoArchivoInterno = () => archivosInternosUtils.mostrarFormularioNuevo();
        window.mostrarVistaListaArchivosInternos = () => archivosInternosUtils.mostrarVistaLista();
        window.mostrarVistaPreviewPlantilla = () => archivosInternosUtils.mostrarVistaPreviewPlantilla();
        window.mostrarVistaPreviewMembretes = () => archivosInternosUtils.mostrarVistaPreviewMembretes();
        window.gestionarMembretes = () => archivosInternosUtils.gestionarMembretes();
    });

    // Funciones específicas para tipos de archivos internos
    function abrirModalTiposArchivosInternos() {
        cargarTiposArchivosInternosModal();
        $('#gestionarTiposArchivoInternoModal').modal('show');
    }

    function cargarTiposArchivosInternosModal() {
        $.ajax({
            url: _URL + "/ajs/archivoInterno/obtener-tipos-archivoInternos",
            method: "GET",
            dataType: 'json',
            success: function (data) {
                if (data.success && data.tipos) {
                    let html = '';
                    data.tipos.forEach(function (tipo) {
                        html += `
                            <tr>
                                <td>${tipo.nombre}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarTipoArchivoInterno(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarTipoArchivoInterno(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $("#lista-tipos-archivoInterno").html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar tipos:", error);
            }
        });
    }

    function agregarTipoArchivoInterno() {
        const nombre = $("#nuevo-tipo-archivoInterno-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/archivoInterno/insertar-tipo-archivoInterno",
            method: "POST",
            data: { nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#nuevo-tipo-archivoInterno-nombre").val('');
                    cargarTiposArchivosInternosModal();
                    archivosInternosUtils.cargarTiposSelect();
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    }

    function editarTipoArchivoInterno(id, nombre) {
        $("#editar-tipo-archivoInterno-id").val(id);
        $("#editar-tipo-archivoInterno-nombre").val(nombre);
        $("#editarTipoArchivoInternoModal").modal('show');
    }

    function guardarTipoArchivoInternoEditado() {
        const id = $("#editar-tipo-archivoInterno-id").val();
        const nombre = $("#editar-tipo-archivoInterno-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/archivoInterno/editar-tipo-archivoInterno",
            method: "POST",
            data: { id: id, nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#editarTipoArchivoInternoModal").modal('hide');
                    cargarTiposArchivosInternosModal();
                    archivosInternosUtils.cargarTiposSelect();
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    }

    function eliminarTipoArchivoInterno(id, nombre) {
        Swal.fire({
            title: '¿Está seguro?',
            text: `¿Desea eliminar el tipo "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + "/ajs/archivoInterno/eliminar-tipo-archivoInterno",
                    method: "POST",
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire('Eliminado', data.msg, 'success');
                            cargarTiposArchivosInternosModal();
                            archivosInternosUtils.cargarTiposSelect();
                        } else {
                            Swal.fire('Error', data.msg, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    }
                });
            }
        });
    }

    // Funciones para WhatsApp
    function compartirWhatsAppArchivoInterno(id) {
        console.log('Compartiendo archivo interno por WhatsApp:', id);
        window.archivoInternoActualWhatsApp = id;
        $('#compartirWhatsAppArchivoInternoModal').modal('show');
    }

    function enviarWhatsAppArchivoInterno() {
        const numero = $('#numeroWhatsAppArchivoInterno').val().trim();
        const mensaje = $('#mensajeWhatsAppArchivoInterno').val().trim();

        if (!numero) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor ingrese un número de WhatsApp',
                icon: 'error'
            });
            return;
        }

        if (!numero.match(/^[0-9]{9}$/)) {
            Swal.fire({
                title: 'Error',
                text: 'El número debe tener exactamente 9 dígitos',
                icon: 'error'
            });
            return;
        }

        Swal.fire({
            title: 'Compartiendo...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: _URL + '/ajs/archivoInterno/compartir-whatsapp',
            method: 'POST',
            data: {
                id_archivoInterno: window.archivoInternoActualWhatsApp,
                numero: numero,
                mensaje: mensaje
            },
            success: function(response) {
                if (response.res) {
                    $('#compartirWhatsAppArchivoInternoModal').modal('hide');
                    Swal.close();
                    
                    // Abrir WhatsApp en nueva ventana
                    window.open(response.whatsapp_url, '_blank');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al compartir por WhatsApp: ' + (response.error || 'Error desconocido'),
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al compartir por WhatsApp. Intente nuevamente.',
                    icon: 'error'
                });
            }
        });
    }

    // Exportar funciones globalmente
    window.compartirWhatsAppArchivoInterno = compartirWhatsAppArchivoInterno;
    window.enviarWhatsAppArchivoInterno = enviarWhatsAppArchivoInterno;

    // Funciones para manejar preview de imágenes
    function handleImagePreviewArchivo(input, numero) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(`upload-area-archivo-${numero}`).style.display = 'none';
                document.getElementById(`preview-area-archivo-${numero}`).style.display = 'block';
                document.getElementById(`preview-img-archivo-${numero}`).src = e.target.result;
                document.getElementById(`imagen${numero}_data`).value = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function clearImagePreviewArchivo(numero) {
        document.getElementById(`imagen${numero}_file`).value = '';
        document.getElementById(`imagen${numero}_data`).value = '';
        document.getElementById(`upload-area-archivo-${numero}`).style.display = 'block';
        document.getElementById(`preview-area-archivo-${numero}`).style.display = 'none';
        document.getElementById(`preview-img-archivo-${numero}`).src = '';
    }

    function showImageModalArchivo(src) {
        Swal.fire({
            imageUrl: src,
            imageAlt: 'Vista previa',
            showCloseButton: true,
            showConfirmButton: false,
            width: '80%'
        });
    }

    window.handleImagePreviewArchivo = handleImagePreviewArchivo;
    window.clearImagePreviewArchivo = clearImagePreviewArchivo;
    window.showImageModalArchivo = showImageModalArchivo;
</script>

<!-- Modal para compartir por WhatsApp -->
<div class="modal fade" id="compartirWhatsAppArchivoInternoModal" tabindex="-1" aria-labelledby="compartirWhatsAppArchivoInternoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compartirWhatsAppArchivoInternoModalLabel">
                    <i class="fab fa-whatsapp text-success me-2"></i>Compartir Archivo Interno por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="numeroWhatsAppArchivoInterno" class="form-label">
                        <i class="fas fa-phone me-1"></i>Número de WhatsApp
                    </label>
                    <input type="text" class="form-control" id="numeroWhatsAppArchivoInterno" 
                           placeholder="Ingrese el número sin +51 (ej: 999888777)" maxlength="9">
                </div>
                <div class="mb-3">
                    <label for="mensajeWhatsAppArchivoInterno" class="form-label">
                        <i class="fas fa-comment me-1"></i>Mensaje adicional (opcional)
                    </label>
                    <textarea class="form-control" id="mensajeWhatsAppArchivoInterno" rows="3" 
                              placeholder="Mensaje adicional que desee agregar..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="enviarWhatsAppArchivoInterno()">
                    <i class="fab fa-whatsapp me-1"></i>Compartir
                </button>
            </div>
        </div>
    </div>
</div>