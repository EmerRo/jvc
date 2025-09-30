<?php
/**
 * Lógica PHP principal para el Dashboard Home
 * Extraído de home.php para mejor organización
 */

// Variables de sesión - verificar que existan
if (!isset($_SESSION['id_empresa']) || !isset($_SESSION['sucursal'])) {
    die("Error: Sesión no válida. Variables de sesión requeridas no encontradas.");
}

$empresa = $_SESSION['id_empresa'];
$sucursal = $_SESSION['sucursal'];

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
        // Para mes, mostrar semanas del mes
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

            // Formatear fechas para mostrar
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
                // Para períodos largos, agrupar por semanas
                $categorias_grafico = [];
                $fecha_actual = clone $inicio;
                while ($fecha_actual <= $fin) {
                    $categorias_grafico[] = $fecha_actual->format('d/m');
                    $fecha_actual->modify('+7 days');
                }
            }

            // Establecer período actual como personalizado
            $periodo_actual = 'personalizado';
        } else {
            // Valores por defecto si no hay fechas
            $textos_periodo = [
                'titulo_principal' => 'Ventas del Período',
                'comparativa' => 'vs. Período Anterior',
                'periodo_comparativo' => 'periodo_anterior'
            ];
            $categorias_grafico = ['Sin datos'];
        }
        break;
}

// Configuración de fechas para comparativas según el período
$fecha_inicio_comparativa = '';
$fecha_fin_comparativa = '';

switch ($textos_periodo['periodo_comparativo']) {
    case 'ayer':
        $ayer = clone $ahora;
        $ayer->modify('-1 day');
        $fecha_inicio_comparativa = $ayer->format('Y-m-d');
        $fecha_fin_comparativa = $ayer->format('Y-m-d');
        break;

    case 'semana_anterior':
        $inicioSemanaAnterior = clone $ahora;
        $inicioSemanaAnterior->modify('-1 week')->modify('-' . ($ahora->format('N') - 1) . ' days');
        $finSemanaAnterior = clone $inicioSemanaAnterior;
        $finSemanaAnterior->modify('+6 days');
        $fecha_inicio_comparativa = $inicioSemanaAnterior->format('Y-m-d');
        $fecha_fin_comparativa = $finSemanaAnterior->format('Y-m-d');
        break;

    case 'mes_anterior':
        $mesAnterior = clone $ahora;
        $mesAnterior->modify('-1 month');
        $fecha_inicio_comparativa = $mesAnterior->format('Y-m-01');
        $fecha_fin_comparativa = $mesAnterior->format('Y-m-t');
        break;

    case 'anio_anterior':
        $anioAnterior = clone $ahora;
        $anioAnterior->modify('-1 year');
        $fecha_inicio_comparativa = $anioAnterior->format('Y-01-01');
        $fecha_fin_comparativa = $anioAnterior->format('Y-12-31');
        break;
}

// Conexión a la base de datos
if (!class_exists('Conexion')) {
    die("Error: Clase Conexion no encontrada. Asegúrese de que esté incluida en el contexto.");
}

try {
    $conexion = (new Conexion())->getConexion();
    if (!$conexion) {
        die("Error: No se pudo establecer conexión a la base de datos.");
    }
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Consulta principal adaptada al período
$sql = "SELECT 
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' AND estado = '1' and sucursal='$sucursal' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalv,
  (SELECT COUNT(*) FROM clientes WHERE id_empresa = '$empresa') cnt_cli,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='$sucursal' and id_tido =2 AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalvF,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='$sucursal' and id_tido =1 AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalvB,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='$sucursal' AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa') totalvMA,
  (SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) 
   FROM productos_ventas pv 
   INNER JOIN ventas v ON pv.id_venta = v.id_venta 
   WHERE v.id_empresa='$empresa' AND v.estado = '1' AND v.sucursal='$sucursal' 
   AND v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') utilidad_bruta_actual,
  (SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) 
   FROM productos_ventas pv 
   INNER JOIN ventas v ON pv.id_venta = v.id_venta 
   WHERE v.id_empresa='$empresa' AND v.estado = '1' AND v.sucursal='$sucursal' 
   AND v.fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa') utilidad_bruta_anterior";

$data = $conexion->query($sql)->fetch_assoc();

// Incluir las funciones auxiliares
include_once 'home-functions.php';
?>