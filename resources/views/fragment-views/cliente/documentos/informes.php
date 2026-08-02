<!-- resources\views\fragment-views\cliente\documentos\informes.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/informes.css') ?>?v=<?= time() ?>">

<!-- Añadir PDF.js para la vista previa de documentos -->
<script src="<?= URL::to('public/lib/pdfjs/pdf.min.js') ?>"></script>
<script>
    // Configurar PDF.js worker solo una vez
    (function () {
        if (typeof pdfjsLib !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
        }
    })();
</script>

<div class="tab-content" id="informesTabsContent">
    <!-- Navegación entre Lista y Nuevo Informe -->
    
    <!-- Versión Desktop: Botones horizontales -->
    <div class="d-none d-lg-flex mb-4 gap-2 flex-wrap">
        <button class="btn border-rojo"
            onclick="$('#lista-informes').addClass('show active'); $('#nuevo-informe, #editar-informe').removeClass('show active');">
            <i class="fas fa-list me-2"></i>Lista de Informes
        </button>
        <button class="btn bg-rojo text-white" onclick="mostrarFormularioNuevoInforme()">
            <i class="fas fa-plus me-2"></i>Nuevo Informe
        </button>
        <button class="btn border-rojo" onclick="$('#editarPlantillaInformeModal').modal('show')">
            <i class="fas fa-edit me-2"></i>Editar Plantilla
        </button>
        <button id="btn-gestionar-membretes" class="btn border-rojo">
            <i class="fas fa-image me-2"></i>Gestionar Membretes
        </button>
        <button class="btn bg-rojo hover:bg-white" onclick="window.InformesModule.reiniciar()">
            <i class="fas fa-sync me-2"></i>Reiniciar Módulo
        </button>
    </div>

    </div>
    <!-- Lista de Informes -->
    <div class="tab-pane fade show active" id="lista-informes" role="tabpanel">
        <!-- Título con menú móvil y búsqueda -->
        <div class="mb-4">
            <!-- Título con dropdown móvil -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-negro font-medium mb-0">Informes</h3>
                <!-- Dropdown móvil al lado del título -->
                <div class="dropdown d-lg-none">
                    <button class="btn btn-sm border-rojo" type="button" id="dropdownMenuInformes" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuInformes">
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); $('#lista-informes').addClass('show active'); $('#nuevo-informe, #editar-informe').removeClass('show active');">
                                <i class="fas fa-list me-2 text-rojo"></i>Lista de Informes
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); mostrarFormularioNuevoInforme();">
                                <i class="fas fa-plus me-2 text-rojo"></i>Nuevo Informe
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); $('#editarPlantillaInformeModal').modal('show');">
                                <i class="fas fa-edit me-2 text-rojo"></i>Editar Plantilla
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); $('#gestionarMembretesInformeModal').modal('show');">
                                <i class="fas fa-image me-2 text-rojo"></i>Gestionar Membretes
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); window.InformesModule.reiniciar();">
                                <i class="fas fa-sync me-2 text-rojo"></i>Reiniciar Módulo
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Búsqueda y filtro -->
            <div class="d-flex justify-content-end gap-2">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-rojo text-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-rojo" id="buscar-informe"
                        placeholder="Buscar informes..." onkeyup="buscarInformes()">
                </div>
                <button class="btn border-rojo dropdown-toggle" type="button" id="dropdownFiltro"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter"></i><span class="d-none d-sm-inline ms-2">Filtrar</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="filtro-tipos" aria-labelledby="dropdownFiltro">
                    <li>
                        <h6 class="dropdown-header">Tipo de Informe</h6>
                    </li>
                    <li><a class="dropdown-item" href="#" data-tipo="todos">Todos</a></li>
                    <!-- Se cargarán dinámicamente los tipos de informes -->
                </ul>
            </div>
        </div>

        <!-- Info de paginación -->
        <div id="info-paginacion-container" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted" id="info-paginacion-texto">
                    <!-- La información de paginación se cargará aquí -->
                </small>
            </div>
        </div>

        <!-- Grid de informes -->
        <div class="row row-cols-1 row-cols-md-3 g-4" id="lista-informes-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-rojo" role="status">
                    <span class="visually-hidden">Cargando informes...</span>
                </div>
                <p class="mt-2 text-gris">Cargando informes...</p>
            </div>
        </div>

        <!-- Controles de paginación -->
        <div id="paginacion-container" style="display: none;">
            <nav aria-label="Paginación de informes" class="mt-4">
                <ul class="pagination justify-content-center" id="paginacion-lista">
                    <!-- Los controles de paginación se cargarán aquí -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Nuevo Informe -->
    <div class="tab-pane fade" id="nuevo-informe" role="tabpanel">
        <!-- Se cargará dinámicamente -->
    </div>

    <!-- Editar Informe -->
    <div class="tab-pane fade" id="editar-informe" role="tabpanel">
        <!-- Se cargará dinámicamente -->
    </div>

    <!-- Editar Plantilla -->
    <div class="tab-pane fade" id="editar-plantilla" role="tabpanel">
        <!-- Se cargará dinámicamente -->
    </div>
</div>
<!-- Modal para Editar Plantilla -->
<div class="modal fade" id="editarPlantillaInformeModal" tabindex="-1"
    aria-labelledby="editarPlantillaInformeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarPlantillaInformeModalLabel">Editar Plantilla de Informes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formInformeTemplate" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="titulo_template" class="form-label fw-medium text-negro">Título por Defecto
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo_template"
                                name="titulo" value="" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-negro">Contenido por Defecto</label>
                            <!-- Contenedor para el editor -->
                            <div id="editor-container-template"
                                style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                <!-- El editor Quill se cargará aquí -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btn-preview-template" class="btn border-rojo">
                    <i class="fas fa-eye me-2"></i>Vista Previa
                </button>
                <button type="button" id="btn-save-template" class="btn bg-rojo text-white">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Informe -->
<div class="modal fade" id="editarInformeModal" tabindex="-1" aria-labelledby="editarInformeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarInformeModalLabel">Editar Informe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-editar-informe-content">
                    <!-- El contenido del formulario se cargará aquí dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btn-preview-informe-modal" class="btn border-rojo">
                    <i class="fas fa-eye me-2"></i>Vista Previa
                </button>
                <button type="button" id="btn-save-informe-modal" class="btn bg-rojo text-white">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Gestionar Tipos de Informe -->
<div class="modal fade" id="gestionarTiposInformeModal" tabindex="-1" aria-labelledby="gestionarTiposInformeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarTiposInformeModalLabel">Gestionar Tipos de Informe</h5>
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
                                <label for="nuevo-tipo-nombre" class="form-label">Nombre del Tipo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuevo-tipo-nombre"
                                    placeholder="Ej: TÉCNICO, PAGO, SERVICIO">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn bg-rojo text-white w-100"
                                    onclick="agregarTipoInforme()">
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
                                <tbody id="lista-tipos-informe">
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
<div class="modal fade" id="editarTipoModal" tabindex="-1" aria-labelledby="editarTipoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarTipoModalLabel">Editar Tipo de Informe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar-tipo-id">
                <div class="mb-3">
                    <label for="editar-tipo-nombre" class="form-label">Nombre del Tipo <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editar-tipo-nombre">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="guardarTipoEditado()">Guardar
                    Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Gestionar Membretes -->
<div class="modal fade" id="gestionarMembretesInformeModal" tabindex="-1"
    aria-labelledby="gestionarMembretesInformeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarMembretesInformeModalLabel">Gestionar Membretes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formMembretesInforme" enctype="multipart/form-data">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="header_image_template" class="form-label fw-medium text-negro">Imagen de
                                Encabezado</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control border rounded-start shadow-sm"
                                    id="header_image_template" name="header_image"
                                    accept="image/png,image/jpeg,image/gif">
                                <button class="btn border-rojo rounded-end" type="button"
                                    id="reset-header-template">Restablecer</button>
                            </div>
                            <div class="form-text text-gris small">Recomendado: imagen PNG de 210mm x 40mm (ancho
                                completo A4)</div>
                            <div class="mt-2 border p-2 rounded bg-light">
                                <p class="mb-1 fw-bold">Vista previa:</p>
                                <div id="header-preview-container-template" class="text-center">
                                    <img id="header-preview-template" src="/placeholder.svg"
                                        alt="Vista previa del encabezado" class="img-fluid"
                                        style="max-height: 100px; display: none;">
                                    <div id="header-placeholder-template" class="text-muted">No se ha seleccionado
                                        ninguna imagen</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="footer_image_template" class="form-label fw-medium text-negro">Imagen de Pie de
                                Página</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control border rounded-start shadow-sm"
                                    id="footer_image_template" name="footer_image"
                                    accept="image/png,image/jpeg,image/gif">
                                <button class="btn border-rojo rounded-end" type="button"
                                    id="reset-footer-template">Restablecer</button>
                            </div>
                            <div class="form-text text-gris small">Recomendado: imagen PNG de 210mm x 30mm (ancho
                                completo A4)</div>
                            <div class="mt-2 border p-2 rounded bg-light">
                                <p class="mb-1 fw-bold">Vista previa:</p>
                                <div id="footer-preview-container-template" class="text-center">
                                    <img id="footer-preview-template" src="/placeholder.svg"
                                        alt="Vista previa del pie de página" class="img-fluid"
                                        style="max-height: 100px; display: none;">
                                    <div id="footer-placeholder-template" class="text-muted">No se ha seleccionado
                                        ninguna imagen</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btn-preview-membretes" class="btn border-rojo">
                    <i class="fas fa-eye me-2"></i>Vista Previa
                </button>
                <button type="button" id="btn-save-membretes" class="btn bg-rojo text-white">
                    <i class="fas fa-save me-2"></i>Guardar Membretes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa para Membretes -->
<div class="modal fade" id="previewMembretesModal" tabindex="-1" aria-labelledby="previewMembretesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewMembretesModalLabel">Vista Previa de Membretes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-membretes" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="confirmarEliminarInformeModal" tabindex="-1"
    aria-labelledby="confirmarEliminarInformeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarInformeModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este informe? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white"
                    id="btn-confirmar-eliminar-informe">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para compartir por WhatsApp -->
<div class="modal fade" id="compartirWhatsAppInformeModal" tabindex="-1" aria-labelledby="compartirWhatsAppInformeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compartirWhatsAppInformeModalLabel">
                    <i class="fab fa-whatsapp text-success me-2"></i>Compartir Informe por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="numeroWhatsAppInforme" class="form-label">
                            <i class="fas fa-phone me-1"></i>Número de WhatsApp
                        </label>
                        <input type="text" class="form-control" id="numeroWhatsAppInforme" 
                               placeholder="Ej: 51999999999" required>
                        <div class="form-text">Incluya el código de país (51 para Perú)</div>
                    </div>
                    <div class="mb-3">
                        <label for="mensajeWhatsAppInforme" class="form-label">
                            <i class="fas fa-comment me-1"></i>Mensaje (opcional)
                        </label>
                        <textarea class="form-control" id="mensajeWhatsAppInforme" rows="3" 
                                  placeholder="Mensaje personalizado para acompañar el informe..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="enviarWhatsAppInforme()">
                    <i class="fab fa-whatsapp me-1"></i>Compartir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script para el módulo de informes -->
<script>
    window.InformesModule = window.InformesModule || (function () {
        // Variables globales
        // Variables globales
        window.informes = [];
        window.filtroActual = '';
        window.tipoFiltroActual = 'todos';
        window.paginaActual = 1;
        window.informesPorPagina = 9;
        
        let informes = window.informes;
        let filtroActual = window.filtroActual;
        let tipoFiltroActual = window.tipoFiltroActual;
        let paginaActual = window.paginaActual;
        let informesPorPagina = window.informesPorPagina;
        let totalInformes = 0;
        let informeEditor = null;
        let informeEditorModal = null;
        let templateEditor = null;
        let imagen1InformeChanged = false;
        let imagen2InformeChanged = false;
        let headerTemplateImageChanged = false;
        let footerTemplateImageChanged = false;
        let currentImagen1Informe = null;
        let currentImagen2Informe = null;
        let currentHeaderTemplateImage = null;
        let currentFooterTemplateImage = null;
        let editMode = false;
        let informeId = null;
        let moduloInformesInicializado = false;
        let vistaPreviewEnProceso = false;

// En informes.php, dentro del módulo InformesModule
function cleanup() {
    console.log('Limpiando módulo de informes completamente...');
    
    // Limpiar editores
    if (informeEditor) {
        try {
            informeEditor.off('text-change');
            informeEditor = null;
        } catch (e) {
            console.error('Error al limpiar informeEditor:', e);
        }
    }

    if (templateEditor) {
        try {
            templateEditor.off('text-change');
            templateEditor = null;
        } catch (e) {
            console.error('Error al limpiar templateEditor:', e);
        }
    }

    // Limpiar TODOS los eventos del documento
    $(document).off('.informes');
    $(document).off('click.informes');
    $(document).off('keyup.informes');
    $(document).off('change.informes');
    $(document).off('shown.bs.tab.informes');
    $(document).off('hidden.bs.modal.informes');
    $(document).off('show.bs.modal.informes');

    // Limpiar eventos del window
    $(window).off('beforeunload.informes');

    // Limpiar modales
    $('#editarPlantillaInformeModal').off();
    $('#gestionarMembretesInformeModal').off();
    $('.modal').modal('hide');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');

    // Remover elementos dinámicos
    $('[id^="previewTemplateModal_"]').remove();
    $('#imageModal').remove();

    // Resetear variables globales
    informes = [];
    window.informes = informes;
    filtroActual = '';
    tipoFiltroActual = 'todos';
    editMode = false;
    informeId = null;
    vistaPreviewEnProceso = false;
    moduloInformesInicializado = false;
    
    // Resetear flags de imágenes
    headerImageChanged = false;
    footerImageChanged = false;
    headerTemplateImageChanged = false;
    footerTemplateImageChanged = false;
    imagen1InformeChanged = false;
    imagen2InformeChanged = false;
    
    console.log('Módulo de informes limpiado completamente');
}

        // Función para limpiar el módulo
        function limpiarModulo() {
            console.log('Limpiando módulo de informes...');

            // Limpiar editores
            if (informeEditor) {
                try {
                    informeEditor.off('text-change');
                    informeEditor = null;
                } catch (e) {
                    console.error('Error al limpiar informeEditor:', e);
                }
            }

            if (templateEditor) {
                try {
                    templateEditor.off('text-change');
                    templateEditor = null;
                } catch (e) {
                    console.error('Error al limpiar templateEditor:', e);
                }
            }

            // Limpiar eventos
            $(document).off('click.informes');
            $(document).off('keyup.informes');
            $(document).off('change.informes');

            // Limpiar modales
            $('#editarPlantillaInformeModal').off('show.bs.modal');
            $('#editarPlantillaInformeModal').off('shown.bs.modal');
            $('#editarPlantillaInformeModal').off('hidden.bs.modal');
            $('#gestionarMembretesInformeModal').off('show.bs.modal');

            // Remover elementos dinámicos
            $('[id^="previewTemplateModal_"]').remove();
            $('#imageModal').remove();

            // Resetear variables
            informes = [];
    window.informes = informes;
            filtroActual = '';
            tipoFiltroActual = 'todos';
            editMode = false;
            informeId = null;
            vistaPreviewEnProceso = false;

            // Resetear flags de imágenes
            imagen1InformeChanged = false;
            imagen2InformeChanged = false;
            headerTemplateImageChanged = false;
            footerTemplateImageChanged = false;
            imagen1InformeChanged = false;
            imagen2InformeChanged = false;

            // Limpiar imágenes actuales
            currentImagen1Informe = null;
            currentImagen2Informe = null;
            currentHeaderTemplateImage = null;
            currentFooterTemplateImage = null;
            currentImagen1Informe = null;
            currentImagen2Informe = null;

            // NO resetear moduloInformesInicializado aquí
        }
        // Función para reiniciar completamente el módulo
        function reiniciarModuloCompleto() {
            console.log('Reiniciando módulo completo...');
            limpiarModulo();
            moduloInformesInicializado = false;
            window.informesModuloInicializado = false;
            inicializarModuloInformes();
        }



        $(document).ready(function () {
            console.log("Documento listo, configurando módulo de informes...");

            // Configurar PDF.js worker globalmente
            if (typeof pdfjsLib !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
            }

            // Inicializar módulo directamente
            window.InformesModule.init();



            // Configurar PDF.js worker globalmente
            if (typeof pdfjsLib !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
            }

            // Inicializar módulo directamente
            window.InformesModule.init();


            // Evento para cambio de pestaña
            $('#informes-tab').off('shown.bs.tab.informes').on('shown.bs.tab.informes', function (e) {
                console.log('Pestaña informes activada');
                // Forzar recarga de informes cuando se activa la pestaña
                setTimeout(function () {
                    if (moduloInformesInicializado) {
                        cargarInformes();
                    } else {
                        window.InformesModule.init();
                    }
                }, 100);
            });

            // Limpiar módulo cuando se cambie de pestaña
            $('a[data-bs-toggle="tab"]').not('#informes-tab').off('shown.bs.tab.informes').on('shown.bs.tab.informes', function (e) {
                console.log('Cambiando de pestaña, limpiando informes...');
                // Solo limpiar editores, no resetear la inicialización
                if (window.InformesModule) {
                    limpiarModulo();
                }
            });

        });

        // Limpiar al salir de la página
        $(window).on('beforeunload', function () {
            if (window.InformesModule) {
                window.InformesModule.cleanup();
            }
        });



        function inicializarModuloInformes() {
            console.log('Inicializando módulo de Informes...');

            // Si ya está inicializado, solo recargar informes
            if (moduloInformesInicializado) {
                console.log('Módulo ya inicializado, solo recargando informes...');
                cargarInformes();
                return;
            }


            // Limpiar módulo anterior si existe
            limpiarModulo();


            console.log('Inicializando módulo de Informes...');

            // Configurar PDF.js worker si no está configurado
            if (typeof pdfjsLib !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
            }

            moduloInformesInicializado = true;


            console.log('Inicializando módulo de Informes...');
            moduloInformesInicializado = true;

            // Cargar los informes
            cargarInformes();

            // Cargar los tipos de informes para el filtro
            cargarTiposInforme();

            // Configurar el modal de confirmación para eliminar
            $('#confirmarEliminarInformeModal').off('show.bs.modal.informes').on('show.bs.modal.informes', function (event) {

                const button = $(event.relatedTarget);
                const id = button.data('id');

                $('#btn-confirmar-eliminar-informe').off('click').on('click', function () {
                    eliminarInforme(id);
                });
            });

            // Configurar eventos de búsqueda
            $("#buscar-informe").off('keyup.informes').on("keyup.informes", function () {

                buscarInformes();
            });

            // Inicializar los modales
            $("#editarPlantillaInformeModal").off('show.bs.modal.informes').on('show.bs.modal.informes', function () {

                // console.log('Modal Editar Plantilla abriendo...');

                // Destruir el editor existente si hay uno
                if (templateEditor) {
                    try {
                        // Eliminar todos los elementos de la barra de herramientas
                        const toolbarElement = document.querySelector('#editor-container-template .ql-toolbar');
                        if (toolbarElement) {
                            toolbarElement.remove();
                        }

                        // Eliminar el contenedor del editor
                        const editorElement = document.querySelector('#editor-container-template .ql-editor');
                        if (editorElement) {
                            editorElement.remove();
                        }

                        // Limpiar el contenedor principal
                        const container = document.getElementById('editor-container-template');
                        if (container) {
                            container.innerHTML = '';
                        }

                        templateEditor = null;
                    } catch (e) {
                        console.error('Error al limpiar editor:', e);
                    }
                }

                // LIMPIAR TODOS LOS EVENTOS ANTERIORES
                $("#btn-preview-template").off('click');
                $("#btn-save-template").off('click');

                cargarDatosPlantillaYMembretes();
            });

            $("#editarPlantillaInformeModal").off('shown.bs.modal.informes').on('shown.bs.modal.informes', function () {

                console.log('Modal completamente abierto');

                // ASIGNAR EVENTOS UNA SOLA VEZ
                $("#btn-preview-template").off('click.informes').on('click.informes', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!vistaPreviewEnProceso) {
                        console.log('Vista previa clickeada UNA vez');
                        mostrarVistaPreviewTemplate();
                    }
                });

                $("#btn-save-template").off('click.informes').on('click.informes', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Guardar clickeado UNA vez');
                    guardarTemplate();
                });

            });

            // Limpiar editor cuando se cierra el modal
            $("#editarPlantillaInformeModal").off('hidden.bs.modal.informes').on('hidden.bs.modal.informes', function () {

                console.log('Modal cerrado, limpiando editor...');
                if (templateEditor) {
                    try {
                        // Eliminar todos los elementos de la barra de herramientas
                        const toolbarElement = document.querySelector('#editor-container-template .ql-toolbar');
                        if (toolbarElement) {
                            toolbarElement.remove();
                        }

                        // Limpiar el contenedor
                        const container = document.getElementById('editor-container-template');
                        if (container) {
                            container.innerHTML = '';
                        }

                        templateEditor = null;
                    } catch (e) {
                        console.error('Error al limpiar editor:', e);
                    }
                }

                // Limpiar eventos de imágenes para evitar errores
                try {
                    $("#header_image_template, #footer_image_template").off('change');
                    $("#reset-header-template, #reset-footer-template").off('click');
                } catch (e) {
                    console.error('Error al limpiar eventos de imágenes:', e);
                }
            });

            // Configurar evento para el botón principal Gestionar Membretes
            $("#btn-gestionar-membretes").off('click').on('click', function () {
                $("#gestionarMembretesInformeModal").modal('show');
            });

            $("#gestionarMembretesInformeModal").on('show.bs.modal', function () {
                cargarDatosPlantillaYMembretes();
            });

            // Configurar eventos para los botones de los modales de membretes
            $("#btn-preview-membretes").off('click').on("click", function () {
                mostrarVistaPreviewMembretes();
            });

            $("#btn-save-membretes").off('click').on("click", function () {
                guardarMembretes();
            });

            // Manejar la vista previa de las imágenes seleccionadas
            $("#header_image_template").on("change", function () {
                previewImage(this, "header-preview-template", "header-placeholder-template");
                headerTemplateImageChanged = true;
            });

            $("#footer_image_template").on("change", function () {
                previewImage(this, "footer-preview-template", "footer-placeholder-template");
                footerTemplateImageChanged = true;
            });

            // Manejar los botones de restablecer
            $("#reset-header-template").on("click", function () {
                resetImage("header_image_template", "header-preview-template", "header-placeholder-template", currentHeaderTemplateImage);
            });

            $("#reset-footer-template").on("click", function () {
                resetImage("footer_image_template", "footer-preview-template", "footer-placeholder-template", currentFooterTemplateImage);
            });

            // Agregar CSS dinámicamente para solucionar problemas de z-index y aria-hidden
            const customCSS = `
        #editarPlantillaInformeModal {
            z-index: 1055 !important;
        }
        
        #editarPlantillaInformeModal .modal-backdrop {
            z-index: 1054 !important;
        }
        
        #layout-wrapper[aria-hidden="true"] {
            pointer-events: none;
        }
        
        #layout-wrapper[aria-hidden="true"] .modal {
            pointer-events: auto;
        }
        
        #editarPlantillaInformeModal .modal-footer .btn {
            position: relative;
            z-index: 1060 !important;
            pointer-events: auto !important;
        }
        
        #editarPlantillaInformeModal.modal.show {
            display: block !important;
        }
        
        /* Ocultar barras de herramientas duplicadas */
        #editor-container-template .ql-toolbar:not(:first-of-type) {
            display: none !important;
        }
    `;

            // Agregar el CSS al documento
            const styleElement = document.createElement('style');
            styleElement.textContent = customCSS;
            document.head.appendChild(styleElement);
        }
        // Función para cargar los informes
        function cargarInformes(preservarPagina = false) {
            // VERIFICAR QUE EL CONTENEDOR EXISTA
            if (!$("#lista-informes-container").length) {
                console.error("Contenedor de informes no encontrado");
                return;
            }

            // Mostrar indicador de carga
            $("#lista-informes-container").html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando informes...</p>
        </div>
    `);
            $("#info-paginacion-container").hide();
            $("#paginacion-container").hide();


            // VERIFICAR QUE _URL ESTÉ DEFINIDA
            if (typeof _URL === 'undefined') {
                console.error('Variable _URL no está definida');
                $("#lista-informes-container").html(`
        <div class="col-12 text-center py-5">
            <div class="alert alert-danger" role="alert">
                Error de configuración. Variable _URL no definida.
            </div>
        </div>
    `);
                $("#info-paginacion-container").hide();
                $("#paginacion-container").hide();
                return;
            }

            // Construir la URL con los filtros
            let url = _URL + "/ajs/informe/render";

            if (filtroActual && tipoFiltroActual !== 'todos') {
                url += `?filtro=${encodeURIComponent(filtroActual)}&tipo_busqueda=${tipoFiltroActual}`;
            }

            // Realizar petición AJAX para obtener los informes
            $.ajax({
                url: url,
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    console.log('Datos recibidos:', data);
                    // Asegurarse de que data sea un array
                    informes = Array.isArray(data) ? data : [];
                    window.informes = informes; // Sincronizar con variable global

                    // Verificar si el contenedor aún existe antes de renderizar
                    if ($("#lista-informes-container").length) {
                        renderizarInformes();
                    } else {
                        console.error('Contenedor de informes desapareció durante la carga');
                    }
                },

                error: function (xhr, status, error) {
                    console.error("Error al cargar informes:", status, error);
                    $("#lista-informes-container").html(`
                <div class="col-12 text-center py-5">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar los informes. Por favor, intente nuevamente.
                    </div>
                    <button class="btn bg-rojo text-white mt-3" onclick="cargarInformes()">
                        <i class="fas fa-sync me-2"></i>Reintentar
                    </button>
                </div>
            `);
                    $("#info-paginacion-container").hide();
                    $("#paginacion-container").hide();
                }
            });
        }
        function cargarDatosPlantillaYMembretes() {
            $.ajax({
                url: _URL + "/ajs/informe/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // Cargar datos en el modal de Editar Plantilla
                        $("#titulo_template").val(data.titulo);

                        // LIMPIAR EDITOR EXISTENTE ANTES DE INICIALIZAR UNO NUEVO
                        if (templateEditor) {
                            templateEditor = null;
                        }

                        // Limpiar el contenedor COMPLETAMENTE
                        const editorContainer = document.getElementById('editor-container-template');
                        if (editorContainer) {
                            // Eliminar todos los elementos hijos
                            while (editorContainer.firstChild) {
                                editorContainer.removeChild(editorContainer.firstChild);
                            }
                            // Limpiar cualquier texto residual
                            editorContainer.innerHTML = '';
                            editorContainer.textContent = '';
                        }

                        // Usar setTimeout para asegurar que el DOM esté listo
                        setTimeout(function () {
                            // Verificar que el contenido esté limpio antes de inicializar
                            const contenidoLimpio = data.contenido || '';
                            console.log('Contenido a cargar:', contenidoLimpio);
                            inicializarTemplateEditor(contenidoLimpio);
                        }, 200);

                        // Cargar datos en el modal de Gestionar Membretes
                        currentHeaderTemplateImage = data.header_image;
                        currentFooterTemplateImage = data.footer_image;

                        // Mostrar las imágenes actuales
                        if (data.header_image) {
                            $("#header-preview-template").attr("src", data.header_image).show();
                            $("#header-placeholder-template").hide();
                        } else {
                            $("#header-preview-template").hide();
                            $("#header-placeholder-template").show();
                        }

                        if (data.footer_image) {
                            $("#footer-preview-template").attr("src", data.footer_image).show();
                            $("#footer-placeholder-template").hide();
                        } else {
                            $("#footer-preview-template").hide();
                            $("#footer-placeholder-template").show();
                        }
                    } else {
                        console.error("Error al cargar plantilla:", data.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar plantilla:", status, error);
                }
            });
        }
        // Función para mostrar la vista previa de membretes
        function mostrarVistaPreviewMembretes() {
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Generando vista previa',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('titulo', "VISTA PREVIA DE MEMBRETES");
            formData.append('contenido', "<p>Este es un ejemplo de contenido para visualizar los membretes.</p>");

            // Añadir las imágenes si han sido cambiadas
            if (headerTemplateImageChanged && document.getElementById('header_image_template').files[0]) {
                formData.append('header_image', document.getElementById('header_image_template').files[0]);
            } else if (currentHeaderTemplateImage) {
                formData.append('header_image_base64', currentHeaderTemplateImage);
            }

            if (footerTemplateImageChanged && document.getElementById('footer_image_template').files[0]) {
                formData.append('footer_image', document.getElementById('footer_image_template').files[0]);
            } else if (currentFooterTemplateImage) {
                formData.append('footer_image_base64', currentFooterTemplateImage);
            }

            // Enviar datos para generar vista previa
            $.ajax({
                url: _URL + "/ajs/informe/vista-previa",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        // Crear un objeto Blob con el PDF base64
                        const byteCharacters = atob(data.pdfBase64);
                        const byteNumbers = new Array(byteCharacters.length);
                        for (let i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/pdf' });

                        // Crear una URL para el blob
                        const pdfUrl = URL.createObjectURL(blob);

                        // Mostrar el PDF en el iframe
                        $("#preview-frame-membretes").attr("src", pdfUrl);

                        // Ocultar el modal de membretes y mostrar el de vista previa
                        $("#gestionarMembretesInformeModal").modal("hide");
                        $("#previewMembretesModal").modal("show");

                        // Cuando se cierre el modal de vista previa, volver a mostrar el de membretes
                        $("#previewMembretesModal").on('hidden.bs.modal', function () {
                            URL.revokeObjectURL(pdfUrl);
                            $("#gestionarMembretesInformeModal").modal("show");
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo generar la vista previa',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al generar vista previa:", status, error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }

        // Función para guardar los membretes
        function guardarMembretes() {
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando membretes...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('titulo', $("#titulo_template").val() || "INFORME");
            formData.append('contenido', templateEditor ? templateEditor.root.innerHTML : "");

            // Añadir las imágenes si han sido cambiadas
            if (headerTemplateImageChanged && document.getElementById('header_image_template').files[0]) {
                formData.append('header_image', document.getElementById('header_image_template').files[0]);
            }

            if (footerTemplateImageChanged && document.getElementById('footer_image_template').files[0]) {
                formData.append('footer_image', document.getElementById('footer_image_template').files[0]);
            }

            // Enviar datos al servidor
            $.ajax({
                url: _URL + "/ajs/informe/guardar-template",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // Actualizar las imágenes actuales si se proporcionaron nuevas URLs
                        if (data.header_image) {
                            currentHeaderTemplateImage = data.header_image;
                        }

                        if (data.footer_image) {
                            currentFooterTemplateImage = data.footer_image;
                        }

                        // Restablecer los indicadores de cambio
                        headerTemplateImageChanged = false;
                        footerTemplateImageChanged = false;

                        // CERRAR el SweetAlert de loading primero
                        Swal.close();
                        
                        // Forzar cierre completo del modal
                        const modalElement = document.getElementById('gestionarMembretesInformeModal');
                        
                        // Remover todas las clases y atributos problemáticos
                        $(modalElement).removeClass('show');
                        modalElement.style.display = 'none';
                        modalElement.setAttribute('aria-hidden', 'true');
                        modalElement.removeAttribute('aria-modal');
                        
                        // Limpiar completamente el estado del modal
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css({
                            'overflow': '',
                            'padding-right': ''
                        });
                        
                        // Restaurar el focus al documento
                        document.activeElement.blur();
                        
                        // Mostrar SweetAlert de éxito DESPUÉS de limpiar
                        setTimeout(() => {
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Los membretes se han guardado correctamente',
                                icon: 'success'
                            });
                            
                            // Restaurar event listeners después de la limpieza
                            reinitializarEventListenersMembretes();
                        }, 200);
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudieron guardar los membretes',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al guardar los membretes:", status, error);
                    
                    // CERRAR el SweetAlert de loading primero
                    Swal.close();
                    
                    // Forzar cierre del modal también en errores
                    const modalElement = document.getElementById('gestionarMembretesInformeModal');
                    $(modalElement).removeClass('show');
                    modalElement.style.display = 'none';
                    modalElement.setAttribute('aria-hidden', 'true');
                    modalElement.removeAttribute('aria-modal');
                    
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css({
                        'overflow': '',
                        'padding-right': ''
                    });
                    
                    document.activeElement.blur();
                    
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo conectar con el servidor',
                            icon: 'error'
                        });
                        
                        // Restaurar event listeners también en caso de error
                        reinitializarEventListenersMembretes();
                    }, 200);
                }
            });
        }

        // Función para reinicializar los event listeners de membretes
        function reinitializarEventListenersMembretes() {
            // Reinicializar el botón principal Gestionar Membretes
            $("#btn-gestionar-membretes").off('click').on('click', function () {
                $("#gestionarMembretesInformeModal").modal('show');
            });

            // Reinicializar el modal de gestionar membretes
            $("#gestionarMembretesInformeModal").off('show.bs.modal').on('show.bs.modal', function () {
                cargarDatosPlantillaYMembretes();
            });

            // Reinicializar los botones del modal
            $("#btn-preview-membretes").off('click').on("click", function () {
                mostrarVistaPreviewMembretes();
            });

            $("#btn-save-membretes").off('click').on("click", function () {
                guardarMembretes();
            });

            // Reinicializar eventos de imagen
            $("#header_image_template").off('change').on("change", function () {
                previewImage(this, "header-preview-template", "header-placeholder-template");
                headerTemplateImageChanged = true;
            });

            $("#footer_image_template").off('change').on("change", function () {
                previewImage(this, "footer-preview-template", "footer-placeholder-template");
                footerTemplateImageChanged = true;
            });

            // Reinicializar botones de reset
            $("#reset-header-template").off('click').on("click", function () {
                resetImage("header_image_template", "header-preview-template", "header-placeholder-template", currentHeaderTemplateImage);
            });

            $("#reset-footer-template").off('click').on("click", function () {
                resetImage("footer_image_template", "footer-preview-template", "footer-placeholder-template", currentFooterTemplateImage);
            });
        }

        function renderizarInformes() {
            console.log(`Renderizando informes - Página: ${window.paginaActual}, Total informes: ${window.informes?.length || 0}`);
            
            if (!window.informes || window.informes.length === 0) {
                $("#lista-informes-container").html(`
            <div class="col-12 text-center py-5">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron informes.
                </div>
                <button class="btn bg-rojo text-white mt-3" onclick="mostrarFormularioNuevoInforme()">
                    <i class="fas fa-plus me-2"></i>Crear primer informe
                </button>
            </div>
        `);
                $("#info-paginacion-container").hide();
                $("#paginacion-container").hide();
                return;
            }

            // Calcular paginación
            totalInformes = window.informes.length;
            const totalPaginas = Math.ceil(totalInformes / window.informesPorPagina);
            const inicioIndex = (window.paginaActual - 1) * window.informesPorPagina;
            const finIndex = inicioIndex + window.informesPorPagina;
            const informesPaginados = window.informes.slice(inicioIndex, finIndex);

            // Actualizar información de paginación
            $("#info-paginacion-texto").text(`Mostrando ${inicioIndex + 1}-${Math.min(finIndex, totalInformes)} de ${totalInformes} informes`);
            $("#info-paginacion-container").show();

            let html = '';

            informesPaginados.forEach(function (informe) {
                const fecha = new Date(informe.fecha_creacion).toLocaleDateString();
                const cliente = informe.cliente_nombre || 'Sin cliente';

                // Generar un ID único para el canvas de PDF
                const canvasId = `pdf-preview-${informe.id_informe}`;

                html += `
            <div class="col">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-rojo">${informe.tipo}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-dark" type="button" id="dropdownInforme${informe.id_informe}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownInforme${informe.id_informe}">
                                <li><a class="dropdown-item" href="${_URL}/ajs/informe/generarPDF?id=${informe.id_informe}" target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i> Ver PDF
                                </a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="editarInforme(${informe.id_informe})">
                                    <i class="fas fa-edit me-2"></i> Editar
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmarEliminarInformeModal" data-id="${informe.id_informe}">
                                    <i class="fas fa-trash-alt me-2"></i> Eliminar
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Añadir vista previa del PDF -->
                    <div class="card-body p-0">
                        <div class="document-preview">
                            <!-- Spinner de carga -->
                            <div id="loading-${canvasId}" class="text-center p-4">
                                <div class="spinner-border text-rojo" role="status">
                                    <span class="visually-hidden">Cargando PDF...</span>
                                </div>
                                <p class="mt-2 text-muted">Cargando vista previa...</p>
                            </div>
                            <!-- Canvas para el PDF -->
                            <canvas id="${canvasId}" class="pdf-preview-canvas" style="display: none;"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <h5 class="card-title">${informe.titulo}</h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> ${cliente}<br>
                                <i class="fas fa-calendar-alt me-1"></i> ${fecha}
                            </small>
                        </p>
                        <div class="d-flex justify-content-between mt-2">
                            <div class="btn-group">
                                <a href="${_URL}/ajs/informe/generarPDF?id=${informe.id_informe}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                </a>
                                <button class="btn btn-sm btn-outline-success" onclick="compartirWhatsAppInforme(${informe.id_informe})">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" type="button" onclick="editarInforme(${informe.id_informe})">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
            });

            // Actualizar grid de informes (solo las tarjetas)
            $("#lista-informes-container").html(html);

            // Inicializar dropdowns de Bootstrap para los elementos dinámicos
            setTimeout(() => {
                const dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                dropdownElementList.forEach(dropdownToggleEl => {
                    new bootstrap.Dropdown(dropdownToggleEl);
                });
            }, 100);

            // Actualizar controles de paginación por separado
            if (totalPaginas > 1) {
                let paginacionHtml = `
                    <li class="page-item ${window.paginaActual === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="cambiarPagina(${window.paginaActual - 1}); return false;" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>`;
                    
                for (let i = 1; i <= totalPaginas; i++) {
                    paginacionHtml += `
                        <li class="page-item ${i === window.paginaActual ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="cambiarPagina(${i}); return false;">${i}</a>
                        </li>`;
                }
                
                paginacionHtml += `
                    <li class="page-item ${window.paginaActual === totalPaginas ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="cambiarPagina(${window.paginaActual + 1}); return false;" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>`;
                
                $("#paginacion-lista").html(paginacionHtml);
                $("#paginacion-container").show();
            } else {
                $("#paginacion-container").hide();
            }

            // Inicializar la carga de PDFs después de que el HTML esté en el DOM
            informesPaginados.forEach(function (informe) {
                const canvasId = `pdf-preview-${informe.id_informe}`;
                // Cargar PDF directamente
                setTimeout(() => {
                    renderPdfPreview(`${_URL}/ajs/informe/generarPDFBase64?id=${informe.id_informe}`, canvasId);
                }, 100);
            });
        }
        
        // Exportar función renderizarInformes globalmente
        window.renderizarInformes = renderizarInformes;

        // Función para cargar los tipos de informes para el filtro
        function cargarTiposInforme() {
            $.ajax({
                url: _URL + "/ajs/informe/getTipos",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.tipos && data.tipos.length > 0) {
                        let html = `
                    <li><h6 class="dropdown-header">Tipo de Informe</h6></li>
                    <li><a class="dropdown-item active" href="#" data-tipo="todos">Todos</a></li>
                `;

                        data.tipos.forEach(function (tipo) {
                            html += `<li><a class="dropdown-item" href="#" data-tipo="tipo" data-valor="${tipo.tipo}">${tipo.tipo}</a></li>`;
                        });

                        $("#filtro-tipos").html(html);

                        // Configurar los eventos de clic para los filtros
                        $("#filtro-tipos .dropdown-item").on("click", function (e) {
                            e.preventDefault();

                            // Actualizar la clase active
                            $("#filtro-tipos .dropdown-item").removeClass("active");
                            $(this).addClass("active");

                            // Obtener el tipo de filtro
                            const tipo = $(this).data("tipo");

                            if (tipo === "todos") {
                                tipoFiltroActual = "todos";
                                filtroActual = "";
                            } else {
                                tipoFiltroActual = "tipo";
                                filtroActual = $(this).data("valor");
                            }

                            // Recargar los informes con el filtro
                            cargarInformes();
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar tipos de informe:", status, error);
                }
            });
        }

        // Función para buscar informes
        function buscarInformes() {
            const busqueda = $("#buscar-informe").val().trim().toLowerCase();

            if (busqueda === "") {
                // Si la búsqueda está vacía, mostrar todos los informes según el filtro de tipo
                if (tipoFiltroActual === "todos") {
                    filtroActual = "";
                }
            } else {
                // Si hay texto de búsqueda, filtrar por título
                filtroActual = busqueda;
                tipoFiltroActual = "titulo";
            }

            // Recargar los informes con el filtro
            cargarInformes();
        }

        // Función para mostrar el formulario de nuevo informe
        function mostrarFormularioNuevoInforme() {
            // Mostrar indicador de carga
            $("#nuevo-informe").html(`
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando formulario...</p>
        </div>
    `);

            // Mostrar la pestaña de nuevo informe
            $('#lista-informes').removeClass('show active');
            $('#nuevo-informe').addClass('show active');

            // Cargar el formulario
            $.ajax({
                url: _URL + "/ajs/informe/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    renderizarFormularioInforme(false, null, data);
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar plantilla:", status, error);
                    $("#nuevo-informe").html(`
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar el formulario. Por favor, intente nuevamente.
                </div>
                <button class="btn bg-rojo text-white mt-3" onclick="mostrarFormularioNuevoInforme()">
                    <i class="fas fa-sync me-2"></i>Reintentar
                </button>
            `);
                }
            });
        }

        // Función para editar un informe existente
        function editarInforme(id) {
            // Mostrar indicador de carga en el modal
            $("#modal-editar-informe-content").html(`
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando informe...</p>
        </div>
    `);

            // Mostrar el modal de editar informe
            $('#editarInformeModal').modal('show');

            // Cargar los datos del informe
            $.ajax({
                url: _URL + "/ajs/informe/getOne",
                method: "POST",
                data: { id_informe: id },
                dataType: 'json',
                success: function (data) {
                    if (!data.error) {
                        renderizarFormularioInformeModal(true, data);
                    } else {
                        console.error("Error al cargar informe:", data.error);
                        $("#modal-editar-informe-content").html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar el informe. Por favor, intente nuevamente.
                    </div>
                    <button class="btn bg-rojo text-white mt-3" onclick="$('#editarInformeModal').modal('hide')">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                `);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar informe:", status, error);
                    $("#modal-editar-informe-content").html(`
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar el informe. Por favor, intente nuevamente.
                </div>
                <button class="btn bg-rojo text-white mt-3" onclick="$('#editarInformeModal').modal('hide')">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            `);
                }
            });
        }

        // Modificar la función editarPlantillaInforme
        function editarPlantillaInforme() {
            // Mostrar el modal en lugar de cambiar la pestaña
            $("#editarPlantillaInformeModal").modal("show");
        }

        // Función para renderizar el formulario en el modal de editar informe
        function renderizarFormularioInformeModal(esEdicion, informe, plantilla = null) {
            editMode = esEdicion;
            informeId = esEdicion ? informe.id_informe : null;

            const titulo = esEdicion ? "Editar Informe" : "Nuevo Informe";

            // Valores por defecto
            const valores = {
                id_informe: esEdicion ? informe.id_informe : '',
                tipo: esEdicion ? informe.tipo : '',
                titulo: esEdicion ? informe.titulo : (plantilla ? plantilla.titulo : ''),
                contenido: esEdicion ? informe.contenido : (plantilla ? plantilla.contenido : ''),
                cliente_id: esEdicion ? informe.cliente_id : '',
                cliente_nombre: esEdicion ? informe.cliente_nombre : '',
                cliente_documento: esEdicion ? informe.cliente_documento : '',
                cliente_direccion: esEdicion ? informe.cliente_direccion : '',
                persona_entregar: esEdicion ? informe.persona_entregar : '',
                imagen1: esEdicion ? informe.imagen1 : '', // Solo mostrar imágenes en modo edición
                imagen2: esEdicion ? informe.imagen2 : ''  // Solo mostrar imágenes en modo edición
            };

            // Guardar las imágenes actuales
            currentImagen1Informe = valores.imagen1;
            currentImagen2Informe = valores.imagen2;

            // Renderizar el formulario en el modal
            $("#modal-editar-informe-content").html(`
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form id="formInformeModal" enctype="multipart/form-data">
                <input type="hidden" id="id_informe_modal" name="id_informe" value="${valores.id_informe}">
                
               <!-- CONTENEDOR 1: TÍTULO Y CLIENTE (SIN MIN-HEIGHT) -->
<div class="container-fluid p-0 mb-3">
    <div class="row">
        <!-- TÍTULO DEL INFORME -->
        <div class="col-md-6">
            <div>
                <label for="titulo_informe_modal" class="form-label fw-medium text-negro">Título del Informe <span class="text-danger">*</span></label>
                <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo_informe_modal" name="titulo" value="${valores.titulo}" required>
            </div>
        </div>
        
        <!-- CLIENTE BÚSQUEDA SOLAMENTE -->
        <div class="col-md-6">
            <div>
                <label for="cliente_search_modal" class="form-label fw-medium text-negro">Cliente <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control border rounded-start-2 shadow-sm" id="cliente_search_modal" placeholder="Buscar por nombre o documento..." autocomplete="off">
                    <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-search-cliente-modal">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <input type="hidden" id="cliente_id_modal" name="cliente_id" value="${valores.cliente_id}">
            </div>
        </div>
    </div>
</div>

<!-- CONTENEDOR 2: INFORMACIÓN DEL CLIENTE (SEPARADO Y OCULTO) -->
<div class="container-fluid p-0 mb-3" id="cliente_info_container_modal" style="display: ${valores.cliente_id ? 'block' : 'none'};">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info mb-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="alert-heading mb-1">
                            <i class="fas fa-user me-2"></i>Cliente Seleccionado
                        </h6>
                        <div class="fw-bold" id="cliente_nombre_modal">${valores.cliente_nombre || ''}</div>
                        <div class="text-muted small" id="cliente_documento_modal">Documento: ${valores.cliente_documento || ''}</div>
                        <div class="text-muted small" id="cliente_direccion_modal">Dirección: ${valores.cliente_direccion || 'No especificada'}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarClienteModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENEDOR 3: TIPO DE INFORME (INDEPENDIENTE) -->
                <div class="container-fluid p-0 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="min-height: 80px;">
                                <label for="tipo_informe_modal" class="form-label fw-medium text-negro">Tipo de Informe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control border rounded-start-2 shadow-sm" id="tipo_informe_modal" name="tipo" required>
                                        <option value="">Seleccione un tipo</option>
                                    </select>
                                    <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-gestionar-tipos-modal" onclick="abrirModalTipos()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div style="min-height: 80px;">
                                <label for="persona_entregar_modal" class="form-label fw-medium text-negro">Persona a Entregar</label>
                                <input type="text" class="form-control border rounded-2 shadow-sm" id="persona_entregar_modal" name="persona_entregar" value="${valores.persona_entregar}" placeholder="Nombre de la persona que recibirá el informe">
                            </div>
                        </div>
                    </div>
                </div>

<!-- CONTENEDOR 4: EDITOR DE CONTENIDO -->
                <div class="container-fluid p-0 mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="min-height: 80px;">
                                <label class="form-label fw-medium text-negro">Contenido del Informe <span class="text-danger">*</span></label>
                                <!-- Contenedor para el editor -->
                                <div id="editor-container-informe-modal" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                    <!-- El editor Quill se cargará aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<!-- CONTENEDOR 5: IMÁGENES -->
                <div class="container-fluid p-0 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="min-height: 80px;">
                                <label for="imagen1_modal" class="form-label fw-medium text-negro">Imagen 1</label>
                                <input type="file" class="form-control border rounded-2 shadow-sm" id="imagen1_modal" name="imagen1" accept="image/*">
                                <div class="mt-2">
                                    <img id="imagen1-preview-modal" src="${valores.imagen1 || ''}" alt="Vista previa imagen 1" style="max-width: 100%; max-height: 150px; display: ${valores.imagen1 ? 'block' : 'none'};" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div style="min-height: 80px;">
                                <label for="imagen2_modal" class="form-label fw-medium text-negro">Imagen 2</label>
                                <input type="file" class="form-control border-2 shadow-sm" id="imagen2_modal" name="imagen2" accept="image/*">
                                <div class="mt-2">
                                    <img id="imagen2-preview-modal" src="${valores.imagen2 || ''}" alt="Vista previa imagen 2" style="max-width: 100%; max-height: 150px; display: ${valores.imagen2 ? 'block' : 'none'};" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
            `);

            // Cargar tipos de informe
            cargarTiposInformeParaSelect('tipo_informe_modal');

            // Inicializar el editor Quill con un pequeño delay para asegurar que el DOM esté listo
            setTimeout(() => {
                inicializarInformeEditorModal(valores.contenido);
            }, 100);

            // Inicializar autocomplete para búsqueda de clientes
            inicializarAutocompleteClienteModal();

            // Configurar eventos para las imágenes
            configurarEventosImagenesModal();
        }

        // Función para inicializar el editor Quill en el modal
        function inicializarInformeEditorModal(contenido = '') {
            try {
                console.log('Inicializando editor Quill para informes en modal...');

                // Verificar si ya existe un editor y limpiarlo
                if (informeEditor) {
                    try {
                        informeEditor = null;
                    } catch (e) {
                        console.log('Limpiando editor anterior...');
                    }
                }

                // Verificar si Quill está cargado
                if (typeof Quill === 'undefined') {
                    console.log('Quill no está cargado, cargando dinámicamente...');
                    // Cargar Quill dinámicamente
                    const quillScript = document.createElement('script');
                    quillScript.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
                    document.head.appendChild(quillScript);

                    const quillStyle = document.createElement('link');
                    quillStyle.rel = 'stylesheet';
                    quillStyle.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                    document.head.appendChild(quillStyle);

                    quillScript.onload = function () {
                        console.log('Quill cargado dinámicamente, inicializando en modal...');
                        inicializarQuillInformeModal(contenido);
                    };
                } else {
                    console.log('Quill ya está cargado, inicializando en modal directamente...');
                    inicializarQuillInformeModal(contenido);
                }
            } catch (error) {
                console.error('Error al inicializar Quill para informes en modal:', error);
            }
        }

        // Función para inicializar Quill en el modal
        function inicializarQuillInformeModal(contenido) {
            const toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ];

            let intentos = 0;
            const maxIntentos = 20; // Máximo 1 segundo (20 * 50ms)

            // Función para intentar inicializar el editor
            const intentarInicializar = () => {
                intentos++;
                const editorContainer = document.getElementById('editor-container-informe-modal');
                if (!editorContainer) {
                    if (intentos >= maxIntentos) {
                        console.error('No se pudo encontrar el contenedor del editor del modal después de múltiples intentos');
                        return;
                    }
                    console.log(`Contenedor del editor del modal no encontrado, reintentando en 50ms... (intento ${intentos}/${maxIntentos})`);
                    setTimeout(intentarInicializar, 50);
                    return;
                }

                console.log('Contenedor del editor del modal encontrado, limpiando y preparando...');
                
                // Limpiar el contenedor del editor
                editorContainer.innerHTML = '';
                
                // Crear un div interno para el editor
                const editorDiv = document.createElement('div');
                editorDiv.id = 'quill-editor-informe-modal-' + Date.now();
                editorDiv.style.minHeight = '400px';
                editorContainer.appendChild(editorDiv);
                
                console.log('Contenedor del modal preparado, inicializando Quill...');

                informeEditorModal = new Quill('#' + editorDiv.id, {
                    modules: {
                        toolbar: toolbarOptions,
                        clipboard: {
                            matchVisual: false
                        }
                    },
                    theme: 'snow',
                    placeholder: 'Contenido del informe...',
                    bounds: '#' + editorDiv.id // Limitar el alcance del editor a su contenedor
                });

                // Establecer el contenido inicial
                if (contenido) {
                    try {
                        informeEditorModal.root.innerHTML = contenido;
                        console.log('Contenido del informe cargado en el editor del modal');
                    } catch (e) {
                        console.warn('No se pudo cargar el contenido inicial en el modal:', e);
                    }
                }

                console.log('Editor Quill para informes en modal inicializado correctamente');

                // Asegurarse de que los eventos de los botones funcionen
                setTimeout(function () {
                    // Volver a asignar los eventos a los botones del modal
                    $("#btn-preview-informe-modal").off('click').on("click", function () {
                        mostrarVistaPreviaModal();
                    });

                    $("#btn-save-informe-modal").off('click').on("click", function () {
                        guardarInformeModal();
                    });
                }, 500);
            };

            // Iniciar el proceso de inicialización
            intentarInicializar();
        }

        // Función para inicializar autocomplete de cliente en el modal
        function inicializarAutocompleteClienteModal() {
            $("#cliente_search_modal").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: _URL + "/ajs/cliente/buscar",
                        method: "POST",
                        data: { term: request.term },
                        dataType: 'json',
                        success: function (data) {
                            if (data.success && data.clientes) {
                                response(data.clientes.map(function (cliente) {
                                    return {
                                        label: cliente.nombre + ' - ' + cliente.documento,
                                        value: cliente.nombre,
                                        cliente: cliente
                                    };
                                }));
                            } else {
                                response([]);
                            }
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    const cliente = ui.item.cliente;
                    $("#cliente_id_modal").val(cliente.id_cliente);
                    $("#cliente_nombre_modal").text(cliente.nombre);
                    $("#cliente_documento_modal").text('Documento: ' + cliente.documento);
                    $("#cliente_direccion_modal").text('Dirección: ' + (cliente.direccion || 'No especificada'));
                    $("#cliente_info_container_modal").show();
                    $("#cliente_search_modal").val('');
                    $(document).trigger('clienteSeleccionado');
                }
            });

            // Configurar el botón de búsqueda
            $("#btn-search-cliente-modal").on("click", function () {
                const term = $("#cliente_search_modal").val();
                if (term.length >= 2) {
                    $("#cliente_search_modal").autocomplete("search");
                }
            });
        }

        // Función para limpiar cliente en el modal
        function limpiarClienteModal() {
            $("#cliente_id_modal").val('');
            $("#cliente_nombre_modal").text('');
            $("#cliente_documento_modal").text('');
            $("#cliente_direccion_modal").text('');
            $("#cliente_info_container_modal").hide();
            $("#cliente_search_modal").val('');
        }

        // Función para configurar eventos de imágenes en el modal
        function configurarEventosImagenesModal() {
            $("#imagen1_modal").on("change", function () {
                imagen1InformeChanged = true;
                previewImageModal(this, "imagen1-preview-modal");
            });

            $("#imagen2_modal").on("change", function () {
                imagen2InformeChanged = true;
                previewImageModal(this, "imagen2-preview-modal");
            });
        }

        // Función para preview de imagen en el modal
        function previewImageModal(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    try {
                        const previewElement = $("#" + previewId);
                        if (previewElement.length > 0) {
                            previewElement.attr("src", e.target.result).show();
                        } else {
                            console.warn(`Elemento de preview no encontrado: ${previewId}`);
                        }
                    } catch (error) {
                        console.error(`Error al establecer src en ${previewId}:`, error);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Función para mostrar vista previa en el modal
        function mostrarVistaPreviaModal() {
            // Verificar que el editor esté inicializado
            if (!informeEditorModal) {
                Swal.fire({
                    title: 'Error',
                    text: 'El editor no está inicializado correctamente',
                    icon: 'error'
                });
                return;
            }

            // Obtener el contenido actual
            const contenido = informeEditorModal.root.innerHTML;
            const titulo = $("#titulo_informe_modal").val();

            if (!titulo.trim()) {
                Swal.fire({
                    title: 'Error',
                    text: 'El título no puede estar vacío',
                    icon: 'error'
                });
                return;
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Generando vista previa',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('titulo', titulo);
            formData.append('contenido', contenido);

            // Añadir las imágenes específicas del informe para vista previa
            if (imagen1InformeChanged && document.getElementById('imagen1_modal').files[0]) {
                formData.append('imagen1_informe', document.getElementById('imagen1_modal').files[0]);
            } else if (currentImagen1Informe) {
                formData.append('imagen1_informe_base64', currentImagen1Informe);
            }

            if (imagen2InformeChanged && document.getElementById('imagen2_modal').files[0]) {
                formData.append('imagen2_informe', document.getElementById('imagen2_modal').files[0]);
            } else if (currentImagen2Informe) {
                formData.append('imagen2_informe_base64', currentImagen2Informe);
            }

            // Enviar datos para generar vista previa
            $.ajax({
                url: _URL + "/ajs/informe/vista-previa",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        // Crear un objeto Blob con el PDF base64
                        const byteCharacters = atob(data.pdfBase64);
                        const byteNumbers = new Array(byteCharacters.length);
                        for (let i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/pdf' });

                        // Crear una URL para el blob
                        const pdfUrl = URL.createObjectURL(blob);

                        // Mostrar el PDF en el iframe
                        $("#preview-frame-informe").attr("src", pdfUrl);
                        $("#previewInformeModal").modal("show");

                        // Limpiar la URL cuando se cierre el modal
                        $("#previewInformeModal").on('hidden.bs.modal', function () {
                            URL.revokeObjectURL(pdfUrl);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo generar la vista previa',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al generar vista previa:", status, error);
                    console.log("Respuesta del servidor:", xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }

        // Función para guardar informe desde el modal
        function guardarInformeModal() {
            // Obtener el contenido del editor
            if (!informeEditorModal) {
                Swal.fire({
                    title: 'Error',
                    text: 'El editor no está inicializado correctamente',
                    icon: 'error'
                }).then(() => {
                    restaurarFuncionalidadModal();
                });
                return;
            }

            const contenido = informeEditorModal.root.innerHTML;
            const tipo = $("#tipo_informe_modal").val();
            const titulo = $("#titulo_informe_modal").val();
            const cliente_id = $("#cliente_id_modal").val();
            const persona_entregar = $("#persona_entregar_modal").val();

            // Validar campos obligatorios
            if (!tipo.trim() || !titulo.trim()) {
                Swal.fire({
                    title: 'Error',
                    text: 'Los campos Tipo y Título son obligatorios',
                    icon: 'error'
                }).then(() => {
                    // Restaurar funcionalidad del modal después de la alerta
                    restaurarFuncionalidadModal();
                });
                return;
            }

            // Validar que el cliente sea requerido
            if (!cliente_id || cliente_id.trim() === '') {
                Swal.fire({
                    title: 'Cliente Requerido',
                    text: 'Debe seleccionar un cliente para crear el informe',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    // Restaurar funcionalidad del modal después de la alerta
                    restaurarFuncionalidadModal();
                    // Enfocar el campo de búsqueda de cliente
                    setTimeout(() => {
                        $("#cliente_search_modal").focus();
                    }, 200);
                });
                return;
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando informe...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('tipo', tipo);
            formData.append('titulo', titulo);
            formData.append('contenido', contenido);
            formData.append('cliente_id', cliente_id);
            formData.append('persona_entregar', persona_entregar);

            // En modo edición siempre hay un ID
            formData.append('id_informe', informeId);

            // Añadir las imágenes específicas del informe
            if (imagen1InformeChanged && document.getElementById('imagen1_modal').files[0]) {
                formData.append('imagen1', document.getElementById('imagen1_modal').files[0]);
            } else if (currentImagen1Informe) {
                formData.append('imagen1_base64', currentImagen1Informe);
            }

            if (imagen2InformeChanged && document.getElementById('imagen2_modal').files[0]) {
                formData.append('imagen2', document.getElementById('imagen2_modal').files[0]);
            } else if (currentImagen2Informe) {
                formData.append('imagen2_base64', currentImagen2Informe);
            }

            // Enviar datos al servidor para editar
            $.ajax({
                url: _URL + "/ajs/informe/editar",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.res) {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.msg,
                            icon: 'success'
                        }).then(() => {
                            // Cerrar el modal y refrescar la lista
                            $('#editarInformeModal').modal('hide');
                            cargarInformes();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo guardar el informe',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al guardar el informe:", status, error);
                    console.log("Respuesta del servidor:", xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }
        function renderizarFormularioInforme(esEdicion, informe, plantilla = null) {
            editMode = esEdicion;
            informeId = esEdicion ? informe.id_informe : null;

            const contenedor = esEdicion ? $("#editar-informe") : $("#nuevo-informe");
            const titulo = esEdicion ? "Editar Informe" : "Nuevo Informe";

            // Valores por defecto
            const valores = {
                id_informe: esEdicion ? informe.id_informe : '',
                tipo: esEdicion ? informe.tipo : '',
                titulo: esEdicion ? informe.titulo : (plantilla ? plantilla.titulo : ''),
                contenido: esEdicion ? informe.contenido : (plantilla ? plantilla.contenido : ''),
                cliente_id: esEdicion ? informe.cliente_id : '',
                cliente_nombre: esEdicion ? informe.cliente_nombre : '',
                cliente_documento: esEdicion ? informe.cliente_documento : '',
                cliente_direccion: esEdicion ? informe.cliente_direccion : '',
                persona_entregar: esEdicion ? informe.persona_entregar : '',
                imagen1: esEdicion ? informe.imagen1 : '', // Solo mostrar imágenes en modo edición
                imagen2: esEdicion ? informe.imagen2 : ''  // Solo mostrar imágenes en modo edición
            };

            // Guardar las imágenes actuales
            currentImagen1Informe = valores.imagen1;
            currentImagen2Informe = valores.imagen2;

            // Renderizar el formulario con contenedores COMPLETAMENTE SEPARADOS
            contenedor.html(`
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white py-3" style="background-image: linear-gradient(to right, #CA3438, #d04a4e);">
            <h5 class="card-title mb-0 fw-bold">${titulo}</h5>
            <p class="card-subtitle mb-0 opacity-75 small">Complete la información del informe</p>
        </div>
        <div class="card-body p-4">
            <form id="formInforme" enctype="multipart/form-data">
                <input type="hidden" id="id_informe" name="id_informe" value="${valores.id_informe}">
                
               <!-- CONTENEDOR 1: TÍTULO Y CLIENTE (SIN MIN-HEIGHT) -->
<div class="container-fluid p-0 mb-3">
    <div class="row">
        <!-- TÍTULO DEL INFORME -->
        <div class="col-md-6">
            <div>
                <label for="titulo_informe" class="form-label fw-medium text-negro">Título del Informe <span class="text-danger">*</span></label>
              <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo_informe" name="titulo" value="${valores.titulo}" required>
<p id="error-titulo" class="text-danger small mt-1" style="display: none;">Este campo es requerido</p>
            </div>
        </div>
        
        <!-- CLIENTE BÚSQUEDA SOLAMENTE -->
        <div class="col-md-6">
            <div>
                <label for="cliente_search" class="form-label fw-medium text-negro">Cliente <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control border rounded-start-2 shadow-sm" id="cliente_search" placeholder="Buscar por nombre o documento..." autocomplete="off">
                    <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-search-cliente">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <input type="hidden" id="cliente_id" name="cliente_id" value="${valores.cliente_id}">
                <p id="error-cliente" class="text-danger small mt-1" style="display: none;">Este campo es requerido</p>
            </div>
        </div>
    </div>
</div>

<!-- CONTENEDOR 2: INFORMACIÓN DEL CLIENTE (SEPARADO Y OCULTO) -->
<div class="container-fluid p-0 mb-3" id="cliente_info_container" style="display: ${valores.cliente_id ? 'block' : 'none'};">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info mb-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="alert-heading mb-1">
                            <i class="fas fa-user me-2"></i>Cliente Seleccionado
                        </h6>
                        <div class="fw-bold" id="cliente_nombre">${valores.cliente_nombre || ''}</div>
                        <div class="text-muted small" id="cliente_documento">Documento: ${valores.cliente_documento || ''}</div>
                        <div class="text-muted small" id="cliente_direccion">Dirección: ${valores.cliente_direccion || 'No especificada'}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarCliente()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENEDOR 3: TIPO DE INFORME (INDEPENDIENTE) -->
                <div class="container-fluid p-0 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="min-height: 80px;">
                                <label for="tipo_informe" class="form-label fw-medium text-negro">Tipo de Informe <span class="text-danger">*</span></label>
                                <div class="input-group">
                                   <select class="form-control border rounded-start-2 shadow-sm" id="tipo_informe" name="tipo" required>
                                      <option value="">Seleccione un tipo</option>
                                                        </select>
                                    <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-gestionar-tipos" onclick="abrirModalTipos()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <p id="error-tipo" class="text-danger small mt-1" style="display: none;">Este campo es requerido</p>
                                <div class="form-text text-gris small">Este campo se usará para filtrar los informes.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Espacio vacío -->
                        </div>
                    </div>
                </div>
                
                <!-- CONTENEDOR 4: PERSONA A ENTREGAR (INDEPENDIENTE) -->
                <div class="container-fluid p-0 mb-3" id="campo-persona-entregar" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="min-height: 80px;">
                                <label for="persona_entregar" class="form-label fw-medium text-negro">
                                    <i class="fas fa-user me-1"></i>Persona a Entregar / Dirigido a
                                    <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" class="form-control border rounded-2 shadow-sm" 
                                       id="persona_entregar" name="persona_entregar" 
                                       value="${valores.persona_entregar || ''}" 
                                       placeholder="Nombre de la persona responsable o a quien va dirigido">
                                <div class="form-text text-gris small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Este campo aparece automáticamente cuando selecciona una empresa (RUC)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Espacio para balance visual -->
                        </div>
                    </div>
                </div>
                <!-- CONTENEDOR 5: CONTENIDO DEL INFORME (INDEPENDIENTE) -->
                <div class="container-fluid p-0 mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-negro">Contenido del Informe <span class="text-danger">*</span></label>
                            <div id="editor-container-informe" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                <!-- El editor Quill se cargará aquí -->
                            </div>
                            <p id="error-contenido" class="text-danger small mt-1" style="display: none;">Este campo es requerido</p>
                        </div>
                    </div>
                </div>

                <!-- CONTENEDOR 6: IMÁGENES DEL INFORME (DESPUÉS DEL CONTENIDO) -->
                <div class="container-fluid p-0 mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-medium text-negro mb-3">
                                <i class="fas fa-images me-2"></i>Imágenes del Informe (Opcional)
                                <small class="text-muted">- Aparecerán en una segunda página</small>
                            </h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="imagen1_informe" class="form-label fw-medium text-negro">Primera Imagen</label>
                            
                            <div class="image-preview-container" id="preview-container-1">
                                <input type="file" class="d-none" id="imagen1_informe" name="imagen1" 
                                       accept="image/png,image/jpeg,image/gif" onchange="handleImagePreview(this, 1)">
                                
                                <div id="upload-area-1" class="upload-area" onclick="document.getElementById('imagen1_informe').click()">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <div class="upload-placeholder">
                                        <strong>Haz clic para seleccionar</strong><br>
                                        <small>o arrastra una imagen aquí</small>
                                    </div>
                                </div>
                                
                                <div id="preview-area-1" class="preview-area" style="display: none;">
                                    <img id="preview-img-1" class="preview-image" onclick="showImageModal(this.src)">
                                    <div class="image-actions">
                                        <button type="button" class="btn-image-action btn-view" onclick="showImageModal(document.getElementById('preview-img-1').src)">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                        <button type="button" class="btn-image-action btn-remove" onclick="clearImagePreview(1)">
                                            <i class="fas fa-trash"></i> Quitar
                                        </button>
                                    </div>
                                    <div class="image-info" id="image-info-1"></div>
                                </div>
                            </div>
                            
                            <div class="form-text text-gris small mt-2">
                                <i class="fas fa-info-circle me-1"></i>Formatos: PNG, JPG, GIF (Máx. 5MB)
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="imagen2_informe" class="form-label fw-medium text-negro">Segunda Imagen</label>
                            
                            <div class="image-preview-container" id="preview-container-2">
                                <input type="file" class="d-none" id="imagen2_informe" name="imagen2" 
                                       accept="image/png,image/jpeg,image/gif" onchange="handleImagePreview(this, 2)">
                                
                                <div id="upload-area-2" class="upload-area" onclick="document.getElementById('imagen2_informe').click()">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <div class="upload-placeholder">
                                        <strong>Haz clic para seleccionar</strong><br>
                                        <small>o arrastra una imagen aquí</small>
                                    </div>
                                </div>
                                
                                <div id="preview-area-2" class="preview-area" style="display: none;">
                                    <img id="preview-img-2" class="preview-image" onclick="showImageModal(this.src)">
                                    <div class="image-actions">
                                        <button type="button" class="btn-image-action btn-view" onclick="showImageModal(document.getElementById('preview-img-2').src)">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                        <button type="button" class="btn-image-action btn-remove" onclick="clearImagePreview(2)">
                                            <i class="fas fa-trash"></i> Quitar
                                        </button>
                                    </div>
                                    <div class="image-info" id="image-info-2"></div>
                                </div>
                            </div>
                            
                            <div class="form-text text-gris small mt-2">
                                <i class="fas fa-info-circle me-1"></i>Formatos: PNG, JPG, GIF (Máx. 5MB)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para vista completa de imagen -->
                <div id="imageModal" class="image-modal" onclick="closeImageModal()">
                    <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
                    <div class="image-modal-content">
                        <img id="modalImage">
                    </div>
                </div>
              
                
            </form>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" onclick="volverAListaInformes()">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <div>
                    <button type="button" id="btn-preview-informe" class="btn border-rojo me-2">
                        <i class="fas fa-eye me-2"></i>Vista Previa
                    </button>
                  <button type="button" id="btn-save-informe" class="btn bg-rojo text-white" disabled>
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
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
`);


            // Inicializar el editor Quill con un pequeño delay para asegurar que el DOM esté listo
            setTimeout(() => {
                inicializarInformeEditor(valores.contenido);
            }, 100);

            // Inicializar autocomplete para búsqueda de clientes
            $("#cliente_search").autocomplete({
                source: _URL + "/ajs/buscar/cliente/datos", // Usamos la ruta existente
                minLength: 2,
                select: function (event, ui) {
                    event.preventDefault();

                    // Establecer los valores seleccionados
                    $("#cliente_id").val(ui.item.codigo);
                    $("#cliente_nombre").text(ui.item.datos);
                    $("#cliente_documento").text("Documento: " + ui.item.documento);
                    $("#cliente_direccion").text("Dirección: " + (ui.item.direccion || "No especificada"));

                    // Mostrar la información del cliente en el contenedor separado
                    $("#cliente_info_container").slideDown(300);

                    verificarTipoDocumento(ui.item.documento);
                    $(document).trigger('clienteSeleccionado');

                    // Establecer el valor en el campo de búsqueda
                    $(this).val(ui.item.datos);

                    return false;
                }
            }).autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<div class='autocomplete-item'><strong>" + item.documento + "</strong> | " + item.datos + "</div>")
                    .appendTo(ul);
            };

            // Agregar botón para limpiar la selección
            $("#btn-search-cliente").on("click", function () {
                if ($("#cliente_search").val().trim() === "") {
                    // Si está vacío, limpiar la selección
                    $("#cliente_id").val("");
                    $("#cliente_info_container").slideUp(300);
                    $("#campo-persona-entregar").slideUp(300);
                } else {
                    // Si tiene texto, iniciar búsqueda
                    $("#cliente_search").autocomplete("search", $("#cliente_search").val());
                }
            });

            // Si hay un cliente seleccionado, mostrar su información
            if (valores.cliente_id && valores.cliente_nombre) {
                $("#cliente_search").val(valores.cliente_nombre);
                $("#cliente_info_container").show();
                if (esEdicion && informe.cliente_documento) {
                    verificarTipoDocumento(informe.cliente_documento);
                }
            }

            // Manejar el envío del formulario usando el botón de guardar
            $("#btn-save-informe").on("click", function () {
                guardarInforme();
            });

            // Manejar la vista previa
            $("#btn-preview-informe").on("click", function () {
                mostrarVistaPrevia();
            });
            // Configurar eventos para las imágenes del informe
            $("#imagen1_informe").on("change", function () {
                previewImage(this, "imagen1-preview-informe", "imagen1-placeholder-informe");
                imagen1InformeChanged = true;
            });

            $("#imagen2_informe").on("change", function () {
                previewImage(this, "imagen2-preview-informe", "imagen2-placeholder-informe");
                imagen2InformeChanged = true;
            });

            // Botones de limpiar
            $("#reset-imagen1-informe").on("click", function () {
                resetImage("imagen1_informe", "imagen1-preview-informe", "imagen1-placeholder-informe", currentImagen1Informe);
                imagen1InformeChanged = false;
            });

            $("#reset-imagen2-informe").on("click", function () {
                resetImage("imagen2_informe", "imagen2-preview-informe", "imagen2-placeholder-informe", currentImagen2Informe);
                imagen2InformeChanged = false;
            });

            // Mostrar imágenes existentes si estamos editando (reemplazar código existente)
            if (valores.imagen1) {
                const container1 = document.getElementById('preview-container-1');
                const uploadArea1 = document.getElementById('upload-area-1');
                const previewArea1 = document.getElementById('preview-area-1');
                const previewImg1 = document.getElementById('preview-img-1');
                const imageInfo1 = document.getElementById('image-info-1');

                previewImg1.src = valores.imagen1;
                uploadArea1.style.display = 'none';
                previewArea1.style.display = 'block';
                container1.classList.add('has-image');
                imageInfo1.innerHTML = '<strong>Imagen existente</strong><br>Cargada previamente';
                currentImagen1Informe = valores.imagen1;
            }

            if (valores.imagen2) {
                const container2 = document.getElementById('preview-container-2');
                const uploadArea2 = document.getElementById('upload-area-2');
                const previewArea2 = document.getElementById('preview-area-2');
                const previewImg2 = document.getElementById('preview-img-2');
                const imageInfo2 = document.getElementById('image-info-2');

                previewImg2.src = valores.imagen2;
                uploadArea2.style.display = 'none';
                previewArea2.style.display = 'block';
                container2.classList.add('has-image');
                imageInfo2.innerHTML = '<strong>Imagen existente</strong><br>Cargada previamente';
                currentImagen2Informe = valores.imagen2;
            }

            // Cargar tipos de informe en el select
            cargarTiposInformeSelect(valores.tipo);

            // VINCULAR VALIDADORES
            vincularValidadores();
        }

        // Función para vincular los eventos de validación
        function vincularValidadores() {
            console.log("Vinculando validadores en tiempo real...");
            const contenedor = $('#nuevo-informe, #editar-informe');

            // Limpiar listeners anteriores para evitar duplicados
            contenedor.off('input blur', '#titulo_informe');
            contenedor.off('change', '#tipo_informe');
            $(document).off('clienteSeleccionado.informe');

            // Vincular nuevos listeners
            contenedor.on('input blur', '#titulo_informe', validarTitulo);
            contenedor.on('change', '#tipo_informe', validarTipo);
            $(document).on('clienteSeleccionado.informe', validarCliente);

            // Validación inicial
            setTimeout(() => {
                console.log("Realizando validación inicial...");
                validarTitulo();
                validarTipo();
                validarCliente();
                validarContenido();
            }, 500);
        }

        // Función para renderizar el formulario de plantilla

        function verificarTipoDocumento(documento) {
            // RUC en Perú: 11 dígitos y empieza con 20, 15, 16, 17
            const esRUC = documento && documento.length === 11 && /^(20|15|16|17)/.test(documento);

            if (esRUC) {
                $("#campo-persona-entregar").slideDown(300);
                $("#persona_entregar").attr('placeholder', 'Nombre de la persona responsable o a quien va dirigido');
            } else {
                $("#campo-persona-entregar").slideUp(300);
                $("#persona_entregar").val(''); // Limpiar el campo si no es RUC
            }
        }
        function renderizarFormularioPlantilla(data) {
            // Guardar las imágenes actuales
            currentHeaderTemplateImage = data.header_image;
            currentFooterTemplateImage = data.footer_image;

            // Renderizar el formulario
            $("#editar-plantilla").html(`
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white py-3" style="background-image: linear-gradient(to right, #CA3438, #d04a4e);">
                <h5 class="card-title mb-0 fw-bold">Editor de Plantilla de Informes</h5>
                <p class="card-subtitle mb-0 opacity-75 small">Personalice la plantilla base para todos los informes</p>
            </div>
            <div class="card-body p-4">
                <form id="formInformeTemplate" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="titulo_template" class="form-label fw-medium text-negro">Título por Defecto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo_template" name="titulo" value="${data.titulo || 'INFORME'}" required>
                        </div>
                    </div>
                    
                    <!-- Secciones para las imágenes -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="header_image_template" class="form-label fw-medium text-negro">Imagen de Encabezado</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control border rounded-start shadow-sm" id="header_image_template" name="header_image" accept="image/png,image/jpeg,image/gif">
                                <button class="btn btn-outline-secondary rounded-end" type="button" id="reset-header-template">Restablecer</button>
                            </div>
                            <div class="form-text text-gris small">Recomendado: imagen PNG de 210mm x 40mm (ancho completo A4)</div>
                            <div class="mt-2 border p-2 rounded bg-light">
                                <p class="mb-1 fw-bold">Vista previa:</p>
                                <div id="header-preview-container-template" class="text-center">
                                    <img id="header-preview-template" src="${data.header_image || '/placeholder.svg'}" alt="Vista previa del encabezado" class="img-fluid" style="max-height: 100px; ${data.header_image ? '' : 'display: none;'}">
                                    <div id="header-placeholder-template" class="text-muted" ${data.header_image ? 'style="display: none;"' : ''}>No se ha seleccionado ninguna imagen</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="footer_image_template" class="form-label fw-medium text-negro">Imagen de Pie de Página</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control border rounded-start shadow-sm" id="footer_image_template" name="footer_image" accept="image/png,image/jpeg,image/gif">
                                <button class="btn btn-outline-secondary rounded-end" type="button" id="reset-footer-template">Restablecer</button>
                            </div>
                            <div class="form-text text-gris small">Recomendado: imagen PNG de 210mm x 30mm (ancho completo A4)</div>
                            <div class="mt-2 border p-2 rounded bg-light">
                                <p class="mb-1 fw-bold">Vista previa:</p>
                                <div id="footer-preview-container-template" class="text-center">
                                    <img id="footer-preview-template" src="${data.footer_image || '/placeholder.svg'}" alt="Vista previa del pie de página" class="img-fluid" style="max-height: 100px; ${data.footer_image ? '' : 'display: none;'}">
                                    <div id="footer-placeholder-template" class="text-muted" ${data.footer_image ? 'style="display: none;"' : ''}>No se ha seleccionado ninguna imagen</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-negro">Contenido por Defecto</label>
                            <!-- Contenedor para el editor -->
                            <div id="editor-container-template" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                <!-- El editor Quill se cargará aquí -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="volverAListaInformes()">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <div>
                            <button type="button" id="btn-preview-template" class="btn border-rojo me-2">
                                <i class="fas fa-eye me-2"></i>Vista Previa
                            </button>
                            <button type="button" id="btn-save-template" class="btn bg-rojo text-white">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
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
    `);

            // Inicializar el editor Quill
            inicializarTemplateEditor(data.contenido);

            // Manejar el envío del formulario usando el botón de guardar
            $("#btn-save-template").on("click", function () {
                guardarTemplate();
            });

            // Manejar la vista previa
            $("#btn-preview-template").on("click", function () {
                mostrarVistaPreviewTemplate();
            });

            // Manejar la vista previa de las imágenes seleccionadas
            $("#header_image_template").on("change", function () {
                previewImage(this, "header-preview-template", "header-placeholder-template");
                headerTemplateImageChanged = true;
            });

            $("#footer_image_template").on("change", function () {
                previewImage(this, "footer-preview-template", "footer-placeholder-template");
                footerTemplateImageChanged = true;
            });

            // Manejar los botones de restablecer
            $("#reset-header-template").on("click", function () {
                resetImage("header_image_template", "header-preview-template", "header-placeholder-template", currentHeaderTemplateImage);
            });

            $("#reset-footer-template").on("click", function () {
                resetImage("footer_image_template", "footer-preview-template", "footer-placeholder-template", currentFooterTemplateImage);
            });
        }

        function inicializarInformeEditor(contenido = '') {
            try {
                console.log('Inicializando editor Quill para informes...');

                // Verificar si ya existe un editor y limpiarlo
                if (informeEditor) {
                    try {
                        informeEditor = null;
                    } catch (e) {
                        console.log('Limpiando editor anterior...');
                    }
                }

                // Verificar si Quill está cargado
                if (typeof Quill === 'undefined') {
                    console.log('Quill no está cargado, cargando dinámicamente...');
                    // Cargar Quill dinámicamente
                    const quillScript = document.createElement('script');
                    quillScript.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
                    document.head.appendChild(quillScript);

                    const quillStyle = document.createElement('link');
                    quillStyle.rel = 'stylesheet';
                    quillStyle.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                    document.head.appendChild(quillStyle);

                    quillScript.onload = function () {
                        console.log('Quill cargado dinámicamente, inicializando...');
                        inicializarQuillInforme(contenido);
                    };
                } else {
                    console.log('Quill ya está cargado, inicializando directamente...');
                    inicializarQuillInforme(contenido);
                }
            } catch (error) {
                console.error('Error al inicializar Quill para informes:', error);
            }
        }

        function inicializarQuillInforme(contenido) {
            const toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ];

            let intentos = 0;
            const maxIntentos = 20; // Máximo 1 segundo (20 * 50ms)

            // Función para intentar inicializar el editor
            const intentarInicializar = () => {
                intentos++;
                const editorContainer = document.getElementById('editor-container-informe');
                if (!editorContainer) {
                    if (intentos >= maxIntentos) {
                        console.error('No se pudo encontrar el contenedor del editor después de múltiples intentos');
                        return;
                    }
                    console.log(`Contenedor del editor no encontrado, reintentando en 50ms... (intento ${intentos}/${maxIntentos})`);
                    setTimeout(intentarInicializar, 50);
                    return;
                }

                console.log('Contenedor del editor encontrado, limpiando y preparando...');
                
                // Limpiar el contenedor del editor
                editorContainer.innerHTML = '';
                
                // Crear un div interno para el editor
                const editorDiv = document.createElement('div');
                editorDiv.id = 'quill-editor-informe-' + Date.now();
                editorDiv.style.minHeight = '400px';
                editorContainer.appendChild(editorDiv);
                
                console.log('Contenedor preparado, inicializando Quill...');

            informeEditor = new Quill('#' + editorDiv.id, {
                modules: {
                    toolbar: toolbarOptions,
                    clipboard: {
                        matchVisual: false
                    }
                },
                theme: 'snow',
                placeholder: 'Contenido del informe...',
                bounds: '#' + editorDiv.id // Limitar el alcance del editor a su contenedor
            });

            // VINCULAR VALIDACIÓN DE CONTENIDO
            if (informeEditor) {
                informeEditor.on('text-change', function(delta, oldDelta, source) {
                    if (source === 'user') {
                        validarContenido();
                    }
                });
            }

            // Establecer el contenido inicial
            if (contenido) {
                try {
                    informeEditor.root.innerHTML = contenido;
                    console.log('Contenido del informe cargado en el editor');
                } catch (e) {
                    console.warn('No se pudo cargar el contenido inicial:', e);
                }
            }

            console.log('Editor Quill para informes inicializado correctamente');

            // Asegurarse de que los eventos de los botones funcionen
            setTimeout(function () {
                // Volver a asignar los eventos a los botones
                $("#btn-preview-informe").off('click').on("click", function () {
                    mostrarVistaPrevia();
                });

                $("#btn-save-informe").off('click').on("click", function () {
                    guardarInforme();
                });
            }, 500);
            };

            // Iniciar el proceso de inicialización
            intentarInicializar();
        }

        // Función para inicializar el editor de plantillas
        function inicializarTemplateEditor(contenido = '') {
            try {
                console.log('Inicializando editor Quill para plantillas...');

                if (templateEditor) {
                    templateEditor.root.innerHTML = contenido;
                    return;
                }

                // Verificar si Quill está cargado
                if (typeof Quill === 'undefined') {
                    // Cargar Quill dinámicamente
                    const quillScript = document.createElement('script');
                    quillScript.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
                    document.head.appendChild(quillScript);

                    const quillStyle = document.createElement('link');
                    quillStyle.rel = 'stylesheet';
                    quillStyle.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                    document.head.appendChild(quillStyle);

                    quillScript.onload = function () {
                        inicializarQuillTemplate(contenido);
                    };
                } else {
                    inicializarQuillTemplate(contenido);
                }
            } catch (error) {
                console.error('Error al inicializar Quill para plantillas:', error);
            }
        }

        function inicializarQuillTemplate(contenido) {
            console.log('Inicializando Quill Template con contenido:', contenido);

            const editorContainer = document.getElementById('editor-container-template');
            if (!editorContainer) {
                console.error('No se encontró el contenedor del editor de plantillas');
                return;
            }

            if (templateEditor) {
                templateEditor = null;
            }

            while (editorContainer.firstChild) {
                editorContainer.removeChild(editorContainer.firstChild);
            }
            editorContainer.innerHTML = '';
            editorContainer.textContent = '';

            editorContainer.offsetHeight;

            const editorDiv = document.createElement('div');
            editorDiv.id = 'quill-editor-' + Date.now();
            editorDiv.style.minHeight = '300px';
            // IMPORTANTE: No agregar borde aquí para evitar duplicación
            editorDiv.style.border = 'none';
            editorContainer.appendChild(editorDiv);

            const toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ];

            templateEditor = new Quill('#' + editorDiv.id, {
                modules: {
                    toolbar: toolbarOptions,
                    clipboard: {
                        matchVisual: false
                    }
                },
                theme: 'snow',
                placeholder: 'Escriba el contenido de la plantilla aquí...',
                bounds: '#editor-container-template'
            });

            templateEditor.setText('');

            if (contenido && contenido.trim() !== '') {
                setTimeout(function () {
                    templateEditor.root.innerHTML = contenido;
                }, 100);
            }

            console.log('Editor Quill para plantillas inicializado correctamente');
        }
        function cargarClientes(clienteSeleccionado = '') {
            $.ajax({
                url: _URL + "/ajs/clientes/listar",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data && data.length > 0) {
                        let options = '<option value="">Seleccione un cliente</option>';
                        data.forEach(function (cliente) {
                            const selected = cliente.id == clienteSeleccionado ? 'selected' : '';
                            options += `<option value="${cliente.id}" ${selected}>${cliente.nombre}</option>`;
                        });
                        $("#cliente_id").html(options);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar clientes:", status, error);
                }
            });
        }
// Variables para controlar el estado de validación
let camposValidos = {
    titulo: false,
    tipo: false,
    cliente: false,
    contenido: false
};

// Función para validar todos los campos
function validarFormulario() {
    const todosCamposValidos = Object.values(camposValidos).every(valido => valido);
    const btnGuardar = document.getElementById('btn-save-informe');
    
    if (todosCamposValidos) {
        btnGuardar.disabled = false;
        btnGuardar.classList.remove('btn-secondary');
        btnGuardar.classList.add('bg-rojo', 'text-white');
    } else {
        btnGuardar.disabled = true;
        btnGuardar.classList.remove('bg-rojo', 'text-white');
        btnGuardar.classList.add('btn-secondary');
    }
}

// Función para mostrar/ocultar errores
function mostrarError(campo, mostrar) {
    const errorElement = document.getElementById(`error-${campo}`);
    if (errorElement) {
        errorElement.style.display = mostrar ? 'block' : 'none';
    }
}

// Validación del título
function validarTitulo() {
    const elemento = document.getElementById('titulo_informe');
    if (!elemento) return; // Si el elemento no existe, salir de la función
    
    const titulo = elemento.value.trim();
    const esValido = titulo.length > 0;
    
    camposValidos.titulo = esValido;
    mostrarError('titulo', !esValido);
    validarFormulario();
}

// Validación del tipo

   function validarTipo() {
    const elemento = document.getElementById('tipo_informe');
    if (!elemento) return; // ✅ CORRECTO - verifica que el elemento existe
    
    const tipo = elemento.value.trim();
    const esValido = tipo.length > 0;
    
    camposValidos.tipo = esValido;
    mostrarError('tipo', !esValido);
    validarFormulario();
}

// Validación del cliente
    function validarCliente() {
        const elemento = document.getElementById('cliente_id');
    if (!elemento) return; // ✅ CORRECTO - verifica que el elemento existe

    const clienteId = elemento.value.trim();
    const esValido = clienteId.length > 0;
    
    camposValidos.cliente = esValido;
    mostrarError('cliente', !esValido);
    validarFormulario();
}

// Validación del contenido
function validarContenido() {
    if (informeEditor) {
        const contenido = informeEditor.getText().trim();
        const esValido = contenido.length > 0;
        
        camposValidos.contenido = esValido;
        mostrarError('contenido', !esValido);
        validarFormulario();
    }
}

// La lógica de validación se ha movido a las funciones de renderizado e inicialización para asegurar que los elementos existan.
        // Función para guardar un informe
        function guardarInforme() {
            // Obtener el contenido del editor
            if (!informeEditor) {
                Swal.fire({
                    title: 'Error',
                    text: 'El editor no está inicializado correctamente',
                    icon: 'error'
                });
                return;
            }

            const contenido = informeEditor.root.innerHTML;
            const tipo = $("#tipo_informe").val();
            const titulo = $("#titulo_informe").val();
            const cliente_id = $("#cliente_id").val();
            const persona_entregar = $("#persona_entregar").val();

        

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando informe...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('tipo', tipo);
            formData.append('titulo', titulo);
            formData.append('contenido', contenido);
            formData.append('cliente_id', cliente_id);
            formData.append('persona_entregar', persona_entregar);

            if (editMode) {
                formData.append('id_informe', informeId);
            }

            // Añadir las imágenes específicas del informe
            if (imagen1InformeChanged && document.getElementById('imagen1_informe').files[0]) {
                formData.append('imagen1', document.getElementById('imagen1_informe').files[0]);
            } else if (currentImagen1Informe && editMode) {
                formData.append('imagen1_base64', currentImagen1Informe);
            }

            if (imagen2InformeChanged && document.getElementById('imagen2_informe').files[0]) {
                formData.append('imagen2', document.getElementById('imagen2_informe').files[0]);
            } else if (currentImagen2Informe && editMode) {
                formData.append('imagen2_base64', currentImagen2Informe);
            }

            // Determinar la URL según el modo
            const url = editMode ?
                _URL + "/ajs/informe/editar" :
                _URL + "/ajs/informe/insertar";

            // Enviar datos al servidor
            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.res) {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.msg,
                            icon: 'success'
                        }).then(() => {
                            // REFRESCAR LA PÁGINA COMPLETA
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo guardar el informe',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al guardar el informe:", status, error);
                    console.log("Respuesta del servidor:", xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }

        // Función para guardar la plantilla
        function guardarTemplate() {
            // Obtener el contenido del editor
            if (!templateEditor) {
                Swal.fire({
                    title: 'Error',
                    text: 'El editor no está inicializado correctamente',
                    icon: 'error'
                });
                return;
            }

            const contenido = templateEditor.root.innerHTML;
            const titulo = $("#titulo_template").val();

            // Validar que haya contenido
            if (!titulo.trim()) {
                Swal.fire({
                    title: 'Error',
                    text: 'El título no puede estar vacío',
                    icon: 'error'
                });
                return;
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando cambios...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('titulo', titulo);
            formData.append('contenido', contenido);

            // Añadir las imágenes si han sido cambiadas
            if (headerTemplateImageChanged && document.getElementById('header_image_template').files[0]) {
                formData.append('header_image', document.getElementById('header_image_template').files[0]);
            }

            if (footerTemplateImageChanged && document.getElementById('footer_image_template').files[0]) {
                formData.append('footer_image', document.getElementById('footer_image_template').files[0]);
            }

            // Enviar datos al servidor
            $.ajax({
                url: _URL + "/ajs/informe/guardar-template",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // Actualizar las imágenes actuales si se proporcionaron nuevas URLs
                        if (data.header_image) {
                            currentHeaderTemplateImage = data.header_image;
                        }

                        if (data.footer_image) {
                            currentFooterTemplateImage = data.footer_image;
                        }

                        // Restablecer los indicadores de cambio
                        headerTemplateImageChanged = false;
                        footerTemplateImageChanged = false;

                        Swal.fire({
                            title: 'Éxito',
                            text: 'La plantilla se ha guardado correctamente',
                            icon: 'success'
                        }).then(() => {
                            // Volver a la lista de informes
                            volverAListaInformes();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo guardar la plantilla',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al guardar la plantilla:", status, error);
                    console.log("Respuesta del servidor:", xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }

        // Función para mostrar la vista previa de un informe
        function mostrarVistaPrevia() {
            // Verificar que el editor esté inicializado
            if (!informeEditor) {
                Swal.fire({
                    title: 'Error',
                    text: 'El editor no está inicializado correctamente',
                    icon: 'error'
                });
                return;
            }

            // Obtener el contenido actual
            const contenido = informeEditor.root.innerHTML;
            const titulo = $("#titulo_informe").val();

            if (!titulo.trim()) {
                Swal.fire({
                    title: 'Error',
                    text: 'El título no puede estar vacío',
                    icon: 'error'
                });
                return;
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Generando vista previa',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear un objeto FormData para enviar archivos
            const formData = new FormData();
            formData.append('titulo', titulo);
            formData.append('contenido', contenido);

            // Añadir las imágenes específicas del informe para vista previa
            if (imagen1InformeChanged && document.getElementById('imagen1_informe').files[0]) {
                formData.append('imagen1_informe', document.getElementById('imagen1_informe').files[0]);
            } else if (currentImagen1Informe) {
                formData.append('imagen1_informe_base64', currentImagen1Informe);
            }

            if (imagen2InformeChanged && document.getElementById('imagen2_informe').files[0]) {
                formData.append('imagen2_informe', document.getElementById('imagen2_informe').files[0]);
            } else if (currentImagen2Informe) {
                formData.append('imagen2_informe_base64', currentImagen2Informe);
            }


            // Enviar datos para generar vista previa
            $.ajax({
                url: _URL + "/ajs/informe/vista-previa",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        // Crear un objeto Blob con el PDF base64
                        const byteCharacters = atob(data.pdfBase64);
                        const byteNumbers = new Array(byteCharacters.length);
                        for (let i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/pdf' });

                        // Crear una URL para el blob
                        const pdfUrl = URL.createObjectURL(blob);

                        // Mostrar el PDF en el iframe
                        $("#preview-frame-informe").attr("src", pdfUrl);
                        $("#previewInformeModal").modal("show");

                        // Limpiar la URL cuando se cierre el modal
                        $("#previewInformeModal").on('hidden.bs.modal', function () {
                            URL.revokeObjectURL(pdfUrl);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo generar la vista previa',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al generar vista previa:", status, error);
                    console.log("Respuesta del servidor:", xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }

        function mostrarVistaPreviewTemplate() {
            console.log('Función mostrarVistaPreviewTemplate llamada');

            // Prevenir ejecuciones múltiples
            if (vistaPreviewEnProceso) {
                console.log('Vista previa ya en proceso, ignorando...');
                return;
            }

            vistaPreviewEnProceso = true;
            console.log('Vista previa clickeada UNA vez');

            if (!templateEditor) {
                console.error('templateEditor no está inicializado');
                vistaPreviewEnProceso = false;
                Swal.fire({
                    title: 'Error',
                    text: 'El editor no está inicializado correctamente',
                    icon: 'error'
                });
                return;
            }

            const contenido = templateEditor.root.innerHTML;
            const titulo = $("#titulo_template").val();

            Swal.fire({
                title: 'Generando vista previa',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('titulo', titulo);
            formData.append('contenido', contenido);

            if (headerTemplateImageChanged && document.getElementById('header_image_template').files[0]) {
                formData.append('header_image', document.getElementById('header_image_template').files[0]);
            } else if (currentHeaderTemplateImage) {
                formData.append('header_image_base64', currentHeaderTemplateImage);
            }

            if (footerTemplateImageChanged && document.getElementById('footer_image_template').files[0]) {
                formData.append('footer_image', document.getElementById('footer_image_template').files[0]);
            } else if (currentFooterTemplateImage) {
                formData.append('footer_image_base64', currentFooterTemplateImage);
            }

            $.ajax({
                url: _URL + "/ajs/informe/vista-previa",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    vistaPreviewEnProceso = false;
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        const byteCharacters = atob(data.pdfBase64);
                        const byteNumbers = new Array(byteCharacters.length);
                        for (let i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/pdf' });
                        const pdfUrl = URL.createObjectURL(blob);

                        // Guardar el contenido actual del editor antes de cerrarlo
                        const savedContent = templateEditor.root.innerHTML;
                        const savedTitle = $("#titulo_template").val();

                        // Destruir el editor actual para evitar duplicación
                        try {
                            // Eliminar todos los elementos de la barra de herramientas
                            const toolbarElement = document.querySelector('#editor-container-template .ql-toolbar');
                            if (toolbarElement) {
                                toolbarElement.remove();
                            }

                            // Limpiar el contenedor
                            const container = document.getElementById('editor-container-template');
                            if (container) {
                                container.innerHTML = '';
                            }

                            templateEditor = null;
                        } catch (e) {
                            console.error('Error al limpiar editor:', e);
                        }

                        const modalId = 'previewTemplateModal_' + Date.now();
                        const previewModal = `
                    <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true" style="z-index: 1060 !important;">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Vista Previa de la Plantilla</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe style="width: 100%; height: 600px; border: none;" src="${pdfUrl}"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                        $('[id^="previewTemplateModal_"]').remove();
                        $("body").append(previewModal);
                        $("#editarPlantillaInformeModal").modal("hide");

                        setTimeout(function () {
                            const modalElement = document.getElementById(modalId);
                            const bsModal = new bootstrap.Modal(modalElement);
                            bsModal.show();

                            $(modalElement).on('hidden.bs.modal', function () {
                                URL.revokeObjectURL(pdfUrl);
                                $(modalElement).remove();

                                // Volver a mostrar el modal principal
                                $("#editarPlantillaInformeModal").modal("show");

                                // Recrear el editor con el contenido guardado después de que el modal esté visible
                                $("#editarPlantillaInformeModal").one('shown.bs.modal', function () {
                                    setTimeout(function () {
                                        // Limpiar cualquier texto residual en el contenedor
                                        const editorContainer = document.getElementById('editor-container-template');
                                        if (editorContainer) {
                                            editorContainer.innerHTML = '';
                                        }

                                        // Inicializar un nuevo editor con el contenido guardado
                                        inicializarQuillTemplate(savedContent);
                                        $("#titulo_template").val(savedTitle);
                                    }, 300);
                                });
                            });
                        }, 300);
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.msg || 'No se pudo generar la vista previa',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    vistaPreviewEnProceso = false;
                    console.error("Error al generar vista previa:", status, error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });
                }
            });
        }
        // Función para eliminar un informe
        function eliminarInforme(id) {
            // Mostrar indicador de carga
            $("#btn-confirmar-eliminar-informe").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...').prop('disabled', true);

            // Realizar petición AJAX para eliminar el informe
            $.ajax({
                url: _URL + "/ajs/informe/borrar",
                method: "POST",
                data: { id_informe: id },
                dataType: 'json',
                success: function (data) {
                    // Cerrar el modal
                    $('#confirmarEliminarInformeModal').modal('hide');

                    if (data.res) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            title: 'Éxito',
                            text: data.msg,
                            icon: 'success'
                        }).then(() => {
                            // Recargar los informes
                            cargarInformes();
                        });
                    } else {
                        // Mostrar mensaje de error
                        Swal.fire({
                            title: 'Error',
                            text: data.msg,
                            icon: 'error'
                        });
                    }

                    // Restaurar el botón
                    $("#btn-confirmar-eliminar-informe").html('Eliminar').prop('disabled', false);
                },
                error: function (xhr, status, error) {
                    console.error("Error al eliminar informe:", status, error);

                    // Cerrar el modal
                    $('#confirmarEliminarInformeModal').modal('hide');

                    // Mostrar mensaje de error
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error'
                    });

                    // Restaurar el botón
                    $("#btn-confirmar-eliminar-informe").html('Eliminar').prop('disabled', false);
                }
            });
        }

        // Función para volver a la lista de informes
        function volverAListaInformes() {
            // Limpiar los editores
            informeEditor = null;
            templateEditor = null;
            imagen1InformeChanged = false;
            imagen2InformeChanged = false;
            currentImagen1Informe = null;
            currentImagen2Informe = null;

            // Mostrar la pestaña de lista
            $('#nuevo-informe, #editar-informe, #editar-plantilla').removeClass('show active');
            $('#lista-informes').addClass('show active');

            // Recargar los informes
            cargarInformes();
        }

        // Función auxiliar para previsualizar imágenes
        function previewImage(input, previewId, placeholderId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);

            // Verificar que los elementos existan antes de continuar
            if (!preview || !placeholder) {
                console.warn(`Elementos de preview no encontrados: previewId=${previewId}, placeholderId=${placeholderId}`);
                return;
            }

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    // Verificar nuevamente que el elemento existe antes de establecer src
                    if (!preview || !preview.isConnected) {
                        console.warn(`Elemento preview ${previewId} ya no está en el DOM`);
                        return;
                    }
                    
                    try {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                        placeholder.style.display = "none";
                    } catch (error) {
                        console.error(`Error al establecer src en ${previewId}:`, error);
                    }
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                // NO mostrar imagen por defecto, solo ocultar
                try {
                    preview.style.display = "none";
                    placeholder.style.display = "block";
                    preview.removeAttribute('src'); // ELIMINAR cualquier src por defecto
                } catch (error) {
                    console.error(`Error al ocultar elementos de preview:`, error);
                }
            }
        }

        // Función auxiliar para restablecer imágenes
        function resetImage(inputId, previewId, placeholderId, defaultImage) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);

            // Verificar que los elementos existan antes de continuar
            if (!input || !preview || !placeholder) {
                console.warn(`Elementos de reset no encontrados: inputId=${inputId}, previewId=${previewId}, placeholderId=${placeholderId}`);
                return;
            }

            // Limpiar el input file
            input.value = "";

            try {
                if (defaultImage) {
                    // Si hay una imagen por defecto, mostrarla
                    preview.src = defaultImage;
                    preview.style.display = "block";
                    placeholder.style.display = "none";
                } else {
                    // Si no hay imagen por defecto, mostrar el placeholder
                    preview.style.display = "none";
                    placeholder.style.display = "block";
                }
            } catch (error) {
                console.error(`Error al restablecer imagen en ${previewId}:`, error);
            }

            // Marcar que la imagen ha sido restablecida
            if (inputId === "header_image") {
                headerImageChanged = false;
            } else if (inputId === "footer_image") {
                footerImageChanged = false;
            } else if (inputId === "header_image_template") {
                headerTemplateImageChanged = false;
            } else if (inputId === "footer_image_template") {
                footerTemplateImageChanged = false;
            } else if (inputId === "imagen1_informe") {
                imagen1InformeChanged = false;
            } else if (inputId === "imagen2_informe") {
                imagen2InformeChanged = false;
            }

        }
        function renderPdfPreview(pdfUrl, canvasId) {
            console.log('Renderizando PDF:', pdfUrl, 'en canvas:', canvasId);

            // Verificar que PDF.js esté configurado
            if (typeof pdfjsLib === 'undefined') {
                console.error('Error: PDF.js no está cargado');
                return;
            }

            // Verificar que el worker esté configurado
            if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
            }

            const canvas = document.getElementById(canvasId);
            if (!canvas) {
                console.error('Canvas no encontrado:', canvasId);
                return;
            }

            // Ocultar el canvas y mostrar el spinner de carga
            const loadingElement = document.getElementById(`loading-${canvasId}`);
            if (loadingElement) {
                loadingElement.style.display = 'block';
            }
            canvas.style.display = 'none';

            // Primero obtener el PDF como base64
            fetch(pdfUrl, {
                    headers: { 'token-app': localStorage.getItem("_token") }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.pdfBase64) {
                        // Convertir base64 a Uint8Array
                        const pdfData = atob(data.pdfBase64);
                        const pdfArray = new Uint8Array(pdfData.length);
                        for (let i = 0; i < pdfData.length; i++) {
                            pdfArray[i] = pdfData.charCodeAt(i);
                        }

                        // Cargar el PDF desde el array de bytes
                        return pdfjsLib.getDocument({ data: pdfArray }).promise;
                    } else {
                        throw new Error(data.error || 'Error al obtener el PDF');
                    }
                })
                .then(function (pdf) {
                    console.log('PDF cargado correctamente, páginas:', pdf.numPages);
                    
                    // Obtener la primera página
                    return pdf.getPage(1);
                })
                .then(function (page) {
                    console.log('Página obtenida, renderizando...');
                    
                    const context = canvas.getContext('2d');
                    
                    // Obtener el tamaño del contenedor padre
                    const container = canvas.parentElement;
                    const containerWidth = container.clientWidth;
                    const containerHeight = container.clientHeight;
                    
                    // Establecer el tamaño del canvas
                    canvas.width = containerWidth;
                    canvas.height = containerHeight;
                    
                    // Obtener el viewport original del PDF
                    const viewport = page.getViewport({ scale: 1.0 });
                    
                    // Calcular la escala para que el PDF se ajuste al ancho del canvas
                    const scale = containerWidth / viewport.width;
                    const scaledViewport = page.getViewport({ scale: scale });
                    
                    // Alineación superior (offsetY = 0)
                    const offsetY = 0;
                    
                    // Renderizar la página
                    const renderContext = {
                        canvasContext: context,
                        viewport: scaledViewport,
                        transform: [1, 0, 0, 1, 0, offsetY]
                    };
                    
                    // Limpiar canvas
                    context.fillStyle = 'white';
                    context.fillRect(0, 0, canvas.width, canvas.height);
                    
                    return page.render(renderContext).promise;
                })
                .then(function () {
                    console.log('PDF renderizado correctamente en', canvasId);
                    
                    // Ocultar el spinner de carga y mostrar el canvas
                    const loadingElement = document.getElementById(`loading-${canvasId}`);
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                    canvas.style.display = 'block';
                })
                .catch(function (error) {
                    console.error('Error al procesar el PDF:', error);
                    
                    // Ocultar el spinner de carga y mostrar mensaje de error
                    const loadingElement = document.getElementById(`loading-${canvasId}`);
                    if (loadingElement) {
                        loadingElement.innerHTML = `
                            <div class="text-center p-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <p class="text-muted mb-2">Error al cargar la vista previa</p>
                                <button class="btn btn-sm btn-outline-primary" onclick="window.open('${pdfUrl.replace('generarPDFBase64', 'generarPDF')}', '_blank')">
                                    <i class="fas fa-external-link-alt me-1"></i>Abrir PDF
                                </button>
                            </div>
                        `;
                        loadingElement.style.display = 'block';
                    }
                    canvas.style.display = 'none';
                });
        }
        // Función para abrir el modal de tipos
        function abrirModalTipos() {
            cargarTiposInforme();
            $('#gestionarTiposInformeModal').modal('show');
        }
        // Funciones mejoradas para manejo de imágenes
        function handleImagePreview(input, imageNumber) {
            const file = input.files[0];
            const container = document.getElementById(`preview-container-${imageNumber}`);

            // VERIFICAR QUE LOS ELEMENTOS EXISTAN
            if (!container) {
                console.error(`Container preview-container-${imageNumber} no encontrado`);
                return;
            }

            const uploadArea = document.getElementById(`upload-area-${imageNumber}`);
            const previewArea = document.getElementById(`preview-area-${imageNumber}`);
            const previewImg = document.getElementById(`preview-img-${imageNumber}`);
            const imageInfo = document.getElementById(`image-info-${imageNumber}`);

            if (file) {
                // Validar tamaño (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Archivo muy grande',
                        text: 'El archivo debe ser menor a 5MB',
                        icon: 'warning'
                    });
                    input.value = '';
                    return;
                }

                // Validar tipo
                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        title: 'Tipo de archivo no válido',
                        text: 'Solo se permiten imágenes (PNG, JPG, GIF)',
                        icon: 'warning'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    uploadArea.style.display = 'none';
                    previewArea.style.display = 'block';
                    container.classList.add('has-image');

                    // Mostrar información del archivo
                    const sizeKB = (file.size / 1024).toFixed(1);
                    imageInfo.innerHTML = `
                <strong>${file.name}</strong><br>
                Tamaño: ${sizeKB} KB | Tipo: ${file.type.split('/')[1].toUpperCase()}
            `;

                    // Marcar como cambiado
                    if (imageNumber === 1) {
                        imagen1InformeChanged = true;
                    } else {
                        imagen2InformeChanged = true;
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function clearImagePreview(imageNumber) {
            const input = document.getElementById(`imagen${imageNumber}_informe`);
            const container = document.getElementById(`preview-container-${imageNumber}`);
            const uploadArea = document.getElementById(`upload-area-${imageNumber}`);
            const previewArea = document.getElementById(`preview-area-${imageNumber}`);

            // Limpiar input
            input.value = '';

            // Mostrar área de upload y ocultar preview
            uploadArea.style.display = 'block';
            previewArea.style.display = 'none';
            container.classList.remove('has-image');

            // Marcar como no cambiado
            if (imageNumber === 1) {
                imagen1InformeChanged = false;
                currentImagen1Informe = null;
            } else {
                imagen2InformeChanged = false;
                currentImagen2Informe = null;
            }
        }

        function showImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');

            modal.style.display = 'block';
            modalImg.src = imageSrc;

            // Prevenir scroll del body
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';

            // Restaurar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Agregar soporte para drag & drop
        function setupDragAndDrop() {
            [1, 2].forEach(imageNumber => {
                const container = document.getElementById(`preview-container-${imageNumber}`);
                const input = document.getElementById(`imagen${imageNumber}_informe`);

                // VERIFICAR QUE LOS ELEMENTOS EXISTAN
                if (!container || !input) {
                    console.log(`Elementos drag&drop no encontrados para imagen ${imageNumber}`);
                    return;
                }

                container.addEventListener('dragover', function (e) {

                    e.preventDefault();
                    container.style.borderColor = '#CA3438';
                    container.style.backgroundColor = '#fff5f5';
                });

                container.addEventListener('dragleave', function (e) {
                    e.preventDefault();
                    container.style.borderColor = '#e0e0e0';
                    container.style.backgroundColor = '';
                });

                container.addEventListener('drop', function (e) {
                    e.preventDefault();
                    container.style.borderColor = '#e0e0e0';
                    container.style.backgroundColor = '';

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        handleImagePreview(input, imageNumber);
                    }
                });
            });
        }
        // Función para limpiar cliente seleccionado
        function limpiarCliente() {
            $("#cliente_id").val("");
            $("#cliente_search").val("");
            $("#cliente_info_container").slideUp(300);
            $("#campo-persona-entregar").slideUp(300);
        }


        // Inicializar drag & drop cuando se carga el formulario
        setTimeout(function () {
            setupDragAndDrop();
        }, 500); // Dar tiempo a que se cree el DOM


        // Función para cargar tipos de informe en el select
        function cargarTiposInformeSelect(tipoSeleccionado = '') {
            cargarTiposInformeParaSelect('tipo_informe', tipoSeleccionado);
        }

        // Función para cargar tipos de informe en un select específico
        function cargarTiposInformeParaSelect(selectId, tipoSeleccionado = '') {
            $.ajax({
                url: _URL + "/ajs/informe/obtener-tipos-informe",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.tipos) {
                        let options = '<option value="">Seleccione un tipo</option>';
                        data.tipos.forEach(function (tipo) {
                            const selected = tipo.nombre === tipoSeleccionado ? 'selected' : '';
                            options += `<option value="${tipo.nombre}" ${selected}>${tipo.nombre}</option>`;
                        });
                        $("#" + selectId).html(options);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar tipos:", error);
                }
            });
        }

        // Función para cargar tipos en el modal
        function cargarTiposInforme() {
            $.ajax({
                url: _URL + "/ajs/informe/obtener-tipos-informe",
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
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editarTipo(${tipo.id}, '${tipo.nombre}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarTipo(${tipo.id}, '${tipo.nombre}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                        });
                        $("#lista-tipos-informe").html(html);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar tipos:", error);
                }
            });
        }

        // Función para agregar nuevo tipo
        function agregarTipoInforme() {
            const nombre = $("#nuevo-tipo-nombre").val().trim();

            if (!nombre) {
                Swal.fire('Error', 'El nombre es obligatorio', 'error');
                return;
            }

            $.ajax({
                url: _URL + "/ajs/informe/insertar-tipo-informe",
                method: "POST",
                data: {
                    nombre: nombre
                    // descripcion: descripcion
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Éxito', data.msg, 'success');
                        $("#nuevo-tipo-nombre").val('');
                        cargarTiposInforme();
                        cargarTiposInformeSelect(); // Actualizar el select también
                    } else {
                        Swal.fire('Error', data.msg, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                }
            });
        }

        // Función para editar tipo
        function editarTipo(id, nombre) {
            $("#editar-tipo-id").val(id);
            $("#editar-tipo-nombre").val(nombre);
            $("#editarTipoModal").modal('show');
        }

        // Función para guardar tipo editado
        function guardarTipoEditado() {
            const id = $("#editar-tipo-id").val();
            const nombre = $("#editar-tipo-nombre").val().trim();

            if (!nombre) {
                Swal.fire('Error', 'El nombre es obligatorio', 'error');
                return;
            }

            $.ajax({
                url: _URL + "/ajs/informe/editar-tipo-informe",
                method: "POST",
                data: {
                    id: id,
                    nombre: nombre
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Éxito', data.msg, 'success');
                        $("#editarTipoModal").modal('hide');
                        cargarTiposInforme();
                        cargarTiposInformeSelect(); // Actualizar el select también
                    } else {
                        Swal.fire('Error', data.msg, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                }
            });
        }

        // Función para eliminar tipo
        function eliminarTipo(id, nombre) {
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
                        url: _URL + "/ajs/informe/eliminar-tipo-informe",
                        method: "POST",
                        data: { id: id },
                        dataType: 'json',
                        success: function (data) {
                            if (data.success) {
                                Swal.fire('Eliminado', data.msg, 'success');
                                cargarTiposInforme();
                                cargarTiposInformeSelect(); // Actualizar el select también
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

        // Funciones globales para compatibilidad con HTML
        window.mostrarFormularioNuevoInforme = mostrarFormularioNuevoInforme;
        window.editarInforme = editarInforme;
        window.volverAListaInformes = volverAListaInformes;
        window.abrirModalTipos = abrirModalTipos;
        window.agregarTipoInforme = agregarTipoInforme;
        window.editarTipo = editarTipo;
        window.guardarTipoEditado = guardarTipoEditado;
        window.eliminarTipo = eliminarTipo;
        window.handleImagePreview = handleImagePreview;
        window.clearImagePreview = clearImagePreview;
        window.showImageModal = showImageModal;
        window.closeImageModal = closeImageModal;
        window.limpiarCliente = limpiarCliente;

        // Exponer funciones públicas
        return {
            init: inicializarModuloInformes,
            cleanup: limpiarModulo,
            cargarInformes: cargarInformes,
            reiniciar: reiniciarModuloCompleto,
            mostrarFormularioNuevoInforme: mostrarFormularioNuevoInforme,
            editarInforme: editarInforme,
            eliminarInforme: eliminarInforme,
            volverAListaInformes: volverAListaInformes,
            renderPdfPreview: renderPdfPreview
        };
    })();

            // Función para restaurar la funcionalidad del modal después de alertas
        function restaurarFuncionalidadModal() {
            // Asegurar que el modal esté visible y funcional
            $('#editarInformeModal').modal('show');
            
            // Re-enfocar el modal
            setTimeout(() => {
                $('#editarInformeModal').modal('show');
                
                // Re-habilitar todos los campos del formulario
                $('#editarInformeModal input, #editarInformeModal select, #editarInformeModal textarea').prop('disabled', false);
                
                // Re-habilitar el editor
                if (informeEditorModal) {
                    informeEditorModal.enable();
                }
                
                // Restaurar eventos de los botones
                $("#btn-preview-informe-modal").off('click').on("click", function () {
                    mostrarVistaPreviaModal();
                });

                $("#btn-save-informe-modal").off('click').on("click", function () {
                    guardarInformeModal();
                });
                
                console.log('Funcionalidad del modal restaurada');
            }, 100);
        }

        // Función para limpiar eventos de imágenes de forma segura
        function limpiarEventosImagenes() {
            try {
                // Limpiar eventos de cambio de archivos
                $('input[type="file"]').off('change');
                
                // Limpiar eventos de botones de reset
                $('[id*="reset"]').off('click');
                
                // Limpiar eventos de preview
                $('[id*="preview"]').off('click');
                
                console.log('Eventos de imágenes limpiados');
            } catch (error) {
                console.warn('Error al limpiar eventos de imágenes:', error);
            }
        }

        // Función para cambiar página
        function cambiarPagina(nuevaPagina) {
            console.log(`Cambiando a página ${nuevaPagina}, informes disponibles:`, window.informes?.length || 0);
            
            // Verificar que tenemos informes cargados
            if (!window.informes || window.informes.length === 0) {
                console.error('No hay informes cargados. Recargando...');
                cargarInformes(true); // Preservar página actual
                return;
            }
            
            // Validar página usando la longitud real del array
            const totalPaginasCalculadas = Math.ceil(window.informes.length / window.informesPorPagina);
            if (nuevaPagina < 1 || nuevaPagina > totalPaginasCalculadas) {
                console.warn(`Página ${nuevaPagina} fuera de rango. Total páginas: ${totalPaginasCalculadas}`);
                return;
            }
            
            window.paginaActual = nuevaPagina;
            paginaActual = window.paginaActual; // Sincronizar variable local
            renderizarInformes();
        }

        // Función para compartir por WhatsApp (similar a fichas técnicas)
        function compartirWhatsAppInforme(id) {
            // Almacenar el ID del informe para usar después
            window.informeActualWhatsApp = id;
            $('#compartirWhatsAppInformeModal').modal('show');
        }

        // Función para enviar por WhatsApp
        function enviarWhatsAppInforme() {
            const numero = $('#numeroWhatsAppInforme').val().trim();
            const mensaje = $('#mensajeWhatsAppInforme').val().trim();
            
            if (!numero) {
                Swal.fire({
                    title: 'Campo requerido',
                    text: 'Por favor ingrese un número de WhatsApp',
                    icon: 'warning'
                });
                return;
            }

            Swal.fire({
                title: 'Enviando',
                text: 'Compartiendo informe...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: _URL + '/ajs/informe/compartir-whatsapp',
                method: 'POST',
                data: {
                    id_informe: window.informeActualWhatsApp,
                    numero: numero,
                    mensaje: mensaje
                },
                success: function(response) {
                    if (response.res) { // Cambié de 'success' a 'res'
                        $('#compartirWhatsAppInformeModal').modal('hide');
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

        // Exportar funciones y variables globalmente
        window.renderizarInformes = renderizarInformes;
        window.cambiarPagina = cambiarPagina;
        window.compartirWhatsAppInforme = compartirWhatsAppInforme;
        window.enviarWhatsAppInforme = enviarWhatsAppInforme;
        
        // Exportar variables necesarias para paginación (iniciales)
        window.informes = informes;
        window.paginaActual = paginaActual;
        window.informesPorPagina = informesPorPagina;
        
        // Las variables se sincronizan automáticamente en las funciones que las actualizan

    // Función para mostrar vista previa en el modal
</script>