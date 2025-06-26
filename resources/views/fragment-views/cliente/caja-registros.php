<?php
$conexion = (new Conexion())->getConexion();

$sql="select * from caja_empresa where sucursal='{$_SESSION['sucursal']}' and id_empresa='{$_SESSION['id_empresa']}' ORDER BY fecha DESC";

$listaC = $conexion->query($sql);

?>
<div class="page-title-box">
    <div class="row align-items-center">
            <!-- <div class="col-md-8">
                <h6 class="page-title">Ventas</h6>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Facturacion</a></li>
                    <li class="breadcrumb-item"><a href="/ventas" class="button-link">Ventas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Productos</li>
                </ol>
            </div> -->
        <div class="clearfix">
            <h6 class="page-title text-center">REGISTRO DE CAJAS</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Cajas</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438;">Registros</li>
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
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">

                <h4 class="card-title">Registros de Caja</h4>

                <div class="card-title-desc">

                </div>
                <div class="">
                  <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead>
                        <tr>
                            <th></th>
                            <th>Detalle</th>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $contador=0;
                        foreach ($listaC as $row){
                            $contador++;
                            ?>
                            <tr>
                                <td><?=$contador?></td>
                                <td><?=$row['detalle']?></td>
                                <td><?=Tools::formatoFechaVisual($row['fecha'])?></td>
                                <td><?=$row['entrada']?></td>
                                <td><?=$row['salida']?></td>
                                <td>

                                <?php $total = doubleval($row['entrada']) - doubleval($row['salida']); echo $total?>
                                </td>
                            </tr>
                        <?php
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
    $(document).ready(function(){
        $("#datatable").DataTable({})
    })
</script>
<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>
