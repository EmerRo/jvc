<!-- gestion-activos.php -->
<style>
    .modal-header.bg-danger {
        background-color: #dc3545 !important;
    }

    .correlativo {
        font-size: 0.9rem;
    }

    .correlativo-grande {
        font-size: 1.2rem;
        margin-bottom: 0;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
    }

    .table th {
        font-weight: 600;
    }

    .modal-body.bg-light {
        background-color: #f8f9fa;
    }

    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: white;
    }

    .badge {
        font-size: 0.875em;
        padding: 0.5em 0.75em;
    }

    .badge.bg-warning {
        color: #000 !important;
    }

    .badge i {
        font-size: 0.875em;
    }

    /* Animación para alertas urgentes */
    .badge.bg-danger {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }
</style>
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center h1-style-2">REGISTRO DE GESTIÓN DE ACTIVOS</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="/registro/activos" class="btn bg-rojo text-white button-link">
                            <i class="fa fa-plus"></i> Añadir Registro De Gestión Activos
                        </a>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/motivo" class="btn border-rojo button-link">
                                <i class="fa fa-plus"></i> Motivo
                            </a>
                            <a href="/maquina" class="btn border-rojo button-link">
                                <i class="fa fa-plus"></i> Máquina
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="conte-vue-modals">
                <!-- Modal de Detalles -->
                <!-- Modal de Detalles -->
                <div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <div>
                                    <h5 class="modal-title fw-bold mb-1">Detalles del ACTIVO</h5>
                                    <div class="correlativo text-white-50" id="correlativo"></div>
                                </div>
                                <div class="ms-auto d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-light btn-sm me-2"
                                        id="btnDescargarPDF">
                                        <i class="fas fa-download me-1"></i> Descargar
                                    </button>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                            <div class="modal-body bg-light">
                                <!-- Correlativo grande -->
                                <div class="text-center mb-4">
                                    <h4 class="correlativo-grande text-danger fw-bold" id="correlativo-grande"></h4>
                                </div>

                                <!-- Información Principal -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-user-circle text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Cliente</label>
                                                        <div class="fw-bold" id="detalle-cliente"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-info-circle text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Motivo</label>
                                                        <div class="fw-bold" id="detalle-motivo"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalles del Equipo -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-danger text-white py-2">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-cogs me-2"></i>Especificaciones del Equipo
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-danger">
                                                    <tr>
                                                        <th>Marca</th>
                                                        <th>Modelo</th>
                                                        <th>Equipo</th>
                                                        <th>Número de Serie</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="detalle-marca"></td>
                                                        <td id="detalle-modelo"></td>
                                                        <td id="detalle-equipo"></td>
                                                        <td id="detalle-serie"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fechas y Observaciones -->
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-calendar-minus text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Fecha de Salida</label>
                                                        <div class="fw-bold" id="detalle-salida"></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-plus text-danger me-2 fa-lg"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Fecha de Ingreso</label>
                                                        <div class="fw-bold" id="detalle-ingreso"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-comment-alt text-danger me-2 fa-lg mt-1"></i>
                                                    <div>
                                                        <label class="small text-muted mb-0">Observaciones</label>
                                                        <div class="fw-bold" id="detalle-observaciones"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes"
                                class="table table-bordered dt-responsive nowrap text-center table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Cliente/Razón Social</th>
                                        <th>Motivo</th>
                                        <th>Fecha De Salida</th>
                                        <th>Fecha De Ingreso</th>
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

<!-- Tabla principal con las acciones completas -->
<script>
    $(document).ready(function () {
        tabla_clientes = $("#tabla_clientes").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            "responsive": true, // Habilitar responsividad
    "scrollX": false,   // Deshabilitar scroll horizontal
    "autoWidth": false, // Deshabilitar auto-ancho
    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip', 
            ajax: {
                url: _URL + "/ajs/gestion/activos/render",
                method: "POST",
                dataSrc: ""
            },
            language: {
                url: "ServerSide/Spanish.json"
            },
            columns: [
                { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
                { data: "cliente_razon_social", class: "text-center" },
                { data: "motivo", class: "text-center" },
                { data: "fecha_salida", class: "text-center" },
                {
                    data: "fecha_ingreso",
                    class: "text-center",
                    render: function (data, type, row) {
                        const fechaIngreso = data ? new Date(data) : null;
                        const fechaActual = new Date();
                        const diasRestantes = fechaIngreso ? Math.ceil((fechaIngreso - fechaActual) / (1000 * 60 * 60 * 24)) : null;

                        if (!fechaIngreso || row.estado !== 'CONFIRMADO') {
                            if (row.fecha_ingreso && new Date(row.fecha_ingreso) < fechaActual) {
                                // Fecha vencida
                                return `<span class="badge bg-danger" data-bs-toggle="tooltip" title="Fecha de ingreso vencida">
                        <i class="fas fa-exclamation-triangle me-1"></i>${data || 'No definida'}
                    </span>`;
                            } else if (diasRestantes !== null && diasRestantes <= 3 && diasRestantes > 0) {
                                // Próximo a vencer (3 días o menos)
                                return `<span class="badge bg-warning text-dark" data-bs-toggle="tooltip" 
                        title="Quedan ${diasRestantes} días">
                        <i class="fas fa-clock me-1"></i>${data}
                    </span>`;
                            }
                        }
                        return data || 'Pendiente';
                    }
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        let confirmarBtn = '';
                        let estadoIcon = '';

                        if (row.estado === 'CONFIRMADO') {
                            estadoIcon = `<button class="btn btn-sm btn-success" disabled title="Activo en Oficina">
                                <i class="fas fa-check-circle"></i>
                            </button>`;
                        } else {
                            confirmarBtn = `
                                <button data-id="${row.id}" 
                                        class="btn btn-sm btn-info btnConfirmar" 
                                        title="Confirmar llegada a oficina">
                                    <i class="fas fa-check-circle"></i>
                                </button>`;
                        }

                        return `<div class="btn-group btn-group-sm">
                            <button class="btn btn-info btn-ver" data-id="${row.id}" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button data-id="${row.id}" class="btn btn-warning btnEditar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button data-id="${row.id}" class="btn btn-danger btnBorrar" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            ${confirmarBtn}
                            ${estadoIcon}
                        </div>`;
                    }
                }
            ]
        });

        // Manejador para el botón ver detalles
        // Manejador para el botón ver detalles
        $("#tabla_clientes").on("click", ".btn-ver", function () {
            const id = $(this).data("id");

            $.ajax({
                url: _URL + "/ajs/gestion/activos/obtener",
                type: "POST",
                data: { id: id },
                success: function (response) {
                    const data = JSON.parse(response);

                    // Almacenar el ID en el modal
                    $("#modalDetalles").data("activo-id", id);

                    // Generar correlativo
                    const year = new Date().getFullYear();
                    const correlativo = `${String(id).padStart(6, '0')}/${year}`;

                    // Actualizar correlativos
                    $("#correlativo").text(`N° ${correlativo}`);
                    $("#correlativo-grande").text(`GESTIÓN DE ACTIVOS N° ${correlativo}`);

                    // Llenar el modal con los datos
                    $("#detalle-cliente").text(data.cliente_razon_social);
                    $("#detalle-marca").text(data.marca);
                    $("#detalle-modelo").text(data.modelo);
                    $("#detalle-equipo").text(data.equipo);
                    $("#detalle-serie").text(data.numero_serie);
                    $("#detalle-motivo").text(data.motivo);
                    $("#detalle-ingreso").text(data.fecha_ingreso || 'Pendiente');
                    $("#detalle-salida").text(data.fecha_salida);
                    $("#detalle-observaciones").text(data.observaciones || 'Sin observaciones');

                    // Mostrar el modal
                    $("#modalDetalles").modal('show');
                }
            });
        });

        // Manejador para el botón de descarga
        $("#btnDescargarPDF").click(function () {
            const id = $("#modalDetalles").data("activo-id");
            if (id) {
                window.location.href = `${_URL}/gestion/activos/descargar-pdf/${id}`;
            } else {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo identificar el activo",
                    icon: "error"
                });
            }
        });
        // Acción para eliminar prealerta
        $("#tabla_clientes").on("click", ".btnBorrar", function () {
            const id = $(this).data("id");
            Swal.fire({
                title: "¿Deseas borrar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/gestion/activos/delete",
                        type: "post",
                        data: { idDelete: id },
                        success: function (resp) {
                            tabla_clientes.ajax.reload(null, false);
                            Swal.fire("¡Buen trabajo!", "Registro Borrado Exitosamente", "success");
                        }
                    });
                }
            });
        });

        // Confirmar llegada a oficina
        // Reemplaza el manejador de eventos actual del botón confirmar con este:
        $("#tabla_clientes").on("click", ".btnConfirmar", function () {
            const id = $(this).data("id");
            const row = tabla_clientes.row($(this).closest('tr')).data();

            const confirmarActivo = (fecha = null) => {
                const datos = { id: id };
                if (fecha) {
                    datos.fecha_ingreso = fecha;
                }

                $.ajax({
                    url: _URL + "/ajs/gestion/activos/confirmar",
                    type: "POST",
                    data: datos,
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire(
                                "¡Confirmado!",
                                "El activo ha sido marcado como recibido en oficina",
                                "success"
                            );
                            tabla_clientes.ajax.reload(null, false);
                        } else if (data.requiresFechaIngreso) {
                            // Si se requiere fecha de ingreso, mostrar modal para ingresarla
                            Swal.fire({
                                title: "Fecha de Ingreso Requerida",
                                html: `
                            <p>Este activo no tiene fecha de ingreso registrada.</p>
                            <input type="date" id="fecha_ingreso" class="swal2-input" value="${new Date().toISOString().split('T')[0]}">
                        `,
                                showCancelButton: true,
                                confirmButtonText: "Confirmar",
                                cancelButtonText: "Cancelar",
                                preConfirm: () => {
                                    const fecha = document.getElementById('fecha_ingreso').value;
                                    if (!fecha) {
                                        Swal.showValidationMessage('Por favor seleccione una fecha de ingreso');
                                        return false;
                                    }
                                    return fecha;
                                }
                            }).then((result) => {
                                if (result.isConfirmed && result.value) {
                                    confirmarActivo(result.value);
                                }
                            });
                        } else {
                            Swal.fire(
                                "Error",
                                data.error || "No se pudo actualizar el estado del activo",
                                "error"
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            "Error",
                            "Hubo un problema al comunicarse con el servidor",
                            "error"
                        );
                    }
                });
            };

            // Mostrar el primer modal de confirmación
            Swal.fire({
                title: "¿Confirmar llegada a oficina?",
                text: "Esta acción marcará el activo como recibido en oficina",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, confirmar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmarActivo();
                }
            });
        });
        function verificarFechasVencimiento() {
    const tabla = $("#tabla_clientes").DataTable();
    const datos = tabla.data().toArray();
    let alertasMostradas = 0;

    datos.forEach(row => {
        if (row.estado !== 'CONFIRMADO' && row.fecha_ingreso) {
            const fechaIngreso = new Date(row.fecha_ingreso);
            const fechaActual = new Date();
            const diasRestantes = Math.ceil((fechaIngreso - fechaActual) / (1000 * 60 * 60 * 24));

            // Solo mostrar alertas si no se han mostrado demasiadas
            if (alertasMostradas < 3) {
                if (diasRestantes < 0) {
                    // Fecha vencida
                    Swal.fire({
                        title: '¡Alerta de vencimiento!',
                        html: `El activo de <strong>${row.cliente_razon_social}</strong> tiene la fecha de ingreso vencida.<br>
                              Fecha programada: ${row.fecha_ingreso}`,
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    alertasMostradas++;
                } else if (diasRestantes <= 3) {
                    // Próximo a vencer
                    Swal.fire({
                        title: '¡Próximo vencimiento!',
                        html: `El activo de <strong>${row.cliente_razon_social}</strong> debe ingresar en ${diasRestantes} días.<br>
                              Fecha programada: ${row.fecha_ingreso}`,
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                    alertasMostradas++;
                }
            }
        }
    });
}

// Ejecutar la verificación al cargar la página y cada 24 horas
$(document).ready(function() {
    verificarFechasVencimiento();
    setInterval(verificarFechasVencimiento, 24 * 60 * 60 * 1000);
});

// Agregar tooltips
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
    });
</script>