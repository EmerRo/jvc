<?php
require_once "app/models/Cliente.php";

$c_cliente = new Cliente();
$c_cliente->setIdEmpresa($_SESSION['id_empresa']);
?>
<div class="page-title-box py-2">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center fw-bold">DATOS DE USUARIOS</h6>
        </div>
    </div>
</div>
<style>
    .preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .img-preview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dee2e6;
        transition: border-color 0.3s ease;
    }

    .img-preview:hover { border-color: #CA3438; }

    .placeholder-img {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .preview-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }

    .modulos-scroll-container {
        max-height: 40vh;
        overflow-y: auto;
        border-radius: 0.25rem;
    }

    .modulo-item {
        padding: 10px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        transition: background-color 0.2s;
    }

    .modulo-item:hover { background-color: rgba(0,0,0,0.02); }

    .modulo-header {
        display: flex;
        align-items: center;
    }

    .modulo-header i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .submodulos-container {
        margin-top: 8px;
        padding-left: 24px;
        border-left: 2px solid #f8f9fa;
        margin-left: 8px;
    }

    .submodulo-item { padding: 4px 0; }

    .submodulo-item label {
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }

    .submodulo-item i {
        font-size: 0.8em;
        margin-right: 8px;
        opacity: 0.7;
    }

    #modulos-izquierda, #modulos-derecha { min-height: 100%; }
</style>

<div id="usuarios-app">

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                    <button type="button" id="manage-roles" class="btn border-rojo me-2">
                        <i class="fa fa-cogs me-1"></i>Gestionar Roles
                    </button>
                    <button type="button" id="add-user" class="btn bg-rojo text-white">
                        <i class="fa fa-plus me-1"></i>Agregar
                    </button>
                </div>
                <div class="card-body p-3">

                    <!-- MODAL LISTA CLIENTES (importar) -->
                    <div class="modal fade" id="modal-lista-clientes" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header py-2">
                                    <h5 class="modal-title">Lista de clientes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <table class="table table-bordered text-center" id="tablaImportarCliente">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Documento</th><th>Datos</th><th>Dirección</th>
                                                <th>Dirección 2</th><th>Teléfono</th><th>Teléfono 2</th><th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in listaClientes" :key="index">
                                                <td>{{item.documento}}</td>
                                                <td>{{item.datos}}</td>
                                                <td>{{item.direccion}}</td>
                                                <td>{{item.direccion2}}</td>
                                                <td>{{item.telefono}}</td>
                                                <td>{{item.telefono2}}</td>
                                                <td>{{item.email}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer py-2">
                                    <button @click="agregarListaImport" type="button" class="btn bg-rojo">Guardar</button>
                                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL IMPORTAR EXCEL -->
                    <div class="modal fade" id="importarModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header py-2">
                                    <h5 class="modal-title">Importar Cliente con EXCEL</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <form enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <p class="small">Descargue el modelo en <strong>EXCEL</strong> para importar,
                                                no modifique los campos, <strong>click para descargar</strong>
                                                <a href="<?= URL::to("public/templateExcelClientes.xlsx") ?>">template.xlsx</a>
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small">Importar Excel:</label>
                                            <input type="file" id="nuevoExcel" name="nuevoExcel"
                                                class="form-control form-control-sm"
                                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer py-2">
                                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tabla_clientes"
                            class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer"
                            style="border-collapse:collapse;border-spacing:0;width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Código</th>
                                    <th>Nombres</th>
                                    <th>Rol</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Sueldo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AGREGAR USUARIO -->
    <div class="modal fade" id="usuario-add-bs" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header py-2 bg-rojo text-white">
                    <h5 class="modal-title">Crear Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <form id="myForm">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-id-card me-1"></i>Número de documento<span class="text-danger"> (*)</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="ndoc" id="ndoc" class="form-control form-control-sm" required maxlength="11"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button id="btnBuscarDocUsuario" class="btn btn-sm bg-rojo text-white" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <p class="text-danger error-msg small" id="error-ndoc"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-signature me-1"></i>Nombres<span class="text-danger"> (*)</span>
                                </label>
                                <input type="text" name="nombres" id="nombres" class="form-control form-control-sm" required>
                                <p class="text-danger error-msg small" id="error-nombres"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-user-tag me-1"></i>Rol
                                </label>
                                <select name="rol" id="rol" class="form-select form-select-sm"></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-user-circle me-1"></i>Usuario
                                </label>
                                <input type="text" name="usuario" id="usuario" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-key me-1"></i>Clave<span class="text-danger"> (*)</span>
                                </label>
                                <input type="password" name="clave" id="clave" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-phone me-1"></i>Teléfono<span class="text-danger"> (*)</span>
                                </label>
                                <input type="text" name="telefono" id="telefono" class="form-control form-control-sm" required maxlength="9"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9);">
                                <p class="text-danger error-msg small" id="error-telefono"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-envelope me-1"></i>Correo<span class="text-danger"> (*)</span>
                                </label>
                                <input type="email" name="email" id="email" class="form-control form-control-sm" required>
                                <p class="text-danger error-msg small" id="error-email"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rotativo</label>
                                <select name="rotativo" id="rotativo" class="form-select form-select-sm">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fa fa-money-bill-wave me-1"></i>Sueldo (S/)
                                </label>
                                <input type="number" name="sueldo" id="sueldo" class="form-control form-control-sm"
                                    step="0.01" min="0" placeholder="0.00" value="0">
                            </div>
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <label class="form-label">
                                            <i class="fa fa-camera me-1"></i>Foto de perfil
                                        </label>
                                        <input type="file" name="foto_perfil" id="foto_perfil"
                                               class="form-control form-control-sm" accept="image/*"
                                               @change="onAddPhotoChange">
                                        <small class="text-muted">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="preview-container">
                                            <img v-if="addPreview" :src="addPreview" alt="Preview" class="img-preview">
                                            <div v-else class="img-preview placeholder-img">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                                <div style="font-size:10px;margin-top:5px;">image.png</div>
                                            </div>
                                            <div class="preview-label">Vista previa</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cerrar
                    </button>
                    <button type="button" id="submitButton" class="btn bg-rojo text-white">
                        <i class="fa fa-save me-1"></i>Crear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR USUARIO -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header py-2 bg-rojo">
                    <h5 class="modal-title" id="editarModalTitle">Editar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <form id="clientesEditar">
                        <div class="row g-2">
                            <input type="hidden" name="idCliente" id="idCliente">
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fa fa-barcode me-1"></i>Código
                                </label>
                                <input type="text" class="form-control form-control-sm bg-light fw-bold text-center"
                                       id="codigoUsuario" name="codigoUsuario" readonly
                                       style="font-size:1.1em;letter-spacing:2px;">
                                <small class="text-muted">No editable</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fa fa-user-tag"></i>Rol
                                </label>
                                <select name="rol" id="rol2" class="form-select form-select-sm"></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-signature"></i>Nombre<span class="text-danger"> (*)</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="datosEditar" name="datosEditar" required>
                                <p class="text-danger error-msg small" id="error-datosEditar"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-id-card"></i>Número de documento<span class="text-danger"> (*)</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="doc" name="doc" required maxlength="11"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button id="btnBuscarDocUsuarioEdit" class="btn btn-sm bg-rojo text-white" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <p class="text-danger error-msg small" id="error-doc"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-user-circle"></i>Usuario
                                </label>
                                <input type="text" class="form-control form-control-sm" id="usuariou" name="usuariou">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-key"></i>Clave
                                </label>
                                <input type="password" class="form-control form-control-sm" id="claveu" name="claveu">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-phone"></i>Teléfono<span class="text-danger"> (*)</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="telefonoEditar"
                                    name="telefonoEditar" required maxlength="9"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9);">
                                <p class="text-danger error-msg small" id="error-telefonoEditar"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-envelope"></i>Email<span class="text-danger"> (*)</span>
                                </label>
                                <input required type="email" class="form-control form-control-sm" id="emailEditar" name="emailEditar">
                                <p class="text-danger error-msg small" id="error-emailEditar"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rotativo</label>
                                <select name="rotativou" id="rotativou" class="form-select form-select-sm">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fa fa-money-bill-wave me-1"></i>Sueldo (S/)
                                </label>
                                <input type="number" name="sueldou" id="sueldou" class="form-control form-control-sm"
                                    step="0.01" min="0" placeholder="0.00" value="0">
                            </div>
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <label class="form-label">
                                            <i class="fa fa-camera me-1"></i>Foto de perfil
                                        </label>
                                        <input type="file" name="foto_perfil_edit" id="foto_perfil_edit"
                                               class="form-control form-control-sm" accept="image/*"
                                               @change="onEditPhotoChange">
                                        <small class="text-muted">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB. Dejar vacío para mantener la actual</small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="preview-container">
                                            <img :src="editPreview" alt="Preview" class="img-preview">
                                            <div class="preview-label">Foto actual</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cerrar
                    </button>
                    <button id="updateCliente" type="button" class="btn bg-rojo">
                        <i class="fa fa-save me-1"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL GESTIONAR ROLES -->
    <div class="modal fade" id="roles-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header py-2 bg-rojo text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-user-shield me-1"></i>Gestión de Roles
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <button type="button" id="add-rol" class="btn bg-rojo">
                                <i class="fa fa-plus me-1"></i>Agregar Rol
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tabla_roles"
                            class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer"
                            style="border-collapse:collapse;border-spacing:0;width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CREAR / EDITAR ROL -->
    <div class="modal fade" id="rol-edit-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header py-2 bg-rojo">
                    <h5 class="modal-title" id="rolEditModalLabel">
                        <i class="fa fa-edit me-1"></i>Crear Rol
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <form id="rolForm">
                        <input type="hidden" id="rol_id" name="rol_id" value="">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label small">
                                    <i class="fa fa-tag me-1"></i>Nombre del Rol
                                </label>
                                <input type="text" name="nombre" id="nombre_rol" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column h-100 justify-content-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="ver_precios" name="ver_precios" value="1">
                                        <label class="form-check-label small" for="ver_precios">
                                            <i class="fa fa-dollar-sign me-1"></i>Permitir ver precios y costos
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_eliminar" name="puede_eliminar" value="1">
                                        <label class="form-check-label small" for="puede_eliminar">
                                            <i class="fa fa-trash me-1"></i>Permitir eliminar registros
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-bold mb-0 me-2">
                                    <i class="fa fa-puzzle-piece me-1"></i>Módulos y Submódulos
                                </label>
                                <span class="badge bg-danger text-white">Seleccione accesos</span>
                            </div>
                            <div class="card border shadow-sm">
                                <div class="card-body p-0">
                                    <div class="modulos-scroll-container">
                                        <div class="row g-0">
                                            <div class="col-md-6 border-end" id="modulos-izquierda"></div>
                                            <div class="col-md-6" id="modulos-derecha"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" id="guardarRol" class="btn bg-rojo">
                        <i class="fa fa-save me-1"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div><!-- #usuarios-app -->

<script>
const DEFAULT_AVATAR = '<?= DEFAULT_USER_AVATAR ?>';

// ===== Vue 2: imagen de perfil reactiva =====
const vm = new Vue({
    el: '#usuarios-app',
    data: {
        addPreview: null,
        editPreview: DEFAULT_AVATAR,
        listaClientes: []
    },
    methods: {
        onAddPhotoChange(event) { this._handlePhoto(event, 'addPreview'); },
        onEditPhotoChange(event) { this._handlePhoto(event, 'editPreview'); },

        _handlePhoto(event, target) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'Archivo muy grande', text: 'El archivo debe ser menor a 2MB' });
                event.target.value = '';
                return;
            }
            if (!file.type.match('image.*')) {
                Swal.fire({ icon: 'error', title: 'Tipo de archivo no válido', text: 'Solo se permiten archivos de imagen' });
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => { this[target] = e.target.result; };
            reader.readAsDataURL(file);
        },

        loadEditPhoto(url) {
            this.editPreview = (url && url !== 'null' && url !== '') ? url : DEFAULT_AVATAR;
        },

        resetAddPreview() {
            this.addPreview = null;
        },

        agregarListaImport() {}
    }
});

// ===== Utilidades compartidas =====

function buscarDocumento({ inputId, targetId, errorId, btnId }) {
    const ndoc = $(`#${inputId}`).val().trim();
    $(`#${errorId}`).text('');

    if (!ndoc) { $(`#${errorId}`).text('Ingrese un número de documento'); return; }
    if (ndoc.length !== 8 && ndoc.length !== 11) {
        $(`#${errorId}`).text('El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC)');
        return;
    }

    const $btn = $(`#${btnId}`);
    const btnOriginal = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
        url: _URL + '/ajs/consulta/doc/cliente',
        type: 'POST',
        data: { doc: ndoc },
        timeout: 10000,
        success(response) {
            try {
                const data = JSON.parse(response);
                if (data.res && data.data) {
                    const nombre = data.data.nombre || data.data.razon_social || '';
                    if (nombre) {
                        $(`#${targetId}`).val(nombre);
                    } else {
                        Swal.fire({ title: "Advertencia", text: data.msg || "No se encontró información del documento", icon: "warning" });
                    }
                } else {
                    Swal.fire({ title: "Advertencia", text: data.msg || "No se encontró información del documento", icon: "warning" });
                }
            } catch(e) {
                Swal.fire({ title: "Error", text: "Error al procesar la respuesta del servidor", icon: "error" });
            }
        },
        error(jqXHR, textStatus) {
            const msg = textStatus === 'timeout'
                ? 'El servicio de consulta tardó demasiado. Intentá de nuevo.'
                : 'Error al consultar el documento';
            Swal.fire({ title: "Error", text: msg, icon: "error" });
        },
        complete() { $btn.prop('disabled', false).html(btnOriginal); }
    });
}

function validarFormulario({ docId, nombresId, telefonoId, emailId }) {
    $('.error-msg').text('');
    let ok = true;

    const doc = $(`#${docId}`).val().trim();
    if (!doc) {
        $(`#error-${docId}`).text('El documento es obligatorio'); ok = false;
    } else if (!/^\d+$/.test(doc)) {
        $(`#error-${docId}`).text('El documento solo debe contener números'); ok = false;
    } else if (doc.length !== 8 && doc.length !== 11) {
        $(`#error-${docId}`).text('El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC)'); ok = false;
    } else if (doc.length === 11 && !doc.startsWith('10') && !doc.startsWith('20')) {
        $(`#error-${docId}`).text('El RUC debe empezar con 10 o 20'); ok = false;
    }

    if (!$(`#${nombresId}`).val().trim()) {
        $(`#error-${nombresId}`).text('Los nombres son obligatorios'); ok = false;
    }

    const tel = $(`#${telefonoId}`).val().trim();
    if (!tel) {
        $(`#error-${telefonoId}`).text('El teléfono es obligatorio'); ok = false;
    } else if (tel.length !== 9) {
        $(`#error-${telefonoId}`).text('El teléfono debe tener 9 dígitos'); ok = false;
    }

    const email = $(`#${emailId}`).val().trim();
    if (!email) {
        $(`#error-${emailId}`).text('El email es obligatorio'); ok = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        $(`#error-${emailId}`).text('El formato del email no es válido'); ok = false;
    }

    return ok;
}

function renderModulo(modulo, selMods, selSubs) {
    const checked = selMods.includes(modulo.id) ? 'checked' : '';
    let html = `<div class="modulo-item">
        <div class="modulo-header">
            <div class="form-check">
                <input class="form-check-input modulo-check" type="checkbox"
                    name="modulos[]" value="${modulo.id}" id="modulo_${modulo.id}" ${checked}
                    onchange="toggleSubmodulos('${modulo.id}')">
                <label class="form-check-label" for="modulo_${modulo.id}">
                    <i class="${modulo.icono}"></i><span>${modulo.nombre}</span>
                </label>
            </div>
        </div>`;

    if (modulo.submodulos && modulo.submodulos.length > 0) {
        html += `<div class="submodulos-container" id="submodulos_${modulo.id}"${checked ? '' : ' style="display:none;"'}>`;
        modulo.submodulos.forEach(sub => {
            const subChecked = selSubs.includes(`${modulo.id}|${sub.id}`) ? 'checked' : '';
            html += `<div class="submodulo-item">
                <div class="form-check">
                    <input class="form-check-input submodulo-check" type="checkbox"
                        name="submodulos[]" value="${modulo.id}|${sub.id}"
                        id="submodulo_${modulo.id}_${sub.id}" ${subChecked}
                        data-modulo="${modulo.id}">
                    <label class="form-check-label" for="submodulo_${modulo.id}_${sub.id}">
                        <i class="fa fa-circle-dot"></i>${sub.nombre}
                    </label>
                </div>
            </div>`;
        });
        html += '</div>';
    }
    return html + '</div>';
}

// ===== Limpiar errores al escribir =====
$(document).on('input', '#ndoc, #nombres, #telefono, #email, #doc, #datosEditar, #telefonoEditar, #emailEditar', function() {
    $(`#error-${$(this).attr('id')}`).text('');
});

// ===== Búsqueda de documento =====
$(document).on('click', '#btnBuscarDocUsuario', () =>
    buscarDocumento({ inputId: 'ndoc', targetId: 'nombres', errorId: 'error-ndoc', btnId: 'btnBuscarDocUsuario' })
);
$(document).on('click', '#btnBuscarDocUsuarioEdit', () =>
    buscarDocumento({ inputId: 'doc', targetId: 'datosEditar', errorId: 'error-doc', btnId: 'btnBuscarDocUsuarioEdit' })
);

// ===== DataTable Usuarios =====
if ($.fn.DataTable.isDataTable("#tabla_clientes")) {
    $("#tabla_clientes").DataTable().destroy();
}

var tabla_clientes = $("#tabla_clientes").DataTable({
    paging: true,
    bFilter: true,
    ordering: true,
    searching: true,
    destroy: true,
    ajax: { url: _URL + "/ajs/usuarios/render", method: "POST", dataSrc: "" },
    language: { url: "ServerSide/Spanish.json" },
    columns: [
        { data: "item", class: "text-center" },
        {
            data: "codigo", class: "text-center",
            render: (data) => `<span class="badge bg-primary">${data || '---'}</span>`
        },
        {
            data: "nombres", class: "text-left",
            render(data, type, row) {
                const foto = row.foto_perfil ? (row.foto_perfil.includes('/') ? row.foto_perfil : _URL + '/img/usuarios/' + row.foto_perfil) : DEFAULT_AVATAR;
                return `<div class="d-flex align-items-center">
                    <img src="${foto}" alt="Foto" class="rounded-circle me-3"
                         style="width:32px;height:32px;object-fit:cover;border:2px solid #dee2e6;">
                    <span>${data}</span>
                </div>`;
            }
        },
        { data: "nombre", class: "text-center" },
        { data: "usuario", class: "text-center" },
        { data: "email", class: "text-center" },
        { data: "telefono", class: "text-center" },
        {
            data: "sueldo", class: "text-center",
            render: (data) => (data && parseFloat(data) > 0) ? 'S/ ' + parseFloat(data).toFixed(2) : 'S/ 0.00'
        },
        {
            data: null, class: "text-center",
            render: (data, type, row) => `<div class="btn-group btn-sm">
                <button data-id="${Number(row.usuario_id)}" class="btn btn-sm btn-warning btnEditar"><i class="fa fa-edit"></i></button>
                <button data-id="${Number(row.usuario_id)}" class="btn btn-sm btn-danger btnBorrar"><i class="fa fa-trash"></i></button>
            </div>`
        }
    ]
});

// ===== Editar usuario =====
$("#tabla_clientes").on("click", ".btnEditar", function() {
    $("#loader-menor").show();
    const id = $(this).data("id");
    $("#editarModal").modal("show");
    $("#editarModalTitle").text("Editar Usuario N°" + id);

    $.ajax({
        url: _URL + "/ajs/usuarios/getOne",
        data: { id },
        type: "post",
        success(datos) {
            $.ajax({
                type: "POST",
                url: _URL + "/ajs/getroles",
                success(response) {
                    const roles = JSON.parse(response);
                    const options = roles.map(d => `<option value="${d.rol_id}">${d.nombre}</option>`).join('');
                    $('#rol2').html(options);

                    const json = JSON.parse(datos)[0];
                    $("#loader-menor").hide();
                    $("#rol2").val(json.id_rol);
                    $("#codigoUsuario").val(json.codigo || '---');
                    $("#doc").val(json.num_doc);
                    $("#datosEditar").val(json.nombres);
                    $("#usuariou").val(json.usuario);
                    $("#emailEditar").val(json.email);
                    $("#telefonoEditar").val(json.telefono);
                    $("#rotativou").val(json.rotativo || 0);
                    $("#sueldou").val(json.sueldo || 0);
                    $("#idCliente").val(id);
                    vm.loadEditPhoto(json.foto_perfil);
                },
                error() { $("#loader-menor").hide(); }
            });
        }
    });
});

// ===== Guardar edición =====
$("#updateCliente").click(function() {
    if (!validarFormulario({ docId: 'doc', nombresId: 'datosEditar', telefonoId: 'telefonoEditar', emailId: 'emailEditar' })) return;

    $("#loader-menor").show();
    const formData = new FormData(document.getElementById('clientesEditar'));
    formData.append('idCliente', $("#idCliente").val());

    $.ajax({
        url: _URL + "/ajs/usuarios/editar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success(resp) {
            $("#loader-menor").hide();
            const response = typeof resp === 'string' ? JSON.parse(resp) : resp;

            if (response.success) {
                if (response.new_token) {
                    localStorage.setItem("_token", response.new_token);
                    if (response.foto_perfil) {
                        const imgUrl = response.foto_perfil.includes('/') ? response.foto_perfil : _URL + '/img/usuarios/' + response.foto_perfil;
                        $('.jvc-user-avatar').attr('src', imgUrl);
                    }
                }
                tabla_clientes.ajax.reload(null, false);
                Swal.fire("¡Buen trabajo!", "Actualización exitosa", "success");
                $("#editarModal").modal("hide");
                $("body").removeClass("modal-open");
            } else {
                Swal.fire({ icon: "error", title: "Error", text: response.error || "Error al actualizar usuario" });
            }
        },
        error(xhr, status, error) {
            $("#loader-menor").hide();
            Swal.fire({ icon: "error", title: "Error", text: "Error de conexión: " + error });
        }
    });
});

// ===== Eliminar usuario =====
$("#tabla_clientes").on("click", ".btnBorrar", function() {
    const id = $(this).data("id");
    Swal.fire({
        title: "¿Deseas borrar el registro?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si"
    }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: _URL + "/ajs/usuarios/borrar",
            type: "post",
            data: { value: id },
            success(resp) {
                const response = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (response.error) {
                    Swal.fire({ icon: "error", title: "Error", text: response.error });
                    return;
                }
                tabla_clientes.ajax.reload(null, false);
                Swal.fire("¡Buen trabajo!", "Registro Borrado Exitosamente", "success");
            }
        });
    });
});

// ===== Agregar usuario =====
$('#add-user').on('click', function() {
    $.ajax({
        type: "POST",
        url: _URL + "/ajs/getroles",
        success(response) {
            const data = JSON.parse(response);
            const options = data.map(d => `<option value="${d.rol_id}">${d.nombre}</option>`).join('');
            $('#rol').html(options);
            $('#usuario-add-bs').modal('show');
        }
    });
});

$('#submitButton').click(function() {
    if (!validarFormulario({ docId: 'ndoc', nombresId: 'nombres', telefonoId: 'telefono', emailId: 'email' })) return;

    if (!$('#clave').val().trim()) {
        Swal.fire({ title: "Error", text: "La clave es obligatoria.", icon: "error" });
        return;
    }

    const formData = new FormData(document.getElementById('myForm'));
    $.ajax({
        url: _URL + "/ajs/add/users",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success(response) {
            const resp = typeof response === 'string' ? JSON.parse(response) : response;
            if (resp.success) {
                Swal.fire({ title: "Éxito", text: "Usuario creado correctamente.", icon: "success" });
                $('#usuario-add-bs').modal('hide');
                $('#myForm')[0].reset();
                vm.resetAddPreview();
                tabla_clientes.ajax.reload(null, false);
            } else {
                Swal.fire({ title: "Error", text: resp.error || "No se pudo crear el usuario.", icon: "error" });
            }
        },
        error() {
            Swal.fire({ title: "Error", text: "Hubo un problema al crear el usuario.", icon: "error" });
        }
    });
});

$('#usuario-add-bs').on('hidden.bs.modal', function() {
    vm.resetAddPreview();
    $('#foto_perfil').val('');
});

// ===== Gestión de Roles =====

var tabla_roles;

function toggleSubmodulos(moduloId) {
    const $sub = $(`#submodulos_${moduloId}`);
    if (!$sub.length) return;
    if ($(`#modulo_${moduloId}`).prop('checked')) {
        $sub.slideDown(200);
    } else {
        $sub.find('input[type="checkbox"]').prop('checked', false);
        $sub.slideUp(200);
    }
}
window.toggleSubmodulos = toggleSubmodulos;

function cargarTablaRoles() {
    if ($.fn.DataTable.isDataTable('#tabla_roles')) {
        $('#tabla_roles').DataTable().destroy();
    }
    tabla_roles = $("#tabla_roles").DataTable({
        paging: true,
        bFilter: true,
        ordering: true,
        searching: true,
        destroy: true,
        ajax: { url: _URL + "/ajs/roles/render", method: "POST", dataSrc: "" },
        language: { url: "ServerSide/Spanish.json" },
        columns: [
            { data: "rol_id", class: "text-center" },
            { data: "nombre", class: "text-center" },
            {
                data: null, class: "text-center",
                render(data, type, row) {
                    const delBtn = row.rol_id == 1 ? '' :
                        `<button data-id="${row.rol_id}" class="btn btn-sm btn-danger btnBorrarRol"><i class="fa fa-trash"></i></button>`;
                    return `<div class="btn-group btn-sm">
                        <button data-id="${row.rol_id}" class="btn btn-sm btn-warning btnEditarRol"><i class="fa fa-edit"></i></button>
                        ${delBtn}
                    </div>`;
                }
            }
        ]
    });
}

function cargarModulos(selMods = [], selSubs = []) {
    $.ajax({
        url: _URL + "/ajs/roles/getModulosYSubmodulos",
        type: "POST",
        success(response) {
            const data = JSON.parse(response);
            const mitad = Math.ceil(data.length / 2);

            $('#modulos-izquierda').html(data.slice(0, mitad).map(m => renderModulo(m, selMods, selSubs)).join(''));
            $('#modulos-derecha').html(data.slice(mitad).map(m => renderModulo(m, selMods, selSubs)).join(''));

            $('.modulo-check').on('change', function() {
                const id = $(this).val();
                $(`#submodulos_${id} .submodulo-check`).prop('checked', $(this).prop('checked'));
            });

            $('.submodulo-check').on('change', function() {
                const id = $(this).data('modulo');
                const checked = $(`#submodulos_${id} .submodulo-check:checked`).length;
                $(`#modulo_${id}`).prop('checked', checked > 0);
            });
        },
        error() {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudieron cargar los módulos" });
        }
    });
}

$('#manage-roles').on('click', function() {
    cargarTablaRoles();
    $('#roles-modal').modal('show');
});

$('#add-rol').on('click', function() {
    $('#rolEditModalLabel').html('<i class="fa fa-plus me-2"></i>Crear Rol');
    $('#rol_id').val('');
    $('#nombre_rol').val('');
    $('#ver_precios').prop('checked', false);
    $('#puede_eliminar').prop('checked', false);
    $('#roles-modal').modal('hide');
    cargarModulos();
    setTimeout(() => { $('#rol-edit-modal').modal('show'); }, 500);
});

$("#tabla_roles").on("click", ".btnEditarRol", function() {
    const id = $(this).data("id");
    $('#rolEditModalLabel').html('<i class="fa fa-edit me-2"></i>Editar Rol');
    $('#roles-modal').modal('hide');

    $.ajax({
        url: _URL + "/ajs/roles/getRolPermisos",
        type: "POST",
        data: { id },
        success(response) {
            const data = JSON.parse(response);
            $('#rol_id').val(data.rol.rol_id);
            $('#nombre_rol').val(data.rol.nombre);
            if ('ver_precios' in data.rol) $('#ver_precios').prop('checked', data.rol.ver_precios == 1);
            if ('puede_eliminar' in data.rol) $('#puede_eliminar').prop('checked', data.rol.puede_eliminar == 1);
            cargarModulos(data.modulos || [], data.submodulos || []);
            setTimeout(() => { $('#rol-edit-modal').modal('show'); }, 500);
        },
        error() {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo cargar la información del rol" });
        }
    });
});

$('#rol-edit-modal').on('hidden.bs.modal', function() {
    setTimeout(() => { $('#roles-modal').modal('show'); });
});

$('#guardarRol').on('click', function() {
    if (!$('#nombre_rol').val()) {
        Swal.fire({ icon: "error", title: "Error", text: "El nombre del rol es obligatorio" });
        return;
    }
    const rolId = $('#rol_id').val();
    const url = rolId ? _URL + "/ajs/roles/editar" : _URL + "/ajs/roles/crear";

    $.ajax({
        url,
        type: "POST",
        data: $('#rolForm').serialize(),
        success(response) {
            const data = JSON.parse(response);
            if (data.error) {
                Swal.fire({ icon: "error", title: "Error", text: data.error });
                return;
            }
            Swal.fire({ icon: "success", title: "Éxito", text: data.message });
            $('#rol-edit-modal').modal('hide');
            tabla_roles.ajax.reload(null, false);
        },
        error() {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo guardar el rol" });
        }
    });
});

$("#tabla_roles").on("click", ".btnBorrarRol", function() {
    const id = $(this).data("id");
    Swal.fire({
        title: "¿Deseas eliminar este rol?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: _URL + "/ajs/roles/borrar",
            type: "POST",
            data: { id },
            success(response) {
                const data = JSON.parse(response);
                if (data.error) {
                    Swal.fire({ icon: "error", title: "Error", text: data.error });
                    return;
                }
                Swal.fire({ icon: "success", title: "Éxito", text: data.message });
                tabla_roles.ajax.reload(null, false);
            },
            error() {
                Swal.fire({ icon: "error", title: "Error", text: "No se pudo eliminar el rol" });
            }
        });
    });
});
</script>
