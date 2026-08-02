<div class="modal fade" id="modal-agregar-almacen-repuesto" tabindex="-1" aria-labelledby="modalAgregarAlmacenRepLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-rojo text-white">
                <h6 class="modal-title" id="modalAgregarAlmacenRepLabel">
                    <i class="fa fa-warehouse me-2"></i>Gestionar Almacenes
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Almacenes</label>
                    <div class="list-group" style="max-height: 200px; overflow-y: auto;">
                        <div v-for="alm in almacenes" :key="alm.id_almacen"
                             class="list-group-item d-flex justify-content-between align-items-center"
                             :class="{'active': almacenEditando && almacenEditando.id_almacen == alm.id_almacen}"
                             style="cursor: pointer;" @click="seleccionarAlmacen(alm)">
                            <span>
                                <i class="fa fa-warehouse me-2"></i>{{ alm.nombre }}
                                <span v-if="alm.principal == 1" class="badge bg-warning text-dark ms-2">★ Principal</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-light" @click.stop="seleccionarAlmacen(alm)">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr v-if="almacenEditando">
                <div v-if="almacenEditando">
                    <div class="form-group mb-2">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" v-model="nuevoAlmacen" placeholder="Nombre del almacén">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="marcarPrincipalRep" v-model="marcarPrincipal">
                        <label class="form-check-label" for="marcarPrincipalRep">
                            <i class="fa fa-star text-warning me-1"></i>Marcar como Principal
                        </label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm flex-grow-1" @click="guardarAlmacenEdicion()">
                            <i class="fa fa-save me-1"></i> Actualizar
                        </button>
                        <button v-if="almacenEditando.principal != 1" type="button" class="btn btn-danger btn-sm" @click="eliminarAlmacen()">
                            <i class="fa fa-trash me-1"></i>
                        </button>
                    </div>
                </div>

                <hr v-if="almacenEditando">
                <div class="mt-2">
                    <label class="form-label">{{ almacenEditando ? 'Agregar otro' : 'Nuevo Almacén' }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="nuevoAlmacenNuevo" placeholder="Nombre del nuevo almacén" @keyup.enter="agregarNuevoAlmacen()">
                        <button type="button" class="btn btn-success" @click="agregarNuevoAlmacen()">
                            <i class="fa fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
