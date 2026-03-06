<!-- resources\views\fragment-views\cliente\ordenTrabajo\repuestos.php -->
<?php

require_once 'app/models/Repuesto.php';
$c_repuesto = new Repuesto();
$c_repuesto->setIdEmpresa($_SESSION['id_empresa']);
$almacenRepuesto = 1;

?>
<style>
    .dt-body-left {
        text-align: left !important;
    }

    /* Estilos personalizados para el autocomplete */
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #e9ecef !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        background: white !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        z-index: 9999 !important;
        padding: 4px 0 !important;
    }

    .ui-autocomplete .ui-menu-item .ui-menu-item-wrapper {
        padding: 8px 12px !important;
        font-size: 13px !important;
        line-height: 1.4 !important;
        border: none !important;
        color: #495057 !important;
        background: transparent !important;
        margin: 0 2px !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .ui-autocomplete .ui-menu-item .ui-menu-item-wrapper:hover,
    .ui-autocomplete .ui-menu-item .ui-menu-item-wrapper.ui-state-active,
    .ui-autocomplete .ui-menu-item .ui-menu-item-wrapper.ui-state-focus {
        background: #f8f9fa !important;
        border: 1px solid #CA3438 !important;
        color: #CA3438 !important;
        font-weight: 500 !important;
    }
</style>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title text-center">REPUESTOS</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Almacen</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);"
                        style="font-weight: 500; color: #CA3438;">Repuestos</a></li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <div hidden class="dropdown">
                    <button class="btn btn-primary  dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-cog me-2"></i> Ajustes

                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Separated link</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="conte-vue-modals">
    <input type="hidden" name="almacenId" id="almacenId" value="<?php echo $almacenRepuesto ?>">

    <div class="row">
        <div class="col-12">
            <div class="card"
                style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); background: #fff;">
                <div class="card-header" style="background: #fff; border-bottom: none; padding-bottom: 0;">
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">

                        <!-- Contenedor Izquierdo: Solo para pantallas pequeñas -->
                        <div class="d-flex align-items-center gap-2 d-lg-none">
                            <!-- Dropdown de Opciones para móvil -->
                            <div class="table-view-dropdown ms-2">
                                <div class="dropdown">
                                    <button class="btn bg-rojo text-white dropdown-toggle" type="button"
                                        id="dropdownOpcionesRepuestos" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-cog me-1"></i> Opciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="dropdownOpcionesRepuestos" style="min-width: 250px;">
                                        <li>
                                            <h6 class="dropdown-header">Reportes</h6>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="descarFunccc()">
                                                <i class="fa fa-file-excel me-2"></i> Descargar Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="<?= URL::to('/reporte/historial-stock-repuestos/excel') ?>"
                                                target="_blank">
                                                <i class="fa fa-chart-line me-2"></i> Reporte Movimientos
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#importarModal">
                                                <i class="fa fa-file-excel me-2"></i> Importar
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <h6 class="dropdown-header">Gestión de Stock</h6>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-aumentar-stock">
                                                <i class="fa fa-plus me-2"></i> Aumentar Stock
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-disminuir-stock-repuesto">
                                                <i class="fa fa-minus me-2"></i> Disminuir Stock
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-traslado-almacenes-repuesto">
                                                <i class="fa fa-exchange-alt me-2"></i> Traslado Almacenes
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-historial-stock-repuesto">
                                                <i class="fa fa-history me-2"></i> Historial Stock
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <h6 class="dropdown-header">Configuración</h6>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/unidades/repuestos">
                                                <i class="fa fa-ruler me-2"></i> Unidades
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/categorias/repuestos">
                                                <i class="fa fa-folder me-2"></i> Categorías
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" id="add-rep-dropdown">
                                                <i class="fa fa-plus me-2"></i> Agregar Repuesto
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                class="btnBorrar-dropdown">
                                                <i class="fa fa-trash me-2"></i> Borrar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Contenedor Derecho: Botones individuales para pantallas grandes -->
                        <div class="grid-view-buttons d-none d-lg-flex flex-wrap justify-content-end gap-2">
                            <button onclick="descarFunccc()" class="btn bg-white text-rojo border-rojo btn-sm">
                                <i class="fa fa-file-excel"></i>
                                <span class="d-none d-lg-inline">Descargar Excel</span>
                            </button>
                            <a href="<?= URL::to('/reporte/historial-stock-repuestos/excel') ?>" target="_blank"
                                class="btn bg-white text-rojo border-rojo btn-sm">
                                <i class="fa fa-chart-line"></i>
                                <span class="d-none d-lg-inline">Reporte Movimientos</span>
                            </a>
                            <button data-bs-toggle="modal" data-bs-target="#importarModal"
                                class="btn bg-white text-rojo border-rojo btn-sm">
                                <i class="fa fa-file-excel"></i>
                                <span class="d-none d-lg-inline">Importar</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-aumentar-stock">
                                <i class="fa fa-plus"></i>
                                <span class="d-none d-lg-inline">Aumentar Stock</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-disminuir-stock-repuesto">
                                <i class="fa fa-minus"></i>
                                <span class="d-none d-lg-inline">Disminuir Stock</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-traslado-almacenes-repuesto">
                                <i class="fa fa-exchange-alt"></i>
                                <span class="d-none d-lg-inline">Traslado Almacenes</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-historial-stock-repuesto">
                                <i class="fa fa-history"></i>
                                <span class="d-none d-lg-inline">Historial Stock</span>
                            </button>
                            <a href="/unidades/repuestos" class="btn bg-white text-rojo border-rojo button-link btn-sm">
                                <i class="fa fa-ruler"></i>
                                <span class="d-none d-lg-inline">Unidades</span>
                            </a>
                            <a href="/categorias/repuestos"
                                class="btn bg-white text-rojo border-rojo button-link btn-sm">
                                <i class="fa fa-folder"></i>
                                <span class="d-none d-lg-inline">Categorías</span>
                            </a>
                            <button class="btn bg-rojo text-white bordes btn-sm" id="add-rep">
                                <i class="fa fa-plus"></i>
                                <span class="d-none d-lg-inline">Agregar Repuesto</span>
                            </button>
                            <button class="btn bg-rojo btnBorrar btn-sm">
                                <i class="fa fa-trash"></i>
                                <span class="d-none d-lg-inline">Borrar</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background: #fff; padding: 24px 16px; border-radius: 0 0 20px 20px;">
                    <div class="row">
                        <div class="form-group col-md-2" style="margin:  1rem 0;">
                            <label for="">Almacen</label>
                            <select name="almacenSelect" id="almacenSelect" class="form-control"
                                @change="changeAlmacen($event)" v-model="almacen">
                                <option value="1">Almacen 1</option>
                                <option value="2">Almacen 2</option>
                                <option value="3">Almacen 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Codigo</th>
                                    <th>Nombre Del Repuesto</th>
                                    <th>Unidades</th>
                                    <th>Precios </th>
                                    <th>stock</th>
                                    <th>Editar</th>
                                    <th>Eliminar <input type="checkbox" class='btnSeleccionarTodos'> </th>
                                </tr>
                            </thead>
                            <tbody id='tbodyRepuestos'>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal precios -->
    <?php include __DIR__ . '/../modals/repuesto-modal-precios.php' ?>

    <!-- Modal Aumentar Stock de Repuestos -->
    <?php include __DIR__ . '/../modals/repuesto-modal-aumentar-stock.php' ?>

    <!-- Modal Disminuir Stock de Repuestos -->
    <?php include __DIR__ . '/../modals/repuesto-modal-disminuir-stock.php' ?>

    <!-- Modal Traslado Entre Almacenes - Repuestos -->
    <?php include __DIR__ . '/../modals/repuesto-modal-traslado-almacenes.php' ?>

    <!-- Modal Historial Stock - Repuestos -->
    <?php include __DIR__ . '/../modals/repuesto-modal-historial-stock.php' ?>

    <!-- Modal para agregar repuesto -->
    <?php include __DIR__ . '/../modals/repuesto-modal-add.php' ?>


    <!-- Modal para editar repuesto -->
    <?php include __DIR__ . '/../modals/repuesto-modal-edt.php' ?>

    <!-- modal restock -->

    <?php include __DIR__ . '/../modals/repuesto-modal-restock.php' ?>

    <!-- importar repuestos -->
    <?php include __DIR__ . '/../modals/repuesto-modal-importar.php' ?>

    <!-- lista de repuestos -->
    <?php include __DIR__ . '/../modals/repuesto-modal-lista.php' ?>
    <!-- product-modal-codigo-barras -->

    <?php include __DIR__ . '/../modals/product-modal-codigo-barras.php' ?>


</div>
<script>
    function descarFunccc() {
        window.open(_URL +
            `/reporte/repuesto/excel?texto=${$("#datatable_filter input").val()}`)
    }

    var codRepT = ''
    async function printBarcode() {
        try {
            const printer = await EscPosPrinter.requestPrinter();

            // Conectar a la impresora
            await printer.connect();

            // Configurar el tamaño del ticket (50 mm x 25 mm)
            await printer.setPageFormat(50, 25);

            // Imprimir el título
            await printer.printText('Barcode Title\n');

            // Generar el código de barras utilizando JsBarcode
            const svgData = JsBarcode.generateSvg('123456789', {
                format: 'CODE128',
                displayValue: true,
            });

            // Imprimir el código de barras
            await printer.printImage(svgData);

            // Cortar el ticket
            await printer.cut();

            // Desconectar la impresora
            await printer.disconnect();
        } catch (error) {
            console.error(error);
        }
    }

    function imprimir2() {
        window.open(_URL + "/ge/bar/code2?code=" + codeBarraTemps + "&nombre=" + nombreBarraTemps + "&scal=" + $("#scalimg").val(), "_blank");
    }

    function imprimir() {
        window.open(_URL + "/ge/bar/code?code=" + codeBarraTemps + "&nombre=" + nombreBarraTemps + "&scal=" + $("#scalimg").val(), "_blank");
    }

    function abrirModalBarras(e, n = '') {
        e = e.trim();
        console.log(e);
        nombreBarraTemps = n;
        codeBarraTemps = e;

        // Primero mostrar el modal
        $('#modalCodigoBarras').modal('show');

        // Esperar a que el modal esté completamente visible antes de generar el código de barras
        $('#modalCodigoBarras').on('shown.bs.modal', function () {
            setTimeout(function () {
                try {
                    JsBarcode("#idCodigoBarras", e);
                } catch (error) {
                    console.error("Error al generar código de barras:", error);
                }
            }, 100);
        });
    }
    function clearSelection() {
        // Limpiar array de IDs seleccionados
        arrayIdsOkUsar = [];

        // Desmarcar todos los checkboxes
        $('.btnCheckEliminar').prop('checked', false);
        $('.btnSeleccionarTodos').prop('checked', false);

        // Eliminar datos guardados en localStorage
        localStorage.removeItem('idChecks');
    }
    var nombreBarraTemps = ''
    var codeBarraTemps = ''
    var datatable
    var almacenCod = '<?php echo $_SESSION['sucursal'] ?>'
    var app;
    $(document).ready(function () {
        app = new Vue({
            el: "#conte-vue-modals",
            data: {

                almacen: <?php echo $_SESSION['sucursal'] ?>,
                t: 0,
                listaRep: [],
                almacenImportacionRep: 1, // Almacén por defecto para importación de repuestos
                categorias: [],
                unidades: [],
                subcategorias: [],
                subcategoriasEdit: [],
                precios: [],
                preciosNuevos: [],
                restock: {
                    cod: '',
                    cantidad: '',
                },
                reg: {
                    nombre: '',
                    precio: '0',
                    costo: '0',
                    cantidad: '0',
                    codSunat: '',
                    afecto: '0',
                    ruc: '',
                    razon: '',
                    precio1: 0,
                    precio2: 0,
                    precio3: 0,
                    precio4: 0,
                    codigo: 0,
                    imagen: '',
                    detalle: '',
                    categoria: '',
                    almacen: 1,
                    unidad: '',
                    subcategoria: '',
                    moneda: 'PEN',
                    usar_multiprecio: false,
                    usar_barra: '0', // AGREGADO: Campo para usar código de barras
                },
                edt: {
                    nombre: '',
                    cod_rep: '',
                    cod: '',
                    precio: '0',
                    costo: '0',
                    codSunat: '',
                    afecto: '0',
                    usar_barra: '0',
                    ruc: '',
                    razon: '',
                    precioMayor: '',
                    precioMenor: '',
                    precio2: '',
                    precio3: '',
                    precio4: '',
                    precio_unidad: '',
                    codigo: '',
                    cantidad: '',
                    detalle: '',
                    almacen: '',
                    unidad: '',
                    categoria: '',
                    subcategoria: '',
                    moneda: 'PEN',
                    usar_multiprecio: false,
                },
                listaIdsss: [],
                stockData: {
                    repuesto_id: '',
                    stock_actual: '',
                    cantidad_ingresar: '',
                    repuesto_nombre: ''
                },
                disminuirDataRepuesto: {
                    repuesto_id: '',
                    stock_actual: '',
                    cantidad_disminuir: '',
                    repuesto_nombre: '',
                    unidad: '',
                    observaciones: ''
                },
                trasladoDataRepuesto: {
                    almacen_origen: '',
                    almacen_destino: '',
                    repuesto_id: '',
                    repuestos: [],
                    nota: ''
                },
            },
            computed: {
                // Símbolos de moneda para el formulario de agregar
                simboloMonedaReg() {
                    return this.reg.moneda === 'USD' ? '$' : 'S/';
                },
                // Símbolos de moneda para el formulario de editar
                simboloMonedaEdt() {
                    return this.edt.moneda === 'USD' ? '$' : 'S/';
                }
            },

            methods: {
                agregarPrecio() {
                    this.precios.push({
                        nombre: '',
                        precio: '0.00'
                    });
                },

                eliminarPrecio(index) {
                    this.precios.splice(index, 1);
                },

                // Métodos para el modal de agregar
                agregarPrecioNuevo() {
                    this.preciosNuevos.push({
                        nombre: '',
                        precio: '0.00'
                    });
                },

                eliminarPrecioNuevo(index) {
                    this.preciosNuevos.splice(index, 1);
                },
                cargarUnidades() {
                    $.get(_URL + "/ajs/get/unidades/rep", (data) => {
                        this.unidades = JSON.parse(data);
                    }).fail((jqXHR, textStatus, errorThrown) => {
                        console.error("Error al cargar las unidades: " + textStatus, errorThrown);
                        alert("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
                    });
                },
                cargarCategorias() {
                    $.get(_URL + "/ajs/get/categorias/rep", (data) => {
                        this.categorias = JSON.parse(data);
                    }).fail((jqXHR, textStatus, errorThrown) => {
                        console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                        alert("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
                    });
                },
                cargarSubcategorias() {
                    if (!this.reg.categoria) {
                        this.subcategorias = [];
                        return;
                    }

                    $.post(_URL + "/ajs/get/subcategorias/rep/by-categoria", {
                        categoria_id: this.reg.categoria
                    }, (data) => {
                        this.subcategorias = JSON.parse(data);
                        if (this.subcategorias.length === 0) {
                            this.reg.subcategoria = '';
                        }
                    }).fail((jqXHR, textStatus, errorThrown) => {
                        console.error("Error al cargar las subcategorías: " + textStatus, errorThrown);
                        alert("No se pudo cargar las subcategorías. Por favor, intenta nuevamente.");
                    });
                },
                cargarSubcategoriasEdit() {
                    if (!this.edt.categoria) {
                        this.subcategoriasEdit = [];
                        return;
                    }

                    $.post(_URL + "/ajs/get/subcategorias/rep/by-categoria", {
                        categoria_id: this.edt.categoria
                    }, (data) => {
                        this.subcategoriasEdit = JSON.parse(data);

                        // Si hay una subcategoría seleccionada previamente, mantenerla seleccionada
                        if (this.edt.subcategoria && this.subcategoriasEdit.length > 0) {
                            // Verificar si la subcategoría existe en las opciones cargadas
                            const subcategoriaExiste = this.subcategoriasEdit.some(
                                subcat => subcat.id == this.edt.subcategoria
                            );

                            // Si no existe, limpiar la selección
                            if (!subcategoriaExiste) {
                                this.edt.subcategoria = '';
                            }
                        } else if (this.subcategoriasEdit.length === 0) {
                            this.edt.subcategoria = '';
                        }
                    }).fail((jqXHR, textStatus, errorThrown) => {
                        console.error("Error al cargar las subcategorías: " + textStatus, errorThrown);
                        alert("No se pudo cargar las subcategorías. Por favor, intenta nuevamente.");
                    });
                },
                agregarIds() {
                    this.t = 5
                    console.log(this.listaIdsss);
                    this.listaIdsss.push({
                        id: 20
                    })
                    console.log(this.listaIdsss);
                },
                agregarPrecios() {
                    const data = {
                        ...this.edt
                    }
                    _ajax("/ajs/data/repuesto/edt/precios", "POST", data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                alertExito("Actualizado")
                                    .then(function () {
                                        location.reload()
                                    })
                            } else {
                                alertAdvertencia("No se pudo actualizar")
                            }
                        }
                    )
                },
                changeAlmacen(event) {
                    clearSelection();


                    $('.filter-option').prop('checked', false);



                    almacenCod = event.target.value;

                    if ($.fn.DataTable.isDataTable('#datatable')) {
                        datatable.destroy();
                    }


                    datatable = $("#datatable").DataTable({
                        order: [[0, 'ASC']],
                        "processing": true,
                        "serverSide": true,
                        "sAjaxSource": _URL + "/ajs/server/sider/repuestos",
                        "language": {
                            "sProcessing": "Procesando...",
                            "sLengthMenu": "Mostrar _MENU_ registros",
                            "sZeroRecords": "No se encontraron resultados",
                            "sEmptyTable": "Ningún dato disponible en esta tabla",
                            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                            "sInfoPostFix": "",
                            "sSearch": "Buscar:",
                            "sUrl": "",
                            "sInfoThousands": ",",
                            "sLoadingRecords": "Cargando...",
                            "oPaginate": {
                                "sFirst": "Primero",
                                "sLast": "Último",
                                "sNext": "Siguiente",
                                "sPrevious": "Anterior"
                            },
                            "oAria": {
                                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                            },
                            "buttons": {
                                "copy": "Copiar",
                                "colvis": "Visibilidad"
                            }
                        },
                        "fnServerParams": function (aoData) {
                            aoData.push(
                                { "name": "almacenId", "value": almacenCod },
                                {
                                    "name": "filter", "value": $('#maquinas').prop('checked') ? 'JVC' :
                                        $('#implementos').prop('checked') ? 'IMPLE' :
                                            $('#rep').prop('checked') ? 'REP' : ''
                                }
                            );
                        },
                        columnDefs: [
                            {
                                "targets": [0, 1],
                                "className": "text-center"
                            },
                            {
                                "targets": 0,
                                "render": function (data, type, row, meta) {
                                    return '<a href="javascript:abrirModalBarras(\'' + row[0] + '\',\'' + row[0] + '\')">' + row[0] + '</a>';
                                }
                            },
                            {
                                "targets": [3],
                                "className": "text-center",
                                "render": function (data, type, row, meta) {
                                    // La moneda está en row[7] (índice 7 del array de columnas)
                                    const moneda = row[7] || 'PEN';
                                    const simbolo = moneda === 'USD' ? '$' : 'S/';
                                    return `${simbolo}${parseFloat(data || 0).toFixed(2)}`;
                                }
                            },
                            {
                                "targets": 5,
                                "render": function (data, type, row, meta) {
                                    return `<button data-item="${row[6]}" class="btn-edt btn btn-sm btn-info"><i class="fa fa-edit"></i></button>`;
                                }
                            },
                            {
                                "targets": 6,
                                "render": function (data, type, row, meta) {
                                    return `<input type="checkbox" data-id="${row[6]}" class="btnCheckEliminar">`;
                                }
                            }
                        ],
                        "drawCallback": function (settings) {
                            $("#datatable_processing").hide();
                        },
                        "error": function (xhr, error, thrown) {
                            console.log('Error en DataTables:', error);
                            $("#datatable_processing").hide();
                        }
                    });
                },
                // AGREGADO: Método para generar código de barras en modal de agregar
                generarCodeBarraAdd() {
                    this.$nextTick(() => {
                        setTimeout(() => {
                            if (document.getElementById("barcodeAdd") && this.reg.codigo) {
                                try {
                                    JsBarcode("#barcodeAdd", this.reg.codigo);
                                } catch (error) {
                                    console.error("Error al generar código de barras:", error);
                                }
                            }
                        }, 100);
                    });
                },
                handleUsarBarraChange() {
                    console.log("Cambio en usar_barra:", this.edt.usar_barra);

                    if (this.edt.usar_barra === '1' && this.edt.codigo) {
                        // Generar código de barras
                        this.$nextTick(() => {
                            this.edtGenerarCodeBarra();
                        });
                    } else {
                        // Limpiar código de barras cuando se desactiva
                        this.$nextTick(() => {
                            const barcodeElement = document.getElementById("barcodeEdit");
                            if (barcodeElement) {
                                barcodeElement.src = "";
                                barcodeElement.style.display = "none";
                            }
                        });
                    }
                },
                edtGenerarCodeBarra() {
                    console.log("Intentando generar código de barras para:", this.edt.codigo);

                    const barcodeElement = document.getElementById("barcodeEdit");
                    if (!barcodeElement) {
                        console.warn("Elemento #barcodeEdit no encontrado");
                        return;
                    }

                    if (!this.edt.codigo) {
                        console.warn("No hay código para generar");
                        return;
                    }

                    try {
                        JsBarcode("#barcodeEdit", this.edt.codigo, {
                            format: "CODE128",
                            width: 2,
                            height: 50,
                            displayValue: true
                        });

                        // AGREGAR: Asegurar que la imagen sea visible
                        barcodeElement.style.display = "block";

                        console.log("Código de barras generado exitosamente");
                    } catch (error) {
                        console.error("Error al generar código de barras:", error);
                    }
                },
                // AGREGADO: Método para manejar el cambio de usar_barra en editar
                toggleBarcodeEdit() {
                    this.$nextTick(() => {
                        if (this.edt.usar_barra === '1' && this.edt.codigo) {
                            // Generar código de barras si se activa
                            setTimeout(() => {
                                this.edtGenerarCodeBarra();
                            }, 100);
                        }
                    });
                },
                agregarListaImport() {
                    if (this.listaRep.length > 0) {
                        _ajax("/ajs/data/repuesto/add/lista", "POST", {
                            lista: JSON.stringify(this.listaRep),
                            almacen: this.almacenImportacionRep
                        },
                            function (resp) {
                                console.log(resp);
                                if (resp.res) {
                                    alertExito("Agregado")
                                        .then(function () {
                                            location.reload()
                                        })
                                } else {
                                    alertAdvertencia("No se pudo Agregar")
                                }
                            }
                        )
                    } else {
                        alertAdvertencia("La lista esta vacia")
                    }
                },
                ChangeconsultarDocRUC() {
                    if (this.reg.ruc.length == 11) {
                        this.getInfoDoc2();
                    } else {
                        this.reg.ruc = ''
                    }
                },
                consultarDocRUC() {
                    if (this.reg.ruc.length == 11) {
                        this.getInfoDoc2();
                    } else if (this.edt.ruc.length == 11) {
                        this.getInfoDoc3();
                    } else {
                        alertAdvertencia("El RUC es de 11 dígitos")
                    }
                },
                getInfoDoc2() {
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/cliente", "POST", {
                        doc: this.reg.ruc
                    },
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                app._data.reg.razon = resp.data.razon_social;
                                app._data.reg.direccion = resp.data.direccion;
                                app._data.reg.distrito = resp.data.distrito;
                                app._data.reg.provincia = resp.data.provincia;
                                app._data.reg.departamento = resp.data.departamento;
                                app._data.reg.ubigeo = resp.data.ubigeo;
                            } else {
                                alertAdvertencia("Documento no encontrado")
                            }
                        }
                    )
                },
                getInfoDoc3() {
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/cliente", "POST", {
                        doc: this.edt.ruc
                    },
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                app._data.edt.razon = resp.data.razon_social;
                                app._data.edt.direccion = resp.data.direccion;
                                app._data.edt.distrito = resp.data.distrito;
                                app._data.edt.provincia = resp.data.provincia;
                                app._data.edt.departamento = resp.data.departamento;
                                app._data.edt.ubigeo = resp.data.ubigeo;
                            } else {
                                alertAdvertencia("Documento no encontrado")
                            }
                        }
                    )
                },
                eliminarItemTablaRep(index) {
                    this.listaRep.splice(index, 1)
                },
                agregarStock() {
                    const data = {
                        ...this.restock
                    }
                    _ajax("/ajs/data/repuesto/restock", "POST", data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                alertExito("Actualizado")
                                    .then(function () {
                                        location.reload()
                                    })
                            } else {
                                alertAdvertencia("No se pudo actualizar")
                            }
                        }
                    )
                },
                actualizarRep() {
                    let formData = new FormData();

                    formData.append('cod', this.edt.cod);
                    formData.append('nombre', this.edt.nombre);
                    formData.append('codigo', this.edt.codigo);
                    formData.append('detalle', this.edt.detalle);
                    formData.append('categoria', this.edt.categoria);
                    formData.append('unidad', this.edt.unidad);
                    formData.append('moneda', this.edt.moneda);
                    formData.append('precio', this.edt.precio);
                    formData.append('costo', this.edt.costo);
                    formData.append('almacen', this.edt.almacen);
                    formData.append('codSunat', this.edt.codSunat);
                    formData.append('afecto', this.edt.afecto);
                    formData.append('usar_barra', this.edt.usar_barra);
                    formData.append('precioMayor', this.edt.precioMayor);
                    formData.append('precioMenor', this.edt.precioMenor);
                    formData.append('precio3', this.edt.precio3);
                    formData.append('precio4', this.edt.precio4);
                    formData.append('cantidad', this.edt.cantidad);
                    formData.append('razon', 1);
                    formData.append('ruc', 1);
                    formData.append('usar_multiprecio', this.edt.usar_multiprecio ? '1' : '0');
                    formData.append('subcategoria', this.edt.subcategoria);

                    $.ajax({
                        url: _URL + '/ajs/data/repuesto/edt',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            // Si usa multiprecio, guardar los precios
                            if (app._data.edt.usar_multiprecio) {
                                _ajax("/ajs/data/repuesto/guardar/precios", "POST", {
                                    id_repuesto: app._data.edt.cod,
                                    precios: app._data.precios
                                }, function (resp) {
                                    if (resp.res) {
                                        alertExito("Repuesto actualizado exitosamente").then(() => {
                                            $("#modal-edt-rep").modal("hide");
                                            datatable.ajax.reload(null, false);
                                        });
                                    } else {
                                        alertAdvertencia("Error al guardar los precios");
                                    }
                                });
                            } else {
                                alertExito("Repuesto actualizado exitosamente").then(() => {
                                    $("#modal-edt-rep").modal("hide");
                                    datatable.ajax.reload(null, false);
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error en la solicitud:", error);
                            alert("Hubo un error al intentar actualizar el repuesto.");
                        }
                    });
                },
                onImageChange(event) {
                    const file = event.target.files[0];
                    this.reg.imagen = file;
                },
                agregarRep() {
                    const formData = new FormData();
                    formData.append('nombre', this.reg.nombre);
                    formData.append('precio', this.reg.precio);
                    formData.append('costo', this.reg.costo);
                    formData.append('cantidad', this.reg.cantidad);
                    formData.append('codSunat', this.reg.codSunat);
                    formData.append('afecto', this.reg.afecto);
                    formData.append('ruc', this.reg.ruc);
                    formData.append('razon', this.reg.razon);
                    formData.append('precio1', this.reg.precio1);
                    formData.append('precio2', this.reg.precio2);
                    formData.append('precio3', this.reg.precio3);
                    formData.append('precio4', this.reg.precio4);
                    formData.append('codigo', this.reg.codigo);
                    formData.append('imagen', this.reg.imagen);
                    formData.append('detalle', this.reg.detalle);
                    formData.append('categoria', this.reg.categoria);
                    formData.append('subcategoria', this.reg.subcategoria);
                    formData.append('almacen', this.reg.almacen);
                    formData.append('unidad', this.reg.unidad);
                    formData.append('moneda', this.reg.moneda);
                    formData.append('usar_multiprecio', this.reg.usar_multiprecio ? '1' : '0');
                    formData.append('usar_barra', this.reg.usar_barra); // AGREGADO

                    // Agregar los precios si usa multiprecio
                    if (this.reg.usar_multiprecio) {
                        formData.append('precios', JSON.stringify(this.preciosNuevos));
                    }

                    fetch(_URL + '/ajs/data/repuesto/add', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(resp => {
                            if (resp.res) {
                                alertExito("Agregado").then(() => {
                                    $("#modal-add-rep").modal("hide");
                                    datatable.ajax.reload(null, false);
                                    this.preciosNuevos = [];
                                });
                            } else {
                                alertAdvertencia("No se pudo agregar");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alertAdvertencia("Error en la solicitud");
                        });
                },
                setInfo(data) {
                    $("#modal-edt-rep").modal("show");

                    console.log("Datos recibidos:", data);
                    console.log("Valor de usar_multiprecio en la base de datos:", data.usar_multiprecio);
                    console.log("Tipo de dato:", typeof data.usar_multiprecio);

                    // Convertir el valor de usar_multiprecio correctamente
                    this.edt = {
                        usar_multiprecio: data.usar_multiprecio === '1' || data.usar_multiprecio === 1
                    };

                    this.edt = {
                        ...this.edt,
                        usar_barra: data.usar_barra || '0'
                    };
                    this.edt.usar_barra = data.usar_barra || '0'; // AGREGADO
                    this.edt.cod = data.id_repuesto;
                    this.edt.nombre = data.nombre;
                    this.edt.precio = data.precio;
                    this.edt.costo = parseFloat(data.costo + "").toFixed(2);
                    this.edt.codSunat = data.codsunat;
                    this.edt.afecto = data.iscbp;
                    this.edt.precioMayor = data.precio_mayor;
                    this.edt.precioMenor = data.precio_menor;
                    this.edt.precio2 = data.precio2;
                    this.edt.precio3 = data.precio3;
                    this.edt.precio4 = data.precio4;
                    this.edt.razon = data.razon_social;
                    this.edt.ruc = data.ruc;
                    this.edt.codigo = data.codigo;
                    this.edt.cantidad = data.cantidad;
                    this.edt.detalle = data.detalle;
                    this.edt.almacen = data.almacen;
                    this.edt.unidad = data.unidad;
                    this.edt.categoria = data.categoria;
                    this.edt.moneda = data.moneda;
                    this.cargarSubcategoriasEdit();

                    console.log("DEBUG - usar_barra recibido:", data.usar_barra);
                    console.log("DEBUG - usar_barra asignado:", this.edt.usar_barra);
                    console.log("DEBUG - tipo de dato:", typeof this.edt.usar_barra);
                    // si el repuesto tiene una subcategoria guardada, cargarla
                    if (data.subcategoria) {
                        this.edt.subcategoria = data.subcategoria;
                    }

                    $.get(_URL + "/ajs/get/unidades/rep", function (data, textStatus, jqXHR) {
                        let option = '';
                        let resp = JSON.parse(data);
                        $.each(resp, function (i, v) {
                            console.log(v.id);
                            option += `<option value="${v.id}">${v.nombre}</option>`;
                        });
                        $('#unidades-edt').html(option);
                        $('#unidades-edt').val(app._data.edt.unidad); // Establecer el valor de la unidad
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.error("Error al cargar las unidades: " + textStatus, errorThrown);
                        alert("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
                    });
                    // categoria
                    $.get(_URL + "/ajs/get/categorias/rep", function (data, textStatus, jqXHR) {
                        let option = '';
                        let resp = JSON.parse(data);
                        $.each(resp, function (i, v) {
                            console.log(v.id);
                            option += `<option value="${v.id}">${v.nombre}</option>`;
                        });
                        $('#categorias-edt').html(option);
                        $('#categorias-edt').val(app._data.edt.categoria);
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.error("Error al cargar las categorias: " + textStatus, errorThrown);
                        alert("No se pudo cargar las categorias. Por favor, intenta nuevamente.");
                    });

                    this.$nextTick(() => {
                        const barcodeElement = document.getElementById("barcodeEdit");
                        if (barcodeElement) {
                            barcodeElement.src = "";
                            barcodeElement.style.display = "none";
                        }
                    });

                    // Cargar los precios si usa multiprecio
                    if (this.edt.usar_multiprecio) {
                        _ajax("/ajs/data/repuesto/obtener/precios", "POST", {
                            id_repuesto: data.id_repuesto
                        }, function (resp) {
                            if (resp.res) {
                                app._data.precios = resp.precios;
                            } else {
                                app._data.precios = [];
                            }
                        });
                    } else {
                        this.precios = [];
                    }
                },
                onlyNumber($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                        $event.preventDefault();
                    }
                },
                aumentarStockRepuesto() {
                    if (!this.stockData.repuesto_id) {
                        alertAdvertencia("Debe seleccionar un repuesto");
                        return;
                    }

                    if (!this.stockData.cantidad_ingresar || this.stockData.cantidad_ingresar <= 0) {
                        alertAdvertencia("Debe ingresar una cantidad válida");
                        return;
                    }

                    const data = {
                        repuesto_id: this.stockData.repuesto_id,
                        cantidad: this.stockData.cantidad_ingresar
                    };

                    _ajax("/ajs/data/repuesto/aumentar/stock", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Stock aumentado exitosamente").then(() => {
                                $("#modal-aumentar-stock").modal("hide");
                                datatable.ajax.reload(null, false);
                                // Limpiar formulario
                                app._data.stockData = {
                                    repuesto_id: '',
                                    stock_actual: '',
                                    cantidad_ingresar: '',
                                    repuesto_nombre: ''
                                };
                                $('#buscar-repuesto-stock').val('');
                            });
                        } else {
                            alertAdvertencia("Error al aumentar el stock");
                        }
                    });
                },
                disminuirStockRepuesto() {
                    if (!this.disminuirDataRepuesto.repuesto_id) {
                        alertAdvertencia("Debe seleccionar un repuesto");
                        return;
                    }

                    if (!this.disminuirDataRepuesto.cantidad_disminuir || this.disminuirDataRepuesto.cantidad_disminuir <= 0) {
                        alertAdvertencia("Debe ingresar una cantidad válida");
                        return;
                    }

                    if (this.disminuirDataRepuesto.cantidad_disminuir > this.disminuirDataRepuesto.stock_actual) {
                        alertAdvertencia("La cantidad a disminuir no puede ser mayor al stock actual");
                        return;
                    }

                    const data = {
                        repuesto_id: this.disminuirDataRepuesto.repuesto_id,
                        cantidad: this.disminuirDataRepuesto.cantidad_disminuir,
                        observaciones: this.disminuirDataRepuesto.observaciones
                    };

                    _ajax("/ajs/data/repuesto/disminuir/stock", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Stock disminuido exitosamente").then(() => {
                                $("#modal-disminuir-stock-repuesto").modal("hide");
                                datatable.ajax.reload(null, false);
                                app._data.disminuirDataRepuesto = {
                                    repuesto_id: '',
                                    stock_actual: '',
                                    cantidad_disminuir: '',
                                    repuesto_nombre: '',
                                    unidad: '',
                                    observaciones: ''
                                };
                                $('#buscar-repuesto-disminuir').val('');
                            });
                        } else {
                            alertAdvertencia("Error al disminuir el stock");
                        }
                    });
                },
                limpiarRepuestoTraslado() {
                    $('#buscar-repuesto-traslado').val('');
                    this.trasladoDataRepuesto.repuesto_id = '';
                },
                eliminarRepuestoTraslado(index) {
                    this.trasladoDataRepuesto.repuestos.splice(index, 1);
                },
                realizarTrasladoRepuesto() {
                    if (!this.trasladoDataRepuesto.almacen_origen) {
                        alertAdvertencia("Debe seleccionar el almacén de origen");
                        return;
                    }

                    if (!this.trasladoDataRepuesto.almacen_destino) {
                        alertAdvertencia("Debe seleccionar el almacén de destino");
                        return;
                    }

                    if (this.trasladoDataRepuesto.almacen_origen === this.trasladoDataRepuesto.almacen_destino) {
                        alertAdvertencia("El almacén de origen y destino no pueden ser el mismo");
                        return;
                    }

                    if (this.trasladoDataRepuesto.repuestos.length === 0) {
                        alertAdvertencia("Debe agregar al menos un repuesto");
                        return;
                    }

                    const data = {
                        almacen_origen: this.trasladoDataRepuesto.almacen_origen,
                        almacen_destino: this.trasladoDataRepuesto.almacen_destino,
                        repuestos: this.trasladoDataRepuesto.repuestos,
                        nota: this.trasladoDataRepuesto.nota
                    };

                    _ajax("/ajs/data/repuesto/traslado/almacenes", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Traslado realizado exitosamente").then(() => {
                                $("#modal-traslado-almacenes-repuesto").modal("hide");
                                datatable.ajax.reload(null, false);
                                app._data.trasladoDataRepuesto = {
                                    almacen_origen: '',
                                    almacen_destino: '',
                                    repuesto_id: '',
                                    repuestos: [],
                                    nota: ''
                                };
                                $('#buscar-repuesto-traslado').val('');
                            });
                        } else {
                            alertAdvertencia(resp.error || "Error al realizar el traslado");
                        }
                    });
                },
                cargarHistorialStock() {
                    // Destruir DataTable si ya existe
                    if ($.fn.DataTable.isDataTable('#tabla-historial-stock-repuesto')) {
                        const table = $('#tabla-historial-stock-repuesto').DataTable();
                        table.destroy();
                        $('#tabla-historial-stock-repuesto').empty();
                    }

                    // Reconstruir la estructura de la tabla
                    $('#tabla-historial-stock-repuesto').html(`
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Repuesto</th>
                                    <th>Movimiento</th>
                                    <th>Cantidad</th>
                                    <th>Costo</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        `);

                    // Inicializar DataTable
                    const table = $('#tabla-historial-stock-repuesto').DataTable({
                        ajax: {
                            url: _URL + "/ajs/data/repuesto/historial/stock",
                            type: "POST",
                            dataSrc: "data"
                        },
                        columns: [
                            { data: "codigo" },
                            { data: "repuesto_nombre" },
                            {
                                data: "tipo_movimiento",
                                render: function (data) {
                                    const clase = data === 'INGRESO' ? 'bg-success' : 'bg-danger';
                                    return `<span class="badge ${clase}">${data}</span>`;
                                }
                            },
                            { data: "cantidad" },
                            {
                                data: "costo_compra",
                                render: function (data) {
                                    return data ? 'S/ ' + parseFloat(data).toFixed(2) : '<span class="text-muted">-</span>';
                                }
                            },
                            {
                                data: "fecha_movimiento",
                                render: function (data) {
                                    const fecha = new Date(data);
                                    const dia = String(fecha.getDate()).padStart(2, '0');
                                    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                                    const anio = fecha.getFullYear();
                                    const hora = String(fecha.getHours()).padStart(2, '0');
                                    const min = String(fecha.getMinutes()).padStart(2, '0');
                                    return `${dia}/${mes}/${anio} ${hora}:${min}`;
                                }
                            },
                            { data: "usuario" },
                            {
                                data: "observaciones",
                                render: function (data) {
                                    if (!data) return '<span class="text-muted">-</span>';
                                    if (data.length > 50) {
                                        return `<span title="${data}">${data.substring(0, 50)}...</span>`;
                                    }
                                    return data;
                                }
                            }
                        ],
                        language: {
                            url: "ServerSide/Spanish.json"
                        },
                        order: [[5, 'desc']], // Ordenar por fecha descendente
                        pageLength: 15,
                        lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "Todos"]],
                        responsive: true,
                        dom: '<"row mb-2"<"col-12 col-sm-6 mb-2 mb-sm-0"B><"col-12 col-sm-6"f>>' +
                            '<"row"<"col-12"tr>>' +
                            '<"row mt-2"<"col-12 col-sm-5 mb-2 mb-sm-0"l><"col-12 col-sm-2 text-center mb-2 mb-sm-0"i><"col-12 col-sm-5"p>>',
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel"></i> Excel',
                                className: 'btn btn-success btn-sm me-1',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fa fa-file-pdf"></i> PDF',
                                className: 'btn btn-danger btn-sm',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            }
                        ]
                    });
                }
            },
            mounted() {
                this.cargarCategorias();
                this.cargarUnidades();

            }

        })

        datatable = $("#datatable").DataTable({
            order: [[0, 'ASC']],
            "processing": true,
            "serverSide": true,
            "sAjaxSource": _URL + "/ajs/server/sider/repuestos",
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            "fnServerParams": function (aoData) {
                aoData.push(
                    { "name": "almacenId", "value": almacenCod },
                    {
                        "name": "filter", "value": $('#maquinas').prop('checked') ? 'JVC' :
                            $('#implementos').prop('checked') ? 'IMPLE' :
                                $('#rep').prop('checked') ? 'REP' : ''
                    }
                );
            },
            "drawCallback": function (settings) {
                $("#datatable_processing").hide();

                setTimeout(() => {
                    if (localStorage.getItem('isChecks')) {
                        restoreCheckboxStates();
                    }
                }, 100)
            },
            columnDefs: [
                {
                    "targets": [0, 1],
                    "className": "text-center"
                },
                {
                    "targets": 0,
                    "render": function (data, type, row, meta) {
                        return '<a href="javascript:abrirModalBarras(\'' + row[0] + '\',\'' + row[0] + '\')">' + row[0] + '</a>';
                    }
                },
                {
                    "targets": [3],
                    "className": "text-center",
                    "render": function (data, type, row, meta) {
                        // La moneda está en row[7] (índice 7 del array de columnas)
                        const moneda = row[7] || 'PEN';
                        const simbolo = moneda === 'USD' ? '$' : 'S/';
                        return `${simbolo}${parseFloat(data || 0).toFixed(2)}`;
                    }
                },
                {
                    "targets": 5,
                    "render": function (data, type, row, meta) {
                        return `<button data-item="${row[6]}" class="btn-edt btn btn-sm btn-info"><i class="fa fa-edit"></i></button>`;
                    }
                },
                {
                    "targets": 6,
                    "render": function (data, type, row, meta) {
                        return `<input type="checkbox" data-id="${row[6]}" class="btnCheckEliminar">`;
                    }
                }
            ]
        });

        $('.filter-option').on('change', function () {
            $('.filter-option').not(this).prop('checked', false);
            datatable.ajax.reload();
        });

        $("#file-import-exel").change(function () {
            console.log("Importando Excel")
            if ($("#file-import-exel").val().length > 0) {
                var fd = new FormData();
                fd.append('file', $("#file-import-exel")[0].files[0]);
                $.ajax({
                    type: 'POST',
                    url: _URL + '/ajs/data/repuesto/add/exel',
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        console.log('inicio');
                        $("#loader-menor").show();
                    },
                    error: function (err) {
                        $("#loader-menor").hide();
                        console.log(err);
                    },
                    success: function (resp) {
                        $("#loader-menor").hide();
                        console.log(resp);
                        resp = JSON.parse(resp)
                        if (resp.res) {
                            var bloc = true;
                            var listaTemp = [];
                            resp.data.forEach(function (el) {
                                console.log(el);
                                if (!bloc) {
                                    listaTemp.push({
                                        repuesto: el[0],
                                        descripcicon: el[1],
                                        cantidad: el[2],
                                        costo: el[3],
                                        precio_unidad: el[4] ?? 0,
                                        precio: el[5] ?? 0,
                                        precio2: el[6] ?? 0,
                                        almacen: el[7],
                                        afecto: false,
                                        codigoRep: el[8]
                                    })
                                }
                                bloc = false
                            })
                            app._data.listaRep = listaTemp
                            $("#importarModal").modal("hide")
                            $("#modal-lista-repuestos").modal("show")
                        } else {
                            alertAdvertencia("No se pudo subir el Archivo")
                        }
                        $("#file-import-exel").val("")
                    }
                })
            }
        })
        var arrayIdsOkUsar = []

        $("#datatable").on("click", ".btn-re-stock", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            app._data.restock.cod = cod
            app._data.restock.cantidad = ''
            $("#modal-restock").modal("show");
        })
        $("#generarreporteRep").click(() => {
            console.log("Generando reporte de repuesto")
            const anioREd = $("#anioreporEFG").val()
            const messREd = parseInt($("#mesreprEFG").val())
            const diaRed = $("#diareporEfghg").val().length > 0 ? parseInt($("#diareporEfghg").val()) : 'nn'
            window.open(_URL +
                `/reporte/repuestos/pdf/${codRepT}?fecha=${anioREd}${messREd}-${diaRed}`)
            $("#modal-repEreport").modal("hide");
        })

        $("#datatable").on("click", ".btn-reporte", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            codRepT = cod
            $("#modal-repEreport").modal("show");
        })

        $("#datatable").on("click", ".btn-ver-precios", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            console.log(cod);
            $("#modal-precios").modal("show");
            _ajax("/ajs/cargar/repuestos/precios", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    $("#modal-precios").modal("show");
                    app._data.edt.precio = resp.precio == null ? parseFloat(0 + "").toFixed(2) : resp.precio
                    app._data.edt.precio2 = resp.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio2 + "").toFixed(2)
                    app._data.edt.precio3 = resp.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio3 + "").toFixed(2)
                    app._data.edt.precio4 = resp.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio4 + "").toFixed(2)
                    app._data.edt.precio_unidad = resp.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio_unidad + "").toFixed(2)

                    $('#precio1').val(resp.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio + "").toFixed(2))
                    $('#precio2').val(resp.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio2 + "").toFixed(2))
                    $('#precio3').val(resp.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio3 + "").toFixed(2))
                    $('#precio4').val(resp.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio4 + "").toFixed(2))
                    $('#precio_unidad').val(resp.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio_unidad + "").toFixed(2))
                    app._data.edt.cod_rep = cod
                }
            )
        })

        $("#datatable").on("click", ".btn-edt", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            _ajax("/ajs/data/repuesto/info", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    if (resp.res) {
                        app.setInfo(resp.data)
                    } else {
                        alertAdvertencia("Informacion no encontrada")
                    }
                }
            )
        })


        $('#modal-edt-rep').on('shown.bs.modal', function () {
            app.$nextTick(() => {
                if (app.edt.usar_barra === '1' && app.edt.codigo) {
                    app.edtGenerarCodeBarra();
                } else {
                    // Limpiar si no debe mostrar código de barras
                    const barcodeElement = document.getElementById("barcodeEdit");
                    if (barcodeElement) {
                        barcodeElement.src = "";
                        barcodeElement.style.display = "none";
                    }
                }
            });
        });


        // Manejador para los checkboxes individuales con prevención de propagación
        $(document).on("click", ".btnCheckEliminar", function (e) {
            // Prevenir la propagación del evento
            e.stopPropagation();

            const id = $(this).attr("data-id");
            const isChecked = $(this).prop("checked");

            if (isChecked) {
                // Agregar al array si no existe
                if (!arrayIdsOkUsar.some(item => item.id === id)) {
                    arrayIdsOkUsar.push({ id: id });
                }
            } else {
                // Eliminar del array
                arrayIdsOkUsar = arrayIdsOkUsar.filter(item => item.id !== id);
                $('.btnSeleccionarTodos').prop('checked', false);
            }

            // Verificar si todos los checkboxes visibles están marcados
            const allChecked = $(".btnCheckEliminar:visible").length === $(".btnCheckEliminar:visible:checked").length;
            if (allChecked && $(".btnCheckEliminar:visible").length > 0) {
                $(".btnSeleccionarTodos").prop("checked", true);
            }

            // Guardar en localStorage
            if (arrayIdsOkUsar.length > 0) {
                localStorage.setItem("idChecks", JSON.stringify(arrayIdsOkUsar));
            } else {
                localStorage.removeItem("idChecks");
            }
        });

        // Manejador para el checkbox "Seleccionar Todos" con prevención de propagación
        $(".btnSeleccionarTodos").on("click", function (e) {
            // Prevenir la propagación del evento
            e.stopPropagation();

            const isChecked = $(this).prop("checked");

            // Limpiar array antes de agregar nuevos elementos
            arrayIdsOkUsar = [];

            if (isChecked) {
                // Si está marcado, seleccionar todos los checkboxes visibles
                $(".btnCheckEliminar:visible").each(function () {
                    // Marcar el checkbox
                    $(this).prop("checked", true);

                    // Obtener el ID y agregarlo al array
                    const id = $(this).attr("data-id");
                    if (id && !arrayIdsOkUsar.some(item => item.id === id)) {
                        arrayIdsOkUsar.push({ id: id });
                    }
                });
            } else {
                // Si está desmarcado, desmarcar todos los checkboxes
                $(".btnCheckEliminar:visible").prop("checked", false);
            }

            // Guardar en localStorage solo si hay elementos seleccionados
            if (arrayIdsOkUsar.length > 0) {
                localStorage.setItem("idChecks", JSON.stringify(arrayIdsOkUsar));
            } else {
                localStorage.removeItem("idChecks");
            }
        });

        $('.btnBorrar').click(function () {
            console.log(localStorage.getItem('idChecks'));
            let ids = localStorage.getItem('idChecks')
            if (!ids) {
                alertAdvertencia("Seleccione repuestos");
                return;
            }

            let arrayId = JSON.parse(ids)
            Swal.fire({
                title: 'Desea borrar estos repuestos?',
                showDenyButton: true,
                confirmButtonText: 'Si',
                denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    if (localStorage.getItem("idChecks") !== null) {
                        _ajax("/ajs/data/repuesto/delete", "POST", {
                            arrayId
                        },
                            function (resp) {
                                console.log(resp);
                                if (resp.res) {
                                    localStorage.removeItem('idChecks');
                                    Swal.fire('Buen trabajo',
                                        'Repuestos borrados exitosamente',
                                        'success', {}).then((result) => {
                                            // Limpiar checkboxes
                                            $('.btnCheckEliminar').prop('checked', false);
                                            $('.btnSeleccionarTodos').prop('checked', false);
                                            arrayIdsOkUsar = [];

                                            // Actualizar solo la tabla
                                            datatable.ajax.reload(function () {
                                                // Este callback se ejecuta después de recargar los datos
                                                console.log("Tabla actualizada después de borrar");
                                            }, false);
                                        });
                                } else {
                                    alertAdvertencia("Ocurrio un error")
                                }
                            })
                    } else {
                        alertAdvertencia("Seleccione repuestos")
                    }
                }
            })
        });

        $('#add-rep').click(function () {


            $.get(_URL + "/ajs/get/unidades/rep", function (data, textStatus, jqXHR) {
                let option = '';
                let resp = JSON.parse(data);
                $.each(resp, function (i, v) {
                    console.log(v.id);
                    option += `<option value="${v.id}">${v.nombre}</option>`;
                });

                $('#unidades').html(option);

            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las unidades: " + textStatus, errorThrown);
                alert("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
            });
            $('#modal-add-rep').modal('show');
        });


        $("#buscar-repuesto-stock").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: _URL + "/ajs/cargar/repuestos/" + almacenCod,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    },
                    error: function (xhr, status, error) {
                        console.error("Error en autocomplete:", error);
                        response([]);
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-aumentar-stock .modal-body",
            select: function (event, ui) {
                $(this).val(ui.item.label || ui.item.nombre);

                app._data.stockData.repuesto_id = ui.item.codigo;
                app._data.stockData.stock_actual = ui.item.cnt;
                app._data.stockData.repuesto_nombre = ui.item.nombre;
                $('#repuesto-seleccionado-id').val(ui.item.codigo);

                $(this).autocomplete("close");
                return false;
            },
            open: function () {
                $('.ui-autocomplete').css({
                    'z-index': 9999,
                    'max-width': $('#buscar-repuesto-stock').outerWidth() + 'px',
                    'font-size': '13px'
                });

                var input = $('#buscar-repuesto-stock');
                var inputOffset = input.position();

                $('.ui-autocomplete').css({
                    'left': inputOffset.left + 'px',
                    'top': (inputOffset.top + input.outerHeight() + 2) + 'px'
                });
            },
            close: function () {
                $('.ui-autocomplete').hide();
            }
        });


        // Autocomplete para DISMINUIR stock
        $("#buscar-repuesto-disminuir").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: _URL + "/ajs/cargar/repuestos/" + almacenCod,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-disminuir-stock-repuesto .modal-body",
            select: function (event, ui) {
                $(this).val(ui.item.label || ui.item.nombre);
                app._data.disminuirDataRepuesto.repuesto_id = ui.item.codigo;
                app._data.disminuirDataRepuesto.stock_actual = ui.item.cnt;
                app._data.disminuirDataRepuesto.repuesto_nombre = ui.item.nombre;
                app._data.disminuirDataRepuesto.unidad = ui.item.unidad || '';
                $('#repuesto-disminuir-id').val(ui.item.codigo);
                $(this).autocomplete("close");
                return false;
            }
        });

        // Autocomplete para TRASLADO
        $("#buscar-repuesto-traslado").autocomplete({
            source: function (request, response) {
                const almacenOrigen = app._data.trasladoDataRepuesto.almacen_origen;
                if (!almacenOrigen) {
                    response([]);
                    return;
                }
                $.ajax({
                    url: _URL + "/ajs/cargar/repuestos/" + almacenOrigen,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-traslado-almacenes-repuesto .modal-body",
            select: function (event, ui) {
                const existe = app._data.trasladoDataRepuesto.repuestos.find(p => p.id_repuesto === ui.item.codigo);
                if (existe) {
                    alertAdvertencia("Este repuesto ya está en la lista");
                    $(this).val('');
                    return false;
                }

                app._data.trasladoDataRepuesto.repuestos.push({
                    id_repuesto: ui.item.codigo,
                    nombre: ui.item.nombre,
                    unidad: ui.item.unidad || 'Unidad',
                    cantidad: 1,
                    stock_disponible: ui.item.cnt
                });

                $(this).val('');
                $(this).autocomplete("close");
                return false;
            }
        });

        // Limpiar autocomplete cuando se cierre el modal
        $('#modal-aumentar-stock').on('hidden.bs.modal', function () {
            $('#buscar-repuesto-stock').val('');
            $('.ui-autocomplete').hide();

            // Limpiar los datos de Vue
            app._data.stockData = {
                repuesto_id: '',
                stock_actual: '',
                cantidad_ingresar: '',
                repuesto_nombre: ''
            };
        });

        // Limpiar modal disminuir al cerrar
        $('#modal-disminuir-stock-repuesto').on('hidden.bs.modal', function () {
            $('#buscar-repuesto-disminuir').val('');
            $('.ui-autocomplete').hide();
            app._data.disminuirDataRepuesto = {
                repuesto_id: '',
                stock_actual: '',
                cantidad_disminuir: '',
                repuesto_nombre: '',
                unidad: '',
                observaciones: ''
            };
        });

        // Limpiar modal traslado al cerrar
        $('#modal-traslado-almacenes-repuesto').on('hidden.bs.modal', function () {
            $('#buscar-repuesto-traslado').val('');
            $('.ui-autocomplete').hide();
            app._data.trasladoDataRepuesto = {
                almacen_origen: '',
                almacen_destino: '',
                repuesto_id: '',
                repuestos: [],
                nota: ''
            };
        });

        // Manejar clic en "Agregar Repuesto" desde el dropdown móvil
        $('#add-rep-dropdown').click(function () {
            $('#add-rep').click();
        });

        // Manejar clic en "Borrar" desde el dropdown móvil
        $('.btnBorrar-dropdown').click(function () {
            $('.btnBorrar').click();
        });

        // Evento para cargar historial cuando se abre el modal
        $('#modal-historial-stock-repuesto').on('shown.bs.modal', function () {
            setTimeout(function () {
                app.cargarHistorialStock();
            }, 100);
        });

        // Evento para limpiar cuando se cierra el modal
        $('#modal-historial-stock-repuesto').on('hidden.bs.modal', function () {
            if ($.fn.DataTable.isDataTable('#tabla-historial-stock-repuesto')) {
                $('#tabla-historial-stock-repuesto').DataTable().clear().destroy();
            }
            $('#tabla-historial-stock-repuesto tbody').empty();
        });
    })

    function truncateText(text, maxLength) {
        if (text == null) {
            return null;
        }
        if (text.length > maxLength) {
            return text.substring(0, maxLength) + '...';
        }
        return text;
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img-preview').attr('src', e.target.result).show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxJVC = document.getElementById('maquinas');
        const checkboxIMPLE = document.getElementById('implementos');

        function handleCheckboxChange(checkedBox, otherBox, searchValue) {
            otherBox.checked = false;

            const searchInput = $('div.dataTables_filter input');

            if (checkedBox.checked) {
                searchInput.val(searchValue).trigger('keyup');
            } else {
                searchInput.val('').trigger('keyup');
            }
        }

        checkboxJVC.addEventListener('change', function () {
            handleCheckboxChange(checkboxJVC, checkboxIMPLE, 'JVC');
        });

        checkboxIMPLE.addEventListener('change', function () {
            handleCheckboxChange(checkboxIMPLE, checkboxJVC, 'IMPLE');
        });
    });
</script>