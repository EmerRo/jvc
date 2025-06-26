<!-- resources\views\fragment-views\cliente\cotizaciones.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">

<div class="page-title-box">
    <div class="row align-items-center">
        <!-- cotizaciones -->
        <div class="clearfix">
            <h6 class="page-title text-center">COTIZACIONES</h6>
            <ol class="breadcrumb m-0 float-start">

                <li class="breadcrumb-item"><a class="button-link"
                        style="font-weight: 500; color: #CA3438;">Cotizaciones</a></li>

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


                <h4 class="card-title"></h4>

                <div class="card-title-desc text-end " style="padding: 10px 10px 0 0;">

                    <?php if ($_SESSION["rol"] == 1): ?>
                        <button id="ventas-reporte" class="btn bg-white text-rojo border-rojo" style="margin-left: 5px;">
                            <i class="fa fa-file-pdf-o"></i> Exportar Reporte de Vendedores
                        </button>
                    <?php endif; ?>
                    <a href="/cotizaciones/add" id="folder_btn_nuevo_folder"
                        class="btn bg-rojo bordes text-white button-link">
                        <i class="fa fa-plus "></i> Nueva Cotización
                    </a>
                </div>

                <!-- Contenedor con overflow auto en lugar de table-responsive -->
                <div class="table-responsive">
                    <table id="datatable-c" class="table table-bordered dt-responsive nowrap text-center table-sm"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Sub. Total</th>
                                <th>IGV</th>
                                <th>Total</th>
                                <th>Vendedor</th>
                                <th>Estado</th>
                                <th>Vender</th>
                                <th>Guía Remisión</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ventas-reporte-bs" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo ">
                <h5 class="modal-title" id="exampleModalLabel">Reporte de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= URL::to('/reporte/cotizaciones/vendedores') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="text" class="form-control" name="rangoFechas" id="rangoFechas" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Vendedores</label>
                            <select name="vendedor" id="vendedor" class="form-control">
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn border-rojo text-rojo bg-white">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    function tes() {
        /*$("#loader-menor").show()
        _ajax("/ajs/cotizaciones", "POST", {}, function(resp) {
            //console.log(resp);
            tabla.rows().remove();
            resp.forEach(function(item) {
                let simbol='S/ '
                if (item.moneda.toString()==='2'){
                    item.total =item.total/item.cm_tc
                    simbol='$ '
                }
                tabla.row.add([
                    item.numero,
                    item.fecha,
                    item.documento + " | " + item.datos,
                    simbol+(parseFloat(item.total) / 1.18).toFixed(4),
                    simbol+(parseFloat(item.total) / 1.18 * 0.18).toFixed(4),
                    simbol+(parseFloat(item.total)).toFixed(4),
                    item.estado,
                    item.cotizacion_id,
                    item.cotizacion_id,

                    item.cotizacion_id
                ]).draw(false);
            })
        })*/
    }
    var tabla;
    $(document).ready(function () {
        // Configuración para hacer el DataTable responsivo
        tabla = $("#datatable-c").DataTable({
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            "processing": true,
            "serverSide": true,
            "sAjaxSource": _URL + "/data/cotizaciones/lista/ss",
            order: [
                [0, "desc"]
            ],
            language: {
                url: "ServerSide/Spanish.json",
            },
            columnDefs: [
                {
                    targets: 8,
                    render(data) {
                        return `<a href="/ventas/productos?coti=${data}" class="btn btn-success btn-sm button-link"><i class="fa fa-align-justify"></i></a>`;
                    }
                },
                
                {
                    targets: 7,
                    render: function (data, type, row, meta) {
                        if (data == '1') {
                            return '<span class="badge rounded-pill bg-success">Vendido</span>'
                        } else if (data == '2') {
                            return '<span class="badge rounded-pill bg-warning">Facturado</span>'
                        } else {
                            return '<span class="badge rounded-pill bg-danger">No Vendido</span>'
                        }
                    }
                },

                {
                    targets: 10,
                    render: function (data, type, row, meta) {
                        return `
                    <div class="btn-group">
                        <a href="${'/cotizaciones/edt/' + data}" class="button-link btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                        <a href="${_URL + '/r/cotizaciones/reporte/' + data}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file"></i></a>
                        <button onclick="eliminarCotizacion(${data})" data-cod="" type="button" class="btn-del btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                    </div>`;
                    }
                },
                {
                    targets: 9,
                    render(data) {
                        return `<a href="/guia/remision/registrar?coti=${data}" class="btn btn-success btn-sm button-link"><i class="fa fa-clipboard"></i></a>`;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        let subtotal = parseFloat(data) / 1.18;
                        return 'S/ ' + subtotal.toFixed(2);
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        let subtotal = parseFloat(data) / 1.18;
                        let igv = subtotal * 0.18;
                        return 'S/ ' + igv.toFixed(2);
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return 'S/ ' + parseFloat(data).toFixed(2);
                    }
                }
            ]
        });



        // Ajustar el ancho de la tabla cuando cambia el tamaño de la ventana
        $(window).resize(function () {
            tabla.columns.adjust().draw();
        });

        tes();
        $("#ventas-reporte").on('click', function () {
            $.ajax({
                type: "POST",
                url: _URL + "/ajs/cotizaciones/getvendedores",
                success: function (response) {
                    $('#rangoFechas').daterangepicker({
                        opens: 'left', // posición del selector de fechas
                        locale: {
                            format: 'YYYY-MM-DD', // formato de fecha
                            applyLabel: 'Aplicar', // etiqueta para aplicar el rango seleccionado
                            cancelLabel: 'Cancelar', // etiqueta para cancelar la selección
                            fromLabel: 'Desde', // etiqueta para el input de fecha de inicio
                            toLabel: 'Hasta', // etiqueta para el input de fecha de fin
                            customRangeLabel: 'Rango personalizado', // etiqueta para un rango de fechas personalizado
                            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'], // días de la semana
                            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] // nombres de los meses
                        }
                    });
                    let data = JSON.parse(response);
                    let options = "<option value='0'>--Todos--</option>";
                    $.each(data, function (i, d) {
                        options += `<option value="${d.usuario_id}">${d.nombres}</option>`;
                    });
                    $('#vendedor').html(options);
                    $('#ventas-reporte-bs').modal('show');
                }, error: function (response) {
                    console.log(response);
                }
            });
        });
    });

    function eliminarCotizacion(cod) {
        console.log(cod)
        _ajax("/ajs/cotizaciones/del", "POST", { cod }, function (resp) {
            tabla.ajax.reload();
        })
    }
</script>