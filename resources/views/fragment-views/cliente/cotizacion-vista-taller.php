<!-- resources\views\fragment-views\cliente\cotizacion-vista-taller.php -->
<style>
.page-title {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
}

.badge-tipo {
    font-size: 11px !important;
    padding: 6px 12px !important;
    font-weight: 500 !important;
    min-width: 100px !important;
    display: inline-block !important;
    text-align: center !important;
}

/* Estilos para dropdown personalizado en tabla (copiado de taller.php) */
.action-button {
    background: #0d6efd;
    border: 1px solid #0d6efd;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 12px;
    font-weight: 400;
}

.action-button:hover {
    background-color: #0a58ca;
    border-color: #0a58ca;
    color: white;
}

.dropdown-actions {
    position: fixed;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    min-width: 180px;
    max-width: 250px;
    display: none;
    max-height: 300px;
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

.dropdown-actions .divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 4px 0;
}

/* Muestra el menú cuando tiene la clase show */
.action-menu.show .dropdown-actions {
    display: block;
}
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title">COTIZACIONES DEL TALLER</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Taller cotizaciones</a></li>
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
                
                <!-- Filtro simple -->
                <div class="mb-3 d-flex align-items-center" style="width: fit-content;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Filtrar</span>
                        <i class="fas fa-filter text-muted"></i>
                        <select id="filtroTipo" class="form-select form-select-sm"
                            style="width: auto; min-width: 150px;">
                            <option value="">Todos</option>
                            <option value="ORD TRABAJO">Orden de Trabajo</option>
                            <option value="ORD SERVICIO">Orden de Servicio</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="datatable-c" class="table table-bordered dt-responsive nowrap text-center table-sm" 
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Vender</th>
                                <th>Guía</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal WhatsApp -->
<div class="modal fade" id="modal-whatsapp" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="whatsappModalLabel">
                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="whatsapp-telefono" class="form-label">Número de teléfono</label>
                    <input type="text" class="form-control" id="whatsapp-telefono"
                           placeholder="Ej: 51987654321 (incluir código de país)"
                           maxlength="15">
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Incluir código de país. Ej: 51987654321 para Perú
                    </div>
                </div>
                <input type="hidden" id="whatsapp-cotizacion-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="enviarWhatsApp()">
                    <i class="fab fa-whatsapp"></i> Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    function tes() {
        // Función vacía por ahora
    }
    
    var tabla;
    $(document).ready(function () {
        tabla = $("#datatable-c").DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "sAjaxSource": _URL + "/data/taller/cotizaciones/lista/ss",
            "language": {
                "url": "ServerSide/Spanish.json"
            },
            order: [
                [0, "desc"]  // Ordenar por número (columna 0) descendente - más recientes primero
            ],
            
            columnDefs: [
                // Columna # correlativa
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        // Calcular el número correlativo basado en la página actual
                        var pageInfo = tabla.page.info();
                        return pageInfo.start + meta.row + 1;
                    }
                },
                // Columna fecha - índice 1 (data[1])
                {
                    targets: 1,
                    render: function (data, type, row, meta) {
                        return data; // Mostrar la fecha tal como viene
                    }
                },
                // Columna cliente - índice 2 (data[2])
                {
                    targets: 2,
                    render: function (data, type, row, meta) {
                        return data; // Mostrar el documento/cliente tal como viene
                    }
                },
                // Columna vendedor - índice 3 (data[3])
                {
                    targets: 3,
                    render: function (data, type, row, meta) {
                        return data; // Mostrar el vendedor tal como viene
                    }
                },
                // Columna tipo - índice 4 (data[4]) - tipo_origen
                {
                    targets: 4,
                    render: function (data, type, row, meta) {
                        // Verificar diferentes posibles valores para orden de trabajo
                        if (data && (data.toString().includes('TRABAJO') || data.toString().includes('trabajo'))) {
                            return `<span class="badge bg-warning badge-tipo">ORD TRABAJO</span>`;
                        } 
                        // Verificar diferentes posibles valores para servicio
                        else if (data && (data.toString().includes('SERVICIO') || data.toString().includes('servicio') || data.toString().includes('Servicio'))) {
                            return `<span class="badge bg-success badge-tipo">ORD SERVICIO</span>`;
                        } 
                        // Si no coincide con ninguno, mostrar el valor original
                        else {
                            return `<span class="badge bg-secondary badge-tipo">${data || 'N/A'}</span>`;
                        }
                    }
                },
                // Columna estado - se renderiza por defecto (asumo que viene como texto)
                {
                    targets: 5,
                    render: function (data, type, row, meta) {
                        // Aquí puedes agregar lógica para mostrar el estado como badge si es necesario
                        if (data == '1') {
                            return '<span class="badge bg-success">Vendido</span>';
                        } else if (data == '2') {
                            return '<span class="badge bg-warning">Facturado</span>';
                        } else {
                            return '<span class="badge bg-danger">No Vendido</span>';
                        }
                    }
                },
                // Columna vender - índice 6 (data[7] - primer cotizacion_id)
                {
                    targets: 6,
                    render: function (data, type, row, meta) {
                        // Usar data[7] que contiene cotizacion_id
                        var cotizacion_id = row[7];
                        return `<a href="/ventas/productos?coti-taller=${cotizacion_id}" class="btn btn-success btn-sm button-link"><i class="fa fa-align-justify"></i></a>`;
                    }
                },
                // Columna guía - índice 7 (data[7] - segundo cotizacion_id)
                {
                    targets: 7,
                    render: function (data, type, row, meta) {
                        // data[6] contiene guia_numero, data[7] contiene cotizacion_id
                        var guia_numero = row[6];
                        var cotizacion_id = row[7];
                        
                        if (guia_numero && guia_numero !== '' && guia_numero !== null) {
                            // Ya tiene guía - mostrar ícono verde con tooltip
                            return `<button class="btn btn-success btn-sm" title="Ya tiene Guía de Remisión: ${guia_numero}" disabled>
                                        <i class="fa fa-clipboard"></i>
                                    </button>`;
                        } else {
                            // No tiene guía - mostrar botón normal para crear
                            return `<a href="/guia/remision/registrar?coti-taller=${cotizacion_id}" class="btn btn-primary btn-sm button-link" title="Crear Guía de Remisión">
                                        <i class="fa fa-clipboard"></i>
                                    </a>`;
                        }
                    }
                },
                // Columna de acciones - índice 8 (usa la primera columna cotizacion_id en posición 7)
                {
                    targets: 8,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        // Con el dataset actual, el cotizacion_id está en row[7]
                        var cotizacionId = row[7];
                        return `
                            <div class="btn-group" role="group">
                                <a href="/edt/coti/taller?id=${cotizacionId}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <div class="action-menu">
                                    <button type="button" class="action-button" title="Reportes">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                    <div class="dropdown-actions">
                                        <a href="${_URL + '/r/taller/reporte/' + cotizacionId}" target="_blank">
                                            <i class="fas fa-file-pdf text-danger"></i> PDF A4
                                        </a>
                                        <div class="divider"></div>
                                        <a href="javascript:void(0)" onclick="abrirModalWhatsApp(${cotizacionId})">
                                            <i class="fab fa-whatsapp text-success"></i> Enviar WhatsApp
                                        </a>
                                        <div class="divider"></div>
                                        <a href="javascript:void(0)" onclick="generarReporteInventarioPdf(${cotizacionId})">
                                            <i class="fas fa-file-pdf text-danger"></i> Reporte Inventario PDF
                                        </a>
                                        <a href="javascript:void(0)" onclick="generarReporteInventarioExcel(${cotizacionId})">
                                            <i class="fas fa-file-excel text-success"></i> Reporte Inventario Excel
                                        </a>
                                    </div>
                                </div>
                                <button onclick="eliminarCotizacion(${cotizacionId})" type="button" class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Evento para el filtro de tipo
        $('#filtroTipo').on('change', function() {
            var valorFiltro = this.value;
            
            if (valorFiltro === '') {
                // Mostrar todos los registros
                tabla.column(4).search('').draw();
            } else {
                // Filtrar por el tipo seleccionado
                tabla.column(4).search(valorFiltro).draw();
            }
        });

        tes();
    });

    function eliminarCotizacion(cod) {
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Usar jQuery AJAX en lugar de _ajax para mejor control
                $.ajax({
                    url: _URL + "/ajs/taller/cotizaciones/del",
                    type: "POST",
                    data: { cod: cod },
                    dataType: 'json',
                    success: function(resp) {
                        console.log("Respuesta del servidor:", resp);
                        
                        if (resp.success === true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'La cotización ha sido eliminada correctamente.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Recargar la tabla
                            if (tabla && typeof tabla.ajax !== 'undefined') {
                                tabla.ajax.reload(null, false); // false para mantener la paginación
                            } else {
                                // Si hay problemas con la recarga, recargar la página
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resp.message || 'No se pudo eliminar la cotización'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la petición:", {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor. Inténtelo nuevamente.'
                        });
                    }
                });
            }
        });
    }

    // Función para abrir modal de WhatsApp
    function abrirModalWhatsApp(cotizacionId) {
        $('#whatsapp-cotizacion-id').val(cotizacionId);
        $('#modal-whatsapp').modal('show');
    }

    // Función para enviar por WhatsApp
    function enviarWhatsApp() {
        const telefono = $('#whatsapp-telefono').val().trim();
        const cotizacionId = $('#whatsapp-cotizacion-id').val();

        if (!telefono) {
            Swal.fire({
                icon: 'warning',
                title: 'Teléfono requerido',
                text: 'Por favor ingrese un número de teléfono'
            });
            return;
        }

        // Construir URL del PDF
        const pdfUrl = `${_URL}/r/taller/reporte/${cotizacionId}`;

        // Construir mensaje de WhatsApp
        const mensaje = `Cotización N° ${cotizacionId}%0A${encodeURIComponent(pdfUrl)}`;
        const whatsappUrl = `https://wa.me/${telefono}?text=${mensaje}`;

        // Abrir WhatsApp
        window.open(whatsappUrl, '_blank');

        // Cerrar modal
        $('#modal-whatsapp').modal('hide');

        // Limpiar campo
        $('#whatsapp-telefono').val('');
    }

    // Función para generar reporte inventario PDF
    function generarReporteInventarioPdf(cotizacionId) {
        const url = `${_URL}/r/taller/inventario/${cotizacionId}`;
        window.open(url, '_blank');
    }

    // Función para generar reporte inventario Excel
    function generarReporteInventarioExcel(cotizacionId) {
        // Mostrar loading
        Swal.fire({
            title: 'Generando reporte...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Generar y descargar Excel
        const url = `${_URL}/r/taller/inventario/excel/${cotizacionId}`;

        // Crear enlace temporal para descarga
        const link = document.createElement('a');
        link.href = url;
        link.download = `reporte_inventario_${cotizacionId}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Cerrar loading después de un momento
        setTimeout(() => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Reporte generado',
                text: 'El archivo Excel se ha descargado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1500);
    }

    // Sistema de dropdown personalizado (copiado de taller.php)
    $(document).on("click", ".action-button", function (e) {
        e.stopPropagation();
        const button = $(this);
        const menu = button.closest(".action-menu");
        const dropdown = menu.find(".dropdown-actions");

        // Cerrar otros menús
        $(".action-menu").not(menu).removeClass("show");

        if (menu.hasClass("show")) {
            menu.removeClass("show");
        } else {
            // Calcular posición para position: fixed
            const buttonOffset = button.offset();
            const buttonHeight = button.outerHeight();
            const buttonWidth = button.outerWidth();
            const dropdownWidth = 180; // min-width definido en CSS

            // Posición por defecto (debajo del botón, alineado a la derecha)
            let top = buttonOffset.top + buttonHeight + 5;
            let left = buttonOffset.left + buttonWidth - dropdownWidth;

            // Verificar si se sale por la derecha de la pantalla
            if (left < 10) {
                left = buttonOffset.left; // Alinear a la izquierda del botón
            }

            // Verificar si se sale por abajo de la pantalla
            const dropdownHeight = 250; // altura estimada
            if (top + dropdownHeight > $(window).height()) {
                top = buttonOffset.top - dropdownHeight - 5; // Mostrar arriba del botón
            }

            // Aplicar posición
            dropdown.css({
                'top': top + 'px',
                'left': left + 'px'
            });

            menu.addClass("show");
        }
    });

    // Cerrar dropdown al hacer click en una opción
    $(document).on("click", ".dropdown-actions a", function () {
        $(this).closest(".action-menu").removeClass("show");
    });

    // Cerrar dropdown al hacer click fuera
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".action-menu").length) {
            $(".action-menu").removeClass("show");
        }
    });
</script>

<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>