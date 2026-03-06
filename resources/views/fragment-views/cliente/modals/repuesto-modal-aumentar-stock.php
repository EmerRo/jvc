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
