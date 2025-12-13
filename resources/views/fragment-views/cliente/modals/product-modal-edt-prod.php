 <div class="modal fade" id="modal-edt-prod" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="exampleModalLabel">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-edit me-2"></i>Editar
                                Producto
                            </h5>
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
                                        <select v-model="edt.categoria" id="categoria-edt" class="form-control" required>
                                            <option value="">Seleccione categoría</option>
                                        </select>
                                    </div>

                                    <!-- PRIMERA FILA: Precio Venta, Costo, Cantidad -->
                                    <div class="form-group col-md-4 mt-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaEdt }}</span>Precio
                                                Venta</label>
                                            <span v-if="parseFloat(edt.precio) <= 0" class="text-danger small">
                                                <i class="fa fa-exclamation-triangle"></i> Precio está en 0
                                            </span>
                                        </div>
                                        <input v-model="edt.precio" @keypress="onlyNumber" required value="0"
                                            type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4 mt-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaEdt }}</span>Costo</label>
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
                                        <select v-model="edt.unidad" id="unidades-edt" class="form-control" required>
                                            <option value="">Seleccione unidad</option>
                                            <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                                {{unit.nombre}}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mt-2">
                                        <label><i class="fa fa-warehouse me-1"></i>Almacén</label>
                                        <select v-model="edt.almacen" required class="form-control">
                                            <option value="1">Almacén 1</option>
                                            <option value="2">Almacén 2</option>
                                            <option value="3">Almacén 3</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 mt-2">
                                        <label><i class="fa fa-money-bill-wave me-1"></i>Moneda</label>
                                        <select v-model="edt.moneda" class="form-control" required>
                                            <option value="PEN">Soles (PEN)</option>
                                            <option value="USD">Dólares (USD)</option>
                                        </select>
                                    </div>

                                    <!-- TERCERA FILA: Cod. Sunat -->
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
                                            <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaEdt }}</span>Precio Distribuidor</label>
                                            <span v-if="parseFloat(edt.precioMayor) <= 0" class="text-danger small">
                                                <i class="fa fa-exclamation-triangle"></i> Precio está en 0
                                            </span>
                                        </div>
                                        <input v-model="edt.precioMayor" @keypress="onlyNumber" value="0" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4 mt-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaEdt }}</span>Precio Mayorista</label>
                                            <span v-if="parseFloat(edt.precioMenor) <= 0" class="text-danger small">
                                                <i class="fa fa-exclamation-triangle"></i> Precio está en 0
                                            </span>
                                        </div>
                                        <input v-model="edt.precioMenor" @keypress="onlyNumber" value="0" type="text"
                                            class="form-control">
                                    </div>

                                    <!-- Campos ocultos -->
                                    <div class="form-group col-md-4 mt-2" hidden>
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaEdt }}</span>Precio 3</label>
                                        <input v-model="edt.precio3" @keypress="onlyNumber" value="0" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4 mt-2" hidden>
                                        <label><span class="me-1" style="font-weight: bold;">{{ simboloMonedaEdt }}</span>Precio 4</label>
                                        <input v-model="edt.precio4" @keypress="onlyNumber" value="0" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-8 mt-3">
                                        <label class="d-flex align-items-center mb-2">
                                            <i class="fa fa-image me-2"></i>
                                            <span class="fw-bold">Imagen del Producto</span>
                                        </label>

                                        <!-- Contenedor de la imagen existente -->
                                        <div class="image-container position-relative"
                                            style="display: none; border: 1px solid #dee2e6; border-radius: 4px; overflow: hidden; background-color: #f8f9fa; text-align: center; min-height: 200px; align-items: center; justify-content: center; display: flex;">
                                            <img id="img-preview" alt="Vista previa"
                                                style="max-height: 180px; width: auto; margin: 10px;" />

                                            <!-- Botón de edición -->
                                            <div id="image-edit-button"
                                                style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                                                <button type="button" class="btn btn-light" onclick="toggleImageMenu()"
                                                    style="background-color: rgba(255, 255, 255, 0.9); border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    <i class="fa fa-pencil-alt me-1"></i>
                                                    Editar
                                                </button>

                                                <!-- Menú desplegable -->
                                                <div id="image-menu" class="position-absolute shadow-sm"
                                                    style="display: none; top: 100%; right: 0; margin-top: 5px; background-color: white; border-radius: 4px; border: 1px solid #dee2e6; min-width: 160px; z-index: 1000;">
                                                    <div class="p-2 hover-bg-light" style="cursor: pointer;"
                                                        onclick="changeImage()">
                                                        <i class="fa fa-upload me-2"></i> Cambiar foto...
                                                    </div>
                                                    <div class="p-2 text-danger hover-bg-light" style="cursor: pointer;"
                                                        onclick="removeImage()">
                                                        <i class="fa fa-trash me-2"></i> Eliminar foto
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Input oculto para subir imagen -->
                                        <input type="file" id="upload-input" name="imagen" class="d-none"
                                            accept="image/*" onchange="previewImage(this)" />

                                        <!-- Mensaje y botón para cuando NO hay imagen -->
                                        <div id="no-image-message" class="text-center p-3 border rounded bg-light mt-2"
                                            style="display: none;">
                                            <i class="fa fa-image fa-2x text-muted mb-2 d-block"></i>
                                            <p class="mb-2">No hay imagen para este producto</p>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                onclick="changeImage()">
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
                                                <button @click="edtGenerarCodeBarra" type="button"
                                                    class="btn border-rojo"><i class="fa fa-sync-alt"></i>
                                                    Generar</button>
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
                                            <input v-model="edt.usar_multiprecio" class="form-check-input"
                                                type="checkbox" id="usar_multiprecio_edit"
                                                style="width: 3em; height: 1.5em;">
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
                                                    <th style="width: 15%; padding: 4px 8px; text-align: center;">
                                                        Opciones
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(precio, index) in precios" :key="index">
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
                                                            <span class="input-group-text"><span
                                                                    style="font-weight: bold;">S/</span></span>
                                                            <input v-model="precio.precio" @keypress="onlyNumber"
                                                                type="text" class="form-control" placeholder="0.00">
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
                                <button type="submit" class="btn bg-rojo"><i
                                        class="fa fa-save me-1"></i>Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fa fa-times me-1"></i>Cerrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>