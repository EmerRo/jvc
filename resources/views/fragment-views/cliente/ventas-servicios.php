<!-- resources\views\fragment-views\cliente\ventas-servicios.php -->
<?php
$conexion = (new Conexion())->getConexion();

$datoEmpresa = $conexion->query("select * from empresas where id_empresa='{$_SESSION['id_empresa']}'")->fetch_assoc();

$igv_empresa = $datoEmpresa['igv'];
?>
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>
<div class="page-title-box">
    <div class="row align-items-center">
        <h6 class="page-title text-center">FACTURAR SERVICIOS</h6>
        <div class="col-md-8">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438">Servicios</li>
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

                    <h4 class="card-title">Venta de Servicios</h4>

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
                                                placeholder="Consultar Productos/Servicios"
                                                class="form-control ui-autocomplete-input" id="input_buscar_productos"
                                                autocomplete="off">
                                            <div class="input-group-btn p-1">
                                                <label class=""> <input id="btn-scan-qr" v-model="usar_scaner"
                                                        @click="toggleCamara" type="checkbox"> Usar
                                                    Scanner</label><br />
                                                <label @click="abrirMultipleBusaque"
                                                    style="color: blue;cursor: pointer">Búsqueda Múltiple</label>
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
                                                        style="background-color: #CA3438; color: white;">
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

                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Cod. SUNAT</label>
                                    <div class="col-lg-5">
                                        <input v-model="producto.codsunat" type="text" placeholder=""  class="form-control">
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="col-md-12 mt-5">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Producto/Servicio</th>
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
                                        <h5 class="modal-title">Editar Producto/Servicio</h5>
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">
                                                <input v-model="venta.dir2_cli" type="text" placeholder="Asunto"
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
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
                                    <div class="form-group  mb-3">

                                        <div class="col-lg-12">
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
                                        </div>
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
                    <h5 class="modal-title" id="exampleModalLabel">Búsqueda Múltiple</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="pointSel==1">
                        <div class="mb-3">
                            <label class="form-label">Buscar Producto/Servicio</label>
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
                                    <td>Producto/Servicio</td>
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
                    precio_unidad: '',
                    precioVenta: '',
                    precio_usado: 1,
                    tipo_precio: 'PV',
                    precio_mostrado: '',
                    precio_mayor: '',
                    precio_menor: '',
                },
                productoEdit: {
                    index: -1,
                    descripcion: '',
                    cantidad: '',
                    precio: '',
                    stock: '',
                    productoid: '',
                    precio_mostrado: '',
                    precioVenta: '',
                    costo: '',
                    precio_mayor: '',
                    precio_menor: '',
                    tipo_precio: 'PV'
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
                    tipoventa: 2,
                    total: 0,
                    dias_lista: [],
                    metodo: 12,
                    metodo2: 12,
                    moneda: 1,
                    tc: '',
                    tiene_inicial: false,
                    monto_inicial: 0,
                    porcentaje_inicial: 0,
                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                numeroCuotas: 1,
                cuotas: [],
                pointSel: 1
            },
            watch: {
                'venta.cantidadPagos'(newValue) {
                    const cantidadPagos = parseInt(newValue);
                    if (cantidadPagos > 0) {
                        const montoIndividual = this.venta.total > 0 ? (this.venta.total / cantidadPagos).toFixed(2) : '';

                        for (let i = 0; i < 5; i++) {
                            if (i < cantidadPagos) {
                                if (!this.venta.pagos[i].metodoPago) {
                                    this.venta.pagos[i].metodoPago = this.venta.metodo;
                                }
                                this.venta.pagos[i].montoPago = montoIndividual;
                            } else {
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
                        if (!this.venta.tc || this.venta.tc.trim() === '' || parseFloat(this.venta.tc) <= 0) {
                            this.venta.tc = '3.70';
                        }
                    }
                }

            },
            methods: {
                mostrarMensajeProducto() {
                    if (this.producto.descripcion.length === 0) {
                        this.mensajeProductoVisible = true;
                    }
                },
                generarCuotas() {
                    const numCuotas = parseInt(this.numeroCuotas) || 1;
                    this.cuotas = [];

                    const montoTotal = this.venta.tiene_inicial
                        ? (parseFloat(this.venta.total) - parseFloat(this.venta.monto_inicial || 0))
                        : parseFloat(this.venta.total);

                    const montoPorCuota = (montoTotal / numCuotas).toFixed(2);

                    let fechaBase;
                    try {
                        fechaBase = new Date(this.venta.fecha);
                        if (isNaN(fechaBase.getTime())) {
                            fechaBase = new Date();
                        }
                    } catch (e) {
                        fechaBase = new Date();
                    }

                    for (let i = 0; i < numCuotas; i++) {
                        try {
                            const fechaCuota = new Date(fechaBase.getTime());
                            fechaCuota.setMonth(fechaCuota.getMonth() + i + 1);

                            const monto = i === numCuotas - 1
                                ? (montoTotal - (montoPorCuota * (numCuotas - 1))).toFixed(2)
                                : montoPorCuota;

                            this.cuotas.push({
                                fecha: this.formatDate(fechaCuota),
                                monto: monto
                            });
                        } catch (e) {
                            console.error(`Error al generar cuota ${i + 1}:`, e);
                        }
                    }

                    this.actualizarDiasPago();
                },
                actualizarDiasPago() {
                    try {
                        this.venta.dias_lista = this.cuotas.map(cuota => ({
                            fecha: cuota.fecha,
                            monto: cuota.monto
                        }));

                        if (this.cuotas.length > 0) {
                            this.venta.fechaVen = this.cuotas[this.cuotas.length - 1].fecha;
                        }
                    } catch (e) {
                        console.error("Error en actualizarDiasPago:", e);
                    }
                },
                actualizarTotalCuotas() {
                    this.actualizarDiasPago();
                },
                calcularCuotasRestantes() {
                    this.generarCuotas();
                },
                calcularMontoInicial() {
                    if (this.venta.porcentaje_inicial && this.venta.total) {
                        this.venta.monto_inicial = (this.venta.total * this.venta.porcentaje_inicial / 100).toFixed(2);
                        this.generarCuotas();
                    }
                },

                seleccionarPrecioConTipo(tipo, precio) {
                    this.producto.precio_mostrado = precio;
                    this.producto.precio = precio;
                    this.producto.tipo_precio = tipo;

                    if (tipo === 'PV') {
                        this.producto.precioVenta = precio;
                    }

                    const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });

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
                            app.scanning = true;

                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr");
                            btnScanQR.checked = true;
                            video.setAttribute("playsinline", true);
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
                                        url: _URL + '/ajs/compra/buscar/producto',
                                        data: {
                                            producto: respuesta
                                        },
                                        success: function (response) {
                                            let data = JSON.parse(response);
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
                                                    title: 'Se agregó correctamente',
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
                                                    precio_unidad);
                                                $("#input_buscar_productos").val('');
                                                app.usar_scaner = false;
                                                app.cerrarCamara();
                                            } else {
                                                $("#input_buscar_productos").val('');
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Advertencia',
                                                    text: 'No se encontró ningún producto',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.usar_scaner = false;
                                                app.cerrarCamara();
                                            }
                                        },
                                        error: function () {
                                            alert('Error al buscar el producto.');
                                        }
                                    });
                                }

                            };
                        });
                },
                cerrarCamara() {
                    app.usar_scaner = false;
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
                        _get("/ajs/cargar/productos/<?php echo $_SESSION["sucursal"] ?>?term=" + this.dataKey, (result) => {
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
                                        app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(3) : parseFloat(ui.item.precio_unidad + "").toFixed(2)
                                        app.producto.precioVenta = parseFloat(ui.item.precio_unidad + "").toFixed(0)
                                        app.producto.codigo = ui.item.codigo
                                        app.producto.costo = ui.item.costo
                                        app.producto.precio_mayor = ui.item.precio_mayor == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_mayor + "").toFixed(2)
                                        app.producto.precio_menor = ui.item.precio_menor == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_menor + "").toFixed(2)
                                        app.producto.precio_mostrado = app.producto.precio
                                        app.producto.tipo_precio = 'PV'
                                        
                                        let array = [{
                                            precio: app.producto.precio
                                        },
                                        {
                                            precio: app.producto.precio2
                                        },
                                        {
                                            precio: app.producto.precio_unidad
                                        }
                                        ]

                                        app.precioProductos = array
                                        $("#input_buscar_productos").val('')
                                        $("#example-text-input").focus();
                                    } else {
                                        $("#input_buscar_productos").val('')
                                    }
                                }
                            )
                        }
                    }
                },
                onChangeAlmacen(event) {
                    this.producto.almacen = event.target.value
                    var self = this
                    $("#input_buscar_productos").autocomplete({

                        source: _URL + `/ajs/cargar/productos/${self.producto.almacen}`,
                        minLength: 1,
                        select: function (event, ui) {
                            event.preventDefault();
                            
                            app.producto.productoid = ui.item.codigo
                            app.producto.descripcion = ui.item.codigo_pp + " | " + ui.item.nombre
                            app.producto.nom_prod = ui.item.descripcion
                            app.producto.cantidad = ''
                            app.producto.stock = ui.item.cnt
                            app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio
                            app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2)
                            app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad
                            app.producto.codigo = ui.item.codigo
                            app.producto.costo = ui.item.costo
                            app.producto.precioVenta = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad
                            app.producto.precio_mayor = ui.item.precio_mayor == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_mayor
                            app.producto.precio_menor = ui.item.precio_menor == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_menor
                            app.producto.precio_mostrado = app.producto.precio
                            app.producto.tipo_precio = 'PV'
                            
                            let array = [{
                                precio: app.producto.precio
                            },
                            {
                                precio: app.producto.precio2
                            },
                            {
                                precio: app.producto.precio_unidad
                            }
                            ]

                            app.precioProductos = array
                            $('#input_buscar_productos').val("");
                            $("#example-text-input").focus()
                        }
                    });
                },
                onlyNumber($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                        $event.preventDefault();
                    }
                    if (keyCode === 46 && $event.target.value.includes('.')) {
                        $event.preventDefault();
                    }
                },
                onlyNumberDecimal($event) {
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
                        $("#loader-menor").show()
                        this.venta.dir_pos = 1

                        if (this.venta.num_doc.length == 11) {
                            this.venta.tipo_doc = '2';
                        } else if (this.venta.num_doc.length == 8) {
                            this.venta.tipo_doc = '1';
                        }

                        this.buscarSNdoc();

                        _ajax("/ajs/consulta/doc/cliente", "POST", {
                            doc: this.venta.num_doc
                        },
                            function (resp) {
                                $("#loader-menor").hide()
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
                        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos")
                    }
                },

                guardarVenta() {
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
                                        mensaje = 'Debe especificar los días de pagos para una venta a crédito';
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
                                        mensaje = 'Debe especificar los días de pagos para una venta a crédito';
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
                                            
                                            let desde = localStorage.getItem('desde')
                                            if (resp.res) {
                                                alertExito("Éxito", "Venta Guardada").then(function () {
                                                    $("#ce-t-a4").attr("href", _URL + "/venta/comprobante/pdf/" + resp.venta + "/" + resp.nomxml);
                                                    $("#ce-t-a4-m").attr("href", _URL + "/venta/comprobante/pdf/ma4/" + resp.venta + "/" + resp.nomxml);
                                                    $("#ce-t-8cm").attr("href", _URL + "/venta/pdf/voucher/8cm/" + resp.venta + "/" + resp.nomxml);
                                                    $("#ce-t-5_6cm").attr("href", _URL + "/venta/pdf/voucher/5.6cm/" + resp.venta + "/" + resp.nomxml);
                                                    $("#modalImprimirComprobante").modal("show");
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
                        precio_unidad: '',
                        precioVenta: '',
                        precio_usado: 1,
                        tipo_precio: 'PV',
                        precio_mostrado: '',
                        precio_mayor: '',
                        precio_menor: '',
                    }
                },

                addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2, precio_unidad) {
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
                            prod.precio_unidad = parseFloat(precio_unidad).toFixed(2);
                            prod.precioVenta = parseFloat(precio).toFixed(2);
                            this.productos.push(prod);
                        }
                    } else {
                        alert("No se pudo guardar los datos");
                    }
                },

                addProduct() {
                    if (this.producto.descripcion.length > 0) {
                        const prod = {
                            ...this.producto
                        }
                        this.productos.push(prod)
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
                    const producto = this.productos[index];
                    this.productoEdit = {
                        index: index,
                        guia_detalle_id: producto.guia_detalle_id,
                        descripcion: producto.descripcion,
                        cantidad: producto.cantidad,
                        precio: producto.precio,
                        stock: producto.stock,
                        productoid: producto.productoid,
                        precio_mostrado: producto.precio_mostrado || producto.precio,
                        precioVenta: producto.precioVenta,
                        costo: producto.costo,
                        precio_mayor: producto.precio_mayor,
                        precio_menor: producto.precio_menor,
                        tipo_precio: producto.tipo_precio || 'PV'
                    };
                    new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
                },

                actualizarProducto() {
                    if (!this.productoEdit.cantidad || this.productoEdit.cantidad <= 0) {
                        alertAdvertencia("Por favor, ingrese una cantidad válida");
                        return;
                    }

                    const index = this.productoEdit.index;
                    if (index > -1) {
                        const productoActualizado = {
                            ...this.productos[index],
                            cantidad: parseFloat(this.productoEdit.cantidad),
                            descripcion: this.productoEdit.descripcion
                        };

                        this.$set(this.productos, index, productoActualizado);
                        this.$forceUpdate();

                        localStorage.setItem('productosCotizacion', JSON.stringify(this.productos));

                        bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                        alertExito("Producto actualizado correctamente");
                    } else {
                        alertAdvertencia("No se pudo encontrar el producto para actualizar");
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
                formatoDecimal(num, desc = 2) {
                    return parseFloat(num + "").toFixed(desc);
                },
                visualFechaSee(fecha) {
                    return formatFechaVisual(fecha);
                },
                formatDate(date) {
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
                focusDiasPagos() {
                    $("#modal-dias-pagos").modal("show")
                },
                changeTipoPago(event) {
                    this.venta.fechaVen = this.venta.fecha;
                    this.venta.dias_lista = []
                    this.venta.dias_pago = ''
                },

                cargarCotizacion() {
                    const vue = this;
                    _post("/ajs/cotizaciones/info", {
                        coti: $("#cotizacion").val()
                    },
                        function (resp) {
                            vue.productos = resp.productos.map(ert => {
                                ert.descripcion = ert.codigo.toString().trim() + ' | ' + ert.descripcion
                                ert.edicion = false
                                return ert
                            })

                            if (resp.cliente_doc) {
                                if (resp.cliente_doc.length === 11) {
                                    vue.venta.tipo_doc = '2';
                                } else if (resp.cliente_doc.length === 8) {
                                    vue.venta.tipo_doc = '1';
                                } else {
                                    vue.venta.tipo_doc = resp.id_tido;
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

                            vue.buscarSNdoc();

                            setTimeout(function () {
                                vue.venta.dias_lista = resp.cuotas
                            }, 1000)
                        })
                },

                cargarDatosGuia() {
                    const vue = this;
                    const guiaId = $("#guia").val();

                    if (!guiaId) {
                        return;
                    }

                    _post("/ajs/guia/remision/info", {
                        guia: guiaId
                    }, function (resp) {
                        if (resp.res) {
                            if (Array.isArray(resp.productos) && resp.productos.length > 0) {
                                vue.productos = resp.productos;
                            } else {
                                vue.productos = [];
                            }

                            if (resp.cliente_doc) {
                                if (resp.cliente_doc.length === 11) {
                                    vue.venta.tipo_doc = '2';
                                } else if (resp.cliente_doc.length === 8) {
                                    vue.venta.tipo_doc = '1';
                                } else {
                                    vue.venta.tipo_doc = '6';
                                }
                            }

                            vue.venta.num_doc = resp.cliente_doc || '';
                            vue.venta.nom_cli = resp.cliente_nombre || '';
                            vue.venta.dir_cli = resp.cliente_direccion || '';
                            vue.venta.tipo_pago = '1';
                            vue.venta.fecha = resp.guia.fecha_emision || $("#fecha-app").val();

                            vue.buscarSNdoc();
                        } else {
                            alertAdvertencia("Error al cargar datos de la guía: " + (resp.error || 'Error desconocido'));
                        }
                    });
                }

            },
            created() {
                const guiaId = $("#guia").val();
                if (guiaId) {
                    this.cargarDatosGuia();
                }

                const cotiId = $("#cotizacion").val();
                if (cotiId) {
                    this.cargarCotizacion();
                }
            },

            computed: {
                totalValorCuotas() {
                    let total = 0;

                    if (this.venta.tiene_inicial) {
                        total += parseFloat(this.venta.monto_inicial || 0);
                    }

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
                app._data.metodosPago = resp
            }
        )
        $("#input_datos_cliente").autocomplete({
            source: _URL + "/ajs/buscar/cliente/datos",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                app._data.venta.dir_pos = 1
                app._data.venta.nom_cli = ui.item.datos
                app._data.venta.num_doc = ui.item.documento
                app._data.venta.dir_cli = ui.item.direccion

                if (ui.item.documento.length === 8) {
                    app._data.venta.tipo_doc = '1'
                } else if (ui.item.documento.length === 11) {
                    app._data.venta.tipo_doc = '2'
                }

                app.onChangeTiDoc()
            }
        });

        function cargarPreciosAdicionales(idProducto, tipo = 'producto') {
            const url = tipo === 'repuesto'
                ? _URL + '/ajs/cargar/repuesto_precios/' + idProducto
                : _URL + '/ajs/cargar/producto_precios/' + idProducto;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
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

                app.producto.productoid = ui.item.codigo
                app.producto.descripcion = ui.item.codigo_pp + " | " + ui.item.nombre
                app.producto.nom_prod = ui.item.nombre || ui.item.descripcion
                app.producto.cantidad = '1'
                app.producto.stock = ui.item.cnt

                app.producto.precio = ui.item.precio == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio).toFixed(2)
                app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio2).toFixed(2)
                app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio_unidad).toFixed(2)
                app.producto.codigo = ui.item.codigo
                app.producto.costo = ui.item.costo == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.costo).toFixed(2)
                app.producto.precio_mayor = ui.item.precio_mayor == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio_mayor).toFixed(2)
                app.producto.precio_menor = ui.item.precio_menor == null ? parseFloat(0).toFixed(2) : parseFloat(ui.item.precio_menor).toFixed(2)

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
                {
                    precio: app.producto.precio_unidad
                }
                ]

                app.precioProductos = array
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
            let printA4 = $(this).attr('href')
            if ($("#device-app").val() == 'desktop') {
                var iframe = document.createElement('iframe');
                iframe.style.display = "none";
                iframe.src = printA4;
                document.body.appendChild(iframe);
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } else {
                window.open(printA4)
            }
        })

        $('#container-vue .modalImprimirComprobante').on('hidden.bs.modal', function (e) {
            location.reload();
        })

        $('#modalImprimirComprobante').on('hidden.bs.modal', function (e) {
            location.reload();
        })
    })
</script>