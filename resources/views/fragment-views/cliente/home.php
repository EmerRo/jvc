<?php
/**
 * Dashboard Home Refactorizado
 * Archivo principal que incluye todos los componentes modulares
 */

// Activar reporte de errores para debuggear
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Rutas de los archivos
$home_logic_path = __DIR__ . '/home-components/home-logic.php';
$home_functions_path = __DIR__ . '/home-components/home-functions.php';

echo "<!-- Iniciando carga del dashboard -->\n";

// Incluir la lógica PHP principal
if (file_exists($home_logic_path)) {
    try {
        include_once $home_logic_path;
        echo "<!-- Archivo home-logic.php incluido -->\n";
    } catch (Exception $e) {
        echo "<!-- Error con home-logic.php, usando versión simplificada: " . $e->getMessage() . " -->\n";
        include_once __DIR__ . '/home-components/home-logic-simple.php';
    }
} else {
    echo "<!-- home-logic.php no encontrado, usando versión simplificada -->\n";
    include_once __DIR__ . '/home-components/home-logic-simple.php';
}

// Intentar incluir las funciones
if (file_exists($home_functions_path)) {
    try {
        include_once $home_functions_path;
        echo "<!-- Archivo home-functions.php incluido -->\n";
    } catch (Exception $e) {
        echo "<!-- Error con home-functions.php: " . $e->getMessage() . " -->\n";
        include_once __DIR__ . '/home-components/home-functions-simple.php';
    }
} else {
    echo "<!-- home-functions.php no encontrado, usando versión simplificada -->\n";
    include_once __DIR__ . '/home-components/home-functions-simple.php';
}

// Verificar que las variables necesarias estén definidas
$data = $data ?? [];
$dataListVen = $dataListVen ?? [];
$dataUtilidadBruta = $dataUtilidadBruta ?? [];
$categorias_grafico = $categorias_grafico ?? ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
$productos_nombres = $productos_nombres ?? [];
$productos_cantidades = $productos_cantidades ?? [];
$textos_periodo = $textos_periodo ?? ['titulo_principal' => 'Dashboard'];
$periodo_actual = $periodo_actual ?? 'mes';
$fecha_inicio = $fecha_inicio ?? date('Y-m-01');
$fecha_fin = $fecha_fin ?? date('Y-m-t');
$empresa = $empresa ?? '1';
$sucursal = $sucursal ?? '1';
$conexion = $conexion ?? null;

echo "<!-- Variables verificadas y definidas -->\n";

// Preparar datos para JavaScript
$dashboardData = [
    'totalVentas' => floatval($data['totalv'] ?? 0),
    'totalFacturas' => floatval($data['totalvF'] ?? 0), 
    'totalBoletas' => floatval($data['totalvB'] ?? 0),
    'totalMesAnterior' => floatval($data['totalvMA'] ?? 0),
    'utilidadBrutaActual' => floatval($data['utilidad_bruta_actual'] ?? 0),
    'utilidadBrutaAnterior' => floatval($data['utilidad_bruta_anterior'] ?? 0),
    'ventasAnuales' => $dataListVen,
    'utilidadBrutaPorPeriodo' => $dataUtilidadBruta,
    'ingresosPorPeriodo' => generarDatosIngresos($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion),
    'egresosPorPeriodo' => generarDatosEgresos($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $conexion),
    'categoriasGrafico' => $categorias_grafico,
    'productosNombres' => $productos_nombres,
    'productosCantidades' => $productos_cantidades,
    'clientesNombres' => [],
    'clientesCompras' => [],
    'ingresosMensuales' => floatval($data['totalv'] ?? 0),
    'egresosMensuales' => floatval($data['totalv'] ?? 0) * 0.7,
    'gananciaMensual' => floatval($data['totalv'] ?? 0) * 0.3,
    'textosPeriodo' => $textos_periodo,
    'periodoActual' => $periodo_actual,
    // Datos para productos por categoría
    'categorias' => $categorias ?? [],
    'productosPorCategoria' => isset($productos_por_categoria) ? array_column($productos_por_categoria, 'productos') : [],
    'productosPorCategoriaAnterior' => [], 
    // Datos adicionales para productos
    'totalProductosVendidos' => array_sum($productos_cantidades),
    'totalCategoriasActivas' => isset($categorias) ? count($categorias) : 0,
    'rotacionPromedio' => 0
];

echo "<!-- Dashboard data preparado: " . count($dashboardData) . " elementos -->\n";
?>

<!-- Dashboard funcionando correctamente -->

<div id="app">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="page-title">Dashboard</h6>
                            <ol class="breadcrumb m-10">
                                <li class="breadcrumb-item active">Bienvenido <strong>JVC</strong> al Sistema de Facturación
                                    Electrónica <strong>JVC</strong></li>
                            </ol>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn border-rojo" @click.prevent="abrirModalReporte()">
                                <i class="fas fa-file-download me-1"></i> Descargar Reporte
                            </button>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <!-- Tabs de navegación -->
                <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'ventas' }" @click="setActiveTab('ventas')"
                            id="ventas-tab" type="button">
                            <i class="fas fa-chart-line"></i> Ventas
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'productos' }"
                            @click="setActiveTab('productos')" id="productos-tab" type="button">
                            <i class="fas fa-box"></i> Productos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'stock' }"
                            @click="setActiveTab('stock')" id="stock-tab" type="button">
                            <i class="fas fa-cubes"></i> Stock
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'ingresos-egresos' }"
                            @click="setActiveTab('ingresos-egresos')" id="ingresos-egresos-tab" type="button">
                            <i class="fas fa-money-bill-wave"></i> Ingresos y Egresos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'clientes' }"
                            @click="setActiveTab('clientes')" id="clientes-tab" type="button">
                            <i class="fas fa-users"></i> Clientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'cotizaciones' }"
                            @click="setActiveTab('cotizaciones')" id="cotizaciones-tab" type="button">
                            <i class="fas fa-file-invoice"></i> Cotizaciones
                        </button>
                    </li>
                    <!-- <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'guias' }"
                            @click="setActiveTab('guias')" id="guias-tab" type="button">
                            <i class="fas fa-truck"></i> Guías
                        </button>
                    </li> -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" :class="{ active: activeTab === 'metas-ventas' }"
                            @click="setActiveTab('metas-ventas')" id="metas-ventas-tab" type="button">
                            <i class="fas fa-target"></i> Metas de Ventas
                        </button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content mt-3" id="dashboardTabsContent">
                    <?php include __DIR__ . '/home-components/partials/ventas-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/productos-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/stock-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/ingresos-egresos-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/clientes-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/cotizaciones-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/guias-tab.php'; ?>
                    <?php include __DIR__ . '/home-components/partials/metas-ventas-tab.php'; ?>
                </div>
            </div>
        </div>

        <!-- Incluir todos los modales -->
        <?php include __DIR__ . '/home-components/partials/modales.php'; ?>
    </div>
</div>

<!-- Scripts necesarios -->
<!-- Librerías externas -->
    <script src="<?= URL::to('public/js/highcharts/highcharts.js') ?>?v=<?= time() ?>"></script>


<script>
    // Pasar datos del servidor a JavaScript
    window.dashboardData = <?= json_encode($dashboardData) ?>;
    window.periodoActual = <?= json_encode($periodo_actual) ?>;
    window.periodoTexto = <?= json_encode($textos_periodo['titulo_principal'] ?? 'Este mes') ?>;
    
    // Debug: Verificar si Highcharts se carga correctamente
    console.log('Highcharts disponible:', typeof Highcharts !== 'undefined');
    console.log('Dashboard data:', window.dashboardData);
    console.log('_URL ya definida:', typeof _URL !== 'undefined' ? _URL : 'NO DEFINIDA');
</script>

<!-- JavaScript modular -->
<script src="<?= URL::to('resources/views/fragment-views/cliente/home-components/assets/js/dashboard-app.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('resources/views/fragment-views/cliente/home-components/assets/js/charts-ventas.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('resources/views/fragment-views/cliente/home-components/assets/js/charts-productos.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('resources/views/fragment-views/cliente/home-components/assets/js/charts-stock.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('resources/views/fragment-views/cliente/home-components/assets/js/charts-ingresos.js') ?>?v=<?= time() ?>"></script>
<script src="<?= URL::to('resources/views/fragment-views/cliente/home-components/assets/js/charts-clientes.js') ?>?v=<?= time() ?>"></script>

<script>
    // Solo funciones stub para asegurar que todos los métodos existan
    if (!Vue.prototype.cargarEstadisticasStock) {
        Vue.prototype.cargarEstadisticasStock = function() {
            // La función real está definida en dashboard-app.js
            console.log('cargarEstadisticasStock: Función stub - la real se ejecuta en el componente Vue');
        };
    }

    if (!Vue.prototype.inicializarGraficosVentas) {
        Vue.prototype.inicializarGraficosVentas = function() {
            console.log('inicializarGraficosVentas: Función stub ejecutada');
        };
    }

    if (!Vue.prototype.inicializarGraficosProductos) {
        Vue.prototype.inicializarGraficosProductos = function() {
            console.log('inicializarGraficosProductos: Función stub ejecutada');
        };
    }

    if (!Vue.prototype.inicializarGraficosStock) {
        Vue.prototype.inicializarGraficosStock = function() {
            console.log('inicializarGraficosStock: Función stub ejecutada');
        };
    }

    if (!Vue.prototype.inicializarGraficosIngresos) {
        Vue.prototype.inicializarGraficosIngresos = function() {
            console.log('inicializarGraficosIngresos: Función stub ejecutada');
        };
    }

    if (!Vue.prototype.inicializarGraficosClientes) {
        Vue.prototype.inicializarGraficosClientes = function() {
            console.log('inicializarGraficosClientes: Función stub ejecutada');
        };
    }
</script>

<style>
/* Estilos específicos para el dashboard */
.chart-container {
    min-height: 300px;
    position: relative;
}

/* Fix para evitar espacios en blanco en pestañas */
.tab-pane {
    min-height: auto;
    opacity: 1;
    transform: none;
}

.tab-pane.fade:not(.show) {
    opacity: 0;
    height: 0;
    overflow: hidden;
}

.tab-pane.fade.show {
    opacity: 1;
    height: auto;
    overflow: visible;
}

.chart-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}

.no-data-message {
    text-align: center;
    color: #6c757d;
    padding: 50px 20px;
}

.no-data-message i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.mini-stat-img {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
}

.stock-alert {
    animation: pulse 2s infinite;
}

/* Fix para Alertas de Stock */
.list-group-item {
    border: 1px solid #dee2e6;
    background-color: #fff;
}

.list-group-item:hover {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    color: #212529 !important;
}

.list-group-item:hover strong,
.list-group-item:hover .text-muted {
    color: inherit !important;
}

.list-group-item .badge {
    font-size: 0.75rem;
    font-weight: 600;
}

@keyframes pulse {
    0% { background-color: transparent; }
    50% { background-color: rgba(220, 53, 69, 0.1); }
    100% { background-color: transparent; }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
    opacity: 1;
    transform: translate3d(0, 0, 0);
}

.slide-in-left {
    animation: slideInLeft 0.8s ease-out;
    opacity: 1;
    transform: translate3d(0, 0, 0);
}

.slide-in-right {
    animation: slideInRight 0.8s ease-out;
    opacity: 1;
    transform: translate3d(0, 0, 0);
}

.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 40px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translate3d(-40px, 0, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translate3d(40px, 0, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #dc3545;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>