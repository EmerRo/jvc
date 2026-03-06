  <div class="modal fade" id="modal-lista-repuestos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-rojo text-white">
                    <h5 class="modal-title" id="staticBackdropLabel">Lista de repuestos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fa fa-warehouse me-1"></i><strong>Seleccione Almacén de Destino:</strong></label>
                            <select v-model="almacenImportacionRep" class="form-control" required>
                                <option value="1">Almacén 1</option>
                                <option value="2">Almacén 2</option>
                                <option value="3">Almacén 3</option>
                            </select>
                            <small class="text-muted">Todos los repuestos se importarán a este almacén</small>
                        </div>
                    </div>
                    <table class="table table-sm table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Repuesto</th>
                                <th>Detalle</th>
                                <th>Cnt</th>
                                <th>Costo</th>
                                <th>Precio Venta</th>
                                <th>Precio Distribuidor</th>
                                <th>Precio Mayorista</th>
                                <th>Codigo</th>
                                <!-- <th>Almacen</th> -->
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in listaRep">
                                <td>{{item.repuesto}}</td>
                                <td>{{item.descripcicon}}</td>
                                <td>{{parseFloat(item.cantidad || 0).toFixed(2)}}</td>
                                <td>{{parseFloat(item.costo || 0).toFixed(2)}}</td>
                                <td>{{parseFloat(item.precio_unidad || 0).toFixed(2)}}</td>
                                <td>{{parseFloat(item.precio || 0).toFixed(2)}}</td>
                                <td>{{parseFloat(item.precio2 || 0).toFixed(2)}}</td>
                                <td>{{item.codigoRep}}</td>
                                <!-- <td>{{item.almacen}}</td> -->
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
