<!-- resources\views\fragment-views\cliente\cobranzas.php -->
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title text-center">COBRANZAS</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item active" aria-current="page" style="font-weight: 500; color: #CA3438;">
                    Cobranzas</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <h4 class="card-title">Venta de Producto</h4>
                <div class="card-title-desc"></div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Codigo</th>
                                <th>F. Emision</th>
                                <th>F. Vencimiento</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th>Situacion</th>
                                <th>Dias V.</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Modal de Detalles de Cuotas -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles de Cuotas</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="" class="col-xs-12 col-sm-12 col-md-12 no-padding">
                                    <table id="datatableDiasCompras"
                                        class="table table-bordered dt-responsive nowrap text-center table-sm"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="text-align: center;">Item</th>
                                                <th style="text-align: center;">Monto</th>
                                                <th style="text-align: center;">F. Vencimiento</th>
                                                <th style="text-align: center;">Estado</th>
                                                <th style="text-align: center;">Pagar</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger cerrarpagos">Cerrar</button>
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
        const datatable = $("#datatable").DataTable({
            order: [[2, "asc"]],
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/cuentas/cobrar/render",
                method: "POST",
                dataSrc: "",
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
                    },
                },
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
                    render: function (data, type, row) {
                        return `<div class="text-center">
                                    <div class="btn-group">S/ ${parseFloat(row.total || 0).toFixed(2)}</div>
                                </div>`;
                    },
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return `<div class="text-center">
                                    <div class="btn-group">S/ ${parseFloat(row.pagado || 0).toFixed(2)}</div>
                                </div>`;
                    },
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return `<div class="text-center">
                                    <div class="btn-group">S/ ${parseFloat(row.saldo || 0).toFixed(2)}</div>
                                </div>`;
                    },
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        let vencimiento = row.fecha_vencimiento;
                        const [year, month, day] = vencimiento.split('-');
                        const vencimientoFecha = [month, day, year].join('/');
                        var today = new Date();
                        var dd = String(today.getDate()).padStart(2, '0');
                        var mm = String(today.getMonth() + 1).padStart(2, '0');
                        var yyyy = today.getFullYear();
                        today = mm + '/' + dd + '/' + yyyy;
                        
                        const totalPagado = parseFloat(row.pagado || 0);
                        const totalVenta = parseFloat(row.total || 0);
                        
                        if (totalPagado >= totalVenta) {
                            return `<div class="text-center">
                                        <div class="btn-group"><span class="badge bg-success">Pagado</span></div>
                                    </div>`;
                        } else if (today > vencimientoFecha) {
                            return `<div class="text-center">
                                        <div class="btn-group"><span class="badge bg-danger">Vencido</span></div>
                                    </div>`;
                        } else {
                            return `<div class="text-center">
                                        <div class="btn-group"><span class="badge bg-warning">Vigente</span></div>
                                    </div>`;
                        }
                    },
                },
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
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
                    render: function (data, type, row) {
                        return `<div class="text-center">
                                    <div class="btn-group">
                                        <button data-id="${Number(row.id_venta)}" class="btn btn-success btnDetalles btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>`;
                    },
                },
            ],
        });

        // Evento para mostrar detalles de cuotas
        $("#datatable").on("click", ".btnDetalles", function (event) {
            $("#loader-menor").show();
            var id = $(this).data("id");
            
            // Guardar el ID de la venta en el modal para uso posterior
            $("#exampleModal").data('venta-id', id);
            
            $("#exampleModal").modal("show");
            $("#exampleModal").find(".modal-title").text("Detalles de Cuotas - Venta N° " + id);
            
            // Cargar las cuotas
            recargarTablaCuotas(id);
        });
        
        // Función para recargar la tabla de cuotas
        function recargarTablaCuotas(ventaId) {
            $.ajax({
                url: _URL + "/ajs/getAllCuotas/byIdVenta",
                data: { id: ventaId },
                type: "post",
                success: function (resp) {
                    $("#loader-menor").hide();
                    resp = JSON.parse(resp);
                    console.log("Cuotas recibidas:", resp);

                    // Destruir la tabla existente si existe
                    if ($.fn.DataTable.isDataTable('#datatableDiasCompras')) {
                        $('#datatableDiasCompras').DataTable().destroy();
                    }

                    $("#datatableDiasCompras").DataTable({
                        paging: true,
                        bFilter: true,
                        ordering: true,
                        searching: true,
                        destroy: true,
                        data: resp,
                        language: {
                            url: "ServerSide/Spanish.json",
                        },
                        columns: [
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row, meta) {
                                    return meta.row + 1;
                                },
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row) {
                                    return `S/ ${parseFloat(row.monto || 0).toFixed(2)}`;
                                },
                            },
                            {
                                data: "fecha",
                                class: "text-center",
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row) {
                                    let vencimiento = row.fecha;
                                    const [year, month, day] = vencimiento.split('-');
                                    const vencimientoFecha = [month, day, year].join('/');
                                    var today = new Date();
                                    var dd = String(today.getDate()).padStart(2, '0');
                                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                                    var yyyy = today.getFullYear();
                                    today = mm + '/' + dd + '/' + yyyy;
                                    
                                    if (row.estado == '1') {
                                        return `<div class="text-center">
                                                    <div class="btn-group"><span class="badge bg-success">Pagado</span></div>
                                                </div>`;
                                    } else if (today > vencimientoFecha) {
                                        return `<div class="text-center">
                                                    <div class="btn-group"><span class="badge bg-danger">Vencido</span></div>
                                                </div>`;
                                    } else {
                                        return `<div class="text-center">
                                                    <div class="btn-group"><span class="badge bg-warning">Vigente</span></div>
                                                </div>`;
                                    }
                                },
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row) {
                                    if (row.estado == '0') {
                                        return `<div class="text-center">
                                                    <div class="btn-group">
                                                        <button data-id="${Number(row.dias_venta_id)}" class="btn btn-success btnPagar btn-sm">
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
                },
                error: function(xhr, status, error) {
                    $("#loader-menor").hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar las cuotas'
                    });
                }
            });
        }

        // Evento para pagar cuota
        $(document).on("click", ".btnPagar", function (event) {
            var id = $(this).data("id");
            var $botonPagar = $(this); // Guardar referencia al botón
            
            Swal.fire({
                title: '¿Confirmar pago de cuota?',
                text: 'Esta acción marcará la cuota como pagada',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, pagar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: _URL + '/ajs/pagar/cuota/cobranza',
                        data: { id: id },
                        success: function (resp) {
                            try {
                                let data = JSON.parse(resp);
                                console.log("Respuesta del pago:", data);
                                
                                if (data) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: 'La cuota ha sido pagada correctamente',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    
                                    // Recargar la tabla de cuotas del modal obteniendo los datos nuevamente
                                    const ventaId = $("#exampleModal").data('venta-id');
                                    if (ventaId) {
                                        recargarTablaCuotas(ventaId);
                                    }
                                    
                                    // Recargar la tabla principal después de un breve delay
                                    setTimeout(() => {
                                        datatable.ajax.reload();
                                    }, 500);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se pudo procesar el pago'
                                    });
                                }
                            } catch (e) {
                                console.error("Error al procesar respuesta:", e);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al procesar la respuesta del servidor'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error en la petición:", error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error de conexión con el servidor'
                            });
                        }
                    });
                }
            });
        });

        // Cerrar modal y recargar tabla principal
        $('.cerrarpagos').click(function () {
            $('#exampleModal').modal('hide');
            datatable.ajax.reload();
        });
    });
</script>