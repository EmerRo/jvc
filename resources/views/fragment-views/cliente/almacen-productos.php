<!-- resources\views\fragment-views\cliente\almacen-productos.php -->
<?php

require_once 'app/models/Producto.php';
$c_producto = new Producto();
$c_producto->setIdEmpresa($_SESSION['id_empresa']);
$almacenProducto = 1;

?>
<link rel="stylesheet" href="<?= URL::to('/public/css/almacen-productos.css') ?>?v=<?= time() ?>">

    

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
        </div>
        <div class="clearfix">
            <h6 class="page-title text-center">PRODUCTOS</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Almacen</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);"
                        style="font-weight: 500; color: #CA3438;">Kardex</a></li>
            </ol>
        </div>
    </div>
</div>

<div id="conte-vue-modals">
    <input type="hidden" name="almacenId" id="almacenId" value="<?php echo $almacenProducto ?>">

    <div class="row">
        <div class="col-12">
            <div class="card"
                style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); background: #fff;">
                <div class="card-header" style="background: #fff; border-bottom: none; padding-bottom: 0;">
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">

                        <!-- Contenedor Izquierdo: Solo para pantallas pequeñas -->
                        <div class="d-flex align-items-center gap-2 d-lg-none">
                            <div class="view-toggle-buttons d-flex align-items-center">
                                <button id="btn-table-view" class="btn active">
                                    <i class="fa fa-table me-1"></i> Vista Tabla
                                </button>
                                <button id="btn-grid-view" class="btn">
                                    <i class="fa fa-th-large me-1"></i> Vista Grid
                                </button>
                            </div>
                            <!-- Dropdown de Opciones para móvil -->
                            <div class="table-view-dropdown ms-2">
                                <div class="dropdown">
                                    <button class="btn bg-rojo text-white dropdown-toggle" type="button"
                                        id="dropdownOpciones" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-cog me-1"></i> Opciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOpciones" style="min-width: 250px;">
                                        <li>
                                            <h6 class="dropdown-header">Filtrar productos</h6>
                                        </li>
                                        <li>
                                            <select id="filtroProductos-mobile"
                                                class="form-select mx-3 mb-2 filter-option-select"
                                                style="width: calc(100% - 2rem);">
                                                <option value="">Todos</option>
                                                <option value="JVC">#JVC</option>
                                                <option value="IMPLE">#IMPLE</option>
                                                <option value="CEP">#CEP</option>
                                                <option value="PAD">#PAD</option>
                                                <option value="PORT">#PORT</option>
                                                <option value="ACC">#ACC</option>
                                            </select>

                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="descarFunccc()">
                                                <i class="fa fa-file-excel me-2"></i> Descargar Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-reporte-movimientos">
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
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-aumentar-stock">
                                                <i class="fa fa-plus me-2"></i> Aumentar Stock
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-disminuir-stock">
                                                <i class="fa fa-minus me-2"></i> Disminuir Stock
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-traslado-almacenes">
                                                <i class="fa fa-exchange-alt me-2"></i> Traslado Almacenes
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#modal-historial-stock">
                                                <i class="fa fa-history me-2"></i> Historial Stock
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/unidades">
                                                <i class="fa fa-ruler me-2"></i> Unidades
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/categorias">
                                                <i class="fa fa-folder me-2"></i> Categorías
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" id="add-prod-dropdown">
                                                <i class="fa fa-plus me-2"></i> Agregar Producto
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
                                <i class="fa fa-file-excel me-1"></i>
                                <span class="d-none d-lg-inline">Descargar Excel por búsqueda</span>
                            </button>
                            <button data-bs-toggle="modal" data-bs-target="#modal-reporte-movimientos" class="btn bg-white text-rojo border-rojo btn-sm">
                                <i class="fa fa-chart-line me-1"></i>
                                <span class="d-none d-lg-inline">Reporte Movimientos</span>
                            </button>
                            <button data-bs-toggle="modal" data-bs-target="#importarModal"
                                class="btn bg-white text-rojo border-rojo btn-sm">
                                <i class="fa fa-file-excel me-1"></i>
                                <span class="d-none d-lg-inline">Importar</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-aumentar-stock">
                                <i class="fa fa-plus me-1"></i>
                                <span class="d-none d-lg-inline">Aumentar Stock</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-disminuir-stock">
                                <i class="fa fa-minus me-1"></i>
                                <span class="d-none d-lg-inline">Disminuir Stock</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-traslado-almacenes">
                                <i class="fa fa-exchange-alt me-1"></i>
                                <span class="d-none d-lg-inline">Traslado Almacenes</span>
                            </button>
                            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-historial-stock">
                                <i class="fa fa-history me-1"></i>
                                <span class="d-none d-lg-inline">Historial Stock</span>
                            </button>
                            <a href="/unidades" class="btn bg-white text-rojo border-rojo button-link btn-sm">
                                <i class="fa fa-ruler me-1"></i>
                                <span class="d-none d-lg-inline">Unidades</span>
                            </a>
                            <a href="/categorias" class="btn bg-white text-rojo border-rojo button-link btn-sm">
                                <i class="fa fa-folder me-1"></i>
                                <span class="d-none d-lg-inline">Categorías</span>
                            </a>
                            <button class="btn bg-rojo text-white btn-sm" id="add-prod">
                                <i class="fa fa-plus me-1"></i>
                                <span class="d-none d-lg-inline">Agregar Producto</span>
                            </button>
                            <button class="btn bg-rojo btnBorrar btn-sm">
                                <i class="fa fa-trash me-1"></i>
                                <span class="d-none d-lg-inline">Borrar</span>
                            </button>
                        </div>
                    </div>

                    <div class="card-body" style="background: #fff; padding: 24px 16px; border-radius: 0 0 20px 20px;">

                        <div class="row align-items-end">
                            <div class="form-group col-md-3">
                                <label for="">Almacén</label>
                                <div class="d-flex align-items-center gap-1">
<select name="almacenSelect" id="almacenSelect" class="form-control"
                                            @change="changeAlmacen($event)" v-model="almacen">
                                            <option value="" disabled selected>Seleccionar</option>
                                            <option v-for="alm in almacenes" :key="alm.id_almacen" :value="alm.id_almacen">
                                                {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
                                            </option>
                                        </select>
                                    <button type="button" class="btn btn-sm btn-outline-success" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" @click="abrirModalAgregarAlmacen()" title="Agregar almacén">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Toggle de Vista y Filtros para pantallas grandes -->
                            <div class="col-md-6 d-none d-lg-block">
                                <div class="d-flex align-items-end justify-content-start gap-3" style="height: 100%;">
                                    <!-- Filtro Dropdown -->
                                    <div class="filter-dropdown">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted">Filtrar</span>
                                            <i class="fas fa-filter text-muted"></i>
                                            <select id="filtroProductos" class="form-select filter-option-select"
                                                style="width: auto; min-width: 120px;">
                                                <option value="">Todos</option>
                                                <option value="JVC">#JVC</option>
                                                <option value="IMPLE">#IMPLE</option>
                                                <option value="CEP">#CEP</option>
                                                <option value="PAD">#PAD</option>
                                                <option value="PORT">#PORT</option>
                                                <option value="ACC">#ACC</option>
                                            </select>

                                        </div>
                                    </div>

                                    <!-- Botones de Vista -->
                                    <div class="view-toggle-buttons-desktop d-flex align-items-center gap-2">
                                        <button id="btn-table-view-desktop" class="btn active">
                                            <i class="fa fa-table me-1"></i> Vista Tabla
                                        </button>
                                        <button id="btn-grid-view-desktop" class="btn">
                                            <i class="fa fa-th-large me-1"></i> Vista Grid
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Vista de Tabla (existente) -->
                            <div id="table-view" class="table-view">
                                <div class="table-responsive">
                                    <table id="datatable"
                                        class="table table-bordered dt-responsive nowrap text-center table-sm"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Unidades</th>
                                                <th>Precios</th>
                                                <th>Stock</th>
                                                <th>Editar</th>
                                                <th>Eliminar <input type="checkbox" class='btnSeleccionarTodos'></th>
                                            </tr>
                                        </thead>
                                        <tbody id='tbodyProductos'>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Vista de Grid (nueva) -->
                            <div id="grid-view" class="products-grid">
                                <!-- Filtros para vista grid -->
                                <div class="grid-filters">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="grid-search">
                                                <input type="text" id="grid-search-input" class="form-control"
                                                    placeholder="Buscar productos...">
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button class="btn border-rojo bg-white btnSeleccionarTodosGrid">
                                                <i class="fa fa-check-square me-1"></i> Seleccionar Todos
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading -->
                                <div class="loading-grid" id="loading-grid">
                                    <i class="fa fa-spinner"></i>
                                    <p>Cargando productos...</p>
                                </div>

                                <!-- Grid de productos -->
                                <div class="product-grid-container" id="products-container">
                                    <!-- Los productos se cargarán aquí dinámicamente -->
                                </div>

                                <!-- Paginación -->
                                <div class="grid-pagination" id="grid-pagination">
                                    <!-- La paginación se generará dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- modal de precios resources\views\fragment-views\cliente\modals\product-modal-precios.php -->
            <?php include __DIR__ . '/modals/product-modal-precios.php'; ?>

            <!-- modal de agregar producto resources\views\fragment-views\cliente\modals\product-modal-add-prod.php -->
            <?php include __DIR__ . '/modals/product-modal-add-prod.php' ?>

            <!-- modal de editar producto resources\views\fragment-views\cliente\modals\product-modal-edt-prod.php-->
            <?php include __DIR__ . '/modals/product-modal-edt-prod.php' ?>

            <!-- Modal Historial Stock  resources\views\fragment-views\cliente\modals\product-modal-historial-stock.php-->
            <?php include __DIR__ . '/modals/product-modal-historial-stock.php' ?>

            <!-- Modal Reporte de Movimientos -->
            <?php include __DIR__ . '/components/modal-reporte-movimientos.php' ?>

            <!-- Modal Aumentar Stock de Productos resources\views\fragment-views\cliente\modals\product-modal-aumentar-stock.php-->
            <?php include __DIR__ . '/modals/product-modal-aumentar-stock.php' ?>

            <!-- Modal Disminuir Stock de Productos resources\views\fragment-views\cliente\modals\product-modal-disminuir-stock.php-->
            <?php include __DIR__ . '/modals/product-modal-disminuir-stock.php' ?>

            <!-- Modal Agregar/Editar Almacén -->
            <?php include __DIR__ . '/components/modal-agregar-editar-almacen.php' ?>

            <!-- Modal Traslado Entre Almacenes resources\views\fragment-views\cliente\modals\product-modal-traslado-almacenes.php-->
            <?php include __DIR__ . '/modals/product-modal-traslado-almacenes.php' ?>

            <!-- Modal restock Stock de Productos resources\views\fragment-views\cliente\modals\product-modal-restock.php-->
            <?php include __DIR__ . '/modals/product-modal-restock.php' ?>

            <!-- Modal importar  Productos resources\views\fragment-views\cliente\modals\product-modal-importar.php-->
            <?php include __DIR__ . '/modals/product-modal-importar.php' ?>

            <!-- Modal lista  Productos resources\views\fragment-views\cliente\modals\product-modal-lista-productos.php-->
            <?php include __DIR__ . '/modals/product-modal-lista-productos.php' ?>

            <!-- Modal codigo de barras resources\views\fragment-views\cliente\modals\product-modal-codigo-barras.php-->
            <?php include __DIR__ . '/modals/product-modal-codigo-barras.php' ?>

            <!-- Modal de reporte resources\views\fragment-views\cliente\modals\product-modal-reporte.php -->
            <?php include __DIR__ . '/modals/product-modal-reporte.php' ?>

        </div>

    </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/@pokusew/escpos@3.0.8/dist/index.min.js"></script> -->


<script>
    var almacenCod = '<?php 
        $almacenPrincipal = '1';
        $con = new Conexion();
        $sql = "SELECT id_almacen FROM almacenes WHERE id_empresa = '{$_SESSION['id_empresa']}' AND estado = '1' AND principal = '1' LIMIT 1";
        $result = $con->getConexion()->query($sql);
        if ($row = $result->fetch_assoc()) $almacenPrincipal = $row['id_almacen'];
        echo $almacenPrincipal;
    ?>';
    window._almacenPrincipal = almacenCod;
</script>
<script src="<?= URL::to('public/js/components/productos.js') ?>?v=<?= time() ?>"></script>