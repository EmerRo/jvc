<!-- resources\views\fragment-views\cliente\documentos\componentes\formularios\nueva-ficha.php -->
<div class="card border-0 shadow-sm">
    <!--  bg-gradient-to-r from-[#CA3438] to-[#d04a4e] -->
    <div class="card-header text-white py-3" style=" background-image: linear-gradient(to right, #CA3438, #d04a4e);">
        <h5 class="card-title mb-0 fw-bold">Nueva Ficha Técnica</h5>
        <p class="card-subtitle mb-0 opacity-75 small">Ingrese la información y suba los archivos de la ficha técnica
        </p>
    </div>
    <div class="card-body p-4">
        <form id="nuevaFichaForm" enctype="multipart/form-data">
            <input type="hidden" name="tipo" value="ficha_tecnica">
            <input type="hidden" id="id-ficha-editar" name="id_ficha" value="">

            <!-- Sección de Información Básica -->
            <div class="mb-4 pb-2 border-bottom">
                <h6 class="text-dark mb-3 fw-bold">Información Básica</h6>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="titulo" class="form-label fw-medium text-negro">Título de la Ficha <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control border rounded-2 shadow-sm" id="titulo" name="titulo"
                            placeholder="Ingrese el título de la ficha técnica" required>
                        <div class="form-text text-gris small">Ejemplo: Ficha Técnica - Nombre del Producto</div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="id_producto" class="form-label fw-medium text-negro">Producto Relacionado</label>
                        <div class="input-group">
                            <input type="text" class="form-control border rounded-start shadow-sm" id="buscar_producto"
                                placeholder="Buscar producto..." autocomplete="off">
                            <input type="hidden" id="id_producto" name="id_producto">
                            <button class="btn bg-rojo text-white border rounded-end" type="button"
                                onclick="buscarProductos()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="resultados_productos" class="list-group mt-2 shadow-sm position-absolute z-3"
                            style="display: none; max-height: 200px; overflow-y: auto; width: calc(100% - 3rem);"></div>
                        <div class="form-text text-gris small">Relacione esta ficha técnica con un producto específico
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Archivos -->
            <div class="mb-4 pb-2 border-bottom">
                <h6 class="text-dark mb-3 fw-bold">Archivos</h6>

                <div class="row">
                    <!-- PDF Upload -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block fw-medium text-negro">PDF de la Ficha Técnica <span
                                class="text-danger">*</span></label>
                        <div class="file-upload-container p-4 border border-2 border-dashed rounded bg-light text-center position-relative cursor-pointer mb-2"
                            style="transition: all 0.2s ease;">
                            <input type="file" class="d-none" name="pdf" accept=".pdf" id="pdf_file">

                            <div>
                                <div class="rounded-circle bg-[#CA3438]/10 p-3 d-inline-flex justify-content-center align-items-center mb-3"
                                    style="background-color: rgba(202, 52, 56, 0.1);">
                                    <i class="fas fa-file-pdf fa-2x" style="color: #CA3438;"></i>
                                </div>
                                <p class="mb-2 text-negro">Arrastre y suelte su archivo PDF aquí</p>
                                <button type="button" class="btn btn-sm text-white select-file-btn"
                                    style="background-color: #CA3438; border-color: #CA3438;">
                                    <i class="fas fa-folder-open me-2"></i>Seleccionar archivo
                                </button>
                            </div>
                            <div class="selected-files mt-3" style="display: none;"></div>
                        </div>
                        <div class="form-text text-gris small">Tamaño máximo: 4MB | Formato: PDF</div>

                    </div>

                    <!-- Archivo Editable Upload -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label d-block fw-medium text-negro">Archivo Editable (Excel, Word, Corel,
                            Photoshop)</label>

                        <div class="file-upload-container p-4 border border-2 border-dashed rounded bg-light text-center position-relative cursor-pointer mb-2"
                            style="transition: all 0.2s ease;">
                            <input type="file" class="d-none" name="editable" accept=".xlsx,.xls,.doc,.docx,.cdr,.psd,.ai"
                                id="editable_file">

                            <div>
                                <div class="rounded-circle bg-[#CA3438]/10 p-3 d-inline-flex justify-content-center align-items-center mb-3"
                                    style="background-color: rgba(202, 52, 56, 0.1);">
                                    <i class="fas fa-file-image fa-2x" style="color: #CA3438;"></i>
                                </div>
                                <p class="mb-2 text-negro">Arrastre y suelte su archivo editable aquí</p>
                                <button type="button" class="btn btn-sm text-white select-file-btn"
                                    style="background-color: #CA3438; border-color: #CA3438;">
                                    <i class="fas fa-folder-open me-2"></i>Seleccionar archivo
                                </button>
                            </div>
                            <div class="selected-files mt-3" style="display: none;"></div>
                        </div>
                        <div class="form-text text-gris small">Tamaño máximo: 4MB | Formatos: .xlsx, .xls, .doc, .docx, .cdr, .psd,
                            .ai</div>

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
                        <div class="file-upload-container p-4 border border-2 border-dashed rounded bg-light text-center position-relative cursor-pointer mb-2"
                            style="transition: all 0.2s ease;">
                         <input type="file" class="d-none" name="imagenes[]" accept=".jpg,.jpeg,.png,.gif" multiple id="imagenes_file" onchange="filtrarImagenesLigeras(this)">


                            <div>
                                <div class="rounded-circle bg-[#CA3438]/10 p-3 d-inline-flex justify-content-center align-items-center mb-3"
                                    style="background-color: rgba(202, 52, 56, 0.1);">
                                    <i class="fas fa-images fa-2x" style="color: #CA3438;"></i>
                                </div>
                                <p class="mb-2 text-negro">Arrastre y suelte sus imágenes aquí</p>
                                <button type="button" class="btn btn-sm text-white select-file-btn"
                                    style="background-color: #CA3438; border-color: #CA3438;">
                                    <i class="fas fa-folder-open me-2"></i>Seleccionar imágenes
                                </button>
                            </div>
                            <div class="selected-files mt-3" style="display: none;"></div>
                        </div>
                        <div class="form-text text-gris small">Máximo 3 imágenes, 2MB c/u (se comprimen automáticamente)
                            | Formatos: .jpg, .jpeg, .png, .gif</div>

                    </div>

                    <!-- Link de YouTube -->
                    <div class="col-md-6 mb-4">
                        <label for="youtube" class="form-label fw-medium text-negro">Link de YouTube</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #CA3438; color: white;">
                                <i class="fab fa-youtube"></i>
                            </span>
                            <input type="url" class="form-control border" id="youtube" name="youtube"
                                placeholder="https://youtube.com/watch?v=...">
                            <button class="btn text-white" type="button" onclick="validarYouTubeLink()"
                                style="background-color: #CA3438; border-color: #CA3438;">
                                Validar
                            </button>
                        </div>
                        <div class="form-text text-gris small">Ingrese el enlace completo del video de YouTube
                            relacionado</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn border-rojo"
                    onclick="$('#lista-fichas').addClass('show active'); $('#nueva-ficha').removeClass('show active');">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormularioCompleto()">
                        <i class="fas fa-eraser me-2"></i>Limpiar
                    </button>
                    <button type="submit" class="btn text-white" style="background-color: #CA3438; border-color: #CA3438;">
                        <i class="fas fa-save me-2"></i>Guardar Ficha Técnica
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    // NUEVA: Función para filtrar imágenes ligeras (máximo 2MB)
    function filtrarImagenesLigeras(input) {
        const maxSize = 2 * 1024 * 1024; // 2MB en bytes
        const files = Array.from(input.files);
        const imagenesValidas = [];
        const imagenesRechazadas = [];
        
        files.forEach(file => {
            if (file.size <= maxSize) {
                imagenesValidas.push(file);
            } else {
                imagenesRechazadas.push(file);
            }
        });
        
        // Mostrar advertencia si hay imágenes muy pesadas
        if (imagenesRechazadas.length > 0) {
            const nombresRechazados = imagenesRechazadas.map(f => f.name).join(', ');
            Swal.fire({
                icon: 'warning',
                title: 'Imágenes muy pesadas',
                html: `Las siguientes imágenes exceden el límite de 2MB y serán comprimidas automáticamente:<br><br><strong>${nombresRechazados}</strong>`,
                confirmButtonColor: '#3085d6'
            });
        }
        
        // Actualizar el input con solo las imágenes válidas
        if (imagenesValidas.length > 0) {
            const dataTransfer = new DataTransfer();
            imagenesValidas.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }
    }

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
            beforeSend: function () {
                $('#resultados_productos').html(`
                <div class="list-group-item text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <span class="ms-2">Buscando productos...</span>
                </div>
            `).show();
            },
            success: function (data) {
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
            error: function (xhr, status, error) {
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

    // NUEVA: Función para limpiar completamente el formulario
    function limpiarFormularioCompleto() {
        // Limpiar campos de texto
        $('#titulo').val('');
        $('#buscar_producto').val('');
        $('#id_producto').val('');
        $('#youtube').val('');
        
        // Limpiar archivos
        $('#pdf_file').val('');
        $('#editable_file').val('');
        $('#imagenes_file').val('');
        
        // Limpiar previews
        $('.selected-files').hide().html('');
        
        // Limpiar resultados de búsqueda
        $('#resultados_productos').hide().html('');
        
        // Limpiar array de imágenes acumuladas (si existe)
        if (typeof imagenesAcumuladas !== 'undefined') {
            imagenesAcumuladas = [];
        }
        
        // Mostrar confirmación
        Swal.fire({
            icon: 'success',
            title: 'Formulario limpiado',
            text: 'Todos los campos han sido limpiados correctamente',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // NUEVA: Función para mostrar preview de imágenes
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

    // Función para validar archivos - MEJORADA
    function validarArchivos() {
        // Detectar si estamos en modo edición
        const idFichaEditar = document.getElementById('id-ficha-editar');
        const esEdicion = idFichaEditar && idFichaEditar.value !== '';
        
        // Validar PDF (REQUERIDO solo en creación, opcional en edición)
        const pdfInput = document.getElementById('pdf_file');
        if (!esEdicion && pdfInput.files.length === 0) {
            Swal.fire('Error', 'El archivo PDF es obligatorio', 'error');
            return false;
        }

        // Si se está subiendo un PDF, validar su tamaño
        if (pdfInput.files.length > 0) {
            const pdfFile = pdfInput.files[0];
            if (pdfFile.size > 4 * 1024 * 1024) {
                Swal.fire('Error', 'El archivo PDF excede el tamaño máximo de 4MB', 'error');
                return false;
            }
        }

        // Validar imágenes - VALIDACIÓN MEJORADA
        const imagenesInput = document.getElementById('imagenes_file');
        if (imagenesInput.files.length > 0) {
            // Validar máximo 3 imágenes
            if (imagenesInput.files.length > 3) {
                Swal.fire('Error', 'Solo se pueden subir máximo 3 imágenes', 'error');
                return false;
            }

            // NUEVO: Validar cada imagen individualmente
            for (let i = 0; i < imagenesInput.files.length; i++) {
                const imagen = imagenesInput.files[i];
                
                // Verificar que el archivo sea válido
                if (!imagen || imagen.size === 0) {
                    Swal.fire('Error', `La imagen ${imagen.name} está corrupta o vacía`, 'error');
                    return false;
                }
                
                // Validar tipo de archivo
                if (!imagen.type.startsWith('image/')) {
                    Swal.fire('Error', `El archivo ${imagen.name} no es una imagen válida`, 'error');
                    return false;
                }
                
                // Validar tamaño (10MB antes de compresión)
                if (imagen.size > 10 * 1024 * 1024) {
                    Swal.fire('Error', `La imagen ${imagen.name} es demasiado grande (máximo 10MB antes de compresión)`, 'error');
                    return false;
                }
            }
        }

        // Validar archivo editable
        const editableInput = document.getElementById('editable_file');
        if (editableInput.files.length > 0) {
            const editableFile = editableInput.files[0];
            if (editableFile.size > 4 * 1024 * 1024) {
                Swal.fire('Error', 'El archivo editable excede el tamaño máximo de 4MB', 'error');
                return false;
            }

            // Validar extensión
            const extension = editableFile.name.split('.').pop().toLowerCase();
            const extensionesPermitidas = ['xlsx', 'xls', 'doc', 'docx', 'cdr', 'psd', 'ai'];
            if (!extensionesPermitidas.includes(extension)) {
                Swal.fire('Error', 'Formato de archivo no permitido. Solo: Excel (.xlsx, .xls), Word (.doc, .docx), Corel (.cdr), Photoshop (.psd), Illustrator (.ai)', 'error');
                return false;
            }
        }

        return true;
    }

    // Función para validar el enlace de YouTube
    function validarYouTubeLink() {
        const youtubeInput = $('#youtube');
        const url = youtubeInput.val().trim();
        
        if (!url) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor, ingrese un enlace de YouTube',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Patrón para validar URLs de YouTube
        const youtubePattern = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+/;
        
        if (!youtubePattern.test(url)) {
            Swal.fire({
                icon: 'error',
                title: 'Enlace inválido',
                text: 'Por favor, ingrese un enlace válido de YouTube',
                confirmButtonColor: '#3085d6'
            });
            youtubeInput.focus();
            return;
        }
        
        // Si es válido, mostrar confirmación
        Swal.fire({
            icon: 'success',
            title: 'Enlace válido',
            text: 'El enlace de YouTube ha sido validado correctamente',
            timer: 1500,
            showConfirmButton: false
        });
    }
</script>