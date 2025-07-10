<!-- resources\views\fragment-views\cliente\ordenTrabajo\repuestos.php -->
<?php

require_once "app/models/Repuesto.php";
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
            <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); background: #fff;">
                <div class="card-header" style="background: #fff; border-bottom: none; padding-bottom: 0;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Lista de Repuestos</h4>
                        <div class="text-end mt-2 mt-md-0">
                            <button onclick="descarFunccc()" class="btn bg-white text-rojo border-rojo"><i class="fa fa-file-excel"></i> Descargar Excel por búsqueda</button>
                            <button data-bs-toggle="modal" data-bs-target="#importarModal" class="btn bg-white text-rojo border-rojo"><i class="fa fa-file-excel"></i> Importar</button>
                            <button class="btn border-rojo bg-white" data-bs-toggle="modal" data-bs-target="#modal-aumentar-stock"><i class="fa fa-plus"></i> Aumentar Stock de Repuestos</button>
                            <a href="/unidades/repuestos" class="btn bg-white text-rojo border-rojo button-link"><i class="fa fa-plus"></i> Unidades</a>
                            <a href="/categorias/repuestos" class="btn bg-white text-rojo border-rojo button-link"><i class="fa fa-plus"></i> Categorias</a>
                            <button class="btn bg-rojo text-white bordes" id="add-rep"><i class="fa fa-plus"></i> Agregar Repuesto</button>
                            <button class="btn btn-danger btnBorrar"><i class="fa fa-trash"></i> Borrar</button>
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
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
    <!-- Modal Aumentar Stock de Repuestos -->
    <div class="modal fade" id="modal-aumentar-stock" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-box me-2"></i>Aumentar Stock de Repuestos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="aumentarStockRepuesto">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            Aquí Debes Buscar y Seleccionar un Repuesto:
                        </div>

                        <div class="form-group mb-3">
                            <label><i class="fa fa-search me-1"></i>Buscar Repuesto:</label>
                            <input type="text" id="buscar-repuesto-stock" class="form-control"
                                placeholder="Buscar por código o nombre...">
                            <input type="hidden" id="repuesto-seleccionado-id" v-model="stockData.repuesto_id">
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

    <!-- Modal para agregar repuesto -->
    <div class="modal fade" id="modal-add-rep" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-tools me-2"></i>Nuevo Repuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="agregarRep">
                    <div class="modal-body">
                        <div class="row g-2">
                            <!-- Primera fila -->
                            <div class="col-md-8 mb-2">
                                <label><i class="fa fa-tag me-1"></i>Nombre de repuesto</label>
                                <input v-model="reg.nombre" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-barcode me-1"></i>Código</label>
                                <input v-model="reg.codigo" type="text" class="form-control" required>
                            </div>

                            <!-- Segunda fila - Compatibilidad, Categoría y Subcategoría -->
                            <div class="col-md-8 mb-2">
                                <label><i class="fa fa-cogs me-1"></i>Compatibilidad</label>
                                <textarea v-model="reg.detalle" class="form-control" style="height: 120px;"></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label><i class="fa fa-folder me-1"></i>Categoría</label>
                                    <select v-model="reg.categoria" class="form-control" @change="cargarSubcategorias"
                                        required>
                                        <option value="" disabled>Seleccione una categoría</option>
                                        <option v-for="cat in categorias" :value="cat.id">{{ cat.nombre }}</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label><i class="fa fa-folder-open me-1"></i>Subcategoría</label>
                                    <select v-model="reg.subcategoria" class="form-control" :disabled="!reg.categoria">
                                        <option value="" disabled>Seleccione una subcategoría</option>
                                        <option v-for="subcat in subcategorias" :value="subcat.id">{{ subcat.nombre }}
                                        </option>
                                    </select>
                                    <div v-if="reg.categoria && subcategorias.length > 0"
                                        class="text-success small mt-1">
                                        <i class="fa fa-check-circle"></i> Hay subcategorías disponibles
                                    </div>
                                    <div v-if="reg.categoria && subcategorias.length === 0"
                                        class="text-warning small mt-1">
                                        <i class="fa fa-exclamation-triangle"></i> Esta categoría no tiene subcategorías
                                    </div>
                                </div>
                            </div>

                            <!-- Tercera fila - Precios y Cantidad -->
                            <div class="col-md-4 mb-2">
                                <label><span class="me-1" style="font-weight: bold;">S/</span>Precio Venta</label>
                                <input v-model="reg.precio" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-money-bill me-1"></i>Costo</label>
                                <input v-model="reg.costo" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-cubes me-1"></i>Cantidad</label>
                                <input v-model="reg.cantidad" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>

                            <!-- Cuarta fila - Unidades, Almacén y Cod. Sunat -->
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-ruler me-1"></i>Unidades</label>
                                <select v-model="reg.unidad" class="form-control" required>
                                    <option value="" disabled>Seleccione una unidad</option>
                                    <option v-for="unidad in unidades" :value="unidad.id">{{ unidad.nombre }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-warehouse me-1"></i>Almacén</label>
                                <select v-model="reg.almacen" class="form-control" required>
                                    <option value="" disabled>Seleccione un almacén</option>
                                    <option value="1">Almacen 1</option>
                                    <option value="2">Almacen 2</option>
                                    <option value="3">Almacen 3</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-file-alt me-1"></i>Cod. Sunat</label>
                                <input v-model="reg.codSunat" type="text" class="form-control">
                            </div>

                            <!-- Quinta fila - Afecto ICBP y Precios adicionales -->
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-check-circle me-1"></i>Afecto ICBP</label>
                                <select v-model="reg.afecto" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-store me-1"></i>Precio Distribuidor</label>
                                <input v-model="reg.precio1" @keypress="onlyNumber" type="text" class="form-control">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-store-alt me-1"></i>Precio Mayorista</label>
                                <input v-model="reg.precio2" @keypress="onlyNumber" type="text" class="form-control">
                            </div>

                            <!-- Sexta fila - Código de barras -->
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-barcode me-1"></i>Usar Código Barra</label>
                                <div class="input-group">
                                    <select v-model="reg.usar_barra" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    <button v-if="reg.usar_barra=='1'" @click="generarCodeBarraAdd" type="button"
                                        class="btn bg-rojo text-white">Generar</button>
                                </div>
                            </div>

                            <!-- Código de barras preview para agregar -->
                            <div class="col-md-12 mt-2 text-center" v-if="reg.usar_barra=='1'">
                                <img id="barcodeAdd" class="img-fluid" />
                            </div>

                            <div class="form-group col-md-4 mt-2">
                                <label><i class="fa fa-tags me-1"></i>¿Utilizar MultiPrecio?</label>
                                <div class="form-check form-switch">
                                    <input v-model="reg.usar_multiprecio" class="form-check-input" type="checkbox"
                                        id="usar_multiprecio_add" style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2 fw-bold" for="usar_multiprecio_add"
                                        :class="{'text-danger': reg.usar_multiprecio, 'text-secondary': !reg.usar_multiprecio}">
                                        {{ reg.usar_multiprecio ? 'Sí' : 'No' }}
                                    </label>
                                </div>
                            </div>


                        </div>
                        <div v-if="reg.usar_multiprecio" class="col-md-12 mt-3">
                            <div class="card border-danger">
                                <div
                                    class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fa fa-list-ul me-2"></i>Lista de Precios</h5>
                                    <button type="button" @click="agregarPrecioNuevo" class="btn btn-sm btn-light">
                                        <i class="fa fa-plus me-1"></i> Agregar
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Nombre</th>
                                                    <th class="text-center">Precio</th>
                                                    <th class="text-center" style="width: 100px;">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(precio, index) in preciosNuevos" :key="index">
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fa fa-tag"></i></span>
                                                            <input v-model="precio.nombre" type="text"
                                                                class="form-control" placeholder="Nombre del precio">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><span class="me-1"
                                                                    style="font-weight: bold;">S/</span></span>
                                                            <input v-model="precio.precio" @keypress="onlyNumber"
                                                                type="text" class="form-control" placeholder="0.00">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button @click="eliminarPrecioNuevo(index)" type="button"
                                                            class="btn btn-sm btn-danger">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr v-if="preciosNuevos.length === 0">
                                                    <td colspan="3" class="text-center text-muted">
                                                        No hay precios configurados. Haga clic en "Agregar" para
                                                        crear uno.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-rojo text-white"><i
                                class="fa fa-save me-1"></i>Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fa fa-times me-1"></i>Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal para editar repuesto -->
    <div class="modal fade" id="modal-edt-rep" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-edit me-2"></i>Editar Repuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="actualizarRep">
                    <div class="modal-body">
                        <div class="row g-2">
                            <input v-model="edt.cod_rep" type="hidden" class="form-control">

                            <!-- Primera fila -->
                            <div class="col-md-8 mb-2">
                                <label><i class="fa fa-tag me-1"></i>Nombre de repuesto</label>
                                <input v-model="edt.nombre" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-barcode me-1"></i>Código</label>
                                <input v-model="edt.codigo" type="text" class="form-control" required>
                            </div>

                            <!-- Segunda fila - Compatibilidad, Categoría y Subcategoría -->
                            <div class="col-md-8 mb-2">
                                <label><i class="fa fa-cogs me-1"></i>Compatibilidad</label>
                                <textarea v-model="edt.detalle" class="form-control" style="height: 120px;"></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label><i class="fa fa-folder me-1"></i>Categoría</label>
                                    <select v-model="edt.categoria" class="form-control"
                                        @change="cargarSubcategoriasEdit" required>
                                        <option value="" disabled>Seleccione una categoría</option>
                                        <option v-for="cat in categorias" :value="cat.id">{{ cat.nombre }}</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label><i class="fa fa-folder-open me-1"></i>Subcategoría</label>
                                    <select v-model="edt.subcategoria" class="form-control" :disabled="!edt.categoria">
                                        <option value="" disabled>Seleccione una subcategoría</option>
                                        <option v-for="subcat in subcategoriasEdit" :value="subcat.id">{{ subcat.nombre
                                            }}</option>
                                    </select>
                                    <div v-if="edt.categoria && subcategoriasEdit.length > 0"
                                        class="text-success small mt-1">
                                        <i class="fa fa-check-circle"></i> Hay subcategorías disponibles
                                    </div>
                                    <div v-if="edt.categoria && subcategoriasEdit.length === 0"
                                        class="text-warning small mt-1">
                                        <i class="fa fa-exclamation-triangle"></i> Esta categoría no tiene subcategorías
                                    </div>
                                </div>
                            </div>

                            <!-- Tercera fila - Precios y Cantidad -->
                            <div class="col-md-4 mb-2">
                                <label><span class="me-1" style="font-weight: bold;">S/</span>Precio Venta</label>
                                <input v-model="edt.precio" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-money-bill me-1"></i>Costo</label>
                                <input v-model="edt.costo" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-cubes me-1"></i>Cantidad</label>
                                <input v-model="edt.cantidad" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>

                            <!-- Cuarta fila - Unidades, Almacén y Cod. Sunat -->
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-ruler me-1"></i>Unidades</label>
                                <select v-model="edt.unidad" class="form-control" required>
                                    <option value="" disabled>Seleccione una unidad</option>
                                    <option v-for="unidad in unidades" :value="unidad.id">{{ unidad.nombre }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-warehouse me-1"></i>Almacén</label>
                                <select v-model="edt.almacen" class="form-control" required>
                                    <option value="" disabled>Seleccione un almacén</option>
                                    <option value="1">Almacen 1</option>
                                    <option value="2">Almacen 2</option>
                                    <option value="3">Almacen 3</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-file-alt me-1"></i>Cod. Sunat</label>
                                <input v-model="edt.codSunat" type="text" class="form-control">
                            </div>

                            <!-- Quinta fila - Afecto ICBP y Precios adicionales -->
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-check-circle me-1"></i>Afecto ICBP</label>
                                <select v-model="edt.afecto" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-store me-1"></i>Precio Distribuidor</label>
                                <input v-model="edt.precioMayor" @keypress="onlyNumber" type="text"
                                    class="form-control">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-store-alt me-1"></i>Precio Mayorista</label>
                                <input v-model="edt.precioMenor" @keypress="onlyNumber" type="text"
                                    class="form-control">
                            </div>

                            <!-- Sexta fila - Código de barras -->
                            <div class="col-md-4 mb-2">
                                <label><i class="fa fa-barcode me-1"></i>Usar Código Barra</label>
                                <div class="input-group">
                                    <select v-model="edt.usar_barra" class="form-control"
                                        @change="handleUsarBarraChange">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                    <button v-if="edt.usar_barra == '1'" @click="edtGenerarCodeBarra" type="button"
                                        class="btn bg-rojo text-white">Generar</button>
                                </div>

                            </div>

                            <!-- Código de barras preview para editar -->
                            <div class="col-md-12 mt-2 text-center" v-show="edt.usar_barra == '1'"
                                id="barcode-container-edit">
                                <label><i class="fa fa-barcode me-1"></i>Código de Barras</label>
                                <div
                                    class="p-3 border rounded bg-light d-flex justify-content-center align-items-center">
                                    <img id="barcodeEdit" class="img-fluid" style="max-width: 300px;" />
                                </div>
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
                    <div v-if="edt.usar_multiprecio" class="col-md-12 mt-3">
                        <div class="card border-danger">
                            <div
                                class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fa fa-list-ul me-2"></i>Lista de Precios</h5>
                                <button type="button" @click="agregarPrecio" class="btn btn-sm btn-light">
                                    <i class="fa fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Nombre</th>
                                                <th class="text-center">Precio</th>
                                                <th class="text-center" style="width: 100px;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(precio, index) in precios" :key="index">
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                                        <input v-model="precio.nombre" type="text" class="form-control"
                                                            placeholder="Nombre del precio">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <span class="me-1" style="font-weight: bold;">S/</span>
                                                        </span>
                                                        <input v-model="precio.precio" @keypress="onlyNumber"
                                                            type="text" class="form-control" placeholder="0.00">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button @click="eliminarPrecio(index)" type="button"
                                                        class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="precios.length === 0">
                                                <td colspan="3" class="text-center text-muted">
                                                    No hay precios configurados. Haga clic en "Agregar" para crear uno.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-rojo text-white"><i
                                class="fa fa-save me-1"></i>Actualizar</button>
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal"><i
                                class="fa fa-times me-1"></i>Cerrar</button>
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
                        <button type="submit" class="btn bg-rojo text-white">Guardar</button>
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
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
                        <i class="fas fa-file-excel me-2"></i>Importar Repuestos con EXCEL
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



    <div class="modal fade" id="modal-lista-repuestos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Lista de repuestos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Repuesto</th>
                                <th>Descripcion</th>
                                <th>Cantidad</th>
                                <th>Costo</th>
                                <th>Precio Venta</th>
                                <th>Precio 1</th>
                                <th>Precio 2</th>
                                <th>Almacen</th>
                                <th>Codigo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in listaRep">
                                <td>{{item.repuesto}}</td>
                                <td>{{item.descripcicon}}</td>
                                <td> {{item.cantidad}}</td>
                                <td>{{item.costo}}</td>
                                <td>{{item.precio_unidad}}</td>
                                <td>{{item.precio}}</td>
                                <td>{{item.precio2}}</td>
                                <td>{{item.almacen}}</td>
                                <td>{{item.codigoRep}}</td>
                                <td><button @click="eliminarItemTablaRep(index)" class="btn-sm btn btn-danger"><i
                                            class="fa fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button @click="agregarListaImport" type="button" class="btn bg-rojo text-white">Guardar</button>
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cancelar</button>

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
                        <button class="btn bg-rojo text-white" id="btnImprimir" onclick="imprimir()">Imprimir</button>
                        <button class="btn bg-rojo text-white" id="btnImprimir2" onclick="imprimir2()">Imprimir
                            2</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="modal-repEreport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte De Repuesto</h5>
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
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dia</label>
                        <input id='diareporEfghg' class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="generarreporteRep" type="button" class="btn bg-rojo text-white">Generar</button>
                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-imagen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Imagen del Repuesto</h5>
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
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" id="submitCategoria" class="btn bg-rojo text-white">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="listaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Lista de Categorías</h5>
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
        var almacenCod = '<?php echo $_SESSION["sucursal"] ?>'
        let app;
        $(document).ready(function () {
            app = new Vue({
                el: "#conte-vue-modals",
                data: {

                    almacen: <?php echo $_SESSION["sucursal"] ?>,
                    t: 0,
                    listaRep: [],
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
                        usar_multiprecio: false,
                    },
                    listaIdsss: [],
                    stockData: {
                        repuesto_id: '',
                        stock_actual: '',
                        cantidad_ingresar: '',
                        repuesto_nombre: ''
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
                                lista: JSON.stringify(this.listaRep)
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

            $("#datatable").on("click", ".btn-re-foto", function (evt) {
                const cod = $(evt.currentTarget).attr("data-foto");
                $('#modal-imagen').modal('show');
                let img = `<img src='/public/img/repuestos/${cod}' style="width:100%">`;
                $('#imagen').html(img);
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
                    console.error("Error al guardar la categoría: " + textStatus, errorThrown);
                    alert("No se pudo guardar la categoría. Por favor, intenta nuevamente.");
                });
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