<!-- resources\views\fragment-views\cliente\ventas.php -->
<link rel="stylesheet" href="<?= URL::to('public/css/factura.css') ?>?v=<?= time() ?>">
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
        </div>
        <div class="clearfix">
            <h6 class="page-title text-center">VENTAS</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="font-weight: 500; color: #CA3438;">Ventas
                </li>
            </ol>
        </div>

    </div>
</div>

<!-- end page title -->


<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <h4 class="card-title">Lista de Ventas</h4>

                <div class="card-title-desc d-flex flex-wrap gap-2 justify-content-end align-items-center">

                    <!-- Dropdown de Opciones para móvil -->
                    <div class="opciones-dropdown-ventas">
                        <div class="dropdown">
                            <button class="btn bg-rojo text-white dropdown-toggle" type="button"
                                id="dropdownOpcionesVentas" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bars me-1"></i> Opciones
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOpcionesVentas"
                                style="min-width: 250px;">
                                <li>
                                    <h6 class="dropdown-header">Facturar</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/ventas/productos">
                                        <i class="fa fa-plus me-2"></i> Facturar Productos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/ventas/servicios">
                                        <i class="fa fa-plus me-2"></i> Facturar Servicios
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/nota/electronica">
                                        <i class="fa fa-plus me-2"></i> Nota Electrónica
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <h6 class="dropdown-header">Reportes</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#ventas-pdf-reporte-v-p">
                                        <i class="fa fa-file-pdf-o me-2"></i> Ventas por Producto
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#ventas-pdf-reporte">
                                        <i class="fa fa-file-pdf-o me-2"></i> Reporte de Venta
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#ventas-pdf-reporteganancia">
                                        <i class="fa fa-file-pdf-o me-2"></i> Venta Ganancias
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#ventas-text-reporte">
                                        <i class="fa fa-file-text me-2"></i> Exportar TXT
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#ventas-xls-reporte">
                                        <i class="fa fa-file-excel me-2"></i> Exportar XLS
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#ventas-xls-reporte-rvta">
                                        <i class="fa fa-file-excel me-2"></i> Reporte RVTA
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Botones para desktop -->
                    <button class="btn btn-sm bg-white text-rojo border-rojo btn-ventas-desktop" data-bs-toggle="modal"
                        data-bs-target="#ventas-pdf-reporte-v-p"><i class="fa fa-cube me-1"></i> Reporte Ventas Producto</button>
                    <button class="btn btn-sm bg-white text-rojo border-rojo btn-ventas-desktop" data-bs-toggle="modal"
                        data-bs-target="#ventas-pdf-reporte"><i class="fa fa-file-pdf me-1"></i> Reporte de Venta</button>
                    <button class="btn btn-sm bg-white text-rojo border-rojo btn-ventas-desktop" data-bs-toggle="modal"
                        data-bs-target="#ventas-pdf-reporteganancia"><i class="fa fa-chart-line me-1"></i> Venta Ganancias</button>
                    <button class="btn btn-sm bg-white text-rojo border-rojo btn-ventas-desktop" data-bs-toggle="modal"
                        data-bs-target="#ventas-text-reporte"><i class="fa fa-file-alt me-1"></i> Exportar TXT</button>
                    <button class="btn btn-sm bg-white text-rojo border-rojo btn-ventas-desktop" data-bs-toggle="modal"
                        data-bs-target="#ventas-xls-reporte"><i class="fa fa-file-excel me-1"></i> Exportar XLS</button>
                    <button class="btn btn-sm bg-white text-rojo border-rojo btn-ventas-desktop" data-bs-toggle="modal"
                        data-bs-target="#ventas-xls-reporte-rvta"><i class="fa fa-table me-1"></i> Reporte RVTA</button>
                    <a href="/nota/electronica" class="btn btn-sm bg-rojo text-white bordes btn-ventas-desktop"><i class="fa fa-sticky-note me-1"></i> Nota Electrónica</a>
                    <a href="/ventas/servicios" class="btn btn-sm bg-rojo text-white bordes btn-ventas-desktop"><i class="fa fa-wrench me-1"></i> Facturar Servicios</a>
                    <a href="/ventas/productos" class="btn btn-sm bg-rojo text-white bordes btn-ventas-desktop"><i class="fa fa-shopping-cart me-1"></i> Facturar Productos</a>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap "
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead class="table-light">
                            <tr>
                                <!-- <th></th> -->
                                <th>Documento</th>
                                <th>Fecha V.</th>
                                <th>Cliente</th>
                                <th>Sub. Total</th>
                                <th>IGV</th>
                                <th>Total</th>
                                <th>Sunat</th>
                                <th>Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div> <!-- end col -->
</div>

<!-- Modal de Reporte de Ventas y Ganancias -->
<?php include __DIR__ . '/modals/ventas/modal-reporte-ganancia.php'; ?>

<!-- Modal de Reporte de Ventas -->
<?php include __DIR__ . '/modals/ventas/reporte-ventas.php'; ?>
<!-- Modal de Reporte por Producto -->
<?php include __DIR__ . '/modals/ventas/modal-venta-por-producto.php'; ?>

<!-- Modal de Reporte TXT -->
<?php include __DIR__ . '/modals/ventas/modal-reporte-txt.php'; ?>
<!-- Modal de Reporte Excel -->
<?php include __DIR__ . '/modals/ventas/modal-excel-reporte.php'; ?>

<!-- Modal de Reporte Excel RVTA -->
<?php include __DIR__ . '/modals/ventas/modal-excel-RVTA.php'; ?>

<!-- Modal de Enviar Comprobante (WhatsApp + Email) -->
<?php include __DIR__ . '/modals/modal-enviar-comprobante.php'; ?>



<div id="modal_ver_detalle" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <input type="hidden" name="idUsaVenta" id="idPresu" value="">
            <input type="hidden" name="trid" id="trid" value="">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="myModalLabel">Detalle Productos en venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_detalle">

            </div>

            |
            <div class="modal-footer">

                <button type="button" class="btn border-rojo waves-effect" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<!-- imprimir comporbante -->
<?php include __DIR__ . '/modals/imprimir-comprobante.php'; ?>



<div class="modal fade" id="modalEnviarFacturaReporteDia" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-rojo">
                <h5 class="modal-title" id="exampleModalLabel">Compartir Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row text-start">
                    <div class="col-md-12">
                        <form id="from-send-email">
                            <div class="form-group">
                                <label>Enviar Por Email</label>
                                <div class="input-group mb-3">
                                    <input name="email" type="email" class="form-control emailCompartir"
                                        placeholder="ejemplo@gmail.com">
                                    <div class="input-group-prepend">
                                        <button type="submit" class="btn bg-rojo"><i class="fa fa-send"></i>
                                            Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form id="from-send-whatsapp">

                            <div class="form-group">
                                <label>Enviar a Whatsapp</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+51 </span>
                                    </div>
                                    <input require name="numeroCompartir" type="text"
                                        class="form-control numeroCompartir" oninput="process(this)" maxlength="9">

                                    <div class="input-group-prepend">
                                        <button class="btn bg-rojo" type="submit"><i class="fa fa-send"></i>
                                            Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function process(input) {
        let value = input.value;
        let numbers = value.replace(/[^0-9]/g, "");
        input.value = numbers;
    }

    function tes() {
        /*$("#loader-menor").show()
        _ajax("/ajs/ventas", "POST", {}, function(resp) {
            //console.log(resp);
            tabla.rows().remove();
            resp.forEach(function(item) {
                const igv_v = parseFloat(item.igv + "")
                tabla.row.add([
                    item.cod_v,
                    item.abreviatura + " | " + item.serie + " - " + item.numero,
                    item.fecha_emision,
                    item.documento + " | " + item.datos,
                    item.apli_igv == '1' ? (parseFloat(item.total) / (igv_v + 1)).toFixed(4) : (parseFloat(item.total)).toFixed(4),
                    item.apli_igv == '1' ? (parseFloat(item.total) / (igv_v + 1) * igv_v).toFixed(4) : '0.00',
                    (parseFloat(item.total)).toFixed(4),
                    item.enviado_sunat + "-" + item.id_tido + "-" + item.cod_v,
                    item.estado,
                    item.id_venta
                ]).draw(false);
            })
        })*/
    }
    var tabla;



    $(document).ready(function () {

        $('#from-send-whatsapp').submit(function (e) {
            e.preventDefault()
            if ($('.numeroCompartir').val() !== '') {
                console.log($('.numeroCompartir').val());
                var link = "https://api.whatsapp.com/send?phone=";
                const cod_ = 51;
                const number_ = $('.numeroCompartir').val();
                const mensaje = localStorage.getItem('linkFechaReporte');
                if (number_.length > 0) {
                    link += cod_ + number_
                    if (mensaje.length > 0) {
                        link += "&text=" + encodeURIComponent(mensaje)
                        console.log(link);
                        window.open(link);
                    }
                }
            } else {
                alertAdvertencia('Ingrese un numero valido')
            }

        })
        $('#from-send-email').submit(function (e) {
            e.preventDefault()
            if ($('.emailCompartir').val() !== '') {
                console.log($('.emailCompartir').val());
            } else {
                alertAdvertencia('Ingrese un correo valido')
            }
            /*  console.log($(this)); */
        })
        $("#pdf-generar-resporte-ventas-ganancia").submit(function (evt) {
            evt.preventDefault();
            const anio = $("#pdf-generar-resporte-ventas-ganancia").find("[name='anio']").val()
            var mes = $("#pdf-generar-resporte-ventas-ganancia").find("[name='mes']").val()
            var dia = $("#pdf-generar-resporte-ventas-ganancia").find("[name='dia']").val()
            if (dia === '') {
                dia = 'nn'
            }
            localStorage.removeItem('fechaReporte')
            mes = parseInt(mes + '')
            window.open(_URL + "/reporte/ventasganancias/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            $('#ventas-pdf-reporte').modal('hide')

            localStorage.setItem('fechaReporte', anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            localStorage.setItem('linkFechaReporte', _URL + "/reporte/ventasganancias/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            console.log(localStorage.getItem('fechaReporte'));

        })
        $("#pdf-generar-resporte-ventas").submit(function (evt) {
            evt.preventDefault();
            const anio = $("#pdf-generar-resporte-ventas").find("[name='anio']").val()
            var mes = $("#pdf-generar-resporte-ventas").find("[name='mes']").val()
            var dia = $("#pdf-generar-resporte-ventas").find("[name='dia']").val()
            var metodo = $("#pdf-generar-resporte-ventas").find("[name='metodo']").val()
            if (dia === '') {
                dia = 'nn'
            }
            localStorage.removeItem('fechaReporte')
            mes = parseInt(mes + '')
            window.open(_URL + "/reporte/ventas/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia + '-' + metodo)
            $('#ventas-pdf-reporte').modal('hide')
            $('#modalEnviarFacturaReporteDia').modal('show')
            localStorage.setItem('fechaReporte', anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            localStorage.setItem('linkFechaReporte', _URL + "/reporte/ventas/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            console.log(localStorage.getItem('fechaReporte'));

        })
        $("#pdf-generar-resporte-ventas-xls").submit(function (evt) {
            evt.preventDefault();
            console.log($('#anioExcel').val());
            console.log($('#mesExcel').val());
            window.open(_URL + '/reporte/excel/' + $('#anioExcel').val() + '-' + $('#mesExcel').val())
        })
        $("#pdf-generar-resporte-ventas-xls-rvta").submit(function (evt) {
            evt.preventDefault();
            console.log($('#anioExcel22').val());
            console.log($('#mesExcel22').val());
            window.open(_URL + '/reporte/rvta/excel/' + $('#anioExcel22').val() + '-' + $('#mesExcel22').val())
        })


        /*    $('.btnExcelll').click(function() {
            window.open(_URL + '/reporte/test/excel')
        })

 */
        $("#txt-generar-resporte-ventas").submit(function (evt) {
            evt.preventDefault();
            $("#loader-menor").show();

            $.ajax({
                url: _URL + "/ajs/generar/txt/ventareporte",
                type: "POST",
                data: $(this).serialize(),
                success: function (resp) {
                    console.log(resp);
                    if (resp.error) {
                        alertError("Error: " + resp.error);
                    } else if (resp.file) {
                        downloadFile(_URL + "/files/temp/" + resp.file);
                        alertExito("Archivo generado correctamente");
                    } else {
                        alertAdvertencia("Respuesta inesperada del servidor");
                    }
                    $("#loader-menor").hide();
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud:", error);
                    alertError("Error en la solicitud: " + error);
                    $("#loader-menor").hide();
                }
            });
        })

        $("#datatable").on("click", ".btn-detalle-vent", function (evt) {
            var trid = $(this).closest('tr').attr('id');
            var ventaIdForImprimir = $(this).data('venta')
            var valor = $('#idUsaVenta').val(ventaIdForImprimir);
            $('#btnImprimirFactura').attr("href", _URL + '/venta/pdf/voucher/' + ventaIdForImprimir + "/")
        })

        $("#datatable").on("click", ".btn-send-sunat", function (evt) {
            const cod = ($(evt.currentTarget).attr('data-venta'));
            $("#loader-menor").show()
            _ajax("/ajs/send/sunat/venta", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    if (resp.res) {
                        alertExito("Enviado a la sunat")
                        tes();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: "Alerta",
                            html: resp.msg,
                        })
                    }
                }
            )
        })

        tabla = $("#datatable").DataTable({
            "processing": true,
            "serverSide": true,
            "sAjaxSource": _URL + "/ajs/ventas",
            "responsive": false, // Deshabilitar responsividad
            "scrollX": true,   // Habilitar scroll horizontal
            "autoWidth": false, // Deshabilitar auto-ancho
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "order": [[0, "desc"]],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {
                    targets: 0, // sn_v (Documento)
                    width: "120px",
                    render: function (data, type, row) {
                        if (!row[8]) return ''; // Cambiar row[0] por row[8]
                        return '<a class="btn-info-vent" target="_blank" href="' + row[8].split('--')[0] + '">' + (data || '') + '</a>';
                    }
                },
                {
                    targets: 1, // Fecha
                    width: "100px"
                },
                {
                    targets: 2, // Cliente
                    width: "200px",
                    render: function (data, type, row) {
                        if (type === 'display' && data && data.length > 40) {
                            return '<span title="' + data + '">' + data.substr(0, 40) + '...</span>';
                        }
                        return data;
                    }
                },
                {
                    targets: 3, // Subtotal
                    width: "100px"
                },
                {
                    targets: 4, // IGV
                    width: "100px"
                },
                {
                    targets: 5, // Total
                    width: "100px"
                },
                {
                    targets: 6, // doc_ventae (Sunat)
                    width: "120px",
                    render: function (data, type, row) {
                        if (!data) return '';

                        try {
                            const dataParts = data.split("-");
                            if (!row[8]) return ''; // Usar row[8] en lugar de row[9]

                            const desData = row[8].split('--');

                            if (!(desData[1] == '-')) {
                                if (dataParts[0] == '1') {
                                    return '<span class="badge bg-success">Enviado</span>';
                                } else {
                                    let bntSend = '';
                                    if (dataParts[1] == '2' || dataParts[1] == '1') {
                                        bntSend = '<button data-venta="' + desData[0] + '" class="btn-send-sunat btn btn-sm btn-info"><i class="fas fa-location-arrow"></i></button>';
                                    }
                                    return '<span class="badge bg-warning">Pendiente</span> ' + bntSend;
                                }
                            }
                        } catch (e) {
                            console.error('Error procesando datos:', e);
                            return '';
                        }
                        return '';
                    }
                },
                {
                    targets: 7, // estado
                    width: "90px",
                    render: function (data, type, row) {
                        if (data == 1) {
                            return '<span class="badge bg-success">Normal</span>';
                        } else if (data == 2) {
                            return '<span class="badge bg-danger">Anulado</span>';
                        }
                        return data || '';
                    }
                },
                {
                    targets: 8, // id_venta (Acción)
                    width: "80px",
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (!row[7] || row[7] != 1) return ""; // Usar row[7] para estado

                        try {
                            if (!row[6]) return ""; // Usar row[6] para doc_ventae
                            const estadoSunat = row[6].split("-")[0];
                            if (!data) return "";
                            const desData = data.split("--");

                            const stpan = '<span id="' + desData[0] + '-nom-xml" style="display: none">' + desData[1] + "</span>";

                            return stpan + `
                <div class="action-menu">
                    <button type="button" class="action-button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-actions">
                        <a class="btn-info-vent" href="${desData[0]}">
                            <i class="fa fa-print text-primary"></i> Imprimir
                        </a>
                        <a class="btn-send-fac" data-venta="${desData[0]}">
                            <i class="fas fa-paper-plane text-primary"></i> Enviar Comprobante
                        </a>
                        ${desData[1] != "-" ? `
                            <a href="${_URL}/files/facturacion/xml/20602281761/${desData[1]}.xml" target="_blank">
                                <i class="fas fa-file-code text-info"></i> XML
                            </a>
                        ` : ""}
                        ${estadoSunat != "0" ? `
                            <a href="${_URL}/files/facturacion/cdr/20602281761/R-${desData[1]}.zip" target="_blank">
                                <i class="fa fa-file-zip text-success"></i> CDR
                            </a>
                        ` : ""}
                        <div class="divider"></div>
                        <a class="btn-detalle-vent" data-venta="${desData[0]}">
                            <i class="fa fa-eye text-primary"></i> Ver Detalle
                        </a>
                        <a class="btn-anular-vent" data-venta="${desData[0]}">
                            <i class="fa fa-trash text-danger"></i> Anular
                        </a>
                        <a class="btn-editar-venta" data-venta="${desData[0]}">
                            <i class="fa fa-pen text-warning"></i> Editar
                        </a>
                    </div>
                </div>
            `;
                        } catch (e) {
                            console.error('Error en render de acciones:', e);
                            return '';
                        }
                    }
                }
            ],

            "error": function (xhr, error, thrown) {
                console.error('Error en DataTables:', error);
                alertError("Error al cargar los datos de ventas");
            }
        });

        tes()


        $("#datatable").on("click", ".btn-send-fac", function (evt) {
            const venta = $(evt.currentTarget).data('venta');
            $("#loader-menor").show();

            _post("/ajs/informacion/venta/fb", { venta }, function (resp) {
                $("#loader-menor").hide();

                // Usar el componente modal reutilizable
                abrirModalEnviarComprobante({
                    tipo: 'venta',
                    id: venta,
                    numero: resp.serie ? `${resp.serie}-${resp.numero}` : `Venta #${venta}`,
                    link: resp.link,
                    linkDescarga: resp.linkd,
                    nombreArchivo: resp.file_name,
                    email: resp.mail || '',
                    telefono: resp.telefono || ''
                });
            });
        })

        window.onafterprint = function (e) {
            redirect();
        };

        function redirect() {
            document.location.href = _URL
        }

        // Manejador de action-button eliminado (duplicado)

        function closePrintView() {
            document.location.href = 'somewhere.html';
        }
        /*   $("#ce-t-a4").click(function() {
            let printA4 = $(this).attr('href')
            if ($("#device-app").val() == 'desktop') {
                var iframe = document.createElement('iframe');
                iframe.style.display = "none";
                iframe.src = printA4;
                document.body.appendChild(iframe);
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                iframe.contentWindow.close();
                //investigar cuando pierda el focus en el iframe -
                iframe.contentWindow.addEventListener('afterprint', (evt) => {
                    document.body.removeChild(iframe)
                    console.log('asd');
                });
   
                console.log(printA4);
            } else {
                window.open(printA4)
            }

        }); */









        /* 
                $("#ce-t-a4-m").click(function() {
                    let printA4 = $(this).attr('href')
                    if ($("#device-app").val() == 'desktop') {
                        var iframe = document.createElement('iframe');
                        iframe.style.display = "none";
                        iframe.src = printA4;
                        document.body.appendChild(iframe);
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        iframe.contentWindow.print();
                        console.log(printA4);
                    } else {
                        window.open(printA4)
                    }

                }); */
        /*      $("#ce-t-8cm").click(function() {
                 let printA4 = $(this).attr('href')
                 if ($("#device-app").val() == 'desktop') {

                 } else {
                     window.open(printA4)
                 }
                 var iframe = document.createElement('iframe');
                 iframe.style.display = "none";
                 iframe.src = printA4;
                 document.body.appendChild(iframe);
                 iframe.contentWindow.focus();
                 iframe.contentWindow.print();
                 console.log(printA4);
             });
             $("#ce-t-5_6cm").click(function() {
                 let printA4 = $(this).attr('href')
                 if ($("#device-app").val() == 'desktop') {
                     var iframe = document.createElement('iframe');
                     iframe.style.display = "none";
                     iframe.src = printA4;
                     document.body.appendChild(iframe);
                     iframe.contentWindow.focus();
                     iframe.contentWindow.print();
                     console.log(printA4);
                 } else {
                     window.open(printA4)
                 }

             }); */
        // Manejadores de action-menu movidos fuera del document.ready (ver código al final)




        $("#datatable").on("click", ".btn-info-vent", function (evt) {
            evt.preventDefault();
            const iventa = $(evt.currentTarget).attr('href');
            $("#modalImprimirComprobante").modal("show");
            $("#ce-t-a4").attr("href", _URL + "/venta/comprobante/pdf/" + iventa + '/' + $("#" + iventa + "-nom-xml").text())
            $("#ce-t-a4-m").attr("href", _URL + "/venta/comprobante/pdf/ma4/" + iventa + '/' + $("#" + iventa + "-nom-xml").text())
            $("#ce-t-8cm").attr("href", _URL + "/venta/pdf/voucher/8cm/" + iventa + '/' + $("#" + iventa + "-nom-xml").text())
            $("#ce-t-5_6cm").attr("href", _URL + "/venta/pdf/voucher/5.6cm/" + iventa + '/' + $("#" + iventa + "-nom-xml").text());
        })
        // anular venta sin refresacar
        $("#datatable").on("click", ".btn-anular-vent", function (evt) {
            const iventa = $(evt.currentTarget).attr('data-venta');
            Swal.fire({
                title: 'Anular Venta',
                text: "¿Esta seguro de ANULAR este documento?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Anular!'
            }).then((result) => {
                if (result.isConfirmed) {
                    _ajax("/ajs/venta/anular", "POST", {
                        iventa
                    }, function (resp) {
                        if (resp.res) {
                            // Recargar la tabla para mostrar el estado actualizado
                            tabla.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Venta anulada satisfactoriamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo anular la venta'
                            });
                        }
                    })
                }
            })
        });

        $("#datatable").on("click", ".btn-editar-venta", function (evt) {
            const iventa = $(evt.currentTarget).attr('data-venta');
            /*  console.log(iventa); */
            _ajax("/ajs/venta/consultas/tipo/venta", "POST", {
                iventa
            }, function (resp) {
                console.log(resp);
                /*    console.log(resp); */
                if (resp.res) {
                    if (resp.tipo == 'productos') {
                        /*  localStorage.removeItem('idVenta') */
                        window.location.href = _URL + '/editar-venta-producto/' + iventa
                        /*  localStorage.setItem('idVenta' , iventa) */
                    } else if (resp.tipo == 'servicio') {
                        /*   localStorage.removeItem('idVenta') */
                        window.location.href = _URL + '/editar-venta-servicio/' + iventa
                        /*  localStorage.setItem('idVenta' , iventa) */
                    }
                } else {
                    alertError("Ocurrio un error")
                }
            })
        })
        $("#datatable").on("click", ".btn-detalle-vent", function (evt) {
            const iventa = $(evt.currentTarget).attr('data-venta')
            _ajax("/ajs/venta/detalle", "POST", {
                iventa
            }, function (resp) {
                console.log(resp);
                if (resp.res) {
                    $("#modal_ver_detalle").modal("show")

                    // Encabezado común
                    let headerHTML = 'Fecha : ' + resp.data.fecha_emision +
                        '<br>Documento : ' + (resp.data.id_tido == '2' ? 'FT' : 'BT') + ' | ' + resp.data.serie + ' - ' + resp.data.numero +
                        '<br>Cliente : ' + resp.data.documento + ' | ' + resp.data.datos +
                        '<br>Total : ' + resp.data.montoTotal + '<br><hr>';

                    // Si hay equipos, renderizar agrupado por equipos (sin pestañas)
                    if (resp.data.equipos && resp.data.equipos.length > 0) {
                        let equiposHTML = '<table class="table table-striped">' +
                            '<thead><tr><th>#</th><th>Cod.</th><th>Producto</th><th>Cant.</th><th>Precio</th><th>Total</th></tr></thead>' +
                            '<tbody>';

                        let numeroItem = 1;
                        resp.data.equipos.forEach(function (eq, equipoIdx) {
                            // Header del equipo
                            equiposHTML += '<tr class="table-secondary">' +
                                '<td colspan="6"><strong>EQUIPO: ' +
                                (eq.marca || '') + ' ' + (eq.equipo || '') +
                                ' - Modelo: ' + (eq.modelo || '') + ' - Serie: ' + (eq.numero_serie || '') +
                                '</strong></td></tr>';

                            // Productos del equipo
                            if (eq.items && eq.items.length > 0) {
                                eq.items.forEach(function (item) {
                                    let parcial = (parseFloat(item.precio) * parseFloat(item.cantidad)).toFixed(2);
                                    equiposHTML += '<tr>' +
                                        '<td class="text-center">' + numeroItem + '</td>' +
                                        '<td>' + (item.codigo || '') + '</td>' +
                                        '<td>' + (item.nombre || '') + '</td>' +
                                        '<td class="text-center">' + item.cantidad + '</td>' +
                                        '<td class="text-end">' + parseFloat(item.precio).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parcial + '</td>' +
                                        '</tr>';
                                    numeroItem++;
                                });
                            } else {
                                equiposHTML += '<tr><td colspan="6" class="text-center text-muted">Sin items para este equipo</td></tr>';
                            }

                            // Espacio entre equipos (excepto el último)
                            if (equipoIdx < resp.data.equipos.length - 1) {
                                equiposHTML += '<tr><td colspan="6" style="padding: 5px;"></td></tr>';
                            }
                        });

                        equiposHTML += '</tbody>' +
                            '<tfoot><tr class="table-info"><td colspan="5" class="text-end"><strong>TOTAL VENTA:</strong></td>' +
                            '<td class="text-end"><strong>' + resp.data.montoTotal + '</strong></td>' +
                            '</tr></tfoot></table>';

                        $("#modal_detalle").html(headerHTML + equiposHTML);
                        return;
                    }

                    // Fallback: render plano cuando no hay equipos
                    var rowHTML = '';
                    (resp.data.detalles || []).forEach(function (elm) {
                        let parcial = (parseFloat(elm.precio) * parseFloat(elm.cantidad)).toFixed(2);
                        let utilidad = ((parseFloat(elm.precio) - parseFloat(elm.costo)) * parseFloat(elm.cantidad)).toFixed(2);
                        rowHTML += '<tr>' +
                            '<td class="text-center">' + elm.cantidad + '</td>' +
                            '<td>' + (elm.codigo || '') + '</td>' +
                            '<td>' + (elm.nombre || '') + '</td>' +
                            '<td class="text-right">' + elm.precio + '</td>' +
                            '<td class="text-right">' + parcial + '</td>' +
                            '<td class="text-right">' + utilidad + '</td>' +
                            '</tr>';
                    })
                    $("#modal_detalle").html(headerHTML +
                        '<table class="table table-striped">' +
                        '<thead>' +
                        '<tr>' +
                        '<th>Cant.</th>' +
                        '<th>Cod.</th>' +
                        '<th>Producto</th>' +
                        '<th>Precio</th>' +
                        '<th>Parcial</th>' +
                        '<th>Utilidad</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody>' +
                        rowHTML +
                        '<td class="text-center"></td>' +
                        '<td class="text-right text-capitalize">TOTAL</td>' +
                        '<td class="text-right"></td>' +
                        '<td class="text-right"> ' + resp.data.montoTotal + '</td>' +
                        '<td class="text-right"> ' + resp.data.montoTotal + '</td>' +
                        '</tr>' +
                        '</tfoot>' +
                        '</table>'
                    )
                }
            })
        });

        // Manejo del menú de acciones (dropdown)
        $(document).on("click", function(e) {
            if (!$(e.target).closest(".action-menu").length) {
                $(".action-menu").removeClass("show");
            }
        });

        $(document).on("click", ".action-button", function(e) {
            e.preventDefault();
            e.stopPropagation();

            const menu = $(this).closest(".action-menu");
            const dropdown = menu.find(".dropdown-actions");

            // Cerrar otros menús
            $(".action-menu").not(menu).removeClass("show");

            // Toggle el menú actual
            menu.toggleClass("show");

            if (menu.hasClass("show")) {
                const buttonRect = this.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const dropdownWidth = 200;
                const dropdownHeight = 280;

                // En móvil, centrar el dropdown
                if (viewportWidth <= 768) {
                    dropdown.css({
                        'position': 'fixed',
                        'left': '50%',
                        'top': '50%',
                        'transform': 'translate(-50%, -50%)',
                        'width': '90%',
                        'max-width': '280px'
                    });
                } else {
                    // En desktop, posicionar cerca del botón
                    let top = buttonRect.bottom + window.scrollY + 5;
                    let left = buttonRect.left + window.scrollX - dropdownWidth + 30;

                    // Si no hay espacio abajo, mostrar arriba
                    if (buttonRect.bottom + dropdownHeight > viewportHeight) {
                        top = buttonRect.top + window.scrollY - dropdownHeight - 5;
                    }

                    // Ajustar si se sale por la izquierda
                    if (left < 10) {
                        left = 10;
                    }

                    // Ajustar si se sale por la derecha
                    if (left + dropdownWidth > viewportWidth - 10) {
                        left = viewportWidth - dropdownWidth - 10;
                    }

                    dropdown.css({
                        'position': 'fixed',
                        'top': top + 'px',
                        'left': left + 'px',
                        'transform': 'none',
                        'width': dropdownWidth + 'px'
                    });
                }
            }
        });

        $(document).on("click", ".dropdown-actions a", function() {
            $(this).closest(".action-menu").removeClass("show");
        });
    })
</script>