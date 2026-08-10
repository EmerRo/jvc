<style>
    #tabla_proveedores {
        table-layout: fixed;
    }
    /* Anchos fijos para columnas uniformes */
    #tabla_proveedores th:nth-child(1),
    #tabla_proveedores td:nth-child(1) { width: 105px; } /* RUC */
    #tabla_proveedores th:nth-child(2),
    #tabla_proveedores td:nth-child(2) { width: 28%; } /* Razón Social */
    #tabla_proveedores th:nth-child(3),
    #tabla_proveedores td:nth-child(3) { width: 26%; } /* Dirección */
    #tabla_proveedores th:nth-child(4),
    #tabla_proveedores td:nth-child(4) { width: 125px; } /* Teléfono */
    #tabla_proveedores th:nth-child(5),
    #tabla_proveedores td:nth-child(5) { width: 24%; } /* Email */
    #tabla_proveedores th:nth-child(6),
    #tabla_proveedores td:nth-child(6) { width: 115px; } /* Acciones */
    /* Razón Social y Email: recortar con puntos suspensivos */
    #tabla_proveedores tbody td:nth-child(2),
    #tabla_proveedores tbody td:nth-child(5) {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    /* Email: enlace clicable que se recorta dentro de la celda */
    #tabla_proveedores td:nth-child(5) a {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    /* Dirección: puede envolver en varias líneas */
    #tabla_proveedores tbody td:nth-child(3) {
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">DATOS DE PROVEEDORES</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                <button type="button" data-bs-toggle="modal" data-bs-target="#agregarProveedorModal" class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Agregar</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="tabla_proveedores">
                    <thead>
                        <tr>
                            <th>RUC</th>
                            <th>Razón Social</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="agregarProveedorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title">Agregar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="frmProveedorAgregar">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>RUC <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="rucAgregar" id="rucAgregar" class="form-control" maxlength="11" placeholder="Ingrese RUC" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="input-group-prepend">
                                    <button id="btnBuscarInfoProveedor" class="btn bg-rojo text-white" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger" id="error-rucAgregar"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Razón Social <span class="text-danger">*</span></label>
                            <input type="text" name="razonSocialAgregar" id="razonSocialAgregar" class="form-control" placeholder="Ingrese razón social">
                            <small class="text-danger" id="error-razonSocialAgregar"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Dirección</label>
                            <input type="text" name="direccionAgregar" id="direccionAgregar" class="form-control" placeholder="Ingrese dirección">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefonoAgregar" id="telefonoAgregar" class="form-control" maxlength="9" placeholder="Teléfono">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Email</label>
                            <input type="email" name="emailAgregar" id="emailAgregar" class="form-control" placeholder="Email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Departamento</label>
                            <input type="text" name="departamentoAgregar" id="departamentoAgregar" class="form-control" placeholder="Departamento">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Provincia</label>
                            <input type="text" name="provinciaAgregar" id="provinciaAgregar" class="form-control" placeholder="Provincia">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Distrito</label>
                            <input type="text" name="distritoAgregar" id="distritoAgregar" class="form-control" placeholder="Distrito">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="nuevoProveedor" class="btn bg-rojo text-white">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editarProveedorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title">Editar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="frmProveedorEditar">
                    <input type="hidden" name="idProveedor" id="idProveedor">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>RUC <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="rucEditar" id="rucEditar" class="form-control" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="input-group-prepend">
                                    <button id="btnBuscarInfoProveedorEditar" class="btn bg-rojo text-white" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger" id="error-rucEditar"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Razón Social <span class="text-danger">*</span></label>
                            <input type="text" name="razonSocialEditar" id="razonSocialEditar" class="form-control">
                            <small class="text-danger" id="error-razonSocialEditar"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Dirección</label>
                            <input type="text" name="direccionEditar" id="direccionEditar" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefonoEditar" id="telefonoEditar" class="form-control" maxlength="9">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Email</label>
                            <input type="email" name="emailEditar" id="emailEditar" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Departamento</label>
                            <input type="text" name="departamentoEditar" id="departamentoEditar" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Provincia</label>
                            <input type="text" name="provinciaEditar" id="provinciaEditar" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Distrito</label>
                            <input type="text" name="distritoEditar" id="distritoEditar" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="editarProveedor" class="btn bg-rojo text-white">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<script>
    var tabla_proveedores = $('#tabla_proveedores').DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        ajax: {
            url: _URL + "/ajs/proveedores/render",
            type: "POST",
            dataSrc: ''
        },
        columns: [
            { data: 'ruc' },
            {
                data: 'razon_social',
                render: function(data, type) {
                    return (type === 'display' && data) ? '<span title="' + data + '">' + data + '</span>' : data;
                }
            },
            { data: 'direccion', defaultContent: '-' },
            { data: 'telefono', defaultContent: '-' },
            {
                data: 'email',
                defaultContent: '-',
                render: function(data, type) {
                    return (type === 'display' && data) ? '<a href="mailto:' + data + '" title="' + data + '">' + data + '</a>' : data;
                }
            },
            {
                data: null,
                render: function(data) {
                    return '<button class="btn btn-sm bg-rojo text-white editar-proveedor" data-id="' + data.proveedor_id + '"><i class="fa fa-edit"></i></button> ' +
                        '<button class="btn btn-sm btn-danger eliminar-proveedor" data-id="' + data.proveedor_id + '"><i class="fa fa-trash"></i></button>';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        order: [[0, 'desc']]
    });

    // ─── Búsqueda RUC en Agregar ─────────────────────────────────────────
    $("#btnBuscarInfoProveedor").click(function(e) {
        e.preventDefault();
        var ruc = $("#rucAgregar").val().trim();
        if (!ruc) {
            Swal.fire({ icon: "error", title: "Error", text: "Debe ingresar un RUC" });
            return;
        }
        if (ruc.length !== 11) {
            Swal.fire({ icon: "error", title: "Error", text: "El RUC debe tener 11 dígitos" });
            return;
        }

        $("#loader-menor").show();
        $.ajax({
            url: _URL + "/ajs/consulta/doc/cliente",
            type: "POST",
            data: { doc: ruc },
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    var datos = JSON.parse(resp);
                    if (datos.res) {
                        if (datos.data.razon_social) {
                            $("#razonSocialAgregar").val(datos.data.razon_social);
                        } else if (datos.data.nombre) {
                            $("#razonSocialAgregar").val(datos.data.nombre);
                        } else {
                            alertAdvertencia("Documento no encontrado");
                            return;
                        }
                        $("#direccionAgregar").val(datos.data.direccion || '');
                        $("#departamentoAgregar").val(datos.data.departamento || '');
                        $("#provinciaAgregar").val(datos.data.provincia || '');
                        $("#distritoAgregar").val(datos.data.distrito || '');
                    } else {
                        alertAdvertencia("Documento no encontrado");
                    }
                } catch (e) {
                    console.error("Error parsing response:", e, resp);
                    alertAdvertencia("Error al procesar la respuesta");
                }
            },
            error: function() {
                $("#loader-menor").hide();
                alertAdvertencia("Error de conexión con el servicio de consulta RUC");
            }
        });
    });

    // ─── Búsqueda RUC en Editar ──────────────────────────────────────────
    $("#btnBuscarInfoProveedorEditar").click(function(e) {
        e.preventDefault();
        var ruc = $("#rucEditar").val().trim();
        if (!ruc) {
            Swal.fire({ icon: "error", title: "Error", text: "Debe ingresar un RUC" });
            return;
        }
        if (ruc.length !== 11) {
            Swal.fire({ icon: "error", title: "Error", text: "El RUC debe tener 11 dígitos" });
            return;
        }

        $("#loader-menor").show();
        $.ajax({
            url: _URL + "/ajs/consulta/doc/cliente",
            type: "POST",
            data: { doc: ruc },
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    var datos = JSON.parse(resp);
                    if (datos.res) {
                        if (datos.data.razon_social) {
                            $("#razonSocialEditar").val(datos.data.razon_social);
                        } else if (datos.data.nombre) {
                            $("#razonSocialEditar").val(datos.data.nombre);
                        } else {
                            alertAdvertencia("Documento no encontrado");
                            return;
                        }
                        $("#direccionEditar").val(datos.data.direccion || '');
                        $("#departamentoEditar").val(datos.data.departamento || '');
                        $("#provinciaEditar").val(datos.data.provincia || '');
                        $("#distritoEditar").val(datos.data.distrito || '');
                    } else {
                        alertAdvertencia("Documento no encontrado");
                    }
                } catch (e) {
                    console.error("Error parsing response:", e, resp);
                    alertAdvertencia("Error al procesar la respuesta");
                }
            },
            error: function() {
                $("#loader-menor").hide();
                alertAdvertencia("Error de conexión con el servicio de consulta RUC");
            }
        });
    });

    function validarFormularioAgregar() {
        $('.text-danger').text('');
        let isValid = true;

        const ruc = $('#rucAgregar').val().trim();
        if (!ruc) {
            $('#error-rucAgregar').text('El RUC es obligatorio');
            isValid = false;
        } else if (ruc.length !== 11) {
            $('#error-rucAgregar').text('El RUC debe tener 11 dígitos');
            isValid = false;
        }

        const razon = $('#razonSocialAgregar').val().trim();
        if (!razon) {
            $('#error-razonSocialAgregar').text('La razón social es obligatoria');
            isValid = false;
        }

        return isValid;
    }

    function validarFormularioEditar() {
        $('.text-danger').text('');
        let isValid = true;

        const ruc = $('#rucEditar').val().trim();
        if (!ruc) {
            $('#error-rucEditar').text('El RUC es obligatorio');
            isValid = false;
        } else if (ruc.length !== 11) {
            $('#error-rucEditar').text('El RUC debe tener 11 dígitos');
            isValid = false;
        }

        const razon = $('#razonSocialEditar').val().trim();
        if (!razon) {
            $('#error-razonSocialEditar').text('La razón social es obligatoria');
            isValid = false;
        }

        return isValid;
    }

    $('#nuevoProveedor').click(function() {
        if (!validarFormularioAgregar()) return;

        $("#loader-menor").show();
        let data = $("#frmProveedorAgregar").serializeArray();
        $.ajax({
            type: "POST",
            url: _URL + "/ajs/proveedores/add",
            data: data,
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    let response = JSON.parse(resp);
                    if (response.status === 'success') {
                        tabla_proveedores.ajax.reload(null, false);
                        Swal.fire({ icon: "success", title: "¡Buen trabajo!", text: response.message });
                        $("#agregarProveedorModal").modal("hide");
                        $("body").removeClass("modal-open");
                        $("#frmProveedorAgregar").trigger("reset");
                    } else {
                        if (response.errors) {
                            Object.keys(response.errors).forEach(key => {
                                $(`#error-${key}`).text(response.errors[key]);
                            });
                        } else {
                            Swal.fire({ icon: "error", title: "Error", text: response.message });
                        }
                    }
                } catch (e) {
                    console.error("Error:", e, resp);
                    Swal.fire({ icon: "error", title: "Error", text: "Ocurrió un error al procesar la respuesta" });
                }
            }
        });
    });

    $('#tabla_proveedores').on('click', '.editar-proveedor', function() {
        const id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: _URL + "/ajs/proveedores/getOne",
            data: { id: id },
            success: function(resp) {
                try {
                    let data = JSON.parse(resp);
                    if (data) {
                        $('#idProveedor').val(data.proveedor_id);
                        $('#rucEditar').val(data.ruc);
                        $('#razonSocialEditar').val(data.razon_social);
                        $('#direccionEditar').val(data.direccion);
                        $('#telefonoEditar').val(data.telefono);
                        $('#emailEditar').val(data.email);
                        $('#departamentoEditar').val(data.departamento);
                        $('#provinciaEditar').val(data.provincia);
                        $('#distritoEditar').val(data.distrito);
                        $('#editarProveedorModal').modal('show');
                    }
                } catch (e) {
                    console.error("Error:", e, resp);
                }
            }
        });
    });

    $('#editarProveedor').click(function() {
        if (!validarFormularioEditar()) return;

        $("#loader-menor").show();
        let data = $("#frmProveedorEditar").serializeArray();
        $.ajax({
            type: "POST",
            url: _URL + "/ajs/proveedores/editar",
            data: data,
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    let response = JSON.parse(resp);
                    if (response.status === 'success') {
                        tabla_proveedores.ajax.reload(null, false);
                        Swal.fire({ icon: "success", title: "¡Buen trabajo!", text: response.message });
                        $("#editarProveedorModal").modal("hide");
                        $("body").removeClass("modal-open");
                    } else {
                        if (response.errors) {
                            Object.keys(response.errors).forEach(key => {
                                $(`#error-${key}`).text(response.errors[key]);
                            });
                        } else {
                            Swal.fire({ icon: "error", title: "Error", text: response.message });
                        }
                    }
                } catch (e) {
                    console.error("Error:", e, resp);
                    Swal.fire({ icon: "error", title: "Error", text: "Ocurrió un error al procesar la respuesta" });
                }
            }
        });
    });

    $('#tabla_proveedores').on('click', '.eliminar-proveedor', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'El proveedor será desactivado',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C1272D',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: _URL + "/ajs/proveedores/borrar",
                    data: { value: id },
                    success: function(resp) {
                        try {
                            let r = JSON.parse(resp);
                            if (r === 'nice') {
                                tabla_proveedores.ajax.reload(null, false);
                                Swal.fire({ icon: "success", title: "Eliminado", text: "Proveedor desactivado correctamente" });
                            } else {
                                Swal.fire({ icon: "error", title: "Error", text: "No se pudo eliminar el proveedor" });
                            }
                        } catch (e) {
                            console.error("Error:", e, resp);
                        }
                    }
                });
            }
        });
    });

    $('#agregarProveedorModal').on('hidden.bs.modal', function() {
        $('.text-danger').text('');
        $('#frmProveedorAgregar').trigger('reset');
    });

    $('#editarProveedorModal').on('hidden.bs.modal', function() {
        $('.text-danger').text('');
    });

    // Limpiar errores al escribir
    $('#rucAgregar, #razonSocialAgregar').on('input', function() {
        $('#' + $(this).attr('id').replace('Agregar', 'error-') + $(this).attr('id').match(/Agregar/)).text('');
    });
    $('#rucEditar, #razonSocialEditar').on('input', function() {
        $('#' + $(this).attr('id').replace('Editar', 'error-') + $(this).attr('id').match(/Editar/)).text('');
    });
</script>
