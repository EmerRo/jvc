<!-- plantilla de taller-cotizaciones.php pero sera editar-cotizacion-taller.php-->
<?php
// Verificar la sesión del usuario
if (!isset($_SESSION)) {
    session_start();
}
?>

<link rel="stylesheet" href="<?= URL::to('public/css/taller/styles.css') ?>">
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>
<!-- Incluir Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Incluir Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<style>
    .nav-equipos {
        margin-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 0;
    }

    .nav-equipos .nav-link {
        padding: 8px 16px;
        margin-right: 4px;
        border: 1px solid transparent;
        border-radius: 4px 4px 0 0;
        color: #6c757d;
        cursor: pointer;
    }

    .nav-equipos .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .preview-container img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 4px;
    }
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Editar Cotización</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Cotización</a></li>
                <li class="breadcrumb-item active" aria-current="page">Productos</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
            </div>
        </div>
    </div>
</div>

<input type="hidden" value="<?= date("Y-m-d") ?>" id="fecha-app">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"></h4>
                <div class="card-title-desc">
                    <div class="col-lg-12 text-end">

                        <button type="button" onclick="$('#btn_finalizar_pedido').click()" class="btn bg-rojo text-white">
                            <i class="fa fa-plus"></i>Actualizar Cotización
                        </button>
                        <button  style="margin-left:25px;" class="btn btn-warning"
                            onclick="window.location.href='/taller/coti/view'">
                            <i class="fa fa-arrow-left"></i> Regresar
                        </button>
                    </div>
                </div>

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
                                                <div class="form-group row mb-3">
                                                    <label class="col-lg-2 control-label">Buscar</label>
                                                    <div class="col-lg-10">
                                                        <div class="input-group">
                                                            <input type="text" placeholder="Consultar Productos"
                                                                class="form-control ui-autocomplete-input"
                                                                id="input_buscar_productos" autocomplete="off">
                                                            <div class="input-group-btn p-1">
                                                                <br />
                                                                <label @click="abrirMultipleBusaque"
                                                                    style="color: blue;cursor: pointer">
                                                                    Búsqueda Multiple
                                                                </label>
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
                                                            <div class="col-lg-3">
                                                                <label class="col-form-label">Precio</label>
                                                                <select class="form-control" v-model="producto.precio">
                                                                    <option v-for="(value, key) in precioProductos"
                                                                        :value="value.precio" :key="key">
                                                                        {{ value.precio }}
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Campo de Descuento -->
                                                            <div class="col-lg-6 d-flex align-items-center mt-3">
                                                                <label for="descuento-general"
                                                                    class="col-form-label mb-0 me-2">
                                                                    Descuento %
                                                                </label>
                                                                <input type="checkbox" class="toggle-checkbox"
                                                                    onclick="toggleInput(this)"
                                                                    style="margin-right: 10px;" />
                                                                <input id="descuento-general"
                                                                    class="form-control text-center precio-input"
                                                                    @keypress="onlyNumber" v-model="descuentoGeneral"
                                                                    type="text" placeholder="0" disabled
                                                                    style="max-width: 80px;">
                                                            </div>
                                                        </div>

                                                        <!-- Botón Agregar -->
                                                        <div class="row mt-3">
                                                            <div class="col-lg-2 d-flex align-items-center">
                                                                <button id="submit-a-product" type="submit"
                                                                    class="btn border-rojo w-100 d-flex align-items-center justify-content-center"
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
                                                        <th>P. Unit.</th>
                                                        <th>Parcial</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in productosDelEquipoActual">
                                                        <td>{{index+1}}</td>
                                                        <td>{{item.codigo_prod}}</td>
                                                        <td>{{item.descripcion}}</td>
                                                        <td>
                                                            <span v-if="!item.editable">{{item.cantidad}}</span>
                                                            <input v-if="item.editable" v-model="item.cantidad">
                                                        </td>
                                                        <td>
                                                            <span v-if="!item.editable">{{item.precioVenta}}</span>
                                                            <input v-if="item.editable" v-model="item.precioVenta">
                                                        </td>
                                                        <td>{{item.precioVenta*item.cantidad}}</td>
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
                                                            <button style="display: none" @click="actualizarCotizacion"
                                                                type="button" class="btn btn-lg bg-rojo text-white"
                                                                id="btn_finalizar_pedido">
                                                                <i class="fa fa-save"></i> Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Botones de acción -->
                                            <button class="btn border-rojo" id="edit-condiciones"
                                                style="margin-bottom: 5px;">
                                                Modificar Términos
                                            </button>

                                            <button class="btn border-rojo" id="edit-diagnostico"
                                                style="margin-bottom: 5px;">
                                                Diagnóstico
                                            </button>

                                            <button class="btn border-rojo btn-foto" style="margin-bottom: 5px;">
                                                Foto
                                            </button>

                                            <br>

                                            <!-- Total -->
                                            <div class="bg-rojo text-white pv-15 text-center p-3"
                                                style="height: 90px; color: white">
                                                <h1 class="mv-0 font-400" id="lbl_suma_pedido">
                                                    {{monedaSibol}}
                                                    {{(totalProdustos/(venta.tc||1)).toFixed(2)}}
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
                                            ha sido Actualizada correctamente.</p>
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
                                    </div>

                                    <!-- Vista previa del PDF -->
                                    <div class="border rounded">
                                        <iframe id="pdf-preview" src=""
                                            style="width: 100%; height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between border-top pt-3">
                                    <a href="/taller/coti/view" class="btn btn-success">
                                        <i class="fas fa-list me-2"></i> LISTA DE COTIZACIONES
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de días de pago -->
                    <div class="modal fade" id="modal-dias-pagos" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Días de Pagos</h3>
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
                                        <div class="form-text">
                                            Separar por comas los días de pagos
                                        </div>
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
                                                        <td>{{index + 1}}</td>
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
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                        Cerrar
                                    </button>
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
                <!-- Agregar este campo oculto justo después de la apertura del div container-vue -->
                <input type="hidden" id="cotizacion-id" name="cotizacion-id" v-model="venta.id_cotizacion">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn bg-rojo text-white" id="btnGuardarFotos">
                    Guardar Fotos
                </button>
            </div>
        </div>
    </div>
</div>


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
                    condiciones: '',
                    diagnostico: ''
                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                pointSel: 1,
                equiposPreAlerta: [],
                equipoActivo: null,
            },
            mounted() {
                console.log("Componente montado");
                this.buscarSNdoc();

            },
            watch: {
                'venta.dias_pago'(newValue) {
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
                productos: {
                    deep: true,
                    handler(newValue) {
                        // Recalcular totales cuando cambian los productos
                        this.calcularTotales();
                    }
                }
            },
            methods: {
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
                onChangeAlmacen(event) {
                    console.log(event.target.value)
                    this.producto.almacen = event.target.value
                    var self = this
                    $("#input_buscar_productos").autocomplete({
                        source: _URL + `/ajs/cargar/repuestos/${self.producto.almacen}`,
                        minLength: 1,
                        select: function (event, ui) {
                            event.preventDefault();
                            console.log(ui.item);
                            app.producto.productoid = ui.item.codigo
                            app.producto.codigo_pp = ui.item.codigo_pp
                            app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre
                            app.producto.nom_prod = ui.item.nombre
                            app.producto.cantidad = ''
                            app.producto.stock = ui.item.cnt
                            app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2)
                            app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2)
                            app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_unidad + "").toFixed(2)

                            app.producto.precioVenta = parseFloat(ui.item.precio + "").toFixed(2)
                            app.producto.codigo = ui.item.codigo
                            app.producto.codigo_prod = ui.item.codigo_pp
                            app.producto.costo = ui.item.costo
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
                            console.log(array);
                            $('#input_buscar_productos').val("");
                            $("#example-text-input").focus()
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
                    const productoAEliminar = this.productosDelEquipoActual[index];
                    this.productos = this.productos.filter(p => p !== productoAEliminar);
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
                                    $("#loader-menor").hide()
                                    try {
                                        const response = typeof resp === "string" ? JSON.parse(resp) : resp
                                        if (response.res === true) {
                                            Swal.fire({
                                                icon: "success",
                                                title: "Éxito",
                                                text: "Cotización guardada correctamente",
                                                showConfirmButton: false,
                                                timer: 1500,
                                            }).then(() => {
                                                // $("#backbuttonvp").click()
                                            })
                                        } else {
                                            throw new Error(response.error || "Error al guardar la cotización")
                                        }
                                    } catch (error) {
                                        console.error("Error completo:", resp)
                                        Swal.fire({
                                            icon: "error",
                                            title: "Error",
                                            text: "Error al procesar la respuesta del servidor",
                                        })
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

                        const equipoActual = this.equiposPreAlerta[this.equipoActivo];

                        // Crear nuevo producto con la referencia al equipo
                        const nuevoProducto = {
                            ...this.producto,
                            id_cotizacion_equipo: equipoActual.id_cotizacion_equipo,
                            editable: false
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
                    // Guardar el equipo activo anterior
                    const equipoAnterior = this.equipoActivo;

                    // Actualizar el equipo activo
                    this.equipoActivo = index;

                    // No es necesario filtrar los productos aquí, ya que se manejan a través de productosDelEquipoActual
                    console.log(`Cambiado a equipo ${index + 1}`);
                    this.logProductosPorEquipo();
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
                cargarDatosCotizacion(id) {
                    console.log("Solicitando datos de cotización:", id);
                    _ajax("/ajs/taller/cotizacion/detalle", "POST", { id }, (resp) => {
                        console.log("Respuesta recibida:", resp);
                        if (resp.res) {
                            const data = resp.data;

                            // Cargar datos principales
                            this.venta = {
                                ...this.venta,
                                id_cotizacion: data.id_cotizacion,
                                tipo_doc: data.tipo_doc,
                                moneda: data.moneda,
                                tc: data.tc,
                                tipo_pago: data.tipo_pago,
                                fecha: data.fecha,
                                dias_pago: data.dias_pago,
                                dir_pos: data.dir_pos,
                                num_doc: data.num_doc,
                                nom_cli: data.nom_cli,
                                dir_cli: data.dir_cli,
                                dir2_cli: data.dir2_cli,
                                total: data.total
                            };

                            // Guardar condiciones y diagnóstico en sessionStorage si existen
                            if (data.condiciones) {
                                sessionStorage.setItem('temp_condiciones_taller', data.condiciones);
                                console.log("Condiciones cargadas en sessionStorage:", data.condiciones);
                            }

                            if (data.diagnostico) {
                                sessionStorage.setItem('temp_diagnostico_taller', data.diagnostico);
                                console.log("Diagnóstico cargado en sessionStorage:", data.diagnostico);
                            }


                            // Cargar equipos
                            this.equiposPreAlerta = data.equipos;

                            // Cargar todos los productos
                            this.productos = data.productos;

                            // Activar primer equipo
                            if (this.equiposPreAlerta.length > 0) {
                                this.equipoActivo = 0;
                            }

                            // Cargar fotos si existen
                            if (data.fotos && data.fotos.length > 0) {
                                // Llamar a la función global que definimos en manejo-fotos.js
                                window.cargarFotosIniciales(data.fotos);
                            }

                            console.log("Datos cargados:", {
                                venta: this.venta,
                                equipos: this.equiposPreAlerta,
                                productos: this.productos,
                                fotos: data.fotos,
                                condiciones: data.condiciones,
                                diagnostico: data.diagnostico
                            });
                        }
                    });
                },
                calcularTotales() {
                    let total = 0;
                    this.productos.forEach(producto => {
                        if (producto.id_cotizacion_equipo === this.equiposPreAlerta[this.equipoActivo].id_cotizacion_equipo) {
                            total += producto.precioVenta * producto.cantidad;
                        }
                    });
                    this.venta.total = total;
                },
                logProductosPorEquipo() {
                    this.equiposPreAlerta.forEach((equipo, index) => {
                        const productosFiltrados = this.productos.filter(p => p.id_cotizacion_equipo === equipo.id_cotizacion_equipo);
                        console.log(`Equipo ${index + 1} (ID: ${equipo.id_cotizacion_equipo}):`, productosFiltrados);
                    });
                },
                actualizarCotizacion() {
                    if (this.productos.length > 0) {
                        const formData = new FormData();

                        // Agregar el ID de la cotización
                        formData.append('id_cotizacion', this.venta.id_cotizacion);

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
                        // Agregar condiciones y diagnóstico al formData
                        if (this.venta.condiciones) {
                            formData.append("condiciones", this.venta.condiciones)
                        }
                        if (this.venta.diagnostico) {
                            formData.append("diagnostico", this.venta.diagnostico)
                        }
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
                            url: _URL + "/ajs/taller/cotizaciones/edt",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: (resp) => {
                                $("#loader-menor").hide();
                                try {
                                    // Asegurarse de que resp sea un objeto
                                    const response = typeof resp === 'string' ? JSON.parse(resp) : resp;

                                    if (response.res === true && response.cotizacion) {
                                        // Actualizar el modal con los datos de la cotización
                                        $('#cotizacion-numero').text(response.cotizacion.numero);

                                        // Asegurarse de que la URL del PDF sea correcta
                                        const pdfUrl = response.cotizacion.pdfUrl;
                                        $('#pdf-preview').attr('src', pdfUrl);
                                        $('#btn-a4').attr('href', pdfUrl);

                                        // Configurar el botón de WhatsApp
                                        const whatsappUrl = `https://wa.me/?text=Cotización N° ${response.cotizacion.numero}%0A${encodeURIComponent(pdfUrl)}`;
                                        $('#btn-whatsapp').attr('href', whatsappUrl);

                                        // Mostrar directamente el modal con el PDF
                                        $('#modal-cotizacion-success').modal('show');
                                    } else {
                                        throw new Error(response.error || "Error al actualizar la cotización");
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
                                    response: xhr.responseText
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error al actualizar la cotización'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'No hay productos agregados a la lista'
                        });
                    }
                },
            },
            computed: {
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
                },
                productosDelEquipoActual() {
                    if (this.equipoActivo === null || !this.equiposPreAlerta[this.equipoActivo]) {
                        return [];
                    }
                    const equipoActual = this.equiposPreAlerta[this.equipoActivo];
                    return this.productos.filter(producto =>
                        producto.id_cotizacion_equipo === equipoActual.id_cotizacion_equipo
                    );
                }
            },
            created() {
                const urlParams = new URLSearchParams(window.location.search);
                const cotizacionId = urlParams.get('id');

                if (cotizacionId) {
                    console.log("Cargando cotización:", cotizacionId);
                    this.cargarDatosCotizacion(cotizacionId);
                } else {
                    this.buscarSNdoc();

                }
            }
        });

        // Hacer la instancia accesible globalmente
        window.app = vueApp;

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
<script src="<?= URL::to('public/js/taller-cotizaciones/cargar-repuestos.js') ?>?v=<?= time() ?>"></script>
<!-- Manejo de fotos -->
<script src="<?= URL::to('public/js/taller-cotizaciones/manejo-fotos.js') ?>?v=<?= time() ?>"></script>

<script src="<?= URL::to('public/js/taller-cotizaciones/navegacion.js') ?>?v=<?= time() ?>"></script>
<!-- fin de taller-cotizaciones.php -->
<script>
    // Agregar este código después de la inicialización de Vue, donde están los otros event listeners

    $(document).ready(function () {
        // Event listener para el modal de cotización
        $('#modal-cotizacion-success').on('hidden.bs.modal', function () {
            // Redireccionar a la vista de cotizaciones cuando se cierra el modal
            window.location.href = '/taller/coti/view';
        });
    });
</script>