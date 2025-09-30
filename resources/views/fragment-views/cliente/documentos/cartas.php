<!-- resources/views/fragment-views/cliente/documentos/componentes/cartas.php -->
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

    .carta-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .carta-card:hover {
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

<!-- Vista de lista de cartas -->
<div id="vista-lista-cartas" class="vista active">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Cartas</h3>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control border-rojo" id="buscar-carta" placeholder="Buscar cartas...">
            <button class="btn bg-rojo text-white" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div id="lista-cartas-container">
        <!-- Aquí se cargarán dinámicamente las cartas -->
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando cartas...</p>
        </div>
    </div>
</div>

<!-- Vista de formulario de nueva/editar carta -->
<div id="vista-editar-carta" class="vista">
    <div class="form-header">
        <h3 id="titulo-pagina-carta" class="m-0">Nueva Carta</h3>
        <p class="m-0">Complete la información de la carta</p>
    </div>

    <form id="formCarta" enctype="multipart/form-data">
        <input type="hidden" id="id_carta" name="id">
        <input type="hidden" id="contenido_carta" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="titulo_carta" class="form-label">Título de la Carta</label>
                    <input type="text" class="form-control" id="titulo_carta" name="titulo" required>
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

<!-- Cargar utilidades compartidas -->
<script src="<?= URL::to('public/js/modulo-documentos/utils.js') ?>?v=<?= time() ?>"></script>

<script>
    // Función para inicializar el módulo de cartas - SIMPLIFICADA
    function inicializarModuloCartas() {
        console.log('Inicializando módulo de cartas...');
        
        // Limpiar instancia anterior si existe
        if (window.cartaModuleInstance) {
            console.log('Limpiando instancia anterior de cartas...');
            if (typeof window.cartaModuleInstance.cleanup === 'function') {
                window.cartaModuleInstance.cleanup();
            }
            window.cartaModuleInstance = null;
        }

        // Verificar que DocumentosUtils esté disponible
        if (typeof window.DocumentosUtils !== 'function') {
            console.error('DocumentosUtils no está disponible');
            setTimeout(inicializarModuloCartas, 500); // Reintentar después de 500ms
            return;
        }

        try {
            // Configuración específica para cartas - DIRECTA
            const cartasConfig = {
                tipo: 'carta',
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
            
        } catch (error) {
            console.error('Error al inicializar módulo de cartas:', error);
        }
    }

    // Exponer la función de inicialización globalmente
    window.inicializarModuloCartas = inicializarModuloCartas;

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        // Solo inicializar si estamos en la página correcta
        if (window.location.pathname.includes('cartas') || $('#vista-lista-cartas').length > 0) {
            // Esperar un poco para asegurar que utils.js esté cargado
            setTimeout(inicializarModuloCartas, 100);
        }
    });

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
                              placeholder="Mensaje adicional que desee agregar..."></textarea>
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