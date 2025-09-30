<!-- Tab de Cotizaciones -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'cotizaciones' }" id="cotizaciones"
    role="tabpanel">
    <!-- Filtro de período superior -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoCotizaciones }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoCotizaciones('hoy')">Hoy</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoCotizaciones('mes')">Este mes</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoCotizaciones('trimestre')">Trimestre</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoCotizaciones('semestre')">Semestre</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoCotizaciones('año')">Año</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="abrirModalPersonalizadoCotizaciones()">Personalizado</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row fade-in-up">
        <!-- Tarjetas de resumen -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-2">Total Cotizaciones</p>
                            <h2 class="mb-0">{{ datosCotizaciones.total_cotizaciones || 0 }}</h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-calendar me-1"></i>{{ periodoTextoCotizaciones }}
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-light text-rojo rounded-circle fs-2">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-box text-primary me-2"></i>Productos Más Cotizados
                    </h4>
                    <div v-if="datosCotizaciones.productos_mas_cotizados && datosCotizaciones.productos_mas_cotizados.length">
                        <div class="row">
                            <div v-for="(producto, index) in datosCotizaciones.productos_mas_cotizados.slice(0, 6)" 
                                 :key="producto.id_producto" class="col-md-4 mb-3">
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-primary text-white rounded">
                                                {{ index + 1 }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 text-truncate" style="max-width: 150px;" :title="producto.nombre">
                                            {{ producto.nombre || 'Producto sin nombre' }}
                                        </h6>
                                        <p class="text-muted mb-0">
                                            <small><strong>{{ producto.total_cotizado }}</strong> veces cotizado</small>
                                        </p>
                                        <p class="text-success mb-0">
                                            <small>S/ {{ parseFloat(producto.precio_promedio || 0).toLocaleString('es-PE', {minimumFractionDigits: 2}) }} promedio</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay productos cotizados</h5>
                        <p class="text-muted">Los productos aparecerán aquí cuando se registren cotizaciones</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de cotizaciones por mes -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Evolución de Cotizaciones</h4>
                    </div>

                    <!-- Gráfico principal -->
                    <div class="chart-container" style="height: 400px;">
                        <div ref="cotizacionesChart" class="chart-container">
                            <div v-if="!hayDatosCotizaciones" class="no-data-message text-center py-5">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay cotizaciones registradas</h5>
                                <p class="text-muted">Las cotizaciones aparecerán aquí cuando se registren</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-xl-4">
            <!-- Clientes con más cotizaciones -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Clientes con Más Cotizaciones</h4>
                    <div v-if="datosCotizaciones.top_clientes && datosCotizaciones.top_clientes.length">
                        <div v-for="(cliente, index) in datosCotizaciones.top_clientes.slice(0, 5)" 
                             :key="cliente.id_cliente" class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-primary text-white rounded-circle">
                                        {{ (cliente.datos || cliente.nombre || 'C').charAt(0).toUpperCase() }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ cliente.datos || cliente.nombre || 'Cliente sin nombre' }}</h6>
                                <p class="text-muted mb-0">{{ cliente.total_cotizaciones }} cotizaciones</p>
                                <small class="text-muted">S/ {{ parseFloat(cliente.valor_total || 0).toLocaleString('es-PE', {minimumFractionDigits: 2}) }}</small>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ index + 1 }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No hay datos de clientes</p>
                    </div>
                </div>
            </div>

            <!-- Estados de cotizaciones -->
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title mb-4">Estados de Cotizaciones</h4>
                    <div class="chart-container" style="height: 250px;">
                        <div ref="estadosCotizacionesChart" class="chart-container">
                            <div v-if="!datosCotizaciones.estados_cotizaciones || !datosCotizaciones.estados_cotizaciones.length" 
                                 class="no-data-message text-center py-4">
                                <i class="fas fa-chart-pie fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No hay datos de estados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de cotizaciones recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Cotizaciones Recientes</h4>
                        <div class="d-flex gap-2">
                            <a href="/cotizaciones" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver todas las cotizaciones
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Vendedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cotizacion in datosCotizaciones.cotizaciones_recientes" :key="cotizacion.cotizacion_id">
                                    <td>
                                        <strong class="text-primary">COT-{{ String(cotizacion.numero).padStart(3, '0') }}</strong>
                                    </td>
                                    <td>{{ cotizacion.cliente_datos || cotizacion.cliente_nombre || 'N/A' }}</td>
                                    <td>{{ formatearFecha(cotizacion.fecha) }}</td>
                                    <td>
                                        <strong>S/ {{ parseFloat(cotizacion.total || 0).toLocaleString('es-PE', {minimumFractionDigits: 2}) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge" :class="obtenerClaseEstadoCotizacion(cotizacion.estado)">
                                            {{ obtenerTextoEstadoCotizacion(cotizacion.estado) }}
                                        </span>
                                    </td>
                                    <td>{{ cotizacion.vendedor || 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a :href="`${baseUrl}/r/cotizaciones/reporte/${cotizacion.cotizacion_id}`" 
                                               target="_blank" 
                                               class="btn btn-outline-info" 
                                               title="Ver PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Estado vacío -->
                        <div v-if="!datosCotizaciones.cotizaciones_recientes || !datosCotizaciones.cotizaciones_recientes.length" class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay cotizaciones recientes</h5>
                            <p class="text-muted">Las cotizaciones aparecerán aquí cuando se registren</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>