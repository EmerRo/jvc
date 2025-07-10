<!-- resources\views\fragment-views\cliente\almacen-productos.php -->
<?php

require_once "app/models/Producto.php";
$c_producto = new Producto();
$c_producto->setIdEmpresa($_SESSION['id_empresa']);
$almacenProducto = 1;

?>
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">

<style>
    .dt-body-left {
        text-align: left !important;
    }

    /* Estilos para hacer las tablas de precios más compactas */
    .table-bordered.table-hover {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    /* Eliminar espacios en las celdas de la tabla */
    .table-bordered.table-hover th,
    .table-bordered.table-hover td {
        padding: 0.4rem !important;
        vertical-align: middle !important;
    }

    /* Ajustar el ancho de las columnas */
    .table-bordered.table-hover th:first-child,
    .table-bordered.table-hover td:first-child {
        width: 50%;
    }

    .table-bordered.table-hover th:nth-child(2),
    .table-bordered.table-hover td:nth-child(2) {
        width: 35%;
    }

    .table-bordered.table-hover th:last-child,
    .table-bordered.table-hover td:last-child {
        width: 15%;
        text-align: center;
    }

    /* Eliminar espacios en los input-group dentro de las tablas */
    .table-bordered.table-hover .input-group {
        margin: 0 !important;
    }

    /* Ajustar el padding de los input dentro de las tablas */
    .table-bordered.table-hover .input-group .form-control,
    .table-bordered.table-hover .input-group .input-group-text {
        padding: 0.25rem 0.5rem !important;
        height: auto !important;
    }

    /* Hacer los botones más compactos */
    .table-bordered.table-hover .btn-sm {
        padding: 0.2rem 0.4rem !important;
        font-size: 0.875rem !important;
    }

    /* Ajustar el card-body que contiene la tabla */
    .card-body .table-responsive {
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Eliminar espacios en el encabezado de la tabla */
    .table-light th {
        padding: 0.5rem !important;
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

    .ui-autocomplete .ui-menu-item {
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
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

    /* Asegurar que el autocomplete esté dentro del modal */
    #modal-aumentar-stock .ui-autocomplete {
        position: absolute !important;
        max-width: calc(100% - 24px) !important;
    }

    /* Mejorar el estilo del input de búsqueda */
    #buscar-producto-stock {
        border: 2px solid #e9ecef !important;
        border-radius: 6px !important;
        padding: 10px 12px !important;
        font-size: 14px !important;
        transition: border-color 0.3s ease !important;
    }

    #buscar-producto-stock:focus {
        border-color: #CA3438 !important;
        box-shadow: 0 0 0 0.2rem rgba(202, 52, 56, 0.25) !important;
        outline: none !important;
    }
</style>
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
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <div hidden class="dropdown">
                    <button class="btn btn-primary  dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-cog me-2"></i> Settings
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
    <input type="hidden" name="almacenId" id="almacenId" value="<?php echo $almacenProducto ?>">

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); background: #fff;">
                <div class="card-header" style="background: #fff; border-bottom: none; padding-bottom: 0;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Lista de Productos</h4>
                        <div class="text-end mt-2 mt-md-0">
                            <button onclick="descarFunccc()" class="btn bg-white text-rojo border-rojo"><i class="fa fa-file-excel"></i> Descargar Exel por busqueda</button>
                            <button data-bs-toggle="modal" data-bs-target="#importarModal" class="btn bg-white text-rojo border-rojo"><i class="fa fa-file-excel"></i> Importar</button>
                            <button class="btn border-rojo bg-white" data-bs-toggle="modal" data-bs-target="#modal-aumentar-stock"><i class="fa fa-plus"> </i> Aumentar Stock de Productos</button>
                            <a href="/unidades" class="btn bg-white text-rojo border-rojo button-link"><i class="fa fa-plus"> </i> Unidades</a>
                            <a href="/categorias" class="btn bg-white text-rojo border-rojo button-link"><i class="fa fa-plus"> </i> Categorias</a>
                            <button class="btn bg-rojo text-white bordes" id="add-prod"><i class="fa fa-plus"></i> Agregar Producto</button>
                            <button class="btn btn-danger btnBorrar bordes"><i class="fa fa-trash"></i> Borrar</button>
                            <button hidden class="btn btn-danger" @click="agregarIds"><i class="fa fa-times"></i> Seleccionar Todos</button>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background: #fff; padding: 24px 16px; border-radius: 0 0 20px 20px;">
                    <div class="row">
                        <div class="form-group col-md-2" style="margin:  1rem 0;">
                            <label for="">Almacen</label>
                            <select name="almacenSelect" id="almacenSelect" class="form-control" @change="changeAlmacen($event)" v-model="almacen">
                                <option value="1">Almacen 1</option>
                                <option value="2">Almacen 2</option>
                                <option value="3">Almacen 3</option>
                            </select>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter-option" id="maquinas" name="filter" value="maquinas">
                                <label class="form-check-label" for="maquinas">#JVC</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter-option" id="implementos" name="filter" value="implementos">
                                <label class="form-check-label" for="implementos">#IMPLE</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter-option" id="cep" name="filter" value="cep">
                                <label class="form-check-label" for="cep">#CEP</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter-option" id="pad" name="filter" value="pad">
                                <label class="form-check-label" for="pad">#PAD</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter-option" id="port" name="filter" value="port">
                                <label class="form-check-label" for="port">#PORT</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter-option" id="acc" name="filter" value="acc">
                                <label class="form-check-label" for="acc">#ACC</label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Unidades</th>
                                    <th>Precios </th>
                                    <th>stock</th>
                                    <th>Editar</th>
                                    <th>Eliminar <input type="checkbox" class='btnSeleccionarTodos'> </th>
                                </tr>
                            </thead>
                            <tbody id='tbodyProductos'>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-precios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Precios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form @submit.prevent="agregarPrecios">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Precio Venta: </label>
                                <input v-model="edt.precio_unidad" id="precio_unidad" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 1: </label>
                                <input v-model="edt.precio" id="precio1" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 2: </label>
                                <input v-model="edt.precio2" id="precio2" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 3: </label>
                                <input v-model="edt.precio3" id="precio3" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Precio 4: </label>
                                <input v-model="edt.precio4" id="precio4" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- Modal de Agregar Producto Rediseñado -->
    <div class="modal fade" id="modal-add-prod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="fa fa-plus-circle me-2"></i>Nuevo Producto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="agregarProd">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Primera fila: Nombre y Código -->
                            <div class="form-group col-md-8 mb-3">
                                <label><i class="fa fa-tag me-1"></i>Nombre de producto</label>
                                <input v-model="reg.nombre" type="text" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-barcode me-1"></i>Código</label>
                                <input v-model="reg.codigo" type="text" class="form-control" required>
                            </div>

                            <!-- Segunda fila: Detalle y Categoría -->
                            <div class="form-group col-md-8 mb-3">
                                <label><i class="fa fa-align-left me-1"></i>Detalle de producto</label>
                                <textarea v-model="reg.detalle" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-folder me-1"></i>Categoría</label>
                                <select v-model="reg.categoria" id="categoria" class="form-control" required>
                                    <!-- Las opciones se cargan dinámicamente -->
                                </select>
                            </div>

                            <!-- Tercera fila: Precio, Costo y Cantidad -->
                            <div class="form-group col-md-4 mb-3">
                                <label><span class="me-1" style="font-weight: bold;">S/</span>Precio Venta</label>
                                <input v-model="reg.precio" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-money-bill me-1"></i>Costo</label>
                                <input v-model="reg.costo" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-cubes me-1"></i>Cantidad</label>
                                <input v-model="reg.cantidad" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>

                            <!-- Cuarta fila: Unidades, Almacén y Código Sunat -->
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-ruler me-1"></i>Unidades</label>
                                <select v-model="reg.unidad" class="form-control" id="unidades" required>
                                    <!-- Las opciones se cargan dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-warehouse me-1"></i>Almacén</label>
                                <select v-model="reg.almacen" class="form-control" required>
                                    <option value="1">Almacen 1</option>
                                    <option value="2">Almacen 2</option>
                                    <option value="3">Almacen 3</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-file-alt me-1"></i>Cod. Sunat</label>
                                <input v-model="reg.codSunat" type="text" class="form-control">
                            </div>

                            <!-- Quinta fila: Afecto ICBP, Precio Distribuidor, Precio Mayorista -->
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-check-circle me-1"></i>Afecto ICBP</label>
                                <select v-model="reg.afecto" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-store me-1"></i>Precio Distribuidor</label>
                                <input v-model="reg.precio1" @keypress="onlyNumber" type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label><i class="fa fa-shopping-cart me-1"></i>Precio Mayorista</label>
                                <input v-model="reg.precio2" @keypress="onlyNumber" type="text" class="form-control">
                            </div>

                            <!-- Sexta fila: Imagen del Producto -->
                            <div class="form-group col-md-12 mb-3">
                                <label><i class="fa fa-image me-1"></i>Imagen del Producto</label>
                                <div class="input-group">
                                    <input type="file" @change="onImageChange" class="form-control"
                                        accept="image/png, image/jpeg" id="product-image-input">
                                </div>
                                <div class="mt-2" v-if="imagePreview">
                                    <img :src="imagePreview" alt="Vista previa" class="img-thumbnail"
                                        style="max-height: 150px;">
                                </div>
                                <div class="mt-2" v-else>
                                    <div class="text-center p-3 border rounded bg-light">
                                        <i class="fa fa-image fa-2x text-muted mb-2 d-block"></i>
                                        <p class="mb-0">No hay imagen para este producto</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Séptima fila: ¿Utilizar MultiPrecio? -->
                            <div class="form-group col-md-12 mb-3">
                                <label><i class="fa fa-tags me-1"></i>¿Utilizar MultiPrecio?</label>
                                <div class="form-check form-switch">
                                    <input v-model="reg.usar_multiprecio" class="form-check-input" type="checkbox"
                                        id="usar_multiprecio_add" style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="usar_multiprecio_add"
                                        :class="{'text-danger': reg.usar_multiprecio, 'text-secondary': !reg.usar_multiprecio}">
                                        {{ reg.usar_multiprecio ? 'Sí' : 'No' }}
                                    </label>
                                </div>
                            </div>

                            <!-- Campos ocultos -->
                            <div class="form-group col-md-4" hidden>
                                <label>Precio 3</label>
                                <input v-model="reg.precio3" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>
                            <div class="form-group col-md-4" hidden>
                                <label>Precio 4</label>
                                <input v-model="reg.precio4" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>
                            <!-- Campo oculto para usar_barra -->
                            <div class="form-group" hidden>
                                <input v-model="reg.usar_barra" type="hidden" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- Sección de MultiPrecio -->
                    <div v-if="reg.usar_multiprecio" class="col-md-12 px-3 mb-3">
                        <div class="card border-danger">
                            <div
                                class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fa fa-list-ul me-2"></i>Lista de Precios</h5>
                                <button type="button" @click="agregarPrecioNuevo" class="btn btn-sm btn-light">
                                    <i class="fa fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered m-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%; padding: 4px 8px;">Nombre</th>
                                            <th style="width: 35%; padding: 4px 8px;">Precio</th>
                                            <th style="width: 15%; padding: 4px 8px; text-align: center;">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(precio, index) in preciosNuevos" :key="index">
                                            <td style="padding: 4px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                                    <input v-model="precio.nombre" type="text" class="form-control"
                                                        placeholder="Nombre del precio">
                                                </div>
                                            </td>
                                            <td style="padding: 4px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><span class="me-1"
                                                            style="font-weight: bold;">S/</span></span>
                                                    <input v-model="precio.precio" @keypress="onlyNumber" type="text"
                                                        class="form-control" placeholder="0.00">
                                                </div>
                                            </td>
                                            <td style="padding: 4px; text-align: center;">
                                                <button @click="eliminarPrecioNuevo(index)" type="button"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="preciosNuevos.length === 0">
                                            <td colspan="3" class="text-center text-muted" style="padding: 4px;">
                                                No hay precios configurados. Haga clic en "Agregar" para crear uno.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Cerrar
                        </button>
                        <button type="submit" class="btn bg-rojo text-white">
                            <i class="fa fa-save me-1"></i>Guardar
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- modal de editar -->
    <div class="modal fade" id="modal-edt-prod" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-edit me-2"></i>Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="actualizarProd" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <input v-model="edt.cod_prod" type="hidden" class="form-control">
                            <div class="form-group col-md-8 mt-2">
                                <label><i class="fa fa-tag me-1"></i>Nombre de producto</label>
                                <input v-model="edt.nombre" required type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-barcode me-1"></i>Código</label>
                                <input v-model="edt.codigo" required type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-8 mt-2">
                                <label><i class="fa fa-align-left me-1"></i>Detalle de producto</label>
                                <textarea v-model="edt.detalle" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-folder me-1"></i>Categoría</label>
                                <select v-model="edt.categoria" id="categoria-edt" class="form-control">
                                </select>
                            </div>

                            <!-- PRIMERA FILA: Precio Venta, Costo, Cantidad -->
                            <div class="form-group col-md-4 mt-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label><span class="me-1" style="font-weight: bold;">S/</span>Precio Venta</label>
                                    <span v-if="parseFloat(edt.precio) <= 0" class="text-danger small">
                                        <i class="fa fa-exclamation-triangle"></i> Precio está en 0
                                    </span>
                                </div>
                                <input v-model="edt.precio" @keypress="onlyNumber" required value="0" type="text"
                                    class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label><i class="fa fa-money-bill me-1"></i>Costo</label>
                                    <span v-if="parseFloat(edt.costo) <= 0" class="text-danger small">
                                        <i class="fa fa-exclamation-triangle"></i> Costo está en 0
                                    </span>
                                </div>
                                <input v-model="edt.costo" @keypress="onlyNumber" required value="0" type="text"
                                    class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label><i class="fa fa-cubes me-1"></i>Cantidad</label>
                                    <span v-if="parseInt(edt.cantidad) <= 0" class="text-danger small">
                                        <i class="fa fa-exclamation-triangle"></i> Cantidad está en 0
                                    </span>
                                </div>
                                <input v-model="edt.cantidad" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>

                            <!-- SEGUNDA FILA: Unidades, Almacén, Cod. Sunat -->
                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-ruler me-1"></i>Unidades</label>
                                <select v-model="edt.unidad" id="unidades-edt" class="form-control">
                                    <option v-for="unit in units" :key="unit.id" :value="unit.id">{{unit.nombre}}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-warehouse me-1"></i>Almacén</label>
                                <select v-model="edt.almacen" required class="form-control">
                                    <option value="1">Almacen 1</option>
                                    <option value="2">Almacen 2</option>
                                    <option value="3">Almacen 3</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-file-alt me-1"></i>Cod. Sunat</label>
                                <input v-model="edt.codSunat" type="text" class="form-control">
                            </div>

                            <!-- TERCERA FILA: Afecto ICBP, Precio Distribuidor, Precio Mayorista -->
                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-check-circle me-1"></i>Afecto ICBP</label>
                                <select v-model="edt.afecto" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label><i class="fa fa-store me-1"></i>Precio Distribuidor</label>
                                    <span v-if="parseFloat(edt.precioMayor) <= 0" class="text-danger small">
                                        <i class="fa fa-exclamation-triangle"></i> Precio está en 0
                                    </span>
                                </div>
                                <input v-model="edt.precioMayor" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label><i class="fa fa-shopping-cart me-1"></i>Precio Mayorista</label>
                                    <span v-if="parseFloat(edt.precioMenor) <= 0" class="text-danger small">
                                        <i class="fa fa-exclamation-triangle"></i> Precio está en 0
                                    </span>
                                </div>
                                <input v-model="edt.precioMenor" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>

                            <!-- Campos ocultos -->
                            <div class="form-group col-md-4 mt-2" hidden>
                                <label>Precio 3</label>
                                <input v-model="edt.precio3" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>
                            <div class="form-group col-md-4 mt-2" hidden>
                                <label>Precio 4</label>
                                <input v-model="edt.precio4" @keypress="onlyNumber" value="0" type="text"
                                    class="form-control">
                            </div>
                            <div class="form-group col-md-8 mt-3">
                                <label class="d-flex align-items-center mb-2">
                                    <i class="fa fa-image me-2"></i>
                                    <span class="fw-bold">Imagen del Producto</span>
                                </label>
                                <div class="image-container position-relative"
                                    style="border: 1px solid #dee2e6; border-radius: 4px; overflow: hidden;">
                                    <!-- Contenedor de imagen -->
                                    <div class="image-wrapper" style="position: relative;">
                                        <img id="img-preview" alt="Vista previa"
                                            style="max-height: 150px; width: auto; display: none; margin: 0;" />

                                        <!-- Botón de edición con texto -->
                                        <div id="image-edit-button"
                                            style="display: none; position: absolute; top: 10px; right: 10px;">
                                            <button type="button" class="btn btn-light" onclick="toggleImageMenu()"
                                                style="background-color: rgba(255, 255, 255, 0.9); border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <i class="fa fa-pencil-alt me-1"></i>
                                                Editar imagen
                                            </button>

                                            <!-- Menú desplegable -->
                                            <div id="image-menu" class="position-absolute shadow-sm"
                                                style="display: none; top: 100%; right: 0; margin-top: 5px; background-color: white; border-radius: 4px; border: 1px solid #dee2e6; min-width: 160px; z-index: 1000;">
                                                <div class="p-2 hover-bg-light" style="cursor: pointer;"
                                                    onclick="changeImage()">
                                                    <i class="fa fa-upload me-2"></i> Subir una foto...
                                                </div>
                                                <div class="p-2 text-danger hover-bg-light" style="cursor: pointer;"
                                                    onclick="removeImage()">
                                                    <i class="fa fa-trash me-2"></i> Eliminar foto
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input oculto para subir imagen -->
                                <input type="file" id="upload-input" name="imagen" class="d-none" accept="image/*"
                                    onchange="previewImage(this)" />

                                <!-- Mensaje cuando no hay imagen -->
                                <div id="no-image-message" class="text-center p-3 border rounded bg-light mt-2">
                                    <i class="fa fa-image fa-2x text-muted mb-2 d-block"></i>
                                    <p class="mb-2">No hay imagen para este producto</p>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="changeImage()">
                                        <i class="fa fa-upload me-1"></i> Subir imagen
                                    </button>
                                </div>
                            </div>


                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-qrcode me-1"></i>Usar Código Barra</label>
                                <div class="input-group">
                                    <select v-model="edt.usar_barra" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    <div v-if="edt.usar_barra=='1'" class="input-group-append">
                                        <button @click="edtGenerarCodeBarra" type="button" class="btn border-rojo"><i
                                                class="fa fa-sync-alt"></i> Generar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3 text-center" v-if="edt.usar_barra=='1'">
                                <label><i class="fa fa-barcode me-1"></i>Código de Barras</label>
                                <div class="p-2 border rounded bg-light">
                                    <img id="barcode" class="img-fluid" />
                                </div>
                            </div>

                            <div class="form-group col-md-4 mt-2">
                                <label class="d-flex align-items-center">
                                    <i class="fa fa-tags me-2"></i>
                                    <span class="fw-bold">¿Utilizar MultiPrecio?</span>
                                </label>
                                <div class="form-check form-switch">
                                    <input v-model="edt.usar_multiprecio" class="form-check-input" type="checkbox"
                                        id="usar_multiprecio_edit" style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2 fw-bold" for="usar_multiprecio_edit"
                                        :class="{'text-danger': edt.usar_multiprecio, 'text-secondary': !edt.usar_multiprecio}">
                                        {{ edt.usar_multiprecio ? 'Sí' : 'No' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="edt.usar_multiprecio" class="col-md-12 mt-0 ">
                        <div class="card border-danger mb-3">
                            <div
                                class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fa fa-list-ul me-2"></i>Lista de Precios</h5>
                                <button type="button" @click="agregarPrecio" class="btn btn-sm btn-light">
                                    <i class="fa fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered m-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%; padding: 4px 8px;">Nombre</th>
                                            <th style="width: 35%; padding: 4px 8px;">Precio</th>
                                            <th style="width: 15%; padding: 4px 8px; text-align: center;">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(precio, index) in precios" :key="index">
                                            <td style="padding: 4px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                                    <input v-model="precio.nombre" type="text" class="form-control"
                                                        placeholder="Nombre del precio">
                                                </div>
                                            </td>
                                            <td style="padding: 4px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><span
                                                            style="font-weight: bold;">S/</span></span>
                                                    <input v-model="precio.precio" @keypress="onlyNumber" type="text"
                                                        class="form-control" placeholder="0.00">
                                                </div>
                                            </td>
                                            <td style="padding: 4px; text-align: center;">
                                                <button @click="eliminarPrecio(index)" type="button"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="precios.length === 0">
                                            <td colspan="3" class="text-center text-muted" style="padding: 4px;">
                                                No hay precios configurados. Haga clic en "Agregar" para crear uno.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-rojo"><i class="fa fa-save me-1"></i>Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fa fa-times me-1"></i>Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Aumentar Stock de Productos -->
    <div class="modal fade" id="modal-aumentar-stock" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-box me-2"></i>Aumentar Stock de Productos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="aumentarStockProducto">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            Aquí Debes Buscar y Seleccionar un Producto:
                        </div>

                        <div class="form-group mb-3">
                            <label><i class="fa fa-search me-1"></i>Buscar Producto:</label>
                            <input type="text" id="buscar-producto-stock" class="form-control"
                                placeholder="Buscar por código o nombre...">
                            <input type="hidden" id="producto-seleccionado-id" v-model="stockData.producto_id">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><i class="fa fa-cubes me-1"></i>Stock Actual:</label>
                                    <input type="text" class="form-control" v-model="stockData.stock_actual" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><i class="fa fa-plus-circle me-1"></i>Cant. a Ingresar *:</label>
                                    <input type="number" class="form-control" v-model="stockData.cantidad_ingresar"
                                        min="1" required placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Cerrar
                        </button>
                        <button type="submit" class="btn bg-rojo text-white">
                            <i class="fa fa-check me-1"></i>Aumentar Stock Ahora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-restock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="agregarStock">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cantidad</label>
                            <input v-model="restock.cantidad" required type="text" class="form-control">
                            <small class="form-text text-muted">La cantidad ingresada se sumara a la cantidad
                                actual</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="importarModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                <div class="modal-header bg-rojo text-white" style="border-radius: 15px 15px 0 0; border-bottom: none;">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 600;">
                        <i class="fas fa-file-excel me-2"></i>Importar Productos con EXCEL
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form enctype='multipart/form-data'>
                        <div class="mb-4">
                            <div class="p-3 bg-light rounded-3" style="border: 1px dashed #dee2e6;">
                                <p class="mb-2">Descargue el modelo en <span class="fw-bold">EXCEL</span> para importar,
                                    no
                                    modifique los campos en el archivo.</p>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">Click para descargar:</span>
                                    <a href="<?= URL::to('/reporte/producto/guia') ?>"
                                        class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                        <i class="fas fa-download me-1"></i>plantilla.xlsx
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold mb-2">Importar Excel:</label>
                            <div class="file-upload-wrapper">
                                <div class="file-upload-area"
                                    style="position: relative; border: 2px dashed #CA3438; border-radius: 10px; padding: 20px; text-align: center; background-color: #fff5f5; transition: all 0.3s ease;">
                                    <input id="file-import-exel"
                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                        type="file"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                    <div class="file-info">
                                        <i class="fas fa-cloud-upload-alt"
                                            style="font-size: 2rem; color: #CA3438; margin-bottom: 10px;"></i>
                                        <p class="mb-0" id="file-name-display">Arrastre su archivo aquí o haga click
                                            para seleccionar</p>
                                        <p class="text-muted small mt-1">Formatos aceptados: Excel, CSV</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: none;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="border-radius: 8px; padding: 8px 20px; font-weight: 500;">Cancelar</button>
                    <button type="button" class="btn bg-rojo text-white" id="btn-importar"
                        style="border-radius: 8px; padding: 8px 20px; font-weight: 500;">
                        <i class="fas fa-file-import me-1"></i>Importar
                    </button>
                </div>
            </div>
        </div>
    </div>





    <div class="modal fade" id="modal-lista-productos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="staticBackdropLabel">Lista de productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Descripcion</th>
                                <th>Cantidad</th>
                                <th>Costo</th>
                                <th>Precio Venta</th>
                                <th>Precio 1</th>
                                <th>Precio 2</th>
                                <th>Almacen</th>
                                <th>Codigo</th>
                                <th>Unidades</th>
                                <th>Categorias</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in listaProd">
                                <td>{{item.producto}}</td>
                                <td>{{item.descripcicon}}</td>
                                <td> {{item.cantidad}}</td>
                                <td>{{item.costo}}</td>
                                <td>{{item.precio_unidad}}</td>
                                <td>{{item.precio}}</td>
                                <td>{{item.precio2}}</td>
                                <td>{{item.almacen}}</td>
                                <td>{{item.codigoProd}}</td>
                                <td>{{item.unidad}}</td>
                                <td>{{item.categoria}}</td>
                                <td><button @click="eliminarItemTablaPro(index)" class="btn-sm btn btn-danger"><i
                                            class="fa fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button @click="agregarListaImport" type="button" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCodigoBarras" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Codigo de Barras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3 text-center">
                        <img id="idCodigoBarras">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Escalar</label>
                        <select id="scalimg" class="form-control">
                            <option value="1">NO</option>
                            <option value="2">SI</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button class="btn border-rojo text-rojo" id="btnImprimir"
                            onclick="imprimir()">Imprimir</button>
                        <button class="btn  border-rojo text-rojo" id="btnImprimir2" onclick="imprimir2()">Imprimir
                            2</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-rojo text-white" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="modal-prodEreport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte De Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Año</label>
                    <select id='anioreporEFG' class="form-control">
                        <?php
                        $anio = date("Y");
                        for ($i = 0; $i < 10; $i++) {
                            echo "<option value='$anio'>$anio</option>";
                            $anio--;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mes</label>
                    <select id='mesreprEFG' class="form-control">
                        <?php
                        $contador = 1;
                        $meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                        foreach ($meses as $mes) {
                            echo "<option  " . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
                            $contador++;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Dia</label>
                    <input id='diareporEfghg' class="form-control">
                </div>

            </div>
            <div class="modal-footer">
                <button id="generarreporteProd" type="button" class="btn btn-primary">Generar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-imagen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte De Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 mb-3" id="imagen">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoria">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombreCategoria" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombreCategoria">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="submitCategoria" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="listaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoria">
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Categoria</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCat">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Actualizar Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoria">
                <div class="modal-body">
                    <input type="text" id="idCatU" value="" hidden>
                    <div class="mb-3">
                        <label for="nombreCategoriaU" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombreCategoriaU">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="updateCategoriaBtn" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <style>
    input[type=file].hidden {
        color: transparent;
    }
</style> -->
<script src="
https://cdn.jsdelivr.net/npm/@pokusew/escpos@3.0.8/dist/index.min.js
"></script>
<script>
    function descarFunccc() {
        window.open(_URL +
            `/reporte/producto/excel?texto=${$("#datatable_filter input").val()}`)
    }

    var codProdT = ''
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

        /*   let printA4 = $(this).attr('href') */
        //printBarcode()
        /*let imgCodigo = $('#idCodigoBarras').attr('src');
        var myWindow = window.open("", "Image", "_blank");
        myWindow.document.write("<html><head><title></title></head><body style='width: 5cm; height: 2.5cm; padding: 0; margin: 0;'>");
        myWindow.document.write("<h3 style='font-size: 12px;text-align: center; margin: 0; padding: 0;'>"+nombreBarraTemps+"</h3>");
        myWindow.document.write("<img src='" + imgCodigo + "' style='width: 100%; height:   display: block; margin: 0 auto;'>");
        myWindow.document.write("</body></html>");
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();*/

        /* let imgCodigo = $('#idCodigoBarras').attr('src');
        let ticketContent = `
        <html>
        <head><title>Ticket de impresión</title></head>
        <body style="width: 5cm; height: 2.5cm; padding: 0; margin: 0;">
          <h3 style="font-size: 12px;text-align: center; margin: 0; padding: 0;">"+nombreBarraTemps+"</h3>
          <img src="${imgCodigo}" style="width: 100%; height: calc(100% - 1em); display: block; margin: 0 auto;">
        </body>
        </html>
      `;

        qz.websocket.connect().then(function() {
            return qz.printers.find("XP-350B"); // Nombre de la impresora XPRINTER XP-350B
        }).then(function(printer) {
            let config = qz.configs.create(printer);
            return qz.print(config, [{ type: 'html', format: 'plain', data: ticketContent }]);
        }).then(function() {
            qz.websocket.disconnect();
        }).catch(function(err) {
            console.error(err);
        });*/

    }


    function abrirModalBarras(e, n = '') {
        e = e.trim();
        console.log(e);
        nombreBarraTemps = n;
        codeBarraTemps = e; // Usar el código del producto

        // Primero mostrar el modal
        $('#modalCodigoBarras').modal('show');

        // Esperar a que el modal esté completamente visible antes de generar el código de barras
        $('#modalCodigoBarras').on('shown.bs.modal', function () {
            setTimeout(function () {
                try {
                    JsBarcode("#idCodigoBarras", e); // Usar el código del producto
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
    var almacenCod = '<?php echo $_SESSION["sucursal"] ?>'
    $(document).ready(function () {
        const app = new Vue({
            el: "#conte-vue-modals",
            data: {

                almacen: <?php echo $_SESSION["sucursal"] ?>,
                t: 0,
                listaProd: [],
                restock: {
                    cod: '',
                    cantidad: '',
                },
                precios: [],
                preciosNuevos: [],
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
                    usar_multiprecio: false,
                },
                edt: {
                    nombre: '',
                    cod_prod: '',
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
                    categoria: '',
                    almacen: '',
                    unidad: '',
                    usar_multiprecio: false,
                },
                listaIdsss: [],
                units: [], // Will store available units
                imagePreview: null,
                stockData: {
                    producto_id: '',
                    stock_actual: '',
                    cantidad_ingresar: '',
                    producto_nombre: ''
                },
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
                agregarIds() {
                    /*  console.log('nice'); */
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
                    _ajax("/ajs/data/producto/edt/precios", "POST", data,
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
                    // Limpiar checkboxes y localStorage
                    clearSelection();
                    $('.filter-option').prop('checked', false);

                    // Actualizar el almacén seleccionado
                    almacenCod = event.target.value;

                    // Destruir la tabla actual
                    if ($.fn.DataTable.isDataTable('#datatable')) {
                        datatable.destroy();
                    }


                    // Reinicializar DataTable con la nueva configuración
                    datatable = $("#datatable").DataTable({
                        order: [[0, 'ASC']],
                        "processing": true,
                        "serverSide": true,
                        "sAjaxSource": _URL + "/ajs/server/sider/productos",
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
                                            $('#cep').prop('checked') ? 'CEP' :
                                                $('#pad').prop('checked') ? 'PAD' :
                                                    $('#port').prop('checked') ? 'PORT' :
                                                        $('#acc').prop('checked') ? 'ACC' : ''
                                }
                            );
                        },
                        columnDefs: [
                            {
                                "targets": [0],
                                "className": "text-center",
                                "width": "auto", // Cambiado a auto para que se ajuste al contenido
                                "render": function (data, type, row, meta) {
                                    return '<a href="javascript:abrirModalBarras(\'' + row[0] + '\',\'' + row[0] + '\')">' + row[0] + '</a>';
                                }
                            },
                            {
                                "targets": [1],
                                "className": "dt-body-left",
                                "width": "auto", // La columna nombre se ajustará automáticamente
                                "render": function (data, type, row, meta) {
                                    return '<div style="white-space: normal;">' + data + '</div>'; // Permite que el texto largo se ajuste
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
                                    return `<input type="checkbox" class="btnCheckEliminar" data-id="${row[6]}" data-row-index="${meta.row}">`;
                                }
                            }
                        ],
                        "drawCallback": function (settings) {
                            // Callback después de que la tabla se ha redibujado
                            $("#datatable_processing").hide();

                            // Esperar un momento antes de restaurar estados
                            setTimeout(() => {
                                if (localStorage.getItem('idChecks')) {
                                    restoreCheckboxStates();
                                }

                            }, 100);
                        },
                        "error": function (xhr, error, thrown) {
                            console.log('Error en DataTables:', error);
                            $("#datatable_processing").hide();
                        }
                    });
                },
                edtGenerarCodeBarra() {
                    // Usar $nextTick para asegurar que el DOM se ha actualizado
                    this.$nextTick(() => {
                        setTimeout(() => {
                            if (document.getElementById("barcode")) {
                                try {
                                    // CAMBIO AQUÍ: Usar el código del producto
                                    JsBarcode("#barcode", this.edt.codigo);
                                } catch (error) {
                                    console.error("Error al generar código de barras:", error);
                                }
                            } else {
                                console.warn("Elemento #barcode no encontrado en el DOM");
                            }
                        }, 100);
                    });
                },
                agregarListaImport() {
                    if (this.listaProd.length > 0) {
                        _ajax("/ajs/data/producto/add/lista", "POST", {
                            lista: JSON.stringify(this.listaProd)
                        },
                            function (resp) {
                                console.log(resp);
                                if (resp.res) {
                                    alertExito("Productos agregados exitosamente")
                                        .then(function () {
                                            $("#modal-lista-productos").modal("hide");
                                            datatable.ajax.reload(null, false);
                                        });
                                } else {
                                    let errorMsg = resp.error ? resp.error : "No se pudo agregar la lista de productos";
                                    alertAdvertencia(errorMsg);
                                    console.error("Error al agregar productos:", resp.error);
                                }
                            })
                    } else {
                        alertAdvertencia("La lista está vacía");
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
                eliminarItemTablaPro(index) {
                    this.listaProd.splice(index, 1)
                },
                agregarStock() {
                    const data = {
                        ...this.restock
                    }
                    _ajax("/ajs/data/producto/restock", "POST", data,
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
                actualizarProd() {
                    let formData = new FormData();

                    // Añadir los datos al formData
                    formData.append('cod', this.edt.cod);
                    formData.append('nombre', this.edt.nombre);
                    formData.append('codigo', this.edt.codigo);
                    formData.append('detalle', this.edt.detalle);
                    formData.append('categoria', this.edt.categoria);
                    formData.append('unidad', this.edt.unidad);
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

                    // Agregar la imagen solo si fue seleccionada
                    let imagen = document.querySelector('#upload-input').files[0];
                    if (imagen) {
                        formData.append('imagen', imagen);
                    }

                    // Hacer la solicitud AJAX
                    $.ajax({
                        url: _URL + '/ajs/data/producto/edt',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            // Si usa multiprecio, guardar los precios
                            if (app._data.edt.usar_multiprecio) {
                                _ajax("/ajs/data/producto/guardar/precios", "POST", {
                                    id_producto: app._data.edt.cod,
                                    precios: app._data.precios
                                }, function (resp) {
                                    if (resp.res) {
                                        alertExito("Producto actualizado exitosamente").then(() => {
                                            $("#modal-edt-prod").modal("hide");
                                            datatable.ajax.reload(null, false);
                                        });
                                    } else {
                                        alertAdvertencia("Error al guardar los precios");
                                    }
                                });
                            } else {
                                alertExito("Producto actualizado exitosamente").then(() => {
                                    $("#modal-edt-prod").modal("hide");
                                    datatable.ajax.reload(null, false);
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error en la solicitud:", error);
                            alertAdvertencia("Hubo un error al intentar actualizar el producto.");
                        }
                    });

                },
                onImageChange(event) {
                    const file = event.target.files[0];
                    this.reg.imagen = file;
                    // Crear vista previa
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.imagePreview = null;
                    }
                },
                agregarProd() {
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
                    formData.append('almacen', this.reg.almacen);
                    formData.append('unidad', this.reg.unidad);
                    formData.append('usar_multiprecio', this.reg.usar_multiprecio ? '1' : '0');
                    formData.append('precios', JSON.stringify(this.preciosNuevos));

                    fetch(_URL + '/ajs/data/producto/add', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(resp => {
                            if (resp.res) {
                                alertExito("Agregado").then(() => {
                                    $("#modal-add-prod").modal("hide")
                                    datatable.ajax.reload(null, false) //false evita que la pag recarge
                                    this.preciosNuevos = [];
                                }

                                );
                            } else {
                                alertAdvertencia("No se pudo agregar");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alertAdvertencia("Error en la solicitud");
                        });
                },
                // Función setInfo corregida
                setInfo(data) {
                    $("#modal-edt-prod").modal("show");

                    console.log("Datos recibidos:", data);
                    console.log("Valor de usar_multiprecio en la base de datos:", data.usar_multiprecio);
                    console.log("Tipo de dato:", typeof data.usar_multiprecio);

                    // Convertir el valor de usar_multiprecio correctamente
                    // IMPORTANTE: Esto debe hacerse ANTES de la promesa
                    this.edt = {
                        usar_multiprecio: data.usar_multiprecio === '1' || data.usar_multiprecio === 1
                    };

                    console.log("Valor convertido para Vue:", this.edt.usar_multiprecio);

                    // Primero cargar las categorías y unidades
                    Promise.all([
                        $.get(_URL + "/ajs/get/categorias"),
                        $.get(_URL + "/ajs/get/unidades")
                    ]).then(([categorias, unidades]) => {
                        const cats = JSON.parse(categorias);
                        const units = JSON.parse(unidades);

                        // Poblar los selectores
                        let catOptions = '';
                        cats.forEach(cat => {
                            catOptions += `<option value="${cat.id}">${cat.nombre}</option>`;
                        });
                        $('#categoria-edt').html(catOptions);

                        let unitOptions = '';
                        units.forEach(unit => {
                            unitOptions += `<option value="${unit.id}">${unit.nombre}</option>`;
                        });
                        $('#unidades-edt').html(unitOptions);

                        // Luego setear los valores MANTENIENDO el valor de usar_multiprecio
                        this.edt = {
                            ...this.edt, // Mantener el valor de usar_multiprecio que ya establecimos
                            cod_prod: data.codigo || '', // Asegurarse de que cod_barra se asigne
                            usar_barra: data.usar_barra,
                            cod: data.id_producto,
                            nombre: data.nombre,
                            codigo: data.codigo,
                            detalle: data.detalle,
                            categoria: data.categoria,
                            unidad: data.unidad_id || data.unidad,
                            precio: data.precio,
                            costo: parseFloat(data.costo).toFixed(2),
                            almacen: data.almacen,
                            codSunat: data.codsunat,
                            afecto: data.iscbp,
                            precioMayor: data.precio_mayor,
                            precioMenor: data.precio_menor,
                            precio2: data.precio2,
                            precio3: data.precio3,
                            precio4: data.precio4,
                            cantidad: data.cantidad,
                            razon_social: data.razon_social,
                            ruc: data.ruc
                        };
                        // Si hay código de barras y usar_barra es '1', generar el código de barras
                        if (this.edt.usar_barra === '1') {
                            $("#barcode").show();
                            // Esperar a que el modal esté completamente visible
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    try {
                                        if (document.getElementById("barcode")) {
                                            // Usar el código del producto para el código de barras
                                            JsBarcode("#barcode", this.edt.codigo);
                                        }
                                    } catch (error) {
                                        console.error("Error al generar código de barras:", error);
                                    }
                                }, 300);
                            });
                        } else {
                            $("#barcode").hide();
                        }

                        // Setear los valores de los selectores
                        this.$nextTick(() => {
                            $('#categoria-edt').val(this.edt.categoria);
                            $('#unidades-edt').val(this.edt.unidad);
                        });
                        // Manejar la imagen
                        if (data.imagen) {
                            $('#img-preview').attr('src', _URL + '/public/img/productos/' + data.imagen).show();
                            $('#image-edit-button').show();
                            $('#no-image-message').hide();
                        } else {
                            $('#img-preview').hide();
                            $('#image-edit-button').hide();
                            $('#no-image-message').show();
                        }
                    }).catch(error => {
                        console.error('Error cargando datos:', error);
                        alert('Error al cargar las categorías y unidades');
                    });

                    // Cargar los precios si usa multiprecio
                    if (this.edt.usar_multiprecio) {
                        _ajax("/ajs/data/producto/obtener/precios", "POST", {
                            id_producto: data.id_producto
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
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                        $event.preventDefault();
                    }
                },
                aumentarStockProducto() {
                    if (!this.stockData.producto_id) {
                        alertAdvertencia("Debe seleccionar un producto");
                        return;
                    }

                    if (!this.stockData.cantidad_ingresar || this.stockData.cantidad_ingresar <= 0) {
                        alertAdvertencia("Debe ingresar una cantidad válida");
                        return;
                    }

                    const data = {
                        producto_id: this.stockData.producto_id,
                        cantidad: this.stockData.cantidad_ingresar
                    };

                    _ajax("/ajs/data/producto/aumentar/stock", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Stock aumentado exitosamente").then(() => {
                                $("#modal-aumentar-stock").modal("hide");
                                datatable.ajax.reload(null, false);
                                // Limpiar formulario
                                app._data.stockData = {
                                    producto_id: '',
                                    stock_actual: '',
                                    cantidad_ingresar: '',
                                    producto_nombre: ''
                                };
                                $('#buscar-producto-stock').val('');
                            });
                        } else {
                            alertAdvertencia("Error al aumentar el stock");
                        }
                    });
                }
            },

        })

        datatable = $("#datatable").DataTable({
            order: [[0, 'ASC']],
            "processing": true,
            "serverSide": true,
            "sAjaxSource": _URL + "/ajs/server/sider/productos",
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
                                $('#cep').prop('checked') ? 'CEP' :
                                    $('#pad').prop('checked') ? 'PAD' :
                                        $('#port').prop('checked') ? 'PORT' :
                                            $('#acc').prop('checked') ? 'ACC' : ''
                    }
                );
            },
            "drawCallback": function (settings) {
                // Callback después de que la tabla se ha redibujado
                $("#datatable_processing").hide();

                // Esperar un momento antes de restaurar estados
                setTimeout(() => {
                    if (localStorage.getItem('idChecks')) {
                        restoreCheckboxStates();
                    }

                }, 100);
            },
            columnDefs: [
                {
                    "targets": [0],
                    "className": "text-center",
                    "width": "auto", // Cambiado a auto para que se ajuste al contenido
                    "render": function (data, type, row, meta) {
                        return '<a href="javascript:abrirModalBarras(\'' + row[0] + '\',\'' + row[0] + '\')">' + row[0] + '</a>';
                    }
                },
                {
                    "targets": [1],
                    "className": "dt-body-left",
                    "width": "auto", // La columna nombre se ajustará automáticamente
                    "render": function (data, type, row, meta) {
                        return '<div style="white-space: normal;">' + data + '</div>'; // Permite que el texto largo se ajuste
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
                        return `<input type="checkbox" class="btnCheckEliminar" data-id="${row[6]}" data-row-index="${meta.row}">`;
                    }
                }
            ],
        });
        // Reemplazar el autocomplete existente con esta versión mejorada
        $("#buscar-producto-stock").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: _URL + "/ajs/cargar/productos/" + almacenCod,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-aumentar-stock .modal-body",
            select: function (event, ui) {
                // Actualizar el valor del input con el nombre del producto
                $(this).val(ui.item.label || ui.item.nombre);

                // Actualizar los datos de Vue
                app._data.stockData.producto_id = ui.item.codigo;
                app._data.stockData.stock_actual = ui.item.cnt;
                app._data.stockData.producto_nombre = ui.item.nombre;
                $('#producto-seleccionado-id').val(ui.item.codigo);

                // Cerrar el autocomplete inmediatamente después de la selección
                $(this).autocomplete("close");

                return false; // Prevenir comportamiento por defecto
            }
            ,
            open: function () {
                $('.ui-autocomplete').css({
                    'z-index': 9999,
                    'max-width': $('#buscar-producto-stock').outerWidth() + 'px',
                    'font-size': '13px'
                });

                var input = $('#buscar-producto-stock');
                var inputOffset = input.position();

                $('.ui-autocomplete').css({
                    'left': inputOffset.left + 'px',
                    'top': (inputOffset.top + input.outerHeight() + 2) + 'px'
                });
            },
            close: function () {
                // Asegurar que el dropdown se oculte completamente
                $('.ui-autocomplete').hide();
            }
        });
        // Agregar después de la configuración del autocomplete
        $('#modal-aumentar-stock').on('hidden.bs.modal', function () {
            // Limpiar el autocomplete cuando se cierre el modal
            $('#buscar-producto-stock').val('');
            $('.ui-autocomplete').hide();

            // Limpiar los datos de Vue
            app._data.stockData = {
                producto_id: '',
                stock_actual: '',
                cantidad_ingresar: '',
                producto_nombre: ''
            };
        });

        // Limpiar autocomplete cuando se abra el modal
        $('#modal-aumentar-stock').on('shown.bs.modal', function () {
            $('.ui-autocomplete').hide();
            $('#buscar-producto-stock').focus();
        });



        // Update filter handling
        $('.filter-option').on('change', function () {
            $('.filter-option').not(this).prop('checked', false);
            datatable.ajax.reload();
        });

        $("#file-import-exel").change(function () {
            if ($("#file-import-exel").val().length > 0) {
                var fd = new FormData();
                var file = $("#file-import-exel")[0].files[0];

                // Validar tipo de archivo
                var validExtensions = ['xlsx', 'xls', 'csv'];
                var fileExt = file.name.split('.').pop().toLowerCase();
                if (!validExtensions.includes(fileExt)) {
                    alertAdvertencia("Formato de archivo no válido. Use Excel (.xlsx, .xls) o CSV");
                    $("#file-import-exel").val("");
                    return;
                }

                fd.append('file', file);

                $.ajax({
                    type: 'POST',
                    url: _URL + '/ajs/data/producto/add/exel',
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $("#loader-menor").show();
                    },
                    error: function (err) {
                        $("#loader-menor").hide();
                        console.error("Error al procesar archivo:", err);
                        alertAdvertencia("Error al procesar el archivo: " + (err.responseText || "Error desconocido"));
                        $("#file-import-exel").val("");
                    },
                    success: function (resp) {
                        $("#loader-menor").hide();
                        try {
                            resp = JSON.parse(resp);
                            if (resp.res) {
                                if (!resp.data || resp.data.length <= 1) {
                                    alertAdvertencia("El archivo está vacío o no contiene datos válidos");
                                    return;
                                }

                                var listaTemp = [];
                                var hasErrors = false;

                                // Procesar datos saltando la primera fila (encabezados)
                                for (var i = 1; i < resp.data.length; i++) {
                                    var el = resp.data[i];
                                    if (!el[0]) { // Validar producto
                                        hasErrors = true;
                                        console.error("Fila " + (i + 1) + ": Nombre de producto vacío");
                                        continue;
                                    }

                                    listaTemp.push({
                                        producto: el[0],
                                        descripcicon: el[1] || '',
                                        cantidad: el[2] || 0,
                                        costo: el[3] || 0,
                                        precio_unidad: el[4] || 0,
                                        precio: el[5] || 0,
                                        precio2: el[6] || 0,
                                        almacen: el[7] || 1,
                                        afecto: false,
                                        codigoProd: el[8] || '',
                                        unidad: el[9] || '',
                                        categoria: el[10] || '',
                                        afecto: false
                                    });
                                }

                                if (hasErrors) {
                                    alertAdvertencia("Algunos productos no se pudieron procesar. Revise la consola para más detalles.");
                                }

                                if (listaTemp.length > 0) {
                                    app._data.listaProd = listaTemp;
                                    $("#importarModal").modal("hide");
                                    $("#modal-lista-productos").modal("show");
                                } else {
                                    alertAdvertencia("No se encontraron productos válidos para importar");
                                }
                            } else {
                                alertAdvertencia(resp.error || "No se pudo procesar el archivo");
                            }
                        } catch (e) {
                            console.error("Error procesando respuesta:", e);
                            alertAdvertencia("Error procesando la respuesta del servidor");
                        }
                        $("#file-import-exel").val("");
                    }
                });
            }
        });

        var arrayIdsOkUsar = []

        $("#datatable").on("click", ".btn-re-stock", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            app._data.restock.cod = cod
            app._data.restock.cantidad = ''
            $("#modal-restock").modal("show");

        })
        $("#generarreporteProd").click(() => {
            console.log("---------------------------------------")
            const anioREd = $("#anioreporEFG").val()
            const messREd = parseInt($("#mesreprEFG").val())
            const diaRed = $("#diareporEfghg").val().length > 0 ? parseInt($("#diareporEfghg").val()) : 'nn'
            window.open(_URL +
                `/reporte/productos/pdf/${codProdT}?fecha=${anioREd}${messREd}-${diaRed}`)
            $("#modal-prodEreport").modal("hide");
        })

        $("#datatable").on("click", ".btn-reporte", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");

            codProdT = cod
            $("#modal-prodEreport").modal("show");
            //console.log(cod);

            //window.open(_URL + `/reporte/productos/pdf/${cod}`)
            /*  app._data.restock.cod = cod
             app._data.restock.cantidad = ''
             $("#modal-restock").modal("show"); */

        })

        $("#datatable").on("click", ".btn-ver-precios", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            console.log(cod);
            $("#modal-precios").modal("show");
            _ajax("/ajs/cargar/productos/precios", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    $("#modal-precios").modal("show");
                    /*  $('#precio1').val(resp.precio)
                     $('#precio2').val(resp.precio2)
                     $('#precio3').val(resp.precio3)
                     isNaN(resp.precio4) ? $('#precio4').val('') : parseFloat(resp.precio4 + "").toFixed(2) */

                    app._data.edt.precio = resp.precio == null ? parseFloat(0 + "").toFixed(2) : resp.precio
                    app._data.edt.precio2 = resp.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio2 + "").toFixed(2)
                    /*    .toFixed(2) */
                    app._data.edt.precio3 = resp.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio3 + "").toFixed(2)
                    app._data.edt.precio4 = resp.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio4 + "").toFixed(2)
                    app._data.edt.precio_unidad = resp.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio_unidad + "").toFixed(2)

                    $('#precio1').val(resp.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio + "").toFixed(2))
                    $('#precio2').val(resp.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio2 + "").toFixed(2))
                    $('#precio3').val(resp.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio3 + "").toFixed(2))
                    $('#precio3').val(resp.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio4 + "").toFixed(2))
                    $('#precio_unidad').val(resp.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio_unidad + "").toFixed(2))
                    app._data.edt.cod_prod = cod
                    /* if (resp.res) {


                    } */
                }
            )

        })

        $("#datatable").on("click", ".btn-edt", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            /*   console.log(cod); */
            _ajax("/ajs/data/producto/info", "POST", {
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

        $("#datatable").on("click", ".btn-re-foto", function (evt) {
            const cod = $(evt.currentTarget).attr("data-foto");
            $('#modal-imagen').modal('show');
            let img = `<img src='/public/img/productos/${cod}' style="width:100%">`;
            $('#imagen').html(img);
        })


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
                alertAdvertencia("Seleccione productos");
                return;
            }

            let arrayId = JSON.parse(ids)
            Swal.fire({
                title: 'Desea borrar estos productos?',
                showDenyButton: true,
                confirmButtonText: 'Si',
                denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    if (localStorage.getItem("idChecks") !== null) {
                        _ajax("/ajs/data/producto/delete", "POST", {
                            arrayId
                        },
                            function (resp) {
                                console.log(resp);
                                if (resp.res) {
                                    localStorage.removeItem('idChecks');
                                    Swal.fire('Buen trabajo',
                                        'Productos borrados exitosamente',
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
                        alertAdvertencia("Seleccione productos")
                    }
                }
            })
        });
    });

    $(document).ready(function () {
        $('#add-prod').click(function () {
            $.get(_URL + "/ajs/get/categorias", function (data, textStatus, jqXHR) {
                let option = '<option value="">Seleccione una categoría</option>'; // Opción por defecto
                let resp = JSON.parse(data);
                $.each(resp, function (i, v) {
                    option += `<option value="${v.id}">${v.nombre}</option>`;
                });

                $('#categoria').html(option);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                alertError("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
            });

            $.get(_URL + "/ajs/get/unidades", function (data, textStatus, jqXHR) {
                let option = '<option value="">Seleccione una unidad</option>'; // Opción por defecto
                let resp = JSON.parse(data);
                $.each(resp, function (i, v) {
                    option += `<option value="${v.id}">${v.nombre}</option>`;
                });

                $('#unidades').html(option);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las unidades: " + textStatus, errorThrown);
                alertError("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
            });

            $('#modal-add-prod').modal('show');
        });
        $('#submitCategoria').click(function () {
            $.post("/ajs/save/categorias", {
                nombre: $('#nombreCategoria').val()
            }, function (data, textStatus, jqXHR) {
                Swal.fire({
                    title: "Exito",
                    text: "Se guardo correctamente",
                    icon: "success"
                });
                $('#modalCategoria').modal('hide');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categorías: " + textStatus, errorThrown);
                alert("No se pudo cargar las categorías. Por favor, intenta nuevamente.");
            });
        })
    });



    function truncateText(text, maxLength) {
        if (text == null) {
            return null; // Devuelve null si el texto es null o undefined
        }
        if (text.length > maxLength) {
            return text.substring(0, maxLength) + '...';
        }
        return text;
    }
    let imageMenuOpen = false;

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                $('#img-preview')
                    .attr('src', e.target.result)
                    .show();
                $('#image-edit-button').show();
                $('#no-image-message').hide();
                $('#image-menu').hide();
                imageMenuOpen = false;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleImageMenu() {
        imageMenuOpen = !imageMenuOpen;
        $('#image-menu').fadeToggle(200);
    }

    function changeImage() {
        $('#upload-input').click();
        $('#image-menu').hide();
        imageMenuOpen = false;
    }

    function removeImage() {
        $('#upload-input').val('');
        $('#img-preview').hide();
        $('#image-edit-button').hide();
        $('#no-image-message').show();
        $('#image-menu').hide();
        imageMenuOpen = false;
    }

    // Cerrar el menú al hacer clic fuera
    $(document).click(function (e) {
        if (!$(e.target).closest('#image-edit-button').length) {
            $('#image-menu').hide();
            imageMenuOpen = false;
        }
    });


</script>