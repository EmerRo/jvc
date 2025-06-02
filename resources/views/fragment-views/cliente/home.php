<!-- resources\views\fragment-views\cliente\home.php -->
<?php
$empresa = $_SESSION['id_empresa'];

// Configuración de fechas para comparativas
$anio1 = date("Y");
$mes1 = date("m");
$anio2 = '';
$mes2 = '';
if ($mes1 == 1) {
    $mes2 = '12';
    $anio2 = $anio1 - 1;
} else {
    $anio2 = $anio1;
    $mes2 = $mes1 - 1;
}

// Conexión a la base de datos
$conexion = (new Conexion())->getConexion();

// Consulta principal para datos del dashboard
$sql = "SELECT 
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' AND estado = '1' and sucursal='{$_SESSION['sucursal']}' AND YEAR(fecha_emision)='$anio1' AND MONTH(fecha_emision)='$mes1') totalv,
  (SELECT COUNT(*) FROM clientes WHERE id_empresa = '$empresa') cnt_cli,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' and id_tido =2 AND estado = '1' AND YEAR(fecha_emision)='$anio1' AND MONTH(fecha_emision)='$mes1') totalvF,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' and id_tido =1 AND estado = '1' AND YEAR(fecha_emision)='$anio1' AND MONTH(fecha_emision)='$mes1') totalvB,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' AND estado = '1' AND YEAR(fecha_emision)='$anio2' AND MONTH(fecha_emision)='$mes2') totalvMA,
  (SELECT productos.detalle FROM `productos_ventas` inner join productos on productos_ventas.id_producto = productos.id_producto GROUP BY productos.id_producto ORDER BY SUM(productos_ventas.cantidad) DESC limit 1) prodVen,
  (SELECT SUM(cantidad) FROM productos_ventas GROUP BY id_producto ORDER BY SUM(cantidad) DESC limit 1) prodVenCan";

$data = $conexion->query($sql)->fetch_assoc();

// Datos para gráfico de ventas anuales
$dataListVen = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

$sql = "SELECT 
  MONTH(fecha_emision) mes,
  SUM(total) total
FROM
  ventas 
WHERE id_empresa = '$empresa' 
  AND estado = '1' 
  and sucursal='{$_SESSION['sucursal']}'
  AND YEAR(fecha_emision) = '$anio1'
  GROUP BY mes";
$resultList = $conexion->query($sql);

foreach ($resultList as $dtTemp) {
    $tempValue = 0;
    if (doubleval($dtTemp['total']) > 0) {
        $tempValue = doubleval($dtTemp['total']);
    }
    $dataListVen[intval($dtTemp['mes']) - 1] = $tempValue;
}

// Productos más vendidos
$sql_productos = "SELECT 
  p.id_producto,
  p.codigo,
  COALESCE(p.nombre, p.detalle) as nombre,
  p.detalle,
  SUM(pv.cantidad) as total_vendido,
  SUM(pv.precio * pv.cantidad) as total_ventas
FROM 
  productos_ventas pv
JOIN 
  productos p ON pv.id_producto = p.id_producto
JOIN 
  ventas v ON pv.id_venta = v.id_venta
WHERE 
  v.id_empresa = '$empresa' 
  AND v.estado = '1'
  AND v.sucursal = '{$_SESSION['sucursal']}'
GROUP BY 
  p.id_producto
HAVING 
  total_vendido > 0
ORDER BY 
  total_vendido DESC
LIMIT 5";

$productos_top = $conexion->query($sql_productos);
$productos_nombres = [];
$productos_cantidades = [];

if ($productos_top && $productos_top->num_rows > 0) {
    while ($producto = $productos_top->fetch_assoc()) {
        // Limpiar caracteres de tabulación y espacios extra
        $productos_nombres[] = trim(str_replace(["\t", "\n", "\r"], '', $producto['nombre']));
        $productos_cantidades[] = intval($producto['total_vendido']); // Convertir a entero
    }
    // Reiniciar el puntero del resultado para usarlo después
    $productos_top->data_seek(0);
}
// Productos con stock bajo
$sql_stock_bajo = "SELECT 
  id_producto, 
  codigo,
  COALESCE(nombre, detalle) as nombre, 
  cantidad, 
  precio 
FROM 
  productos 
WHERE 
  id_empresa = '$empresa' 
  AND cantidad <= 10 
  AND estado = '1'
ORDER BY 
  cantidad ASC
LIMIT 5";

$productos_stock_bajo = $conexion->query($sql_stock_bajo);

// Clientes top
$sql_clientes = "SELECT 
  c.id_cliente,
  c.datos,
  COUNT(v.id_venta) as num_compras,
  SUM(v.total) as total_compras
FROM 
  ventas v
JOIN 
  clientes c ON v.id_cliente = c.id_cliente
WHERE 
  v.id_empresa = '$empresa' 
  AND v.estado = '1'
GROUP BY 
  c.id_cliente
ORDER BY 
  total_compras DESC
LIMIT 5";

$clientes_top = $conexion->query($sql_clientes);
$clientes_nombres = [];
$clientes_compras = [];

if ($clientes_top && $clientes_top->num_rows > 0) {
    while ($cliente = $clientes_top->fetch_assoc()) {
        // Limpiar caracteres de tabulación y espacios extra
        $clientes_nombres[] = trim(str_replace(["\t", "\n", "\r"], '', $cliente['datos']));
        $clientes_compras[] = floatval($cliente['total_compras']); // Convertir a float para montos
    }
    // Reiniciar el puntero del resultado para usarlo después
    $clientes_top->data_seek(0);
}

// Ingresos y Egresos (simulados para este ejemplo)
$ingresos_mensuales = $data["totalv"] ?? 0;
$egresos_mensuales = $ingresos_mensuales * 0.6; // Simulado: 60% de los ingresos
$ganancia_mensual = $ingresos_mensuales - $egresos_mensuales;

// Datos para gráfico de ventas por período (día, quincena, mes, etc.)
$periodos = ['Diario', 'Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Trimestral', 'Semestral', 'Anual'];
$ventasPorPeriodo = [];

// Simulamos datos para cada período (en una implementación real, esto vendría de la BD)
$ventasPorPeriodo = [
    number_format($data["totalv"] / 30 ?? 0, 2, ".", ""), // Diario (aproximado)
    number_format($data["totalv"] / 4 ?? 0, 2, ".", ""),  // Semanal (aproximado)
    number_format($data["totalv"] / 2 ?? 0, 2, ".", ""),  // Quincenal (aproximado)
    number_format($data["totalv"] ?? 0, 2, ".", ""),      // Mensual
    number_format($data["totalv"] * 2 ?? 0, 2, ".", ""),  // Bimestral (aproximado)
    number_format($data["totalv"] * 3 ?? 0, 2, ".", ""),  // Trimestral (aproximado)
    number_format($data["totalv"] * 6 ?? 0, 2, ".", ""),  // Semestral (aproximado)
    number_format($data["totalv"] * 12 ?? 0, 2, ".", "") // Anual (aproximado)
];

// Convertir datos PHP a JSON para usar en Vue
$dashboardData = [
    'ventasAnuales' => $dataListVen,
    'periodos' => $periodos,
    'ventasPorPeriodo' => array_map('floatval', $ventasPorPeriodo),
    'productosNombres' => $productos_nombres,
    'productosCantidades' => $productos_cantidades,
    'clientesNombres' => $clientes_nombres,
    'clientesCompras' => $clientes_compras,
    'totalVentas' => $data["totalv"] ?? 0,
    'totalFacturas' => $data["totalvF"] ?? 0,
    'totalBoletas' => $data["totalvB"] ?? 0,
    'totalMesAnterior' => $data["totalvMA"] ?? 0,
    'ingresosMensuales' => $ingresos_mensuales,
    'egresosMensuales' => $egresos_mensuales,
    'gananciaMensual' => $ganancia_mensual
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JVC</title>
    <script src="<?= URL::to('public/js/highcharts/highcharts.js') ?>?v=<?= time() ?>"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= URL::to('public/css/home.css') ?>?v=<?= time() ?>">

</head>

<body>
    <div id="app">
        <div class="dashboard-container">
            <!-- start page title -->
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
                        <div class="btn-group me-2">
                            <button type="button" class="btn bg-rojo text-white dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-calendar-alt me-1"></i> {{ periodoTexto }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('hoy')">Hoy</a>
                                </li>
                                <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('semana')">Esta
                                        semana</a></li>
                                <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('mes')">Este
                                        mes</a></li>
                                <li><a class="dropdown-item" href="#" @click.prevent="cambiarPeriodo('anio')">Este
                                        año</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#"
                                        @click.prevent="abrirModalPersonalizado()">Personalizado</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn border-rojo" @click.prevent="abrirModalReporte()">
                            <i class="fas fa-file-download me-1"></i> Descargar Reporte
                        </button>
                    </div>
                    <div class="modal fade" id="periodoPersonalizadoModal" tabindex="-1"
                        aria-labelledby="periodoPersonalizadoModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content border-rojo">
                                <div class="modal-header bg-rojo text-white">
                                    <h5 class="modal-title" id="periodoPersonalizadoModalLabel">
                                        <i class="fas fa-calendar-alt me-2"></i>Seleccionar Período Personalizado
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formPeriodo">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                                                    <input type="date" class="form-control" id="fechaInicio"
                                                        v-model="filtroFechas.inicio" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="fechaFin" class="form-label">Fecha Fin</label>
                                                    <input type="date" class="form-control" id="fechaFin"
                                                        v-model="filtroFechas.fin" required>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </button>
                                    <button type="button" class="btn btn-primary" @click="aplicarPeriodoPersonalizado">
                                        <i class="fas fa-filter me-1"></i>Aplicar Filtro
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                 <!-- Modal Descargar Reporte -->
<div class="modal fade" id="descargarReporteModal" tabindex="-1"
    aria-labelledby="descargarReporteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="descargarReporteModalLabel">
                    <i class="fas fa-file-download me-2"></i>Descargar Reporte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReporte">
                    <div class="mb-3">
                        <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                        <select class="form-select" id="tipoReporte" v-model="reporteSeleccionado" required>
                            <option value="">Seleccione un tipo de reporte</option>
                            <option value="ventas">Reporte de Ventas</option>
                            <option value="productos">Reporte de Productos</option>
                            <option value="stock">Reporte de Stock</option>
                            <option value="clientes">Reporte de Clientes</option>
                            <option value="metas">Reporte de Metas de Ventas</option>
                            <option value="completo">Reporte Completo</option>
                        </select>
                    </div>
                    
                    <!-- Selector de período -->
                    <div class="mb-3">
                        <label for="tipoPeriodo" class="form-label">Período del Reporte</label>
                        <select class="form-select" id="tipoPeriodo" v-model="tipoPeriodoReporte" @change="cambiarTipoPeriodo" required>
                            <option value="rango">Rango de Fechas</option>
                            <option value="anual">Por Año</option>
                        </select>
                    </div>
                    
                    <!-- Selector de fechas (mostrar solo si es rango) -->
                    <div v-show="tipoPeriodoReporte === 'rango'" class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="reporteFechaInicio"
                                    v-model="filtroFechas.inicio" :required="tipoPeriodoReporte === 'rango'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="reporteFechaFin"
                                    v-model="filtroFechas.fin" :required="tipoPeriodoReporte === 'rango'">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selector de año (mostrar solo si es anual) -->
                    <div v-show="tipoPeriodoReporte === 'anual'" class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteAnio" class="form-label">Año</label>
                                <select class="form-select" id="reporteAnio" v-model="anioSeleccionado" :required="tipoPeriodoReporte === 'anual'">
                                    <option value="">Seleccione un año</option>
                                    <option v-for="anio in aniosDisponibles" :key="anio" :value="anio">{{ anio }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteMes" class="form-label">Mes (Opcional)</label>
                                <select class="form-select" id="reporteMes" v-model="mesSeleccionado">
                                    <option value="">Todo el año</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información adicional para reportes anuales -->
                    <div v-show="tipoPeriodoReporte === 'anual'" class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Reporte Anual:</strong> 
                        <span v-if="mesSeleccionado">
                            Se generará el reporte para {{ obtenerNombreMes(mesSeleccionado) }} de {{ anioSeleccionado }}
                        </span>
                        <span v-else>
                            Se generará el reporte para todo el año {{ anioSeleccionado }}
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn bg-rojo text-white" @click="descargarReporte">
                    <i class="fas fa-download me-1"></i>Descargar
                </button>
            </div>
        </div>
    </div>
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
                    <button class="nav-link" :class="{ active: activeTab === 'stock' }" @click="setActiveTab('stock')"
                        id="stock-tab" type="button">
                        <i class="fas fa-warehouse"></i> Stock
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ active: activeTab === 'ingresos-egresos' }"
                        @click="setActiveTab('ingresos-egresos')" id="ingresos-egresos-tab" type="button">
                        <i class="fas fa-money-bill-wave"></i> Ingresos/Egresos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ active: activeTab === 'clientes' }"
                        @click="setActiveTab('clientes')" id="clientes-tab" type="button">
                        <i class="fas fa-users"></i> Clientes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ active: activeTab === 'metas-ventas' }"
                        @click="setActiveTab('metas-ventas')" id="metas-ventas-tab" type="button">
                        <i class="fas fa-target"></i> Metas de Ventas
                    </button>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="dashboardTabsContent">
                <!-- Tab de Ventas -->
                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'ventas' }" id="ventas"
                    role="tabpanel">
                    <!-- Tarjetas de resumen -->
                    <div class="row fade-in-up">
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-white text-dark">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div
                                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                                            <img class="mt-3 mr-5"
                                                src="<?= URL::to('public/assets/images/services-icon/01.png') ?>">
                                        </div>
                                        <h5 class="text-uppercase fw-light text-end">Monto Vendido</h5>
                                        <h1 class="fw-bolder text-end counter-value">S/ {{
                                            formatNumber(dashboardData.totalVentas) }}</h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">Facturas y Boletas</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-white text-dark">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div
                                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                                            <img class="mt-3 mr-5"
                                                src="<?= URL::to('public/assets/images/services-icon/03.png') ?>">
                                        </div>
                                        <h5 class="fw-light text-uppercase text-end">Total en Facturas</h5>
                                        <h1 class="fw-bolder text-end counter-value">S/ {{
                                            formatNumber(dashboardData.totalFacturas) }}</h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">
                                            {{ calcularPorcentajeFacturas }}% del total
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-white text-dark">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div
                                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                                            <img class="mt-3 mr-5"
                                                src="<?= URL::to('public/assets/images/services-icon/04.png') ?>">
                                        </div>
                                        <h5 class="fw-light text-uppercase text-end">Total en Boletas</h5>
                                        <h1 class="fw-bolder text-end counter-value">S/ {{
                                            formatNumber(dashboardData.totalBoletas) }}</h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">
                                            {{ calcularPorcentajeBoletas }}% del total
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-white text-dark">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div
                                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                                            <img class="mt-3 mr-5"
                                                src="<?= URL::to('public/assets/images/services-icon/02.png') ?>">
                                        </div>
                                        <h5 class="fw-light text-uppercase text-end">Comparativa</h5>
                                        <h1 class="fw-bolder text-end counter-value" v-html="comparativaMesAnterior">
                                        </h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">vs. Mes Anterior</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos de ventas -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card slide-in-left">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Ventas Anuales</h4>
                                    <div class="chart-container">
                                        <div id="ventasAnualesChartLoading" class="chart-loading" v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="ventasAnualesChart" class="chart-container">
                                            <div v-if="!hayDatosVentasAnuales" class="no-data-message">
                                                <i class="fas fa-chart-line"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card slide-in-right">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Comparativa Períodos</h4>
                                    <div class="chart-container">
                                        <div id="comparativaChartLoading" class="chart-loading" v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="comparativaChart" class="chart-container">
                                            <div v-if="!hayDatosComparativa" class="no-data-message">
                                                <i class="fas fa-chart-bar"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparativa con años anteriores -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card fade-in-up">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Comparativa con Años Anteriores</h4>
                                    <div class="chart-container">
                                        <div id="comparativaAnualChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="comparativaAnualChart" class="chart-container">
                                            <div v-if="!hayDatosVentasAnuales" class="no-data-message">
                                                <i class="fas fa-chart-line"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab de Productos -->
                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'productos' }" id="productos"
                    role="tabpanel">
                    <div class="row fade-in-up">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Productos Más Vendidos</h4>
                                    <div class="chart-container">
                                        <div id="productosTopChartLoading" class="chart-loading" v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="productosTopChart" class="chart-container">
                                            <div v-if="!hayDatosProductos" class="no-data-message">
                                                <i class="fas fa-box"></i>
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
                                    <h4 class="card-title mb-4">Distribución de Ventas por Producto</h4>
                                    <div class="chart-container">
                                        <div id="distribucionProductosChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="distribucionProductosChart" class="chart-container">
                                            <div v-if="!hayDatosProductos" class="no-data-message">
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
                                    <h4 class="card-title mb-4">Detalle de Productos Más Vendidos</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Codigo</th>
                                                    <th>Producto</th>
                                                    <th>Descripción</th>
                                                    <th>Unidades Vendidas</th>
                                                    <th>Total Ventas</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($productos_top && $productos_top->num_rows > 0): ?>
                                                    <?php while ($producto = $productos_top->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= $producto['codigo'] ?></td>
                                                            <td><?= $producto['nombre'] ?></td>
                                                            <td><?= $producto['detalle'] ?? 'Sin descripción' ?></td>
                                                            <td><?= $producto['total_vendido'] ?></td>
                                                            <td>S/ <?= number_format($producto['total_ventas'], 2, ".", ",") ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary"
                                                                    @click="verDetalleProducto(<?= $producto['id_producto'] ?>)"
                                                                    title="Ver detalles">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-info"
                                                                    @click="verEstadisticasProducto(<?= $producto['id_producto'] ?>)"
                                                                    title="Ver estadísticas">
                                                                    <i class="fas fa-chart-line"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No hay datos disponibles</td>
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

                <!-- Tab de Stock -->
                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'stock' }" id="stock" role="tabpanel">
                    <div class="row fade-in-up">
                        <div class="col-xl-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Rotación de Inventario</h4>
                                    <div class="chart-container">
                                        <div id="rotacionInventarioChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="rotacionInventarioChart" class="chart-container">
                                            <div v-if="!hayDatosStock" class="no-data-message">
                                                <i class="fas fa-boxes"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Estado del Stock</h4>
                                    <div class="chart-container">
                                        <div id="estadoStockChartLoading" class="chart-loading" v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="estadoStockChart" class="chart-container">
                                            <div v-if="!hayDatosStock" class="no-data-message">
                                                <i class="fas fa-chart-pie"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Alertas de Stock</h4>
                                    <div class="alert alert-danger animate-pulse">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>¡Atención!</strong> Hay productos con stock crítico.
                                    </div>

                                    <div class="overflow-auto" style="max-height: 300px;">
                                        <ul class="list-group">
                                            <?php if ($productos_stock_bajo && $productos_stock_bajo->num_rows > 0): ?>
                                                <?php while ($producto = $productos_stock_bajo->fetch_assoc()): ?>
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center <?= $producto['cantidad'] <= 5 ? 'stock-alert' : '' ?>">
                                                        <div>
                                                            <strong><?= $producto['nombre'] ?></strong>
                                                            <div class="text-muted small">Código:
                                                                <?= $producto['codigo'] ?? 'Sin código' ?>
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="badge <?= $producto['cantidad'] <= 5 ? 'badge-danger' : 'badge-warning' ?>">
                                                            <?= $producto['cantidad'] ?> unidades
                                                        </span>
                                                    </li>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <li class="list-group-item">No hay productos con stock bajo</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card slide-in-left">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Movimientos de Inventario</h4>
                                    <div class="chart-container">
                                        <div id="movimientosInventarioChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="movimientosInventarioChart" class="chart-container">
                                            <div v-if="!hayDatosStock" class="no-data-message">
                                                <i class="fas fa-exchange-alt"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab de Ingresos y Egresos -->
                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'ingresos-egresos' }"
                    id="ingresos-egresos" role="tabpanel">
                    <div class="row fade-in-up">
                        <div class="col-xl-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Ingresos Mensuales</h4>
                                    <h2 class="counter-value text-success">S/ {{
                                        formatNumber(dashboardData.ingresosMensuales) }}</h2>
                                    <div class="progress mt-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="text-muted mt-3">
                                        <i class="fas fa-arrow-up text-success me-1"></i>
                                        {{ calcularPorcentajeIngresos }}% vs. mes anterior
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Egresos Mensuales</h4>
                                    <h2 class="counter-value text-danger">S/ {{
                                        formatNumber(dashboardData.egresosMensuales)
                                        }}</h2>
                                    <div class="progress mt-3">
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            :style="{ width: porcentajeEgresos + '%' }"
                                            :aria-valuenow="porcentajeEgresos" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p class="text-muted mt-3">
                                        <i class="fas fa-arrow-down text-danger me-1"></i>
                                        {{ porcentajeEgresos }}% de los ingresos
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Ganancia Neta</h4>
                                    <h2 class="counter-value text-primary">S/ {{
                                        formatNumber(dashboardData.gananciaMensual)
                                        }}</h2>
                                    <div class="progress mt-3">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            :style="{ width: porcentajeGanancia + '%' }"
                                            :aria-valuenow="porcentajeGanancia" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p class="text-muted mt-3">
                                        <i class="fas fa-check-circle text-primary me-1"></i>
                                        Margen de ganancia: {{ porcentajeGanancia }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card slide-in-left">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Evolución de Ingresos y Egresos</h4>
                                    <div class="chart-container">
                                        <div id="ingresosEgresosChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="ingresosEgresosChart" class="chart-container">
                                            <div v-if="!hayDatosIngresos" class="no-data-message">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card slide-in-right">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Distribución de Egresos</h4>
                                    <div class="chart-container">
                                        <div id="distribucionEgresosChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="distribucionEgresosChart" class="chart-container">
                                            <div v-if="!hayDatosIngresos" class="no-data-message">
                                                <i class="fas fa-chart-pie"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab de Clientes -->
                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'clientes' }" id="clientes"
                    role="tabpanel">
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
                                        <div id="frecuenciaCompraChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
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
                <!-- Tab de Metas de Ventas -->
                <div class="tab-pane fade" :class="{ 'show active': activeTab === 'metas-ventas' }" id="metas-ventas"
                    role="tabpanel">
                    <div class="row fade-in-up">
                        <!-- Columna principal con gráfico de vendedores -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h4 class="card-title mb-0">Competencia de Vendedores - <?= date('F Y') ?></h4>
                                        <button class="btn bg-rojo text-white" @click="abrirModalMeta" id="botonMeta">
                                            <i class="fas fa-target me-1"></i>{{ textoBotonMeta }}
                                        </button>
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
            </div>
            <!-- Modal Establecer Meta CORREGIDO -->
            <div class="modal fade" id="metaModal" tabindex="-1" aria-labelledby="metaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-rojo">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="metaModalLabel">
                                <i class="fas fa-target me-2"></i>Establecer Meta Total de Ventas
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formMeta">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mesSelect" class="form-label">Mes</label>
                                            <select class="form-select" id="mesSelect" required>
                                                <option value="1">Enero</option>
                                                <option value="2">Febrero</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Abril</option>
                                                <option value="5">Mayo</option>
                                                <option value="6">Junio</option>
                                                <option value="7">Julio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="anioInput" class="form-label">Año</label>
                                            <input type="number" class="form-control" id="anioInput"
                                                value="<?= date('Y') ?>" min="2020" max="2030" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- META TOTAL -->
                                <div class="mb-4">
                                    <label for="metaTotalInput" class="form-label">
                                        <i class="fas fa-bullseye text-rojo me-2"></i>
                                        <strong>Meta Total de Ventas (S/)</strong>
                                    </label>
                                    <input type="number" class="form-control form-control-lg" id="metaTotalInput"
                                        step="0.01" min="0" placeholder="Ej: 50000.00" required>
                                    <div class="form-text">Esta meta se distribuirá automáticamente entre todos los
                                        vendedores activos</div>
                                </div>

                                <!-- DISTRIBUCIÓN AUTOMÁTICA -->
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Distribución Automática:</strong> El sistema asignará metas individuales a
                                    cada vendedor
                                    basándose en su rendimiento histórico y la meta total establecida.
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" @click="guardarMetaTotal">
                                <i class="fas fa-save me-1"></i>Establecer Meta Total
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Cliente Detalle -->
            <div class="modal fade" id="clienteDetalleModal" tabindex="-1" aria-labelledby="clienteDetalleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-rojo">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="clienteDetalleModalLabel">
                                <i class="fas fa-user-circle me-2"></i>Detalle del Cliente
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="clienteDetalleContent">
                                <div class="text-center">
                                    <div class="spinner-border text-rojo" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Cargando información del cliente...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Cliente Estadísticas -->
            <div class="modal fade" id="clienteEstadisticasModal" tabindex="-1"
                aria-labelledby="clienteEstadisticasModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content border-rojo">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="clienteEstadisticasModalLabel">
                                <i class="fas fa-chart-line me-2"></i>Estadísticas del Cliente
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="clienteEstadisticasContent">
                                <div class="text-center">
                                    <div class="spinner-border text-rojo" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Cargando estadísticas del cliente...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Producto Detalle -->
            <div class="modal fade" id="productoDetalleModal" tabindex="-1" aria-labelledby="productoDetalleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-rojo">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="productoDetalleModalLabel">
                                <i class="fas fa-box me-2"></i>Detalle del Producto
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="productoDetalleContent">
                                <div class="text-center">
                                    <div class="spinner-border text-rojo" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Cargando información del producto...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Producto Estadísticas -->
            <div class="modal fade" id="productoEstadisticasModal" tabindex="-1"
                aria-labelledby="productoEstadisticasModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content border-rojo">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="productoEstadisticasModalLabel">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas del Producto
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="productoEstadisticasContent">
                                <div class="text-center">
                                    <div class="spinner-border text-rojo" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Cargando estadísticas del producto...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Crear la aplicación Vue
        new Vue({
            el: '#app',
            data: {
                // hayDatosVendedores: false,
                vendedores: [],
                metasData: {},
                activeTab: 'ventas',
                loadingCharts: true,
                dashboardData: <?= json_encode($dashboardData) ?>,
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
                periodoActual: 'mes',
                periodoTexto: 'Este mes',
                filtroFechas: {
                    inicio: '',
                    fin: ''
                },
                reporteSeleccionado: '',
                tipoPeriodoReporte: 'rango',
    anioSeleccionado: '',
    mesSeleccionado: '',
    aniosDisponibles: [],
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
                    return this.dashboardData.ventasAnuales && this.dashboardData.ventasAnuales.some(valor => valor > 0);
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
                    // Simulamos que hay datos de stock si hay datos de productos
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
                }
            },
            methods: {
                setActiveTab(tab) {
                    this.activeTab = tab;
                    // Dar tiempo para que el DOM se actualice
                    this.$nextTick(() => {
                        // Inicializar o actualizar los gráficos de la pestaña activa
                        this.inicializarGraficosPorTab(tab);

                        // Hacer scroll al inicio de la pestaña
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    });
                },
                formatNumber(value) {
                    return new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
                },
                inicializarGraficos() {
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
                inicializarGraficosVentas() {
                    // Gráfico de Ventas Anuales
                    if (this.$refs.ventasAnualesChart && this.hayDatosVentasAnuales) {
                        this.charts.ventasAnuales = Highcharts.chart(this.$refs.ventasAnualesChart, {
                            chart: {
                                type: 'area',
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
                                categories: this.meses,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
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
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                },
                                gridLineDashStyle: 'Dash'
                            },
                            tooltip: {
                                formatter: function () {
                                    return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                                }
                            },
                            plotOptions: {
                                area: {
                                    fillOpacity: 0.3,
                                    marker: {
                                        radius: 4,
                                        lineWidth: 2,
                                        lineColor: '#ffffff'
                                    }
                                }
                            },
                            series: [{
                                name: 'Ventas',
                                color: this.colors.primary,
                                data: this.dashboardData.ventasAnuales
                            }]
                        });
                    }

                    // Gráfico de Comparativa de Períodos
                    if (this.$refs.comparativaChart && this.hayDatosComparativa) {
                        this.charts.comparativa = Highcharts.chart(this.$refs.comparativaChart, {
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
                                categories: this.dashboardData.periodos,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
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
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                },
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
                                    ]
                                }
                            },
                            series: [{
                                name: 'Ventas',
                                data: this.dashboardData.ventasPorPeriodo,
                                showInLegend: false
                            }]
                        });
                    }

                    // Gráfico de Comparativa Anual (simulado)
                    if (this.$refs.comparativaAnualChart && this.hayDatosVentasAnuales) {
                        const añoActual = new Date().getFullYear();
                        const años = [añoActual - 2, añoActual - 1, añoActual];

                        // Generamos datos simulados para años anteriores basados en ventasAnuales
                        const datosAñoAnterior = this.dashboardData.ventasAnuales.map(valor => valor * 0.8);
                        const datosAñoAnteAnterior = this.dashboardData.ventasAnuales.map(valor => valor * 0.6);

                        this.charts.comparativaAnual = Highcharts.chart(this.$refs.comparativaAnualChart, {
                            chart: {
                                type: 'line',
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
                                categories: this.meses,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
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
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
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
                },
                inicializarGraficosProductos() {
                    console.log('Productos nombres:', this.dashboardData.productosNombres);
                    console.log('Productos cantidades:', this.dashboardData.productosCantidades);
                    // Gráfico de Productos Top
                    if (this.$refs.productosTopChart && this.hayDatosProductos) {
                        this.charts.productosTop = Highcharts.chart(this.$refs.productosTopChart, {
                            chart: {
                                type: 'bar',
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
                                categories: this.dashboardData.productosNombres,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: null
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
                                    return '<b>' + this.x + '</b><br>' + this.y + ' unidades';
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 5,
                                    colorByPoint: true,
                                    colors: [
                                        this.colors.primary, this.colors.secondary, this.colors.success,
                                        this.colors.warning, this.colors.danger
                                    ]
                                }
                            },
                            series: [{
                                name: 'Unidades Vendidas',
                                data: this.dashboardData.productosCantidades,
                                showInLegend: false
                            }]
                        });
                    }

                    // Gráfico de Distribución de Productos 
                    if (this.$refs.distribucionProductosChart && this.hayDatosProductos) {
                        // Crear datos para el gráfico de distribución basados en los productos reales
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
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: false
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
                },
                inicializarGraficosStock() {
                    // Solo inicializar gráficos si hay datos
                    if (!this.hayDatosStock) return;

                    // Gráfico de Rotación de Inventario
                    if (this.$refs.rotacionInventarioChart) {
                        // Usar los nombres de productos reales si están disponibles
                        const productosRotacion = this.dashboardData.productosNombres;
                        // Generar datos simulados de rotación basados en los productos reales
                        const rotacionDias = productosRotacion.map(() => Math.floor(Math.random() * 30) + 5);

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
                                categories: productosRotacion,
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
                                    borderRadius: 5,
                                    colorByPoint: true,
                                    colors: [
                                        this.colors.primary, this.colors.secondary, this.colors.success,
                                        this.colors.warning, this.colors.danger
                                    ]
                                }
                            },
                            series: [{
                                name: 'Días promedio',
                                data: rotacionDias,
                                showInLegend: false
                            }]
                        });
                    }

                    // Gráfico de Estado del Stock
                    if (this.$refs.estadoStockChart) {
                        const estadosStock = ['Óptimo', 'Normal', 'Bajo', 'Crítico'];
                        const cantidadesStock = [45, 30, 15, 10];

                        this.charts.estadoStock = Highcharts.chart(this.$refs.estadoStockChart, {
                            chart: {
                                type: 'pie',
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
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: false
                                    },
                                    showInLegend: true
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
                                data: [
                                    { name: estadosStock[0], y: cantidadesStock[0], color: this.colors.success },
                                    { name: estadosStock[1], y: cantidadesStock[1], color: this.colors.info },
                                    { name: estadosStock[2], y: cantidadesStock[2], color: this.colors.warning },
                                    { name: estadosStock[3], y: cantidadesStock[3], color: this.colors.danger }
                                ]
                            }]
                        });
                    }

                    // Gráfico de Movimientos de Inventario
                    if (this.$refs.movimientosInventarioChart) {
                        const mesesMovimientos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
                        const entradas = [120, 150, 180, 130, 160, 190];
                        const salidas = [100, 130, 160, 120, 140, 170];

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
                                categories: mesesMovimientos,
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
                    }
                },
                inicializarGraficosIngresos() {
                    // Solo inicializar gráficos si hay datos
                    if (!this.hayDatosIngresos) return;

                    // Gráfico de Ingresos y Egresos
                    if (this.$refs.ingresosEgresosChart) {
                        const mesesFinanzas = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
                        // Generar datos simulados basados en los ingresos mensuales reales
                        const ingresoBase = this.dashboardData.ingresosMensuales / 6;
                        const ingresos = mesesFinanzas.map((_, i) => ingresoBase * (0.8 + (i * 0.1)));
                        const egresos = ingresos.map(ingreso => ingreso * 0.6);
                        const ganancias = ingresos.map((ingreso, index) => ingreso - egresos[index]);

                        this.charts.ingresosEgresos = Highcharts.chart(this.$refs.ingresosEgresosChart, {
                            chart: {
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
                                categories: mesesFinanzas,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
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
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
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
                                column: {
                                    borderRadius: 5
                                }
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

                    // Gráfico de Distribución de Egresos
                    if (this.$refs.distribucionEgresosChart) {
                        const categoriasEgresos = ['Compras', 'Salarios', 'Servicios', 'Impuestos', 'Otros'];
                        const porcentajesEgresos = [45, 25, 15, 10, 5];

                        this.charts.distribucionEgresos = Highcharts.chart(this.$refs.distribucionEgresosChart, {
                            chart: {
                                type: 'pie',
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
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: false
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
                                data: categoriasEgresos.map((cat, index) => ({
                                    name: cat,
                                    y: porcentajesEgresos[index],
                                    color: [this.colors.primary, this.colors.secondary, this.colors.success, this.colors.warning, this.colors.danger][index]
                                }))
                            }]
                        });
                    }
                },
                inicializarGraficosClientes() {
                    // Solo inicializar gráficos si hay datos
                    if (!this.hayDatosClientes) return;

                    // Gráfico de Clientes Top
                    if (this.$refs.clientesTopChart) {
                        this.charts.clientesTop = Highcharts.chart(this.$refs.clientesTopChart, {
                            chart: {
                                type: 'bar',
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
                                categories: this.dashboardData.clientesNombres,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
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
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                },
                                gridLineDashStyle: 'Dash'
                            },
                            tooltip: {
                                formatter: function () {
                                    return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 5,
                                    colorByPoint: true,
                                    colors: [
                                        this.colors.primary, this.colors.secondary, this.colors.success,
                                        this.colors.warning, this.colors.danger
                                    ]
                                }
                            },
                            series: [{
                                name: 'Total Compras (S/)',
                                data: this.dashboardData.clientesCompras,
                                showInLegend: false
                            }]
                        });
                    }

                    // Gráfico de Frecuencia de Compra
                    if (this.$refs.frecuenciaCompraChart) {
                        const frecuencias = ['Semanal', 'Quincenal', 'Mensual', 'Trimestral', 'Semestral', 'Anual'];
                        const cantidadClientes = [5, 12, 25, 18, 10, 8];

                        this.charts.frecuenciaCompra = Highcharts.chart(this.$refs.frecuenciaCompraChart, {
                            chart: {
                                type: 'pie',
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
                            tooltip: {
                                formatter: function () {
                                    const total = cantidadClientes.reduce((a, b) => a + b, 0);
                                    const percentage = (this.y / total * 100).toFixed(1);
                                    return '<b>' + this.point.name + '</b><br>' +
                                        this.y + ' clientes (' + percentage + '%)';
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: false
                                    },
                                    showInLegend: true
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
                                name: 'Clientes',
                                colorByPoint: true,
                                data: frecuencias.map((freq, index) => ({
                                    name: freq,
                                    y: cantidadClientes[index],
                                    color: [this.colors.primary, this.colors.secondary, this.colors.success,
                                    this.colors.warning, this.colors.danger, this.colors.info][index]
                                }))
                            }]
                        });
                    }
                },
                verDetalleCliente(id) {
                    const modal = new bootstrap.Modal(document.getElementById('clienteDetalleModal'));
                    modal.show();

                    fetch(`${_URL}/ajs/dashboard/cliente-detalle?id=${id}`)
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
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Datos:</label>
                                        <p class="mb-1">${cliente.datos || 'N/A'}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Documento:</label>
                                        <p class="mb-1">${cliente.documento || 'N/A'}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Dirección:</label>
                                        <p class="mb-1">${cliente.direccion || 'N/A'}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Teléfono:</label>
                                        <p class="mb-1">${cliente.telefono || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-rojo h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-rojo"><i class="fas fa-chart-line me-2"></i>Estadísticas de Compras</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border-end">
                                                <h4 class="text-rojo mb-1">${cliente.num_compras}</h4>
                                                <small class="text-muted">Total Compras</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h4 class="text-rojo mb-1">S/ ${parseFloat(cliente.total_compras).toFixed(2)}</h4>
                                            <small class="text-muted">Total Gastado</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h5 class="text-rojo mb-1">S/ ${parseFloat(cliente.promedio_compra).toFixed(2)}</h5>
                                                <small class="text-muted">Promedio por Compra</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-rojo mb-1">${cliente.ultima_venta ? new Date(cliente.ultima_venta).toLocaleDateString() : 'N/A'}</h6>
                                            <small class="text-muted">Última Compra</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                            } else {
                                document.getElementById('clienteDetalleContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('clienteDetalleContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos del cliente
                </div>
            `;
                        });
                },

                verEstadisticasCliente(id) {
                    const modal = new bootstrap.Modal(document.getElementById('clienteEstadisticasModal'));
                    modal.show();

                    fetch(`${_URL}/ajs/dashboard/cliente-estadisticas?id=${id}`)
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
                                    <h3 class="text-rojo">S/ ${parseFloat(stats.total_anual).toFixed(2)}</h3>
                                    <p class="text-muted mb-0">Total Anual ${new Date().getFullYear()}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-rojo text-center">
                                <div class="card-body">
                                    <h3 class="text-rojo">${stats.mejor_mes}</h3>
                                    <p class="text-muted mb-0">Mejor Mes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-rojo text-center">
                                <div class="card-body">
                                    <h3 class="text-rojo">S/ ${parseFloat(stats.compra_maxima).toFixed(2)}</h3>
                                    <p class="text-muted mb-0">Compra Máxima</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-rojo">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-rojo"><i class="fas fa-chart-line me-2"></i>Compras por Mes - ${new Date().getFullYear()}</h6>
                        </div>
                        <div class="card-body">
                            <div id="clienteChart" style="height: 300px;"></div>
                        </div>
                    </div>
                `;

                                // Crear gráfico con Highcharts
                                setTimeout(() => {
                                    Highcharts.chart('clienteChart', {
                                        chart: {
                                            type: 'line',
                                            style: {
                                                fontFamily: 'Poppins, sans-serif'
                                            }
                                        },
                                        title: {
                                            text: null
                                        },
                                        xAxis: {
                                            categories: grafico.meses,
                                            labels: {
                                                style: {
                                                    color: '#6c757d',
                                                    fontSize: '12px'
                                                }
                                            }
                                        },
                                        yAxis: {
                                            title: {
                                                text: 'Monto (S/)',
                                                style: {
                                                    color: '#6c757d'
                                                }
                                            },
                                            labels: {
                                                formatter: function () {
                                                    return 'S/ ' + Highcharts.numberFormat(this.value, 2);
                                                },
                                                style: {
                                                    color: '#6c757d'
                                                }
                                            }
                                        },
                                        tooltip: {
                                            formatter: function () {
                                                return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
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
                                        series: [{
                                            name: 'Compras',
                                            color: '#dc3545',
                                            data: grafico.montos,
                                            showInLegend: false
                                        }],
                                        credits: {
                                            enabled: false
                                        }
                                    });
                                }, 100);
                            } else {
                                document.getElementById('clienteEstadisticasContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('clienteEstadisticasContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error al cargar las estadísticas del cliente
                </div>
            `;
                        });
                },
                // Funciones para modales de productos
                verDetalleProducto(id) {
                    const modal = new bootstrap.Modal(document.getElementById('productoDetalleModal'));
                    modal.show();

                    fetch(`${_URL}/ajs/dashboard/producto-detalle?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const producto = data.producto;
                                document.getElementById('productoDetalleContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-rojo h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-rojo"><i class="fas fa-box me-2"></i>Información del Producto</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Código:</label>
                                        <p class="mb-1">${producto.codigo || 'N/A'}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Nombre:</label>
                                        <p class="mb-1">${producto.nombre || 'N/A'}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Precio:</label>
                                        <p class="mb-1">S/ ${parseFloat(producto.precio || 0).toFixed(2)}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-rojo">Stock:</label>
                                        <p class="mb-1">${producto.stock || 0} unidades</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-rojo h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-rojo"><i class="fas fa-chart-bar me-2"></i>Estadísticas de Ventas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border-end">
                                                <h4 class="text-rojo mb-1">${producto.total_vendido}</h4>
                                                <small class="text-muted">Unidades Vendidas</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h4 class="text-rojo mb-1">S/ ${parseFloat(producto.total_ventas).toFixed(2)}</h4>
                                            <small class="text-muted">Total en Ventas</small>
                                        </div>
                                        <div class="col-12">
                                            <h5 class="text-rojo mb-1">${parseFloat(producto.promedio_mensual).toFixed(1)}</h5>
                                            <small class="text-muted">Promedio Mensual</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                            } else {
                                document.getElementById('productoDetalleContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('productoDetalleContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error al cargar los datos del producto
                </div>
            `;
                        });
                },

                verEstadisticasProducto(id) {
                    const modal = new bootstrap.Modal(document.getElementById('productoEstadisticasModal'));
                    modal.show();

                    fetch(`${_URL}/ajs/dashboard/producto-estadisticas?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const stats = data.estadisticas;
                                const grafico = data.grafico;

                                document.getElementById('productoEstadisticasContent').innerHTML = `
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-rojo text-center">
                                <div class="card-body">
                                    <h3 class="text-rojo">${stats.total_anual}</h3>
                                    <p class="text-muted mb-0">Total Vendido en ${new Date().getFullYear()}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-rojo">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-rojo"><i class="fas fa-chart-bar me-2"></i>Ventas por Mes - ${new Date().getFullYear()}</h6>
                        </div>
                        <div class="card-body">
                            <div id="productoChart" style="height: 300px;"></div>
                        </div>
                    </div>
                `;

                                // Crear gráfico con Highcharts
                                setTimeout(() => {
                                    Highcharts.chart('productoChart', {
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
                                            categories: grafico.meses,
                                            labels: {
                                                style: {
                                                    color: '#6c757d',
                                                    fontSize: '12px'
                                                }
                                            }
                                        },
                                        yAxis: {
                                            title: {
                                                text: 'Cantidad',
                                                style: {
                                                    color: '#6c757d'
                                                }
                                            },
                                            labels: {
                                                style: {
                                                    color: '#6c757d'
                                                }
                                            }
                                        },
                                        tooltip: {
                                            formatter: function () {
                                                return '<b>' + this.x + '</b><br>' + this.y + ' unidades';
                                            }
                                        },
                                        plotOptions: {
                                            column: {
                                                borderRadius: 5,
                                                color: '#dc3545'
                                            }
                                        },
                                        series: [{
                                            name: 'Cantidad Vendida',
                                            data: grafico.cantidades,
                                            showInLegend: false
                                        }],
                                        credits: {
                                            enabled: false
                                        }
                                    });
                                }, 100);
                            } else {
                                document.getElementById('productoEstadisticasContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('productoEstadisticasContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error al cargar las estadísticas del producto
                </div>
            `;
                        });
                },

                guardarMetaTotal() {
                    const form = document.getElementById('formMeta');
                    const formData = new FormData();

                    formData.append('mes', document.getElementById('mesSelect').value);
                    formData.append('anio', document.getElementById('anioInput').value);
                    formData.append('meta_total', document.getElementById('metaTotalInput').value);

                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    fetch(`${_URL}/ajs/dashboard/guardar-meta-total`, {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('metaModal')).hide();
                                this.cargarDatosVendedores();

                                // Mostrar resumen de distribución
                                alert(`Meta total establecida: S/ ${data.meta_total}\n` +
                                    `Distribuida entre ${data.total_vendedores} vendedores\n` +
                                    `Meta promedio por vendedor: S/ ${data.meta_promedio}`);
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al guardar la meta total');
                        });
                },

                abrirModalMeta() {
                    // Solo establecer mes actual
                    document.getElementById('mesSelect').value = new Date().getMonth() + 1;
                    document.getElementById('metaTotalInput').value = '';

                    const modal = new bootstrap.Modal(document.getElementById('metaModal'));
                    modal.show();
                },

                guardarMeta() {
                    const form = document.getElementById('formMeta');
                    const formData = new FormData();

                    formData.append('id_vendedor', document.getElementById('vendedorSelect').value);
                    formData.append('mes', document.getElementById('mesSelect').value);
                    formData.append('anio', document.getElementById('anioInput').value);
                    formData.append('meta_mensual', document.getElementById('metaInput').value);

                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    fetch(`${_URL}/ajs/dashboard/guardar-meta`, {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('metaModal')).hide();
                                this.cargarDatosVendedores();
                                alert('Meta guardada exitosamente');
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al guardar la meta');
                        });
                },

                actualizarTablaVendedores(vendedores) {
                    const tbody = document.getElementById('tablaVendedores');
                    let html = '';

                    // Guardar vendedores en la instancia de Vue
                    this.vendedores = vendedores;

                    vendedores.forEach((vendedor, index) => {
                        const progreso = vendedor.porcentaje_contribucion || 0;
                        const metaEmpresa = vendedor.meta_total_empresa || 0;

                        // Estados basados en contribución
                        let estado, estadoClass;
                        if (progreso >= 20) {
                            estado = 'Top Contributor';
                            estadoClass = 'success';
                        } else if (progreso >= 10) {
                            estado = 'Buen Progreso';
                            estadoClass = 'warning';
                        } else if (progreso > 0) {
                            estado = 'En Progreso';
                            estadoClass = 'info';
                        } else {
                            estado = 'Sin Ventas';
                            estadoClass = 'secondary';
                        }

                        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-2">
                            <div class="avatar-title bg-light text-primary rounded-circle">
                                ${vendedor.nombres.charAt(0)}
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">${vendedor.nombres} ${vendedor.apellidos || ''}</h6>
                            <small class="text-muted">Posición #${vendedor.posicion}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <strong>${progreso.toFixed(1)}%</strong> de S/ ${parseFloat(metaEmpresa).toFixed(2)}
                    <br><small class="text-muted">Meta Total Empresa</small>
                </td>
                <td>S/ ${parseFloat(vendedor.ventas_actuales || 0).toFixed(2)}</td>
                <td>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-${estadoClass}" role="progressbar" 
                             style="width: ${Math.min(progreso * 5, 100)}%" 
                             aria-valuenow="${progreso}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted"><strong>${progreso.toFixed(1)}%</strong> de contribución</small>
                </td>
                <td>
                    <span class="badge badge-${estadoClass}">${estado}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-info" title="Ver detalle de progreso">
                        <i class="fas fa-chart-line"></i>
                    </button>
                </td>
            </tr>
        `;
                    });

                    tbody.innerHTML = html;

                    // Actualizar el gráfico con datos reales
                    this.inicializarGraficosVendedores();
                },
                actualizarResumenMetas(resumen) {
                    const container = document.getElementById('resumenMetas');
                    const progresoTotal = resumen.progreso_total || 0;
                    const metaTotal = resumen.meta_total_empresa || 0;

                    container.innerHTML = `
        <div class="row text-center">
            <div class="col-12 mb-3">
                <h3 class="text-primary">S/ ${parseFloat(metaTotal).toFixed(2)}</h3>
                <small class="text-muted">Meta Total Empresa</small>
            </div>
            <div class="col-6 mb-3">
                <div class="border-end">
                    <h4 class="text-success">${resumen.vendedores_activos}</h4>
                    <small class="text-muted">Vendedores Activos</small>
                </div>
            </div>
            <div class="col-6 mb-3">
                <h4 class="text-warning">${resumen.dias_restantes}</h4>
                <small class="text-muted">Días Restantes</small>
            </div>
            <div class="col-12">
                <div class="progress mb-2" style="height: 15px;">
                    <div class="progress-bar ${progresoTotal >= 100 ? 'bg-success' : 'bg-primary'}" 
                         style="width: ${Math.min(progresoTotal, 100)}%">
                    </div>
                </div>
                <h5 class="text-info">${progresoTotal.toFixed(1)}% Completado</h5>
                <small class="text-muted">S/ ${parseFloat(resumen.total_ventas_mes).toFixed(2)} de S/ ${parseFloat(metaTotal).toFixed(2)}</small>
            </div>
        </div>
    `;
                },

                inicializarGraficosVendedores() {
                    if (this.$refs.vendedoresChart && this.vendedores && this.vendedores.length > 0) {
                        const vendedoresNombres = this.vendedores.map(v => v.nombres);
                        const ventasVendedores = this.vendedores.map(v => parseFloat(v.ventas_actuales || 0));
                        const contribuciones = this.vendedores.map(v => parseFloat(v.porcentaje_contribucion || 0));

                        this.charts.vendedores = Highcharts.chart(this.$refs.vendedoresChart, {
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
                                text: 'Contribución de Vendedores a la Meta Total',
                                style: {
                                    fontSize: '16px',
                                    fontWeight: 'bold'
                                }
                            },
                            xAxis: {
                                categories: vendedoresNombres,
                                labels: {
                                    style: {
                                        color: '#6c757d',
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yAxis: [{
                                title: {
                                    text: 'Ventas (S/)',
                                    style: { color: '#6c757d' }
                                },
                                labels: {
                                    formatter: function () {
                                        return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                                    },
                                    style: { color: '#6c757d' }
                                }
                            }, {
                                title: {
                                    text: 'Contribución (%)',
                                    style: { color: '#dc3545' }
                                },
                                labels: {
                                    formatter: function () {
                                        return this.value + '%';
                                    },
                                    style: { color: '#dc3545' }
                                },
                                opposite: true
                            }],
                            tooltip: {
                                shared: true,
                                formatter: function () {
                                    let s = '<b>' + this.x + '</b><br/>';
                                    this.points.forEach(function (point) {
                                        if (point.series.name === 'Ventas') {
                                            s += '<span style="color:' + point.series.color + '">\u25CF</span> ' +
                                                point.series.name + ': S/ ' + Highcharts.numberFormat(point.y, 2) + '<br/>';
                                        } else {
                                            s += '<span style="color:' + point.series.color + '">\u25CF</span> ' +
                                                point.series.name + ': ' + point.y.toFixed(1) + '%<br/>';
                                        }
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
                                            return 'S/ ' + Highcharts.numberFormat(this.y, 0);
                                        },
                                        style: {
                                            fontSize: '10px'
                                        }
                                    }
                                },
                                spline: {
                                    marker: {
                                        radius: 4,
                                        lineWidth: 2,
                                        lineColor: '#ffffff'
                                    },
                                    dataLabels: {
                                        enabled: true,
                                        formatter: function () {
                                            return this.y.toFixed(1) + '%';
                                        },
                                        style: {
                                            fontSize: '10px'
                                        }
                                    }
                                }
                            },
                            series: [{
                                name: 'Ventas',
                                type: 'column',
                                data: ventasVendedores,
                                color: this.colors.primary,
                                yAxis: 0
                            }, {
                                name: 'Contribución',
                                type: 'spline',
                                data: contribuciones,
                                color: this.colors.danger,
                                yAxis: 1
                            }],
                            credits: { enabled: false }
                        });
                    }
                },
                cargarDatosVendedores() {
                    fetch(`${_URL}/ajs/dashboard/vendedores-metas`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.metasData = data;
                                this.vendedores = data.vendedores;
                                this.actualizarResumenMetas(data.resumen);
                                this.inicializarGraficosVendedores();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                },
                cambiarPeriodo(periodo) {
                    this.periodoActual = periodo;
                    const ahora = new Date();
                    let fechaInicio = new Date();
                    let fechaFin = new Date();

                    switch (periodo) {
                        case 'hoy':
                            this.periodoTexto = 'Hoy';
                            break;
                        case 'semana':
                            this.periodoTexto = 'Esta semana';
                            const diaSemana = ahora.getDay() || 7;
                            fechaInicio.setDate(ahora.getDate() - diaSemana + 1);
                            break;
                        case 'mes':
                            this.periodoTexto = 'Este mes';
                            fechaInicio = new Date(ahora.getFullYear(), ahora.getMonth(), 1);
                            fechaFin = new Date(ahora.getFullYear(), ahora.getMonth() + 1, 0);
                            break;
                        case 'anio':
                            this.periodoTexto = 'Este año';
                            fechaInicio = new Date(ahora.getFullYear(), 0, 1);
                            fechaFin = new Date(ahora.getFullYear(), 11, 31);
                            break;
                    }

                    this.filtroFechas.inicio = this.formatearFecha(fechaInicio);
                    this.filtroFechas.fin = this.formatearFecha(fechaFin);
                    this.cargarDatosConFiltro();
                },

                formatearFecha(fecha) {
                    const year = fecha.getFullYear();
                    const month = String(fecha.getMonth() + 1).padStart(2, '0');
                    const day = String(fecha.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                abrirModalPersonalizado() {
                    if (!this.filtroFechas.inicio || !this.filtroFechas.fin) {
                        const ahora = new Date();
                        this.filtroFechas.inicio = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth(), 1));
                        this.filtroFechas.fin = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth() + 1, 0));
                    }
                    const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoModal'));
                    modal.show();
                },

                aplicarPeriodoPersonalizado() {
                    if (!this.filtroFechas.inicio || !this.filtroFechas.fin) {
                        alert('Por favor seleccione fechas válidas');
                        return;
                    }

                    if (new Date(this.filtroFechas.inicio) > new Date(this.filtroFechas.fin)) {
                        alert('La fecha de inicio no puede ser mayor que la fecha fin');
                        return;
                    }

                    const fInicio = new Date(this.filtroFechas.inicio);
                    const fFin = new Date(this.filtroFechas.fin);
                    this.periodoTexto = `${fInicio.getDate()}/${fInicio.getMonth() + 1} - ${fFin.getDate()}/${fFin.getMonth() + 1}`;

                    bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoModal')).hide();
                    this.cargarDatosConFiltro();
                },

                cargarDatosConFiltro() {
                    this.loadingCharts = true;
                    const params = new URLSearchParams({
                        fecha_inicio: this.filtroFechas.inicio,
                        fecha_fin: this.filtroFechas.fin,
                        periodo: this.periodoActual
                    });

                    fetch(`${_URL}/ajs/dashboard/datos?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.dashboardData = data.dashboardData;
                                this.$nextTick(() => {
                                    this.inicializarGraficos();
                                });
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error))
                        .finally(() => this.loadingCharts = false);
                },
              abrirModalReporte() {
        // Generar años disponibles (últimos 5 años + año actual + próximo año)
        const anioActual = new Date().getFullYear();
        this.aniosDisponibles = [];
        for (let i = anioActual - 5; i <= anioActual + 1; i++) {
            this.aniosDisponibles.push(i);
        }
        this.aniosDisponibles.reverse(); // Mostrar años más recientes primero
        
        // Establecer año actual por defecto
        this.anioSeleccionado = anioActual;
        
        // Usar las mismas fechas que el filtro actual
        const modal = new bootstrap.Modal(document.getElementById('descargarReporteModal'));
        modal.show();
    },
    
    cambiarTipoPeriodo() {
        // Limpiar selecciones cuando cambia el tipo de período
        if (this.tipoPeriodoReporte === 'anual') {
            this.anioSeleccionado = new Date().getFullYear();
            this.mesSeleccionado = '';
        } else {
            // Usar las fechas actuales del filtro
            if (!this.filtroFechas.inicio || !this.filtroFechas.fin) {
                const ahora = new Date();
                this.filtroFechas.inicio = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth(), 1));
                this.filtroFechas.fin = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth() + 1, 0));
            }
        }
    },
    
    obtenerNombreMes(numeroMes) {
        const meses = [
            '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return meses[parseInt(numeroMes)];
    },
    
    descargarReporte() {
        // Validar el formulario
        const form = document.getElementById('formReporte');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        let params;
        
        if (this.tipoPeriodoReporte === 'anual') {
            // Validar que se haya seleccionado un año
            if (!this.anioSeleccionado) {
                alert('Por favor seleccione un año');
                return;
            }
            
            // Construir parámetros para reporte anual
            params = new URLSearchParams({
                tipo: this.reporteSeleccionado,
                periodo_tipo: 'anual',
                anio: this.anioSeleccionado
            });
            
            // Agregar mes si está seleccionado
            if (this.mesSeleccionado) {
                params.append('mes', this.mesSeleccionado);
            }
        } else {
            // Validar fechas para rango
            if (!this.filtroFechas.inicio || !this.filtroFechas.fin) {
                alert('Por favor seleccione las fechas de inicio y fin');
                return;
            }
            
            if (new Date(this.filtroFechas.inicio) > new Date(this.filtroFechas.fin)) {
                alert('La fecha de inicio no puede ser mayor que la fecha fin');
                return;
            }
            
            // Construir parámetros para rango de fechas
            params = new URLSearchParams({
                tipo: this.reporteSeleccionado,
                periodo_tipo: 'rango',
                fecha_inicio: this.filtroFechas.inicio,
                fecha_fin: this.filtroFechas.fin
            });
        }
        
        // Crear la URL completa
        const url = `${_URL}/r/dashboard/reporte?${params}`;
        
        // Abrir en una nueva ventana/pestaña
        window.open(url, '_blank');
        
        // Cerrar el modal
        bootstrap.Modal.getInstance(document.getElementById('descargarReporteModal')).hide();
    }
            },
            mounted() {
                // Esperar a que Vue termine de renderizar
                this.$nextTick(() => {
                    // Esperar un poco más para asegurarse
                    setTimeout(() => {
                        this.inicializarGraficos();
                    }, 100);
                });
            }
        });
    </script>
</body>

</html>