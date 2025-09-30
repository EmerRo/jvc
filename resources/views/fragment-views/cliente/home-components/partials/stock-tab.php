<!-- Tab de Stock -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'stock' }" id="stock" role="tabpanel">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoStock }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoStock('hoy')">Hoy</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoStock('semana')">Esta semana</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoStock('mes')">Este mes</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoStock('anio')">Este año</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="abrirModalPersonalizadoStock()">Personalizado</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control"
                    placeholder="Buscar productos por nombre, código..." v-model="filtroStock"
                    @input="filtrarStock">
            </div>
        </div>
    </div>
    <div class="row fade-in-up">
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Rotación de Inventario</h4>
                    <div class="chart-container">
                        <div id="rotacionInventarioChartLoading" class="chart-loading"
                            v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="rotacionInventarioChart" class="chart-container">
                            <div v-if="!hayDatosStock" class="no-data-message">
                                <i class="fas fa-boxes"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Estado del Stock</h4>
                    <div class="chart-container">
                        <div id="estadoStockChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="estadoStockChart" class="chart-container">
                            <div v-if="!hayDatosStock" class="no-data-message">
                                <i class="fas fa-chart-pie"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Alertas de Stock</h4>
                    <div class="alert alert-danger animate-pulse">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Hay productos con stock crítico.
                    </div>

                    <div style="max-height: 260px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            <?php if ($productos_stock_bajo && $productos_stock_bajo->num_rows > 0): ?>
                                <?php while ($producto = $productos_stock_bajo->fetch_assoc()): ?>
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center <?= $producto['cantidad'] <= 5 ? 'stock-alert' : '' ?>">
                                        <div>
                                            <strong><?= $producto['nombre'] ?></strong>
                                            <div class="text-muted small">Código:
                                                <?= $producto['codigo'] ?? 'Sin código' ?>
                                            </div>
                                        </div>
                                        <span
                                            class="badge <?= $producto['cantidad'] <= 5 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                            <?= $producto['cantidad'] ?> unidades
                                        </span>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="list-group-item">No hay productos con stock bajo</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card slide-in-left">
                <div class="card-body">
                    <h4 class="card-title mb-4">Movimientos de Inventario</h4>
                    <div class="chart-container">
                        <div id="movimientosInventarioChartLoading" class="chart-loading"
                            v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="movimientosInventarioChart" class="chart-container">
                            <div v-if="!hayDatosStock" class="no-data-message">
                                <i class="fas fa-exchange-alt"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>