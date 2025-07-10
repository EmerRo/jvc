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
                
                <!-- Sección de Equipos como Tabla -->
                <div class="equipos-container">
                    <h6 class="text-rojo mb-3">
                        <i class="fa fa-laptop me-2"></i>Equipos Registrados
                    </h6>
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table id="tabla_equipos_modal" class="table table-striped table-bordered table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Marca</th>
                                    <th class="text-center">Modelo</th>
                                    <th class="text-center">Equipo</th>
                                    <th class="text-center">Número de Serie</th>
                                </tr>
                            </thead>
                            <tbody id="detalle_equipos">
                                <!-- Los equipos se agregarán dinámicamente -->
                            </tbody>
                        </table>
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
            <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                <a href="garantia/editar-certificado" class="btn border-rojo me-2">
                    <i class="fa fa-edit"></i> Editar Certificado de Garantía
                </a>
                <a href="garantia/manual" class="btn bg-rojo text-white">
                    <i class="fa fa-plus"></i> Añadir Garantía
                </a>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla_garantia"
                            class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fa fa-hashtag me-1"></i> Número</th>
                                    <th>Cliente</th>
                                    <th>Series</th>
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
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            ajax: {
                url: _URL + "/ajs/garantia/render",
                method: "POST",
                dataSrc: '',
            },
            language: {
                url: "serverSide/Spanish.json",
            },
            columns: [
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row, meta) {
                        if (row.numero) {
                            return row.numero;
                        }
                        const numeroFormateado = String(row.id_garantia).padStart(2, '0');
                        return `GR-${numeroFormateado}`;
                    }
                },
                { data: "cliente_ruc_dni", class: "text-center" },
                { 
                    data: null, 
                    class: "text-center",
                    render: function(data, type, row) {
                        let seriesCount = row.total_series || 1;
                        
                        if (!row.total_series && row.numeros_serie) {
                            try {
                                const series = row.numeros_serie.split(',').map(s => s.trim()).filter(s => s.length > 0);
                                seriesCount = series.length;
                            } catch (e) {
                                console.error("Error al contar series:", e);
                            }
                        }
                        
                        if (row.series_ids) {
                            try {
                                const seriesData = JSON.parse(row.series_ids);
                                if (Array.isArray(seriesData) && seriesData.length > 0) {
                                    seriesCount = seriesData.length;
                                }
                            } catch (e) {
                                console.error("Error al parsear series_ids:", e);
                            }
                        }
                        
                        const seriesTooltip = row.numeros_serie || 'Series no disponibles';
                        return `<span class="badge bg-info" title="${seriesTooltip}" data-bs-toggle="tooltip">${seriesCount} serie${seriesCount > 1 ? 's' : ''}</span>`;
                    }
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return row.guia_remision || '-';
                    }
                },
                { data: "fecha_inicio", class: "text-center" },
                { data: "fecha_caducidad", class: "text-center" },
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

        // Manejador mejorado para el modal de detalles con tabla
        $(document).on('click', '.btnDetalle', function () {
            const id = $(this).data('id');
            
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
                    Swal.close();
                    
                    try {
                        const data = JSON.parse(response);
                        if (data && data.length > 0) {
                            const garantia = data[0];
                            
                            // Llenar la información básica
                            $('#detalle_cliente').text(garantia.cliente_ruc_dni || '-');
                            $('#detalle_guia').text(garantia.guia_remision || '-');
                            $('#detalle_fecha_inicio').text(garantia.fecha_inicio || '-');
                            $('#detalle_fecha_caducidad').text(garantia.fecha_caducidad || '-');

                            // Limpiar la tabla de equipos
                            $('#detalle_equipos').empty();
                            
                            // Llenar la tabla con los equipos
                            data.forEach((item, index) => {
                                const marcaNombre = item.marca_nombre || item.marca || '-';
                                const modeloNombre = item.modelo_nombre || item.modelo || '-';
                                const equipoNombre = item.equipo_nombre || item.equipo || '-';
                                const numeroSerie = item.numero_serie || '-';
                                
                                const fila = `
                                    <tr>
                                        <td class="text-center fw-bold">${index + 1}</td>
                                        <td class="text-center">${marcaNombre}</td>
                                        <td class="text-center">${modeloNombre}</td>
                                        <td class="text-center">${equipoNombre}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">${numeroSerie}</span>
                                        </td>
                                    </tr>
                                `;
                                
                                $('#detalle_equipos').append(fila);
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
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información de la garantía'
                    });
                }
            });
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
            app.garantia.cliente_nombre = garantiaData.cliente_nombre;

            if (garantiaData.equipos && garantiaData.equipos.length > 0) {
                const primerEquipo = garantiaData.equipos[0];
                app.garantia.num_serie = primerEquipo.numero_serie;
                app.garantia.marc = primerEquipo.marca;
                app.garantia.model = primerEquipo.modelo;
            }

            sessionStorage.removeItem('garantia_data');
        }
    });
</script>

<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>