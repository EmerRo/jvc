<?php
/**
 * Lógica PHP simplificada para el Dashboard Home
 * Versión de respaldo cuando no hay dependencias disponibles
 */

// Variables de sesión con valores por defecto para testing
$empresa = $_SESSION['id_empresa'] ?? '1';
$sucursal = $_SESSION['sucursal'] ?? '1';

// Variables para clientes (datos de ejemplo)
$clientes_top = null; // Simular que no hay datos

// Variables para productos top 
$productos_top = null;

// Variables para stock 
$productos_stock_bajo = null;

// Obtener el período actual desde la URL o usar 'mes' por defecto
$periodo_actual = $_GET['periodo'] ?? 'mes';
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

// Configurar fechas y textos según el período
$ahora = new DateTime();
$textos_periodo = [];
$categorias_grafico = [];
$datos_ventas = [];

switch ($periodo_actual) {
    case 'hoy':
        $fecha_inicio = $ahora->format('Y-m-d');
        $fecha_fin = $ahora->format('Y-m-d');
        $textos_periodo = [
            'titulo_principal' => 'Ventas de Hoy',
            'comparativa' => 'vs. Día Anterior',
            'periodo_comparativo' => 'ayer'
        ];
        $categorias_grafico = ['Hoy'];
        break;

    case 'semana':
        $diaSemana = $ahora->format('N'); // 1 (lunes) a 7 (domingo)
        $inicioSemana = clone $ahora;
        $inicioSemana->modify('-' . ($diaSemana - 1) . ' days');
        $fecha_inicio = $inicioSemana->format('Y-m-d');
        $fecha_fin = $ahora->format('Y-m-d');
        $textos_periodo = [
            'titulo_principal' => 'Ventas de Esta Semana',
            'comparativa' => 'vs. Semana Anterior',
            'periodo_comparativo' => 'semana_anterior'
        ];
        $categorias_grafico = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        break;

    case 'mes':
        $inicioMes = new DateTime($ahora->format('Y-m-01'));
        $fecha_inicio = $inicioMes->format('Y-m-d');
        $finMes = new DateTime($ahora->format('Y-m-t'));
        $fecha_fin = $finMes->format('Y-m-d');
        $textos_periodo = [
            'titulo_principal' => 'Ventas de Este Mes',
            'comparativa' => 'vs. Mes Anterior',
            'periodo_comparativo' => 'mes_anterior'
        ];
        $categorias_grafico = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4', 'Semana 5'];
        break;

    case 'anio':
        $inicioAnio = new DateTime($ahora->format('Y-01-01'));
        $fecha_inicio = $inicioAnio->format('Y-m-d');
        $finAnio = new DateTime($ahora->format('Y-12-31'));
        $fecha_fin = $finAnio->format('Y-m-d');
        $textos_periodo = [
            'titulo_principal' => 'Ventas Anuales',
            'comparativa' => 'vs. Año Anterior',
            'periodo_comparativo' => 'anio_anterior'
        ];
        $categorias_grafico = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        break;

    default: // personalizado
        if ($fecha_inicio && $fecha_fin) {
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);
            $diff = $inicio->diff($fin);

            $fecha_inicio_formato = $inicio->format('d/m/Y');
            $fecha_fin_formato = $fin->format('d/m/Y');

            $textos_periodo = [
                'titulo_principal' => "Ventas del $fecha_inicio_formato - $fecha_fin_formato",
                'comparativa' => 'vs. Período Anterior',
                'periodo_comparativo' => 'periodo_anterior'
            ];

            if ($diff->days == 0) {
                $categorias_grafico = [$inicio->format('d/m')];
            } elseif ($diff->days <= 7) {
                $categorias_grafico = [];
                for ($i = 0; $i <= $diff->days; $i++) {
                    $fecha_temp = clone $inicio;
                    $fecha_temp->modify("+$i days");
                    $categorias_grafico[] = $fecha_temp->format('d/m');
                }
            } else {
                $categorias_grafico = [];
                $fecha_actual = clone $inicio;
                while ($fecha_actual <= $fin) {
                    $categorias_grafico[] = $fecha_actual->format('d/m');
                    $fecha_actual->modify('+7 days');
                }
            }

            $periodo_actual = 'personalizado';
        } else {
            $textos_periodo = [
                'titulo_principal' => 'Ventas del Período',
                'comparativa' => 'vs. Período Anterior',
                'periodo_comparativo' => 'periodo_anterior'
            ];
            $categorias_grafico = ['Sin datos'];
        }
        break;
}

// Datos simulados para testing
$data = [
    'totalv' => 25000.50,
    'totalvF' => 15000.30,
    'totalvB' => 10000.20,
    'totalvMA' => 23000.00,
    'utilidad_bruta_actual' => 7500.15,
    'utilidad_bruta_anterior' => 6900.00,
    'cnt_cli' => 150
];

// Datos simulados para gráficos
$dataListVen = [];
$dataUtilidadBruta = [];
for ($i = 0; $i < count($categorias_grafico); $i++) {
    $dataListVen[] = rand(1000, 5000);
    $dataUtilidadBruta[] = rand(500, 2000);
}

// Productos simulados
$productos_nombres = ['Producto A', 'Producto B', 'Producto C', 'Producto D', 'Producto E'];
$productos_cantidades = [45, 38, 25, 20, 15];

// Categorías y productos por categoría
$categorias = null;
$productos_por_categoria = null;

// Mock de resultados de consultas (simulando datos que vendrían de la BD)
// Nota: $productos_top, $productos_stock_bajo y $clientes_top ya están definidos arriba

// Variable de conexión simulada
$conexion = null;

echo "<!-- Archivo home-logic-simple.php cargado correctamente -->\n";
?>