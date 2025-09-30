/**
 * Dashboard Vue.js Application
 * Aplicación principal del dashboard con todos los componentes
 */

// Crear la aplicación Vue
window.vueApp = new Vue({
    el: '#app',
    data: {
        vendedores: [],
        metasData: {},
        activeTab: 'ventas',
        loadingCharts: true,
        dashboardData: typeof window.dashboardData !== 'undefined' ? window.dashboardData : {},
        charts: {},
        colors: {
            primary: '#4361ee',
            secondary: '#3f37c9',
            success: '#4cc9f0',
            danger: '#f94144',
            warning: '#f8961e',
            info: '#90e0ef',
            light: '#f8f9fa',
            dark: '#212529',
            purple: '#7209b7',
            pink: '#f72585',
            indigo: '#560bad',
            teal: '#2ec4b6',
            orange: '#ff9e00',
            yellow: '#ffbe0b'
        },
        meses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        mesesAbrev: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        periodoActual: typeof window.periodoActual !== 'undefined' ? window.periodoActual : 'mes',
        periodoTexto: typeof window.periodoTexto !== 'undefined' ? window.periodoTexto : 'Este mes',
        filtroFechas: {
            inicio: '',
            fin: ''
        },
        reporteSeleccionado: '',
        tipoPeriodoReporte: 'rango',
        anioSeleccionado: '',
        mesSeleccionado: '',
        aniosDisponibles: [],
        filtroVentas: '',
        filtroStock: '',
        filtroClientes: '',
        filtroProductos: '',

        // Datos específicos para stock
        datosStock: null, // Datos de stock cargados desde el servidor
        productosStock: [],
        productosStockFiltrados: [],
        estadisticasStock: null,
        loadingVentas: false,
        loadingStock: false,
        periodoActualStock: 'mes',
        periodoTextoStock: 'Este mes',
        filtroFechasStock: {
            inicio: '',
            fin: ''
        },
        periodoActualProductos: 'mes',
        periodoTextoProductos: 'Este mes',
        filtroFechasProductos: {
            inicio: '',
            fin: ''
        },
        loadingProductos: false,
        periodoActualIngresos: 'mes',
        periodoTextoIngresos: 'Este mes',
        filtroFechasIngresos: {
            inicio: '',
            fin: ''
        },
        loadingIngresos: false,
        periodoActualClientes: 'mes',
        periodoTextoClientes: 'Este mes',
        filtroFechasClientes: {
            inicio: '',
            fin: ''
        },
        loadingClientes: false,
        montoMetaActual: 0, // Para guardar el monto actual de la meta
        
        // Datos para gestión de vendedores
        tipoMetaActiva: 'empresa', // 'empresa' o 'individual'
        todosVendedores: [], // Lista completa de vendedores
        vendedoresFiltrados: [], // Vendedores filtrados
        busquedaVendedores: '',
        filtroRolVendedor: '',
        cargandoVendedores: false,
        resumenVendedores: {},  // Resumen con meta_total_empresa etc

        // Datos para Cotizaciones
        datosCotizaciones: {},
        periodoActualCotizaciones: 'mes',
        periodoTextoCotizaciones: 'Este mes',
        filtroFechasCotizaciones: { inicio: '', fin: '' },
        loadingCotizaciones: false,

        // Datos para Guías
        datosGuias: {},
        periodoActualGuias: 'mes',
        periodoTextoGuias: 'Este mes',
        filtroFechasGuias: { inicio: '', fin: '' },
        loadingGuias: false,
        buscarGuia: '',
        filtroEstadoGuia: '',
        guiasFiltradas: []
    },

    computed: {
        calcularPorcentajeFacturas() {
            if (this.dashboardData.totalVentas > 0) {
                return ((this.dashboardData.totalFacturas / this.dashboardData.totalVentas) * 100).toFixed(1);
            }
            return 0;
        },
        calcularPorcentajeBoletas() {
            if (this.dashboardData.totalVentas > 0) {
                return ((this.dashboardData.totalBoletas / this.dashboardData.totalVentas) * 100).toFixed(1);
            }
            return 0;
        },
        comparativaMesAnterior() {
            const diferencia = this.dashboardData.totalVentas - this.dashboardData.totalMesAnterior;
            const porcentaje = (this.dashboardData.totalMesAnterior > 0) ? (diferencia / this.dashboardData.totalMesAnterior) * 100 : 0;
            const icono = (porcentaje >= 0) ? '<i class="fas fa-arrow-up text-success"></i>' : '<i class="fas fa-arrow-down text-danger"></i>';
            return icono + ' ' + Math.abs(porcentaje.toFixed(1)) + '%';
        },
        calcularPorcentajeIngresos() {
            if (this.dashboardData.totalMesAnterior > 0) {
                return ((this.dashboardData.ingresosMensuales - this.dashboardData.totalMesAnterior) / this.dashboardData.totalMesAnterior * 100).toFixed(1);
            }
            return 0;
        },
        porcentajeEgresos() {
            if (this.dashboardData.ingresosMensuales > 0) {
                return ((this.dashboardData.egresosMensuales / this.dashboardData.ingresosMensuales) * 100).toFixed(1);
            }
            return 0;
        },
        porcentajeGanancia() {
            if (this.dashboardData.ingresosMensuales > 0) {
                return ((this.dashboardData.gananciaMensual / this.dashboardData.ingresosMensuales) * 100).toFixed(1);
            }
            return 0;
        },
        hayDatosVentasAnuales() {
            const datos = this.dashboardData.ventasAnuales;
            if (!datos || !Array.isArray(datos) || datos.length === 0) {
                return false;
            }

            const tieneValores = datos.some(valor => {
                const num = parseFloat(valor);
                return !isNaN(num) && num > 0;
            });

            console.log('hayDatosVentasAnuales:', tieneValores, datos);
            return tieneValores;
        },
        hayDatosComparativa() {
            return this.dashboardData.ventasPorPeriodo && this.dashboardData.ventasPorPeriodo.some(valor => valor > 0);
        },
        hayDatosProductos() {
            return this.dashboardData.productosNombres &&
                this.dashboardData.productosNombres.length > 0 &&
                this.dashboardData.productosCantidades &&
                this.dashboardData.productosCantidades.length > 0 &&
                this.dashboardData.productosCantidades.some(c => parseFloat(c) > 0);
        },
        hayDatosClientes() {
            return this.dashboardData.clientesNombres && this.dashboardData.clientesNombres.length > 0;
        },
        hayDatosStock() {
            return this.hayDatosProductos;
        },
        hayDatosIngresos() {
            return this.dashboardData.ingresosMensuales > 0;
        },
        textoBotonMeta() {
            return this.metasData && this.metasData.tiene_meta
                ? 'Editar Meta Total'
                : 'Establecer Meta Total';
        },
        hayDatosVendedores() {
            return this.vendedores && this.vendedores.length > 0;
        },
        hayDatosUtilidadBruta() {
            return this.dashboardData.utilidadBrutaPorPeriodo &&
                this.dashboardData.utilidadBrutaPorPeriodo.some(valor => valor > 0);
        },
        porcentajeUtilidad() {
            const totalVentas = parseFloat(this.dashboardData.totalVentas) || 0;
            const utilidadActual = parseFloat(this.dashboardData.utilidadBrutaActual) || 0;

            if (totalVentas > 0 && utilidadActual > 0) {
                return ((utilidadActual / totalVentas) * 100).toFixed(1);
            }
            return '0.0';
        },
        porcentajeUtilidadClass() {
            const porcentaje = parseFloat(this.porcentajeUtilidad);
            if (porcentaje >= 30) return 'text-success';
            if (porcentaje >= 15) return 'text-warning';
            return 'text-danger';
        },
        hayDatosProductosPorCategoria() {
            return this.dashboardData.categorias &&
                this.dashboardData.categorias.length > 0 &&
                this.dashboardData.productosPorCategoria &&
                this.dashboardData.productosPorCategoria.length > 0;
        },
        comparativaProductosAnterior() {
            const actual = this.dashboardData.totalProductosVendidos || 0;
            const anterior = this.dashboardData.totalProductosVendidosAnterior || 0;
            const diferencia = actual - anterior;
            const porcentaje = anterior > 0 ? (diferencia / anterior) * 100 : 0;
            const icono = porcentaje >= 0 ? '<i class="fas fa-arrow-up text-success"></i>' : '<i class="fas fa-arrow-down text-danger"></i>';
            return icono + ' ' + Math.abs(porcentaje.toFixed(1)) + '%';
        },
        textoComparativaProductos() {
            switch (this.periodoActualProductos) {
                case 'hoy': return 'vs. Día Anterior';
                case 'semana': return 'vs. Semana Anterior';
                case 'mes': return 'vs. Mes Anterior';
                case 'anio': return 'vs. Año Anterior';
                default: return 'vs. Período Anterior';
            }
        },

        hayDatosProductosPorCategoria() {
            return this.dashboardData.categorias &&
                this.dashboardData.categorias.length > 0 &&
                this.dashboardData.productosPorCategoria &&
                this.dashboardData.productosPorCategoria.length > 0;
        },

        hayDatosStock() {
            // Verificar si hay productos en general, independiente de ventas
            return this.dashboardData.productosNombres && this.dashboardData.productosNombres.length > 0;
        },
        
        // Computed properties para Cotizaciones
        hayDatosCotizaciones() {
            return this.datosCotizaciones.total_cotizaciones && this.datosCotizaciones.total_cotizaciones > 0;
        },
        
        // Computed properties para Guías
        hayDatosGuias() {
            return this.datosGuias.total_guias && this.datosGuias.total_guias > 0;
        },

        // URL base para enlaces
        baseUrl() {
            return _URL;
        }
    },

    methods: {
        setActiveTab(tab) {
            console.log('Cambiando a pestaña:', tab);
            this.activeTab = tab;

            // Limpiar URL al cambiar de pestaña (excepto ventas)
            if (tab !== 'ventas') {
                const cleanUrl = window.location.pathname;
                window.history.pushState({}, '', cleanUrl);
            }

            // Limpiar filtros específicos por pestaña
            switch (tab) {
                case 'stock':
                    this.filtroStock = '';
                    this.cargarEstadisticasStock();
                    // Cargar datos de stock con el período por defecto
                    this.cambiarPeriodoStock(this.periodoActualStock);
                    break;
                case 'ventas':
                    this.filtroVentas = '';
                    break;
                case 'productos':
                    this.filtroProductos = '';
                    // Cargar datos de productos con el período por defecto
                    this.cambiarPeriodoProductos(this.periodoActualProductos);
                    break;
                case 'ingresos-egresos':
                    // Cargar datos de ingresos/egresos con el período por defecto
                    this.cambiarPeriodoIngresos(this.periodoActualIngresos);
                    break;
                case 'clientes':
                    this.filtroClientes = '';
                    // Cargar datos de clientes con el período por defecto
                    this.cambiarPeriodoClientes(this.periodoActualClientes);
                    break;
                case 'metas-ventas':
                    // Cargar datos específicos para metas
                    this.cargarDatosVendedores();
                    break;
                case 'cotizaciones':
                    // Cargar datos de cotizaciones con el período por defecto
                    this.cambiarPeriodoCotizaciones(this.periodoActualCotizaciones);
                    break;
                case 'guias':
                    // Cargar datos de guías con el período por defecto
                    this.cambiarPeriodoGuias(this.periodoActualGuias);
                    break;
            }

            // Usar un delay más pequeño para mejorar la experiencia
            this.$nextTick(() => {
                // Forzar re-render del contenido
                this.$forceUpdate();
                
                // Inicializar gráficos después de que el contenido esté visible
                setTimeout(() => {
                    this.inicializarGraficosPorTab(tab);
                }, 100);
                
                // Scroll suave hacia arriba
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        },

        formatNumber(value) {
            return new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
        },

        destruirTodosLosGraficos() {
            // Destruir todos los gráficos existentes
            Object.keys(this.charts).forEach(chartKey => {
                if (this.charts[chartKey]) {
                    try {
                        this.charts[chartKey].destroy();
                    } catch (e) {
                        console.log(`Chart ${chartKey} already destroyed`);
                    }
                    this.charts[chartKey] = null;
                }
            });
        },

        inicializarGraficos() {
            // Verificar si Highcharts está disponible
            if (typeof Highcharts === 'undefined') {
                console.error('Highcharts no está disponible. Reintentando en 500ms...');
                setTimeout(() => {
                    this.inicializarGraficos();
                }, 500);
                return;
            }

            console.log('Highcharts cargado correctamente, inicializando gráficos...');

            // Configuración global de Highcharts
            Highcharts.setOptions({
                lang: {
                    thousandsSep: ',',
                    numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E']
                },
                credits: {
                    enabled: false
                }
            });

            // Inicializar los gráficos de la pestaña activa
            this.inicializarGraficosPorTab(this.activeTab);
            this.loadingCharts = false;
        },

        inicializarGraficosPorTab(tab) {
            switch (tab) {
                case 'ventas':
                    this.inicializarGraficosVentas();
                    break;
                case 'productos':
                    this.inicializarGraficosProductos();
                    break;
                case 'stock':
                    this.inicializarGraficosStock();
                    break;
                case 'ingresos-egresos':
                    this.inicializarGraficosIngresos();
                    break;
                case 'clientes':
                    this.inicializarGraficosClientes();
                    break;
                case 'metas-ventas':
                    this.cargarDatosVendedores();
                    this.inicializarGraficosVendedores();
                    break;
            }
        },

        obtenerTituloComparativa() {
            if (!this.dashboardData.textosPeriodo) {
                return 'Comparativa con Años Anteriores';
            }

            switch (this.dashboardData.periodoActual) {
                case 'hoy':
                    return 'Comparativa con Días Anteriores';
                case 'semana':
                    return 'Comparativa con Semanas Anteriores';
                case 'mes':
                    return 'Comparativa con Meses Anteriores';
                case 'anio':
                    return 'Comparativa con Años Anteriores';
                default:
                    return 'Comparativa con Períodos Anteriores';
            }
        },

        // Funciones que estaban en home.php como Vue.prototype
        cambiarPeriodo(periodo) {
            // Solo permitir cambio de período en la pestaña de ventas
            if (this.activeTab !== 'ventas') {
                return;
            }

            this.periodoActual = periodo;

            // Actualizar texto del botón
            switch (periodo) {
                case 'hoy':
                    this.periodoTexto = 'Hoy';
                    break;
                case 'semana':
                    this.periodoTexto = 'Esta semana';
                    break;
                case 'mes':
                    this.periodoTexto = 'Este mes';
                    break;
                case 'anio':
                    this.periodoTexto = 'Este año';
                    break;
                default:
                    this.periodoTexto = 'Período personalizado';
            }

            // Destruir gráficos antes de la petición
            this.destruirTodosLosGraficos();

            // Mostrar loading
            this.loadingCharts = true;
            this.loadingVentas = true;

            // Preparar parámetros para la petición AJAX
            const params = new URLSearchParams();
            params.append('periodo', periodo);

            if (periodo === 'personalizado') {
                params.append('fecha_inicio', this.filtroFechas.inicio);
                params.append('fecha_fin', this.filtroFechas.fin);
            }

            // Hacer petición AJAX
            fetch(`${_URL}/ajs/dashboard/datos?${params}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Datos recibidos:', data);

                    if (data.success) {
                        // Actualizar datos del dashboard
                        Object.keys(data.dashboardData).forEach(key => {
                            if (this.dashboardData.hasOwnProperty(key)) {
                                this.dashboardData[key] = data.dashboardData[key];
                            }
                        });

                        this.$nextTick(() => {
                            this.inicializarGraficosVentas();
                            this.loadingCharts = false;
                            this.loadingVentas = false;
                        });
                    } else {
                        console.error('Error en la respuesta:', data.message);
                        this.loadingCharts = false;
                        this.loadingVentas = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loadingCharts = false;
                    this.loadingVentas = false;
                });
        },

        abrirModalPersonalizado() {
            this.filtroFechas.inicio = '';
            this.filtroFechas.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoModal'));
            modal.show();
        },

        aplicarPeriodoPersonalizado() {
            if (!this.filtroFechas.inicio || !this.filtroFechas.fin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor seleccione ambas fechas para continuar',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoModal'));
            if (modal) {
                modal.hide();
            }

            // Llamar a cambiarPeriodo con 'personalizado'
            this.cambiarPeriodo('personalizado');
        },

        abrirModalReporte() {
            const modal = new bootstrap.Modal(document.getElementById('descargarReporteModal'));
            modal.show();
        },

        cambiarTipoPeriodo() {
            // Limpiar valores cuando cambia el tipo
            this.anioSeleccionado = '';
            this.mesSeleccionado = '';
            this.filtroFechas.inicio = '';
            this.filtroFechas.fin = '';
        },

        obtenerNombreMes(numeroMes) {
            return this.meses[parseInt(numeroMes) - 1] || '';
        },

        descargarReporte() {
            if (!this.reporteSeleccionado) {
                Swal.fire({
                    icon: 'info',
                    title: 'Seleccione un reporte',
                    text: 'Por favor seleccione un tipo de reporte para continuar',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            console.log('Descargando reporte:', this.reporteSeleccionado);
            Swal.fire({
                icon: 'info',
                title: 'Funcionalidad en desarrollo',
                text: 'La descarga de reportes PDF estará disponible próximamente',
                confirmButtonText: 'Entendido'
            });
        },

        descargarReporteExcel() {
            if (!this.reporteSeleccionado) {
                Swal.fire({
                    icon: 'info',
                    title: 'Seleccione un reporte',
                    text: 'Por favor seleccione un tipo de reporte para continuar',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Validar fechas o período
            let parametros = new URLSearchParams();
            parametros.append('tipo', this.reporteSeleccionado);
            parametros.append('periodo_tipo', this.tipoPeriodoReporte);

            if (this.tipoPeriodoReporte === 'rango') {
                if (!this.filtroFechas.inicio || !this.filtroFechas.fin) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fechas requeridas',
                        text: 'Por favor seleccione las fechas de inicio y fin del reporte',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                parametros.append('fecha_inicio', this.filtroFechas.inicio);
                parametros.append('fecha_fin', this.filtroFechas.fin);
            } else if (this.tipoPeriodoReporte === 'anual') {
                if (!this.anioSeleccionado) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Año requerido',
                        text: 'Por favor seleccione el año para el reporte',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                parametros.append('anio', this.anioSeleccionado);
                if (this.mesSeleccionado) {
                    parametros.append('mes', this.mesSeleccionado);
                }
            }

            // Mostrar loading
            Swal.fire({
                title: 'Generando reporte...',
                text: 'Por favor espere mientras se genera el archivo Excel',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Crear la URL de descarga
            const url = `${_URL}/r/dashboard/reporte-excel?${parametros.toString()}`;
            
            // Crear un enlace temporal para descargar
            const link = document.createElement('a');
            link.href = url;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Cerrar el loading y mostrar éxito
            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Reporte generado',
                    text: 'El reporte Excel se está descargando',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('descargarReporteModal'));
                if (modal) {
                    modal.hide();
                }
            }, 1000);
        },

        cambiarPeriodoProductos(periodo) {
            this.periodoActualProductos = periodo;

            switch (periodo) {
                case 'hoy':
                    this.periodoTextoProductos = 'Hoy';
                    break;
                case 'semana':
                    this.periodoTextoProductos = 'Esta semana';
                    break;
                case 'mes':
                    this.periodoTextoProductos = 'Este mes';
                    break;
                case 'anio':
                    this.periodoTextoProductos = 'Este año';
                    break;
                default:
                    this.periodoTextoProductos = 'Período personalizado';
            }

            // Mostrar loading
            this.loadingProductos = true;

            // Preparar parámetros
            const params = new URLSearchParams();
            params.append('periodo', periodo);

            if (periodo === 'personalizado') {
                params.append('fecha_inicio', this.filtroFechasProductos.inicio);
                params.append('fecha_fin', this.filtroFechasProductos.fin);
            }

            fetch(`${_URL}/ajs/dashboard/datos-productos?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar datos de productos
                        Object.keys(data.productosData).forEach(key => {
                            if (this.dashboardData.hasOwnProperty(key)) {
                                this.dashboardData[key] = data.productosData[key];
                            }
                        });

                        this.$nextTick(() => {
                            this.inicializarGraficosProductos();
                            this.loadingProductos = false;
                        });
                    } else {
                        console.error('Error al cargar los datos:', data.message);
                        this.loadingProductos = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loadingProductos = false;
                });
        },

        cambiarPeriodoStock(periodo) {
            this.periodoActualStock = periodo;

            switch (periodo) {
                case 'hoy':
                    this.periodoTextoStock = 'Hoy';
                    break;
                case 'semana':
                    this.periodoTextoStock = 'Esta semana';
                    break;
                case 'mes':
                    this.periodoTextoStock = 'Este mes';
                    break;
                case 'anio':
                    this.periodoTextoStock = 'Este año';
                    break;
                default:
                    this.periodoTextoStock = 'Período personalizado';
            }

            // Mostrar loading
            this.loadingStock = true;

            // Preparar parámetros
            const params = new URLSearchParams();
            params.append('periodo', periodo);

            if (periodo === 'personalizado') {
                params.append('fecha_inicio', this.filtroFechasStock.inicio);
                params.append('fecha_fin', this.filtroFechasStock.fin);
            }

            fetch(`${_URL}/ajs/dashboard/datos-stock?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar datos de stock
                        this.datosStock = data.data;

                        this.$nextTick(() => {
                            this.inicializarGraficoRotacion();
                            this.inicializarGraficoMovimientos();
                            this.loadingStock = false;
                        });
                    } else {
                        console.error('Error al cargar los datos de stock:', data.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar los datos de stock'
                        });
                        this.loadingStock = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error', 
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                    this.loadingStock = false;
                });
        },

        abrirModalPersonalizadoStock() {
            this.filtroFechasStock.inicio = '';
            this.filtroFechasStock.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoStockModal'));
            modal.show();
        },

        aplicarPeriodoPersonalizadoStock() {
            if (!this.filtroFechasStock.inicio || !this.filtroFechasStock.fin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor selecciona las fechas de inicio y fin'
                });
                return;
            }

            const fechaInicio = new Date(this.filtroFechasStock.inicio);
            const fechaFin = new Date(this.filtroFechasStock.fin);

            if (fechaInicio > fechaFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas inválidas',
                    text: 'La fecha de inicio debe ser anterior a la fecha de fin'
                });
                return;
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoStockModal'));
            modal.hide();

            // Llamar a cambiarPeriodoStock con 'personalizado'
            this.cambiarPeriodoStock('personalizado');
        },

        cambiarPeriodoIngresos(periodo) {
            this.periodoActualIngresos = periodo;

            switch (periodo) {
                case 'hoy':
                    this.periodoTextoIngresos = 'Hoy';
                    break;
                case 'semana':
                    this.periodoTextoIngresos = 'Esta semana';
                    break;
                case 'mes':
                    this.periodoTextoIngresos = 'Este mes';
                    break;
                case 'anio':
                    this.periodoTextoIngresos = 'Este año';
                    break;
                default:
                    this.periodoTextoIngresos = 'Período personalizado';
            }

            // Mostrar loading
            this.loadingIngresos = true;

            // Preparar parámetros
            const params = new URLSearchParams();
            params.append('periodo', periodo);

            if (periodo === 'personalizado') {
                params.append('fecha_inicio', this.filtroFechasIngresos.inicio);
                params.append('fecha_fin', this.filtroFechasIngresos.fin);
            }

            fetch(`${_URL}/ajs/dashboard/datos-ingresos-egresos?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar datos de ingresos/egresos
                        Object.keys(data.ingresosData).forEach(key => {
                            if (this.dashboardData.hasOwnProperty(key)) {
                                this.dashboardData[key] = data.ingresosData[key];
                            }
                        });

                        this.$nextTick(() => {
                            this.inicializarGraficosIngresos();
                            this.loadingIngresos = false;
                        });
                    } else {
                        console.error('Error al cargar los datos de ingresos/egresos:', data.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar los datos de ingresos/egresos'
                        });
                        this.loadingIngresos = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error', 
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                    this.loadingIngresos = false;
                });
        },

        abrirModalPersonalizadoIngresos() {
            this.filtroFechasIngresos.inicio = '';
            this.filtroFechasIngresos.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoIngresosModal'));
            modal.show();
        },

        aplicarPeriodoPersonalizadoIngresos() {
            if (!this.filtroFechasIngresos.inicio || !this.filtroFechasIngresos.fin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor selecciona las fechas de inicio y fin'
                });
                return;
            }

            const fechaInicio = new Date(this.filtroFechasIngresos.inicio);
            const fechaFin = new Date(this.filtroFechasIngresos.fin);

            if (fechaInicio > fechaFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas inválidas',
                    text: 'La fecha de inicio debe ser anterior a la fecha de fin'
                });
                return;
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoIngresosModal'));
            modal.hide();

            // Llamar a cambiarPeriodoIngresos con 'personalizado'
            this.cambiarPeriodoIngresos('personalizado');
        },

        cambiarPeriodoClientes(periodo) {
            this.periodoActualClientes = periodo;

            switch (periodo) {
                case 'hoy':
                    this.periodoTextoClientes = 'Hoy';
                    break;
                case 'semana':
                    this.periodoTextoClientes = 'Esta semana';
                    break;
                case 'mes':
                    this.periodoTextoClientes = 'Este mes';
                    break;
                case 'anio':
                    this.periodoTextoClientes = 'Este año';
                    break;
                default:
                    this.periodoTextoClientes = 'Período personalizado';
            }

            // Mostrar loading
            this.loadingClientes = true;

            // Preparar parámetros
            const params = new URLSearchParams();
            params.append('periodo', periodo);

            if (periodo === 'personalizado') {
                params.append('fecha_inicio', this.filtroFechasClientes.inicio);
                params.append('fecha_fin', this.filtroFechasClientes.fin);
            }

            // Hacer petición para datos de clientes (gráficos)
            fetch(`${_URL}/ajs/dashboard/datos-clientes?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar datos de clientes en dashboardData
                        if (data.clientesData) {
                            Object.keys(data.clientesData).forEach(key => {
                                if (this.dashboardData.hasOwnProperty(key)) {
                                    this.dashboardData[key] = data.clientesData[key];
                                }
                            });
                        }

                        this.$nextTick(() => {
                            this.inicializarGraficosClientes();
                            this.loadingClientes = false;
                        });
                    } else {
                        console.error('Error al cargar los datos de clientes:', data.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar los datos de clientes'
                        });
                        this.loadingClientes = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error', 
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                    this.loadingClientes = false;
                });
        },

        abrirModalPersonalizadoClientes() {
            this.filtroFechasClientes.inicio = '';
            this.filtroFechasClientes.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoClientesModal'));
            modal.show();
        },

        aplicarPeriodoPersonalizadoClientes() {
            if (!this.filtroFechasClientes.inicio || !this.filtroFechasClientes.fin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor selecciona las fechas de inicio y fin'
                });
                return;
            }

            const fechaInicio = new Date(this.filtroFechasClientes.inicio);
            const fechaFin = new Date(this.filtroFechasClientes.fin);

            if (fechaInicio > fechaFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas inválidas',
                    text: 'La fecha de inicio debe ser anterior a la fecha de fin'
                });
                return;
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoClientesModal'));
            modal.hide();

            // Llamar a cambiarPeriodoClientes con 'personalizado'
            this.cambiarPeriodoClientes('personalizado');
        },

        filtrarClientes() {
            // Implementar filtro de búsqueda de clientes si es necesario
            console.log('Filtrar clientes:', this.filtroClientes);
        },

        abrirModalPersonalizadoProductos() {
            this.filtroFechasProductos.inicio = '';
            this.filtroFechasProductos.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoModal'));
            modal.show();
        },

        // Funciones para vendedores y metas
        cargarDatosVendedores() {
            console.log('Cargando datos de vendedores...');
            fetch(`${_URL}/ajs/dashboard/vendedores-metas`)
                .then(response => response.json())
                .then(data => {
                    console.log('Datos de vendedores recibidos:', data);
                    if (data.success) {
                        this.metasData = data;
                        this.vendedores = data.vendedores;
                        console.log('Vendedores cargados:', this.vendedores);
                        this.actualizarResumenMetas(data.resumen);
                        this.inicializarGraficosVendedores();
                    } else {
                        console.error('Error en la respuesta:', data.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al cargar datos de vendedores'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en la petición de vendedores:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor para cargar los vendedores'
                    });
                });
        },

        inicializarGraficosVendedores() {
            console.log('Inicializando gráficos de vendedores...');
            console.log('Vendedores disponibles:', this.vendedores);
            
            if (!this.hayDatosVendedores) {
                console.log('No hay datos de vendedores para mostrar gráficos');
                return;
            }

            // Destruir gráfico existente si existe
            if (this.charts.vendedores) {
                try {
                    this.charts.vendedores.destroy();
                } catch (e) {
                    console.log('Chart vendedores already destroyed');
                }
            }

            // Preparar datos para el gráfico
            const nombresVendedores = this.vendedores.map(v => v.nombres || v.nombre || 'Vendedor');
            const ventasVendedores = this.vendedores.map(v => parseFloat(v.ventas_actuales || v.ventas_mes || v.ventas || 0));
            // No usamos meta_total_empresa porque es la meta de toda la empresa, no individual

            console.log('Datos para gráfico:', {
                nombres: nombresVendedores,
                ventas: ventasVendedores
            });

            // Crear gráfico de competencia de vendedores
            if (this.$refs.vendedoresChart) {
                this.charts.vendedores = Highcharts.chart(this.$refs.vendedoresChart, {
                    chart: {
                        type: 'column',
                        style: {
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: nombresVendedores,
                        labels: {
                            style: {
                                color: '#6c757d',
                                fontSize: '12px'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Monto de Ventas (S/)',
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
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b><br/>' +
                                this.series.name + ': S/ ' + Highcharts.numberFormat(this.y, 2) + '<br/>';
                        }
                    },
                    plotOptions: {
                        column: {
                            borderRadius: 3,
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return 'S/ ' + Highcharts.numberFormat(this.y, 0);
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Ventas Actuales',
                        data: ventasVendedores,
                        color: this.colors.primary
                    }],
                    legend: {
                        enabled: true,
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                });
            } else {
                console.error('Elemento ref vendedoresChart no encontrado');
            }

            // Crear gráfico de Top Vendedores (gráfico circular)
            if (this.$refs.topVendedoresChart) {
                // Destruir gráfico existente si existe
                if (this.charts.topVendedores) {
                    try {
                        this.charts.topVendedores.destroy();
                    } catch (e) {
                        console.log('Chart topVendedores already destroyed');
                    }
                }

                // Preparar datos para gráfico de pastel (top 5 vendedores)
                const topVendedores = this.vendedores
                    .sort((a, b) => parseFloat(b.ventas_actuales || b.ventas_mes || b.ventas || 0) - parseFloat(a.ventas_actuales || a.ventas_mes || a.ventas || 0))
                    .slice(0, 5);

                const datosTopVendedores = topVendedores.map(vendedor => ({
                    name: vendedor.nombres || vendedor.nombre || 'Vendedor',
                    y: parseFloat(vendedor.ventas_actuales || vendedor.ventas_mes || vendedor.ventas || 0)
                }));

                this.charts.topVendedores = Highcharts.chart(this.$refs.topVendedoresChart, {
                    chart: {
                        type: 'pie',
                        style: {
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    title: {
                        text: null
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>S/ {point.y:,.2f}</b><br>Porcentaje: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b><br>{point.percentage:.1f}%',
                                style: {
                                    fontSize: '11px'
                                }
                            },
                            showInLegend: true,
                            colors: [
                                this.colors.primary,
                                this.colors.success,
                                this.colors.warning,
                                this.colors.danger,
                                this.colors.info
                            ]
                        }
                    },
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom',
                        itemStyle: {
                            fontSize: '11px'
                        }
                    },
                    series: [{
                        name: 'Ventas',
                        colorByPoint: true,
                        data: datosTopVendedores
                    }]
                });
            } else {
                console.error('Elemento ref topVendedoresChart no encontrado');
            }
        },

        actualizarResumenMetas(resumen) {
            const container = document.getElementById('resumenMetas');
            if (!container) return;

            const progresoTotal = resumen.progreso_total || 0;
            const metaTotal = resumen.meta_total_empresa || 0;
            const vendedoresActivos = resumen.vendedores_activos || 0;

            // Guardar el monto actual para usarlo en el modal
            this.montoMetaActual = metaTotal;

            container.innerHTML = `
                <div class="row text-center">
                    <div class="col-12 mb-3 position-relative">
                        <h3 class="text-primary">S/ ${parseFloat(metaTotal).toFixed(2)}</h3>
                        <small class="text-muted">Meta Total Empresa</small>
                        <button class="btn btn-link btn-sm position-absolute top-0 end-0 p-1" 
                                onclick="window.vueApp.abrirModalEditarMeta()" 
                                title="Editar Meta Total">
                            <i class="fas fa-edit text-primary"></i>
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-success">${vendedoresActivos}</h4>
                            <small class="text-muted">Vendedores Activos</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info">${progresoTotal.toFixed(1)}%</h4>
                        <small class="text-muted">Progreso Total</small>
                    </div>
                    <div class="col-12">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: ${progresoTotal}%;" 
                                 aria-valuenow="${progresoTotal}" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        cargarEstadisticasStock() {
            fetch(`${_URL}/ajs/dashboard/estadisticas-stock`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.estadisticasStock = data.estadisticas;
                        this.actualizarGraficoEstadoStock();
                    }
                })
                .catch(error => {
                    console.error('Error al cargar estadísticas de stock:', error);
                });
        },

        actualizarGraficoEstadoStock() {
            if (!this.estadisticasStock || !this.$refs.estadoStockChart) return;

            // Destruir gráfico existente de forma segura
            if (this.charts.estadoStock) {
                try {
                    this.charts.estadoStock.destroy();
                } catch (e) {
                    console.log('Chart already destroyed');
                }
                this.charts.estadoStock = null;
            }

            const stats = this.estadisticasStock;

            this.charts.estadoStock = Highcharts.chart(this.$refs.estadoStockChart, {
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
                            style: { fontSize: '12px', fontWeight: 'bold' }
                        },
                        showInLegend: true,
                        point: {
                            events: {
                                click: (event) => {
                                    this.mostrarProductosPorEstado(event.point.name);
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'Porcentaje',
                    colorByPoint: true,
                    data: [
                        { name: 'Óptimo', y: stats.optimo.porcentaje, color: this.colors.success },
                        { name: 'Normal', y: stats.normal.porcentaje, color: this.colors.info },
                        { name: 'Bajo', y: stats.bajo.porcentaje, color: this.colors.warning },
                        { name: 'Crítico', y: stats.critico.porcentaje, color: this.colors.danger }
                    ]
                }]
            });
        },

        abrirModalMeta() {
            // Limpiar el campo antes de abrir el modal
            const metaInput = document.getElementById('metaTotalInput');
            if (metaInput) {
                metaInput.value = '';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('metaModal'));
            modal.show();
        },

        abrirModalEditarMeta() {
            // Pre-cargar el monto actual en el campo
            this.$nextTick(() => {
                const metaInput = document.getElementById('metaTotalInput');
                if (metaInput && this.montoMetaActual) {
                    metaInput.value = this.montoMetaActual;
                }
            });
            
            const modal = new bootstrap.Modal(document.getElementById('metaModal'));
            modal.show();
        },

        guardarMetaTotal() {
            console.log('Guardando meta total...');
            Swal.fire({
                icon: 'info',
                title: 'Funcionalidad en desarrollo',
                text: 'El guardado de metas estará disponible próximamente',
                confirmButtonText: 'Entendido'
            });
        },

        // Función stub para filtrar stock
        filtrarStock() {
            console.log('Filtrando stock...');
        },

        // Funciones para clientes
        verDetalleCliente(idCliente) {
            console.log('Ver detalle cliente:', idCliente);
            const modal = new bootstrap.Modal(document.getElementById('clienteDetalleModal'));
            modal.show();

            fetch(`${_URL}/ajs/dashboard/cliente-detalle?id=${idCliente}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cliente = data.cliente;
                        document.getElementById('clienteDetalleContent').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-rojo h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-rojo"><i class="fas fa-user me-2"></i>Información Personal</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Nombre:</strong> ${cliente.datos || 'N/A'}</p>
                                            <p><strong>Documento:</strong> ${cliente.documento || 'N/A'}</p>
                                            <p><strong>Email:</strong> ${cliente.email || 'N/A'}</p>
                                            <p><strong>Teléfono:</strong> ${cliente.telefono || 'N/A'}</p>
                                            <p><strong>Dirección:</strong> ${cliente.direccion || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-rojo h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-rojo"><i class="fas fa-chart-bar me-2"></i>Estadísticas de Compra</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Compras:</strong> S/ ${parseFloat(cliente.total_compras || 0).toFixed(2)}</p>
                                            <p><strong>Número de Compras:</strong> ${cliente.num_compras || 0}</p>
                                            <p><strong>Promedio por Compra:</strong> S/ ${(parseFloat(cliente.total_compras || 0) / parseInt(cliente.num_compras || 1)).toFixed(2)}</p>
                                            <p><strong>Cliente desde:</strong> ${cliente.fecha_registro || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('clienteDetalleContent').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.message || 'No se pudo cargar la información del cliente'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('clienteDetalleContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-times me-2"></i>
                            Error al cargar los datos del cliente
                        </div>
                    `;
                });
        },

        verEstadisticasCliente(idCliente) {
            console.log('Ver estadísticas cliente:', idCliente);
            const modal = new bootstrap.Modal(document.getElementById('clienteEstadisticasModal'));
            modal.show();

            fetch(`${_URL}/ajs/dashboard/cliente-estadisticas?id=${idCliente}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.estadisticas;
                        const grafico = data.grafico;

                        document.getElementById('clienteEstadisticasContent').innerHTML = `
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card border-rojo text-center">
                                        <div class="card-body">
                                            <h4 class="text-primary">S/ ${parseFloat(stats.total_anual || 0).toFixed(2)}</h4>
                                            <small class="text-muted">Total Anual ${new Date().getFullYear()}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-rojo text-center">
                                        <div class="card-body">
                                            <h4 class="text-success">${stats.mejor_mes || 'Sin datos'}</h4>
                                            <small class="text-muted">Mejor Mes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-rojo text-center">
                                        <div class="card-body">
                                            <h4 class="text-info">S/ ${parseFloat(stats.compra_maxima || 0).toFixed(2)}</h4>
                                            <small class="text-muted">Compra Máxima</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-rojo">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-rojo"><i class="fas fa-chart-line me-2"></i>Evolución de Compras ${new Date().getFullYear()}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="clienteGraficoContainer" style="height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Crear gráfico si hay datos
                        if (grafico && grafico.meses && grafico.montos) {
                            this.$nextTick(() => {
                                Highcharts.chart('clienteGraficoContainer', {
                                    chart: { type: 'line' },
                                    title: { text: null },
                                    xAxis: { categories: grafico.meses },
                                    yAxis: { 
                                        title: { text: 'Monto (S/)' },
                                        min: 0
                                    },
                                    series: [{
                                        name: 'Compras Mensuales',
                                        data: grafico.montos.map(v => parseFloat(v)),
                                        color: this.colors.primary,
                                        marker: {
                                            enabled: true,
                                            radius: 4
                                        },
                                        lineWidth: 2
                                    }],
                                    tooltip: {
                                        formatter: function() {
                                            return '<b>' + this.x + '</b><br/>Monto: S/ ' + 
                                                   Highcharts.numberFormat(this.y, 2, '.', ',');
                                        }
                                    }
                                });
                            });
                        }
                    } else {
                        document.getElementById('clienteEstadisticasContent').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.message || 'No se pudieron cargar las estadísticas del cliente'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('clienteEstadisticasContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-times me-2"></i>
                            Error al cargar las estadísticas del cliente
                        </div>
                    `;
                });
        },

        // Función para mostrar productos por estado de stock
        mostrarProductosPorEstado(estadoNombre) {
            console.log('Mostrando productos para estado:', estadoNombre);
            Swal.fire({
                icon: 'info',
                title: `Productos en estado: ${estadoNombre}`,
                text: 'Esta funcionalidad estará disponible próximamente',
                confirmButtonText: 'Entendido'
            });
        },

        // ==================== GESTIÓN DE VENDEDORES ====================
        
        // Cambiar entre vista de empresa e individual
        cambiarTipoMeta(tipo) {
            console.log('Cambiando tipo de meta a:', tipo);
            this.tipoMetaActiva = tipo;
            
            // Recargar datos según el tipo seleccionado
            if (tipo === 'individual') {
                this.cargarVendedoresConMetasIndividuales();
            } else {
                this.cargarDatosVendedores();
            }
        },

        // Abrir modal de gestión de vendedores
        abrirModalVendedores() {
            this.cargandoVendedores = true;
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('vendedoresModal'));
            modal.show();
            
            // Cargar vendedores
            this.cargarTodosVendedores();
        },

        // Cargar todos los vendedores con sus datos de ventas
        async cargarTodosVendedores() {
            try {
                this.cargandoVendedores = true;
                
                const response = await fetch(`${_URL}/ajs/dashboard/todos-vendedores`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.todosVendedores = data.vendedores || [];
                    this.resumenVendedores = data.resumen || {};
                    this.aplicarFiltrosVendedores();
                } else {
                    console.error('Error al cargar vendedores:', data.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudieron cargar los vendedores'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            } finally {
                this.cargandoVendedores = false;
            }
        },

        // Aplicar filtros de búsqueda y rol
        aplicarFiltrosVendedores() {
            let vendedores = [...this.todosVendedores];
            
            // Filtro por rol
            if (this.filtroRolVendedor) {
                vendedores = vendedores.filter(v => v.id_rol == this.filtroRolVendedor);
            }
            
            // Filtro por búsqueda de texto
            if (this.busquedaVendedores) {
                const busqueda = this.busquedaVendedores.toLowerCase();
                vendedores = vendedores.filter(v => 
                    (v.nombres || '').toLowerCase().includes(busqueda) ||
                    (v.usuario || '').toLowerCase().includes(busqueda) ||
                    (v.tipo_usuario || '').toLowerCase().includes(busqueda)
                );
            }
            
            this.vendedoresFiltrados = vendedores;
        },

        // Filtrar vendedores por rol
        filtrarVendedoresPorRol() {
            this.aplicarFiltrosVendedores();
        },

        // Editar meta individual de un vendedor
        editarMetaIndividual(vendedor) {
            vendedor.editandoMeta = true;
            vendedor.nueva_meta_individual = vendedor.meta_individual || 0;
            this.$forceUpdate();
        },

        // Cancelar edición de meta
        cancelarEditarMeta(vendedor) {
            vendedor.editandoMeta = false;
            vendedor.nueva_meta_individual = null;
            this.$forceUpdate();
        },

        // Guardar meta individual
        async guardarMetaIndividual(vendedor) {
            try {
                const response = await fetch(`${_URL}/ajs/dashboard/guardar-meta-individual`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        usuario_id: vendedor.usuario_id,
                        meta_individual: vendedor.nueva_meta_individual,
                        mes: new Date().getMonth() + 1,
                        anio: new Date().getFullYear()
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    vendedor.meta_individual = vendedor.nueva_meta_individual;
                    vendedor.editandoMeta = false;
                    vendedor.nueva_meta_individual = null;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Meta guardada',
                        text: `Meta individual de ${vendedor.nombres} actualizada exitosamente`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    this.$forceUpdate();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo guardar la meta individual'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            }
        },

        // Ver detalle de un vendedor
        verDetalleVendedor(vendedor) {
            Swal.fire({
                title: `Detalle de ${vendedor.nombres}`,
                html: `
                    <div class="text-start">
                        <p><strong>Usuario:</strong> ${vendedor.usuario}</p>
                        <p><strong>Rol:</strong> ${vendedor.tipo_usuario}</p>
                        <p><strong>Ventas actuales:</strong> S/ ${parseFloat(vendedor.ventas_actuales || 0).toLocaleString('es-PE', {minimumFractionDigits: 2})}</p>
                        <p><strong>Meta individual:</strong> ${vendedor.meta_individual ? 'S/ ' + parseFloat(vendedor.meta_individual).toLocaleString('es-PE', {minimumFractionDigits: 2}) : 'Sin asignar'}</p>
                        ${vendedor.meta_individual ? `<p><strong>Progreso:</strong> ${((parseFloat(vendedor.ventas_actuales || 0) / parseFloat(vendedor.meta_individual)) * 100).toFixed(1)}%</p>` : ''}
                    </div>
                `,
                confirmButtonText: 'Cerrar',
                customClass: {
                    popup: 'swal-wide'
                }
            });
        },

        // Distribuir metas automáticamente
        async aplicarMetasAutomaticas() {
            const result = await Swal.fire({
                title: '¿Distribuir metas automáticamente?',
                html: 'Esta acción calculará metas individuales basadas en el rendimiento histórico de cada vendedor y la meta total de la empresa.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, distribuir',
                cancelButtonText: 'Cancelar'
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`${_URL}/ajs/dashboard/distribuir-metas-automaticas`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            mes: new Date().getMonth() + 1,
                            anio: new Date().getFullYear()
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Metas distribuidas',
                            text: `Se asignaron metas individuales a ${data.vendedores_actualizados} vendedores`,
                            timer: 3000
                        });
                        
                        // Recargar vendedores
                        this.cargarTodosVendedores();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudieron distribuir las metas'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                }
            }
        },

        // Obtener clase CSS para la barra de progreso
        obtenerClaseProgreso(vendedor) {
            if (!vendedor.meta_individual || vendedor.meta_individual <= 0) return 'bg-secondary';
            
            const progreso = (parseFloat(vendedor.ventas_actuales || 0) / parseFloat(vendedor.meta_individual)) * 100;
            
            if (progreso >= 100) return 'bg-success';
            if (progreso >= 75) return 'bg-info';
            if (progreso >= 50) return 'bg-warning';
            return 'bg-danger';
        },

        // Cargar vendedores con metas individuales
        async cargarVendedoresConMetasIndividuales() {
            // Esta función se llamará cuando se seleccione "Metas Individuales"
            await this.cargarTodosVendedores();
        },

        // ==================== GESTIÓN DE COTIZACIONES ====================
        
        // Cambiar período de cotizaciones
        async cambiarPeriodoCotizaciones(periodo) {
            this.periodoActualCotizaciones = periodo;
            this.loadingCotizaciones = true;
            
            // Definir fechas según el período
            let fechaInicio, fechaFin;
            const hoy = new Date();
            
            switch (periodo) {
                case 'hoy':
                    fechaInicio = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
                    fechaFin = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
                    this.periodoTextoCotizaciones = 'Hoy';
                    break;
                case 'mes':
                    fechaInicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                    fechaFin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
                    this.periodoTextoCotizaciones = 'Este mes';
                    break;
                case 'trimestre':
                    const mesActual = hoy.getMonth();
                    const inicioTrimestre = Math.floor(mesActual / 3) * 3;
                    fechaInicio = new Date(hoy.getFullYear(), inicioTrimestre, 1);
                    fechaFin = new Date(hoy.getFullYear(), inicioTrimestre + 3, 0);
                    this.periodoTextoCotizaciones = 'Este trimestre';
                    break;
                case 'semestre':
                    const inicioSemestre = hoy.getMonth() < 6 ? 0 : 6;
                    fechaInicio = new Date(hoy.getFullYear(), inicioSemestre, 1);
                    fechaFin = new Date(hoy.getFullYear(), inicioSemestre + 6, 0);
                    this.periodoTextoCotizaciones = 'Este semestre';
                    break;
                case 'año':
                    fechaInicio = new Date(hoy.getFullYear(), 0, 1);
                    fechaFin = new Date(hoy.getFullYear(), 11, 31);
                    this.periodoTextoCotizaciones = 'Este año';
                    break;
                case 'personalizado':
                    fechaInicio = new Date(this.filtroFechasCotizaciones.inicio);
                    fechaFin = new Date(this.filtroFechasCotizaciones.fin);
                    this.periodoTextoCotizaciones = 'Período personalizado';
                    break;
                default:
                    return;
            }

            try {
                const params = new URLSearchParams({
                    periodo: periodo,
                    fecha_inicio: fechaInicio.toISOString().split('T')[0],
                    fecha_fin: fechaFin.toISOString().split('T')[0]
                });


                const response = await fetch(`${_URL}/ajs/dashboard/datos-cotizaciones?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.datosCotizaciones = data.cotizaciones;
                    
                    // Inicializar gráficos de cotizaciones
                    this.$nextTick(() => {
                        this.inicializarGraficosCotizaciones();
                    });
                } else {
                    console.error('Error al cargar datos de cotizaciones:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loadingCotizaciones = false;
            }
        },

        // Inicializar gráficos de cotizaciones
        inicializarGraficosCotizaciones() {
            if (!this.hayDatosCotizaciones) return;
            
            // Gráfico de evolución de cotizaciones
            if (this.$refs.cotizacionesChart) {
                const meses = this.datosCotizaciones.evolucion_mensual?.meses || [];
                const cantidades = this.datosCotizaciones.evolucion_mensual?.cantidades || [];
                
                Highcharts.chart(this.$refs.cotizacionesChart, {
                    chart: { type: 'line', height: 400 },
                    title: { text: null },
                    xAxis: { categories: meses },
                    yAxis: {
                        title: { text: 'Cantidad de Cotizaciones' },
                        min: 0
                    },
                    series: [{
                        name: 'Cotizaciones Mensuales',
                        data: cantidades,
                        color: this.colors.primary,
                        marker: {
                            enabled: true,
                            radius: 4
                        },
                        lineWidth: 2
                    }],
                    tooltip: {
                        formatter: function() {
                            return '<b>' + this.x + '</b><br/>Cotizaciones: ' + this.y;
                        }
                    }
                });
            }
            
            // Gráfico de estados de cotizaciones
            if (this.$refs.estadosCotizacionesChart && this.datosCotizaciones.estados_cotizaciones) {
                const coloresPorEstado = {
                    'Pendiente': '#ffc107',
                    'Vendida': '#28a745', 
                    'Enviada': '#17a2b8',
                    'Otros': '#6c757d'
                };

                Highcharts.chart(this.$refs.estadosCotizacionesChart, {
                    chart: { type: 'pie', height: 250 },
                    title: { text: null },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y}'
                            },
                            showInLegend: false
                        }
                    },
                    series: [{
                        name: 'Cotizaciones',
                        data: this.datosCotizaciones.estados_cotizaciones.map(estado => ({
                            name: estado.estado_nombre,
                            y: parseInt(estado.cantidad),
                            color: coloresPorEstado[estado.estado_nombre] || '#6c757d'
                        }))
                    }]
                });
            }
        },

        // Ver detalle de cotización
        verDetalleCotizacion(cotizacionId) {
            Swal.fire({
                icon: 'info',
                title: 'Funcionalidad en desarrollo',
                text: 'El detalle de cotización estará disponible próximamente',
                confirmButtonText: 'Entendido'
            });
        },

        // Obtener clase de estado de cotización
        obtenerClaseEstadoCotizacion(estado) {
            switch (estado) {
                case '1': return 'bg-success';
                case '0': return 'bg-warning';
                case '2': return 'bg-info';
                default: return 'bg-secondary';
            }
        },

        // Obtener texto de estado de cotización
        obtenerTextoEstadoCotizacion(estado) {
            switch (estado) {
                case '1': return 'Vendida';
                case '0': return 'Pendiente';
                case '2': return 'Enviada';
                default: return 'Desconocido';
            }
        },

        // Obtener texto del estado de guías
        obtenerTextoEstadoGuia(estado) {
            switch (estado) {
                case '1': return 'Enviada';
                case '0': return 'Pendiente';
                case '2': return 'Activa';
                default: return 'Desconocido';
            }
        },

        // Obtener clase CSS del estado de guías
        obtenerClaseEstadoGuia(estado) {
            switch (estado) {
                case '1': return 'bg-success';
                case '0': return 'bg-warning text-dark';
                case '2': return 'bg-info';
                default: return 'bg-secondary';
            }
        },

        // Abrir modal personalizado para guías
        abrirModalPersonalizadoGuias() {
            this.filtroFechasGuias.inicio = '';
            this.filtroFechasGuias.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoGuiasModal'));
            modal.show();
        },

        // Aplicar período personalizado para guías
        aplicarPeriodoPersonalizadoGuias() {
            if (!this.filtroFechasGuias.inicio || !this.filtroFechasGuias.fin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor selecciona ambas fechas'
                });
                return;
            }

            const fechaInicio = new Date(this.filtroFechasGuias.inicio);
            const fechaFin = new Date(this.filtroFechasGuias.fin);

            if (fechaInicio > fechaFin) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fechas inválidas',
                    text: 'La fecha de inicio debe ser anterior a la fecha fin'
                });
                return;
            }

            this.periodoTextoGuias = 'Período personalizado';
            
            // Llamar a cambiarPeriodoGuias con fechas personalizadas
            this.cargarDatosGuiasPersonalizado(fechaInicio, fechaFin);
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoGuiasModal'));
            modal.hide();
        },

        // Cargar datos de guías con fechas personalizadas
        async cargarDatosGuiasPersonalizado(fechaInicio, fechaFin) {
            try {
                this.loadingGuias = true;
                
                const params = new URLSearchParams({
                    fecha_inicio: fechaInicio.toISOString().split('T')[0],
                    fecha_fin: fechaFin.toISOString().split('T')[0]
                });

                const response = await fetch(`${_URL}/ajs/dashboard/datos-guias?${params}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    this.datosGuias = data.data;
                    
                    // Inicializar gráficos de guías
                    this.$nextTick(() => {
                        this.inicializarGraficosGuias();
                    });
                } else {
                    console.error('Error al cargar datos de guías:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loadingGuias = false;
            }
        },

        // Abrir modal personalizado para cotizaciones
        abrirModalPersonalizadoCotizaciones() {
            this.filtroFechasCotizaciones.inicio = '';
            this.filtroFechasCotizaciones.fin = '';
            const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoCotizacionesModal'));
            modal.show();
        },

        // Aplicar período personalizado para cotizaciones
        aplicarPeriodoPersonalizadoCotizaciones() {
            if (!this.filtroFechasCotizaciones.inicio || !this.filtroFechasCotizaciones.fin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor selecciona ambas fechas'
                });
                return;
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoCotizacionesModal'));
            modal.hide();

            // Aplicar filtro
            this.cambiarPeriodoCotizaciones('personalizado');
        },

        // ==================== GESTIÓN DE GUÍAS ====================
        
        // Cambiar período de guías
        async cambiarPeriodoGuias(periodo) {
            this.periodoActualGuias = periodo;
            this.loadingGuias = true;
            
            // Definir fechas según el período
            let fechaInicio, fechaFin;
            const hoy = new Date();
            
            switch (periodo) {
                case 'hoy':
                    fechaInicio = new Date(hoy);
                    fechaFin = new Date(hoy);
                    this.periodoTextoGuias = 'Hoy';
                    break;
                case 'mes':
                    fechaInicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                    fechaFin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
                    this.periodoTextoGuias = 'Este mes';
                    break;
                case 'trimestre':
                    const mesActual = hoy.getMonth();
                    const inicioTrimestre = Math.floor(mesActual / 3) * 3;
                    fechaInicio = new Date(hoy.getFullYear(), inicioTrimestre, 1);
                    fechaFin = new Date(hoy.getFullYear(), inicioTrimestre + 3, 0);
                    this.periodoTextoGuias = 'Este trimestre';
                    break;
                case 'semestre':
                    const inicioSemestre = hoy.getMonth() < 6 ? 0 : 6;
                    fechaInicio = new Date(hoy.getFullYear(), inicioSemestre, 1);
                    fechaFin = new Date(hoy.getFullYear(), inicioSemestre + 6, 0);
                    this.periodoTextoGuias = 'Este semestre';
                    break;
                case 'año':
                    fechaInicio = new Date(hoy.getFullYear(), 0, 1);
                    fechaFin = new Date(hoy.getFullYear(), 11, 31);
                    this.periodoTextoGuias = 'Este año';
                    break;
                case 'personalizado':
                    fechaInicio = new Date(this.filtroFechasGuias.inicio);
                    fechaFin = new Date(this.filtroFechasGuias.fin);
                    this.periodoTextoGuias = 'Período personalizado';
                    break;
                default:
                    return;
            }

            try {
                const params = new URLSearchParams({
                    fecha_inicio: fechaInicio.toISOString().split('T')[0],
                    fecha_fin: fechaFin.toISOString().split('T')[0]
                });

                const response = await fetch(`${_URL}/ajs/dashboard/datos-guias?${params}`);
                const data = await response.json();
                
                console.log('Respuesta datos guías:', data); // Debug
                
                if (data.success && data.data) {
                    this.datosGuias = data.data;
                    this.guiasFiltradas = data.data.guias_recientes || [];
                    
                    console.log('Datos guías cargados:', this.datosGuias); // Debug
                    
                    // Inicializar gráficos de guías
                    this.$nextTick(() => {
                        this.inicializarGraficosGuias();
                    });
                } else {
                    console.error('Error al cargar datos de guías:', data);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loadingGuias = false;
            }
        },

        // Inicializar gráficos de guías
        inicializarGraficosGuias() {
            if (!this.hayDatosGuias) return;
            
            // Gráfico de evolución de guías
            if (this.$refs.guiasChart && this.datosGuias.evolucion_mensual) {
                const meses = this.datosGuias.evolucion_mensual.meses || [];
                const cantidades = this.datosGuias.evolucion_mensual.cantidades || [];
                
                Highcharts.chart(this.$refs.guiasChart, {
                    chart: { type: 'column', height: 400 },
                    title: { text: null },
                    xAxis: { categories: meses },
                    yAxis: { title: { text: 'Cantidad de Guías' } },
                    series: [{
                        name: 'Guías Emitidas',
                        data: cantidades,
                        color: this.colors.info
                    }]
                });
            }
            
            // Gráfico de estados de guías
            if (this.$refs.estadosGuiasChart && this.datosGuias.estados_guias) {
                Highcharts.chart(this.$refs.estadosGuiasChart, {
                    chart: { type: 'pie', height: 250 },
                    title: { text: null },
                    series: [{
                        name: 'Guías',
                        data: this.datosGuias.estados_guias.map((estado, index) => ({
                            name: estado.estado === '1' ? 'Enviada' : 'Pendiente',
                            y: parseInt(estado.cantidad),
                            color: estado.estado === '1' ? this.colors.success : this.colors.warning
                        }))
                    }]
                });
            }
        },

        // Ver detalle de guía
        verDetalleGuia(guiaId) {
            Swal.fire({
                icon: 'info',
                title: 'Funcionalidad en desarrollo',
                text: 'El detalle de guía estará disponible próximamente',
                confirmButtonText: 'Entendido'
            });
        },

        // Descargar PDF de guía
        descargarGuiaPDF(guiaId) {
            window.open(`${_URL}/guia/remision/pdf/${guiaId}`, '_blank');
        },

        // Obtener texto del motivo de traslado
        obtenerTextoMotivoTraslado(motivo) {
            switch (motivo) {
                case '1': return 'Venta';
                case '2': return 'Traslado';
                case '3': return 'Recojo';
                case '4': return 'Devolución';
                case '6': return 'Ventas';
                default: return 'Otro';
            }
        },

        // Obtener color para estados
        obtenerColorEstado(estado) {
            switch (estado) {
                case '1': return this.colors.success;
                case '0': return this.colors.danger;
                case '2': return this.colors.warning;
                default: return this.colors.secondary;
            }
        },

        // Formatear fecha
        formatearFecha(fecha) {
            if (!fecha) return 'N/A';
            return new Date(fecha).toLocaleDateString('es-PE');
        },
    },
    
    watch: {
        // Filtrar vendedores automáticamente cuando cambie la búsqueda
        busquedaVendedores() {
            this.aplicarFiltrosVendedores();
        }
    },

    mounted() {
        console.log('Dashboard Vue App montado');
        
        // Detectar pestaña activa desde URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabFromUrl = urlParams.get('tab');
        if (tabFromUrl) {
            this.activeTab = tabFromUrl;
        }

        // Generar años disponibles para reportes
        const currentYear = new Date().getFullYear();
        for (let year = currentYear; year >= currentYear - 5; year--) {
            this.aniosDisponibles.push(year);
        }

        // Inicializar gráficos después de montar el componente
        this.$nextTick(() => {
            this.inicializarGraficos();
        });
    }
});