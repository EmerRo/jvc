/**
 * Gráficos del módulo de Ingresos y Egresos
 * Contiene todas las funciones para inicializar y manejar los gráficos financieros
 */

Vue.prototype.inicializarGraficosIngresos = function() {
    if (!this.hayDatosIngresos) return;

    // Gráfico de Ingresos y Egresos CON DATOS REALES
    if (this.$refs.ingresosEgresosChart) {
        if (this.charts.ingresosEgresos) {
            try {
                this.charts.ingresosEgresos.destroy();
            } catch (e) {
                console.log('Chart already destroyed');
            }
            this.charts.ingresosEgresos = null;
        }

        const categorias = this.dashboardData.categoriasGrafico || this.meses;
        const ingresos = this.dashboardData.ingresosPorPeriodo || [];
        const egresos = this.dashboardData.egresosPorPeriodo || [];
        const ganancias = ingresos.map((ingreso, index) => ingreso - (egresos[index] || 0));

        this.charts.ingresosEgresos = Highcharts.chart(this.$refs.ingresosEgresosChart, {
            chart: {
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            xAxis: {
                categories: categorias,
                labels: {
                    style: { color: '#6c757d', fontSize: '12px' }
                }
            },
            yAxis: {
                title: { text: null },
                labels: {
                    formatter: function () {
                        return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                    },
                    style: { color: '#6c757d', fontSize: '12px' }
                },
                gridLineDashStyle: 'Dash'
            },
            tooltip: {
                shared: true,
                crosshairs: true,
                formatter: function () {
                    let s = '<b>' + this.x + '</b>';
                    this.points.forEach(function (point) {
                        s += '<br/><span style="color:' + point.series.color + '">\u25CF</span> ' +
                            point.series.name + ': S/ ' + Highcharts.numberFormat(point.y, 2);
                    });
                    return s;
                }
            },
            plotOptions: {
                column: { borderRadius: 5 }
            },
            series: [
                {
                    name: 'Ingresos',
                    type: 'column',
                    color: this.colors.success,
                    data: ingresos
                },
                {
                    name: 'Egresos',
                    type: 'column',
                    color: this.colors.danger,
                    data: egresos
                },
                {
                    name: 'Ganancia',
                    type: 'spline',
                    color: this.colors.primary,
                    data: ganancias,
                    marker: {
                        lineWidth: 2,
                        lineColor: this.colors.primary,
                        fillColor: 'white'
                    }
                }
            ]
        });
    }

    // Gráficos de distribución (pie charts)
    this.inicializarGraficosDistribucionIngresos();
    this.inicializarGraficosDistribucionEgresos();
};

Vue.prototype.inicializarGraficosDistribucionIngresos = function() {
    if (this.$refs.distribucionIngresosChart) {
        if (this.charts.distribucionIngresos) {
            try {
                this.charts.distribucionIngresos.destroy();
            } catch (e) {
                console.log('Chart already destroyed');
            }
            this.charts.distribucionIngresos = null;
        }

        // Datos simulados de distribución de ingresos
        const datosIngresos = [
            { name: 'Ventas', y: 75, color: this.colors.primary },
            { name: 'Servicios', y: 20, color: this.colors.secondary },
            { name: 'Otros', y: 5, color: this.colors.info }
        ];

        this.charts.distribucionIngresos = Highcharts.chart(this.$refs.distribucionIngresosChart, {
            chart: {
                type: 'pie',
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
                    showInLegend: false,
                    innerSize: '50%',
                    size: '80%'
                }
            },
            series: [{
                name: 'Ingresos',
                colorByPoint: true,
                data: datosIngresos
            }]
        });
    }
};

Vue.prototype.inicializarGraficosDistribucionEgresos = function() {
    if (this.$refs.distribucionEgresosChart) {
        if (this.charts.distribucionEgresos) {
            try {
                this.charts.distribucionEgresos.destroy();
            } catch (e) {
                console.log('Chart already destroyed');
            }
            this.charts.distribucionEgresos = null;
        }

        // Datos simulados de distribución de egresos
        const datosEgresos = [
            { name: 'Compras', y: 60, color: this.colors.danger },
            { name: 'Gastos Operativos', y: 25, color: this.colors.warning },
            { name: 'Personal', y: 10, color: this.colors.orange },
            { name: 'Otros', y: 5, color: this.colors.secondary }
        ];

        this.charts.distribucionEgresos = Highcharts.chart(this.$refs.distribucionEgresosChart, {
            chart: {
                type: 'pie',
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
                    showInLegend: false,
                    innerSize: '50%',
                    size: '80%'
                }
            },
            series: [{
                name: 'Egresos',
                colorByPoint: true,
                data: datosEgresos
            }]
        });
    }
};

// Funciones para manejo de períodos de ingresos
Vue.prototype.cambiarPeriodoIngresos = function(periodo) {
    this.periodoActualIngresos = periodo;
    
    // Actualizar texto del período
    switch(periodo) {
        case 'hoy': this.periodoTextoIngresos = 'Hoy'; break;
        case 'semana': this.periodoTextoIngresos = 'Esta semana'; break;
        case 'mes': this.periodoTextoIngresos = 'Este mes'; break;
        case 'anio': this.periodoTextoIngresos = 'Este año'; break;
        default: this.periodoTextoIngresos = 'Período personalizado';
    }
    
    // Recargar página con nuevo período
    const url = new URL(window.location);
    url.searchParams.set('periodo', periodo);
    url.searchParams.set('tab', 'ingresos-egresos');
    window.location.href = url.toString();
};

Vue.prototype.abrirModalPersonalizadoIngresos = function() {
    // Configurar fechas iniciales
    this.filtroFechasIngresos.inicio = '';
    this.filtroFechasIngresos.fin = '';
    
    // Mostrar modal personalizado
    const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoModal'));
    modal.show();
};