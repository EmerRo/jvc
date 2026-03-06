<!-- resources\views\fragment-views\cliente\compras.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">

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
                                <th style="text-align: center;">F. Emision</th>
                                <th style="text-align: center;">F. Vencimiento</th>
                                <th style="text-align: center;" width="50%">Razon Social</th>
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
                                            class="table table-bordered dt-responsive text-center table-sm"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
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
                                            <div class="alert alert-info mb-3">
                                                <strong>Tipo de pago:</strong> <span id="tipoPagoText"></span>
                                            </div>
                                            <table id="datatablePagosDetalle"
                                                class="table table-bordered dt-responsive text-center table-sm"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
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
                data: "fecha_emision",
                class: "text-center",
            },
            {
                data: "fecha_vencimiento",
                class: "text-center",
            },
            {
                data: "razon_social",
                class: "text-center",
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
                                          class="btn btn-sm btn-danger btnAnular"
                                          title="Anular"
                                          data-bs-toggle="tooltip">
                                     <i class="fa fa-trash"></i>
                                   </button>`;
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

            // Cargar productos
            $.ajax({
                type: 'POST',
                url: _URL + '/ajas/compra/detalle',
                data: { id: id },
                success: function (resp) {
                    let data = JSON.parse(resp);
                    // Agregar información del usuario en el modal
                    let usuarioInfo = '';
                    if (data.length > 0 && data[0].nombres && data[0].apellidos) {
                        usuarioInfo = `<div class="alert alert-info mb-3">
                    <strong>Registrado por:</strong> ${data[0].nombres} ${data[0].apellidos}
                </div>`;
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
                                    // Formatear precio a 2 decimales
                                    return parseFloat(data).toFixed(2);
                                }
                            }
                        ]
                    });

                    // Verificar si hay pagos a crédito
                    $.ajax({
                        type: 'POST',
                        url: _URL + '/ajs/compra/pagos',
                        data: { id: id },
                        success: function (respPagos) {
                            $("#loader-menor").hide();
                            let dataPagos = JSON.parse(respPagos);

                            if (dataPagos.tipo_pago == 2) {
                                // Es una compra a crédito
                                $("#tipoPagoText").text("Crédito");

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
                                        { data: "monto", class: "text-center" },
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
                                // Es una compra al contado
                                $("#tipoPagoText").text("Contado");
                                $("#datatablePagosDetalle").html('<tr><td colspan="3" class="text-center">Esta compra fue pagada al contado</td></tr>');
                            }
                        }
                    });
                }
            });
        });

        // Event listener para Editar
        $("#datatable").on("click", ".btnEditar", function (event) {
            var id = $(this).data("id");
            // Redirigir a la página de edición
            window.location.href = _URL + '/compras/editar/' + id;
        });

        // Event listener para Anular
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