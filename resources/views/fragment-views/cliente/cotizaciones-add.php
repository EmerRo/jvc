<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<style>
    .ql-editor {
        min-height: 200px;
        font-size: 14px;
        line-height: 1.8;
    }

    .ql-editor p {
        margin-bottom: 8px;
    }

    .ql-toolbar.ql-snow {
        border-radius: 4px 4px 0 0;
    }

    .ql-container.ql-snow {
        border-radius: 0 0 4px 4px;
    }

    /* Contenedor del editor */
    #editor-container {
        background: white;
        margin-bottom: 20px;
    }

    /* Editor */
    #editor-content {
        height: 300px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    /* Barra de herramientas */
    .ql-toolbar.ql-snow {
        border: 1px solid #ced4da;
        border-radius: 0.25rem 0.25rem 0 0;
    }

    /* Área de edición */
    .ql-container.ql-snow {
        border: 1px solid #ced4da;
        border-top: 0;
        border-radius: 0 0 0.25rem 0.25rem;
        font-size: 16px;
    }

    /* Estilos para las viñetas */
    .ql-editor ul {
        padding-left: 20px;
    }

    /* Mejorar la visibilidad del texto seleccionado */
    .ql-editor ::selection {
        background-color: #b4d5fe;
    }
</style>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <!-- <h6 class="page-title text-center"> Nueva Cotización</h6> -->
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Cotización</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438;">Agregar Nueva Cotizacion
                </li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">

            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?= date("Y-m-d") ?>" id="fecha-app">
<input type="hidden" id="cotizacion-id" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title"></h4>

                <div class="card-title-desc">
                    <div class="col-lg-12 text-end">
                        <button type="button" onclick="$('#btn_finalizar_pedido').click()"
                            class="btn bg-rojo text-white">
                            <i class="fa fa-plus "></i> Guardar Cotización
                        </button>

                        <a id="backbuttonvp" style="margin-left:25px;" href="/cotizaciones"
                            class="btn border-rojo text-rojo button-link"><i class="fa fa-arrow-left"></i> Regresar</a>
                    </div>
                </div>
                <div class="row" id="container-vue">
                    <div class="col-12 row">
                        <div class="col-md-8">
                            <div class="panel">
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-md-12">

                                            <form v-on:submit.prevent="addProduct" class="form-horizontal">
                                                <div hidden class="form-group row mb-3">
                                                    <label class="col-lg-2 control-label">Almacén</label>
                                                    <div class="col-lg-3">
                                                        <select class="form-control idAlmacen"
                                                            v-model='producto.almacen'
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
                                                    <!-- Primera fila con Stock Actual, Cantidad y Precio -->
                                                    <div class="col-lg-10">
                                                        <div class="row">
                                                            <!-- Campo Stock Actual -->
                                                            <div class="col-lg-3">
                                                                <label for="stock-actual" class="col-form-label">Stock
                                                                    Actual</label>
                                                                <input id="stock-actual" disabled
                                                                    v-model="producto.stock"
                                                                    class="form-control text-center" type="text"
                                                                    placeholder="0" style="height: 38px;">
                                                            </div>

                                                            <!-- Campo Cantidad -->
                                                            <div class="col-lg-3">
                                                                <label for="cantidad-input"
                                                                    class="col-form-label">Cantidad</label>
                                                                <input id="cantidad-input" @keypress="onlyNumber"
                                                                    required v-model="producto.cantidad"
                                                                    class="form-control text-center" type="text"
                                                                    placeholder="0" style="height: 38px;">
                                                            </div>

                                                            <!-- Campo Precio -->
                                                            <div class="col-lg-3">
                                                                <label for="precio-input"
                                                                    class="col-form-label">Precio</label>
                                                              <div class="input-group" style="height: 38px;">
                                                                    <input id="precio-input" type="text"
                                                                        class="form-control"
                                                                        :class="{'dropdown-toggle': producto.descripcion.length > 0}"
                                                                        :data-bs-toggle="producto.descripcion.length > 0 ? 'dropdown' : ''"
                                                                        aria-expanded="false"
                                                                        v-model="producto.precio_mostrado"
                                                                        style="height: 38px; background-color: #f8f9fa; cursor: pointer;"
                                                                        @click="mostrarMensajeProducto" readonly>
                                                                    <p v-if="mensajeProductoVisible && producto.descripcion.length === 0"
                                                                        class="text-danger small position-absolute"
                                                                        style="top: 100%; left: 0; margin-top: 2px;">
                                                                        Elija un producto primero
                                                                    </p>
                                                                    <ul class="dropdown-menu"
                                                                        style="width: 300px; max-width: none;">
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('PV', producto.precioVenta)"
                                                                                style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                                                <span
                                                                                    style="width: 150px; display: inline-block;">Precio
                                                                                    Venta:</span>
                                                                                <span
                                                                                    style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                                    {{ producto.precioVenta }}</span>
                                                                            </a>
                                                                        </li>
                                                                        <!-- <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('C', producto.costo)"
                                                                                style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                                                <span
                                                                                    style="width: 150px; display: inline-block;">Costo:</span>
                                                                                <span
                                                                                    style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                                    {{ producto.costo }}</span>
                                                                            </a>
                                                                        </li> -->
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('PM', producto.precio_mayor)"
                                                                                style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                                                <span
                                                                                    style="width: 150px; display: inline-block;">Precio
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
                                                                                <span
                                                                                    style="width: 150px; display: inline-block;">Precio
                                                                                    Menorista:</span>
                                                                                <span
                                                                                    style="background-color: #4CAF50; color: white; padding: 2px 8px; border-radius: 15px; font-size: 13px; margin-left: auto;">S/
                                                                                    {{ producto.precio_menor }}</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="input-group-append">
                                                                        <button
                                                                            class="btn btn-outline-secondary dropdown-toggle"
                                                                            type="button"
                                                                            :data-bs-toggle="producto.descripcion.length > 0 ? 'dropdown' : ''"
                                                                            aria-expanded="false"
                                                                            @click="mostrarMensajeProducto"
                                                                            style="background-color: #CA3438; color: white; height: 38px;">
                                                                            <i class="fa fa-chevron-down"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                                            style="width: 300px; max-width: none;">
                                                                            <li v-for="(value, key) in precioProductos"
                                                                                :key="key">
                                                                                <a class="dropdown-item" href="#"
                                                                                    @click.prevent="seleccionarPrecioConTipo(value.nombre, value.precio)"
                                                                                    style="color: #333; padding: 8px 15px; display: flex; align-items: center;">
                                                                                    <span
                                                                                        style="width: 150px; display: inline-block;">{{
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
                                                        </div>

                                                        <!-- Segunda fila con Precio Especial (P.Esp.) y Botón Agregar -->
                                                        <div class="row mt-3">
                                                            <!-- Campo Precio Especial -->
                                                            <div class="col-lg-4 d-flex align-items-center">
                                                                <label for="precio-especial"
                                                                    class="col-form-label mb-0 me-2">Precio
                                                                    Especial</label>
                                                                <input type="checkbox" class="toggle-checkbox me-2"
                                                                    onclick="toggleInput(this)">
                                                                <input id="precio-especial"
                                                                    class="form-control text-center precio-input"
                                                                    @keypress="onlyNumber"
                                                                    v-model="producto.precioEspecial" type="text"
                                                                    placeholder="0" disabled
                                                                    style="width: 80px; height: 38px;">
                                                            </div>
                                                            <div class="col-lg-4 d-flex align-items-center">
                                                                <label for="descuento"
                                                                    class="col-form-label mb-0 me-2">Descuento %</label>
                                                                <input type="checkbox" class="toggle-checkbox me-2"
                                                                    onclick="toggleInput(this)">
                                                                <input id="descuento"
                                                                    class="form-control text-center precio-input"
                                                                    @keypress="onlyNumber" v-model="descuentoGeneral"
                                                                    type="text" placeholder="0" disabled
                                                                    style="width: 80px; height: 38px;">
                                                            </div>

                                                            <!-- Botón Agregar -->
                                                            <div class="col-lg-4 d-flex align-items-center">
                                                                <button id="submit-a-product" type="submit"
                                                                    class="btn bg-rojo text-white w-100"
                                                                    style="height: 38px;">
                                                                    <i class="fa fa-check me-2"></i> Agregar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                            </form>
                                        </div>

                                        <div class="col-md-12 mt-5" style="overflow-x: auto;">
                                            <div class="row">
                                                <div class="text-left col-md-9">
                                                    <h4>Producto</h4>
                                                </div>

                                            </div>
                                            <table class="table" style="width: 100%; ">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Codigo</th>
                                                        <th>Producto</th>
                                                        <th>Cantidad</th>
                                                        <th>P. Unit.</th>
                                                        <th>Parcial</th>
                                                        <th>P.Esp.</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in productos">
                                                        <td>{{index+1}}</td>
                                                        <td>{{item.codigo_pp || item.codigo || ''}}</td>
                                                        <td>{{item.descripcion}}</td>
                                                        <td><span v-if="!item.editable">{{item.cantidad}}</span><input
                                                                v-if="item.editable" v-model="item.cantidad"></td>
                                                        <td><span
                                                                v-if="!item.editable">{{item.precioVenta}}</span><input
                                                                v-if="item.editable" v-model="item.precioVenta"></td>
                                                        <td>{{(item.precioVenta*item.cantidad).toFixed(2)}}</td>
                                                        <td>{{item.precioEspecial}}</td>
                                                        <td>
                                                            <div class="d-flex gap-2 justify-content-center">
                                                                <!-- Botón Editar -->
                                                                <button @click="editarProducto(index)"
                                                                    class="btn btn-warning btn-sm" title="Editar">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                                <!-- Botón Eliminar (el que ya tenía) -->
                                                                <button @click="eliminarItemPro(index)"
                                                                    class="btn btn-danger btn-sm" title="Eliminar">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </td>
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
                                                <form v-on:submit.prevent role="form" class="form-horizontal">
                                                    <div class="row">
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
                                                        <div class="col-md-6 form-group">
                                                            <label class="control-label">Tipo Pago</label>
                                                            <select v-model="venta.tipo_pago" @change="changeTipoPago"
                                                                class="form-control">
                                                                <option value="1">Contado</option>
                                                                <option value="2">Crédito</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div style="display: none" class="form-group">
                                                        <div class="col-lg-12 row">
                                                            <div class="col-lg-6">
                                                                <label class="text-center col-md-12">Serie</label>
                                                                <input v-model="venta.serie" type="text"
                                                                    class="form-control text-center" readonly="">
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="text-center col-md-12">Número</label>
                                                                <input v-model="venta.numero" type="text"
                                                                    class="form-control text-center" readonly="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div v-if="venta.tipo_pago=='2'" class="form-group ">
                                                        <label class="control-label">Días de pago</label>
                                                        <div class="col-lg-12">
                                                            <input @focus="focusDiasPagos" v-model="venta.dias_pago"
                                                                type="text" class="form-control text-center">
                                                        </div>
                                                    </div>

                                                    <div class="form-group  mb-3">
                                                        <label style="display: none"
                                                            class="col-lg-12 text-center">Fecha</label>
                                                        <div class="col-lg-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group ">
                                                                        <label class="control-label">Fecha</label>
                                                                        <div class="col-lg-12">
                                                                            <input v-model="venta.fecha" type="date"
                                                                                placeholder="dd/mm/aaaa"
                                                                                name="input_fecha"
                                                                                class="form-control text-center"
                                                                                value="2021-10-16">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div style="display: none" class="col-md-6">
                                                                    <div class="form-group ">
                                                                        <label class="control-label">Vencimiento</label>
                                                                        <div class="col-lg-12">
                                                                            <input disabled v-model="venta.fechaVen"
                                                                                type="date" placeholder="dd/mm/aaaa"
                                                                                name="input_fecha"
                                                                                class="form-control text-center"
                                                                                value="2021-10-16">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div v-if="venta.tipo_pago=='2'" class="form-group ">
                                                        <label class="control-label">Días de pago</label>
                                                        <div class="col-lg-12">
                                                            <input @focus="focusDiasPagos" v-model="venta.dias_pago"
                                                                type="text" class="form-control text-center">
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
                                                                        <label class="control-label">Tasa de
                                                                            cambio</label>
                                                                        <div class="col-lg-12">
                                                                            <input v-model="venta.tc" type="text"
                                                                                style="outline: none;  border: 1px solid #a49c9c; padding: 5px; width: 100px; border-radius: 5px; ">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="col-lg-12 text-center">Cliente</label>
                                                    </div>

                                                    <div class="form-group mb-3">
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
                                                    <div class="form-group  mb-3">
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.nom_cli" type="text"
                                                                placeholder="Nombre del cliente"
                                                                class="form-control ui-autocomplete-input"
                                                                autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group  mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input v-model="venta.dir_cli" type="text"
                                                                    placeholder="Direccion "
                                                                    class="form-control ui-autocomplete-input"
                                                                    autocomplete="off">
                                                                <!-- <div class="input-group-addon"><input
                                                                        v-model="venta.dir_pos" name="dirserl" value="1"
                                                                        type="radio" class="form-check-input"></div> -->
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <!-- Reemplazar el div de Atención por un componente de Asunto simple -->
                                                    <div class="form-group mb-3">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input v-model="venta.asunto" type="text"
                                                                    placeholder="Atención"
                                                                    class="form-control ui-autocomplete-input"
                                                                    autocomplete="off" list="asuntos-list">
                                                                <datalist id="asuntos-list">
                                                                    <option v-for="asunto in asuntos"
                                                                        :value="asunto.nombre"></option>
                                                                </datalist>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group  mb-3">
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

                                            <button class="btn border-rojo text-rojo" id="add-condiciones"
                                                style="margin-bottom: 5px;">Modificar Terminos</button>

                                            <!-- <button class="btn btn-success" id="edit-diagnostico"
                                                style="margin-bottom: 5px;">Diagnostico</button> -->

                                            <!-- <button class="btn btn-success" id="edit-diagnostico"
                                                style="margin-bottom: 5px;">Foto</button> -->

                                            <br>
                                            <div class="bg-rojo pv-15 text-center p-3"
                                                style="height: 90px; color: white">
                                                <h1 class="mv-0 font-400" id="lbl_suma_pedido">
                                                    {{monedaSibol}} {{(totalProdustos/(venta.tc||1)).toFixed(2)}}
                                                </h1>
                                                <div class="text-uppercase">Suma Pedido</div>
                                            </div>


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
                                            <i class="fas fa-check-circle me-2"></i>¡Cotización Registrada!
                                        </h4>
                                        <p class="text-muted mb-0">La Cotización N° <span id="cotizacion-numero"></span>
                                            ha sido registrada correctamente.</p>
                                    </div>
                                </div>
                                <div class="modal-body p-4">
                                    <!-- Botones de acción -->
                                    <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                                        <button id="btn-a4"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fas fa-file-pdf"></i> A4
                                        </button>
                                        <button id="btn-media-a4"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fas fa-file-pdf"></i> Media A4
                                        </button>
                                        <button id="btn-voucher-8cm"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fas fa-file-pdf"></i> Voucher 8cm
                                        </button>
                                        <button id="btn-voucher-5-6cm"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fas fa-file-pdf"></i> Voucher 5.6cm
                                        </button>
                                        <button id="btn-whatsapp"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fab fa-whatsapp"></i> WHATSAPP
                                        </button>
                                        <a href="#" id="btn-guia"
                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                            <i class="fas fa-clipboard-list"></i> CREAR GUÍA REMISIÓN
                                        </a>
                                    </div>

                                    <!-- Vista previa del PDF -->
                                    <div class="border rounded">
                                        <iframe id="pdf-preview" src=""
                                            style="width: 100%; height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between border-top pt-3">
                                    <a href="/cotizaciones" class="btn btn-success">
                                        <i class="fas fa-list me-2"></i> LISTA DE COTIZACIONES
                                    </a>
                                    <button onclick="location.reload()" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> CREAR NUEVO DOCUMENTO
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Agregar este modal al HTML -->
                    <div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius: 15px;">
                                <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                                    <h5 class="modal-title" id="whatsappModalLabel">
                                        <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-4">
                                        <label for="whatsappNumber" class="form-label">Número de WhatsApp</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+51</span>
                                            <input type="tel" class="form-control form-control-lg" id="whatsappNumber"
                                                placeholder="Ingrese número" maxlength="9"
                                                style="border-radius: 0 8px 8px 0;">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary" id="sendWhatsappBtn">
                                            <i class="fab fa-whatsapp me-2"></i>Enviar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal de Edición -->
                    <div class="modal fade" id="modalEditarProducto" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title">
                                        <i class="fa fa-edit me-2"></i>Editar Producto
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <div class="row g-2">
                                        <!-- Columna izquierda -->
                                        <div class="col-md-6">
                                            <!-- Código del producto -->
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">
                                                    <i class="fa fa-barcode me-1"></i> Código
                                                </label>
                                                <input type="text" class="form-control form-control-sm bg-light"
                                                    v-model="productoEdit.codigo_pp" readonly>
                                            </div>

                                            <!-- Precio con dropdown -->
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">
                                                    <i class="fa fa-money-bill me-1"></i> Precio
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="text" class="form-control dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        data-bs-auto-close="outside"
                                                        v-model="productoEdit.precio_mostrado"
                                                        style="background-color: #f8f9fa; cursor: pointer;" readonly>
                                                    <!-- Dropdown para precios base (desde el input) -->
                                                    <ul class="dropdown-menu w-100 shadow">
                                                        <li class="dropdown-header bg-light py-2 px-3">Precios Base</li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('PV', productoEdit.precioVenta)">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span><i class="fa fa-tag me-2"></i>Precio
                                                                        Venta:</span>
                                                                    <span
                                                                        class="badge bg-info text-white rounded-pill">S/
                                                                        {{ productoEdit.precioVenta }}</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <!-- <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('C', productoEdit.costo)">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span><i class="fa fa-box me-2"></i>Costo:</span>
                                                                    <span
                                                                        class="badge bg-info text-white rounded-pill">S/
                                                                        {{ productoEdit.costo }}</span>
                                                                </div>
                                                            </a>
                                                        </li> -->
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('PM', productoEdit.precio_mayor)">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span><i class="fa fa-warehouse me-2"></i>Precio
                                                                        Mayorista:</span>
                                                                    <span
                                                                        class="badge bg-info text-white rounded-pill">S/
                                                                        {{ productoEdit.precio_mayor }}</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                @click.prevent="seleccionarPrecioEditConTipo('PMn', productoEdit.precio_menor)">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span><i class="fa fa-store me-2"></i>Precio
                                                                        Minorista:</span>
                                                                    <span
                                                                        class="badge bg-info text-white rounded-pill">S/
                                                                        {{ productoEdit.precio_menor }}</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>

                                                 <button class="btn bg-rojo text-white dropdown-toggle" type="button"
    data-bs-toggle="dropdown" aria-expanded="false"
    data-bs-auto-close="outside"
    style="height: 38px;">
    <i class="fa fa-chevron-down"></i>
</button>
                                                    <!-- En el dropdown para precios adicionales (desde el botón) -->
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li class="dropdown-header bg-light py-2 px-3">Precios
                                                            Adicionales</li>
                                                        <template
                                                            v-if="preciosAdicionales && preciosAdicionales.length > 0">
                                                            <li v-for="(value, key) in preciosAdicionales" :key="key">
                                                                <a class="dropdown-item" href="#"
                                                                    @click.prevent="seleccionarPrecioEditConTipo(value.nombre, value.precio)">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center">
                                                                        <span><i class="fa fa-tag me-2"></i>{{
                                                                            value.nombre }}:</span>
                                                                        <span
                                                                            class="badge bg-info text-white rounded-pill">S/
                                                                            {{ value.precio }}</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        </template>
                                                        <li
                                                            v-if="!preciosAdicionales || preciosAdicionales.length === 0">
                                                            <div class="dropdown-item text-center text-muted py-2">
                                                                <i class="fa fa-info-circle me-1"></i> No hay precios
                                                                adicionales
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Cantidad -->
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">
                                                    <i class="fa fa-cubes me-1"></i> Cantidad
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-sm border-rojo text-rojo" type="button"
                                                        @click="productoEdit.cantidad = Math.max(1, parseInt(productoEdit.cantidad) - 1)">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center"
                                                        v-model="productoEdit.cantidad" @keypress="onlyNumber">
                                                    <button class="btn btn-sm border-rojo text-rojo" type="button"
                                                        @click="productoEdit.cantidad = parseInt(productoEdit.cantidad) + 1">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Columna derecha -->
                                        <div class="col-md-6">
                                            <!-- Nombre del producto -->
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">
                                                    <i class="fa fa-tag me-1"></i> Nombre
                                                </label>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model="productoEdit.nom_prod">
                                            </div>

                                            <!-- Precio Especial -->
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label class="form-label small mb-1">
                                                        <i class="fa fa-star me-1"></i> Precio Especial
                                                    </label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            v-model="usarPrecioEspecial" id="flexSwitchCheckDefault">
                                                        <label class="form-check-label small"
                                                            for="flexSwitchCheckDefault">Activar</label>
                                                    </div>
                                                </div>
                                                <div class="input-group input-group-sm mt-1">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control"
                                                        v-model="productoEdit.precioEspecial"
                                                        :disabled="!usarPrecioEspecial"
                                                        :class="{'bg-light': !usarPrecioEspecial}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detalle del producto (ancho completo) -->
                                    <div class="mb-2 mt-2">
                                        <label class="form-label small mb-1">
                                            <i class="fa fa-align-left me-1"></i> Descripción
                                        </label>
                                        <textarea class="form-control form-control-sm" id="detalle"
                                            v-model="productoEdit.detalle" rows="3" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2">
                                    <button type="button" class="btn btn-sm border-rojo text-rojo"
                                        data-bs-dismiss="modal">
                                        <i class="fa fa-times me-1"></i> Cancelar
                                    </button>
                                    <button type="button" class="btn btn-sm bg-rojo text-white"
                                        @click="actualizarProducto">
                                        <i class="fa fa-save me-1"></i> Guardar Cambios
                                    </button>
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
                                    <button type="button" class="btn  bg-white text-rojo border-rojo"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn bg-rojo text-white"
                                        @click="confirmarPagos">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="modalSelMultiProd" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered  modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Busqueda Multiple</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div v-if="pointSel==1">
                                        <div class="mb-3">
                                            <label class="form-label">Buscar Producto</label>
                                            <input v-model="dataKey" @keyup="busquedaKeyPess" type="text"
                                                class="form-control">
                                        </div>

                                        <div class="list-group" style=" height: 300px; overflow-y: scroll;">
                                            <label v-for="item in listaTempProd"
                                                class="list-group-item list-group-item-action"><input
                                                    v-model="itemsLista" :value="item" type="checkbox">
                                                {{item.value}}</label>
                                        </div>
                                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                                            <button @click="pasar2Poiter" class="btn btn-primary">Continuar</button>
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
                                                        <select style="width: 80px;" class="form-control"
                                                            v-model="item.precio_unidad">
                                                            <option v-for="(value, key) in item.precioProductos"
                                                                :value="value.precio" :key="key">{{ value.precio }}
                                                            </option>
                                                            </option>
                                                        </select>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                                            <button @click="pointSel=1" class="btn btn-warning">Regresar</button>
                                            <button @click="agregarProducto2Ps"
                                                class="btn bg-rojo text-white">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal para agregar condiciones  -->
<div class="modal fade" id="add-terminos" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title">Agregar Términos y Condiciones</h5>
                <button type="button" class="close btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div id="editor-container"></div>

                <!-- Nuevo: Selector para guardar condiciones -->
                <div class="mt-3 border-top pt-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="guardarCondiciones"
                            id="guardarSoloCotizacion" value="cotizacion" checked>
                        <label class="form-check-label" for="guardarSoloCotizacion">
                            Guardar solo para esta cotización
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="guardarCondiciones"
                            id="guardarTodasCotizaciones" value="todas">
                        <label class="form-check-label" for="guardarTodasCotizaciones">
                            Guardar para todas las cotizaciones
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo text-rojo" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn bg-rojo text-white" id="guardar-terminos-add">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const app = new Vue({
            el: "#container-vue",
            data: {
                descuentoGeneral: '',
                usar_scaner: false,
                mensajeProductoVisible: false,
                producto: {
                    tipo: 'producto',
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
                    // precio3: '',
                    // precio4: '',
                    precio_unidad: '',
                    precioVenta: '',
                    tipo_precio: 'PV',
                    precio_mostrado: '',
                    precio_usado: 1
                },
                productoEdit: {
                    index: -1,
                    codigo_pp: '',
                    nom_prod: '',
                    detalle: '',
                    cantidad: '',
                    precio: '',
                    precio_mostrado: '', // Nuevo
                    precioVenta: '', // Nuevo
                    costo: '', // Nuevo
                    precio_mayor: '', // Nuevo
                    precio_menor: '', // Nuevo
                    precioEspecial: ''
                },
                productos: [],
                precioProductos: [],
                usar_precio: '5',
                venta: {
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
                    moneda: '1',
                    tc: '',
                    tiene_inicial: false,
                    monto_inicial: 0,
                    porcentaje_inicial: 0,
                    asunto: ''

                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                pointSel: 1,
                asuntos: [],
                numeroCuotas: 1,
                cuotas: [],
                usarPrecioEspecial: false,
                preciosAdicionales: [],
                productoPreciosCache: {},
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
                mostrarMensajeProducto() {
                    // Mostrar el mensaje solo si no hay producto seleccionado
                    if (this.producto.descripcion.length === 0) {
                        this.mensajeProductoVisible = true;
                    }
                },

                seleccionarPrecio(precio) {
                    this.producto.precio = precio;
                    this.producto.precioVenta = precio;
                    this.producto.tipo_precio = 'PV'; // Por defecto
                },
                seleccionarPrecioConTipo(tipo, precio) {
                    //tipo and precio are passed as arguments to the function, so they are declared.
                    this.producto.precio_mostrado = precio
                    this.producto.precio = precio
                    this.producto.tipo_precio = tipo

                    // Si es precio de venta, actualizar también precioVenta
                    if (tipo === "PV") {
                        this.producto.precioVenta = precio
                    }

                    // Ocultar el mensaje cuando se selecciona un precio
                    this.mensajeProductoVisible = false
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

                //mostrarPreciosBase() {
                //    // Limpiamos el array de precios
                //    this.precioProductosBase = [];

                //    // Agregamos los precios base
                //    this.precioProductosBase.push({
                //        nombre: 'PV: Precio Venta',
                //        precio: this.producto.precio
                //    });
                //    this.precioProductosBase.push({
                //        nombre: 'C: Costo',
                //        precio: this.producto.costo
                //    });
                //    this.precioProductosBase.push({
                //        nombre: 'PM: Precio Mayorista',
                //        precio: this.producto.precio_mayor
                //    });
                //    this.precioProductosBase.push({
                //        nombre: 'PMn: Precio Minorista',
                //        precio: this.producto.precio_menor
                //    });

                //    // Asignamos los precios base a precioProductos
                //    this.precioProductos = this.precioProductosBase;
                //},


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
                // Reemplazar completamente el método editarProducto
                editarProducto(index) {
                    console.log("Iniciando edición del producto:", index);
                    const producto = this.productos[index];
                    console.log("Editando producto:", producto);

                    this.productoEdit = {
                        index: index,
                        codigo_pp: producto.codigo_pp,
                        nombre: producto.nombre || producto.descripcion,
                        nom_prod: producto.nombre || producto.descripcion,
                        detalle: producto.detalle || "",
                        cantidad: producto.cantidad,
                        precio_mostrado: producto.precio || producto.precioVenta || "0.00",
                        precioVenta: producto.precioVenta || producto.precio || "0.00",
                        costo: producto.costo || "0.00",
                        precio_mayor: producto.precio_mayor || "0.00",
                        precio_menor: producto.precio_menor || "0.00",
                        precio: producto.precio || "0.00",
                        precio_original: producto.precio || "0.00",
                        precioEspecial: producto.precioEspecial || "",
                        tipo_precio: producto.tipo_precio || "PV",
                        productoid: producto.id_producto || producto.productoid, // Asegurarse de usar el ID correcto
                    };

                    // Establecer si se está usando precio especial
                    this.usarPrecioEspecial = !!producto.precioEspecial;

                    // Inicializar precios adicionales como vacío
                    this.preciosAdicionales = [];

                    // Cargar precios adicionales directamente
                    if (producto.id_producto || producto.productoid) {
                        const productoId = producto.id_producto || producto.productoid;
                        const tipo = producto.tipo || "producto";
                        const url = _URL + "/ajs/cargar/producto_precios/" + productoId;

                        console.log("Cargando precios adicionales para edición:", productoId, tipo);

                        $.ajax({
                            url: url,
                            type: "GET",
                            dataType: "json",
                            success: (data) => {
                                console.log("Precios adicionales recibidos en edición:", data);

                                if (data && data.length > 0) {
                                    // Usar Vue.set para asegurar reactividad
                                    this.preciosAdicionales = data.map((item) => ({
                                        nombre: item.nombre,
                                        precio: Number.parseFloat(item.precio).toFixed(2),
                                    }));

                                    console.log("Precios adicionales asignados:", this.preciosAdicionales);
                                }
                            },
                            error: (xhr, status, error) => {
                                console.error("Error cargando precios adicionales:", error);
                            },
                        });
                    }

                    new bootstrap.Modal(document.getElementById("modalEditarProducto")).show();
                },



                // Método para seleccionar precio en el modal de edición
                seleccionarPrecioEditConTipo(tipo, precio) {
                    console.log("Seleccionando precio:", tipo, precio);
                    this.productoEdit.precio_mostrado = precio
                    this.productoEdit.precio = precio
                    this.productoEdit.tipo_precio = tipo

                    // Cerrar el dropdown después de seleccionar
                    const dropdowns = document.querySelectorAll(".dropdown-menu.show")
                    dropdowns.forEach((dropdown) => {
                        dropdown.classList.remove("show")
                    })
                },

                // Actualizar el método actualizarProducto para manejar el precio especial
                actualizarProducto() {
                    const index = this.productoEdit.index
                    if (index > -1) {
                        this.productos[index] = {
                            ...this.productos[index],
                            nombre: this.productoEdit.nom_prod,
                            descripcion: this.productoEdit.nom_prod,
                            detalle: this.productoEdit.detalle,
                            cantidad: this.productoEdit.cantidad,
                            precioVenta: this.productoEdit.precio,
                            precio: this.productoEdit.precio,
                            precio_mostrado: this.productoEdit.precio_mostrado,
                            tipo_precio: this.productoEdit.tipo_precio,
                            asunto: this.venta.asunto,
                            precioEspecial: this.usarPrecioEspecial ? this.productoEdit.precioEspecial : null,
                        }

                        // Recalcular totales forzando una actualización reactiva
                        this.productos = [...this.productos]

                        // Mostrar alerta de éxito
                        Swal.fire({
                            icon: "success",
                            title: "Producto actualizado",
                            text: "Los cambios se han guardado correctamente",
                            timer: 1500,
                            showConfirmButton: false,
                        })

                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById("modalEditarProducto")).hide()
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
                onChangeAlmacen(event) {
                    console.log("Cambio de almacén:", event.target.value);
                    this.producto.almacen = event.target.value;
                    var self = this;

                    $("#input_buscar_productos").autocomplete({
                        source: _URL + `/ajs/cargar/productos/${self.producto.almacen}`,
                        minLength: 1,
                        select: function (event, ui) {
                            event.preventDefault();
                            console.log("Producto seleccionado:", ui.item);

                            // Asignar valores del producto
                            app.producto.productoid = ui.item.codigo; // Este es el id_producto
                            app.producto.codigo_pp = ui.item.codigo_pp;
                            app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre;
                            app.producto.nom_prod = ui.item.nombre;
                            app.producto.cantidad = '';
                            app.producto.stock = ui.item.cnt;
                            app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2);
                            app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2);
                            app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_unidad + "").toFixed(2);
                            app.producto.codigo = ui.item.codigo;
                            app.producto.codigo_prod = ui.item.codigo_pp;
                            app.producto.costo = ui.item.costo;
                            app.producto.precioVenta = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2);
                            app.producto.precio_original = app.producto.precio;
                            app.producto.tipo_precio = 'PV'; // Establecer el tipo de precio por defecto

                            // En el evento select del autocomplete:
                            cargarPreciosAdicionales(ui.item.codigo, ui.item.tipo);


                            $('#input_buscar_productos').val("");
                            $("#example-text-input").focus();
                        }
                    });
                },
                formatoDecimal(num, desc = 2) {
                    return parseFloat(num + "").toFixed(desc);
                },
                visualFechaSee(fecha) {
                    return formatFechaVisual(fecha);
                },
                formatDate(date) {
                    try {
                        // Asegurarse de que date sea un objeto Date
                        if (!(date instanceof Date)) {
                            date = new Date(date);
                        }

                        // Verificar si la fecha es válida
                        if (isNaN(date.getTime())) {
                            console.error("Fecha inválida en formatDate:", date);
                            // Si la fecha no es válida, usar la fecha actual
                            date = new Date();
                        }

                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');

                        const formattedDate = `${year}-${month}-${day}`;
                        console.log("Fecha formateada:", formattedDate);
                        return formattedDate;
                    } catch (e) {
                        console.error("Error en formatDate:", e);
                        // En caso de error, devolver la fecha actual formateada
                        const now = new Date();
                        return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
                    }
                },
                onlyNumberComas($event) {
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 44) { // 46 is dot
                        $event.preventDefault();
                    }
                },
                // focusDiasPagos() {
                //     //console.log("1000000000000000000")
                //     $("#modal-dias-pagos").modal("show")
                // },
                changeTipoPago(event) {
                    console.log(event.target.value)
                    this.venta.fechaVen = this.venta.fecha;
                    this.venta.dias_lista = []
                    this.venta.dias_pago = ''
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
                    if (this.venta.num_doc.length == 8 || this.venta.num_doc.length == 11) {
                        $("#loader-menor").show()
                        this.venta.dir_pos = 1
                        _ajax("/ajs/prealerta/doc/cliente", "POST", {
                            doc: this.venta.num_doc
                        },
                            function (resp) {
                                $("#loader-menor").hide()
                                if (resp) {
                                    // Si es DNI
                                    if (resp.success && resp.dni) {
                                        app.venta.nom_cli = `${resp.nombres} ${resp.apellidoPaterno} ${resp.apellidoMaterno}`.trim()
                                        app.venta.dir_cli = ''
                                        app.venta.departamento = ''
                                        app.venta.provincia = ''
                                        app.venta.distrito = ''
                                        app.venta.ubigeo = ''
                                        app.venta.tipo_doc = '1' // Cambiar a Boleta
                                        alertExito("Éxito", "Documento DNI encontrado")
                                    }
                                    // Si es RUC 
                                    else if (resp.ruc) {
                                        app.venta.nom_cli = resp.razonSocial
                                        app.venta.dir_cli = resp.direccion || ''
                                        app.venta.departamento = resp.departamento || ''
                                        app.venta.provincia = resp.provincia || ''
                                        app.venta.distrito = resp.distrito || ''
                                        app.venta.ubigeo = resp.ubigeo || ''
                                        app.venta.tipo_doc = '2' // Cambiar a Factura
                                        alertExito("Éxito", "Documento RUC encontrado")
                                    }
                                    else {
                                        alertAdvertencia("No se encontraron datos para el documento ingresado")
                                    }
                                    // Actualizar serie y número
                                    app.onChangeTiDoc()
                                } else {
                                    alertAdvertencia("No se recibió respuesta del servidor")
                                }
                            })
                    } else {
                        alertAdvertencia("El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC")
                    }
                },

                guardarVenta() {
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
                                    mensaje = 'Debe especificar los días de pagos para un cotizacion a crédito';
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
                                    mensaje = 'Debe especificar los días de pagos para un cotizacion a crédito';
                                }
                            }
                        }

                        if (continuar) {
                            const data = {
                                ...this.venta,
                                usar_precio: this.usar_precio,
                                listaPro: JSON.stringify(this.productos),
                                descuento: this.descuentoGeneral || 0,
                                asunto: this.venta.asunto // Incluir el asunto en los datos
                            }
                            data.dias_lista = JSON.stringify(data.dias_lista)
                            console.log(data);
                            $("#loader-menor").show();
                            _ajax("/ajs/cotizaciones/add", "POST",
                                data,
                                function (resp) {
                                    console.log(resp);
                                    if (resp.res) {
                                        // Actualizar el modal con los datos de la cotización
                                        $('#cotizacion-numero').text(resp.cotizacion.numero);
                                        $('#pdf-preview').attr('src', resp.cotizacion.pdfUrl);

                                        // Configurar URLs de los botones
                                        $('#btn-a4').off('click').on('click', function (e) {
                                            e.preventDefault();
                                            $('#pdf-preview').attr('src', resp.cotizacion.pdfUrl);
                                        });

                                        $('#btn-media-a4').off('click').on('click', function (e) {
                                            e.preventDefault();
                                            $('#pdf-preview').attr('src', `/r/cotizaciones/reporte-media-a4/${resp.cotizacion.cotizacion_id}`);
                                        });

                                        $('#btn-voucher-8cm').off('click').on('click', function (e) {
                                            e.preventDefault();
                                            $('#pdf-preview').attr('src', `/r/cotizaciones/reporte-voucher-8cm/${resp.cotizacion.cotizacion_id}`);
                                        });

                                        $('#btn-voucher-5-6cm').off('click').on('click', function (e) {
                                            e.preventDefault();
                                            $('#pdf-preview').attr('src', `/r/cotizaciones/reporte-voucher-5-6cm/${resp.cotizacion.cotizacion_id}`);
                                        });

                                        // Configurar el botón de WhatsApp para abrir el modal
                                        $('#btn-whatsapp').off('click').on('click', function (e) {
                                            e.preventDefault();
                                            $('#whatsappNumber').val('');
                                            $('#whatsappModal').modal('show');
                                        });

                                        // Manejar el envío por WhatsApp
                                        $('#sendWhatsappBtn').off('click').on('click', function () {
                                            const phoneNumber = $('#whatsappNumber').val().trim();

                                            if (!phoneNumber) {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Atención',
                                                    text: 'Por favor ingrese un número de teléfono'
                                                });
                                                return;
                                            }

                                            if (phoneNumber.length !== 9) {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Atención',
                                                    text: 'El número debe tener 9 dígitos'
                                                });
                                                return;
                                            }

                                            // Crear mensaje personalizado
                                            const message = `Te envío la cotización para que puedas revisarla con detalle Cotización N° ${resp.cotizacion.numero}\n\nPuedes revisarla aquí: ${resp.cotizacion.pdfUrl}\n\nSi tienes alguna consulta o necesitas más información, no dudes en escribirme. Estaré encantada de ayudarte.`;

                                            // Generar URL de WhatsApp
                                            const whatsappUrl = `https://api.whatsapp.com/send?phone=51${phoneNumber}&text=${encodeURIComponent(message)}`;

                                            // Cerrar el modal y abrir WhatsApp
                                            $('#whatsappModal').modal('hide');
                                            window.open(whatsappUrl, '_blank');
                                        });

                                        // Validación de entrada numérica
                                        $('#whatsappNumber').off('input').on('input', function () {
                                            this.value = this.value.replace(/[^0-9]/g, '');
                                        });

                                        $('#btn-guia').attr('href', `/guia/remision/registrar?coti=${resp.cotizacion.cotizacion_id}`);

                                        // Mostrar el modal de éxito
                                        $('#modal-cotizacion-success').modal('show');

                                        // Configurar los botones del footer
                                        $('#modal-cotizacion-success').on('hidden.bs.modal', function () {
                                            $("#backbuttonvp").click();
                                        });
                                    } else {
                                        alertAdvertencia("No se pudo Guardar la Cotizacion: " +
                                            (resp.error ? resp.error : "Error desconocido"));
                                    }
                                }
                            )
                        } else {
                            alertAdvertencia(mensaje)
                        }
                    } else {
                        alertAdvertencia("No hay productos agregados a la lista ")
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
                        // precio3: '',
                        // precio4: '',
                        precio_unidad: '',
                        precioVenta: '',
                        tipo_precio: 'PV',
                        precio_mostrado: '',
                        precio_usado: 1
                    }
                    this.precioProductos = [];
                },
                addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2, precio_unidad) {
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
                    if (this.producto.descripcion.length > 0) {
                        // Creamos una copia del producto actual
                        const prod = {
                            ...this.producto,
                            // Usar el precio_mostrado en lugar de precioVenta
                            precioVenta: parseFloat(this.producto.precio_mostrado).toFixed(2),
                            detalle: this.producto.detalle || '',
                            // Agregar el tipo de precio seleccionado
                            tipo_precio: this.producto.tipo_precio
                        }

                        // Formateamos el precio especial a 2 decimales si existe
                        if (prod.precioEspecial && prod.precioEspecial !== '') {
                            prod.precioEspecial = parseFloat(prod.precioEspecial).toFixed(2)
                        }

                        // Formateamos los demás precios a 2 decimales
                        prod.precio = parseFloat(prod.precio).toFixed(2)
                        prod.precio2 = parseFloat(prod.precio2).toFixed(2)
                        prod.precio_unidad = parseFloat(prod.precio_unidad).toFixed(2)

                        // Agregamos el producto al array de productos
                        this.productos.push(prod)
                        console.log('producto agregado', prod)

                        // Limpiamos el formulario y reseteamos los precios
                        this.limpiasDatos()
                        this.producto.precio_mostrado = ''
                        this.producto.tipo_precio = 'PV'
                    } else {
                        alertAdvertencia("Busque un producto primero")
                            .then(function () {
                                setTimeout(function () {
                                    $("#input_buscar_productos").focus()
                                }, 500)
                            })
                    }
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
                }

            },

            computed: {

                monedaSibol() {
                    return (this.venta.moneda == 1 ? 'S/' : '$')
                },

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
                },
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
                }
            }
        });

        // Modificar la función cargarPreciosAdicionales para que solo muestre precios de producto_precios
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


        app.buscarSNdoc();
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

        // Modificar la función onChangeAlmacen para asignar correctamente los precios
        $("#input_buscar_productos").autocomplete({
            source: function (request, response) {
                // Realizamos dos búsquedas en paralelo
                $.when(
                    $.get(_URL + `/ajs/cargar/productos/${app.producto.almacen}?term=${request.term}`),
                    $.get(_URL + `/ajs/cargar/repuestos/${app.producto.almacen}?term=${request.term}`)
                ).done(function (productosResp, repuestosResp) {
                    // Combinamos los resultados
                    let productos = JSON.parse(productosResp[0]);
                    let repuestos = JSON.parse(repuestosResp[0]);

                    // Agregamos un identificador para saber si es producto o repuesto
                    productos = productos.map(p => ({ ...p, tipo: 'producto' }));
                    repuestos = repuestos.map(r => ({ ...r, tipo: 'repuesto' }));

                    // Combinamos y enviamos los resultados
                    response([...productos, ...repuestos]);
                });
            },
            minLength: 1,
            select: function (event, ui) {
                event.preventDefault();

                // Asignar tipo y ID según sea producto o repuesto
                if (ui.item.tipo === 'repuesto') {
                    app.producto.tipo = 'repuesto';
                    app.producto.id_repuesto = ui.item.codigo;
                } else {
                    app.producto.tipo = 'producto';
                    app.producto.id_producto = ui.item.codigo;
                }

                app.producto.codigo_pp = ui.item.codigo_pp;
                app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre;
                app.producto.nom_prod = ui.item.nombre;
                app.producto.cantidad = '1';
                app.producto.stock = ui.item.cnt;

                // Establecer los precios
                const precioVenta = parseFloat(ui.item.precio || 0).toFixed(2);
                app.producto.precioVenta = precioVenta; // Precio de venta que se mantiene
                app.producto.precio_mostrado = precioVenta; // Precio que se muestra inicialmente
                app.producto.precio = precioVenta;
                app.producto.costo = parseFloat(ui.item.costo || 0).toFixed(2);
                app.producto.precio_mayor = parseFloat(ui.item.precio_mayor || 0).toFixed(2);
                app.producto.precio_menor = parseFloat(ui.item.precio_menor || 0).toFixed(2);
                app.producto.precio2 = parseFloat(ui.item.precio2 || 0).toFixed(2);
                app.producto.precio_unidad = parseFloat(ui.item.precio_unidad || 0).toFixed(2);
                app.producto.codigo = ui.item.codigo;
                app.producto.usar_multiprecio = ui.item.usar_multiprecio;
                app.producto.tipo_precio = 'PV'; // Establecer tipo inicial como PV

                // Cargar los precios adicionales de producto_precios
                cargarPreciosAdicionales(ui.item.codigo, ui.item.tipo);

                $('#input_buscar_productos').val("");
                $("#example-text-input").focus();
                app.producto.detalle = ui.item.detalle || '';
            }

        });



        $("#example-text-input").on('keypress', function (e) {
            if (e.which == 13) {
                $("#submit-a-product").click()
                $("#input_buscar_productos").focus()
            }
        });
        $(document).ready(function () {
            // Utilizamos eventos delegados para manejar los clics en los botones de "Editar" y "Guardar"
            $(document).on("click", ".btnedit", function () {
                // Encuentra el padre <tr> del botón
                var tr = $(this).closest("tr");
                // Muestra los inputs y oculta los span con clase "save"
                tr.find(".edit").show();
                tr.find(".save").hide();
                tr.find(".btnedit").hide();
                tr.find(".btnsave").show();
            });

            $(document).on("click", ".btnsave", function () {
                // Encuentra el padre <tr> del botón
                var tr = $(this).closest("tr");
                // Oculta los inputs y muestra los span con clase "save"
                tr.find(".edit").hide();
                tr.find(".save").show();
                tr.find(".btnedit").show();
                tr.find(".btnsave").hide();
            });
        });
    })

</script>
<script src="<?= URL::to('public/js/cotizaciones/condiciones-terminos.js') ?>?v=<?= time() ?>"> </script>
<script>
    function toggleInput(checkbox) {
        // Busca el input relacionado a la clase 'precio-input' dentro del mismo contenedor
        const input = checkbox.parentElement.querySelector('.precio-input');
        input.disabled = !checkbox.checked;  // Habilita el input si la casilla está marcada
    }

    function onlyNumber(event) {
        const keyCode = event.keyCode || event.which;
        // Permite solo números (códigos ASCII 48-57 para 0-9)
        if (keyCode < 48 || keyCode > 57) {
            event.preventDefault();
        }
    }
</script>