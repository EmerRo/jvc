<!-- resources\views\fragment-views\cliente\caja-registros.php -->
<?php
$conexion = (new Conexion())->getConexion();

$sql = "SELECT * FROM caja_empresa WHERE sucursal = ? AND id_empresa = ? ORDER BY caja_id DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $_SESSION['sucursal'], $_SESSION['id_empresa']);
$stmt->execute();
$listaC = $stmt->get_result();
$stmt->close();

?>
<div class="page-title-box">
    <div class="row align-items-center">
          
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

                <h4 class="card-title">Registros de Caja
                    <a href="<?= URL::to('/reporte/cajas/general/excel') ?>" class="btn btn-success float-end"><i class="fas fa-file-excel"></i> Descargar Registro</a>
                </h4>

                <div class="card-title-desc">

                </div>
                <div class="">
                  <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Detalle</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
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
                             <td><?=$row['numero'] ?: '-'?></td>

                                <td><?=$row['detalle']?></td>
                                <td><?=Tools::formatoFechaVisual($row['fecha'])?></td>
                                <td><?= $row['fecha_cierre'] ? (new DateTime($row['fecha_cierre']))->format('d/m/Y h:i A') : '<span class="badge bg-success">Abierta</span>' ?></td>
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
        $("#datatable").DataTable({
            "order": [[ 0, "desc" ]]
        })
    })
</script>
<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>
