<!-- resources/views/fragment-views/cliente/documentos/componentes/constancias.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/informes.css') ?>?v=<?= time() ?>">

<!-- Versión Desktop: Botones horizontales -->
<div class="d-none d-lg-flex mb-4 gap-2 flex-wrap">
    <button class="btn border-rojo" id="btn-lista-constancias">
        <i class="fas fa-list me-2"></i>Lista de Constancias
    </button>
    <button class="btn bg-rojo text-white" id="btn-nueva-constancia">
        <i class="fas fa-plus me-2"></i>Nueva Constancia
    </button>
    <button class="btn border-rojo" id="btn-editar-plantilla">
        <i class="fas fa-edit me-2"></i>Editar Plantilla
    </button>
    <button class="btn border-rojo" id="btn-gestionar-membretes">
        <i class="fas fa-image me-2"></i>Gestionar Membretes
    </button>
    <button class="btn bg-rojo hover:bg-white" onclick="window.constanciaModuleInstance.reiniciar()">
        <i class="fas fa-sync me-2"></i>Reiniciar Módulo
    </button>
</div>

<!-- Versión Mobile: Dropdown -->
<div class="d-lg-none mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="text-negro font-medium mb-0">Constancias</h3>
        <div class="dropdown">
            <button class="btn btn-sm border-rojo" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.mostrarVistaListaConstancias();"><i class="fas fa-list me-2 text-rojo"></i>Lista de Constancias</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.mostrarFormularioNuevoConstancia();"><i class="fas fa-plus me-2 text-rojo"></i>Nueva Constancia</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.editarPlantillaConstancia();"><i class="fas fa-edit me-2 text-rojo"></i>Editar Plantilla</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.gestionarMembretes();"><i class="fas fa-image me-2 text-rojo"></i>Gestionar Membretes</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.constanciaModuleInstance.reiniciar();"><i class="fas fa-sync me-2 text-rojo"></i>Reiniciar Módulo</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Vista de lista de constancias -->
<div id="vista-lista-constancias" class="vista active">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-negro font-medium mb-0 d-none d-lg-block">Constancias</h3>
            <div class="d-flex justify-content-end gap-2 w-100">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-rojo text-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-rojo" id="buscar-constancia" placeholder="Buscar constancias...">
                </div>
                <button class="btn border-rojo dropdown-toggle" type="button" id="dropdownFiltroConstancias"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter"></i><span class="d-none d-sm-inline ms-2">Filtrar</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="filtro-tipos-constancias" aria-labelledby="dropdownFiltroConstancias">
                    <li><h6 class="dropdown-header">Tipo de Constancia</h6></li>
                    <li><a class="dropdown-item active" href="#" data-tipo="todos">Todos</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div id="lista-constancias-container">
        <!-- Aquí se cargarán dinámicamente las constancias -->
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando constancias...</p>
        </div>
    </div>
</div>

<!-- Vista de formulario de nueva/editar constancia -->
<div id="vista-editar-constancia" class="vista">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 id="titulo-pagina-constancia" class="text-negro font-medium mb-0">Nueva Constancia</h3>
    </div>

    <form id="formConstancia" enctype="multipart/form-data">
        <input type="hidden" id="id_constancia" name="id">
        <input type="hidden" id="contenido_constancia" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">
        <input type="hidden" id="imagen1_data" name="imagen1">
        <input type="hidden" id="imagen2_data" name="imagen2">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="titulo_constancia" class="form-label">Título de la Constancia</label>
                    <input type="text" class="form-control" id="titulo_constancia" name="titulo" value="CONSTANCIA" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_constancia" class="form-label">Tipo de Constancia</label>
                    <div class="input-group">
                        <select class="form-select" id="tipo_constancia" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" id="btn-gestionar-tipos-constancia"
                            onclick="abrirModalTiposConstancias()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="form-text text-gris small">Este campo se usará para categorizar las constancias.</div>
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
            <label for="editor-container-constancia" class="form-label">Contenido de la Constancia</label>
            <div id="editor-container-constancia" class="editor-container"></div>
        </div>

        <!-- Sección de Imágenes Adicionales -->
        <div class="mb-4">
            <h6 class="fw-medium text-negro mb-3">
                <i class="fas fa-images me-2"></i>Imágenes del Informe (Opcional)
                <small class="text-muted">- Aparecerán en una segunda página</small>
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-negro">Primera Imagen</label>
                    
                    <div class="image-preview-container" id="preview-container-constancia-1" style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <input type="file" class="d-none" id="imagen1_file" name="imagen1" 
                               accept="image/png,image/jpeg,image/gif" onchange="handleImagePreviewConstancia(this, 1)">
                        
                        <div id="upload-area-constancia-1" class="upload-area" onclick="document.getElementById('imagen1_file').click()" style="cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #CA3438; margin-bottom: 15px; opacity: 0.7;"></i>
                            <div class="upload-placeholder">
                                <strong>Haz clic para seleccionar</strong><br>
                                <small>o arrastra una imagen aquí</small>
                            </div>
                        </div>
                        
                        <div id="preview-area-constancia-1" class="preview-area" style="display: none;">
                            <img id="preview-img-constancia-1" class="preview-image" style="max-width: 100%; max-height: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="image-actions mt-2" style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showImageModalConstancia(document.getElementById('preview-img-constancia-1').src)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="clearImagePreviewConstancia(1)">
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
                    
                    <div class="image-preview-container" id="preview-container-constancia-2" style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <input type="file" class="d-none" id="imagen2_file" name="imagen2" 
                               accept="image/png,image/jpeg,image/gif" onchange="handleImagePreviewConstancia(this, 2)">
                        
                        <div id="upload-area-constancia-2" class="upload-area" onclick="document.getElementById('imagen2_file').click()" style="cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #CA3438; margin-bottom: 15px; opacity: 0.7;"></i>
                            <div class="upload-placeholder">
                                <strong>Haz clic para seleccionar</strong><br>
                                <small>o arrastra una imagen aquí</small>
                            </div>
                        </div>
                        
                        <div id="preview-area-constancia-2" class="preview-area" style="display: none;">
                            <img id="preview-img-constancia-2" class="preview-image" style="max-width: 100%; max-height: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="image-actions mt-2" style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showImageModalConstancia(document.getElementById('preview-img-constancia-2').src)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="clearImagePreviewConstancia(2)">
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
            <button type="button" class="btn btn-secondary" id="btn-cancel-constancia">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn border-rojo" id="btn-preview-constancia">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-constancia">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
</div>

<!-- Modales similares a cartas pero adaptados para constancias -->
<!-- Modal para Gestionar Tipos de Constancia -->
<div class="modal fade" id="gestionarTiposConstanciaModal" tabindex="-1" aria-labelledby="gestionarTiposConstanciaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarTiposConstanciaModalLabel">Gestionar Tipos de Constancia</h5>
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
                                <label for="nuevo-tipo-constancia-nombre" class="form-label">Nombre del Tipo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuevo-tipo-constancia-nombre"
                                    placeholder="Ej: COMERCIAL, FORMAL, NOTIFICACIÓN">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn bg-rojo text-white w-100" onclick="agregarTipoConstancia()">
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
                                <tbody id="lista-tipos-constancia">
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
<div class="modal fade" id="editarTipoConstanciaModal" tabindex="-1" aria-labelledby="editarTipoConstanciaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarTipoConstanciaModalLabel">Editar Tipo de Constancia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar-tipo-constancia-id">
                <div class="mb-3">
                    <label for="editar-tipo-constancia-nombre" class="form-label">Nombre del Tipo <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editar-tipo-constancia-nombre">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="guardarTipoConstanciaEditado()">Guardar
                    Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewConstanciaModal" tabindex="-1" aria-labelledby="previewConstanciaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewConstanciaModalLabel">Vista Previa de la Constancia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-constancia" style="width: 100%; height: 600px; border: none;"></iframe>
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
<div class="modal fade" id="confirmarEliminarConstanciaModal" tabindex="-1"
    aria-labelledby="confirmarEliminarConstanciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarConstanciaModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar esta constancia? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-constancia">Eliminar</button>
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
                    <strong>Información:</strong> Las imágenes configuradas aquí se aplicarán automáticamente a todas
                    las constancias y plantillas.
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
<div class="modal fade" id="editarPlantillaConstanciaModal" tabindex="-1" aria-labelledby="editarPlantillaConstanciaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarPlantillaConstanciaModalLabel">Editar Plantilla de Constancia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPlantillaConstancia" enctype="multipart/form-data">
                    <input type="hidden" id="id_plantilla_constancia" name="id">
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
    // Función para inicializar el módulo de constancias
    function inicializarModuloConstancias() {
        console.log('Inicializando módulo de constancias...');
        
        // Limpiar instancia anterior si existe
        if (window.constanciaModuleInstance) {
            window.constanciaModuleInstance.cleanup();
        }

        // Configuración específica para constancias
        const constanciasConfig = {
            tipo: 'constancia',
            documentType: 'constancia',
            urls: {
                render: _URL + "/ajs/constancia/render",
                insertar: _URL + "/ajs/constancia/insertar",
                editar: _URL + "/ajs/constancia/editar",
                borrar: _URL + "/ajs/constancia/borrar",
                getOne: _URL + "/ajs/constancia/getOne",
                generarPDF: _URL + "/ajs/constancia/generarPDF",
                vistaPrevia: _URL + "/ajs/constancia/vista-previa",
                obtenerTemplate: _URL + "/ajs/constancia/obtener-template",
                guardarTemplate: _URL + "/ajs/constancia/guardar-template",
                obtenerMembretes: _URL + "/ajs/constancia/obtener-membretes",
                guardarMembretes: _URL + "/ajs/constancia/guardar-membretes",
                obtenerTipos: _URL + "/ajs/constancia/obtener-tipos-constancias"
            },
            elementos: {
                // Botones principales
                btnLista: "#btn-lista-constancias",
                btnNuevo: "#btn-nueva-constancia",
                btnEditarPlantilla: "#btn-editar-plantilla",
                btnGestionarMembretes: "#btn-gestionar-membretes",
                
                // Vistas
                vistaLista: "#vista-lista-constancias",
                vistaFormulario: "#vista-editar-constancia",
                contenedorLista: "#lista-constancias-container",
                
                // Formulario principal
                formulario: "#formConstancia",
                idDocumento: "#id_constancia",
                tituloDocumento: "#titulo_constancia",
                tipoDocumento: "#tipo_constancia",
                contenidoDocumento: "#contenido_constancia",
                clienteId: "#cliente_id",
                tituloPagina: "#titulo-pagina-constancia",
                
                // Editor
                editorPrincipal: "#editor-container-constancia",
                
                // Búsqueda
                inputBuscar: "#buscar-constancia",
                
                // Botones de formulario
                btnCancelar: "#btn-cancel-constancia",
                btnGuardar: "#btn-save-constancia",
                btnPreview: "#btn-preview-constancia",
                
                // Modales
                modalEliminar: "#confirmarEliminarConstanciaModal",
                btnConfirmarEliminar: "#btn-confirmar-eliminar-constancia",
                modalPreview: "#previewConstanciaModal",
                previewFrame: "#preview-frame-constancia",
                btnDownloadPdf: "#btn-download-pdf",
                
                // Plantilla
                modalPlantilla: "#editarPlantillaConstanciaModal",
                editorPlantilla: "#editor-container-plantilla",
                formularioPlantilla: "#formPlantillaConstancia",
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

        // Crear nueva instancia
        window.constanciasUtils = new DocumentosUtils(constanciasConfig);
        
        // Exponer funciones globales para compatibilidad
        window.recargarConstancias = () => window.constanciasUtils.cargarDocumentos();
        window.editarConstancia = (id) => window.constanciasUtils.editarDocumento(id);
        window.eliminarConstancia = (id) => window.constanciasUtils.eliminarDocumento(id);
        window.editarPlantillaConstancia = () => window.constanciasUtils.editarPlantilla();
        window.mostrarFormularioNuevoConstancia = () => window.constanciasUtils.mostrarFormularioNuevo();
        window.mostrarVistaListaConstancias = () => window.constanciasUtils.mostrarVistaLista();
        window.mostrarVistaPreviewPlantilla = () => window.constanciasUtils.mostrarVistaPreviewPlantilla();
        window.mostrarVistaPreviewMembretes = () => window.constanciasUtils.mostrarVistaPreviewMembretes();
        window.gestionarMembretes = () => window.constanciasUtils.gestionarMembretes();
        
        console.log('Módulo de constancias inicializado correctamente');
    }

    // Exponer la función de inicialización globalmente
    window.inicializarModuloConstancias = inicializarModuloConstancias;

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        // Solo inicializar si estamos en la página correcta
        if (window.location.pathname.includes('constancias') || $('#vista-lista-constancias').length > 0) {
            inicializarModuloConstancias();
            setTimeout(cargarFiltrosConstancias, 400);
        }
    });

    // Cargar filtros de tipo
    function cargarFiltrosConstancias() {
        if (!window.constanciasUtils) { setTimeout(cargarFiltrosConstancias, 300); return; }
        $.ajax({
            url: _URL + "/ajs/constancia/obtener-tipos-constancias",
            method: "GET",
            dataType: 'json',
            success: function (data) {
                if (data.success && data.tipos) {
                    let html = '<li><h6 class="dropdown-header">Tipo de Constancia</h6></li>';
                    html += '<li><a class="dropdown-item active" href="#" data-tipo="todos">Todos</a></li>';
                    data.tipos.forEach(function (tipo) {
                        html += '<li><a class="dropdown-item" href="#" data-tipo="' + tipo.nombre + '">' + tipo.nombre + '</a></li>';
                    });
                    $("#filtro-tipos-constancias").html(html);
                    $("#filtro-tipos-constancias .dropdown-item").on("click", function (e) {
                        e.preventDefault();
                        $("#filtro-tipos-constancias .dropdown-item").removeClass("active");
                        $(this).addClass("active");
                        const tipo = $(this).data("tipo");
                        if (window.constanciasUtils) {
                            window.constanciasUtils.filtroTipo = tipo;
                            window.constanciasUtils.cargarDocumentos();
                        }
                    });
                }
            }
        });
    }

    // Funciones específicas para tipos de constancias
    function abrirModalTiposConstancias() {
        cargarTiposConstanciasModal();
        $('#gestionarTiposConstanciaModal').modal('show');
    }

    function cargarTiposConstanciasModal() {
        $.ajax({
            url: _URL + "/ajs/constancia/obtener-tipos-constancias",
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
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarTipoConstancia(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarTipoConstancia(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $("#lista-tipos-constancia").html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar tipos:", error);
            }
        });
    }

    function agregarTipoConstancia() {
        const nombre = $("#nuevo-tipo-constancia-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/constancia/insertar-tipo-constancia",
            method: "POST",
            data: { nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#nuevo-tipo-constancia-nombre").val('');
                    cargarTiposConstanciasModal();
                    if (window.constanciasUtils) {
                        window.constanciasUtils.cargarTiposSelect();
                    }
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    }

    function editarTipoConstancia(id, nombre) {
        $("#editar-tipo-constancia-id").val(id);
        $("#editar-tipo-constancia-nombre").val(nombre);
        $("#editarTipoConstanciaModal").modal('show');
    }

    function guardarTipoConstanciaEditado() {
        const id = $("#editar-tipo-constancia-id").val();
        const nombre = $("#editar-tipo-constancia-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/constancia/editar-tipo-constancia",
            method: "POST",
            data: { id: id, nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#editarTipoConstanciaModal").modal('hide');
                    cargarTiposConstanciasModal();
                    if (window.constanciasUtils) {
                        window.constanciasUtils.cargarTiposSelect();
                    }
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    }

    function eliminarTipoConstancia(id, nombre) {
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
                    url: _URL + "/ajs/constancia/eliminar-tipo-constancia",
                    method: "POST",
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire('Eliminado', data.msg, 'success');
                            cargarTiposConstanciasModal();
                            if (window.constanciasUtils) {
                                window.constanciasUtils.cargarTiposSelect();
                            }
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
    function compartirWhatsAppConstancia(id) {
        console.log('Compartiendo constancia por WhatsApp:', id);
        window.constanciaActualWhatsApp = id;
        $('#compartirWhatsAppConstanciaModal').modal('show');
    }

    function enviarWhatsAppConstancia() {
        const numero = $('#numeroWhatsAppConstancia').val().trim();
        const mensaje = $('#mensajeWhatsAppConstancia').val().trim();

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
            url: _URL + '/ajs/constancia/compartir-whatsapp',
            method: 'POST',
            data: {
                id_constancia: window.constanciaActualWhatsApp,
                numero: numero,
                mensaje: mensaje
            },
            success: function(response) {
                if (response.res) {
                    $('#compartirWhatsAppConstanciaModal').modal('hide');
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
    window.compartirWhatsAppConstancia = compartirWhatsAppConstancia;
    window.enviarWhatsAppConstancia = enviarWhatsAppConstancia;

    // Funciones para manejar preview de imágenes
    function handleImagePreviewConstancia(input, numero) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(`upload-area-constancia-${numero}`).style.display = 'none';
                document.getElementById(`preview-area-constancia-${numero}`).style.display = 'block';
                document.getElementById(`preview-img-constancia-${numero}`).src = e.target.result;
                document.getElementById(`imagen${numero}_data`).value = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function clearImagePreviewConstancia(numero) {
        document.getElementById(`imagen${numero}_file`).value = '';
        document.getElementById(`imagen${numero}_data`).value = '';
        document.getElementById(`upload-area-constancia-${numero}`).style.display = 'block';
        document.getElementById(`preview-area-constancia-${numero}`).style.display = 'none';
        document.getElementById(`preview-img-constancia-${numero}`).src = '';
    }

    function showImageModalConstancia(src) {
        Swal.fire({
            imageUrl: src,
            imageAlt: 'Vista previa',
            showCloseButton: true,
            showConfirmButton: false,
            width: '80%'
        });
    }

    window.handleImagePreviewConstancia = handleImagePreviewConstancia;
    window.clearImagePreviewConstancia = clearImagePreviewConstancia;
    window.showImageModalConstancia = showImageModalConstancia;
</script>

<!-- Modal para compartir por WhatsApp -->
<div class="modal fade" id="compartirWhatsAppConstanciaModal" tabindex="-1" aria-labelledby="compartirWhatsAppConstanciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compartirWhatsAppConstanciaModalLabel">
                    <i class="fab fa-whatsapp text-success me-2"></i>Compartir Constancia por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="numeroWhatsAppConstancia" class="form-label">
                        <i class="fas fa-phone me-1"></i>Número de WhatsApp
                    </label>
                    <input type="text" class="form-control" id="numeroWhatsAppConstancia" 
                           placeholder="Ingrese el número sin +51 (ej: 999888777)" maxlength="9">
                </div>
                <div class="mb-3">
                    <label for="mensajeWhatsAppConstancia" class="form-label">
                        <i class="fas fa-comment me-1"></i>Mensaje adicional (opcional)
                    </label>
                    <textarea class="form-control" id="mensajeWhatsAppConstancia" rows="3" 
                              placeholder="Mensaje adicional que desee agregar...">Estimado(a), reciba un cordial saludo. Le comparto este documento para su revisión.</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="enviarWhatsAppConstancia()">
                    <i class="fab fa-whatsapp me-1"></i>Compartir
                </button>
            </div>
        </div>
    </div>
</div>