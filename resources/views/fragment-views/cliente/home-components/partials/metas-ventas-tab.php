<!-- Tab de Metas de Ventas -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'metas-ventas' }" id="metas-ventas"
    role="tabpanel">
    <div class="row fade-in-up">
        <!-- Columna principal con gráfico de vendedores -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-2">Competencia de Vendedores - <?= date('F Y') ?></h4>
                            <!-- Filtro de tipo de meta -->
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="tipoMeta" id="metaEmpresa" 
                                       value="empresa" v-model="tipoMetaActiva" @change="cambiarTipoMeta">
                                <label class="btn btn-outline-primary" for="metaEmpresa">Meta Empresa</label>
                                
                                <input type="radio" class="btn-check" name="tipoMeta" id="metaIndividual" 
                                       value="individual" v-model="tipoMetaActiva" @change="cambiarTipoMeta">
                                <label class="btn btn-outline-primary" for="metaIndividual">Metas Individuales</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" @click="abrirModalVendedores">
                                <i class="fas fa-users me-1"></i>Ver Vendedores
                            </button>
                            <button class="btn bg-rojo text-white" @click="abrirModalMeta" id="botonMeta">
                                <i class="fas fa-target me-1"></i>{{ textoBotonMeta }}
                            </button>
                        </div>
                    </div>

                    <!-- Gráfico principal de vendedores -->
                    <div class="chart-container" style="height: 400px;">
                        <div ref="vendedoresChart" class="chart-container">
                            <div v-if="!hayDatosVendedores" class="no-data-message text-center py-5">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay vendedores con ventas</h5>
                                <p class="text-muted">Los vendedores aparecerán aquí cuando realicen
                                    ventas en el mes actual</p>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo se muestran vendedores con ventas registradas
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Ranking de vendedores (solo si hay datos) -->
                    <div v-if="hayDatosVendedores" class="mt-4">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-trophy me-2"></i>Ranking de Contribución
                        </h6>
                        <div class="row">
                            <div v-for="(vendedor, index) in vendedores.slice(0, 3)"
                                :key="vendedor.usuario_id" class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center py-3">
                                        <div class="position-relative">
                                            <div class="avatar-lg mx-auto mb-2">
                                                <div
                                                    class="avatar-title bg-primary text-white rounded-circle fs-4">
                                                    {{ vendedor.nombres.charAt(0) }}
                                                </div>
                                            </div>
                                            <div v-if="index === 0"
                                                class="position-absolute top-0 start-50 translate-middle">
                                                <i class="fas fa-crown text-warning fs-5"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-1">{{ vendedor.nombres }}</h6>
                                        <small class="text-muted d-block">#{{ vendedor.posicion
                                            }}</small>
                                        <div class="mt-2">
                                            <span class="badge bg-primary">{{
                                                vendedor.porcentaje_contribucion.toFixed(1) }}%</span>
                                        </div>
                                        <small class="text-muted">S/ {{
                                            parseFloat(vendedor.ventas_actuales).toFixed(2) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna lateral con resumen y gráfico -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Resumen de Metas</h4>
                    <div id="resumenMetas">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title mb-4">Top Vendedores</h4>
                    <div class="chart-container">
                        <div ref="topVendedoresChart" class="chart-container">
                            <div v-if="!hayDatosVendedores" class="no-data-message">
                                <i class="fas fa-user-tie"></i>
                                <p>No hay datos disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>