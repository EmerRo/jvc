<?php

require_once "app/models/Cliente.php";

$c_cliente = new Cliente();
$c_cliente->setIdEmpresa($_SESSION['id_empresa']);

?>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">CATEGORIAS PARA REPUESTOS</h6>
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
                                        <th>Categoría</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal para agregar categoría -->
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
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitCategoria" class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal para actualizar categoría -->
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
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateCategoriaBtn" class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal para gestionar subcategorías -->
            <div class="modal fade" id="modalSubcategorias" tabindex="-1" aria-labelledby="subcategoriasModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="subcategoriasModalLabel">Subcategorías de <span id="categoriaTitle"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="categoriaIdActual">
                            <div class="mb-3  text-end">
                                <button type="button" id="btnAddSubcategoria" class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Agregar Subcategoría</button>
                            </div>
                            <div class="table-responsive">
                                <table id="tabla_subcategorias" class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="subcategoriasBody">
                                        <!-- Aquí se cargarán las subcategorías -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para agregar subcategoría -->
            <div class="modal fade" id="modalAddSubcategoria" tabindex="-1" aria-labelledby="addSubcategoriaLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="addSubcategoriaLabel">Agregar Subcategoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formAddSubcategoria">
                            <div class="modal-body">
                                <input type="hidden" id="subcategoriaCategoria">
                                <div class="mb-3">
                                    <label for="nombreSubcategoria" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreSubcategoria">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitSubcategoria" class="btn bg-rojo text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal para editar subcategoría -->
            <div class="modal fade" id="modalEditSubcategoria" tabindex="-1" aria-labelledby="editSubcategoriaLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSubcategoriaLabel">Editar Subcategoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formEditSubcategoria">
                            <div class="modal-body">
                                <input type="hidden" id="subcategoriaId">
                                <input type="hidden" id="subcategoriaCategoriaId">
                                <div class="mb-3">
                                    <label for="nombreSubcategoriaEdit" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreSubcategoriaEdit">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateSubcategoriaBtn" class="btn btn-primary">Actualizar</button>
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
        // Tabla de categorías
        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/get/categorias/rep",
                method: "GET", //usamos el metodo POST
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
                            <div class="btn-group btn-sm">
                                <button data-id="${Number(row.id)}" class="btn btn-sm btn-warning btnEditar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button data-id="${Number(row.id)}" class="btn btn-sm btn-danger btnBorrar">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button data-id="${Number(row.id)}" data-nombre="${row.nombre}" class="btn btn-sm btn-info btnSubcategorias">
                                    <i class="fa fa-list"></i> Subcategorías
                                </button>
                            </div>
                        </div>`;
                    },
                },
            ],
        });

        // Guardar categoría
        $('#submitCategoria').click(function() {
            $.post( _URL + "/ajs/save/categorias/rep", {
                nombre: $('#nombreCategoria').val()
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Éxito",
                    text: "Se guardó correctamente",
                    icon: "success"
                });
                $('#modalCategoria').modal('hide');
                $('#nombreCategoria').val('');
                tabla_clientes.ajax.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
            });
        });

        // Eliminar categoría
        $("#tabla_clientes").on("click", ".btnBorrar", function(event) {
            let id = $(this).data('id');
            Swal.fire({
                title: "¿Está seguro?",
                text: "El cambio es irreversible. Se eliminarán también todas las subcategorías asociadas.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_URL + "/ajs/delete/categorias/rep", {
                        id: id
                    }, function(data, textStatus, jqXHR) {
                        Swal.fire({
                            title: "Eliminado!",
                            text: "Eliminado correctamente",
                            icon: "success"
                        });
                        tabla_clientes.ajax.reload();
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                        alert("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
                    });
                }
            });
        });

        // Editar categoría
        $("#tabla_clientes").on("click", ".btnEditar", function(event) {
            let id = $(this).data('id');
            $.post(_URL + "/ajs/getOne/categorias/rep", {
                id: id
            }, function(data, textStatus, jqXHR) {
                let resp = JSON.parse(data);
                $('#nombreCategoriaU').val(resp[0].nombre);
                $('#idCatU').val(resp[0].id)
                $('#updateCategoria').modal('show');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
            });
        });

        // Actualizar categoría
        $('#updateCategoriaBtn').click(function() {
            $.post(_URL + "/ajs/update/categorias/rep", {
                nombre: $('#nombreCategoriaU').val(),
                id: $('#idCatU').val()
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Éxito",
                    text: "Se guardó correctamente",
                    icon: "success"
                });
                $('#updateCategoria').modal('hide');
                tabla_clientes.ajax.reload();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
            });
        });

        // ===== FUNCIONALIDAD DE SUBCATEGORÍAS =====

        // Abrir modal de subcategorías
        $("#tabla_clientes").on("click", ".btnSubcategorias", function(event) {
            let id = $(this).data('id');
            let nombre = $(this).data('nombre');
            
            $('#categoriaIdActual').val(id);
            $('#categoriaTitle').text(nombre);
            
            cargarSubcategorias(id);
            $('#modalSubcategorias').modal('show');
        });

        // Cargar subcategorías de una categoría
        function cargarSubcategorias(categoriaId) {
            $.post(_URL + "/ajs/get/subcategorias/rep/by-categoria", {
                categoria_id: categoriaId
            }, function(data) {
                let subcategorias = JSON.parse(data);
                let html = '';
                
                if (subcategorias.length === 0) {
                    html = '<tr><td colspan="3" class="text-center">No hay subcategorías registradas</td></tr>';
                } else {
                    subcategorias.forEach(function(subcategoria) {
                        html += `<tr>
                            <td>${subcategoria.id}</td>
                            <td>${subcategoria.nombre}</td>
                            <td>
                                <div class="btn-group btn-sm">
                                    <button data-id="${subcategoria.id}" data-nombre="${subcategoria.nombre}" class="btn btn-sm btn-warning btnEditarSubcategoria">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button data-id="${subcategoria.id}" class="btn btn-sm btn-danger btnBorrarSubcategoria">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });
                }
                
                $('#subcategoriasBody').html(html);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las subcategorías: " + textStatus, errorThrown);
                alert("No se pudo cargar las subcategorías. Por favor, intenta nuevamente.");
            });
        }

        // Abrir modal para agregar subcategoría
        $('#btnAddSubcategoria').click(function() {
            $('#subcategoriaCategoria').val($('#categoriaIdActual').val());
            $('#nombreSubcategoria').val('');
            $('#modalAddSubcategoria').modal('show');
        });

        // Guardar subcategoría
        $('#submitSubcategoria').click(function() {
            let nombre = $('#nombreSubcategoria').val();
            let categoriaId = $('#subcategoriaCategoria').val();
            
            if (!nombre) {
                Swal.fire({
                    title: "Error",
                    text: "Debe ingresar un nombre para la subcategoría",
                    icon: "error"
                });
                return;
            }
            
            $.post(_URL + "/ajs/save/subcategorias/rep", {
                nombre: nombre,
                categoria_id: categoriaId
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Éxito",
                    text: "Subcategoría guardada correctamente",
                    icon: "success"
                });
                $('#modalAddSubcategoria').modal('hide');
                cargarSubcategorias(categoriaId);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al guardar la subcategoría: " + textStatus, errorThrown);
                alert("No se pudo guardar la subcategoría. Por favor, intenta nuevamente.");
            });
        });

        // Editar subcategoría
        $(document).on("click", ".btnEditarSubcategoria", function() {
            let id = $(this).data('id');
            let nombre = $(this).data('nombre');
            
            $('#subcategoriaId').val(id);
            $('#subcategoriaCategoriaId').val($('#categoriaIdActual').val());
            $('#nombreSubcategoriaEdit').val(nombre);
            
            $('#modalEditSubcategoria').modal('show');
        });

        // Actualizar subcategoría
        $('#updateSubcategoriaBtn').click(function() {
            let id = $('#subcategoriaId').val();
            let nombre = $('#nombreSubcategoriaEdit').val();
            let categoriaId = $('#subcategoriaCategoriaId').val();
            
            if (!nombre) {
                Swal.fire({
                    title: "Error",
                    text: "Debe ingresar un nombre para la subcategoría",
                    icon: "error"
                });
                return;
            }
            
            $.post(_URL + "/ajs/update/subcategorias/rep", {
                id: id,
                nombre: nombre,
                categoria_id: categoriaId
            }, function(data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Éxito",
                    text: "Subcategoría actualizada correctamente",
                    icon: "success"
                });
                $('#modalEditSubcategoria').modal('hide');
                cargarSubcategorias(categoriaId);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al actualizar la subcategoría: " + textStatus, errorThrown);
                alert("No se pudo actualizar la subcategoría. Por favor, intenta nuevamente.");
            });
        });

        // Eliminar subcategoría
        $(document).on("click", ".btnBorrarSubcategoria", function() {
            let id = $(this).data('id');
            let categoriaId = $('#categoriaIdActual').val();
            
            Swal.fire({
                title: "¿Está seguro?",
                text: "El cambio es irreversible",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_URL + "/ajs/delete/subcategorias/rep", {
                        id: id
                    }, function(data, textStatus, jqXHR) {
                        Swal.fire({
                            title: "Eliminado!",
                            text: "Subcategoría eliminada correctamente",
                            icon: "success"
                        });
                        cargarSubcategorias(categoriaId);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error al eliminar la subcategoría: " + textStatus, errorThrown);
                        alert("No se pudo eliminar la subcategoría. Por favor, intenta nuevamente.");
                    });
                }
            });
        });
    });
</script>