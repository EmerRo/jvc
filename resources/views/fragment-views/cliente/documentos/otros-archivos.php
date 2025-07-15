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
    <button class="btn bg-rojo text-white" id="btn-nuevo-archivo-interno">
        <i class="fas fa-plus me-2"></i>Nuevo Archivo Interno
    </button>
    <button class="btn border-rojo" id="btn-editar-plantilla">
        <i class="fas fa-edit me-2"></i>Editar Plantilla
    </button>
    <button class="btn border-rojo" id="btn-gestionar-membretes">
        <i class="fas fa-image me-2"></i>Gestionar Membretes
    </button>
</div>

<!-- Vista de lista de archivos internos -->
<div id="vista-lista-archivos-internos" class="vista active">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Archivos Internos</h3>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control border-rojo" id="buscar-archivo-interno" placeholder="Buscar archivos internos...">
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
<div id="vista-editar-archivo-interno" class="vista">
    <div class="form-header">
        <h3 id="titulo-pagina-archivo-interno" class="m-0">Nuevo Archivo Interno</h3>
        <p class="m-0">Complete la información del archivo interno</p>
    </div>

    <form id="formArchivoInterno" enctype="multipart/form-data">
        <input type="hidden" id="id_archivo_interno" name="id">
        <input type="hidden" id="contenido_archivo_interno" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">

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
                        <button class="btn bg-rojo text-white" type="button" id="btn-gestionar-tipos-archivo-interno"
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
            <label for="editor-container-archivo-interno" class="form-label">Contenido del Archivo Interno</label>
            <div id="editor-container-archivo-interno" class="editor-container"></div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" id="btn-cancel-archivo-interno">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn border-rojo" id="btn-preview-archivo-interno">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-archivo-interno">
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
                                <label for="nuevo-tipo-archivo-interno-nombre" class="form-label">Nombre del Tipo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuevo-tipo-archivo-interno-nombre"
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
                                <tbody id="lista-tipos-archivo-interno">
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
                <input type="hidden" id="editar-tipo-archivo-interno-id">
                <div class="mb-3">
                    <label for="editar-tipo-archivo-interno-nombre" class="form-label">Nombre del Tipo <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editar-tipo-archivo-interno-nombre">
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
                <iframe id="preview-frame-archivo-interno" style="width: 100%; height: 600px; border: none;"></iframe>
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
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-archivo-interno">Eliminar</button>
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
        tipo: 'archivo-interno',
        urls: {
            render: _URL + "/ajs/archivo-interno/render",
            insertar: _URL + "/ajs/archivo-interno/insertar",
            editar: _URL + "/ajs/archivo-interno/editar",
            borrar: _URL + "/ajs/archivo-interno/borrar",
            getOne: _URL + "/ajs/archivo-interno/getOne",
            generarPDF: _URL + "/ajs/archivo-interno/generarPDF",
            vistaPrevia: _URL + "/ajs/archivo-interno/vista-previa",
            obtenerTemplate: _URL + "/ajs/archivo-interno/obtener-template",
            guardarTemplate: _URL + "/ajs/archivo-interno/guardar-template",
            obtenerMembretes: _URL + "/ajs/archivo-interno/obtener-membretes",
            guardarMembretes: _URL + "/ajs/archivo-interno/guardar-membretes",
            obtenerTipos: _URL + "/ajs/archivo-interno/obtener-tipos-archivos"
        },
        elementos: {
            // Botones principales
            btnLista: "#btn-lista-archivos-internos",
            btnNuevo: "#btn-nuevo-archivo-interno",
            btnEditarPlantilla: "#btn-editar-plantilla",
            btnGestionarMembretes: "#btn-gestionar-membretes",
            
            // Vistas
            vistaLista: "#vista-lista-archivos-internos",
            vistaFormulario: "#vista-editar-archivo-interno",
            contenedorLista: "#lista-archivos-internos-container",
            
            // Formulario principal
            formulario: "#formArchivoInterno",
            idDocumento: "#id_archivo_interno",
            tituloDocumento: "#titulo_archivo_interno",
            tipoDocumento: "#tipo_archivo_interno",
            contenidoDocumento: "#contenido_archivo_interno",
            clienteId: "#cliente_id",
            tituloPagina: "#titulo-pagina-archivo-interno",
            
            // Editor
            editorPrincipal: "#editor-container-archivo-interno",
            
            // Búsqueda
            inputBuscar: "#buscar-archivo-interno",
            
            // Botones de formulario
            btnCancelar: "#btn-cancel-archivo-interno",
            btnGuardar: "#btn-save-archivo-interno",
            btnPreview: "#btn-preview-archivo-interno",
            
            // Modales
            modalEliminar: "#confirmarEliminarArchivoInternoModal",
            btnConfirmarEliminar: "#btn-confirmar-eliminar-archivo-interno",
            modalPreview: "#previewArchivoInternoModal",
            previewFrame: "#preview-frame-archivo-interno",
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
            url: _URL + "/ajs/archivo-interno/obtener-tipos-archivos",
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
                    $("#lista-tipos-archivo-interno").html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar tipos:", error);
            }
        });
    }

    function agregarTipoArchivoInterno() {
        const nombre = $("#nuevo-tipo-archivo-interno-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/archivo-interno/insertar-tipo-archivo",
            method: "POST",
            data: { nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#nuevo-tipo-archivo-interno-nombre").val('');
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
        $("#editar-tipo-archivo-interno-id").val(id);
        $("#editar-tipo-archivo-interno-nombre").val(nombre);
        $("#editarTipoArchivoInternoModal").modal('show');
    }

    function guardarTipoArchivoInternoEditado() {
        const id = $("#editar-tipo-archivo-interno-id").val();
        const nombre = $("#editar-tipo-archivo-interno-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/archivo-interno/editar-tipo-archivo",
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
                    url: _URL + "/ajs/archivo-interno/eliminar-tipo-archivo",
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
</script>