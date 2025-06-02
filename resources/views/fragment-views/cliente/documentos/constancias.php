<!-- resources/views/fragment-views/cliente/documentos/componentes/constancias.php -->
<style>
    /* Estilos para las vistas */
    .vista {
        display: none;
    }

    .vista.active {
        display: block;
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

    /* Estilos para las tarjetas de constancias */
    .constancia-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .constancia-card:hover {
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

<!-- Botones de acción -->
<div class="mb-4">
    <button class="btn btn-rojo" id="btn-lista-constancias">
        <i class="fas fa-list me-1"></i> Lista de Constancias
    </button>
    <button class="btn btn-outline-danger" id="btn-nueva-constancia">
        <i class="fas fa-plus me-1"></i> Nueva Constancia
    </button>
    <button class="btn btn-outline-danger" id="btn-editar-plantilla-constancia">
        <i class="fas fa-file-alt me-1"></i> Editar Plantilla
    </button>
</div>

<!-- Vista de lista de constancias -->
<div id="vista-lista-constancias" class="vista active">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Constancias y Certificados</h3>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control" id="buscar-constancia" placeholder="Buscar constancias...">
            <button class="btn btn-rojo" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div id="lista-constancias-container">
        <!-- Aquí se cargarán dinámicamente las constancias -->
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando constancias...</p>
        </div>
    </div>
</div>

<!-- Vista de formulario de nueva/editar constancia -->
<div id="vista-editar-constancia" class="vista">
    <div class="form-header">
        <h3 id="titulo-pagina-constancia" class="m-0">Nueva Constancia</h3>
        <p class="m-0">Complete la información de la constancia</p>
    </div>

    <form id="formConstancia" enctype="multipart/form-data">
        <input type="hidden" id="id_constancia" name="id">
        <input type="hidden" id="contenido_constancia" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="titulo_constancia" class="form-label">Título de la Constancia</label>
                    <input type="text" class="form-control" id="titulo_constancia" name="titulo" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_constancia" class="form-label">Tipo de Constancia</label>
                    <select class="form-select" id="tipo_constancia" name="tipo">
                        <option value="">Seleccione un tipo</option>
                        <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                        <option value="ANTIGÜEDAD DE EQUIPO">ANTIGÜEDAD DE EQUIPO</option>
                        <option value="GARANTÍA">GARANTÍA</option>
                        <option value="SERVICIO">SERVICIO</option>
                        <option value="CAPACITACIÓN">CAPACITACIÓN</option>
                        <option value="OTRO">OTRO</option>
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
                        <button class="btn btn-outline-secondary" type="button" id="reset-header-constancia">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-placeholder" id="header-placeholder-constancia">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        Sin imagen
                    </div>
                    <img id="header-preview-constancia" class="image-preview" alt="Vista previa de cabecera">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Imagen de Pie</label>
                    <div class="input-group mb-2">
                        <input type="file" class="form-control" id="footer_image" name="footer_image_file"
                            accept="image/*">
                        <button class="btn btn-outline-secondary" type="button" id="reset-footer-constancia">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-placeholder" id="footer-placeholder-constancia">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        Sin imagen
                    </div>
                    <img id="footer-preview-constancia" class="image-preview" alt="Vista previa de pie">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="editor-container-constancia" class="form-label">Contenido de la Constancia</label>
            <div id="editor-container-constancia" class="editor-container"></div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" id="btn-cancel-constancia">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn-preview-constancia">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-constancia">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
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

<!-- Modal de Edición de Plantilla -->
<div class="modal fade" id="editarPlantillaConstanciaModal" tabindex="-1"
    aria-labelledby="editarPlantillaConstanciaModalLabel" aria-hidden="true">
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
        if (window.constanciasModuleInitialized) {
            console.log("El módulo de constancias ya ha sido inicializado. Evitando reinicialización.");
            return;
        }

        // Marcamos el módulo como inicializado
        window.constanciasModuleInitialized = true;

        console.log("Inicializando módulo de constancias...");

        // Variables del módulo (no globales)
        var constancias = [];
        var filtroActual = '';
        var tipoFiltroActual = 'todos';
        var constanciaEditor = null;
        var templateEditor = null;
        var quillLoaded = false;
        var quillCssLoaded = false;

        // Inicializar cuando el DOM esté listo
        $(document).ready(function () {
            console.log("DOM cargado, configurando eventos del módulo de constancias...");

            // Configurar eventos para los botones de navegación
            $("#btn-lista-constancias").on("click", function () {
                mostrarVistaListaConstancias();
            });

            $("#btn-nueva-constancia").on("click", function () {
                mostrarFormularioNuevoConstancia();
            });

            $("#btn-editar-plantilla-constancia").on("click", function () {
                editarPlantillaConstancia();
            });

            // Configurar el modal de confirmación para eliminar
            $('#confirmarEliminarConstanciaModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');

                $('#btn-confirmar-eliminar-constancia').off('click').on('click', function () {
                    eliminarConstancia(id);
                });
            });

            // Configurar eventos de búsqueda
            $("#buscar-constancia").on("keyup", function () {
                buscarConstancias();
            });

            // Configurar eventos para el formulario de constancia
            $("#btn-cancel-constancia").on("click", function () {
                mostrarVistaListaConstancias();
            });

            $("#btn-save-constancia").on("click", function () {
                guardarConstancia();
            });

            $("#btn-preview-constancia").on("click", function () {
                mostrarVistaPrevia();
            });

            // Configurar eventos para las imágenes
            $("#header_image").on("change", function (e) {
                manejarCambioImagen(e, 'header_image_data', 'header-preview-constancia', 'header-placeholder-constancia');
            });

            $("#footer_image").on("change", function (e) {
                manejarCambioImagen(e, 'footer_image_data', 'footer-preview-constancia', 'footer-placeholder-constancia');
            });

            $("#reset-header-constancia").on("click", function () {
                restablecerImagen('header_image_data', 'header-preview-constancia', 'header-placeholder-constancia');
            });

            $("#reset-footer-constancia").on("click", function () {
                restablecerImagen('footer_image_data', 'footer-preview-constancia', 'footer-placeholder-constancia');
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

            // Cargar constancias
            cargarConstancias();

            // Cargar Quill si no está cargado
            cargarQuillSiNoExiste();
        });

        function destruirEditor() {
            if (constanciaEditor) {
                try {
                    constanciaEditor.off('text-change');

                    //remover el contenido del editor
                    constanciaEditor.container.innerHTML = '';
                    //remover el toolbar so existe 
                    const toolbarElement = document.querySelector('.ql-toolbar');
                    if (toolbarElement && toolbarElement.parentNode) {
                        toolbarElement.parentNode.removeChild(toolbarElement);
                    }
                    // limpiar el contenedor del editor
                    $('#editor-container-constancia').html('');

                    // establecer la variable a null
                    constanciaEditor = null;
                } catch (error) {
                    console.error("Error al destruir el editor:", error);
                }
            }
        }

        // Función para mostrar la vista de lista de constancias
        function mostrarVistaListaConstancias() {
            $(".vista").removeClass("active");
            $("#vista-lista-constancias").addClass("active");

            // Actualizar estado de los botones
            $("#btn-lista-constancias").removeClass("btn-outline-danger").addClass("btn-rojo");
            $("#btn-nueva-constancia").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-editar-plantilla-constancia").addClass("btn-outline-danger").removeClass("btn-rojo");

            destruirEditor();

            // Recargar la lista de constancias
            cargarConstancias();
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

        // Función para cargar las constancias
        function cargarConstancias() {
            console.log("Cargando constancias...");

            // Mostrar indicador de carga
            $("#lista-constancias-container").html(`
            <div class="text-center py-5">
                <div class="spinner-border text-rojo" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando constancias...</p>
            </div>
        `);

            // Construir la URL con los filtros
            let url = _URL + "/ajs/constancia/render";
            if (filtroActual && tipoFiltroActual !== 'todos') {
                url += `?filtro=${encodeURIComponent(filtroActual)}&tipo_busqueda=${tipoFiltroActual}`;
            }

            console.log("URL de carga:", url);

            // Realizar petición AJAX para obtener las constancias
            $.ajax({
                url: url,
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    console.log("Respuesta de constancias:", data);

                    // Asegurarse de que data sea un array
                    if (data === null || data === undefined) {
                        console.log("No se recibieron datos, mostrando mensaje de no hay constancias");
                        mostrarNoHayConstancias();
                        return;
                    }

                    constancias = Array.isArray(data) ? data : [];
                    console.log("Constancias procesadas:", constancias);
                    renderizarConstancias();
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar constancias:", status, error);
                    $("#lista-constancias-container").html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar las constancias. Por favor, intente nuevamente.
                    </div>
                    <button class="btn btn-rojo mt-3" onclick="window.recargarConstancias()">
                        <i class="fas fa-sync me-2"></i>Reintentar
                    </button>
                `);
                }
            });
        }

        // Función para mostrar mensaje de no hay constancias
        function mostrarNoHayConstancias() {
            $("#lista-constancias-container").html(`
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                No se encontraron constancias.
            </div>
            <button class="btn btn-rojo mt-3" id="btn-crear-primera-constancia">
                <i class="fas fa-plus me-2"></i>Crear primera constancia
            </button>
        `);

            // Agregar evento al botón de crear primera constancia
            $("#btn-crear-primera-constancia").on("click", function () {
                mostrarFormularioNuevoConstancia();
            });
        }

        // Función para renderizar las constancias
        function renderizarConstancias() {
            if (!constancias || constancias.length === 0) {
                mostrarNoHayConstancias();
                return;
            }

            let html = '<div class="row row-cols-1 row-cols-md-3 g-4">';

            constancias.forEach(function (constancia) {
                const fecha = new Date(constancia.fecha_creacion).toLocaleDateString();
                const cliente = constancia.cliente_nombre || 'Sin cliente';

                html += `
                <div class="col">
                    <div class="card constancia-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-rojo">${constancia.tipo || 'Sin tipo'}</span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-dark" type="button" id="dropdownConstancia${constancia.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownConstancia${constancia.id}">
                                    <li><a class="dropdown-item" href="${_URL}/ajs/constancia/generarPDF?id=${constancia.id}" target="_blank">
                                        <i class="fas fa-file-pdf me-2"></i> Ver PDF
                                    </a></li>
                                    <li><a class="dropdown-item constancia-editar" href="#" data-id="${constancia.id}">
                                        <i class="fas fa-edit me-2"></i> Editar
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmarEliminarConstanciaModal" data-id="${constancia.id}">
                                        <i class="fas fa-trash-alt me-2"></i> Eliminar
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${constancia.titulo}</h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> ${cliente}<br>
                                    <i class="fas fa-calendar-alt me-1"></i> ${fecha}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="${_URL}/ajs/constancia/generarPDF?id=${constancia.id}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Ver PDF
                            </a>
                            <button class="btn btn-sm btn-rojo constancia-editar" data-id="${constancia.id}">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            });

            html += '</div>';

            $("#lista-constancias-container").html(html);

            // Agregar eventos a los botones de editar
            $(".constancia-editar").on("click", function () {
                const id = $(this).data('id');
                editarConstancia(id);
            });
        }

        // Función para buscar constancias
        function buscarConstancias() {
            const busqueda = $("#buscar-constancia").val().trim().toLowerCase();

            if (busqueda === "") {
                // Si la búsqueda está vacía, mostrar todas las constancias según el filtro de tipo
                if (tipoFiltroActual === "todos") {
                    filtroActual = "";
                }
            } else {
                // Si hay texto de búsqueda, filtrar por título
                filtroActual = busqueda;
                tipoFiltroActual = "titulo";
            }

            // Recargar las constancias con el filtro
            cargarConstancias();
        }

        // Función para mostrar el formulario de nueva constancia
        function mostrarFormularioNuevoConstancia() {
            console.log("Mostrando formulario de nueva constancia...");

            // Actualizar estado de los botones
            $("#btn-lista-constancias").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-nueva-constancia").removeClass("btn-outline-danger").addClass("btn-rojo");
            $("#btn-editar-plantilla-constancia").addClass("btn-outline-danger").removeClass("btn-rojo");

            // Mostrar la vista de edición
            $(".vista").removeClass("active");
            $("#vista-editar-constancia").addClass("active");

            // Limpiar el formulario
            $("#id_constancia").val("");
            $("#titulo_constancia").val("");
            $("#tipo_constancia").val("");
            $("#cliente_id").val("");
            $("#header_image_data").val("");
            $("#footer_image_data").val("");

            // Ocultar las imágenes de vista previa
            $("#header-preview-constancia").hide();
            $("#footer-preview-constancia").hide();
            $("#header-placeholder-constancia").show();
            $("#footer-placeholder-constancia").show();

            // Actualizar título
            $("#titulo-pagina-constancia").text("Nueva Constancia");

            // inicializar autocomplete para clientes
            inicializarAutocompletarClientes();

            // Esperar a que Quill esté cargado
            esperarPorQuill(function () {
                // Inicializar el editor
                inicializarEditorConstancia();

                // Cargar plantilla actual
                cargarPlantillaConstancia();
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

        // Función para editar una constancia existente
        function editarConstancia(id) {
            console.log("Editando constancia ID:", id);

            // Actualizar estado de los botones
            $("#btn-lista-constancias").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-nueva-constancia").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-editar-plantilla-constancia").addClass("btn-outline-danger").removeClass("btn-rojo");

            // Mostrar la vista de edición
            $(".vista").removeClass("active");
            $("#vista-editar-constancia").addClass("active");

            // Actualizar título
            $("#titulo-pagina-constancia").text("Editar Constancia");

            // inicializar el autocomplete para clientes
            inicializarAutocompletarClientes();

            // Esperar a que Quill esté cargado
            esperarPorQuill(function () {
                // Inicializar el editor
                inicializarEditorConstancia();

                // Cargar datos de la constancia
                cargarDatosConstancia(id);
            });
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

        // Función para editar la plantilla de constancia
        function editarPlantillaConstancia() {
            console.log("Editando plantilla de constancia...");
             destruirEditorPlantilla();
            // Actualizar estado de los botones
            $("#btn-lista-constancias").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-nueva-constancia").removeClass("btn-rojo").addClass("btn-outline-danger");
            $("#btn-editar-plantilla-constancia").removeClass("btn-outline-danger").addClass("btn-rojo");

            // Esperar a que Quill esté cargado
            esperarPorQuill(function () {
                // Inicializar el editor de plantilla
                inicializarEditorPlantilla();

                // Cargar datos de la plantilla
                cargarDatosPlantilla();

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('editarPlantillaConstanciaModal'));
                modal.show();
            });
        }

        // Función para inicializar el editor Quill
        function inicializarEditorConstancia() {
            console.log("Inicializando editor Quill...");

            // Verificar que el contenedor exista
            if ($("#editor-container-constancia").length === 0) {
                console.error("Error: No se encontró el contenedor del editor #editor-container-constancia");
                return;
            }
            destruirEditor();

            try {
                // Inicializar Quill
                constanciaEditor = new Quill('#editor-container-constancia', {
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
                    placeholder: 'Escriba el contenido de la constancia...',
                    theme: 'snow'
                });

                console.log("Editor Quill inicializado correctamente");

                // Asignar el evento de cambio de texto solo si el editor se inicializó correctamente
                if (constanciaEditor && constanciaEditor.on) {
                    constanciaEditor.on('text-change', function () {
                        var contenidoInput = document.getElementById('contenido_constancia');
                        if (contenidoInput) {
                            contenidoInput.value = constanciaEditor.root.innerHTML;
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

        // Función para inicializar el editor Quill para la plantilla
        function inicializarEditorPlantilla() {
            console.log("Inicializando editor de plantilla Quill...");

            if (templateEditor) {
                try {
                    // Desconectar todos los eventos del editor
                    templateEditor.off('text-change');

                    // Remover el contenido del editor
                    templateEditor.container.innerHTML = '';

                    // Remover la toolbar si existe
                    const toolbarElement = document.querySelector('#editor-container-plantilla + .ql-toolbar');
                    if (toolbarElement && toolbarElement.parentNode) {
                        toolbarElement.parentNode.removeChild(toolbarElement);
                    }

                    // Limpiar el contenedor del editor
                    $('#editor-container-plantilla').html('');

                    // Establecer la variable a null
                    templateEditor = null;
                } catch (error) {
                    console.error("Error al destruir el editor de plantilla:", error);
                }
            }

            // Asegurarse de que el contenedor esté vacío
            $("#editor-container-plantilla").html('');


            try {
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

                // Asignar el evento de cambio de texto solo si el editor se inicializó correctamente
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
        // Función para cargar la plantilla de constancia
        function cargarPlantillaConstancia() {
            $.ajax({
                url: _URL + "/ajs/constancia/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        plantillaActual = data.data;

                        // Establecer contenido predeterminado basado en la plantilla
                        if (constanciaEditor) {
                            constanciaEditor.root.innerHTML = plantillaActual.contenido;
                            document.getElementById('contenido_constancia').value = plantillaActual.contenido;
                        }

                        // Mostrar imágenes de la plantilla en las vistas previas
                        if (plantillaActual.header_image_url) {
                            document.getElementById('header-preview-constancia').src = plantillaActual.header_image_url;
                            document.getElementById('header-preview-constancia').style.display = 'block';
                            document.getElementById('header-placeholder-constancia').style.display = 'none';
                        }

                        if (plantillaActual.footer_image_url) {
                            document.getElementById('footer-preview-constancia').src = plantillaActual.footer_image_url;
                            document.getElementById('footer-preview-constancia').style.display = 'block';
                            document.getElementById('footer-placeholder-constancia').style.display = 'none';
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar la plantilla:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar la plantilla de constancia'
                    });
                }
            });
        }

        // Función para cargar datos de la plantilla
        function cargarDatosPlantilla() {
            $.ajax({
                url: _URL + "/ajs/constancia/obtener-template",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        plantillaActual = data.data;

                        // Llenar formulario
                        document.getElementById('id_plantilla_constancia').value = plantillaActual.id;
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

        // Función para cargar datos de una constancia
        function cargarDatosConstancia(id) {
            const formData = new FormData();
            formData.append('id', id);

            $.ajax({
                url: _URL + "/ajs/constancia/getOne",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success && data.data) {
                        constanciaActual = data.data;

                        // Llenar formulario
                        document.getElementById('id_constancia').value = constanciaActual.id;
                        document.getElementById('cliente_id').value = constanciaActual.id_cliente || '';
                        // Si hay un cliente seleccionado, mostrar su información
                        if (constanciaActual.id_cliente && constanciaActual.cliente_nombre) {
                            $("#cliente_search").val(constanciaActual.cliente_nombre);
                            $("#cliente_nombre").text(constanciaActual.cliente_nombre);
                            $("#cliente_documento").text("Documento: " + (constanciaActual.cliente_documento || ""));
                            $("#cliente_direccion").text("Dirección: " + (constanciaActual.cliente_direccion || "No especificada"));
                            $("#cliente_info").show();
                        }
                        document.getElementById('tipo_constancia').value = constanciaActual.tipo || '';
                        document.getElementById('titulo_constancia').value = constanciaActual.titulo;
                        document.getElementById('header_image_data').value = constanciaActual.header_image || '';
                        document.getElementById('footer_image_data').value = constanciaActual.footer_image || '';

                        // Mostrar imágenes si existen
                        if (constanciaActual.header_image_url) {
                            document.getElementById('header-preview-constancia').src = constanciaActual.header_image_url;
                            document.getElementById('header-preview-constancia').style.display = 'block';
                            document.getElementById('header-placeholder-constancia').style.display = 'none';
                        }

                        if (constanciaActual.footer_image_url) {
                            document.getElementById('footer-preview-constancia').src = constanciaActual.footer_image_url;
                            document.getElementById('footer-preview-constancia').style.display = 'block';
                            document.getElementById('footer-placeholder-constancia').style.display = 'none';
                        }

                        // Establecer contenido en el editor
                        if (constanciaEditor) {
                            constanciaEditor.root.innerHTML = constanciaActual.contenido;
                            document.getElementById('contenido_constancia').value = constanciaActual.contenido;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Error al cargar la constancia'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar la constancia'
                    });
                }
            });
        }

        // Función para guardar constancia
        function guardarConstancia() {
            // Validar formulario
            const titulo = document.getElementById('titulo_constancia').value.trim();
            const contenido = document.getElementById('contenido_constancia').value.trim();
            const tipo = document.getElementById('tipo_constancia').value.trim();

            if (!titulo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El título es obligatorio'
                });
                return;
            }

            if (!tipo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El tipo es obligatorio'
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
            const formData = new FormData(document.getElementById('formConstancia'));

            // Asegurarse de que el contenido esté incluido
            formData.set('contenido', contenido);

            // Determinar si es inserción o edición
            const constanciaId = document.getElementById('id_constancia').value;
            let url = _URL + '/ajs/constancia/insertar';

            if (constanciaId) {
                url = _URL + '/ajs/constancia/editar';
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Guardando',
                text: 'Guardando constancia...',
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
                            // Volver a la lista de constancias
                            mostrarVistaListaConstancias();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al guardar la constancia'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar la constancia'
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
            const formData = new FormData(document.getElementById('formPlantillaConstancia'));

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
                url: _URL + "/ajs/constancia/guardar-template",
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
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editarPlantillaConstanciaModal'));
                            modal.hide();

                            // Actualizar estado de los botones
                            $("#btn-lista-constancias").addClass("btn-rojo").removeClass("btn-outline-danger");
                            $("#btn-nueva-constancia").removeClass("btn-rojo").addClass("btn-outline-danger");
                            $("#btn-editar-plantilla-constancia").removeClass("btn-rojo").addClass("btn-outline-danger");

                            // Recargar constancias
                            mostrarVistaListaConstancias();
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

        // Función para mostrar la vista previa de una constancia
        function mostrarVistaPrevia() {
            // Validar que haya contenido
            const contenido = document.getElementById('contenido_constancia').value.trim();

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
            formData.append('titulo', document.getElementById('titulo_constancia').value.trim());
            formData.append('contenido', contenido);
            formData.append('header_image', document.getElementById('header_image_data').value);
            formData.append('footer_image', document.getElementById('footer_image_data').value);

            // Enviar solicitud para generar vista previa
            $.ajax({
                url: _URL + "/ajs/constancia/vista-previa",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        // Mostrar la vista previa en el iframe
                        document.getElementById('preview-frame-constancia').src = "data:application/pdf;base64," + data.pdfBase64;

                        // Mostrar el modal
                        const modal = new bootstrap.Modal(document.getElementById('previewConstanciaModal'));
                        modal.show();

                        // Configurar el botón de descarga
                        document.getElementById('btn-download-pdf').onclick = function () {
                            const blob = b64toBlob(data.pdfBase64, 'application/pdf');
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'constancia_' + new Date().getTime() + '.pdf';
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

        // Función para eliminar una constancia
        function eliminarConstancia(id) {
            // Mostrar indicador de carga
            $("#btn-confirmar-eliminar-constancia").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...').prop('disabled', true);

            const formData = new FormData();
            formData.append('id', id);

            $.ajax({
                url: _URL + "/ajs/constancia/borrar",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    // Cerrar el modal
                    $('#confirmarEliminarConstanciaModal').modal('hide');

                    if (data.res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.msg
                        }).then(() => {
                            cargarConstancias();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al eliminar la constancia'
                        });
                    }

                    // Restaurar el botón
                    $("#btn-confirmar-eliminar-constancia").html('Eliminar').prop('disabled', false);
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", status, error);

                    // Cerrar el modal
                    $('#confirmarEliminarConstanciaModal').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar la constancia'
                    });

                    // Restaurar el botón
                    $("#btn-confirmar-eliminar-constancia").html('Eliminar').prop('disabled', false);
                }
            });
        }

        // Exponer algunas funciones al ámbito global para poder llamarlas desde HTML
        window.recargarConstancias = cargarConstancias;
        window.editarConstancia = editarConstancia;
        window.eliminarConstancia = eliminarConstancia;
        window.editarPlantillaConstancia = editarPlantillaConstancia;
        window.mostrarFormularioNuevoConstancia = mostrarFormularioNuevoConstancia;
        window.mostrarVistaListaConstancias = mostrarVistaListaConstancias;
    })();
</script>