<!-- Tab de Guías de Remisión -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'guias' }" id="guias"
    role="tabpanel">
    <div class="row fade-in-up">
        <!-- Tarjetas de resumen -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-2">Total Guías</p>
                            <h2 class="mb-0">{{ datosGuias.total_guias || 0 }}</h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-calendar me-1"></i>{{ periodoTextoGuias }}
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-info-subtle text-info rounded fs-2">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-2">Guías Enviadas</p>
                            <h2 class="mb-0">{{ datosGuias.guias_enviadas || 0 }}</h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-success-subtle text-success">
                                    <i class="fas fa-check me-1"></i>{{ datosGuias.porcentaje_enviadas || 0 }}%
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title bg-success-subtle text-success rounded fs-2">
                                <i class="fas fa-paper-plane"></i>
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
                        <i class="fas fa-box text-primary me-2"></i>Productos Más Transportados
                    </h4>
                    <div v-if="datosGuias.productos_mas_transportados && datosGuias.productos_mas_transportados.length">
                        <div class="row">
                            <div v-for="(producto, index) in datosGuias.productos_mas_transportados.slice(0, 6)" 
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
                                            <small><strong>{{ producto.total_transportado }}</strong> veces transportado</small>
                                        </p>
                                        <p class="text-success mb-0">
                                            <small>{{ parseFloat(producto.peso_promedio || 0).toLocaleString('es-PE', {minimumFractionDigits: 2}) }} Kg promedio</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay productos transportados</h5>
                        <p class="text-muted">Los productos aparecerán aquí cuando se registren guías</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de guías por mes -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Evolución de Guías de Remisión</h4>
                    </div>

                    <!-- Gráfico principal -->
                    <div class="chart-container" style="height: 400px;">
                        <div ref="guiasChart" class="chart-container">
                            <div v-if="!hayDatosGuias" class="no-data-message text-center py-5">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay guías de remisión registradas</h5>
                                <p class="text-muted">Las guías aparecerán aquí cuando se registren</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-xl-4">
            <!-- Clientes con más guías -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Clientes con Más Guías</h4>
                    <div v-if="datosGuias.top_clientes && datosGuias.top_clientes.length">
                        <div v-for="(cliente, index) in datosGuias.top_clientes.slice(0, 5)" 
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
                                <p class="text-muted mb-0">{{ cliente.total_guias }} guías</p>
                                <small class="text-muted">{{ parseFloat(cliente.peso_total || 0).toLocaleString('es-PE', {minimumFractionDigits: 2}) }} Kg total</small>
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

            <!-- Series más usadas -->
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title mb-4">Series Más Utilizadas</h4>
                    <div v-if="datosGuias.series_top && datosGuias.series_top.length">
                        <div v-for="(serie, index) in datosGuias.series_top" 
                             :key="serie.serie" class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-info text-white rounded-circle fw-bold">
                                        {{ serie.serie }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ serie.serie }}-{{ String(serie.numero_inicio || 1).padStart(3, '0') }} a {{ serie.serie }}-{{ String(serie.numero_fin || serie.total_guias).padStart(3, '0') }}</h6>
                                <p class="text-muted mb-0">{{ serie.total_guias }} guías emitidas</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-info">{{ index + 1 }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="fas fa-list-ol fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No hay datos de series</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de guías recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Guías de Remisión Recientes</h4>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" class="form-control" v-model="buscarGuia" 
                                       placeholder="Buscar guía...">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Estado
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" @click.prevent="filtroEstadoGuia = ''">Todos</a></li>
                                    <li><a class="dropdown-item" href="#" @click.prevent="filtroEstadoGuia = '1'">Activa</a></li>
                                    <li><a class="dropdown-item" href="#" @click.prevent="filtroEstadoGuia = '0'">Anulada</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha Emisión</th>
                                    <th>Destinatario</th>
                                    <th>Motivo</th>
                                    <th>Peso/Bultos</th>
                                    <th>Estado SUNAT</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="guia in datosGuias.guias_recientes" :key="guia.id_guia_remision">
                                    <td>
                                        <strong class="text-primary">{{ guia.serie }}-{{ String(guia.numero).padStart(4, '0') }}</strong>
                                    </td>
                                    <td>{{ formatearFecha(guia.fecha_emision) }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ guia.cliente_datos || guia.destinatario_nombre || 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ guia.destinatario_documento || '' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ guia.motivo_nombre || 'N/A' }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ parseFloat(guia.peso || 0).toFixed(2) }} Kg</strong>
                                            <br><small class="text-muted">{{ guia.nro_bultos || 0 }} bultos</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" :class="obtenerClaseEstadoGuia(guia.estado)">
                                            {{ obtenerTextoEstadoGuia(guia.estado) }}
                                        </span>
                                    </td>
                                    <td>{{ guia.vendedor || 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a :href="`${baseUrl}/r/guia-remision/reporte/${guia.id_guia_remision}`" 
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
                        <div v-if="!datosGuias.guias_recientes || !datosGuias.guias_recientes.length" class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay guías recientes</h5>
                            <p class="text-muted">Las guías aparecerán aquí cuando se registren</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>