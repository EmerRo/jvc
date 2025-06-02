<!-- resources/views/fragment-views/cliente/documentos/componentes/archivos-internos.php -->
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
    
    /* Estilos para las tarjetas de archivos */
    .archivo-card {
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .archivo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    /* Cabecera de formulario */
    .form-header {
        background-color: #dc3545;
        color: white;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    /* Estilos para botones */
    .btn-rojo {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-rojo:hover {
        background-color: #c82333;
        color: white;
    }
    
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
    }
    
    /* Estilos para el visor de PDF */
    .pdf-preview {
        width: 100%;
        height: 500px;
        border: 1px solid #dee2e6;
    }
    
    /* Estilos para el área de arrastrar y soltar */
    .dropzone {
        border: 2px dashed #dc3545;
        border-radius: 5px;
        padding: 25px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .dropzone:hover {
        background-color: #f1f1f1;
    }
    
    .dropzone.dragover {
        background-color: #e9ecef;
        border-color: #c82333;
    }
    
    /* Pestañas para cambiar entre documento y PDF */
    .nav-tabs .nav-link {
        color: #495057;
    }
    
    .nav-tabs .nav-link.active {
        color: #dc3545;
        font-weight: bold;
    }
</style>

<div class="mb-4">
    <button class="btn btn-rojo" id="btn-lista-archivos">
        <i class="fas fa-list me-1"></i> Lista de Archivos
    </button>
    <button class="btn btn-outline-danger" id="btn-nuevo-archivo">
        <i class="fas fa-plus me-1"></i> Nuevo Archivo
    </button>
    <button class="btn btn-outline-danger" id="btn-editar-plantilla-archivo">
        <i class="fas fa-file-alt me-1"></i> Editar Plantilla
    </button>
</div>

<!-- Vista de lista de archivos internos -->
<div id="vista-lista-archivos" class="vista active">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Archivos Internos</h3>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control" id="buscar-archivo" placeholder="Buscar archivos...">
            <button class="btn btn-rojo" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div id="lista-archivos-container">
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
<div id="vista-editar-archivo" class="vista">
    <div class="form-header">
        <h3 id="titulo-pagina-archivo" class="m-0">Nuevo Archivo Interno</h3>
        <p class="m-0">Complete la información del archivo interno</p>
    </div>

    <ul class="nav nav-tabs mb-4" id="archivoTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="documento-tab" data-bs-toggle="tab" data-bs-target="#documento-content" type="button" role="tab" aria-controls="documento-content" aria-selected="true">
                <i class="fas fa-file-word me-1"></i> Crear Documento
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pdf-tab" data-bs-toggle="tab" data-bs-target="#pdf-content" type="button" role="tab" aria-controls="pdf-content" aria-selected="false">
                <i class="fas fa-file-pdf me-1"></i> Subir PDF
            </button>
        </li>
    </ul>

    <form id="formArchivo" enctype="multipart/form-data">
        <input type="hidden" id="id_archivo" name="id">
        <input type="hidden" id="contenido_archivo" name="contenido">
        <input type="hidden" id="archivo_pdf_data" name="archivo_pdf">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">
        <input type="hidden" id="es_pdf_subido" name="es_pdf_subido" value="0">

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="titulo_archivo" class="form-label">Título del Archivo</label>
                    <input type="text" class="form-control" id="titulo_archivo" name="titulo" required>
                </div>
                
                <div class="mb-3">
                    <label for="tipo_archivo" class="form-label">Tipo de Archivo</label>
                    <select class="form-select" id="tipo_archivo" name="tipo">
                        <option value="">Seleccione un tipo</option>
                        <option value="MEMO">MEMO</option>
                        <option value="INFORME">INFORME</option>
                        <option value="ACTA">ACTA</option>
                        <option value="REPORTE">REPORTE</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </div>
                
              <div class="mb-3">
    <label for="cliente_search" class="form-label">Cliente</label>
    <div class="input-group">
        <input type="text" class="form-control border rounded-start-2 shadow-sm" id="cliente_search" placeholder="Buscar por nombre o documento..." autocomplete="off">
        <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-search-cliente">
            <i class="fas fa-search"></i>
        </button>
    </div>
    <input type="hidden" id="id_cliente" name="id_cliente">
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
                        <input type="file" class="form-control" id="header_image" name="header_image_file" accept="image/*">
                        <button class="btn btn-outline-secondary" type="button" id="reset-header-archivo">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-placeholder" id="header-placeholder-archivo">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        Sin imagen
                    </div>
                    <img id="header-preview-archivo" class="image-preview" alt="Vista previa de cabecera">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Imagen de Pie</label>
                    <div class="input-group mb-2">
                        <input type="file" class="form-control" id="footer_image" name="footer_image_file" accept="image/*">
                        <button class="btn btn-outline-secondary" type="button" id="reset-footer-archivo">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="image-placeholder" id="footer-placeholder-archivo">
                        <i class="fas fa-image fa-2x mb-2"></i><br>
                        Sin imagen
                    </div>
                    <img id="footer-preview-archivo" class="image-preview" alt="Vista previa de pie">
                </div>
            </div>
        </div>
        
        <div class="tab-content" id="archivoTabsContent">
            <!-- Pestaña de Crear Documento -->
            <div class="tab-pane fade show active" id="documento-content" role="tabpanel" aria-labelledby="documento-tab">
                <div class="mb-3">
                    <label for="editor-container-archivo" class="form-label">Contenido del Documento</label>
                    <div id="editor-container-archivo" class="editor-container"></div>
                </div>
            </div>
            
            <!-- Pestaña de Subir PDF -->
            <div class="tab-pane fade" id="pdf-content" role="tabpanel" aria-labelledby="pdf-tab">
                <div class="mb-3">
                    <label for="archivo_pdf" class="form-label">Archivo PDF</label>
                    <div id="dropzone-pdf" class="dropzone mb-3">
                        <i class="fas fa-file-pdf fa-3x mb-3 text-danger"></i>
                        <h5>Arrastra y suelta un archivo PDF aquí</h5>
                        <p>o</p>
                        <input type="file" id="archivo_pdf" name="archivo_pdf_file" accept="application/pdf" class="d-none">
                        <button type="button" class="btn btn-outline-danger" id="btn-select-pdf">
                            <i class="fas fa-upload me-1"></i> Seleccionar archivo
                        </button>
                        <p class="mt-2 small text-muted">Tamaño máximo: 10MB</p>
                    </div>
                    
                    <div id="pdf-preview-container" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span id="pdf-filename" class="text-truncate"></span>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btn-remove-pdf">
                                <i class="fas fa-times"></i> Eliminar
                            </button>
                        </div>
                        <iframe id="pdf-preview" class="pdf-preview"></iframe>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" id="btn-cancel-archivo">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn-preview-archivo">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-archivo">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewArchivoModal" tabindex="-1" aria-labelledby="previewArchivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewArchivoModalLabel">Vista Previa del Archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-archivo" style="width: 100%; height: 600px; border: none;"></iframe>
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
<div class="modal fade" id="confirmarEliminarArchivoModal" tabindex="-1"
    aria-labelledby="confirmarEliminarArchivoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarArchivoModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este archivo? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger"
                    id="btn-confirmar-eliminar-archivo">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición de Plantilla -->
<div class="modal fade" id="editarPlantillaArchivoModal" tabindex="-1" aria-labelledby="editarPlantillaArchivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarPlantillaArchivoModalLabel">Editar Plantilla de Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPlantillaArchivo" enctype="multipart/form-data">
                    <input type="hidden" id="id_plantilla_archivo" name="id">
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
                                    <input type="file" class="form-control" id="plantilla_header_image" name="header_image_file" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-plantilla-header">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="image-placeholder" id="header-placeholder-plantilla">
                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                    Sin imagen
                                </div>
                                <img id="plantilla-header-preview" class="image-preview" alt="Vista previa de cabecera">
                                <small class="text-muted">Si no selecciona una imagen, se usará la de la plantilla.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen de Pie</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="plantilla_footer_image" name="footer_image_file" accept="image/*">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-plantilla-footer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="image-placeholder" id="footer-placeholder-plantilla">
                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                    Sin imagen
                                </div>
                                <img id="plantilla-footer-preview" class="image-preview" alt="Vista previa de pie">
                                <small class="text-muted">Si no selecciona una imagen, se usará la de la plantilla.</small>
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
(function() {
    // Verificamos si el módulo ya ha sido inicializado
    if (window.archivosInternosModuleInitialized) {
        console.log("El módulo de archivos internos ya ha sido inicializado. Evitando reinicialización.");
        return;
    }
    
    // Marcamos el módulo como inicializado
    window.archivosInternosModuleInitialized = true;
    
    console.log("Inicializando módulo de archivos internos...");
    
    // Variables del módulo (no globales)
    var archivos = [];
    var filtroActual = '';
    var tipoFiltroActual = 'todos';
    var archivoEditor = null;
    var templateEditor = null;
    var quillLoaded = false;
    var quillCssLoaded = false;
    var currentTab = 'documento';

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        console.log("DOM cargado, configurando eventos del módulo de archivos internos...");
        
        // Configurar eventos para los botones de navegación
        $("#btn-lista-archivos").on("click", function() {
            mostrarVistaListaArchivos();
        });
        
        $("#btn-nuevo-archivo").on("click", function() {
            mostrarFormularioNuevoArchivo();
        });
        
        $("#btn-editar-plantilla-archivo").on("click", function() {
            editarPlantillaArchivo();
        });
        
        // Configurar el modal de confirmación para eliminar
        $('#confirmarEliminarArchivoModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            $('#btn-confirmar-eliminar-archivo').off('click').on('click', function () {
                eliminarArchivo(id);
            });
        });

        // Configurar eventos de búsqueda
        $("#buscar-archivo").on("keyup", function () {
            buscarArchivos();
        });
        
        // Configurar eventos para el formulario de archivo
        $("#btn-cancel-archivo").on("click", function() {
            mostrarVistaListaArchivos();
        });
        
        $("#btn-save-archivo").on("click", function() {
            guardarArchivo();
        });
        
        $("#btn-preview-archivo").on("click", function() {
            mostrarVistaPrevia();
        });
        
        // Configurar eventos para las imágenes
        $("#header_image").on("change", function(e) {
            manejarCambioImagen(e, 'header_image_data', 'header-preview-archivo', 'header-placeholder-archivo');
        });
        
        $("#footer_image").on("change", function(e) {
            manejarCambioImagen(e, 'footer_image_data', 'footer-preview-archivo', 'footer-placeholder-archivo');
        });
        
        $("#reset-header-archivo").on("click", function() {
            restablecerImagen('header_image_data', 'header-preview-archivo', 'header-placeholder-archivo');
        });
        
        $("#reset-footer-archivo").on("click", function() {
            restablecerImagen('footer_image_data', 'footer-preview-archivo', 'footer-placeholder-archivo');
        });
        
        // Configurar eventos para el formulario de plantilla
        $("#btn-save-plantilla").on("click", function() {
            guardarPlantilla();
        });
        
        $("#plantilla_header_image").on("change", function(e) {
            manejarCambioImagen(e, 'plantilla_header_image_data', 'plantilla-header-preview', 'header-placeholder-plantilla');
        });
        
        $("#plantilla_footer_image").on("change", function(e) {
            manejarCambioImagen(e, 'plantilla_footer_image_data', 'plantilla-footer-preview', 'footer-placeholder-plantilla');
        });
        
        $("#reset-plantilla-header").on("click", function() {
            restablecerImagen('plantilla_header_image_data', 'plantilla-header-preview', 'header-placeholder-plantilla');
        });
        
        $("#reset-plantilla-footer").on("click", function() {
            restablecerImagen('plantilla_footer_image_data', 'plantilla-footer-preview', 'footer-placeholder-plantilla');
        });
        
        // Configurar eventos para la carga de PDF
        $("#btn-select-pdf").on("click", function() {
            $("#archivo_pdf").click();
        });
        
        $("#archivo_pdf").on("change", function(e) {
            manejarCambioPDF(e);
        });
        
        $("#btn-remove-pdf").on("click", function() {
            restablecerPDF();
        });
        
        // Configurar eventos para arrastrar y soltar PDF
        const dropzone = document.getElementById('dropzone-pdf');
        
        if (dropzone) {
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });
            
            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });
            
            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type === 'application/pdf') {
                        document.getElementById('archivo_pdf').files = files;
                        manejarCambioPDF({ target: { files: files } });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Solo se permiten archivos PDF'
                        });
                    }
                }
            });
        }
        
        // Configurar eventos para las pestañas
        $('#archivoTabs button').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr("id");
            
            if (target === 'documento-tab') {
                currentTab = 'documento';
                $("#es_pdf_subido").val(0);
            } else if (target === 'pdf-tab') {
                currentTab = 'pdf';
                $("#es_pdf_subido").val(1);
            }
        });
        
        // Cargar archivos
        cargarArchivos();
        
        // Cargar Quill si no está cargado
        cargarQuillSiNoExiste();
    });
    function destruirEditor() {
        if (archivoEditor) {
            try {
                archivoEditor.off('text-change');

                archivoEditor.container.innerHTML = '';

                const toolbarElement = document.querySelector('.ql-toolbar');
                 if (toolbarElement && toolbarElement.parentNode) {
                toolbarElement.parentNode.removeChild(toolbarElement);
            }
            $('#editor-container-archivo').html('');
            // establecer la variable a null
            archivoEditor = null;
            } catch (error) {
                console.error("Error al destruir el editor:", error);
            }
        }
    }
    
    // Función para mostrar la vista de lista de archivos
    function mostrarVistaListaArchivos() {
        $(".vista").removeClass("active");
        $("#vista-lista-archivos").addClass("active");
        
        // Actualizar estado de los botones
        $("#btn-lista-archivos").removeClass("btn-outline-danger").addClass("btn-rojo");
        $("#btn-nuevo-archivo").removeClass("btn-rojo").addClass("btn-outline-danger");
        $("#btn-editar-plantilla-archivo").addClass("btn-outline-danger").removeClass("btn-rojo");
        
       destruirEditor();
        
        // Recargar la lista de archivos
        cargarArchivos();
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
            quillScript.onload = function() {
                console.log("Quill cargado correctamente");
                quillLoaded = true;
            };
            quillScript.onerror = function() {
                console.error("Error al cargar Quill");
            };
            document.head.appendChild(quillScript);
        } else {
            quillLoaded = true;
        }
    }

    // Función para cargar los archivos
    function cargarArchivos() {
        console.log("Cargando archivos internos...");
        
        // Mostrar indicador de carga
        $("#lista-archivos-container").html(`
            <div class="text-center py-5">
                <div class="spinner-border text-rojo" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando archivos internos...</p>
            </div>
        `);

        // Construir la URL con los filtros
        let url = _URL + "/ajs/archivo-interno/render";
        if (filtroActual && tipoFiltroActual !== 'todos') {
            url += `?filtro=${encodeURIComponent(filtroActual)}&tipo_busqueda=${tipoFiltroActual}`;
        }

        console.log("URL de carga:", url);

        // Realizar petición AJAX para obtener los archivos
        $.ajax({
            url: url,
            method: "GET",
            dataType: 'json',
            success: function (data) {
                console.log("Respuesta de archivos internos:", data);
                
                // Asegurarse de que data sea un array
                if (data === null || data === undefined) {
                    console.log("No se recibieron datos, mostrando mensaje de no hay archivos");
                    mostrarNoHayArchivos();
                    return;
                }
                
                archivos = Array.isArray(data) ? data : [];
                console.log("Archivos procesados:", archivos);
                renderizarArchivos();
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar archivos:", status, error);
                $("#lista-archivos-container").html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar los archivos. Por favor, intente nuevamente.
                    </div>
                    <button class="btn btn-rojo mt-3" onclick="window.recargarArchivos()">
                        <i class="fas fa-sync me-2"></i>Reintentar
                    </button>
                `);
            }
        });
    }
    
    // Función para mostrar mensaje de no hay archivos
    function mostrarNoHayArchivos() {
        $("#lista-archivos-container").html(`
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                No se encontraron archivos internos.
            </div>
            <button class="btn btn-rojo mt-3" id="btn-crear-primer-archivo">
                <i class="fas fa-plus me-2"></i>Crear primer archivo
            </button>
        `);
        
        // Agregar evento al botón de crear primer archivo
        $("#btn-crear-primer-archivo").on("click", function() {
            mostrarFormularioNuevoArchivo();
        });
    }

    // Función para renderizar los archivos
    function renderizarArchivos() {
        if (!archivos || archivos.length === 0) {
            mostrarNoHayArchivos();
            return;
        }

        let html = '<div class="row row-cols-1 row-cols-md-3 g-4">';

        archivos.forEach(function (archivo) {
            const fecha = new Date(archivo.fecha_creacion).toLocaleDateString();
            const cliente = archivo.cliente_nombre || 'Sin cliente';
            const esPdf = parseInt(archivo.es_pdf_subido) === 1;
            const iconoTipo = esPdf ? 'fa-file-pdf' : 'fa-file-word';
            const colorTipo = esPdf ? 'text-danger' : 'text-primary';

            html += `
                <div class="col">
                    <div class="card archivo-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-rojo">${archivo.tipo || 'Sin tipo'}</span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-dark" type="button" id="dropdownArchivo${archivo.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownArchivo${archivo.id}">
                                    <li><a class="dropdown-item" href="${_URL}/ajs/archivo-interno/generarPDF?id=${archivo.id}" target="_blank">
                                        <i class="fas fa-file-pdf me-2"></i> Ver PDF
                                    </a></li>
                                    <li><a class="dropdown-item archivo-editar" href="#" data-id="${archivo.id}">
                                        <i class="fas fa-edit me-2"></i> Editar
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmarEliminarArchivoModal" data-id="${archivo.id}">
                                        <i class="fas fa-trash-alt me-2"></i> Eliminar
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas ${iconoTipo} ${colorTipo} fa-2x me-3"></i>
                                <h5 class="card-title mb-0">${archivo.titulo}</h5>
                            </div>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> ${cliente}<br>
                                    <i class="fas fa-calendar-alt me-1"></i> ${fecha}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="${_URL}/ajs/archivo-interno/generarPDF?id=${archivo.id}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Ver PDF
                            </a>
                            <button class="btn btn-sm btn-rojo archivo-editar" data-id="${archivo.id}">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        
        $("#lista-archivos-container").html(html);
        
        // Agregar eventos a los botones de editar
        $(".archivo-editar").on("click", function() {
            const id = $(this).data('id');
            editarArchivo(id);
        });
    }

    // Función para buscar archivos
    function buscarArchivos() {
        const busqueda = $("#buscar-archivo").val().trim().toLowerCase();

        if (busqueda === "") {
            // Si la búsqueda está vacía, mostrar todos los archivos según el filtro de tipo
            if (tipoFiltroActual === "todos") {
                filtroActual = "";
            }
        } else {
            // Si hay texto de búsqueda, filtrar por título
            filtroActual = busqueda;
            tipoFiltroActual = "titulo";
        }

        // Recargar los archivos con el filtro
        cargarArchivos();
    }

    // Función para mostrar el formulario de nuevo archivo
    function mostrarFormularioNuevoArchivo() {
        console.log("Mostrando formulario de nuevo archivo...");
        
        // Actualizar estado de los botones
        $("#btn-lista-archivos").removeClass("btn-rojo").addClass("btn-outline-danger");
        $("#btn-nuevo-archivo").removeClass("btn-outline-danger").addClass("btn-rojo");
        $("#btn-editar-plantilla-archivo").addClass("btn-outline-danger").removeClass("btn-rojo");
        
        // Mostrar la vista de edición
        $(".vista").removeClass("active");
        $("#vista-editar-archivo").addClass("active");
        
        // Limpiar el formulario
        $("#id_archivo").val("");
        $("#titulo_archivo").val("");
        $("#tipo_archivo").val("");
        $("#id_cliente").val("");
        $("#header_image_data").val("");
        $("#footer_image_data").val("");
        $("#archivo_pdf_data").val("");
        $("#es_pdf_subido").val("0");
        
        // Ocultar las imágenes de vista previa
        $("#header-preview-archivo").hide();
        $("#footer-preview-archivo").hide();
        $("#header-placeholder-archivo").show();
        $("#footer-placeholder-archivo").show();
        
        // Restablecer el PDF
        restablecerPDF();
        
        // Mostrar la pestaña de documento por defecto
        $('#documento-tab').tab('show');
        currentTab = 'documento';
        
        // Actualizar título
        $("#titulo-pagina-archivo").text("Nuevo Archivo Interno");
        
        // Cargar clientes
        inicializarAutocompletarClientes();
        
        // Esperar a que Quill esté cargado
        esperarPorQuill(function() {
            // Inicializar el editor
            inicializarEditorArchivo();
            
            // Cargar plantilla actual
            cargarPlantillaArchivo();
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
            var checkQuill = setInterval(function() {
                if (typeof Quill !== 'undefined') {
                    clearInterval(checkQuill);
                    quillLoaded = true;
                    console.log("Quill ya está disponible, continuando...");
                    callback();
                }
            }, 100);
            
            // Establecer un tiempo límite de 5 segundos
            setTimeout(function() {
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

    // Función para editar un archivo existente
    function editarArchivo(id) {
        console.log("Editando archivo ID:", id);
        
        // Actualizar estado de los botones
        $("#btn-lista-archivos").removeClass("btn-rojo").addClass("btn-outline-danger");
        $("#btn-nuevo-archivo").removeClass("btn-rojo").addClass("btn-outline-danger");
        $("#btn-editar-plantilla-archivo").addClass("btn-outline-danger").removeClass("btn-rojo");
        
        // Mostrar la vista de edición
        $(".vista").removeClass("active");
        $("#vista-editar-archivo").addClass("active");
        
        // Actualizar título
        $("#titulo-pagina-archivo").text("Editar Archivo Interno");
        
        // Cargar clientes
        inicializarAutocompletarClientes();
        
        // Esperar a que Quill esté cargado
        esperarPorQuill(function() {
            // Inicializar el editor
            inicializarEditorArchivo();
            
            // Cargar datos del archivo
            cargarDatosArchivo(id);
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

    // Función para editar la plantilla de archivo
    function editarPlantillaArchivo() {
        console.log("Editando plantilla de archivo...");
            destruirEditorPlantilla();
        // Actualizar estado de los botones
        $("#btn-lista-archivos").removeClass("btn-rojo").addClass("btn-outline-danger");
        $("#btn-nuevo-archivo").removeClass("btn-rojo").addClass("btn-outline-danger");
        $("#btn-editar-plantilla-archivo").removeClass("btn-outline-danger").addClass("btn-rojo");
        
        // Esperar a que Quill esté cargado
        esperarPorQuill(function() {
            // Inicializar el editor de plantilla
            inicializarEditorPlantilla();
            
            // Cargar datos de la plantilla
            cargarDatosPlantilla();
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('editarPlantillaArchivoModal'));
            modal.show();
        });
    }

    // Función para inicializar el editor Quill
    function inicializarEditorArchivo() {
        console.log("Inicializando editor Quill...");
        
        // Verificar que el contenedor exista
        if ($("#editor-container-archivo").length === 0) {
            console.error("Error: No se encontró el contenedor del editor #editor-container-archivo");
            return;
        }
        destruirEditor();
        
        try {
            // Inicializar Quill
            archivoEditor = new Quill('#editor-container-archivo', {
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
                placeholder: 'Escriba el contenido del documento...',
                theme: 'snow'
            });
            
            console.log("Editor Quill inicializado correctamente");
            
            // Asignar el evento de cambio de texto solo si el editor se inicializó correctamente
            if (archivoEditor && archivoEditor.on) {
                archivoEditor.on('text-change', function() {
                    var contenidoInput = document.getElementById('contenido_archivo');
                    if (contenidoInput) {
                        contenidoInput.value = archivoEditor.root.innerHTML;
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
        
        // Verificar que el contenedor exista
        if ($("#editor-container-plantilla").length === 0) {
            console.error("Error: No se encontró el contenedor del editor #editor-container-plantilla");
            return;
        }
         // Destruir el editor existente si hay uno
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
                templateEditor.on('text-change', function() {
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

    // Función para cargar clientes
   // Función para inicializar el autocomplete de clientes
function inicializarAutocompletarClientes() {
    $("#cliente_search").autocomplete({
        source: _URL + "/ajs/buscar/cliente/datos", // Usamos la ruta existente
        minLength: 2,
        select: function (event, ui) {
            event.preventDefault();

            // Establecer los valores seleccionados
            $("#id_cliente").val(ui.item.codigo); // Usamos 'codigo' que es el campo que devuelve la API
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
            $("#id_cliente").val("");
            $("#cliente_info").hide();
        } else {
            // Si tiene texto, iniciar búsqueda
            $("#cliente_search").autocomplete("search", $("#cliente_search").val());
        }
    });
}

    // Función para cargar la plantilla de archivo
    function cargarPlantillaArchivo() {
        $.ajax({
            url: _URL + "/ajs/archivo-interno/obtener-template",
            method: "GET",
            dataType: 'json',
            success: function (data) {
                if (data.success && data.data) {
                    plantillaActual = data.data;
                    
                    // Establecer contenido predeterminado basado en la plantilla
                    if (archivoEditor) {
                        archivoEditor.root.innerHTML = plantillaActual.contenido;
                        document.getElementById('contenido_archivo').value = plantillaActual.contenido;
                    }
                    
                    // Mostrar imágenes de la plantilla en las vistas previas
                    if (plantillaActual.header_image_url) {
                        document.getElementById('header-preview-archivo').src = plantillaActual.header_image_url;
                        document.getElementById('header-preview-archivo').style.display = 'block';
                        document.getElementById('header-placeholder-archivo').style.display = 'none';
                    }
                    
                    if (plantillaActual.footer_image_url) {
                        document.getElementById('footer-preview-archivo').src = plantillaActual.footer_image_url;
                        document.getElementById('footer-preview-archivo').style.display = 'block';
                        document.getElementById('footer-placeholder-archivo').style.display = 'none';
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar la plantilla:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar la plantilla de archivo'
                });
            }
        });
    }

    // Función para cargar datos de la plantilla
    function cargarDatosPlantilla() {
        $.ajax({
            url: _URL + "/ajs/archivo-interno/obtener-template",
            method: "GET",
            dataType: 'json',
            success: function (data) {
                if (data.success && data.data) {
                    plantillaActual = data.data;
                    
                    // Llenar formulario
                    document.getElementById('id_plantilla_archivo').value = plantillaActual.id;
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
            
            reader.onload = function(e) {
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

    // Función para manejar el cambio de PDF
    function manejarCambioPDF(event) {
        const file = event.target.files[0];

        if (file) {
            if (file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Solo se permiten archivos PDF'
                });
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) { // 10MB
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El archivo es demasiado grande. El tamaño máximo es 10MB.'
                });
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('archivo_pdf_data').value = e.target.result;
                document.getElementById('pdf-preview').src = e.target.result;
                document.getElementById('pdf-filename').textContent = file.name;
                document.getElementById('pdf-preview-container').style.display = 'block';
                document.getElementById('dropzone-pdf').style.display = 'none';
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

    // Función para restablecer PDF
    function restablecerPDF() {
        document.getElementById('archivo_pdf').value = '';
        document.getElementById('archivo_pdf_data').value = '';
        document.getElementById('pdf-preview-container').style.display = 'none';
        document.getElementById('dropzone-pdf').style.display = 'block';
    }

    // Función para cargar datos de un archivo
    function cargarDatosArchivo(id) {
        const formData = new FormData();
        formData.append('id', id);

        $.ajax({
            url: _URL + "/ajs/archivo-interno/getOne",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                if (data.success && data.data) {
                    archivoActual = data.data;
                    
                    // Llenar formulario
                    document.getElementById('id_archivo').value = archivoActual.id;
                    document.getElementById('id_cliente').value = archivoActual.id_cliente || '';
                    if (archivoActual.id_cliente && archivoActual.cliente_nombre) {
                        $('#cliente_search').val(archivoActual.cliente_nombre);
                        $('#cliente_nombre').text(archivoActual.cliente_nombre);
                        $('#cliente_documento').text("Documento: " + (archivoActual.cliente_documento || ""));
                        $('#cliente_direccion').text("Dirección: " + (archivoActual.cliente_direccion || "No especificada"));
                        $('#cliente_info').show();
                    }
                    document.getElementById('tipo_archivo').value = archivoActual.tipo || '';
                    document.getElementById('titulo_archivo').value = archivoActual.titulo;
                    document.getElementById('header_image_data').value = archivoActual.header_image || '';
                    document.getElementById('footer_image_data').value = archivoActual.footer_image || '';
                    document.getElementById('archivo_pdf_data').value = archivoActual.archivo_pdf || '';
                    document.getElementById('es_pdf_subido').value = archivoActual.es_pdf_subido || '0';
                    
                    // Mostrar imágenes si existen
                    if (archivoActual.header_image_url) {
                        document.getElementById('header-preview-archivo').src = archivoActual.header_image_url;
                        document.getElementById('header-preview-archivo').style.display = 'block';
                        document.getElementById('header-placeholder-archivo').style.display = 'none';
                    }
                    
                    if (archivoActual.footer_image_url) {
                        document.getElementById('footer-preview-archivo').src = archivoActual.footer_image_url;
                        document.getElementById('footer-preview-archivo').style.display = 'block';
                        document.getElementById('footer-placeholder-archivo').style.display = 'none';
                    }
                    
                    // Determinar qué pestaña mostrar según el tipo de archivo
                    if (parseInt(archivoActual.es_pdf_subido) === 1) {
                        // Es un PDF subido
                        $('#pdf-tab').tab('show');
                        currentTab = 'pdf';
                        
                        // Mostrar el PDF
                        if (archivoActual.archivo_pdf) {
                            document.getElementById('pdf-preview').src = archivoActual.archivo_pdf;
                            document.getElementById('pdf-filename').textContent = archivoActual.titulo + '.pdf';
                            document.getElementById('pdf-preview-container').style.display = 'block';
                            document.getElementById('dropzone-pdf').style.display = 'none';
                        }
                    } else {
                        // Es un documento creado
                        $('#documento-tab').tab('show');
                        currentTab = 'documento';
                        
                        // Establecer contenido en el editor
                        if (archivoEditor) {
                            archivoEditor.root.innerHTML = archivoActual.contenido;
                            document.getElementById('contenido_archivo').value = archivoActual.contenido;
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Error al cargar el archivo'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar el archivo'
                });
            }
        });
    }

    // Función para guardar archivo
    function guardarArchivo() {
        // Validar formulario
        const titulo = document.getElementById('titulo_archivo').value.trim();
        const tipo = document.getElementById('tipo_archivo').value.trim();
        const esPdfSubido = document.getElementById('es_pdf_subido').value === '1';

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

        // Validar según el tipo de archivo
        if (esPdfSubido) {
            // Si es un PDF subido
            if (!document.getElementById('archivo_pdf_data').value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe subir un archivo PDF'
                });
                return;
            }
        } else {
            // Si es un documento creado
            const contenido = document.getElementById('contenido_archivo').value.trim();
            if (!contenido) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El contenido es obligatorio para documentos creados'
                });
                return;
            }
        }

        // Recopilar datos del formulario
        const formData = new FormData(document.getElementById('formArchivo'));

        // Determinar si es inserción o edición
        const archivoId = document.getElementById('id_archivo').value;
        let url = _URL + '/ajs/archivo-interno/insertar';

        if (archivoId) {
            url = _URL + '/ajs/archivo-interno/editar';
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Guardando',
            text: 'Guardando archivo interno...',
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
                        // Volver a la lista de archivos
                        mostrarVistaListaArchivos();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.msg || 'Error al guardar el archivo'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar el archivo'
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
        const formData = new FormData(document.getElementById('formPlantillaArchivo'));

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
            url: _URL + "/ajs/archivo-interno/guardar-template",
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editarPlantillaArchivoModal'));
                        modal.hide();
                        
                        // Actualizar estado de los botones
                        $("#btn-lista-archivos").addClass("btn-rojo").removeClass("btn-outline-danger");
                        $("#btn-nuevo-archivo").removeClass("btn-rojo").addClass("btn-outline-danger");
                        $("#btn-editar-plantilla-archivo").removeClass("btn-rojo").addClass("btn-outline-danger");
                        
                        // Recargar archivos
                        mostrarVistaListaArchivos();
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

    // Función para mostrar la vista previa de un archivo
    function mostrarVistaPrevia() {
        // Determinar el tipo de archivo
        const esPdfSubido = document.getElementById('es_pdf_subido').value === '1';
        
        if (esPdfSubido) {
            // Si es un PDF subido, mostrar directamente
            const pdfData = document.getElementById('archivo_pdf_data').value;
            if (!pdfData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe subir un archivo PDF para la vista previa'
                });
                return;
            }
            
            // Mostrar el PDF en el iframe
            document.getElementById('preview-frame-archivo').src = pdfData;
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('previewArchivoModal'));
            modal.show();
            
            // Configurar el botón de descarga
            document.getElementById('btn-download-pdf').onclick = function() {
                const blob = b64toBlob(pdfData.replace('data:application/pdf;base64,', ''), 'application/pdf');
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = document.getElementById('titulo_archivo').value.trim() + '.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            };
            
            return;
        }
        
        // Si es un documento creado
        const contenido = document.getElementById('contenido_archivo').value.trim();

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
        formData.append('titulo', document.getElementById('titulo_archivo').value.trim());
        formData.append('contenido', contenido);
        formData.append('header_image', document.getElementById('header_image_data').value);
        formData.append('footer_image', document.getElementById('footer_image_data').value);
        formData.append('es_pdf_subido', '0');

        // Enviar solicitud para generar vista previa
        $.ajax({
            url: _URL + "/ajs/archivo-interno/vista-previa",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                Swal.close();
                
                if (data.success && data.pdfBase64) {
                    // Mostrar la vista previa en el iframe
                    document.getElementById('preview-frame-archivo').src = "data:application/pdf;base64," + data.pdfBase64;
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('previewArchivoModal'));
                    modal.show();
                    
                    // Configurar el botón de descarga
                    document.getElementById('btn-download-pdf').onclick = function() {
                        const blob = b64toBlob(data.pdfBase64, 'application/pdf');
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = document.getElementById('titulo_archivo').value.trim() + '.pdf';
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

    // Función para eliminar un archivo
    function eliminarArchivo(id) {
        // Mostrar indicador de carga
        $("#btn-confirmar-eliminar-archivo").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...').prop('disabled', true);
        
        const formData = new FormData();
        formData.append('id', id);
        
        $.ajax({
            url: _URL + "/ajs/archivo-interno/borrar",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                // Cerrar el modal
                $('#confirmarEliminarArchivoModal').modal('hide');
                
                if (data.res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.msg
                    }).then(() => {
                        cargarArchivos();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.msg || 'Error al eliminar el archivo'
                    });
                }
                
                // Restaurar el botón
                $("#btn-confirmar-eliminar-archivo").html('Eliminar').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
                
                // Cerrar el modal
                $('#confirmarEliminarArchivoModal').modal('hide');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar el archivo'
                });
                
                // Restaurar el botón
                $("#btn-confirmar-eliminar-archivo").html('Eliminar').prop('disabled', false);
            }
        });
    }
    
    // Exponer algunas funciones al ámbito global para poder llamarlas desde HTML
    window.recargarArchivos = cargarArchivos;
    window.editarArchivo = editarArchivo;
    window.eliminarArchivo = eliminarArchivo;
    window.editarPlantillaArchivo = editarPlantillaArchivo;
    window.mostrarFormularioNuevoArchivo = mostrarFormularioNuevoArchivo;
    window.mostrarVistaListaArchivos = mostrarVistaListaArchivos;
})();
</script>

<Actions>
  <Action name="Agregar filtros por tipo de documento" description="Implementar filtros para buscar archivos por tipo (MEMO, INFORME, etc.)" />
  <Action name="Mejorar el editor de texto" description="Añadir más opciones de formato y estilos al editor de documentos" />
  <Action name="Implementar versiones de documentos" description="Añadir sistema para guardar y comparar versiones anteriores de los documentos" />
  <Action name="Añadir firmas digitales" description="Permitir añadir firmas digitales a los documentos" />
  <Action name="Implementar sistema de comentarios" description="Añadir funcionalidad para comentar y revisar documentos" />
</Actions>

