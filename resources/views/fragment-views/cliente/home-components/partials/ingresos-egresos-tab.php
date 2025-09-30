<!-- Tab de Ingresos y Egresos -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'ingresos-egresos' }"
    id="ingresos-egresos" role="tabpanel">
    <!-- NUEVO: Filtro de período para Ingresos y Egresos -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-end">
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoIngresos }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoIngresos('hoy')">Hoy</a></li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoIngresos('semana')">Esta semana</a></li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoIngresos('mes')">Este mes</a></li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="cambiarPeriodoIngresos('anio')">Este año</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#"
                            @click.prevent="abrirModalPersonalizadoIngresos()">Personalizado</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row fade-in-up">
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Ingresos Mensuales</h4>
                    <h2 class="counter-value text-success">S/ {{
                        formatNumber(dashboardData.ingresosMensuales) }}</h2>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted mt-3">
                        <i class="fas fa-arrow-up text-success me-1"></i>
                        {{ calcularPorcentajeIngresos }}% vs. mes anterior
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Egresos Mensuales</h4>
                    <h2 class="counter-value text-danger">S/ {{
                        formatNumber(dashboardData.egresosMensuales)
                        }}</h2>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-danger" role="progressbar"
                            :style="{ width: porcentajeEgresos + '%' }"
                            :aria-valuenow="porcentajeEgresos" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <p class="text-muted mt-3">
                        <i class="fas fa-arrow-down text-danger me-1"></i>
                        {{ porcentajeEgresos }}% de los ingresos
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Ganancia Neta</h4>
                    <h2 class="counter-value text-primary">S/ {{
                        formatNumber(dashboardData.gananciaMensual)
                        }}</h2>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-primary" role="progressbar"
                            :style="{ width: porcentajeGanancia + '%' }"
                            :aria-valuenow="porcentajeGanancia" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <p class="text-muted mt-3">
                        <i class="fas fa-check-circle text-primary me-1"></i>
                        Margen de ganancia: {{ porcentajeGanancia }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card slide-in-left">
                <div class="card-body">
                    <h4 class="card-title mb-4">Evolución de Ingresos y Egresos</h4>
                    <div class="chart-container">
                        <div id="ingresosEgresosChartLoading" class="chart-loading"
                            v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="ingresosEgresosChart" class="chart-container">
                            <div v-if="!hayDatosIngresos" class="no-data-message">
                                <i class="fas fa-money-bill-wave"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <!-- REEMPLAZAR el gráfico único por dos gráficos -->
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card slide-in-right">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Distribución de Ingresos</h4>
                            <div class="chart-container">
                                <div id="distribucionIngresosChartLoading" class="chart-loading"
                                    v-if="loadingCharts">
                                    <div class="spinner"></div>
                                </div>
                                <div ref="distribucionIngresosChart" class="chart-container">
                                    <div v-if="!hayDatosIngresos" class="no-data-message">
                                        <i class="fas fa-chart-pie"></i>
                                        <p>No hay datos disponibles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card slide-in-right">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Distribución de Egresos</h4>
                            <div class="chart-container">
                                <div id="distribucionEgresosChartLoading" class="chart-loading"
                                    v-if="loadingCharts">
                                    <div class="spinner"></div>
                                </div>
                                <div ref="distribucionEgresosChart" class="chart-container">
                                    <div v-if="!hayDatosIngresos" class="no-data-message">
                                        <i class="fas fa-chart-pie"></i>
                                        <p>No hay datos disponibles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>