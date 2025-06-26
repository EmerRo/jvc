<!-- resources/views/fragment-views/cliente/documentos/componentes/cartas.php -->
<style>
    /* Estilos generales */


    /* Contenedor de la vista previa del documento */


    /* Estilos para las imágenes de cabecera y pie */
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

    /* Estilos para el editor */
    .editor-container {
        height: 300px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }

    /* Estilos para las vistas */
    .vista {
        display: none;
    }

    .vista.active {
        display: block;
    }

    /* Estilos para las tarjetas de cartas */
    .carta-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .carta-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Cabecera de formulario */
    .form-header {
        background-color: #dc3545;
        color: white;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    /* Contenedor de la vista previa del documento */
    .document-preview {
        height: 250px;
        overflow: hidden;
        display: block;
        background-color: white;
        padding: 0;
        margin: 0;
    }

    /* Estilo para el canvas de PDF */
    .pdf-preview-canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 100%;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    /* Asegurar que los botones sean clickeables */
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
<!-- Actualizar Quill.js a versión más reciente -->
<link href="https://cdn.quilljs.com/2.0.2/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/2.0.2/quill.min.js"></script>

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

<script>
    // Encapsulamos todo el código en una función anónima autoejecutable (IIFE)
    // para evitar contaminar el espacio de nombres global
    (function () {
        // Verificamos si el módulo ya ha sido inicializado
        if (window.cartasModuleInitialized) {
            console.log("El módulo de cartas ya ha sido inicializado. Evitando reinicialización.");
            return;
        }

        // Marcamos el módulo como inicializado
        window.cartasModuleInitialized = true;

        console.log("Inicializando módulo de cartas...");

        // Verificar compatibilidad del navegador
        if (!window.MutationObserver) {
            console.warn('MutationObserver no está disponible en este navegador');
            Swal.fire({
                icon: 'warning',
                title: 'Navegador no compatible',
                text: 'Su navegador no es compatible con todas las funciones. Por favor, actualice su navegador.'
            });
        }
        // Variables del módulo (no globales)
        var cartas = [];
        var filtroActual = '';
        var tipoFiltroActual = 'todos';
        var cartaEditor = null;
        var templateEditor = null;
        var quillLoaded = false;
        var quillCssLoaded = false;
        var procesandoAccion = false;

        // Inicializar cuando el DOM esté listo
        $(document).ready(function () {
            console.log("DOM cargado, configurando eventos del módulo de cartas...");

            // Configurar eventos para los botones de navegación
            $("#btn-lista-cartas").on("click", function () {
                mostrarVistaListaCartas();
            });
            $("#btn-nueva-carta").on("click", function () {
                if (procesandoAccion) return; // Evitar múltiples clics
                procesandoAccion = true;

                mostrarFormularioNuevoCarta();

                setTimeout(function () {
                    procesandoAccion = false;
                }, 500);
            });

            $("#btn-editar-plantilla").on("click", function () {
                editarPlantillaCarta();
            });

            // Configurar el modal de confirmación para eliminar
            $('#confirmarEliminarCartaModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');

                $('#btn-confirmar-eliminar-carta').off('click').on('click', function () {
                    eliminarCarta(id);
                });
            });

            // Configurar eventos de búsqueda
            $("#buscar-carta").on("keyup", function () {
                buscarCartas();
            });

            // Configurar eventos para el formulario de carta
            $("#btn-cancel-carta").on("click", function () {
                mostrarVistaListaCartas();
            });

            $("#btn-save-carta").on("click", function () {
                guardarCarta();
            });

            $("#btn-preview-carta").on("click", function () {
                mostrarVistaPreviaCarta();
            });


            // Configurar eventos para el formulario de plantilla
            $("#btn-save-plantilla").on("click", function () {
                guardarPlantilla();
            });




            $("#btn-gestionar-membretes").on("click", function () {
                gestionarMembretes();
            });

            // Configurar eventos para el formulario de membretes
            $("#btn-save-membretes").on("click", function () {
                guardarMembretes();
            });

            $("#membrete_header_image").on("change", function (e) {
                manejarCambioImagen(e, 'membrete_header_image_data', 'membrete-header-preview', 'header-placeholder-membrete');
            });

            $("#membrete_footer_image").on("change", function (e) {
                manejarCambioImagen(e, 'membrete_footer_image_data', 'membrete-footer-preview', 'footer-placeholder-membrete');
            });

            $("#reset-membrete-header").on("click", function () {
                restablecerImagen('membrete_header_image_data', 'membrete-header-preview', 'header-placeholder-membrete');
            });

            $("#reset-membrete-footer").on("click", function () {
                restablecerImagen('membrete_footer_image_data', 'membrete-footer-preview', 'footer-placeholder-membrete');
            });
            $("#btn-preview-plantilla").on("click", function () {
                mostrarVistaPreviewPlantilla();
            });
            $("#btn-preview-membretes").on("click", function () {
                mostrarVistaPreviewMembretes();
            });

            // Cargar cartas
            cargarCartas();

            // Cargar Quill si no está cargado
            cargarQuillSiNoExiste();
        });

        // Función para mostrar la vista de lista de cartas
        function mostrarVistaListaCartas() {
            $(".vista").removeClass("active");
            $("#vista-lista-cartas").addClass("active");

            // Actualizar estado de los botones
            $("#btn-lista-cartas").removeClass("btn-outline-danger").addClass("border-rojo");
            $("#btn-nueva-carta").removeClass("bg-rojo text-white").addClass("border-rojo");
            $("#btn-editar-plantilla").addClass("border-rojo").removeClass("bg-rojo text-white");

            // Destruir el editor correctamente
            destruirEditor();

            // Recargar la lista de cartas
            cargarCartas();
        }


        // Función para cargar Quill si no existe
        function cargarQuillSiNoExiste() {
            if (typeof Quill === 'undefined') {
                console.log("Quill no está cargado, cargando la biblioteca...");

                // Cargar CSS de Quill si no está cargado
                if (!quillCssLoaded) {
                    var quillCSS = document.createElement('link');
                    quillCSS.rel = 'stylesheet';
                    quillCSS.href = 'https://cdn.quilljs.com/1.3.7/quill.snow.css';
                    document.head.appendChild(quillCSS);
                    quillCssLoaded = true;
                }

                // Cargar JavaScript de Quill
                var quillScript = document.createElement('script');
                quillScript.src = 'https://cdn.quilljs.com/1.3.7/quill.min.js';
                quillScript.onload = function () {
                    console.log("Quill cargado correctamente");
                    quillLoaded = true;
                };
                quillScript.onerror = function () {
                    console.error("Error al cargar Quill");
                };
                document.head.appendChild(quillScript);
            } else {
                quillLoaded = true;
            }
        }

        // Función para cargar las cartas
        function cargarCartas() {
            console.log("Cargando cartas...");

            // Mostrar indicador de carga
            $("#lista-cartas-container").html(`
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando cartas...</p>
        </div>
    `);

            // Construir la URL con los filtros
            let url = _URL + "/ajs/carta/render";
            if (filtroActual && tipoFiltroActual !== 'todos') {
                url += `?filtro=${encodeURIComponent(filtroActual)}&tipo_busqueda=${tipoFiltroActual}`;
            }

            console.log("URL de carga:", url);

            // Realizar petición AJAX para obtener las cartas
            $.ajax({
                url: url,
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    console.log("Respuesta de cartas:", data);

                    // Asegurarse de que data.cartas sea un array
                    if (!data || !data.cartas) {
                        console.log("No se recibieron datos, mostrando mensaje de no hay cartas");
                        mostrarNoHayCartas();
                        return;
                    }

                    cartas = Array.isArray(data.cartas) ? data.cartas : [];
                    console.log("Cartas procesadas:", cartas);
                    renderizarCartas();
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar cartas:", status, error);
                    $("#lista-cartas-container").html(`
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar las cartas. Por favor, intente nuevamente.
                </div>
                <button class="btn btn-rojo mt-3" onclick="window.recargarCartas()">
                    <i class="fas fa-sync me-2"></i>Reintentar
                </button>
            `);
                }
            });
        }

        // Función para mostrar mensaje de no hay cartas
        function mostrarNoHayCartas() {
            $("#lista-cartas-container").html(`
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                No se encontraron cartas.
            </div>
            <button class="btn btn-rojo mt-3" id="btn-crear-primera-carta">
                <i class="fas fa-plus me-2"></i>Crear primera carta
            </button>
        `);

            // Agregar evento al botón de crear primera carta
            $("#btn-crear-primera-carta").on("click", function () {
                mostrarFormularioNuevoCarta();
            });
        }

        // Función para renderizar las cartas
        function renderizarCartas() {
            if (!cartas || cartas.length === 0) {
                mostrarNoHayCartas();
                return;
            }

            let html = '<div class="row row-cols-1 row-cols-md-3 g-4">';

            cartas.forEach(function (carta) {
                const fecha = new Date(carta.fecha_creacion).toLocaleDateString();
                const cliente = carta.cliente_nombre || 'Sin cliente';

                // Generar un ID único para el canvas de PDF
                const canvasId = `pdf-preview-carta-${carta.id}`;

                html += `
            <div class="col">
                <div class="card carta-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-rojo">${carta.tipo || 'Sin tipo'}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-dark" type="button" id="dropdownCarta${carta.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownCarta${carta.id}">
                                <li><a class="dropdown-item" href="${_URL}/ajs/carta/generarPDF?id=${carta.id}" target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i> Ver PDF
                                </a></li>
                                <li><a class="dropdown-item carta-editar" href="#" data-id="${carta.id}">
                                    <i class="fas fa-edit me-2"></i> Editar
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmarEliminarCartaModal" data-id="${carta.id}">
                                    <i class="fas fa-trash-alt me-2"></i> Eliminar
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Vista previa del PDF -->
                    <div class="card-body p-0">
                        <div class="document-preview">
                            <canvas id="${canvasId}" class="pdf-preview-canvas"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <h5 class="card-title">${carta.titulo}</h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> ${cliente}<br>
                                <i class="fas fa-calendar-alt me-1"></i> ${fecha}
                            </small>
                        </p>
                        <div class="d-flex justify-content-between mt-2">
                            <a href="${_URL}/ajs/carta/generarPDF?id=${carta.id}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Ver PDF
                            </a>
                            <button class="btn btn-sm btn-outline-secondary carta-editar" data-id="${carta.id}">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
            });

            html += '</div>';
            $("#lista-cartas-container").html(html);

            // Agregar eventos a los botones de editar
            $(".carta-editar").on("click", function () {
                const id = $(this).data('id');
                editarCarta(id);
            });

            // Inicializar la carga de PDFs después de que el HTML esté en el DOM
            cartas.forEach(function (carta) {
                const canvasId = `pdf-preview-carta-${carta.id}`;
                setTimeout(() => {
                    renderPdfPreviewCarta(`${_URL}/ajs/carta/generarPDF?id=${carta.id}`, canvasId);
                }, 100);
            });
        }

        // Función para buscar cartas
        function buscarCartas() {
            const busqueda = $("#buscar-carta").val().trim().toLowerCase();

            if (busqueda === "") {
                // Si la búsqueda está vacía, mostrar todas las cartas según el filtro de tipo
                if (tipoFiltroActual === "todos") {
                    filtroActual = "";
                }
            } else {
                // Si hay texto de búsqueda, filtrar por título
                filtroActual = busqueda;
                tipoFiltroActual = "titulo";
            }

            // Recargar las cartas con el filtro
            cargarCartas();
        }

        function destruirEditor() {
            if (cartaEditor) {
                try {
                    // Remover event listeners de forma segura
                    if (cartaEditor.off) {
                        cartaEditor.off();
                    }

                    // Limpiar DOM de manera segura
                    const container = cartaEditor.container;
                    if (container && container.parentNode) {
                        // Remover toolbars
                        const toolbars = container.parentNode.querySelectorAll('.ql-toolbar');
                        toolbars.forEach(toolbar => {
                            if (toolbar && toolbar.parentNode) {
                                try {
                                    toolbar.parentNode.removeChild(toolbar);
                                } catch (e) {
                                    console.warn('Error removiendo toolbar:', e);
                                }
                            }
                        });

                        // Limpiar contenedor
                        try {
                            container.innerHTML = '';
                        } catch (e) {
                            console.warn('Error limpiando contenedor:', e);
                        }
                    }

                    // Limpiar con jQuery como respaldo
                    $('#editor-container-carta').empty();
                    cartaEditor = null;
                } catch (error) {
                    console.error("Error al destruir el editor:", error);
                    // Forzar limpieza
                    $('#editor-container-carta').empty();
                    cartaEditor = null;
                }
            }

            // Ocultar autocomplete si existe
            const autocompleteResults = elementoSeguro('autocomplete-results');
            if (autocompleteResults) {
                autocompleteResults.style.display = 'none';
            }
        }
        function mostrarVistaPreviewMembretes() {
            // Marcar que debemos regresar al modal de membretes
            window.regresarAMembretes = true;

            // CERRAR el modal de membretes PRIMERO
            const modalMembretes = bootstrap.Modal.getInstance(document.getElementById('gestionarMembretesModal'));
            if (modalMembretes) {
                modalMembretes.hide();
            }

            // Esperar a que el modal se cierre completamente
            $('#gestionarMembretesModal').on('hidden.bs.modal', function () {
                $(this).off('hidden.bs.modal');

                // Obtener las imágenes ACTUALES del formulario
                const headerImageData = document.getElementById('membrete_header_image_data').value;
                const footerImageData = document.getElementById('membrete_footer_image_data').value;

                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Generando vista previa',
                    text: 'Por favor espere...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Crear FormData para enviar las imágenes
                const formData = new FormData();
                formData.append('titulo', 'Vista Previa de Membretes');
                formData.append('contenido', 'Contenido de ejemplo para mostrar los membretes configurados.');

                // Solo agregar imágenes si existen
                if (headerImageData && headerImageData.trim() !== '') {
                    formData.append('header_image', headerImageData);
                }
                if (footerImageData && footerImageData.trim() !== '') {
                    formData.append('footer_image', footerImageData);
                }

                $.ajax({
                    url: _URL + "/ajs/carta/vista-previa",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (data) {
                        Swal.close();

                        if (data.success && data.pdfBase64) {
                            document.getElementById('preview-frame-carta').src = "data:application/pdf;base64," + data.pdfBase64;
                            const modal = new bootstrap.Modal(document.getElementById('previewCartaModal'));
                            modal.show();
                        } else {
                            window.regresarAMembretes = false; // Cancelar regreso si hay error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.msg || 'Error al generar la vista previa'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        window.regresarAMembretes = false; // Cancelar regreso si hay error
                        console.error("Error en vista previa:", status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al generar la vista previa'
                        });
                    }
                });
            });
        }
        // Función para mostrar el formulario de nueva carta
        function mostrarFormularioNuevoCarta() {
            console.log("Mostrando formulario de nueva carta...");

            // Actualizar estado de los botones
            $("#btn-lista-cartas").removeClass("border-rojo").addClass("border-rojo");
            $("#btn-nueva-carta").removeClass("border-rojo").addClass("bg-rojo text-white");
            $("#btn-editar-plantilla").addClass("border-rojo").removeClass("bg-rojo text-white");
            // Mostrar la vista de edición
            $(".vista").removeClass("active");
            $("#vista-editar-carta").addClass("active");

            // Limpiar el formulario
            $("#id_carta").val("");
            $("#titulo_carta").val("");
            $("#tipo_carta").val("");
            $("#cliente_id").val("");
            $("#header_image_data").val("");
            $("#footer_image_data").val("");

            // Ocultar las imágenes de vista previa
            $("#header-preview-carta").hide();
            $("#footer-preview-carta").hide();
            $("#header-placeholder-carta").show();
            $("#footer-placeholder-carta").show();

            // Actualizar título
            $("#titulo-pagina-carta").text("Nueva Carta");

            // Inicializar autocomplete para clientes
            inicializarAutocompletarClientes();

            // Esperar a que Quill esté cargado
            esperarPorQuill(function () {
                // Inicializar el editor
                inicializarEditorCarta();

                // Cargar plantilla actual
                cargarPlantillaCarta();
            });
            cargarTiposCartasSelect();
        }

        // Función para esperar a que Quill esté cargado
        function esperarPorQuill(callback) {
            if (typeof Quill !== 'undefined') {
                quillLoaded = true;
                callback();
                return;
            }

            console.log("Esperando a que Quill se cargue...");
            cargarQuillSiNoExiste();

            // Usar una promesa en lugar de setInterval
            const checkQuill = () => {
                return new Promise((resolve, reject) => {
                    if (typeof Quill !== 'undefined') {
                        resolve();
                        return;
                    }

                    const timeout = setTimeout(() => {
                        reject(new Error('Tiempo de espera agotado para cargar Quill'));
                    }, 5000);

                    const interval = setInterval(() => {
                        if (typeof Quill !== 'undefined') {
                            clearInterval(interval);
                            clearTimeout(timeout);
                            resolve();
                        }
                    }, 100);
                });
            };

            checkQuill()
                .then(() => {
                    quillLoaded = true;
                    console.log("Quill ya está disponible, continuando...");
                    callback();
                })
                .catch((error) => {
                    console.error(error.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el editor. Por favor, recargue la página e intente nuevamente.'
                    });
                });
        }

        // Función para editar una carta existente
        function editarCarta(id) {
            console.log("Editando carta ID:", id);

            // Actualizar estado de los botones
            $("#btn-lista-cartas").removeClass("btn-rojo").addClass("btn-outline-secondary");
            $("#btn-nueva-carta").removeClass("btn-rojo").addClass("btn-outline-secondary");
            $("#btn-editar-plantilla").addClass("btn-outline-secondary").removeClass("btn-rojo");

            // Mostrar la vista de edición
            $(".vista").removeClass("active");
            $("#vista-editar-carta").addClass("active");

            // Actualizar título
            $("#titulo-pagina-carta").text("Editar Carta");

            // Inicializar autocomplete para clientes
            inicializarAutocompletarClientes();
            // Esperar a que Quill esté cargado
            esperarPorQuill(function () {
                // Inicializar el editor
                inicializarEditorCarta();

                // Cargar datos de la carta
                cargarDatosCarta(id);
            });
        }

        // Función para editar la plantilla de carta
        function editarPlantillaCarta() {
            console.log("Editando plantilla de carta...");

            // Destruir cualquier instancia existente del editor antes de continuar
            destruirEditorPlantilla();

            // Actualizar estado de los botones
            $("#btn-lista-cartas").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-nueva-carta").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-editar-plantilla").removeClass("btn-outline-danger").addClass("btn-rojo");

            // Mostrar el modal PRIMERO
            const modal = new bootstrap.Modal(document.getElementById('editarPlantillaCartaModal'));
            modal.show();

            // Esperar a que el modal esté completamente visible
            $('#editarPlantillaCartaModal').on('shown.bs.modal', function () {
                // Remover el event listener para evitar múltiples ejecuciones
                $(this).off('shown.bs.modal');

                // Esperar a que Quill esté cargado
                esperarPorQuill(function () {
                    // Inicializar el editor PRIMERO
                    inicializarEditorPlantilla();

                    // Luego cargar los datos con un pequeño delay
                    setTimeout(() => {
                        cargarDatosPlantilla();
                    }, 200);
                });
            });
        }
        // Función para inicializar el editor Quill
        function inicializarEditorCarta() {
            console.log("Inicializando editor Quill...");

            // Verificar que el contenedor exista
            if ($("#editor-container-carta").length === 0) {
                console.error("Error: No se encontró el contenedor del editor #editor-container-carta");
                return;
            }

            // Destruir el editor existente si hay uno
            destruirEditor();

            try {
                // Asegurarse de que el contenedor esté vacío
                $("#editor-container-carta").html('');

                // Inicializar Quill
                cartaEditor = new Quill('#editor-container-carta', {
                    modules: {
                        toolbar: [
                            [{ 'font': [] }, { 'size': [] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'script': 'sub' }, { 'script': 'super' }],
                            [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                            [{ 'direction': 'rtl' }, { 'align': [] }],
                            ['link', 'image', 'video'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Escriba el contenido de la carta...',
                    theme: 'snow'
                });

                console.log("Editor Quill inicializado correctamente");

                // Asignar el evento de cambio de texto solo si el editor se inicializó correctamente
                if (cartaEditor && cartaEditor.on) {
                    cartaEditor.on('text-change', function () {
                        var contenidoInput = document.getElementById('contenido_carta');
                        if (contenidoInput) {
                            contenidoInput.value = cartaEditor.root.innerHTML;
                        }
                    });
                } else {
                    console.error("Error: El editor Quill no se inicializó correctamente");
                }
            } catch (error) {
                console.error("Error al inicializar Quill:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al inicializar el editor: ' + error.message
                });
            }
        }
        function destruirEditorPlantilla() {
            if (templateEditor) {
                try {
                    // Remover todos los event listeners de manera segura
                    if (templateEditor.off) {
                        templateEditor.off();
                    }

                    // Limpiar el DOM de manera más segura
                    const container = templateEditor.container;
                    if (container && container.parentNode) {
                        // Remover todas las toolbars relacionadas de manera segura
                        const toolbars = container.parentNode.querySelectorAll('.ql-toolbar');
                        toolbars.forEach(toolbar => {
                            if (toolbar && toolbar.parentNode) {
                                toolbar.parentNode.removeChild(toolbar);
                            }
                        });

                        // Limpiar el contenedor
                        if (container) {
                            while (container.firstChild) {
                                container.removeChild(container.firstChild);
                            }
                        }
                    }

                    // Limpiar el contenedor del editor con jQuery
                    $('#editor-container-plantilla').empty();

                    // Establecer la variable a null
                    templateEditor = null;
                } catch (error) {
                    console.error("Error al destruir el editor de plantilla:", error);
                    // Forzar limpieza en caso de error
                    $('#editor-container-plantilla').empty();
                    templateEditor = null;
                }
            }

            // Asegúrate de que el dropdown de autocompletado esté oculto
            if ($("#autocomplete-results").length) {
                $("#autocomplete-results").hide();
            }
        }
        function inicializarEditorPlantilla() {
            console.log("Inicializando editor de plantilla Quill...");

            // Verificar que el contenedor exista
            if ($("#editor-container-plantilla").length === 0) {
                console.error("Error: No se encontró el contenedor del editor #editor-container-plantilla");
                return;
            }

            // Destruir el editor existente si hay uno
            destruirEditorPlantilla();

            try {
                // Asegurarse de que el contenedor esté vacío de manera segura
                $("#editor-container-plantilla").empty();

                // Inicializar Quill
                templateEditor = new Quill('#editor-container-plantilla', {
                    modules: {
                        toolbar: [
                            [{ 'font': [] }, { 'size': [] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'script': 'sub' }, { 'script': 'super' }],
                            [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                            [{ 'direction': 'rtl' }, { 'align': [] }],
                            ['link', 'image', 'video'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Escriba el contenido de la plantilla...',
                    theme: 'snow'
                });

                console.log("Editor de plantilla Quill inicializado correctamente");

                // Asignar el evento de cambio de texto usando el API moderno de Quill
                if (templateEditor && templateEditor.on) {
                    templateEditor.on('text-change', function () {
                        var contenidoInput = elementoSeguro('contenido_plantilla');
                        if (contenidoInput) {
                            contenidoInput.value = templateEditor.root.innerHTML;
                        }
                    });

                    // Evento cuando el editor está listo
                    templateEditor.on('editor-change', function () {
                        console.log("Editor de plantilla listo para recibir contenido");
                    });
                } else {
                    console.error("Error: El editor de plantilla Quill no se inicializó correctamente");
                }
            } catch (error) {
                console.error("Error al inicializar Quill para plantilla:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al inicializar el editor de plantilla: ' + error.message
                });
            }
        }

        // Reemplazar los eventos existentes del modal por estos:
        $('#editarPlantillaCartaModal').on('hidden.bs.modal', function () {
            console.log("Modal de plantilla cerrado, destruyendo editor");
            destruirEditorPlantilla();
            // Limpiar completamente el contenedor
            $('#editor-container-plantilla').empty();
        });

        $('#editarPlantillaCartaModal').on('show.bs.modal', function () {
            console.log("Modal de plantilla abriéndose");
            // Asegurarse de que no haya instancias previas
            destruirEditorPlantilla();
        });
        // Evento para regresar al modal de membretes después de cerrar vista previa
        $('#previewCartaModal').on('hidden.bs.modal', function () {
            // Verificar si venimos del modal de membretes
            if (window.regresarAMembretes) {
                window.regresarAMembretes = false;

                // Reabrir el modal de membretes
                setTimeout(() => {
                    const modal = new bootstrap.Modal(document.getElementById('gestionarMembretesModal'));
                    modal.show();
                }, 300);
            }
        });
        // Función para inicializar el autocomplete de clientes
        function inicializarAutocompletarClientes() {
            let timeoutId;
            let currentRequest;

            // Limpiar cualquier autocomplete previo
            $("#cliente_search").off('input keyup');
            $("#cliente_search").removeData('autocomplete-initialized');

            // Crear contenedor para resultados si no existe
            if (!$("#autocomplete-results").length) {
                $("body").append('<div id="autocomplete-results" class="autocomplete-dropdown" style="display: none; position: absolute; z-index: 9999; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;"></div>');
            }

            const $input = $("#cliente_search");
            const $results = $("#autocomplete-results");

            // Función para buscar clientes
            function buscarClientes(query) {
                // Cancelar petición anterior si existe
                if (currentRequest) {
                    currentRequest.abort();
                }

                if (query.length < 2) {
                    $results.hide();
                    return;
                }

                currentRequest = $.ajax({
                    url: _URL + "/ajs/buscar/cliente/datos",
                    method: "GET",
                    data: { term: query },
                    dataType: 'json',
                    success: function (data) {
                        mostrarResultados(data);
                    },
                    error: function (xhr) {
                        if (xhr.statusText !== 'abort') {
                            console.error("Error en búsqueda de clientes:", xhr);
                        }
                    },
                    complete: function () {
                        currentRequest = null;
                    }
                });
            }

            // Función para mostrar resultados
            function mostrarResultados(items) {
                $results.empty();

                if (!items || items.length === 0) {
                    $results.hide();
                    return;
                }

                items.forEach(function (item) {
                    const $item = $('<div class="autocomplete-item" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">')
                        .html('<strong>' + item.documento + '</strong> | ' + item.datos)
                        .on('click', function () {
                            seleccionarCliente(item);
                        })
                        .on('mouseenter', function () {
                            $(this).css('background-color', '#f5f5f5');
                        })
                        .on('mouseleave', function () {
                            $(this).css('background-color', 'white');
                        });

                    $results.append($item);
                });

                // Posicionar el dropdown
                const inputOffset = $input.offset();
                $results.css({
                    top: inputOffset.top + $input.outerHeight(),
                    left: inputOffset.left,
                    width: $input.outerWidth(),
                    display: 'block'
                });
            }

            // Función para seleccionar cliente
            function seleccionarCliente(item) {
                $("#cliente_id").val(item.codigo);
                $("#cliente_nombre").text(item.datos);
                $("#cliente_documento").text("Documento: " + item.documento);
                $("#cliente_direccion").text("Dirección: " + (item.direccion || "No especificada"));
                $("#cliente_info").show();
                $input.val(item.datos);
                $results.hide();
            }

            // Event listeners
            $input.on('input', function () {
                const query = $(this).val().trim();

                clearTimeout(timeoutId);
                timeoutId = setTimeout(function () {
                    buscarClientes(query);
                }, 300);
            });

            $input.on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $results.hide();
                }
            });

            $input.on('blur', function () {
                // Delay para permitir clicks en resultados
                setTimeout(function () {
                    $results.hide();
                }, 200);
            });

            // Botón de búsqueda
            $("#btn-search-cliente").off('click').on("click", function () {
                const query = $input.val().trim();
                if (query === "") {
                    $("#cliente_id").val("");
                    $("#cliente_info").hide();
                    $results.hide();
                } else {
                    buscarClientes(query);
                }
            });

            // Marcar como inicializado
            $input.data('autocomplete-initialized', true);
        }
        function elementoSeguro(id) {
            const elemento = document.getElementById(id);
            if (!elemento) {
                console.warn(`Elemento con ID '${id}' no encontrado en el DOM`);
                return null;
            }
            return elemento;
        }
        function cargarPlantillaCarta() {
            $.ajax({
                url: _URL + "/ajs/carta/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        plantillaActual = data.data;

                        // Establecer contenido predeterminado basado en la plantilla
                        if (cartaEditor) {
                            cartaEditor.root.innerHTML = plantillaActual.contenido;
                            const contenidoInput = elementoSeguro('contenido_carta');
                            if (contenidoInput) {
                                contenidoInput.value = plantillaActual.contenido;
                            }
                        }

                        // Usar la función segura para todos los elementos
                        const headerPreview = elementoSeguro('header-preview-carta');
                        const footerPreview = elementoSeguro('footer-preview-carta');
                        const headerPlaceholder = elementoSeguro('header-placeholder-carta');
                        const footerPlaceholder = elementoSeguro('footer-placeholder-carta');

                        // Mostrar imágenes solo si los elementos existen
                        if (plantillaActual.header_image_url && headerPreview && headerPlaceholder) {
                            headerPreview.src = plantillaActual.header_image_url;
                            headerPreview.style.display = 'block';
                            headerPlaceholder.style.display = 'none';
                        }

                        if (plantillaActual.footer_image_url && footerPreview && footerPlaceholder) {
                            footerPreview.src = plantillaActual.footer_image_url;
                            footerPreview.style.display = 'block';
                            footerPlaceholder.style.display = 'none';
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar la plantilla:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar la plantilla de carta'
                    });
                }
            });
        }
        function cargarDatosPlantilla() {
            $.ajax({
                url: _URL + "/ajs/carta/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        plantillaActual = data.data;

                        // Llenar formulario usando elementos seguros
                        const idPlantilla = elementoSeguro('id_plantilla_carta');
                        const tituloPlantilla = elementoSeguro('titulo_plantilla');
                        const headerImageData = elementoSeguro('plantilla_header_image_data');
                        const footerImageData = elementoSeguro('plantilla_footer_image_data');

                        if (idPlantilla) idPlantilla.value = plantillaActual.id;
                        if (tituloPlantilla) tituloPlantilla.value = plantillaActual.titulo;
                        if (headerImageData) headerImageData.value = plantillaActual.header_image || '';
                        if (footerImageData) footerImageData.value = plantillaActual.footer_image || '';

                        // Verificar elementos de vista previa
                        const headerPreview = elementoSeguro('plantilla-header-preview');
                        const footerPreview = elementoSeguro('plantilla-footer-preview');
                        const headerPlaceholder = elementoSeguro('header-placeholder-plantilla');
                        const footerPlaceholder = elementoSeguro('footer-placeholder-plantilla');

                        // Mostrar imágenes si existen los elementos
                        if (plantillaActual.header_image_url && headerPreview && headerPlaceholder) {
                            headerPreview.src = plantillaActual.header_image_url;
                            headerPreview.style.display = 'block';
                            headerPlaceholder.style.display = 'none';
                        }

                        if (plantillaActual.footer_image_url && footerPreview && footerPlaceholder) {
                            footerPreview.src = plantillaActual.footer_image_url;
                            footerPreview.style.display = 'block';
                            footerPlaceholder.style.display = 'none';
                        }

                        // CRÍTICO: Establecer contenido en el editor con verificación
                        if (templateEditor && templateEditor.root) {
                            // Verificar que el editor esté completamente inicializado
                            const checkEditorReady = () => {
                                if (templateEditor.root && templateEditor.root.innerHTML !== undefined) {
                                    templateEditor.root.innerHTML = plantillaActual.contenido;
                                    const contenidoInput = elementoSeguro('contenido_plantilla');
                                    if (contenidoInput) {
                                        contenidoInput.value = plantillaActual.contenido;
                                    }
                                    console.log("Contenido establecido en el editor:", plantillaActual.contenido.substring(0, 100) + "...");
                                } else {
                                    // Si el editor no está listo, esperar un poco más
                                    setTimeout(checkEditorReady, 100);
                                }
                            };

                            checkEditorReady();
                        } else {
                            console.warn("Editor de plantilla no está inicializado al cargar datos");
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Error al cargar la plantilla'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar la plantilla'
                    });
                }
            });
        }
        function manejarCambioImagen(event, inputId, previewId, placeholderId) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    // Actualizar el campo oculto
                    const hiddenInput = document.getElementById(inputId);
                    if (hiddenInput) {
                        hiddenInput.value = e.target.result;
                    }

                    // Actualizar la vista previa
                    const previewImg = document.getElementById(previewId);
                    const placeholder = document.getElementById(placeholderId);

                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    }

                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }

                    console.log('Imagen cargada correctamente:', inputId);
                };

                reader.readAsDataURL(file);
            }
        }

        // Función para restablecer imagen
        function restablecerImagen(inputId, previewId, placeholderId) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).style.display = 'none';

            if (placeholderId) {
                document.getElementById(placeholderId).style.display = 'block';
            }
        }

        // Función para cargar datos de una carta
        function cargarDatosCarta(id) {
            const formData = new FormData();
            formData.append('id', id);

            $.ajax({
                url: _URL + "/ajs/carta/getOne",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        cartaActual = data.data;

                        // Llenar formulario
                        document.getElementById('id_carta').value = cartaActual.id;
                        document.getElementById('cliente_id').value = cartaActual.id_cliente || '';
                        // Si hay un cliente seleccionado, mostrar su información
                        if (cartaActual.id_cliente && cartaActual.cliente_nombre) {
                            $("#cliente_search").val(cartaActual.cliente_nombre);
                            $("#cliente_nombre").text(cartaActual.cliente_nombre);
                            $("#cliente_documento").text("Documento: " + (cartaActual.cliente_documento || ""));
                            $("#cliente_direccion").text("Dirección: " + (cartaActual.cliente_direccion || "No especificada"));
                            $("#cliente_info").show();
                        }
                        cargarTiposCartasSelect(cartaActual.tipo || '');
                        document.getElementById('titulo_carta').value = cartaActual.titulo;
                        document.getElementById('header_image_data').value = cartaActual.header_image || '';
                        document.getElementById('footer_image_data').value = cartaActual.footer_image || '';

                        // Mostrar imágenes si existen
                        if (cartaActual.header_image_url) {
                            document.getElementById('header-preview-carta').src = cartaActual.header_image_url;
                            document.getElementById('header-preview-carta').style.display = 'block';
                            document.getElementById('header-placeholder-carta').style.display = 'none';
                        }

                        if (cartaActual.footer_image_url) {
                            document.getElementById('footer-preview-carta').src = cartaActual.footer_image_url;
                            document.getElementById('footer-preview-carta').style.display = 'block';
                            document.getElementById('footer-placeholder-carta').style.display = 'none';
                        }

                        // Establecer contenido en el editor
                        if (cartaEditor) {
                            cartaEditor.root.innerHTML = cartaActual.contenido;
                            document.getElementById('contenido_carta').value = cartaActual.contenido;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Error al cargar la carta'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar la carta'
                    });
                }
            });
        }

        // Función para guardar carta
        function guardarCarta() {
            // Validar formulario
            const titulo = document.getElementById('titulo_carta').value.trim();
            const contenido = document.getElementById('contenido_carta').value.trim();
            const tipo = document.getElementById('tipo_carta').value.trim();

            if (!titulo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El título es obligatorio'
                });
                return;
            }

            if (!contenido) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El contenido es obligatorio'
                });
                return;
            }

            // Recopilar datos del formulario
            const formData = new FormData(document.getElementById('formCarta'));

            // Asegurarse de que el contenido esté incluido
            formData.set('contenido', contenido);

            // Determinar si es inserción o edición
            const cartaId = document.getElementById('id_carta').value;
            let url = _URL + '/ajs/carta/insertar';

            if (cartaId) {
                url = _URL + '/ajs/carta/editar';
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando carta...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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
                            icon: 'success',
                            title: 'Éxito',
                            text: data.msg
                        }).then(() => {
                            // Volver a la lista de cartas
                            mostrarVistaListaCartas();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al guardar la carta'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar la carta'
                    });
                }
            });
        }

        // Función para guardar plantilla
        function guardarPlantilla() {
            // Validar formulario
            const titulo = document.getElementById('titulo_plantilla').value.trim();
            const contenido = document.getElementById('contenido_plantilla').value.trim();

            if (!titulo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El título es obligatorio'
                });
                return;
            }

            if (!contenido) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El contenido es obligatorio'
                });
                return;
            }

            // Recopilar datos del formulario
            const formData = new FormData(document.getElementById('formPlantillaCarta'));

            // Asegurarse de que el contenido esté incluido
            formData.set('contenido', contenido);

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando plantilla...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Enviar datos al servidor
            $.ajax({
                url: _URL + "/ajs/carta/guardar-template",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.mensaje || 'Plantilla guardada correctamente'
                        }).then(() => {
                            // Cerrar modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editarPlantillaCartaModal'));
                            modal.hide();

                            // Actualizar estado de los botones
                            $("#btn-lista-cartas").addClass("btn-rojo").removeClass("btn-outline-secondary");
                            $("#btn-nueva-carta").removeClass("btn-rojo").addClass("btn-outline-secondary");
                            $("#btn-editar-plantilla").removeClass("btn-rojo").addClass("btn-outline-secondary");

                            // Recargar cartas
                            mostrarVistaListaCartas();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al guardar la plantilla'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar la plantilla'
                    });
                }
            });
        }

        // Función para mostrar la vista previa de una carta
        function mostrarVistaPreviaCarta() {
            // Validar que haya contenido
            const contenido = document.getElementById('contenido_carta').value.trim();

            if (!contenido) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe ingresar contenido para la vista previa'
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

            // Recopilar datos para la vista previa
            const formData = new FormData();
            formData.append('titulo', document.getElementById('titulo_carta').value.trim());
            formData.append('contenido', contenido);
            formData.append('header_image', document.getElementById('header_image_data').value);
            formData.append('footer_image', document.getElementById('footer_image_data').value);

            // Enviar solicitud para generar vista previa
            $.ajax({
                url: _URL + "/ajs/carta/vista-previa",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        // Mostrar la vista previa en el iframe
                        document.getElementById('preview-frame-carta').src = "data:application/pdf;base64," + data.pdfBase64;

                        // Mostrar el modal
                        const modal = new bootstrap.Modal(document.getElementById('previewCartaModal'));
                        modal.show();

                        // Configurar el botón de descarga
                        document.getElementById('btn-download-pdf').onclick = function () {
                            const blob = b64toBlob(data.pdfBase64, 'application/pdf');
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'carta_' + new Date().getTime() + '.pdf';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        };
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al generar la vista previa'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al generar la vista previa'
                    });
                }
            });
        }
        function mostrarVistaPreviewPlantilla() {
            // CERRAR el modal de edición de plantilla PRIMERO
            const modalPlantilla = bootstrap.Modal.getInstance(document.getElementById('editarPlantillaCartaModal'));
            if (modalPlantilla) {
                modalPlantilla.hide();
            }

            // Esperar a que el modal se cierre completamente
            $('#editarPlantillaCartaModal').on('hidden.bs.modal', function () {
                $(this).off('hidden.bs.modal');

                // Obtener contenido ACTUAL del editor
                let contenidoActual = '';
                let tituloActual = 'Vista Previa de Plantilla';

                if (templateEditor && templateEditor.root) {
                    contenidoActual = templateEditor.root.innerHTML;
                }

                const tituloInput = elementoSeguro('titulo_plantilla');
                if (tituloInput && tituloInput.value.trim()) {
                    tituloActual = tituloInput.value.trim();
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

                $.ajax({
                    url: _URL + "/ajs/carta/vista-previa",
                    method: "POST",
                    data: {
                        titulo: tituloActual,
                        contenido: contenidoActual
                    },
                    dataType: 'json',
                    success: function (data) {
                        Swal.close();

                        if (data.success && data.pdfBase64) {
                            document.getElementById('preview-frame-carta').src = "data:application/pdf;base64," + data.pdfBase64;
                            const modal = new bootstrap.Modal(document.getElementById('previewCartaModal'));
                            modal.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.msg || 'Error al generar la vista previa'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        console.error("Error en vista previa:", status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al generar la vista previa'
                        });
                    }
                });
            });
        }

        // Función para convertir base64 a Blob
        function b64toBlob(b64Data, contentType = '', sliceSize = 512) {
            const byteCharacters = atob(b64Data);
            const byteArrays = [];

            for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                const slice = byteCharacters.slice(offset, offset + sliceSize);
                const byteNumbers = new Array(slice.length);

                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);
                byteArrays.push(byteArray);
            }

            const blob = new Blob(byteArrays, { type: contentType });
            return blob;
        }

        // Función para eliminar una carta
        function eliminarCarta(id) {
            // Mostrar indicador de carga
            $("#btn-confirmar-eliminar-carta").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...').prop('disabled', true);

            const formData = new FormData();
            formData.append('id', id);

            $.ajax({
                url: _URL + "/ajs/carta/borrar",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    // Cerrar el modal
                    $('#confirmarEliminarCartaModal').modal('hide');

                    if (data.res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.msg
                        }).then(() => {
                            cargarCartas();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al eliminar la carta'
                        });
                    }

                    // Restaurar el botón
                    $("#btn-confirmar-eliminar-carta").html('Eliminar').prop('disabled', false);
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);

                    // Cerrar el modal
                    $('#confirmarEliminarCartaModal').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar la carta'
                    });

                    // Restaurar el botón
                    $("#btn-confirmar-eliminar-carta").html('Eliminar').prop('disabled', false);
                }
            });
        }
        // Función para gestionar membretes
        function gestionarMembretes() {
            console.log("Gestionando membretes...");

            // Actualizar estado de los botones
            $("#btn-lista-cartas").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-nueva-carta").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-editar-plantilla").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-gestionar-membretes").removeClass("btn-outline-warning").addClass("bg-rojo text-white");

            // Cargar datos actuales de membretes
            cargarDatosMembretes();

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('gestionarMembretesModal'));
            modal.show();
        }

        // Función para cargar datos de membretes
        function cargarDatosMembretes() {
            $.ajax({
                url: _URL + "/ajs/carta/obtener-membretes",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        const membretes = data.data;

                        // Llenar campos ocultos
                        document.getElementById('membrete_header_image_data').value = membretes.header_image || '';
                        document.getElementById('membrete_footer_image_data').value = membretes.footer_image || '';

                        // Mostrar imágenes si existen
                        if (membretes.header_image_url) {
                            document.getElementById('membrete-header-preview').src = membretes.header_image_url;
                            document.getElementById('membrete-header-preview').style.display = 'block';
                            document.getElementById('header-placeholder-membrete').style.display = 'none';
                        } else {
                            document.getElementById('membrete-header-preview').style.display = 'none';
                            document.getElementById('header-placeholder-membrete').style.display = 'block';
                        }

                        if (membretes.footer_image_url) {
                            document.getElementById('membrete-footer-preview').src = membretes.footer_image_url;
                            document.getElementById('membrete-footer-preview').style.display = 'block';
                            document.getElementById('footer-placeholder-membrete').style.display = 'none';
                        } else {
                            document.getElementById('membrete-footer-preview').style.display = 'none';
                            document.getElementById('footer-placeholder-membrete').style.display = 'block';
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar membretes:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los membretes'
                    });
                }
            });
        }

        function guardarMembretes() {
            // Recopilar datos del formulario
            const formData = new FormData(document.getElementById('formMembretes'));

            // Agregar archivos de imagen si existen
            const headerFile = document.getElementById('membrete_header_image').files[0];
            const footerFile = document.getElementById('membrete_footer_image').files[0];

            if (headerFile) {
                formData.append('header_image_file', headerFile);
                console.log('Archivo de cabecera agregado:', headerFile.name);
            }

            if (footerFile) {
                formData.append('footer_image_file', footerFile);
                console.log('Archivo de pie agregado:', footerFile.name);
            }

            // Agregar datos base64 si existen
            const headerData = document.getElementById('membrete_header_image_data').value;
            const footerData = document.getElementById('membrete_footer_image_data').value;

            if (headerData && headerData.trim() !== '') {
                formData.append('header_image', headerData);
                console.log('Datos base64 de cabecera agregados');
            }

            if (footerData && footerData.trim() !== '') {
                formData.append('footer_image', footerData);
                console.log('Datos base64 de pie agregados');
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando membretes...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Enviar datos al servidor
            $.ajax({
                url: _URL + "/ajs/carta/guardar-membretes",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    console.log('Respuesta del servidor:', data);

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.mensaje || 'Membretes guardados correctamente'
                        }).then(() => {
                            // Cerrar modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('gestionarMembretesModal'));
                            modal.hide();

                            // Restaurar estado de botones
                            $("#btn-lista-cartas").addClass("btn-rojo").removeClass("btn-outline-danger");
                            $("#btn-nueva-carta").removeClass("btn-rojo").addClass("btn-outline-danger");
                            $("#btn-editar-plantilla").removeClass("btn-rojo").addClass("btn-outline-danger");
                            $("#btn-gestionar-membretes").removeClass("bg-rojo text-white").addClass("btn-outline-warning");

                            // Volver a la lista
                            mostrarVistaListaCartas();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al guardar los membretes'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    console.error("Respuesta del servidor:", xhr.responseText);

                    // Intentar parsear la respuesta para más detalles
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.error("Error detallado:", response);
                    } catch (e) {
                        console.error("No se pudo parsear la respuesta de error");
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión al guardar los membretes'
                    });
                }
            });
        }
        // Función para abrir el modal de tipos
        function abrirModalTiposCartas() {
            cargarTiposCartasModal();
            $('#gestionarTiposCartaModal').modal('show');
        }

        // Función para cargar tipos de carta en el select
        function cargarTiposCartasSelect(tipoSeleccionado = '') {
            $.ajax({
                url: _URL + "/ajs/carta/obtener-tipos-cartas",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.tipos) {
                        let options = '<option value="">Seleccione un tipo</option>';
                        data.tipos.forEach(function (tipo) {
                            const selected = tipo.nombre === tipoSeleccionado ? 'selected' : '';
                            options += `<option value="${tipo.nombre}" ${selected}>${tipo.nombre}</option>`;
                        });
                        $("#tipo_carta").html(options);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar tipos:", error);
                }
            });
        }

        // Función para cargar tipos en el modal
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

        // Función para agregar nuevo tipo
        function agregarTipoCarta() {
            const nombre = $("#nuevo-tipo-carta-nombre").val().trim();

            if (!nombre) {
                Swal.fire('Error', 'El nombre es obligatorio', 'error');
                return;
            }

            $.ajax({
                url: _URL + "/ajs/carta/insertar-tipo-carta",
                method: "POST",
                data: {
                    nombre: nombre
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Éxito', data.msg, 'success');
                        $("#nuevo-tipo-carta-nombre").val('');
                        cargarTiposCartasModal();
                        cargarTiposCartasSelect(); // Actualizar el select también
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
        function editarTipoCarta(id, nombre) {
            $("#editar-tipo-carta-id").val(id);
            $("#editar-tipo-carta-nombre").val(nombre);
            $("#editarTipoCartaModal").modal('show');
        }

        // Función para guardar tipo editado
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
                data: {
                    id: id,
                    nombre: nombre
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Éxito', data.msg, 'success');
                        $("#editarTipoCartaModal").modal('hide');
                        cargarTiposCartasModal();
                        cargarTiposCartasSelect(); // Actualizar el select también
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
                                cargarTiposCartasSelect(); // Actualizar el select también
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
        // Función para renderizar la vista previa del PDF de cartas
        function renderPdfPreviewCarta(pdfUrl, canvasId) {
            console.log('Renderizando PDF de carta:', pdfUrl, 'en canvas:', canvasId);

            // Verificar que pdfjsLib esté disponible
            if (typeof pdfjsLib === 'undefined') {
                console.error('Error: PDF.js no está cargado');
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    canvas.parentNode.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                    <p class="mt-2">Error: PDF.js no disponible</p>
                </div>
            `;
                }
                return;
            }

            // Cargar el documento PDF
            pdfjsLib.getDocument(pdfUrl).promise.then(function (pdf) {
                // Obtener la primera página
                pdf.getPage(1).then(function (page) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) {
                        console.error('Canvas no encontrado:', canvasId);
                        return;
                    }

                    const context = canvas.getContext('2d');

                    // Obtener el tamaño del contenedor padre
                    const container = canvas.parentElement;
                    const containerWidth = container.clientWidth;
                    const containerHeight = container.clientHeight;

                    // Establecer el tamaño del canvas al tamaño del contenedor
                    canvas.width = containerWidth * 2;
                    canvas.height = containerHeight * 2;

                    // Obtener el viewport original del PDF
                    const viewport = page.getViewport({ scale: 1.0 });

                    // Calcular la escala para que el PDF llene el ancho del canvas
                    const scale = (canvas.width / viewport.width) * 1.0;

                    // Crear un nuevo viewport con la escala calculada
                    const scaledViewport = page.getViewport({ scale: scale });

                    // Calcular el desplazamiento horizontal para centrar el contenido
                    const offsetX = (canvas.width - scaledViewport.width) / 2;
                    const offsetY = 0; // Esto hace que se muestre desde arriba

                    // Renderizar la página en el canvas con alta calidad
                    const renderContext = {
                        canvasContext: context,
                        viewport: scaledViewport,
                        transform: [1, 0, 0, 1, offsetX, offsetY],
                        intent: 'display'
                    };

                    // Limpiar el canvas antes de renderizar
                    context.fillStyle = 'white';
                    context.fillRect(0, 0, canvas.width, canvas.height);

                    // Renderizar la página
                    page.render(renderContext).promise.then(function () {
                        console.log('PDF de carta renderizado correctamente en', canvasId);
                    }).catch(function (error) {
                        console.error('Error al renderizar el PDF de carta:', error);
                    });
                }).catch(function (error) {
                    console.error('Error al obtener la página del PDF de carta:', error);
                    // Mostrar un icono de PDF en caso de error
                    const canvas = document.getElementById(canvasId);
                    if (canvas) {
                        canvas.parentNode.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-file-pdf fa-4x text-danger"></i>
                    <p class="mt-2">Ver PDF</p>
                </div>
            `;
                    }
                });
            }).catch(function (error) {
                console.error('Error al cargar el PDF de carta:', error);
                // Mostrar un icono de PDF en caso de error
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    canvas.parentNode.innerHTML = `
            <div class="text-center p-4">
                <i class="fas fa-file-pdf fa-4x text-danger"></i>
                <p class="mt-2">Ver PDF</p>
            </div>
        `;
                }
            });
        }
        // Agregar a las funciones globales
        window.gestionarMembretes = gestionarMembretes;

        // Exponer algunas funciones al ámbito global para poder llamarlas desde HTML
        window.recargarCartas = cargarCartas;
        window.editarCarta = editarCarta;
        window.eliminarCarta = eliminarCarta;
        window.editarPlantillaCarta = editarPlantillaCarta;
        window.mostrarFormularioNuevoCarta = mostrarFormularioNuevoCarta;
        window.mostrarVistaListaCartas = mostrarVistaListaCartas;
        window.mostrarVistaPreviewPlantilla = mostrarVistaPreviewPlantilla;
        window.mostrarVistaPreviewMembretes = mostrarVistaPreviewMembretes;
        window.abrirModalTiposCartas = abrirModalTiposCartas;
        window.agregarTipoCarta = agregarTipoCarta;
        window.editarTipoCarta = editarTipoCarta;
        window.guardarTipoCartaEditado = guardarTipoCartaEditado;
        window.eliminarTipoCarta = eliminarTipoCarta;
    })();
</script>