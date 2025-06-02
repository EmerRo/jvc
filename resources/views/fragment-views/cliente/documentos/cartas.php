<!-- resources/views/fragment-views/cliente/documentos/componentes/cartas.php -->
<style>
    /* Estilos generales */
    .btn-rojo {
        background-color: #dc3545;
        color: white;
    }

    .btn-rojo:hover {
        background-color: #c82333;
        color: white;
    }

    .border-rojo {
        border-color: #dc3545;
    }

    .bg-rojo {
        background-color: #dc3545;
    }

    .text-rojo {
        color: #dc3545;
    }

    /* Contenedor de la vista previa del documento */
    .document-preview {
        height: 200px;
        overflow: hidden;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 15px;
    }

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
    <button class="btn btn-rojo" id="btn-lista-cartas">
        <i class="fas fa-list me-1"></i> Lista de Cartas
    </button>
    <button class="btn btn-outline-danger" id="btn-nueva-carta">
        <i class="fas fa-plus me-1"></i> Nueva Carta
    </button>
    <button class="btn btn-outline-danger" id="btn-editar-plantilla">
        <i class="fas fa-file-alt me-1"></i> Editar Plantilla
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
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="titulo_carta" class="form-label">Título de la Carta</label>
                    <input type="text" class="form-control" id="titulo_carta" name="titulo" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_carta" class="form-label">Tipo de Carta</label>
                    <select class="form-select" id="tipo_carta" name="tipo">
                        <option value="">Seleccione un tipo</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Formal">Formal</option>
                        <option value="Informativa">Informativa</option>
                        <option value="Solicitud">Solicitud</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

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

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Imagen de Cabecera</label>
                    <div class="input-group mb-2">
                        <input type="file" class="form-control" id="header_image" name="header_image_file"
                            accept="image/*">
                        <button class="btn btn-outline-secondary" type="button" id="reset-header-carta">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-placeholder" id="header-placeholder-carta">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        Sin imagen
                    </div>
                    <img id="header-preview-carta" class="image-preview" alt="Vista previa de cabecera">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Imagen de Pie</label>
                    <div class="input-group mb-2">
                        <input type="file" class="form-control" id="footer_image" name="footer_image_file"
                            accept="image/*">
                        <button class="btn btn-outline-secondary" type="button" id="reset-footer-carta">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-placeholder" id="footer-placeholder-carta">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        Sin imagen
                    </div>
                    <img id="footer-preview-carta" class="image-preview" alt="Vista previa de pie">
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
            <button type="button" class="btn btn-outline-secondary" id="btn-preview-carta">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-carta">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen de Cabecera</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="plantilla_header_image"
                                        name="header_image_file" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-plantilla-header">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="image-placeholder" id="header-placeholder-plantilla">
                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                    Sin imagen
                                </div>
                                <img id="plantilla-header-preview" class="image-preview" alt="Vista previa de cabecera">
                                <small class="text-muted">Si no selecciona una imagen, se usará la de la
                                    plantilla.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen de Pie</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="plantilla_footer_image"
                                        name="footer_image_file" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-plantilla-footer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="image-placeholder" id="footer-placeholder-plantilla">
                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                    Sin imagen
                                </div>
                                <img id="plantilla-footer-preview" class="image-preview" alt="Vista previa de pie">
                                <small class="text-muted">Si no selecciona una imagen, se usará la de la
                                    plantilla.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editor-container-plantilla" class="form-label">Contenido de la Plantilla</label>
                        <div id="editor-container-plantilla" class="editor-container"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

            // Configurar eventos para las imágenes
            $("#header_image").on("change", function (e) {
                manejarCambioImagen(e, 'header_image_data', 'header-preview-carta', 'header-placeholder-carta');
            });

            $("#footer_image").on("change", function (e) {
                manejarCambioImagen(e, 'footer_image_data', 'footer-preview-carta', 'footer-placeholder-carta');
            });

            $("#reset-header-carta").on("click", function () {
                restablecerImagen('header_image_data', 'header-preview-carta', 'header-placeholder-carta');
            });

            $("#reset-footer-carta").on("click", function () {
                restablecerImagen('footer_image_data', 'footer-preview-carta', 'footer-placeholder-carta');
            });

            // Configurar eventos para el formulario de plantilla
            $("#btn-save-plantilla").on("click", function () {
                guardarPlantilla();
            });

            $("#plantilla_header_image").on("change", function (e) {
                manejarCambioImagen(e, 'plantilla_header_image_data', 'plantilla-header-preview', 'header-placeholder-plantilla');
            });

            $("#plantilla_footer_image").on("change", function (e) {
                manejarCambioImagen(e, 'plantilla_footer_image_data', 'plantilla-footer-preview', 'footer-placeholder-plantilla');
            });

            $("#reset-plantilla-header").on("click", function () {
                restablecerImagen('plantilla_header_image_data', 'plantilla-header-preview', 'header-placeholder-plantilla');
            });

            $("#reset-plantilla-footer").on("click", function () {
                restablecerImagen('plantilla_footer_image_data', 'plantilla-footer-preview', 'footer-placeholder-plantilla');
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
            $("#btn-lista-cartas").removeClass("btn-outline-danger").addClass("btn-rojo");
            $("#btn-nueva-carta").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-editar-plantilla").addClass("btn-outline-danger").removeClass("btn-rojo");

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
                    quillCSS.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                    document.head.appendChild(quillCSS);
                    quillCssLoaded = true;
                }

                // Cargar JavaScript de Quill
                var quillScript = document.createElement('script');
                quillScript.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
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

                html += `
                <div class="col">
                    <div class="card carta-card">
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
                        <div class="card-body">
                            <h5 class="card-title">${carta.titulo}</h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> ${cliente}<br>
                                    <i class="fas fa-calendar-alt me-1"></i> ${fecha}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="${_URL}/ajs/carta/generarPDF?id=${carta.id}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Ver PDF
                            </a>
                            <button class="btn btn-sm btn-rojo carta-editar" data-id="${carta.id}">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
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
                    // Desconectar todos los eventos del editor
                    cartaEditor.off('text-change');

                    // Remover el contenido del editor
                    cartaEditor.container.innerHTML = '';

                    // Remover la toolbar si existe
                    const toolbarElement = document.querySelector('.ql-toolbar');
                    if (toolbarElement && toolbarElement.parentNode) {
                        toolbarElement.parentNode.removeChild(toolbarElement);
                    }

                    // Limpiar el contenedor del editor
                    $('#editor-container-carta').html('');

                    // Establecer la variable a null
                    cartaEditor = null;
                } catch (error) {
                    console.error("Error al destruir el editor:", error);
                }
            }
        }

        // Función para mostrar el formulario de nueva carta
        function mostrarFormularioNuevoCarta() {
            console.log("Mostrando formulario de nueva carta...");

            // Actualizar estado de los botones
            $("#btn-lista-cartas").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-nueva-carta").removeClass("btn-outline-danger").addClass("btn-rojo");
            $("#btn-editar-plantilla").addClass("btn-outline-danger").removeClass("btn-rojo");
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
        }

        // Función para esperar a que Quill esté cargado
        function esperarPorQuill(callback) {
            if (quillLoaded) {
                callback();
            } else {
                console.log("Esperando a que Quill se cargue...");
                cargarQuillSiNoExiste();

                // Verificar cada 100ms si Quill ya está cargado
                var checkQuill = setInterval(function () {
                    if (typeof Quill !== 'undefined') {
                        clearInterval(checkQuill);
                        quillLoaded = true;
                        console.log("Quill ya está disponible, continuando...");
                        callback();
                    }
                }, 100);

                // Establecer un tiempo límite de 5 segundos
                setTimeout(function () {
                    clearInterval(checkQuill);
                    if (!quillLoaded) {
                        console.error("Tiempo de espera agotado para cargar Quill");
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo cargar el editor. Por favor, recargue la página e intente nuevamente.'
                        });
                    }
                }, 5000);
            }
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

            // Esperar a que Quill esté cargado
            esperarPorQuill(function () {
                // Cargar datos de la plantilla primero
                cargarDatosPlantilla();

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('editarPlantillaCartaModal'));
                modal.show();

                // Inicializar el editor después de que el modal esté visible
                setTimeout(() => {
                    inicializarEditorPlantilla();
                }, 100);
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
                    // Desconectar todos los eventos del editor
                    templateEditor.off('text-change');

                    // Remover el contenido del editor
                    templateEditor.container.innerHTML = '';

                    // Remover todas las toolbars de Quill que puedan existir
                    const toolbars = document.querySelectorAll('.ql-toolbar');
                    toolbars.forEach(toolbar => {
                        if (toolbar.parentNode) {
                            toolbar.parentNode.removeChild(toolbar);
                        }
                    });

                    // Remover todos los contenedores de editor que puedan existir
                    const editors = document.querySelectorAll('.ql-editor');
                    editors.forEach(editor => {
                        if (editor.parentNode) {
                            editor.parentNode.removeChild(editor);
                        }
                    });

                    // Limpiar el contenedor del editor
                    $('#editor-container-plantilla').html('');

                    // Establecer la variable a null
                    templateEditor = null;
                } catch (error) {
                    console.error("Error al destruir el editor de plantilla:", error);
                }
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
                // Asegurarse de que el contenedor esté vacío
                $("#editor-container-plantilla").html('');

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

                // Asignar el evento de cambio de texto
                if (templateEditor && templateEditor.on) {
                    templateEditor.on('text-change', function () {
                        var contenidoInput = document.getElementById('contenido_plantilla');
                        if (contenidoInput) {
                            contenidoInput.value = templateEditor.root.innerHTML;
                        }
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

        // Agregar evento para destruir el editor cuando se cierre el modal
        $('#editarPlantillaCartaModal').on('hidden.bs.modal', function () {
            destruirEditorPlantilla();
        });
        $('#editarPlantillaCartaModal').on('hidden.bs.modal', function () {
            // Limpiar completamente el contenedor
            $('#editor-container-plantilla').empty();
        });
        $('#editarPlantillaCartaModal').on('show.bs.modal', function () {
            // Asegurarse de que no haya instancias previas
            destruirEditorPlantilla();
        });

        // Función para inicializar el autocomplete de clientes
        function inicializarAutocompletarClientes() {
            $("#cliente_search").autocomplete({
                source: _URL + "/ajs/buscar/cliente/datos", // Usamos la ruta existente
                minLength: 2,
                select: function (event, ui) {
                    event.preventDefault();

                    // Establecer los valores seleccionados
                    $("#cliente_id").val(ui.item.codigo); // Usamos 'codigo' que es el campo que devuelve la API
                    $("#cliente_nombre").text(ui.item.datos);
                    $("#cliente_documento").text("Documento: " + ui.item.documento);
                    $("#cliente_direccion").text("Dirección: " + (ui.item.direccion || "No especificada"));

                    // Mostrar la información del cliente
                    $("#cliente_info").show();

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
                    $("#cliente_info").hide();
                } else {
                    // Si tiene texto, iniciar búsqueda
                    $("#cliente_search").autocomplete("search", $("#cliente_search").val());
                }
            });
        }

        // Función para cargar la plantilla de carta
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
                            document.getElementById('contenido_carta').value = plantillaActual.contenido;
                        }

                        // Mostrar imágenes de la plantilla en las vistas previas
                        if (plantillaActual.header_image_url) {
                            document.getElementById('header-preview-carta').src = plantillaActual.header_image_url;
                            document.getElementById('header-preview-carta').style.display = 'block';
                            document.getElementById('header-placeholder-carta').style.display = 'none';
                        }

                        if (plantillaActual.footer_image_url) {
                            document.getElementById('footer-preview-carta').src = plantillaActual.footer_image_url;
                            document.getElementById('footer-preview-carta').style.display = 'block';
                            document.getElementById('footer-placeholder-carta').style.display = 'none';
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

        // Función para cargar datos de la plantilla
        function cargarDatosPlantilla() {
            $.ajax({
                url: _URL + "/ajs/carta/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        plantillaActual = data.data;

                        // Llenar formulario
                        document.getElementById('id_plantilla_carta').value = plantillaActual.id;
                        document.getElementById('titulo_plantilla').value = plantillaActual.titulo;
                        document.getElementById('plantilla_header_image_data').value = plantillaActual.header_image || '';
                        document.getElementById('plantilla_footer_image_data').value = plantillaActual.footer_image || '';

                        // Mostrar imágenes
                        if (plantillaActual.header_image_url) {
                            document.getElementById('plantilla-header-preview').src = plantillaActual.header_image_url;
                            document.getElementById('plantilla-header-preview').style.display = 'block';
                            document.getElementById('header-placeholder-plantilla').style.display = 'none';
                        }

                        if (plantillaActual.footer_image_url) {
                            document.getElementById('plantilla-footer-preview').src = plantillaActual.footer_image_url;
                            document.getElementById('plantilla-footer-preview').style.display = 'block';
                            document.getElementById('footer-placeholder-plantilla').style.display = 'none';
                        }

                        // Establecer contenido en el editor
                        if (templateEditor) {
                            templateEditor.root.innerHTML = plantillaActual.contenido;
                            document.getElementById('contenido_plantilla').value = plantillaActual.contenido;
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

        // Función para manejar el cambio de imagen
        function manejarCambioImagen(event, inputId, previewId, placeholderId) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    document.getElementById(inputId).value = e.target.result;
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';

                    if (placeholderId) {
                        document.getElementById(placeholderId).style.display = 'none';
                    }
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
                        document.getElementById('tipo_carta').value = cartaActual.tipo || '';
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

        // Exponer algunas funciones al ámbito global para poder llamarlas desde HTML
        window.recargarCartas = cargarCartas;
        window.editarCarta = editarCarta;
        window.eliminarCarta = eliminarCarta;
        window.editarPlantillaCarta = editarPlantillaCarta;
        window.mostrarFormularioNuevoCarta = mostrarFormularioNuevoCarta;
        window.mostrarVistaListaCartas = mostrarVistaListaCartas;
    })();
</script>