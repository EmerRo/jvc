<!-- resources\views\fragment-views\cliente\ordenServicio\motivo.php -->
<?php

?>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">Motivos</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalModelo" class="btn bg-rojo text-white"><i class="fa fa-plus"></i> Añadir Motivo</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="gestion/activos" class="btn border-rojo "><i class="fa fa-arrow-left"></i> Regresar</a>
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
                                        <th>Nombre del Motivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar Modelo -->
            <div class="modal fade" id="modalModelo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar Motivo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addModeloForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nombreModelo" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreModelo">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="submitModelo" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Actualizar Modelo -->
            <div class="modal fade" id="updateModelo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Motivo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="updateModeloForm">
                            <div class="modal-body">
                                <input type="text" id="idMotivoU" value="" hidden>
                                <div class="mb-3">
                                    <label for="nombreMotivoU" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreMotivoU">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" id="updateModeloBtn" class="btn bg-rojo text-white">Guardar</button>
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
    // Función para cargar motivos
    function cargarMotivos() {
        $.ajax({
            url: _URL + '/ajs/get/motivos', // Asegúrate de usar _URL
            type: 'GET',
            dataType: 'json', // Especificar que esperamos JSON
            success: function(response) {
                try {
                    // Si la respuesta ya es un objeto (no necesita parse)
                    if (response.status) {
                        actualizarSelectMotivos(response.data);
                        actualizarListaMotivos(response.data);
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta:', e);
                    alertAdvertencia("Error al cargar los motivos");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición:', error);
                alertAdvertencia("Error al cargar los motivos");
            }
        });
    }
// Inicializar DataTable
var tabla = $('#tabla_clientes').DataTable({
         "processing": true,
        "responsive": true,    // ✅ Habilitar responsividad
        "scrollX": false,      // ✅ Deshabilitar scroll horizontal
        "autoWidth": false,    // ✅ Deshabilitar auto-ancho
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
    "ajax": {
        "url": _URL + '/ajs/get/motivos',
        "dataSrc": function(json) {
            // Si viene con status, devolver data, sino el array completo
            return json.status ? json.data : json;
        }
    },
    "columns": [
        {"data": null, render: function (data, type, row, meta) {
            return meta.row + 1;
        }},
        {"data": "nombre"},
        {"data": null, render: function (data, type, row) {
            return `
                <button class="btn btn-primary btn-sm editar-motivo" data-id="${row.id}" data-nombre="${row.nombre}">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm btn-eliminar-motivo" data-id="${row.id}">
                    <i class="fa fa-trash"></i>
                </button>
            `;
        }}
    ],
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
    }
});
 

    // Función para eliminar motivo
    $(document).on('click', '.btn-eliminar-motivo', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: "¿Está seguro?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + '/ajs/delete/motivos',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            cargarMotivos();
                            alertExito("Motivo eliminado correctamente");
                        } else {
                            alertAdvertencia(response.message || "Error al eliminar el motivo");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la petición:', error);
                        alertAdvertencia("Error al eliminar el motivo");
                    }
                });
            }
        });
    });
    // Evento para guardar motivo desde el modal
$('#submitModelo').on('click', function() {
    const nombreMotivo = $('#nombreModelo').val();
    
    if (!nombreMotivo.trim()) {
        alertAdvertencia("El nombre del motivo es requerido");
        return;
    }
    
    $.ajax({
        url: _URL + '/ajs/save/motivos',
        type: 'POST',
        data: { nombre: nombreMotivo },
        dataType: 'json',
        success: function(response) {
            $('#nombreModelo').val('');
            $('#modalModelo').modal('hide');
            tabla.ajax.reload();
            alertExito("Motivo guardado correctamente");
        },
        error: function() {
            alertAdvertencia("Error al guardar el motivo");
        }
    });
});

// Evento para actualizar motivo
$('#updateModeloBtn').on('click', function() {
    const id = $('#idMotivoU').val();
    const nombre = $('#nombreMotivoU').val();
    
    if (!nombre.trim()) {
        alertAdvertencia("El nombre del motivo es requerido");
        return;
    }
    
    $.ajax({
        url: _URL + '/ajs/update/motivos',
        type: 'POST',
        data: { id: id, nombre: nombre },
        dataType: 'json',
        success: function(response) {
            $('#updateModelo').modal('hide');
            tabla.ajax.reload();
            alertExito("Motivo actualizado correctamente");
        },
        error: function() {
            alertAdvertencia("Error al actualizar el motivo");
        }
    });
});

// Evento para abrir modal de edición
$(document).on('click', '.editar-motivo', function() {
    const id = $(this).data('id');
    const nombre = $(this).data('nombre');
    
    $('#idMotivoU').val(id);
    $('#nombreMotivoU').val(nombre);
    $('#updateModelo').modal('show');
});

    // Función para actualizar el select de motivos
    function actualizarSelectMotivos(motivos) {
        const select = $('#select_motivo');
        select.empty();
        select.append('<option value="">Seleccione un motivo</option>');
        motivos.forEach(function(motivo) {
            select.append(`<option value="${motivo.id}">${motivo.nombre}</option>`);
        });
    }

    // Función para actualizar la lista de motivos en el modal
    function actualizarListaMotivos(motivos) {
        const lista = $('#listaMotivos');
        lista.empty();
        motivos.forEach(function(motivo) {
            lista.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${motivo.nombre}
                    <button class="btn btn-danger btn-sm btn-eliminar-motivo" data-id="${motivo.id}">
                        <i class="fa fa-trash"></i> Eliminar
                    </button>
                </li>
            `);
        });
    }

    // Cargar motivos al iniciar
    cargarMotivos();
});
</script>
