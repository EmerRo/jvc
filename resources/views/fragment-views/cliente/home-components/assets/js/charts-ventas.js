/**
 * Gráficos del módulo de Ventas
 * Contiene todas las funciones para inicializar y manejar los gráficos de ventas
 */

// Extender el prototipo de Vue con métodos de gráficos de ventas
Vue.prototype.inicializarGraficosVentas = function() {
    console.log('Inicializando gráficos de ventas...', this.dashboardData);

    // Gráfico de Ventas Anuales
    if (this.$refs.ventasAnualesChart) {
        // Destruir gráfico existente de forma segura
        if (this.charts.ventasAnuales) {
            try {
                this.charts.ventasAnuales.destroy();
            } catch (e) {
                console.log('Chart ventasAnuales already destroyed');
            }
            this.charts.ventasAnuales = null;
        }

        // Verificar datos de manera más flexible
        const tieneVentasAnuales = this.dashboardData.ventasAnuales &&
            Array.isArray(this.dashboardData.ventasAnuales) &&
            this.dashboardData.ventasAnuales.length > 0;

        console.log('¿Tiene datos de ventas anuales?', tieneVentasAnuales, this.dashboardData.ventasAnuales);

        if (tieneVentasAnuales) {
            const chartColors = window.getChartColors();
            this.charts.ventasAnuales = Highcharts.chart(this.$refs.ventasAnualesChart, {
                chart: {
                    type: 'area',
                    backgroundColor: chartColors.backgroundColor,
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: this.dashboardData.categoriasGrafico || this.meses,
                    labels: {
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    lineColor: chartColors.gridLineColor,
                    tickColor: chartColors.gridLineColor
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    gridLineColor: chartColors.gridLineColor,
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    backgroundColor: chartColors.tooltipBg,
                    borderColor: chartColors.tooltipBorder,
                    style: {
                        color: chartColors.textColor
                    },
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                legend: {
                    itemStyle: {
                        color: chartColors.textColor
                    }
                },
                plotOptions: {
                    area: {
                        fillOpacity: 0.3,
                        marker: {
                            radius: 4,
                            lineWidth: 2,
                            lineColor: chartColors.backgroundColor
                        }
                    }
                },
                series: [{
                    name: 'Ventas',
                    color: this.colors.primary,
                    data: this.dashboardData.ventasAnuales
                }]
            });
        } else {
            console.log('No hay datos para el gráfico de ventas anuales');
        }
    }

    // Gráfico de Comparativa de Períodos
    if (this.$refs.comparativaChart) {
        if (this.charts.comparativa) {
            try {
                this.charts.comparativa.destroy();
            } catch (e) {
                console.log('Chart comparativa already destroyed');
            }
            this.charts.comparativa = null;
        }

        const tieneComparativa = this.dashboardData.ventasPorPeriodo &&
            Array.isArray(this.dashboardData.ventasPorPeriodo) &&
            this.dashboardData.ventasPorPeriodo.length > 0;

        if (tieneComparativa) {
            const chartColors = window.getChartColors();
            this.charts.comparativa = Highcharts.chart(this.$refs.comparativaChart, {
                chart: {
                    type: 'column',
                    backgroundColor: chartColors.backgroundColor,
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: this.dashboardData.periodos,
                    labels: {
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    lineColor: chartColors.gridLineColor,
                    tickColor: chartColors.gridLineColor
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    gridLineColor: chartColors.gridLineColor,
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        colorByPoint: true,
                        colors: [
                            this.colors.primary, this.colors.secondary, this.colors.success,
                            this.colors.warning, this.colors.danger, this.colors.info,
                            this.colors.purple, this.colors.pink
                        ],
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return 'S/ ' + Highcharts.numberFormat(this.y, 0);
                            },
                            style: {
                                fontSize: '11px',
                                fontWeight: 'bold',
                                color: '#333',
                                textOutline: '1px white'
                            },
                            y: -5
                        }
                    }
                },
                series: [{
                    name: 'Ventas',
                    data: this.dashboardData.ventasPorPeriodo,
                    showInLegend: false
                }]
            });
        }
    }

    // Resto de gráficos (comparativa anual y utilidad bruta)
    this.inicializarGraficosComparativaAnual();
    this.inicializarGraficoUtilidadBruta();
};

Vue.prototype.inicializarGraficosComparativaAnual = function() {
    if (this.$refs.comparativaAnualChart) {
        if (this.charts.comparativaAnual) {
            try {
                this.charts.comparativaAnual.destroy();
            } catch (e) {
                console.log('Chart comparativaAnual already destroyed');
            }
            this.charts.comparativaAnual = null;
        }

        const tieneVentasAnuales = this.dashboardData.ventasAnuales &&
            Array.isArray(this.dashboardData.ventasAnuales) &&
            this.dashboardData.ventasAnuales.length > 0;

        if (tieneVentasAnuales) {
            const chartColors = window.getChartColors();
            const añoActual = new Date().getFullYear();
            const años = [añoActual - 2, añoActual - 1, añoActual];

            // Generamos datos simulados para años anteriores
            const datosAñoAnterior = this.dashboardData.ventasAnuales.map(valor => valor * 0.8);
            const datosAñoAnteAnterior = this.dashboardData.ventasAnuales.map(valor => valor * 0.6);

            this.charts.comparativaAnual = Highcharts.chart(this.$refs.comparativaAnualChart, {
                chart: {
                    type: 'line',
                    backgroundColor: chartColors.backgroundColor,
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: this.dashboardData.categoriasGrafico || this.meses,
                    labels: {
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    lineColor: chartColors.gridLineColor,
                    tickColor: chartColors.gridLineColor
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    gridLineColor: chartColors.gridLineColor,
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
                    line: {
                        marker: {
                            radius: 4,
                            lineWidth: 2,
                            lineColor: '#ffffff'
                        }
                    }
                },
                series: [
                    {
                        name: años[0].toString(),
                        color: this.colors.secondary,
                        data: datosAñoAnteAnterior,
                        lineWidth: 2
                    },
                    {
                        name: años[1].toString(),
                        color: this.colors.warning,
                        data: datosAñoAnterior,
                        lineWidth: 2
                    },
                    {
                        name: años[2].toString(),
                        color: this.colors.primary,
                        data: this.dashboardData.ventasAnuales,
                        lineWidth: 3
                    }
                ]
            });
        }
    }
};

Vue.prototype.inicializarGraficoUtilidadBruta = function() {
    if (this.$refs.utilidadBrutaChart) {
        if (this.charts.utilidadBruta) {
            try {
                this.charts.utilidadBruta.destroy();
            } catch (e) {
                console.log('Chart utilidadBruta already destroyed');
            }
            this.charts.utilidadBruta = null;
        }

        const tieneUtilidadBruta = this.dashboardData.utilidadBrutaPorPeriodo &&
            Array.isArray(this.dashboardData.utilidadBrutaPorPeriodo) &&
            this.dashboardData.utilidadBrutaPorPeriodo.length > 0;

        if (tieneUtilidadBruta) {
            const chartColors = window.getChartColors();
            this.charts.utilidadBruta = Highcharts.chart(this.$refs.utilidadBrutaChart, {
                chart: {
                    type: 'column',
                    backgroundColor: chartColors.backgroundColor,
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: this.dashboardData.categoriasGrafico || this.meses,
                    labels: {
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    lineColor: chartColors.gridLineColor,
                    tickColor: chartColors.gridLineColor
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: chartColors.textColor,
                            fontSize: '12px'
                        }
                    },
                    gridLineColor: chartColors.gridLineColor,
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>Utilidad: S/ ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        color: this.colors.success,
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return 'S/ ' + Highcharts.numberFormat(this.y, 0);
                            },
                            style: {
                                fontSize: '10px'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Utilidad Bruta',
                    data: this.dashboardData.utilidadBrutaPorPeriodo,
                    showInLegend: false
                }]
            });
        }
    }
};

Vue.prototype.obtenerTituloComparativa = function() {
    const año = new Date().getFullYear();
    switch (this.periodoActual) {
        case 'hoy': return `Comparativa Diaria - ${año}`;
        case 'semana': return `Comparativa Semanal - ${año}`;
        case 'mes': return `Comparativa Mensual - ${año}`;
        case 'anio': return `Comparativa Anual`;
        default: return `Comparativa de Ventas - ${año}`;
    }
};