<!-- resources\views\fragment-views\cliente\mi-empresa.php -->
<style>
    .empresa-card {
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    }

    .seccion-titulo {
        font-size: 14px;
        font-weight: 600;
        color: #ca3438;
        border-bottom: 2px solid #ca3438;
        padding-bottom: 6px;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .logo-preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .logo-preview {
        width: 160px;
        height: 160px;
        border-radius: 12px;
        object-fit: contain;
        border: 3px solid #dee2e6;
        background-color: #f8f9fa;
        transition: border-color 0.3s ease;
    }

    .logo-preview:hover {
        border-color: #ca3438;
    }

    .logo-placeholder {
        width: 160px;
        height: 160px;
        border-radius: 12px;
        border: 3px dashed #dee2e6;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .logo-placeholder i {
        font-size: 40px;
        margin-bottom: 8px;
    }

    .modo-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .modo-beta {
        background-color: #fff3cd;
        color: #856404;
    }

    .modo-production {
        background-color: #d4edda;
        color: #155724;
    }

    #formEmpresa .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #495057;
        margin-bottom: 4px;
    }

    #formEmpresa .form-control,
    #formEmpresa .form-select {
        border-radius: 8px;
        font-size: 13px;
    }

    #formEmpresa .form-control:focus,
    #formEmpresa .form-select:focus {
        border-color: #ca3438;
        box-shadow: 0 0 0 0.2rem rgba(202, 52, 56, 0.15);
    }

    .btn-guardar {
        border-radius: 10px;
        padding: 10px 32px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        z-index: 10;
    }
</style>

<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center fw-bold">MI EMPRESA</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card empresa-card position-relative">
            <!-- Loading -->
            <div class="loading-overlay" id="loadingEmpresa">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="formEmpresa" enctype="multipart/form-data">

                    <div class="row">
                        <!-- Columna izquierda: Datos del formulario -->
                        <div class="col-lg-9">

                            <!-- DATOS GENERALES -->
                            <div class="mb-4">
                                <div class="seccion-titulo">
                                    <i class="fa fa-building me-1"></i> Datos Generales
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fa fa-id-card me-1"></i>RUC <span class="text-danger">(*)</span>
                                        </label>
                                        <input type="text" id="emp_ruc" name="ruc" class="form-control form-control-sm" maxlength="11"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">
                                            <i class="fa fa-file-signature me-1"></i>Razón Social <span class="text-danger">(*)</span>
                                        </label>
                                        <input type="text" id="emp_razon_social" name="razon_social" class="form-control form-control-sm" maxlength="245">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fa fa-store me-1"></i>Nombre Comercial
                                        </label>
                                        <input type="text" id="emp_comercial" name="comercial" class="form-control form-control-sm" maxlength="245">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fa fa-percent me-1"></i>IGV (%)
                                        </label>
                                        <input type="number" id="emp_igv" name="igv" class="form-control form-control-sm" step="0.01" min="0" max="100">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            <i class="fa fa-bullhorn me-1"></i>Propaganda
                                        </label>
                                        <input type="text" id="emp_propaganda" name="propaganda" class="form-control form-control-sm" maxlength="250"
                                            placeholder="Texto promocional en comprobantes">
                                    </div>
                                </div>
                            </div>

                            <!-- CONTACTO -->
                            <div class="mb-4">
                                <div class="seccion-titulo">
                                    <i class="fa fa-address-book me-1"></i> Contacto
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fa fa-envelope me-1"></i>Email
                                        </label>
                                        <input type="email" id="emp_email" name="email" class="form-control form-control-sm" maxlength="145">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fa fa-phone me-1"></i>Teléfono Principal
                                        </label>
                                        <input type="text" id="emp_telefono" name="telefono" class="form-control form-control-sm" maxlength="30">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fa fa-phone me-1"></i>Teléfono 2
                                        </label>
                                        <input type="text" id="emp_telefono2" name="telefono2" class="form-control form-control-sm" maxlength="30">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fa fa-phone me-1"></i>Teléfono 3
                                        </label>
                                        <input type="text" id="emp_telefono3" name="telefono3" class="form-control form-control-sm" maxlength="30">
                                    </div>
                                </div>
                            </div>

                            <!-- UBICACIÓN -->
                            <div class="mb-4">
                                <div class="seccion-titulo">
                                    <i class="fa fa-map-marker-alt me-1"></i> Ubicación
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            <i class="fa fa-location-dot me-1"></i>Dirección
                                        </label>
                                        <input type="text" id="emp_direccion" name="direccion" class="form-control form-control-sm" maxlength="245">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">
                                            <i class="fa fa-hashtag me-1"></i>Ubigeo
                                        </label>
                                        <input type="text" id="emp_ubigeo" name="ubigeo" class="form-control form-control-sm" maxlength="6"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Departamento</label>
                                        <input type="text" id="emp_departamento" name="departamento" class="form-control form-control-sm" maxlength="45">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Provincia</label>
                                        <input type="text" id="emp_provincia" name="provincia" class="form-control form-control-sm" maxlength="45">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Distrito</label>
                                        <input type="text" id="emp_distrito" name="distrito" class="form-control form-control-sm" maxlength="45">
                                    </div>
                                </div>
                            </div>

                            <!-- SUNAT / FACTURACIÓN -->
                            <div class="mb-4">
                                <div class="seccion-titulo">
                                    <i class="fa fa-file-invoice me-1"></i> SUNAT / Facturación
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fa fa-user me-1"></i>Usuario SOL
                                        </label>
                                        <input type="text" id="emp_user_sol" name="user_sol" class="form-control form-control-sm" maxlength="45">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fa fa-key me-1"></i>Clave SOL
                                        </label>
                                        <input type="password" id="emp_clave_sol" name="clave_sol" class="form-control form-control-sm" maxlength="45">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fa fa-server me-1"></i>Modo de Facturación
                                        </label>
                                        <select id="emp_modo" name="modo" class="form-select form-select-sm">
                                            <option value="beta">🟡 Beta (Pruebas)</option>
                                            <option value="production">🟢 Producción</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Columna derecha: Logo -->
                        <div class="col-lg-3">
                            <div class="mb-4">
                                <div class="seccion-titulo">
                                    <i class="fa fa-image me-1"></i> Logo
                                </div>
                                <div class="logo-preview-container">
                                    <div id="logoPlaceholder" class="logo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <span class="small">Sin logo</span>
                                    </div>
                                    <img id="logoPreview" class="logo-preview d-none" src="" alt="Logo empresa">
                                    <label for="emp_logo" class="btn btn-sm border-rojo text-rojo bg-white mt-2" style="cursor:pointer;">
                                        <i class="fa fa-upload me-1"></i>Cambiar Logo
                                    </label>
                                    <input type="file" id="emp_logo" name="logo" class="d-none" accept="image/*">
                                    <span class="text-muted" style="font-size:11px;">PNG, JPG (máx. 2MB)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOTÓN GUARDAR -->
                    <div class="text-end mt-3 pt-3 border-top">
                        <button type="submit" class="btn bg-rojo text-white btn-guardar">
                            <i class="fa fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    // Cargar datos de la empresa al iniciar
    cargarDatosEmpresa();

    function cargarDatosEmpresa() {
        $('#loadingEmpresa').show();
        $.ajax({
            url: _URL + '/ajs/empresa/info',
            type: 'POST',
            headers: { 'token-app': localStorage.getItem("_token") },
            success: function (response) {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data && data.id_empresa) {
                    llenarFormulario(data);
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información de la empresa.', 'error');
                }
                $('#loadingEmpresa').hide();
            },
            error: function (xhr, status, error) {
                console.error('Error AJAX:', error);
                Swal.fire('Error', 'Error al conectar con el servidor.', 'error');
                $('#loadingEmpresa').hide();
            }
        });
    }

    function llenarFormulario(emp) {
        $('#emp_ruc').val(emp.ruc || '');
        $('#emp_razon_social').val(emp.razon_social || '');
        $('#emp_comercial').val(emp.comercial || '');
        $('#emp_igv').val(emp.igv || '');
        $('#emp_propaganda').val(emp.propaganda || '');
        $('#emp_email').val(emp.email || '');
        $('#emp_telefono').val(emp.telefono || '');
        $('#emp_telefono2').val(emp.telefono2 || '');
        $('#emp_telefono3').val(emp.telefono3 || '');
        $('#emp_direccion').val(emp.direccion || '');
        $('#emp_ubigeo').val(emp.ubigeo || '');
        $('#emp_departamento').val(emp.departamento || '');
        $('#emp_provincia').val(emp.provincia || '');
        $('#emp_distrito').val(emp.distrito || '');
        $('#emp_user_sol').val(emp.user_sol || '');
        $('#emp_clave_sol').val(emp.clave_sol || '');
        $('#emp_modo').val(emp.modo || 'beta');

        // Logo
        if (emp.logo) {
            $('#logoPreview').attr('src', _URL + '/logos/' + emp.logo).removeClass('d-none');
            $('#logoPlaceholder').addClass('d-none');
        } else {
            $('#logoPreview').addClass('d-none');
            $('#logoPlaceholder').removeClass('d-none');
        }
    }

    // Preview de logo al seleccionar archivo
    $('#emp_logo').on('change', function () {
        var file = this.files[0];
        if (!file) return;

        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Advertencia', 'El archivo no debe superar los 2MB.', 'warning');
            $(this).val('');
            return;
        }

        // Validar tipo
        if (!file.type.match('image.*')) {
            Swal.fire('Advertencia', 'Solo se permiten archivos de imagen.', 'warning');
            $(this).val('');
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#logoPreview').attr('src', e.target.result).removeClass('d-none');
            $('#logoPlaceholder').addClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    // Enviar formulario
    $('#formEmpresa').on('submit', function (e) {
        e.preventDefault();

        // Validar campos obligatorios
        var ruc = $('#emp_ruc').val().trim();
        var razonSocial = $('#emp_razon_social').val().trim();

        if (!ruc || !razonSocial) {
            Swal.fire({
                title: 'Campos obligatorios',
                text: 'El RUC y la Razón Social son obligatorios.',
                icon: 'warning'
            });
            return;
        }

        if (ruc.length !== 11) {
            Swal.fire({
                title: 'RUC inválido',
                text: 'El RUC debe tener 11 dígitos.',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: '¿Guardar cambios?',
            text: 'Se actualizarán los datos de la empresa.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ca3438',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) {
                guardarEmpresa();
            }
        });
    });

    function guardarEmpresa() {
        var formData = new FormData($('#formEmpresa')[0]);

        $('#loadingEmpresa').show();
        $.ajax({
            url: _URL + '/ajs/empresa/actualizar',
            type: 'POST',
            headers: { 'token-app': localStorage.getItem("_token") },
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.status === 'ok' || data.success) {
                        Swal.fire('¡Buen trabajo!', 'Datos de la empresa actualizados correctamente.', 'success');
                        cargarDatosEmpresa();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'No se pudo actualizar la empresa.',
                            icon: 'error'
                        });
                    }
                } catch (e) {
                    console.error('Error al procesar respuesta:', e);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al procesar la respuesta del servidor.',
                        icon: 'error'
                    });
                }
                $('#loadingEmpresa').hide();
            },
            error: function (xhr, status, error) {
                console.error('Error AJAX:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un problema al guardar los datos.',
                    icon: 'error'
                });
                $('#loadingEmpresa').hide();
            }
        });
    }

});
</script>
