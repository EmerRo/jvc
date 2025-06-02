<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <!-- <h6 class="page-title">Cotización</h6> -->
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Cotizacion</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438;">Editar Cotizacion</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">

            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?= $coti ?>" id="cotizacion">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title"></h4>

                <div class="card-title-desc">
                    <div class="col-lg-12 text-end">
                        <a hidden href="/ventas/productos?coti=<?= $coti ?>" class="btn btn-success button-link"
                            id="btnVenta">Proceder a Vender</a>
                        <button type="button" onclick="$('#btn_finalizar_pedido').click()" class="btn bg-rojo text-white">
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
                                                <div class="form-group row mb-3">
                                                    <label class="col-lg-2 control-label">Buscar</label>
                                                    <div class="col-lg-10">
                                                        <input type="text" placeholder="Consultar Productos"
                                                            class="form-control ui-autocomplete-input"
                                                            id="input_buscar_productos" autocomplete="off">
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
                                                <div class="form-group row  mb-3">
                                                    <!-- primera fila con stock actual, cantidad y precio -->

                                                    <div class="col-lg-10">
                                                        <div class="row">

                                                            <div class="row  col-lg-3">
                                                                <label for="example-text-input" class="col-form-label">
                                                                    Stock Actual
                                                                </label>
                                                                <input disabled v-model="producto.stock"
                                                                    class="form-control text-center" type="text"
                                                                    placeholder="0">
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

                                                            <div class=" col-lg-3">
                                                                <label for="example-text-input"
                                                                    class="col-form-label">Precio</label>
                                                                <div class="input-group">
                                                                    <input id="precio-input" type="text"
                                                                        class="form-control dropdown-toggle"
                                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                                        v-model="producto.precio_mostrado"
                                                                        style="height: 38px; background-color: #f8f9fa; cursor: pointer;"
                                                                        readonly>
                                                                    <ul class="dropdown-menu w-100">
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('PV', producto.precioVenta)">
                                                                                PV: {{ producto.precioVenta }}
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('C', producto.costo)">
                                                                                C: {{ producto.costo }}
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('PM', producto.precio_mayor)">
                                                                                PM: {{ producto.precio_mayor }}
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                @click.prevent="seleccionarPrecioConTipo('PMn', producto.precio_menor)">
                                                                                PMn: {{ producto.precio_menor }}
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="input-group-append">
                                                                        <button
                                                                            class="btn btn-outline-secondary dropdown-toggle"
                                                                            type="button" data-bs-toggle="dropdown"
                                                                            aria-expanded="false"
                                                                            style="background-color: #CA3438; color: white;">
                                                                            <i class="fa fa-tag"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li v-for="(value, key) in precioProductos"
                                                                                :key="key">
                                                                                <a class="dropdown-item" href="#"
                                                                                    @click.prevent="seleccionarPrecioConTipo(value.nombre, value.precio)"
                                                                                    style="color: #6c757d;">
                                                                                    {{ value.nombre }}: {{ value.precio
                                                                                    }}
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Nuevos campos para Precio Especial y Descuento -->
                                                <div class="row mt-3">
                                                    <!-- Campo de Precio Especial -->
                                                    <div class="col-lg-6 d-flex align-items-center">
                                                        <label for="example-text-input"
                                                            class="col-form-label mb-0 me-2">Precio Especial</label>



                                                        <input type="checkbox" class="toggle-checkbox"
                                                            onclick="toggleInput(this)" style="margin-right: 10px;" />
                                                        <input id="precio-especial"
                                                            class="form-control text-center precio-input"
                                                            @keypress="onlyNumber" v-model="producto.precioEspecial"
                                                            type="text" placeholder="0" disabled
                                                            style="max-width: 80px;">
                                                    </div>


                                                    <!-- Campo de Descuento -->
                                                    <div class="col-lg-6 d-flex align-items-center">
                                                        <label for="descuento-general" class="col-form-label mb-0 me-2">
                                                            Descuento %
                                                        </label>
                                                        <input type="checkbox" class="toggle-checkbox"
                                                            onclick="toggleInput(this)" style="margin-right: 10px;" />
                                                        <input id="descuento-general"
                                                            class="form-control text-center precio-input"
                                                            @keypress="onlyNumber" v-model="descuentoGeneral"
                                                            type="text" placeholder="0" disabled
                                                            style="max-width: 80px;">
                                                    </div>
                                                    <div class="col-lg-2 d-flex align-items-center">
                                                        <button id="submit-a-product" type="submit" c
                                                            class="btn bg-rojo text-white w-100 d-flex align-items-center justify-content-center"
                                                            style="min-width: 110px;"></i>
                                                            Agregar
                                                        </button>
                                                    </div>
                                                </div>



                                            </form>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-9"></div>
                                            <div class="col-md-3" v-if="productos.length > 0">
                                                <!-- <label for="">Usar</label> -->
                                                <!-- <select name="" id="" class="form-control text-right" v-model="usar_precio" @change="cambiarPrecio($event)">
                                                    <option value="1">Precio 1</option>
                                                    <option value="2">Precio 2</option>
                                                    <option value="3">Precio 3</option>
                                                    <option value="4">Precio Club</option>
                                                    <option value="5">Precio Unidad</option>
                                                </select> -->
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="margin-top: 25px">
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
                                                        <th>Código</th>
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
                                                        <td>{{item.codigo_pp}}</td>
                                                        <td>{{item.nombre || item.descripcion}}</td>
                                                        <td>
                                                            <input v-if="item.editable" v-model="item.cantidad">
                                                            <span
                                                                v-if="!item.editable">{{formatNumber(item.cantidad)}}</span>
                                                        </td>
                                                        <td><input v-if="item.editable" v-model="item.precioVenta">
                                                            <span v-if="!item.editable">{{item.precioVenta}}</span>
                                                        </td>
                                                        <td>{{formatoDecimal(item.precioVenta * item.cantidad, 2)}}</td>
                                                        <td>{{item.precioEspecial ?
                                                            parseFloat(item.precioEspecial).toFixed(2) : '-'}}</td>
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
                                                                    @click="buscarDocumentSS" >
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
                                                                    placeholder="Dirección"
                                                                    class="form-control ui-autocomplete-input"
                                                                    autocomplete="off">
                                                                <!-- <div class="input-group-addon"><input
                                                                        v-model="venta.dir_pos" name="dirserl" value="1"
                                                                        type="radio" class="form-check-input"></div> -->
                                                            </div>

                                                        </div>
                                                    </div>
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
                                                                type="button" class="btn btn-lg btn-primary"
                                                                id="btn_finalizar_pedido">
                                                                <i class="fa fa-save"></i> Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <button class="btn border-rojo text-rojo" id="edit-condiciones"
                                                style="margin-bottom: 5px;">Modificar Terminos</button>

                                            <div class="bg-rojo pv-15 text-center  p-3"
                                                style="height: 90px; color: white">
                                                <h1 class="mv-0 font-400" id="lbl_suma_pedido">S/ {{totalProdustos}}
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
                                            <i class="fas fa-check-circle me-2"></i>¡Cotización Actualizada!
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


                    <!-- <div class="modal fade" id="modal-dias-pagos" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="exampleModalLabel">Días de Pagos</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="">
                                                <label class="form-label">Fecha Emisión</label>
                                                <input v-model="venta.fecha" disabled type="date" class="form-control">
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
                                    <div class="mb-3">
                                        <label class="form-label">Días de pagos</label>
                                        <input placeholder="10,20,30,........" v-model="venta.dias_pago"
                                            @keypress="onlyNumberComas" type="text" class="form-control">
                                        <div class="form-text">Separe por comas los días de pagos</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="text-center table-sm table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Fecha</th>
                                                        <th>Monto</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in venta.dias_lista">
                                                        <td></td>
                                                        <td>{{visualFechaSee(item.fecha)}}</td>
                                                        <td>S/ {{formatoDecimal(item.monto)}}</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2">Total</th>
                                                        <th>{{totalValorListaDias}}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Modal de Configuración de Pagos Modificado -->
                    <div class="modal fade" id="modal-dias-pagos" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
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
                                            <button class="btn btn-primary" type="button"
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
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-primary"
                                        @click="confirmarPagos">Confirmar</button>
                                </div>
                            </div>
                        </div>
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
                                    <!-- Código del producto -->
                                    <div class="mb-3">
                                        <label class="form-label">Código</label>
                                        <input type="text" class="form-control" v-model="productoEdit.codigo_pp"
                                            readonly>
                                    </div>
                                    <!-- Nombre del producto -->
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" v-model="productoEdit.nom_prod">
                                    </div>
                                    <!-- Detalle del producto -->
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" id="detalle" v-model="productoEdit.detalle"
                                            rows="5"></textarea>
                                    </div>
                                    <!-- Cantidad -->
                                    <div class="mb-3">
                                        <label class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" v-model="productoEdit.cantidad"
                                            @keypress="onlyNumber">
                                    </div>
                                    <!-- Modificar el modal de edición para incluir el selector de precios con icono verde -->
                                    <!-- Reemplazar el div del precio en el modal de edición (líneas aproximadas 450-460) -->

                                    <!-- Precio -->
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
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="background-color: #CA3438; color: white;">
                                                    <i class="fa fa-tag"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li v-for="(value, key) in precioProductosEdit" :key="key">
                                                        <a class="dropdown-item" href="#"
                                                            @click.prevent="seleccionarPrecioEditConTipo(value.nombre, value.precio)"
                                                            style="color: #6c757d;">
                                                            {{ value.nombre }}: {{ value.precio }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Precio Especial</label>
                                        <input type="number" class="form-control" v-model="productoEdit.precioEspecial">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo text-rojo"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn bg-rojo text-white" @click="actualizarProducto">
                                        Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para editar condiciones -->
                    <div class="modal fade" id="edit-terminos" tabindex="-1" role="dialog" data-backdrop="static">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title">Editar Términos y Condiciones</h5>
                                    <button type="button" class="close btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                   
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="editor-container"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo text-rojo" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn bg-rojo text-white" id="guardar-terminos">Guardar
                                        cambios</button>
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
    $(document).ready(function () {
        const app = new Vue({
            el: "#container-vue",
            data: {
                descuentoGeneral: '',
                producto: {
                    editable: false,
                    productoid: "",
                    tipo: 'producto',
                    descripcion: "",
                    detalle: "",
                    nom_prod: "",
                    cantidad: "",
                    stock: "",
                    precio: "",
                    codigo: "",
                    costo: "",
                    codsunat: "",
                    precio: '1',
                    almacen: '1',
                    precio2: '',
                    precio3: '',
                    precio4: '',
                    precio_unidad: '',
                    precioVenta: '',
                    precio_mayor: '',
                    precio_menor: '',
                    tipo_precio: 'PV',
                    precio_mostrado: '',
                    precioEspecial: '',
                    precio_usado: 1,
                    index: ''
                },
                productoEdit: {
                    index: -1,
                    codigo_pp: '',
                    nom_prod: '',
                    detalle: '',
                    cantidad: '',
                    precio: '',
                    precioEspecial: ''
                },
                productos: [],
                precioProductos: [],
                usar_precio: '1',
                venta: {
                    cotiId: '',
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
                    asunto: ''

                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                pointSel: 1,
                asuntos: [],
                numeroCuotas: 1,
                cuotas: [],
                precioProductosEdit: [],
            },
            watch: {
                'descuentoGeneral': function (newValue) {

                    if (newValue && !isNaN(newValue)) {
                        const descuento = parseFloat(newValue);
                        if (descuento > 100) {
                            this.descuentoGeneral = '100';
                        }
                    }
                },
                'venta.dias_pago': {
                    handler: function (newValue) {
                        const listD = (newValue + "").split(",");
                        this.dias_lista = [];
                        if (listD.length > 0) {

                            var listaTemp = listD.filter(ite => ite.length > 0)
                            const palorInicial = (parseFloat(this.venta.total + "") / listaTemp.length).toFixed(0)
                            var totalValos = parseFloat(this.venta.total + "");
                            listaTemp = listaTemp.map((num, index) => {
                                var fecha_ = new Date(this.venta.fecha)
                                const dias_ = parseInt(num + "")
                                fecha_.setDate(fecha_.getDate() + dias_);
                                var value = 0;
                                if (index + 1 == listaTemp.length) {
                                    value = totalValos;
                                    this.venta.fechaVen = this.formatDate(fecha_)
                                } else {
                                    value = palorInicial;
                                    totalValos -= palorInicial;
                                }
                                return {
                                    fecha: this.formatDate(fecha_),
                                    monto: value
                                }
                            });

                            this.venta.dias_lista = listaTemp

                        }
                    },
                    deep: true
                }
            },
            methods: {
                seleccionarPrecioConTipo(tipo, precio) {
                    this.producto.precio_mostrado = precio;
                    this.producto.precio = precio;
                    this.producto.tipo_precio = tipo;

                    // Si es precio de venta, actualizar también precioVenta
                    if (tipo === 'PV') {
                        this.producto.precioVenta = precio;
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
                            console.error("Fecha base inválida, usando fecha actual:", fechaBase);
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

                editarProducto(index) {
                    const producto = this.productos[index];
                    this.productoEdit = {
                        index: index,
                        codigo_pp: producto.codigo_pp,
                        nombre: producto.nombre || producto.descripcion,
                        nom_prod: producto.nombre || producto.descripcion,
                        detalle: producto.detalle || '',
                        cantidad: producto.cantidad,
                        precio: producto.precioVenta || producto.precio || '0.00',
                        precio_mostrado: producto.precioVenta || producto.precio || '0.00',
                        precioVenta: producto.precioVenta || producto.precio || '0.00',
                        costo: producto.costo || '0.00',
                        precio_mayor: producto.precio_mayor || producto.precio2 || '0.00',
                        precio_menor: producto.precio_menor || producto.precio_unidad || '0.00',
                        tipo_precio: producto.tipo_precio || 'PV',
                        precioEspecial: producto.precioEspecial || ''
                    };

                    // Cargar precios adicionales para el producto
                    this.cargarPreciosAdicionalesParaEdicion(producto.productoid, producto.tipo || 'producto');

                    new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
                },
                actualizarProducto() {
                    const index = this.productoEdit.index;
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
                            precioEspecial: this.productoEdit.precioEspecial || null
                        };

                        // Recalcular totales forzando una actualización reactiva
                        this.productos = [...this.productos];

                        // Mostrar alerta de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Producto actualizado',
                            text: 'Los cambios se han guardado correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                    }
                },



                cambiarPrecio(event) {

                    var self = this

                    this.productos.forEach(element => {
                        if (event.target.value == 1) {
                            element.precioVenta = parseFloat(element.precio + "").toFixed(2)
                            element.precio_usado = '1'
                        } else if (event.target.value == 2) {
                            element.precioVenta = parseFloat(element.precio2 + "").toFixed(2)
                            element.precio_usado = '2'
                        } else if (event.target.value == 3) {
                            element.precioVenta = parseFloat(element.precio3 + "").toFixed(2)
                            element.precio_usado = '3'

                        } else if (event.target.value == 4) {
                            element.precioVenta = parseFloat(element.precio4 + "").toFixed(2)
                            element.precio_usado = '4'
                        } else {
                            element.precioVenta = parseFloat(element.precio_unidad + "").toFixed(2)
                            element.precio_usado = '5'
                        }

                    });
                    self.usar_precio = event.target.value
                },
                cargarCotizacion() {
                    const vue = this;
                    _post("/ajs/cotizaciones/info", {
                        coti: $("#cotizacion").val()
                    },
                        function (resp) {
                            console.log('Respuesta de cotización:', resp);
                            vue.productos = resp.productos.map(producto => {
                                return {
                                    ...producto,
                                    editable: false,
                                    precioEspecial: producto.precioEspecial || '',
                                    nombre: producto.nombre || producto.nom_prod || producto.descripcion,
                                    productoid: producto.productoid || producto.id_producto || producto.id_repuesto
                                }
                            });

                            vue.venta.fecha = resp.fecha
                            vue.venta.cotiId = resp.cotizacion_id
                            vue.venta.tipo_doc = resp.id_tido
                            vue.venta.tipo_pago = resp.id_tipo_pago
                            vue.venta.dias_pago = resp.dias_pagos
                            vue.venta.dir_pos = parseInt(resp.direccion + "")
                            vue.venta.num_doc = resp.cliente_doc
                            vue.venta.nom_cli = resp.cliente_nom
                            vue.venta.dir_cli = resp.cliente_dir1
                            vue.venta.dir2_cli = resp.cliente_dir2
                            vue.venta.asunto = resp.asunto || ''


                            vue.descuentoGeneral = resp.descuento || '';

                            setTimeout(function () {
                                vue.venta.dias_lista = resp.cuotas
                            }, 1000)


                        }
                    )
                },
                formatoDecimal(valor, decimales = 2) {
                    return parseFloat(valor).toFixed(decimales);
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
                editarProd(index, i) {
                    this.producto.index = i
                    var self = this
                    _ajax("/ajs/consulta/prod/coti", "POST", {
                        index
                    },
                        function (resp) {
                            $("#loader-menor").hide();
                            console.log('Producto editado:', resp);
                            self.producto.nombre = resp.nombre;
                            self.producto.descripcion = resp.id_producto + ' | ' + resp.nombre;
                            self.producto.stock = resp.cantidad;
                            self.producto.cantidad = '';
                            self.producto.codigo = resp.id_producto;
                            self.producto.productoid = resp.id_producto;
                            self.producto.nom_prod = resp.nombre;
                            self.producto.precio = parseFloat(resp.precio + "").toFixed(2);
                            self.producto.precio2 = parseFloat(resp.precio2 + "").toFixed(2);
                            self.producto.precio3 = parseFloat(resp.precio3 + "").toFixed(2);
                            self.producto.precioVenta = parseFloat(resp.precio + "").toFixed(2);
                            self.producto.precioEspecial = resp.precioEspecial ? parseFloat(resp.precioEspecial).toFixed(2) : '';
                            let array = [{
                                precio: parseFloat(resp.precio + "").toFixed(2)
                            },
                            {
                                precio: parseFloat(resp.precio2 + "").toFixed(2)
                            },
                            {
                                precio: parseFloat(resp.precio3 + "").toFixed(2)
                            },
                            ]
                            self.precioProductos = array
                        }
                    )
                },
                buscarDocumentSS() {
                    if (this.venta.num_doc.length == 8 || this.venta.num_doc.length == 11) {
                        $("#loader-menor").show()
                        this.venta.dir_pos = 1
                        _ajax("/ajs/consulta/doc/cliente", "POST", {
                            doc: this.venta.num_doc
                        },
                            function (resp) {
                                $("#loader-menor").hide()
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
                formatNumber(value) {
                    const number = parseFloat(value);
                    return number % 1 === 0 ? Math.floor(number) : number.toFixed(1);
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
                                    mensaje = 'Debe especificar los días de pagos para una cotización a crédito';
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
                                    mensaje = 'Debe especificar los días de pagos para una cotización a crédito';
                                }
                            }


                        }

                        if (continuar) {

                            const data = {
                                ...this.venta,
                                usar_precio: this.usar_precio,
                                listaPro: JSON.stringify(this.productos),
                                descuentoGeneral: this.descuentoGeneral,
                                asunto: this.venta.asunto
                            }
                            data.dias_lista = JSON.stringify(data.dias_lista)
                            $("#loader-menor").show();
                            _ajax("/ajs/cotizaciones/edt", "POST",
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
                        almacen: '1',
                        precio2: '',
                        precio3: '',
                        precio4: '',
                        precio_unidad: '',
                        precioVenta: '',
                        precioEspecial: '',
                        precio_mayor: '',
                        precio_menor: '',
                        tipo_precio: 'PV',
                        precio_mostrado: '',
                        precio_usado: 1,
                        index: ''
                    }
                },
                addProduct() {
                    console.log(this.producto)

                    if (this.producto.descripcion.length > 0) {
                        const prod = {
                            editable: false,
                            nombre: this.producto.nom_prod,
                            ...this.producto,
                            productoid: this.producto.productoid || this.producto.id_producto || this.producto.id_repuesto
                        }
                        if (this.producto.precioEspecial && this.producto.precioEspecial !== '') {
                            prod.precioEspecial = parseFloat(this.producto.precioEspecial).toFixed(2);
                        } else {
                            prod.precioEspecial = '';
                        }
                        if (this.producto.index !== '') {
                            let id = this.producto.index
                            this.productos[id].cantidad = this.producto.cantidad
                            this.productos[id].codigo_pp = this.producto.codigo_pp
                            this.productos[id].codigo = this.producto.codigo
                            this.productos[id].descripcion = this.producto.descripcion
                            this.productos[id].precio = this.producto.precio
                            this.productos[id].precio2 = this.producto.precio2
                            this.productos[id].precio3 = this.producto.precio3
                            this.productos[id].precioVenta = this.producto.precioVenta
                            this.productos[id].precioEspecial = this.producto.precioEspecial
                            this.productos[id].precio_usado = this.producto.precio_usado
                            this.productos[id].productoid = this.producto.productoid
                            this.productos[id].stock = this.producto.stock
                            console.log(this.productos[id]);
                        } else {
                            this.productos.push(prod)
                        }
                        this.precioProductos = []
                        this.limpiasDatos();
                    } else {
                        alertAdvertencia("Busque un producto primero")
                            .then(function () {
                                setTimeout(function () {
                                    $("#input_buscar_productos").focus();
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
                },
                seleccionarPrecioEditConTipo(tipo, precio) {
                    this.productoEdit.precio_mostrado = precio;
                    this.productoEdit.precio = precio;
                    this.productoEdit.tipo_precio = tipo;

                    if (tipo === 'PV') {
                        this.productoEdit.precioVenta = precio;
                    }
                },

                // Modificar la función cargarPreciosAdicionales para separar los precios base de los adicionales
                cargarPreciosAdicionales(idProducto, tipo = 'producto') {
                    console.log("Cargando precios para ID:", idProducto, "Tipo:", tipo);

                    // Inicializar con array vacío para precios adicionales
                    this.precioProductos = [];

                    // Si no hay ID de producto, no continuar
                    if (!idProducto) return;

                    // Determinar la URL según el tipo
                    const url = tipo === 'repuesto'
                        ? _URL + '/ajs/cargar/repuesto_precios/' + idProducto
                        : _URL + '/ajs/cargar/producto_precios/' + idProducto;

                    const self = this;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            console.log("Precios adicionales recibidos:", data);

                            if (data && data.length > 0) {
                                // Solo agregar los precios adicionales (no los base)
                                data.forEach(item => {
                                    self.precioProductos.push({
                                        nombre: item.nombre,
                                        precio: parseFloat(item.precio).toFixed(2)
                                    });
                                });
                            } else {
                                // Si no hay precios adicionales, agregar un mensaje
                                self.precioProductos.push({
                                    nombre: "No hay precios adicionales",
                                    precio: ""
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error cargando precios:", error);
                            // En caso de error, mostrar un mensaje
                            self.precioProductos.push({
                                nombre: "Error al cargar precios",
                                precio: ""
                            });
                        }
                    });
                },

                // Modificar la función cargarPreciosAdicionalesParaEdicion para separar los precios base de los adicionales
                cargarPreciosAdicionalesParaEdicion(idProducto, tipo = 'producto') {
                    console.log("Cargando precios para edición, ID:", idProducto, "Tipo:", tipo);

                    // Inicializar con array vacío para precios adicionales
                    this.precioProductosEdit = [];

                    // Si no hay ID de producto, no continuar
                    if (!idProducto) return;

                    // Determinar la URL según el tipo
                    const url = tipo === 'repuesto'
                        ? _URL + '/ajs/cargar/repuesto_precios/' + idProducto
                        : _URL + '/ajs/cargar/producto_precios/' + idProducto;

                    // Hacer la petición AJAX
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: (data) => {
                            console.log("Precios adicionales recibidos para edición:", data);

                            if (data && data.length > 0) {
                                // Solo agregar los precios adicionales (no los base)
                                data.forEach(item => {
                                    this.precioProductosEdit.push({
                                        nombre: item.nombre,
                                        precio: parseFloat(item.precio).toFixed(2)
                                    });
                                });
                            } else {
                                // Si no hay precios adicionales, agregar un mensaje
                                this.precioProductosEdit.push({
                                    nombre: "No hay precios adicionales",
                                    precio: ""
                                });
                            }
                        },
                        error: (xhr, status, error) => {
                            console.error("Error cargando precios para edición:", error);
                            // En caso de error, mostrar un mensaje
                            this.precioProductosEdit.push({
                                nombre: "Error al cargar precios",
                                precio: ""
                            });
                        }
                    });
                },
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
                totalValorListaDias() {
                    var total_ = 0;
                    this.venta.dias_lista.forEach((el) => {
                        total_ += parseFloat(el.monto + "")
                    })
                    return "S/ " + total_.toFixed(2);
                },
                isDirreccionCont() {
                    return this.venta.dir2_cli.length > 0;
                },
                totalProdustos() {
                    let total = 0;
                    this.productos.forEach((prod) => {
                        // Use precioEspecial if available, otherwise use precioVenta
                        const precio = prod.precioEspecial && parseFloat(prod.precioEspecial) > 0
                            ? parseFloat(prod.precioEspecial)
                            : parseFloat(prod.precioVenta || prod.precio || 0);

                        const cantidad = parseFloat(prod.cantidad || 0);
                        total += precio * cantidad;
                    });

                    // Apply general discount if exists
                    if (this.descuentoGeneral && this.descuentoGeneral !== '') {
                        const descuento = parseFloat(this.descuentoGeneral) / 100;
                        total = total * (1 - descuento);
                    }

                    this.venta.total = total;
                    return total.toFixed(2);
                }
            }
        });
        app.buscarSNdoc();
        app.cargarCotizacion()
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
                // Prevenir la acción por defecto
                event.preventDefault();

                // Limpiar el campo de búsqueda inmediatamente
                $(this).val("");

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
                app.producto.detalle = ui.item.detalle;
                app.producto.cantidad = '1';
                app.producto.stock = ui.item.cnt;

                // Establecer los precios
                const precioVenta = parseFloat(ui.item.precio || 0).toFixed(2);
                app.producto.precioVenta = precioVenta; // Precio de venta que se mantiene
                app.producto.precio_mostrado = precioVenta; // Precio que se muestra inicialmente
                app.producto.precio = precioVenta;
                app.producto.costo = parseFloat(ui.item.costo || 0).toFixed(2);
                app.producto.precio_mayor = parseFloat(ui.item.precio_mayor || ui.item.precio2 || 0).toFixed(2);
                app.producto.precio_menor = parseFloat(ui.item.precio_menor || ui.item.precio_unidad || 0).toFixed(2);
                app.producto.precio2 = parseFloat(ui.item.precio2 || 0).toFixed(2);
                app.producto.precio_unidad = parseFloat(ui.item.precio_unidad || 0).toFixed(2);
                app.producto.codigo = ui.item.codigo;
                app.producto.productoid = ui.item.codigo;
                app.producto.usar_multiprecio = ui.item.usar_multiprecio;
                app.producto.tipo_precio = 'PV'; // Establecer tipo inicial como PV

                // Cargar los precios adicionales
                app.cargarPreciosAdicionales(ui.item.codigo, ui.item.tipo);

                // Enfocar el campo de cantidad
                setTimeout(function () {
                    $("#cantidad-input").focus();
                }, 100);

                return false;
            }
        });

        $("#example-text-input-cnt").on('keypress', function (e) {
            if (e.which == 13) {
                $("#submit-a-product").click()
                $("#input_buscar_productos").focus()
            }
        });
    })

    // Función para cargar precios adicionales
</script>

<script>
    function toggleInput(checkbox) {
        const input = checkbox.parentElement.querySelector('.precio-input');
        input.disabled = !checkbox.checked;
    }

    function onlyNumber(event) {
        const keyCode = event.keyCode || event.which;

        if (keyCode < 48 || keyCode > 57) {
            event.preventDefault();
        }
    }
</script>
<script src="<?= URL::to('public/js/cotizaciones/condiciones-terminos.js') ?>?v=<?= time() ?>"> </script>