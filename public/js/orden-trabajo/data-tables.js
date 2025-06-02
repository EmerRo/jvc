// Configuración de DataTables
let tabla_clientes;

$(document).ready(function() {
    tabla_clientes = $("#tabla_clientes").DataTable({
        paging: true,
        bFilter: true,
        ordering: true,
        searching: true,
        destroy: true,
        ajax: {
            url: _URL + "/ajs/prealerta/render",
            method: "POST",
            dataSrc: function (json) {
                if (json.error) {
                    console.error('Error del servidor:', json.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: json.message || 'Error al cargar los datos'
                    });
                    return [];
                }
                return Array.isArray(json) ? json : [];
            },
            error: function (xhr, error, thrown) {
                console.error('Error en la solicitud AJAX:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar los datos. Por favor, intenta de nuevo.'
                });
            }
        },
        language: {
            url: "ServerSide/Spanish.json",
        },
        columns: [
            {
                data: null,
                class: "text-center",
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: "cliente_razon_social",
                class: "text-center",
            },
            {
                data: "atencion_encargado",
                class: "text-center",
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.equipos && row.equipos.length > 0) {
                        return row.equipos.map(function (equipo) {
                            return equipo.marca;
                        }).join('<br><br>');
                    }
                    return '';
                }
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.equipos && row.equipos.length > 0) {
                        return row.equipos.map(function (equipo) {
                            return equipo.modelo;
                        }).join('<br><br>');
                    }
                    return '';
                }
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.equipos && row.equipos.length > 0) {
                        return row.equipos.map(function (equipo) {
                            return equipo.equipo;
                        }).join('<br><br>');
                    }
                    return '';
                }
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.equipos && row.equipos.length > 0) {
                        return row.equipos.map(function (equipo) {
                            return ` ${equipo.numero_serie}`;
                        }).join('<br><br>');
                    }
                    return '';
                }
            },
            {
                data: "fecha_ingreso",
                class: "text-center",
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return `<div class="text-center">
                        <div class="btn-group btn-sm">
                            <button data-id="${row.id_preAlerta}" class="btn btn-sm btn-warning btnEditar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button data-id="${row.id_preAlerta}" class="btn btn-sm btn-danger btnBorrar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>`;
                },
            },
        ],
    });
});

