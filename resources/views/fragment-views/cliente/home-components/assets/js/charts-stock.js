/**
 * Gráficos del módulo de Stock
 * Contiene todas las funciones para inicializar y manejar los gráficos de inventario
 */

Vue.prototype.inicializarGraficosStock = function() {
    console.log('Inicializando gráficos de stock...');
    
    // Cargar datos reales de stock desde el servidor
    this.cargarDatosStock();
};

Vue.prototype.cargarDatosStock = function() {
    console.log('Cargando datos de stock desde el servidor...');
    
    fetch(`${_URL}/ajs/dashboard/datos-stock`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos de stock recibidos:', data);
            
            if (data.success) {
                // Actualizar datos de stock en el componente
                this.datosStock = data.data;
                
                // Inicializar gráficos con los datos reales
                this.inicializarGraficoRotacion();
                this.inicializarGraficoMovimientos();
            } else {
                console.error('Error al cargar datos de stock:', data.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los datos de stock'
                });
            }
        })
        .catch(error => {
            console.error('Error en la petición de datos de stock:', error);
            Swal.fire({
                icon: 'error', 
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor'
            });
        });
};

Vue.prototype.inicializarGraficoRotacion = function() {
    if (!this.$refs.rotacionInventarioChart || !this.datosStock || !this.datosStock.rotacion) {
        console.log('No hay datos de rotación disponibles');
        return;
    }

    const rotacionData = this.datosStock.rotacion;
    
    // Extraer nombres y días de rotación
    const productosNombres = rotacionData.map(item => item.nombre);
    const diasRotacion = rotacionData.map(item => item.dias_rotacion);

    // Crear datos con colores según los días de rotación
    const datosConColores = diasRotacion.map(dias => {
        let color;
        if (dias < 10) {
            color = '#28a745'; // Verde - Rotación alta
        } else if (dias <= 20) {
            color = '#ffc107'; // Amarillo - Aceptable  
        } else {
            color = '#dc3545'; // Rojo - Baja rotación
        }

        return {
            y: parseInt(dias),
            color: color
        };
    });

    this.charts.rotacionInventario = Highcharts.chart(this.$refs.rotacionInventarioChart, {
        chart: {
            type: 'column',
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
            categories: productosNombres,
                labels: {
                    style: {
                        color: '#6c757d',
                        fontSize: '12px'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Días promedio de rotación',
                    style: {
                        color: '#6c757d',
                        fontSize: '12px'
                    }
                },
                labels: {
                    style: {
                        color: '#6c757d',
                        fontSize: '12px'
                    }
                },
                gridLineDashStyle: 'Dash'
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b><br>' + this.y + ' días';
                }
            },
            plotOptions: {
                column: {
                    borderRadius: 5
                }
            },
            series: [{
                name: 'Días promedio',
                data: datosConColores,
                showInLegend: false
            }]
        });
};

Vue.prototype.inicializarGraficoMovimientos = function() {
    if (!this.$refs.movimientosInventarioChart || !this.datosStock || !this.datosStock.movimientos) {
        console.log('No hay datos de movimientos disponibles');
        return;
    }

    const movimientosData = this.datosStock.movimientos;
    
    // Extraer meses únicos
    const meses = [...new Set(movimientosData.map(item => item.mes))].sort();
    
    // Procesar datos de entradas y salidas
    const entradas = [];
    const salidas = [];
    
    meses.forEach(mes => {
        const entradasMes = movimientosData.filter(item => item.mes === mes && item.tipo_movimiento === 'INGRESO');
        const salidasMes = movimientosData.filter(item => item.mes === mes && item.tipo_movimiento === 'SALIDA');
        
        const totalEntradas = entradasMes.reduce((sum, item) => sum + parseInt(item.total_cantidad), 0);
        const totalSalidas = salidasMes.reduce((sum, item) => sum + parseInt(item.total_cantidad), 0);
        
        entradas.push(totalEntradas);
        salidas.push(totalSalidas);
    });

    // Formatear meses para mostrar (convertir YYYY-MM a formato corto)
    const mesesFormateados = meses.map(mes => {
        const [year, month] = mes.split('-');
        const mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 
                             'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        return mesesNombres[parseInt(month) - 1];
    });

    this.charts.movimientosInventario = Highcharts.chart(this.$refs.movimientosInventarioChart, {
        chart: {
            type: 'areaspline',
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
            categories: mesesFormateados,
            labels: {
                style: {
                    color: '#6c757d',
                    fontSize: '12px'
                }
            }
        },
        yAxis: {
            title: {
                text: 'Unidades',
                style: {
                    color: '#6c757d',
                    fontSize: '12px'
                }
            },
            labels: {
                style: {
                    color: '#6c757d',
                    fontSize: '12px'
                }
            },
            gridLineDashStyle: 'Dash'
        },
        tooltip: {
            shared: true,
            crosshairs: true
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.3,
                marker: {
                    radius: 4,
                    lineWidth: 2,
                    lineColor: '#ffffff'
                }
            }
        },
        series: [
            {
                name: 'Entradas',
                color: this.colors.success,
                data: entradas
            },
            {
                name: 'Salidas',
                color: this.colors.danger,
                data: salidas
            }
        ]
    });
};

// Funciones auxiliares para stock
Vue.prototype.cargarEstadisticasStock = function() {
    // Simular carga de estadísticas de stock
    this.estadisticasStock = {
        optimo: 45,
        normal: 30,
        bajo: 20,
        critico: 5
    };
    console.log('Estadísticas de stock cargadas:', this.estadisticasStock);
};

Vue.prototype.filtrarStock = function() {
    // Implementar filtro de stock
    if (!this.filtroStock) {
        this.productosStockFiltrados = [...this.productosStock];
        return;
    }

    const filtro = this.filtroStock.toLowerCase();
    this.productosStockFiltrados = this.productosStock.filter(producto => 
        producto.nombre.toLowerCase().includes(filtro) ||
        producto.codigo.toLowerCase().includes(filtro)
    );
    
    console.log('Stock filtrado:', this.productosStockFiltrados.length, 'productos');
};

Vue.prototype.mostrarProductosPorEstado = function(estadoNombre) {
    console.log('Mostrar productos por estado:', estadoNombre);
    
    // Configurar el modal según el estado
    const alertaElement = document.getElementById('alertaEstadoSeleccionado');
    const iconoElement = document.getElementById('iconoEstadoSeleccionado');
    const tituloElement = document.getElementById('tituloEstadoSeleccionado');
    const descripcionElement = document.getElementById('descripcionEstadoSeleccionado');
    
    // Configurar colores y textos según el estado
    let alertClass = 'alert-info';
    let iconClass = 'fas fa-box';
    let descripcion = '';
    
    switch(estadoNombre) {
        case 'Óptimo':
            alertClass = 'alert-success';
            iconClass = 'fas fa-check-circle';
            descripcion = 'Productos con stock suficiente para la demanda';
            break;
        case 'Normal':
            alertClass = 'alert-info';
            iconClass = 'fas fa-info-circle';
            descripcion = 'Productos con stock adecuado';
            break;
        case 'Bajo':
            alertClass = 'alert-warning';
            iconClass = 'fas fa-exclamation-triangle';
            descripcion = 'Productos que necesitan reposición pronto';
            break;
        case 'Crítico':
            alertClass = 'alert-danger';
            iconClass = 'fas fa-exclamation-circle';
            descripcion = 'Productos con stock muy bajo - Reposición urgente';
            break;
    }
    
    // Aplicar configuración
    alertaElement.className = `alert ${alertClass}`;
    iconoElement.className = iconClass;
    tituloElement.textContent = `Estado: ${estadoNombre}`;
    descripcionElement.textContent = descripcion;
    
    // Simular carga de productos
    document.getElementById('loadingProductosEstado').style.display = 'block';
    document.getElementById('cuerpoTablaProductosEstado').innerHTML = '';
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('productosEstadoModal'));
    modal.show();
    
    // Simular datos después de 1 segundo
    setTimeout(() => {
        const productosSimulados = this.generarProductosSimulados(estadoNombre);
        this.llenarTablaProductosEstado(productosSimulados);
        document.getElementById('loadingProductosEstado').style.display = 'none';
        document.getElementById('contadorProductosEstado').textContent = `${productosSimulados.length} productos encontrados`;
    }, 1000);
};

Vue.prototype.generarProductosSimulados = function(estado) {
    const productos = [];
    const cantidad = Math.floor(Math.random() * 10) + 5; // 5-15 productos
    
    for(let i = 1; i <= cantidad; i++) {
        let stock;
        switch(estado) {
            case 'Óptimo': stock = Math.floor(Math.random() * 200) + 100; break;
            case 'Normal': stock = Math.floor(Math.random() * 50) + 25; break;
            case 'Bajo': stock = Math.floor(Math.random() * 15) + 10; break;
            case 'Crítico': stock = Math.floor(Math.random() * 5) + 1; break;
            default: stock = Math.floor(Math.random() * 100) + 1;
        }
        
        productos.push({
            codigo: `PROD-${String(i).padStart(3, '0')}`,
            nombre: `Producto ${estado} ${i}`,
            categoria: 'General',
            stock: stock,
            precio: (Math.random() * 500 + 50).toFixed(2),
            estado: estado,
            actualizado: new Date().toLocaleDateString()
        });
    }
    
    return productos;
};

Vue.prototype.llenarTablaProductosEstado = function(productos) {
    const tbody = document.getElementById('cuerpoTablaProductosEstado');
    tbody.innerHTML = '';
    
    productos.forEach(producto => {
        const fila = document.createElement('tr');
        
        let badgeClass = 'badge-secondary';
        switch(producto.estado) {
            case 'Óptimo': badgeClass = 'badge-success'; break;
            case 'Normal': badgeClass = 'badge-info'; break;
            case 'Bajo': badgeClass = 'badge-warning'; break;
            case 'Crítico': badgeClass = 'badge-danger'; break;
        }
        
        fila.innerHTML = `
            <td>${producto.codigo}</td>
            <td>${producto.nombre}</td>
            <td>${producto.categoria}</td>
            <td><span class="fw-bold">${producto.stock}</span></td>
            <td>S/ ${producto.precio}</td>
            <td><span class="badge ${badgeClass}">${producto.estado}</span></td>
            <td>${producto.actualizado}</td>
        `;
        
        tbody.appendChild(fila);
    });
    
    // Configurar búsqueda en la tabla
    document.getElementById('busquedaProductosEstado').addEventListener('input', (e) => {
        const filtro = e.target.value.toLowerCase();
        const filas = tbody.querySelectorAll('tr');
        
        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });
};