<?php
require_once 'app/models/PreAlerta.php';

$c_prealerta = new PreAlerta();
?>
<link rel="stylesheet" href="<?= URL::to('public/css/taller/prealerta.css') ?>?v=<?= time() ?> ?>">

<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">
                REGISTRO DE MAQUINAS A REPARAR
            </h6>
        </div>
    </div>
</div>

<div class="row">
     <div class="col-md-8">
            <div class="clearfix">
                <ol class="breadcrumb m-0 float-start" style="background: transparent;">
                    <li class="breadcrumb-item"><a href="javascript: void(0);" style="color: #718096; text-decoration: none;">Orden Servicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="font-weight: 500; color: #CA3438;">Pre Alerta</li>
                </ol>
            </div>
        </div>
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header bg-white bordes">
                <div class="row align-items-center">
                    <div class="col-md-12 text-end">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar"
                            class="btn bg-rojo text-white">
                            <i class="fa fa-plus"></i> Añadir
                        </button>
                    </div>
                </div>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <?php include __DIR__ . '/table/table-view.php' ?>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/partials/_prealerta_modal_detalles.php' ?>
            <?php include __DIR__ . '/partials/_prealerta_modal_agregar.php' ?>
            <?php include __DIR__ . '/partials/_prealerta_modal_editar.php' ?>
        </div>
    </div>
</div>

<?php include PATH_VIEWS . 'fragment-views/cliente/modals/orden/modal-marca.php'; ?>
<?php include PATH_VIEWS . 'fragment-views/cliente/modals/orden/modal-modelo.php'; ?>
<?php include PATH_VIEWS . 'fragment-views/cliente/modals/orden/modal-equipo.php'; ?>
<?php include PATH_VIEWS . 'fragment-views/cliente/modals/orden/modal-tecnico.php'; ?>

<script src="<?= URL::to('public/js/orden/orden-base.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('public/js/orden/orden-servicio.js') ?>?v=<?= time() ?>"></script>
