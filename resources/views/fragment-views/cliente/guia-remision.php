<!-- resources\views\fragment-views\cliente\guia-remision.php-->
<?php
require_once "app/models/GuiaRemision.php";
require_once "app/models/Varios.php";

$c_guia = new GuiaRemision();
$c_varios = new Varios();

$c_guia->setIdEmpresa($_SESSION['id_empresa']);
?>
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css')  ?>?v=<?= time() ?>">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* Ajustes específicos para la tabla de guías */
#datatable th:last-child,
#datatable td:last-child {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    text-align: center !important;
}

/* Estilos para el table responsive */
.table-responsive {
    overflow: visible !important;
}

.card {
    overflow: visible !important;
    border-radius: 20px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
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
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    min-width: 200px;
    z-index: 1000;
    display: none;
    margin-top: 5px;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

/* Para filas cerca del final, abrir hacia arriba */
tr:nth-last-child(-n+3) .dropdown-actions {
    top: auto;
    bottom: 100%;
    margin-top: 0;
    margin-bottom: 5px;
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

<!-- WhatsApp Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
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
                        <input type="tel" class="form-control form-control-lg" id="whatsappNumber" placeholder="Ingrese número" maxlength="9" style="border-radius: 0 8px 8px 0;">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="sendWhatsappBtn">
                        <i class="fab fa-whatsapp me-2"></i>Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-12 text-center mb-3">
            <h6 class="page-title ">GUIA DE REMISIÓN</h6>
        </div>
        <div class="col-md-8">
            <div class="clearfix">
                <ol class="breadcrumb m-0 float-start" style="background: transparent;">
                    <li class="breadcrumb-item"><a href="javascript: void(0);" style="color: #718096; text-decoration: none;">Facturación</a></li>
                    <li class="breadcrumb-item active " aria-current="page" style="font-weight: 500; color: #CA3438;">Guía Remisión</li>
                </ol>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <!-- ✅ NUEVO: Filtros y botones -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                          <span class="text-muted">Filtrar</span>
                                <i class="fas fa-filter text-muted"></i>
                            <select id="filtroGuias" class="form-select" style="width: auto;">
                                <option value="todos">Todas las Guías</option>
                                <option value="facturas">Guías de Facturas</option>
                                <option value="cotizaciones">Guías de Cotizaciones</option>
                                <option value="manuales">Guías Manuales</option>
                            </select>
                        </div>
                    </div>
                       
               
                     
                    <div class="col-md-6">
                        <div class="text-end">
                            <a href="/guia/remision/registrar" class="btn border-rojo button-link" >
                                <i class="fa fa-plus me-1"></i> Crear Guía de Remisión
                            </a>
                            <a href="/guia/remision/manual/registrar" class="btn bg-rojo text-white bordes button-link" >
                                <i class="fa fa-plus me-1"></i> Crear Guía de Remisión Manual
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap table-sm text-center" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <!-- <th width="5%">Item</th> -->
                                <th width="15%">Documento</th>
                                <th width="10%">Fecha</th>
                                <th width="20%">Cliente</th>
                                <th width="15%">Factura</th>
                                <th width="10%">Sunat</th>
                                <th width="5%">PDF</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $c_guia->verFilas();
                            $filas = [];

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $filas[] = $row;
                                }
                            }

                            if (!empty($filas)) {
                                usort($filas, function($a, $b) {
                                    return strtotime($b['fecha_emision']) - strtotime($a['fecha_emision']);
                                });
                            }

                            // $item = 1;
                            foreach ($filas as $fila) {
                                $doc_guia = "GR | " . $fila['serie'] . "-" . $c_varios->zerofill($fila['numero'], 4);
                                
                                // ✅ MEJORADO: Solo mostrar factura real o N/A
                                if ($fila['doc_venta'] !== 'N/A' && !empty($fila['serie_venta']) && !empty($fila['numero_venta'])) {
                                    $doc_venta = $fila['doc_venta'] . " | " . $fila['serie_venta'] . "-" . $c_varios->zerofill($fila['numero_venta'], 4);
                                } else {
                                    $doc_venta = "N/A";
                                }
                                
                                $pdf_url = URL::to('/guia/remision/pdf/' . $fila['id_guia_remision'] . '/' . $fila['nom_guia_xml']);
                                ?>
                                <tr data-tipo="<?php echo $fila['tipo_guia']; ?>">
                                    <!-- <td><?php echo $item ?></td> -->
                                    <td><a target="_blank" href="<?php echo $pdf_url ?>"><?php echo $doc_guia ?></a></td>
                                    <td><?php echo $c_varios->fecha_mysql_web($fila['fecha_emision']) ?></td>
                                    <td><?php echo $fila['datos'] ?></td>
                                    <td>
                                        <?php if ($doc_venta === 'N/A'): ?>
                                            <span class="text-muted">N/A</span>
                                        <?php else: ?>
                                            <?php echo $doc_venta ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($fila['enviado_sunat'] == '1'): ?>
                                            <span class="badge bg-success">Enviado</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pendiente</span>
                                            <i data-item="<?php echo $fila['id_guia_remision'] ?>" class="btn-send-sunat btn-sm btn btn-info fas fa-location-arrow"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $pdf_url ?>" target="_blank">
                                            <i class="fas fa-file-pdf fa-lg" style="color: #f40f02;"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="action-menu">
                                            <button type="button" class="action-button">
                                                <i class="fas fa-bars"></i>
                                            </button>
                                            <div class="dropdown-actions">
                                                <a href="<?= URL::to('files/facturacion/xml/' . $fila['ruc_empresa'] . '/' . $fila['nom_guia_xml'] . '.xml') ?>" target="_blank">
                                                    <i class="fa fa-file text-info"></i> Archivo XML
                                                </a>
                                                <a class="whatsapp-share"
                                                   data-pdf-url="<?php echo $pdf_url ?>"
                                                   data-guide="<?php echo htmlspecialchars($doc_guia) ?>"
                                                   data-client="<?php echo htmlspecialchars($fila['datos']) ?>">
                                                    <i class="fab fa-whatsapp text-success"></i> Enviar por WhatsApp
                                                </a>
                                                <div class="divider"></div>
                                                <!-- Mostrar ambos botones: Crear Factura y Crear Boleta -->
                                                <a onclick="crearDocumento(<?php echo $fila['id_guia_remision']; ?>, 'factura')">
                                                    <i class="fas fa-file-invoice text-primary"></i> Crear Factura
                                                </a>
                                                <a onclick="crearDocumento(<?php echo $fila['id_guia_remision']; ?>, 'boleta')">
                                                    <i class="fas fa-receipt text-success"></i> Crear Boleta
                                                </a>
                                                <a onclick="duplicarGuia(<?php echo $fila['id_guia_remision']; ?>)">
                                                    <i class="fas fa-copy text-warning"></i> Duplicar Guía
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                // $item++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
// ✅ SOLUCIÓN: Verificar si las variables ya existen antes de declararlas
if (typeof window.currentPdfUrl === 'undefined') {
    window.currentPdfUrl = '';
}
if (typeof window.currentGuideNumber === 'undefined') {
    window.currentGuideNumber = '';
}
if (typeof window.currentClientName === 'undefined') {
    window.currentClientName = '';
}
if (typeof window.tablaGuias === 'undefined') {
    window.tablaGuias = null;
}

$(document).ready(function() {
    // Limpiar DataTable existente si existe
    if (window.tablaGuias && $.fn.DataTable.isDataTable('#datatable')) {
        window.tablaGuias.destroy();
        $('#datatable').empty();
    }

    // Inicializar DataTable
    window.tablaGuias = $("#datatable").DataTable({
        responsive: true,
        order: [[1, "desc"]],
        language: {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "infoThousands": ",",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        columnDefs: [{
            targets: '_all',
            className: 'text-center'
        }]
    });

    // Manejo del menú de acciones (igual que en ventas.php)
    $(document).on("click", (e) => {
        if (!$(e.target).closest(".action-menu").length) {
            $(".action-menu").removeClass("show");
        }
    });

    $(document).on("click", ".action-button", function (e) {
        e.stopPropagation();
        const menu = $(this).closest(".action-menu");
        $(".action-menu").not(menu).removeClass("show");
        menu.toggleClass("show");
    });

    $(document).on("click", ".dropdown-actions a", function () {
        $(this).closest(".action-menu").removeClass("show");
    });

    // ✅ CORREGIDO: Manejar cambio de filtro SIN refrescar página
    $('#filtroGuias').on('change', function() {
        const filtro = $(this).val();
        
        // Mostrar/ocultar filas según el filtro seleccionado
        if (filtro === 'todos') {
            // Mostrar todas las filas
            window.tablaGuias.rows().nodes().to$().show();
        } else {
            // Ocultar todas las filas primero
            window.tablaGuias.rows().nodes().to$().hide();
            
            // Mostrar solo las filas que coinciden con el filtro
            window.tablaGuias.rows().nodes().to$().filter(function() {
                return $(this).data('tipo') === filtro;
            }).show();
        }
        
        // Redibujar la tabla para actualizar la paginación
        window.tablaGuias.draw();
    });

    // Manejar el envío a SUNAT
    $("#datatable").on("click", ".btn-send-sunat", function(evt) {
        const cod = $(evt.currentTarget).attr('data-item');
        $("#loader-menor").show();
        _ajax("/ajs/send/sunat/guiaremision", "POST", { cod },
            function(resp) {
                if (resp.res) {
                    alertExito("Enviado a la sunat")
                        .then(function() {
                            location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: "Alerta",
                        html: resp.msg,
                    });
                }
            }
        );
    });

    // Manejar envío por WhatsApp
    $(document).on('click', '.whatsapp-share', function() {
        window.currentPdfUrl = $(this).data('pdf-url');
        window.currentGuideNumber = $(this).data('guide');
        window.currentClientName = $(this).data('client');
        $('#whatsappNumber').val('');
        $('#whatsappModal').modal('show');
    });

    $('#sendWhatsappBtn').click(function() {
        const phoneNumber = $('#whatsappNumber').val().trim();

        if (!phoneNumber) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor ingrese un número de teléfono'
            });
            return;
        }

        if (phoneNumber.length !== 9) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'El número debe tener 9 dígitos'
            });
            return;
        }

        const whatsappUrl = 'https://api.whatsapp.com/send?phone=51' + phoneNumber +
            '&text=' + encodeURIComponent('Guía de remisión ' + window.currentGuideNumber + ' para ' + window.currentClientName + ': ' + window.currentPdfUrl);

        $('#whatsappModal').modal('hide');
        window.open(whatsappUrl, '_blank');
    });

    $('#whatsappNumber').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

// Cambiar el nombre de la función de crearFactura a crearDocumento
function crearDocumento(idGuia, tipoDoc) {
    localStorage.setItem('desde', 'coti_guia');
    localStorage.setItem('datosGuiaRemosion', idGuia);
    localStorage.setItem('tipoDocumento', tipoDoc); // Opcional: guardar el tipo para uso posterior
    window.location.href = _URL + '/ventas/productos?guia=' + idGuia;
}

// Mantener la función original por compatibilidad (opcional)
function crearFactura(idGuia) {
    crearDocumento(idGuia, 'factura');
}

function duplicarGuia(idGuia) {
    Swal.fire({
        icon: 'info',
        title: 'Duplicar Guía',
        text: '¿Deseas duplicar esta guía?',
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la vista de duplicación con el ID de la guía
            window.location.href = _URL + '/guia/remision/duplicada?id=' + idGuia;
        }
    });
}
</script>