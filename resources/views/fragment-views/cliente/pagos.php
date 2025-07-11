<!-- resources\views\fragment-views\cliente\pagos.php -->
<div class="page-title-box">
    <div class="row align-items-center">

        <div class="clearfix">
            <h6 class="page-title text-center">CUENTAS POR PAGAR</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);" style="color: #CA3438;">Cuentas Por Pagar</a>
                </li>
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

                <div class="table-responsive">


                    <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead class="table-light">
                            <tr>
                                <th style="text-align: center;">Item</th>
                                <th style="text-align: center;">Codigo</th>
                                <th style="text-align: center;">F. Emision</th>
                                <th style="text-align: center;">F. Vencimiento</th>
                                <th style="text-align: center;">Empresa</th>
                                <!--   <th style="text-align: center;">Moneda</th> -->
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">Pagado</th>
                                <th style="text-align: center;">Saldo</th>
                                <th style="text-align: center;">Situacion</th>
                                <th style="text-align: center;">Dias Vencidos</th>
                                <th style="text-align: center;">Detalles</th>

                            </tr>
                        </thead>

                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg"> <!-- Agregado modal-lg para hacerlo más amplio -->
                        <div class="modal-content ">
                            <div class="modal-header bg-rojo text-white">
                                <!-- Cambiado a bg-primary para un mejor aspecto -->
                                <h5 class="modal-title" id="exampleModalLabel">Detalles de compra</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table id="datatableDiasCompras"
                                        class="table table-bordered dt-responsive nowrap text-center table-hover table-striped"
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
        var days = daysdifference('03/25/2021', '03/22/2021');
        // Add two dates to two variables
        console.log(days);

        function daysdifference(firstDate, secondDate) {
            var startDay = new Date(firstDate);
            var endDay = new Date(secondDate);

            // Determine the time difference between two dates
            var millisBetween = startDay.getTime() - endDay.getTime();

            // Determine the number of days between two dates
            var days = millisBetween / (1000 * 3600 * 24);

            // Show the final number of days between dates
            return Math.round(Math.abs(days));
        }
        $.ajax({
            type: 'POST',
            url: _URL + '/ajs/cuentas/ventas/render',
            success: function (resp) {
                let data = JSON.parse(resp)
                let iguaalcion = data[5]
                console.log();
                if (iguaalcion.pagado > parseFloat(iguaalcion.total).toFixed(3)) {
                    console.log('nice');
                } else {
                    console.log('bad');
                }
            }
        });
        datatable = $("#datatable").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            ajax: {
                url: _URL + "/ajs/cuentas/ventas/render",
                method: "POST",
                dataSrc: "",
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columns: [
            //     {
            //     data: "id_compra",
            //     class: "text-center",
            // },
   {
    data: null,
    class: "text-center",
    render: function(data, type, row, meta) {
        // Obtener la información de paginación
        var info = $('#datatable').DataTable().page.info();
        return info.start + meta.row + 1;
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
                    if (row.total !== null) {
                        return `<div class="text-center">
                                            <div class="btn-group">S/ ${row.total}</div></div>`;
                    } else {
                        return `<div class="text-center">
                                            <div class="btn-group"></div></div>`;
                    }

                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.pagado !== null) {
                        return `<div class="text-center">
                                            <div class="btn-group">S/ ${row.pagado}</div></div>`;
                    } else {
                        return `<div class="text-center">
                                            <div class="btn-group"></div></div>`;
                    }

                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.saldo !== null) {
                        return `<div class="text-center">
                                            <div class="btn-group">S/ ${row.saldo}</div></div>`;
                    } else {
                        return `<div class="text-center">
                                            <div class="btn-group"></div></div>`;
                    }

                },
            },

            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {

                    let vencimiento = row.fecha_vencimiento
                    const [year, month, day] = vencimiento.split('-');
                    const vencimientoFecha = [month, day, year].join('/');
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;
                    if ((parseFloat(row.total).toFixed(3) == parseFloat(row.pagado).toFixed(3))) {
                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-success">Pagado</span></div></div>`;
                    } else if ((parseFloat(row.total).toFixed(3) > parseFloat(row.pagado).toFixed(3)) && today > vencimientoFecha) {
                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-danger">Vencido</span></div></div>`;
                    } else if ((parseFloat(row.total).toFixed(3) > parseFloat(row.pagado).toFixed(3)) && today < vencimientoFecha) {
                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-info">Vigente</span></div></div>`;
                    }


                },
            },

            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    let vencimiento = row.fecha_vencimiento
                    const [year, month, day] = vencimiento.split('-');
                    const vencimientoFecha = [month, day, year].join('/');
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;
                    const dateToday = new Date(today);
                    const dateVencimiento = new Date(vencimientoFecha);
                    const diffTime = Math.abs(dateToday - dateVencimiento);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    /* console.log(diffDays); */
                    if (today > vencimientoFecha) {
                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-danger">${diffDays}</span></div></div>`;
                    } else {
                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-success">0</span></div></div>`;
                    }

                },
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return `<div class="text-center">
                                            <div class="btn-group"><button  data-id="${Number(
                        row.id_compra
                    )}" class="btn btn-success btnDetalles btn-sm"><i class="fa fa-eye"></i> </button></div></div>`;
                },
            },
            ],
        });

        $("#datatable").on("click", ".btnDetalles ", function (event) {
            $("#loader-menor").show()
            var table = $("#tablaMaquina").DataTable();
            var trid = $(this).closest("tr").attr("id");
            var id = $(this).data("id");
            $("#exampleModal").modal("show");
            $("#exampleModal")
                .find(".modal-title")
                .text("Detalles compra N° " + id);
            $.ajax({
                url: _URL + "/ajs/getAllCuotas/byIdCompra",
                data: {
                    id: id,
                },
                type: "post",
                success: function (resp) {
                    $("#loader-menor").hide()
                    resp = JSON.parse(resp)
                    console.log(resp[0]['fecha']);

                    let vencimiento = resp[0]['fecha']
                    const [year, month, day] = vencimiento.split('-');
                    const vencimientoFecha = [month, day, year].join('/');
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();
                    today = mm + '/' + dd + '/' + yyyy;
                    const dateToday = new Date(today);
                    const dateVencimiento = new Date(vencimientoFecha);
                    const diffTime = Math.abs(dateToday - dateVencimiento);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    console.log(today);
                    console.log('vencimient ' + vencimientoFecha);
                    datatableDiasCompras = $("#datatableDiasCompras").DataTable({

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
                            //     {
                            //     data: "dias_compra_id",
                            //     class: "text-center",
                            // },
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row, meta) {
                                    return meta.row + 1; // Número correlativo
                                },
                            },
                            {
                                data: "monto",
                                class: "text-center",
                            },
                            {
                                data: "fecha",
                                class: "text-center",
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row) {

                                    let vencimiento = row.fecha
                                    const [year, month, day] = vencimiento.split('-');
                                    const vencimientoFecha = [month, day, year].join('/');
                                    var today = new Date();
                                    var dd = String(today.getDate()).padStart(2, '0');
                                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                                    var yyyy = today.getFullYear();
                                    today = mm + '/' + dd + '/' + yyyy;
                                    if ((today > vencimientoFecha) && row.estado == '0') {
                                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-danger">Vencido</span></div></div>`;
                                    } else if ((today < vencimientoFecha || vencimientoFecha == today) && row.estado == '0') {
                                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-success">Vigente</span></div></div>`;
                                    } else if (row.estado == '1') {
                                        return `<div class="text-center">
              <div class="btn-group"><span class="badge bg-info">Pagado</span></div></div>`;
                                    }


                                },
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function (data, type, row) {
                                    if (row.estado == '0') {
                                        return `<div class="text-center">
                                            <div class="btn-group"><button  data-id="${Number(
                                            row.dias_compra_id
                                        )}" class="btn btn-success btnPagar btn-sm"><i class="fas fa-money-bill"></i> </button></div></div>`;
                                    }
                                    if (row.estado == '1') {
                                        return `<div class="text-center">
                                            <div class="btn-group"></div></div>`;
                                    }
                                },
                            },

                        ],
                    });


                },
            })
        });
        $("#datatableDiasCompras").on("click", ".btnPagar ", function (event) {

            var table = $("#tablaMaquina").DataTable();
            var trid = $(this).closest("tr").attr("id");
            var id = $(this).data("id");
            Swal.fire({
                title: '¿Desea pagar la cuota N° ' + id + ' ? ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#loader-menor").show()
                    $.ajax({
                        type: 'POST',
                        url: _URL + '/ajs/pagar/cuota/pago',
                        data: { id: id },
                        success: function (resp) {
                            $("#loader-menor").hide()
                            let data = JSON.parse(resp)
                            console.log(data);
                            location.reload();
                            /*  */
                        }
                    });
                }
            })
        })
    });
</script>