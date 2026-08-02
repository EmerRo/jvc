<style>
    .modal-header.bg-danger {
        background-color: #dc3545 !important;
    }

    .correlativo {
        font-size: 0.9rem;
    }

    .correlativo-grande {
        font-size: 1.2rem;
        margin-bottom: 0;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
    }

    .table th {
        font-weight: 600;
    }

    .modal-body.bg-light {
        background-color: #f8f9fa;
    }

    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: white;
    }

    .badge {
        font-size: 0.875em;
        padding: 0.5em 0.75em;
    }

    .badge.bg-warning {
        color: #000 !important;
    }

    .badge i {
        font-size: 0.875em;
    }

    /* ESTILOS UNIFORMES PARA CONTADOR DE DÍAS */
    .contador-dias {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 60px !important;
        width: 60px !important;
        height: 32px !important;
        padding: 0 !important;
        border-radius: 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-align: center !important;
        border: none !important;
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    .contador-dias.vencido {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .contador-dias.urgente {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    .contador-dias.normal {
        background-color: #007bff !important;
        color: white !important;
    }

    .contador-dias.confirmado {
        background-color: #28a745 !important;
        color: white !important;
    }

    .contador-dias.sin-fecha {
        background-color: #6c757d !important;
        color: white !important;
    }

    .contador-dias.vencido {
        animation: pulse-vencido 2s infinite;
    }

    @keyframes pulse-vencido {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    .contador-dias i {
        font-size: 10px !important;
        margin-right: 2px !important;
    }

    /* ESTILOS MEJORADOS PARA AUTOCOMPLETE */
    .ui-autocomplete {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        font-family: inherit;
        width: auto !important;
        min-width: 300px;
        max-width: 500px;
    }

    .ui-menu-item {
        margin: 0;
        padding: 0;
        border: none;
    }

    .ui-menu-item-wrapper {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f4;
        font-size: 14px;
        line-height: 1.4;
        color: #495057;
        transition: all 0.2s ease;
        display: block;
        text-decoration: none;
        word-wrap: break-word;
        white-space: normal;
    }

    .ui-menu-item:hover .ui-menu-item-wrapper,
    .ui-menu-item.ui-state-active .ui-menu-item-wrapper,
    .ui-menu-item.ui-state-focus .ui-menu-item-wrapper {
        background-color: #f8f9fa;
        color: #495057;
        border-left: 3px solid #dc3545;
    }

    .ui-menu-item:last-child .ui-menu-item-wrapper {
        border-bottom: none;
    }

    .modal {
        z-index: 1050;
    }

    .modal.modal-stacked {
        z-index: 1055;
    }

    .ui-autocomplete {
        z-index: 1060 !important;
    }

    .swal2-container {
        z-index: 2000 !important;
    }

    .swal-high-zindex {
        z-index: 3000 !important;
    }

    .ui-autocomplete-input:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .autocomplete-container {
        position: relative;
    }

    #tablaMotivos thead th {
        background-color: transparent !important;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    #tablaMotivos tbody tr {
        background-color: transparent !important;
    }

    #tablaMotivos tbody td {
        background-color: transparent !important;
        border-bottom: 1px solid #dee2e6;
    }

    #tablaMotivos tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    .estado-oficina {
        color: #28a745;
        font-weight: bold;
    }

    .estado-no-oficina {
        color: #dc3545;
        font-weight: bold;
    }
</style>

<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center h1-style-2">REGISTRO DE GESTIÓN DE ACTIVOS</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="clearfix">
            <ol class="breadcrumb m-0 float-start" style="background: transparent;">
                <li class="breadcrumb-item"><a href="javascript: void(0);" style="color: #718096; text-decoration: none;">Orden Servicio</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="font-weight: 500; color: #CA3438;">Gestion activos</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">

            <div class="card-title-desc text-end" style="padding: 10px 10px 0 0;">
                <button id="btnAbrirModalRegistro" type="button" class="btn bg-rojo text-white button-link">
                    <i class="fa fa-plus"></i> Añadir Registro
                </button>
                <a href="/maquina" class="btn border-rojo button-link">
                    <i class="fa fa-plus"></i> Máquina
                </a>
            </div>

            <div id="conte-vue-modals">
                <?php include __DIR__ . '/partials/_modal_registro.php' ?>
                <?php include __DIR__ . '/partials/_modal_detalles.php' ?>
                <?php include __DIR__ . '/partials/_modal_motivos.php' ?>

                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table table-bordered dt-responsive nowrap text-center table-sm">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag me-1"></i> Número</th>
                                        <th>Cliente/Razón Social</th>
                                        <th>Motivo</th>
                                        <th>Fecha De Salida</th>
                                        <th>Fecha De Ingreso</th>
                                        <th><i class="fa fa-clock me-1"></i> Días V.</th>
                                        <th>Acciones</th>
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

<script>
    var tabla_clientes;
    window.modoEdicion = false;
    window.activoEditandoId = null;
</script>
<script src="<?= URL::to('public/js/modulo-gestion-activos.js') ?>?v=<?= time() ?>"></script>
