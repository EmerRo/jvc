<?php
// Verificar la sesión del usuario
if (!isset($_SESSION)) {
    session_start();
}

// Obtener permisos del usuario según su rol
$puedeVerPrecios = true; // Por defecto, puede ver precios
$puedeEditar = true; // Por defecto, puede editar
$puedeEliminar = true; // Por defecto, puede eliminar
$esRolOrdenTrabajo = false; // Por defecto, no es rol orden trabajo
$origenEsOrdenTrabajo = false; // Por defecto, no es origen orden trabajo

// Consultar permisos específicos del rol
if (isset($_SESSION['id_rol'])) {
    $rolId = $_SESSION['id_rol'];
    $conexion = (new Conexion())->getConexion();

    // Verificar si es rol orden trabajo
    $sqlRol = "SELECT nombre FROM roles WHERE rol_id = ?";
    $stmtRol = $conexion->prepare($sqlRol);
    $stmtRol->bind_param("i", $rolId);
    $stmtRol->execute();
    $resultRol = $stmtRol->get_result();
    if ($rowRol = $resultRol->fetch_assoc()) {
        $esRolOrdenTrabajo = (strtoupper($rowRol['nombre']) === 'ORDEN TRABAJO');
    }

    // Verificar permisos generales
    $sql = "SELECT ver_precios, puede_eliminar FROM roles WHERE rol_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $rolId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $puedeVerPrecios = (bool) $row['ver_precios'];
        $puedeEliminar = (bool) $row['puede_eliminar'];
    }

    // Verificar si tiene permiso para editar órdenes
    $sqlPermisos = "SELECT COUNT(*) as tiene_permiso FROM rol_submodulo rs 
                    INNER JOIN submodulos s ON rs.submodulo_id = s.submodulo_id 
                    WHERE rs.rol_id = ? AND s.nombre IN ('ORDEN DE SERVICIO', 'ORDEN DE TRABAJO')";
    $stmtPermisos = $conexion->prepare($sqlPermisos);
    $stmtPermisos->bind_param("i", $rolId);
    $stmtPermisos->execute();
    $resultPermisos = $stmtPermisos->get_result();
    if ($rowPermisos = $resultPermisos->fetch_assoc()) {
        $puedeEditar = $rowPermisos['tiene_permiso'] > 0;
    }
}

// Verificar si el origen es ORD TRABAJO
if (isset($_GET['id'])) {
    $preAlertaId = $_GET['id'];
    $conexion = (new Conexion())->getConexion();
    $sqlOrigen = "SELECT origen FROM pre_alerta WHERE id_preAlerta = ?";
    $stmtOrigen = $conexion->prepare($sqlOrigen);
    $stmtOrigen->bind_param("i", $preAlertaId);
    $stmtOrigen->execute();
    $resultOrigen = $stmtOrigen->get_result();
    if ($rowOrigen = $resultOrigen->fetch_assoc()) {
        $origenEsOrdenTrabajo = ($rowOrigen['origen'] === 'ORD TRABAJO');
    }
}

// Determinar si se deben mostrar los botones y campos de descuento
$mostrarBotonesYDescuento = !$esRolOrdenTrabajo && !$origenEsOrdenTrabajo;
?>
<link rel="stylesheet" href="<?= URL::to('public/css/taller/styles.css') ?>">
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>

<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title"><?php echo $esRolOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?></h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Taller</a></li>
                <li class="breadcrumb-item"><a href="/ventas"
                        class="button-link"><?php echo $esRolOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438;">Nueva Cotizacion</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">

                <button type="button" onclick="$('#btn_finalizar_pedido').click()" class="btn bg-rojo text-white">
                    <i class="fa fa-plus"></i> Guardar
                    <?php echo $esRolOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?>
                </button>
                <button style="margin-left:25px;" class="btn btn-warning"
                    onclick="window.location.href='<?= URL::to('/taller') ?>'">
                    <i class="fa fa-arrow-left"></i> Regresar
                </button>


            </div>
        </div>
    </div>
</div>

<input type="hidden" value="<?= date("Y-m-d") ?>" id="fecha-app">

<div class="row ">
    <div class="col-12 ">
        <div class="card">
            <div class="card-body">



                <div class="row" id="container-vue" v-cloak>
                    <div class="col-12 row">
                        <!-- Columna izquierda -->
                        <div class="col-md-8">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row">
                                        <!-- Navegación por equipos -->
                                        <div class="col-md-12 mb-4">
                                            <nav class="nav nav-equipos">
                                                <a v-for="(equipo, index) in equiposPreAlerta" :key="index"
                                                    class="nav-link" :class="{ active: equipoActivo === index }"
                                                    @click.prevent="cambiarEquipo(index)" href="#">
                                                    Equipo {{index + 1}}
                                                </a>
                                            </nav>
                                        </div>

                                        <!-- Detalles del equipo actual -->
                                        <div class="col-md-12 mb-4" v-if="equipoActivo !== null">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Marca</label>
                                                    <input type="text" class="form-control"
                                                        :value="equiposPreAlerta[equipoActivo]?.marca" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Equipo</label>
                                                    <input type="text" class="form-control"
                                                        :value="equiposPreAlerta[equipoActivo]?.equipo" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Modelo</label>
                                                    <input type="text" class="form-control"
                                                        :value="equiposPreAlerta[equipoActivo]?.modelo" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Serie</label>
                                                    <input type="text" class="form-control"
                                                        :value="equiposPreAlerta[equipoActivo]?.numero_serie" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <!-- Formulario de búsqueda de productos -->
                                            <form v-on:submit.prevent="addProduct" class="form-horizontal">
                                            <canvas hidden="" id="qr-canvas" v-show="toggleCamara"
                                            style="width: 300px; padding: 10px;"></canvas>
                                                <div class="form-group row mb-3">
                                                    <label class="col-lg-2 control-label">Buscar</label>
                                                    <div class="col-lg-10">
                                                        <div class="input-group">
                                                            <input type="text" placeholder="Consultar Productos"
                                                                class="form-control ui-autocomplete-input"
                                                                id="input_buscar_productos" autocomplete="off">
                                                                <div class="input-group-btn p-1">
                                                                <label class=""> <input id="btn-scan-qr"
                                                                        v-model="usar_scaner" @click="toggleCamara"
                                                                        type="checkbox"> Usar Scanner</label><br />
                                                                <label @click="abrirMultipleBusaque"
                                                                    style="color: blue;cursor: pointer">Busqueda
                                                                    Multiple</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-lg-2 control-label">Descripción</label>
                                                    <div class="col-lg-10">
                                                        <input required v-model="producto.descripcion" type="text"
                                                            placeholder="Descripción" class="form-control"
                                                            readonly="true">
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <div class="col-lg-10">
                                                        <div class="row">
                                                            <!-- Stock Actual -->
                                                            <div class="col-lg-3">
                                                                <label class="col-form-label">Stock Actual</label>
                                                                <input disabled v-model="producto.stock"
                                                                    class="form-control text-center" type="text"
                                                                    placeholder="0">
                                                            </div>

                                                            <!-- Cantidad -->
                                                            <div class="col-lg-3">
                                                                <label class="col-form-label">Cantidad</label>
                                                                <input @keypress="onlyNumber" required
                                                                    v-model="producto.cantidad"
                                                                    class="form-control text-center" type="text"
                                                                    placeholder="0">
                                                            </div>

                                                            <!-- Precio -->
                                                            <div class="col-lg-3" <?php echo !$puedeVerPrecios ? 'style="display:none;"' : ''; ?>>
                                                                <label class="col-form-label">Precio</label>
                                                                <select class="form-control" v-model="producto.precio">
                                                                    <option v-for="(value, key) in precioProductos"
                                                                        :value="value.precio" :key="key">
                                                                        {{ value.precio }}
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Campo de Descuento -->
                                                            <?php if ($mostrarBotonesYDescuento): ?>
                                                                <div class="col-lg-6 d-flex align-items-center mt-3">
                                                                    <label for="descuento-general"
                                                                        class="col-form-label mb-0 me-3">
                                                                        Descuento %
                                                                    </label>
                                                                    <input type="checkbox" class="toggle-checkbox"
                                                                        onclick="toggleInput(this)"
                                                                        style="margin-right: 15px;" />
                                                                    <input id="descuento-general"
                                                                        class="form-control text-center precio-input"
                                                                        @keypress="onlyNumber" v-model="descuentoGeneral"
                                                                        type="text" placeholder="0" disabled
                                                                        style="max-width: 80px;">
                                                                </div>
                                                            <?php endif; ?>

                                                        </div>

                                                        <!-- Botón Agregar -->
                                                        <div class="row mt-3">
                                                            <div class="col-lg-2 d-flex align-items-center">
                                                                <button id="submit-a-product" type="submit"
                                                                    class="btn bg-rojo text-white w-100 d-flex align-items-center justify-content-center"
                                                                    style="min-width: 110px;">
                                                                    <i class="fa fa-check me-2"></i> Agregar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Tabla de productos -->
                                        <div class="col-md-12 mt-5" style="overflow-x: auto;">
                                            <div class="row">
                                                <div class="text-left col-md-9">
                                                    <h4>Producto</h4>
                                                </div>
                                            </div>
                                            <table class="table" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Código</th>
                                                        <th>Producto</th>
                                                        <th>Cantidad</th>
                                                        <?php if ($puedeVerPrecios): ?>
                                                            <th>P. Unit.</th>
                                                            <th>Parcial</th>
                                                        <?php endif; ?>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in productos">
                                                        <td>{{index+1}}</td>
                                                        <td>{{item.codigo_prod}}</td>
                                                        <td>{{item.descripcion}}</td>
                                                        <td>
                                                            <span v-if="!item.editable">{{item.cantidad}}</span>
                                                            <input v-if="item.editable" v-model="item.cantidad">
                                                        </td>
                                                        <?php if ($puedeVerPrecios): ?>
                                                            <td>
                                                                <span v-if="!item.editable">{{item.precioVenta}}</span>
                                                                <input v-if="item.editable" v-model="item.precioVenta">
                                                            </td>
                                                            <td>{{(item.precioVenta*item.cantidad).toFixed(2)}}</td>
                                                        <?php endif; ?>
                                                        <td>

                                                            <button @click="eliminarItemPro(index)" type="button"
                                                                class="btn btn-danger btn-sm">
                                                                <i class="fa fa-times"></i>
                                                            </button>


                                                            <button v-if="!item.editable" @click="item.editable=true"
                                                                class="btn btn-info btn-sm">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <button v-if="item.editable" @click="item.editable=false"
                                                                class="btn btn-warning btn-sm">
                                                                <i class="fa fa-save"></i>
                                                            </button>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="widget padding-0 white-bg">
                                            <div class="padding-20 text-center">
                                                <!-- Formulario de datos de venta -->
                                                <form v-on:submit.prevent role="form" class="form-horizontal">
                                                    <div class="row">
                                                        <!-- Tipo de documento -->
                                                        <div class="col-md-6 form-group">
                                                            <label class="control-label">Documento</label>
                                                            <div class="col-md-12">
                                                                <select @change="onChangeTiDoc($event)"
                                                                    v-model="venta.tipo_doc" class="form-control">
                                                                    <option value="1">BOLETA DE VENTA</option>
                                                                    <option value="2">FACTURA</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Tipo de pago -->
                                                        <div class="col-md-6 form-group">
                                                            <label class="control-label">Tipo Pago</label>
                                                            <select v-model="venta.tipo_pago" @change="changeTipoPago"
                                                                class="form-control">
                                                                <option value="1">Contado</option>
                                                                <option value="2">Crédito</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Serie y número (ocultos) -->
                                                    <div style="display: none" class="form-group">
                                                        <div class="col-lg-12 row">
                                                            <div class="col-lg-6">
                                                                <label class="text-center col-md-12">Serie</label>
                                                                <input v-model="venta.serie" type="text"
                                                                    class="form-control text-center" readonly>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="text-center col-md-12">Número</label>
                                                                <input v-model="venta.numero" type="text"
                                                                    class="form-control text-center" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Días de pago (si es crédito) -->
                                                    <div v-if="venta.tipo_pago=='2'" class="form-group">
                                                        <label class="control-label">Días de pago</label>
                                                        <div class="col-lg-12">
                                                            <input @focus="focusDiasPagos" v-model="venta.dias_pago"
                                                                type="text" class="form-control text-center">
                                                        </div>
                                                    </div>

                                                    <!-- Fecha -->
                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">Fecha</label>
                                                                        <div class="col-lg-12">
                                                                            <input v-model="venta.fecha" type="date"
                                                                                placeholder="dd/mm/aaaa"
                                                                                class="form-control text-center">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Moneda -->
                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">Moneda</label>
                                                                        <div class="col-lg-12">
                                                                            <select v-model="venta.moneda"
                                                                                class="form-control">
                                                                                <option value="1">SOLES</option>
                                                                                <option value="2">DOLARES</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" v-if="venta.moneda == '2'">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            Tasa de cambio
                                                                        </label>
                                                                        <div class="col-lg-12">
                                                                            <input v-model="venta.tc" type="text"
                                                                                class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Datos del cliente -->
                                                    <div class="form-group">
                                                        <label class="col-lg-12 text-center">Cliente</label>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input id="input_datos_cliente" v-model="venta.num_doc"
                                                                    type="text" placeholder="Ingrese Documento"
                                                                    class="form-control" maxlength="11" readonly>
                                                                <div class="input-group-addon btn bg-rojo text-white"
                                                                    @click="buscarDocumentSS">
                                                                    <i class="fa fa-search"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.nom_cli" type="text"
                                                                placeholder="Nombre del cliente" class="form-control"
                                                                autocomplete="off" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input v-model="venta.dir_cli" type="text"
                                                                    placeholder="Dirección" class="form-control"
                                                                    autocomplete="off">
                                                                <!-- <div class="input-group-addon">
                                                                    <input v-model="venta.dir_pos" name="dirserl"
                                                                        value="1" type="radio" class="form-check-input">
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input v-model="venta.dir2_cli" type="text"
                                                                    placeholder="Atención" class="form-control"
                                                                    autocomplete="off">
                                                                <!-- <div class="input-group-addon">
                                                                    <input :disabled="!isDirreccionCont"
                                                                        v-model="venta.dir_pos" name="dirserl" value="2"
                                                                        type="radio" class="form-check-input">
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Botón guardar (oculto) -->
                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <button style="display: none" @click="guardarVenta"
                                                                type="button" class="btn btn-lg bg-rojo text-white"
                                                                id="btn_finalizar_pedido">
                                                                <i class="fa fa-save"></i> Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Botones de acción -->
                                            <?php if ($mostrarBotonesYDescuento): ?>
                                                <button class="btn border-rojo" id="edit-condiciones"
                                                    style="margin-bottom: 5px;">
                                                    Modificar Términos
                                                </button>

                                                <button class="btn border-rojo" id="edit-diagnostico"
                                                    style="margin-bottom: 5px;">
                                                    Diagnóstico
                                                </button>
                                            <?php endif; ?>

                                            <button class="btn border-rojo btn-foto" style="margin-bottom: 5px;">
                                                Foto
                                            </button>

                                            <?php if ($esRolOrdenTrabajo || $origenEsOrdenTrabajo): ?>
                                                <button class="btn border-rojo" id="btn-observaciones"
                                                    data-bs-toggle="modal" data-bs-target="#modal-observaciones"
                                                    style="margin-bottom: 5px;">
                                                    Observaciones
                                                </button>
                                            <?php endif; ?>

                                            <br>

                                            <!-- Total -->
                                            <?php if ($puedeVerPrecios): ?>
                                                <div class="bg-rojo text-white pv-15 text-center p-3"
                                                    style="height: 90px; color: white">
                                                    <h1 class="mv-0 font-400" id="lbl_suma_pedido">
                                                        {{monedaSibol}}
                                                        {{(totalProdustos/(venta.tc||1)).toFixed(2)}}
                                                    </h1>
                                                    <div class="text-uppercase">Suma Pedido</div>
                                                </div>
                                            <?php else: ?>
                                                <div class="bg-rojo text-white pv-15 text-center p-3"
                                                    style="height: 90px; color: white">
                                                    <h1 class="mv-0 font-400">
                                                        <i class="fa fa-lock"></i> Información Restringida
                                                    </h1>
                                                    <div class="text-uppercase">Contacte a un administrador</div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-cotizacion-success" tabindex="-1"
                        aria-labelledby="modalCotizacionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-header text-center justify-content-center border-bottom pb-3">
                                    <div class="text-center w-100">
                                        <h4 class="modal-title text-success mb-2" id="modalCotizacionLabel">
                                            <i
                                                class="fas fa-check-circle me-2"></i>¡<?php echo $esRolOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?>
                                            Actualizada!
                                        </h4>
                                        <p class="text-muted mb-0">La
                                            <?php echo $esRolOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?> N°
                                            <span id="cotizacion-numero"></span>
                                            ha sido Actualizada correctamente.
                                        </p>
                                    </div>
                                </div>
                                <div class="modal-body p-4">
                                    <!-- Botones de acción -->
                                    <div class="d-flex justify-content-center gap-3 mb-4">
                                        <a href="#" id="btn-a4" target="_blank"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fas fa-file-pdf"></i> A4
                                        </a>
                                        <a href="#" id="btn-whatsapp" target="_blank"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fab fa-whatsapp"></i> WHATSAPP
                                        </a>
                                        <?php if ($esRolOrdenTrabajo): ?>
                                            <a href="#" id="btn-inventario-pdf-modal"
                                                class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
                                                <i class="fas fa-file-pdf"></i> REPORTE INVENTARIO PDF
                                            </a>
                                            <a href="#" id="btn-inventario-excel-modal"
                                                class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                                                <i class="fas fa-file-excel"></i> REPORTE INVENTARIO EXCEL
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Vista previa del PDF -->
                                    <div class="border rounded">
                                        <iframe id="pdf-preview" src=""
                                            style="width: 100%; height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between border-top pt-3">
                                    <a href="/taller/coti/view" class="btn border-rojo">
                                        <i class="fas fa-list me-2"></i> LISTA DE
                                        <?php echo $esRolOrdenTrabajo ? 'ÓRDENES DE TRABAJO' : 'COTIZACIONES'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                  
                    <!-- Modal de Configuración de Pagos Modificado -->
                    <div class="modal fade" id="modal-dias-pagos" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h3 class="modal-title" id="exampleModalLabel">Configuración de Pagos</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Información básica -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="">
                                                <label class="form-label">Fecha Emisión</label>
                                                <input v-model="venta.fecha" type="date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="">
                                                <label class="form-label">Monto Total Venta</label>
                                                <input :value="'S/ '+venta.total" disabled type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pago inicial -->
                                    <div class="mb-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                v-model="venta.tiene_inicial" id="checkInicial">
                                            <label class="form-check-label" for="checkInicial">
                                                <input class="form-check-input" type="checkbox"
                                                    v-model="venta.tiene_inicial" id="checkInicial">
                                                <label class="form-check-label" for="checkInicial">
                                                    Incluir pago inicial
                                                </label>
                                        </div>
                                        <div v-if="venta.tiene_inicial" class="input-group">
                                            <span class="input-group-text">S/</span>
                                            <input type="number" class="form-control" v-model="venta.monto_inicial"
                                                placeholder="Monto inicial" @input="calcularCuotasRestantes">
                                            <span class="input-group-text">o</span>
                                            <input type="number" class="form-control" v-model="venta.porcentaje_inicial"
                                                placeholder="%" min="0" max="100" @input="calcularMontoInicial">
                                        </div>
                                    </div>

                                    <!-- Número de cuotas -->
                                    <div class="mb-3">
                                        <label class="form-label">Número de cuotas</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" v-model="numeroCuotas" min="1"
                                                max="36" @change="generarCuotas">
                                            <button class="btn bg-rojo text-white" type="button"
                                                @click="generarCuotas">Generar</button>
                                        </div>
                                    </div>

                                    <!-- Tabla de cuotas con fechas seleccionables -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Tipo</th>
                                                            <th>Fecha</th>
                                                            <th>Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Mostrar pago inicial si existe -->
                                                        <tr v-if="venta.tiene_inicial">
                                                            <td>0</td>
                                                            <td>Inicial</td>
                                                            <td>{{visualFechaSee(venta.fecha)}}</td>
                                                            <td>S/ {{formatoDecimal(venta.monto_inicial)}}</td>
                                                        </tr>
                                                        <!-- Mostrar cuotas con fechas seleccionables -->
                                                        <tr v-for="(cuota, index) in cuotas" :key="index">
                                                            <td>{{index + 1}}</td>
                                                            <td>Cuota</td>
                                                            <td>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    v-model="cuota.fecha" @change="actualizarDiasPago">
                                                            </td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input type="number"
                                                                        class="form-control form-control-sm"
                                                                        v-model="cuota.monto"
                                                                        @input="actualizarTotalCuotas">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="3">Total</th>
                                                            <th>{{totalValorCuotas}}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn bg-rojo text-white"
                                        @click="confirmarPagos">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de búsqueda múltiple -->
                    <div class="modal fade" id="modalSelMultiProd" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Búsqueda Múltiple</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div v-if="pointSel==1">
                                        <div class="mb-3">
                                            <label class="form-label">Buscar Producto</label>
                                            <input v-model="dataKey" @keyup="busquedaKeyPess" type="text"
                                                class="form-control">
                                        </div>

                                        <div class="list-group" style="height: 300px; overflow-y: scroll;">
                                            <label v-for="item in listaTempProd"
                                                class="list-group-item list-group-item-action">
                                                <input v-model="itemsLista" :value="item" type="checkbox">
                                                {{item.value}}
                                            </label>
                                        </div>
                                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                                            <button @click="pasar2Poiter" class="btn bg-rojo text-white">
                                                Continuar
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="pointSel==2">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>Producto</td>
                                                    <td>Stock</td>
                                                    <td>Cantidad</td>
                                                    <td>Precio</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in itemsLista">
                                                    <th>{{item.codigo_pp}} | {{item.descripcion}}</th>
                                                    <th>{{item.cnt}}</th>
                                                    <th>
                                                        <input style="width: 80px;" v-model="item.cantidad" />
                                                    </th>
                                                    <th>
                                                        <select style="width: 80px;" class="form-control"
                                                            v-model="item.precio_unidad">
                                                            <option v-for="(value, key) in item.precioProductos"
                                                                :value="value.precio" :key="key">
                                                                {{ value.precio }}
                                                            </option>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                                            <button @click="pointSel=1" class="btn btn-warning">
                                                Regresar
                                            </button>
                                            <button @click="agregarProducto2Ps" class="btn bg-rojo text-white">
                                                Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de términos -->
<div class="modal fade" id="modal-terminos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h1 class="modal-title fs-5">Modificar Condiciones</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editor-container-terminos"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" id="guardar-terminos" class="btn bg-rojo text-white">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Diagnóstico -->
<div class="modal fade" id="modal-diagnostico" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h1 class="modal-title fs-5">Modificar Diagnóstico</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editor-container-diagnostico"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" id="guardar-diagnostico" class="btn bg-rojo text-white">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir fotos -->
<div class="modal fade" id="modalFotos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title">Subir Fotos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="formFotos" enctype="multipart/form-data">
                    <input type="hidden" id="id_cotizacion" name="id_cotizacion">
                    <div class="mb-3">
                        <label for="imageInput" class="form-label">
                            Seleccionar imágenes (máximo 12)
                        </label>
                        <input type="file" class="form-control" name="images[]" id="imageInput" multiple
                            accept="image/*" required>
                        <small class="text-muted">
                            Formatos permitidos: JPG, PNG, GIF
                        </small>
                    </div>
                    <div id="imagePreview" class="preview-container">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn bg-rojo text-white" id="btnGuardarFotos">
                    Guardar Fotos
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Reemplazar el modal de observaciones con una versión más simple -->
<?php if ($esRolOrdenTrabajo || $origenEsOrdenTrabajo): ?>
    <div class="modal fade" id="modal-observaciones" tabindex="-1" aria-labelledby="observacionesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="observacionesModalLabel">Observaciones de Orden de Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="observaciones-textarea" class="form-control" rows="10"
                        placeholder="Escriba las observaciones de la orden de trabajo..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn bg-rojo text-white" id="guardar-observaciones">Guardar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Asegurar que la instancia de Vue sea accesible globalmente
    let vueApp = null;

    // Esperar a que el documento esté listo
    $(document).ready(function () {
        // Destruir instancia anterior si existe
        if (vueApp) {
            vueApp.$destroy();
        }
        // Crear la instancia de Vue primero
        vueApp = new Vue({
            el: "#container-vue",
            data: {
                descuentoGeneral: '',
                productos: [],
                usar_scaner: false,
                producto: {
                    editable: false,
                    productoid: "",
                    descripcion: "",
                    nom_prod: "",
                    cantidad: "",
                    stock: "",
                    precio: "",
                    codigo: "",
                    codigo_pp: "",
                    costo: "",
                    codsunat: "",
                    precio: '1',
                    almacen: '<?php echo $_SESSION["sucursal"] ?>',
                    precio2: '',
                    precio_unidad: '',
                    precioVenta: '',
                    precio_usado: 1
                },
                precioProductos: [],
                usar_precio: '5',
                venta: {
                    dir_pos: 1,
                    tipo_doc: '1',
                    serie: '',
                    numero: '',
                    tipo_pago: '1',
                    dias_pago: '',
                    fecha: $("#fecha-app").val(),
                    fechaVen: $("#fecha-app").val(),
                    sendwp: false,
                    numwp: "",
                    num_doc: "",
                    nom_cli: "",
                    dir_cli: "",
                    dir2_cli: "",
                    tipoventa: 1,
                    total: 0,
                    dias_lista: [],
                    moneda: '1',
                    tc: '',
                    id_cotizacion: null,
                    fotos: [] // Array para almacenar las fotos
                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                pointSel: 1,
                equiposPreAlerta: [],
                equipoActivo: null,
                numeroCuotas: 1,
                cuotas: [],
            },
            mounted() {
                console.log("Componente montado");
                this.buscarSNdoc();
                this.cargarDatosPreAlerta();
            },
            watch: {
              
                'descuentoGeneral': function (newValue) {
                    // Validamos que sea un número y no exceda 100
                    if (newValue && !isNaN(newValue)) {
                        const descuento = parseFloat(newValue);
                        if (descuento > 100) {
                            this.descuentoGeneral = '100';
                        }
                        // El total se actualizará automáticamente por el computed property
                    }
                }
            },
            methods: {
                
                toggleCamara() {
                    if (!app.usar_scaner) {
                        app.encenderCamara();
                    } else {
                        app.cerrarCamara();
                    }
                },
                encenderCamara() {
                    navigator.mediaDevices
                        .getUserMedia({
                            video: {
                                facingMode: "environment"
                            }
                        })
                        .then(function (stream) {
                            app.scanning = true; // Actualiza el estado de escaneo
                            // Configuración de la cámara y la lógica de escaneo



                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr");
                            btnScanQR.checked = true;
                            video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                            video.srcObject = stream;
                            video.play();

                            function tick() {
                                canvasElement.height = video.videoHeight;
                                canvasElement.width = video.videoWidth;
                                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);

                                app.scanning && requestAnimationFrame(tick);
                            }

                            function scan() {
                                try {
                                    qrcode.decode();
                                } catch (e) {
                                    setTimeout(scan, 500);
                                }
                            }

                            video.addEventListener("loadeddata", function () {
                                canvasElement.hidden = false;

                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => {
                                $("#input_buscar_productos").val(respuesta);
                                if (respuesta) {
                                    $.ajax({
                                        type: "post",
                                        url: _URL + '/ajas/compra/buscar/producto',
                                        data: {
                                            producto: respuesta // Código escaneado
                                        },
                                        success: function (response) {
                                            //console.log(response);
                                            let data = JSON.parse(response);
                                            console.log(data);
                                            // // Manejar la respuesta del servidor
                                            if (data.res == true) {
                                                //alert("es verdadero el producto");

                                                let id = data.data[0].id_producto;
                                                let codigo_app = data.data[0].codigo;
                                                let codsunat = data.data[0].codsunat;
                                                let costo = data.data[0].costo;
                                                // let descripcion = data.data[0].descripcion;
                                                let nom_prod = data.data[0].descripcion;

                                                // let idempresa = data.data[0].empresa;
                                                let precio = data.data[0].precio;
                                                let precio2 = data.data[0].precio2;
                                                // let precio3 = data.data[0].precio3;
                                                // let precio4 = data.data[0].precio4;
                                                let precio_unidad = data.data[0].precio_unidad;

                                                Swal.fire({
                                                    title: 'Se agrego correctamente',
                                                    text: respuesta,
                                                    icon: 'success',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2, precio_unidad);
                                                $("#input_buscar_productos").val('');
                                                app.usar_scaner = false;
                                                app.cerrarCamara();
                                            } else {
                                                // alert("el producto no existe");
                                                $("#input_buscar_productos").val('');
                                                // Producto no encontrado
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Advertencia',
                                                    text: 'No se encontró ningun producto',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.usar_scaner = false;
                                                app.cerrarCamara();
                                            }
                                        },
                                        error: function () {
                                            // Manejar errores de AJAX
                                            alert('Error al buscar el producto.');
                                        }
                                    });


                                    // // Swal.fire({
                                    // //     title: 'Se agrego correctamente',
                                    // //     text: respuesta,
                                    // //     icon: 'success',
                                    // //     confirmButtonText: 'Cerrar'
                                    // }).then(() => {
                                    //     app.encenderCamara(); // Detiene la cámara después de escanear
                                }

                            };
                        });
                },
                cerrarCamara() {
                    // Lógica para apagar la cámara
                    //this.camaraEncendida = false;
                    app.usar_scaner = false; // Actualiza el estado de escaneo
                    const video = document.querySelector("video");
                    const canvasElement = document.getElementById("qr-canvas");
                    const canvas = canvasElement.getContext("2d");


                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach((track) => {
                            track.stop();
                        });
                    }
                    document.getElementById("btn-scan-qr").checked = false;
                    canvasElement.hidden = true;
                },
               
                generarCuotas() {
                    const numCuotas = parseInt(this.numeroCuotas) || 1;
                    this.cuotas = [];

                    // Calcular el monto por cuota
                    const montoTotal = this.venta.tiene_inicial
                        ? (parseFloat(this.venta.total) - parseFloat(this.venta.monto_inicial || 0))
                        : parseFloat(this.venta.total);

                    const montoPorCuota = (montoTotal / numCuotas).toFixed(2);

                    // Asegurarse de que la fecha base sea válida
                    let fechaBase;
                    try {
                        fechaBase = new Date(this.venta.fecha);
                        if (isNaN(fechaBase.getTime())) {
                            // Si la fecha no es válida, usar la fecha actual
                            fechaBase = new Date();
                            console.error("Fecha base inválida en formatDate:", fechaBase);
                        }
                    } catch (e) {
                        fechaBase = new Date();
                        console.error("Error al procesar fecha base:", e);
                    }

                    console.log("Fecha base para generar cuotas:", fechaBase);

                    for (let i = 0; i < numCuotas; i++) {
                        try {
                            // Crear una nueva fecha para cada cuota para evitar referencias compartidas
                            const fechaCuota = new Date(fechaBase.getTime());
                            fechaCuota.setMonth(fechaCuota.getMonth() + i + 1); // Incrementar un mes por cada cuota

                            console.log(`Cuota ${i + 1} - Fecha calculada:`, fechaCuota);

                            // Ajustar el monto de la última cuota para evitar problemas de redondeo
                            const monto = i === numCuotas - 1
                                ? (montoTotal - (montoPorCuota * (numCuotas - 1))).toFixed(2)
                                : montoPorCuota;

                            this.cuotas.push({
                                fecha: this.formatDate(fechaCuota),
                                monto: monto
                            });

                            console.log(`Cuota ${i + 1} - Fecha formateada:`, this.formatDate(fechaCuota));
                        } catch (e) {
                            console.error(`Error al generar cuota ${i + 1}:`, e);
                        }
                    }

                    this.actualizarDiasPago();
                },
                actualizarDiasPago() {
                    try {
                        // Guardar las cuotas en dias_lista para mantener compatibilidad
                        this.venta.dias_lista = this.cuotas.map(cuota => ({
                            fecha: cuota.fecha,
                            monto: cuota.monto
                        }));

                        // Actualizar la fecha de vencimiento con la última cuota
                        if (this.cuotas.length > 0) {
                            this.venta.fechaVen = this.cuotas[this.cuotas.length - 1].fecha;
                        }

                        console.log("Días lista actualizado:", this.venta.dias_lista);
                        console.log("Fecha vencimiento actualizada:", this.venta.fechaVen);
                    } catch (e) {
                        console.error("Error en actualizarDiasPago:", e);
                    }
                },

                // Actualizar el total de las cuotas
                actualizarTotalCuotas() {
                    this.actualizarDiasPago();
                },
                recargarDatos() {
                    // Recargar datos cuando se regresa a la página
                    this.productos = [];
                    this.buscarSNdoc();
                    this.cargarDatosPreAlerta();
                },
                agregarProducto2Ps() {
                    this.pointSel = 1
                    this.productos = this.productos.concat(this.itemsLista.map(e => {
                        e.precioVenta = e.precio_unidad
                        e.edicion = false
                        return {
                            ...e,
                            precioVenta: e.precio_unidad,
                            edicion: false,
                            productoid: e.codigo
                        }
                    }))
                    this.itemsLista = []
                    this.listaTempProd = []
                    this.dataKey = ''
                    $("#modalSelMultiProd").modal('hide')
                },
                pasar2Poiter() {
                    this.itemsLista = this.itemsLista.map(e => {
                        e.cantidad = '1'
                        let array = [{
                            precio: e.precio
                        },
                        {
                            precio: e.precio2
                        },
                        {
                            precio: e.precio_unidad
                        }
                        ]
                        e.precio_unidad = array[array.length - 1].precio || 0
                        e.precioProductos = array
                        return e
                    })
                    this.pointSel = 2
                },
                busquedaKeyPess(evt) {
                    const vue = this
                    vue.listaTempProd = []
                    if (this.dataKey.length > 0) {
                        _get("/ajs/cargar/repuestos/<?php echo $_SESSION["sucursal"] ?>?term=" + this.dataKey, (result) => {
                            console.log(result)
                            vue.listaTempProd = result
                        })
                    }
                },
                abrirMultipleBusaque() {
                    $("#modalSelMultiProd").modal('show')
                },
                cambiarPrecio(event) {
                    console.log(event.target.value)
                    var self = this
                    this.productos.forEach(element => {
                        if (event.target.value == 1) {
                            element.precioVenta = element.precio
                            element.precio_usado = '1'
                        } else if (event.target.value == 2) {
                            element.precioVenta = element.precio2
                            element.precio_usado = '2'
                        } else {
                            element.precioVenta = element.precio_unidad
                            element.precio_usado = '5'
                        }
                    });
                },


                formatDate(date) {
                    console.log(date);
                    var d = date,
                        month = '' + (d.getMonth() + 1),
                        day = '' + (d.getDate() + 1),
                        year = d.getFullYear();

                    if (month.length < 2)
                        month = '0' + month;
                    if (day.length < 2)
                        day = '0' + day;

                    return [year, month, day].join('-');
                },
                onlyNumberComas($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 44) {
                        $event.preventDefault();
                    }
                },
                focusDiasPagos() {
                    $("#modal-dias-pagos").modal("show")
                },
                changeTipoPago(event) {
                    console.log(event.target.value)
                    this.venta.fechaVen = this.venta.fecha;
                    this.venta.dias_lista = []
                    this.venta.dias_pago = ''
                },
                onlyNumber($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                        $event.preventDefault();
                    }
                },
                eliminarItemPro(index) {
                    this.productos.splice(index, 1)
                },
                buscarDocumentSS() {
                    if (this.venta.num_doc.length == 8 || this.venta.num_doc.length == 11) {
                        $("#loader-menor").show();
                        this.venta.dir_pos = 1;

                        _ajax("/ajs/consulta/doc/cliente", "POST", {
                            doc: this.venta.num_doc
                        },
                            function (resp) {
                                $("#loader-menor").hide();
                                console.log(resp);
                                if (resp.res) {
                                    app._data.venta.nom_cli = (resp.data.nombre ? resp.data.nombre : '') + (resp.data.razon_social ? resp.data.razon_social : '')
                                    app._data.venta.dir_cli = resp.data.direccion
                                } else {
                                    alertAdvertencia("Documento no encontrado")
                                }
                            }
                        )
                    } else {
                        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos")
                    }
                },
                guardarVenta() {
                    if (this.productos.length > 0) {
                        var continuar = true;
                        var mensaje = '';

                        // Validaciones según tipo de documento
                        if (this.venta.tipo_doc == '1') {  // Boleta
                            if (this.venta.num_doc.length == 11) {
                                continuar = false;
                                mensaje = 'No puede emitir Boleta usando RUC';
                            }
                            if (this.venta.tipo_pago == 2) {
                                if (this.venta.dias_lista.length == 0) {
                                    continuar = false;
                                    mensaje = 'Debe especificar los días de pagos para una cotización a crédito';
                                }
                            }
                        } else if (this.venta.tipo_doc == '2') {  // Factura
                            mensaje = 'Solo se puede emitir Factura usando RUC';
                            if (this.venta.num_doc.length != 11) {
                                continuar = false;
                            }
                            if (this.venta.tipo_pago == 2) {
                                if (this.venta.dias_lista.length == 0) {
                                    continuar = false;
                                    mensaje = 'Debe especificar los días de pagos para una cotización a crédito';
                                }
                            }
                        }

                        if (continuar) {
                            const formData = new FormData();

                            // Obtener el ID de pre-alerta de la URL
                            const urlParams = new URLSearchParams(window.location.search);
                            const preAlertaId = urlParams.get('id');

                            // Agregar el ID de pre-alerta al formData si existe
                            if (preAlertaId) {
                                formData.append('id_prealerta', preAlertaId);
                            }

                            // Agregar datos de la venta
                            Object.keys(this.venta).forEach((key) => {
                                if (key !== "fotos") {
                                    formData.append(key, this.venta[key])
                                }
                            })

                            // Agregar datos adicionales
                            formData.append("usar_precio", this.usar_precio)
                            formData.append("listaPro", JSON.stringify(this.productos))
                            formData.append("dias_lista", JSON.stringify(this.venta.dias_lista))
                            formData.append("descuento", this.descuentoGeneral || 0)

                            // Procesar y agregar equipos con sus fotos
                            const equiposConFotos = this.equiposPreAlerta.map((equipo) => {
                                const equipoData = { ...equipo }
                                delete equipoData.fotos // Eliminar las fotos del objeto equipo
                                return equipoData
                            })
                            formData.append("equipos", JSON.stringify(equiposConFotos))

                            // Agregar todas las fotos de todos los equipos
                            let fotoIndex = 0
                            this.equiposPreAlerta.forEach((equipo, equipoIndex) => {
                                if (equipo.fotos && Array.isArray(equipo.fotos)) {
                                    equipo.fotos.forEach((foto) => {
                                        if (foto instanceof File) {
                                            formData.append(`fotos[${fotoIndex}]`, foto)
                                            formData.append(`fotos_equipo[${fotoIndex}]`, equipoIndex)
                                            fotoIndex++
                                        }
                                    })
                                }
                            })

                            // Mostrar loader
                            $("#loader-menor").show()

                            // Realizar la petición AJAX
                            $.ajax({
                                url: _URL + "/ajs/taller/cotizaciones/add",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: (resp) => {
                                    $("#loader-menor").hide();
                                    try {
                                        const response = typeof resp === "string" ? JSON.parse(resp) : resp;
                                        if (response.res === true && response.cotizacion) {
                                            // Actualizar el modal con los datos de la cotización
                                            $('#cotizacion-numero').text(response.cotizacion.cotizacion_id);

                                            // Asegurarse de que la URL del PDF sea correcta
                                            const pdfUrl = response.cotizacion.pdfUrl;
                                            $('#pdf-preview').attr('src', pdfUrl);
                                            $('#btn-a4').attr('href', pdfUrl);

                                            // Configurar el botón de WhatsApp
                                            const whatsappUrl = `https://wa.me/?text=Cotización N° ${response.cotizacion.cotizacion_id}%0A${encodeURIComponent(pdfUrl)}`;
                                            $('#btn-whatsapp').attr('href', whatsappUrl);

                                            // Actualizar el estado de la pre-alerta a CULMINADO
                                            const preAlertaId = urlParams.get('id');
                                            if (preAlertaId) {
                                                $.ajax({
                                                    url: _URL + "/ajs/prealerta/culminar",
                                                    type: "POST",
                                                    data: { id_preAlerta: preAlertaId },
                                                    success: function (culminarResp) {
                                                        console.log("Pre-alerta marcada como CULMINADA:", culminarResp);
                                                    },
                                                    error: function (xhr, status, error) {
                                                        console.error("Error al actualizar estado de pre-alerta:", error);
                                                    }
                                                });
                                            }

                                            // Mostrar directamente el modal con el PDF
                                            $('#modal-cotizacion-success').modal('show');
                                        } else {
                                            throw new Error(response.error || "Error al guardar la cotización");
                                        }
                                    } catch (error) {
                                        console.error("Error al procesar la respuesta:", error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Error al procesar la respuesta del servidor'
                                        });
                                    }
                                },
                                error: (xhr, status, error) => {
                                    $("#loader-menor").hide()
                                    console.error("Error en la petición AJAX:", {
                                        status: status,
                                        error: error,
                                        response: xhr.responseText,
                                    })
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: "Ocurrió un error al guardar la cotización",
                                    })
                                },
                            })
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Advertencia",
                                text: mensaje,
                            })
                        }
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Advertencia",
                            text: "No hay productos agregados a la lista",
                        })
                    }
                },
                buscarSNdoc() {
                    _ajax("/ajs/consulta/sn", "POST", {
                        doc: this.venta.tipo_doc
                    },
                        function (resp) {
                            app.venta.serie = resp.serie
                            app.venta.numero = resp.numero
                        }
                    )
                },
                onChangeTiDoc(event) {
                    this.buscarSNdoc();
                },
                limpiasDatos() {
                    this.producto = {
                        editable: false,
                        productoid: "",
                        descripcion: "",
                        nom_prod: "",
                        cantidad: "",
                        stock: "",
                        precio: "",
                        codigo: "",
                        costo: "",
                        codsunat: "",
                        precio: '1',
                        almacen: '<?php echo $_SESSION["sucursal"] ?>',
                        precio2: '',
                        precio_unidad: '',
                        precioVenta: '',
                        precio_usado: 1
                    }
                },
                addProduct() {
                    if (this.producto.descripcion.length > 0) {
                        // Verificar si hay stock disponible
                        const stockDisponible = parseFloat(this.producto.stock);
                        const cantidadSolicitada = parseFloat(this.producto.cantidad);

                        if (stockDisponible === 0) {
                            Swal.fire({
                                icon: "warning",
                                title: "Stock no disponible",
                                text: `El producto "${this.producto.descripcion}" no tiene stock disponible.`,
                            });
                            return;
                        }

                        if (cantidadSolicitada > stockDisponible) {
                            Swal.fire({
                                icon: "warning",
                                title: "Stock insuficiente",
                                text: `Solo hay ${stockDisponible} unidades disponibles del producto "${this.producto.descripcion}".`,
                            });
                            return;
                        }

                        if (this.equipoActivo === null) {
                            alertAdvertencia("Por favor, seleccione un equipo primero");
                            return;
                        }

                        <?php if (!$puedeVerPrecios): ?>
                            // Si el usuario no puede ver precios, usar precio por defecto (0)
                            this.producto.precioVenta = 0;
                        <?php endif; ?>

                        <?php if (!$puedeEditar): ?>
                            // Si el usuario no puede editar, deshabilitar edición
                            this.producto.editable = false;
                        <?php endif; ?>

                        // Crear nuevo producto con la referencia al equipo
                        const nuevoProducto = {
                            ...this.producto,
                            equipoActivo: this.equipoActivo, // Guardar el índice del equipo activo
                            editable: <?php echo $puedeEditar ? 'false' : 'false'; ?>
                        };

                        // Agregar al array general de productos
                        this.productos = [...this.productos, nuevoProducto];

                        // Limpiar los campos
                        this.limpiasDatos();
                    } else {
                        alertAdvertencia("Busque un producto primero")
                            .then(() => {
                                setTimeout(() => {
                                    $("#input_buscar_productos").focus();
                                }, 500);
                            });
                    }
                },


                cargarDatosPreAlerta() {

                    const preAlertaId = new URLSearchParams(window.location.search).get("id")

                    if (preAlertaId) {
                        console.log("Cargando datos para pre-alerta ID:", preAlertaId)

                        _post(
                            "/ajs/taller/prealerta/info",
                            {
                                id: preAlertaId,
                            },
                            (resp) => {
                                console.log("Respuesta de pre-alerta:", resp)
                                if (resp && resp.res) {
                                    const data = resp.data
                                    this.venta.num_doc = data.cliente_doc || ""
                                    this.venta.nom_cli = data.cliente_nombre || ""
                                    this.venta.dir_cli = data.cliente_direccion || ""
                                    this.venta.fecha = data.fecha_ingreso || $("#fecha-app").val()

                                    // Procesar los arrays concatenados
                                    const marcas = data.marcas[0].split(",")
                                    const equipos = data.equipos[0].split(",")
                                    const modelos = data.modelos[0].split(",")
                                    const series = data.numeros_serie[0].split(",")

                                    // Crear array de equipos procesados
                                    this.equiposPreAlerta = marcas.map((marca, index) => ({
                                        marca: marca.trim(),
                                        equipo: equipos[index].trim(),
                                        modelo: modelos[index].trim(),
                                        numero_serie: series[index].trim(),
                                        productos: [], // Array para los productos de cada equipo
                                    }))

                                    // Activar el primer equipo por defecto
                                    if (this.equiposPreAlerta.length > 0) {
                                        this.equipoActivo = 0
                                    }

                                    this.determinarTipoDocumento(data.cliente_doc)
                                }
                            },
                        )
                    }
                },


                // Agregar este nuevo método
                determinarTipoDocumento(numDoc) {
                    if (numDoc && numDoc.length === 11) {
                        this.venta.tipo_doc = '2'; // Factura para RUC
                    } else {
                        this.venta.tipo_doc = '1'; // Boleta para otros casos
                    }
                    this.buscarSNdoc(); // Actualizar serie y número
                },


                cambiarEquipo(index) {
                    this.equipoActivo = index;
                    // Ensure the equiposPreAlerta array exists and has items
                    if (this.equiposPreAlerta && this.equiposPreAlerta[index]) {
                        // Make sure the productos array exists for this equipment
                        if (!this.equiposPreAlerta[index].productos) {
                            this.equiposPreAlerta[index].productos = [];
                        }
                        // Update the productos array with the current equipment's products
                        this.productos = this.equiposPreAlerta[index].productos;
                    }
                },


                actualizarVistaPrevia() {
                    const preview = document.getElementById('imagePreview');
                    if (preview) {
                        preview.innerHTML = '';
                        this.venta.fotos.forEach((foto, index) => {
                            this.addImagePreview(foto.url, index, true, foto.name);
                        });
                    }
                },
                confirmarPagos() {
                    if (this.venta.tipo_pago === '2' && this.cuotas.length === 0) {
                        alertAdvertencia('Debe especificar al menos una cuota para crédito');
                        return;
                    }

                    if (this.venta.tiene_inicial && !this.venta.monto_inicial) {
                        alertAdvertencia('Debe especificar el monto inicial');
                        return;
                    }

                    // Asegurarse de que las fechas estén en el formato correcto (YYYY-MM-DD)
                    this.cuotas.forEach(cuota => {
                        // Verificar si la fecha ya está en formato YYYY-MM-DD
                        if (!/^\d{4}-\d{2}-\d{2}$/.test(cuota.fecha)) {
                            // Si no está en el formato correcto, convertirla
                            const fechaObj = new Date(cuota.fecha);
                            cuota.fecha = this.formatDate(fechaObj);
                        }
                    });

                    // Actualizar el campo dias_pago para mantener compatibilidad
                    this.venta.dias_pago = this.cuotas.map(cuota => cuota.fecha).join(',');

                    // Actualizar dias_lista con las cuotas actualizadas
                    this.venta.dias_lista = this.cuotas;

                    // Establecer la fecha de vencimiento como la última cuota
                    if (this.cuotas.length > 0) {
                        this.venta.fechaVen = this.cuotas[this.cuotas.length - 1].fecha;
                    }

                    $('#modal-dias-pagos').modal('hide');
                }
            },
            computed: {
                totalValorCuotas() {
                    let total = 0;

                    // Agregar monto inicial si existe
                    if (this.venta.tiene_inicial) {
                        total += parseFloat(this.venta.monto_inicial || 0);
                    }

                    // Sumar montos de las cuotas
                    this.cuotas.forEach(cuota => {
                        total += parseFloat(cuota.monto || 0);
                    });

                    return "S/ " + total.toFixed(2);
                },
                monedaSibol() {
                    return (this.venta.moneda == 1 ? 'S/' : '$')
                },
                totalValorListaDias() {
                    var total_ = 0;
                    this.venta.dias_lista.forEach((el) => {
                        total_ += parseFloat(el.monto + "")
                    })
                    return "S/ " + total_.toFixed(4);
                },
                isDirreccionCont() {
                    return this.venta.dir2_cli.length > 0;
                },
                totalProdustos() {
                    // Primero calculamos el total sin descuento
                    let total = 0;
                    this.productos.forEach((prod) => {
                        total += parseFloat(prod.precioVenta) * parseFloat(prod.cantidad);
                    });

                    // Aplicamos el descuento si existe
                    if (this.descuentoGeneral && this.descuentoGeneral !== '') {
                        const descuento = parseFloat(this.descuentoGeneral) / 100;
                        total = total * (1 - descuento);
                    }

                    // Actualizamos el total en venta y retornamos con 2 decimales
                    this.venta.total = total;
                    return total.toFixed(2);
                }
            },
            created() {
                this.buscarSNdoc();
                this.cargarDatosPreAlerta();
            }
        });

        // Hacer la instancia accesible globalmente
        window.app = vueApp;
        $('#modal-cotizacion-success').on('hidden.bs.modal', function () {
            window.location.href = '/taller';
        });
        // Inicializar componentes de UI
        $("#input_datos_cliente").autocomplete({
            source: _URL + "/ajs/buscar/cliente/datos",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui.item);
                app._data.venta.dir_pos = 1
                app._data.venta.nom_cli = ui.item.datos
                app._data.venta.num_doc = ui.item.documento
                app._data.venta.dir_cli = ui.item.direccion
            }
        });

    });


</script>

<!-- Scripts de la aplicación -->

<!-- Funciones utilitarias , toggleInput, formatFechaVisual, formatoDecimal -->
<script src="<?= URL::to('public/js/taller-cotizaciones/utils.js') ?>?v=<?= time() ?>"></script>
<!-- Inicialización de componentes UI -->
<script src="<?= URL::to('public/js/taller-cotizaciones/init.js') ?>?v=<?= time() ?>"></script>
<!--  Manejo de términos y condiciones -->
<script src="<?= URL::to('public/js/taller-cotizaciones/terminos-condiciones.js') ?>?v=<?= time() ?>"></script>
<!-- Manejo del diagnóstico -->
<script src="<?= URL::to('public/js/taller-cotizaciones/diagnostico.js') ?>?v=<?= time() ?>"></script>
<!-- Manejo de carga de repuestos, input_buscar_productos -->
<script>
    // Pasar la variable de permisos al JavaScript
    const puedeVerPrecios = <?php echo $puedeVerPrecios ? 'true' : 'false'; ?>;
</script>
<script src="<?= URL::to('public/js/taller-cotizaciones/cargar-repuestos.js') ?>?v=<?= time() ?>"></script>
<!-- Manejo de fotos -->
<script src="<?= URL::to('public/js/taller-cotizaciones/manejo-fotos.js') ?>?v=<?= time() ?>"></script>

<script src="<?= URL::to('public/js/taller-cotizaciones/navegacion.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('public/js/taller-cotizaciones/observaciones-integration.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('public/js/taller-cotizaciones/botones-reportes.js') ?>?v=<?= time() ?>"></script>