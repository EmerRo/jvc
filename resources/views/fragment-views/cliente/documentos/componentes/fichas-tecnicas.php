<!-- resources/views/fragment-views/cliente/documentos/componentes/fichas-tecnicas.php -->

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
    
    /* NUEVOS ESTILOS PARA EVITAR DESBORDAMIENTO */
    
    /* Contenedor de la tarjeta */
    .card {
        overflow: hidden;
    }
    
    /* Header de la tarjeta */
    .card-header {
        overflow: hidden;
    }
    
    /* Título de la tarjeta */
    .card-title {
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
        line-height: 1.3;
    }
    
    /* Badge del producto */
    .badge {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal !important;
        line-height: 1.2;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }
    
    /* Contenedor de badges */
    .card-header .mt-1 {
        overflow: hidden;
        word-wrap: break-word;
    }
    
    /* Asegurar que el contenido no se desborde */
    .card-body {
        overflow: hidden;
    }
    
    /* Texto pequeño en la parte inferior */
    .card-body small {
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    
    /* Responsive: En pantallas pequeñas, hacer el texto aún más pequeño */
    @media (max-width: 768px) {
        .badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }
        
        .card-title {
            font-size: 1rem;
        }
    }
</style>
<div class="tab-content" id="fichasTabsContent">
    <!-- Navegación entre Lista y Nueva Ficha -->
    <div class="d-flex mb-4 gap-2">
        <button class="btn border-rojo" onclick="$('#lista-fichas').addClass('show active'); $('#nueva-ficha').removeClass('show active');">
            <i class="fas fa-list me-2"></i>Lista de Fichas
        </button>
        <button class="btn bg-rojo text-white" onclick="$('#nueva-ficha').addClass('show active'); $('#lista-fichas').removeClass('show active');">
            <i class="fas fa-plus me-2"></i>Subir Nueva
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
    if (window.ModuloFichasTecnicas) {
        console.log('Módulo de Fichas Técnicas ya inicializado');
        return;
    }
    
    // Marcar el módulo como inicializado
    window.ModuloFichasTecnicas = true;
// Esperar a que el documento esté listo
$(document).ready(function () {
    limpiarEventosDuplicados();
    // Inicializar el módulo de fichas técnicas
    console.log('Inicializando módulo de Fichas Técnicas...');
    
    // Cargar las fichas técnicas al inicio
    // cargarFichas();
    
    // Retrasar la carga inicial de fichas para dar tiempo a PDF.js de inicializarse
    setTimeout(function() {
        cargarFichas();
    }, 300);
    // Botón Nueva Ficha
    $('#nueva-ficha-btn').on('click', function() {
        // Cambiar a la pestaña de nueva ficha
        $('#lista-fichas').removeClass('show active');
        $('#nueva-ficha').addClass('show active');
    });
    
    // Inicializar búsqueda
    $('#buscar-ficha_tecnica').on('keyup', function() {
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
                        $('#lista-fichas').removeClass('show active');
                        $('#nueva-ficha').addClass('show active');
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

function renderizarFichas(fichas) {
    const contenedor = $('#lista-archivos-ficha_tecnica');
    let html = '';
    
    fichas.forEach(ficha => {
        html += `
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">${ficha.titulo || 'Sin título'}</h5>
                        <div class="mt-1">
                           <!-- <span class="badge bg-primary">v${ficha.version || '1.0'}</span> -->
                         ${ficha.id_producto ? `<span class="badge bg-secondary ms-1" style="color: #CA3438" title="${escapeHtml(ficha.nombre_producto || 'Producto asociado')}">${ficha.nombre_producto || 'Producto asociado'}</span>` : ''}
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
                                    <button class="btn btn-sm btn-outline-primary" onclick="verFicha(${ficha.id_archivo})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="compartirWhatsApp(${ficha.id_archivo})">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarFicha(${ficha.id_archivo})">
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

}

function getPreviewHTML(ficha) {
    if (ficha.tipo_adjunto === 'youtube') {
        return `<div class="ratio ratio-16x9">
            <iframe src="${getYouTubeEmbedUrl(ficha.ruta_adjunto)}" allowfullscreen></iframe>
        </div>`;
    } else if (ficha.tipo_adjunto === 'pdf') {
        // Generar un ID único para el canvas
        const canvasId = `pdf-preview-${ficha.id_archivo}`;
        
        // Inicializar la carga del PDF después de que se renderice el HTML
        // Aumentar el delay para evitar renderizados múltiples
        setTimeout(() => {
            renderPdfPreview(ficha.ruta_adjunto, canvasId);
        }, 200 + Math.random() * 100); // Delay aleatorio para evitar colisiones
        
        return `<div class="document-preview">
            <canvas id="${canvasId}" class="pdf-preview-canvas"></canvas>
        </div>`;
    } else if (ficha.tipo_adjunto === 'imagen') {
        return `<div class="document-preview">
            <img src="${ficha.ruta_adjunto}" class="img-fluid" alt="${ficha.adjunto_principal || 'Vista previa'}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>`;
    }
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
    });
    activeRenderTasks.clear();
    console.log('Todas las tareas de renderizado han sido canceladas');
}
// Variable global para rastrear las tareas de renderizado activas
const activeRenderTasks = new Map();

// Función para renderizar la vista previa del PDF (versión mejorada)
function renderPdfPreview(pdfUrl, canvasId) {
    // Verificar si pdfjsLib está disponible
    if (!window.pdfjsLib || typeof window.pdfjsLib.getDocument !== 'function') {
        console.log('PDF.js aún no está cargado completamente, reintentando en 100ms...');
        setTimeout(() => renderPdfPreview(pdfUrl, canvasId), 100);
        return;
    }
    
    try {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error('Canvas no encontrado:', canvasId);
            return;
        }
        
        // Cancelar cualquier tarea de renderizado activa para este canvas
        if (activeRenderTasks.has(canvasId)) {
            console.log('Cancelando tarea de renderizado anterior para:', canvasId);
            activeRenderTasks.get(canvasId).cancel();
            activeRenderTasks.delete(canvasId);
        }
        
        // Cargar el documento PDF usando la API de promesas
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        
        // Guardar la tarea de carga para poder cancelarla si es necesario
        activeRenderTasks.set(canvasId, loadingTask);
        
        loadingTask.promise.then(function(pdf) {
            // Verificar si la tarea fue cancelada
            if (!activeRenderTasks.has(canvasId)) {
                console.log('Tarea cancelada para:', canvasId);
                return;
            }
            
            // Obtener la primera página
            return pdf.getPage(1);
        }).then(function(page) {
            // Verificar nuevamente si la tarea fue cancelada
            if (!activeRenderTasks.has(canvasId)) {
                console.log('Tarea cancelada para:', canvasId);
                return;
            }
            
            const canvas = document.getElementById(canvasId);
            if (!canvas) {
                console.error('Canvas no encontrado durante el renderizado:', canvasId);
                activeRenderTasks.delete(canvasId);
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
            const offsetY = 0;
            
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
            
            // Crear la tarea de renderizado
            const renderTask = page.render(renderContext);
            
            // Actualizar la tarea activa con la tarea de renderizado
            activeRenderTasks.set(canvasId, renderTask);
            
            // Renderizar la página
            return renderTask.promise;
        }).then(function() {
            console.log('PDF renderizado correctamente para:', canvasId);
            // Limpiar la tarea completada
            activeRenderTasks.delete(canvasId);
        }).catch(function(error) {
            // Limpiar la tarea con error
            activeRenderTasks.delete(canvasId);
            
            // Solo mostrar error si no fue una cancelación
            if (error.name !== 'RenderingCancelledException') {
                console.error('Error al renderizar el PDF:', error);
                // Mostrar un icono de PDF en caso de error
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
            if (data.res) {
                mostrarModalFicha(data.ficha, data.adjuntos);
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
    const modal = $('#verArchivoModal');
    const modalTitle = modal.find('.modal-title');
    const modalBody = $('#verArchivoModalBody');
    const descargarBtn = $('#descargarArchivoBtn');
    
    modalTitle.text(ficha.titulo || 'Ficha Técnica');
    
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
                    <!--    <p><strong>Versión:</strong> ${ficha.version || '1.0'}</p> -->
                        <p><strong>Fecha:</strong> ${formatDate(ficha.fecha_actualizacion)}</p>
                        ${ficha.nombre_producto ? `<p><strong>Producto:</strong> ${ficha.nombre_producto}</p>` : ''}
                    </div>
                </div>
                
                <div class="card mt-0">
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
    
    modalBody.html(html);
    
    // Configurar botón de descarga
    const pdfAdjunto = adjuntos.find(adj => adj.tipo_adjunto === 'pdf');
    if (pdfAdjunto) {
        descargarBtn.on('click', function() {
            window.open(pdfAdjunto.ruta_adjunto, '_blank');
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
    // Buscar el adjunto principal
    const principal = adjuntos.find(adj => adj.es_principal === '1');
    
    if (principal) {
        if (principal.tipo_adjunto === 'pdf') {
            return `
                <div class="ratio ratio-16x9">
                    <iframe src="${principal.ruta_adjunto}" allowfullscreen></iframe>
                </div>
            `;
        } else if (principal.tipo_adjunto === 'youtube') {
            return `
                <div class="ratio ratio-16x9">
                    <iframe src="${getYouTubeEmbedUrl(principal.ruta_adjunto)}" allowfullscreen></iframe>
                </div>
            `;
        } else if (principal.tipo_adjunto === 'imagen') {
            return `
                <div class="text-center">
                    <img src="${principal.ruta_adjunto}" class="img-fluid rounded" alt="${principal.nombre_adjunto || 'Vista previa'}">
                </div>
            `;
        }
    }
    
    // Si no hay adjunto principal, mostrar la primera imagen o un placeholder
    const imagen = adjuntos.find(adj => adj.tipo_adjunto === 'imagen');
    if (imagen) {
        return `
            <div class="text-center">
                <img src="${imagen.ruta_adjunto}" class="img-fluid rounded" alt="${imagen.nombre_adjunto || 'Vista previa'}">
            </div>
        `;
    }
    
    return `
        <div class="text-center p-5 bg-light rounded">
            <i class="fas fa-file-alt fa-5x text-primary mb-3"></i>
            <h4>Ficha Técnica</h4>
            <p class="text-muted">No hay vista previa disponible</p>
        </div>
    `;
}

// Función para obtener la lista de adjuntos
function getListaAdjuntos(adjuntos) {
    if (!adjuntos || adjuntos.length === 0) {
        return '<li class="list-group-item">No hay archivos adjuntos</li>';
    }
    
    let html = '';
    
    // Agrupar adjuntos por tipo
    const grupos = {
        pdf: { icon: 'fas fa-file-pdf text-danger', title: 'PDF' },
        editable: { icon: 'fas fa-file-image text-primary', title: 'Archivo Editable' },
        imagen: { icon: 'fas fa-image text-info', title: 'Imagen' },
        youtube: { icon: 'fab fa-youtube text-danger', title: 'Video de YouTube' }
    };
    
    Object.keys(grupos).forEach(tipo => {
        const tipoAdjuntos = adjuntos.filter(adj => adj.tipo_adjunto === tipo);
        
        if (tipoAdjuntos.length > 0) {
            tipoAdjuntos.forEach(adj => {
                // Determinar el icono del botón según el tipo
                const iconoBoton = adj.tipo_adjunto === 'youtube' ? 'fas fa-eye' : 'fas fa-download';
                const tituloBoton = adj.tipo_adjunto === 'youtube' ? 'Ver video' : 'Descargar';
                
                // Truncar nombre si es muy largo (especialmente para URLs de YouTube)
                let nombreMostrar = adj.nombre_adjunto || 'Archivo';
                if (adj.tipo_adjunto === 'youtube' && nombreMostrar.length > 50) {
                    nombreMostrar = nombreMostrar.substring(0, 47) + '...';
                }
                
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-2" style="min-width: 0;">
                            <i class="${grupos[tipo].icon} me-2"></i>
                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${escapeHtml(adj.nombre_adjunto || 'Archivo')}">${escapeHtml(nombreMostrar)}</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="window.open('${adj.ruta_adjunto}', '_blank')" title="${tituloBoton}">
                                <i class="${iconoBoton}"></i>
                            </button>
                        </div>
                    </li>
                `;
            });
        }
    });
    
    return html;
}

// Función para compartir por WhatsApp
function compartirWhatsApp(id) {
    $('#id_archivo_compartir').val(id);
    $('#compartirWhatsAppModal').modal('show');
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
    
    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/compartir-whatsapp',
        type: 'POST',
        data: {
            id_archivo: id_archivo,
            telefono: telefono
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

// Función para eliminar una ficha técnica
function eliminarFicha(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
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
                        Swal.fire(
                            '¡Eliminado!',
                            'La ficha técnica ha sido eliminada.',
                            'success'
                        );
                        cargarFichas();
                    } else {
                        Swal.fire(
                            'Error',
                            'Error al eliminar la ficha técnica: ' + (data.error || 'Error desconocido'),
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error',
                        'Error al eliminar la ficha técnica. Intente nuevamente.',
                        'error'
                    );
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
        
        // Manejador para hacer clic en el contenedor (área de arrastrar y soltar)
        container.on('click', function(e) {
            // Verificamos que el clic no sea en botones o elementos interactivos
            if ($(e.target).is('button') || 
                $(e.target).closest('button').length || 
                $(e.target).is('input') || 
                $(e.target).closest('input').length) {
                return;
            }
            
            // Usamos el método nativo click() para evitar recursión en jQuery
            input[0].click();
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
            mostrarArchivosSeleccionados(input, preview);
        });
    });
}

// Función para mostrar archivos seleccionados
function mostrarArchivosSeleccionados(input, preview) {
    if (input[0].files.length > 0) {
        const fileNames = Array.from(input[0].files).map(file => file.name).join(', ');
        preview.html(`
            <div class="alert alert-info mb-0">
                <i class="fas fa-check-circle me-2"></i>
                Archivos seleccionados: ${fileNames}
            </div>
        `);
        preview.show();
    }
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
        console.log('Seleccionando producto:', id, nombre);
        
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
    
    console.log('Eventos duplicados limpiados');
}
// Función para guardar una ficha técnica
function guardarFicha(form) {
    const formData = new FormData(form);
    const submitBtn = $(form).find('button[type="submit"]');
    const btnText = submitBtn.html();
    
    submitBtn.prop('disabled', true);
    submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
    
    $.ajax({
        url: _URL + '/ajs/fichas-tecnicas/guardar',
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
                    text: 'Ficha técnica guardada correctamente',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    form.reset();
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
    const url = $('#youtube_link').val().trim();
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
window.verFicha = verFicha;
window.compartirWhatsApp = compartirWhatsApp;
window.eliminarFicha = eliminarFicha;
window.cargarFichas = cargarFichas;
window.aplicarFiltros = aplicarFiltros;
window.validarYouTubeLink = validarYouTubeLink;
window.seleccionarProducto = seleccionarProducto;
window.buscarProductos = buscarProductos;
})();
</script>
