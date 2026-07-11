 <div class="modal fade" id="modal-traslado-almacenes-repuesto" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-exchange-alt me-2"></i>Traslados Entre Almacenes - Repuestos
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form @submit.prevent="realizarTrasladoRepuesto">
                            <div class="modal-body">
                                <!-- ALMACÉN DE ORIGEN -->
                                <div class="mb-4">
                                    <h6 class="text-primary">
                                        <i class="fa fa-warehouse me-2"></i>ALMACÉN DE ORIGEN:
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><i class="fa fa-warehouse me-1"></i>Almacén Origen: *</label>
                                                <select v-model="trasladoDataRepuesto.almacen_origen" class="form-control" required
                                                    @change="limpiarRepuestoTraslado">
                                                    <option value="">Seleccione...</option>
                                                    <option value="1">Almacén 1</option>
                                                    <option value="2">Almacén 2</option>
                                                    <option value="3">Almacén 3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label><i class="fa fa-shopping-cart me-1"></i>Repuesto:</label>
                                                <input type="text" id="buscar-repuesto-traslado" class="form-control"
                                                    placeholder="Selecciona un Repuesto..."
                                                    :disabled="!trasladoDataRepuesto.almacen_origen">
                                                <input type="hidden" id="repuesto-traslado-id" v-model="trasladoDataRepuesto.repuesto_id">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- LISTA DE REPUESTOS -->
                                <div class="mb-4" v-if="trasladoDataRepuesto.repuestos.length > 0">
                                    <h6 class="text-dark">
                                        <i class="fa fa-list me-2"></i>LISTA DE REPUESTOS:
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Unidad Med</th>
                                                    <th>Cantidad</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in trasladoDataRepuesto.repuestos" :key="index">
                                                    <td>{{ item.nombre }}</td>
                                                    <td>{{ item.unidad }}</td>
                                                    <td>
                                                        <input type="number" v-model="item.cantidad" 
                                                            class="form-control form-control-sm" 
                                                            min="1" :max="item.stock_disponible" required>
                                                    </td>
                                                    <td>
                                                        <button type="button" @click="eliminarRepuestoTraslado(index)"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- ALMACÉN DESTINO -->
                                <div class="mb-3">
                                    <h6 class="text-success">
                                        <i class="fa fa-warehouse me-2"></i>ALMACÉN DESTINO:
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><i class="fa fa-warehouse me-1"></i>Almacén Destino: *</label>
                                                <select v-model="trasladoDataRepuesto.almacen_destino" class="form-control" required>
                                                    <option value="">Seleccione...</option>
                                                    <option v-for="alm in almacenes" :key="alm.id_almacen" :value="alm.id_almacen" :disabled="trasladoDataRepuesto.almacen_origen == alm.id_almacen">{{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><i class="fa fa-pencil-alt me-1"></i>Nota:</label>
                                                <input type="text" v-model="trasladoDataRepuesto.nota" class="form-control"
                                                    placeholder="Cantidad">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning" v-if="trasladoDataRepuesto.almacen_origen && trasladoDataRepuesto.almacen_destino && trasladoDataRepuesto.almacen_origen === trasladoDataRepuesto.almacen_destino">
                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                    El almacén de origen y destino no pueden ser el mismo.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i>Cerrar
                                </button>
                                <button type="submit" class="btn bg-rojo text-white" 
                                    :disabled="trasladoDataRepuesto.repuestos.length === 0 || trasladoDataRepuesto.almacen_origen === trasladoDataRepuesto.almacen_destino">
                                    <i class="fa fa-check me-1"></i>Iniciar Traslado!
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
