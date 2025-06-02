<div class="container mt-4">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h6 class="page-title">Editor de Plantilla de Informes</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Personalizar Plantilla</h5>
                    <div>
                        <button type="button" id="btn-preview" class="btn border-rojo me-2">
                            <i class="fa fa-eye"></i> Vista Previa
                        </button>
                        <button type="button" id="btn-save" class="btn bg-rojo text-white">
                            <i class="fa fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formInformeTemplate" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="titulo_template" class="form-label">Título por Defecto</label>
                                <input type="text" class="form-control" id="titulo_template" name="titulo_template" value="INFORME">
                            </div>
                        </div>
                        
                        <!-- Secciones para las imágenes -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="header_image" class="form-label">Imagen de Encabezado</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="header_image" name="header_image" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-header">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Recomendado: imagen PNG de 210mm x 40mm (ancho completo A4)</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="header-preview-container" class="text-center">
                                        <img id="header-preview" src="/placeholder.svg" alt="Vista previa del encabezado" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="header-placeholder" class="text-muted">No se ha seleccionado ninguna imagen</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="footer_image" class="form-label">Imagen de Pie de Página</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="footer_image" name="footer_image" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-footer">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Recomendado: imagen PNG de 210mm x 30mm (ancho completo A4)</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="footer-preview-container" class="text-center">
                                        <img id="footer-preview" src="/placeholder.svg" alt="Vista previa del pie de página" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="footer-placeholder" class="text-muted">No se ha seleccionado ninguna imagen</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Contenido por Defecto</label>
                                <!-- Contenedor para el editor -->
                                <div id="editor-container-template" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
                                    <!-- El editor Quill se cargará aquí -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Vista Previa de la Plantilla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    let templateEditor = null;
    let headerImageChanged = false;
    let footerImageChanged = false;
    let currentHeaderImage = null;
    let currentFooterImage = null;
    
    $(document).ready(function() {
        // Inicializar el editor Quill
        initializeTemplateEditor();
        
        // Cargar el contenido de la plantilla desde la base de datos
        cargarContenidoTemplate();
        
        // Manejar el envío del formulario usando el botón de guardar
        $("#btn-save").on("click", function() {
            guardarTemplate();
        });
        
        // Manejar la vista previa
        $("#btn-preview").on("click", function() {
            mostrarVistaPrevia();
        });
        
        // Manejar la vista previa de las imágenes seleccionadas
        $("#header_image").on("change", function() {
            previewImage(this, "header-preview", "header-placeholder");
            headerImageChanged = true;
        });
        
        $("#footer_image").on("change", function() {
            previewImage(this, "footer-preview", "footer-placeholder");
            footerImageChanged = true;
        });
        
        // Manejar los botones de restablecer
        $("#reset-header").on("click", function() {
            resetImage("header_image", "header-preview", "header-placeholder", currentHeaderImage);
        });
        
        $("#reset-footer").on("click", function() {
            resetImage("footer_image", "footer-preview", "footer-placeholder", currentFooterImage);
        });
    });
    
    function previewImage(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
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
        }
    }
    
    function initializeTemplateEditor() {
        try {
            console.log('Inicializando editor Quill...');
            
            if (templateEditor) {
                console.log('El editor ya está inicializado');
                return;
            }

            // Registrar formatos personalizados para mejor manejo de HTML
            const Block = Quill.import('blots/block');
            const Inline = Quill.import('blots/inline');

            // Registrar formato personalizado para strong
            class BoldBlot extends Inline {}
            BoldBlot.blotName = 'bold';
            BoldBlot.tagName = 'strong';
            Quill.register(BoldBlot);
            
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

            console.log('Editor Quill inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar Quill:', error);
        }
    }
    
    function cargarContenidoTemplate() {
        // Mostrar indicador de carga
        if (templateEditor) {
            templateEditor.setText('Cargando contenido...');
        }
        
        // Realizar petición AJAX para obtener el contenido de la plantilla
        $.ajax({
            url: _URL + "/ajs/informe/obtener-template",
            method: "GET",
            dataType: 'json',
            success: function(data) {
                try {
                    console.log("Respuesta completa:", data);
                    
                    if (data && data.success) {
                        // Verificar si hay contenido
                        if (data.contenido) {
                            console.log("Contenido recibido (primeros 100 caracteres):", data.contenido.substring(0, 100));
                            
                            // Cargar el contenido HTML en el editor
                            templateEditor.root.innerHTML = data.contenido;
                            templateEditor.update();
                        } else {
                            console.warn("El contenido está vacío");
                            templateEditor.setText('');
                        }
                        
                        // Actualizar el título si existe
                        if (data.titulo) {
                            $("#titulo_template").val(data.titulo);
                        }
                        
                        // Cargar las imágenes
                        if (data.header_image) {
                            currentHeaderImage = data.header_image;
                            $("#header-preview").attr("src", data.header_image);
                            $("#header-preview").show();
                            $("#header-placeholder").hide();
                            console.log("Imagen de encabezado cargada:", data.header_image);
                        }

                        if (data.footer_image) {
                            currentFooterImage = data.footer_image;
                            $("#footer-preview").attr("src", data.footer_image);
                            $("#footer-preview").show();
                            $("#footer-placeholder").hide();
                            console.log("Imagen de pie de página cargada:", data.footer_image);
                        }
                    } else {
                        // Si no hay datos, dejar el editor vacío
                        console.error("Error en la respuesta:", data);
                        templateEditor.setText('');
                    }
                } catch (e) {
                    console.error("Error al procesar la respuesta:", e);
                    templateEditor.setText('');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al obtener el contenido de la plantilla:", status, error);
                console.log("Respuesta del servidor:", xhr.responseText);
                
                // Dejar el editor vacío en caso de error
                templateEditor.setText('');
            }
        });
    }
    
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
        if (headerImageChanged && document.getElementById('header_image').files[0]) {
            formData.append('header_image', document.getElementById('header_image').files[0]);
        }
        
        if (footerImageChanged && document.getElementById('footer_image').files[0]) {
            formData.append('footer_image', document.getElementById('footer_image').files[0]);
        }
        
        // Enviar datos al servidor
        $.ajax({
            url: _URL + "/ajs/informe/guardar-template",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Actualizar las imágenes actuales si se proporcionaron nuevas URLs
                    if (data.header_image) {
                        currentHeaderImage = data.header_image;
                    }
                    
                    if (data.footer_image) {
                        currentFooterImage = data.footer_image;
                    }
                    
                    // Restablecer los indicadores de cambio
                    headerImageChanged = false;
                    footerImageChanged = false;
                    
                    Swal.fire({
                        title: 'Éxito',
                        text: 'La plantilla se ha guardado correctamente',
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.msg || 'No se pudo guardar la plantilla',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
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
    
    function mostrarVistaPrevia() {
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
            success: function(data) {
                Swal.close();
                
                if (data.success && data.pdfBase64) {
                    // Crear un objeto Blob con el PDF base64
                    const byteCharacters = atob(data.pdfBase64);
                    const byteNumbers = new Array(byteCharacters.length);
                    for (let i = 0; i < byteCharacters.length; i++) {
                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                    }
                    const byteArray = new Uint8Array(byteNumbers);
                    const blob = new Blob([byteArray], {type: 'application/pdf'});
                    
                    // Crear una URL para el blob
                    const pdfUrl = URL.createObjectURL(blob);
                    
                    // Mostrar el PDF en el iframe
                    $("#preview-frame").attr("src", pdfUrl);
                    $("#previewModal").modal("show");
                    
                    // Limpiar la URL cuando se cierre el modal
                    $("#previewModal").on('hidden.bs.modal', function () {
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
            error: function(xhr, status, error) {
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
</script>