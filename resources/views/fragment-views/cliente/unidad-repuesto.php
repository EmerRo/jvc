<?php

// require_once "app/models/Cliente.php";

// $c_cliente = new Cliente();
// $c_cliente->setIdEmpresa($_SESSION['id_empresa']);

?>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">UNIDADES PARA REPUESTOS</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalUnidad" class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Añadir</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="/orden/repuestos" class="btn border-rojo bg-white"><i class="fa fa-arrow-left"></i> Regresar</a>
                    </div>
                </div>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Unidades</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar Unidad -->
            <div class="modal fade" id="modalUnidad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar Unidad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addUnidadForm"> <!-- Cambiado a "addUnidadForm" -->
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nombreUnidad" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreUnidad">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitUnidad" class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Actualizar Unidad -->
            <div class="modal fade" id="updateUnidad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Unidad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="updateUnidadForm"> <!-- Cambiado a "updateUnidadForm" -->
                            <div class="modal-body">
                                <input type="text" id="idUniU" value="" hidden>
                                <div class="mb-3">
                                    <label for="nombreUnidadU" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreUnidadU">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateUnidadBtn" class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/get/unidades/rep",
                method: "GET", 
                dataSrc: "",
            },
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            columns: [{
                    data: "id",
                    class: "text-center",
                },
                {
                    data: "nombre",
                    class: "text-center",
                },
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        return `<div class="text-center">
                            <div class="btn-group btn-sm"><button  data-id="${Number(row.id)}" class="btn btn-sm btn-warning btnEditar"
                            ><i class="fa fa-edit"></i> </button>
                            <button btn-sm  data-id="${Number(row.id)}" class="btn btn-sm  btn-danger btnBorrar"><i class="fa fa-trash"></i> </button>
                        `;
                    },
                },
            ],
        });

        // Agregar Unidad
        $('#submitUnidad').click(function() {
            $.post( _URL + "/ajs/save/unidades/rep", {
                nombre: $('#nombreUnidad').val() // Corregido "nombreUnidad"
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Exito",
                    text: "Se guardó correctamente",
                    icon: "success"
                });
                $('#modalUnidad').modal('hide');
                tabla_clientes.ajax.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las Unidades: " + textStatus, errorThrown);
                alert("No se pudo cargar las Unidades. Por favor, intenta nuevamente.");
            });
        });

        // Eliminar Unidad
        $("#tabla_clientes").on("click", ".btnBorrar", function(event) {
            let id = $(this).data('id');
            Swal.fire({
                title: "Está seguro",
                text: "El cambio es irreversible",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Aceptar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_URL + "/ajs/delete/Unidades/rep", {
                        id: id
                    }, function(data, textStatus, jqXHR) {
                        Swal.fire({
                            title: "Eliminado!",
                            text: "Eliminado correctamente",
                            icon: "success"
                        });
                        tabla_clientes.ajax.reload();
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error al cargar las Unidades: " + textStatus, errorThrown);
                        alert("No se pudo cargar las Unidades. Por favor, intenta nuevamente.");
                    });
                }
            });
        });

        // Editar Unidad
        $("#tabla_clientes").on("click", ".btnEditar", function(event) {
            let id = $(this).data('id');
            $.post(__URL + "/ajs/getOne/unidades/rep", {
                id: id
            }, function(data, textStatus, jqXHR) {
                let resp = JSON.parse(data);
                $('#nombreUnidadU').val(resp[0].nombre);
                $('#idUniU').val(resp[0].id);
                $('#updateUnidad').modal('show');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las Unidades: " + textStatus, errorThrown);
                alert("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
            });
        });

        // Actualizar Unidad
        $('#updateUnidadBtn').click(function() {
            $.post(__URL + "/ajs/update/unidades/rep", {
                nombre: $('#nombreUnidadU').val(), // Corregido "nombreUnidadU"
                id: $('#idUniU').val()
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Éxito",
                    text: "Se guardó correctamente",
                    icon: "success"
                });
                $('#updateUnidad').modal('hide');
                tabla_clientes.ajax.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las unidades: " + textStatus, errorThrown);
                alert("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
            });
        });
    });
</script>
