
            <div class="modal fade" id="modal-lista-productos" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="staticBackdropLabel">Lista de productos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label"><i class="fa fa-warehouse me-1"></i><strong>Seleccione Almacén de Destino:</strong></label>
                                    <select v-model="almacenImportacion" class="form-control" required>
                                        <option value="1">Almacén 1</option>
                                        <option value="2">Almacén 2</option>
                                        <option value="3">Almacén 3</option>
                                    </select>
                                    <small class="text-muted">Todos los productos se importarán a este almacén</small>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label"><i class="fa fa-search me-1"></i><strong>Buscar Producto:</strong></label>
                                    <input v-model="buscarProductoImport" type="text" class="form-control" placeholder="Buscar por código, nombre o detalle...">
                                    <small class="text-muted">
                                        <span v-if="buscarProductoImport">
                                            Mostrando {{ productosFiltrados.length }} de {{ listaProd.length }} productos
                                        </span>
                                        <span v-else>
                                            Total: {{ listaProd.length }} productos
                                        </span>
                                    </small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label"><strong>Acciones:</strong></label>
                                    <button @click="toggleModoEdicion" type="button" 
                                        :class="modoEdicion ? 'btn btn-warning w-100' : 'btn border-rojo bg-white w-100'">
                                        <i :class="modoEdicion ? 'fa fa-eye' : 'fa fa-edit'"></i>
                                        {{ modoEdicion ? 'Ver' : 'Editar' }}
                                    </button>
                                </div>
                            </div>
                            <table class="table table-sm table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Detalle</th>
                                        <th>Cnt</th>
                                        <th>Costo</th>
                                        <th>Precio Venta</th>
                                        <th>Precio Distribuidor</th>
                                        <th>Precio Mayorista</th>
                                        <th>Codigo</th>
                                        <!-- <th>Almacen</th> -->
                                        <!-- <th>Unidades</th>
                                        <th>Categorías</th> -->
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in productosFiltrados" :key="index">
                                        <!-- Modo Edición -->
                                        <template v-if="modoEdicion">
                                            <td>
                                                <input type="text" v-model="item.producto" class="form-control form-control-sm" style="min-width: 150px;">
                                            </td>
                                            <td>
                                                <textarea v-model="item.descripcicon" class="form-control form-control-sm" 
                                                    style="white-space: pre-line; text-align: left; min-width: 200px; min-height: 60px;" 
                                                    rows="3"></textarea>
                                            </td>
                                            <td>
                                                <input type="number" v-model="item.cantidad" step="0.01" class="form-control form-control-sm" style="width: 80px;">
                                            </td>
                                            <td>
                                                <input type="number" v-model="item.costo" step="0.01" class="form-control form-control-sm" style="width: 90px;">
                                            </td>
                                            <td>
                                                <input type="number" v-model="item.precio_unidad" step="0.01" class="form-control form-control-sm" style="width: 100px;">
                                            </td>
                                            <td>
                                                <input type="number" v-model="item.precio_mayor" step="0.01" class="form-control form-control-sm" style="width: 120px;">
                                            </td>
                                            <td>
                                                <input type="number" v-model="item.precio_menor" step="0.01" class="form-control form-control-sm" style="width: 120px;">
                                            </td>
                                            <td>
                                                <input type="text" v-model="item.codigoProd" class="form-control form-control-sm" style="width: 100px;">
                                            </td>
                                        </template>
                                        
                                        <!-- Modo Vista -->
                                        <template v-else>
                                            <td>{{item.producto}}</td>
                                            <td style="white-space: pre-line; text-align: left;">{{item.descripcicon}}</td>
                                            <td>{{parseFloat(item.cantidad || 0).toFixed(2)}}</td>
                                            <td>{{parseFloat(item.costo || 0).toFixed(2)}}</td>
                                            <td>{{parseFloat(item.precio_unidad || 0).toFixed(2)}}</td>
                                            <td>{{parseFloat(item.precio_mayor || 0).toFixed(2)}}</td>
                                            <td>{{parseFloat(item.precio_menor || 0).toFixed(2)}}</td>
                                            <td>{{item.codigoProd}}</td>
                                        </template>
                                        
                                        <!-- <td>{{item.almacen}}</td> -->
                                        <!-- <td>{{item.unidad}}</td>
                                        <td>{{item.categoria}}</td> -->
                                        <td><button @click="eliminarItemTablaPro(listaProd.indexOf(item))"
                                                class="btn-sm btn btn-danger"><i class="fa fa-times"></i></button></td>
                                    </tr>
                                    <tr v-if="productosFiltrados.length === 0">
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fa fa-search me-2"></i>No se encontraron productos que coincidan con la búsqueda
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button @click="agregarListaImport" type="button" class="btn bg-rojo">Guardar</button>
                            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cancelar</button>

                        </div>
                    </div>
                </div>
            </div>