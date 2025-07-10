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
                            <label class="form-label mb-0 fw-bold">Filtrar por:</label>
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
                                <th width="10%"></th>
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
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu<?= $fila['id_guia_remision'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-bars"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu<?= $fila['id_guia_remision'] ?>">
                                                <li>
                                                    <a class="dropdown-item" href="<?= URL::to('files/facturacion/xml/' . $fila['ruc_empresa'] . '/' . $fila['nom_guia_xml'] . '.xml') ?>" target="_blank">
                                                        <i class="fa fa-file me-2"></i> Archivo XML
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item whatsapp-share"
                                                        data-pdf-url="<?php echo $pdf_url ?>"
                                                        data-guide="<?php echo htmlspecialchars($doc_guia) ?>"
                                                        data-client="<?php echo htmlspecialchars($fila['datos']) ?>">
                                                        <i class="fab fa-whatsapp me-2"></i> Enviar por WhatsApp
                                                    </button>
                                                </li>
                                               <li>
    <?php 
    // Determinar el tipo de documento y el texto a mostrar
    $documento = $fila['documento_cliente'];
    $es_ruc = (strlen($documento) == 11 && is_numeric($documento));
    $texto_crear = $es_ruc ? 'Crear Factura' : 'Crear Boleta';
    $icono_crear = $es_ruc ? 'fas fa-file-invoice' : 'fas fa-receipt';
    ?>
    <button type="button" class="dropdown-item" onclick="crearDocumento(<?php echo $fila['id_guia_remision']; ?>, '<?php echo $es_ruc ? 'factura' : 'boleta'; ?>')">
        <i class="<?php echo $icono_crear; ?> me-2"></i> <?php echo $texto_crear; ?>
    </button>
</li>

                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="duplicarGuia(<?php echo $fila['id_guia_remision']; ?>)">
                                                        <i class="fas fa-copy me-2"></i> Duplicar Guía
                                                    </button>
                                                </li>
                                            </ul>
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

<style>
.modal-content {
    border: none;
}

.modal-header {
    border-bottom: none;
}

.btn-close:focus {
    box-shadow: none;
}

#whatsappNumber:focus {
    box-shadow: none;
    border-color: #0d6efd;
}

.input-group-text {
    background-color: #f8f9fa;
    border-radius: 8px 0 0 8px;
}

/* Estilos para la tabla y el responsive */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Estilos para el menú desplegable */
.dropdown-menu {
    min-width: 200px;
    padding: 0.5rem 0;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    position: absolute;
    transform: translate3d(0px, 40px, 0px);
    top: 0;
    left: auto;
    right: 0;
    will-change: transform;
}
.dropdown {
    position: relative;
}


.dropdown-item {
    padding: 0.5rem 1rem;
    color: #333;
    transition: background-color 0.2s;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
}

/* Asegurar que el menú desplegable no se corte */
.dropdown-menu-end {
    right: 0;
    left: auto;
}
.table-responsive {
    overflow: visible;
}
.card {
    overflow: visible;
}

/* Ajustes para la tabla */
.table th, .table td {
    white-space: nowrap;
    vertical-align: middle;
}

/* Ajuste para dispositivos móviles */
@media (max-width: 768px) {
    .dropdown-menu {
        position: fixed;
        top: auto;
        left: 50%;
        transform: translateX(-50%);
        bottom: 20px;
        width: 90%;
        max-width: 300px;
    }
}

/* ✅ NUEVO: Estilos para el filtro */
.form-select {
    border: 1px solid #CA3438;
    border-radius: 8px;
}

.form-select:focus {
    border-color: #CA3438;
    box-shadow: 0 0 0 0.2rem rgba(202, 52, 56, 0.25);
}

/* ✅ CORREGIDO: N/A sin fondo gris */
.text-muted {
    color: #6c757d !important;
    background: none !important;
}
</style>

<script>
let currentPdfUrl = '';
let currentGuideNumber = '';
let currentClientName = '';
let tabla; // Variable global para la tabla

$(document).ready(function() {
    // Inicializar DataTable
    tabla = $("#datatable").DataTable({
        responsive: true,
        order: [[1, "desc"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [{
            targets: '_all',
            className: 'text-center'
        }]
    });

    // ✅ CORREGIDO: Manejar cambio de filtro SIN refrescar página
    $('#filtroGuias').on('change', function() {
        const filtro = $(this).val();
        
        // Mostrar/ocultar filas según el filtro seleccionado
        if (filtro === 'todos') {
            // Mostrar todas las filas
            tabla.rows().nodes().to$().show();
        } else {
            // Ocultar todas las filas primero
            tabla.rows().nodes().to$().hide();
            
            // Mostrar solo las filas que coinciden con el filtro
            tabla.rows().nodes().to$().filter(function() {
                return $(this).data('tipo') === filtro;
            }).show();
        }
        
        // Redibujar la tabla para actualizar la paginación
        tabla.draw();
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
        currentPdfUrl = $(this).data('pdf-url');
        currentGuideNumber = $(this).data('guide');
        currentClientName = $(this).data('client');
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
            '&text=' + encodeURIComponent('Guía de remisión ' + currentGuideNumber + ' para ' + currentClientName + ': ' + currentPdfUrl);

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