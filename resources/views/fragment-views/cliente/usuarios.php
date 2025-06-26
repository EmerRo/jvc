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
    .bg-gris {
        background-color: rgb(107, 76, 76);
        color: #000;

    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white py-2">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" id="add-user" class="btn bg-rojo text-white "><i
                                class="fa fa-plus me-1 "></i>Agregar</button>
                        <button type="button" id="manage-roles" class="btn border-rojo"><i
                                class="fa fa-cogs me-1"></i>Gestionar Roles</button>
                    </div>
                </div>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body p-3">
                    <!-- MODAL CONFIRMAR DATOS -->
                    <div class="modal fade" id="modal-lista-clientes" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header py-2">
                                    <h5 class="modal-title" id="staticBackdropLabel">Lista de clientes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <table class="table table-sm table-bordered text-center" id="tablaImportarCliente">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Documento</th>
                                                <th>Datos</th>
                                                <th>Dirección</th>
                                                <th>Dirección 2</th>
                                                <th>Teléfono</th>
                                                <th>Teléfono 2</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyImportar">
                                            <tr id="trImportar" v-for="(item,index) in listaClientes">
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
                                    <button @click="agregarListaImport" type="button"
                                        class="btn btn-primary btn-sm">Guardar</button>
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL DE IMPORTAR XLS -->
                    <div class="modal fade" id="importarModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header py-2">
                                    <h5 class="modal-title" id="exampleModalLabel">Importar Cliente con EXCEL</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <form enctype='multipart/form-data'>
                                        <div class="mb-3">
                                            <p class="small">Descargue el modelo en <span class="fw-bold">EXCEL</span>
                                                para importar, no
                                                modifique los campos en el archivo, <span class="fw-bold">click para
                                                    descargar</span> <a
                                                    href="<?= URL::to("public/templateExcelClientes.xlsx") ?>">template.xlsx</a>
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
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tabla_clientes"
                            class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Rol</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Nombres</th>
                                    <th>Teléfono</th>
                                    <!-- <th>Tienda</th> -->
                                    <!-- <th>Rotativo</th> -->
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL AGREGAR USUARIO -->
<div class="modal fade" id="usuario-add-bs" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 bg-danger text-white">
                <h5 class="modal-title" id="exampleModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="myForm">
                    <div class="row g-3">
                        <!-- Primera fila -->
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-user-tag me-1"></i>Rol
                            </label>
                            <select name="rol" id="rol" class="form-select form-select-sm">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-signature me-1"></i>Nombres
                            </label>
                            <input type="text" name="nombres" id="nombres" class="form-control form-control-sm"
                                required>
                        </div>

                        <!-- Segunda fila -->
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-id-card me-1"></i>Número de documento
                            </label>
                            <input type="text" name="ndoc" id="ndoc" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-user-circle me-1"></i>Usuario
                            </label>
                            <input type="text" name="usuario" id="usuario" class="form-control form-control-sm"
                                required>
                        </div>

                        <!-- Tercera fila -->
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-key me-1"></i>Clave
                            </label>
                            <input type="password" name="clave" id="clave" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-phone me-1"></i>Teléfono
                            </label>
                            <input type="text" name="telefono" id="telefono" class="form-control form-control-sm">
                        </div>

                        <!-- Cuarta fila -->
                        <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-envelope me-1"></i>Correo
                            </label>
                            <input type="email" name="email" id="email" class="form-control form-control-sm" required>
                        </div>
                        <!-- <div class="col-md-6">
                            <label class="form-label ">
                                <i class="fa fa-store me-1"></i>Tienda
                            </label>
                            <select name="tienda" id="tiendau" class="form-select form-select-sm">
                                <option value="1">Tienda 435</option>
                                <option value="2">Tienda 426</option>
                            </select>
                        </div> -->
                        <!-- <div class="col-md-3">
                            <label class="form-label ">Rotativo</label>
                            <select name="rotativo" id="rotativou" class="form-select form-select-sm">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cerrar
                </button>
                <button type="button" id="submitButton" class="btn bg-rojo text-white ">
                    <i class="fa fa-save me-1"></i>Crear
                </button>
            </div>
        </div>
    </div>
</div>
<!-- EDITAR MODAL -->
<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 bg-danger text-white">
                <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="clientesEditar">
                    <div class="row g-3">
                        <input type="text" name="idCliente" id="idCliente" value="" hidden>

                        <!-- Primera fila -->
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-user-tag me-1"></i>Rol
                            </label>
                            <select name="rol" id="rol2" class="form-select form-select-sm">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-signature me-1"></i>Nombre
                            </label>
                            <input type="text" class="form-control form-control-sm" id="datosEditar" name="datosEditar">
                        </div>

                        <!-- Segunda fila -->
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-id-card me-1"></i>Número de documento
                            </label>
                            <input type="text" class="form-control form-control-sm" id="doc" name="doc">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-user-circle me-1"></i>Usuario
                            </label>
                            <input type="text" class="form-control form-control-sm" id="usuariou" name="usuariou">
                        </div>

                        <!-- Tercera fila -->
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-key me-1"></i>Clave
                            </label>
                            <input type="password" class="form-control form-control-sm" id="claveu" name="claveu">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-phone me-1"></i>Teléfono
                            </label>
                            <input type="text" class="form-control form-control-sm" id="telefonoEditar"
                                name="telefonoEditar">
                        </div>

                        <!-- Cuarta fila -->
                        <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-envelope me-1"></i>Email
                            </label>
                            <input required type="email" class="form-control form-control-sm" id="emailEditar"
                                name="emailEditar">
                        </div>
                        <!-- <div class="col-md-6">
                            <label class="form-label small">
                                <i class="fa fa-store me-1"></i>Tienda
                            </label>
                            <select name="tiendau" id="tiendau" class="form-select form-select-sm">
                                <option value="1">Tienda 435</option>
                                <option value="2">Tienda 426</option>
                            </select>
                        </div> -->
                        <!-- <div class="col-md-3">
                            <label class="form-label small">Rotativo</label>
                            <select name="rotativou" id="rotativou" class="form-select form-select-sm">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cerrar
                </button>
                <button id="updateCliente" type="button" class="btn btn-primary btn-sm">
                    <i class="fa fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA GESTIONAR ROLES -->
<div class="modal fade" id="roles-modal" tabindex="-1" aria-labelledby="rolesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 bg-rojo text-white">
                <h5 class="modal-title" id="rolesModalLabel">
                    <i class="fa fa-user-shield me-1"></i>Gestión de Roles
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <button type="button" id="add-rol" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i>Agregar Rol
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="tabla_roles"
                        class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA CREAR/EDITAR ROL -->
<div class="modal fade" id="rol-edit-modal" tabindex="-1" aria-labelledby="rolEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 bg-danger text-white">
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
                            <span class="badge bg-danger text-white">
                                Seleccione accesos
                            </span>
                        </div>
                        
                        <div class="card border shadow-sm">
                            <div class="card-body p-0">
                                <!-- Contenedor con un solo scroll para ambas columnas -->
                                <div class="modulos-scroll-container" style="max-height: 40vh; overflow-y: auto;">
                                    <div class="row g-0">
                                        <!-- Columna izquierda para módulos -->
                                        <div class="col-md-6 border-end" id="modulos-izquierda">
                                            <!-- Los módulos de la izquierda se cargarán aquí -->
                                        </div>
                                        <!-- Columna derecha para módulos -->
                                        <div class="col-md-6" id="modulos-derecha">
                                            <!-- Los módulos de la derecha se cargarán aquí -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Cancelar
                </button>
                <button type="button" id="guardarRol" class="btn btn-primary btn-sm">
                    <i class="fa fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
<style>
    /* Estilos para los módulos y submódulos */
    .modulo-item {
        padding: 10px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        transition: background-color 0.2s;
    }
    
    .modulo-item:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
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
    
    .submodulo-item {
        padding: 4px 0;
    }
    
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
</style>
<style>
    /* Estilos para los módulos y submódulos */
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
    
    .modulo-item:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
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
    
    .submodulo-item {
        padding: 4px 0;
    }
    
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
    
    /* Asegurar que los submódulos no se desborden */
    #modulos-izquierda, #modulos-derecha {
        min-height: 100%;
    }
</style>
<script>
    $(document).ready(function () {

        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/usuarios/render",
                method: "POST",
                dataSrc: "",
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columns: [{
                data: "usuario_id",
                class: "text-center",
            },
            {
                data: "nombre",
                class: "text-center",
            },
            {
                data: "usuario",
                class: "text-center",
            },
            {
                data: "email",
                class: "text-center",
            },
            {
                data: "nombres",
                class: "text-center",
            },
            {
                data: "telefono",
                class: "text-center",
            },
            // {
            //     data: "tienda",
            //     class: "text-center",
            // },
            // {
            //     data: "rotativo",
            //     class: "text-center",
            // },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return `<div class="text-center">
            <div class="btn-group btn-sm"><button  data-id="${Number(row.usuario_id)}" class="btn btn-sm btn-warning btnEditar"
            ><i class="fa fa-edit"></i> </button>
            <button btn-sm  data-id="${Number(row.usuario_id)}" class="btn btn-sm  btn-danger btnBorrar"><i class="fa fa-trash"></i> </button>
            </div></div>`;
                },
            },
            ],
        });

        $("#tabla_clientes").on("click", ".btnEditar ", function (event) {
            $("#loader-menor").show();
            var table = $("#tabla_clientes").DataTable();
            var trid = $(this).closest("tr").attr("id");
            var id = $(this).data("id");
            $("#editarModal").modal("show");
            $("#editarModal")
                .find(".modal-title")
                .text("Editar Usuario N°" + id);
            $.ajax({
                url: _URL + "/ajs/usuarios/getOne",
                data: {
                    id: id,
                },
                type: "post",
                success: function (datos) {
                    $.ajax({
                        type: "POST",
                        url: _URL + "/ajs/getroles",
                        success: function (response) {
                            let data = JSON.parse(response);
                            let options = '';
                            $.each(data, function (i, d) {
                                options += `<option value="${d.rol_id}">${d.nombre}</option>`;
                            });
                            $('#rol2').html(options);
                            $("#loader-menor").hide();
                            let json = JSON.parse(datos)[0];
                            $("#rol2").val(json.id_rol);
                            $("#doc").val(json.num_doc);
                            $("#datosEditar").val(json.nombres);
                            $("#usuariou").val(json.usuario);
                            $("#emailEditar").val(json.email);
                            $("#telefonoEditar").val(json.telefono);
                            $("#tiendau").val(json.sucursal);
                            $("#rotativou").val(json.rotativo);
                            $("#idCliente").val(id);
                            $("#trid").val(trid);
                        },
                        error: function (response) {
                            console.log(response);
                        }
                    });
                },
            });
        });

        $("#updateCliente").click(function () {
            $("#loader-menor").show();
            let data = $("#clientesEditar").serializeArray();
            let id = $("#idCliente").val();

            $.ajax({
                url: _URL + "/ajs/usuarios/editar",
                type: "POST",
                data: data,
                success: function (resp) {
                    $("#loader-menor").hide();
                    console.log(resp);
                    if (Array.isArray(data)) {
                        tabla_clientes.ajax.reload(null, false);
                        Swal.fire("¡Buen trabajo!", "Actualización exitosa", "success");
                        $("#editarModal").modal("hide");
                        $("body").removeClass("modal-open");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: JSON.parse(resp),
                        });
                    }
                },
            });
        });

        $("#tabla_clientes").on("click", ".btnBorrar", function () {
            var id = $(this).data("id");
            let idData = {
                value: id,
            };
            Swal.fire({
                title: "¿Deseas borrar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/usuarios/borrar",
                        type: "post",
                        data: idData,
                        success: function (resp) {
                            const response = typeof resp === 'string' ? JSON.parse(resp) : resp;

                            if (response.error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: response.error,
                                });
                                return;
                            }

                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire(
                                "¡Buen trabajo!",
                                "Registro Borrado Exitosamente",
                                "success"
                            );
                        },
                    });
                }
            });
        });

        $('#add-user').on('click', function () {
            $.ajax({
                type: "POST",
                url: _URL + "/ajs/getroles",
                success: function (response) {
                    let data = JSON.parse(response);
                    let options = '';
                    $.each(data, function (i, d) {
                        options += `<option value="${d.rol_id}">${d.nombre}</option>`;
                    });
                    $('#rol').html(options);
                    $('#rol2').html(options);
                    $('#usuario-add-bs').modal('show');
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });

        $('#submitButton').click(function () {
            if ($('#rol').val() && $('#ndoc').val() && $('#usuario').val() && $('#clave').val() && $('#email').val() && $('#nombres').val()) {
                var formData = $('#myForm').serialize();

                $.ajax({
                    url: _URL + "/ajs/add/users",
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        Swal.fire({
                            title: "Éxito",
                            text: "Usuario creado correctamente.",
                            icon: "success"
                        });
                        $('#usuario-add-bs').modal('hide');
                        tabla_clientes.ajax.reload(null, false);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: "Error",
                            text: "Hubo un problema al crear el usuario.",
                            icon: "error"
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Por favor, completa todos los campos obligatorios.",
                    icon: "error"
                });
            }
        });

        // GESTIÓN DE ROLES

        // Abrir modal de gestión de roles
        $('#manage-roles').on('click', function () {
            cargarTablaRoles();
            $('#roles-modal').modal('show');
        });

        // Inicializar DataTable para roles
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
                ajax: {
                    url: _URL + "/ajs/roles/render",
                    method: "POST",
                    dataSrc: "",
                },
                language: {
                    url: "ServerSide/Spanish.json",
                },
                columns: [
                    {
                        data: "rol_id",
                        class: "text-center",
                    },
                    {
                        data: "nombre",
                        class: "text-center",
                    },
                    {
                        data: null,
                        class: "text-center",
                        render: function (data, type, row) {
                            // No mostrar botón de eliminar para el rol ADMIN (rol_id = 1)
                            let deleteButton = row.rol_id == 1 ? '' :
                                `<button data-id="${row.rol_id}" class="btn btn-sm btn-danger btnBorrarRol">
                                    <i class="fa fa-trash"></i>
                                </button>`;

                            return `<div class="text-center">
                                <div class="btn-group btn-sm">
                                    <button data-id="${row.rol_id}" class="btn btn-sm btn-warning btnEditarRol">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    ${deleteButton}
                                </div>
                            </div>`;
                        },
                    },
                ],
            });
        }

        // Función para mostrar/ocultar submódulos
        function toggleSubmodulos(moduloId) {
            const moduloCheck = $(`#modulo_${moduloId}`);
            const submodulosContainer = $(`#submodulos_${moduloId}`);

            if (!submodulosContainer.length) {
                return; // Si no hay submódulos, no hacer nada
            }

            if (moduloCheck.prop('checked')) {
                submodulosContainer.slideDown(200);
            } else {
                // Si se desmarca el módulo, desmarcar todos sus submódulos
                submodulosContainer.find('input[type="checkbox"]').prop('checked', false);
                submodulosContainer.slideUp(200);
            }
        }

        // Cargar módulos y submódulos en dos columnas separadas
        function cargarModulos(modulosSeleccionados = [], submodulosSeleccionados = []) {
            $.ajax({
                url: _URL + "/ajs/roles/getModulosYSubmodulos",
                type: "POST",
                success: function (response) {
                    const data = JSON.parse(response);
                    
                    // Dividir los módulos en dos grupos para las columnas izquierda y derecha
                    const mitad = Math.ceil(data.length / 2);
                    const modulosIzquierda = data.slice(0, mitad);
                    const modulosDerecha = data.slice(mitad);
                    
                    // Generar HTML para la columna izquierda
                    let htmlIzquierda = '';
                    modulosIzquierda.forEach(function(modulo) {
                        const moduloChecked = modulosSeleccionados.includes(parseInt(modulo.modulo_id)) ? 'checked' : '';
                        const tieneSubmodulos = modulo.submodulos && modulo.submodulos.length > 0;
                        
                        htmlIzquierda += `
                        <div class="modulo-item">
                            <div class="modulo-header">
                                <div class="form-check">
                                    <input class="form-check-input modulo-check" type="checkbox" 
                                        name="modulos[]" value="${modulo.modulo_id}" 
                                        id="modulo_${modulo.modulo_id}" ${moduloChecked}
                                        onchange="toggleSubmodulos(${modulo.modulo_id})">
                                    <label class="form-check-label" for="modulo_${modulo.modulo_id}">
                                        <i class="${modulo.icono}"></i>
                                        <span>${modulo.nombre}</span>
                                    </label>
                                </div>
                            </div>`;
                        
                        if (tieneSubmodulos) {
                            htmlIzquierda += `<div class="submodulos-container" id="submodulos_${modulo.modulo_id}"${moduloChecked ? '' : ' style="display:none;"'}>`;
                            modulo.submodulos.forEach(function(submodulo) {
                                const submoduloChecked = submodulosSeleccionados.includes(parseInt(submodulo.submodulo_id)) ? 'checked' : '';
                                htmlIzquierda += `
                                <div class="submodulo-item">
                                    <div class="form-check">
                                        <input class="form-check-input submodulo-check" type="checkbox" 
                                            name="submodulos[]" value="${submodulo.submodulo_id}" 
                                            id="submodulo_${submodulo.submodulo_id}" ${submoduloChecked}
                                            data-modulo="${modulo.modulo_id}">
                                        <label class="form-check-label" for="submodulo_${submodulo.submodulo_id}">
                                            <i class="fa fa-circle-dot"></i>
                                            ${submodulo.nombre}
                                        </label>
                                    </div>
                                </div>`;
                            });
                            htmlIzquierda += '</div>';
                        }
                        
                        htmlIzquierda += '</div>';
                    });
                    
                    // Generar HTML para la columna derecha
                    let htmlDerecha = '';
                    modulosDerecha.forEach(function(modulo) {
                        const moduloChecked = modulosSeleccionados.includes(parseInt(modulo.modulo_id)) ? 'checked' : '';
                        const tieneSubmodulos = modulo.submodulos && modulo.submodulos.length > 0;
                        
                        htmlDerecha += `
                        <div class="modulo-item">
                            <div class="modulo-header">
                                <div class="form-check">
                                    <input class="form-check-input modulo-check" type="checkbox" 
                                        name="modulos[]" value="${modulo.modulo_id}" 
                                        id="modulo_${modulo.modulo_id}" ${moduloChecked}
                                        onchange="toggleSubmodulos(${modulo.modulo_id})">
                                    <label class="form-check-label" for="modulo_${modulo.modulo_id}">
                                        <i class="${modulo.icono}"></i>
                                        <span>${modulo.nombre}</span>
                                    </label>
                                </div>
                            </div>`;
                        
                        if (tieneSubmodulos) {
                            htmlDerecha += `<div class="submodulos-container" id="submodulos_${modulo.modulo_id}"${moduloChecked ? '' : ' style="display:none;"'}>`;
                            modulo.submodulos.forEach(function(submodulo) {
                                const submoduloChecked = submodulosSeleccionados.includes(parseInt(submodulo.submodulo_id)) ? 'checked' : '';
                                htmlDerecha += `
                                <div class="submodulo-item">
                                    <div class="form-check">
                                        <input class="form-check-input submodulo-check" type="checkbox" 
                                            name="submodulos[]" value="${submodulo.submodulo_id}" 
                                            id="submodulo_${submodulo.submodulo_id}" ${submoduloChecked}
                                            data-modulo="${modulo.modulo_id}">
                                        <label class="form-check-label" for="submodulo_${submodulo.submodulo_id}">
                                            <i class="fa fa-circle-dot"></i>
                                            ${submodulo.nombre}
                                        </label>
                                    </div>
                                </div>`;
                            });
                            htmlDerecha += '</div>';
                        }
                        
                        htmlDerecha += '</div>';
                    });
                    
                    // Insertar el HTML en los contenedores
                    $('#modulos-izquierda').html(htmlIzquierda);
                    $('#modulos-derecha').html(htmlDerecha);
                    
                    // Añadir evento para seleccionar todos los submódulos cuando se selecciona un módulo
                    $('.modulo-check').on('change', function() {
                        const moduloId = $(this).val();
                        const isChecked = $(this).prop('checked');
                        
                        $(`#submodulos_${moduloId} .submodulo-check`).prop('checked', isChecked);
                    });
                    
                    // Añadir evento para verificar si todos los submódulos están seleccionados
                    $('.submodulo-check').on('change', function() {
                        const moduloId = $(this).data('modulo');
                        const totalSubmodulos = $(`#submodulos_${moduloId} .submodulo-check`).length;
                        const checkedSubmodulos = $(`#submodulos_${moduloId} .submodulo-check:checked`).length;
                        
                        // Si todos los submódulos están seleccionados, seleccionar el módulo
                        if (totalSubmodulos === checkedSubmodulos && checkedSubmodulos > 0) {
                            $(`#modulo_${moduloId}`).prop('checked', true);
                        }
                        // Si al menos un submódulo está seleccionado pero no todos, mantener el módulo seleccionado
                        else if (checkedSubmodulos > 0) {
                            $(`#modulo_${moduloId}`).prop('checked', true);
                        }
                        // Si ningún submódulo está seleccionado, deseleccionar el módulo
                        else {
                            $(`#modulo_${moduloId}`).prop('checked', false);
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar módulos:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudieron cargar los módulos",
                    });
                }
            });
        }

        // Hacer disponible la función toggleSubmodulos globalmente
        window.toggleSubmodulos = toggleSubmodulos;

        // Abrir modal para crear rol
        $('#add-rol').on('click', function () {
            $('#rolEditModalLabel').html('<i class="fa fa-plus me-2"></i>Crear Rol');
            $('#rol_id').val('');
            $('#nombre_rol').val('');
            $('#ver_precios').prop('checked', false);
            $('#puede_eliminar').prop('checked', false);

            // Ocultar el modal de gestión de roles antes de mostrar el de creación
            $('#roles-modal').modal('hide');

            cargarModulos();

            // Mostrar el modal de creación después de un pequeño retraso
            setTimeout(function () {
                $('#rol-edit-modal').modal('show');
            }, 500);
        });

        // Editar rol
        $("#tabla_roles").on("click", ".btnEditarRol", function () {
            const id = $(this).data("id");
            $('#rolEditModalLabel').html('<i class="fa fa-edit me-2"></i>Editar Rol');

            // Ocultar el modal de gestión de roles antes de mostrar el de edición
            $('#roles-modal').modal('hide');

            $.ajax({
                url: _URL + "/ajs/roles/getOne",
                type: "POST",
                data: { id: id },
                success: function (response) {
                    const data = JSON.parse(response);
                    $('#rol_id').val(data.rol.rol_id);
                    $('#nombre_rol').val(data.rol.nombre);

                    // Configurar checkboxes de permisos si existen en la respuesta
                    if (data.rol.hasOwnProperty('ver_precios')) {
                        $('#ver_precios').prop('checked', data.rol.ver_precios == 1);
                    }
                    if (data.rol.hasOwnProperty('puede_eliminar')) {
                        $('#puede_eliminar').prop('checked', data.rol.puede_eliminar == 1);
                    }

                    cargarModulos(
                        data.modulos.map(id => parseInt(id)),
                        data.submodulos ? data.submodulos.map(id => parseInt(id)) : []
                    );

                    // Mostrar el modal de edición después de cargar los datos
                    setTimeout(function () {
                        $('#rol-edit-modal').modal('show');
                    }, 500); // Pequeño retraso para asegurar que el primer modal se cierre completamente
                },
                error: function (xhr, status, error) {
                    console.error("Error al obtener rol:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo cargar la información del rol",
                    });
                }
            });
        });

        // Cuando se cierra el modal de edición, volver a mostrar el modal de gestión
        $('#rol-edit-modal').on('hidden.bs.modal', function () {
            setTimeout(function () {
                $('#roles-modal').modal('show');
            });
        });

        // Guardar rol (crear o editar)
        $('#guardarRol').on('click', function () {
            if (!$('#nombre_rol').val()) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "El nombre del rol es obligatorio",
                });
                return;
            }

            const formData = $('#rolForm').serialize();
            const rolId = $('#rol_id').val();
            const url = rolId ? _URL + "/ajs/roles/editar" : _URL + "/ajs/roles/crear";

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                success: function (response) {
                    const data = JSON.parse(response);

                    if (data.error) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.error,
                        });
                        return;
                    }

                    Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: data.message,
                    });

                    // Cerrar el modal de edición
                    $('#rol-edit-modal').modal('hide');

                    // Recargar la tabla de roles
                    tabla_roles.ajax.reload(null, false);
                },
                error: function (xhr, status, error) {
                    console.error("Error al guardar rol:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo guardar el rol",
                    });
                }
            });
        });

        // Eliminar rol
        $("#tabla_roles").on("click", ".btnBorrarRol", function () {
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
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/roles/borrar",
                        type: "POST",
                        data: { id: id },
                        success: function (response) {
                            const data = JSON.parse(response);

                            if (data.error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: data.error,
                                });
                                return;
                            }

                            Swal.fire({
                                icon: "success",
                                title: "Éxito",
                                text: data.message,
                            });

                            tabla_roles.ajax.reload(null, false);
                        },
                        error: function (xhr, status, error) {
                            console.error("Error al eliminar rol:", error);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "No se pudo eliminar el rol",
                            });
                        }
                    });
                }
            });
        });

        // Función para mejorar la visualización de los módulos
        function mejorarVisualizacionModulos() {
            // Añadir iconos a los checkboxes de módulos si no los tienen
            $('.modulo-check').each(function () {
                const label = $(this).next('label');
                if (!label.find('i').length) {
                    label.prepend('<i class="fa fa-folder me-2"></i>');
                }
            });

            // Añadir iconos a los checkboxes de submódulos si no los tienen
            $('.submodulo-check').each(function () {
                const label = $(this).next('label');
                if (!label.find('i').length) {
                    label.prepend('<i class="fa fa-file me-2"></i>');
                }
            });

            // Mejorar el estilo de los contenedores de submódulos
            $('.submodulos-container').each(function () {
                if (!$(this).hasClass('styled')) {
                    $(this).addClass('styled p-2 border-start border-3 ms-4 mt-2');
                }
            });
        }

        // Llamar a la función después de cargar los módulos
        $(document).on('DOMNodeInserted', '#modulos-izquierda, #modulos-derecha', function () {
            setTimeout(mejorarVisualizacionModulos, 100);
        });
    });
</script>