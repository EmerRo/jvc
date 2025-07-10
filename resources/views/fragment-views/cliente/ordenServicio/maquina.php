<!-- Agregar esto en la sección head de tu HTML o en tu archivo CSS -->
<style>
    .badge {
        display: inline-block;
        min-width: 120px;
        padding: 0.5em 1em;
        font-size: 0.875em;
        font-weight: 600;
        text-align: center;
    }

    .bg-success {
        background-color: rgb(138, 138, 138) !important;
    }

    .bg-danger {
        background-color: #ef4444 !important;
    }
</style>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">REGISTRO DE MAQUINAS </h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="clearfix">
            <ol class="breadcrumb m-0 float-start" style="background: transparent;">
                <li class="breadcrumb-item"><a href="javascript: void(0);"
                        style="color: #718096; text-decoration: none;">Orden Servicio</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);"
                        style="color: #718096; text-decoration: none;">Gestion Activos</a></li>
                <li class="breadcrumb-item active " aria-current="page" style="font-weight: 500; color: #CA3438;">
                    Maquinas</li>
            </ol>
        </div>
    </div>
    <div class="col-12">
        <div class="card"
            style="border-radius:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">

            <div class="card-title-desc text-end" style="padding: 10px 10px 0 0;">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalMaquina"
                    class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Nueva Maquina</button>
                <a href="gestion/activos" class="btn border-rojo"><i class="fa fa-arrow-left"></i> Regresar</a>
            </div>

            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_maquinas"
                                class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag me-1"></i> Número</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Equipo</th>
                                        <th>N° serie</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar Maquina -->
            <div class="modal fade" id="modalMaquina" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar Maquina</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addMaquinaForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" class="form-control" name="marca" id="marca">
                                </div>

                                <div class="mb-3">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input type="text" class="form-control" name="modelo" id="modelo">
                                </div>
                                <div class="mb-3">
                                    <label for="equipo" class="form-label">Equipo</label>
                                    <input type="text" class="form-control" name="equipo" id="equipo">
                                </div>
                                <div class="mb-3">
                                    <label for="numero_serie" class="form-label">N° serie</label>
                                    <input type="text" class="form-control" name="numero_serie" id="numero_serie">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitMaquina" class="btn bg-rojo">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Actualizar Maquina -->
            <div class="modal fade" id="updateMaquina" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Maquina</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="updateMaquinaForm">
                            <div class="modal-body">
                                <input type="text" id="idMaquinaU" value="" hidden>
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" class="form-control" id="marcaU">
                                </div>
                                <div class="mb-3">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input type="text" class="form-control" id="modeloU">
                                </div>
                                <div class="mb-3">
                                    <label for="nombreMaquinaU" class="form-label">Equipo</label>
                                    <input type="text" class="form-control" id="nombreMaquinaU">
                                </div>
                                <div class="mb-3">
                                    <label for="numero_serieU" class="form-label">N° serie</label>
                                    <input type="text" class="form-control" id="numero_serieU">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateMaquinaBtn" class="btn bg-rojo">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Configuración de DataTable para las maquinas
        tabla_maquinas = $("#tabla_maquinas").DataTable({
            "processing": true,
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            cache: false,
            ajax: {
                url: _URL + "/ajs/get/maquinas",
                method: "GET",
                dataSrc: function (data) {
                    return data;
                },
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columns: [
                { data: "numero", class: "text-center" }, // CAMBIO: usar campo numero
                { data: "marca", class: "text-center" },
                { data: "modelo", class: "text-center" },
                { data: "equipo", class: "text-center" },
                { data: "numero_serie", class: "text-center" },
                {
                    data: "estado_actual",
                    class: "text-center",
                    render: function (data, type, row) {
                        let badgeClass = data === 'DISPONIBLE' ? 'bg-success' : 'bg-danger';
                        return `<span class="badge ${badgeClass}" style="min-width: 120px;">${data}</span>`;
                    }
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return `<div class="text-center">
                    <div class="btn-group btn-sm">
                        <button data-id="${Number(row.id)}" class="btn btn-sm btn-warning btnEditar">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button data-id="${Number(row.id)}" class="btn btn-sm btn-danger btnBorrar">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>`;
                    },
                }
            ]
        });

        // Resto del código JavaScript permanece igual...
        $('#submitMaquina').click(function () {
            $.post(_URL + "/ajs/save/maquinas", {
                equipo: $('#equipo').val(),
                marca: $('#marca').val(),
                modelo: $('#modelo').val(),
                numero_serie: $('#numero_serie').val(),
            })
                .done(function (response) {
                    let data = JSON.parse(response);
                    if (data.status === "success") {
                        Swal.fire({
                            title: "Éxito",
                            text: data.message,
                            icon: "success"
                        });
                        $('#modalMaquina').modal('hide');
                        $('#addMaquinaForm')[0].reset();
                        tabla_maquinas.ajax.reload();
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: data.message,
                            icon: "error"
                        });
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Error:", textStatus, errorThrown);
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo guardar la máquina",
                        icon: "error"
                    });
                });
        });

        // Editar Maquina
        $("#tabla_maquinas").on("click", ".btnEditar", function () {
            let id = $(this).data('id');
            $.post(_URL + "/ajs/getOne/maquinas", { id: id })
                .done(function (response) {
                    let data = JSON.parse(response);
                    $('#idMaquinaU').val(data.id);
                    $('#nombreMaquinaU').val(data.equipo);
                    $('#marcaU').val(data.marca);
                    $('#modeloU').val(data.modelo);
                    $('#numero_serieU').val(data.numero_serie);
                    $('#updateMaquina').modal('show');
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Error:", textStatus, errorThrown);
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo cargar los datos de la máquina",
                        icon: "error"
                    });
                });
        });

        // Actualizar Maquina
        $('#updateMaquinaBtn').click(function () {
            $.post(_URL + "/ajs/update/maquinas", {
                id: $('#idMaquinaU').val(),
                equipo: $('#nombreMaquinaU').val(),
                marca: $('#marcaU').val(),
                modelo: $('#modeloU').val(),
                numero_serie: $('#numero_serieU').val()
            })
                .done(function (response) {
                    Swal.fire({
                        title: "Actualizado",
                        text: "Datos actualizados correctamente",
                        icon: "success"
                    });
                    $('#updateMaquina').modal('hide');
                    $('#updateMaquinaForm')[0].reset();
                    tabla_maquinas.ajax.reload();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Error:", textStatus, errorThrown);
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo actualizar la máquina",
                        icon: "error"
                    });
                });
        });

        // Eliminar Maquina
        $("#tabla_maquinas").on("click", ".btnBorrar", function () {
            let id = $(this).data('id');
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
                    $.post(_URL + "/ajs/delete/maquinas", { id: id })
                        .done(function (response) {
                            Swal.fire({
                                title: "Eliminado",
                                text: "Eliminado correctamente",
                                icon: "success"
                            });
                            tabla_maquinas.ajax.reload();
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            console.error("Error:", textStatus, errorThrown);
                            Swal.fire({
                                title: "Error",
                                text: "No se pudo eliminar la máquina",
                                icon: "error"
                            });
                        });
                }
            });
        });
    });
</script>