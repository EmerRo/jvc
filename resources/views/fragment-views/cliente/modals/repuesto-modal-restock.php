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