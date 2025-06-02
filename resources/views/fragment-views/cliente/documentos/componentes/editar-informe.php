<div class="container mt-4">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h6 class="page-title" id="titulo-pagina">Nuevo Informe</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Datos del Informe</h5>
                    <div>
                        <button type="button" id="btn-preview" class="btn border-rojo me-2">
                            <i class="fa fa-eye"></i> Vista Previa
                        </button>
                        <button type="button" id="btn-save" class="btn bg-rojo text-white">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formInforme" enctype="multipart/form-data">
                        <input type="hidden" id="id_informe" name="id_informe">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_informe" class="form-label">Tipo de Informe</label>
                                <input type="text" class="form-control" id="tipo_informe" name="tipo_informe" placeholder="Ej: TÉCNICO, PAGO, SERVICIO" required>
                                <small class="form-text text-muted">Este campo se usará para filtrar los informes.</small>
                            </div>
                          <div class="mb-3">
    <label for="cliente_search" class="form-label">Cliente</label>
    <div class="input-group">
        <input type="text" class="form-control border rounded-start-2 shadow-sm" id="cliente_search" placeholder="Buscar por nombre o documento..." autocomplete="off">
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
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="titulo_informe" class="form-label">Título del Informe</label>
                                <input type="text" class="form-control" id="titulo_informe" name="titulo_informe" required>
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
                                <small class="form-text text-muted">Si no selecciona una imagen, se usará la de la plantilla.</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="header-preview-container" class="text-center">
                                        <img id="header-preview" src="/placeholder.svg" alt="Vista previa del encabezado" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="header-placeholder" class="text-muted">Se usará la imagen de la plantilla</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="footer_image" class="form-label">Imagen de Pie de Página</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control" id="footer_image" name="footer_image" accept="image/png,image/jpeg,image/gif">
                                    <button class="btn btn-outline-secondary" type="button" id="reset-footer">Restablecer</button>
                                </div>
                                <small class="form-text text-muted">Si no selecciona una imagen, se usará la de la plantilla.</small>
                                <div class="mt-2 border p-2 rounded bg-light">
                                    <p class="mb-1 fw-bold">Vista previa:</p>
                                    <div id="footer-preview-container" class="text-center">
                                        <img id="footer-preview" src="/placeholder.svg" alt="Vista previa del pie de página" class="img-fluid" style="max-height: 100px; display: none;">
                                        <div id="footer-placeholder" class="text-muted">Se usará la imagen de la plantilla</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Contenido del Informe</label>
                                <!-- Contenedor para el editor -->
                                <div id="editor-container-informe" style="min-height: 400px; border: 1px solid #ccc; border-radius: 5px;">
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
                <h5 class="modal-title" id="previewModalLabel">Vista Previa del Informe</h5>
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
    let informeEditor = null;
    let headerImageChanged = false;
    let footerImageChanged = false;
    let currentHeaderImage = null;
    let currentFooterImage = null;
    let editMode = false;
    let informeId = null;
    
    $(document).ready(function() {
        // Verificar si estamos en modo edición
        const urlParams = new URLSearchParams(window.location.search);
        informeId = urlParams.get('id');
        
        if (informeId) {
            editMode = true;
            $("#titulo-pagina").text("Editar Informe");
            $("#id_informe").val(informeId);
        }
        
        // Inicializar el editor Quill
        initializeInformeEditor();
        
     // Inicializar autocomplete para clientes
inicializarAutocompletarClientes();
        // Si estamos en modo edición, cargar los datos del informe
        if (editMode) {
            cargarDatosInforme(informeId);
        } else {
            // Cargar el contenido de la plantilla para un nuevo informe
            cargarContenidoPlantilla();
        }
        
        // Manejar el envío del formulario usando el botón de guardar
        $("#btn-save").on("click", function() {
            guardarInforme();
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
    
    function initializeInformeEditor() {
        try {
            console.log('Inicializando editor Quill...');
            
            if (informeEditor) {
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

            informeEditor = new Quill('#editor-container-informe', {
                modules: {
                    toolbar: toolbarOptions,
                    clipboard: {
                        matchVisual: false
                    }
                },
                theme: 'snow',
                placeholder: 'Contenido del informe...'
            });

            console.log('Editor Quill inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar Quill:', error);
        }
    }
    
 // Función para inicializar el autocomplete de clientes
function inicializarAutocompletarClientes() {
    $("#cliente_search").autocomplete({
        source: _URL + "/ajs/buscar/cliente/datos", // Usamos la ruta existente
        minLength: 2,
        select: function (event, ui) {
            event.preventDefault();

            // Establecer los valores seleccionados
            $("#cliente_id").val(ui.item.codigo); // Usamos 'codigo' que es el campo que devuelve la API
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
}
    
    function cargarContenidoPlantilla() {
        // Mostrar indicador de carga
        if (informeEditor) {
            informeEditor.setText('Cargando contenido...');
        }
        
        // Realizar petición AJAX para obtener el contenido de la plantilla
        $.ajax({
            url: _URL + "/ajs/informe/obtener-template",
            method: "GET",
            dataType: 'json',
            success: function(data) {
                try {
                    if (data && data.success) {
                        // Verificar si hay contenido
                        if (data.contenido) {
                            // Cargar el contenido HTML en el editor
                            informeEditor.root.innerHTML = data.contenido;
                            informeEditor.update();
                        } else {
                            informeEditor.setText('');
                        }
                        
                        // Actualizar el título si existe
                        if (data.titulo) {
                            $("#titulo_informe").val(data.titulo);
                        }
                        
                        // Guardar las URLs de las imágenes por defecto
                        if (data.header_image) {
                            currentHeaderImage = data.header_image;
                        }

                        if (data.footer_image) {
                            currentFooterImage = data.footer_image;
                        }
                    } else {
                        // Si no hay datos, dejar el editor vacío
                        console.error("Error en la respuesta:", data);
                        informeEditor.setText('');
                    }
                } catch (e) {
                    console.error("Error al procesar la respuesta:", e);
                    informeEditor.setText('');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al obtener el contenido de la plantilla:", status, error);
                informeEditor.setText('');
            }
        });
    }
    
    function cargarDatosInforme(id) {
        // Mostrar indicador de carga
        if (informeEditor) {
            informeEditor.setText('Cargando contenido...');
        }
        
        // Realizar petición AJAX para obtener los datos del informe
        $.ajax({
            url: _URL + "/ajs/informe/getOne",
            method: "POST",
            data: { id_informe: id },
            dataType: 'json',
            success: function(data) {
                try {
                    if (data && !data.error) {
                        // Cargar los datos en el formulario
                        $("#tipo_informe").val(data.tipo);
                        $("#titulo_informe").val(data.titulo);
                        $("#cliente_id").val(data.cliente_id);
                        
                        // Cargar el contenido en el editor
                        if (data.contenido) {
                            informeEditor.root.innerHTML = data.contenido;
                            informeEditor.update();
                        } else {
                            informeEditor.setText('');
                        }
                        
                        // Cargar las imágenes
                        if (data.header_image) {
                            currentHeaderImage = data.header_image;
                            $("#header-preview").attr("src", data.header_image);
                            $("#header-preview").show();
                            $("#header-placeholder").hide();
                        }

                        if (data.footer_image) {
                            currentFooterImage = data.footer_image;
                            $("#footer-preview").attr("src", data.footer_image);
                            $("#footer-preview").show();
                            $("#footer-placeholder").hide();
                        }
                    } else {
                        // Si no hay datos, mostrar error
                        console.error("Error en la respuesta:", data);
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo cargar el informe',
                            icon: 'error'
                        }).then(() => {
                            window.location.href = _URL + "/documentos";
                        });
                    }
                } catch (e) {
                    console.error("Error al procesar la respuesta:", e);
                    informeEditor.setText('');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los datos del informe:", status, error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor',
                    icon: 'error'
                }).then(() => {
                    window.location.href = _URL + "/documentos";
                });
            }
        });
    }
    
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
            success: function(data) {
                if (data.res) {
                    Swal.fire({
                        title: 'Éxito',
                        text: data.msg,
                        icon: 'success'
                    }).then(() => {
                        // Redirigir a la lista de informes
                        window.location.href = _URL + "/documentos";
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.msg || 'No se pudo guardar el informe',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
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