<!-- Tab de Ventas -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'ventas' }" id="ventas" role="tabpanel">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-end">
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTexto }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('hoy')">Hoy</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('semana')">Esta semana</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('mes')">Este mes</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('anio')">Este año</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="abrirModalPersonalizado()">Personalizado</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de resumen -->
    <div class="row fade-in-up">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5" src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/01.png">
                        </div>
                        <h5 class="text-uppercase fw-light text-end">Monto Vendido</h5>
                        <h1 class="fw-bolder text-end counter-value">S/ {{ formatNumber(dashboardData.totalVentas) }}</h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">Facturas y Boletas</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5" src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/03.png">
                        </div>
                        <h5 class="fw-light text-uppercase text-end">Total en Facturas</h5>
                        <h1 class="fw-bolder text-end counter-value">S/ {{ formatNumber(dashboardData.totalFacturas) }}</h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">{{ calcularPorcentajeFacturas }}% del total</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5" src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/04.png">
                        </div>
                        <h5 class="fw-light text-uppercase text-end">Total en Boletas</h5>
                        <h1 class="fw-bolder text-end counter-value">S/ {{ formatNumber(dashboardData.totalBoletas) }}</h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">{{ calcularPorcentajeBoletas }}% del total</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-white text-dark">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                            <img class="mt-3 mr-5" src="<?= isset($base_url) ? $base_url : '' ?>public/assets/images/services-icon/02.png">
                        </div>
                        <h5 class="fw-light text-uppercase text-end">Comparativa</h5>
                        <h1 class="fw-bolder text-end counter-value" v-html="comparativaMesAnterior"></h1>
                    </div>
                    <div class="pt-2">
                        <p class="mb-0 mt-1 text-end">{{ dashboardData.textosPeriodo ? dashboardData.textosPeriodo.comparativa : 'vs. Mes Anterior' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de ventas -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card slide-in-left">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ dashboardData.textosPeriodo ? dashboardData.textosPeriodo.titulo_principal : 'Ventas Anuales' }}</h4>
                    <div class="chart-container">
                        <div id="ventasAnualesChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="ventasAnualesChart" class="chart-container">
                            <div v-if="!hayDatosVentasAnuales" class="no-data-message">
                                <i class="fas fa-chart-line"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card slide-in-right">
                <div class="card-body">
                    <h4 class="card-title mb-4">Comparativa Períodos</h4>
                    <div class="chart-container">
                        <div id="comparativaChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="comparativaChart" class="chart-container">
                            <div v-if="!hayDatosComparativa" class="no-data-message">
                                <i class="fas fa-chart-bar"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparativa con años anteriores y Utilidad Bruta -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card fade-in-up">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ obtenerTituloComparativa() }}</h4>
                    <div class="chart-container">
                        <div id="comparativaAnualChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="comparativaAnualChart" class="chart-container">
                            <div v-if="!hayDatosVentasAnuales" class="no-data-message">
                                <i class="fas fa-chart-line"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card fade-in-up">
                <div class="card-body">
                    <h4 class="card-title mb-4">Utilidad Bruta - {{ dashboardData.textosPeriodo ? dashboardData.textosPeriodo.titulo_principal : 'Período Actual' }}</h4>
                    <div class="chart-container">
                        <div id="utilidadBrutaChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="utilidadBrutaChart" class="chart-container">
                            <div v-if="!hayDatosUtilidadBruta" class="no-data-message">
                                <i class="fas fa-chart-bar"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                    <!-- Resumen de utilidad -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-6">
                                <h5 class="text-success mb-1">S/ {{ formatNumber(dashboardData.utilidadBrutaActual) }}</h5>
                                <small class="text-muted">Utilidad Actual</small>
                            </div>
                            <div class="col-6">
                                <h5 class="mb-1" :class="porcentajeUtilidadClass">{{ porcentajeUtilidad }}%</h5>
                                <small class="text-muted">Margen Bruto</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>