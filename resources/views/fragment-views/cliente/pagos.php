<!-- resources\views\fragment-views\cliente\pagos.php -->
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
                            
                                <th style="text-align: center;">Código</th>
                                <th style="text-align: center;">F.Emisión</th>
                                <th style="text-align: center;">F.Vencimiento</th>
                                <th style="text-align: center;">Empresa</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">Pagado</th>
                                <th style="text-align: center;">Saldo</th>
                                <th style="text-align: center;">Cuotas</th>
                                <th style="text-align: center;">Situación</th>
                                <th style="text-align: center;">Días V</th>
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
    // Verificar si las variables globales ya existen
    if (typeof window.datatable === 'undefined') {
        window.datatable = null;
    }
    if (typeof window.datatableDiasCompras === 'undefined') {
        window.datatableDiasCompras = null;
    }
    if (typeof window.currentCompraId === 'undefined') {
        window.currentCompraId = null;
    }

    function daysdifference(firstDate, secondDate) {
        var startDay = new Date(firstDate);
        var endDay = new Date(secondDate);
        var millisBetween = startDay.getTime() - endDay.getTime();
        var days = millisBetween / (1000 * 3600 * 24);
        return Math.round(Math.abs(days));
    }

    // Función para obtener información de cuotas
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

    // Función para recargar datos de la tabla principal
    function recargarTablaPrincipal() {
        if (window.datatable) {
            window.datatable.ajax.reload(null, false);
        }
    }

    // Función para recargar datos del modal
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
                console.error('Response:', xhr.responseText);
            }
        },
        language: {
            url: "ServerSide/Spanish.json",
        },
        columns: [
            // {
            //     data: null,
            //     class: "text-center",
            //     render: function(data, type, row, meta) {
            //         var info = $('#datatable').DataTable().page.info();
            //         return info.start + meta.row + 1;
            //     },
            // },
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
                    const total = parseFloat(row.total || 0);
                    return `<div class="text-center">
                        <div class="btn-group">S/ ${total.toFixed(2)}</div>
                    </div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    const pagado = parseFloat(row.pagado || 0);
                    return `<div class="text-center">
                        <div class="btn-group">S/ ${pagado.toFixed(2)}</div>
                    </div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    const saldo = parseFloat(row.saldo || row.total || 0);
                    return `<div class="text-center">
                        <div class="btn-group">S/ ${saldo.toFixed(2)}</div>
                    </div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `<div class="text-center" id="cuotas-${row.id_compra}">
                        <div class="btn-group">
                            <span class="badge bg-secondary">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    const total = parseFloat(row.total || 0);
                    const pagado = parseFloat(row.pagado || 0);

                    let vencimiento = row.fecha_vencimiento;
                    const [year, month, day] = vencimiento.split('-');
                    const vencimientoFecha = [month, day, year].join('/');
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;

                    if (total.toFixed(2) == pagado.toFixed(2)) {
                        return `<div class="text-center">
                            <div class="btn-group"><span class="badge bg-success">Pagado</span></div>
                        </div>`;
                    } else if (total.toFixed(2) > pagado.toFixed(2) && today > vencimientoFecha) {
                        return `<div class="text-center">
                            <div class="btn-group"><span class="badge bg-danger">Vencido</span></div>
                        </div>`;
                    } else if (total.toFixed(2) > pagado.toFixed(2) && today <= vencimientoFecha) {
                        return `<div class="text-center">
                            <div class="btn-group"><span class="badge bg-info">Vigente</span></div>
                        </div>`;
                    }
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    let vencimiento = row.fecha_vencimiento;
                    const [year, month, day] = vencimiento.split('-');
                    const vencimientoFecha = [month, day, year].join('/');
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;
                    const dateToday = new Date(today);
                    const dateVencimiento = new Date(vencimientoFecha);
                    const diffTime = Math.abs(dateToday - dateVencimiento);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    if (today > vencimientoFecha) {
                        return `<div class="text-center">
                            <div class="btn-group"><span class="badge bg-danger">${diffDays}</span></div>
                        </div>`;
                    } else {
                        return `<div class="text-center">
                            <div class="btn-group"><span class="badge bg-success">0</span></div>
                        </div>`;
                    }
                },
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return `<div class="text-center">
                        <div class="btn-group">
                            <button data-id="${row.id_compra}" class="btn btn-success btnDetalles btn-sm">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>`;
                },
            },
        ],
        drawCallback: function() {
            // Cargar información de cuotas después de dibujar la tabla
            $('#datatable tbody tr').each(function() {
                const row = window.datatable.row(this).data();
                if (row && row.id_compra) {
                    obtenerInfoCuotas(row.id_compra, function(info) {
                        $(`#cuotas-${row.id_compra}`).html(`
                            <div class="btn-group">
                                <span class="badge bg-primary">${info.pagadas}/${info.total}</span>
                            </div>
                        `);
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

        // Limpiar DataTable de detalles si existe
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
                                    let vencimiento = row.fecha;
                                    const [year, month, day] = vencimiento.split('-');
                                    const vencimientoFecha = [month, day, year].join('/');
                                    var today = new Date();
                                    var dd = String(today.getDate()).padStart(2, '0');
                                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                                    var yyyy = today.getFullYear();
                                    today = mm + '/' + dd + '/' + yyyy;

                                    if ((today > vencimientoFecha) && row.estado == '0') {
                                        return `<div class="text-center">
                                            <div class="btn-group"><span class="badge bg-danger">Vencido</span></div>
                                        </div>`;
                                    } else if ((today <= vencimientoFecha) && row.estado == '0') {
                                        return `<div class="text-center">
                                            <div class="btn-group"><span class="badge bg-success">Vigente</span></div>
                                        </div>`;
                                    } else if (row.estado == '1') {
                                        return `<div class="text-center">
                                            <div class="btn-group"><span class="badge bg-info">Pagado</span></div>
                                        </div>`;
                                    }
                                },
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    if (row.estado == '0') {
                                        return `<div class="text-center">
                                            <div class="btn-group">
                                                <button data-id="${row.dias_compra_id}" class="btn btn-success btnPagar btn-sm">
                                                    <i class="fas fa-money-bill"></i>
                                                </button>
                                            </div>
                                        </div>`;
                                    } else {
                                        return `<div class="text-center">
                                            <div class="btn-group">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Pagado
                                                </span>
                                            </div>
                                        </div>`;
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
            title: '¿Desea pagar la cuota N° ' + id + '?',
            text: 'Esta acción marcará la cuota como pagada',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-check"></i> Sí, Pagar',
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
                                // Mostrar mensaje de éxito
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'La cuota ha sido pagada correctamente',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Actualizar tabla del modal
                                if (window.currentCompraId) {
                                    recargarTablaModal(window.currentCompraId);
                                }

                                // Actualizar tabla principal después de un breve delay
                                setTimeout(() => {
                                    recargarTablaPrincipal();
                                }, 500);
                                
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo procesar el pago'
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            // Asumir éxito si no se puede parsear pero no hay error HTTP
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'La cuota ha sido pagada correctamente',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (window.currentCompraId) {
                                recargarTablaModal(window.currentCompraId);
                            }
                            setTimeout(() => {
                                recargarTablaPrincipal();
                            }, 500);
                        }
                    },
                    error: function(xhr, error, thrown) {
                        $("#loader-menor").hide();
                        console.error('Error al pagar cuota:', error);
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