<!-- resources\views\fragment-views\cliente\editar-certificado.php -->
<style>
    /* Agregar estos estilos al CSS existente */
.ql-editor strong {
    font-weight: 700 !important;
}

.ql-editor p strong {
    font-weight: 700 !important;
}

.ql-editor li strong {
    font-weight: 700 !important;
}

/* Asegurar que el texto en negrita se mantenga al pegar */
.ql-clipboard strong {
    font-weight: 700 !important;
}

</style>
<div class="container mt-4">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h6 class="page-title">Editor de Certificado de Garantía</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Personalizar Certificado</h5>
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
                    <form id="formCertificadoGarantia" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="titulo_certificado" class="form-label">Título del Certificado</label>
                                <input type="text" class="form-control" id="titulo_certificado" name="titulo_certificado" value="CERTIFICADO DE GARANTÍA">
                            </div>
                        </div>
                        
                        <!-- Nuevas secciones para las imágenes -->
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
                                <label class="form-label">Contenido del Certificado</label>
                                <!-- Contenedor único para el editor -->
                                <div id="editor-container-certificado" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
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
                <h5 class="modal-title" id="previewModalLabel">Vista Previa del Certificado</h5>
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

<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    let certificadoEditor = null;
    let headerImageChanged = false;
    let footerImageChanged = false;
    let currentHeaderImage = null;
    let currentFooterImage = null;
    
    // Verificar si Quill está cargado antes de inicializar
    function checkQuillLoaded() {
        if (typeof Quill === 'undefined') {
            console.log('Quill aún no está cargado, reintentando en 100ms...');
            setTimeout(checkQuillLoaded, 100);
            return;
        }
        
        console.log('Quill está cargado, inicializando editor...');
        initApp();
    }
    
    function initApp() {
        // Inicializar el editor Quill
        initializeCertificadoEditor();
        
        // Cargar el contenido del certificado desde la base de datos
        cargarContenidoCertificado();
        
        // Manejar el envío del formulario usando el botón de guardar
        $("#btn-save").on("click", function() {
            guardarCertificado();
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
    }
    
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
    
    function initializeCertificadoEditor() {
    try {
        console.log('Inicializando editor Quill...');
        
        if (certificadoEditor) {
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

        certificadoEditor = new Quill('#editor-container-certificado', {
            modules: {
                toolbar: toolbarOptions,
                clipboard: {
                    matchVisual: false,
                    matchers: [
                        ['strong', function(node, delta) {
                            return delta.compose(new Delta().retain(delta.length(), { bold: true }));
                        }]
                    ]
                }
            },
            formats: [
                'bold', 'italic', 'underline', 'strike',
                'align', 'list', 'bullet', 'indent',
                'size', 'color', 'background',
                'font', 'header', 'blockquote',
                'code-block', 'link', 'script',
                'direction'
            ],
            theme: 'snow',
            placeholder: 'Contenido del certificado de garantía...'
        });

        // Agregar manejador para preservar etiquetas strong
        certificadoEditor.clipboard.addMatcher('strong', function(node, delta) {
            return delta.compose(new Delta().retain(delta.length(), { bold: true }));
        });

        console.log('Editor Quill inicializado correctamente');
    } catch (error) {
        console.error('Error al inicializar Quill:', error);
    }
}


function cargarContenidoHTML(html) {
    console.log("Cargando HTML en el editor:", html.substring(0, 100) + "...");
    
    try {
        // Crear un div temporal
        const tempDiv = document.createElement('div');
        tempDiv.style.display = 'none';
        document.body.appendChild(tempDiv);
        
        // Insertar el HTML
        tempDiv.innerHTML = html;
        
        // Limpiar el editor
        certificadoEditor.setContents([]);
        
        // Procesar cada nodo de nivel superior
        Array.from(tempDiv.childNodes).forEach(node => {
            if (node.nodeType === Node.ELEMENT_NODE) {
                if (node.tagName === 'OL' || node.tagName === 'UL') {
                    // Procesar listas
                    const listType = node.tagName === 'OL' ? 'ordered' : 'bullet';
                    Array.from(node.children).forEach((li) => {
                        // Verificar si hay elementos strong dentro del li
                        const strongElements = li.querySelectorAll('strong');
                        if (strongElements.length > 0) {
                            // Si hay elementos strong, procesarlos individualmente
                            let currentPos = 0;
                            let text = li.textContent;
                            
                            // Para cada elemento strong, insertar el texto antes, durante y después con el formato adecuado
                            strongElements.forEach(strong => {
                                const strongText = strong.textContent;
                                const strongIndex = text.indexOf(strongText, currentPos);
                                
                                if (strongIndex > currentPos) {
                                    // Texto antes del strong
                                    certificadoEditor.insertText(
                                        certificadoEditor.getLength(), 
                                        text.substring(currentPos, strongIndex),
                                        { 'list': listType }
                                    );
                                }
                                
                                // Texto del strong con formato bold
                                certificadoEditor.insertText(
                                    certificadoEditor.getLength(),
                                    strongText,
                                    { 'list': listType, 'bold': true }
                                );
                                
                                currentPos = strongIndex + strongText.length;
                            });
                            
                            // Texto después del último strong
                            if (currentPos < text.length) {
                                certificadoEditor.insertText(
                                    certificadoEditor.getLength(),
                                    text.substring(currentPos),
                                    { 'list': listType }
                                );
                            }
                            
                            // Añadir salto de línea
                            certificadoEditor.insertText(certificadoEditor.getLength(), '\n');
                        } else {
                            // Si no hay strong, insertar el texto completo
                            certificadoEditor.insertText(certificadoEditor.getLength(), li.textContent + '\n', {
                                'list': listType
                            });
                        }
                    });
                } else if (node.tagName === 'P') {
                    // Procesar párrafos
                    const strongElements = node.querySelectorAll('strong');
                    if (strongElements.length > 0) {
                        // Si hay elementos strong, procesarlos individualmente
                        let currentPos = 0;
                        let text = node.textContent;
                        
                        // Para cada elemento strong, insertar el texto antes, durante y después con el formato adecuado
                        strongElements.forEach(strong => {
                            const strongText = strong.textContent;
                            const strongIndex = text.indexOf(strongText, currentPos);
                            
                            if (strongIndex > currentPos) {
                                // Texto antes del strong
                                certificadoEditor.insertText(
                                    certificadoEditor.getLength(), 
                                    text.substring(currentPos, strongIndex)
                                );
                            }
                            
                            // Texto del strong con formato bold
                            certificadoEditor.insertText(
                                certificadoEditor.getLength(),
                                strongText,
                                { 'bold': true }
                            );
                            
                            currentPos = strongIndex + strongText.length;
                        });
                        
                        // Texto después del último strong
                        if (currentPos < text.length) {
                            certificadoEditor.insertText(
                                certificadoEditor.getLength(),
                                text.substring(currentPos)
                            );
                        }
                        
                        // Añadir salto de línea
                        certificadoEditor.insertText(certificadoEditor.getLength(), '\n');
                    } else {
                        // Si no hay strong, insertar el texto completo
                        certificadoEditor.insertText(certificadoEditor.getLength(), node.textContent + '\n');
                    }
                }
            }
        });
        
        // Eliminar el div temporal
        document.body.removeChild(tempDiv);
        
        console.log("HTML cargado en el editor con formato preservado");
    } catch (error) {
        console.error("Error al cargar el HTML en el editor:", error);
        
        // En caso de error, intentar el método directo como fallback
        certificadoEditor.root.innerHTML = html;
        certificadoEditor.update();
    }
}

// Modifica la función cargarContenidoCertificado para usar cargarContenidoHTML
function cargarContenidoCertificado() {
    // Mostrar indicador de carga
    if (certificadoEditor) {
        certificadoEditor.setText('Cargando contenido...');
    }
    
    // Realizar petición AJAX para obtener el contenido del certificado
    $.ajax({
        url: _URL + "/ajs/certificado/obtener",
        method: "GET",
        dataType: 'json', // Especificar que esperamos JSON
        success: function(data) {
            try {
                console.log("Respuesta completa:", data);
                
                if (data && data.success) {
                    // Verificar si hay contenido
                    if (data.contenido) {
                        console.log("Contenido recibido (primeros 100 caracteres):", data.contenido.substring(0, 100));
                        
                        // Usar la nueva función para cargar el contenido HTML
                        cargarContenidoHTML(data.contenido);
                        
                        // Verificar si las listas se cargaron correctamente
                        console.log("¿Hay listas en el editor?", certificadoEditor.root.querySelectorAll('ol, ul').length > 0);
                    } else {
                        console.warn("El contenido está vacío");
                        certificadoEditor.setText('');
                    }
                    
                    // Actualizar el título si existe
                    if (data.titulo) {
                        $("#titulo_certificado").val(data.titulo);
                    }
                    
                    // Cargar las imágenes (ya sean de la base de datos o las predeterminadas)
                    if (data.header_image) {
                        currentHeaderImage = data.header_image;
                        $("#header-preview").attr("src", data.header_image);
                        $("#header-preview").show();
                        $("#header-placeholder").hide();
                        console.log("Imagen de encabezado cargada:", data.header_image);
                    } else {
                        // Cargar imagen por defecto si no hay imagen en la respuesta
                        currentHeaderImage = _URL + "/public/img/garantia/header.png";
                        $("#header-preview").attr("src", currentHeaderImage);
                        $("#header-preview").show();
                        $("#header-placeholder").hide();
                        console.log("Cargando imagen de encabezado por defecto:", currentHeaderImage);
                    }

                    if (data.footer_image) {
                        currentFooterImage = data.footer_image;
                        $("#footer-preview").attr("src", data.footer_image);
                        $("#footer-preview").show();
                        $("#footer-placeholder").hide();
                        console.log("Imagen de pie de página cargada:", data.footer_image);
                    } else {
                        // Cargar imagen por defecto si no hay imagen en la respuesta
                        currentFooterImage = _URL + "/public/img/garantia/footer.png";
                        $("#footer-preview").attr("src", currentFooterImage);
                        $("#footer-preview").show();
                        $("#footer-placeholder").hide();
                        console.log("Cargando imagen de pie de página por defecto:", currentFooterImage);
                    }
                } else {
                    // Si no hay datos, dejar el editor vacío
                    console.error("Error en la respuesta:", data);
                    certificadoEditor.setText('');
                }
            } catch (e) {
                console.error("Error al procesar la respuesta:", e);
                certificadoEditor.setText('');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener el contenido del certificado:", status, error);
            console.log("Respuesta del servidor:", xhr.responseText);
            
            // Intentar parsear la respuesta por si contiene JSON válido después del error
            try {
                const responseText = xhr.responseText;
                const jsonStartIndex = responseText.indexOf('{');
                if (jsonStartIndex !== -1) {
                    const jsonPart = responseText.substring(jsonStartIndex);
                    const data = JSON.parse(jsonPart);
                    
                    if (data && data.success === false) {
                        console.log("Se encontró un mensaje de error en la respuesta:", data.message);
                    }
                }
            } catch (e) {
                console.error("No se pudo extraer JSON de la respuesta");
            }
            
            // Dejar el editor vacío en caso de error
            certificadoEditor.setText('');
        }
    });
}

    
    function guardarCertificado() {
        // Obtener el contenido del editor
        if (!certificadoEditor) {
            Swal.fire({
                title: 'Error',
                text: 'El editor no está inicializado correctamente',
                icon: 'error'
            });
            return;
        }
        
        const contenido = certificadoEditor.root.innerHTML;
        const titulo = $("#titulo_certificado").val();
        
        // Validar que haya contenido
        if (!contenido.trim()) {
            Swal.fire({
                title: 'Error',
                text: 'El contenido del certificado no puede estar vacío',
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
            url: _URL + "/ajs/certificado/guardar",
            method: "POST",
            data: formData,
            processData: false,  // Importante para FormData
            contentType: false,  // Importante para FormData
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
                        text: 'El certificado se ha guardado correctamente',
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo guardar el certificado',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al guardar el certificado:", status, error);
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
    if (!certificadoEditor) {
        Swal.fire({
            title: 'Error',
            text: 'El editor no está inicializado correctamente',
            icon: 'error'
        });
        return;
    }
    
    // Obtener el contenido actual
    const contenido = certificadoEditor.root.innerHTML;
    const titulo = $("#titulo_certificado").val();
    
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
    }
    
    if (footerImageChanged && document.getElementById('footer_image').files[0]) {
        formData.append('footer_image', document.getElementById('footer_image').files[0]);
    }
    
    // Enviar datos para generar vista previa
    $.ajax({
        url: _URL + "/ajs/certificado/vista-previa",
        method: "POST",
        data: formData,
        processData: false,  // Importante para FormData
        contentType: false,  // Importante para FormData
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
                    text: data.message || 'No se pudo generar la vista previa',
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
    
    // Iniciar la verificación de carga de Quill cuando el documento esté listo
    $(document).ready(function() {
        console.log('Documento listo, verificando si Quill está cargado...');
        checkQuillLoaded();
    });
</script>