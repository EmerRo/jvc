<!-- resources/views/fragment-views/cliente/documentos/componentes/otros-archivos.php -->
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
    <button class="btn border-rojo" id="btn-lista-otros-archivos">
        <i class="fas fa-list me-2"></i>Lista de Otros Archivos
    </button>
    <button class="btn bg-rojo text-white" id="btn-nuevo-otro-archivo">
        <i class="fas fa-plus me-2"></i>Nuevo Otro Archivo
    </button>
    <button class="btn border-rojo" id="btn-editar-plantilla">
        <i class="fas fa-edit me-2"></i>Editar Plantilla
    </button>
    <button class="btn border-rojo" id="btn-gestionar-membretes">
        <i class="fas fa-image me-2"></i>Gestionar Membretes
    </button>
</div>

<!-- Vista de lista de otros archivos -->
<div id="vista-lista-otros-archivos" class="vista active">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Otros Archivos</h3>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control border-rojo" id="buscar-otro-archivo" placeholder="Buscar otros archivos...">
            <button class="btn bg-rojo text-white" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div id="lista-otros-archivos-container">
        <!-- Aquí se cargarán dinámicamente los otros archivos -->
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando otros archivos...</p>
        </div>
    </div>
</div>

<!-- Vista de formulario de nuevo/editar otro archivo -->
<div id="vista-editar-otro-archivo" class="vista">
    <div class="form-header">
        <h3 id="titulo-pagina-otro-archivo" class="m-0">Nuevo Otro Archivo</h3>
        <p class="m-0">Complete la información del otro archivo</p>
    </div>

    <form id="formOtroArchivo" enctype="multipart/form-data">
        <input type="hidden" id="id_otro_archivo" name="id">
        <input type="hidden" id="contenido_otro_archivo" name="contenido">
        <input type="hidden" id="header_image_data" name="header_image">
        <input type="hidden" id="footer_image_data" name="footer_image">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="titulo_otro_archivo" class="form-label">Título del Otro Archivo</label>
                    <input type="text" class="form-control" id="titulo_otro_archivo" name="titulo" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_otro_archivo" class="form-label">Tipo de Otro Archivo</label>
                    <div class="input-group">
                        <select class="form-select" id="tipo_otro_archivo" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                        </select>
                        <button class="btn bg-rojo text-white" type="button" id="btn-gestionar-tipos-otro-archivo"
                            onclick="abrirModalTiposOtrosArchivos()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="form-text text-gris small">Este campo se usará para categorizar los otros archivos.</div>
                </div>

                <div class="mb-3">
                    <label for="motivo_otro_archivo" class="form-label">Motivo o Descripción</label>
                    <textarea class="form-control" id="motivo_otro_archivo" name="motivo" rows="3"
                        placeholder="Describe el motivo o propósito de este archivo..."></textarea>
                    <div class="form-text text-gris small">Campo opcional para describir el propósito del archivo.</div>
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
            <label for="editor-container-otro-archivo" class="form-label">Contenido del Otro Archivo</label>
            <div id="editor-container-otro-archivo" class="editor-container"></div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" id="btn-cancel-otro-archivo">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn border-rojo" id="btn-preview-otro-archivo">
                <i class="fas fa-eye me-1"></i> Vista Previa
            </button>
            <button type="button" class="btn btn-rojo" id="btn-save-otro-archivo">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </form>
</div>

<!-- Modal para Gestionar Tipos de Otro Archivo -->
<div class="modal fade" id="gestionarTiposOtroArchivoModal" tabindex="-1"
    aria-labelledby="gestionarTiposOtroArchivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="gestionarTiposOtroArchivoModalLabel">Gestionar Tipos de Otro Archivo</h5>
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
                                <label for="nuevo-tipo-otro-archivo-nombre" class="form-label">Nombre del Tipo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuevo-tipo-otro-archivo-nombre"
                                    placeholder="Ej: COMERCIAL, FORMAL, NOTIFICACIÓN">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn bg-rojo text-white w-100"
                                    onclick="agregarTipoOtroArchivo()">
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
                                <tbody id="lista-tipos-otro-archivo">
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
<div class="modal fade" id="editarTipoOtroArchivoModal" tabindex="-1" aria-labelledby="editarTipoOtroArchivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarTipoOtroArchivoModalLabel">Editar Tipo de Otro Archivo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar-tipo-otro-archivo-id">
                <div class="mb-3">
                    <label for="editar-tipo-otro-archivo-nombre" class="form-label">Nombre del Tipo <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editar-tipo-otro-archivo-nombre">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" onclick="guardarTipoOtroArchivoEditado()">Guardar
                    Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewOtroArchivoModal" tabindex="-1" aria-labelledby="previewOtroArchivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewOtroArchivoModalLabel">Vista Previa del Otro Archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame-otro-archivo" style="width: 100%; height: 600px; border: none;"></iframe>
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
<div class="modal fade" id="confirmarEliminarOtroArchivoModal" tabindex="-1"
    aria-labelledby="confirmarEliminarOtroArchivoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarOtroArchivoModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este otro archivo? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar-otro-archivo">Eliminar</button>
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
                    los otros archivos y plantillas.
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
<div class="modal fade" id="editarPlantillaOtroArchivoModal" tabindex="-1"
    aria-labelledby="editarPlantillaOtroArchivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="editarPlantillaOtroArchivoModalLabel">Editar Plantilla de Otro Archivo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPlantillaOtroArchivo" enctype="multipart/form-data">
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
    // Configuración específica para otros archivos
    const otrosArchivosConfig = {
        tipo: 'otro-archivo',
        urls: {
            render: _URL + "/ajs/otro-archivo/render",
            insertar: _URL + "/ajs/otro-archivo/insertar",
            editar: _URL + "/ajs/otro-archivo/editar",
            borrar: _URL + "/ajs/otro-archivo/borrar",
            getOne: _URL + "/ajs/otro-archivo/getOne",
            generarPDF: _URL + "/ajs/otro-archivo/generarPDF",
            vistaPrevia: _URL + "/ajs/otro-archivo/vista-previa",
            obtenerTemplate: _URL + "/ajs/otro-archivo/obtener-template",
            guardarTemplate: _URL + "/ajs/otro-archivo/guardar-template",
            obtenerMembretes: _URL + "/ajs/otro-archivo/obtener-membretes",
            guardarMembretes: _URL + "/ajs/otro-archivo/guardar-membretes",
            obtenerTipos: _URL + "/ajs/otro-archivo/obtener-tipos-archivos"
        },
        elementos: {
            // Botones principales
            btnLista: "#btn-lista-otros-archivos",
            btnNuevo: "#btn-nuevo-otro-archivo",
            btnEditarPlantilla: "#btn-editar-plantilla",
            btnGestionarMembretes: "#btn-gestionar-membretes",
            
            // Vistas
            vistaLista: "#vista-lista-otros-archivos",
            vistaFormulario: "#vista-editar-otro-archivo",
            contenedorLista: "#lista-otros-archivos-container",
            
            // Formulario principal
            formulario: "#formOtroArchivo",
            idDocumento: "#id_otro_archivo",
            tituloDocumento: "#titulo_otro_archivo",
            tipoDocumento: "#tipo_otro_archivo",
            contenidoDocumento: "#contenido_otro_archivo",
            clienteId: "#cliente_id",
            tituloPagina: "#titulo-pagina-otro-archivo",
            motivoDocumento: "#motivo_otro_archivo",
            
            // Editor
            editorPrincipal: "#editor-container-otro-archivo",
            
            // Búsqueda
            inputBuscar: "#buscar-otro-archivo",
            
            // Botones de formulario
            btnCancelar: "#btn-cancel-otro-archivo",
            btnGuardar: "#btn-save-otro-archivo",
            btnPreview: "#btn-preview-otro-archivo",
            
            // Modales
            modalEliminar: "#confirmarEliminarOtroArchivoModal",
            btnConfirmarEliminar: "#btn-confirmar-eliminar-otro-archivo",
            modalPreview: "#previewOtroArchivoModal",
            previewFrame: "#preview-frame-otro-archivo",
            btnDownloadPdf: "#btn-download-pdf",
            
            // Plantilla
            modalPlantilla: "#editarPlantillaOtroArchivoModal",
            editorPlantilla: "#editor-container-plantilla",
            formularioPlantilla: "#formPlantillaOtroArchivo",
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

    // Inicializar el módulo de otros archivos
    let otrosArchivosUtils;

    $(document).ready(function() {
        otrosArchivosUtils = new DocumentosUtils(otrosArchivosConfig);
        
        // Exponer funciones globales para compatibilidad
        window.recargarOtrosArchivos = () => otrosArchivosUtils.cargarDocumentos();
        window.editarOtroArchivo = (id) => otrosArchivosUtils.editarDocumento(id);
        window.eliminarOtroArchivo = (id) => otrosArchivosUtils.eliminarDocumento(id);
        window.editarPlantillaOtroArchivo = () => otrosArchivosUtils.editarPlantilla();
        window.mostrarFormularioNuevoOtroArchivo = () => otrosArchivosUtils.mostrarFormularioNuevo();
        window.mostrarVistaListaOtrosArchivos = () => otrosArchivosUtils.mostrarVistaLista();
        window.mostrarVistaPreviewPlantilla = () => otrosArchivosUtils.mostrarVistaPreviewPlantilla();
        window.mostrarVistaPreviewMembretes = () => otrosArchivosUtils.mostrarVistaPreviewMembretes();
        window.gestionarMembretes = () => otrosArchivosUtils.gestionarMembretes();
    });

    // Funciones específicas para tipos de otros archivos
    function abrirModalTiposOtrosArchivos() {
        cargarTiposOtrosArchivosModal();
        $('#gestionarTiposOtroArchivoModal').modal('show');
    }

    function cargarTiposOtrosArchivosModal() {
        $.ajax({
            url: _URL + "/ajs/otro-archivo/obtener-tipos-archivos",
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
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarTipoOtroArchivo(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarTipoOtroArchivo(${tipo.id}, '${tipo.nombre}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $("#lista-tipos-otro-archivo").html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar tipos:", error);
            }
        });
    }

    function agregarTipoOtroArchivo() {
        const nombre = $("#nuevo-tipo-otro-archivo-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/otro-archivo/insertar-tipo-archivo",
            method: "POST",
            data: { nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#nuevo-tipo-otro-archivo-nombre").val('');
                    cargarTiposOtrosArchivosModal();
                    otrosArchivosUtils.cargarTiposSelect();
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    }

    function editarTipoOtroArchivo(id, nombre) {
        $("#editar-tipo-otro-archivo-id").val(id);
        $("#editar-tipo-otro-archivo-nombre").val(nombre);
        $("#editarTipoOtroArchivoModal").modal('show');
    }

    function guardarTipoOtroArchivoEditado() {
        const id = $("#editar-tipo-otro-archivo-id").val();
        const nombre = $("#editar-tipo-otro-archivo-nombre").val().trim();

        if (!nombre) {
            Swal.fire('Error', 'El nombre es obligatorio', 'error');
            return;
        }

        $.ajax({
            url: _URL + "/ajs/otro-archivo/editar-tipo-archivo",
            method: "POST",
            data: { id: id, nombre: nombre },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    Swal.fire('Éxito', data.msg, 'success');
                    $("#editarTipoOtroArchivoModal").modal('hide');
                    cargarTiposOtrosArchivosModal();
                    otrosArchivosUtils.cargarTiposSelect();
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    }

    function eliminarTipoOtroArchivo(id, nombre) {
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
                    url: _URL + "/ajs/otro-archivo/eliminar-tipo-archivo",
                    method: "POST",
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire('Eliminado', data.msg, 'success');
                            cargarTiposOtrosArchivosModal();
                            otrosArchivosUtils.cargarTiposSelect();
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