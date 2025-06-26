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

<!-- CAMBIO PRINCIPAL: Seguir la estructura de compras.php -->
<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body" id="container-vue">

                <!-- CAMBIO 1: Usar la misma estructura de botones que compras.php -->
                <div class="card-title-desc text-end">
                    <button data-bs-toggle="modal" data-bs-target="#nuevaSalida" class="btn bg-white text-rojo"
                        @click="btnCerrar"
                        style="border-radius: 10px; padding: 8px 16px; font-weight: 500; border: 1px solid #CA3438; margin-right: 8px; transition: all 0.3s ease;">
                        <i class="fa fa-plus"></i> Nueva Salida
                    </button>
                    <button data-bs-toggle="modal" data-bs-target="#nuevoIngreso" class="btn bg-rojo text-white"
                        @click="btnCerrar" style="border-radius: 10px; padding: 8px 16px; font-weight: 500;">
                        <i class="fa fa-plus"></i> Nuevo Ingreso
                    </button>
                </div>

                  <div class="table-responsive">
                    <table id="datatable"
                        class="table table-bordered dt-responsive nowrap text-center table-sm"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">#</th>
                                <th style="text-align: center;">Producto</th>
                                <th style="text-align: center;">Cantidad</th>
                                <th style="text-align: center;">Tipo</th>
                                <th style="text-align: center;">Usuario</th>
                                <th style="text-align: center;">Egreso</th>
                                <th style="text-align: center;">Ingreso</th>
                                <th style="text-align: center;">Fecha Creación</th>
                                <th style="text-align: center;">Fecha Actualización</th>
                                <th style="text-align: center;">Confirmar <br> Traslado</th>
                                <th style="text-align: center;">Reporte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($getAll as $row): ?>
                                <tr>
                                    <td><?php echo $row['intercambio_id'] ?></td>
                                    <td class="text-start"><?php echo $row['codigo'] ?> | <?php echo $row['nombre'] ?>
                                    </td>
                                    <td><?php echo $row['cantidad'] ?></td>
                                    <?php
                                    $tipo = ($row['tipo'] == 'i') ? 'Ingreso' : 'Salida';
                                    ?>
                                    <td><?php echo $tipo ?></td>
                                   <td><?php echo $row["nombres"] ?></td>
                                    <td><?php echo $row['almacen_egreso_nombre'] ?></td>
                                    <td><?php echo $row['almacen_ingreso_nombre'] ?></td>
                                    <td><?php echo isset($row['fecha_creacion_formatted']) ? $row['fecha_creacion_formatted'] : 'N/A' ?></td>
                                    <td><?php echo isset($row['fecha_actualizacion_formatted']) ? $row['fecha_actualizacion_formatted'] : 'N/A' ?></td>
                                    <td class="text-center">
                                        <?php if ($row['tipo'] == 'e' && $row['estado'] == '0'): ?>
                                            <button data-item="<?= $row['intercambio_id'] ?>"
                                                class="btn-confirmar btn btn-sm btn-success">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                            href="<?= URL::to('reporte/ingresos/egresos/' . $row['intercambio_id'] . '') ?>"
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form v-on:submit.prevent="addIngreso" class="form-horizontal">
                                <div class="modal-body">
                                    <div class="row">
                                        <canvas hidden="" id="qr-canvas2" v-show="toggleCamara2"
                                            style="width: 300px; padding: 10px;"></canvas>
                                        <div class="mb-3 col-md-12">
                                            <label class="">
                                                <input id="btn-scan-qr2" v-model="usar_scaner2" @click="toggleCamara2"
                                                    type="checkbox"> Usar Scanner
                                            </label>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label">Producto</label>
                                            <input type="text" placeholder="Consultar Productos"
                                                class="form-control ui-autocomplete-input" id="input_buscar_productos"
                                                autocomplete="off">
                                        </div>
                                        <div class="mb-3">
                                            <label class="control-label">Nombre</label>
                                            <input required v-model="producto.nombre" type="text" placeholder="Nombre"
                                                class="form-control" readonly="true">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label class="control-label">Cantidad</label>
                                            <input required v-model="producto.cantidad" type="text" class="form-control"
                                                @keypress="onlyNumber">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="control-label">Ingreso Almacén</label>
                                            <select name="almacen" id="almacen" v-model="producto.almacen"
                                                class="form-control" @change="onChangeAlmacen($event)">
                                                <option value="1">Almacen 1</option>
                                                <option value="2">Almacen 2</option>
                                                <option value="3">Almacen 3</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label class="control-label">Stock Act.</label>
                                            <input v-model="producto.stock" type="text" class="form-control"
                                                readonly="true">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label">Observaciones</label>
                                            <textarea v-model="producto.observaciones" class="form-control" rows="3"
                                                placeholder="Ingrese observaciones (opcional)"></textarea>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form v-on:submit.prevent="addSalida" class="form-horizontal">
                                <div class="modal-body">
                                    <div class="row">
                                        <canvas hidden="" id="qr-canvas" v-show="toggleCamara"
                                            style="width: 300px; padding: 10px;"></canvas>
                                        <div class="mb-3 col-md-12">
                                            <label class="">
                                                <input id="btn-scan-qr" v-model="usar_scaner" @click="toggleCamara"
                                                    type="checkbox">
                                                Usar Scanner
                                            </label>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label">Producto</label>
                                            <input type="text" placeholder="Consultar Productos"
                                                class="form-control ui-autocomplete-input"
                                                id="input_buscar_productos_salida" autocomplete="off">
                                        </div>
                                        <div class="mb-3">
                                            <label class="control-label">Nombre</label>
                                            <input required v-model="producto.nombre" type="text" placeholder="Nombre"
                                                class="form-control" readonly="true">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="control-label">Del Almacén</label>
                                            <select name="delAlmacen" id="delAlmacen" v-model="producto.almacen"
                                                class="form-control" @change="onChangeAlmacen($event)">
                                                <option value="1">Almacén 1</option>
                                                <option value="2">Almacén 2</option>
                                                <option value="3">Almacén 3</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="control-label">Al Almacén</label>
                                            <select v-model="producto.alAlmacen" class="form-control">
                                                <option value="1" v-if="producto.almacen !== '1'">Almacén 1</option>
                                                <option value="2" v-if="producto.almacen !== '2'">Almacén 2</option>
                                                <option value="3" v-if="producto.almacen !== '3'">Almacén 3</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="control-label">Cantidad</label>
                                            <input required v-model="producto.cantidad" type="text" class="form-control"
                                                @keypress="onlyNumber">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="control-label">Stock Act.</label>
                                            <input v-model="producto.stock" type="text" class="form-control"
                                                readonly="true">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="control-label">Observaciones</label>
                                            <textarea v-model="producto.observaciones" class="form-control" rows="3"
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
                producto: {
                    productoid: "",
                    cantidad: "",
                    stock: "0",
                    codigo: "",
                    almacen: "1",
                    alAlmacen: "2",
                    tipo: '',
                    nombre: '',
                    observaciones: ''
                },
            },
            methods: {
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
                        .getUserMedia({
                            video: { facingMode: "environment" }
                        })
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
                                try {
                                    qrcode.decode();
                                } catch (e) {
                                    setTimeout(scan, 500);
                                }
                            };

                            video.addEventListener("loadeddata", () => {
                                canvasElement.hidden = false;
                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => {
                                this.procesarCodigoQR(respuesta);
                            };
                        });
                },
                encenderCamara2() {
                    navigator.mediaDevices
                        .getUserMedia({
                            video: { facingMode: "environment" }
                        })
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
                                try {
                                    qrcode.decode();
                                } catch (e) {
                                    setTimeout(scan, 500);
                                }
                            };

                            video.addEventListener("loadeddata", () => {
                                canvasElement.hidden = false;
                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => {
                                this.procesarCodigoQR(respuesta);
                            };
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
                        url: _URL + '/ajas/compra/buscar/producto',
                        data: { producto: codigo },
                        success: (response) => {
                            const data = JSON.parse(response);
                            if (data.res) {
                                this.producto.productoid = data.data[0].id_producto;
                                this.producto.nombre = data.data[0].nombre;
                                this.producto.cantidad = 0;
                                this.producto.stock = data.data[0].cantidad;
                                this.producto.codigo = data.data[0].codigo;
                                this.producto.almacen = data.data[0].almacen;

                                $('#input_buscar_productos').val("");
                                $('#almacen').prop("disabled", false);
                                $('#delAlmacen').prop("disabled", false);

                                this.actualizarStock();
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Advertencia',
                                    text: 'No se encontró ningún producto',
                                    confirmButtonText: 'Cerrar'
                                });
                            }
                            this.usar_scaner = false;
                            this.cerrarCamara();
                        },
                        error: () => {
                            alert('Error al buscar el producto.');
                        }
                    });
                },
                actualizarStock() {
                    _ajax("/ajs/consulta/stock/almacen", "POST", {
                        almacen: this.producto.almacen,
                        producto: this.producto.productoid
                    }, (resp) => {
                        this.producto.stock = resp ? resp.cantidad : 0;
                    });
                },
                btnCerrar() {
                    this.producto = {
                        productoid: "",
                        nombre: "",
                        cantidad: "",
                        stock: "0",
                        codigo: "",
                        almacen: "1",
                        alAlmacen: "2",
                        observaciones: ""
                    };
                },
                addIngreso() {
                    if (this.producto.nombre.length > 0) {
                        const data = {
                            ...this.producto,
                            tipo: 'i'
                        };
                        _ajax("/ajs/ingreso/almacen/add", "POST", data,
                            (resp) => {
                                if (resp.res) {
                                    alertExito('Bien', "Registro Correcto")
                                        .then(() => location.reload());
                                } else {
                                    alertAdvertencia("No se pudo Guardar el Ingreso");
                                }
                            }
                        );
                    } else {
                        alertAdvertencia("Busque un producto primero")
                            .then(() => {
                                setTimeout(() => {
                                    $("#input_buscar_productos").focus();
                                }, 500);
                            });
                    }
                },
                addSalida() {
                    $("#btnguardarSalida").prop('disabled', true);
                    if (this.producto.nombre.length > 0 && this.producto.stock > 0) {
                        if (parseInt(this.producto.cantidad) > parseInt(this.producto.stock)) {
                            alertAdvertencia("La cantidad no puede ser mayor al stock disponible");
                            $("#btnguardarSalida").prop('disabled', false);
                            return;
                        }

                        const data = {
                            ...this.producto,
                            tipo: 'e'
                        };

                        _ajax("/ajs/egreso/almacen/add", "POST", data,
                            (resp) => {
                                if (resp.res) {
                                    alertExito('Bien', "Registro Correcto")
                                        .then(() => location.reload());
                                } else {
                                    alertAdvertencia(resp.msg || "No se pudo Guardar la Salida");
                                    $("#btnguardarSalida").prop('disabled', false);
                                }
                            }
                        );
                    } else {
                        alertAdvertencia("Busque un producto primero o verifique stock")
                            .then(() => {
                                setTimeout(() => {
                                    $("#input_buscar_productos").focus();
                                }, 500);
                            });
                        $("#btnguardarSalida").prop('disabled', false);
                    }
                },
                onChangeAlmacen(event) {
                    const newAlmacen = event.target.value;

                    if (newAlmacen === '1') {
                        this.producto.alAlmacen = '2';
                    } else if (newAlmacen === '2') {
                        this.producto.alAlmacen = '1';
                    } else if (newAlmacen === '3') {
                        this.producto.alAlmacen = '1';
                    }

                    if (this.producto.nombre) {
                        this.producto.almacen = newAlmacen;
                        this.actualizarStock();
                    }
                },
                onlyNumber($event) {
                    const keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                        $event.preventDefault();
                    }
                }
            },
            watch: {
                'producto.almacen': function (newVal, oldVal) {
                    if (newVal !== oldVal) {
                        this.onChangeAlmacen({ target: { value: newVal } });
                    }
                }
            }
        });

        $("#input_buscar_productos, #input_buscar_productos_salida").autocomplete({
            source: _URL + "/ajs/cargar/productos",
            minLength: 1,
            appendTo: function (element) {
                return $(element).closest('.modal');
            },
            select: function (event, ui) {
                event.preventDefault();
                app.producto.productoid = ui.item.codigo;
                app.producto.nombre = ui.item.nombre;
                app.producto.cantidad = 0;
                app.producto.stock = ui.item.cnt;
                app.producto.codigo = ui.item.codigo;
                app.producto.almacen = ui.item.almacen;

                $(this).val("");
                $('#almacen, #delAlmacen').prop("disabled", false);

                app.actualizarStock();
            }
        });

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
            ],
            "orderFixed": {
                "pre": [0, 'asc']
            }
        });

        $("#datatable").on("click", ".btn-confirmar", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            Swal.fire({
                title: '¿Desea confirmar el traslado?',
                showDenyButton: true,
                confirmButtonText: 'Si',
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    _ajax("/ajs/confirmar/traslado", "POST", { cod },
                        function (resp) {
                            if (resp.res) {
                                Swal.fire('Buen trabajo', 'Traslado Exitoso', 'success')
                                    .then(() => location.reload());
                            } else {
                                alertAdvertencia("Ocurrió un error");
                            }
                        }
                    );
                }
            });
        });
    });
</script>