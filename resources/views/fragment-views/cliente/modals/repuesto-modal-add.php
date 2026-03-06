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
                                <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio Venta</label>
                                <input v-model="reg.precio" @keypress="onlyNumber" type="text" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Costo</label>
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
                                <label><i class="fa fa-dollar-sign me-1"></i>Moneda</label>
                                <select v-model="reg.moneda" class="form-control" required>
                                    <option value="" disabled>Seleccione una moneda</option>
                                    <option value="PEN">PEN - Soles</option>
                                    <option value="USD">USD - Dólares</option>
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
                                <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio Distribuidor</label>
                                <input v-model="reg.precio1" @keypress="onlyNumber" type="text" class="form-control">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio Mayorista</label>
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
