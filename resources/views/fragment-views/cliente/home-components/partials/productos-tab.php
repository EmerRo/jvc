<!-- Tab de Productos -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'productos' }" id="productos" role="tabpanel">
    <!-- NUEVO: Filtro de período para productos -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-end">
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoProductos }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoProductos('hoy')">Hoy</a></li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoProductos('semana')">Esta semana</a></li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoProductos('mes')">Este mes</a></li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoProductos('anio')">Este año</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="abrirModalPersonalizadoProductos()">Personalizado</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen de productos -->
    <div class="row fade-in-up mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div
                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5"
                                src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/01.png">
                        </div>
                        <h5 class="text-uppercase fw-light text-end">Productos Vendidos</h5>
                        <h1 class="fw-bolder text-end counter-value">{{
                            dashboardData.totalProductosVendidos || 0 }}</h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">Unidades totales</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div
                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5"
                                src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/02.png">
                        </div>
                        <h5 class="fw-light text-uppercase text-end">Categorías Activas</h5>
                        <h1 class="fw-bolder text-end counter-value">{{
                            dashboardData.totalCategoriasActivas || 0 }}</h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">Con productos vendidos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div
                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5"
                                src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/03.png">
                        </div>
                        <h5 class="fw-light text-uppercase text-end">Comparativa</h5>
                        <h1 class="fw-bolder text-end counter-value"
                            v-html="comparativaProductosAnterior"></h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">{{ textoComparativaProductos }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div
                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5"
                                src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/04.png">
                        </div>
                        <h5 class="fw-light text-uppercase text-end">Rotación Promedio</h5>
                        <h1 class="fw-bolder text-end counter-value">{{ dashboardData.rotacionPromedio
                            || 0 }}</h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">Días promedio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de productos -->
    <div class="row fade-in-up">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Productos Más Vendidos - {{ periodoTextoProductos }}
                    </h4>
                    <div class="chart-container">
                        <div id="productosTopChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="productosTopChart" class="chart-container">
                            <div v-if="!hayDatosProductos" class="no-data-message">
                                <i class="fas fa-box"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Distribución de Ventas por Producto</h4>
                    <div class="chart-container">
                        <div id="distribucionProductosChartLoading" class="chart-loading"
                            v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="distribucionProductosChart" class="chart-container">
                            <div v-if="!hayDatosProductos" class="no-data-message">
                                <i class="fas fa-chart-pie"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NUEVO: Productos más vendidos por categoría -->
    <div class="row">
        <div class="col-12">
            <div class="card slide-in-left">
                <div class="card-body">
                    <h4 class="card-title mb-4">Productos Más Vendidos por Categoría - {{
                        periodoTextoProductos }}</h4>
                    <div class="chart-container">
                        <div id="productosCategoriaChartLoading" class="chart-loading"
                            v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="productosCategoriaChart" class="chart-container">
                            <div v-if="!hayDatosProductosPorCategoria" class="no-data-message">
                                <i class="fas fa-chart-bar"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card slide-in-left">
                <div class="card-body">
                    <h4 class="card-title mb-4">Detalle de Productos Más Vendidos</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Producto</th>
                                    <th>Descripción</th>
                                    <th>Unidades Vendidas</th>
                                    <th>Total Ventas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($productos_top && $productos_top->num_rows > 0): ?>
                                    <?php while ($producto = $productos_top->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $producto['codigo'] ?></td>
                                            <td><?= $producto['nombre'] ?></td>
                                            <td><?= $producto['detalle'] ?? 'Sin descripción' ?></td>
                                            <td><?= $producto['total_vendido'] ?></td>
                                            <td>S/ <?= number_format($producto['total_ventas'], 2, ".", ",") ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    @click="verDetalleProducto(<?= $producto['id_producto'] ?>)"
                                                    title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info"
                                                    @click="verEstadisticasProducto(<?= $producto['id_producto'] ?>)"
                                                    title="Ver estadísticas">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>