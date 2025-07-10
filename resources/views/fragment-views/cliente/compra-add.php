<!-- resources\views\fragment-views\cliente\compra-add.php -->
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>


<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<!-- Incluir estilos específicos del buscador -->


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Compra</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Compras</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438">Agregar Orden Compra</li>
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
        <div class="card">
            <div class="card-body">


                <div class="card-title-desc">

                </div>
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="col-lg-12">
                            <hr class="fg-black line-body" />
                        </div>

                        <div id="" class="col-xs-12 col-sm-12 col-md-12 no-padding">

                            <div class="col-xs-12 col-sm-12 col-md-12 no-padding">

                                <div class="row" id="container-vue">
                                    <div class="col-12 row">
                                        <div class="col-md-8">
                                            <div class="panel">
                                                <div class="panel-body">
                                                    <!-- <canvas id="qr-canvas" v-show="scanning" style="width: 300px;"></canvas>
                                                        <button id="btn-scan-qr" @click="encenderCamara" class="btn btn-primary" >Escanear QR</button> -->

                                                    <!-- Canvas para mostrar la vista de la cámara -->
                                                    <canvas hidden="" id="qr-canvas" v-show="toggleCamara"
                                                        style="width: 300px;"></canvas>
                                                    <div class="row">


                                                        <div class="col-md-12">


                                                            <form id="frmCompraProducto" class="form-horizontal">
                                                                <div class="form-group row mb-3">
                                                                    <label
                                                                        class="col-lg-2 control-label d-flex align-items-center">Buscar</label>
                                                                    <div class="col-lg-8">
                                                                        <div class="row">
                                                                            <!-- Contenedor para búsqueda y tipo -->
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-prepend"
                                                                                        style="width: 100px;">
                                                                                        <select class="form-select"
                                                                                            v-model="producto.tipo"
                                                                                            @change="actualizarAutocomplete"
                                                                                            style="border-radius: 0.25rem 0 0 0.25rem;">
                                                                                            <option value="producto">
                                                                                                Producto</option>
                                                                                            <option value="repuesto">
                                                                                                Repuesto</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="descripcionBuscar"
                                                                                        v-model="producto.productoBusca"
                                                                                        placeholder="Buscar...">
                                                                                </div>
                                                                            </div>

                                                                            <!-- Selector de almacén -->
                                                                            <div class="col-lg-4">
                                                                                <select class="form-select"
                                                                                    v-model="producto.almacen"
                                                                                    @change="actualizarAutocomplete">
                                                                                    <option value="1">Alm 1</option>
                                                                                    <option value="2">Alm 2</option>
                                                                                    <option value="3">Alm 3</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <button id="btn-scan-qr" @click="toggleCamara"
                                                                            class="btn bg-rojo text-white w-100">
                                                                            Escanear QR
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row mb-3">
                                                                    <label
                                                                        class="col-lg-2 control-label d-flex align-items-center h-100">Descripcion</label>
                                                                    <div class="col-lg-10">
                                                                        <input type="text" class="form-control"
                                                                            placeholder="Producto"
                                                                            id="descripcionBuscar"
                                                                            v-model="producto.descripcion" readonly="">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row mb-3">
                                                                    <label
                                                                        class="col-lg-2 control-label d-flex align-items-center h-100">Stock
                                                                        Actual</label>
                                                                    <div class="col-lg-2">
                                                                        <input disabled class="form-control text-center"
                                                                            type="text" placeholder="0" id="stockActual"
                                                                            name="stockActual" v-model="producto.stock">
                                                                    </div>
                                                                    <label
                                                                        class="col-lg-1 control-label d-flex align-items-center h-100">Cantidad</label>
                                                                    <div class="col-lg-2">
                                                                        <input
                                                                            class="form-control text-center only-number"
                                                                            type="text" placeholder="0" id="cantidad"
                                                                            name="cantidad" autocomplete="off"
                                                                            v-model="producto.cantidad">
                                                                    </div>
                                                                    <label
                                                                        class="col-lg-1 control-label d-flex align-items-center h-100">Precio</label>
                                                                    <div class="col-lg-2">
                                                                        <input @keypress="onlyNumber"
                                                                            class="form-control text-end" type="text"
                                                                            placeholder="0.00" id="example-text-input"
                                                                            v-model="producto.precio">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <button @click="addProduct" type="button"
                                                                            class="btn border-rojo text-rojo w-100">
                                                                            <i class="fa fa-check"></i> Agregar
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div id="modal_ver_detalle" class="modal fade"
                                                                    tabindex="-1" aria-labelledby="myModalLabel"
                                                                    style="display: none;" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <input type="hidden" name="idProducto"
                                                                                id="idProducto" value="">

                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="myModalLabel">Productos
                                                                                </h5>

                                                                            </div>
                                                                            <div class="modal-body" id="modal_detalle">
                                                                                <table class="table">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th
                                                                                                style="width: 10%;text-align: center;">
                                                                                                Item</th>
                                                                                            <th
                                                                                                style="width: 70%;text-align: center;">
                                                                                                Producto</th>
                                                                                            <th
                                                                                                style="width: 10%;text-align: center;">
                                                                                                Stock</th>
                                                                                            <th
                                                                                                style="width: 10%;text-align: center;">
                                                                                                Agregar</th>

                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr v-for="product in productoInfo "
                                                                                            :key="product.id_producto">
                                                                                            <td
                                                                                                style="text-align: center;">
                                                                                                {{product.id_producto}}
                                                                                            </td>
                                                                                            <td
                                                                                                style="text-align: center;">
                                                                                                {{product.codigo_pp}} |
                                                                                                {{product.nom_prod ||
                                                                                                product.descripcion}}
                                                                                            </td>
                                                                                            <td
                                                                                                style="text-align: center;">
                                                                                                {{product.cantidad}}
                                                                                            </td>
                                                                                            <td
                                                                                                style="text-align: center;">
                                                                                                <button
                                                                                                    @click="agregarLista(product.id_producto,product.nombre,product.cantidad, $event)"
                                                                                                    data-id="product.id_producto"
                                                                                                    class="btn btn-success"><i
                                                                                                        class="fa fa-plus"></i></button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>

                                                                                </table>
                                                                            </div>


                                                                            <div class="modal-footer">

                                                                                <button class="btn btn-info"
                                                                                    data-dismiss="modal"
                                                                                    aria-label="Close">Guardar</button>
                                                                            </div>
                                                                        </div>
                                                                        <!-- /.modal-content -->
                                                                    </div>
                                                                    <!-- /.modal-dialog -->
                                                                </div>


                                                            </form>
                                                        </div>

                                                        <div class="col-md-12 mt-5" style="margin-top: 25px;">
                                                            <div class="form-group ">
                                                                <div
                                                                    style="width: 100%; height: 20px; border-bottom: 2px solid #0866c6; text-align: left">
                                                                    <span
                                                                        style="font-size: 16px; font-weight: bold ; background-color: #ffffff; padding: 1px 4px;">
                                                                        Productos
                                                                        <!--Padding is optional-->
                                                                    </span>

                                                                </div>
                                                            </div>
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
                                                                        <td>{{item.codigo_app}} | {{item.nom_prod}}
                                                                        </td>
                                                                        <td>{{item.cantidad}}</td>
                                                                        <td>{{item.precio}}</td>
                                                                        <td>{{item.precio*item.cantidad}}</td>
                                                                        <td><button @click="eliminarItemPro(index)"
                                                                                type="button"
                                                                                class="btn btn-danger btn-xs">
                                                                                <i class="fa fa-times"></i>
                                                                            </button></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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
                                                                <form v-on:submit.prevent role="form"
                                                                    class="form-horizontal">
                                                                    <div class="form-group">
                                                                        <div class="row">
                                                                            <div class="col-md-6 text-center">
                                                                                <label
                                                                                    class="form-label w-100">Documento</label>
                                                                                <select @change="onChangeTiDoc($event)"
                                                                                    v-model="venta.tipo_doc"
                                                                                    class="form-control text-center">
                                                                                    <option value="2">FACTURA</option>
                                                                                    <option value="12">NOTA DE COMPRA
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6 text-center">
                                                                                <label class="form-label w-100">Tipo
                                                                                    Pago</label>
                                                                                <select v-model="venta.tipo_pago"
                                                                                    @change="changeTipoPago"
                                                                                    class="form-control text-center">
                                                                                    <option value="1">Contado</option>
                                                                                    <option value="2">Credito</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-2">
                                                                            <div class="col-md-6 text-center">
                                                                                <label
                                                                                    class="form-label w-100">Serie</label>
                                                                                <input v-model="venta.serie" type="text"
                                                                                    class="form-control text-center">
                                                                            </div>
                                                                            <div class="col-md-6 text-center">
                                                                                <label
                                                                                    class="form-label w-100">Numero</label>
                                                                                <input v-model="venta.numero"
                                                                                    type="text"
                                                                                    class="form-control text-center">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-2">
                                                                            <div class="col-md-6 text-center">
                                                                                <label
                                                                                    class="form-label w-100">Moneda</label>
                                                                                <select v-model="venta.moneda"
                                                                                    @change="chageMoneda"
                                                                                    class="form-control text-center">
                                                                                    <option value="1">PEN</option>
                                                                                    <option value="2">USD</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="col-lg-12 text-center">Fecha</label>
                                                                        <div class="col-lg-12">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group ">
                                                                                        <label
                                                                                            class="control-label">Emision</label>
                                                                                        <div class="col-lg-12">
                                                                                            <input v-model="venta.fecha"
                                                                                                type="date"
                                                                                                placeholder="dd/mm/aaaa"
                                                                                                name="input_fecha"
                                                                                                class="form-control text-center">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group ">
                                                                                        <label
                                                                                            class="control-label">Vencimiento</label>
                                                                                        <div class="col-lg-12">
                                                                                            <input
                                                                                                v-model="venta.fechaVen"
                                                                                                type="date"
                                                                                                placeholder="dd/mm/aaaa"
                                                                                                name="input_fecha"
                                                                                                class="form-control text-center">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div v-if="venta.tipo_pago=='2'"
                                                                        class="form-group ">
                                                                        <label class="control-label">Dias de
                                                                            pago</label>
                                                                        <div class="col-lg-12">
                                                                            <input @focus="focusDiasPagos"
                                                                                v-model="venta.dias_pago" type="text"
                                                                                class="form-control text-center">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-lg-4 control-label"
                                                                            style="text-align:center;">Proveedor</label>
                                                                    </div>

                                                                    <div class="form-group mb-3">
                                                                        <div class="col-lg-12">
                                                                            <div class="input-group">
                                                                                <input id="input_datos_cliente"
                                                                                    v-model="venta.num_doc" type="text"
                                                                                    placeholder="Ingrese Documento"
                                                                                    class="form-control" maxlength="11">
                                                                                <div class="input-group-addon">
                                                                                    <button @click="buscarDocumentSS"
                                                                                        class="btn bg-rojo text-white"
                                                                                        type="button"><i
                                                                                            class="fa fa-search"></i></button>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group  mb-3">
                                                                        <div class="col-lg-12">
                                                                            <input v-model="venta.nom_cli" type="text"
                                                                                placeholder="Nombre del cliente"
                                                                                class="form-control ui-autocomplete-input"
                                                                                autocomplete="off">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-3">
                                                                        <div class="col-lg-12">
                                                                            <div class="mb-3">
                                                                                <input v-model="venta.dir_cli"
                                                                                    type="text"
                                                                                    placeholder="Direccion 1"
                                                                                    class="form-control"
                                                                                    autocomplete="off">
                                                                            </div>
                                                                            <div>
                                                                                <button id="btn-observaciones"
                                                                                    class="btn border-rojo text-rojo w-100">Agregar
                                                                                    Observaciones</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group  mb-3">
                                                                        <div class="col-lg-12">
                                                                            <button @click="guardarCompra" type="button"
                                                                                class="btn btn-lg bg-rojo text-white"
                                                                                id="btn_finalizar_pedido">
                                                                                <i class="fa fa-save"></i> Guardar
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="bg-rojo text-white pv-15 text-center  p-3"
                                                                style="height: 90px; color: white">
                                                                <h1 class="mv-0 font-400" id="lbl_suma_pedido">S/
                                                                    {{totalProdustos}}</h1>
                                                                <div class="text-uppercase">Suma Pedido</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>



                                    <!-- Modal de Configuración de Pagos Modificado -->
                                    <div class="modal fade" id="modal-dias-pagos" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-rojo text-white">
                                                    <h3 class="modal-title" id="exampleModalLabel">Configuración de
                                                        Pagos</h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Información básica -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <div class="">
                                                                <label class="form-label">Fecha Emisión</label>
                                                                <input v-model="venta.fecha" type="date"
                                                                    class="form-control">
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
                                                            <input type="number" class="form-control"
                                                                v-model="venta.monto_inicial"
                                                                placeholder="Monto inicial"
                                                                @input="calcularCuotasRestantes">
                                                            <span class="input-group-text">o</span>
                                                            <input type="number" class="form-control"
                                                                v-model="venta.porcentaje_inicial" placeholder="%"
                                                                min="0" max="100" @input="calcularMontoInicial">
                                                        </div>
                                                    </div>

                                                    <!-- Número de cuotas -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Número de cuotas</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control"
                                                                v-model="numeroCuotas" min="1" max="36"
                                                                @change="generarCuotas">
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
                                                                            <td>S/
                                                                                {{formatoDecimal(venta.monto_inicial)}}
                                                                            </td>
                                                                        </tr>
                                                                        <!-- Mostrar cuotas con fechas seleccionables -->
                                                                        <tr v-for="(cuota, index) in cuotas"
                                                                            :key="index">
                                                                            <td>{{index + 1}}</td>
                                                                            <td>Cuota</td>
                                                                            <td>
                                                                                <input type="date"
                                                                                    class="form-control form-control-sm"
                                                                                    v-model="cuota.fecha"
                                                                                    @change="actualizarDiasPago">
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group input-group-sm">
                                                                                    <span
                                                                                        class="input-group-text">S/</span>
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
                                                    <button type="button" class="btn  bg-white text-rojo border-rojo"
                                                        data-bs-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn bg-rojo text-white"
                                                        @click="confirmarPagos">Confirmar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal para agregar observaciones -->
                                    <div class="modal fade" id="add-observaciones" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-rojo text-white">
                                                    <h5 class="modal-title">Agregar Observaciones</h5>
                                                    <button type="button" class="close btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="observaciones-container"></div>

                                                    <!-- Selector para guardar observaciones -->
                                                    <div class="mt-3 border-top pt-3">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="guardarObservaciones" id="guardarSoloCompra"
                                                                value="compra" checked>
                                                            <label class="form-check-label" for="guardarSoloCompra">
                                                                Guardar solo para esta compra
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="guardarObservaciones" id="guardarTodasCompras"
                                                                value="todas">
                                                            <label class="form-check-label" for="guardarTodasCompras">
                                                                Guardar para todas las compras
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn border-rojo text-rojo"
                                                        data-bs-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn bg-rojo text-white"
                                                        id="guardar-observaciones-add">Guardar cambios</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


    $(".only-number").keypress((evt) => {
        let keyCode = evt.keyCode ? evt.keyCode : evt.which;
        if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
            // 46 is dot
            evt.preventDefault();
        }
    });


    $(document).ready(function () {
        $('#btnCerrar').click(function () {
            $("#modal_ver_detalle").modal('toggle');
        })
        var app = new Vue({
            el: "#container-vue",
            data: {
                scanning: false,
                //camaraEncendida: false,
                producto: {
                    tipo: "producto",
                    almacen: "1",
                    productoid: "",
                    descripcion: "",
                    codigo_app: '',
                    nom_prod: "",
                    cantidad: "",
                    stock: "",
                    precio: "",
                    codigo: "",
                    costo: "",
                    codsunat: "",
                    productoBuscar: "",
                },
                productos: [],
                productoDesc: [],
                productoInfo: [],
                venta: {
                    dir_pos: 1,
                    tipo_doc: '2',
                    serie: '',
                    numero: '',
                    tipo_pago: '1',
                    dias_pago: '',
                    fecha: '',
                    fechaVen: '',
                    sendwp: false,
                    numwp: "",
                    num_doc: "",
                    nom_cli: "",
                    dir_cli: "",
                    dir2_cli: "",
                    tipoventa: 1,
                    total: 0,
                    moneda: "1",
                    dias_lista: [],

                },
                numeroCuotas: 1,
                cuotas: [],
            },
            created() {
                // Establecer la fecha actual y la fecha de vencimiento al cargar el componente
                this.setDefaultDates();
            },

            methods: {

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

                // Método para abrir el modal de configuración de pagos
                abrirConfiguracionPagos() {
                    // Si ya hay cuotas definidas, usarlas
                    if (this.venta.dias_lista && this.venta.dias_lista.length > 0) {
                        this.cuotas = this.venta.dias_lista.map(item => ({
                            fecha: item.fecha,
                            monto: item.monto
                        }));
                        this.numeroCuotas = this.cuotas.length;
                    } else {
                        // Si no hay cuotas, generar una por defecto
                        this.numeroCuotas = 1;
                        this.generarCuotas();
                    }

                    $("#modal-dias-pagos").modal("show");
                },

                // Sobrescribir el método focusDiasPagos para usar el nuevo sistema
                focusDiasPagos() {
                    this.abrirConfiguracionPagos();
                },
                calcularMontoInicial() {
                    if (this.venta.porcentaje_inicial) {
                        this.venta.monto_inicial = (this.venta.total * this.venta.porcentaje_inicial / 100).toFixed(2);
                        this.recalcularCuotas();
                    }
                },

                calcularCuotasRestantes() {
                    if (this.venta.tiene_inicial && this.venta.monto_inicial) {
                        const montoRestante = this.venta.total - this.venta.monto_inicial;
                        // Recalcular las cuotas con el monto restante
                        this.recalcularCuotas(montoRestante);
                    } else {
                        // Calcular cuotas con el monto total
                        this.recalcularCuotas(this.venta.total);
                    }
                },

                recalcularCuotas() {
                    if (this.venta.dias_pago) {
                        // Forzar la actualización de las cuotas
                        const diasPago = this.venta.dias_pago;
                        this.venta.dias_pago = '';
                        this.$nextTick(() => {
                            this.venta.dias_pago = diasPago;
                        });
                    }
                },
                focusDiasPagos() {
                    this.abrirConfiguracionPagos();
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
                },

                setDefaultDates() {
                    // Obtener la fecha actual
                    const today = new Date();

                    // Establecer la fecha de emisión como hoy
                    this.venta.fecha = this.formatDate(today);

                    // Establecer la fecha de vencimiento como mañana
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    this.venta.fechaVen = this.formatDate(tomorrow);
                },
                toggleCamara() {
                    if (!app.scanning) {
                        app.encenderCamara();
                    } else {
                        app.cerrarCamara();
                    }
                },
                encenderCamara() {
                    navigator.mediaDevices
                        .getUserMedia({ video: { facingMode: "environment" } })
                        .then(function (stream) {
                            app.scanning = true; // Actualiza el estado de escaneo
                            // Configuración de la cámara y la lógica de escaneo
                            document.getElementById("btn-scan-qr").textContent = "Apagar Cámara";
                            document.getElementById("btn-scan-qr").classList.remove("btn-primary");
                            document.getElementById("btn-scan-qr").classList.add("btn-danger");
                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr");

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
                                $("#descripcionBuscar").val(respuesta);
                                if (respuesta) {
                                    $.ajax({
                                        type: "post",
                                        url: _URL + '/ajs/compra/buscar/producto',
                                        data: {
                                            producto: respuesta // Código escaneado
                                        },
                                        success: function (response) {
                                            console.log(response);
                                            let data = JSON.parse(response);

                                            // // Manejar la respuesta del servidor
                                            if (data.res == true) {
                                                //alert("es verdadero el producto");
                                                let descripcion = data.data[0].descripcion;
                                                let precio = data.data[0].precio;
                                                let id = data.data[0].id_producto;
                                                let codigo = data.data[0].codigo;

                                                Swal.fire({
                                                    title: 'Se agrego correctamente',
                                                    text: respuesta,
                                                    icon: 'success',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.addProductQR(id, descripcion, precio, codigo);
                                                app.scanning = false;
                                                app.cerrarCamara();
                                            } else {
                                                // alert("el producto no existe");
                                                $("#descripcionBuscar").val('');
                                                // Producto no encontrado
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Advertencia',
                                                    text: 'No se encontró ningun producto',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.scanning = false;
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
                    app.scanning = false; // Actualiza el estado de escaneo
                    const video = document.querySelector("video");
                    const canvasElement = document.getElementById("qr-canvas");
                    const canvas = canvasElement.getContext("2d");
                    document.getElementById("btn-scan-qr").textContent = "Escanear QR";
                    document.getElementById("btn-scan-qr").classList.remove("btn-danger");
                    document.getElementById("btn-scan-qr").classList.add("btn-primary");
                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach((track) => {
                            track.stop();
                        });
                    }
                    canvasElement.hidden = true;
                },
                // Otros métodos que puedas necesitar

                agregarLista(id, producto, stock, $event) {

                    $event.preventDefault()
                    Swal.fire({
                        title: "¿Deseas agregar este producto?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#modal_ver_detalle").modal("hide")
                            this.producto.productoid = id;
                            this.producto.descripcion = producto; // Asegúrate que aquí se está pasando el nombre
                            this.producto.stock = stock;
                            this.limpiasDatos;
                            $("#descripcionBuscar").val('')
                            this.productoInfo = []
                            this.venta.dias_lista = []
                            this.venta.dias_pago = ''
                            /* $.ajax({
                                url: _URL + "/ajs/usuarios/logout",
                                type: "POST",
                            }).done(function() {
                                window.location.href = _URL + "/login";
                            }); */
                        } else { }
                    });
                },
                buscarProducto() {
                    /* this.limpiasDatos(); */
                    this.limpiasDatos;
                    /* $("#descripcionBuscar").val('') */
                    this.productoInfo = []
                    $('#stockActual').val('')
                    $('#producto').empty()
                    var self = this;
                    self.productos.stock = ''
                    var self = this
                    if ($("#descripcionBuscar").val().length > 2) {
                        $("#loader-menor").show()
                        $.ajax({
                            type: "post",
                            url: _URL + '/ajs/compra/buscar/producto',
                            data: {
                                producto: $("#descripcionBuscar").val()
                            },
                            success: function (resp) {
                                $('#stockActual').val('')
                                self.productos.stock = ''
                                $("#loader-menor").hide()
                                let data = JSON.parse(resp);
                                if (data.res) {
                                    $("#modal_ver_detalle").modal("show")
                                    data.data.map(function (item) {
                                        self.productoInfo.push(item)
                                        /*  console.log(item); */
                                    })

                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Advertencia',
                                        text: 'No se encontró ningun producto',
                                    })
                                }

                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'Digite al menos 3 caracter',
                        })
                    }
                },
                formatoDecimal(num, desc = 2) {
                    return parseFloat(num + "").toFixed(desc);
                },
                visualFechaSee(fecha) {
                    return formatFechaVisual(fecha);
                },
                formatDate(date) {
                    /*  console.log(date); */
                    var d = date,
                        month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
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
                chageMoneda(event) {
                    console.log(event.target.value)
                    this.venta.moneda = event.target.value;

                },
                onlyNumber($event) {
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                        $event.preventDefault();
                    }
                },
                eliminarItemPro(index) {
                    this.productos.splice(index, 1)
                },

                buscarDocumentSS() {
                    if (this.venta.num_doc.length == 11) {
                        $("#loader-menor").show()
                        this.venta.dir_pos = 1
                        _ajax("/ajs/consulta/doc/cliente", "POST", {
                            doc: this.venta.num_doc
                        },
                            function (resp) {
                                $("#loader-menor").hide()
                                /* console.log(resp); */
                                if (resp.res) {
                                    app._data.venta.nom_cli = (resp.data.nombre ? resp.data.nombre : '') + (resp.data.razon_social ? resp.data.razon_social : '')
                                    app._data.venta.dir_cli = resp.data.direccion
                                } else {
                                    alertAdvertencia("Documento no enocntrado")
                                }
                            }
                        )
                    } else {
                        alertAdvertencia("El RUC debe tener 11 digitos")
                    }
                },
                guardarCompra() {
                    if (this.productos.length > 0) {
                        let data = JSON.stringify(this.productos);
                        let datos =
                            console.log(data);

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
                        } else if (this.venta.tipo_doc == '2') {
                            mensaje = 'Solo se puede emitir Factura usando RUC';
                            if (this.venta.num_doc.length != 11) {
                                continuar = false;
                            }
                            if (this.venta.tipo_pago == 2) {
                                if (this.venta.dias_lista.length == 0) {
                                    continuar = false;
                                    mensaje = 'Debe especificar los días de pagos para un venta a crédito';
                                }
                            }


                        }

                        if (continuar) {
                            const data = {
                                ...this.venta,
                                listaPro: JSON.stringify(this.productos)
                            }
                            data.dias_lista = JSON.stringify(data.dias_lista)
                            console.log(data);
                            /*   $("#loader-menor").show(); */
                            /*  console.log(this.venta.dir_cli); */
                            if (this.venta.fecha !== undefined && this.venta.nom_cli !== '' && this.venta.serie !== '') {
                                if (this.venta.dir_cli !== undefined) {
                                    $("#loader-menor").hide();
                                    _ajax("/ajs/compras/add", "POST",
                                        data,
                                        function (resp) {
                                            /*  console.log(JSON.parse(resp)); */
                                            /*    console.log(resp); */
                                            if (resp.resp) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Bien',
                                                    text: 'Registro Exitoso',
                                                }).then(function () {
                                                    console.log(resp);
                                                    window.location = _URL + '/compras';
                                                });
                                            } else {
                                                alertAdvertencia('Alerta', 'No se pudo insertar el registro')
                                            }
                                        }
                                    )
                                } else {
                                    $("#loader-menor").hide();
                                    alertAdvertencia('Alerta', 'Llene el formulario Correctamente')
                                }
                            } else {
                                $("#loader-menor").hide();
                                alertAdvertencia('Alerta', 'Llene el formulario Correctamente')
                            }
                        } else {
                            alertAdvertencia(mensaje)
                        }
                    } else {
                        alertAdvertencia("No hay productos agregados a la lista ")
                    }

                },
                buscarSNdoc() {

                    /*     $.ajax({
                            type: 'post',
                            url: _URL + '/ajs/consulta/sn',
                            data: {
                                doc: this.venta.tipo_doc
                            },
                            success: function(resp) {
                                $("#loader-menor").hide()
                                if (isJson(resp)) {
                                    func(JSON.parse(resp));
                                } else {
                                    console.log(resp)
                                    alertError('ERR', 'Error en el servidor')
                                }

                            }
                        }); */
                    /*_ajax("/ajs/consulta/sn", "POST", {
                            doc: this.venta.tipo_doc
                        },
                        function(resp) {
                            app.venta.serie = resp.serie
                        }
                    )*/
                },
                onChangeTiDoc(event) {
                    this.buscarSNdoc();
                },
                limpiasDatos() {
                    const tipo = this.producto.tipo;  // Guardar el tipo actual
                    const almacen = this.producto.almacen;  // Guardar el almacén actual
                    this.producto = {
                        tipo: tipo,  // Mantener el tipo
                        almacen: almacen,  // Mantener el almacén
                        productoid: "",
                        descripcion: "",
                        cantidad: "",
                        precio: "",
                        codigo: "",
                        costo: "",
                        productoBusca: "",
                        stock: "",
                        productoInfo: []
                    }
                },
                addProductQR(id, descripcion, precio, codigo) {
                    //if (this.producto.stock)
                    let cantidad = 1;
                    if (descripcion.length > 0 && precio.length > 0) {
                        const prod = {
                            value: codigo + "|" + descripcion + "| P.Venta S/:" + precio + "|Stock:" + cantidad,
                            ...this.productos
                        }
                        prod.cantidad = cantidad;
                        prod.codigo = codigo;
                        prod.productoid = id;
                        prod.descripcion = descripcion;
                        prod.precio = precio.toString();
                        this.productos.push(prod);
                        //this.limpiasDatos();
                        console.log("QR", prod);
                    } else {
                        alert("No se pudo guardar los datos");
                    }
                },
                addProduct() {
                    if (this.producto.descripcion.length > 0 &&
                        this.producto.cantidad.length > 0 &&
                        this.producto.precio.length > 0) {

                        const prod = {
                            ...this.producto
                        }

                        // IMPORTANTE: Asegurar que el tipo se mantenga
                        prod.tipo = this.producto.tipo;
                        prod.id_item = this.producto.productoid; // Para referencia

                        this.productos.push(prod);
                        console.log("Producto agregado con tipo:", prod.tipo);
                        this.limpiasDatos();
                    } else {
                        alertAdvertencia("Llene todos los campos");
                    }
                }
                ,
                onChangeSelect(event) {

                    var self = this;
                    $.ajax({
                        type: "post",
                        url: _URL + '/buscar/buscarStock',
                        data: {
                            id: event.target.value
                        },
                        success: function (resp) {
                            $("#loader-menor").hide()
                            let data = JSON.parse(resp)
                            /*  console.log(data); */
                            /* const dataStock = this.producto = {
                                stock: element[4]
                            } */
                            if (data.res) {
                                /* console.log(data.data[0][2]); */
                                self.producto.stock = JSON.parse(data.data[0][0]);
                                self.producto.descripcion = (data.data[0][1]);
                                self.producto.productoid = JSON.parse(data.data[0][2]);

                                /*    console.log(data.data); */
                                /*  self.producto.descripcion = JSON.parse(data.data[0][0]); */
                                /*  this.producto = {
                                     stock: data.data[0][0]
                                 }
                                 console.log(this.producto); */
                            }
                        }
                    });
                },
                actualizarAutocomplete() {
                    console.log(`Actualizando para ${this.producto.tipo} en almacén: ${this.producto.almacen}`);

                    // Determinar URL según el tipo
                    let sourceUrl = '';
                    if (this.producto.tipo === 'producto') {
                        sourceUrl = _URL + `/ajs/cargar/productos/${this.producto.almacen}`;
                    } else if (this.producto.tipo === 'repuesto') {
                        sourceUrl = _URL + `/ajs/cargar/repuestos/${this.producto.almacen}`;
                    }

                    // Destruir y recrear autocomplete
                    $("#descripcionBuscar").autocomplete("destroy");
                    $("#descripcionBuscar").autocomplete({
                        source: sourceUrl,
                        minLength: 1,
                        select: function (event, ui) {
                            event.preventDefault();

                            app.producto.productoid = ui.item.id || ui.item.codigo;
                            app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre;
                            app.producto.nom_prod = ui.item.nombre;
                            app.producto.stock = ui.item.cantidad || ui.item.cnt;
                            // CORREGIDO: Usar COSTO en lugar de precio para compras
                            app.producto.precio = parseFloat(ui.item.costo || 0).toFixed(2);
                            app.producto.codigo = ui.item.codigo;
                            app.producto.costo = ui.item.costo;

                            console.log("Tipo seleccionado:", app.producto.tipo);
                        }
                    });
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

                totalProdustos() {
                    var total = 0;
                    this.productos.forEach(function (prod) {
                        total += prod.precio * prod.cantidad
                    })
                    this.venta.total = total;
                    return total.toFixed(2);
                }
            }
        })
     

        $("#descripcionBuscar").autocomplete({

            source: _URL + `/ajs/cargar/productos/${app.producto.almacen}`,
            minLength: 1,
            select: function (event, ui) {
                event.preventDefault();
                /*    console.log(item);
                   console.log(ui); */
                console.log(ui.item);
                /*  return */
                app.producto.productoid = ui.item.codigo
                app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre
                app.producto.nom_prod = ui.item.nombre
                app.producto.codigo_app = ui.item.codigo_pp
                app.producto.cantidad = ''
                app.producto.stock = ui.item.cnt
                // CORREGIDO: Usar COSTO en lugar de precio para compras
                app.producto.precio = ui.item.costo == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.costo + "").toFixed(2)
                app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2)
                app.producto.precio3 = ui.item.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio3 + "").toFixed(2)
                app.producto.precio4 = ui.item.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio4 + "").toFixed(2)
                app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_unidad + "").toFixed(2)
                app.producto.precioVenta = parseFloat(ui.item.precio_unidad + "").toFixed(2)
                app.producto.codigo = ui.item.codigo
                app.producto.costo = ui.item.costo
                let array = [{
                    precio: app.producto.precio
                },
                {
                    precio: app.producto.precio2
                },
                {
                    precio: app.producto.precio3
                },
                {
                    precio: app.producto.precio4
                },
                {
                    precio: app.producto.precio_unidad
                }
                ]

                app.precioProductos = array
                /*  app.precioProductos = array */
                console.log(array);
                $('#input_buscar_productos').val("");
            }
        });

        $("#input_datos_cliente").autocomplete({
            source: _URL + `/ajs/asearch/provedor/data`,
            minLength: 1,
            select: function (event, ui) {
                event.preventDefault();
                //console.log(ui)
                app._data.venta.num_doc = ui.item.documento
                app._data.venta.nom_cli = ui.item.datos
            }
        })

    })
</script>

<script src="<?= URL::to('public/js/cotizaciones/observaciones.js') ?>?v=<?= time() ?>"></script>