<!-- resources\views\fragment-views\cliente\documentos\informes.php -->
<style>
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
    #btn-preview-informe,
    #btn-save-informe,
    .btn-outline-secondary {
        position: relative;
        z-index: 1000;
    }

    /* Asegurar que el editor no sobrepase su contenedor */
    .ql-container {
        overflow: visible;
    }

    .card-body {
        overflow: visible;
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
<div class="tab-content" id="informesTabsContent">
  <!-- Navegación entre Lista y Nuevo Informe -->
<div class="d-flex mb-4 gap-2">
    <button class="btn border-rojo"
        onclick="$('#lista-informes').addClass('show active'); $('#nuevo-informe, #editar-informe, #editar-plantilla').removeClass('show active');">
        <i class="fas fa-list me-2"></i>Lista de Informes
    </button>
    <button class="btn bg-rojo text-white" onclick="mostrarFormularioNuevoInforme()">
        <i class="fas fa-plus me-2"></i>Nuevo Informe
    </button>
    <button class="btn border-rojo" onclick="editarPlantillaInforme()">
        <i class="fas fa-edit me-2"></i>Editar Plantilla
    </button>
</div>
    <!-- Lista de Informes -->
    <div class="tab-pane fade show active" id="lista-informes" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-negro font-medium">Informes</h3>
            <div class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-rojo text-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-rojo" id="buscar-informe"
                        placeholder="Buscar informes..." onkeyup="buscarInformes()">
                </div>
                <div class="dropdown">
                    <button class="btn border-rojo dropdown-toggle" type="button" id="dropdownFiltro"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <ul class="dropdown-menu" id="filtro-tipos" aria-labelledby="dropdownFiltro">
                        <li>
                            <h6 class="dropdown-header">Tipo de Informe</h6>
                        </li>
                        <li><a class="dropdown-item" href="#" data-tipo="todos">Todos</a></li>
                        <!-- Se cargarán dinámicamente los tipos de informes -->
                    </ul>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4" id="lista-informes-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-rojo" role="status">
                    <span class="visually-hidden">Cargando informes...</span>
                </div>
                <p class="mt-2 text-gris">Cargando informes...</p>
            </div>
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

<!-- Script para el módulo de informes -->
<script>
    // Variables globales
    let informes = [];
    let filtroActual = '';
    let tipoFiltroActual = 'todos';
    let informeEditor = null;
    let templateEditor = null;
    let headerImageChanged = false;
    let footerImageChanged = false;
    let headerTemplateImageChanged = false;
    let footerTemplateImageChanged = false;
    let currentHeaderImage = null;
    let currentFooterImage = null;
    let currentHeaderTemplateImage = null;
    let currentFooterTemplateImage = null;
    let editMode = false;
    let informeId = null;
    let moduloInformesInicializado = false;
// Inicializar inmediatamente cuando se carga la página
    $(document).ready(function() {
        console.log("Documento listo, inicializando módulo de informes...");
        
        // Inicializar el módulo de informes directamente
        inicializarModuloInformes();
        
        // Mantener el código existente para la inicialización en cambio de pestaña
        $('#informes-tab').on('shown.bs.tab', function(e) {
            inicializarModuloInformes();
        });
    });

   
 
// Inicialización del módulo
    function inicializarModuloInformes() {
        // Evitar inicialización múltiple
        if (moduloInformesInicializado) {
            console.log('El módulo de informes ya está inicializado, omitiendo reinicialización.');
            return;
        }
        
        console.log('Inicializando módulo de Informes...');
        moduloInformesInicializado = true;

        // Cargar los informes
        cargarInformes();

        // Cargar los tipos de informes para el filtro
        cargarTiposInforme();

        // Configurar el modal de confirmación para eliminar
        $('#confirmarEliminarInformeModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            $('#btn-confirmar-eliminar-informe').off('click').on('click', function() {
                eliminarInforme(id);
            });
        });

        // Configurar eventos de búsqueda
        $("#buscar-informe").on("keyup", function() {
            buscarInformes();
        });
    }

    // Función para cargar los informes

    function cargarInformes() {
        // Mostrar indicador de carga
        $("#lista-informes-container").html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando informes...</p>
        </div>
    `);

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
                // Asegurarse de que data sea un array
                informes = Array.isArray(data) ? data : [];
                renderizarInformes();
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
            }
        });
    }

function renderizarInformes() {
    if (!informes || informes.length === 0) {
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
        return;
    }

    let html = '';

    informes.forEach(function (informe) {
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
                                <li><a class="dropdown-item" href="#" onclick="editarInforme(${informe.id_informe})">
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
                            <canvas id="${canvasId}" class="pdf-preview-canvas"></canvas>
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
                            <a href="${_URL}/ajs/informe/generarPDF?id=${informe.id_informe}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Ver PDF
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editarInforme(${informe.id_informe})">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    $("#lista-informes-container").html(html);
    
    // Inicializar la carga de PDFs después de que el HTML esté en el DOM
    informes.forEach(function (informe) {
        const canvasId = `pdf-preview-${informe.id_informe}`;
        setTimeout(() => {
            renderPdfPreview(`${_URL}/ajs/informe/generarPDF?id=${informe.id_informe}`, canvasId);
        }, 100);
    });
}

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
        // Mostrar indicador de carga
        $("#editar-informe").html(`
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando informe...</p>
        </div>
    `);

        // Mostrar la pestaña de editar informe
        $('#lista-informes').removeClass('show active');
        $('#editar-informe').addClass('show active');

        // Cargar los datos del informe
        $.ajax({
            url: _URL + "/ajs/informe/getOne",
            method: "POST",
            data: { id_informe: id },
            dataType: 'json',
            success: function (data) {
                if (!data.error) {
                    renderizarFormularioInforme(true, data);
                } else {
                    console.error("Error al cargar informe:", data.error);
                    $("#editar-informe").html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar el informe. Por favor, intente nuevamente.
                    </div>
                    <button class="btn bg-rojo text-white mt-3" onclick="volverAListaInformes()">
                        <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                    </button>
                `);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar informe:", status, error);
                $("#editar-informe").html(`
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar el informe. Por favor, intente nuevamente.
                </div>
                <button class="btn bg-rojo text-white mt-3" onclick="volverAListaInformes()">
                    <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                </button>
            `);
            }
        });
    }

   // Modificar la función editarPlantillaInforme() o el evento del botón
function editarPlantillaInforme() {
    // Mostrar indicador de carga
    $("#editar-plantilla").html(`
        <div class="text-center py-5">
            <div class="spinner-border text-rojo" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-gris">Cargando plantilla...</p>
        </div>
    `);

    // CAMBIO IMPORTANTE: Ocultar TODAS las otras vistas antes de mostrar la de editar plantilla
    $('#lista-informes, #nuevo-informe, #editar-informe').removeClass('show active');
    $('#editar-plantilla').addClass('show active');

    // Cargar los datos de la plantilla
    $.ajax({
        url: _URL + "/ajs/informe/obtener-template",
        method: "GET",
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                renderizarFormularioPlantilla(data);
            } else {
                console.error("Error al cargar plantilla:", data.error);
                $("#editar-plantilla").html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar la plantilla. Por favor, intente nuevamente.
                    </div>
                    <button class="btn bg-rojo text-white mt-3" onclick="volverAListaInformes()">
                        <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                    </button>
                `);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar plantilla:", status, error);
            $("#editar-plantilla").html(`
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar la plantilla. Por favor, intente nuevamente.
                </div>
                <button class="btn bg-rojo text-white mt-3" onclick="volverAListaInformes()">
                    <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                </button>
            `);
        }
    });
}
    // Función para renderizar el formulario de informe
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
            header_image: esEdicion ? informe.header_image : (plantilla ? plantilla.header_image : ''),
            footer_image: esEdicion ? informe.footer_image : (plantilla ? plantilla.footer_image : '')
        };

        // Guardar las imágenes actuales
        currentHeaderImage = valores.header_image;
        currentFooterImage = valores.footer_image;

        // Renderizar el formulario
        contenedor.html(`
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white py-3" style="background-image: linear-gradient(to right, #CA3438, #d04a4e);">
                <h5 class="card-title mb-0 fw-bold">${titulo}</h5>
                <p class="card-subtitle mb-0 opacity-75 small">Complete la información del informe</p>
            </div>
            <div class="card-body p-4">
                <form id="formInforme" enctype="multipart/form-data">
                    <input type="hidden" id="id_informe" name="id_informe" value="${valores.id_informe}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo_informe" class="form-label fw-medium text-negro">Tipo de Informe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border rounded-2 shadow-sm" id="tipo_informe" name="tipo" value="${valores.tipo}" placeholder="Ej: TÉCNICO, PAGO, SERVICIO" required>
                            <div class="form-text text-gris small">Este campo se usará para filtrar los informes.</div>
                        </div>
                      <div class="col-md-6">
    <label for="cliente_search" class="form-label fw-medium text-negro">Cliente</label>
    <div class="input-group">
        <input type="text" class="form-control border rounded-start-2 shadow-sm" id="cliente_search" placeholder="Buscar por nombre o documento..." autocomplete="off">
        <button class="btn bg-rojo text-white rounded-end-2" type="button" id="btn-search-cliente">
            <i class="fas fa-search"></i>
        </button>
    </div>
    <input type="hidden" id="cliente_id" name="cliente_id" value="${valores.cliente_id}">
    <div class="mt-2" id="cliente_info" style="display: ${valores.cliente_id ? 'block' : 'none'};">
        <div class="p-2 border rounded bg-light">
            <p class="mb-1"><strong id="cliente_nombre">${valores.cliente_nombre || ''}</strong></p>
            <p class="mb-0 small text-muted" id="cliente_documento"></p>
            <p class="mb-0 small text-muted" id="cliente_direccion"></p>
        </div>
    </div>
</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="titulo_informe" class="form-label fw-medium text-negro">Título del Informe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo_informe" name="titulo" value="${valores.titulo}" required>
                        </div>
                    </div>
                    
                    <!-- Secciones para las imágenes -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="header_image" class="form-label fw-medium text-negro">Imagen de Encabezado</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control border rounded-start shadow-sm" id="header_image" name="header_image" accept="image/png,image/jpeg,image/gif">
                                <button class="btn btn-outline-secondary rounded-end" type="button" id="reset-header-informe">Restablecer</button>
                            </div>
                            <div class="form-text text-gris small">Si no selecciona una imagen, se usará la de la plantilla.</div>
                            <div class="mt-2 border p-2 rounded bg-light">
                                <p class="mb-1 fw-bold">Vista previa:</p>
                                <div id="header-preview-container-informe" class="text-center">
                                    <img id="header-preview-informe" src="${valores.header_image || '/placeholder.svg'}" alt="Vista previa del encabezado" class="img-fluid" style="max-height: 100px; ${valores.header_image ? '' : 'display: none;'}">
                                    <div id="header-placeholder-informe" class="text-muted" ${valores.header_image ? 'style="display: none;"' : ''}>Se usará la imagen de la plantilla</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="footer_image" class="form-label fw-medium text-negro">Imagen de Pie de Página</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control border rounded-start shadow-sm" id="footer_image" name="footer_image" accept="image/png,image/jpeg,image/gif">
                                <button class="btn btn-outline-secondary rounded-end" type="button" id="reset-footer-informe">Restablecer</button>
                            </div>
                            <div class="form-text text-gris small">Si no selecciona una imagen, se usará la de la plantilla.</div>
                            <div class="mt-2 border p-2 rounded bg-light">
                                <p class="mb-1 fw-bold">Vista previa:</p>
                                <div id="footer-preview-container-informe" class="text-center">
                                    <img id="footer-preview-informe" src="${valores.footer_image || '/placeholder.svg'}" alt="Vista previa del pie de página" class="img-fluid" style="max-height: 100px; ${valores.footer_image ? '' : 'display: none;'}">
                                    <div id="footer-placeholder-informe" class="text-muted" ${valores.footer_image ? 'style="display: none;"' : ''}>Se usará la imagen de la plantilla</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-negro">Contenido del Informe <span class="text-danger">*</span></label>
                            <!-- Contenedor para el editor -->
                            <div id="editor-container-informe" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                <!-- El editor Quill se cargará aquí -->
                            </div>
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
                            <button type="button" id="btn-save-informe" class="btn bg-rojo text-white">
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

        // Inicializar el editor Quill
        inicializarInformeEditor(valores.contenido);

        // Inicializar autocomplete para búsqueda de clientes
        $("#cliente_search").autocomplete({
            source: _URL + "/ajs/buscar/cliente/datos", // Usamos la ruta existente
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();

                // Establecer los valores seleccionados
                $("#cliente_id").val(ui.item.codigo); // Usamos 'codigo' que es el campo que devuelve tu API
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

        // Si hay un cliente seleccionado, mostrar su información
        if (valores.cliente_id && valores.cliente_nombre) {
            $("#cliente_search").val(valores.cliente_nombre);
            $("#cliente_info").show();
        }

        // Manejar el envío del formulario usando el botón de guardar
        $("#btn-save-informe").on("click", function () {
            guardarInforme();
        });

        // Manejar la vista previa
        $("#btn-preview-informe").on("click", function () {
            mostrarVistaPrevia();
        });

        // Manejar la vista previa de las imágenes seleccionadas
        $("#header_image").on("change", function () {
            previewImage(this, "header-preview-informe", "header-placeholder-informe");
            headerImageChanged = true;
        });

        $("#footer_image").on("change", function () {
            previewImage(this, "footer-preview-informe", "footer-placeholder-informe");
            footerImageChanged = true;
        });

        // Manejar los botones de restablecer
        $("#reset-header-informe").on("click", function () {
            resetImage("header_image", "header-preview-informe", "header-placeholder-informe", currentHeaderImage);
        });

        $("#reset-footer-informe").on("click", function () {
            resetImage("footer_image", "footer-preview-informe", "footer-placeholder-informe", currentFooterImage);
        });
    }

    // Función para renderizar el formulario de plantilla
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

            if (informeEditor) {
                informeEditor.root.innerHTML = contenido;
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
                    inicializarQuillInforme(contenido);
                };
            } else {
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

        // Asegurarse de que el contenedor existe
        const editorContainer = document.getElementById('editor-container-informe');
        if (!editorContainer) {
            console.error('No se encontró el contenedor del editor');
            return;
        }

        informeEditor = new Quill('#editor-container-informe', {
            modules: {
                toolbar: toolbarOptions,
                clipboard: {
                    matchVisual: false
                }
            },
            theme: 'snow',
            placeholder: 'Contenido del informe...',
            bounds: '#editor-container-informe' // Limitar el alcance del editor a su contenedor
        });

        // Establecer el contenido inicial
        if (contenido) {
            informeEditor.root.innerHTML = contenido;
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

    // Función auxiliar para inicializar Quill para plantillas
    function inicializarQuillTemplate(contenido) {
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

        templateEditor = new Quill('#editor-container-template', {
            modules: {
                toolbar: toolbarOptions,
                clipboard: {
                    matchVisual: false
                }
            },
            theme: 'snow',
            placeholder: 'Contenido por defecto para los informes...'
        });

        // Establecer el contenido inicial
        if (contenido) {
            templateEditor.root.innerHTML = contenido;
        }

        console.log('Editor Quill para plantillas inicializado correctamente');
    }

    // Función para cargar clientes
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

        // Validar campos obligatorios
        if (!tipo.trim() || !titulo.trim()) {
            Swal.fire({
                title: 'Error',
                text: 'Los campos Tipo y Título son obligatorios',
                icon: 'error'
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

        if (editMode) {
            formData.append('id_informe', informeId);
        }

        // Añadir las imágenes si han sido cambiadas
        if (headerImageChanged && document.getElementById('header_image').files[0]) {
            formData.append('header_image', document.getElementById('header_image').files[0]);
        } else if (currentHeaderImage && editMode) {
            formData.append('header_image_base64', currentHeaderImage);
        }

        if (footerImageChanged && document.getElementById('footer_image').files[0]) {
            formData.append('footer_image', document.getElementById('footer_image').files[0]);
        } else if (currentFooterImage && editMode) {
            formData.append('footer_image_base64', currentFooterImage);
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
                        // Volver a la lista de informes
                        volverAListaInformes();
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

        // Añadir las imágenes si han sido cambiadas
        if (headerImageChanged && document.getElementById('header_image').files[0]) {
            formData.append('header_image', document.getElementById('header_image').files[0]);
        } else if (currentHeaderImage) {
            formData.append('header_image_base64', currentHeaderImage);
        }

        if (footerImageChanged && document.getElementById('footer_image').files[0]) {
            formData.append('footer_image', document.getElementById('footer_image').files[0]);
        } else if (currentFooterImage) {
            formData.append('footer_image_base64', currentFooterImage);
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

    // Función para mostrar la vista previa de la plantilla
    function mostrarVistaPreviewTemplate() {
        // Verificar que el editor esté inicializado
        if (!templateEditor) {
            Swal.fire({
                title: 'Error',
                text: 'El editor no está inicializado correctamente',
                icon: 'error'
            });
            return;
        }

        // Obtener el contenido actual
        const contenido = templateEditor.root.innerHTML;
        const titulo = $("#titulo_template").val();

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
                    $("#preview-frame-template").attr("src", pdfUrl);
                    $("#previewTemplateModal").modal("show");

                    // Limpiar la URL cuando se cierre el modal
                    $("#previewTemplateModal").on('hidden.bs.modal', function () {
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
                console.error("Error al generar vista previa de la plantilla:", status, error);
                console.log("Respuesta del servidor:", xhr.responseText);
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

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
                placeholder.style.display = "none";
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = "none";
            placeholder.style.display = "block";
        }
    }

    // Función auxiliar para restablecer imágenes
    function resetImage(inputId, previewId, placeholderId, defaultImage) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);

        // Limpiar el input file
        input.value = "";

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

        // Marcar que la imagen ha sido restablecida
        if (inputId === "header_image") {
            headerImageChanged = false;
        } else if (inputId === "footer_image") {
            footerImageChanged = false;
        } else if (inputId === "header_image_template") {
            headerTemplateImageChanged = false;
        } else if (inputId === "footer_image_template") {
            footerTemplateImageChanged = false;
        }
    }
// Función para renderizar la vista previa del PDF
function renderPdfPreview(pdfUrl, canvasId) {
    console.log('Renderizando PDF:', pdfUrl, 'en canvas:', canvasId);
    
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
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        // Obtener la primera página
        pdf.getPage(1).then(function(page) {
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
            page.render(renderContext).promise.then(function() {
                console.log('PDF renderizado correctamente en', canvasId);
            }).catch(function(error) {
                console.error('Error al renderizar el PDF:', error);
            });
        }).catch(function(error) {
            console.error('Error al obtener la página del PDF:', error);
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
    }).catch(function(error) {
        console.error('Error al cargar el PDF:', error);
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
</script>
