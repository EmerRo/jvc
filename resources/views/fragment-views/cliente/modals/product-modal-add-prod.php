
            <!-- Modal de Agregar Producto Rediseñado -->
            <div class="modal fade" id="modal-add-prod" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
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
                                            <option value="">Seleccione categoría</option>
                                            <!-- Las opciones se cargan dinámicamente -->
                                        </select>
                                    </div>

                                    <!-- Tercera fila: Precio, Costo y Cantidad -->
                                    <div class="form-group col-md-4 mb-3">
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio
                                            Venta</label>
                                        <input v-model="reg.precio" @keypress="onlyNumber" type="text"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-3">
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Costo</label>
                                        <input v-model="reg.costo" @keypress="onlyNumber" type="text"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-3">
                                        <label><i class="fa fa-cubes me-1"></i>Cantidad</label>
                                        <input v-model="reg.cantidad" @keypress="onlyNumber" type="text"
                                            class="form-control" required>
                                    </div>

                                    <!-- Cuarta fila: Unidades, Almacén y Código Sunat -->
                                    <div class="form-group col-md-4 mb-3">
                                        <label><i class="fa fa-ruler me-1"></i>Unidades</label>
                                        <select v-model="reg.unidad" class="form-control" id="unidades" required>
                                            <option value="">Seleccione unidad</option>
                                            <!-- Las opciones se cargan dinámicamente -->
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-3">
                                        <label><i class="fa fa-warehouse me-1"></i>Almacén</label>
                                        <select v-model="reg.almacen" class="form-control" required>
                                            <option value="1">Almacén 1</option>
                                            <option value="2">Almacén 2</option>
                                            <option value="3">Almacén 3</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mb-3">
                                        <label><i class="fa fa-money-bill-wave me-1"></i>Moneda</label>
                                        <select v-model="reg.moneda" class="form-control" required>
                                            <option value="PEN">Soles (PEN)</option>
                                            <option value="USD">Dólares (USD)</option>
                                        </select>
                                    </div>

                                    <!-- Quinta fila: Código Sunat -->
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
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio Distribuidor</label>
                                        <input v-model="reg.precio1" @keypress="onlyNumber" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4 mb-3">
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio Mayorista</label>
                                        <input v-model="reg.precio2" @keypress="onlyNumber" type="text"
                                            class="form-control">
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
                                            <input v-model="reg.usar_multiprecio" class="form-check-input"
                                                type="checkbox" id="usar_multiprecio_add"
                                                style="width: 3em; height: 1.5em;">
                                            <label class="form-check-label ms-2" for="usar_multiprecio_add"
                                                :class="{'text-danger': reg.usar_multiprecio, 'text-secondary': !reg.usar_multiprecio}">
                                                {{ reg.usar_multiprecio ? 'Sí' : 'No' }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Campos ocultos -->
                                    <div class="form-group col-md-4" hidden>
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio 3</label>
                                        <input v-model="reg.precio3" @keypress="onlyNumber" value="0" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4" hidden>
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaReg }}</span>Precio 4</label>
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
                                                    <th style="width: 15%; padding: 4px 8px; text-align: center;">
                                                        Opciones
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(precio, index) in preciosNuevos" :key="index">
                                                    <td style="padding: 4px;">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><i
                                                                    class="fa fa-tag"></i></span>
                                                            <input v-model="precio.nombre" type="text"
                                                                class="form-control" placeholder="Nombre del precio">
                                                        </div>
                                                    </td>
                                                    <td style="padding: 4px;">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><span class="me-1"
                                                                    style="font-weight: bold;">S/</span></span>
                                                            <input v-model="precio.precio" @keypress="onlyNumber"
                                                                type="text" class="form-control" placeholder="0.00">
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
                                                    <td colspan="3" class="text-center text-muted"
                                                        style="padding: 4px;">
                                                        No hay precios configurados. Haga clic en "Agregar" para crear
                                                        uno.
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