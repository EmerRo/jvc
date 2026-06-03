<!-- resources\views\fragment-views\cliente\cotizaciones.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">
<link rel="stylesheet" href="<?= URL::to('public/css/cotizaciones.css') ?>?v=<?= time() ?>">


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

                <div class="card-title-desc d-flex justify-content-end align-items-center flex-wrap gap-2" style="padding: 10px 10px 0 0;">
                    
                    <!-- Dropdown de Opciones para móvil -->
                    <div class="opciones-dropdown">
                        <div class="dropdown">
                            <button class="btn bg-rojo text-white dropdown-toggle" type="button"
                                id="dropdownOpcionesCotizaciones" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bars me-1"></i> Opciones
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOpcionesCotizaciones" style="min-width: 250px;">
                                <li>
                                    <a class="dropdown-item" href="/cotizaciones/add">
                                        <i class="fa fa-plus me-2"></i> Nueva Cotización
                                    </a>
                                </li>
                                <?php if ($_SESSION['rol'] == 1): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="ventas-reporte-mobile">
                                            <i class="fa fa-file-pdf me-2"></i> Reporte de Vendedores
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Botones para desktop -->
                    <?php if ($_SESSION['rol'] == 1): ?>
                        <button id="ventas-reporte" class="btn bg-white text-rojo border-rojo btn-nueva-cotizacion-desktop" style="margin-left: 5px;">
                            <i class="fa fa-file-pdf"></i> Exportar Reporte de Vendedores
                        </button>
                    <?php endif; ?>
                    <a href="/cotizaciones/add" id="folder_btn_nuevo_folder"
                        class="btn bg-rojo bordes text-white button-link btn-nueva-cotizacion-desktop">
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
                <form id="form-reporte-vendedores" action="<?= URL::to('/reporte/cotizaciones/vendedores') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="text" class="form-control" name="rangoFechas" id="rangoFechas" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Vendedores</label>
                            <select name="vendedor" id="vendedor" class="form-control" style="width:100%">
                                <option value="0" data-foto="<?= URL::to('public/assets/images/users/user-4.jpg') ?>">--Todos--</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" id="btn-generar-reporte" class="btn border-rojo text-rojo bg-white">
                                <i class="fa fa-file-excel me-1"></i> Generar
                            </button>
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

<!-- Overlay de carga durante la generación del Excel -->
<div id="overlay-reporte-vendedores" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:200000; align-items:center; justify-content:center; flex-direction:column; gap:18px;">
    <div style="background:#fff; border-radius:14px; padding:28px 36px; box-shadow:0 18px 50px rgba(0,0,0,.25); text-align:center; min-width:280px;">
        <div class="spinner-border text-rojo" role="status" style="width:3rem; height:3rem; color:#CA3438 !important;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <div style="margin-top:14px; font-weight:600; color:#1e293b;">Generando reporte...</div>
        <div id="overlay-reporte-vendedores-msg" style="margin-top:4px; font-size:.85rem; color:#64748b;">Esto puede tardar unos segundos</div>
    </div>
</div>

<!-- Select2 CSS+JS (solo se cargan en este modal) -->
<link rel="stylesheet" href="<?= URL::to('public/assets/libs/select2/css/select2.min.css') ?>?v=<?= time() ?>">
<style>
    /* Avatares del select2 */
    .vendedor-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        object-fit: cover; display: inline-block; vertical-align: middle;
        border: 1px solid #e5e7eb; flex-shrink: 0;
    }
    .vendedor-item { display: flex; align-items: center; gap: 12px; min-height: 44px; }
    .vendedor-item .vendedor-nombre { font-weight: 500; color: #1e293b; }

    /* Contenedor del select */
    .select2-container--default .select2-selection--single {
        height: 48px; padding: 4px 12px; border: 1px solid #ced4da; border-radius: 6px;
        display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal; padding-left: 0; display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px; right: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-width: 6px 5px 0 5px;
    }

    /* Dropdown dentro del modal */
    .select2-container--default .select2-dropdown {
        border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,.18);
        z-index: 10060 !important;  /* encima del backdrop del modal */
    }
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 280px; min-width: 100%; padding: 4px 0;
    }
    .select2-container--default .select2-results__option {
        padding: 0;  /* el padding lo da .vendedor-item */
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #fee2e2; color: #1e293b;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #CA3438; color: #fff;
    }
    .select2-container--default .select2-results__option[aria-selected=true] .vendedor-nombre {
        color: #fff;
    }
</style>
<script src="<?= URL::to('public/assets/libs/select2/js/select2.min.js') ?>?v=<?= time() ?>"></script>

<!-- Modal de Enviar Comprobante (WhatsApp + Email) -->
<?php include __DIR__ . '/modals/modal-enviar-comprobante.php'; ?>

<!-- Modal de Imprimir Comprobante (componente reutilizable) -->
<?php include __DIR__ . '/modals/imprimir-comprobante.php'; ?>

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
            "scrollX": true,   // Habilitar scroll horizontal
            "autoWidth": false, // Deshabilitar auto-ancho
            order: [
                [0, "desc"]
            ],
            language: {
                url: "ServerSide/Spanish.json",
            },
            drawCallback: function() {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    bootstrap.Tooltip.getOrCreateInstance(el);
                });
            },
            columnDefs: [
                {
                    targets: 0, // Columna N°
                    width: "70px",
                    render: function (data, type, row, meta) {
                        // data = número de cotización (ej: 7)
                        // row[8] = ID de cotización (ej: 56)
                        const numeroFormateado = 'COT-' + String(data).padStart(2, '0');
                        const cotizacionId = row[8]; // Obtener el ID desde la columna 8
                        return `<a href="/r/cotizaciones/reporte/${cotizacionId}" target="_blank" class="text-primary" style="text-decoration: none;">${numeroFormateado}</a>`;
                    }
                },
                {
                    targets: 1, // Columna Fecha
                    width: "90px"
                },
                {
                    targets: 2, // Columna Cliente
                    width: "200px",
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data && data.length > 40) {
                            return '<span title="' + data + '">' + data.substr(0, 40) + '...</span>';
                        }
                        return data;
                    }
                },
                {
                    targets: 3, // Sub. Total
                    width: "85px"
                },
                {
                    targets: 4, // IGV
                    width: "85px"
                },
                {
                    targets: 5, // Total
                    width: "85px"
                },
                {
                    targets: 6, // Vendedor
                    width: "100px"
                },
                
                
                {
                    targets: 7, // Estado
                    width: "100px",
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
                    targets: 8, // Vender
                    width: "60px",
                    render(data, type, row) {
                        if (row[7] == '1') {
                            const label = row[13] ? `Venta N° ${row[13]}` : 'Ya vendida';
                            return `<button class="btn btn-secondary btn-sm" disabled
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="${label}">
                                        <i class="fa fa-align-justify"></i>
                                    </button>`;
                        }
                        return `<a href="/ventas/productos?coti=${data}" class="btn btn-success btn-sm button-link"><i class="fa fa-align-justify"></i></a>`;
                    }
                },

                
                {
                    targets: 9, // Guía
                    width: "60px",
                    render(data) {
                        return `<a href="/guia/remision/registrar?coti=${data}" class="btn btn-success btn-sm button-link"><i class="fa fa-clipboard"></i></a>`;
                    }
                },
                {
                    targets: 10, // Acciones
                    width: "80px",
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return `
                        <div class="action-menu">
                            <button type="button" class="action-button">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-actions">
                                <a href="javascript:void(0)" onclick="abrirEnviarCotizacion(${data})" title="Enviar Cotización">
                                    <i class="fas fa-paper-plane text-primary"></i> Enviar Cotización
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
        
        // Manejar clic en reporte tanto para desktop como mobile
        $("#ventas-reporte, #ventas-reporte-mobile").on('click', function () {
            const $btn = $(this);
            $btn.prop('disabled', true);
            $.ajax({
                type: "POST",
                url: _URL + "/ajs/cotizaciones/getvendedores",
                success: function (response) {
                    $('#rangoFechas').daterangepicker({
                        opens: 'left',
                        locale: {
                            format: 'YYYY-MM-DD',
                            applyLabel: 'Aplicar',
                            cancelLabel: 'Cancelar',
                            fromLabel: 'Desde',
                            toLabel: 'Hasta',
                            customRangeLabel: 'Rango personalizado',
                            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
                        }
                    });

                    let data = JSON.parse(response);
                    const $select = $('#vendedor');

                    // Resetear
                    $select.empty();
                    $select.append(new Option('--Todos--', '0', false, false));
                    $select.find('option[value="0"]').attr('data-foto', _URL + '/public/assets/images/users/user-4.jpg');

                    data.forEach(function (d) {
                        const opt = new Option(d.nombres, d.usuario_id, false, false);
                        $(opt).attr('data-foto', d.foto_url || (_URL + '/public/assets/images/users/user-4.jpg'));
                        $select.append(opt);
                    });

                    // Inicializar Select2 con template HTML (avatar + nombre)
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                    $select.select2({
                        width: '100%',
                        placeholder: 'Seleccione un vendedor...',
                        allowClear: false,
                        minimumResultsForSearch: 10,  // con 4 vendedores no hace falta search
                        dropdownParent: $('#ventas-reporte-bs'),  // renderiza DENTRO del modal
                        dropdownAutoWidth: true,
                        templateResult: function (option) {
                            if (!option.id) return option.text;
                            const foto = $(option.element).attr('data-foto') || (_URL + '/public/assets/images/users/user-4.jpg');
                            return $(`
                                <div class="vendedor-item">
                                    <img class="vendedor-avatar" src="${foto}" alt=""
                                         onerror="this.onerror=null;this.src='${_URL}/public/assets/images/users/user-4.jpg';" />
                                    <span class="vendedor-nombre">${option.text}</span>
                                </div>
                            `);
                        },
                        templateSelection: function (option) {
                            if (!option.id) return option.text;
                            const foto = $(option.element).attr('data-foto') || (_URL + '/public/assets/images/users/user-4.jpg');
                            return $(`
                                <span class="vendedor-item">
                                    <img class="vendedor-avatar" src="${foto}" alt=""
                                         onerror="this.onerror=null;this.src='${_URL}/public/assets/images/users/user-4.jpg';" />
                                    <span class="vendedor-nombre">${option.text}</span>
                                </span>
                            `);
                        },
                        escapeMarkup: function (m) { return m; }
                    });

                    $btn.prop('disabled', false);
                    $('#ventas-reporte-bs').modal('show');
                },
                error: function (response) {
                    console.log(response);
                    $btn.prop('disabled', false);
                }
            });
        });

        // Submit del formulario de reporte con fetch + overlay + descarga
        const $overlay = $('#overlay-reporte-vendedores');
        const $overlayMsg = $('#overlay-reporte-vendedores-msg');
        const $btnGenerar = $('#btn-generar-reporte');
        const $formReporte = $('#form-reporte-vendedores');

        $formReporte.on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const url = $(form).attr('action');

            // Mostrar overlay
            $overlayMsg.text('Generando archivo Excel...');
            $overlay.css('display', 'flex');
            $btnGenerar.prop('disabled', true);

            fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function (resp) {
                if (!resp.ok) {
                    throw new Error('HTTP ' + resp.status);
                }
                return resp.blob().then(function (blob) {
                    return { blob: blob, resp: resp };
                });
            })
            .then(function (data) {
                const blob = data.blob;
                const contentType = data.resp.headers.get('Content-Type') || '';
                const contentDisposition = data.resp.headers.get('Content-Disposition') || '';

                // Si el servidor devolvió un JSON de error (no es xlsx)
                if (contentType.indexOf('application/json') !== -1 || contentType.indexOf('text/html') !== -1) {
                    return blob.text().then(function (text) {
                        throw new Error(text || 'Error desconocido al generar el reporte');
                    });
                }

                // Extraer nombre del archivo del header
                let filename = 'reporte_cotizaciones_vendedores.xlsx';
                const m = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
                if (m && m[1]) {
                    filename = m[1].replace(/['"]/g, '');
                }

                // Disparar descarga
                const urlBlob = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = urlBlob;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(function () { URL.revokeObjectURL(urlBlob); }, 1500);

                // Cerrar modal y overlay
                $('#ventas-reporte-bs').modal('hide');
                if (typeof alertExito === 'function') {
                    alertExito('Reporte generado correctamente');
                }
            })
            .catch(function (err) {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al generar',
                        text: err.message || 'No se pudo generar el reporte'
                    });
                } else {
                    alert('Error: ' + (err.message || 'no se pudo generar el reporte'));
                }
            })
            .finally(function () {
                $overlay.hide();
                $btnGenerar.prop('disabled', false);
            });
        });
    });

    function eliminarCotizacion(cod) {
        console.log(cod)
        _ajax("/ajs/cotizaciones/del", "POST", { cod }, function (resp) {
            tabla.ajax.reload();
        })
    }

    var cotizacionParaImprimir = null;

    // Función para abrir modal de enviar cotización (WhatsApp + Email)
    function abrirEnviarCotizacion(cotizacionId) {
        const linkReporte = `${_URL}/r/cotizaciones/reporte/${cotizacionId}`;
        const numeroCotizacion = `COT-${String(cotizacionId).padStart(2, '0')}`;

        abrirModalEnviarComprobante({
            tipo: 'cotizacion',
            id: cotizacionId,
            numero: numeroCotizacion,
            link: linkReporte,
            linkDescarga: linkReporte,
            nombreArchivo: `cotizacion_${cotizacionId}.pdf`,
            email: '',
            telefono: ''
        });
    }

    function abrirModalImprimir(cotizacionId) {
        cotizacionParaImprimir = cotizacionId;

        // Configurar las URLs de los diferentes formatos (usando IDs del componente)
        $('#ce-t-a4').attr('href', `${_URL}/r/cotizaciones/reporte/${cotizacionId}`);
        $('#ce-t-a4-m').attr('href', `${_URL}/r/cotizaciones/reporte-media-a4/${cotizacionId}`);
        $('#ce-t-8cm').attr('href', `${_URL}/r/cotizaciones/reporte-voucher-8cm/${cotizacionId}`);
        $('#ce-t-5_6cm').attr('href', `${_URL}/r/cotizaciones/reporte-voucher-5-6cm/${cotizacionId}`);

        $('#modalImprimirComprobante').modal('show');
    }

    // Manejo del menú de acciones
    $(document).ready(function() {
        // Manejo del menú de acciones mejorado para móvil
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
                const buttonRect = this.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const dropdownWidth = 200;
                const dropdownHeight = 220;

                // En móvil, centrar el dropdown
                if (viewportWidth <= 768) {
                    dropdown.css({
                        'position': 'fixed',
                        'left': '50%',
                        'top': '50%',
                        'transform': 'translate(-50%, -50%)',
                        'width': '90%',
                        'max-width': '320px'
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

        $(document).on("click", ".dropdown-actions a", function () {
            $(this).closest(".action-menu").removeClass("show");
        });
    });
</script>