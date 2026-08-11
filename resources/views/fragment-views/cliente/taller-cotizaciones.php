<!-- resources\views\fragment-views\cliente\taller-cotizaciones.php -->
<?php
require_once 'app/models/ModulosHelper.php';
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
    if ($stmtRol === false) {
        die('Error en prepare: ' . $conexion->error);
    }
    $stmtRol->bind_param("i", $rolId);
    $stmtRol->execute();
    $resultRol = $stmtRol->get_result();
    if ($rowRol = $resultRol->fetch_assoc()) {
        $esRolOrdenTrabajo = (strtoupper($rowRol['nombre']) === 'ORDEN TRABAJO');
    }
    $esRolTaller = (strtoupper($rowRol['nombre']) === 'TALLER');


    // Verificar permisos generales
    $sql = "SELECT ver_precios, puede_eliminar FROM roles WHERE rol_id = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        die('Error en prepare: ' . $conexion->error);
    }
    $stmt->bind_param("i", $rolId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $puedeVerPrecios = (bool) $row['ver_precios'];
        $puedeEliminar = (bool) $row['puede_eliminar'];
    }

    // Verificar si tiene permiso para editar órdenes
// Obtener módulos permitidos para el rol
    $modulosPermitidos = ModulosHelper::obtenerModulosParaRol($rolId, $conexion);

    // Verificar si tiene acceso a módulos de taller u órdenes
    $puedeEditar = false;
    foreach ($modulosPermitidos as $modulo) {
        // Verificar si tiene acceso al módulo de taller o sus submódulos
        if (
            strtoupper($modulo['nombre']) === 'TALLER' ||
            strpos(strtoupper($modulo['nombre']), 'ORDEN') !== false
        ) {
            $puedeEditar = true;
            break;
        }

        // También verificar submódulos
        if (!empty($modulo['submodulos'])) {
            foreach ($modulo['submodulos'] as $submodulo) {
                if (
                    strpos(strtoupper($submodulo['nombre']), 'ORDEN') !== false ||
                    strpos(strtoupper($submodulo['nombre']), 'TALLER') !== false
                ) {
                    $puedeEditar = true;
                    break 2; // Salir de ambos loops
                }
            }
        }
    }
}

// Verificar el tipo de origen desde la URL
$tipoOrigen = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$origenEsOrdenTrabajo = ($tipoOrigen === 'ORD TRABAJO');

// Determinar si se deben mostrar los botones y campos de descuento
// Solo ocultar si es rol orden trabajo O si el origen es orden trabajo
$mostrarBotonesYDescuento = !$esRolOrdenTrabajo && !$origenEsOrdenTrabajo;
?>
<link rel="stylesheet" href="<?= URL::to('public/css/taller/styles.css') ?>">
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>

<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<style>
    .taller-cotizacion-view .taller-productos-table,
    .taller-cotizacion-view .taller-pagos-modal,
    .taller-autocomplete-menu,
    #modal-terminos .ql-container,
    #modal-terminos .ql-editor,
    #modal-terminos .ql-toolbar,
    #modal-terminos .ql-tooltip,
    #modal-diagnostico .ql-container,
    #modal-diagnostico .ql-editor,
    #modal-diagnostico .ql-toolbar,
    #modal-diagnostico .ql-tooltip,
    #modal-terminos-opciones .ql-container,
    #modal-terminos-opciones .ql-editor,
    #modal-terminos-opciones .ql-toolbar,
    #modal-terminos-opciones .ql-tooltip,
    #modal-diagnostico-opciones .ql-container,
    #modal-diagnostico-opciones .ql-editor,
    #modal-diagnostico-opciones .ql-toolbar,
    #modal-diagnostico-opciones .ql-tooltip {
        font-family: 'Sarabun', sans-serif !important;
    }
</style>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">
                <?php echo $esRolOrdenTrabajo || $origenEsOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?>
            </h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Taller</a></li>
                <li class="breadcrumb-item"><a href="/ventas"
                        class="button-link"><?php echo $esRolOrdenTrabajo || $origenEsOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438;">Nueva Cotizacion</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">

                <button type="button" onclick="$('#btn_finalizar_pedido').click()" class="btn bg-rojo text-white">
                    <i class="fa fa-plus"></i> Guardar
                    <?php echo $esRolOrdenTrabajo || $origenEsOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?>
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

                <div class="row taller-cotizacion-view" id="container-vue" v-cloak>
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

                                        <!-- Título del equipo actual -->
                                        <div class="col-md-12 mb-2" v-if="equipoActivo !== null">
                                            <h5 class="text-center mb-3" style="font-weight: 600;">
                                                <span style="color: #6c757d;">Equipo:</span>
                                                <span style="color: #2c3e50; font-weight: 700;">{{ equiposPreAlerta[equipoActivo]?.marca }} {{ equiposPreAlerta[equipoActivo]?.equipo }}</span>
                                                <span v-if="equiposPreAlerta[equipoActivo]?.modelo">
                                                    <span style="color: #6c757d;"> // Modelo:</span>
                                                    <span style="color: #2c3e50; font-weight: 700;">{{ equiposPreAlerta[equipoActivo]?.modelo }}</span>
                                                </span>
                                                <span v-if="equiposPreAlerta[equipoActivo]?.numero_serie">
                                                    <span style="color: #6c757d;"> // Serie:</span>
                                                    <span style="color: #2c3e50; font-weight: 700;">{{ equiposPreAlerta[equipoActivo]?.numero_serie }}</span>
                                                </span>
                                            </h5>
                                        </div>

                                        <div class="col-md-12">
                                            <!-- Formulario de búsqueda de productos -->
                                            <form v-on:submit.prevent="addProduct" class="form-horizontal">
                                                <canvas hidden="" id="qr-canvas" v-show="usar_scaner"
                                                    style="width: 300px; padding: 10px;"></canvas>
                                                <div class="form-group row mb-3">
                                                    <label class="col-lg-2 control-label">Almacén</label>
                                                    <div class="col-lg-10">
                                                        <div class="input-group">
                                                            <select id="selector_almacen_taller" class="form-select">
                                                                <option value="">Cargando almacenes...</option>
                                                            </select>
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
                                                    <label class="col-lg-2 control-label">Buscar</label>
                                                    <div class="col-lg-10">
                                                        <div class="input-group">
                                                            <input type="text" placeholder="Consultar Productos del almacén"
                                                                class="form-control ui-autocomplete-input"
                                                                id="input_buscar_productos" autocomplete="off"
                                                                disabled>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text" id="almacen-help-text"><i class="fa fa-info-circle text-muted"></i> Selecciona un almacén</span>
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
                                                                <label class="col-form-label">Precio (PEN)</label>
                                                                <select class="form-control" v-model="producto.precio">
                                                                    <option v-for="(value, key) in precioProductos"
                                                                        :value="value.precio" :key="key">
                                                                        S/ {{ formatoDecimal(value.precio) }} (PEN)
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Botón Agregar -->
                                                            <div class="<?php echo $puedeVerPrecios ? 'col-lg-3' : 'col-lg-6'; ?> d-flex align-items-end">
                                                                <button id="submit-a-product" type="submit"
                                                                    class="btn bg-rojo text-white d-flex align-items-center justify-content-center"
                                                                    style="min-width: 110px; margin-top: 32px;">
                                                                    <i class="fa fa-check me-2"></i> Agregar
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Campo de Descuento -->
                                                        <?php if ($mostrarBotonesYDescuento): ?>
                                                            <div class="row mt-3">
                                                                <div class="col-lg-6 d-flex align-items-center">
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
                                                            </div>
                                                        <?php endif; ?>
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
                                            <table class="table taller-productos-table" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50px; text-align: center;">Item</th>
                                                        <th style="width: 100px;">Código</th>
                                                        <th>Producto</th>
                                                        <th style="width: 80px; text-align: center;">Cantidad</th>
                                                        <?php if ($puedeVerPrecios): ?>
                                                            <th style="width: 100px;">P. Unit.</th>
                                                            <th style="width: 100px;">Parcial</th>
                                                        <?php endif; ?>
                                                        <th style="width: 100px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in productos">
                                                        <td style="width: 50px; text-align: center;">{{index+1}}</td>
                                                        <td style="width: 100px;">{{item.codigo_prod}}</td>
                                                        <td>{{item.descripcion}}</td>
                                                        <td style="width: 80px; text-align: center;">
                                                            <span v-if="!item.editable">{{item.cantidad}}</span>
                                                            <input v-if="item.editable" v-model="item.cantidad" style="text-align: center; width: 60px;">
                                                        </td>
                                                        <?php if ($puedeVerPrecios): ?>
                                                            <td>
                                                                <span v-if="!item.editable">{{formatearMoneda(item.precioVenta)}}</span>
                                                                <div v-if="item.editable" class="input-group input-group-sm">
                                                                    <span class="input-group-text">{{monedaSibol}}</span>
                                                                    <input class="form-control"
                                                                        :value="montoEnMoneda(item.precioVenta)"
                                                                        @input="actualizarPrecioProductoDesdeMoneda(item, $event)"
                                                                        aria-label="Precio unitario">
                                                                </div>
                                                            </td>
                                                            <td>{{formatearMoneda(item.precioVenta*item.cantidad)}}</td>
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
                                                        <div class="col-md-6 form-group" <?php echo $esRolTaller ? 'style="display:none;"' : ''; ?>>

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
                                                        <div class="col-md-6 form-group" <?php echo $esRolTaller ? 'style="display:none;"' : ''; ?>>

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
                                                    <div class="form-group mb-3" <?php echo $esRolTaller ? 'style="display:none;"' : ''; ?>>

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
                                                    <div class="form-group mb-3" <?php echo $esRolTaller ? 'style="display:none;"' : ''; ?>>
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
                                                                            <input v-model="venta.tc" @input="invalidarTasaManual"
                                                                                type="text" inputmode="decimal"
                                                                                class="form-control"
                                                                                :class="{'is-invalid': !tasaCambioValida}">
                                                                            <div v-if="cargandoTasa" class="form-text">Obteniendo tasa de cambio...</div>
                                                                            <div v-else-if="!tasaCambioValida" class="invalid-feedback">
                                                                                Ingrese una tasa válida mayor que cero.
                                                                            </div>
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

                                                    <div class="form-group mb-3" <?php echo $esRolTaller ? 'style="display:none;"' : ''; ?>>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input id="input_datos_cliente" v-model="venta.num_doc"
                                                                    type="text" placeholder="Ingrese Documento"
                                                                    class="form-control" maxlength="11">
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

                                                    <div class="form-group mb-3" <?php echo $esRolTaller ? 'style="display:none;"' : ''; ?>>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input v-model="venta.dir_cli" type="text"
                                                                    placeholder="Dirección" class="form-control"
                                                                    autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input v-model="venta.dir2_cli" type="text"
                                                                    placeholder="Atención" class="form-control"
                                                                    autocomplete="off">
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
                                                <button class="btn border-rojo" id="add-condiciones"
                                                    style="margin-bottom: 5px;">
                                                    Modificar Términos
                                                </button>

                                                <button class="btn border-rojo" id="add-diagnostico"
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
                                                        {{formatearMoneda(totalProdustos)}}
                                                    </h1>
                                                    <div class="text-uppercase">Suma Pedido</div>
                                                </div>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modales y resto del contenido -->
                    <div class="modal fade" id="modal-cotizacion-success" tabindex="-1"
                        aria-labelledby="modalCotizacionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-header text-center justify-content-center border-bottom pb-3">
                                    <div class="text-center w-100">
                                        <h4 class="modal-title text-success mb-2" id="modalCotizacionLabel">
                                            <i
                                                class="fas fa-check-circle me-2"></i>¡<?php echo $esRolOrdenTrabajo || $origenEsOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?>
                                            Actualizada!
                                        </h4>
                                        <p class="text-muted mb-0">La
                                            <?php echo $esRolOrdenTrabajo || $origenEsOrdenTrabajo ? 'Orden de Trabajo' : 'Cotización'; ?>
                                            N°
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
                                        <?php if ($esRolOrdenTrabajo || $origenEsOrdenTrabajo): ?>
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
                                    <a href="/tallers/coti/view" class="btn border-rojo">
                                        <i class="fas fa-list me-2"></i> LISTA DE
                                        <?php echo $esRolOrdenTrabajo || $origenEsOrdenTrabajo ? 'ÓRDENES DE TRABAJO' : 'COTIZACIONES'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de Configuración de Pagos -->
                    <div class="modal fade taller-pagos-modal" id="modal-dias-pagos" tabindex="-1" aria-labelledby="exampleModalLabel"
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
                                                <input :value="formatearMoneda(venta.total)" disabled type="text"
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
                                                Incluir pago inicial
                                            </label>
                                        </div>
                                        <div v-if="venta.tiene_inicial" class="input-group">
                                            <span class="input-group-text">{{monedaSibol}}</span>
                                            <input type="number" class="form-control"
                                                :value="montoEnMoneda(venta.monto_inicial)"
                                                placeholder="Monto inicial"
                                                @input="actualizarMontoInicialDesdeMoneda">
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
                                                            <td>{{formatearMoneda(venta.monto_inicial)}}</td>
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
                                                                    <span class="input-group-text">{{monedaSibol}}</span>
                                                                    <input type="number"
                                                                        class="form-control form-control-sm"
                                                                        :value="montoEnMoneda(cuota.monto)"
                                                                        @input="actualizarMontoCuotaDesdeMoneda(index, $event)">
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
                                                                S/ {{ formatoDecimal(value.precio) }} (PEN)
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
                                            <input type="file" class="form-control" name="images[]" id="imageInput"
                                                multiple accept="image/jpeg,image/png" required>
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

                    <!-- Modal de observaciones -->
                    <?php if ($esRolOrdenTrabajo || $origenEsOrdenTrabajo): ?>
                        <div class="modal fade" id="modal-observaciones" tabindex="-1"
                            aria-labelledby="observacionesModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="observacionesModalLabel">Observaciones de Orden de
                                            Trabajo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea id="observaciones-textarea" class="form-control" rows="10"
                                            placeholder="Escriba las observaciones de la orden de trabajo..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn border-rojo"
                                            data-bs-dismiss="modal">Cerrar</button>
                                        <button type="button" class="btn bg-rojo text-white"
                                            id="guardar-observaciones">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Asegurar que la instancia de Vue sea accesible globalmente
    let vueApp = null;

    $(document).on("autocompletecreate.tallerCotizaciones", "#input_datos_cliente, #input_buscar_productos", function () {
        $(this).autocomplete("widget").addClass("taller-autocomplete-menu");
    });

    // Estilos CSS para indicadores de stock
    const stockStyles = `
        <style>
            .stock-indicator {
                font-size: 11px;
                padding: 2px 6px;
                border-radius: 4px;
                background-color: #f8f9fa;
                border-left: 3px solid #dee2e6;
            }
            
            .stock-indicator .text-danger {
                font-weight: bold;
            }
            
            .stock-indicator .text-success {
                font-weight: bold;
            }
            
            .stock-indicator i {
                margin-right: 4px;
            }
            
            .table td {
                vertical-align: top;
            }
            
            /* Estilos para alertas de stock */
            .swal2-popup .text-start {
                text-align: left;
            }
            
            .swal2-popup pre {
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                border: 1px solid #dee2e6;
                margin: 10px 0;
                font-family: inherit;
            }

            /* Optimización de layout para tabla sin precios */
            .table-fixed {
                table-layout: fixed !important;
            }

            .table th,
            .table td {
                padding: 8px 12px;
                text-align: left;
                vertical-align: middle;
                word-wrap: break-word;
                overflow: hidden;
            }

            .table th:first-child,
            .table td:first-child {
                text-align: center;
                font-weight: 600;
            }

            .table th:nth-child(2),
            .table td:nth-child(2) {
                font-size: 12px;
                color: #6c757d;
                font-weight: 500;
            }

            .table th:nth-child(4),
            .table td:nth-child(4) {
                text-align: center;
                font-weight: 500;
            }

            /* Responsive para pantallas pequeñas */
            @media (max-width: 768px) {
                .table th:nth-child(2),
                .table td:nth-child(2) {
                    display: none;
                }

                .table th:nth-child(3) {
                    width: 70% !important;
                }
            }
        </style>
    `;
    
    // Insertar estilos en el head
    document.head.insertAdjacentHTML('beforeend', stockStyles);

    // Esperar a que el documento esté listo
    $(document).ready(function () {
        // Verificar que jQuery esté cargado
        if (typeof $ === 'undefined') {
            console.error('jQuery no está cargado');
            return;
        }
        if (typeof Vue === 'undefined') {
            console.error('Vue.js no está cargado');
            return;
        }

        // Destruir instancia anterior si existe
        if (vueApp) {
            vueApp.$destroy();
        }

        // Crear la instancia de Vue
        vueApp = new Vue({
            el: "#container-vue",
            data: {
                descuentoGeneral: '',
                productos: [],
                usar_scaner: false,
                scanning: false, // Agregar estado de escaneo
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
                    fotos: [], // Array para almacenar las fotos
                    tiene_inicial: false,
                    monto_inicial: 0,
                    porcentaje_inicial: 0
                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                pointSel: 1,
                equiposPreAlerta: [],
                equipoActivo: null,
                numeroCuotas: 1,
                cuotas: [],
                cargandoTasa: false,
                tasaRequestVersion: 0,
                tipoOrigen: '<?php echo $tipoOrigen; ?>' // Agregar el tipo de origen
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
                },
                'venta.moneda': function (newValue, oldValue) {
                    this.tasaRequestVersion++;
                    this.cargandoTasa = false;

                    if (newValue === '1') {
                        this.venta.tc = '';
                        return;
                    }

                    if (newValue === '2' && oldValue !== '2') {
                        this.$nextTick(() => this.actualizarTasaCambio());
                    }
                }
            },
            methods: {
                esTasaValida(valor = this.venta.tc) {
                    const tasa = Number(valor);
                    return Number.isFinite(tasa) && tasa > 0;
                },

                invalidarTasaManual() {
                    this.tasaRequestVersion++;
                    this.cargandoTasa = false;
                },

                actualizarTasaCambio() {
                    const requestVersion = ++this.tasaRequestVersion;
                    this.cargandoTasa = true;

                    $.ajax({
                        type: 'GET',
                        url: _URL + '/ajs/cotizaciones/tasa-cambio',
                        dataType: 'json',
                        headers: { 'token-app': localStorage.getItem('_token') },
                        success: (result) => {
                            if (requestVersion !== this.tasaRequestVersion || this.venta.moneda !== '2') return;

                            const tasa = result && result.data ? Number(result.data.venta) : NaN;
                            if (result && result.success && Number.isFinite(tasa) && tasa > 0) {
                                this.venta.tc = String(result.data.venta);
                            } else {
                                alertAdvertencia('No se obtuvo una tasa de cambio válida. Ingrésela manualmente.');
                            }
                        },
                        error: () => {
                            if (requestVersion === this.tasaRequestVersion && this.venta.moneda === '2') {
                                alertAdvertencia('No se pudo obtener la tasa de cambio. Ingrésela manualmente.');
                            }
                        },
                        complete: () => {
                            if (requestVersion === this.tasaRequestVersion) this.cargandoTasa = false;
                        }
                    });
                },

                montoEnMoneda(montoPen) {
                    const monto = Number(montoPen);
                    if (!Number.isFinite(monto)) return '';
                    if (this.venta.moneda !== '2') return monto.toFixed(2);
                    if (!this.esTasaValida()) return '';
                    return (monto / Number(this.venta.tc)).toFixed(2);
                },

                montoEnPen(valorMoneda) {
                    const monto = Number(valorMoneda);
                    if (!Number.isFinite(monto)) return null;
                    if (this.venta.moneda !== '2') return monto;
                    if (!this.esTasaValida()) return null;
                    return monto * Number(this.venta.tc);
                },

                actualizarPrecioProductoDesdeMoneda(item, event) {
                    const precioPen = this.montoEnPen(event.target.value);
                    if (precioPen !== null) item.precioVenta = precioPen;
                },

                formatearMoneda(montoPen) {
                    const montoMostrado = this.montoEnMoneda(montoPen);
                    if (montoMostrado === '') return this.venta.moneda === '2' ? 'TC requerido' : 'S/ 0.00';
                    return `${this.monedaSibol} ${montoMostrado}`;
                },

                actualizarMontoInicialDesdeMoneda(event) {
                    const montoPen = this.montoEnPen(event.target.value);
                    if (montoPen === null) return;
                    this.venta.monto_inicial = montoPen.toFixed(2);
                    this.calcularCuotasRestantes();
                },

                actualizarMontoCuotaDesdeMoneda(index, event) {
                    const montoPen = this.montoEnPen(event.target.value);
                    if (montoPen === null || !this.cuotas[index]) return;
                    this.cuotas[index].monto = montoPen.toFixed(2);
                    this.actualizarTotalCuotas();
                },

                validarMontosPago() {
                    const total = Number(this.venta.total);
                    const inicial = this.venta.tiene_inicial ? Number(this.venta.monto_inicial || 0) : 0;
                    if (!Number.isFinite(total) || total <= 0) {
                        alertAdvertencia('El total debe ser mayor que cero.');
                        return false;
                    }
                    if (!Number.isFinite(inicial) || inicial < 0 || inicial > total) {
                        alertAdvertencia('El pago inicial debe estar entre cero y el total.');
                        return false;
                    }
                    if (this.cuotas.some(cuota => !Number.isFinite(Number(cuota.monto)) || Number(cuota.monto) <= 0)) {
                        alertAdvertencia('Todas las cuotas deben tener un monto válido mayor que cero.');
                        return false;
                    }
                    const totalProgramado = inicial + this.cuotas.reduce((suma, cuota) => suma + Number(cuota.monto), 0);
                    if (this.cuotas.length > 0 && Math.abs(totalProgramado - total) > 0.01) {
                        alertAdvertencia('El pago inicial y las cuotas deben sumar el total de la cotización.');
                        return false;
                    }
                    return true;
                },

                // Método faltante 1: toggleCamara
                toggleCamara() {
                    if (!this.usar_scaner) {
                        this.encenderCamara();
                    } else {
                        this.cerrarCamara();
                    }
                },

                // Método faltante 2: encenderCamara
                encenderCamara() {
                    navigator.mediaDevices
                        .getUserMedia({
                            video: {
                                facingMode: "environment"
                            }
                        })
                        .then((stream) => {
                            this.scanning = true; // Actualiza el estado de escaneo

                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr");
                            btnScanQR.checked = true;
                            video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                            video.srcObject = stream;
                            video.play();

                            const tick = () => {
                                canvasElement.height = video.videoHeight;
                                canvasElement.width = video.videoWidth;
                                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);

                                this.scanning && requestAnimationFrame(tick);
                            }

                            const scan = () => {
                                try {
                                    qrcode.decode();
                                } catch (e) {
                                    setTimeout(scan, 500);
                                }
                            }

                            video.addEventListener("loadeddata", () => {
                                canvasElement.hidden = false;
                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => {
                                $("#input_buscar_productos").val(respuesta);
                                if (respuesta) {
                                    $.ajax({
                                        type: "post",
                                        url: _URL + '/ajs/compra/buscar/producto',
                                        data: {
                                            producto: respuesta // Código escaneado
                                        },
                                        success: (response) => {
                                            let data = JSON.parse(response);
                                            console.log(data);
                                            // Manejar la respuesta del servidor
                                            if (data.res == true) {
                                                let id = data.data[0].id_producto;
                                                let codigo_app = data.data[0].codigo;
                                                let codsunat = data.data[0].codsunat;
                                                let costo = data.data[0].costo;
                                                let nom_prod = data.data[0].descripcion;
                                                let precio = data.data[0].precio;
                                                let precio2 = data.data[0].precio2;
                                                let precio_unidad = data.data[0].precio_unidad;

                                                Swal.fire({
                                                    title: 'Se agrego correctamente',
                                                    text: respuesta,
                                                    icon: 'success',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                this.addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2, precio_unidad);
                                                $("#input_buscar_productos").val('');
                                                this.usar_scaner = false;
                                                this.cerrarCamara();
                                            } else {
                                                $("#input_buscar_productos").val('');
                                                // Producto no encontrado
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Advertencia',
                                                    text: 'No se encontró ningun producto',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                this.usar_scaner = false;
                                                this.cerrarCamara();
                                            }
                                        },
                                        error: () => {
                                            // Manejar errores de AJAX
                                            alert('Error al buscar el producto.');
                                        }
                                    });
                                }
                            };
                        });
                },

                // Método faltante 3: cerrarCamara
                cerrarCamara() {
                    this.usar_scaner = false;
                    this.scanning = false; // Actualiza el estado de escaneo
                    const video = document.querySelector("video");
                    const canvasElement = document.getElementById("qr-canvas");

                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach((track) => {
                            track.stop();
                        });
                    }
                    document.getElementById("btn-scan-qr").checked = false;
                    canvasElement.hidden = true;
                },

                // Método faltante 4: addProductQR
                addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2, precio_unidad) {
                    if (this.equipoActivo === null) {
                        alertAdvertencia("Por favor, seleccione un equipo primero");
                        return;
                    }

                    const nuevoProducto = {
                        productoid: id,
                        codigo: codigo_app,
                        codigo_pp: codigo_app,
                        codsunat: codsunat,
                        costo: costo,
                        descripcion: nom_prod,
                        nom_prod: nom_prod,
                        precio: precio,
                        precio2: precio2,
                        precio_unidad: precio_unidad,
                        precioVenta: precio_unidad,
                        cantidad: '1',
                        stock: '999', // Valor por defecto para QR
                        editable: false,
                        equipoActivo: this.equipoActivo,
                        almacen: '<?php echo $_SESSION["sucursal"] ?>',
                        precio_usado: 5
                    };

                    this.productos = [...this.productos, nuevoProducto];
                },

                // Método faltante 5: abrirMultipleBusaque
                abrirMultipleBusaque() {
                    $("#modalSelMultiProd").modal('show');
                },

                // Método faltante 6: busquedaKeyPess
                busquedaKeyPess(evt) {
                    const vue = this;
                    vue.listaTempProd = [];
                    if (this.dataKey.length > 0) {
                        _get("/ajs/cargar/repuestos/<?php echo $_SESSION["sucursal"] ?>?term=" + this.dataKey, (result) => {
                            console.log(result);
                            vue.listaTempProd = result;
                        });
                    }
                },

                // Método faltante 7: pasar2Poiter
                pasar2Poiter() {
                    this.itemsLista = this.itemsLista.map(e => {
                        e.cantidad = '1';
                        let array = [{
                            precio: e.precio
                        },
                        {
                            precio: e.precio2
                        },
                        {
                            precio: e.precio_unidad
                        }
                        ];
                        e.precio_unidad = array[array.length - 1].precio || 0;
                        e.precioProductos = array;
                        return e;
                    });
                    this.pointSel = 2;
                },

                // Método faltante 8: agregarProducto2Ps
                agregarProducto2Ps() {
                    this.pointSel = 1;
                    this.productos = this.productos.concat(this.itemsLista.map(e => {
                        e.precioVenta = e.precio_unidad;
                        e.edicion = false;
                        return {
                            ...e,
                            precioVenta: e.precio_unidad,
                            edicion: false,
                            productoid: e.codigo
                        };
                    }));
                    this.itemsLista = [];
                    this.listaTempProd = [];
                    this.dataKey = '';
                    $("#modalSelMultiProd").modal('hide');
                },

                // Método faltante 9: focusDiasPagos
                focusDiasPagos() {
                    $("#modal-dias-pagos").modal("show");
                },

                // Método faltante 10: generarCuotas
                generarCuotas() {
                    const numCuotas = Number(this.numeroCuotas);
                    const total = Number(this.venta.total);
                    const inicial = this.venta.tiene_inicial ? Number(this.venta.monto_inicial || 0) : 0;
                    if (!Number.isInteger(numCuotas) || numCuotas < 1 || numCuotas > 36 ||
                        !Number.isFinite(total) || total <= 0 || !Number.isFinite(inicial) ||
                        inicial < 0 || inicial > total || total - inicial <= 0) {
                        alertAdvertencia('Revise el total, el pago inicial y el número de cuotas.');
                        return;
                    }

                    this.cuotas = [];
                    const montoTotal = total - inicial;

                    const montoPorCuota = (montoTotal / numCuotas).toFixed(2);

                    // Asegurarse de que la fecha base sea válida
                    let fechaBase;
                    try {
                        fechaBase = new Date(this.venta.fecha);
                        if (isNaN(fechaBase.getTime())) {
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
                            const fechaCuota = new Date(fechaBase.getTime());
                            fechaCuota.setMonth(fechaCuota.getMonth() + i + 1);

                            console.log(`Cuota ${i + 1} - Fecha calculada:`, fechaCuota);

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

                // Método faltante 11: actualizarDiasPago
                actualizarDiasPago() {
                    try {
                        this.venta.dias_lista = this.cuotas.map(cuota => ({
                            fecha: cuota.fecha,
                            monto: cuota.monto
                        }));

                        if (this.cuotas.length > 0) {
                            this.venta.fechaVen = this.cuotas[this.cuotas.length - 1].fecha;
                        }

                        console.log("Días lista actualizado:", this.venta.dias_lista);
                        console.log("Fecha vencimiento actualizada:", this.venta.fechaVen);
                    } catch (e) {
                        console.error("Error en actualizarDiasPago:", e);
                    }
                },

                // Método faltante 12: actualizarTotalCuotas
                actualizarTotalCuotas() {
                    this.actualizarDiasPago();
                },

                // Método faltante 13: confirmarPagos
                confirmarPagos() {
                    if (this.venta.moneda === '2' && !this.esTasaValida()) {
                        alertAdvertencia('Ingrese una tasa de cambio válida antes de configurar los pagos.');
                        return;
                    }
                    if (this.venta.tipo_pago === '2' && this.cuotas.length === 0) {
                        alertAdvertencia('Debe especificar al menos una cuota para crédito');
                        return;
                    }

                    if (!this.validarMontosPago()) {
                        return;
                    }

                    if (this.venta.tiene_inicial && Number(this.venta.monto_inicial) <= 0) {
                        alertAdvertencia('Debe especificar el monto inicial');
                        return;
                    }

                    this.cuotas.forEach(cuota => {
                        if (!/^\d{4}-\d{2}-\d{2}$/.test(cuota.fecha)) {
                            const fechaObj = new Date(cuota.fecha);
                            cuota.fecha = this.formatDate(fechaObj);
                        }
                    });

                    this.venta.dias_pago = this.cuotas.map(cuota => cuota.fecha).join(',');
                    this.venta.dias_lista = this.cuotas;

                    if (this.cuotas.length > 0) {
                        this.venta.fechaVen = this.cuotas[this.cuotas.length - 1].fecha;
                    }

                    $('#modal-dias-pagos').modal('hide');
                },

                // Método faltante 14: formatDate
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

                // Método faltante 15: calcularCuotasRestantes
                calcularCuotasRestantes() {
                    const inicial = Number(this.venta.monto_inicial);
                    const total = Number(this.venta.total);
                    if (Number.isFinite(inicial) && Number.isFinite(total) && total > 0 && inicial >= 0 && inicial <= total) {
                        const porcentaje = (inicial / total) * 100;
                        this.venta.porcentaje_inicial = porcentaje.toFixed(2);
                        if (this.cuotas.length > 0 && inicial < total) this.generarCuotas();
                    }
                },

                // Método faltante 16: calcularMontoInicial
                calcularMontoInicial() {
                    const porcentaje = Number(this.venta.porcentaje_inicial);
                    const total = Number(this.venta.total);
                    if (Number.isFinite(porcentaje) && porcentaje >= 0 && porcentaje <= 100 && Number.isFinite(total) && total > 0) {
                        const monto = total * porcentaje / 100;
                        this.venta.monto_inicial = monto.toFixed(2);
                        if (this.cuotas.length > 0 && monto < total) this.generarCuotas();
                    }
                },

                // Método faltante 17: visualFechaSee
                visualFechaSee(fecha) {
                    if (!fecha) return '';
                    const fechaObj = new Date(fecha);
                    return fechaObj.toLocaleDateString('es-ES');
                },

                // Método faltante 18: formatoDecimal
                formatoDecimal(valor) {
                    if (!valor) return '0.00';
                    return parseFloat(valor).toFixed(2);
                },
                cargarDatosPreAlerta() {
                    const preAlertaId = new URLSearchParams(window.location.search).get("id");
                    const tipo = new URLSearchParams(window.location.search).get("tipo") || this.tipoOrigen;

                    console.log("Cargando datos para ID:", preAlertaId, "tipo:", tipo);

                    if (preAlertaId) {
                        _post("/ajs/taller/prealerta/info", {
                            id: preAlertaId,
                            tipo: tipo
                        }, (resp) => {
                            console.log("Respuesta de pre-alerta:", resp);

                            if (resp && resp.res && resp.data) {
                                const data = resp.data;

                                // Asignar datos del cliente
                                this.venta.num_doc = data.cliente_doc || "";
                                this.venta.nom_cli = data.cliente_nombre || "";
                                this.venta.dir_cli = data.cliente_direccion || "";
                                this.venta.fecha = data.fecha_ingreso || $("#fecha-app").val();

                                // Procesar equipos CORRECTAMENTE
                                if (data.marcas && data.marcas.length > 0) {
                                    this.equiposPreAlerta = [];

                                    for (let i = 0; i < data.marcas.length; i++) {
                                        this.equiposPreAlerta.push({
                                            marca: data.marcas[i] ? data.marcas[i].trim() : '',
                                            equipo: data.equipos[i] ? data.equipos[i].trim() : '',
                                            modelo: data.modelos[i] ? data.modelos[i].trim() : '',
                                            numero_serie: data.numeros_serie[i] ? data.numeros_serie[i].trim() : '',
                                            productos: [] // Inicializar array de productos por equipo
                                        });
                                    }

                                    // NUEVO: Cargar productos existentes y asignarlos a sus equipos correspondientes
                                    if (data.productos_existentes && data.productos_existentes.length > 0) {
                                        console.log("Productos existentes encontrados:", data.productos_existentes);

                                        // Inicializar arrays de productos para cada equipo
                                        this.equiposPreAlerta.forEach((equipo, index) => {
                                            if (!equipo.productos) {
                                                equipo.productos = [];
                                            }
                                        });

                                        // Asignar productos a sus equipos correspondientes usando el número de serie
                                        data.productos_existentes.forEach(producto => {
                                            // Buscar el índice del equipo que corresponde a este producto
                                            const equipoIndex = this.equiposPreAlerta.findIndex(equipo =>
                                                equipo.numero_serie === producto.equipoInfo.numero_serie
                                            );

                                            if (equipoIndex !== -1) {
                                                console.log(`Asignando producto ${producto.codigo_prod} al equipo ${equipoIndex} (serie: ${producto.equipoInfo.numero_serie})`);

                                             // Formatear el producto para que sea compatible con Vue
const productoFormateado = {
    productoid: producto.productoid,
    codigo_prod: producto.codigo_prod,
    descripcion: producto.descripcion,
    cantidad: producto.cantidad,
    precioVenta: producto.precioVenta,
    costo: producto.costo,
    equipoActivo: equipoIndex,
    editable: false,
    stock: '999', // Valor por defecto
    precio: producto.precioVenta,
    precio2: producto.precioVenta,
    precio_unidad: producto.precioVenta,
    almacen: '<?php echo $_SESSION["sucursal"] ?>',
    precio_usado: 5,
    tipo_item: producto.tipo_item // AGREGAR ESTA LÍNEA
};


                                                // Agregar al equipo correspondiente
                                                this.equiposPreAlerta[equipoIndex].productos.push(productoFormateado);
                                            } else {
                                                console.error(`No se encontró equipo para el producto ${producto.codigo_prod} con serie ${producto.equipoInfo.numero_serie}`);
                                            }
                                        });

                                        // Activar el primer equipo y cargar sus productos
                                        if (this.equiposPreAlerta.length > 0) {
                                            this.equipoActivo = 0;
                                            this.productos = [...(this.equiposPreAlerta[0].productos || [])];

                                            console.log("Equipo activo:", this.equipoActivo);
                                            console.log("Productos del equipo activo:", this.productos);
                                        }
                                    }

                                    // Activar el primer equipo si no se activó ninguno
                                    if (this.equipoActivo === null && this.equiposPreAlerta.length > 0) {
                                        this.equipoActivo = 0;
                                        this.productos = [...this.equiposPreAlerta[0].productos];
                                    }

                                    console.log("Equipos cargados:", this.equiposPreAlerta.length);
                                    console.log("Productos cargados:", this.productos.length);
                                }

                                this.determinarTipoDocumento(data.cliente_doc);

                                // Forzar actualización del DOM
                                this.$nextTick(() => {
                                    console.log("Vista actualizada, equipos:", this.equiposPreAlerta);
                                    console.log("Productos en vista:", this.productos);
                                });
                            } else {
                                console.error("No se recibieron datos válidos:", resp);
                            }
                        });
                    }
                },


                guardarVenta() {
                    if (this.venta.moneda === '2' && !this.esTasaValida()) {
                        alertAdvertencia('Ingrese una tasa de cambio válida antes de guardar.');
                        return;
                    }
                    if (!Number.isFinite(Number(this.venta.total)) || Number(this.venta.total) <= 0) {
                        alertAdvertencia('El total de la cotización debe ser mayor que cero.');
                        return;
                    }
                    if (this.venta.tipo_pago === '2' && !this.validarMontosPago()) return;

                    if (this.productos.length > 0) {
                        var continuar = true;
                        var mensaje = '';

                        // NUEVA VALIDACIÓN: Verificar stock antes de continuar
                        const productosSinStock = this.productos.filter(producto => {
                            const stockDisponible = parseFloat(producto.stock || 0);
                            const cantidadSolicitada = parseFloat(producto.cantidad || 0);
                            return stockDisponible < cantidadSolicitada;
                        });

                        if (productosSinStock.length > 0) {
                            const mensajeStock = productosSinStock.map(producto => {
                                const stockDisponible = parseFloat(producto.stock || 0);
                                const cantidadSolicitada = parseFloat(producto.cantidad || 0);
                                return `• ${producto.descripcion}\n  Stock disponible: ${stockDisponible}, Cantidad solicitada: ${cantidadSolicitada}`;
                            }).join('\n');

                            Swal.fire({
                                icon: "warning",
                                title: "Stock Insuficiente",
                                html: `
                                    <div class="text-start">
                                        <p><strong>Los siguientes productos no tienen stock suficiente:</strong></p>
                                        <pre class="text-danger" style="white-space: pre-wrap; font-size: 12px;">${mensajeStock}</pre>
                                        <hr>
                                        <p class="text-muted small">
                                            <i class="fa fa-info-circle"></i> 
                                            Ajuste las cantidades o contacte al administrador para actualizar el stock.
                                        </p>
                                    </div>
                                `,
                                confirmButtonText: "Entendido",
                                confirmButtonColor: "#dc3545"
                            });
                            return;
                        }

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

                            // Obtener el ID de pre-alerta y tipo de la URL
                            const urlParams = new URLSearchParams(window.location.search);
                            const preAlertaId = urlParams.get('id');
                            const tipoOrigen = urlParams.get('tipo') || this.tipoOrigen;

                            // Agregar el ID de pre-alerta y tipo al formData si existe
                            if (preAlertaId) {
                                formData.append('id_prealerta', preAlertaId);
                            }
                            if (tipoOrigen) {
                                formData.append('tipo_origen', tipoOrigen);
                            }

                            // Agregar datos de la venta
                            Object.keys(this.venta).forEach((key) => {
                                if (key !== "fotos" && key !== "dias_lista") {
                                    formData.append(key, this.venta[key])
                                }
                            })

                           // CORREGIDO: Recopilar TODOS los productos de TODOS los equipos
let todosLosProductos = [];

// Guardar productos del equipo actual antes de recopilar
if (this.equipoActivo !== null && this.equiposPreAlerta[this.equipoActivo]) {
    this.equiposPreAlerta[this.equipoActivo].productos = [...this.productos];
}

// Recopilar productos de todos los equipos
this.equiposPreAlerta.forEach((equipo, equipoIndex) => {
    if (equipo.productos && equipo.productos.length > 0) {
        equipo.productos.forEach(producto => {
            // Asegurar que cada producto tenga el equipoActivo correcto
            const productoCompleto = {
                ...producto,
                equipoActivo: equipoIndex
            };
            todosLosProductos.push(productoCompleto);
        });
    }
});

console.log("Productos a enviar:", todosLosProductos);

// Agregar datos adicionales
formData.append("usar_precio", this.usar_precio)
formData.append("listaPro", JSON.stringify(todosLosProductos))
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
                                    
                                    // DEBUG: Log de la respuesta
                                    console.log("Respuesta del servidor:", resp);
                                    console.log("Tipo de respuesta:", typeof resp);
                                    
                                    try {
                                        const response = typeof resp === "string" ? JSON.parse(resp) : resp;
                                        console.log("Respuesta parseada:", response);
                                        console.log("response.res:", response.res);
                                        console.log("response.error:", response.error);
                                        console.log("response.error_type:", response.error_type);
                                        
                                        // NUEVO: Verificar si la respuesta indica error (aunque venga en success)
                                        if (response.res === false) {
                                            console.log("Detectado error en respuesta, mostrando alerta...");
                                            
                                            // Es un error, mostrar la alerta correspondiente
                                            if (response.error_type === 'stock_insuficiente' || (response.error && response.error.includes('Stock insuficiente'))) {
                                                console.log("Mostrando alerta de stock insuficiente");
                                                
                                                // Mostrar alerta específica para stock insuficiente
                                                Swal.fire({
                                                    icon: "warning",
                                                    title: "Stock Insuficiente",
                                                    html: `
                                                        <div class="text-start">
                                                            <p><strong>No se puede procesar la cotización:</strong></p>
                                                            <p class="text-danger">${response.error}</p>
                                                            <hr>
                                                            <p class="text-muted small">
                                                                <i class="fa fa-info-circle"></i> 
                                                                Verifique el stock disponible de los productos antes de continuar.
                                                            </p>
                                                        </div>
                                                    `,
                                                    confirmButtonText: "Entendido",
                                                    confirmButtonColor: "#dc3545"
                                                });
                                            } else {
                                                console.log("Mostrando alerta de error general");
                                                
                                                // Otros tipos de error
                                                Swal.fire({
                                                    icon: "error",
                                                    title: "Error",
                                                    text: response.error || "Error al guardar la cotización",
                                                    confirmButtonText: "Cerrar"
                                                });
                                            }
                                            return; // Salir aquí, no continuar
                                        }
                                        
                                        console.log("Respuesta exitosa, continuando...");
                                        
                                        // Si llegamos aquí, la respuesta fue exitosa
                                        if (response.cotizacion) {
                                            // Actualizar el modal con los datos de la cotización
                                            $('#cotizacion-numero').text(response.cotizacion.cotizacion_id);

                                            // Asegurarse de que la URL del PDF sea correcta
                                            const pdfUrl = response.cotizacion.pdfUrl;
                                            $('#pdf-preview').attr('src', pdfUrl);
                                            $('#btn-a4').attr('href', pdfUrl);

                                            // Configurar el botón de WhatsApp
                                            const whatsappUrl = `https://wa.me/?text=Cotización N° ${response.cotizacion.cotizacion_id}%0A${encodeURIComponent(pdfUrl)}`;
                                            $('#btn-whatsapp').attr('href', whatsappUrl);

                                            // Mostrar directamente el modal con el PDF
                                            $('#modal-cotizacion-success').modal('show');
                                        } else {
                                            throw new Error("Respuesta exitosa pero sin datos de cotización");
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
                                    $("#loader-menor").hide();
                                    console.error("Error en la petición AJAX:", {
                                        status: status,
                                        error: error,
                                        response: xhr.responseText,
                                    });
                                    
                                    // Intentar parsear la respuesta para obtener el mensaje específico
                                    try {
                                        const response = JSON.parse(xhr.responseText);
                                        if (response.error && response.error.includes("Stock insuficiente")) {
                                            // Mostrar alerta específica para stock insuficiente
                                            Swal.fire({
                                                icon: "warning",
                                                title: "Stock Insuficiente",
                                                html: `
                                                    <div class="text-start">
                                                        <p><strong>No se puede procesar la cotización:</strong></p>
                                                        <p class="text-danger">${response.error}</p>
                                                        <hr>
                                                        <p class="text-muted small">
                                                            <i class="fa fa-info-circle"></i> 
                                                            Verifique el stock disponible de los productos antes de continuar.
                                                        </p>
                                                    </div>
                                                `,
                                                confirmButtonText: "Entendido",
                                                confirmButtonColor: "#dc3545"
                                            });
                                        } else if (response.error) {
                                            // Mostrar otros errores específicos
                                            Swal.fire({
                                                icon: "error",
                                                title: "Error",
                                                text: response.error,
                                                confirmButtonText: "Cerrar"
                                            });
                                        } else {
                                            // Error genérico si no se puede parsear
                                            Swal.fire({
                                                icon: "error",
                                                title: "Error",
                                                text: "Ocurrió un error al guardar la cotización",
                                                confirmButtonText: "Cerrar"
                                            });
                                        }
                                    } catch (parseError) {
                                        // Si no se puede parsear la respuesta, mostrar error genérico
                                        console.error("Error parseando respuesta:", parseError);
                                        Swal.fire({
                                            icon: "error",
                                            title: "Error",
                                            text: "Ocurrió un error al guardar la cotización",
                                            confirmButtonText: "Cerrar"
                                        });
                                    }
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
                    // Guardar el índice del equipo que se está dejando
                    const indiceAnterior = this.equipoActivo;

                    // Guardar los productos del equipo anterior antes de cambiar
                    if (indiceAnterior !== null && this.equiposPreAlerta[indiceAnterior]) {
                        this.equiposPreAlerta[indiceAnterior].productos = [...this.productos];
                    }

                    // Cambiar al nuevo equipo activo
                    this.equipoActivo = index;

                    // Cargar los productos del nuevo equipo
                    if (this.equiposPreAlerta && this.equiposPreAlerta[index]) {
                        if (!this.equiposPreAlerta[index].productos) {
                            this.equiposPreAlerta[index].productos = [];
                        }
                        this.productos = [...this.equiposPreAlerta[index].productos];
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

                // Resto de métodos de Vue...
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

                eliminarItemPro(index) {
                    this.productos.splice(index, 1)
                },

                onlyNumber($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                        $event.preventDefault();
                    }
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
                                    const direccion = typeof resp.data.direccion === 'string' ? resp.data.direccion.trim() : '';
                                    if (direccion) {
                                        app._data.venta.dir_cli = direccion;
                                    }
                                } else {
                                    alertAdvertencia("Documento no encontrado")
                                }
                            }
                        )
                    } else {
                        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos")
                    }
                },

                changeTipoPago(event) {
                    console.log(event.target.value)
                    this.venta.fechaVen = this.venta.fecha;
                    this.venta.dias_lista = []
                    this.venta.dias_pago = ''
                },

                getStockClass(item) {
                    if (parseFloat(item.stock) < parseFloat(item.cantidad)) {
                        return 'text-danger';
                    } else if (parseFloat(item.stock) === parseFloat(item.cantidad)) {
                        return 'text-success';
                    } else {
                        return 'text-muted';
                    }
                },

                getStockIcon(item) {
                    if (parseFloat(item.stock) < parseFloat(item.cantidad)) {
                        return 'fa-exclamation-triangle';
                    } else if (parseFloat(item.stock) === parseFloat(item.cantidad)) {
                        return 'fa-check-circle';
                    } else {
                        return 'fa-question-circle';
                    }
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

                    return this.formatearMoneda(total);
                },
                tasaCambioValida() {
                    return this.venta.moneda !== '2' || this.esTasaValida();
                },
                monedaSibol() {
                    return (this.venta.moneda == 1 ? 'S/' : '$')
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
            classes: {
                "ui-autocomplete": "taller-autocomplete-menu"
            },
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui.item);
                app._data.venta.dir_pos = 1
                app._data.venta.nom_cli = ui.item.datos
                app._data.venta.num_doc = ui.item.documento
                const direccion = typeof ui.item.direccion === 'string' ? ui.item.direccion.trim() : '';
                if (direccion) {
                    app._data.venta.dir_cli = direccion;
                }
            }
        });

        console.log("Vue app inicializada:", window.app);
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
