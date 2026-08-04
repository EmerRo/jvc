<!-- resources\views\fragment-views\cliente\compras.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">
<style>
    #datatable td:nth-child(5) {
        max-width: 180px;
        white-space: normal !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }
</style>

<div class="page-title-box">
    <div class="row align-items-center">

        <div class="clearfix">
            <h6 class="page-title text-center">ORDEN DE COMPRA</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Compras</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Orden de compra</a></li>

            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">



                <div class="card-title-desc text-end">
                    <a href="/compras/add" class="btn bg-rojo text-white button-link">
                        <i class="fa fa-plus "></i> Agregar Compra
                    </a>
                    <a target="_blank" href="/reporte/compras" class="btn bg-white text-rojo "
                        style="border-radius: 10px; padding: 8px 16px; font-weight: 500; border: 1px solid #CA3438; margin-left: 8px; transition: all 0.3s ease;">
                        <i class="fa fa-file me-1"></i> Exportar Reporte
                    </a>

                </div>


                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead class="table-light">
                            <tr>
                                <th style="text-align: center;">Documento</th>
                                <th style="text-align: center;">Comprobante</th>
                                <th style="text-align: center;">F. Emision</th>
                                <th style="text-align: center;">F. Vencimiento</th>
                                <th style="text-align: center;">Proveedor</th>
                                <th style="text-align: center;">Usuario</th>
                                <th style="text-align: center;">Detalles</th>
                                <th style="text-align: center;">Reporte</th>
                                <th style="text-align: center;">Estado</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>

                    </table>
                </div>

                <div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 50%;" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">Agregar</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="productos-tab" data-bs-toggle="tab"
                                            data-bs-target="#productos" type="button" role="tab"
                                            aria-controls="productos" aria-selected="true">Productos</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pagos-tab" data-bs-toggle="tab"
                                            data-bs-target="#pagos" type="button" role="tab" aria-controls="pagos"
                                            aria-selected="false">Pagos</button>
                                    </li>
                                </ul>
                                <div class="tab-content pt-3" id="myTabContent">
                                    <div class="tab-pane fade show active" id="productos" role="tabpanel"
                                        aria-labelledby="productos-tab">
                                        <table id="datatableProductoDetalle"
                                            class="table table-bordered dt-responsive nowrap text-center table-sm"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="text-align: center;">Código</th>
                                                    <th style="text-align: center;">Producto</th>
                                                    <th style="text-align: center;">Cantidad</th>
                                                    <th style="text-align: center;">Precio</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab">
                                        <div id="infoPagos">
                                            <div class="alert alert-info mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div><strong>Tipo de pago:</strong> <span id="tipoPagoText"></span></div>
                                                <a href="/pagos" id="linkPagarCuotas" class="btn btn-sm btn-primary"
                                                    style="display: none;">
                                                    <i class="fa fa-money"></i> Pagar cuotas aquí
                                                </a>
                                            </div>
                                            <table id="datatablePagosDetalle"
                                                class="table table-bordered dt-responsive nowrap text-center table-sm"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="text-align: center;">Fecha</th>
                                                        <th style="text-align: center;">Monto</th>
                                                        <th style="text-align: center;">Estado</th>
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

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {





        datatable = $("#datatable").DataTable({
            order: [[0, "desc"]],
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/prodcutos/compras/render",
                method: "POST",
                dataSrc: "",
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columns: [
                // mostrar serie y numero de compra con concatenación serie + '-' + numero
                {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        return row.serie + '-' + row.numero;
                    },
                },
                
                
            {
                    data: null,
                    class: "text-center",
                    render: function (data, type, row) {
                        if (row.serie_proveedor || row.numero_proveedor) {
                            return (row.serie_proveedor || '') + (row.serie_proveedor && row.numero_proveedor ? '-' : '') + (row.numero_proveedor || '');
                        }
                        return '-';
                    },
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
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return (row.ruc ? row.ruc + ' - ' : '') + (row.razon_social || '');
                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    // Depuración para ver cada fila
                    console.log("Datos de fila:", row);
                    // Mostrar el nombre y apellido del usuario si existen
                    return row.nombres ? row.nombres + (row.apellidos ? ' ' + row.apellidos : '') : 'No registrado';


                },
            },

            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return `<div class="text-center">
              <div class="btn-group"><button  data-id="${Number(
                        row.id_compra
                    )}" data-documento="${row.serie}-${row.numero}" class="btn  btn-sm btn-success btnDetalle"><i class="fa fa-eye"></i> </button></div></div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return `<div class="text-center">
              <div class="btn-group"><a target="_blank" class="btn btn-sm btn-info" href="${_URL}/reporte/compras/pdf/${row.id_compra}" ><i class="fa fa-file"></i> </a></div></div>`;
                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    // Columna Estado con badges
                    if (row.estado === '1') {
                        return `<span class="badge bg-success">Normal</span>`;
                    } else if (row.estado === '3') {
                        return `<span class="badge bg-warning text-dark">Devuelta</span>`;
                    } else {
                        return `<span class="badge bg-danger">Anulada</span>`;
                    }
                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    // Columna Acciones con iconos
                    let iconos = '';

                    // Icono de editar (siempre visible)
                    iconos += `<button data-id="${row.id_compra}"
                                      class="btn btn-sm btn-warning btnEditar me-1"
                                      title="Editar"
                                      data-bs-toggle="tooltip">
                                 <i class="fa fa-edit"></i>
                               </button>`;

                    // Icono de anular (solo si está activa)
                    if (row.estado === '1') {
                        iconos += `<button data-id="${row.id_compra}"
                                          class="btn btn-sm btn-danger btnAnular me-1"
                                          title="Anular"
                                          data-bs-toggle="tooltip">
                                     <i class="fa fa-trash"></i>
                                   </button>`;
                        iconos += `<button data-id="${row.id_compra}"
                                          class="btn btn-sm btn-dark btnDevolver"
                                          title="Devolver"
                                          data-bs-toggle="tooltip">
                                     <i class="fa fa-undo"></i>
                                   </button>`;
                    }
                    
                    if (row.estado === '3') {
                        iconos += `<span class="text-warning" title="${row.devolucion_observaciones || 'Sin observaciones'}" data-bs-toggle="tooltip">
                                     <i class="fa fa-info-circle"></i> Devuelto
                                   </span>`;
                    }

                    return `<div class="text-center">${iconos}</div>`;
                },
            },
            ],
        });

        // Inicializar tooltips después de cargar la tabla
        datatable.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $("#datatable").on("click", ".btnDetalle ", function (event) {
            $("#loader-menor").show();
            var id = $(this).data("id");
            var documento = $(this).data("documento");
            $("#modalDetalle").modal("show");
            $("#modalDetalle").find(".modal-title").text("Detalle compra " + documento);

            // Limpiar contenido previo
            if ($.fn.DataTable.isDataTable("#datatablePagosDetalle")) {
                $("#datatablePagosDetalle").DataTable().destroy();
            }
            $("#datatableProductoDetalle tbody").html('');
            $("#datatablePagosDetalle").html('<thead class="table-light"><tr><th style="text-align: center;">Fecha</th><th style="text-align: center;">Monto</th><th style="text-align: center;">Estado</th></tr></thead>');
            $("#tipoPagoText").text('');
            $("#linkPagarCuotas").hide();

            // Cargar productos
            $.ajax({
                type: 'POST',
                url: _URL + '/ajas/compra/detalle',
                data: { id: id },
                success: function (resp) {
                    try {
                        var res = JSON.parse(resp);
                        if (!res.res) {
                            $("#loader-menor").hide();
                            if (res.msg) {
                                $("#datatableProductoDetalle").html('<tr><td colspan="4" class="text-center text-danger">' + res.msg + '</td></tr>');
                            }
                            return;
                        }
                        var data = res.data;
                        // Agregar información del usuario en el modal
                        var usuarioInfo = '';
                        if (data.length > 0 && data[0].nombres && data[0].apellidos) {
                            usuarioInfo = '<div class="alert alert-info mb-3"><strong>Registrado por:</strong> ' + data[0].nombres + ' ' + data[0].apellidos + '</div>';
                            $("#infoPagos").prepend(usuarioInfo);
                        }
                        datatableProductoDetalle = $("#datatableProductoDetalle").DataTable({
                            paging: true,
                            bFilter: true,
                            ordering: true,
                            searching: true,
                            destroy: true,
                            language: { url: "ServerSide/Spanish.json" },
                            data: data,
                            columns: [
                                { data: "codigo", class: "text-center" },
                                { data: "nombre", class: "text-center" },
                                { data: "cantidad", class: "text-center" },
                                { 
                                    data: "precio", 
                                    class: "text-center",
                                    render: function(data, type, row) {
                                        return parseFloat(data).toFixed(2);
                                    }
                                }
                            ]
                        });
                    } catch (e) {
                        $("#loader-menor").hide();
                        $("#datatableProductoDetalle").html('<tr><td colspan="4" class="text-center text-danger">Error al procesar respuesta del servidor</td></tr>');
                        return;
                    }

                    // Verificar si hay pagos a crédito
                    $.ajax({
                        type: 'POST',
                        url: _URL + '/ajs/compra/pagos',
                        data: { id: id },
                        success: function (respPagos) {
                            $("#loader-menor").hide();
                            try {
                                let dataPagos = JSON.parse(respPagos);

                                if (dataPagos.tipo_pago == 2) {
                                    $("#tipoPagoText").text("Crédito");
                                    $("#linkPagarCuotas").show();

                                    datatablePagosDetalle = $("#datatablePagosDetalle").DataTable({
                                        paging: true,
                                        bFilter: true,
                                        ordering: true,
                                        searching: true,
                                        destroy: true,
                                        language: { url: "ServerSide/Spanish.json" },
                                        data: dataPagos.pagos,
                                        columns: [
                                            { data: "fecha", class: "text-center" },
                                            {
                                                data: "monto",
                                                class: "text-center",
                                                render: function(data, type, row) {
                                                    return parseFloat(data).toFixed(2);
                                                }
                                            },
                                            {
                                                data: "estado",
                                                class: "text-center",
                                                render: function (data) {
                                                    if (data == 0) {
                                                        return '<span class="badge bg-warning">Pendiente</span>';
                                                    } else {
                                                        return '<span class="badge bg-success">Pagado</span>';
                                                    }
                                                }
                                            }
                                        ]
                                    });
                                } else {
                                    $("#tipoPagoText").text("Contado");
                                    $("#datatablePagosDetalle").html('<thead class="table-light"><tr><th class="text-center">Fecha</th><th class="text-center">Monto</th><th class="text-center">Estado</th></tr></thead><tbody><tr><td colspan="3" class="text-center">Esta compra fue pagada al contado</td></tr></tbody>');
                                }
                            } catch (e) {
                                $("#datatablePagosDetalle").html('<thead class="table-light"><tr><th class="text-center">Fecha</th><th class="text-center">Monto</th><th class="text-center">Estado</th></tr></thead><tbody><tr><td colspan="3" class="text-center text-danger">Error al cargar pagos</td></tr></tbody>');
                            }
                        },
                        error: function () {
                            $("#loader-menor").hide();
                            $("#datatablePagosDetalle").html('<thead class="table-light"><tr><th class="text-center">Fecha</th><th class="text-center">Monto</th><th class="text-center">Estado</th></tr></thead><tbody><tr><td colspan="3" class="text-center text-danger">Error de conexión al cargar pagos</td></tr></tbody>');
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#loader-menor").hide();
                    var msg = 'Error de conexión al cargar detalle';
                    if (jqXHR.responseText) {
                        msg += ': ' + jqXHR.responseText.substring(0, 200);
                    }
                    $("#datatableProductoDetalle").html('<tr><td colspan="4" class="text-center text-danger">' + msg + '</td></tr>');
                }
            });
        });

        // Event listener para Editar
        $("#datatable").on("click", ".btnEditar", function (event) {
            var id = $(this).data("id");
            // Redirigir a la página de edición
            window.location.href = _URL + '/compras/editar/' + id;
        });

        // Event listener para Devolver
        $("#datatable").on("click", ".btnDevolver", function (event) {
            var id = $(this).data("id");
            $("#loader-menor").show();
            
            $.ajax({
                type: 'POST',
                url: _URL + '/ajs/compra/detalle-devolucion',
                data: { id: id },
                success: function (resp) {
                    $("#loader-menor").hide();
                    let data = JSON.parse(resp);
                    if (data.res) {
                        $('#devolucionCompraId').val(id);
                        $('#devolucionCompraTitulo').text('Devolver: ' + data.compra.serie + '-' + data.compra.numero + (data.compra.razon_social ? ' - ' + data.compra.razon_social : ''));
                        
                        let html = '';
                        if (data.detalle.productos.length > 0) {
                            data.detalle.productos.forEach(function(p) {
                                let disponible = parseFloat(p.cantidad) - parseFloat(p.cantidad_devuelta || '0');
                                if (disponible <= 0) return;
                                let nombre = (p.codigo ? p.codigo + ' - ' : '') + (p.descripcion || 'Producto #' + p.id_producto);
                                html += `<tr>
                                    <td>${nombre}</td>
                                    <td class="text-center">${p.cantidad}</td>
                                    <td class="text-center">${p.cantidad_devuelta || '0'}</td>
                                    <td class="text-center"><input type="number" class="form-control form-control-sm devolver-cantidad" 
                                        data-tipo="producto" data-id="${p.id_producto}" 
                                        min="0" max="${disponible}" value="0" style="width:80px;display:inline;"></td>
                                </tr>`;
                            });
                        }
                        if (data.detalle.repuestos.length > 0) {
                            html += '<tr class="table-secondary"><td colspan="4"><strong>Repuestos</strong></td></tr>';
                            data.detalle.repuestos.forEach(function(r) {
                                let disponible = parseFloat(r.cantidad) - parseFloat(r.cantidad_devuelta || '0');
                                if (disponible <= 0) return;
                                let nombre = r.descripcion || 'Repuesto #' + r.id_repuesto;
                                html += `<tr>
                                    <td>${nombre}</td>
                                    <td class="text-center">${r.cantidad}</td>
                                    <td class="text-center">${r.cantidad_devuelta || '0'}</td>
                                    <td class="text-center"><input type="number" class="form-control form-control-sm devolver-cantidad" 
                                        data-tipo="repuesto" data-id="${r.id_repuesto}" 
                                        min="0" max="${disponible}" value="0" style="width:80px;display:inline;"></td>
                                </tr>`;
                            });
                        }
                        
                        $('#devolucionItemsBody').html(html);
                        $('#devolucionObservaciones').val(data.compra.devolucion_observaciones || '');
                        $('#devolverCompraModal').modal('show');
                    } else {
                        Swal.fire('Error', data.msg || 'No se pudo cargar el detalle', 'error');
                    }
                },
                error: function() {
                    $("#loader-menor").hide();
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        });
        
        $("#btnConfirmarDevolucion").on("click", function() {
            var id = $('#devolucionCompraId').val();
            var observaciones = $('#devolucionObservaciones').val().trim();
            var items = [];
            
            $('.devolver-cantidad').each(function() {
                var cant = parseFloat($(this).val()) || 0;
                if (cant > 0) {
                    items.push({
                        tipo: $(this).data('tipo'),
                        id: $(this).data('id'),
                        cantidad: cant
                    });
                }
            });
            
            if (items.length === 0) {
                Swal.fire('Atención', 'Debe indicar al menos una cantidad para devolver', 'warning');
                return;
            }
            
            Swal.fire({
                title: '¿Confirmar devolución?',
                text: 'Se revertirá el stock de los productos seleccionados.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, devolver',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#loader-menor").show();
                    $.ajax({
                        type: 'POST',
                        url: _URL + '/ajs/compra/devolver',
                        data: {
                            id: id,
                            observaciones: observaciones,
                            items: JSON.stringify(items)
                        },
                        success: function (resp) {
                            $("#loader-menor").hide();
                            let data = JSON.parse(resp);
                            if (data.res) {
                                $('#devolverCompraModal').modal('hide');
                                Swal.fire('Devuelta', data.msg, 'success').then(() => {
                                    datatable.ajax.reload();
                                });
                            } else {
                                Swal.fire('Error', data.msg || 'No se pudo devolver la compra.', 'error');
                            }
                        },
                        error: function() {
                            $("#loader-menor").hide();
                            Swal.fire('Error', 'Error de conexión', 'error');
                        }
                    });
                }
            });
        });
        $("#datatable").on("click", ".btnAnular", function (event) {
            var id = $(this).data("id");

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas anular esta orden de compra?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#loader-menor").show();
                    $.ajax({
                        type: 'POST',
                        url: _URL + '/ajs/compra/anular',
                        data: { id: id },
                        success: function (resp) {
                            $("#loader-menor").hide();
                            let data = JSON.parse(resp);
                            if (data.res) {
                                Swal.fire(
                                    'Anulada',
                                    'La orden de compra ha sido anulada correctamente.',
                                    'success'
                                ).then(() => {
                                    datatable.ajax.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.msg || 'No se pudo anular la orden de compra.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            $("#loader-menor").hide();
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al procesar la solicitud.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    })
</script>

<!-- Modal de Devolución -->
<div class="modal fade" id="devolverCompraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="devolucionCompraTitulo">Devolver Compra</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="devolucionCompraId">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap text-center table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Productos</th>
                                <th class="text-center">Cant. Comprada</th>
                                <th class="text-center">Ya Devuelto</th>
                                <th class="text-center">Devolver ahora</th>
                            </tr>
                        </thead>
                        <tbody id="devolucionItemsBody"></tbody>
                    </table>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" id="devolucionObservaciones" rows="2" 
                        placeholder="Motivo de la devolución..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn bg-rojo text-white" id="btnConfirmarDevolucion">
                    <i class="fa fa-undo me-1"></i>Confirmar Devolución
                </button>
            </div>
        </div>
    </div>
</div>