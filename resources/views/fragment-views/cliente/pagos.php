<!-- resources\views\fragment-views\cliente\pagos.php -->
<style>
    .contador-dias {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 60px;
        height: 32px;
        padding: 0 8px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        border: none;
        line-height: 1;
        white-space: nowrap;
    }
    .contador-dias.vencido { background-color: #dc3545; color: white; animation: pulse-vencido 2s infinite; }
    .contador-dias.urgente { background-color: #ffc107; color: #000; }
    .contador-dias.normal { background-color: #28a745; color: white; }
    .contador-dias.pagado-dias { background-color: #28a745; color: white; }
    .contador-dias.sin-fecha { background-color: #6c757d; color: white; }
    @keyframes pulse-vencido { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }

    .badge-situacion {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-situacion.pagado { background-color: #28a745; color: white; }
    .badge-situacion.vigente { background-color: #17a2b8; color: white; }
    .badge-situacion.vencido { background-color: #dc3545; color: white; }
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title text-center">CUENTAS POR PAGAR</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);" style="color: #CA3438;">Cuentas Por Pagar</a></li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th style="text-align: center;">Codigo</th>
                                <th style="text-align: center;">F. Emision</th>
                                <th style="text-align: center;">F. Vencimiento</th>
                                <th style="text-align: center;">Empresa</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">Pagado</th>
                                <th style="text-align: center;">Saldo</th>
                                <th style="text-align: center;">Cuotas</th>
                                <th style="text-align: center;">Situacion</th>
                                <th style="text-align: center;">Dias V.</th>
                                <th style="text-align: center;">Detalles</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">Detalles de compra</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table id="datatableDiasCompras" class="table table-bordered dt-responsive nowrap text-center table-hover table-striped" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="text-align: center;">Item</th>
                                                <th style="text-align: center;">Monto</th>
                                                <th style="text-align: center;">F. Vencimiento</th>
                                                <th style="text-align: center;">Estado</th>
                                                <th style="text-align: center;">Pagar</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-danger text-white" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    if (typeof window.datatable === 'undefined') {
        window.datatable = null;
    }
    if (typeof window.datatableDiasCompras === 'undefined') {
        window.datatableDiasCompras = null;
    }
    if (typeof window.currentCompraId === 'undefined') {
        window.currentCompraId = null;
    }

    // Helper: calcular diferencia de dias usando Date objects (fix bug de comparacion de strings)
    function calcularDiasVencimiento(fechaVencimiento) {
        const [year, month, day] = fechaVencimiento.split('-');
        const dateVenc = new Date(year, month - 1, day);
        const dateHoy = new Date();
        dateHoy.setHours(0, 0, 0, 0);
        const diffTime = dateVenc - dateHoy;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }

    // Helper: generar badge de situacion
    function badgeSituacion(row) {
        const total = parseFloat(row.total || 0);
        const pagado = parseFloat(row.pagado || 0);
        const diasRestantes = calcularDiasVencimiento(row.fecha_vencimiento);

        if (pagado >= total) {
            return '<span class="badge-situacion pagado"><i class="fas fa-check-circle"></i> PAGADO</span>';
        } else if (diasRestantes < 0) {
            return '<span class="badge-situacion vencido"><i class="fas fa-exclamation-triangle"></i> VENCIDO</span>';
        } else {
            return '<span class="badge-situacion vigente"><i class="fas fa-clock"></i> VIGENTE</span>';
        }
    }

    // Helper: generar badge de dias vencimiento estilo gestion activos
    function badgeDiasV(row) {
        const total = parseFloat(row.total || 0);
        const pagado = parseFloat(row.pagado || 0);
        const diasRestantes = calcularDiasVencimiento(row.fecha_vencimiento);

        if (pagado >= total) {
            return '<span class="contador-dias pagado-dias"><i class="fas fa-check"></i> OK</span>';
        } else if (diasRestantes < 0) {
            return `<span class="contador-dias vencido"><i class="fas fa-arrow-up"></i> ${Math.abs(diasRestantes)}</span>`;
        } else if (diasRestantes <= 3) {
            return `<span class="contador-dias urgente"><i class="fas fa-exclamation"></i> ${diasRestantes}</span>`;
        } else {
            return `<span class="contador-dias normal">${diasRestantes}</span>`;
        }
    }

    // Funcion para obtener informacion de cuotas
    function obtenerInfoCuotas(idCompra, callback) {
        $.ajax({
            url: _URL + "/ajs/getAllCuotas/byIdCompra",
            data: { id: idCompra },
            type: "post",
            success: function(resp) {
                try {
                    const cuotas = JSON.parse(resp);
                    const totalCuotas = cuotas.length;
                    const cuotasPagadas = cuotas.filter(c => c.estado == '1').length;
                    callback({ total: totalCuotas, pagadas: cuotasPagadas });
                } catch (e) {
                    callback({ total: 0, pagadas: 0 });
                }
            },
            error: function() {
                callback({ total: 0, pagadas: 0 });
            }
        });
    }

    // Funcion para recargar datos del modal
    function recargarTablaModal(idCompra) {
        if (window.datatableDiasCompras) {
            $.ajax({
                url: _URL + "/ajs/getAllCuotas/byIdCompra",
                data: { id: idCompra },
                type: "post",
                success: function(resp) {
                    try {
                        const data = JSON.parse(resp);
                        window.datatableDiasCompras.clear().rows.add(data).draw();
                    } catch (e) {
                        console.error('Error al recargar modal:', e);
                    }
                }
            });
        }
    }

    // Limpiar DataTable existente si existe
    if (window.datatable && $.fn.DataTable.isDataTable('#datatable')) {
        window.datatable.destroy();
        $('#datatable').empty();
    }

    window.datatable = $("#datatable").DataTable({
        paging: true,
        bFilter: true,
        ordering: true,
        searching: true,
        destroy: true,
        ajax: {
            url: _URL + "/ajs/cuentas/ventas/render",
            method: "POST",
            dataSrc: "",
            error: function(xhr, error, thrown) {
                console.error('Error en AJAX:', error);
            }
        },
        language: {
            url: "ServerSide/Spanish.json",
        },
        columns: [
            {
                data: "factura",
                class: "text-center",
            },
            {
                data: "fecha_emision",
                class: "text-center",
            },
            {
                data: "fecha_vencimiento",
                class: "text-center",
            },
            {
                data: "cliente",
                class: "text-center",
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `S/ ${parseFloat(row.total || 0).toFixed(2)}`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `S/ ${parseFloat(row.pagado || 0).toFixed(2)}`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `S/ ${parseFloat(row.saldo || row.total || 0).toFixed(2)}`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `<div id="cuotas-${row.id_compra}">
                        <span class="badge bg-secondary"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return badgeSituacion(row);
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return badgeDiasV(row);
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `<button data-id="${row.id_compra}" class="btn btn-success btnDetalles btn-sm">
                        <i class="fa fa-eye"></i>
                    </button>`;
                },
            },
        ],
        drawCallback: function() {
            $('#datatable tbody tr').each(function() {
                const row = window.datatable.row(this).data();
                if (row && row.id_compra) {
                    obtenerInfoCuotas(row.id_compra, function(info) {
                        $(`#cuotas-${row.id_compra}`).html(
                            `<span class="badge bg-primary">${info.pagadas}/${info.total}</span>`
                        );
                    });
                }
            });
        }
    });

    // Manejar clic en detalles
    $("#datatable").on("click", ".btnDetalles", function(event) {
        $("#loader-menor").show();
        var id = $(this).data("id");
        window.currentCompraId = id;

        $("#exampleModal").modal("show");
        $("#exampleModal").find(".modal-title").text("Detalles compra N° " + id);

        if (window.datatableDiasCompras && $.fn.DataTable.isDataTable('#datatableDiasCompras')) {
            window.datatableDiasCompras.destroy();
            $('#datatableDiasCompras').empty();
        }

        $.ajax({
            url: _URL + "/ajs/getAllCuotas/byIdCompra",
            data: { id: id },
            type: "post",
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    const data = JSON.parse(resp);

                    if (!data || data.length === 0) {
                        console.warn('No hay datos de cuotas para mostrar');
                        return;
                    }

                    window.datatableDiasCompras = $("#datatableDiasCompras").DataTable({
                        paging: true,
                        bFilter: true,
                        ordering: true,
                        searching: true,
                        destroy: true,
                        data: data,
                        language: {
                            url: "ServerSide/Spanish.json",
                        },
                        columns: [
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row, meta) {
                                    return meta.row + 1;
                                },
                            },
                            {
                                data: "monto",
                                class: "text-center",
                                render: function(data, type, row) {
                                    return `S/ ${parseFloat(data || 0).toFixed(2)}`;
                                }
                            },
                            {
                                data: "fecha",
                                class: "text-center",
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    if (row.estado == '1') {
                                        return '<span class="badge-situacion pagado"><i class="fas fa-check-circle"></i> PAGADO</span>';
                                    }
                                    const [year, month, day] = row.fecha.split('-');
                                    const dateVenc = new Date(year, month - 1, day);
                                    const dateHoy = new Date();
                                    dateHoy.setHours(0, 0, 0, 0);
                                    if (dateHoy > dateVenc) {
                                        return '<span class="badge-situacion vencido"><i class="fas fa-exclamation-triangle"></i> VENCIDO</span>';
                                    }
                                    return '<span class="badge-situacion vigente"><i class="fas fa-clock"></i> VIGENTE</span>';
                                },
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    if (row.estado == '0') {
                                        return `<button data-id="${row.dias_compra_id}" class="btn btn-success btnPagar btn-sm">
                                            <i class="fas fa-money-bill"></i>
                                        </button>`;
                                    } else {
                                        return `<span class="badge bg-success">
                                            <i class="fas fa-check"></i> Pagado
                                        </span>`;
                                    }
                                },
                            },
                        ],
                    });
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            },
            error: function(xhr, error, thrown) {
                $("#loader-menor").hide();
                console.error('Error al obtener cuotas:', error);
            }
        });
    });

    // Manejar pago de cuotas
    $(document).on("click", ".btnPagar", function(event) {
        var id = $(this).data("id");

        Swal.fire({
            title: '¿Confirmar pago de cuota?',
            text: 'Esta accion marcara la cuota como pagada',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-check"></i> Si, Pagar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $("#loader-menor").show();

                $.ajax({
                    type: 'POST',
                    url: _URL + '/ajs/pagar/cuota/pago',
                    data: { id: id },
                    success: function(resp) {
                        $("#loader-menor").hide();

                        try {
                            let data = JSON.parse(resp);

                            if (data && (data === true || data.success || data.affected_rows > 0)) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Exito!',
                                    text: 'La cuota ha sido pagada correctamente',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                if (window.currentCompraId) {
                                    recargarTablaModal(window.currentCompraId);
                                }

                                setTimeout(() => {
                                    window.datatable.ajax.reload(null, false);
                                }, 500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo procesar el pago'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Exito!',
                                text: 'La cuota ha sido pagada correctamente',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (window.currentCompraId) {
                                recargarTablaModal(window.currentCompraId);
                            }
                            setTimeout(() => {
                                window.datatable.ajax.reload(null, false);
                            }, 500);
                        }
                    },
                    error: function(xhr, error, thrown) {
                        $("#loader-menor").hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo procesar el pago. Intente nuevamente.'
                        });
                    }
                });
            }
        });
    });
});
</script>
