// public\js\guia-remision\motivos.js
$(document).ready(function () {
    // Función para cargar motivos
    function cargarMotivos() {
        $.ajax({
            url: _URL + '/ajs/get/motivos-guia',  // Asegurarse de usar _URL
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function (response) {
                try {
                    // Si la respuesta es string, parsearlo
                    const data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.status) {
                        actualizarSelectMotivos(data.data);
                        actualizarListaMotivos(data.data);
                    } else {
                        console.error('Error en la respuesta:', data);
                        alertAdvertencia("Error al cargar los motivos");
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta:', e);
                    alertAdvertencia("Error al procesar los motivos");
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la petición:', error);
                alertAdvertencia("Error al cargar los motivos");
            }
        });
    }

    // Evento para agregar nuevo motivo
    $('#motivoForm').on('submit', function (e) {
        e.preventDefault();
        const nombreMotivo = $('#nombreMotivo').val().trim();

        if (!nombreMotivo) {
            alertAdvertencia("El nombre del motivo es requerido");
            return;
        }

        $.ajax({
            url: _URL + '/ajs/save/motivos-guia',
            type: 'POST',
            data: { nombre: nombreMotivo },
            success: function (response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.status) {
                        $('#nombreMotivo').val('');
                        cargarMotivos();
                        $('#motivoModal').modal('hide');
                        alertExito("Motivo guardado correctamente");
                    } else {
                        alertAdvertencia(data.message || "Error al guardar el motivo");
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta:', e);
                    alertAdvertencia("Error al procesar la respuesta");
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la petición:', error);
                alertAdvertencia("Error al guardar el motivo");
            }
        });
    });

    // Función para eliminar motivo
    $(document).on('click', '.btn-eliminar-motivo', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + '/ajs/delete/motivos-guia',
                    type: 'POST',
                    data: { id: id },
                    success: function (response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;

                            if (data.status) {
                                cargarMotivos();
                                alertExito("Motivo eliminado correctamente");
                            } else {
                                alertAdvertencia(data.message || "Error al eliminar el motivo");
                            }
                        } catch (e) {
                            console.error('Error al procesar la respuesta:', e);
                            alertAdvertencia("Error al procesar la respuesta");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error en la petición:', error);
                        alertAdvertencia("Error al eliminar el motivo");
                    }
                });
            }
        });
    });

    // Función para actualizar el select de motivos
    function actualizarSelectMotivos(motivos) {
        const select = $('#select_motivo');
        select.empty();
        select.append('<option value="">Seleccione un motivo</option>');

        if (Array.isArray(motivos)) {
            motivos.forEach(function (motivo) {
                select.append(`<option value="${motivo.id}">${motivo.nombre}</option>`);
            });
        }
    }

    // Función para actualizar la lista de motivos en el modal
    function actualizarListaMotivos(motivos) {
        const lista = $('#listaMotivos');
        lista.empty();

        if (Array.isArray(motivos)) {
            motivos.forEach(function (motivo) {
                lista.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${motivo.nombre}
                    <button class="btn btn-danger btn-sm btn-eliminar-motivo" data-id="${motivo.id}">
                        <i class="fa fa-trash"></i> 
                    </button>
                </li>
            `);
            });
        }
    }

    // Cargar motivos al iniciar
    cargarMotivos();
});