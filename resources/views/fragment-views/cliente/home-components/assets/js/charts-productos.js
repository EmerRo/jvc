/**
 * Gráficos del módulo de Productos
 * Contiene todas las funciones para inicializar y manejar los gráficos de productos
 */

Vue.prototype.inicializarGraficosProductos = function() {
    // Gráfico de Productos Top
    if (this.$refs.productosTopChart && this.hayDatosProductos) {
        if (this.charts.productosTop) {
            try {
                this.charts.productosTop.destroy();
            } catch (e) {
                console.log('Chart productosTop already destroyed');
            }
            this.charts.productosTop = null;
        }

        this.charts.productosTop = Highcharts.chart(this.$refs.productosTopChart, {
            chart: {
                type: 'bar',
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            xAxis: {
                categories: this.dashboardData.productosNombres,
                labels: {
                    style: { color: '#6c757d', fontSize: '12px' }
                }
            },
            yAxis: {
                title: { text: null },
                labels: {
                    style: { color: '#6c757d', fontSize: '12px' }
                },
                gridLineDashStyle: 'Dash'
            },
            tooltip: {
                formatter: function () {
                    const total = this.series.data.reduce((sum, point) => sum + point.y, 0);
                    const porcentaje = ((this.y / total) * 100).toFixed(1);
                    return '<b>' + this.x + '</b><br>' +
                        this.y + ' unidades<br>' +
                        '<b>' + porcentaje + '%</b> del total';
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
                            const total = this.series.data.reduce((sum, point) => sum + point.y, 0);
                            const porcentaje = ((this.y / total) * 100).toFixed(1);
                            return porcentaje + '%';
                        },
                        style: { fontSize: '10px', fontWeight: 'bold' }
                    }
                }
            },
            series: [{
                name: 'Unidades Vendidas',
                data: this.dashboardData.productosCantidades,
                showInLegend: false
            }]
        });
    }

    // Gráfico de Distribución de Productos CON PORCENTAJES VISIBLES
    if (this.$refs.distribucionProductosChart && this.hayDatosProductos) {
        if (this.charts.distribucionProductos) {
            try {
                this.charts.distribucionProductos.destroy();
            } catch (e) {
                console.log('Chart distribucionProductos already destroyed');
            }
            this.charts.distribucionProductos = null;
        }

        const datosDistribucion = this.dashboardData.productosNombres.map((nombre, index) => {
            const cantidad = this.dashboardData.productosCantidades[index];
            const total = this.dashboardData.productosCantidades.reduce((a, b) => a + b, 0);
            const porcentaje = (cantidad / total) * 100;

            return {
                name: nombre,
                y: porcentaje,
                color: [this.colors.primary, this.colors.secondary, this.colors.info,
                this.colors.danger, this.colors.warning][index % 5]
            };
        });

        this.charts.distribucionProductos = Highcharts.chart(this.$refs.distribucionProductosChart, {
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
                            fontSize: '12px',
                            fontWeight: 'bold'
                        }
                    },
                    showInLegend: true,
                    innerSize: '60%'
                }
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                itemStyle: {
                    color: '#6c757d',
                    fontWeight: 'normal',
                    fontSize: '12px'
                }
            },
            series: [{
                name: 'Porcentaje',
                colorByPoint: true,
                data: datosDistribucion
            }]
        });
    }

    // Gráfico de productos por categoría CON COMPARATIVA
    if (this.$refs.productosCategoriaChart && this.hayDatosProductosPorCategoria) {
        if (this.charts.productosCategoria) {
            try {
                this.charts.productosCategoria.destroy();
            } catch (e) {
                console.log('Chart productosCategoria already destroyed');
            }
            this.charts.productosCategoria = null;
        }

        this.charts.productosCategoria = Highcharts.chart(this.$refs.productosCategoriaChart, {
            chart: {
                type: 'column',
                style: { fontFamily: 'Poppins, sans-serif' },
                animation: { duration: 1000 }
            },
            title: { text: null },
            xAxis: {
                categories: this.dashboardData.categorias || [],
                labels: {
                    style: { color: '#6c757d', fontSize: '12px' },
                    rotation: -45 // Rotar etiquetas si son muy largas
                }
            },
            yAxis: {
                title: {
                    text: 'Unidades Vendidas',
                    style: { color: '#6c757d' }
                },
                labels: {
                    style: { color: '#6c757d', fontSize: '12px' }
                }
            },
            tooltip: {
                shared: true,
                formatter: function () {
                    let s = '<b>' + this.x + '</b><br/>';
                    this.points.forEach(function (point) {
                        s += '<span style="color:' + point.series.color + '">\u25CF</span> ' +
                            point.series.name + ': ' + point.y + ' unidades<br/>';
                    });
                    return s;
                }
            },
            plotOptions: {
                column: {
                    borderRadius: 5,
                    dataLabels: {
                        enabled: true,
                        formatter: function () {
                            return this.y;
                        },
                        style: { fontSize: '10px', fontWeight: 'bold' }
                    }
                }
            },
            series: [{
                name: this.periodoTextoProductos,
                data: this.dashboardData.productosPorCategoria || [],
                color: this.colors.primary
            }, {
                name: this.textoComparativaProductos.replace('vs. ', ''),
                data: this.dashboardData.productosPorCategoriaAnterior || [],
                color: this.colors.secondary
            }],
            credits: { enabled: false }
        });
    }
};

// Funciones para manejo de productos
Vue.prototype.verDetalleProducto = function(idProducto) {
    console.log('Ver detalle del producto:', idProducto);
    
    // Mostrar modal de carga
    const modal = new bootstrap.Modal(document.getElementById('productoDetalleModal'));
    modal.show();
    
    // Simular carga de datos
    setTimeout(() => {
        document.getElementById('productoDetalleContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Información del Producto</h6>
                    <p><strong>ID:</strong> ${idProducto}</p>
                    <p><strong>Código:</strong> PROD-${idProducto}</p>
                    <p><strong>Nombre:</strong> Producto ${idProducto}</p>
                    <p><strong>Categoría:</strong> General</p>
                    <p><strong>Stock Actual:</strong> ${Math.floor(Math.random() * 100) + 1} unidades</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Estadísticas de Ventas</h6>
                    <p><strong>Total Vendido:</strong> ${Math.floor(Math.random() * 500) + 50} unidades</p>
                    <p><strong>Ingresos Generados:</strong> S/ ${(Math.random() * 10000 + 1000).toFixed(2)}</p>
                    <p><strong>Última Venta:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Precio Promedio:</strong> S/ ${(Math.random() * 100 + 10).toFixed(2)}</p>
                </div>
            </div>
        `;
    }, 1000);
};

Vue.prototype.verEstadisticasProducto = function(idProducto) {
    console.log('Ver estadísticas del producto:', idProducto);
    
    // Mostrar modal de carga
    const modal = new bootstrap.Modal(document.getElementById('productoEstadisticasModal'));
    modal.show();
    
    // Simular carga de gráfico
    setTimeout(() => {
        document.getElementById('productoEstadisticasContent').innerHTML = `
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Evolución de Ventas - Producto ${idProducto}</h6>
                    <div id="estadisticasProductoChart" style="height: 300px;"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h4 class="text-primary">${Math.floor(Math.random() * 1000) + 100}</h4>
                            <p class="mb-0">Unidades Vendidas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h4 class="text-success">S/ ${(Math.random() * 50000 + 5000).toFixed(2)}</h4>
                            <p class="mb-0">Ingresos Totales</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Crear gráfico simulado
        Highcharts.chart('estadisticasProductoChart', {
            chart: { type: 'line' },
            title: { text: null },
            xAxis: { categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'] },
            yAxis: { title: { text: 'Unidades' } },
            series: [{
                name: 'Ventas',
                data: [Math.random() * 100, Math.random() * 100, Math.random() * 100, 
                       Math.random() * 100, Math.random() * 100, Math.random() * 100]
            }],
            credits: { enabled: false }
        });
    }, 1000);
};