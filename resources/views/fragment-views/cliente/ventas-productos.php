<!-- resources\views\fragment-views\cliente\ventas-productos.php -->
<?php
$conexion = (new Conexion())->getConexion();

$datoEmpresa = $conexion->query("select * from empresas where id_empresa='{$_SESSION['id_empresa']}'")->fetch_assoc();

$igv_empresa = $datoEmpresa['igv'];
?>
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>
<div class="page-title-box">
    <div class="row align-items-center">
        <h6 class="page-title text-center">FACTURAR PRODUCTOS</h6>
        <div class="col-md-8">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438">Productos</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <button id="backbuttonvp" href="/ventas" type="button" class="btn border-rojo button-link"><i
                        class="fa fa-arrow-left"></i> Regresar</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="fecha-app" value="<?= date("Y-m-d") ?>">
<?php
if (isset($_GET["coti"])) {
    echo "<input type='hidden' id='cotizacion' value='{$_GET["coti"]}'>";
}
?>
<?php
if (isset($_GET["guia"])) {
    echo "<input type='hidden' id='guia' value='{$_GET["guia"]}'>";
}
?>

<div class="row" id="container-vue">
    <div class="col-12 row">
        <div class="col-md-8">
            <div class="card ">
                <div class="card-body">

                    <h4 class="card-title">Venta de Productos</h4>

                    <div class="card-title-desc">

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <form v-on:submit.prevent="addProduct" class="form-horizontal">

                                <div hidden class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Almacén</label>
                                    <div class="col-lg-3">
                                        <select class="form-control idAlmacen" v-model='producto.almacen'
                                            @change="onChangeAlmacen($event)">
                                            <option value="1">Almacén 1</option>
                                            <option value="2">Tienda 1</option>
                                        </select>
                                    </div>
                                </div>
                                <canvas hidden="" id="qr-canvas" v-show="toggleCamara"
                                    style="width: 300px; padding: 10px;"></canvas>
                                <div class="form-group row mb-3">

                                    <label class="col-lg-2 control-label">Buscar</label>

                                    <div class="col-lg-10">

                                        <div class="input-group">
                                            <input @input="chambioInputSearchProd" type="text"
                                                placeholder="Consultar Productos"
                                                class="form-control ui-autocomplete-input" id="input_buscar_productos"
                                                autocomplete="off">
                                            <div class="input-group-btn p-1">
                                                <!-- <button id="btn-scan-qr" @click="toggleCamara" class="btn btn-primary">
                                                                        Escanear QR
                                                                        </button> -->
                                                <!-- Canvas para mostrar la vista de la cámara -->

                                                <label class=""> <input id="btn-scan-qr" v-model="usar_scaner"
                                                        @click="toggleCamara" type="checkbox"> Usar
                                                    Scanner</label><br />
                                                <label @click="abrirMultipleBusaque"
                                                    style="color: blue;cursor: pointer">Busqueda Multiple</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Descripción</label>
                                    <div class="col-lg-10">
                                        <input required v-model="producto.descripcion" type="text"
                                            placeholder="Descripción" class="form-control" readonly="true">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <!-- Campo Stock Actual -->
                                        <div class="col-lg-3">
                                            <label for="example-text-input" class="col-form-label">Stock Actual</label>
                                            <input disabled v-model="producto.stock" class="form-control text-center"
                                                type="text" placeholder="0">
                                        </div>

                                        <!-- Campo Cantidad -->
                                        <div class="col-lg-3">
                                            <label for="example-text-input" class="col-form-label">Cantidad</label>
                                            <input @keypress="onlyNumber" required v-model="producto.cantidad"
                                                class="form-control text-center" type="text" placeholder="0"
                                                id="example-text-input">
                                        </div>

                                        <!-- Campo Precio -->
                                        <div class="col-lg-3">
                                            <label for="precio-input" class="col-form-label">Precio</label>
                                            <div class="input-group">
                                                <input id="precio-input" type="text" class="form-control"
                                                    :class="{'dropdown-toggle': producto.descripcion.length > 0}"
                                                    :data-bs-toggle="producto.descripcion.length > 0 ? 'dropdown' : ''"
                                                    aria-expanded="false" v-model="producto.precio_mostrado"
                                                    style="height: 38px; background-color: #f8f9fa; cursor: pointer;"
                                                    @click="mostrarMensajeProducto" readonly>
                                                <p v-if="mensajeProductoVisible && producto.descripcion.length === 0"
                                                    class="text-danger small position-absolute"
                                                    style="top: 100%; left: 0; margin-top: 2px;">
                                                    Elija un producto primero
                                                </p>
                                                <ul class="dropdown-menu" style="width: 300px; max-width: none;">
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            @click.prevent="seleccionarPrecioConTipo('PV', producto.precioVenta)"
                                                            style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                            <span style="width: 150px; display: inline-block;">Precio
                                                                Venta:</span>
                                                            <span
                                                                style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                {{ producto.precioVenta }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            @click.prevent="seleccionarPrecioConTipo('C', producto.costo)"
                                                            style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                            <span
                                                                style="width: 150px; display: inline-block;">Costo:</span>
                                                            <span
                                                                style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                {{ producto.costo }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            @click.prevent="seleccionarPrecioConTipo('PM', producto.precio_mayor)"
                                                            style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                            <span style="width: 150px; display: inline-block;">Precio
                                                                Mayorista:</span>
                                                            <span
                                                                style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                {{ producto.precio_mayor }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                            @click.prevent="seleccionarPrecioConTipo('PMn', producto.precio_menor)"
                                                            style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                            <span style="width: 150px; display: inline-block;">Precio
                                                                Menorista:</span>
                                                            <span
                                                                style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                {{ producto.precio_menor }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary dropdown-toggle"
                                                        type="button"
                                                        :data-bs-toggle="producto.descripcion.length > 0 ? 'dropdown' : ''"
                                                        aria-expanded="false" @click="mostrarMensajeProducto"
                                                      style="background-color: #CA3438; color: white; height: 38px;">
                                                        <i class="fa fa-chevron-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        style="width: 300px; max-width: none;">
                                                        <li v-for="(value, key) in precioProductos" :key="key">
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioConTipo(value.nombre, value.precio)"
                                                                style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                                <span style="width: 150px; display: inline-block;">{{
                                                                    value.nombre }}:</span>
                                                                <span
                                                                    style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                    {{ value.precio }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Botón Agregar -->
                                        <div class="col-lg-2 d-flex align-items-end">
                                            <button id="submit-a-product" type="submit"
                                                class="btn bg-rojo text-white w-100">
                                                <i class="fa fa-check"></i> Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>



                            </form>
                        </div>

                        <div class="col-md-12 mt-5">
                            <!-- <div class="row">
                                <div class="text-left col-md-9">
                                    <h4>Detalle Venta</h4>
                                </div>
                                <div class="col-md-3" v-if="productos.length > 0">
                                    <label for="">Usar</label>
                                    <select name="" id="" class="form-control text-right" v-model="usar_precio" @change="cambiarPrecio($event)">
                                        <option value="1">Precio 1</option>
                                        <option value="2">Precio 2</option>
                                        <option value="3">Precio 3</option>
                                        <option value="4">Precio Club</option>
                                        <option value="5">Precio Unidad</option>
                                    </select>
                                </div>
                            </div> -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>P. Unit.</th>
                                        <th>Parcial</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in productos">
                                        <td>{{index+1}}</td>
                                        <td>{{item.descripcion}}</td>
                                        <td><span v-if="!item.edicion">{{parseFloat(item.cantidad)}}</span><input
                                                v-if="item.edicion" v-model="item.cantidad"></td>
                                        <td><span v-if="!item.edicion">{{item.precioVenta}}</span><input
                                                v-if="item.edicion" v-model="item.precioVenta"></td>
                                        <td>{{(item.precioVenta*Number(item.cantidad)).toFixed(2)}}</td>
                                        <td style="white-space: nowrap;">
                                            <div class="d-flex">
                                                <button @click="eliminarItemPro(index)" type="button"
                                                    class="btn btn-danger btn-sm me-1">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <button @click="editarProducto(index)" class="btn btn-warning btn-sm"
                                                    title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </div>
                                            <!-- <button v-if="item.edicion" @click="item.edicion=false"
        class="btn btn-warning btn-sm"><i class="fa fa-save"></i></button> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Modal de Edición -->
                        <div class="modal fade" id="modalEditarProducto" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-rojo text-white">
                                        <h5 class="modal-title">Editar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" class="form-control" v-model="productoEdit.descripcion">
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Cantidad</label>
                                                <input type="number" class="form-control"
                                                    v-model="productoEdit.cantidad" @keypress="onlyNumber">
                                            </div>
                                            <!-- Precio con dropdown en el modal de edición -->
                                            <div class="mb-3">
                                                <label class="form-label">Precio</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        v-model="productoEdit.precio_mostrado"
                                                        style="background-color: #f8f9fa; cursor: pointer;" readonly>
                                                    <ul class="dropdown-menu w-100">
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('PV', productoEdit.precioVenta)">
                                                                PV: {{ productoEdit.precioVenta }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('C', productoEdit.costo)">
                                                                C: {{ productoEdit.costo }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('PM', productoEdit.precio_mayor)">
                                                                PM: {{ productoEdit.precio_mayor }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('PMn', productoEdit.precio_menor)">
                                                                PMn: {{ productoEdit.precio_menor }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false"
                                                            style="background-color: #CA3438; color: white;">
                                                            <i class="fa fa-chevron-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li v-for="(value, key) in precioProductos" :key="key">
                                                                <a class="dropdown-item" href="#"
                                                                    @click.prevent="seleccionarPrecioEditConTipo(value.nombre, value.precio)"
                                                                    style="color: #333; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
                                                                    <span>{{ value.nombre }}:</span>
                                                                    <span
                                                                        style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px;">S/
                                                                        {{ value.precio }}</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn border-rojo"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn bg-rojo text-white"
                                            @click="actualizarProducto">
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="card ">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="widget padding-0 white-bg">
                            <div class="padding-20 text-center">
                                <form v-on:submit.prevent role="form" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label class="control-label">Aplicar IGV Venta</label>
                                            <select :disabled="!apli_igv_is" v-model="venta.apli_igv"
                                                class="form-control">
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="control-label">Documento</label>
                                            <div class="col-md-12">
                                                <select @change="onChangeTiDoc($event)" v-model="venta.tipo_doc"
                                                    class="form-control">
                                                    <option value="1">BOLETA DE VENTA</option>
                                                    <option value="2">FACTURA</option>
                                                    <option value="6">NOTA DE VENTA</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="control-label">Tipo Pago</label>
                                            <select v-model="venta.tipo_pago" @change="changeTipoPago"
                                                class="form-control">
                                                <option value="1">Contado</option>
                                                <option value="2">Crédito</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="control-label">Método Pago</label>
                                            <select class="form-control" v-model='venta.metodo'>
                                                <option v-for="(value, key) in metodosPago"
                                                    :value="value.id_metodo_pago" :key="key">{{ value.nombre }}</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Ser | Num</label>
                                        <div class="col-lg-12 row">
                                            <div class="col-lg-6">
                                                <input v-model="venta.serie" type="text"
                                                    class="form-control text-center" readonly="">
                                            </div>
                                            <div class="col-lg-6">
                                                <input v-model="venta.numero" type="text"
                                                    class="form-control text-center" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <label class="col-lg-4 control-label"> </label>
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Moneda</label>
                                                        <div class="col-lg-12">
                                                            <select v-model="venta.moneda" class="form-control">
                                                                <option value="1">SOLES</option>
                                                                <option value="2">DOLARES</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="venta.moneda =='2' ">
                                                    <div class="form-group ">
                                                        <label class="control-label">Tasa de cambio</label>
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.tc" type="text"
                                                                @keypress="onlyNumberDecimal($event)"
                                                                style="outline: none; border: 1px solid  #a49c9c; padding:5px; width: 100px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <label class="col-lg-4 control-label">Fecha</label>
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Emisión</label>
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.fecha" type="date"
                                                                placeholder="dd/mm/aaaa" name="input_fecha"
                                                                class="form-control text-center" value="2021-10-16">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Vencimiento</label>
                                                        <div class="col-lg-12">
                                                            <input disabled v-model="venta.fechaVen" type="date"
                                                                placeholder="dd/mm/aaaa" name="input_fecha"
                                                                class="form-control text-center" value="2021-10-16">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="venta.tipo_pago=='2'" class="form-group ">
                                        <label class="control-label">Días de pago</label>
                                        <div class="col-lg-12">
                                            <input @focus="focusDiasPagos" v-model="venta.dias_pago" type="text"
                                                class="form-control text-center">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Cliente</label>
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">

                                                <input id="input_datos_cliente" v-model="venta.num_doc" type="text"
                                                    placeholder="Ingrese Documento" class="form-control" maxlength="11">
                                                <div class="input-group-prepend">
                                                    <button @click="buscarDocumentSS" class="btn bg-rojo text-white"
                                                        type="button"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <input v-model="venta.nom_cli" type="text" placeholder="Nombre del cliente"
                                                class="form-control ui-autocomplete-input" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">
                                                <input v-model="venta.dir_cli" type="text" placeholder="Dirección"
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
                                                <!-- <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">
                                                        <input v-model="venta.dir_pos" name="dirserl" value="1"
                                                            type="radio" class="form-check-input">
                                                    </span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">
                                                <input v-model="venta.dir2_cli" type="text" placeholder="Asunto"
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
                                                <!-- <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">
                                                        <input :disabled="!isDirreccionCont" v-model="venta.dir_pos"
                                                            name="dirserl" value="2" type="radio"
                                                            class="form-check-input">
                                                    </span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <label>Observaciones</label>
                                            <div class="input-group">

                                                <input v-model="venta.observ" type="text" placeholder=""
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Campo Doc. de Referencia - solo visible cuando viene de cotización -->
<div class="form-group mb-3" v-if="vieneDesCotizacion">
    <div class="col-lg-12">
        <label>Doc. de Referencia</label>
        <div class="input-group">
            <input v-model="venta.doc_referencia" type="text" placeholder="Ingrese documento de referencia" 
                   class="form-control ui-autocomplete-input" autocomplete="off">
        </div>
    </div>
</div>

                                    <div class="form-group  mb-3">

                                        <!-- <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Paga con</label>
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.pagacon" @keypress="onlyNumber"
                                                                type="text" placeholder=""
                                                                class="form-control text-center">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Vuelto</label>
                                                        <div class="col-lg-12">
                                                            <input :value="vuelDelPago" disabled type="text"
                                                                class="form-control text-center">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="form-group  mb-3">
                                        <label>Cantidad de Pagos</label>
                                        <select class="form-control" v-model="venta.cantidadPagos">
                                            <option value="1">1 Pago</option>
                                            <option value="2">2 Pagos</option>
                                            <option value="3">3 Pagos</option>
                                            <option value="4">4 Pagos</option>
                                            <option value="5">5 Pagos</option>
                                        </select>
                                    </div>

                                    <div v-for="(index, pagoIndex) in parseInt(venta.cantidadPagos)" :key="pagoIndex">
                                        <!-- <div class="col-md-12 form-group">
                                            <label class="control-label">Método de Pago {{ pagoIndex + 1 }}</label>
                                            <select class="form-control" v-model="venta.pagos[pagoIndex].metodoPago">
                                                <option v-for="(value, key) in metodosPago"
                                                    :value="value.id_metodo_pago" :key="key">{{ value.nombre }}</option>
                                            </select>
                                        </div> -->

                                        <div class="form-group mb-3">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Método de Pago {{ pagoIndex + 1
                                                                }}</label>
                                                            <select class="form-control"
                                                                v-model="venta.pagos[pagoIndex].metodoPago">
                                                                <option value="">Seleccione método</option>
                                                                <option v-for="(value, key) in metodosPago"
                                                                    :value="value.id_metodo_pago" :key="key">{{
                                                                    value.nombre }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Monto de Pago {{ pagoIndex + 1
                                                                }}</label>
                                                            <div class="col-lg-12">
                                                                <input v-model="venta.pagos[pagoIndex].montoPago"
                                                                    @keypress="onlyNumber" type="text" placeholder=""
                                                                    class="form-control text-center">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <button @click="guardarVenta" type="button"
                                                class="btn btn-lg bg-rojo text-white" id="btn_finalizar_pedido">
                                                <i class="fa fa-save"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="bg-rojo text-white pv-15 text-center  p-3" style="height: 90px; color: white">
                                <h1 class="mv-0 font-400" id="lbl_suma_pedido">{{monedaSibol}} {{totalProdustos}}</h1>
                                <div class="text-uppercase">Suma Pedido</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


    <!-- Modal de Configuración de Pagos Modificado -->
    <div class="modal fade" id="modal-dias-pagos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h3 class="modal-title" id="exampleModalLabel">Configuración de Pagos</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <input :value="'S/ '+venta.total" disabled type="text" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Pago inicial -->
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" v-model="venta.tiene_inicial"
                                id="checkInicial">
                            <label class="form-check-label" for="checkInicial">
                                <input class="form-check-input" type="checkbox" v-model="venta.tiene_inicial"
                                    id="checkInicial">
                                <label class="form-check-label" for="checkInicial">
                                    Incluir pago inicial
                                </label>
                        </div>
                        <div v-if="venta.tiene_inicial" class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" class="form-control" v-model="venta.monto_inicial"
                                placeholder="Monto inicial" @input="calcularCuotasRestantes">
                            <span class="input-group-text">o</span>
                            <input type="number" class="form-control" v-model="venta.porcentaje_inicial" placeholder="%"
                                min="0" max="100" @input="calcularMontoInicial">
                        </div>
                    </div>

                    <!-- Número de cuotas -->
                    <div class="mb-3">
                        <label class="form-label">Número de cuotas</label>
                        <div class="input-group">
                            <input type="number" class="form-control" v-model="numeroCuotas" min="1" max="36"
                                @change="generarCuotas">
                            <button class="btn bg-rojo text-white" type="button" @click="generarCuotas">Generar</button>
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
                                                    <input type="number" class="form-control form-control-sm"
                                                        v-model="cuota.monto" @input="actualizarTotalCuotas">
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
                    <button type="button" class="btn  bg-white text-rojo border-rojo"
                        data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn bg-rojo text-white" @click="confirmarPagos">Confirmar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalImprimirComprobante" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Imprimir Comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <button id="ce-t-a4" class="print-pfd-sld mt-2 btn bg-rojo text-white"><i
                            class="fa fa-file-pdf"></i> Hoja
                        A4</button>
                    <button id="ce-t-a4-m" class="print-pfd-sld mt-2 btn bg-rojo text-white"><i
                            class="fa fa-file-pdf"></i>
                        Media Hoja A4</button>
                    <button id="ce-t-8cm" class="print-pfd-sld mt-2 btn border-rojo"><i class="fas fa-file-invoice"></i>
                        Voucher 8cm</button>
                    <button id="ce-t-5_6cm" class="print-pfd-sld mt-2 btn border-rojo"><i
                            class="fas fa-file-invoice"></i>
                        Voucher 5.8cm</button>

                </div>
                <div class="modal-footer">
                    <a href="/ventas" class="btn border-rojo">Cerrar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSelMultiProd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Busqueda Multiple</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="pointSel==1">
                        <div class="mb-3">
                            <label class="form-label">Buscar Producto</label>
                            <input v-model="dataKey" @keyup="busquedaKeyPess" type="text" class="form-control">
                        </div>

                        <div class="list-group" style=" height: 300px; overflow-y: scroll;">
                            <label v-for="item in listaTempProd" class="list-group-item list-group-item-action"><input
                                    v-model="itemsLista" :value="item" type="checkbox"> {{item.value}}</label>
                        </div>
                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                            <button @click="pasar2Poiter" class="btn bg-rojo text-white">Continuar</button>
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
                                    <th><input style="width: 80px;" v-model="item.cantidad" /></th>
                                    <th>
                                        <select style="width: 80px;" class="form-control" v-model="item.precio_unidad">
                                            <option v-for="(value, key) in item.precioProductos" :value="value.precio"
                                                :key="key">{{ value.precio }}</option>
                                        </select>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                            <button @click="pointSel=1" class="btn border-rojo">Regresar</button>
                            <button @click="agregarProducto2Ps" class="btn bg-rojo text-white">Agregar</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


</div>



<script>

    $(document).ready(function () {

        console.log($('.idAlmacen').val());

        const app = new Vue({
            el: "#container-vue",
            data: {
                enProceso: true,
                usar_scaner: false,
                apli_igv_is: true,
                mensajeProductoVisible: false, 
                producto: {
                    edicion: false,
                    productoid: "",
                    descripcion: "",
                    nom_prod: "",
                    cantidad: "",
                    stock: "",
                    codigo: "",
                    costo: "",
                    codsunat: "",
                    precio: '',
                    almacen: '<?php echo $_SESSION["sucursal"] ?>',
                    precio2: '',
                    // precio3: '',
                    // precio4: '',
                    precio_unidad: '',
                    precioVenta: '',
                    precio_usado: 1,
                    tipo_precio: 'PV',
                    precio_mostrado: '',
                },
                productoEdit: {
                    index: -1,
                    descripcion: '',
                    cantidad: '',
                    precio: '',
                    stock: '',
                    productoid: '',
                    precio_mostrado: '', // Nuevo
                    precioVenta: '', // Nuevo
                    costo: '', // Nuevo
                    precio_mayor: '', // Nuevo
                    precio_menor: '', // Nuevo
                    tipo_precio: 'PV' // Nuevo
                },
                usar_precio: '5',
                productos: [],
                metodosPago: [],
                precioProductos: [],
                venta: {
                    cantidadPagos: 0,
                    pagos: Array.from({
                        length: 5
                    }, () => ({
                        metodoPago: null,
                        montoPago: null
                    })),
                    segundoPago: false,
                    pagacon2: '',
                    pagacon: '',
                    observ: '',
                    apli_igv: 1,
                    dir_pos: 1,
                    tipo_doc: '2',
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
                    metodo: 12,
                    metodo2: 12,
                    moneda: 1,
                    tc: '',
                     doc_referencia: '',
                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                numeroCuotas: 1,
                cuotas: [],
                pointSel: 1,
                 vieneDesCotizacion: false,
            },
            watch: {

                // 'venta.dias_pago'(newValue) {
                //     const listD = (newValue + "").split(",");
                //     this.dias_lista = [];
                //     if (listD.length > 0) {

                //         var listaTemp = listD.filter(ite => ite.length > 0)
                //         const palorInicial = (parseFloat(this.venta.total + "") / listaTemp.length).toFixed(0)
                //         var totalValos = parseFloat(this.venta.total + "");
                //         listaTemp = listaTemp.map((num, index) => {
                //             var fecha_ = new Date(this.venta.fecha)
                //             const dias_ = parseInt(num + "")
                //             fecha_.setDate(fecha_.getDate() + dias_);
                //             var value = 0;
                //             if (index + 1 == listaTemp.length) {
                //                 value = totalValos;
                //                 this.venta.fechaVen = this.formatDate(fecha_)
                //             } else {
                //                 value = palorInicial;
                //                 totalValos -= palorInicial;
                //             }
                //             return {
                //                 fecha: this.formatDate(fecha_),
                //                 monto: value
                //             }
                //         });
                //         //console.log(palorInicial+"<<<<<<<<<<<<<")
                //         this.venta.dias_lista = listaTemp
                //         //console.log(listaTemp);
                //     }

                // },
                'venta.cantidadPagos'(newValue) {
                    // Asegurarse de que todos los pagos tengan valores iniciales
                    const cantidadPagos = parseInt(newValue);
                    if (cantidadPagos > 0) {
                        // Si el total está disponible, distribuir equitativamente
                        const montoIndividual = this.venta.total > 0 ? (this.venta.total / cantidadPagos).toFixed(2) : '';

                        // Inicializar o actualizar cada pago
                        for (let i = 0; i < 5; i++) {
                            if (i < cantidadPagos) {
                                // Si el pago ya existe, mantener su método pero actualizar el monto
                                if (!this.venta.pagos[i].metodoPago) {
                                    this.venta.pagos[i].metodoPago = this.venta.metodo; // Usar el método principal por defecto
                                }
                                this.venta.pagos[i].montoPago = montoIndividual;
                            } else {
                                // Reiniciar pagos no utilizados
                                this.venta.pagos[i].metodoPago = null;
                                this.venta.pagos[i].montoPago = null;
                            }
                        }
                    }
                },
                'venta.moneda'(newVal) {
                    if (newVal == '1') {
                        this.venta.tc = '';
                    } else if (newVal == '2') {
                        // Asegurar que siempre haya un tipo de cambio válido para dólares
                        if (!this.venta.tc || this.venta.tc.trim() === '' || parseFloat(this.venta.tc) <= 0) {
                            this.venta.tc = '3.70';
                        }
                    }
                }

            },
            methods: {
                mostrarMensajeProducto() {
                    // Mostrar el mensaje solo si no hay producto seleccionado
                    if (this.producto.descripcion.length === 0) {
                        this.mensajeProductoVisible = true;
                    }
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
                // Actualizar el campo dias_pago basado en las fechas seleccionables
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


                seleccionarPrecioConTipo(tipo, precio) {
                    this.producto.precio_mostrado = precio;
                    this.producto.precio = precio;
                    this.producto.tipo_precio = tipo;

                    // Si es precio de venta, actualizar también precioVenta
                    if (tipo === 'PV') {
                        this.producto.precioVenta = precio;
                    }

                    // Cerrar el dropdown después de seleccionar
                    const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });

                    // Ocultar el mensaje cuando se selecciona un precio
                    this.mensajeProductoVisible = false;
                },

                seleccionarPrecioEditConTipo(tipo, precio) {
                    this.productoEdit.precio_mostrado = precio;
                    this.productoEdit.precio = precio;
                    this.productoEdit.tipo_precio = tipo;

                    if (tipo === 'PV') {
                        this.productoEdit.precioVenta = precio;
                    }
                },
                mounted() {
                    // Recuperar productos guardados si existen
                    const productosGuardados = localStorage.getItem('productosCotizacion');
                    if (productosGuardados) {
                        this.productos = JSON.parse(productosGuardados);
                    }
                },

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
                                                app.addProductQR(id,
                                                    codigo_app, codsunat,
                                                    costo,
                                                    nom_prod,
                                                    precio,
                                                    precio2,
                                                    // precio3,
                                                    // precio4,
                                                    precio_unidad);
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
                        // {
                        //     precio: e.precio3
                        // },
                        // {
                        //     precio: e.precio4
                        // },
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
                        _get("/ajs/cargar/productos/<?php echo $_SESSION["sucursal"] ?>?term=" + this.dataKey, (result) => {
                            console.log(result)
                            vue.listaTempProd = result
                        })
                    }

                },
                abrirMultipleBusaque() {
                    $("#modalSelMultiProd").modal('show')
                },
                chambioInputSearchProd() {
                    const codInput = $("#input_buscar_productos").val().trim();
                    if (this.usar_scaner) {
                        if (codInput.length > 3) {
                            _post("/ajs/data/producto/info/code", {
                                code: codInput,
                                almacen: this.producto.almacen
                            },
                                function (resp) {
                                    console.log(resp.data);
                                    if (resp.res) {
                                        const ui = {
                                            item: resp.data
                                        }
                                        app.producto.productoid = ui.item.codigo
                                        app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre
                                        app.producto.nom_prod = ui.item.descripcion
                                        app.producto.cantidad = ''
                                        app.producto.stock = ui.item.cantidad
                                        app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2)
                                        app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2)
                                        // app.producto.precio3 = ui.item.precio3 == null ? parseFloat(0 + "").toFixed(4) : parseFloat(ui.item.precio3 + "").toFixed(4)
                                        // app.producto.precio4 = ui.item.precio4 == null ? parseFloat(0 + "").toFixed(4) : parseFloat(ui.item.precio4 + "").toFixed(4)
                                        app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(3) : parseFloat(ui.item.precio_unidad + "").toFixed(2)
                                        app.producto.precioVenta = parseFloat(ui.item.precio_unidad + "").toFixed(0)
                                        app.producto.codigo = ui.item.codigo
                                        app.producto.costo = ui.item.costo
                                        let array = [{
                                            precio: app.producto.precio
                                        },
                                        {
                                            precio: app.producto.precio2
                                        },
                                        // {
                                        //     precio: app.producto.precio3
                                        // },
                                        // {
                                        //     precio: app.producto.precio4
                                        // },
                                        {
                                            precio: app.producto.precio_unidad
                                        }
                                        ]

                                        app.precioProductos = array
                                        console.log(array);
                                        $("#input_buscar_productos").val('')
                                        $("#example-text-input").focus();
                                    } else {
                                        //alertAdvertencia("No se encontro algun producto con el codigo: "+$("#input_buscar_productos").val())
                                        $("#input_buscar_productos").val('')
                                    }
                                }
                            )
                        }
                    }
                },
                cambiarPrecio(event) {
                    console.log(event.target.value)

                    var self = this

                    this.productos.forEach(element => {
                        if (event.target.value == 1) {
                            element.precioVenta = element.precio
                            /*  ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2) */
                            element.precio_usado = '1'
                        } else if (event.target.value == 2) {
                            element.precioVenta = element.precio2
                            element.precio_usado = '2'
                            // } else if (event.target.value == 3) {
                            //     element.precioVenta = element.precio3
                            //     element.precio_usado = '3'

                            // } else if (event.target.value == 4) {
                            //     element.precioVenta = element.precio4
                            //     element.precio_usado = '4'
                        } else {
                            element.precioVenta = element.precio_unidad
                            element.precio_usado = '5'
                        }

                    });
                },
                buscarPorCodigoBarra() {

                },
                // Modifica la función cargarCotizacion() así:
                cargarCotizacion() {
                    const vue = this;
                    _post("/ajs/cotizaciones/info", {
                        coti: $("#cotizacion").val()
                    },
                        function (resp) {
                            console.log("aaaaaaaaa", resp);
                            vue.productos = resp.productos.map(ert => {
                                ert.descripcion = ert.codigo.toString().trim() + ' | ' + ert.descripcion
                                ert.edicion = false
                                return ert
                            })

                            // Determinar tipo de documento basado en el número de documento del cliente
                            if (resp.cliente_doc) {
                                if (resp.cliente_doc.length === 11) {
                                    vue.venta.tipo_doc = '2'; // Factura para RUC
                                } else if (resp.cliente_doc.length === 8) {
                                    vue.venta.tipo_doc = '1'; // Boleta para DNI
                                } else {
                                    vue.venta.tipo_doc = resp.id_tido; // Mantener el tipo original si no es RUC ni DNI
                                }
                            }

                            vue.venta.moneda = resp.moneda
                            vue.venta.tc = resp.cm_tc
                            vue.venta.tipo_pago = resp.id_tipo_pago
                            vue.venta.dias_pago = resp.dias_pagos
                            vue.venta.dir_pos = parseInt(resp.direccion + "")
                            vue.venta.num_doc = resp.cliente_doc
                            vue.venta.nom_cli = resp.cliente_nom
                            vue.venta.dir_cli = resp.cliente_dir1
                            vue.venta.dir2_cli = resp.cliente_dir2
                            vue.usar_precio = resp.usar_precio

                            // Actualizar serie y número después de determinar el tipo de documento
                            vue.buscarSNdoc();

                            setTimeout(function () {
                                vue.venta.dias_lista = resp.cuotas
                            }, 1000)
                        })
                },

                // Modifica la función cargarDatosGuia() así:
                cargarDatosGuia() {
                    const vue = this;
                    const guiaId = $("#guia").val();

                    if (!guiaId) {
                        console.log("No guide ID found");
                        return;
                    }

                    _post("/ajs/guia/remision/info", {
                        guia: guiaId
                    }, function (resp) {
                        console.log("Guide API response:", resp);

                        if (resp.res) {
                            if (Array.isArray(resp.productos) && resp.productos.length > 0) {
                                vue.productos = resp.productos;
                            } else {
                                vue.productos = [];
                            }

                            // Determinar tipo de documento basado en el número de documento del cliente
                            if (resp.cliente_doc) {
                                if (resp.cliente_doc.length === 11) {
                                    vue.venta.tipo_doc = '2'; // Factura para RUC
                                } else if (resp.cliente_doc.length === 8) {
                                    vue.venta.tipo_doc = '1'; // Boleta para DNI
                                } else {
                                    vue.venta.tipo_doc = '6'; // NOTA DE VENTA por defecto
                                }
                            }

                            vue.venta.num_doc = resp.cliente_doc || '';
                            vue.venta.nom_cli = resp.cliente_nombre || '';
                            vue.venta.dir_cli = resp.cliente_direccion || '';
                            vue.venta.tipo_pago = '1'; // Contado
                            vue.venta.fecha = resp.guia.fecha_emision || $("#fecha-app").val();

                            // Actualizar serie y número después de determinar el tipo de documento
                            vue.buscarSNdoc();
                        } else {
                            console.error("Error loading guide data:", resp);
                            alertAdvertencia("Error al cargar datos de la guía: " + (resp.error || 'Error desconocido'));
                        }
                    });
                },


                // Add created hook to Vue instance



                formatoDecimal(num, desc = 2) {
                    return parseFloat(num + "").toFixed(desc);
                },
                visualFechaSee(fecha) {
                    return formatFechaVisual(fecha);
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
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 44) { // 46 is dot
                        $event.preventDefault();
                    }
                },
                focusDiasPagos() {
                    //console.log("1000000000000000000")
                    $("#modal-dias-pagos").modal("show")
                },
                changeTipoPago(event) {
                    console.log(event.target.value)
                    this.venta.fechaVen = this.venta.fecha;
                    this.venta.dias_lista = []
                    this.venta.dias_pago = ''
                },
                onChangeAlmacen(event) {
                    /*    window.localStorage.removeItem('idChecks'); */
                    console.log(event.target.value)
                    this.producto.almacen = event.target.value
                    var self = this
                    $("#input_buscar_productos").autocomplete({

                        source: _URL + `/ajs/cargar/productos/${self.producto.almacen}`,
                        minLength: 1,
                        select: function (event, ui) {
                            event.preventDefault();
                            /*    console.log(item);
                               console.log(ui); */
                            console.log(ui.item);
                            /*   return */
                            app.producto.productoid = ui.item.codigo
                            app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre
                            app.producto.nom_prod = ui.item.descripcion
                            app.producto.cantidad = ''
                            app.producto.stock = ui.item.cnt
                            app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio
                            app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2)
                            // app.producto.precio3 = ui.item.precio3 == null ? parseFloat(0 + "").toFixed(4) : parseFloat(ui.item.precio3 + "").toFixed(4)
                            // app.producto.precio4 = ui.item.precio4 == null ? parseFloat(0 + "").toFixed(4) : parseFloat(ui.item.precio4 + "").toFixed(4)
                            app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad
                            app.producto.codigo = ui.item.codigo
                            app.producto.costo = ui.item.costo
                            app.producto.precioVenta = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad
                            let array = [{
                                precio: app.producto.precio
                            },
                            {
                                precio: app.producto.precio2
                            },
                            // {
                            //     precio: app.producto.precio3
                            // },
                            // {
                            //     precio: app.producto.precio4
                            // },
                            {
                                precio: app.producto.precio_unidad
                            }
                            ]

                            app.precioProductos = array
                            console.log(array);
                            $('#input_buscar_productos').val("");
                            $("#example-text-input").focus()
                        }
                    });
                },
                onlyNumber($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                        $event.preventDefault();
                    }
                    // Evitar más de un punto decimal
                    if (keyCode === 46 && $event.target.value.includes('.')) {
                        $event.preventDefault();
                    }
                },
                eliminarItemPro(index) {
                    this.productos.splice(index, 1)
                    /*  this.producto.almacen = 1 */
                },
                // Modifica la función buscarDocumentSS() así:
                buscarDocumentSS() {
                    if (this.venta.num_doc.length == 8 || this.venta.num_doc.length == 11) {
                        $("#loader-menor").show()
                        this.venta.dir_pos = 1

                        // Determinar tipo de documento basado en la longitud
                        if (this.venta.num_doc.length == 11) {
                            // Es RUC - cambiar a Factura (2)
                            this.venta.tipo_doc = '2';
                        } else if (this.venta.num_doc.length == 8) {
                            // Es DNI - cambiar a Boleta (1)
                            this.venta.tipo_doc = '1';
                        }

                        // Actualizar serie y número después de cambiar tipo_doc
                        this.buscarSNdoc();

                        _ajax("/ajs/consulta/doc/cliente", "POST", {
                            doc: this.venta.num_doc
                        },
                            function (resp) {
                                $("#loader-menor").hide()
                                console.log(resp);
                                if (resp.res) {
                                    app._data.venta.nom_cli = (resp.data.nombre ? resp.data.nombre : '') + (resp.data.razon_social ? resp.data.razon_social : '')
                                    if (typeof resp.data.direccion !== 'undefined') {
                                        app._data.venta.dir_cli = resp.data.direccion.trim().length > 0 ? resp.data.direccion : '-'
                                    }
                                } else {
                                    alertAdvertencia("Documento no encontrado")
                                }
                            })
                    } else {
                        alertAdvertencia("Documento, DNI es 8 digitos y RUC 11 digitos")
                    }
                },

                guardarVenta() {
                    console.log("Moneda:", this.venta.moneda);
                    console.log("Tipo de cambio:", this.venta.tc);
                    console.log("Tipo de documento:", this.venta.tipo_doc);
                    console.log("Número de documento:", this.venta.num_doc);
                    console.log("Tipo de pago:", this.venta.tipo_pago);
                    console.log("Días de pago:", this.venta.dias_lista);
                    console.log("Total de la venta:", this.venta.total);

                    const vuee = this
                    if (this.enProceso) {
                        this.enProceso = false
                        if (this.productos.length > 0) {
                            var continuar = true;
                            var mensaje = '';

                            if (this.venta.tipo_doc == '1') {
                                if (this.venta.num_doc.length == 11) {
                                    continuar = false;
                                    mensaje = 'No puede emitir Boleta usando RUC';
                                }
                                if (this.venta.tipo_pago == 2) {
                                    if (this.venta.dias_lista.length == 0) {
                                        continuar = false;
                                        mensaje = 'Debe especificar los días de pagos para un venta a crédito';
                                    }
                                }
                                if (this.venta.moneda == '2') {
                                    const tc = parseFloat(this.venta.tc);
                                    if (!this.venta.tc || this.venta.tc.trim() === '' || isNaN(tc) || tc <= 0) {
                                        continuar = false;
                                        mensaje = 'Debe especificar un tipo de cambio válido cuando la moneda es Dólares';
                                    }
                                }
                            } else if (this.venta.tipo_doc == '2') {
                                if (this.venta.nom_cli.length < 5) {
                                    mensaje = 'Debe escribir la Razón Social o dar al botón para buscar el ruc';
                                    continuar = false;
                                }
                                if (this.venta.num_doc.length != 11) {
                                    mensaje = 'Solo se puede emitir Factura usando RUC';
                                    continuar = false;
                                }
                                if (this.venta.tipo_pago == 2) {
                                    if (this.venta.dias_lista.length == 0) {
                                        continuar = false;
                                        mensaje = 'Debe especificar los días de pagos para un venta a crédito';
                                    }
                                }
                                if (this.venta.moneda == '2') {
                                    const tc = parseFloat(this.venta.tc);
                                    if (!this.venta.tc || this.venta.tc.trim() === '' || isNaN(tc) || tc <= 0) {
                                        continuar = false;
                                        mensaje = 'Debe especificar un tipo de cambio válido cuando la moneda es Dólares';
                                    }
                                }
                            }

                            if (continuar) {
                                if (this.venta.total > 0) {
                                    let idCoti = JSON.parse('<?php echo addslashes(json_encode(isset($_GET["coti"]) ? $_GET["coti"] : null)); ?>');
                                    const data = {
                                        ...this.venta,
                                        listaPro: JSON.stringify(this.productos),
                                        datosGuiaRemosion: localStorage.getItem('datosGuiaRemosion'),
                                        datosTransporteGuiaRemosion: localStorage.getItem('datosTransporteGuiaRemosion'),
                                        productosGuiaRemosion: localStorage.getItem('productosGuiaRemosion'),
                                        datosUbigeoGuiaRemosion: localStorage.getItem('datosUbigeoGuiaRemosion'),
                                        idCoti: idCoti
                                    }
                                    data.dias_lista = JSON.stringify(data.dias_lista)

                                    _ajax("/ajs/ventas/add", "POST",
                                        data,
                                        function (resp) {
                                            vuee.enProceso = true
                                            console.log(resp);

                                            let desde = localStorage.getItem('desde')
                                            if (resp.res) {
                                                alertExito("Exito", "Venta Guardada").then(function () {
                                                    $("#ce-t-a4").attr("href", _URL + "/venta/comprobante/pdf/" + resp.venta + "/" + resp.nomxml);
                                                    $("#ce-t-a4-m").attr("href", _URL + "/venta/comprobante/pdf/ma4/" + resp.venta + "/" + resp.nomxml);
                                                    $("#ce-t-8cm").attr("href", _URL + "/venta/pdf/voucher/8cm/" + resp.venta + "/" + resp.nomxml);
                                                    $("#ce-t-5_6cm").attr("href", _URL + "/venta/pdf/voucher/5.6cm/" + resp.venta + "/" + resp.nomxml);
                                                    $("#modalImprimirComprobante").modal("show");
                                                })
                                                    .then(function () {
                                                        //location.reload();
                                                    })

                                                if (desde == 'coti_guia') {
                                                    let idVenta = {
                                                        idVenta: resp.venta
                                                    }
                                                    data.idVenta = resp.venta
                                                    _ajax("/ajs/guia/remision/add2", "POST", {
                                                        data
                                                    },
                                                        function (resp) {
                                                            console.log(resp);
                                                            localStorage.removeItem("desde");
                                                            localStorage.removeItem("datosGuiaRemosion");
                                                            localStorage.removeItem("datosTransporteGuiaRemosion");
                                                            localStorage.removeItem("productosGuiaRemosion");
                                                            localStorage.removeItem("datosUbigeoGuiaRemosion");
                                                            $("#backbuttonvp").click();
                                                        }
                                                    )
                                                }
                                            } else {
                                                alertAdvertencia(resp.mensaje || "No se pudo Guardar la Venta")
                                            }
                                        }
                                    )
                                } else {
                                    alertAdvertencia('El monto debe ser mayor a 0')
                                }
                            } else {
                                this.enProceso = true
                                alertAdvertencia(mensaje)
                            }
                        } else {
                            this.enProceso = true
                            alertAdvertencia("No hay productos agregados a la lista ")
                        }
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
                    if (this.venta.tipo_doc == 6) {
                        this.apli_igv_is = false
                        this.venta.apli_igv = 1
                    } else {
                        this.apli_igv_is = true;
                    }
                },
                limpiasDatos() {
                    this.producto = {
                        edicion: false,
                        productoid: "",
                        descripcion: "",
                        nom_prod: "",
                        cantidad: "",
                        stock: "",
                        codigo: "",
                        costo: "",
                        codsunat: "",
                        precio: '',
                        almacen: '<?php echo $_SESSION["sucursal"] ?>',
                        precio2: '',
                        // precio3: '',
                        // precio4: '',
                        precio_unidad: '',
                        precioVenta: '',
                        precio_usado: 1
                    }
                },

                addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2,
                    //  precio3,
                    //   precio4,
                    precio_unidad) {
                    //if (this.producto.stock)
                    let cantidad = 1;

                    if (codigo_app.length > 0) {
                        const exisProduct = this.productos.findIndex(prod => prod.codigo === codigo_app);
                        if (exisProduct !== -1) {
                            this.productos[exisProduct].cantidad += cantidad;
                            this.productos[exisProduct].precio = parseFloat(precio).toFixed(2);
                        } else {
                            const prod = {
                                ...this.producto
                            }
                            prod.productoid = id;
                            prod.descripcion = codigo_app + "|" + nom_prod;
                            prod.nom_prod = nom_prod;
                            prod.cantidad = cantidad;
                            prod.codigo = codigo_app;
                            prod.costo = costo;
                            prod.codsunat = codsunat;
                            prod.precio = parseFloat(precio).toFixed(2);
                            prod.precio2 = parseFloat(precio2).toFixed(2);
                            // prod.precio3 = parseFloat(precio3).toFixed(2);
                            // prod.precio4 = parseFloat(precio4).toFixed(2);
                            prod.precio_unidad = parseFloat(precio_unidad).toFixed(2);
                            prod.precioVenta = parseFloat(precio).toFixed(2);
                            this.productos.push(prod);
                            //this.limpiasDatos();
                            console.log("QR", prod);
                        }
                    } else {
                        alert("No se pudo guardar los datos");
                    }
                },

                addProduct() {
                    //if (this.producto.stock)
                    if (this.producto.descripcion.length > 0) {
                        const prod = {
                            ...this.producto
                        }
                        this.productos.push(prod)
                        console.log("addproduct:", prod);
                        this.limpiasDatos();
                        this.usar_precio = 5
                    } else {
                        alertAdvertencia("Busque un producto primero")
                            .then(function () {
                                setTimeout(function () {
                                    $("#input_buscar_productos").focus();
                                }, 500)
                            })
                    }

                },
                editarProducto(index) {
                    // Copiar los datos del producto al formulario de edición
                    const producto = this.productos[index];
                    this.productoEdit = {
                        index: index,
                        guia_detalle_id: producto.guia_detalle_id, // Asegúrate de que este campo exista
                        descripcion: producto.descripcion,
                        cantidad: producto.cantidad,
                        precio: producto.precio,
                        stock: producto.stock,
                        productoid: producto.productoid
                    };
                    // Abrir el modal
                    new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
                },

                actualizarProducto() {
                    // Validar cantidad
                    if (!this.productoEdit.cantidad || this.productoEdit.cantidad <= 0) {
                        alertAdvertencia("Por favor, ingrese una cantidad válida");
                        return;
                    }

                    // Actualizar inmediatamente en el array local usando Vue.set
                    const index = this.productoEdit.index;
                    if (index > -1) {
                        // Crear el objeto actualizado
                        const productoActualizado = {
                            ...this.productos[index],
                            cantidad: parseFloat(this.productoEdit.cantidad),
                            descripcion: this.productoEdit.descripcion
                        };

                        // Usar Vue.set para asegurar reactividad inmediata
                        this.$set(this.productos, index, productoActualizado);

                        // Forzar actualización de la vista
                        this.$forceUpdate();

                        // Guardar en localStorage
                        localStorage.setItem('productosCotizacion', JSON.stringify(this.productos));

                        // Cerrar el modal y mostrar mensaje de éxito
                        bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                        alertExito("Producto actualizado correctamente");
                    } else {
                        alertAdvertencia("No se pudo encontrar el producto para actualizar");
                    }
                },
                // Modificar el método confirmarPagos
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
            created() {
    console.log("Component created");
    
    // Verificar si viene de cotización
    const cotiId = $("#cotizacion").val();
    if (cotiId) {
        console.log("Quote ID found:", cotiId);
        this.vieneDesCotizacion = true; // Mostrar el campo
        this.cargarCotizacion();
    }
    
    // Check for guide ID
    const guiaId = $("#guia").val();
    if (guiaId) {
        console.log("Guide ID found:", guiaId);
        this.cargarDatosGuia();
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
                vuelDelPago() {
                    if (this.venta.pagacon.length > 0) {
                        let pagacon = parseFloat(this.venta.pagacon)
                        if (this.venta.segundoPago) {
                            pagacon = pagacon + (isNaN(parseFloat(this.venta.pagacon2)) ? 0 : parseFloat(this.venta.pagacon2))
                        }
                        return pagacon - parseFloat(this.totalProdustos)
                    } else {
                        return ''
                    }
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
                    const vue = this
                    var total = 0;
                    this.productos.forEach(function (prod) {
                        if (vue.venta.moneda == 2) {
                            total += (prod.precioVenta / parseFloat(vue.venta.tc || '1')) * prod.cantidad
                        } else {
                            total += prod.precioVenta * prod.cantidad
                        }

                    })

                    this.venta.total = total;
                    return total.toFixed(2)
                }
            }
        });
        app.buscarSNdoc();

        _ajax("/ajs/consulta/metodo/pago", "POST", {

        },
            function (resp) {
                console.log(resp);
                app._data.metodosPago = resp
                /*     app.venta.serie = resp.serie
                    app.venta.numero = resp.numero */
            }
        )
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
                /*$('#input_datos_cliente').val(ui.item.datos);
                $('#input_documento_cliente').val(ui.item.documento);
                $('#input_datos_cliente').focus();*/

                // Cambiar tipo de documento basado en la longitud del documento
                if (ui.item.documento.length === 8) {
                    app._data.venta.tipo_doc = '1' // Boleta para DNI
                } else if (ui.item.documento.length === 11) {
                    app._data.venta.tipo_doc = '2' // Factura para RUC
                }

                // Actualizar serie y número
                app.onChangeTiDoc()
            }
        });

        // Agregar esta función fuera del objeto Vue
        function cargarPreciosAdicionales(idProducto, tipo = 'producto') {
            console.log("Cargando precios para ID:", idProducto, "Tipo:", tipo);

            // Determinar la URL según el tipo
            const url = tipo === 'repuesto'
                ? _URL + '/ajs/cargar/repuesto_precios/' + idProducto
                : _URL + '/ajs/cargar/producto_precios/' + idProducto;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    console.log("Precios adicionales recibidos:", data);

                    let array = [];

                    if (data && data.length > 0) {
                        data.forEach(item => {
                            array.push({
                                nombre: item.nombre,
                                precio: parseFloat(item.precio).toFixed(2)
                            });
                        });
                        app.precioProductos = array;
                    } else {
                        app.precioProductos = [
                            { nombre: 'No hay precios adicionales', precio: app.producto.precio }
                        ];
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error cargando precios:", error);
                    app.precioProductos = [
                        { nombre: 'Error al cargar precios', precio: app.producto.precio }
                    ];
                }
            });
        }
        $("#input_buscar_productos").autocomplete({

            source: _URL + `/ajs/cargar/productos/${app.producto.almacen}`,
            minLength: 1,
            select: function (event, ui) {
                event.preventDefault();
                /*    console.log(item);
                   console.log(ui); */
                console.log(ui.item);
                /*  return */

                app.producto.productoid = ui.item.codigo
                app.producto.descripcion = ui.item.codigo_pp + " | " + ui.item.nombre
                app.producto.nom_prod = ui.item.nombre || ui.item.descripcion
                app.producto.cantidad = '1'
                app.producto.stock = ui.item.cnt

                // Asignar y formatear todos los precios correctamente
                app.producto.precio = ui.item.precio == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio).toFixed(2)
                app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio2).toFixed(2)
                app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio_unidad).toFixed(2)
                app.producto.codigo = ui.item.codigo
                app.producto.costo = ui.item.costo == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.costo).toFixed(2)
                app.producto.precio_mayor = ui.item.precio_mayor == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio_mayor).toFixed(2)
                app.producto.precio_menor = ui.item.precio_menor == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio_menor).toFixed(2)

                // Establecer el precio de venta y precio mostrado
                app.producto.precioVenta = app.producto.precio
                app.producto.tipo_precio = 'PV'
                app.producto.precio_mostrado = app.producto.precio

                cargarPreciosAdicionales(ui.item.codigo, ui.item.tipo);
                let array = [{
                    precio: app.producto.precio
                },
                {
                    precio: app.producto.precio2
                },
                // {
                //     precio: app.producto.precio3
                // },
                // {
                //     precio: app.producto.precio4
                // },
                {
                    precio: app.producto.precio_unidad
                }
                ]

                app.precioProductos = array
                /*  app.precioProductos = array */
                console.log(array);
                $('#input_buscar_productos').val("");
                $("#example-text-input").focus()
            }
        });

        <?php
        if (isset($_GET["coti"])) {
            echo "app.cargarCotizacion();";
        }
        ?>
        $("#example-text-input").on('keypress', function (e) {
            if (e.which == 13) {
                $("#submit-a-product").click()
                $("#input_buscar_productos").focus()
            }
        });
        $("#container-vue").on("click", ".print-pfd-sld", function () {
            console.log("ssssssssssssssssssss")

            let printA4 = $(this).attr('href')
            if ($("#device-app").val() == 'desktop') {
                var iframe = document.createElement('iframe');
                iframe.style.display = "none";
                iframe.src = printA4;
                document.body.appendChild(iframe);
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                console.log(printA4);
            } else {
                window.open(printA4)
            }
        })

        $('#container-vue .modalImprimirComprobante').on('hidden.bs.modal', function (e) {
            location.reload();
        })

        $('#modalImprimirComprobante').on('hidden.bs.modal', function (e) {
            window.location.href = _URL + '/ventas';
        })
    })
</script>


<script>
    function toggleInput(checkbox) {
        // Busca el input relacionado a la clase 'precio-input' dentro del mismo contenedor
        const input = checkbox.parentElement.querySelector('.precio-input');
        input.disabled = !checkbox.checked; // Habilita el input si la casilla está marcada
    }

    function onlyNumber(event) {
        const keyCode = event.keyCode || event.which;
        // Permite solo números (códigos ASCII 48-57 para 0-9)
        if (keyCode < 48 || keyCode > 57) {
            event.preventDefault();
        }
    }
</script>