/**
 * Gráficos del módulo de Clientes
 * Contiene todas las funciones para inicializar y manejar los gráficos de clientes
 */

Vue.prototype.inicializarGraficosClientes = function() {
    if (!this.hayDatosClientes) return;

    const chartColors = window.getChartColors();

    // Gráfico de Clientes Top
    if (this.$refs.clientesTopChart) {
        if (this.charts.clientesTop) {
            try {
                this.charts.clientesTop.destroy();
            } catch (e) {
                console.log('Chart already destroyed');
            }
            this.charts.clientesTop = null;
        }

        // Usar datos reales si están disponibles
        const clientesNombres = this.dashboardData.clientesNombres || [];
        const clientesCompras = this.dashboardData.clientesCompras || [];

        this.charts.clientesTop = Highcharts.chart(this.$refs.clientesTopChart, {
            chart: {
                type: 'bar',
                backgroundColor: chartColors.backgroundColor,
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            xAxis: {
                categories: clientesNombres,
                labels: {
                    style: { color: chartColors.textColor, fontSize: '12px' }
                },
                lineColor: chartColors.gridLineColor,
                tickColor: chartColors.gridLineColor
            },
            yAxis: {
                title: {
                    text: 'Total Compras (S/)',
                    style: { color: chartColors.textColor, fontSize: '12px' }
                },
                labels: {
                    formatter: function () {
                        return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                    },
                    style: { color: chartColors.textColor, fontSize: '12px' }
                },
                gridLineColor: chartColors.gridLineColor,
                gridLineDashStyle: 'Dash'
            },
            tooltip: {
                backgroundColor: chartColors.tooltipBg,
                borderColor: chartColors.tooltipBorder,
                style: { color: chartColors.tooltipTextColor }
            },
            legend: {
                itemStyle: { color: chartColors.textColor }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b><br>Total: S/ ' + Highcharts.numberFormat(this.y, 2);
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    colorByPoint: true,
                    colors: [
                        this.colors.primary, this.colors.secondary, this.colors.success,
                        this.colors.warning, this.colors.danger
                    ],
                    dataLabels: {
                        enabled: true,
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.y, 0);
                        },
                        style: { fontSize: '10px', fontWeight: 'bold' }
                    }
                }
            },
            series: [{
                name: 'Total Compras',
                data: clientesCompras,
                showInLegend: false
            }]
        });
    }

    // Gráfico de Frecuencia de Compra
    if (this.$refs.frecuenciaCompraChart) {
        if (this.charts.frecuenciaCompra) {
            try {
                this.charts.frecuenciaCompra.destroy();
            } catch (e) {
                console.log('Chart already destroyed');
            }
            this.charts.frecuenciaCompra = null;
        }

        // Datos simulados de frecuencia de compra
        const datosFrecuencia = [
            { name: 'Clientes Frecuentes', y: 35, color: this.colors.success },
            { name: 'Clientes Regulares', y: 40, color: this.colors.primary },
            { name: 'Clientes Ocasionales', y: 20, color: this.colors.warning },
            { name: 'Clientes Nuevos', y: 5, color: this.colors.info }
        ];

        this.charts.frecuenciaCompra = Highcharts.chart(this.$refs.frecuenciaCompraChart, {
            chart: {
                type: 'pie',
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br>Cantidad: <b>{point.y}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>{point.percentage:.1f}%',
                        style: {
                            fontSize: '11px',
                            fontWeight: 'bold'
                        }
                    },
                    showInLegend: true,
                    innerSize: '50%'
                }
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                itemStyle: {
                    color: '#6c757d',
                    fontWeight: 'normal',
                    fontSize: '11px'
                }
            },
            series: [{
                name: 'Porcentaje',
                colorByPoint: true,
                data: datosFrecuencia
            }]
        });
    }
};

// Funciones para manejo de clientes
Vue.prototype.verDetalleCliente = function(idCliente) {
    console.log('Ver detalle del cliente:', idCliente);
    
    // Mostrar modal de carga
    const modal = new bootstrap.Modal(document.getElementById('clienteDetalleModal'));
    modal.show();
    
    // Simular carga de datos
    setTimeout(() => {
        document.getElementById('clienteDetalleContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Información Personal</h6>
                    <p><strong>ID:</strong> ${idCliente}</p>
                    <p><strong>Nombre:</strong> Cliente ${idCliente}</p>
                    <p><strong>Email:</strong> cliente${idCliente}@email.com</p>
                    <p><strong>Teléfono:</strong> +51 9${String(Math.floor(Math.random() * 100000000)).padStart(8, '0')}</p>
                    <p><strong>Fecha de Registro:</strong> ${new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toLocaleDateString()}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Historial de Compras</h6>
                    <p><strong>Total de Compras:</strong> ${Math.floor(Math.random() * 50) + 5}</p>
                    <p><strong>Monto Total:</strong> S/ ${(Math.random() * 50000 + 5000).toFixed(2)}</p>
                    <p><strong>Promedio por Compra:</strong> S/ ${(Math.random() * 1000 + 100).toFixed(2)}</p>
                    <p><strong>Última Compra:</strong> ${new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toLocaleDateString()}</p>
                    <p><strong>Estado:</strong> <span class="badge badge-success">Activo</span></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-muted">Productos Favoritos</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark">Producto A</span>
                        <span class="badge bg-light text-dark">Producto B</span>
                        <span class="badge bg-light text-dark">Producto C</span>
                    </div>
                </div>
            </div>
        `;
    }, 1000);
};

Vue.prototype.verEstadisticasCliente = function(idCliente) {
    console.log('Ver estadísticas del cliente:', idCliente);
    
    // Mostrar modal de carga
    const modal = new bootstrap.Modal(document.getElementById('clienteEstadisticasModal'));
    modal.show();
    
    // Simular carga de gráfico
    setTimeout(() => {
        document.getElementById('clienteEstadisticasContent').innerHTML = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h4 class="text-primary">${Math.floor(Math.random() * 100) + 10}</h4>
                            <p class="mb-0">Total de Compras</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h4 class="text-success">S/ ${(Math.random() * 100000 + 10000).toFixed(2)}</h4>
                            <p class="mb-0">Monto Total Gastado</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h6>Evolución de Compras - Cliente ${idCliente}</h6>
                    <div id="estadisticasClienteChart" style="height: 300px;"></div>
                </div>
                <div class="col-md-4">
                    <h6>Método de Pago Preferido</h6>
                    <div id="metodoPagoChart" style="height: 300px;"></div>
                </div>
            </div>
        `;
        
        // Crear gráfico de evolución de compras
        Highcharts.chart('estadisticasClienteChart', {
            chart: { type: 'area' },
            title: { text: null },
            xAxis: { categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'] },
            yAxis: { title: { text: 'Monto (S/)' } },
            plotOptions: {
                area: { fillOpacity: 0.3 }
            },
            series: [{
                name: 'Compras',
                data: [
                    Math.random() * 5000, Math.random() * 5000, Math.random() * 5000,
                    Math.random() * 5000, Math.random() * 5000, Math.random() * 5000
                ],
                color: this.colors.primary
            }],
            credits: { enabled: false }
        });
        
        // Crear gráfico de método de pago
        Highcharts.chart('metodoPagoChart', {
            chart: { type: 'pie' },
            title: { text: null },
            plotOptions: {
                pie: {
                    dataLabels: { enabled: true },
                    innerSize: '50%'
                }
            },
            series: [{
                name: 'Método',
                data: [
                    { name: 'Efectivo', y: 40, color: this.colors.success },
                    { name: 'Tarjeta', y: 35, color: this.colors.primary },
                    { name: 'Transferencia', y: 25, color: this.colors.info }
                ]
            }],
            credits: { enabled: false }
        });
    }, 1000);
};