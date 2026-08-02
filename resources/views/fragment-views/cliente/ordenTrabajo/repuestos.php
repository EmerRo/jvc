<?php
require_once 'app/models/Repuesto.php';
$repuesto = new Repuesto();
$repuesto->setIdEmpresa($_SESSION['id_empresa']);
$almacenRepuesto = 1;
?>
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">
<link rel="stylesheet" href="<?= URL::to('/public/css/almacen-productos.css') ?>?v=<?= time() ?>">
<style>
    .dt-body-left { text-align: left; }
    .ui-autocomplete {
        max-height: 200px; overflow-y: auto; overflow-x: hidden;
        border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        background: #fff; border: 1px solid #ddd; z-index: 1050 !important;
    }
    .ui-autocomplete .ui-menu-item { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; }
    .ui-autocomplete .ui-menu-item:hover,
    .ui-autocomplete .ui-state-focus { background: #CA3438; color: #fff; border: none; margin: 0; }
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title text-center">REPUESTOS</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Almacen</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);" style="font-weight: 500; color: #CA3438;">Repuestos</a></li>
            </ol>
        </div>
    </div>
</div>

<div id="conte-vue-modals">
    <input type="hidden" name="almacenId" id="almacenId" value="<?= $almacenRepuesto ?>">

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); background: #fff;">
                <?php include __DIR__ . '/table/header-actions.php' ?>
                <div class="card-body" style="background: #fff; padding: 24px 16px; border-radius: 0 0 20px 20px;">
                    <?php include __DIR__ . '/table/table-view.php' ?>
                    <?php include __DIR__ . '/table/grid-view.php' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php include __DIR__ . '/../modals/repuesto-modal-precios.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-aumentar-stock.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-disminuir-stock.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-traslado-almacenes.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-historial-stock.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-add.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-edt.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-restock.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-importar.php' ?>
    <?php include __DIR__ . '/../modals/repuesto-modal-lista.php' ?>
    <?php include __DIR__ . '/../modals/product-modal-codigo-barras.php' ?>
    <?php include __DIR__ . '/table/almacen-modal.php' ?>
</div>

<script>
    // ===== GLOBALES =====
    var codRepT = ''
    var nombreBarraTemps = ''
    var codeBarraTemps = ''
    var datatable
    var almacenCod = 4
    var app;

    function descarFunccc() {
        window.open(_URL + `/reporte/repuesto/excel?texto=${$("#datatable_filter input").val()}`)
    }
    function abrirModalBarras(e, n) {
        n = n || '';
        e = e.trim();
        nombreBarraTemps = n;
        codeBarraTemps = e;
        $('#modalCodigoBarras').modal('show');
        $('#modalCodigoBarras').off('shown.bs.modal').on('shown.bs.modal', function () {
            setTimeout(function () {
                try { JsBarcode("#idCodigoBarras", e); } catch (err) { console.error(err); }
            }, 100);
        });
    }
    function imprimir() {
        window.open(_URL + "/ge/bar/code?code=" + codeBarraTemps + "&nombre=" + nombreBarraTemps + "&scal=" + $("#scalimg").val(), "_blank");
    }
    function imprimir2() {
        window.open(_URL + "/ge/bar/code2?code=" + codeBarraTemps + "&nombre=" + nombreBarraTemps + "&scal=" + $("#scalimg").val(), "_blank");
    }
    function clearSelection() {
        arrayIdsOkUsar = [];
        $('.btnCheckEliminar').prop('checked', false);
        $('.btnSeleccionarTodos').prop('checked', false);
        localStorage.removeItem('idChecks');
    }

    // ===== DOMContentLoaded: Filtros JVC/IMPLE =====
    document.addEventListener('DOMContentLoaded', function () {
        var checkboxJVC = document.getElementById('maquinas');
        var checkboxIMPLE = document.getElementById('implementos');
        if (checkboxJVC && checkboxIMPLE) {
            function handleCheckboxChange(checkedBox, otherBox, searchValue) {
                otherBox.checked = false;
                var searchInput = $('div.dataTables_filter input');
                if (checkedBox.checked) {
                    searchInput.val(searchValue).trigger('keyup');
                } else {
                    searchInput.val('').trigger('keyup');
                }
            }
            checkboxJVC.addEventListener('change', function () {
                handleCheckboxChange(checkboxJVC, checkboxIMPLE, 'JVC');
            });
            checkboxIMPLE.addEventListener('change', function () {
                handleCheckboxChange(checkboxIMPLE, checkboxJVC, 'IMPLE');
            });
        }
    });
</script>
<script src="<?= URL::to('public/js/orden/orden-repuestos-vue.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('public/js/orden/orden-repuestos-init.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('public/js/orden/orden-repuestos-grid.js') ?>?v=<?= time() ?>"></script>
