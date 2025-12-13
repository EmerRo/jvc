
<div class="modal fade" id="modal-aumentar-stock" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-medio">
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
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-cubes me-1"></i>Stock Actual:</label>
                                            <input type="text" class="form-control" v-model="stockData.stock_actual"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-plus-circle me-1"></i>Cant. a Ingresar *:</label>
                                            <input type="number" class="form-control"
                                                v-model="stockData.cantidad_ingresar" min="1" required placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa fa-dollar-sign me-1"></i>Precio de Compra *:</label>
                                            <div class="input-group">
                                                <select class="form-select" v-model="stockData.moneda" style="max-width: 80px;">
                                                    <option value="PEN">S/</option>
                                                    <option value="USD">$</option>
                                                </select>
                                                <input type="number" class="form-control" step="0.01"
                                                    v-model="stockData.costo_compra" min="0" required placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label><i class="fa fa-clipboard me-1"></i>Observación:</label>
                                    <textarea class="form-control" v-model="stockData.observaciones" 
                                        rows="3" placeholder="Escribe aquí una observación"></textarea>
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