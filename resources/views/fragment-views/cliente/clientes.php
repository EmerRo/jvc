<?php

require_once "app/models/Cliente.php";

$c_cliente = new Cliente();
$c_cliente->setIdEmpresa($_SESSION['id_empresa']);

?>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">DATOS DE CLIENTES</h6>

        </div>

    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                <button type="button" data-bs-toggle="modal" data-bs-target="#importarModal"
                    class="btn border-rojo bg-white me-2"><i class="fa fa-file-excel"></i> Importar</button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#agregarModal"
                    class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Agregar</button>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <!-- MODAL CONFIRMAR DATOS -->
                    <div class="modal fade" id="modal-lista-clientes" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog  modal-dialog-scrollable modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Lista de clientes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table  class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="tablaImportarCliente">
                                        <thead>
                                            <tr>
                                                <th>Documento</th>
                                                <th>Datos</th>
                                                <th>Direccion</th>
                                                <th>Direccion 2</th>
                                                <th>Telefono</th>
                                                <th>Telefon 2</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyImportar">
                                            <!--  <tr id="trImportar"></tr> -->
                                            <tr id="trImportar" v-for="(item,index) in listaClientes">
                                                <!--  -->
                                                <td>{{item.documento}}</td>
                                                <td> {{item.datos}}</td>
                                                <td>{{item.direccion}}</td>
                                                <td>{{item.direccion2}}</td>
                                                <td>{{item.telefono}}</td>
                                                <td>{{item.telefono2}}</td>
                                                <td>{{item.email}}</td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <!--  <button id="agregarClientesImport" type="button" class="btn btn-primary">Guardar</button> -->
                                    <button @click="agregarListaImport" type="button"
                                        class="btn bg-rojo text-white">Guardar</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL DE IMPORTAR XLS -->
                    <div class="modal fade" id="importarModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title" id="exampleModalLabel">Importar Cliente con EXCEL</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form enctype='multipart/form-data'>
                                        <div class="mb-3">
                                            <p>Descargue el modelo en <span class="fw-bold">EXCEL</span> para importar,
                                                no
                                                modifique los campos en el archivo, <span class="fw-bold">click para
                                                    descargar</span> <a
                                                    href="<?= URL::to("public/templateExcelClientes.xlsx") ?>">template.xlsx</a>
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="col-form-label">Importar Excel:</label>

                                        </div>
                                        <input type="file" id="nuevoExcel" name="nuevoExcel"
                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-danger" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL DE AGREGAR CLIENTE -->
                    <div class="modal fade" id="agregarModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title" id="exampleModalLabel">Agregar</h5>
                                </div>
                                <div class="modal-body">
                                    <!-- Modificaciones para el formulario de agregar cliente -->
                                    <form id="frmClientesAgregar">
                                        <div class="row p-2">
                                            <!-- Fila 1 -->
                                            <div class="col-md-6">
                                                <label>DNI o RUC<span style="color: red;"> (*)</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" required maxlength="11"
                                                        id="documentoAgregar" name="documentoAgregar">
                                                    <div class="input-group-prepend">
                                                        <button id="btnBuscarInfo" class="btn bg-rojo text-white">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="text-danger error-msg" id="error-documentoAgregar"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Nombre/Razon Social <span style="color: red;">(*)</span></label>
                                                <input type="text" class="form-control" id="datosAgregar"
                                                    name="datosAgregar" required>
                                                <p class="text-danger error-msg" id="error-datosAgregar"></p>
                                            </div>

                                            <!-- Fila 2 -->
                                            <div class="col-md-6">
                                                <label>Dirección</label>
                                                <input type="text" class="form-control" id="direccionAgregar"
                                                    name="direccionAgregar">
                                                <p class="text-danger error-msg" id="error-direccionAgregar"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Dirección de Llegada <small>(opcional)</small></label>
                                                <input type="text" class="form-control" id="direccionAgregar2"
                                                    name="direccionAgregar2">
                                            </div>

                                            <!-- Fila 3 -->
                                            <div class="col-md-6">
                                                <label>Teléfono</label>
                                                <input type="number" class="form-control" id="telefonoAgregar"
                                                    name="telefonoAgregar" maxlength="9"
                                                    oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);">
                                                <p class="text-danger error-msg" id="error-telefonoAgregar"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Teléfono 2 <small>(opcional)</small></label>
                                                <input type="number" class="form-control" id="telefonoAgregar2"
                                                    name="telefonoAgregar2" maxlength="9"
                                                    oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);">
                                            </div>

                                            <!-- Fila 4 -->
                                            <div class="col-md-6">
                                                <label>Email <small>(opcional)</small></label>
                                                <input type="email" class="form-control" id="direccion"
                                                    name="direccion">
                                                <p class="text-danger error-msg" id="error-email"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Rubro</label>
                                                <div class="input-group">
                                                    <select class="form-control" id="rubroCliente" name="rubroCliente">
                                                        <option value="">Seleccione un rubro</option>
                                                    </select>
                                                    <button class="btn bg-rojo text-white" type="button"
                                                        onclick="$('#gestionRubrosModal').modal('show')">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                    <button id="nuevoCliente" type="button" class="btn bg-rojo text-white">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- EDITAR MODAL -->
                    <div class="modal fade" id="editarModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="clientesEditar">
                                        <div class="row p-2">
                                            <!-- Fila 1 -->
                                            <div class="col-md-6">
                                                <label>DNI<span style="color: red;"> (*)</span></label>
                                                <div class="input-group">
                                                    <input type="hidden" name="idCliente" id="idCliente" value="">
                                                    <input type="hidden" name="trid" id="trid" value="">
                                                    <input type="text" class="form-control" id="documentoEditar"
                                                        name="documentoEditar" required maxlength="11">
                                                    <div class="input-group-prepend">
                                                        <button id="btnBuscarInfoEditar" class="btn bg-rojo text-white">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="text-danger error-msg" id="error-documentoEditar"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Nombre/Razon Social <span style="color: red;">(*)</span></label>
                                                <input type="text" class="form-control" id="datosEditar"
                                                    name="datosEditar" required>
                                                <p class="text-danger error-msg" id="error-datosEditar"></p>
                                            </div>

                                            <!-- Fila 2 -->
                                            <div class="col-md-6">
                                                <label>Dirección</label>
                                                <input type="text" class="form-control" id="direccionEditar"
                                                    name="direccionEditar">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Dirección de Llegada <small>(opcional)</small></label>
                                                <input type="text" class="form-control" id="direccionEditar2"
                                                    name="direccionEditar2">
                                            </div>

                                            <!-- Fila 3 -->
                                            <div class="col-md-6">
                                                <label>Teléfono</label>
                                                <input type="number" class="form-control" id="telefonoEditar"
                                                    name="telefonoEditar" maxlength="9"
                                                    oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);">
                                                <p class="text-danger error-msg" id="error-telefonoEditar"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Teléfono 2 <small>(opcional)</small></label>
                                                <input type="number" class="form-control" id="telefonoEditar2"
                                                    name="telefonoEditar2" maxlength="9"
                                                    oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);">
                                            </div>

                                            <!-- Fila 4 -->
                                            <div class="col-md-6">
                                                <label>Email <small>(opcional)</small></label>
                                                <input type="email" class="form-control" id="emailEditar"
                                                    name="emailEditar">
                                                <p class="text-danger error-msg" id="error-emailEditar"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Rubro</label>
                                                <div class="input-group">
                                                    <select class="form-control" id="rubroClienteEditar"
                                                        name="rubroClienteEditar">
                                                        <option value="">Seleccione un rubro</option>
                                                    </select>
                                                    <button class="btn bg-rojo text-white" type="button"
                                                        onclick="$('#gestionRubrosModal').modal('show')">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                    <button id="updateCliente" type="button" class="btn bg-rojo text-white">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Gestionar Rubros -->
                    <div class="modal fade" id="gestionRubrosModal" tabindex="-1"
                        aria-labelledby="gestionRubrosModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title" id="gestionRubrosModalLabel">Gestionar Rubros</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nombreRubro"
                                                placeholder="Nombre del rubro">
                                            <button class="btn bg-rojo text-white" id="btnAgregarRubro">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaRubros"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes"
                                class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Documento</th>
                                        <th>Nombre/Razon Social</th>
                                        <th>Email</th>
                                        <th>Télefono</th>
                                        <th>Rubro</th>
                                        <th>S/ Venta</th>
                                        <th>Ultima Venta</th>
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
</div>


<script>
    $(document).ready(function () {

        const app = new Vue({
            el: "#conte-vue-modals",
            data: {
                listaClientes: []
            },
            methods: {
                agregarListaImport() {

                    if (this.listaClientes.length > 0) {

                        _ajax("/ajs/clientes/add/por/lista", "POST", {
                            lista: JSON.stringify(this.listaClientes)
                        },
                            function (resp) {
                                console.log(resp);
                                /* return */
                                if (resp.res) {
                                    alertExito("Agregado")
                                        .then(function () {
                                            location.reload()
                                        })
                                } else {
                                    alertAdvertencia("No se pudo Agregar")
                                }
                            }
                        )
                    } else {
                        alertAdvertencia("La lista esta vacia")
                    }
                },


            }
        })

        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/clientes/render",
                method: "POST",
                dataSrc: "",
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columns: [{
                data: null,
        class: "text-center",
        render: function (data, type, row, meta) {
            // meta.row contiene el índice de la fila actual
            return meta.row + 1;
        }
    },
            {
                data: "documento",
                class: "text-center",
            },
            {
                data: "datos",
                class: "text-center",
            },
            {
                data: "email",
                class: "text-center",
            },
            {
                data: "telefono",
                class: "text-center",
            },
            {
                data: "rubro_nombre", // Nueva columna para mostrar el nombre del rubro
                class: "text-center",
                render: function (data, type, row) {
                    return data ? data : '<span class="text-muted">Sin rubro</span>';
                }
            },
            {
                data: "ultima_venta",
                class: "text-center",
            },
            {
                data: "total_venta",
                class: "text-center",
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return `<div class="text-center">
            <div class="btn-group btn-sm"><button data-id="${Number(row.id_cliente)}" class="btn btn-sm btn-warning btnEditar"
            ><i class="fa fa-edit"></i> </button>
            <button btn-sm data-id="${Number(row.id_cliente)}" class="btn btn-sm btn-danger btnBorrar"><i class="fa fa-trash"></i> </button>
            <a href="${_URL}/reporte/cliente/${Number(row.id_cliente)}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file"></i></a>
            </div></div>`;
                },
            },
            ],
            // Configurar el orden inicial para que muestre los más recientes primero
            order: [[0, 'asc']]
        });
        function validarFormularioAgregar() {
            // Limpiar mensajes de error previos
            $(".error-msg").text("");

            let isValid = true;

            // Validar documento
            const documento = $("#documentoAgregar").val().trim();
            if (!documento) {
                $("#error-documentoAgregar").text("El documento es obligatorio");
                isValid = false;
            } else if (documento.length !== 8 && documento.length !== 11) {
                $("#error-documentoAgregar").text("El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC)");
                isValid = false;
            }

            // Validar nombre/razón social
            const datos = $("#datosAgregar").val().trim();
            if (!datos) {
                $("#error-datosAgregar").text("El nombre/razón social es obligatorio");
                isValid = false;
            }

            // Validar teléfono (si se proporciona)
            const telefono = $("#telefonoAgregar").val().trim();
            if (telefono && telefono.length !== 9) {
                $("#error-telefonoAgregar").text("El teléfono debe tener 9 dígitos");
                isValid = false;
            }

            // Validar email (si se proporciona)
            const email = $("#direccion").val().trim();
            if (email && !validateEmail(email)) {
                $("#error-email").text("El formato del email no es válido");
                isValid = false;
            }

            return isValid;
        }

        // Función de validación para el formulario de editar
        function validarFormularioEditar() {
            // Limpiar mensajes de error previos
            $(".error-msg").text("");

            let isValid = true;

            // Validar documento
            const documento = $("#documentoEditar").val().trim();
            if (!documento) {
                $("#error-documentoEditar").text("El documento es obligatorio");
                isValid = false;
            } else if (documento.length !== 8 && documento.length !== 11) {
                $("#error-documentoEditar").text("El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC)");
                isValid = false;
            }

            // Validar nombre/razón social
            const datos = $("#datosEditar").val().trim();
            if (!datos) {
                $("#error-datosEditar").text("El nombre/razón social es obligatorio");
                isValid = false;
            }

            // Validar teléfono (si se proporciona)
            const telefono = $("#telefonoEditar").val().trim();
            if (telefono && telefono.length !== 9) {
                $("#error-telefonoEditar").text("El teléfono debe tener 9 dígitos");
                isValid = false;
            }

            // Validar email (si se proporciona)
            const email = $("#emailEditar").val().trim();
            if (email && !validateEmail(email)) {
                $("#error-emailEditar").text("El formato del email no es válido");
                isValid = false;
            }

            return isValid;
        }

        // Función auxiliar para validar email
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        $("#nuevoCliente").click(function () {
            // Validar el formulario antes de enviar
            if (!validarFormularioAgregar()) {
                return; // Detener si hay errores de validación
            }

            $("#loader-menor").show();
            let data = $("#frmClientesAgregar").serializeArray();
            $.ajax({
                type: "POST",
                url: _URL + "/ajs/clientes/add",
                data: data,
                success: function (resp) {
                    $("#loader-menor").hide();
                    try {
                        let response = JSON.parse(resp);

                        if (response.status === 'success') {
                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire({
                                icon: "success",
                                title: "¡Buen trabajo!",
                                text: response.message || "Registro Exitoso"
                            });
                            $("#agregarModal").modal("hide");
                            $("body").removeClass("modal-open");
                            $("#frmClientesAgregar").trigger("reset");
                        } else {
                            // Mostrar errores en los campos correspondientes
                            if (response.errors) {
                                Object.keys(response.errors).forEach(key => {
                                    $(`#error-${key}`).text(response.errors[key]);
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: response.message || "Ocurrió un error desconocido"
                                });
                            }
                        }
                    } catch (e) {
                        console.error("Error al procesar la respuesta:", e, resp);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al procesar la respuesta del servidor: " + e.message
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $("#loader-menor").hide();
                    console.error("Error en la petición AJAX:", { xhr, status, error });
                    Swal.fire({
                        icon: "error",
                        title: "Error de conexión",
                        text: "No se pudo conectar con el servidor: " + error
                    });
                }
            });
        });

        // Modificar la función que carga los datos del cliente al editar
        $("#tabla_clientes").on("click", ".btnEditar", function (event) {
            $("#loader-menor").show();
            var table = $("#tabla_clientes").DataTable();
            var trid = $(this).closest("tr").attr("id");
            var id = $(this).data("id");
            $("#editarModal").modal("show");
            $("#editarModal")
                .find(".modal-title")
                .text("Editar cliente N°" + id);

            // Limpiar mensajes de error previos
            $(".error-msg").text("");

            // Variable para almacenar el ID del rubro
            let rubroId = null;

            $.ajax({
                url: _URL + "/ajs/clientes/getOne",
                data: {
                    id: id,
                },
                type: "post",
                success: function (data) {
                    $("#loader-menor").hide();
                    try {
                        let json = JSON.parse(data);
                        let datos = json[0];
                        console.log("Datos del cliente:", datos);

                        // Guardar el ID del rubro para usarlo después
                        rubroId = datos.id_rubro;

                        $("#documentoEditar").val(datos.documento);
                        $("#datosEditar").val(datos.datos);
                        $("#direccionEditar").val(datos.direccion);
                        $("#direccionEditar2").val(datos.direccion2);
                        $("#telefonoEditar").val(datos.telefono);
                        $("#telefonoEditar2").val(datos.telefono2);
                        $("#emailEditar").val(datos.email);
                        $("#idCliente").val(id);
                        $("#trid").val(trid);

                        // Cargar los rubros y luego establecer el valor
                        cargarRubrosEditar(rubroId);
                    } catch (e) {
                        console.error("Error al procesar los datos:", e);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al procesar los datos del cliente"
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $("#loader-menor").hide();
                    console.error("Error en la petición AJAX:", { xhr, status, error });
                    Swal.fire({
                        icon: "error",
                        title: "Error de conexión",
                        text: "No se pudo conectar con el servidor: " + error
                    });
                }
            });
        });
        $("#updateCliente").click(function () {
            // Validar el formulario antes de enviar
            if (!validarFormularioEditar()) {
                return; // Detener si hay errores de validación
            }

            $("#loader-menor").show();
            let data = $("#clientesEditar").serializeArray();
            let id = $("#idCliente").val();

            $.ajax({
                url: _URL + "/ajs/clientes/editar",
                type: "POST",
                data: data,
                success: function (resp) {
                    $("#loader-menor").hide();
                    try {
                        let response = JSON.parse(resp);

                        if (response.status === 'success') {
                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire("¡Buen trabajo!", "Actualización exitosa", "success");
                            $("#editarModal").modal("hide");
                            $("body").removeClass("modal-open");
                        } else {
                            // Mostrar errores en los campos correspondientes
                            if (response.errors) {
                                Object.keys(response.errors).forEach(key => {
                                    $(`#error-${key}Editar`).text(response.errors[key]);
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: response.message || "Ocurrió un error desconocido"
                                });
                            }
                        }
                    } catch (e) {
                        console.error("Error al procesar la respuesta:", e, resp);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al procesar la respuesta del servidor: " + e.message
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $("#loader-menor").hide();
                    console.error("Error en la petición AJAX:", { xhr, status, error });
                    Swal.fire({
                        icon: "error",
                        title: "Error de conexión",
                        text: "No se pudo conectar con el servidor: " + error
                    });
                }
            });
        });
        $("#tabla_clientes").on("click", ".btnBorrar", function () {
            var id = $(this).data("id");
            let idData = {
                name: "idDelete",
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
                        url: _URL + "/ajs/clientes/borrar",
                        type: "post",
                        data: idData,
                        success: function (resp) {
                            /* console.log(resp); */
                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire(
                                "¡Buen trabajo!",
                                "Registro Borrado Exitosamente",
                                "success"
                            );
                        },
                    });
                } else { }
            });
        });
        $("#btnBuscarInfo").click(function (e) {
            e.preventDefault();
            if (!$("#documentoAgregar").val()) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Debe ingresar un DNI o RUC",
                });
            } else {
                if (
                    $("#documentoAgregar").val().length === 8 ||
                    $("#documentoAgregar").val().length === 11
                ) {
                    let docu = $("#documentoAgregar").val();
                    $("#loader-menor").show();
                    $.ajax({
                        url: _URL + "/ajs/consulta/doc/cliente",
                        type: "post",
                        data: {
                            doc: docu
                        },
                        success: function (resp) {
                            $("#loader-menor").hide();
                            let datos = JSON.parse(resp);
                            console.log(datos.data);
                            /*  console.log(resp); */
                            if (datos.data.nombre) {
                                $("#datosAgregar").val(datos.data.nombre);
                            } else if (datos.data.razon_social) {
                                $("#datosAgregar").val(datos.data.razon_social);
                            } else {
                                alertAdvertencia("Documento no encontrado");
                            }
                            console.log(datos.data.direccion)
                            $("#direccionAgregar").val(datos.data.direccion || '');
                            /* $("#datosAgregar").val(datos.data.dni);   */
                            //PRUEBA RUC 10427993120
                        },
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Debe ingresar un DNI o RUC",
                    });
                }
            }
        });
        $("#btnBuscarInfoEditar").click(function (e) {
            e.preventDefault();
            if (!$("#documentoEditar").val()) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Debe ingresar un DNI o RUC",
                });
            } else {
                if (
                    $("#documentoEditar").val().length === 8 ||
                    $("#documentoEditar").val().length === 11
                ) {
                    let docu = $("#documentoEditar").val();
                    $("#loader-menor").show();
                    $.ajax({
                        url: _URL + "/ajs/consulta/doc/cliente",
                        type: "post",
                        data: {
                            doc: docu
                        },
                        success: function (resp) {
                            $("#loader-menor").hide();
                            let datos = JSON.parse(resp);
                            console.log(datos.data);
                            console.log(resp);
                            if (datos.data.nombre) {
                                $("#datosEditar").val(datos.data.nombre);
                            } else if (datos.data.razon_social) {
                                $("#datosEditar").val(datos.data.razon_social);
                            } else {
                                alertAdvertencia("Documento no encontrado");
                            }
                            $("#direccionEditar").val(datos.data.direccion || '');
                            /* $("#datosAgregar").val(datos.data.dni);   */
                            //PRUEBA RUC 10427993120
                        },
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Debe ingresar un DNI o RUC",
                    });
                }
            }
        });



        $("#nuevoExcel").change(function () {
            console.log("aaaaaaaa")
            if ($("#nuevoExcel").val().length > 0) {
                var fd = new FormData();
                fd.append('file', $("#nuevoExcel")[0].files[0]);
                $.ajax({
                    type: 'POST',
                    url: _URL + "/ajs/clientes/add/exel",
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        console.log('inicio');
                        $("#loader-menor").show();
                    },
                    error: function (err) {
                        $("#loader-menor").hide();
                        console.log(err);
                    },
                    success: function (resp) {
                        $("#loader-menor").hide();
                        console.log(resp);
                        /* return */
                        resp = JSON.parse(resp)
                        if (resp.res) {
                            var bloc = true;
                            var listaTemp = [];
                            resp.data.forEach(function (el) {
                                if (!bloc) {
                                    listaTemp.push({
                                        documento: el[0],
                                        datos: el[1],
                                        direccion: el[2],
                                        direccion2: el[3],
                                        telefono: el[4],
                                        telefono2: el[5],
                                        email: el[6],
                                        /* codSunat: el[8],
                                        almacen: el[9],
                                        afecto: false,
                                        precio_unidad: el[3],
                                        codigoProd: el[10] */
                                    })
                                }
                                bloc = false
                            })
                            app._data.listaClientes = listaTemp
                            $("#importarModal").modal("hide")
                            $("#modal-lista-clientes").modal("show")
                        } else {
                            alertAdvertencia("No se pudo subir el Archivo")
                        }
                        $("#nuevoExcel").val("")

                    }
                })
            }
        })
        // Nueva función para cargar rubros específicamente para editar
function cargarRubrosEditar(rubroId) {
    $.ajax({
        url: _URL + "/ajs/rubros/render",
        type: "POST",
        success: function (resp) {
            try {
                let rubros = JSON.parse(resp);
                let options = '<option value="">Seleccione un rubro</option>';

                rubros.forEach(function (rubro) {
                    // Marcar como seleccionado si coincide con el ID del rubro del cliente
                    const selected = (rubro.id_rubro == rubroId) ? 'selected' : '';
                    options += `<option value="${rubro.id_rubro}" ${selected}>${rubro.nombre}</option>`;
                });

                // Actualizar solo el select de edición
                $("#rubroClienteEditar").html(options);
                
                // Forzar la selección del rubro correcto
                if (rubroId) {
                    $("#rubroClienteEditar").val(rubroId);
                }
                
                console.log("Rubro seleccionado:", rubroId);
            } catch (e) {
                console.error("Error al procesar los rubros:", e);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar rubros:", error);
        }
    });
}
        // Funciones para gestionar rubros
        function cargarRubros() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: _URL + "/ajs/rubros/render",
            type: "POST",
            success: function (resp) {
                try {
                    let rubros = JSON.parse(resp);
                    let html = '';
                    let options = '<option value="">Seleccione un rubro</option>';

                    rubros.forEach(function (rubro) {
                        html += `<tr>
                            <td>${rubro.nombre}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btnEditarRubro" data-id="${rubro.id_rubro}" data-nombre="${rubro.nombre}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnEliminarRubro" data-id="${rubro.id_rubro}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                        options += `<option value="${rubro.id_rubro}">${rubro.nombre}</option>`;
                    });

                    $("#tablaRubros").html(html);
                    $("#rubroCliente").html(options);
                    resolve();
                } catch (e) {
                    console.error("Error al procesar los rubros:", e);
                    reject(e);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar rubros:", error);
                reject(error);
            }
        });
    });
}
        $("#btnAgregarRubro").click(function () {
            let nombre = $("#nombreRubro").val();
            if (!nombre) return;

            $.ajax({
                url: _URL + "/ajs/rubros/add",
                type: "POST",
                data: { nombre: nombre },
                success: function (resp) {
                    let data = JSON.parse(resp);
                    if (data.status === 'success') {
                        $("#nombreRubro").val('');
                        cargarRubros();
                        Swal.fire("¡Éxito!", "Rubro agregado correctamente", "success");
                    }
                }
            });
        });

        // Modificar el evento para editar rubro
        // Modificar el evento para editar rubro
        $(document).on('click', '.btnEditarRubro', function () {
            let id = $(this).data('id');
            let nombre = $(this).data('nombre');

            // Crear el modal de edición con un input normal
            let modalContent = `
        <div class="modal" id="editarRubroModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Rubro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editRubroNombre">Nombre del Rubro</label>
                            <input type="text" class="form-control" id="editRubroNombre" value="${nombre}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="guardarRubroEdit">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Remover modal anterior si existe
            $('#editarRubroModal').remove();

            // Agregar el nuevo modal al body
            $('body').append(modalContent);

            // Mostrar el modal
            let modal = new bootstrap.Modal(document.getElementById('editarRubroModal'));
            modal.show();

            // Manejar el guardado
            $('#guardarRubroEdit').click(function () {
                let nuevoNombre = $('#editRubroNombre').val();

                if (nuevoNombre.trim() === '') {
                    Swal.fire('Error', 'El nombre del rubro no puede estar vacío', 'error');
                    return;
                }

                $.ajax({
                    url: _URL + "/ajs/rubros/update",
                    type: "POST",
                    data: {
                        id_rubro: id,
                        nombre: nuevoNombre
                    },
                    success: function (resp) {
                        let data = JSON.parse(resp);
                        if (data.status === 'success') {
                            modal.hide();
                            cargarRubros();
                            Swal.fire("¡Éxito!", "Rubro actualizado correctamente", "success");
                        } else {
                            Swal.fire("Error", data.message || "Error al actualizar el rubro", "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Error al conectar con el servidor", "error");
                    }
                });
            });
        });


        // Modificar el evento para eliminar rubro
        $(document).on('click', '.btnEliminarRubro', function () {
            let id = $(this).data('id');

            Swal.fire({
                title: "¿Está seguro?",
                text: "Esta acción no se puede revertir",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/rubros/delete",
                        type: "POST",
                        data: { id_rubro: id },
                        success: function (resp) {
                            let data = JSON.parse(resp);
                            if (data.status === 'success') {
                                cargarRubros();
                                Swal.fire("¡Éxito!", "Rubro eliminado correctamente", "success");
                            } else {
                                Swal.fire("Error", data.message, "error");
                            }
                        }
                    });
                }
            });
        });



        // Cargar rubros al abrir los modales
        $("#agregarModal, #editarModal").on("shown.bs.modal", function () {
            cargarRubros();
        });

    });
</script>