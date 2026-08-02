// public/js/orden/orden-repuestos-vue.js
// Vue instance para la gestión de repuestos (almacén)
// Depende de: jQuery, Vue 2 CDN

var app;

$(document).ready(function () {
    app = new Vue({
        el: "#conte-vue-modals",
        data: {
            almacen: 4,
            t: 0,
            listaRep: [],
            almacenes: [],
            almacenesLoaded: false,
            almacenImportacionRep: 1,
            categorias: [],
            unidades: [],
            subcategorias: [],
            subcategoriasEdit: [],
            precios: [],
            preciosNuevos: [],
            almacenEditando: null,
            nuevoAlmacen: '',
            nuevoAlmacenNuevo: '',
            marcarPrincipal: false,
            restock: { cod: '', cantidad: '' },
            reg: {
                nombre: '', precio: '0', costo: '0', cantidad: '0',
                codSunat: '', afecto: '0', ruc: '', razon: '',
                precio1: 0, precio2: 0, precio3: 0, precio4: 0,
                codigo: 0, imagen: '', detalle: '', categoria: '',
                almacen: 1, unidad: '', subcategoria: '',
                moneda: 'PEN', usar_multiprecio: false, usar_barra: '0',
            },
            edt: {
                nombre: '', cod_rep: '', cod: '', precio: '0', costo: '0',
                codSunat: '', afecto: '0', usar_barra: '0',
                ruc: '', razon: '', precioMayor: '', precioMenor: '',
                precio2: '', precio3: '', precio4: '', precio_unidad: '',
                codigo: '', cantidad: '', detalle: '', almacen: '',
                unidad: '', categoria: '', subcategoria: '',
                moneda: 'PEN', usar_multiprecio: false,
                imagen: '', imagen_actual: '',
            },
            listaIdsss: [],
            stockData: {
                repuesto_id: '', stock_actual: '', cantidad_ingresar: '', repuesto_nombre: ''
            },
            disminuirDataRepuesto: {
                repuesto_id: '', stock_actual: '', cantidad_disminuir: '',
                repuesto_nombre: '', unidad: '', observaciones: ''
            },
            trasladoDataRepuesto: {
                almacen_origen: '', almacen_destino: '', repuesto_id: '',
                repuestos: [], nota: ''
            },
        },
        computed: {
            simboloMonedaReg() {
                return this.reg.moneda === 'USD' ? '$' : 'S/';
            },
            simboloMonedaEdt() {
                return this.edt.moneda === 'USD' ? '$' : 'S/';
            }
        },
        mounted() {
            this.cargarAlmacenes();
        },
        watch: {
            almacenes: {
                immediate: true,
                handler() {
                    if (this.almacenes.length > 0 && !this.almacenesLoaded) {
                        var existe = this.almacenes.some(function (a) {
                            return a.id_almacen == app.almacen;
                        });
                        if (!existe) {
                            app.almacen = this.almacenes[0].id_almacen;
                        }
                        app.$nextTick(function () {
                            app.changeAlmacenById(app.almacen);
                        });
                        this.almacenesLoaded = true;
                    }
                }
            }
        },
        methods: {
            // ========== PRECIOS ==========
            agregarPrecio() {
                this.precios.push({});
            },
            eliminarPrecio(index) {
                this.precios.splice(index, 1);
            },
            agregarPrecioNuevo() {
                this.preciosNuevos.push({});
            },
            eliminarPrecioNuevo(index) {
                this.preciosNuevos.splice(index, 1);
            },

            // ========== CARGA INICIAL ==========
            cargarUnidades() {
                _get("/ajs/get/unidades/rep", function (resp) {
                    app.unidades = JSON.parse(resp);
                });
            },
            cargarAlmacenes() {
                _ajax("/ajs/data/repuesto/almacen/listar", "POST", {}, function (resp) {
                    app.almacenes = resp;
                });
            },
            cargarCategorias() {
                _get("/ajs/get/categorias/rep", function (resp) {
                    app.categorias = JSON.parse(resp);
                });
            },
            cargarSubcategorias() {
                var data = { categoria: this.reg.categoria };
                _ajax("/ajs/get/subcategorias/rep/by-categoria", "POST", data, function (resp) {
                    app.subcategorias = resp;
                });
            },
            cargarSubcategoriasEdit() {
                var data = { categoria: this.edt.categoria };
                _ajax("/ajs/get/subcategorias/rep/by-categoria", "POST", data, function (resp) {
                    app.subcategoriasEdit = resp;
                });
            },

            // ========== ALMACENES ==========
            abrirModalAlmacen() {
                this.almacenEditando = null;
                this.nuevoAlmacen = '';
                this.nuevoAlmacenNuevo = '';
                this.marcarPrincipal = false;
                $("#modal-agregar-almacen-repuesto").modal("show");
            },
            seleccionarAlmacen(alm) {
                this.almacenEditando = alm;
                this.nuevoAlmacen = alm.nombre;
                this.marcarPrincipal = alm.principal == 1;
            },
            agregarNuevoAlmacen() {
                if (!this.nuevoAlmacenNuevo) {
                    alertAdvertencia("Ingrese un nombre");
                    return;
                }
                _ajax("/ajs/data/repuesto/almacen/agregar", "POST", {
                    nombre: this.nuevoAlmacenNuevo,
                    marcarPrincipal: this.marcarPrincipal ? 1 : 0
                }, function (resp) {
                    if (resp == "nice") {
                        app.cargarAlmacenes();
                        app.nuevoAlmacenNuevo = '';
                        alertExito("Almacén agregado");
                    } else {
                        alertAdvertencia("Error al agregar almacén");
                    }
                });
            },
            guardarAlmacenEdicion() {
                if (!this.nuevoAlmacen) {
                    alertAdvertencia("Ingrese un nombre");
                    return;
                }
                _ajax("/ajs/data/repuesto/almacen/editar", "POST", {
                    id: this.almacenEditando.id_almacen,
                    nombre: this.nuevoAlmacen,
                    marcarPrincipal: this.marcarPrincipal ? 1 : 0
                }, function (resp) {
                    if (resp == "nice") {
                        app.cargarAlmacenes();
                        alertExito("Almacén actualizado");
                    } else {
                        alertAdvertencia("Error al actualizar");
                    }
                });
            },
            eliminarAlmacen() {
                Swal.fire({
                    title: "¿Eliminar almacén?",
                    text: "Esta acción no se puede deshacer",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#C1272D",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then(function (result) {
                    if (result.isConfirmed) {
                        _ajax("/ajs/data/repuesto/almacen/eliminar", "POST", {
                            id: app.almacenEditando.id_almacen
                        }, function (resp) {
                            if (resp == "nice") {
                                app.cargarAlmacenes();
                                app.almacenEditando = null;
                                alertExito("Almacén eliminado");
                            } else {
                                alertAdvertencia("No se pudo eliminar");
                            }
                        });
                    }
                });
            },

            // ========== CAMBIO DE ALMACÉN ==========
            changeAlmacen(event) {
                var almacenId = event.target.value;
                almacenCod = almacenId;
                if (datatable) datatable.destroy();
                datatable = $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    sAjaxSource: "/ajs/server/sider/repuestos",
                    fnServerParams: function (aoData) {
                        aoData.push({ name: "almacenId", value: almacenCod });
                        var checked = [];
                        $(".filter-option:checked").each(function () {
                            checked.push($(this).val());
                        });
                        aoData.push({ name: "filter", value: checked.join(",") });
                    },
                    order: [[0, "ASC"]],
                    columns: [
                        { data: "codigo", render: function (data) { return '<a href="javascript:void(0)" onclick="abrirModalBarras(\'' + data + '\')" class="enlace-rojo">' + data + '</a>'; } },
                        { data: "nombre" },
                        { data: "unidad" },
                        { data: "precio", render: function (data, type, row) { return (row.moneda === "USD" ? "$" : "S/") + " " + parseFloat(data).toFixed(2); } },
                        { data: "stock" },
                        { data: null, render: function (data) { return '<button class="btn-edt btn btn-sm btn-info" data-id="' + data.id_repuesto + '" data-tipo="' + data.tipo_repuesto + '"><i class="fa fa-edit"></i></button>'; } },
                        { data: null, render: function (data) { return '<input type="checkbox" class="btnCheckEliminar" value="' + data.id_repuesto + '">'; } }
                    ],
                    drawCallback: function () { $("#datatable_processing").hide(); },
                    fnServerData: function (sSource, aoData, fnCallback, oSettings) {
                        oSettings.jqXHR = $.ajax({
                            dataType: "json",
                            type: "GET",
                            url: sSource,
                            data: aoData,
                            success: fnCallback,
                            error: function (resp) { console.log(resp); $("#datatable_processing").hide(); }
                        });
                    }
                });
            },
            changeAlmacenById(id) {
                almacenCod = id;
                if (datatable) datatable.destroy();
                datatable = $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    sAjaxSource: "/ajs/server/sider/repuestos",
                    fnServerParams: function (aoData) {
                        aoData.push({ name: "almacenId", value: almacenCod });
                        var checked = [];
                        $(".filter-option:checked").each(function () {
                            checked.push($(this).val());
                        });
                        aoData.push({ name: "filter", value: checked.join(",") });
                    },
                    order: [[0, "ASC"]],
                    columns: [
                        { data: "codigo", render: function (data) { return '<a href="javascript:void(0)" onclick="abrirModalBarras(\'' + data + '\')" class="enlace-rojo">' + data + '</a>'; } },
                        { data: "nombre" },
                        { data: "unidad" },
                        { data: "precio", render: function (data, type, row) { return (row.moneda === "USD" ? "$" : "S/") + " " + parseFloat(data).toFixed(2); } },
                        { data: "stock" },
                        { data: null, render: function (data) { return '<button class="btn-edt btn btn-sm btn-info" data-id="' + data.id_repuesto + '" data-tipo="' + data.tipo_repuesto + '"><i class="fa fa-edit"></i></button>'; } },
                        { data: null, render: function (data) { return '<input type="checkbox" class="btnCheckEliminar" value="' + data.id_repuesto + '">'; } }
                    ],
                    drawCallback: function () { $("#datatable_processing").hide(); },
                    fnServerData: function (sSource, aoData, fnCallback, oSettings) {
                        oSettings.jqXHR = $.ajax({
                            dataType: "json",
                            type: "GET",
                            url: sSource,
                            data: aoData,
                            success: fnCallback,
                            error: function (resp) { console.log(resp); $("#datatable_processing").hide(); }
                        });
                    }
                });
            },

            // ========== CÓDIGO DE BARRAS ==========
            generarCodeBarraAdd() {
                var cod = this.reg.usar_barra == '1' ? this.reg.codigo : this.reg.codigo;
                if (cod) {
                    $("#barcodeAdd").html("");
                    JsBarcode("#barcodeAdd", cod.toString(), { format: "CODE128", width: 2, height: 50 });
                }
            },
            handleUsarBarraChange() {
                if (this.edt.usar_barra == '1') {
                    this.edt.codigo = this.edt.cod_rep;
                    this.$nextTick(function () { app.edtGenerarCodeBarra(); });
                }
            },
            edtGenerarCodeBarra() {
                var cod = this.edt.codigo;
                if (cod) {
                    $("#barcodeEdit").html("");
                    JsBarcode("#barcodeEdit", cod.toString(), { format: "CODE128", width: 2, height: 50 });
                }
            },
            toggleBarcodeEdit() {
                this.$nextTick(function () { app.edtGenerarCodeBarra(); });
            },

            // ========== IMPORTACIÓN ==========
            agregarListaImport() {
                _ajax("/ajs/data/repuesto/add/lista", "POST", {
                    datos: this.listaRep,
                    almacen: this.almacenImportacionRep
                }, function (resp) {
                    if (resp == "nice") {
                        app.listaRep = [];
                        $("#importarModal").modal("hide");
                        app.changeAlmacenById(app.almacen);
                        alertExito("Repuestos importados correctamente");
                    } else {
                        alertAdvertencia("Error al importar");
                    }
                });
            },

            // ========== CONSULTA RUC ==========
            ChangeconsultarDocRUC() {
                if (this.reg.ruc && this.reg.ruc.length == 11) {
                    this.consultarDocRUC();
                }
            },
            consultarDocRUC() {
                if (this.reg.ruc.length == 11) {
                    this.getInfoDoc2();
                }
            },
            getInfoDoc2() {
                _ajax("/ajs/consulta/doc/cliente", "POST", { doc: this.reg.ruc }, function (resp) {
                    var r = JSON.parse(resp);
                    if (r.data.razon_social) {
                        app.reg.razon = r.data.razon_social;
                    } else if (r.data.nombre) {
                        app.reg.razon = r.data.nombre;
                    }
                });
            },
            getInfoDoc3() {
                _ajax("/ajs/consulta/doc/cliente", "POST", { doc: this.edt.ruc }, function (resp) {
                    var r = JSON.parse(resp);
                    if (r.data.razon_social) {
                        app.edt.razon = r.data.razon_social;
                    } else if (r.data.nombre) {
                        app.edt.razon = r.data.nombre;
                    }
                });
            },

            eliminarItemTablaRep(index) {
                this.listaRep.splice(index, 1);
            },
            agregarStock() {
                _ajax("/ajs/data/repuesto/restock", "POST", this.restock, function (resp) {
                    if (resp == "nice") {
                        app.restock = { cod: '', cantidad: '' };
                        alertExito("Stock actualizado");
                    }
                });
            },

            // ========== CRUD REPUESTO ==========
            actualizarRep() {
                var fd = new FormData();
                fd.append("nombre", this.edt.nombre);
                fd.append("codigo", this.edt.codigo);
                fd.append("cod_rep", this.edt.cod_rep);
                fd.append("precio", this.edt.precio);
                fd.append("costo", this.edt.costo);
                fd.append("codSunat", this.edt.codSunat);
                fd.append("afecto", this.edt.afecto);
                fd.append("usar_barra", this.edt.usar_barra);
                fd.append("precioMayor", this.edt.precioMayor);
                fd.append("precioMenor", this.edt.precioMenor);
                fd.append("precio2", this.edt.precio2);
                fd.append("precio3", this.edt.precio3);
                fd.append("precio4", this.edt.precio4);
                fd.append("precio_unidad", this.edt.precio_unidad);
                fd.append("detalle", this.edt.detalle);
                fd.append("almacen", this.edt.almacen);
                fd.append("unidad", this.edt.unidad);
                fd.append("categoria", this.edt.categoria);
                fd.append("subcategoria", this.edt.subcategoria);
                fd.append("moneda", this.edt.moneda);
                fd.append("usar_multiprecio", this.edt.usar_multiprecio ? 1 : 0);
                if (this.edt.imagen) fd.append("imagen", this.edt.imagen);
                $.ajax({
                    url: _URL + "/ajs/data/repuesto/edt",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp == "nice") {
                            if (app.edt.usar_multiprecio) {
                                app.agregarPrecios();
                            }
                            $("#modal-edt-rep").modal("hide");
                            app.changeAlmacenById(app.almacen);
                            alertExito("Repuesto actualizado");
                            document.dispatchEvent(new CustomEvent("repuestoActualizado"));
                        } else {
                            alertAdvertencia("Error al actualizar");
                        }
                    }
                });
            },
            onImageChange(event) {
                this.reg.imagen = event.target.files[0];
            },
            onImageChangeEdit(event) {
                this.edt.imagen = event.target.files[0];
            },
            agregarRep() {
                var fd = new FormData();
                fd.append("nombre", this.reg.nombre);
                fd.append("codigo", this.reg.codigo);
                fd.append("precio", this.reg.precio);
                fd.append("costo", this.reg.costo);
                fd.append("cantidad", this.reg.cantidad);
                fd.append("codSunat", this.reg.codSunat);
                fd.append("afecto", this.reg.afecto);
                fd.append("usar_barra", this.reg.usar_barra);
                fd.append("precio1", this.reg.precio1);
                fd.append("precio2", this.reg.precio2);
                fd.append("precio3", this.reg.precio3);
                fd.append("precio4", this.reg.precio4);
                fd.append("detalle", this.reg.detalle);
                fd.append("categoria", this.reg.categoria);
                fd.append("almacen", this.reg.almacen);
                fd.append("unidad", this.reg.unidad);
                fd.append("subcategoria", this.reg.subcategoria);
                fd.append("moneda", this.reg.moneda);
                fd.append("usar_multiprecio", this.reg.usar_multiprecio ? 1 : 0);
                if (this.reg.imagen) fd.append("imagen", this.reg.imagen);

                fetch(_URL + "/ajs/data/repuesto/add", {
                    method: "POST", body: fd
                }).then(function (r) { return r.json(); }).then(function (resp) {
                    if (resp.res) {
                        $("#modal-add-repuesto").modal("hide");
                        app.reg = {
                            nombre: '', precio: '0', costo: '0', cantidad: '0',
                            codSunat: '', afecto: '0', ruc: '', razon: '',
                            precio1: 0, precio2: 0, precio3: 0, precio4: 0,
                            codigo: 0, imagen: '', detalle: '', categoria: '',
                            almacen: 1, unidad: '', subcategoria: '',
                            moneda: 'PEN', usar_multiprecio: false, usar_barra: '0',
                        };
                        app.changeAlmacenById(app.almacen);
                        alertExito("Repuesto registrado");
                        document.dispatchEvent(new CustomEvent("repuestoAgregado"));
                    } else {
                        alertAdvertencia(resp.msg || "Error al registrar");
                    }
                }).catch(function () {
                    alertAdvertencia("Error de conexión");
                });
            },

            // ========== EDITAR (setInfo) ==========
            setInfo(data) {
                if (data.imagen && data.imagen !== "null") {
                    this.edt.imagen_actual = _URL + "/public/uploads/" + data.imagen;
                } else {
                    this.edt.imagen_actual = _URL + "/public/images/noimage.jpg";
                }
                this.edt.nombre = data.nombre;
                this.edt.cod_rep = data.codigo;
                this.edt.cod = data.codigo;
                this.edt.precio = data.precio;
                this.edt.costo = data.costo;
                this.edt.codSunat = data.codsunat;
                this.edt.afecto = data.afecto;
                this.edt.usar_barra = data.usar_barra;
                this.edt.codigo = data.usar_barra == '1' ? data.codigo : data.cod_barra;
                this.edt.cantidad = data.cantidad;
                this.edt.detalle = data.detalle;
                this.edt.almacen = data.almacen;
                this.edt.unidad = data.unidad;
                this.edt.categoria = data.categoria;
                this.edt.subcategoria = data.subcategoria;
                this.edt.moneda = data.moneda;
                this.edt.usar_multiprecio = data.usar_multiprecio == 1;

                this.cargarUnidades();
                this.cargarCategorias();

                _ajax("/ajs/get/subcategorias/rep/by-categoria", "POST", { categoria: data.categoria }, function (resp) {
                    app.subcategoriasEdit = resp;
                    app.$nextTick(function () { app.edt.subcategoria = data.subcategoria; });
                });

                _ajax("/ajs/data/repuesto/obtener/precios", "POST", { id: data.id_repuesto }, function (resp) {
                    try {
                        app.precios = JSON.parse(resp);
                    } catch (e) {
                        app.precios = resp || [];
                    }
                });
            },

            onlyNumber($event) {
                var key = $event.keyCode || $event.which;
                if (key < 48 || key > 57) $event.preventDefault();
            },

            // ========== STOCK ==========
            aumentarStockRepuesto() {
                _ajax("/ajs/data/repuesto/aumentar/stock", "POST", {
                    repuesto_id: this.stockData.repuesto_id,
                    cantidad_ingresar: this.stockData.cantidad_ingresar
                }, function (resp) {
                    if (resp == "nice") {
                        app.changeAlmacenById(app.almacen);
                        $("#modal-aumentar-stock").modal("hide");
                        alertExito("Stock aumentado");
                        document.dispatchEvent(new CustomEvent("repuestoActualizado"));
                    } else {
                        alertAdvertencia(resp.msg || "Error al aumentar stock");
                    }
                });
            },
            disminuirStockRepuesto() {
                _ajax("/ajs/data/repuesto/disminuir/stock", "POST", {
                    repuesto_id: this.disminuirDataRepuesto.repuesto_id,
                    cantidad_disminuir: this.disminuirDataRepuesto.cantidad_disminuir,
                    observaciones: this.disminuirDataRepuesto.observaciones
                }, function (resp) {
                    if (resp == "nice") {
                        app.changeAlmacenById(app.almacen);
                        $("#modal-disminuir-stock-repuesto").modal("hide");
                        alertExito("Stock disminuido");
                        document.dispatchEvent(new CustomEvent("repuestoActualizado"));
                    } else {
                        alertAdvertencia(resp.msg || "Error al disminuir stock");
                    }
                });
            },
            limpiarRepuestoTraslado() {
                this.trasladoDataRepuesto.repuesto_id = '';
            },
            eliminarRepuestoTraslado(index) {
                this.trasladoDataRepuesto.repuestos.splice(index, 1);
            },
            realizarTrasladoRepuesto() {
                _ajax("/ajs/data/repuesto/traslado/almacenes", "POST", {
                    almacen_origen: this.trasladoDataRepuesto.almacen_origen,
                    almacen_destino: this.trasladoDataRepuesto.almacen_destino,
                    repuestos: JSON.stringify(this.trasladoDataRepuesto.repuestos),
                    nota: this.trasladoDataRepuesto.nota
                }, function (resp) {
                    if (resp == "nice") {
                        app.changeAlmacenById(app.almacen);
                        $("#modal-traslado-almacenes-repuesto").modal("hide");
                        app.trasladoDataRepuesto = {
                            almacen_origen: '', almacen_destino: '', repuesto_id: '',
                            repuestos: [], nota: ''
                        };
                        alertExito("Traslado realizado");
                    } else {
                        alertAdvertencia(resp.msg || "Error en traslado");
                    }
                });
            },

            // ========== HISTORIAL ==========
            cargarHistorialStock() {
                if ($.fn.DataTable.isDataTable("#tabla-historial-stock-repuesto")) {
                    $("#tabla-historial-stock-repuesto").DataTable().destroy();
                }
                $("#tabla-historial-stock-repuesto").DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "/ajs/data/repuesto/historial/stock",
                        type: "POST",
                        dataSrc: "data"
                    },
                    order: [[5, "desc"]],
                    pageLength: 15,
                    responsive: true,
                    language: { url: "ServerSide/Spanish.json" },
                    dom: "Bfrtip",
                    buttons: [
                        { extend: "excelHtml5", text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
                        { extend: "pdfHtml5", text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm' }
                    ],
                    columns: [
                        { data: "codigo" },
                        { data: "repuesto_nombre" },
                        { data: "tipo_movimiento", render: function (data) { return data == "entrada" ? '<span class="badge bg-success">Entrada</span>' : '<span class="badge bg-danger">Salida</span>'; } },
                        { data: "cantidad" },
                        { data: "costo_compra", render: function (data) { return data ? "S/ " + parseFloat(data).toFixed(2) : "-"; } },
                        { data: "fecha_movimiento", render: function (data) { return data ? data.replace(" ", " <br> ") : "-"; } },
                        { data: "usuario" },
                        { data: "observaciones", render: function (data) { return data && data.length > 50 ? '<span title="' + data + '">' + data.substring(0, 50) + '...</span>' : (data || '-'); } }
                    ]
                });
            }
        }
    });
});
