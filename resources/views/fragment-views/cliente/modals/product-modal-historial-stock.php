  <div class="modal fade" id="modal-historial-stock" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-history me-2"></i>Historial de Stock
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Movimiento</th>
                                            <th>Cantidad</th>
                                            <th>Costo Compra</th>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in historialStock" :key="item.id">
                                            <td>{{ item.codigo }}</td>
                                            <td>{{ item.producto_nombre }}</td>
                                            <td>
                                                <span class="badge"
                                                    :class="item.tipo_movimiento === 'INGRESO' ? 'bg-success' : 'bg-danger'">
                                                    {{ item.tipo_movimiento }}
                                                </span>
                                            </td>
                                            <td>{{ item.cantidad }}</td>
                                            <td>
                                                <span v-if="item.costo_compra">
                                                    S/ {{ parseFloat(item.costo_compra).toFixed(2) }}
                                                </span>
                                                <span v-else class="text-muted">-</span>
                                            </td>
                                            <td>{{ formatearFecha(item.fecha_movimiento) }}</td>
                                            <td>{{ item.usuario }}</td>
                                            <td>
                                                <span v-if="item.observaciones" :title="item.observaciones">
                                                    {{ item.observaciones.length > 30 ? item.observaciones.substring(0, 30) + '...' : item.observaciones }}
                                                </span>
                                                <span v-else class="text-muted">-</span>
                                            </td>
                                        </tr>
                                        <tr v-if="historialStock.length === 0">
                                            <td colspan="8" class="text-center text-muted">No hay movimientos
                                                registrados
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>