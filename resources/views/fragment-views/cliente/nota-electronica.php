<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Nota Electronica</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturacion</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Notas Electronicas
                    </a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438">Agregar Nota Eletronica
                </li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <button onclick="$('#submit-form-registro').click()" type="button"
                    class="btn bg-rojo text-white">Guardar</button>
                <button id="backbuttonvp" href="/nota/electronica/lista" type="button"
                    class="btn bg-white text-rojo border-rojo button-link"><i class="fa fa-arrow-left"></i>
                    Regresar</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="fecha-app" value="<?= date("Y-m-d") ?>">
<div class="row" id="container-vue">

    <div class="col-md-4">
        <!-- Tarjeta de Nota Electrónica -->
        <div class="card">
            <div class="card-header">
                Nota Electronica De Credito | Debito
            </div>
            <div class="card-body">
                <form v-on:submit.prevent="guardarNotaElectronica">
                    <input style="display: none" type="submit" name="" id="submit-form-registro">
                    <div class="text-center">
                        <!-- Tipo de documento -->
                        <div class="form-group mb-3">
                            <label class="control-label">Doc.</label>
                            <div class="col-md-12">
                                <select required @change="onChangeTiDocNE($event)" v-model="venta.tipo_docNE"
                                    class="form-control">
                                    <option value="3">NOTA DE CREDITO</option>
                                    <option value="4">NOTA DE DEBITO</option>
                                </select>
                            </div>
                        </div>

                        <!-- Serie y Número -->
                        <div class="form-group mb-3">
                            <label class="control-label">Ser | Num</label>
                            <div class="row mx-0">
                                <div class="col-6 ps-0 pe-1">
                                    <input disabled v-model="venta.serieNE" type="text"
                                        class="form-control text-center">
                                </div>
                                <div class="col-6 ps-1 pe-0">
                                    <input disabled v-model="venta.numeroNE" type="text"
                                        class="form-control text-center">
                                </div>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="form-group mb-3">
                            <label class="control-label">Motivo</label>
                            <div class="col-md-12">
                                <select required v-model="venta.motivoNE" class="form-control">
                                    <option v-for="(item,index) in listaMotivos" :value="item.id_motivo">{{item.nombre}}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tarjeta de Búsqueda de Documento -->
        <div class="card mt-3">
            <div class="card-header">
                Buscar Documento A Afectar
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="widget padding-0 white-bg">
                        <div class="padding-20 text-center">
                            <!-- Tipo de documento -->
                            <div class="form-group mb-3">
                                <label class="control-label">Doc.</label>
                                <div class="col-md-12">
                                    <select @change="onChangeTiDoc($event)" v-model="venta.tipo_doc"
                                        class="form-control">
                                        <option value="1">BOLETA DE VENTA</option>
                                        <option value="2">FACTURA</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Serie, Número y Botón de búsqueda -->
                            <div class="form-group mb-3">
                                <label class="control-label">Ser | Num</label>
                                <div class="row mx-0">
                                    <div class="col-5 ps-0 pe-1">
                                        <input v-model="venta.serie" type="text" class="form-control text-center">
                                    </div>
                                    <div class="col-5 ps-1 pe-1">
                                        <input v-model="venta.numero" type="text" class="form-control text-center">
                                    </div>
                                    <div class="col-2 ps-1 pe-0">
                                        <button @click="buscarDocVenta" type="button"
                                            class="btn btn-white text-rojo w-100"
                                            style="border-radius: 10px; padding: 8px; font-weight: 500; border: 1px solid #CA3438; transition: all 0.3s ease;"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del cliente -->
                            <div class="form-group mb-2">
                                <label class="control-label">Cliente</label>
                            </div>

                            <div class="form-group mb-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input disabled id="input_datos_cliente" v-model="venta.num_doc" type="text"
                                            placeholder="Documento" class="form-control" maxlength="11">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="col-md-12">
                                    <input disabled v-model="venta.nom_cli" type="text" placeholder="Nombre del cliente"
                                        class="form-control ui-autocomplete-input" autocomplete="off">
                                </div>
                            </div>

                            <!-- Monto -->
                            <div class="form-group mb-2">
                                <label class="control-label">Monto</label>
                            </div>
                            <div class="form-group mb-3">
                                <div class="col-md-12">
                                    <input v-model="venta.total" disabled class="text-center form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Productos -->
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Productos</h4>
                <div class="card-title-desc"></div>

                <div class="row">
                    <!-- Formulario para agregar productos -->
                    <div class="col-md-12">
                        <form v-on:submit.prevent="addProduct" class="form-horizontal">
                            <div class="form-group row mb-3">
                                <label class="col-lg-2 control-label">Descripcion</label>
                                <div class="col-lg-10">
                                    <input required v-model="producto.descripcion" type="text" placeholder="Descripcion"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="form-group row mb-3 align-items-center">
                                <div class="col-lg-2">
                                    <label class="control-label mb-0">Cantidad</label>
                                </div>
                                <div class="col-lg-2">
                                <input required v-model="producto.cantidad" @keypress="validarNumero" class="form-control text-center" type="text" placeholder="0">
                                </div>

                                <div class="col-lg-1">
                                    <label class="control-label mb-0">Precio</label>
                                </div>
                                <div class="col-lg-2">
                                <input required v-model="producto.precio" @keypress="validarNumero" class="form-control text-end" type="text" placeholder="0.00">
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn bg-rojo text-white w-100"><i
                                            class="fa fa-check"></i> Agregar</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de detalle de venta -->
                    <div class="col-md-12 mt-4">
                        <h4>Detalle Venta</h4>
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
                                    <td>{{item.cantidad}}</td>
                                    <td>{{item.precio}}</td>
                                    <td>{{(parseFloat(item.precio) * parseFloat(item.cantidad)).toFixed(2)}}</td>
                                    <td>
                                        <button @click="eliminarItemPro(index)" type="button"
                                            class="btn btn-danger btn-xs">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-center">{{totalProdustos}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<textarea id="jsom-motivos" style="display: none"><?php
$listaMotivos = (new Conexion())->getConexion()->query("select * from motivo_documento");
$temp = [];
foreach ($listaMotivos as $motivo) {
    $temp[] = $motivo;
}
echo json_encode($temp);
?></textarea>

<script>
    $(document).ready(function () {
        const app = new Vue({
            el: "#container-vue",
            data: {
                producto: {
                    productoid: "",
                    descripcion: "",
                    cantidad: "",
                    precio: "",
                    codigo: "",
                    costo: "",
                },
                motivos: [],
                productos: [],
                venta: {
                    ventacod: '',
                    tipo_doc: '1',
                    serie: '',
                    numero: '',
                    fecha: $("#fecha-app").val(),
                    sendwp: false,
                    numwp: "",
                    num_doc: "",
                    nom_cli: "",
                    dir_cli: "",
                    tipoventa: 2,
                    total: 0,

                    motivoNE: '',
                    serieNE: '',
                    numeroNE: '',
                    tipo_docNE: '',
                    total_NE: 0
                }
            },
            methods: {

                validarNumero(event) {
                    // Permitir solo números y punto decimal
                    const charCode = (event.which) ? event.which : event.keyCode;
                    if (
                        (charCode > 31 && (charCode < 48 || charCode > 57)) &&
                        charCode !== 46
                    ) {
                        event.preventDefault();
                    }

                    // Evitar múltiples puntos decimales
                    if (charCode === 46 && event.target.value.includes('.')) {
                        event.preventDefault();
                    }
                },
                guardarNotaElectronica() {
                    if (this.venta.ventacod != "") {
                        if (this.productos.length > 0) {
                            const data = {
                                ...this.venta,
                                listaPro: JSON.stringify(this.productos)
                            }
                            $("#loader-menor").show();
                            _ajax("/ajs/nota/electronica/add", "POST",
                                data,
                                function (resp) {
                                    console.log(resp);
                                    if (resp.res) {
                                        alertExito("Exito", "Venta Guardada")
                                            .then(function () {
                                                location.href = _URL + "/nota/electronica/lista"
                                            })
                                    } else {
                                        alertAdvertencia("No se pudo Guardar la Venta")
                                    }
                                }
                            )
                        } else {
                            alertAdvertencia("Ho hay itens agregado a la lista")
                        }
                    } else {
                        alertAdvertencia("No a buscado un documento de venta")
                    }
                },
                buscarDocVenta() {
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/venta/info", "POST",
                        { tidoc: this.venta.tipo_doc, serie: this.venta.serie, numero: this.venta.numero },
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                app._data.venta.ventacod = resp.data.id_venta
                                app._data.venta.num_doc = resp.data.documento
                                app._data.venta.nom_cli = resp.data.datos
                                app._data.venta.total = resp.data.total
                                alertExito("Documento de venta encontrado")
                            } else {
                                alertAdvertencia("Documento no encontrado")
                            }
                        }
                    )
                },
                eliminarItemPro(index) {
                    this.productos.splice(index, 1)
                },
                buscarDocumentSS() {
                    if (this.venta.num_doc.length == 8 || this.venta.num_doc.length == 11) {
                        $("#loader-menor").show()
                        _ajax("/ajs/consulta/doc/cliente", "POST",
                            { doc: this.venta.num_doc },
                            function (resp) {
                                $("#loader-menor").hide()
                                console.log(resp);
                                if (resp.res) {
                                    app._data.venta.nom_cli = (resp.data.nombre ? resp.data.nombre : '') + (resp.data.razon_social ? resp.data.razon_social : '')
                                    app._data.venta.dir_cli = resp.data.direccion
                                } else {
                                    alertAdvertencia("Documento no enocntrado")
                                }
                            }
                        )
                    } else {
                        alertAdvertencia("Documento, DNI es 8 digitos y RUC 11 digitos")
                    }
                },
                guardarVenta() {
                    const data = {
                        ...this.venta,
                        listaPro: JSON.stringify(this.productos)
                    }
                    $("#loader-menor").show();
                    _ajax("/ajs/ventas/add", "POST",
                        data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                alertExito("Exito", "Venta Guardada")
                                    .then(function () {
                                        $("#backbuttonvp").click();
                                    })
                            } else {
                                alertAdvertencia("No se pudo Guardar la Venta")
                            }
                        }
                    )
                },
                onChangeTiDocNE(event) {
                    this.buscarSNdoc()
                    /*this.venta.serieNE=''
                    this.venta.numeroNE=''*/
                },
                buscarSNdoc() {
                    _ajax("/ajs/consulta/sn", "POST",
                        { doc: this.venta.tipo_docNE },
                        function (resp) {
                            app.venta.serieNE = resp.serie
                            app.venta.numeroNE = resp.numero
                        }
                    )
                },
                onChangeTiDoc(event) {
                    this.venta.serie = ''
                    this.venta.numero = ''
                },
                limpiasDatos() {
                    this.producto = {
                        productoid: "",
                        descripcion: "",
                        cantidad: "",
                        precio: "",
                        codigo: "",
                        costo: "",
                    }
                },
                addProduct() {
                    // Validar y convertir valores a números
                    const cantidad = parseFloat(this.producto.cantidad) || 0;
                    const precio = parseFloat(this.producto.precio) || 0;

                    const prod = {
                        productoid: this.producto.productoid,
                        descripcion: this.producto.descripcion,
                        cantidad: cantidad,
                        precio: precio,
                        codigo: this.producto.codigo,
                        costo: this.producto.costo,
                    }

                    this.productos.push(prod)
                    this.limpiasDatos();
                }
            },
            computed: {
                listaMotivos() {
                    var temp = [];
                    this.motivos.forEach(function (el) {
                        if (el.id_tido == app._data.venta.tipo_docNE) {
                            temp.push(el)
                        }
                    })
                    return temp;
                },
                totalProdustos() {
                    var total = 0;
                    this.productos.forEach(function (prod) {
                        const cantidad = parseFloat(prod.cantidad) || 0;
                        const precio = parseFloat(prod.precio) || 0;
                        total += precio * cantidad;
                    })
                    this.venta.total_NE = total;
                    return total.toFixed(2);
                }
            },
            mounted() {
                this.motivos = JSON.parse($("#jsom-motivos").val());
            }
        });

        //app.buscarSNdoc();

        $("#input_datos_cliente").autocomplete({
            source: _URL + "/ajs/buscar/cliente/datos",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui.item);
                app._data.venta.nom_cli = ui.item.datos
                app._data.venta.num_doc = ui.item.documento
                app._data.venta.dir_cli = ui.item.direccion
                /*$('#input_datos_cliente').val(ui.item.datos);
                $('#input_documento_cliente').val(ui.item.documento);
                $('#input_datos_cliente').focus();*/
            }
        });
    })
</script>