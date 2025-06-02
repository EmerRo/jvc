<!-- resources\views\fragment-views\cliente\documentos\componentes\formularios\nueva-ficha.php -->
<div class="card border-0 shadow-sm">
    <!--  bg-gradient-to-r from-[#CA3438] to-[#d04a4e] -->
    <div class="card-header text-white py-3" style=" background-image: linear-gradient(to right, #CA3438, #d04a4e);">
        <h5 class="card-title mb-0 fw-bold">Nueva Ficha Técnica</h5>
        <p class="card-subtitle mb-0 opacity-75 small">Ingrese la información y suba los archivos de la ficha técnica</p>
    </div>
    <div class="card-body p-4">
        <form id="nuevaFichaForm" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="ficha_tecnica">
            
            <!-- Sección de Información Básica -->
            <div class="mb-4 pb-2 border-bottom">
                <h6 class="text-dark mb-3 fw-bold">Información Básica</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="titulo" class="form-label fw-medium text-negro">Título de la Ficha <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo" name="titulo" placeholder="Ingrese el título de la ficha técnica" required>
                        <div class="form-text text-gris small">Ejemplo: Ficha Técnica - Nombre del Producto</div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label for="id_producto" class="form-label fw-medium text-negro">Producto Relacionado</label>
                        <div class="input-group">
                            <input type="text" class="form-control border rounded-start shadow-sm" id="buscar_producto" placeholder="Buscar producto..." autocomplete="off">
                            <input type="hidden" id="id_producto" name="id_producto">
                            <button class="btn bg-rojo text-white border rounded-end" type="button" onclick="buscarProductos()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="resultados_productos" class="list-group mt-2 shadow-sm position-absolute z-3" style="display: none; max-height: 200px; overflow-y: auto; width: calc(100% - 3rem);"></div>
                        <div class="form-text text-gris small">Relacione esta ficha técnica con un producto específico</div>
                    </div>
                </div>
            </div>
            
            <!-- Sección de Archivos -->
            <div class="mb-4 pb-2 border-bottom">
                <h6 class="text-dark mb-3 fw-bold">Archivos</h6>
                
                <div class="row">
                    <!-- PDF Upload -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block fw-medium text-negro">PDF de la Ficha Técnica <span class="text-danger">*</span></label>
                        <div class="file-upload-container p-4 border border-2 border-dashed rounded bg-light text-center position-relative cursor-pointer mb-2" style="transition: all 0.2s ease;">
                            <input type="file" class="d-none" name="pdf" accept=".pdf" id="pdf_file" required>
                            <div>
                                <div class="rounded-circle bg-[#CA3438]/10 p-3 d-inline-flex justify-content-center align-items-center mb-3" style="background-color: rgba(202, 52, 56, 0.1);">
                                    <i class="fas fa-file-pdf fa-2x" style="color: #CA3438;"></i>
                                </div>
                                <p class="mb-2 text-negro">Arrastre y suelte su archivo PDF aquí</p>
                                <button type="button" class="btn btn-sm text-white select-file-btn" style="background-color: #CA3438; border-color: #CA3438;">
                                    <i class="fas fa-folder-open me-2"></i>Seleccionar archivo
                                </button>
                            </div>
                            <div class="selected-files mt-3" style="display: none;"></div>
                        </div>
                        <div class="form-text text-gris small">Tamaño máximo: 10MB | Formato: PDF</div>
                    </div>
                    
                    <!-- Archivo Editable Upload -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block fw-medium text-negro">Archivo Editable (Corel, Photoshop)</label>
                        <div class="file-upload-container p-4 border border-2 border-dashed rounded bg-light text-center position-relative cursor-pointer mb-2" style="transition: all 0.2s ease;">
                            <input type="file" class="d-none" name="editable" accept=".cdr,.psd,.ai" id="editable_file">
                            <div>
                                <div class="rounded-circle bg-[#CA3438]/10 p-3 d-inline-flex justify-content-center align-items-center mb-3" style="background-color: rgba(202, 52, 56, 0.1);">
                                    <i class="fas fa-file-image fa-2x" style="color: #CA3438;"></i>
                                </div>
                                <p class="mb-2 text-negro">Arrastre y suelte su archivo editable aquí</p>
                                <button type="button" class="btn btn-sm text-white select-file-btn" style="background-color: #CA3438; border-color: #CA3438;">
                                    <i class="fas fa-folder-open me-2"></i>Seleccionar archivo
                                </button>
                            </div>
                            <div class="selected-files mt-3" style="display: none;"></div>
                        </div>
                        <div class="form-text text-gris small">Tamaño máximo: 50MB | Formatos: .cdr, .psd, .ai</div>
                    </div>
                </div>
            </div>
            
            <!-- Sección de Multimedia -->
            <div class="mb-4">
                <h6 class="text-dark mb-3 fw-bold">Multimedia</h6>
                
                <div class="row">
                    <!-- Imágenes Upload -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block fw-medium text-negro">Imágenes del Producto</label>
                        <div class="file-upload-container p-4 border border-2 border-dashed rounded bg-light text-center position-relative cursor-pointer mb-2" style="transition: all 0.2s ease;">
                            <input type="file" class="d-none" name="imagenes[]" accept=".jpg,.jpeg,.png" multiple id="imagenes_file">
                            <div>
                                <div class="rounded-circle bg-[#CA3438]/10 p-3 d-inline-flex justify-content-center align-items-center mb-3" style="background-color: rgba(202, 52, 56, 0.1);">
                                    <i class="fas fa-images fa-2x" style="color: #CA3438;"></i>
                                </div>
                                <p class="mb-2 text-negro">Arrastre y suelte sus imágenes aquí</p>
                                <button type="button" class="btn btn-sm text-white select-file-btn" style="background-color: #CA3438; border-color: #CA3438;">
                                    <i class="fas fa-folder-open me-2"></i>Seleccionar imágenes
                                </button>
                            </div>
                            <div class="selected-files mt-3" style="display: none;"></div>
                        </div>
                        <div class="form-text text-gris small">Tamaño máximo total: 20MB | Formatos: .jpg, .jpeg, .png</div>
                    </div>
                    
                    <!-- Link de YouTube -->
                    <div class="col-md-6 mb-4">
                        <label for="youtube_link" class="form-label fw-medium text-negro">Link de YouTube</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #CA3438; color: white;">
                                <i class="fab fa-youtube"></i>
                            </span>
                            <input type="url" class="form-control border" id="youtube_link" name="youtube_link" placeholder="https://youtube.com/watch?v=...">
                            <button class="btn text-white" type="button" onclick="validarYouTubeLink()" style="background-color: #CA3438; border-color: #CA3438;">
                                Validar
                            </button>
                        </div>
                        <div class="form-text text-gris small">Ingrese el enlace completo del video de YouTube relacionado</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" onclick="$('#lista-fichas').addClass('show active'); $('#nueva-ficha').removeClass('show active');">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn text-white" style="background-color: #CA3438; border-color: #CA3438;">
                    <i class="fas fa-save me-2"></i>Guardar Ficha Técnica
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// Función para buscar productos
function buscarProductos() {
    const termino = $('#buscar_producto').val().trim();
    
    if (termino.length < 2) {
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: 'Ingrese al menos 2 caracteres para buscar',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    $.ajax({
        url: _URL + '/ajs/gestion/productos',
        type: 'POST',
        data: { termino: termino },
        dataType: 'json', // Aseguramos que solo esperamos JSON
        beforeSend: function() {
            $('#resultados_productos').html(`
                <div class="list-group-item text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <span class="ms-2">Buscando productos...</span>
                </div>
            `).show();
        },
        success: function(data) {
            if (data.res && data.productos.length > 0) {
                let html = '';
                data.productos.forEach(producto => {
                    // Escapamos TODAS las comillas y caracteres especiales para evitar problemas
                    const nombreProducto = producto.nombre
                        .replace(/\\/g, '\\\\')
                        .replace(/'/g, "\\'")
                        .replace(/"/g, '\\"')
                        .replace(/\n/g, '\\n')
                        .replace(/\r/g, '\\r')
                        .replace(/\t/g, '\\t');
                    
                    html += `
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action" 
                           onclick="seleccionarProducto(${producto.id_producto}, '${nombreProducto}')">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${producto.nombre}</strong>
                                    <br>
                                    <small class="text-muted">Código: ${producto.codigo}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Seleccionar</span>
                            </div>
                        </a>
                    `;
                });
                $('#resultados_productos').html(html).show();
            } else {
                $('#resultados_productos').html(`
                    <div class="list-group-item text-center">
                        <p class="mb-0">No se encontraron productos</p>
                    </div>
                `).show();
            }
        },
        error: function(xhr, status, error) {
            $('#resultados_productos').html(`
                <div class="list-group-item text-center text-danger">
                    <p class="mb-0">Error al buscar productos</p>
                </div>
            `).show();
        }
    });
}

// Función para seleccionar un producto
function seleccionarProducto(id, nombre) {
    // Establecer el ID en el campo oculto
    $('#id_producto').val(id);
    
    // Mostrar el nombre del producto seleccionado en el campo de búsqueda
    $('#buscar_producto').val(nombre);
    
    // Ocultar los resultados
    $('#resultados_productos').hide();
}
</script>