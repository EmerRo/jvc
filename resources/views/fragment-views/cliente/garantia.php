<!-- resources\views\fragment-views\cliente\garantia.php -->
<div class="container mt-4">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h6 class="page-title">REGISTRO DE GARANTÍA</h6>
            </div>
        </div>
    </div>

</div>

<!-- Modal de Detalles Mejorado -->
<div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="detalleModalLabel">Detalles de la Garantía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-rojo mb-3">
                                    <i class="fa fa-user-circle me-2"></i>Información del Cliente
                                </h6>
                                <div class="mb-2">
                                    <label class="text-muted small">Cliente:</label>
                                    <p class="mb-1 fw-bold" id="detalle_cliente">-</p>
                                </div>
                                <div>
                                    <label class="text-muted small">Guía de Remisión:</label>
                                    <p class="mb-0 fw-bold" id="detalle_guia">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-rojo mb-3">
                                    <i class="fa fa-calendar me-2"></i>Fechas
                                </h6>
                                <div class="mb-2">
                                    <label class="text-muted small">Fecha de Inicio:</label>
                                    <p class="mb-1 fw-bold" id="detalle_fecha_inicio">-</p>
                                </div>
                                <div>
                                    <label class="text-muted small">Fecha de Caducidad:</label>
                                    <p class="mb-0 fw-bold" id="detalle_fecha_caducidad">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="equipos-container">
                    <h6 class="text-rojo mb-3">
                        <i class="fa fa-laptop me-2"></i>Equipos Registrados
                    </h6>
                    <!-- Contenedor con scroll para los equipos -->
                    <div id="detalle_equipos" class="mt-3" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                        <!-- Los equipos se agregarán dinámicamente -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-rojo text-white" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Tabla de Garantías -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card"
            style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista de Garantías Registradas</h5>
                <div>
                    <a href="garantia/editar-certificado" class="btn border-rojo me-2">
                        <i class="fa fa-edit"></i> Editar Certificado de Garantía
                    </a>
                    <a href="garantia/manual" class="btn bg-rojo text-white me-2">
                        <i class="fa fa-plus"></i> Añadir Garantía
                    </a>
                </div>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla_garantia"
                            class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Cliente</th>
                                    <th>series</th>
                                    <th>Guía De Remisión</th>
                                    <th>Fecha De Inicio</th>
                                    <th>Fecha De Caducidad</th>
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

<script>
    $(document).ready(function () {
        var tabla_garantia = $("#tabla_garantia").DataTable({
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
                url: _URL + "/ajs/garantia/render",
                method: "POST",
                dataSrc: '',
            },
            language: {
                url: "serverSide/Spanish.json",
            },
            // Modificar la configuración de columnas en DataTables
            // Configuración correcta de columnas para DataTables
columns: [
    {
        // Primera columna: Item (número correlativo)
        data: null,
        class: "text-center",
        render: function (data, type, row, meta) {
            return meta.row + 1; // Esto muestra el número de fila + 1
        }
    },
    // Segunda columna: Cliente
    { data: "cliente_ruc_dni", class: "text-center" },
    // Tercera columna: Series (cantidad)
    { 
        data: null, 
        class: "text-center",
        render: function(data, type, row) {
            // Verificar si hay series_ids y mostrar la cantidad
            let seriesCount = 1; // Por defecto, 1 serie
            if (row.series_ids) {
                try {
                    const seriesData = JSON.parse(row.series_ids);
                    if (Array.isArray(seriesData)) {
                        seriesCount = seriesData.length;
                    }
                } catch (e) {
                    console.error("Error al parsear series_ids:", e);
                }
            }
            
            // Mostrar un badge con la cantidad de series
            return `<span class="badge bg-info">${seriesCount}</span>`;
        }
    },
    // Cuarta columna: Guía de Remisión
    {
        data: null,
        class: "text-center",
        render: function (data, type, row) {
            return row.guia_remision || '-';
        }
    },
    // Quinta columna: Fecha de Inicio
    { data: "fecha_inicio", class: "text-center" },
    // Sexta columna: Fecha de Caducidad
    { data: "fecha_caducidad", class: "text-center" },
    // Séptima columna: Acciones
    {
        data: null,
        class: "text-center",
        render: function (data, type, row) {
            return `<div class="text-center">
                <div class="btn-group btn-sm">
                    <button data-id="${row.id_garantia}" class="btn btn-sm btn-info btnDetalle" title="Ver detalles">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button data-id="${row.id_garantia}" class="btn btn-sm btn-warning btnEditar" title="Editar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <a href="${_URL + '/r/garantia/certificado/' + row.id_garantia}" target="_blank" class="btn btn-sm btn-primary" title="Ver certificado">
                        <i class="fa fa-file"></i>
                    </a>
                    <button data-id="${row.id_garantia}" class="btn btn-sm btn-danger btnBorrar" title="Eliminar">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;
        }
    }
]
        });

        // Manejador para el botón de detalles

       // Modificar el modal de detalles para añadir scroll
$(document).on('click', '.btnDetalle', function () {
    const id = $(this).data('id');
    
    // Mostrar un indicador de carga
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo detalles de la garantía',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: _URL + "/ajs/garantia/getOne",
        method: "POST",
        data: { id_garantia: id },
        success: function (response) {
            // Cerrar el indicador de carga
            Swal.close();
            
            try {
                const data = JSON.parse(response);
                if (data && data.length > 0) {
                    // Usar el primer elemento para la información general
                    const garantia = data[0];
                    
                    // Depurar los datos recibidos
                    console.log("Datos de garantía recibidos:", data);

                    // Llenar la información básica
                    $('#detalle_cliente').text(garantia.cliente_ruc_dni || '-');
                    $('#detalle_guia').text(garantia.guia_remision || '-');
                    $('#detalle_fecha_inicio').text(garantia.fecha_inicio || '-');
                    $('#detalle_fecha_caducidad').text(garantia.fecha_caducidad || '-');

                    // Limpiar la sección de equipos
                    $('#detalle_equipos').empty();
                    
                    // Añadir estilos de scroll al contenedor de equipos
                    $('#detalle_equipos').css({
                        'max-height': '400px',
                        'overflow-y': 'auto',
                        'padding-right': '10px'
                    });

                    // Mostrar el contador de series
                    if (data.length > 1) {
                        $('#detalle_equipos').append(`
                            <div class="alert alert-info mb-3 sticky-top" style="top: 0; z-index: 1020; background-color: #cff4fc;">
                                <i class="fa fa-info-circle me-2"></i>
                                Esta garantía incluye ${data.length} series.
                            </div>
                        `);
                    }
                    
                    // Mostrar cada serie como un equipo
                    data.forEach((item, index) => {
                        const marcaNombre = item.marca_nombre || item.marca || '-';
                        const modeloNombre = item.modelo_nombre || item.modelo || '-';
                        const equipoNombre = item.equipo_nombre || item.equipo || '-';
                        const numeroSerie = item.numero_serie || '-';
                        
                        $('#detalle_equipos').append(`
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Equipo ${index + 1}</h6>
                                        <span class="badge bg-rojo">${numeroSerie !== '-' ? 'Serie: ' + numeroSerie : 'Sin número de serie'}</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="text-muted small">Marca:</label>
                                            <p class="mb-0 fw-bold">${marcaNombre}</p>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="text-muted small">Modelo:</label>
                                            <p class="mb-0 fw-bold">${modeloNombre}</p>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="text-muted small">Equipo:</label>
                                            <p class="mb-0 fw-bold">${equipoNombre}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });

                    // Mostrar el modal
                    $('#detalleModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin datos',
                        text: 'No se encontraron datos de la garantía'
                    });
                }
            } catch (error) {
                console.error("Error al procesar los datos:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar los datos de la garantía'
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información de la garantía'
            });
        }
    });



            // Función auxiliar para mostrar un único equipo
            function mostrarEquipoUnico(garantia) {
                const marcaNombre = garantia.marca_nombre || garantia.marca || '-';
                const modeloNombre = garantia.modelo_nombre || garantia.modelo || '-';
                const equipoNombre = garantia.equipo_nombre || garantia.equipo || '-';
                const numeroSerie = garantia.numero_serie || '-';

                $('#detalle_equipos').append(`
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">Información del Equipo</h6>
                        <span class="badge bg-rojo">${numeroSerie !== '-' ? 'Serie: ' + numeroSerie : 'Sin número de serie'}</span>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="text-muted small">Marca:</label>
                            <p class="mb-0 fw-bold">${marcaNombre}</p>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-muted small">Modelo:</label>
                            <p class="mb-0 fw-bold">${modeloNombre}</p>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-muted small">Equipo:</label>
                            <p class="mb-0 fw-bold">${equipoNombre}</p>
                        </div>
                    </div>
                </div>
            </div>
        `);
            }
        });
        $('#closeModalButton').click(function () {
            $('#editModal').modal('hide');
        });
        $('#cerrarModalButton').click(function () {
            $('#editModal').modal('hide');
        });
        // Manejador para guardar la edición
        $("#formEditarGarantia").on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: _URL + "/ajs/garantia/editar",
                method: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    const res = JSON.parse(response);
                    if (res.res) {
                        Swal.fire('Éxito', res.msg, 'success');
                        $("#editModal").modal('hide');
                        tabla_garantia.ajax.reload();
                    } else {
                        Swal.fire('Error', res.msg, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire('Error', 'No se pudo actualizar la garantía', 'error');
                }
            });
        });

        // Manejador para el botón de borrar
        $(document).on('click', '.btnBorrar', function () {
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
                        url: _URL + "/ajs/garantia/borrar",
                        method: "POST",
                        data: { value: id },
                        success: function (response) {
                            const res = JSON.parse(response);
                            if (res.res) {
                                Swal.fire('Eliminado', res.msg, 'success');
                                tabla_garantia.ajax.reload();
                            } else {
                                Swal.fire('Error', res.msg, 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('Error', 'No se pudo eliminar la garantía', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function () {

        $("#input_buscar_Dataseries").autocomplete({
            source: _URL + "/ajs/buscar/serie/datos",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui.item);
                console.log('datos buscados');
                app.garantia.cliente_nombre = ui.item.cliente_ruc_dni;
                app.garantia.num_serie = ui.item.numero_serie;
                app.garantia.marc = ui.item.marca;
                app.garantia.model = ui.item.modelo;
                $('#input_buscar_DataSeries').val("")

            }
        })
        const garantiaData = JSON.parse(sessionStorage.getItem('garantia_data'));
        if (garantiaData) {
            // Pre-llenar los campos
            app.garantia.cliente_nombre = garantiaData.cliente_nombre;

            if (garantiaData.equipos && garantiaData.equipos.length > 0) {
                const primerEquipo = garantiaData.equipos[0];
                app.garantia.num_serie = primerEquipo.numero_serie;
                app.garantia.marc = primerEquipo.marca;
                app.garantia.model = primerEquipo.modelo;
            }

            // Limpiar los datos almacenados
            sessionStorage.removeItem('garantia_data');
        }

    });
</script>
<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>