<!-- resources/views/fragment-views/cliente/documentos/componentes/cartas.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/informes.css') ?>?v=<?= time() ?>">

<!-- Versión Desktop: Botones horizontales -->
<div class="d-none d-lg-flex mb-4 gap-2 flex-wrap">
    <button class="btn border-rojo" id="btn-lista-cartas">
        <i class="fas fa-list me-2"></i>Lista de Cartas
    </button>
    <button class="btn bg-rojo text-white" id="btn-nueva-carta">
        <i class="fas fa-plus me-2"></i>Nueva Carta
    </button>
    <button class="btn border-rojo" id="btn-editar-plantilla">
        <i class="fas fa-edit me-2"></i>Editar Plantilla
    </button>
    <button class="btn border-rojo" id="btn-gestionar-membretes">
        <i class="fas fa-image me-2"></i>Gestionar Membretes
    </button>
    <button class="btn bg-rojo hover:bg-white" onclick="window.cartaModuleInstance.reiniciar()">
        <i class="fas fa-sync me-2"></i>Reiniciar Módulo
    </button>
</div>

<!-- Versión Mobile: Dropdown -->
<div class="d-lg-none mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="text-negro font-medium mb-0">Cartas</h3>
        <div class="dropdown">
            <button class="btn btn-sm border-rojo" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.mostrarVistaListaCartas();"><i class="fas fa-list me-2 text-rojo"></i>Lista de Cartas</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.mostrarFormularioNuevoCarta();"><i class="fas fa-plus me-2 text-rojo"></i>Nueva Carta</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.editarPlantillaCarta();"><i class="fas fa-edit me-2 text-rojo"></i>Editar Plantilla</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.gestionarMembretes();"><i class="fas fa-image me-2 text-rojo"></i>Gestionar Membretes</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); window.cartaModuleInstance.reiniciar();"><i class="fas fa-sync me-2 text-rojo"></i>Reiniciar Módulo</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Vista de lista de cartas -->
<div id="vista-lista-cartas" class="vista active">
    <!-- Título con menú móvil y búsqueda -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-negro font-medium mb-0 d-none d-lg-block">Cartas</h3>
            <div class="d-flex justify-content-end gap-2 w-100">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-rojo text-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-rojo" id="buscar-carta" placeholder="Buscar cartas...">
                </div>
                <button class="btn border-rojo dropdown-toggle" type="button" id="dropdownFiltroCartas"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter"></i><span class="d-none d-sm-inline ms-2">Filtrar</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="filtro-tipos-cartas" aria-labelledby="dropdownFiltroCartas">
                    <li><h6 class="dropdown-header">Tipo de Carta</h6></li>
                    <li><a class="dropdown-item active" href="#" data-tipo="todos">Todos</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div id="lista-cartas-container">
        <!-- Aquí se cargarán dinámicamente las cartas -->
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando cartas...</p>
        </div>
    </div>
</div>

<!-- Vista de formulario de nueva/editar carta -->
<div id="vista-editar-carta" class="vista">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 id="titulo-pagina-carta" class="text-negro font-medium mb-0">Nueva Carta</h3>
    </div>

    <form id="formCarta" enctype="multipart/form-data">
        <input type="hidden" id="id_carta" name="id">
        <input type="hidden" id="contenido_carta" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">
        <input type="hidden" id="imagen1_data" name="imagen1">
        <input type="hidden" id="imagen2_data" name="imagen2">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="titulo_carta" class="form-label">Título de la Carta</label>
                    <input type="text" class="form-control" id="titulo_carta" name="titulo" value="CARTA" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_carta" class="form-label">Tipo de Carta</label>
                    <div class="input-group">
                        <select class="form-select" id="tipo_carta" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" id="btn-gestionar-tipos-carta"
                            onclick="abrirModalTiposCartas()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="form-text text-gris small">Este campo se usará para categorizar las cartas.</div>
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
            <label for="editor-container-carta" class="form-label">Contenido de la Carta</label>
            <div id="editor-container-carta" class="editor-container"></div>
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
                    
                    <div class="image-preview-container" id="preview-container-carta-1" style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <input type="file" class="d-none" id="imagen1_file" name="imagen1" 
                               accept="image/png,image/jpeg,image/gif" onchange="handleImagePreviewCarta(this, 1)">
                        
                        <div id="upload-area-carta-1" class="upload-area" onclick="document.getElementById('imagen1_file').click()" style="cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #CA3438; margin-bottom: 15px; opacity: 0.7;"></i>
                            <div class="upload-placeholder">
                                <strong>Haz clic para seleccionar</strong><br>
                                <small>o arrastra una imagen aquí</small>
                            </div>
                        </div>
                        
                        <div id="preview-area-carta-1" class="preview-area" style="display: none;">
                            <img id="preview-img-carta-1" class="preview-image" style="max-width: 100%; max-height: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="image-actions mt-2" style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showImageModalCarta(document.getElementById('preview-img-carta-1').src)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="clearImagePreviewCarta(1)">
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
                    
                    <div class="image-preview-container" id="preview-container-carta-2" style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <input type="file" class="d-none" id="imagen2_file" name="imagen2" 
                               accept="image/png,image/jpeg,image/gif" onchange="handleImagePreviewCarta(this, 2)">
                        
                        <div id="upload-area-carta-2" class="upload-area" onclick="document.getElementById('imagen2_file').click()" style="cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #CA3438; margin-bottom: 15px; opacity: 0.7;"></i>
                            <div class="upload-placeholder">
                                <strong>Haz clic para seleccionar</strong><br>
                                <small>o arrastra una imagen aquí</small>
                            </div>
                        </div>
                        
                        <div id="preview-area-carta-2" class="preview-area" style="display: none;">
                            <img id="preview-img-carta-2" class="preview-image" style="max-width: 100%; max-height: 120px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="image-actions mt-2" style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showImageModalCarta(document.getElementById('preview-img-carta-2').src)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="clearImagePreviewCarta(2)">
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
            <button type="button" class="btn btn-secondary" id="btn-cancel-carta">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn border-rojo" id="btn-preview-carta">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-carta">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
</div>

<!-- Modal para Gestionar Tipos de Carta -->
<div class="modal fade" id="gestionarTiposCartaModal" tabindex="-1" aria-labelledby="gestionarTiposCartaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarTiposCartaModalLabel">Gestionar Tipos de Carta</h5>
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
                                <label for="nuevo-tipo-carta-nombre" class="form-label">Nombre del Tipo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuevo-tipo-carta-nombre"
                                    placeholder="Ej: COMERCIAL, FORMAL, NOTIFICACIÓN">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn bg-rojo text-white w-100" onclick="agregarTipoCarta()">
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
                                <tbody id="lista-tipos-carta">
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
<div class="modal fade" id="editarTipoCartaModal" tabindex="-1" aria-labelledby="editarTipoCartaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarTipoCartaModalLabel">Editar Tipo de Carta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar-tipo-carta-id">
                <div class="mb-3">
                    <label for="editar-tipo-carta-nombre" class="form-label">Nombre del Tipo <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editar-tipo-carta-nombre">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="guardarTipoCartaEditado()">Guardar
                    Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewCartaModal" tabindex="-1" aria-labelledby="previewCartaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewCartaModalLabel">Vista Previa de la Carta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-carta" style="width: 100%; height: 600px; border: none;"></iframe>
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
<div class="modal fade" id="confirmarEliminarCartaModal" tabindex="-1"
    aria-labelledby="confirmarEliminarCartaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarCartaModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar esta carta? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-carta">Eliminar</button>
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
                    las cartas y plantillas.
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
<div class="modal fade" id="editarPlantillaCartaModal" tabindex="-1" aria-labelledby="editarPlantillaCartaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarPlantillaCartaModalLabel">Editar Plantilla de Carta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPlantillaCarta" enctype="multipart/form-data">
                    <input type="hidden" id="id_plantilla_carta" name="id">
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

<!-- Cargar PDF.js y las utilidades una sola vez, incluso al reinsertar este fragmento. -->
<script>
    (function prepararDependenciasCartas() {
        const pdfJsUrl = '<?= URL::to('public/lib/pdfjs/pdf.min.js') ?>';
        const pdfWorkerUrl = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
        const documentosUtilsUrl = '<?= URL::to('public/js/modulo-documentos/utils.js') ?>';

        function cargarScriptUnaVez(id, src, estaDisponible) {
            if (estaDisponible()) {
                return Promise.resolve();
            }

            return new Promise((resolve, reject) => {
                let script = document.getElementById(id)
                    || Array.from(document.scripts).find(item => item.src && item.src.split('?')[0] === src.split('?')[0]);

                const resolver = () => estaDisponible()
                    ? resolve()
                    : reject(new Error(`El script ${src} terminó sin exponer su API`));

                if (!script) {
                    script = document.createElement('script');
                    script.id = id;
                    script.src = src;
                    document.head.appendChild(script);
                }

                script.addEventListener('load', resolver, { once: true });
                script.addEventListener('error', () => reject(new Error(`No se pudo cargar ${src}`)), { once: true });
            });
        }

        if (!window.pdfJsReady) {
            window.pdfJsReady = cargarScriptUnaVez(
                'pdfjs-local-script',
                pdfJsUrl,
                () => Boolean(window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function')
            ).then(() => {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;
                return window.pdfjsLib;
            });
        }

        if (!window.documentosUtilsReady) {
            window.documentosUtilsReady = cargarScriptUnaVez(
                'documentos-utils-script',
                documentosUtilsUrl,
                () => typeof window.DocumentosUtils === 'function'
            ).then(() => window.DocumentosUtils);
        }

        // El módulo puede listar cartas aunque falle PDF.js; cada tarjeta mostrará su enlace alternativo.
        window.cartasAssetsReady = window.documentosUtilsReady;
    })();
</script>

<script>
    // Función para inicializar el módulo de cartas - SIMPLIFICADA
    async function inicializarModuloCartas() {
        console.log('Inicializando módulo de cartas...');

        const rootElement = document.getElementById('vista-lista-cartas');
        if (!rootElement) {
            return null;
        }

        if (window.cartaModuleInstance
            && !window.cartaModuleInstance.destroyed
            && window.cartaModuleInstance.rootElement === rootElement) {
            return window.cartaModuleInstance;
        }

        if (window.cartasInitializationPromise && window.cartasInitializationRoot === rootElement) {
            return window.cartasInitializationPromise;
        }

        window.cartasInitializationRoot = rootElement;
        window.cartasInitializationPromise = (async () => {
            await window.cartasAssetsReady;

        // Limpiar instancia anterior si existe
        if (window.cartaModuleInstance) {
            console.log('Limpiando instancia anterior de cartas...');
            if (!window.cartaModuleInstance.destroyed && typeof window.cartaModuleInstance.cleanup === 'function') {
                window.cartaModuleInstance.cleanup();
            }
            window.cartaModuleInstance = null;
        }

        // Verificar que DocumentosUtils esté disponible
        if (typeof window.DocumentosUtils !== 'function') {
            console.error('DocumentosUtils no está disponible');
            throw new Error('DocumentosUtils no está disponible');
        }

        try {
            // Configuración específica para cartas - DIRECTA
            const cartasConfig = {
                tipo: 'carta',
                documentType: 'carta',
                reinicializar: () => window.inicializarModuloCartas(),
                urls: {
                    render: _URL + "/ajs/carta/render",
                    insertar: _URL + "/ajs/carta/insertar",
                    editar: _URL + "/ajs/carta/editar",
                    borrar: _URL + "/ajs/carta/borrar",
                    getOne: _URL + "/ajs/carta/getOne",
                    generarPDF: _URL + "/ajs/carta/generarPDF",
                    vistaPrevia: _URL + "/ajs/carta/vista-previa",
                    obtenerTemplate: _URL + "/ajs/carta/obtener-template",
                    guardarTemplate: _URL + "/ajs/carta/guardar-template",
                    obtenerMembretes: _URL + "/ajs/carta/obtener-membretes",
                    guardarMembretes: _URL + "/ajs/carta/guardar-membretes",
                    obtenerTipos: _URL + "/ajs/carta/obtener-tipos-cartas"
                },
                elementos: {
                    // Botones principales
                    btnLista: "#btn-lista-cartas",
                    btnNuevo: "#btn-nueva-carta",
                    btnEditarPlantilla: "#btn-editar-plantilla",
                    btnGestionarMembretes: "#btn-gestionar-membretes",
                    
                    // Vistas
                    vistaLista: "#vista-lista-cartas",
                    vistaFormulario: "#vista-editar-carta",
                    contenedorLista: "#lista-cartas-container",
                    
                    // Formulario principal
                    formulario: "#formCarta",
                    idDocumento: "#id_carta",
                    tituloDocumento: "#titulo_carta",
                    tipoDocumento: "#tipo_carta",
                    contenidoDocumento: "#contenido_carta",
                    clienteId: "#cliente_id",
                    tituloPagina: "#titulo-pagina-carta",
                    
                    // Editor
                    editorPrincipal: "#editor-container-carta",
                    
                    // Búsqueda
                    inputBuscar: "#buscar-carta",
                    
                    // Botones de formulario
                    btnCancelar: "#btn-cancel-carta",
                    btnGuardar: "#btn-save-carta",
                    btnPreview: "#btn-preview-carta",
                    
                    // Modales
                    modalEliminar: "#confirmarEliminarCartaModal",
                    btnConfirmarEliminar: "#btn-confirmar-eliminar-carta",
                    modalPreview: "#previewCartaModal",
                    previewFrame: "#preview-frame-carta",
                    btnDownloadPdf: "#btn-download-pdf",
                    
                    // Plantilla
                    modalPlantilla: "#editarPlantillaCartaModal",
                    editorPlantilla: "#editor-container-plantilla",
                    formularioPlantilla: "#formPlantillaCarta",
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
            cartasConfig.rootElement = rootElement;
            cartasConfig.modalesPropios = [
                '#gestionarTiposCartaModal',
                '#editarTipoCartaModal',
                '#previewCartaModal',
                '#confirmarEliminarCartaModal',
                '#gestionarMembretesModal',
                '#editarPlantillaCartaModal',
                '#compartirWhatsAppCartaModal'
            ];
            window.cartaModuleInstance = new DocumentosUtils(cartasConfig);
            
            // Exponer funciones globales para compatibilidad
            window.recargarCartas = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.cargarDocumentos();
                }
            };
            
            window.editarCarta = (id) => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.editarDocumento(id);
                }
            };
            
            window.eliminarCarta = (id) => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.eliminarDocumento(id);
                }
            };
            
            window.editarPlantillaCarta = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.editarPlantilla();
                }
            };
            
            window.mostrarFormularioNuevoCarta = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.mostrarFormularioNuevo();
                }
            };
            
            window.mostrarVistaListaCartas = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.mostrarVistaLista();
                }
            };
            
            window.mostrarVistaPreviewPlantilla = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.mostrarVistaPreviewPlantilla();
                }
            };
            
            window.mostrarVistaPreviewMembretes = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.mostrarVistaPreviewMembretes();
                }
            };
            
            window.gestionarMembretes = () => {
                if (window.cartaModuleInstance) {
                    window.cartaModuleInstance.gestionarMembretes();
                }
            };
            
            console.log('Módulo de cartas inicializado correctamente');
            cargarFiltrosCartas();
            return window.cartaModuleInstance;
        } catch (error) {
            console.error('Error al inicializar módulo de cartas:', error);
            throw error;
        }
        })();

        try {
            return await window.cartasInitializationPromise;
        } catch (error) {
            window.cartasInitializationPromise = null;
            window.cartasInitializationRoot = null;
            $('#lista-cartas-container').html(`
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No se pudo iniciar el visor de cartas. Recargue la página para intentarlo nuevamente.
                </div>
            `);
            throw error;
        }
    }

    // Exponer la función de inicialización globalmente
    window.inicializarModuloCartas = inicializarModuloCartas;

    // Cargar filtros de tipo
    function cargarFiltrosCartas() {
        if (!window.cartaModuleInstance) return;
        $.ajax({
            url: _URL + "/ajs/carta/obtener-tipos-cartas",
            method: "GET",
            dataType: 'json',
            success: function (data) {
                if (data.success && data.tipos) {
                    let html = '<li><h6 class="dropdown-header">Tipo de Carta</h6></li>';
                    html += '<li><a class="dropdown-item active" href="#" data-tipo="todos">Todos</a></li>';
                    data.tipos.forEach(function (tipo) {
                        html += '<li><a class="dropdown-item" href="#" data-tipo="' + tipo.nombre + '">' + tipo.nombre + '</a></li>';
                    });
                    $("#filtro-tipos-cartas").html(html);
                    $("#filtro-tipos-cartas .dropdown-item")
                        .off("click.cartas")
                        .on("click.cartas", function (e) {
                        e.preventDefault();
                        $("#filtro-tipos-cartas .dropdown-item").removeClass("active");
                        $(this).addClass("active");
                        const tipo = $(this).data("tipo");
                        if (window.cartaModuleInstance) {
                            window.cartaModuleInstance.filtroTipo = tipo;
                            window.cartaModuleInstance.cargarDocumentos();
                        }
                        });
                }
            }
        });
    }

    // Funciones específicas para tipos de cartas
    function abrirModalTiposCartas() {
        cargarTiposCartasModal();
        $('#gestionarTiposCartaModal').modal('show');
    }

    function cargarTiposCartasModal() {
        $.ajax({
            url: _URL + "/ajs/carta/obtener-tipos-cartas",
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
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarTipoCarta(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarTipoCarta(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $("#lista-tipos-carta").html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar tipos:", error);
            }
        });
    }

    function agregarTipoCarta() {
        const nombre = $("#nuevo-tipo-carta-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/carta/insertar-tipo-carta",
            method: "POST",
            data: { nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#nuevo-tipo-carta-nombre").val('');
                    cargarTiposCartasModal();
                    if (window.cartaModuleInstance && window.cartaModuleInstance.cargarTiposSelect) {
                        window.cartaModuleInstance.cargarTiposSelect();
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

    function editarTipoCarta(id, nombre) {
        $("#editar-tipo-carta-id").val(id);
        $("#editar-tipo-carta-nombre").val(nombre);
        $("#editarTipoCartaModal").modal('show');
    }

    function guardarTipoCartaEditado() {
        const id = $("#editar-tipo-carta-id").val();
        const nombre = $("#editar-tipo-carta-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/carta/editar-tipo-carta",
            method: "POST",
            data: { id: id, nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#editarTipoCartaModal").modal('hide');
                    cargarTiposCartasModal();
                    if (window.cartaModuleInstance && window.cartaModuleInstance.cargarTiposSelect) {
                        window.cartaModuleInstance.cargarTiposSelect();
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

    function eliminarTipoCarta(id, nombre) {
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
                    url: _URL + "/ajs/carta/eliminar-tipo-carta",
                    method: "POST",
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire('Eliminado', data.msg, 'success');
                            cargarTiposCartasModal();
                            if (window.cartaModuleInstance && window.cartaModuleInstance.cargarTiposSelect) {
                                window.cartaModuleInstance.cargarTiposSelect();
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
    function compartirWhatsAppCarta(id) {
        console.log('Compartiendo carta por WhatsApp:', id);
        window.cartaActualWhatsApp = id;
        $('#compartirWhatsAppCartaModal').modal('show');
    }

    function enviarWhatsAppCarta() {
        const numero = $('#numeroWhatsAppCarta').val().trim();
        const mensaje = $('#mensajeWhatsAppCarta').val().trim();

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
            url: _URL + '/ajs/carta/compartir-whatsapp',
            method: 'POST',
            data: {
                id_carta: window.cartaActualWhatsApp,
                numero: numero,
                mensaje: mensaje
            },
            success: function(response) {
                if (response.res) {
                    $('#compartirWhatsAppCartaModal').modal('hide');
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
    window.compartirWhatsAppCarta = compartirWhatsAppCarta;
    window.enviarWhatsAppCarta = enviarWhatsAppCarta;

    // Funciones para manejar preview de imágenes
    function handleImagePreviewCarta(input, numero) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(`upload-area-carta-${numero}`).style.display = 'none';
                document.getElementById(`preview-area-carta-${numero}`).style.display = 'block';
                document.getElementById(`preview-img-carta-${numero}`).src = e.target.result;
                document.getElementById(`imagen${numero}_data`).value = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function clearImagePreviewCarta(numero) {
        document.getElementById(`imagen${numero}_file`).value = '';
        document.getElementById(`imagen${numero}_data`).value = '';
        document.getElementById(`upload-area-carta-${numero}`).style.display = 'block';
        document.getElementById(`preview-area-carta-${numero}`).style.display = 'none';
        document.getElementById(`preview-img-carta-${numero}`).src = '';
    }

    function showImageModalCarta(src) {
        Swal.fire({
            imageUrl: src,
            imageAlt: 'Vista previa',
            showCloseButton: true,
            showConfirmButton: false,
            width: '80%'
        });
    }

    window.handleImagePreviewCarta = handleImagePreviewCarta;
    window.clearImagePreviewCarta = clearImagePreviewCarta;
    window.showImageModalCarta = showImageModalCarta;
</script>

<!-- Modal para compartir por WhatsApp -->
<div class="modal fade" id="compartirWhatsAppCartaModal" tabindex="-1" aria-labelledby="compartirWhatsAppCartaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compartirWhatsAppCartaModalLabel">
                    <i class="fab fa-whatsapp text-success me-2"></i>Compartir Carta por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="numeroWhatsAppCarta" class="form-label">
                        <i class="fas fa-phone me-1"></i>Número de WhatsApp
                    </label>
                    <input type="text" class="form-control" id="numeroWhatsAppCarta" 
                           placeholder="Ingrese el número sin +51 (ej: 999888777)" maxlength="9">
                </div>
                <div class="mb-3">
                    <label for="mensajeWhatsAppCarta" class="form-label">
                        <i class="fas fa-comment me-1"></i>Mensaje adicional (opcional)
                    </label>
                    <textarea class="form-control" id="mensajeWhatsAppCarta" rows="3" 
                              placeholder="Mensaje adicional que desee agregar...">Estimado(a), reciba un cordial saludo. Le comparto este documento para su revisión.</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="enviarWhatsAppCarta()">
                    <i class="fab fa-whatsapp me-1"></i>Compartir
                </button>
            </div>
        </div>
    </div>
</div>
