<?php

require_once "app/models/Cliente.php";

$c_cliente = new Cliente();
$c_cliente->setIdEmpresa($_SESSION['id_empresa']);

?>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">Categorias</h6>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalCategoria" class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Agregar</button>
                        <!--   <button type="button" data-bs-toggle="modal" data-bs-target="#editarModal" class="btn btn-warning">Editar</button> -->
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="/jvc/almacen/productos" class="btn border-rojo text-rojo bg-white"><i class="fa fa-arrow-left"></i> Regresar</a>
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
                                        <th>categiria</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar Categoria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addCategoria">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nombreCategoria" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreCategoria">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary border-rojo bg-white text-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitCategoria" class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="updateCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Categoria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addCategoria">
                            <div class="modal-body">
                                <input type="text" id="idCatU" value="" hidden>
                                <div class="mb-3">
                                    <label for="nombreCategoriaU" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreCategoriaU">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary border-rojo text-rojo bg-white" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateCategoriaBtn" class="btn bg-rojo text-white">Guardar</button>
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
                url: _URL + "/ajs/get/categorias",
                method: "GET", //usamos el metodo POST
                dataSrc: "",
            },
            language: {
                url: "ServerSide/Spanish.json",
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
        $('#submitCategoria').click(function() {
            $.post(_URL + "/ajs/save/categorias", {
                nombre: $('#nombreCategoria').val()
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Exito",
                    text: "Se guardo correctamente",
                    icon: "success"
                });
                $('#modalCategoria').modal('hide');
                tabla_clientes.ajax.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorÃ­as: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorÃ­as. Por favor, intenta nuevamente.");
            });
        })

        $("#tabla_clientes").on("click", ".btnBorrar ", function(event) {
            let id = $(this).data('id');
            Swal.fire({
                title: "Esta seguro",
                text: "El cambio es irreversible",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Aceptar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post( _URL + "/ajs/delete/categorias", {
                        id: id
                    }, function(data, textStatus, jqXHR) {
                        Swal.fire({
                            title: "eliminado!",
                            text: "Eliminado correctamente",
                            icon: "success"
                        });
                        tabla_clientes.ajax.reload();
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error al cargar las categorÃ­as: " + textStatus, errorThrown);
                        alert("No se pudo cargar las categorÃ­as. Por favor, intenta nuevamente.");
                    });

                }
            });
        })

        $("#tabla_clientes").on("click", ".btnEditar ", function(event) {

            let id = $(this).data('id');
            $.post(_URL + "/ajs/getOne/categorias", {
                id: id
            }, function(data, textStatus, jqXHR) {
                let resp = JSON.parse(data);
                $('#nombreCategoriaU').val(resp[0].nombre);
                $('#idCatU').val(resp[0].id)
                $('#updateCategoria').modal('show');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorÃ­as: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorÃ­as. Por favor, intenta nuevamente.");
            });
        })

        $('#updateCategoriaBtn').click(function() {
            $.post(_url + "/ajs/update/categorias", {
                nombre: $('#nombreCategoriaU').val(),
                id: $('#idCatU').val()
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Exito",
                    text: "Se guardo correctamente",
                    icon: "success"
                });
                $('#updateCategoria').modal('hide');
                tabla_clientes.ajax.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorÃ­as: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorÃ­as. Por favor, intenta nuevamente.");
            });
        })
    });
</script>