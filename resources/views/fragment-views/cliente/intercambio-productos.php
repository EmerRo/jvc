<!-- resources\views\fragment-views\cliente\intercambio-productos.php -->
<?php
require_once "app/http/controllers/VentasController.php";
$c_venta = new VentasController();
$getAll = $c_venta->ingresosEgresosRender();
?>

<style>
    .ui-autocomplete {
        z-index: 1065;
    }
    #datatable td:nth-child(2),
    #datatable th:nth-child(2) {
        white-space: normal;
        word-wrap: break-word;
        text-align: left;
    }

    .tipo-item-select {
        border: 1.5px solid #CA3438 !important;
        color: #CA3438;
        font-weight: 600;
        border-radius: 8px;
    }
    .tipo-item-select:focus {
        border-color: #CA3438 !important;
        box-shadow: 0 0 0 0.2rem rgba(202, 52, 56, 0.2) !important;
        outline: none;
    }
</style>

<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title text-center">INTERCAMBIO DE PRODUCTOS</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Almacen</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link" style="color: #CA3438;">Intercambio
                        productos</a></li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body" id="container-vue">

                <div class="card-title-desc text-end">
                    <button data-bs-toggle="modal" data-bs-target="#nuevaSalida" class="btn bg-white text-rojo"
                        @click="btnCerrar"
                        style="border-radius: 10px; border: 1px solid #CA3438; margin-right: 8px;">
                        <i class="fa fa-plus"></i> Nueva Salida
                    </button>
                    <button data-bs-toggle="modal" data-bs-target="#nuevoIngreso" class="btn bg-rojo text-white"
                        @click="btnCerrar" style="border-radius: 10px;">
                        <i class="fa fa-plus"></i> Nuevo Ingreso
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-bordered dt-responsive nowrap text-center table-sm"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ítem</th>
                                <th>Tipo Ítem</th>
                                <th>Cantidad</th>
                                <th>Movimiento</th>
                                <th>Usuario</th>
                                <th>Egreso</th>
                                <th>Ingreso</th>
                                <th>Fecha Creación</th>
                                <th>Fecha Actualización</th>
                                <th>Confirmar <br> Traslado</th>
                                <th>Reporte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($getAll as $row): ?>
                                <tr>
                                    <td><?php echo $row['intercambio_id'] ?></td>
                                    <td class="text-start"><?php echo htmlspecialchars($row['codigo'] ?? '') ?> | <?php echo htmlspecialchars($row['nombre'] ?? '') ?></td>
                                    <td><?php echo ($row['tipo_item'] ?? 'producto') === 'repuesto' ? '<span class="badge bg-warning text-dark">Repuesto</span>' : '<span class="badge bg-info text-white">Producto</span>' ?></td>
                                    <td><?php echo $row['cantidad'] ?></td>
                                    <?php $mov = ($row['tipo'] == 'i') ? 'Ingreso' : 'Salida'; ?>
                                    <td><?php echo $mov ?></td>
                                    <td><?php echo htmlspecialchars($row["nombres"] ?? '') ?></td>
                                    <td><?php echo htmlspecialchars($row['almacen_egreso_nombre'] ?? 'N/A') ?></td>
                                    <td><?php echo htmlspecialchars($row['almacen_ingreso_nombre'] ?? 'N/A') ?></td>
                                    <td><?php echo $row['fecha_creacion_formatted'] ?? 'N/A' ?></td>
                                    <td><?php echo $row['fecha_actualizacion_formatted'] ?? 'N/A' ?></td>
                                    <td class="text-center">
                                        <?php if ($row['tipo'] == 'e' && $row['estado'] == '0'): ?>
                                            <button
                                                data-item="<?= $row['intercambio_id'] ?>"
                                                data-tipo-item="<?= htmlspecialchars($row['tipo_item'] ?? 'producto') ?>"
                                                class="btn-confirmar btn btn-sm btn-success">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                            href="<?= URL::to('reporte/ingresos/egresos/' . $row['intercambio_id']) ?>"
                                            class="btn-reporte btn btn-sm btn-primary">
                                            <i class="fa fa-file"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Nuevo Ingreso -->
                <div class="modal fade" id="nuevoIngreso" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">Nuevo Ingreso</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form v-on:submit.prevent="addIngreso" class="form-horizontal">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label fw-semibold">Tipo de ítem</label>
                                            <select class="form-select form-select-sm tipo-item-select"
                                                v-model="tipoItem" @change="cambiarTipo(tipoItem)">
                                                <option value="producto">📦 Producto</option>
                                                <option value="repuesto">🔧 Repuesto</option>
                                            </select>
                                        </div>
                                        <!-- 1. Almacén destino -->
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label fw-semibold">Almacén de ingreso</label>
                                            <select name="almacen" id="almacen" v-model="producto.almacen"
                                                class="form-control" @change="onChangeAlmacenIngreso($event)">
                                                <option value="" disabled>Seleccionar almacén</option>
                                                <option v-for="alm in almacenes" :key="alm.id_almacen" :value="parseInt(alm.id_almacen)">
                                                    {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- 2. Buscar ítem (habilitado solo si hay almacén) -->
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label fw-semibold">
                                                {{ tipoItem === 'repuesto' ? 'Repuesto' : 'Producto' }}
                                            </label>
                                            <input type="text"
                                                :placeholder="producto.almacen ? (tipoItem === 'repuesto' ? 'Buscar repuesto...' : 'Buscar producto...') : 'Seleccioná un almacén primero'"
                                                class="form-control ui-autocomplete-input"
                                                id="input_buscar_productos"
                                                :disabled="!producto.almacen"
                                                autocomplete="off">
                                        </div>

                                        <!-- 3. Nombre + Stock -->
                                        <div class="mb-3 col-md-8">
                                            <label class="control-label">Nombre</label>
                                            <input v-model="producto.nombre" type="text"
                                                class="form-control bg-light" readonly>
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <label class="control-label">Stock actual</label>
                                            <input v-model="producto.stock" type="text"
                                                class="form-control bg-light text-center fw-bold" readonly>
                                        </div>

                                        <!-- 4. Cantidad -->
                                        <div class="mb-3 col-md-4">
                                            <label class="control-label fw-semibold">Cantidad a ingresar</label>
                                            <input required v-model="producto.cantidad" type="text"
                                                class="form-control" placeholder="0" @keypress="onlyNumber"
                                                :disabled="!producto.productoid">
                                        </div>

                                        <!-- 5. Observaciones -->
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label">Observaciones</label>
                                            <textarea v-model="producto.observaciones" class="form-control" rows="2"
                                                placeholder="Ingrese observaciones (opcional)"></textarea>
                                        </div>

                                        <canvas hidden="" id="qr-canvas2" v-show="toggleCamara2"
                                            style="width: 300px; padding: 10px;"></canvas>
                                        <div class="col-md-12">
                                            <label>
                                                <input id="btn-scan-qr2" v-model="usar_scaner2" @click="toggleCamara2"
                                                    type="checkbox"> Usar Scanner QR
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo text-rojo" @click="btnCerrar"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn bg-rojo text-white">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Nueva Salida -->
                <div class="modal fade" id="nuevaSalida" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">Nueva Salida</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form v-on:submit.prevent="addSalida" class="form-horizontal">
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <canvas hidden="" id="qr-canvas" v-show="toggleCamara"
                                            style="width: 300px; padding: 10px;"></canvas>

                                        <!-- Scanner -->
                                        <div class="col-md-12">
                                            <label>
                                                <input id="btn-scan-qr" v-model="usar_scaner" @click="toggleCamara"
                                                    type="checkbox">
                                                Usar Scanner
                                            </label>
                                        </div>

                                        <!-- Búsqueda de producto -->
                                        <div class="col-md-12">
                                            <label class="control-label fw-semibold">Tipo de ítem</label>
                                            <select class="form-select form-select-sm tipo-item-select"
                                                v-model="tipoItem" @change="cambiarTipo(tipoItem)">
                                                <option value="producto">📦 Producto</option>
                                                <option value="repuesto">🔧 Repuesto</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">{{ tipoItem === 'repuesto' ? 'Repuesto' : 'Producto' }}</label>
                                            <input type="text" placeholder="Buscar por código o nombre..."
                                                class="form-control ui-autocomplete-input"
                                                id="input_buscar_productos_salida" autocomplete="off">
                                        </div>

                                        <!-- Nombre (readonly) -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Nombre</label>
                                            <input required v-model="producto.nombre" type="text"
                                                placeholder="Se completa al buscar el producto"
                                                class="form-control bg-light" readonly>
                                        </div>

                                        <!-- Del Almacén | Stock -->
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Del Almacén <small class="text-muted">(origen)</small></label>
                                            <select name="delAlmacen" id="delAlmacen" v-model="producto.almacen"
                                                class="form-control" @change="onChangeAlmacen($event)">
                                                <option value="" disabled selected>Seleccionar</option>
                                                <option v-for="alm in almacenes" :key="alm.id_almacen" :value="parseInt(alm.id_almacen)">
                                                    {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Stock Actual</label>
                                            <input v-model="producto.stock" type="text"
                                                class="form-control bg-light text-center fw-bold" readonly>
                                        </div>

                                        <!-- Al Almacén -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Al Almacén <small class="text-muted">(destino)</small></label>
                                            <select v-model="producto.alAlmacen" class="form-control">
                                                <option value="" disabled selected>Seleccionar</option>
                                                <option v-for="alm in almacenes.filter(a => parseInt(a.id_almacen) !== producto.almacen)"
                                                        :key="alm.id_almacen" :value="parseInt(alm.id_almacen)">
                                                    {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Cantidad -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Cantidad</label>
                                            <input required v-model="producto.cantidad" type="text"
                                                class="form-control" placeholder="0" @keypress="onlyNumber">
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Observaciones</label>
                                            <textarea v-model="producto.observaciones" class="form-control" rows="2"
                                                placeholder="Ingrese observaciones (opcional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo text-rojo" @click="btnCerrar"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button id="btnguardarSalida" type="submit"
                                        class="btn bg-rojo text-white">Guardar</button>
                                </div>
                            </form>
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
                usar_scaner: false,
                usar_scaner2: false,
                tipoItem: 'producto',
                almacenes: [],
                producto: {
                    productoid: "",
                    cantidad: "",
                    stock: "0",
                    codigo: "",
                    almacen: "",
                    alAlmacen: "",
                    tipo: '',
                    nombre: '',
                    observaciones: ''
                },
            },
            mounted() {
                this.cargarAlmacenes();
            },
            methods: {
                cambiarTipo(tipo) {
                    this.tipoItem = tipo;
                    this.resetProducto();
                    this._actualizarAutocompleteIngreso();
                },
                onChangeAlmacenIngreso(event) {
                    const almacen = parseInt(event.target.value);
                    this.producto.almacen = almacen;
                    this.resetProducto(almacen);
                    this._actualizarAutocompleteIngreso(almacen);
                },
                _actualizarAutocompleteIngreso(almacen) {
                    const alm = almacen || this.producto.almacen;
                    const base = this.tipoItem === 'repuesto' ? '/ajs/cargar/repuestos' : '/ajs/cargar/productos';
                    const url  = alm ? (_URL + base + '/' + alm) : (_URL + base);
                    $("#input_buscar_productos").autocomplete('option', 'source', url);
                },
                resetProducto(mantenerAlmacen) {
                    const principal = this.almacenes.find(a => a.principal == 1);
                    const defaultAlm = principal || (this.almacenes.length > 0 ? this.almacenes[0] : null);
                    const defaultId  = defaultAlm ? parseInt(defaultAlm.id_almacen) : '';
                    this.producto = {
                        productoid:    "",
                        nombre:        "",
                        cantidad:      "",
                        stock:         "0",
                        codigo:        "",
                        almacen:       mantenerAlmacen || defaultId,
                        alAlmacen:     "",
                        observaciones: ""
                    };
                    $("#input_buscar_productos").val("");
                },
                urlIngreso()  { return this.tipoItem === 'repuesto' ? '/ajs/ingreso/almacen/repuesto/add' : '/ajs/ingreso/almacen/add'; },
                urlEgreso()   { return this.tipoItem === 'repuesto' ? '/ajs/egreso/almacen/repuesto/add'  : '/ajs/egreso/almacen/add'; },
                urlStock()    { return this.tipoItem === 'repuesto' ? '/ajs/consulta/stock/almacen/repuesto' : '/ajs/consulta/stock/almacen'; },
                cargarAlmacenes() {
                    _get('/ajs/almacenes/listar', (res) => {
                        if (res.estado) {
                            this.almacenes = res.almacenes;
                            const principal  = res.almacenes.find(a => a.principal == 1);
                            const defaultAlm = principal || (res.almacenes.length > 0 ? res.almacenes[0] : null);
                            if (defaultAlm) {
                                this.producto.almacen = parseInt(defaultAlm.id_almacen);
                                const other = res.almacenes.find(a => parseInt(a.id_almacen) !== this.producto.almacen);
                                if (other) this.producto.alAlmacen = parseInt(other.id_almacen);
                                this.$nextTick(() => this._actualizarAutocompleteIngreso(this.producto.almacen));
                            }
                        }
                    });
                },
                toggleCamara() {
                    if (!this.usar_scaner) {
                        this.encenderCamara();
                    } else {
                        this.cerrarCamara();
                    }
                },
                toggleCamara2() {
                    if (!this.usar_scaner2) {
                        this.encenderCamara2();
                    } else {
                        this.cerrarCamara2();
                    }
                },
                encenderCamara() {
                    navigator.mediaDevices
                        .getUserMedia({ video: { facingMode: "environment" } })
                        .then((stream) => {
                            this.scanning = true;
                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr");

                            btnScanQR.checked = true;
                            video.setAttribute("playsinline", true);
                            video.srcObject = stream;
                            video.play();

                            const tick = () => {
                                if (canvasElement.hidden) return;
                                canvasElement.height = video.videoHeight;
                                canvasElement.width = video.videoWidth;
                                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                                this.scanning && requestAnimationFrame(tick);
                            };

                            const scan = () => {
                                try { qrcode.decode(); } catch (e) { setTimeout(scan, 500); }
                            };

                            video.addEventListener("loadeddata", () => {
                                canvasElement.hidden = false;
                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => { this.procesarCodigoQR(respuesta); };
                        });
                },
                encenderCamara2() {
                    navigator.mediaDevices
                        .getUserMedia({ video: { facingMode: "environment" } })
                        .then((stream) => {
                            this.scanning = true;
                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas2");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr2");

                            btnScanQR.checked = true;
                            video.setAttribute("playsinline", true);
                            video.srcObject = stream;
                            video.play();

                            const tick = () => {
                                if (canvasElement.hidden) return;
                                canvasElement.height = video.videoHeight;
                                canvasElement.width = video.videoWidth;
                                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                                this.scanning && requestAnimationFrame(tick);
                            };

                            const scan = () => {
                                try { qrcode.decode(); } catch (e) { setTimeout(scan, 500); }
                            };

                            video.addEventListener("loadeddata", () => {
                                canvasElement.hidden = false;
                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => { this.procesarCodigoQR(respuesta); };
                        });
                },
                cerrarCamara() {
                    this.usar_scaner = false;
                    const video = document.querySelector("video");
                    const canvasElement = document.getElementById("qr-canvas");
                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                    }
                    document.getElementById("btn-scan-qr").checked = false;
                    canvasElement.hidden = true;
                },
                cerrarCamara2() {
                    this.usar_scaner2 = false;
                    const video = document.querySelector("video");
                    const canvasElement = document.getElementById("qr-canvas2");
                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                    }
                    document.getElementById("btn-scan-qr2").checked = false;
                    canvasElement.hidden = true;
                },
                procesarCodigoQR(codigo) {
                    $.ajax({
                        type: "post",
                        url: _URL + '/ajs/compra/buscar/producto',
                        data: { producto: codigo },
                        success: (response) => {
                            const data = JSON.parse(response);
                            if (data.res) {
                                this.producto.productoid = data.data[0].id_producto;
                                this.producto.nombre = data.data[0].nombre;
                                this.producto.cantidad = 0;
                                this.producto.stock = data.data[0].cantidad;
                                this.producto.codigo = data.data[0].codigo;
                                this.producto.almacen = parseInt(data.data[0].almacen);
                                $('#input_buscar_productos').val("");
                                $('#almacen, #delAlmacen').prop("disabled", false);
                                this.actualizarStock();
                            } else {
                                Swal.fire({ icon: 'warning', title: 'Advertencia', text: 'No se encontró ningún producto', confirmButtonText: 'Cerrar' });
                            }
                            this.usar_scaner = false;
                            this.cerrarCamara();
                        },
                        error: () => { alert('Error al buscar el producto.'); }
                    });
                },
                actualizarStock() {
                    _ajax(this.urlStock(), "POST", {
                        almacen: this.producto.almacen,
                        producto: this.producto.productoid
                    }, (resp) => {
                        this.producto.stock = resp ? resp.cantidad : 0;
                    });
                },
                btnCerrar() {
                    const principal = this.almacenes.find(a => a.principal == 1);
                    const defaultAlm = principal || (this.almacenes.length > 0 ? this.almacenes[0] : null);
                    const defaultId  = defaultAlm ? parseInt(defaultAlm.id_almacen) : '';
                    const other      = this.almacenes.find(a => parseInt(a.id_almacen) !== defaultId);

                    this.producto = {
                        productoid: "",
                        nombre: "",
                        cantidad: "",
                        stock: "0",
                        codigo: "",
                        almacen: defaultId,
                        alAlmacen: other ? parseInt(other.id_almacen) : '',
                        observaciones: ""
                    };
                    $("#input_buscar_productos, #input_buscar_productos_salida").val("");
                },
                addIngreso() {
                    const label = this.tipoItem === 'repuesto' ? 'repuesto' : 'producto';
                    if (!this.producto.almacen) {
                        alertAdvertencia("Seleccioná un almacén primero");
                        return;
                    }
                    if (!this.producto.productoid) {
                        alertAdvertencia(`Buscá y seleccioná un ${label}`).then(() => {
                            setTimeout(() => { $("#input_buscar_productos").focus(); }, 400);
                        });
                        return;
                    }
                    const data = { ...this.producto, tipo: 'i' };
                    _ajax(this.urlIngreso(), "POST", data, (resp) => {
                        if (resp.res) {
                            alertExito('Bien', "Ingreso registrado").then(() => location.reload());
                        } else {
                            alertAdvertencia(resp.msg || "No se pudo guardar el ingreso");
                        }
                    });
                },
                addSalida() {
                    const label = this.tipoItem === 'repuesto' ? 'repuesto' : 'producto';
                    $("#btnguardarSalida").prop('disabled', true);
                    if (this.producto.nombre.length > 0 && this.producto.stock > 0) {
                        if (parseInt(this.producto.cantidad) > parseInt(this.producto.stock)) {
                            alertAdvertencia("La cantidad no puede ser mayor al stock disponible");
                            $("#btnguardarSalida").prop('disabled', false);
                            return;
                        }
                        const data = { ...this.producto, tipo: 'e' };
                        _ajax(this.urlEgreso(), "POST", data, (resp) => {
                            if (resp.res) {
                                alertExito('Bien', "Registro Correcto").then(() => location.reload());
                            } else {
                                alertAdvertencia(resp.msg || "No se pudo Guardar la Salida");
                                $("#btnguardarSalida").prop('disabled', false);
                            }
                        });
                    } else {
                        alertAdvertencia(`Busque un ${label} primero o verifique stock`).then(() => {
                            setTimeout(() => { $("#input_buscar_productos_salida").focus(); }, 500);
                        });
                        $("#btnguardarSalida").prop('disabled', false);
                    }
                },
                onChangeAlmacen(event) {
                    const newAlmacen = parseInt(event.target.value);
                    this.producto.almacen = newAlmacen;
                    var other = this.almacenes.find(a => parseInt(a.id_almacen) !== newAlmacen);
                    if (other) this.producto.alAlmacen = parseInt(other.id_almacen);
                    if (this.producto.productoid) {
                        this.actualizarStock();
                    }
                },
                onlyNumber($event) {
                    const keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                        $event.preventDefault();
                    }
                }
            }
        });

        const autocompleteOpts = {
            source: _URL + "/ajs/cargar/productos",
            minLength: 1,
            appendTo: function (element) {
                return $(element).closest('.modal');
            },
            response: function (event, ui) {
                if (!ui.content.length) {
                    ui.content.push({
                        label: '⚠ No se encontró en este almacén',
                        value: '',
                        noResult: true
                    });
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.noResult) return;
                app.producto.productoid = ui.item.codigo;
                app.producto.nombre     = ui.item.nombre;
                app.producto.cantidad   = 0;
                app.producto.stock      = ui.item.cnt;
                app.producto.codigo     = ui.item.codigo;
                app.producto.almacen    = parseInt(ui.item.almacen) || app.producto.almacen;
                $(this).val("");
                app.actualizarStock();
            }
        };

        $("#input_buscar_productos").autocomplete(autocompleteOpts);
        $("#input_buscar_productos_salida").autocomplete(autocompleteOpts);

        $('#datatable').DataTable({
            responsive: true,
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {
                    "targets": 0,
                    "render": function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                }
            ]
        });

        $("#datatable").on("click", ".btn-confirmar", function (evt) {
            const cod      = $(evt.currentTarget).attr("data-item");
            const tipoItem = $(evt.currentTarget).attr("data-tipo-item") || 'producto';
            const url      = tipoItem === 'repuesto' ? '/ajs/confirmar/traslado/repuesto' : '/ajs/confirmar/traslado';
            Swal.fire({
                title: '¿Desea confirmar el traslado?',
                showDenyButton: true,
                confirmButtonText: 'Si',
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    _ajax(url, "POST", { cod }, function (resp) {
                        if (resp.res) {
                            Swal.fire('Buen trabajo', 'Traslado Exitoso', 'success').then(() => location.reload());
                        } else {
                            alertAdvertencia("Ocurrió un error al confirmar el traslado");
                        }
                    });
                }
            });
        });
    });
</script>
