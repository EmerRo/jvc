 <div class="modal fade" id="modal-disminuir-stock-repuesto" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-minus-circle me-2"></i>Disminuir Stock de Repuestos
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form @submit.prevent="disminuirStockRepuesto">
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fa fa-shopping-cart me-2"></i>
                                    Aquí Debes Buscar y Seleccionar un Repuesto:
                                </div>

                                <div class="form-group mb-3">
                                    <label><i class="fa fa-search me-1"></i>Buscar Repuesto:</label>
                                    <input type="text" id="buscar-repuesto-disminuir" class="form-control"
                                        placeholder="Buscar por código o nombre...">
                                    <input type="hidden" id="repuesto-disminuir-id" v-model="disminuirDataRepuesto.repuesto_id">
                                </div>

                                <div class="alert alert-primary" v-if="disminuirDataRepuesto.repuesto_nombre">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <strong>Almacén {{ almacen }}</strong>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-cubes me-1"></i>Stock Actual:</label>
                                            <div class="input-group">
                                                <span class="input-group-text" v-if="disminuirDataRepuesto.unidad">{{ disminuirDataRepuesto.unidad }}</span>
                                                <input type="text" class="form-control" v-model="disminuirDataRepuesto.stock_actual"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-minus-circle me-1"></i>Cant. a Disminuir *:</label>
                                            <div class="input-group">
                                                <span class="input-group-text" v-if="disminuirDataRepuesto.unidad">{{ disminuirDataRepuesto.unidad }}</span>
                                                <input type="number" class="form-control"
                                                    v-model="disminuirDataRepuesto.cantidad_disminuir" min="1" 
                                                    :max="disminuirDataRepuesto.stock_actual"
                                                    required placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label><i class="fa fa-clipboard me-1"></i>Observación:</label>
                                    <textarea class="form-control" v-model="disminuirDataRepuesto.observaciones" 
                                        rows="3" placeholder="Escribe aquí una observación"></textarea>
                                </div>

                                <div class="alert alert-warning" v-if="disminuirDataRepuesto.cantidad_disminuir > disminuirDataRepuesto.stock_actual">
                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                    La cantidad a disminuir no puede ser mayor al stock actual.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i>Cerrar
                                </button>
                                <button type="submit" class="btn btn-primary" 
                                    :disabled="disminuirDataRepuesto.cantidad_disminuir > disminuirDataRepuesto.stock_actual">
                                    <i class="fa fa-check me-1"></i>Sí, Disminuir Stock Ahora!
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
