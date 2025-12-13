<!-- resources\views\fragment-views\cliente\cotizaciones.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">

<style>
/* Estilos para el menú de acciones - Copiados de guia-remision.php y taller.php */
.table-responsive {
    overflow: visible !important;
}

.card {
    overflow: visible !important;
}

/* Estilos para el menú de acciones */
.action-menu {
    position: relative;
    display: inline-block;
    width: 30px;
    margin: 0 auto;
}

.action-button {
    background: none;
    border: none;
    color: #6b7280;
    width: 30px;
    height: 30px;
    padding: 5px;
    cursor: pointer;
    transition: color 0.2s;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-button:hover {
    color: #4f46e5;
    background-color: #f3f4f6;
}

.dropdown-actions {
    position: fixed;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    min-width: 200px;
    z-index: 9999;
    display: none;
    margin-top: 5px;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

.dropdown-actions a {
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.2s;
    cursor: pointer;
    white-space: nowrap;
}

.dropdown-actions a:hover {
    background-color: #f3f4f6;
}

.dropdown-actions i {
    width: 16px;
}

.dropdown-actions .text-danger:hover {
    background-color: #fee2e2;
}

.dropdown-actions .divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 4px 0;
}

/* Muestra el menú cuando tiene la clase show */
.action-menu.show .dropdown-actions {
    display: block;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .dropdown-actions {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        bottom: 20px;
        top: auto;
        width: 90%;
        max-width: 300px;
        max-height: 60vh;
        overflow-y: auto;
    }
}
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <!-- cotizaciones -->
        <div class="clearfix">
            <h6 class="page-title text-center">COTIZACIONES</h6>
          
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

                        <thead class="table-light">
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Sub. Total</th>
                                <th>IGV</th>
                                <th>Total</th>
                                <th>Vendedor</th>
                                <th>Estado</th>
                                <th>Vender</th>
                                <th>Guía </th>
                                <th>Acción</th>
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

<!-- Modal de WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="whatsappModalLabel">
                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label for="whatsappNumber" class="form-label">Número de WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text">+51</span>
                        <input type="tel" class="form-control form-control-lg" id="whatsappNumber"
                            placeholder="Ingrese número" maxlength="9" style="border-radius: 0 8px 8px 0;">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="enviarWhatsApp()">
                        <i class="fab fa-whatsapp me-2"></i>Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Imprimir Cotización -->
<div class="modal fade" id="modalImprimirCotizacion" tabindex="-1" aria-labelledby="modalImprimirCotizacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo">
                <h5 class="modal-title" id="modalImprimirCotizacionLabel">Imprimir Cotización</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row g-3">
                    <div class="col-6">
                        <a id="cot-a4" href="#" target="_blank" class="btn bg-rojo text-white w-100">
                            <i class="fa fa-file-pdf me-2"></i>Hoja A4
                        </a>
                    </div>
                    <div class="col-6">
                        <a id="cot-media-a4" href="#" target="_blank" class="btn bg-rojo text-white w-100">
                            <i class="fa fa-file-pdf me-2"></i>Media Hoja A4
                        </a>
                    </div>
                    <div class="col-6">
                        <a id="cot-voucher-8cm" href="#" target="_blank" class="btn bg-white border-rojo text-rojo w-100">
                            <i class="fas fa-file-invoice me-2"></i>Voucher 8cm
                        </a>
                    </div>
                    <div class="col-6">
                        <a id="cot-voucher-5-6cm" href="#" target="_blank" class="btn bg-white border-rojo text-rojo w-100">
                            <i class="fas fa-file-invoice me-2"></i>Voucher 5.6cm
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-rojo text-white" data-bs-dismiss="modal">Cerrar</button>
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
                    targets: 0, // Columna N°
                    render: function (data, type, row, meta) {
                        // data = número de cotización (ej: 7)
                        // row[8] = ID de cotización (ej: 56)
                        const numeroFormateado = 'COT-' + String(data).padStart(2, '0');
                        const cotizacionId = row[8]; // Obtener el ID desde la columna 8
                        return `<a href="/r/cotizaciones/reporte/${cotizacionId}" target="_blank" class="text-primary" style="text-decoration: none;">${numeroFormateado}</a>`;
                    }
                },
                
                
                {
                    targets: 7,
                    render: function (data, type, row, meta) {
                        if (data == '1') {
                            return '<span class="badge  bg-success">Vendido</span>'
                        } else if (data == '2') {
                            return '<span class="badge  bg-warning">Facturado</span>'
                        } else {
                            return '<span class="badge bg-danger">No Vendido</span>'
                        }
                    }
                },
                {
                    targets: 8,
                    render(data) {
                        return `<a href="/ventas/productos?coti=${data}" class="btn btn-success btn-sm button-link"><i class="fa fa-align-justify"></i></a>`;
                    }
                },

                
                {
                    targets: 9,
                    render(data) {
                        return `<a href="/guia/remision/registrar?coti=${data}" class="btn btn-success btn-sm button-link"><i class="fa fa-clipboard"></i></a>`;
                    }
                },
                {
                    targets: 10,
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return `
                        <div class="action-menu">
                            <button type="button" class="action-button">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-actions">
                                <a href="javascript:void(0)" onclick="abrirWhatsApp(${data})" title="Enviar por WhatsApp">
                                    <i class="fab fa-whatsapp text-success"></i> WhatsApp
                                </a>
                                <a class="button-link" href="/cotizaciones/edt/${data}">
                                    <i class="fa fa-edit text-primary"></i> Editar
                                </a>
                                <a href="javascript:void(0)" onclick="abrirModalImprimir(${data})">
                                    <i class="fa fa-print text-primary"></i> Imprimir
                                </a>
                                <div class="divider"></div>
                                <a class="text-danger" href="javascript:void(0)" onclick="eliminarCotizacion(${data})">
                                    <i class="fa fa-times"></i> Eliminar
                                </a>
                            </div>
                        </div>`;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        let aplicarIgv = row[11]; // aplicar_igv está en el índice 11
                        let moneda = row[12]; // moneda está en el índice 12
                        let simbolo = (moneda == '2') ? '$ ' : 'S/ ';
                        let subtotal = aplicarIgv == '1' ? parseFloat(data) / 1.18 : parseFloat(data);
                        return simbolo + subtotal.toFixed(2);
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        let aplicarIgv = row[11]; // aplicar_igv está en el índice 11
                        let moneda = row[12]; // moneda está en el índice 12
                        let simbolo = (moneda == '2') ? '$ ' : 'S/ ';
                        if (aplicarIgv == '1') {
                            let subtotal = parseFloat(data) / 1.18;
                            let igv = subtotal * 0.18;
                            return simbolo + igv.toFixed(2);
                        } else {
                            return simbolo + '0.00';
                        }
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        let moneda = row[12]; // moneda está en el índice 12
                        let simbolo = (moneda == '2') ? '$ ' : 'S/ ';
                        return simbolo + parseFloat(data).toFixed(2);
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

    var cotizacionActual = null;
    var cotizacionParaImprimir = null;

    function abrirWhatsApp(cotizacionId) {
        cotizacionActual = cotizacionId;
        $('#whatsappNumber').val('');
        $('#whatsappModal').modal('show');
    }

    function abrirModalImprimir(cotizacionId) {
        cotizacionParaImprimir = cotizacionId;

        // Configurar las URLs de los diferentes formatos
        $('#cot-a4').attr('href', `${_URL}/r/cotizaciones/reporte/${cotizacionId}`);
        $('#cot-media-a4').attr('href', `${_URL}/r/cotizaciones/reporte-media-a4/${cotizacionId}`);
        $('#cot-voucher-8cm').attr('href', `${_URL}/r/cotizaciones/reporte-voucher-8cm/${cotizacionId}`);
        $('#cot-voucher-5-6cm').attr('href', `${_URL}/r/cotizaciones/reporte-voucher-5-6cm/${cotizacionId}`);

        $('#modalImprimirCotizacion').modal('show');
    }

    // Función para enviar por WhatsApp
    function enviarWhatsApp() {
        const phoneNumber = $('#whatsappNumber').val().trim();

        if (!phoneNumber) {
            alert('Por favor ingrese un número de teléfono');
            return;
        }

        if (phoneNumber.length !== 9) {
            alert('El número debe tener 9 dígitos');
            return;
        }

        // Generar el enlace del reporte PDF
        const linkReporte = `${_URL}/r/cotizaciones/reporte/${cotizacionActual}`;

        // Mensaje personalizado para WhatsApp
        const mensaje = `Te envío la cotización para que puedas revisarla con detalle Cotización N° COT-${String(cotizacionActual).padStart(2, '0')}\n\nPuedes revisarla aquí: ${linkReporte}\n\nSi tienes alguna consulta o necesitas más información, no dudes en escribirme. Estaré encantada de ayudarte.`;

        // Construir la URL de WhatsApp
        const whatsappUrl = `https://api.whatsapp.com/send?phone=51${phoneNumber}&text=${encodeURIComponent(mensaje)}`;

        // Cerrar el modal y abrir WhatsApp
        $('#whatsappModal').modal('hide');
        window.open(whatsappUrl, '_blank');
    }

    // Validación para que solo acepte números en el input de WhatsApp
    $(document).ready(function() {
        $('#whatsappNumber').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Manejo del menú de acciones (copiado de guia-remision.php)
        $(document).on("click", (e) => {
            if (!$(e.target).closest(".action-menu").length) {
                $(".action-menu").removeClass("show");
            }
        });

        $(document).on("click", ".action-button", function (e) {
            e.stopPropagation();
            const menu = $(this).closest(".action-menu");
            const dropdown = menu.find(".dropdown-actions");

            // Cerrar otros menús
            $(".action-menu").not(menu).removeClass("show");

            // Toggle el menú actual
            menu.toggleClass("show");

            if (menu.hasClass("show")) {
                // Posicionar el dropdown usando coordenadas fijas
                const buttonRect = this.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const dropdownHeight = 200; // Altura estimada del dropdown

                let top = buttonRect.bottom + window.scrollY + 5;
                let left = buttonRect.right + window.scrollX - 180; // Alinear el dropdown con el borde derecho del botón, menos el ancho del dropdown

                // Si no hay espacio abajo, mostrar arriba
                if (buttonRect.bottom + dropdownHeight > viewportHeight) {
                    top = buttonRect.top + window.scrollY - dropdownHeight - 5;
                }

                // Ajustar si se sale por la izquierda del viewport
                if (left < 10) {
                    left = buttonRect.left + window.scrollX;
                }

                // Ajustar si se sale por la derecha del viewport
                if (left + 200 > window.innerWidth) {
                    left = window.innerWidth - 210;
                }

                dropdown.css({
                    'top': top + 'px',
                    'left': left + 'px'
                });
            }
        });

        $(document).on("click", ".dropdown-actions a", function () {
            $(this).closest(".action-menu").removeClass("show");
        });
    });
</script>