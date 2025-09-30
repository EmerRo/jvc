<!-- Tab de Clientes -->
<div class="tab-pane fade" :class="{ 'show active': activeTab === 'clientes' }" id="clientes"
    role="tabpanel">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="btn-group">
                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoClientes }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoClientes('hoy')">Hoy</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoClientes('semana')">Esta semana</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoClientes('mes')">Este mes</a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodoClientes('anio')">Este año</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="abrirModalPersonalizadoClientes()">Personalizado</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control"
                    placeholder="Buscar clientes..." v-model="filtroClientes"
                    @input="filtrarClientes">
            </div>
        </div>
    </div>
    <div class="row fade-in-up">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Clientes Top por Compras</h4>
                    <div class="chart-container">
                        <div id="clientesTopChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="clientesTopChart" class="chart-container">
                            <div v-if="!hayDatosClientes" class="no-data-message">
                                <i class="fas fa-users"></i>
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
                    <h4 class="card-title mb-4">Frecuencia de Compra</h4>
                    <div class="chart-container">
                        <div id="frecuenciaCompraChartLoading" class="chart-loading" v-if="loadingCharts">
                            <div class="spinner"></div>
                        </div>
                        <div ref="frecuenciaCompraChart" class="chart-container">
                            <div v-if="!hayDatosClientes" class="no-data-message">
                                <i class="fas fa-chart-pie"></i>
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
                    <h4 class="card-title mb-4">Detalle de Clientes Top</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Cliente</th>
                                    <th>Frecuencia de Compra</th>
                                    <th>Total Compras</th>
                                    <th>Método de Pago</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($clientes_top && $clientes_top->num_rows > 0): ?>
                                    <?php while ($cliente = $clientes_top->fetch_assoc()): ?>
                                        <tr>
                                            <!-- <td><?= $cliente['id_cliente'] ?></td> -->
                                            <td><?= $cliente['datos'] ?></td>
                                            <td><?= $cliente['num_compras'] ?> compras</td>
                                            <td>S/ <?= number_format($cliente['total_compras'], 2, ".", ",") ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Simulamos diferentes métodos de pago
                                                $metodos = ['Contado', 'Crédito', 'Transferencia'];
                                                echo $metodos[array_rand($metodos)];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Simulamos diferentes estados
                                                $estados = [
                                                    '<span class="badge badge-success">Al día</span>',
                                                    '<span class="badge badge-warning">Pendiente</span>',
                                                    '<span class="badge badge-danger">Atrasado</span>'
                                                ];
                                                echo $estados[array_rand($estados)];
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    @click="verDetalleCliente(<?= $cliente['id_cliente'] ?>)"
                                                    title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info"
                                                    @click="verEstadisticasCliente(<?= $cliente['id_cliente'] ?>)"
                                                    title="Ver estadísticas">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay datos disponibles</td>
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