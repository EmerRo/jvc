<!-- resources/views/fragment-views/cliente/documentos/componentes/fichas-tecnicas.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/fichas-tecnicas.css') ?>?v=<?= time() ?>">


<div class="tab-content" id="fichasTabsContent">
    <!-- Navegación entre Lista y Nueva Ficha -->
    <div class="d-flex mb-4 gap-2">
        <button class="btn border-rojo" onclick="limpiarFormularioCompleto(); $('#lista-fichas').addClass('show active'); $('#nueva-ficha').removeClass('show active');">
            <i class="fas fa-list me-2"></i>Lista de Fichas
        </button>
        <button class="btn bg-rojo text-white" onclick="mostrarFormularioNuevaFicha()">
            <i class="fas fa-plus me-2"></i>Subir Nueva
        </button>
        <button class="btn bg-rojo hover:bg-white" onclick="reiniciarModuloFichas()">
            <i class="fas fa-sync me-2"></i>Reiniciar Módulo
        </button>
    </div>

    <!-- Lista de Fichas -->
    <div class="tab-pane fade show active" id="lista-fichas" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-negro font-medium">Fichas Técnicas</h3>
            <div class="d-flex gap-2 ">
                <div class="input-group">
                    <span class="input-group-text bg-rojo text-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-rojo" id="buscar-ficha_tecnica" placeholder="Buscar fichas...">
                </div>
                <button class="btn btn-selector" type="button" data-bs-toggle="modal" data-bs-target="#filtroModal">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>
        
        <!-- Toolbar de selección masiva -->
        <div id="toolbar-sel-fichas" class="d-flex align-items-center gap-2 flex-wrap mb-3 px-2 py-2 rounded border bg-light d-none">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" id="check-all-fichas" style="width:1.1rem;height:1.1rem;cursor:pointer;">
                <label class="form-check-label fw-semibold" for="check-all-fichas">Seleccionar todos</label>
            </div>
            <span class="badge bg-secondary ms-1" id="count-sel-fichas">0 seleccionados</span>
            <button class="btn btn-danger btn-sm ms-auto" onclick="eliminarFichasSeleccionadas()">
                <i class="fas fa-trash-alt me-1"></i> Eliminar seleccionadas
            </button>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4" id="lista-archivos-ficha_tecnica">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-rojo" role="status">
                    <span class="visually-hidden">Cargando fichas técnicas...</span>
                </div>
                <p class="mt-2 text-gris">Cargando fichas técnicas...</p>
            </div>
        </div>
    </div>
    
    <!-- Nueva Ficha -->
    <div class="tab-pane fade" id="nueva-ficha" role="tabpanel">
        <?php include 'formularios/nueva-ficha.php'; ?>
    </div>
</div>



<!-- Script para el módulo de fichas técnicas -->
<script>
    // Envolver todo el código en una función autoejecutable para evitar conflictos globales
(function() {
    'use strict';
    
    // Verificar si el módulo ya está inicializado
    if (window.ModuloFichasTecnicas && window.ModuloFichasTecnicas.inicializado) {
        return;
    }
    
    // Crear namespace del módulo si no existe
    window.ModuloFichasTecnicas = window.ModuloFichasTecnicas || {};
    
    // Marcar como inicializado
    window.ModuloFichasTecnicas.inicializado = true;

    const estadoAdjuntosFicha = {
        pdf: { url: null, eliminar: false },
        editable: { url: null, eliminar: false }
    };

    let fichasSeleccionadas = new Set();
    
    // Limpiar eventos duplicados antes de inicializar
    function limpiarEventosDuplicados() {
        $('#buscar-ficha_tecnica').off('keyup.fichasTecnicas');
        $('#nuevaFichaForm').off('submit.fichasTecnicas');
        $('#nueva-ficha-btn').off('click.fichasTecnicas');
        $(document).off('click.fichasTecnicas');
    }
    
    // Ejecutar limpieza
    limpiarEventosDuplicados();
// Esperar a que el documento esté listo
$(document).ready(function () {
    limpiarEventosDuplicados();
    // Inicializar el módulo de fichas técnicas
    // console.log('Inicializando módulo de Fichas Técnicas...');
    
    // Cargar las fichas técnicas al inicio
    // cargarFichas();
    
    // Retrasar la carga inicial de fichas para dar tiempo a PDF.js de inicializarse
    setTimeout(function() {
        cargarFichas();
    }, 300);
    // Botón Nueva Ficha
    $('#nueva-ficha-btn').off('click.fichasTecnicas').on('click.fichasTecnicas', function() {
        mostrarFormularioNuevaFicha();
    });
    
    // Inicializar búsqueda
 $('#buscar-ficha_tecnica').off('keyup.fichasTecnicas').on('keyup.fichasTecnicas', function() {
        buscarFichas($(this).val());
    });
    
    // Inicializar contenedores de archivos
    inicializarUploadContainers();
    
    // Inicializar búsqueda de productos
    inicializarBusquedaProductos();
    
    // Formulario de nueva ficha
    $('#nuevaFichaForm').on('submit', function(e) {
        e.preventDefault();
        guardarFicha(this);
    });

    $('#nuevaFichaForm').off('click.adjuntosFicha').on('click.adjuntosFicha', '[data-adjunto-accion]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const tipo = $(this).data('adjunto-tipo');
        const accion = $(this).data('adjunto-accion');

        if (accion === 'quitar') {
            quitarArchivoFicha(tipo);
        } else if (accion === 'cancelar-eliminacion') {
            cancelarEliminacionArchivoFicha(tipo);
        }
    });
});

// Función para cargar las fichas técnicas
function cargarFichas() {
    const contenedor = $('#lista-archivos-ficha_tecnica');
    
    // Mostrar estado de carga
    contenedor.html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando fichas técnicas...</span>
            </div>
            <p class="mt-2">Cargando fichas técnicas...</p>
        </div>
    `);
    
    // Realizar petición AJAX
    $.ajax({
        url: _URL +  '/ajs/fichas-tecnicas/listar',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            if (data.res && Array.isArray(data.fichas)) {
                if (data.fichas.length > 0) {
                    renderizarFichas(data.fichas);
                } else {
                    contenedor.html(`
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h4>No hay fichas técnicas</h4>
                                <p>No se encontraron fichas técnicas en el sistema.</p>
                            </div>
                            <button class="btn btn-primary mt-3" id="crear-primera-ficha">
                                <i class="fas fa-plus me-2"></i>Crear primera ficha técnica
                            </button>
                        </div>
                    `);
                    
                    // Agregar evento al botón de crear primera ficha
                    $('#crear-primera-ficha').on('click', function() {
                        mostrarFormularioNuevaFicha();
                    });
                }
            } else {
                contenedor.html(`
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h4>Error al cargar fichas técnicas</h4>
                            <p>La respuesta del servidor no tiene el formato esperado.</p>
                        </div>
                        <button class="btn btn-primary mt-3" onclick="cargarFichas()">
                            <i class="fas fa-sync me-2"></i>Reintentar
                        </button>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            contenedor.html(`
                <div class="col-12 text-center py-5">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h4>Error al cargar fichas técnicas</h4>
                        <p>Error: ${error}</p>
                        <p>Estado: ${status}</p>
                    </div>
                    <button class="btn btn-primary mt-3" onclick="cargarFichas()">
                        <i class="fas fa-sync me-2"></i>Reintentar
                    </button>
                </div>
            `);
        }
    });
}

function actualizarToolbarFichas() {
    const count = fichasSeleccionadas.size;
    $('#toolbar-sel-fichas').toggleClass('d-none', count === 0);
    $('#count-sel-fichas').text(`${count} seleccionada${count !== 1 ? 's' : ''}`);
}

function eliminarFichasSeleccionadas() {
    const count = fichasSeleccionadas.size;
    if (count === 0) return;
    const ids = Array.from(fichasSeleccionadas);

    Swal.fire({
        title: `¿Eliminar ${count} ficha${count !== 1 ? 's' : ''} técnica${count !== 1 ? 's' : ''}?`,
        text: 'Se eliminarán las fichas seleccionadas junto con sus archivos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar'
    }).then((r1) => {
        if (!r1.isConfirmed) return;

        Swal.fire({
            title: 'Confirmación final',
            html: `Está a punto de eliminar <strong>${count} ficha técnica${count !== 1 ? 's' : ''}</strong> de forma permanente, incluyendo todos sus archivos adjuntos.<br><br>Esta acción <strong>no se puede deshacer</strong>.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c0392b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar definitivamente',
            cancelButtonText: 'Cancelar'
        }).then((r2) => {
            if (!r2.isConfirmed) return;

            Swal.fire({ title: 'Eliminando...', text: 'Por favor espere', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: _URL + '/ajs/fichas-tecnicas/eliminar-masivo',
                method: 'POST',
                data: { ids: ids },
                dataType: 'json',
                success: function (data) {
                    if (data.res) {
                        Swal.fire({ icon: 'success', title: 'Eliminadas', text: data.msg, timer: 2000, showConfirmButton: false });
                        fichasSeleccionadas.clear();
                        cargarFichas();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.msg || data.error });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor' });
                }
            });
        });
    });
}

function renderizarFichas(fichas) {
    fichasSeleccionadas.clear();
    actualizarToolbarFichas();
    const contenedor = $('#lista-archivos-ficha_tecnica');
    let html = '';

    fichas.forEach(ficha => {
        html += `
            <div class="col">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-start gap-2">
                        <input type="checkbox" class="form-check-input ficha-sel-check mt-1" data-id="${ficha.id_archivo}"
                            style="width:1.1rem;height:1.1rem;cursor:pointer;flex-shrink:0;">
                        <div class="flex-grow-1">
                        <h5 class="card-title mb-0">${ficha.numero ? `<span class="badge bg-light text-dark me-2">#${ficha.numero}</span>` : ''}${ficha.titulo || 'Sin título'}</h5>
                        <div class="mt-1">
                           <!-- <span class="badge bg-primary">v${ficha.version || '1.0'}</span> -->
                         ${ficha.id_producto ? `<span class="badge bg-secondary ms-1" style="color: #CA3438" title="${escapeHtml(ficha.nombre_producto || 'Producto asociado')}">${ficha.nombre_producto || 'Producto asociado'}</span>` : ''}
                        </div>
                        </div>
                    </div>
                    <!-- Eliminamos el padding del card-body para la vista previa -->
                    <div class="card-body p-0">
                        <!-- Vista previa del documento -->
                        <div class="document-preview">
                            ${getPreviewHTML(ficha)}
                        </div>
                        <!-- Información y botones con padding normal -->
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Actualizado: ${formatDate(ficha.fecha_actualizacion)}</small>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="verFicha(${ficha.id_archivo})" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editarFicha(${ficha.id_archivo})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="compartirWhatsApp(${ficha.id_archivo})" title="Compartir">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarFicha(${ficha.id_archivo})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    contenedor.html(html);

    // Eventos de selección masiva
    $('.ficha-sel-check').off('change').on('change', function () {
        const id = parseInt($(this).data('id'));
        if ($(this).is(':checked')) fichasSeleccionadas.add(id);
        else { fichasSeleccionadas.delete(id); $('#check-all-fichas').prop('checked', false); }
        actualizarToolbarFichas();
    });

    $('#check-all-fichas').off('change').on('change', function () {
        const checked = $(this).is(':checked');
        $('.ficha-sel-check').each(function () {
            $(this).prop('checked', checked);
            const id = parseInt($(this).data('id'));
            if (checked) fichasSeleccionadas.add(id);
            else fichasSeleccionadas.delete(id);
        });
        actualizarToolbarFichas();
    });
}

function getPreviewHTML(ficha) {
    // NUEVO: Usar la nueva estructura de adjuntos
    const adjuntos = ficha.adjuntos || {};
    
    // Si hay PDF, mostrarlo como principal
    if (adjuntos.pdf && adjuntos.pdf.url) {
        const canvasId = `pdf-preview-${ficha.id_archivo}`;
        
        // Inicializar la carga del PDF después de que se renderice el HTML
        setTimeout(() => {
            renderPdfPreview(adjuntos.pdf.url, canvasId);
        }, 100);
        
        return `<div class="document-preview">
            <canvas id="${canvasId}" class="pdf-preview-canvas"></canvas>
        </div>`;
    }
    
    // Si hay imágenes, mostrar la primera como preview
    if (adjuntos.imagenes && adjuntos.imagenes.length > 0) {
        return `<div class="document-preview">
            <img src="${adjuntos.imagenes[0].url}" class="img-fluid" alt="${adjuntos.imagenes[0].nombre || 'Vista previa'}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>`;
    }
    
    // Si hay archivo editable, mostrar icono
    if (adjuntos.editable && adjuntos.editable.url) {
        return `<div class="document-preview d-flex align-items-center justify-content-center">
            <div class="text-center">
                <i class="fas fa-file-word fa-4x text-primary"></i>
                <p class="mt-2">Archivo editable</p>
            </div>
        </div>`;
    }
    
    // Si hay YouTube, mostrar preview del video
    if (adjuntos.youtube && adjuntos.youtube.url) {
        return `<div class="document-preview d-flex align-items-center justify-content-center">
            <div class="text-center">
                <i class="fab fa-youtube fa-4x text-danger"></i>
                <p class="mt-2">Video de YouTube</p>
            </div>
        </div>`;
    }
    
    // Vista por defecto
    return `<div class="document-preview d-flex align-items-center justify-content-center">
        <div class="text-center">
            <i class="fas fa-file-alt fa-4x text-primary"></i>
            <p class="mt-2">Ver documento</p>
        </div>
    </div>`;
}

// Función para limpiar todas las tareas de renderizado activas
function limpiarTareasRenderizado() {
    activeRenderTasks.forEach((task, canvasId) => {
        if (task && typeof task.cancel === 'function') {
            task.cancel();
        }
        // Limpiar el canvas
        clearCanvas(canvasId);
        // Liberar el lock
        canvasLocks.delete(canvasId);
        renderQueue.delete(canvasId);
    });
    activeRenderTasks.clear();
  
    // console.log('Todas las tareas de renderizado han sido canceladas');
}

// Función para limpiar un canvas específico
function clearCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (canvas) {
        const context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = 'white';
        context.fillRect(0, 0, canvas.width, canvas.height);
    }
}

// Variable global para rastrear las tareas de renderizado activas
const activeRenderTasks = new Map();
const canvasLocks = new Map(); // Para evitar renderizados simultáneos
const renderQueue = new Map(); // Cola de renderizado por canvas

// Función para renderizar la vista previa del PDF (versión mejorada)
function renderPdfPreview(pdfUrl, canvasId) {
    // Verificar si pdfjsLib está disponible
    if (!window.pdfjsLib || typeof window.pdfjsLib.getDocument !== 'function') {
        setTimeout(() => renderPdfPreview(pdfUrl, canvasId), 100);
        return;
    }
    
    // Verificar si ya hay una operación en curso para este canvas
    if (canvasLocks.has(canvasId)) {
        return; // Salir silenciosamente si ya está siendo procesado
    }
    
    try {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            return;
        }
        
        // Bloquear este canvas para evitar renderizados simultáneos
        canvasLocks.set(canvasId, true);
        
        // Cancelar cualquier tarea anterior para este canvas
        if (activeRenderTasks.has(canvasId)) {
            const previousTask = activeRenderTasks.get(canvasId);
            if (previousTask && typeof previousTask.cancel === 'function') {
                previousTask.cancel();
            }
            activeRenderTasks.delete(canvasId);
        }
        
        // Limpiar el canvas antes de empezar
        clearCanvas(canvasId);
        
        // Cargar el documento PDF
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        activeRenderTasks.set(canvasId, loadingTask);
        
        loadingTask.promise.then(function(pdf) {
            // Verificar si la tarea fue cancelada
            if (!activeRenderTasks.has(canvasId) || !canvasLocks.has(canvasId)) {
                return Promise.reject(new Error('Task cancelled'));
            }
            
            return pdf.getPage(1);
        }).then(function(page) {
            // Verificar nuevamente si la tarea fue cancelada
            if (!activeRenderTasks.has(canvasId) || !canvasLocks.has(canvasId)) {
                return Promise.reject(new Error('Task cancelled'));
            }
            
            const canvas = document.getElementById(canvasId);
            if (!canvas) {
                return Promise.reject(new Error('Canvas not found'));
            }
            
            const context = canvas.getContext('2d');
            
            // Configurar el canvas
            const container = canvas.parentElement;
            const containerWidth = container.clientWidth;
            const containerHeight = container.clientHeight;
            
            canvas.width = containerWidth * 2;
            canvas.height = containerHeight * 2;
            
            // Configurar el viewport
            const viewport = page.getViewport({ scale: 1.0 });
            const scale = (canvas.width / viewport.width) * 1.0;
            const scaledViewport = page.getViewport({ scale: scale });
            
            const offsetX = (canvas.width - scaledViewport.width) / 2;
            const offsetY = 0;
            
            // Limpiar el canvas nuevamente antes de renderizar
            clearCanvas(canvasId);
            
            // Configurar el contexto de renderizado
            const renderContext = {
                canvasContext: context,
                viewport: scaledViewport,
                transform: [1, 0, 0, 1, offsetX, offsetY],
                intent: 'display'
            };
            
            // Crear y ejecutar la tarea de renderizado
            const renderTask = page.render(renderContext);
            activeRenderTasks.set(canvasId, renderTask);
            
            return renderTask.promise;
        }).then(function() {
            // Renderizado exitoso - limpiar referencias
            activeRenderTasks.delete(canvasId);
            canvasLocks.delete(canvasId);
        }).catch(function(error) {
            // Limpiar en caso de error
            activeRenderTasks.delete(canvasId);
            canvasLocks.delete(canvasId);
            
            // Solo mostrar error si no fue una cancelación
            if (error.message !== 'Task cancelled' && error.name !== 'RenderingCancelledException') {
                console.error('Error al renderizar PDF:', error);
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    canvas.parentNode.innerHTML = `
                        <div class="text-center p-3">
                            <i class="fas fa-file-pdf fa-4x text-danger"></i>
                            <p class="mt-2">Ver PDF</p>
                        </div>
                    `;
                }
            }
        });
    } catch (error) {
        console.error('Error general al renderizar PDF:', error);
        activeRenderTasks.delete(canvasId);
        canvasLocks.delete(canvasId);
    }
}

// Función para convertir URL de YouTube a formato embed
function getYouTubeEmbedUrl(url) {
    if (!url) return '';
    
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    
    if (match && match[2].length === 11) {
        return `https://www.youtube.com/embed/${match[2]}`;
    }
    
    return url;
}

// Función para ver una ficha técnica
function verFicha(id) {
    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/obtener',
        type: 'POST',
        data: { id_archivo: id },
        dataType: 'json',
        success: function(data) {
            // NUEVO: Log para debug

            
            if (data.res) {
                // NUEVO: Log para debug

                
                // CORREGIDO: Pasar los adjuntos que están dentro de ficha
                mostrarModalFicha(data.ficha, data.ficha.adjuntos);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener la ficha técnica: ' + (data.error || 'Error desconocido'),
                    confirmButtonColor: '#3085d6'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', xhr, status, error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al obtener la ficha técnica. Intente nuevamente.',
                confirmButtonColor: '#3085d6'
            });
        }
    });
}

// Función para mostrar el modal de ficha técnica
function mostrarModalFicha(ficha, adjuntos) {
    // NUEVO: Log para debug
    
    
    const modal = $('#verArchivoModal');
    const modalTitle = modal.find('.modal-title');
    const modalBody = $('#verArchivoModalBody');
    const descargarBtn = $('#descargarArchivoBtn');
    
    modalTitle.text(ficha.titulo || 'Ficha Técnica');
    
    // NUEVO: Validar que adjuntos exista y tenga la estructura correcta
    if (!adjuntos || typeof adjuntos !== 'object') {
        console.warn('Adjuntos no válidos, intentando obtener desde ficha:', adjuntos);
        // Intentar obtener adjuntos desde ficha si no se pasaron directamente
        adjuntos = ficha.adjuntos || {};
    }
    
    // NUEVO: Log después de la validación
    
    
    let html = `
        <div class="row">
            <div class="col-md-8">
                ${getContenidoPrincipal(ficha, adjuntos)}
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Información</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Fecha:</strong> ${formatDate(ficha.fecha_creacion || ficha.fecha_actualizacion)}</p>
                        ${ficha.nombre_producto ? `<p><strong>Producto:</strong> ${ficha.nombre_producto}</p>` : ''}
                        ${ficha.version ? `<p><strong>Versión:</strong> ${ficha.version}</p>` : ''}
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Archivos Adjuntos</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            ${getListaAdjuntos(adjuntos)}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // NUEVO: Log del HTML generado
    
    
    modalBody.html(html);
    
    // Configurar botón de descarga
    if (adjuntos && adjuntos.pdf && adjuntos.pdf.url) {
        descargarBtn.on('click', function() {
            window.open(adjuntos.pdf.url, '_blank');
        });
        descargarBtn.show();
    } else {
        descargarBtn.hide();
    }
    
    // Mostrar modal
    modal.modal('show');
}

// Función para obtener el contenido principal
function getContenidoPrincipal(ficha, adjuntos) {
    // NUEVO: Log para debug
    
    
    // NUEVO: Validar que adjuntos exista
    if (!adjuntos || typeof adjuntos !== 'object') {
        console.warn('Adjuntos no válidos en getContenidoPrincipal:', adjuntos);
        return `
            <div class="text-center p-5 bg-light rounded">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                <h4>Error en los datos</h4>
                <p class="text-muted">No se pudieron cargar los archivos adjuntos</p>
                <small class="text-muted">Debug: adjuntos = ${JSON.stringify(adjuntos)}</small>
                <br><small class="text-muted">Tipo: ${typeof adjuntos}</small>
            </div>
        `;
    }
    
    // NUEVO: Log de las propiedades disponibles
    
    
    // NUEVO: Mostrar PDF como contenido principal si existe
    if (adjuntos.pdf && adjuntos.pdf.url) {
        
        return `
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        Documento PDF
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="${adjuntos.pdf.url}" allowfullscreen style="border: none;"></iframe>
                    </div>
                </div>
            </div>
        `;
    }
    
    // NUEVO: Si hay imágenes, mostrar galería como contenido principal
    if (adjuntos.imagenes && Array.isArray(adjuntos.imagenes) && adjuntos.imagenes.length > 0) {
        
        return `
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images text-info me-2"></i>
                        Galería de Imágenes (${adjuntos.imagenes.length})
                    </h5>
                </div>
                <div class="card-body">
                    ${getGaleriaImagenes(adjuntos.imagenes)}
                </div>
            </div>
        `;
    }
    
    // NUEVO: Si hay archivo editable, mostrarlo como contenido principal
    if (adjuntos.editable && adjuntos.editable.url) {
        
        return `
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-word text-primary me-2"></i>
                        Archivo Editable
                    </h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-file-word fa-5x text-primary mb-3"></i>
                    <h4>${adjuntos.editable.nombre || 'Archivo editable'}</h4>
                    <p class="text-muted">Archivo disponible para descarga</p>
                    <a href="${adjuntos.editable.url}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download me-2"></i>Descargar
                    </a>
                </div>
            </div>
        `;
    }
    
    // NUEVO: Si hay YouTube, mostrar video como contenido principal
    if (adjuntos.youtube && adjuntos.youtube.url) {
        
        return `
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fab fa-youtube text-danger me-2"></i>
                        Video de YouTube
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="${getYouTubeEmbedUrl(adjuntos.youtube.url)}" allowfullscreen style="border: none;"></iframe>
                    </div>
                </div>
            </div>
        `;
    }
    
    // NUEVO: Log si no se encontró ningún contenido
    
    
    // Vista por defecto si no hay adjuntos
    return `
        <div class="text-center p-5 bg-light rounded">
            <i class="fas fa-file-alt fa-5x text-muted mb-3"></i>
            <h4>Ficha Técnica</h4>
            <p class="text-muted">No hay archivos adjuntos para mostrar</p>
            <small class="text-muted">Debug: adjuntos = ${JSON.stringify(adjuntos)}</small>
            <br><small class="text-muted">Tipo: ${typeof adjuntos}</small>
            <br><small class="text-muted">Propiedades: ${Object.keys(adjuntos).join(', ')}</small>
        </div>
    `;
}
// NUEVA: Función para mostrar galería de imágenes (máximo 3)
function getGaleriaImagenes(imagenes) {
    if (!imagenes || imagenes.length === 0) {
        return `
            <div class="text-center p-5 bg-light rounded">
                <i class="fas fa-image fa-5x text-muted mb-3"></i>
                <p class="text-muted">No hay imágenes disponibles</p>
            </div>
        `;
    }
    
    if (imagenes.length === 1) {
        // Una sola imagen - mostrar grande
        return `
            <div class="text-center">
                <img src="${imagenes[0].url}" 
                     class="img-fluid rounded shadow-sm" 
                     alt="${imagenes[0].nombre || 'Imagen'}"
                     style="max-height: 400px; cursor: pointer; border: 2px solid #e9ecef;"
                     onclick="ampliarImagen('${imagenes[0].url}', '${escapeHtml(imagenes[0].nombre || 'Imagen')}')"
                     title="Haz clic para ampliar">
                <div class="mt-2">
                    <small class="text-muted">${imagenes[0].nombre || 'Imagen'}</small>
                    <br><small class="text-info"><i class="fas fa-mouse-pointer me-1"></i>Haz clic para ampliar</small>
                </div>
            </div>
        `;
    } else if (imagenes.length === 2) {
        // Dos imágenes - mostrar en columnas
        return `
            <div class="row g-3">
                ${imagenes.map((img, index) => `
                    <div class="col-6">
                        <div class="text-center">
                            <img src="${img.url}" 
                                 class="img-fluid rounded shadow-sm" 
                                 alt="${img.nombre || `Imagen ${index + 1}`}"
                                 style="height: 200px; width: 100%; object-fit: cover; cursor: pointer; border: 2px solid #e9ecef;"
                                 onclick="ampliarImagen('${img.url}', '${escapeHtml(img.nombre || `Imagen ${index + 1}`)}')"
                                 title="Haz clic para ampliar">
                            <div class="mt-2">
                                <small class="text-muted">${img.nombre || `Imagen ${index + 1}`}</small>
                                <br><small class="text-info"><i class="fas fa-mouse-pointer me-1"></i>Haz clic para ampliar</small>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    } else {
        // Tres imágenes - mostrar en grid especial
        return `
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-center">
                        <img src="${imagenes[0].url}" 
                             class="img-fluid rounded shadow-sm" 
                             alt="${imagenes[0].nombre || 'Imagen 1'}"
                             style="height: 250px; width: 100%; object-fit: cover; cursor: pointer; border: 2px solid #e9ecef;"
                             onclick="ampliarImagen('${imagenes[0].url}', '${escapeHtml(imagenes[0].nombre || 'Imagen 1')}')"
                             title="Haz clic para ampliar">
                        <div class="mt-2">
                            <small class="text-muted">${imagenes[0].nombre || 'Imagen 1'}</small>
                            <br><small class="text-info"><i class="fas fa-mouse-pointer me-1"></i>Haz clic para ampliar</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="text-center">
                                <img src="${imagenes[1].url}" 
                                     class="img-fluid rounded shadow-sm" 
                                     alt="${imagenes[1].nombre || 'Imagen 2'}"
                                     style="height: 120px; width: 100%; object-fit: cover; cursor: pointer; border: 2px solid #e9ecef;"
                                     onclick="ampliarImagen('${imagenes[1].url}', '${escapeHtml(imagenes[1].nombre || 'Imagen 2')}')"
                                     title="Haz clic para ampliar">
                                <div class="mt-2">
                                    <small class="text-muted">${imagenes[1].nombre || 'Imagen 2'}</small>
                                    <br><small class="text-info"><i class="fas fa-mouse-pointer me-1"></i>Haz clic para ampliar</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-center">
                                <img src="${imagenes[2].url}" 
                                     class="img-fluid rounded shadow-sm" 
                                     alt="${imagenes[2].nombre || 'Imagen 3'}"
                                     style="height: 120px; width: 100%; object-fit: cover; cursor: pointer; border: 2px solid #e9ecef;"
                                     onclick="ampliarImagen('${imagenes[2].url}', '${escapeHtml(imagenes[2].nombre || 'Imagen 3')}')"
                                     title="Haz clic para ampliar">
                                <div class="mt-2">
                                    <small class="text-muted">${imagenes[2].nombre || 'Imagen 3'}</small>
                                    <br><small class="text-info"><i class="fas fa-mouse-pointer me-1"></i>Haz clic para ampliar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// NUEVA: Función para ampliar imagen en modal
function ampliarImagen(rutaImagen, nombreImagen) {
    // Validar parámetros
    if (!rutaImagen) {
        console.error('ampliarImagen: rutaImagen es requerida');
        return;
    }
    
    const nombre = nombreImagen || 'Imagen';
    
    Swal.fire({
        title: nombre,
        html: `
            <div class="text-center">
                <img src="${rutaImagen}" 
                     class="img-fluid" 
                     alt="${nombre}" 
                     style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        width: 'auto',
        customClass: {
            popup: 'swal-wide'
        },
        didOpen: () => {
            // Agregar botón de descarga
            const swalContainer = Swal.getContainer();
            if (swalContainer) {
                const downloadBtn = document.createElement('button');
                downloadBtn.className = 'btn btn-success mt-3';
                downloadBtn.innerHTML = '<i class="fas fa-download me-2"></i>Descargar';
                downloadBtn.onclick = () => {
                    const link = document.createElement('a');
                    link.href = rutaImagen;
                    link.download = nombre;
                    link.click();
                };
                swalContainer.querySelector('.swal2-html-container').appendChild(downloadBtn);
            }
        }
    });
}

// NUEVA: Función para ampliar imagen en modal (versión alternativa)
function ampliarImagenAlternativa(rutaImagen, nombreImagen) {
    // Crear modal personalizado con Bootstrap
    const modalHtml = `
        <div class="modal fade" id="imagenAmpliadaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${nombreImagen || 'Imagen'}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${rutaImagen}" 
                             class="img-fluid" 
                             alt="${nombreImagen || 'Imagen'}" 
                             style="max-width: 100%; max-height: 60vh; border-radius: 8px;">
                    </div>
                    <div class="modal-footer">
                        <a href="${rutaImagen}" 
                           class="btn btn-success" 
                           download="${nombreImagen || 'imagen'}" 
                           target="_blank">
                            <i class="fas fa-download me-2"></i>Descargar
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('imagenAmpliadaModal');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    // Agregar nuevo modal al body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('imagenAmpliadaModal'));
    modal.show();
    
    // Limpiar modal cuando se cierre
    document.getElementById('imagenAmpliadaModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Función para obtener la lista de adjuntos
function getListaAdjuntos(adjuntos) {
    // NUEVO: Validar que adjuntos exista
    if (!adjuntos || typeof adjuntos !== 'object' || Object.keys(adjuntos).length === 0) {
        return '<li class="list-group-item text-center text-muted">No hay archivos adjuntos</li>';
    }
    
    let html = '';
    let totalAdjuntos = 0;
    
    // NUEVO: Definir grupos de adjuntos con iconos y títulos
    const grupos = {
        pdf: { 
            icon: 'fas fa-file-pdf text-danger', 
            title: 'Documento PDF',
            tipo: 'pdf'
        },
        editable: { 
            icon: 'fas fa-file-word text-primary', 
            title: 'Archivo Editable',
            tipo: 'editable'
        },
        imagenes: { 
            icon: 'fas fa-images text-info', 
            title: 'Imágenes',
            tipo: 'imagenes'
        },
        youtube: { 
            icon: 'fab fa-youtube text-danger', 
            title: 'Video de YouTube',
            tipo: 'youtube'
        }
    };
    
    // NUEVO: Procesar cada tipo de adjunto
    Object.keys(grupos).forEach(tipo => {
        const grupo = grupos[tipo];
        
        if (tipo === 'imagenes' && adjuntos[tipo] && Array.isArray(adjuntos[tipo]) && adjuntos[tipo].length > 0) {
            // Manejar imágenes como array
            adjuntos[tipo].forEach((img, index) => {
                if (img && img.url) {
                    totalAdjuntos++;
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1 me-2" style="min-width: 0;">
                                <i class="${grupo.icon} me-2"></i>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${escapeHtml(img.nombre || `Imagen ${index + 1}`)}">
                                    ${escapeHtml(img.nombre || `Imagen ${index + 1}`)}
                                </span>
                                <small class="text-muted d-block">${grupo.title}</small>
                            </div>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" onclick="ampliarImagen('${img.url}', '${escapeHtml(img.nombre || `Imagen ${index + 1}`)}')" title="Ver imagen">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="${img.url}" class="btn btn-sm btn-outline-success" target="_blank" title="Descargar imagen">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </li>
                    `;
                }
            });
        } else if (adjuntos[tipo] && adjuntos[tipo].url) {
            // Manejar otros tipos como objetos individuales
            const adjunto = adjuntos[tipo];
            totalAdjuntos++;
            
            // Determinar el nombre a mostrar
            let nombreMostrar = adjunto.nombre || grupo.title;
            if (tipo === 'youtube' && nombreMostrar.length > 50) {
                nombreMostrar = nombreMostrar.substring(0, 47) + '...';
            }
            
            // Determinar botones según el tipo
            let botones = '';
            if (tipo === 'youtube') {
                botones = `
                    <button class="btn btn-sm btn-outline-danger" onclick="window.open('${adjunto.url}', '_blank')" title="Ver video">
                        <i class="fab fa-youtube"></i>
                    </button>
                `;
            } else if (tipo === 'pdf') {
                botones = `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="window.open('${adjunto.url}', '_blank')" title="Ver PDF">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="${adjunto.url}" class="btn btn-sm btn-outline-success" target="_blank" title="Descargar PDF">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                `;
            } else {
                botones = `
                    <a href="${adjunto.url}" class="btn btn-sm btn-outline-success" target="_blank" title="Descargar archivo">
                        <i class="fas fa-download"></i>
                    </a>
                `;
            }
            
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1 me-2" style="min-width: 0;">
                        <i class="${grupo.icon} me-2"></i>
                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${escapeHtml(nombreMostrar)}">
                            ${escapeHtml(nombreMostrar)}
                        </span>
                        <small class="text-muted d-block">${grupo.title}</small>
                    </div>
                    <div>
                        ${botones}
                    </div>
                </li>
            `;
        }
    });
    
    // NUEVO: Si no hay adjuntos válidos
    if (totalAdjuntos === 0) {
        return '<li class="list-group-item text-center text-muted">No hay archivos adjuntos válidos</li>';
    }
    
    // NUEVO: Agregar contador total
    html = `
        <li class="list-group-item bg-light">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Total: ${totalAdjuntos} archivo${totalAdjuntos !== 1 ? 's' : ''} adjunto${totalAdjuntos !== 1 ? 's' : ''}
            </small>
        </li>
    ` + html;
    
    return html;
}

// Función para compartir por WhatsApp
function compartirWhatsApp(id) {
    $('#id_archivo_compartir').val(id);
    $('#compartirWhatsAppModal').modal('show');
}

// Función para marcar todos los archivos
function marcarTodosArchivos() {
    $('#check_pdf, #check_editable, #check_imagenes, #check_youtube').prop('checked', true);
}

// Función para desmarcar todos los archivos
function desmarcarTodosArchivos() {
    $('#check_pdf, #check_editable, #check_imagenes, #check_youtube').prop('checked', false);
}

// Función para enviar por WhatsApp
function enviarWhatsApp() {
    const id_archivo = $('#id_archivo_compartir').val();
    const telefono = $('#telefono').val();
    
    if (!telefono || telefono.length !== 9 || !/^\d+$/.test(telefono)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ingrese un número de teléfono válido (9 dígitos)',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    // Obtener selecciones de archivos
    const incluirPDF = $('#check_pdf').is(':checked');
    const incluirEditable = $('#check_editable').is(':checked');
    const incluirImagenes = $('#check_imagenes').is(':checked');
    const incluirYouTube = $('#check_youtube').is(':checked');

    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/compartir-whatsapp',
        type: 'POST',
        data: {
            id_archivo: id_archivo,
            telefono: telefono,
            incluir_pdf: incluirPDF,
            incluir_editable: incluirEditable,
            incluir_imagenes: incluirImagenes,
            incluir_youtube: incluirYouTube
        },
        dataType: 'json',
        success: function(data) {
            if (data.res) {
                // Cerrar el modal
                $('#compartirWhatsAppModal').modal('hide');
                
                // Abrir WhatsApp en nueva ventana
                window.open(data.whatsapp_url, '_blank');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al compartir por WhatsApp: ' + (data.error || 'Error desconocido'),
                    confirmButtonColor: '#3085d6'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al compartir por WhatsApp. Intente nuevamente.',
                confirmButtonColor: '#3085d6'
            });
        }
    });
}

// Función para mostrar archivos existentes en modo edición
function mostrarArchivosExistentes(adjuntos) {
    // Limpiar previews anteriores
    $('.selected-files').hide().html('');

    adjuntos = adjuntos || {};
    establecerAdjuntoPersistido('pdf', adjuntos.pdf && adjuntos.pdf.url ? adjuntos.pdf.url : null);
    establecerAdjuntoPersistido('editable', adjuntos.editable && adjuntos.editable.url ? adjuntos.editable.url : null);
    
    // Mostrar imágenes existentes
    if (adjuntos.imagenes && adjuntos.imagenes.length > 0) {
        const imagenesPreview = $('#imagenes_file').closest('.file-upload-container').find('.selected-files');
        let html = '<div class="alert alert-info mb-0"><strong>Imágenes actuales:</strong><br><small class="text-muted">Sube nuevas imágenes para reemplazarlas</small><div class="row g-2 mt-2">';
        
        adjuntos.imagenes.forEach((img, index) => {
            html += `
                <div class="col-4">
                    <div class="position-relative">
                        <img src="${_URL}/${img.url}" 
                             class="img-fluid rounded" 
                             style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                             onclick="window.open('${_URL}/${img.url}', '_blank')"
                             title="Click para ver en tamaño completo">
                        <div class="position-absolute top-0 start-0 bg-dark text-white px-2 py-1 rounded-end" style="font-size: 0.7rem;">
                            ${index + 1}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
        imagenesPreview.html(html).show();
    }
}

// Función para editar una ficha técnica
function editarFicha(id) {
    // Mostrar loader
    Swal.fire({
        title: 'Cargando ficha...',
        html: '<div style="display: flex; justify-content: center; align-items: center; padding: 20px;"><div style="width: 60px; height: 60px; border: 6px solid #f3f3f3; border-top: 6px solid #CA3438; border-radius: 50%; animation: spin 1s linear infinite;"></div></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });

    // Obtener datos de la ficha
    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/obtener',
        type: 'POST',
        data: { id_archivo: id },
        dataType: 'json',
        success: function(data) {
            Swal.close();
            
            if (data.res && data.ficha) {
                limpiarFormularioCompleto();

                // Cambiar a la pestaña de nueva ficha
                $('#lista-fichas').removeClass('show active');
                $('#nueva-ficha').addClass('show active');
                
                // Llenar el formulario con los datos existentes
                $('#titulo').val(data.ficha.titulo || '');
                
                // Buscar y seleccionar el producto si existe
                if (data.ficha.id_producto && data.ficha.nombre_producto) {
                    $('#buscar_producto').val(data.ficha.nombre_producto);
                    $('#id_producto').val(data.ficha.id_producto);
                }
                
                // Mostrar archivos existentes
                mostrarArchivosExistentes(data.ficha.adjuntos);
                
                // Llenar YouTube si existe
                if (data.ficha.adjuntos.youtube && data.ficha.adjuntos.youtube.url) {
                    $('#youtube').val(data.ficha.adjuntos.youtube.url);
                }
                
                // Guardar el ID de la ficha para actualizar (no crear nueva)
                $('#id-ficha-editar').val(id);
                
                // Cambiar el texto del botón
                $('#nuevaFichaForm button[type="submit"]').html('<i class="fas fa-save me-2"></i>Actualizar Ficha Técnica');
                
                // Mostrar mensaje informativo
                Swal.fire({
                    icon: 'info',
                    title: 'Modo Edición',
                    html: 'Modifica los campos necesarios y haz clic en "Actualizar Ficha Técnica".<br><br><strong>Nota:</strong> Si subes nuevos archivos, reemplazarán los existentes.',
                    timer: 4000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', 'No se pudo cargar la ficha para editar', 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Error al cargar la ficha. Intente nuevamente.', 'error');
        }
    });
}

// Función para eliminar una ficha técnica
function eliminarFicha(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará COMPLETAMENTE la ficha técnica y todos sus archivos. Esta acción NO se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: _URL + '/ajs/fichas-tecnicas/eliminar',
                type: 'POST',
                data: { id_archivo: id },
                dataType: 'json',
                success: function(data) {
                    if (data.res) {
                        let mensaje = `¡Eliminado completamente! ${data.mensaje}`;
                        if (data.adjuntos_eliminados > 0) {
                            mensaje += `\n\nSe eliminaron ${data.adjuntos_eliminados} registros de adjuntos de la base de datos.`;
                        }
                        if (data.archivos_eliminados > 0) {
                            mensaje += `\n\nSe eliminaron ${data.archivos_eliminados} archivos del servidor.`;
                        }
                        if (data.advertencias && data.advertencias.length > 0) {
                            mensaje += `\n\nAdvertencias: ${data.advertencias.join(', ')}`;
                        }
                        
                        Swal.fire('¡Eliminado!', mensaje, 'success');
                        cargarFichas();
                    } else {
                        Swal.fire('Error', 'Error al eliminar: ' + (data.error || 'Error desconocido'), 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Error al eliminar la ficha técnica. Intente nuevamente.', 'error');
                }
            });
        }
    });
}

// Función para buscar fichas técnicas
function buscarFichas(termino) {
    limpiarTareasRenderizado();
    if (termino.length < 2) {
        cargarFichas();
        return;
    }
    
    const contenedor = $('#lista-archivos-ficha_tecnica');
    
    // Mostrar spinner mientras se busca
    contenedor.html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Buscando fichas técnicas...</p>
        </div>
    `);
    
    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/listar',
        type: 'POST',
        data: { termino: termino },
        dataType: 'json',
        success: function(data) {
            if (data.res) {
                if (data.fichas && data.fichas.length > 0) {
                    renderizarFichas(data.fichas);
                } else {
                    contenedor.html(`
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-warning">
                                <i class="fas fa-search fa-2x mb-3"></i>
                                <h4>Sin resultados</h4>
                                <p>No se encontraron fichas técnicas para "${termino}".</p>
                            </div>
                            <button class="btn btn-outline-primary mt-3" onclick="cargarFichas()">
                                <i class="fas fa-arrow-left me-2"></i>Volver a todas las fichas
                            </button>
                        </div>
                    `);
                }
            } else {
                contenedor.html(`
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h4>Error en la búsqueda</h4>
                            <p>${data.error || 'Error desconocido al buscar fichas técnicas'}</p>
                        </div>
                        <button class="btn btn-outline-primary mt-3" onclick="cargarFichas()">
                            <i class="fas fa-arrow-left me-2"></i>Volver a todas las fichas
                        </button>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            contenedor.html(`
                <div class="col-12 text-center py-5">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h4>Error en la búsqueda</h4>
                        <p>Ocurrió un error al buscar fichas técnicas.</p>
                    </div>
                    <button class="btn btn-outline-primary mt-3" onclick="cargarFichas()">
                        <i class="fas fa-arrow-left me-2"></i>Volver a todas las fichas
                    </button>
                </div>
            `);
        }
    });
}

// Función para formatear fechas
function formatDate(dateString) {
    if (!dateString) return '';
    
    try {
        return new Date(dateString).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateString;
    }
}

function inicializarUploadContainers() {
    $('.file-upload-container').each(function() {
        const container = $(this);
        const input = container.find('input[type="file"]');
        const preview = container.find('.selected-files');
        const selectBtn = container.find('.select-file-btn');
        
        // Evitamos la propagación del evento al contenedor padre
        // cuando se hace clic en el botón de selección
        if (selectBtn.length) {
            selectBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Usamos el método nativo click() para evitar recursión en jQuery
                input[0].click();
            });
        }
        
    container.on('drop', function(e) {
    e.preventDefault();
    container.removeClass('border-primary');
    
    // Si es el input de imágenes, manejar acumulación
    if (input.attr('name') === 'imagenes[]') {
        // Crear un input temporal para manejar los archivos arrastrados
        const tempInput = document.createElement('input');
        tempInput.type = 'file';
        tempInput.files = e.originalEvent.dataTransfer.files;
        
        // Simular el evento change
        const tempJQuery = $(tempInput);
        manejarSeleccionImagenes(tempJQuery, preview);
    } else {
        input[0].files = e.originalEvent.dataTransfer.files;
        mostrarArchivosSeleccionados(input, preview);
    }
});

        
        // Manejador para arrastrar y soltar
        container.on('dragover', function(e) {
            e.preventDefault();
            container.addClass('border-primary');
        });
        
        container.on('dragleave', function() {
            container.removeClass('border-primary');
        });
        
        container.on('drop', function(e) {
            e.preventDefault();
            container.removeClass('border-primary');
            input[0].files = e.originalEvent.dataTransfer.files;
            mostrarArchivosSeleccionados(input, preview);
        });
        
      // Manejador para cambios en el input de archivo
input.on('change', function() {
    // Si es el input de imágenes, manejar acumulación
    if (input.attr('name') === 'imagenes[]') {
        manejarSeleccionImagenes(input, preview);
    } else {
        mostrarArchivosSeleccionados(input, preview);
    }
});

    });
}

// Función para mostrar archivos seleccionados
function mostrarArchivosSeleccionados(input, preview) {
    if (input[0].files.length > 0) {
        // NUEVO: Si son imágenes, mostrar preview visual
        if (input.attr('name') === 'imagenes[]') {
            mostrarPreviewImagenes(input[0].files, preview);
        } else {
            const tipo = input.attr('name') === 'pdf' ? 'pdf' : 'editable';
            const fileNames = Array.from(input[0].files).map(file => escapeHtml(file.name)).join(', ');
            actualizarIntencionEliminacion(tipo, false);
            preview.html(`
                <div class="alert alert-info mb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="text-break">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Archivo seleccionado:</strong> ${fileNames}
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                        data-adjunto-accion="quitar" data-adjunto-tipo="${tipo}"
                        aria-label="Quitar archivo ${tipo === 'pdf' ? 'PDF' : 'editable'} seleccionado">
                        <i class="fas fa-times me-1" aria-hidden="true"></i>Quitar archivo
                    </button>
                </div>
            `);
            preview.show();
        }
    }
}

function establecerAdjuntoPersistido(tipo, url) {
    estadoAdjuntosFicha[tipo].url = url;
    actualizarIntencionEliminacion(tipo, false);
    $(`#${tipo === 'pdf' ? 'pdf_file' : 'editable_file'}`).val('');

    if (url) {
        mostrarAdjuntoPersistido(tipo);
    }
}

function actualizarIntencionEliminacion(tipo, eliminar) {
    estadoAdjuntosFicha[tipo].eliminar = eliminar;
    $(`#eliminar-${tipo}`).val(eliminar ? '1' : '0');
}

function mostrarAdjuntoPersistido(tipo) {
    const estado = estadoAdjuntosFicha[tipo];
    const input = $(`#${tipo === 'pdf' ? 'pdf_file' : 'editable_file'}`);
    const preview = input.closest('.file-upload-container').find('.selected-files');

    if (!estado.url) {
        preview.hide().html('');
        return;
    }

    const nombre = escapeHtml(estado.url.split('/').pop());
    const icono = tipo === 'pdf' ? 'fa-file-pdf text-danger' : 'fa-file text-primary';
    const accionArchivo = tipo === 'pdf' ? 'Ver' : 'Descargar';

    preview.html(`
        <div class="alert alert-info mb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="text-break">
                <i class="fas ${icono} me-2" aria-hidden="true"></i>
                <strong>Archivo actual:</strong> ${nombre}
                <br><small class="text-muted">Sube un nuevo archivo para reemplazarlo</small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="${_URL}/${estado.url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                    <i class="fas ${tipo === 'pdf' ? 'fa-eye' : 'fa-download'} me-1" aria-hidden="true"></i>${accionArchivo}
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                    data-adjunto-accion="quitar" data-adjunto-tipo="${tipo}"
                    aria-label="Quitar archivo ${tipo === 'pdf' ? 'PDF' : 'editable'} actual">
                    <i class="fas fa-times me-1" aria-hidden="true"></i>Quitar archivo
                </button>
            </div>
        </div>
    `).show();
}

function quitarArchivoFicha(tipo) {
    if (!estadoAdjuntosFicha[tipo]) return;

    const input = $(`#${tipo === 'pdf' ? 'pdf_file' : 'editable_file'}`);
    const teniaArchivoSeleccionado = input[0].files.length > 0;
    input.val('');

    if (teniaArchivoSeleccionado) {
        if (estadoAdjuntosFicha[tipo].url && !estadoAdjuntosFicha[tipo].eliminar) {
            mostrarAdjuntoPersistido(tipo);
        } else {
            input.closest('.file-upload-container').find('.selected-files').hide().html('');
        }
        return;
    }

    if (estadoAdjuntosFicha[tipo].url) {
        actualizarIntencionEliminacion(tipo, true);
        input.closest('.file-upload-container').find('.selected-files').html(`
            <div class="alert alert-warning mb-0 d-flex flex-wrap align-items-center justify-content-between gap-2" role="status">
                <span><i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>Se eliminará al guardar</span>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                    data-adjunto-accion="cancelar-eliminacion" data-adjunto-tipo="${tipo}"
                    aria-label="Cancelar eliminación del archivo ${tipo === 'pdf' ? 'PDF' : 'editable'}">
                    Cancelar eliminación
                </button>
            </div>
        `).show();
    } else {
        actualizarIntencionEliminacion(tipo, false);
        input.closest('.file-upload-container').find('.selected-files').hide().html('');
    }
}

function cancelarEliminacionArchivoFicha(tipo) {
    if (!estadoAdjuntosFicha[tipo] || !estadoAdjuntosFicha[tipo].url) return;

    actualizarIntencionEliminacion(tipo, false);
    mostrarAdjuntoPersistido(tipo);
}

function reiniciarEstadoAdjuntosFicha() {
    ['pdf', 'editable'].forEach(function(tipo) {
        estadoAdjuntosFicha[tipo].url = null;
        actualizarIntencionEliminacion(tipo, false);
    });
}

function mostrarFormularioNuevaFicha() {
    limpiarFormularioCompleto();
    $('#lista-fichas').removeClass('show active');
    $('#nueva-ficha').addClass('show active');
}

// NUEVA: Función para mostrar preview de imágenes seleccionadas
function mostrarPreviewImagenes(files, preview) {
    if (files.length === 0) {
        preview.hide();
        return;
    }
    
    let html = '<div class="row g-2 mt-2">';
    let imagenesProcessadas = 0;
    const maxImagenes = Math.min(files.length, 3);
    
    for (let i = 0; i < maxImagenes; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgHtml = `
                    <div class="col-4">
                        <div class="position-relative">
                            <img src="${e.target.result}" 
                                 class="img-fluid rounded" 
                                 style="height: 80px; width: 100%; object-fit: cover;">
                            <div class="position-absolute top-0 start-0 bg-dark text-white px-2 py-1 rounded-end" style="font-size: 0.7rem;">
                                ${i + 1}
                            </div>
                        </div>
                        <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                    </div>
                `;
                
                if (imagenesProcessadas === 0) {
                    html = '<div class="row g-2 mt-2">' + imgHtml;
                } else {
                    html += imgHtml;
                }
                
                imagenesProcessadas++;
                
                if (imagenesProcessadas === maxImagenes) {
                    html += '</div>';
                    
                    if (files.length > 3) {
                        html += `
                            <div class="alert alert-warning mb-0 small mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Solo se procesarán las primeras 3 imágenes (${files.length} seleccionadas)
                            </div>
                        `;
                    }
                    
                    preview.html(html);
                    preview.show();
                }
            };
            reader.readAsDataURL(file);
        }
    }
}

// Variable global para almacenar las imágenes seleccionadas
let imagenesAcumuladas = [];

// NUEVA: Función para manejar la selección acumulativa de imágenes
function manejarSeleccionImagenes(input, preview) {
    const nuevasImagenes = Array.from(input[0].files);
    
    // Agregar las nuevas imágenes al array acumulado
    nuevasImagenes.forEach(imagen => {
        // Verificar que no exceda el límite de 3 imágenes
        if (imagenesAcumuladas.length < 3) {
            // Verificar que no sea una imagen duplicada (mismo nombre y tamaño)
            const yaExiste = imagenesAcumuladas.some(img => 
                img.name === imagen.name && img.size === imagen.size
            );
            
            if (!yaExiste) {
                imagenesAcumuladas.push(imagen);
            }
        }
    });
    
    // Mostrar mensaje si se alcanzó el límite
    if (imagenesAcumuladas.length >= 3 && nuevasImagenes.length > 0) {
        Swal.fire({
            icon: 'info',
            title: 'Límite alcanzado',
            text: 'Ya has seleccionado el máximo de 3 imágenes',
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    // Actualizar el input con todas las imágenes acumuladas
    actualizarInputImagenes(input[0], imagenesAcumuladas);
    
    // Mostrar preview de todas las imágenes acumuladas
    mostrarPreviewImagenesAcumuladas(imagenesAcumuladas, preview);
}

// NUEVA: Función para actualizar el input con las imágenes acumuladas
function actualizarInputImagenes(inputElement, imagenes) {
    // Crear un nuevo DataTransfer para simular la selección múltiple
    const dataTransfer = new DataTransfer();
    
    // Agregar cada imagen al DataTransfer
    imagenes.forEach(imagen => {
        dataTransfer.items.add(imagen);
    });
    
    // Asignar los archivos al input
    inputElement.files = dataTransfer.files;
}

// NUEVA: Función para mostrar preview de imágenes acumuladas con opción de eliminar
function mostrarPreviewImagenesAcumuladas(imagenes, preview) {
    if (imagenes.length === 0) {
        preview.hide();
        return;
    }
    
    // NUEVO: Crear un array para almacenar las promesas de FileReader
    const promesasImagenes = [];
    
    // NUEVO: Crear el HTML base primero
    let html = '<div class="row g-2 mt-2">';
    
    // NUEVO: Agregar placeholders para todas las imágenes
    imagenes.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            html += `
                <div class="col-4" id="imagen-preview-${index}">
                    <div class="position-relative">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="height: 80px; width: 100%;">
                            <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                        </div>
                        <div class="position-absolute top-0 start-0 bg-dark text-white px-2 py-1 rounded-end" style="font-size: 0.7rem;">
                            ${index + 1}
                        </div>
                        <button type="button" 
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" 
                                style="width: 25px; height: 25px; font-size: 0.7rem; line-height: 1;"
                                onclick="eliminarImagenSeleccionada(${index})"
                                title="Eliminar imagen">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                </div>
            `;
        }
    });
    
    html += '</div>';
    
    // NUEVO: Agregar información adicional
    html += `
        <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    ${imagenes.length} de 3 imágenes seleccionadas
                </small>
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm" 
                        onclick="limpiarTodasLasImagenes()">
                    <i class="fas fa-trash me-1"></i>Limpiar todo
                </button>
            </div>
        </div>
    `;
    
    // NUEVO: Mostrar el HTML base inmediatamente
    preview.html(html);
    preview.show();
    
    // NUEVO: Ahora cargar las imágenes una por una
    imagenes.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // NUEVO: Actualizar solo la imagen específica sin tocar el resto del HTML
                const imagenContainer = $(`#imagen-preview-${index}`);
                if (imagenContainer.length > 0) {
                    const imgElement = imagenContainer.find('.bg-light');
                    imgElement.html(`
                        <img src="${e.target.result}" 
                             class="img-fluid rounded" 
                             style="height: 80px; width: 100%; object-fit: cover;">
                    `);
                }
            };
            
            reader.readAsDataURL(file);
        }
    });
}

// NUEVA: Función para eliminar una imagen específica
function eliminarImagenSeleccionada(index) {
    // Eliminar la imagen del array
    imagenesAcumuladas.splice(index, 1);
    
    // Actualizar el input
    const inputElement = document.getElementById('imagenes_file');
    actualizarInputImagenes(inputElement, imagenesAcumuladas);
    
    // Actualizar el preview
    const preview = $('#imagenes_file').closest('.file-upload-container').find('.selected-files');
    mostrarPreviewImagenesAcumuladas(imagenesAcumuladas, preview);
}

// NUEVA: Función para limpiar todas las imágenes
function limpiarTodasLasImagenes() {
    imagenesAcumuladas = [];
    
    // Limpiar el input
    const inputElement = document.getElementById('imagenes_file');
    inputElement.value = '';
    
    // Ocultar preview
    const preview = $('#imagenes_file').closest('.file-upload-container').find('.selected-files');
    preview.hide();
}



// Función para inicializar búsqueda de productos
function inicializarBusquedaProductos() {
    const buscarProductoInput = $('#buscar_producto');
    const resultadosDiv = $('#resultados_productos');
    const idProductoInput = $('#id_producto');
    
    if (!buscarProductoInput.length) return;
    
    // Eliminar eventos previos para evitar duplicados
    buscarProductoInput.off('input');
    
    buscarProductoInput.on('input', function() {
        const termino = $(this).val().trim();
        
        if (termino.length < 2) {
            resultadosDiv.hide();
            return;
        }
        
        $.ajax({
            url: _URL + '/ajs/gestion/productos',
            type: 'POST',
            data: { termino: termino },
            dataType: 'json', // Aseguramos que solo esperamos JSON
            success: function(data) {
                if (data.res && data.productos && data.productos.length > 0) {
                    let html = '';
                    data.productos.forEach(producto => {
                        // Usar data attributes en lugar de onclick inline
                        html += `
                            <a href="javascript:void(0)" 
                               class="list-group-item list-group-item-action producto-item" 
                               data-id="${producto.id_producto}" 
                               data-nombre="${escapeHtml(producto.nombre)}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${escapeHtml(producto.nombre)}</strong>
                                        <br>
                                        <small class="text-muted">Código: ${escapeHtml(producto.codigo || '')}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">Seleccionar</span>
                                </div>
                            </a>
                        `;
                    });
                    
                    resultadosDiv.html(html).show();
                    
                    // Eliminar eventos previos y agregar nuevos usando delegación
                    $(document).off('click', '.producto-item');
                    $(document).on('click', '.producto-item', function(e) {
                        e.preventDefault();
                        const id = $(this).data('id');
                        const nombre = $(this).data('nombre');
                        seleccionarProducto(id, nombre);
                    });
                    
                } else {
                    resultadosDiv.html(`
                        <div class="list-group-item text-center">
                            <p class="mb-0">No se encontraron productos</p>
                        </div>
                    `).show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en búsqueda de productos:', error);
                resultadosDiv.html(`
                    <div class="list-group-item text-center text-danger">
                        <p class="mb-0">Error al buscar productos</p>
                    </div>
                `).show();
            }
        });
    });
    
    // Ocultar resultados al hacer clic fuera
    $(document).off('click.resultadosProductos');
    $(document).on('click.resultadosProductos', function(e) {
        if (!buscarProductoInput.is(e.target) && !resultadosDiv.is(e.target) && resultadosDiv.has(e.target).length === 0) {
            resultadosDiv.hide();
        }
    });
}

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Función para seleccionar un producto
function seleccionarProducto(id, nombre) {
    try {

        
        // Establecer el ID en el campo oculto
        $('#id_producto').val(id);
        
        // Mostrar el nombre del producto seleccionado en el campo de búsqueda
        $('#buscar_producto').val(nombre);
        
        // Ocultar los resultados
        $('#resultados_productos').hide();
    } catch (error) {
        console.error('Error al seleccionar producto:', error);
        alert('Ocurrió un error al seleccionar el producto. Por favor, inténtelo de nuevo.');
    }
}
// Función para limpiar eventos duplicados
function limpiarEventosDuplicados() {
    // Limpiar eventos de búsqueda
    $('#buscar-ficha_tecnica').off('keyup');
    
    // Limpiar eventos de formularios
    $('#nuevaFichaForm').off('submit');
    
    // Limpiar eventos de botones
    $('.file-upload-container').off('click dragover dragleave drop');
    $('.select-file-btn').off('click');
    
    // Limpiar eventos de productos
    $('#buscar_producto').off('input');
    $('.producto-item').off('click');
    
    // console.log('Eventos duplicados limpiados');
}
// Función para guardar una ficha técnica
function guardarFicha(form) {
    // Validar archivos antes de enviar
    if (!validarArchivos()) {
        return false;
    }
    
    // Validar título
    const titulo = $('#titulo').val().trim();
    if (!titulo) {
        Swal.fire('Error', 'El título es obligatorio', 'error');
        return false;
    }

    const formData = new FormData(form);
    const submitBtn = $(form).find('button[type="submit"]');
    const btnText = submitBtn.html();
    
    // Detectar si es edición o creación
    const idFicha = $('#id-ficha-editar').val();
    const esEdicion = idFicha && idFicha !== '';
    const url = esEdicion ? _URL + '/ajs/fichas-tecnicas/actualizar' : _URL + '/ajs/fichas-tecnicas/guardar';
    const mensajeExito = esEdicion ? 'Ficha técnica actualizada correctamente' : 'Ficha técnica guardada correctamente';
    
    submitBtn.prop('disabled', true);
    submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>' + (esEdicion ? 'Actualizando...' : 'Guardando...'));
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(data) {
            if (data.res) {
              Swal.fire({
    icon: 'success',
    title: '¡Éxito!',
    text: mensajeExito,
    confirmButtonColor: '#3085d6'
}).then(() => {
    // NUEVO: Limpiar completamente el formulario
    limpiarFormularioCompleto();
    
    // Limpiar el ID de edición
    $('#id-ficha-editar').val('');
    
    // Restaurar el texto del botón
    submitBtn.html('<i class="fas fa-save me-2"></i>Guardar Ficha Técnica');
    
    // Cambiar a la pestaña de lista
    $('#nueva-ficha').removeClass('show active');
    $('#lista-fichas').addClass('show active');
    
    cargarFichas();
});

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la ficha técnica: ' + (data.error || 'Error desconocido'),
                    confirmButtonColor: '#3085d6'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar la ficha técnica. Intente nuevamente.',
                confirmButtonColor: '#3085d6'
            });
        },
        complete: function() {
            submitBtn.prop('disabled', false);
            submitBtn.html(btnText);
        }
    });
}

// Función para aplicar filtros
function aplicarFiltros() {
    const fechaDesde = $('#filtro_fecha_desde').val();
    const fechaHasta = $('#filtro_fecha_hasta').val();
    const idProducto = $('#filtro_id_producto').val();
    
    let params = {};
    
    if (fechaDesde) {
        params.fecha_desde = fechaDesde;
    }
    
    if (fechaHasta) {
        params.fecha_hasta = fechaHasta;
    }
    
    if (idProducto) {
        params.id_producto = idProducto;
    }
    
    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/listar',
        type: 'POST',
        data: params,
        dataType: 'json',
        success: function(data) {
            if (data.res) {
                renderizarFichas(data.fichas);
                // Cerrar el modal
                $('#filtroModal').modal('hide');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al aplicar filtros: ' + (data.error || 'Error desconocido'),
                    confirmButtonColor: '#3085d6'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al aplicar filtros. Intente nuevamente.',
                confirmButtonColor: '#3085d6'
            });
        }
    });
}

// Función para validar link de YouTube
function validarYouTubeLink() {
    const url = $('#youtube').val().trim();
    if (!url) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ingrese un enlace de YouTube',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    
    if (match && match[2].length === 11) {
        const embedUrl = `https://www.youtube.com/embed/${match[2]}`;
        Swal.fire({
            icon: 'success',
            title: 'Enlace válido',
            html: `
                <p>El enlace de YouTube es válido.</p>
                <div class="ratio ratio-16x9 mt-3">
                    <iframe src="${embedUrl}" allowfullscreen></iframe>
                </div>
            `,
            confirmButtonColor: '#3085d6'
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Enlace inválido',
            text: 'El enlace de YouTube no es válido. Ingrese un enlace completo (ej: https://www.youtube.com/watch?v=XXXX)',
            confirmButtonColor: '#3085d6'
        });
    }
}
// NUEVO: Agregar estilos CSS para el modal de imagen ampliada
if (!document.getElementById('swal-custom-styles')) {
    const style = document.createElement('style');
    style.id = 'swal-custom-styles';
    style.textContent = `
        .swal-wide {
            max-width: 90vw !important;
        }
        .swal-wide .swal2-popup {
            max-width: none !important;
        }
    `;
    document.head.appendChild(style);
}
// Limpiar imágenes acumuladas al enviar formulario exitosamente
function limpiarFormularioImagenes() {
    imagenesAcumuladas = [];
    const inputElement = document.getElementById('imagenes_file');
    if (inputElement) {
        inputElement.value = '';
    }
    const preview = $('#imagenes_file').closest('.file-upload-container').find('.selected-files');
    preview.hide();
}

// NUEVA: Función para limpiar completamente el formulario
function limpiarFormularioCompleto() {
    // 1. Limpiar campos de texto
    $('#titulo').val('');
    $('#buscar_producto').val('');
    $('#id_producto').val('');
    $('#youtube').val('');
    
    // 2. Limpiar campos de archivos
    $('#pdf_file').val('');
    $('#editable_file').val('');
    $('#imagenes_file').val('');
    
    // 3. Limpiar previews de archivos
    $('.selected-files').hide();
    $('.selected-files').html('');
    
    // 4. Limpiar imágenes acumuladas
    imagenesAcumuladas = [];
    
    // 5. Limpiar contenedores de archivos
    $('.file-upload-container').removeClass('border-primary');
    
    // 6. Resetear el formulario HTML
    $('#nuevaFichaForm')[0].reset();
    $('#id-ficha-editar').val('');
    $('#nuevaFichaForm button[type="submit"]').html('<i class="fas fa-save me-2"></i>Guardar Ficha Técnica');
    reiniciarEstadoAdjuntosFicha();
    
    // 7. Limpiar resultados de búsqueda de productos
    $('#resultados_productos').hide();
    
    
}

window.verFicha = verFicha;
window.compartirWhatsApp = compartirWhatsApp;
window.enviarWhatsApp = enviarWhatsApp;
window.marcarTodosArchivos = marcarTodosArchivos;
window.desmarcarTodosArchivos = desmarcarTodosArchivos;
window.eliminarFicha = eliminarFicha;
window.cargarFichas = cargarFichas;
window.aplicarFiltros = aplicarFiltros;
window.validarYouTubeLink = validarYouTubeLink;
window.seleccionarProducto = seleccionarProducto;
window.buscarProductos = buscarProductos;
window.eliminarImagenSeleccionada = eliminarImagenSeleccionada;
window.limpiarTodasLasImagenes = limpiarTodasLasImagenes;
window.limpiarFormularioCompleto = limpiarFormularioCompleto;
window.mostrarFormularioNuevaFicha = mostrarFormularioNuevaFicha;
// NUEVO: Agregar funciones de ampliar imagen al scope global
window.ampliarImagen = ampliarImagen;
window.ampliarImagenAlternativa = ampliarImagenAlternativa;

// Función para reiniciar el módulo de fichas técnicas
window.reiniciarModuloFichas = function() {
    console.log('Reiniciando módulo de fichas técnicas...');
    
    // Limpiar formulario completo
    limpiarFormularioCompleto();
    
    // Limpiar eventos duplicados
    limpiarEventosDuplicados();
    
    // Limpiar contenedores de archivos
    $('.selected-files').hide().html('');
    $('.file-upload-container').removeClass('border-primary');
    
    // Resetear vista a lista de fichas
    $('#nueva-ficha').removeClass('show active');
    $('#lista-fichas').addClass('show active');
    
    // Recargar fichas
    cargarFichas();
};

// Exponer funciones al scope global para que puedan ser llamadas desde onclick
window.editarFicha = editarFicha;
window.verFicha = verFicha;
window.eliminarFicha = eliminarFicha;
window.compartirWhatsApp = compartirWhatsApp;
window.cargarFichas = cargarFichas;
window.reiniciarModuloFichas = reiniciarModuloFichas;
window.eliminarFichasSeleccionadas = eliminarFichasSeleccionadas;
window.actualizarToolbarFichas = actualizarToolbarFichas;
window.ampliarImagen = ampliarImagen;
window.eliminarImagenSeleccionada = eliminarImagenSeleccionada;
window.limpiarTodasLasImagenes = limpiarTodasLasImagenes;
window.mostrarArchivosExistentes = mostrarArchivosExistentes;

})();

</script>
