<!-- resources\views\fragment-views\cliente\home.php -->
<?php
$empresa = $_SESSION['id_empresa'];

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
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
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
$conexion = (new Conexion())->getConexion();

// Consulta principal adaptada al período
$sql = "SELECT 
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' AND estado = '1' and sucursal='{$_SESSION['sucursal']}' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalv,
  (SELECT COUNT(*) FROM clientes WHERE id_empresa = '$empresa') cnt_cli,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' and id_tido =2 AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalvF,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' and id_tido =1 AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalvB,
  (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa') totalvMA,
  (SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) 
   FROM productos_ventas pv 
   INNER JOIN ventas v ON pv.id_venta = v.id_venta 
   WHERE v.id_empresa='$empresa' AND v.estado = '1' AND v.sucursal='{$_SESSION['sucursal']}' 
   AND v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') utilidad_bruta_actual,
  (SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) 
   FROM productos_ventas pv 
   INNER JOIN ventas v ON pv.id_venta = v.id_venta 
   WHERE v.id_empresa='$empresa' AND v.estado = '1' AND v.sucursal='{$_SESSION['sucursal']}' 
   AND v.fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa') utilidad_bruta_anterior";

$data = $conexion->query($sql)->fetch_assoc();

// Generar datos para el gráfico según el período
function generarDatosGrafico($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion)
{
    $datos = [];

    switch ($periodo) {
        case 'hoy':
            $sql = "SELECT SUM(total) as total FROM ventas 
                   WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                   AND fecha_emision = '$fecha_inicio'";
            $result = $conexion->query($sql);
            $row = $result->fetch_assoc();
            $datos = [$row['total'] ?? 0];
            break;

        case 'semana':
            // Obtener ventas por día de la semana
            $inicio = new DateTime($fecha_inicio);
            for ($i = 0; $i < 7; $i++) {
                $fecha_dia = clone $inicio;
                $fecha_dia->modify("+$i days");
                $fecha_str = $fecha_dia->format('Y-m-d');

                $sql = "SELECT SUM(total) as total FROM ventas 
                       WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                       AND fecha_emision = '$fecha_str'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['total'] ?? 0);
            }
            break;

        case 'mes':
            // Obtener ventas por semana del mes
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);

            for ($semana = 1; $semana <= 5; $semana++) {
                $inicio_semana = clone $inicio;
                $inicio_semana->modify('+' . (($semana - 1) * 7) . ' days');
                $fin_semana = clone $inicio_semana;
                $fin_semana->modify('+6 days');

                if ($fin_semana > $fin) {
                    $fin_semana = $fin;
                }

                $sql = "SELECT SUM(total) as total FROM ventas 
                       WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                       AND fecha_emision BETWEEN '{$inicio_semana->format('Y-m-d')}' AND '{$fin_semana->format('Y-m-d')}'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['total'] ?? 0);

                if ($fin_semana >= $fin)
                    break;
            }
            break;

        case 'anio':
            // Mantener el comportamiento original para año (por meses)
            $datos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $anio = date('Y', strtotime($fecha_inicio));

            $sql = "SELECT MONTH(fecha_emision) mes, SUM(total) total
                   FROM ventas 
                   WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal'
                   AND YEAR(fecha_emision) = '$anio'
                   GROUP BY mes";
            $result = $conexion->query($sql);

            while ($row = $result->fetch_assoc()) {
                $datos[intval($row['mes']) - 1] = floatval($row['total']);
            }
            break;
    }

    return $datos;
}
// Función para generar datos de utilidad bruta según el período
function generarDatosUtilidadBruta($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion)
{
    $datos = [];

    switch ($periodo) {
        case 'hoy':
            $sql = "SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) as utilidad 
                   FROM productos_ventas pv 
                   INNER JOIN ventas v ON pv.id_venta = v.id_venta 
                   WHERE v.id_empresa = '$empresa' AND v.estado = '1' AND v.sucursal = '$sucursal' 
                   AND v.fecha_emision = '$fecha_inicio'";
            $result = $conexion->query($sql);
            $row = $result->fetch_assoc();
            $datos = [$row['utilidad'] ?? 0];
            break;

        case 'semana':
            // Obtener utilidad por día de la semana
            $inicio = new DateTime($fecha_inicio);
            for ($i = 0; $i < 7; $i++) {
                $fecha_dia = clone $inicio;
                $fecha_dia->modify("+$i days");
                $fecha_str = $fecha_dia->format('Y-m-d');

                $sql = "SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) as utilidad 
                       FROM productos_ventas pv 
                       INNER JOIN ventas v ON pv.id_venta = v.id_venta 
                       WHERE v.id_empresa = '$empresa' AND v.estado = '1' AND v.sucursal = '$sucursal' 
                       AND v.fecha_emision = '$fecha_str'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['utilidad'] ?? 0);
            }
            break;

        case 'mes':
            // Obtener utilidad por semana del mes
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);

            for ($semana = 1; $semana <= 5; $semana++) {
                $inicio_semana = clone $inicio;
                $inicio_semana->modify('+' . (($semana - 1) * 7) . ' days');
                $fin_semana = clone $inicio_semana;
                $fin_semana->modify('+6 days');

                if ($fin_semana > $fin) {
                    $fin_semana = $fin;
                }

                $sql = "SELECT SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) as utilidad 
                       FROM productos_ventas pv 
                       INNER JOIN ventas v ON pv.id_venta = v.id_venta 
                       WHERE v.id_empresa = '$empresa' AND v.estado = '1' AND v.sucursal = '$sucursal' 
                       AND v.fecha_emision BETWEEN '{$inicio_semana->format('Y-m-d')}' AND '{$fin_semana->format('Y-m-d')}'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['utilidad'] ?? 0);

                if ($fin_semana >= $fin)
                    break;
            }
            break;

        case 'anio':
            // Utilidad por meses del año
            $datos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $anio = date('Y', strtotime($fecha_inicio));

            $sql = "SELECT MONTH(v.fecha_emision) mes, SUM(pv.precio * pv.cantidad - pv.costo * pv.cantidad) utilidad
                   FROM productos_ventas pv 
                   INNER JOIN ventas v ON pv.id_venta = v.id_venta 
                   WHERE v.id_empresa = '$empresa' AND v.estado = '1' AND v.sucursal = '$sucursal'
                   AND YEAR(v.fecha_emision) = '$anio'
                   GROUP BY mes";
            $result = $conexion->query($sql);

            while ($row = $result->fetch_assoc()) {
                $datos[intval($row['mes']) - 1] = floatval($row['utilidad']);
            }
            break;
    }

    return $datos;
}

// Función para generar datos de INGRESOS según el período
function generarDatosIngresos($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion)
{
    $datos = [];

    switch ($periodo) {
        case 'hoy':
            $sql = "SELECT SUM(total) as total FROM ventas 
                   WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                   AND fecha_emision = '$fecha_inicio'";
            $result = $conexion->query($sql);
            $row = $result->fetch_assoc();
            $datos = [$row['total'] ?? 0];
            break;

        case 'semana':
            $inicio = new DateTime($fecha_inicio);
            for ($i = 0; $i < 7; $i++) {
                $fecha_dia = clone $inicio;
                $fecha_dia->modify("+$i days");
                $fecha_str = $fecha_dia->format('Y-m-d');

                $sql = "SELECT SUM(total) as total FROM ventas 
                       WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                       AND fecha_emision = '$fecha_str'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['total'] ?? 0);
            }
            break;

        case 'mes':
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);

            for ($semana = 1; $semana <= 5; $semana++) {
                $inicio_semana = clone $inicio;
                $inicio_semana->modify('+' . (($semana - 1) * 7) . ' days');
                $fin_semana = clone $inicio_semana;
                $fin_semana->modify('+6 days');

                if ($fin_semana > $fin) {
                    $fin_semana = $fin;
                }

                $sql = "SELECT SUM(total) as total FROM ventas 
                       WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                       AND fecha_emision BETWEEN '{$inicio_semana->format('Y-m-d')}' AND '{$fin_semana->format('Y-m-d')}'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['total'] ?? 0);

                if ($fin_semana >= $fin)
                    break;
            }
            break;

        case 'anio':
            $datos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $anio = date('Y', strtotime($fecha_inicio));

            $sql = "SELECT MONTH(fecha_emision) mes, SUM(total) total
                   FROM ventas 
                   WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal'
                   AND YEAR(fecha_emision) = '$anio'
                   GROUP BY mes";
            $result = $conexion->query($sql);

            while ($row = $result->fetch_assoc()) {
                $datos[intval($row['mes']) - 1] = floatval($row['total']);
            }
            break;
    }

    return $datos;
}

// Función para generar datos de EGRESOS según el período
function generarDatosEgresos($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $conexion)
{
    $datos = [];

    switch ($periodo) {
        case 'hoy':
            $sql = "SELECT SUM(total) as total FROM compras 
                   WHERE id_empresa = '$empresa' 
                   AND fecha_emision = '$fecha_inicio'";
            $result = $conexion->query($sql);
            $row = $result->fetch_assoc();
            $datos = [$row['total'] ?? 0];
            break;

        case 'semana':
            $inicio = new DateTime($fecha_inicio);
            for ($i = 0; $i < 7; $i++) {
                $fecha_dia = clone $inicio;
                $fecha_dia->modify("+$i days");
                $fecha_str = $fecha_dia->format('Y-m-d');

                $sql = "SELECT SUM(total) as total FROM compras 
                       WHERE id_empresa = '$empresa' 
                       AND fecha_emision = '$fecha_str'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['total'] ?? 0);
            }
            break;

        case 'mes':
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);

            for ($semana = 1; $semana <= 5; $semana++) {
                $inicio_semana = clone $inicio;
                $inicio_semana->modify('+' . (($semana - 1) * 7) . ' days');
                $fin_semana = clone $inicio_semana;
                $fin_semana->modify('+6 days');

                if ($fin_semana > $fin) {
                    $fin_semana = $fin;
                }

                $sql = "SELECT SUM(total) as total FROM compras 
                       WHERE id_empresa = '$empresa' 
                       AND fecha_emision BETWEEN '{$inicio_semana->format('Y-m-d')}' AND '{$fin_semana->format('Y-m-d')}'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos[] = floatval($row['total'] ?? 0);

                if ($fin_semana >= $fin)
                    break;
            }
            break;

        case 'anio':
            $datos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $anio = date('Y', strtotime($fecha_inicio));

            $sql = "SELECT MONTH(fecha_emision) mes, SUM(total) total
                   FROM compras 
                   WHERE id_empresa = '$empresa'
                   AND YEAR(fecha_emision) = '$anio'
                   GROUP BY mes";
            $result = $conexion->query($sql);

            while ($row = $result->fetch_assoc()) {
                $datos[intval($row['mes']) - 1] = floatval($row['total']);
            }
            break;
    }

    return $datos;
}


$dataListVen = generarDatosGrafico($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $_SESSION['sucursal'], $conexion);

$dataUtilidadBruta = generarDatosUtilidadBruta($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $_SESSION['sucursal'], $conexion);

// Productos más vendidos (mantener la lógica existente pero filtrada por período)
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
  AND v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
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
        $productos_nombres[] = trim(str_replace(["\t", "\n", "\r"], '', $producto['nombre']));
        $productos_cantidades[] = intval($producto['total_vendido']);
    }
    $productos_top->data_seek(0);
}

// Productos con stock bajo (mantener igual)
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

// Clientes top (filtrado por período)
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
  AND v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
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
        $clientes_nombres[] = trim(str_replace(["\t", "\n", "\r"], '', $cliente['datos']));
        $clientes_compras[] = floatval($cliente['total_compras']);
    }
    $clientes_top->data_seek(0);
}

// Ingresos y Egresos REALES
$ingresos_mensuales = $data["totalv"] ?? 0;

// Obtener egresos REALES de la tabla compras
$sql_egresos = "SELECT SUM(total) as total_egresos 
               FROM compras 
               WHERE id_empresa='$empresa' 
               AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$egresos_result = $conexion->query($sql_egresos);
$egresos_data = $egresos_result->fetch_assoc();
$egresos_mensuales = $egresos_data['total_egresos'] ?? 0;

// Egresos del período comparativo
$sql_egresos_anterior = "SELECT SUM(total) as total_egresos_anterior 
                        FROM compras 
                        WHERE id_empresa='$empresa' 
                        AND fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa'";
$egresos_anterior_result = $conexion->query($sql_egresos_anterior);
$egresos_anterior_data = $egresos_anterior_result->fetch_assoc();
$egresos_mensuales_anterior = $egresos_anterior_data['total_egresos_anterior'] ?? 0;

$ganancia_mensual = $ingresos_mensuales - $egresos_mensuales;
$dataIngresos = generarDatosIngresos($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $_SESSION['sucursal'], $conexion);
$dataEgresos = generarDatosEgresos($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $conexion);


// Datos para gráfico de ventas por período
$periodos = ['Diario', 'Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Trimestral', 'Semestral', 'Anual'];
$ventasPorPeriodo = [];

$ventasPorPeriodo = [
    number_format($data["totalv"] / 30 ?? 0, 2, ".", ""),
    number_format($data["totalv"] / 4 ?? 0, 2, ".", ""),
    number_format($data["totalv"] / 2 ?? 0, 2, ".", ""),
    number_format($data["totalv"] ?? 0, 2, ".", ""),
    number_format($data["totalv"] * 2 ?? 0, 2, ".", ""),
    number_format($data["totalv"] * 3 ?? 0, 2, ".", ""),
    number_format($data["totalv"] * 6 ?? 0, 2, ".", ""),
    number_format($data["totalv"] * 12 ?? 0, 2, ".", "")
];

// Convertir datos PHP a JSON para usar en Vue
$dashboardData = [
    'ventasAnuales' => $dataListVen,
    'categoriasGrafico' => $categorias_grafico,
    'textosPeriodo' => $textos_periodo,
    'periodoActual' => $periodo_actual,
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
    'gananciaMensual' => $ganancia_mensual,
    // para utilidad bruta
    'utilidadBrutaActual' => $data["utilidad_bruta_actual"] ?? 0,
    'utilidadBrutaAnterior' => $data["utilidad_bruta_anterior"] ?? 0,
    'utilidadBrutaPorPeriodo' => $dataUtilidadBruta,
    'totalProductosVendidos' => array_sum($productos_cantidades),
    'totalProductosVendidosAnterior' => 0, // Se calculará en la carga inicial
    'totalCategoriasActivas' => 0,
    'rotacionPromedio' => 15,
    'categorias' => [],
    'productosPorCategoria' => [],
    'ingresosPorPeriodo' => $dataIngresos,
    'egresosPorPeriodo' => $dataEgresos,
    'egresosMensualesAnterior' => $egresos_mensuales_anterior,
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
                                            <select class="form-select" id="tipoReporte" v-model="reporteSeleccionado"
                                                required>
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
                                            <select class="form-select" id="tipoPeriodo" v-model="tipoPeriodoReporte"
                                                @change="cambiarTipoPeriodo" required>
                                                <option value="rango">Rango de Fechas</option>
                                                <option value="anual">Por Año</option>
                                            </select>
                                        </div>

                                        <!-- Selector de fechas (mostrar solo si es rango) -->
                                        <div v-show="tipoPeriodoReporte === 'rango'" class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="reporteFechaInicio" class="form-label">Fecha
                                                        Inicio</label>
                                                    <input type="date" class="form-control" id="reporteFechaInicio"
                                                        v-model="filtroFechas.inicio"
                                                        :required="tipoPeriodoReporte === 'rango'">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="reporteFechaFin" class="form-label">Fecha Fin</label>
                                                    <input type="date" class="form-control" id="reporteFechaFin"
                                                        v-model="filtroFechas.fin"
                                                        :required="tipoPeriodoReporte === 'rango'">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selector de año (mostrar solo si es anual) -->
                                        <div v-show="tipoPeriodoReporte === 'anual'" class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="reporteAnio" class="form-label">Año</label>
                                                    <select class="form-select" id="reporteAnio"
                                                        v-model="anioSeleccionado"
                                                        :required="tipoPeriodoReporte === 'anual'">
                                                        <option value="">Seleccione un año</option>
                                                        <option v-for="anio in aniosDisponibles" :key="anio"
                                                            :value="anio">{{ anio }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="reporteMes" class="form-label">Mes (Opcional)</label>
                                                    <select class="form-select" id="reporteMes"
                                                        v-model="mesSeleccionado">
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
                                                Se generará el reporte para {{ obtenerNombreMes(mesSeleccionado) }} de
                                                {{ anioSeleccionado }}
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
                                        <i class="fas fa-download me-1"></i>Descargar PDF
                                    </button>
                                    <!-- NUEVO BOTÓN PARA EXCEL -->
                                    <button type="button" class="btn bg-success text-white"
                                        @click="descargarReporteExcel">
                                        <i class="fas fa-file-excel me-1"></i>Descargar Excel
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
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
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
                        </div>
                    </div>
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
                                        <p class="mb-0 mt-1 text-end">{{ dashboardData.textosPeriodo ?
                                            dashboardData.textosPeriodo.comparativa : 'vs. Mes Anterior' }}</p>
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
                                    <h4 class="card-title mb-4">{{ dashboardData.textosPeriodo ?
                                        dashboardData.textosPeriodo.titulo_principal : 'Ventas Anuales' }}</h4>
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

                    <!-- Comparativa con años anteriores y Utilidad Bruta -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card fade-in-up">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">{{ obtenerTituloComparativa() }}</h4>
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
                        <div class="col-xl-4">
                            <div class="card fade-in-up">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Utilidad Bruta - {{ dashboardData.textosPeriodo ?
                                        dashboardData.textosPeriodo.titulo_principal : 'Período Actual' }}</h4>
                                    <div class="chart-container">
                                        <div id="utilidadBrutaChartLoading" class="chart-loading" v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="utilidadBrutaChart" class="chart-container">
                                            <div v-if="!hayDatosUtilidadBruta" class="no-data-message">
                                                <i class="fas fa-chart-bar"></i>
                                                <p>No hay datos disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Resumen de utilidad -->
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h5 class="text-success mb-1">S/ {{
                                                    formatNumber(dashboardData.utilidadBrutaActual) }}</h5>
                                                <small class="text-muted">Utilidad Actual</small>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1" :class="porcentajeUtilidadClass">{{ porcentajeUtilidad
                                                    }}%</h5>
                                                <small class="text-muted">Margen Bruto</small>
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
                    <!-- NUEVO: Filtro de período para productos -->
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoProductos }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoProductos('hoy')">Hoy</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoProductos('semana')">Esta semana</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoProductos('mes')">Este mes</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoProductos('anio')">Este año</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="abrirModalPersonalizadoProductos()">Personalizado</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjetas de resumen de productos -->
                    <div class="row fade-in-up mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-white text-dark">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div
                                            class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3">
                                            <img class="mt-3 mr-5"
                                                src="<?= URL::to('public/assets/images/services-icon/01.png') ?>">
                                        </div>
                                        <h5 class="text-uppercase fw-light text-end">Productos Vendidos</h5>
                                        <h1 class="fw-bolder text-end counter-value">{{
                                            dashboardData.totalProductosVendidos || 0 }}</h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">Unidades totales</p>
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
                                        <h5 class="fw-light text-uppercase text-end">Categorías Activas</h5>
                                        <h1 class="fw-bolder text-end counter-value">{{
                                            dashboardData.totalCategoriasActivas || 0 }}</h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">Con productos vendidos</p>
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
                                        <h5 class="fw-light text-uppercase text-end">Comparativa</h5>
                                        <h1 class="fw-bolder text-end counter-value"
                                            v-html="comparativaProductosAnterior"></h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">{{ textoComparativaProductos }}</p>
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
                                        <h5 class="fw-light text-uppercase text-end">Rotación Promedio</h5>
                                        <h1 class="fw-bolder text-end counter-value">{{ dashboardData.rotacionPromedio
                                            || 0 }}</h1>
                                    </div>
                                    <div class="pt-2">
                                        <p class="mb-0 mt-1 text-end">Días promedio</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos de productos -->
                    <div class="row fade-in-up">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Productos Más Vendidos - {{ periodoTextoProductos }}
                                    </h4>
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

                    <!-- NUEVO: Productos más vendidos por categoría -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card slide-in-left">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Productos Más Vendidos por Categoría - {{
                                        periodoTextoProductos }}</h4>
                                    <div class="chart-container">
                                        <div id="productosCategoriaChartLoading" class="chart-loading"
                                            v-if="loadingCharts">
                                            <div class="spinner"></div>
                                        </div>
                                        <div ref="productosCategoriaChart" class="chart-container">
                                            <div v-if="!hayDatosProductosPorCategoria" class="no-data-message">
                                                <i class="fas fa-chart-bar"></i>
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
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <div class="input-group" style="max-width: 300px;">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control"
                                    placeholder="Buscar productos por nombre, código..." v-model="filtroStock"
                                    @input="filtrarStock">
                            </div>
                        </div>
                    </div>
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
                        <div class="col-xl-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Alertas de Stock</h4>
                                    <div class="alert alert-danger animate-pulse">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>¡Atención!</strong> Hay productos con stock crítico.
                                    </div>

                                    <div class="overflow-auto" style="max-height: 260px;">
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
                    <!-- NUEVO: Filtro de período para Ingresos y Egresos -->
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="button" class="btn bg-rojo text-white dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-calendar-alt me-1"></i> {{ periodoTextoIngresos }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoIngresos('hoy')">Hoy</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoIngresos('semana')">Esta semana</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoIngresos('mes')">Este mes</a></li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="cambiarPeriodoIngresos('anio')">Este año</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"
                                            @click.prevent="abrirModalPersonalizadoIngresos()">Personalizado</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
            <!-- REEMPLAZAR el gráfico único por dos gráficos -->
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card slide-in-right">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Distribución de Ingresos</h4>
                            <div class="chart-container">
                                <div id="distribucionIngresosChartLoading" class="chart-loading"
                                    v-if="loadingCharts">
                                    <div class="spinner"></div>
                                </div>
                                <div ref="distribucionIngresosChart" class="chart-container">
                                    <div v-if="!hayDatosIngresos" class="no-data-message">
                                        <i class="fas fa-chart-pie"></i>
                                        <p>No hay datos disponibles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
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
                                    <div id="frecuenciaCompraChartLoading" class="chart-loading" v-if="loadingCharts">
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
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
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
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
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
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
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
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Productos por Estado de Stock -->
        <div class="modal fade" id="productosEstadoModal" tabindex="-1" aria-labelledby="productosEstadoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content border-rojo">
                    <div class="modal-header bg-rojo text-white">
                        <h5 class="modal-title" id="productosEstadoModalLabel">
                            <i class="fas fa-boxes me-2"></i>Productos por Estado de Stock
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Información del estado seleccionado -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert" id="alertaEstadoSeleccionado" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i id="iconoEstadoSeleccionado" class="fas fa-box me-3 fs-4"></i>
                                        <div>
                                            <h6 class="mb-1" id="tituloEstadoSeleccionado">Estado: </h6>
                                            <small id="descripcionEstadoSeleccionado">Productos en este
                                                estado</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de búsqueda -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="busquedaProductosEstado"
                                        placeholder="Buscar por nombre, código o categoría...">
                                </div>
                            </div>
                            <!-- <div class="col-md-6 text-end">
                                    <button class="btn btn-success btn-sm" id="exportarProductosEstado">
                                        <i class="fas fa-file-excel me-1"></i>Exportar Excel
                                    </button>
                                </div> -->
                        </div>

                        <!-- Tabla de productos -->
                        <div style="max-height: 60vh; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaProductosEstado">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Stock Actual</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                            <th>Última Actualización</th>
                                            <!-- <th>Acciones</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpoTablaProductosEstado">
                                        <!-- Los productos se cargarán aquí dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Loading state -->
                        <div id="loadingProductosEstado" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-rojo" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando productos...</p>
                        </div>

                        <!-- Estado vacío -->
                        <div id="estadoVacioProductos" class="text-center py-5" style="display: none;">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron productos</h5>
                            <p class="text-muted">No hay productos en este estado o que coincidan con la búsqueda
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="me-auto">
                            <small class="text-muted" id="contadorProductosEstado">0 productos encontrados</small>
                        </div>
                        <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
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
                periodoActual: <?= json_encode($periodo_actual) ?>,
                periodoTexto: <?= json_encode($textos_periodo['titulo_principal'] ?? 'Este mes') ?>,
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
                productosStock: [],
                productosStockFiltrados: [],
                estadisticasStock: null,
                loadingVentas: false,
                loadingStock: false,
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
                    // **CORREGIR**: Verificación más robusta
                    const datos = this.dashboardData.ventasAnuales;
                    if (!datos || !Array.isArray(datos) || datos.length === 0) {
                        return false;
                    }

                    // Verificar si hay al menos un valor mayor que 0
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
                }
            },
            methods: {
                setActiveTab(tab) {
                    this.activeTab = tab;

                    // **NUEVO**: Limpiar URL al cambiar de pestaña (excepto ventas)
                    if (tab !== 'ventas') {
                        const cleanUrl = window.location.pathname;
                        window.history.pushState({}, '', cleanUrl);
                    }

                    // Limpiar filtros específicos por pestaña
                    switch (tab) {
                        case 'stock':
                            this.filtroStock = '';
                            this.cargarEstadisticasStock();
                            break;
                        case 'ventas':
                            this.filtroVentas = '';
                            break;
                        case 'productos':
                            this.filtroProductos = '';
                            break;
                        case 'clientes':
                            this.filtroClientes = '';
                            break;
                    }

                    this.$nextTick(() => {
                        this.inicializarGraficosPorTab(tab);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
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
                    // **CORREGIR**: Remover verificación que bloquea la inicialización
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

                        // **NUEVO**: Verificar datos de manera más flexible
                        const tieneVentasAnuales = this.dashboardData.ventasAnuales &&
                            Array.isArray(this.dashboardData.ventasAnuales) &&
                            this.dashboardData.ventasAnuales.length > 0;

                        console.log('¿Tiene datos de ventas anuales?', tieneVentasAnuales, this.dashboardData.ventasAnuales);

                        if (tieneVentasAnuales) {
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
                                    categories: this.dashboardData.categoriasGrafico || this.meses,
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
                        } else {
                            console.log('No hay datos para el gráfico de ventas anuales');
                        }
                    }

                    // **CONTINUAR CON EL RESTO DE GRÁFICOS...**
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

                    // Resto de gráficos (comparativa anual y utilidad bruta)...
                    this.inicializarGraficosComparativaAnual();
                    this.inicializarGraficoUtilidadBruta();
                },
                inicializarGraficosComparativaAnual() {
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
                            const añoActual = new Date().getFullYear();
                            const años = [añoActual - 2, añoActual - 1, añoActual];

                            // Generamos datos simulados para años anteriores
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
                                    categories: this.dashboardData.categoriasGrafico || this.meses,
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
                    }
                },

                inicializarGraficoUtilidadBruta() {
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
                            this.charts.utilidadBruta = Highcharts.chart(this.$refs.utilidadBrutaChart, {
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
                                    categories: this.dashboardData.categoriasGrafico || this.meses,
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
                },
                inicializarGraficosProductos() {
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

                    // NUEVO: Gráfico de productos por categoría CON COMPARATIVA
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

                        // En lugar de solo pasar rotacionDias, crear un array de objetos:
                        const datosConColores = rotacionDias.map(dias => {
                            let color;
                            if (dias < 10) {
                                color = '#28a745'; // Verde - Rotación alta
                            } else if (dias <= 20) {
                                color = '#ffc107'; // Amarillo - Aceptable  
                            } else {
                                color = '#dc3545'; // Rojo - Baja rotación
                            }

                            return {
                                y: dias,
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
                                    borderRadius: 5
                                    // colorByPoint: true,
                                    // colors: [
                                    //     this.colors.primary, this.colors.secondary, this.colors.success,
                                    //     this.colors.warning, this.colors.danger
                                    // ]
                                }
                            },
                            series: [{
                                name: 'Días promedio',
                                data: datosConColores,
                                showInLegend: false
                            }]
                        });
                    }

                    // Gráfico de Estado del Stock
                    if (this.$refs.estadoStockChart) {
                        this.cargarEstadisticasStock();
                        if (this.charts.estadoStock) {
                            try {
                                this.charts.estadoStock.destroy();
                            } catch (e) {
                                console.log('Chart already destroyed');
                            }
                            this.charts.estadoStock = null;
                        }

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
                                    point: {
                                        events: {
                                            click: (event) => {
                                                const estadoNombre = event.point.name;
                                                console.log('Clic en estado:', estadoNombre);

                                                // Usar la referencia capturada
                                                this.mostrarProductosPorEstado(estadoNombre);
                                            }
                                        }
                                    }
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
                            // series: [{
                            //     name: 'Porcentaje',
                            //     colorByPoint: true,
                            //     data: [
                            //         { name: estadosStock[0], y: cantidadesStock[0], color: this.colors.success },
                            //         { name: estadosStock[1], y: cantidadesStock[1], color: this.colors.info },
                            //         { name: estadosStock[2], y: cantidadesStock[2], color: this.colors.warning },
                            //         { name: estadosStock[3], y: cantidadesStock[3], color: this.colors.danger }
                            //     ]
                            // }]
                            series: [{
                                name: 'Porcentaje',
                                colorByPoint: true,
                                data: [
                                    { name: 'Óptimo', y: 45, color: this.colors.success },
                                    { name: 'Normal', y: 30, color: this.colors.info },
                                    { name: 'Bajo', y: 20, color: this.colors.warning },
                                    { name: 'Crítico', y: 5, color: this.colors.danger }
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

                    // NUEVO: Gráfico de Distribución de Ingresos
                    if (this.$refs.distribucionIngresosChart) {
                        if (this.charts.distribucionIngresos) {
                            try {
                                this.charts.distribucionIngresos.destroy();
                            } catch (e) {
                                console.log('Chart already destroyed');
                            }
                            this.charts.distribucionIngresos = null;
                        }

                        const categoriasIngresos = ['Facturas', 'Boletas', 'Servicios', 'Otros'];
                        const porcentajesIngresos = [
                            (this.dashboardData.totalFacturas / this.dashboardData.totalVentas) * 100 || 0,
                            (this.dashboardData.totalBoletas / this.dashboardData.totalVentas) * 100 || 0,
                            15, // Servicios (simulado)
                            10  // Otros (simulado)
                        ];

                        this.charts.distribucionIngresos = Highcharts.chart(this.$refs.distribucionIngresosChart, {
                            chart: {
                                type: 'pie',
                                style: { fontFamily: 'Poppins, sans-serif' },
                                animation: { duration: 1000 }
                            },
                            title: { text: null },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br>Monto: <b>S/ {point.y:.2f}</b>'
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
                                    innerSize: '60%'
                                }
                            },
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                itemStyle: { color: '#6c757d', fontWeight: 'normal', fontSize: '12px' }
                            },
                            series: [{
                                name: 'Porcentaje',
                                colorByPoint: true,
                                data: categoriasIngresos.map((cat, index) => ({
                                    name: cat,
                                    y: (porcentajesIngresos[index] / 100) * this.dashboardData.ingresosMensuales,
                                    percentage: porcentajesIngresos[index],
                                    color: [this.colors.primary, this.colors.secondary, this.colors.info, this.colors.warning][index]
                                }))
                            }]
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
                    // **NUEVO**: Solo permitir cambio de período en la pestaña de ventas
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
                    }

                    // **NUEVO**: Mostrar loading inmediatamente
                    this.loadingCharts = true;

                    // Cargar datos via AJAX
                    this.cargarDatosViaAjax(periodo);
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

                    // Formatear fechas para el botón
                    const fInicio = new Date(this.filtroFechas.inicio);
                    const fFin = new Date(this.filtroFechas.fin);
                    const fechaInicioTexto = `${fInicio.getDate().toString().padStart(2, '0')}/${(fInicio.getMonth() + 1).toString().padStart(2, '0')}`;
                    const fechaFinTexto = `${fFin.getDate().toString().padStart(2, '0')}/${(fFin.getMonth() + 1).toString().padStart(2, '0')}`;

                    this.periodoTexto = `${fechaInicioTexto} - ${fechaFinTexto}`;
                    this.periodoActual = 'personalizado';

                    bootstrap.Modal.getInstance(document.getElementById('periodoPersonalizadoModal')).hide();

                    // Cargar datos via AJAX
                    this.cargarDatosViaAjax('personalizado');
                },

                actualizarGraficosConNuevosDatos() {
                    // Actualizar gráfico de ventas anuales
                    if (this.charts.ventasAnuales && this.hayDatosVentasAnuales) {
                        this.charts.ventasAnuales.series[0].setData(this.dashboardData.ventasAnuales, true);
                        this.charts.ventasAnuales.xAxis[0].setCategories(this.dashboardData.categoriasGrafico || this.meses, true);
                    }

                    // Actualizar gráfico de comparativa
                    if (this.charts.comparativa && this.hayDatosComparativa) {
                        this.charts.comparativa.series[0].setData(this.dashboardData.ventasPorPeriodo, true);
                    }

                    // Actualizar gráfico de productos
                    if (this.charts.productosTop && this.hayDatosProductos) {
                        this.charts.productosTop.series[0].setData(this.dashboardData.productosCantidades, true);
                        this.charts.productosTop.xAxis[0].setCategories(this.dashboardData.productosNombres, true);
                    }

                    // Actualizar gráfico de clientes
                    if (this.charts.clientesTop && this.hayDatosClientes) {
                        this.charts.clientesTop.series[0].setData(this.dashboardData.clientesCompras, true);
                        this.charts.clientesTop.xAxis[0].setCategories(this.dashboardData.clientesNombres, true);
                    }
                },
                destruirTodosLosGraficos() {
                    const graficosADestruir = [
                        'ventasAnuales', 'comparativa', 'comparativaAnual', 'utilidadBruta',
                        'productosTop', 'distribucionProductos', 'rotacionInventario',
                        'estadoStock', 'movimientosInventario', 'ingresosEgresos',
                        'distribucionEgresos', 'clientesTop', 'frecuenciaCompra', 'vendedores'
                    ];

                    graficosADestruir.forEach(nombreGrafico => {
                        if (this.charts[nombreGrafico]) {
                            try {
                                this.charts[nombreGrafico].destroy();
                            } catch (e) {
                                console.log(`Gráfico ${nombreGrafico} ya destruido`);
                            }
                            this.charts[nombreGrafico] = null;
                        }
                    });
                },

                cargarDatosViaAjax(periodo) {
                    console.log('Cargando datos para período:', periodo);

                    // Mostrar loading
                    this.loadingCharts = true;

                    // Preparar parámetros
                    let params = new URLSearchParams({ periodo: periodo });

                    if (periodo === 'personalizado') {
                        params.append('fecha_inicio', this.filtroFechas.inicio);
                        params.append('fecha_fin', this.filtroFechas.fin);
                    }

                    // Destruir gráficos antes de la petición
                    this.destruirTodosLosGraficos();

                    // Hacer petición AJAX
                    fetch(`${_URL}/ajs/dashboard/datos?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Datos recibidos:', data);

                            if (data.success) {
                                // **CORREGIR**: Actualizar datos de manera más explícita
                                Object.keys(data.dashboardData).forEach(key => {
                                    this.dashboardData[key] = data.dashboardData[key];
                                });

                                console.log('Dashboard data actualizado:', this.dashboardData);

                                // Forzar actualización de Vue
                                this.$forceUpdate();

                                // Actualizar URL solo si estamos en ventas
                                if (this.activeTab === 'ventas') {
                                    const newUrl = `${window.location.pathname}?${params}`;
                                    window.history.pushState({}, '', newUrl);
                                }

                                // **NUEVO**: Delay más largo para asegurar que Vue actualice
                                this.$nextTick(() => {
                                    setTimeout(() => {
                                        console.log('Inicializando gráficos después de AJAX...');
                                        if (this.activeTab === 'ventas') {
                                            this.inicializarGraficosVentas();
                                        }
                                        this.loadingCharts = false;
                                    }, 500); // Aumentar delay a 500ms
                                });
                            } else {
                                console.error('Error en respuesta:', data.message);
                                alert('Error al cargar los datos: ' + data.message);
                                this.loadingCharts = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error en petición AJAX:', error);
                            alert('Error al cargar los datos');
                            this.loadingCharts = false;
                        });
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
                },
                descargarReporteExcel() {
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

                    // Crear la URL completa para Excel
                    const url = `${_URL}/r/dashboard/reporte-excel?${params}`;

                    // Abrir en una nueva ventana/pestaña
                    window.open(url, '_blank');

                    // Cerrar el modal
                    bootstrap.Modal.getInstance(document.getElementById('descargarReporteModal')).hide();
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
                // **NUEVO**: Mostrar productos por estado de stock
                mostrarProductosPorEstado(estadoNombre) {
                    console.log('Mostrando productos para estado:', estadoNombre);

                    // Configurar el modal
                    this.configurarModalEstado(estadoNombre);

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('productosEstadoModal'));
                    modal.show();

                    // Cargar los productos
                    this.cargarProductosPorEstado(estadoNombre);
                },

                // **NUEVO**: Configurar la apariencia del modal según el estado
                configurarModalEstado(estadoNombre) {
                    const configuraciones = {
                        'Óptimo': {
                            clase: 'alert-success',
                            icono: 'fas fa-check-circle',
                            descripcion: 'Productos con stock óptimo (más de 20 unidades)'
                        },
                        'Normal': {
                            clase: 'alert-info',
                            icono: 'fas fa-info-circle',
                            descripcion: 'Productos con stock normal (11-20 unidades)'
                        },
                        'Bajo': {
                            clase: 'alert-warning',
                            icono: 'fas fa-exclamation-triangle',
                            descripcion: 'Productos con stock bajo (6-10 unidades)'
                        },
                        'Crítico': {
                            clase: 'alert-danger',
                            icono: 'fas fa-exclamation-circle',
                            descripcion: 'Productos con stock crítico (5 o menos unidades)'
                        }
                    };

                    const config = configuraciones[estadoNombre];

                    // Actualizar elementos del modal
                    document.getElementById('alertaEstadoSeleccionado').className = `alert ${config.clase}`;
                    document.getElementById('iconoEstadoSeleccionado').className = `${config.icono} me-3 fs-4`;
                    document.getElementById('tituloEstadoSeleccionado').textContent = `Estado: ${estadoNombre}`;
                    document.getElementById('descripcionEstadoSeleccionado').textContent = config.descripcion;
                },

                // **NUEVO**: Cargar productos por estado desde el servidor
                cargarProductosPorEstado(estadoNombre) {
                    // Mostrar loading
                    document.getElementById('loadingProductosEstado').style.display = 'block';
                    document.getElementById('cuerpoTablaProductosEstado').style.display = 'none';
                    document.getElementById('estadoVacioProductos').style.display = 'none';

                    // Hacer petición AJAX
                    fetch(`${_URL}/ajs/dashboard/productos-por-estado?estado=${encodeURIComponent(estadoNombre)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.mostrarProductosEnTabla(data.productos, estadoNombre);
                            } else {
                                this.mostrarErrorProductos(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.mostrarErrorProductos('Error al cargar los productos');
                        })
                        .finally(() => {
                            document.getElementById('loadingProductosEstado').style.display = 'none';
                        });
                },

                // **NUEVO**: Mostrar productos en la tabla
                mostrarProductosEnTabla(productos, estadoNombre) {
                    const tbody = document.getElementById('cuerpoTablaProductosEstado');
                    const contador = document.getElementById('contadorProductosEstado');

                    if (productos.length === 0) {
                        document.getElementById('estadoVacioProductos').style.display = 'block';
                        tbody.style.display = 'none';
                        contador.textContent = '0 productos encontrados';
                        return;
                    }

                    // Generar filas de la tabla
                    let html = '';
                    productos.forEach(producto => {
                        const badgeClass = this.obtenerClaseBadgeEstado(estadoNombre);
                        const fechaFormateada = producto.fecha_registro ?
                            new Date(producto.fecha_registro).toLocaleDateString() : 'N/A';

                        html += `
                <tr>
                    <td><strong>${producto.codigo}</strong></td>
                    <td>${producto.nombre}</td>
                    <td>${producto.categoria || 'Sin categoría'}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${producto.stock} unidades
                        </span>
                    </td>
                    <td>S/ ${parseFloat(producto.precio || 0).toFixed(2)}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${estadoNombre}
                        </span>
                    </td>
                    <td>${fechaFormateada}</td>
                    
                 <!--<td>
                        <button class="btn btn-sm btn-outline-primary me-1" 
                                onclick="app.verDetalleProducto(${producto.id_producto})"
                                title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" 
                                onclick="app.verEstadisticasProducto(${producto.id_producto})"
                                title="Ver estadísticas">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    </td> -->
                </tr>
            `;
                    });

                    tbody.innerHTML = html;
                    tbody.style.display = 'table-row-group';
                    contador.textContent = `${productos.length} producto(s) encontrado(s)`;

                    // Configurar búsqueda en tiempo real
                    this.configurarBusquedaProductos(productos, estadoNombre);
                },

                // **NUEVO**: Obtener clase CSS para badges según el estado
                obtenerClaseBadgeEstado(estadoNombre) {
                    const clases = {
                        'Óptimo': 'bg-success',
                        'Normal': 'bg-info',
                        'Bajo': 'bg-warning',
                        'Crítico': 'bg-danger'
                    };
                    return clases[estadoNombre] || 'bg-secondary';
                },

                // **NUEVO**: Configurar búsqueda en tiempo real
                configurarBusquedaProductos(productosOriginales, estadoNombre) {
                    const inputBusqueda = document.getElementById('busquedaProductosEstado');

                    inputBusqueda.oninput = (e) => {
                        const termino = e.target.value.toLowerCase();
                        const productosFiltrados = productosOriginales.filter(producto =>
                            producto.nombre.toLowerCase().includes(termino) ||
                            producto.codigo.toLowerCase().includes(termino) ||
                            (producto.categoria && producto.categoria.toLowerCase().includes(termino))
                        );

                        this.mostrarProductosEnTabla(productosFiltrados, estadoNombre);
                    };
                },

                // **NUEVO**: Mostrar error al cargar productos
                mostrarErrorProductos(mensaje) {
                    const tbody = document.getElementById('cuerpoTablaProductosEstado');
                    tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${mensaje}
                </td>
            </tr>
        `;
                    tbody.style.display = 'table-row-group';
                },
                filtrarStock() {
                    if (!this.filtroStock.trim()) {
                        this.productosStockFiltrados = this.productosStock;
                        return;
                    }

                    this.productosStockFiltrados = this.productosStock.filter(producto => {
                        return producto.nombre.toLowerCase().includes(this.filtroStock.toLowerCase()) ||
                            producto.codigo.toLowerCase().includes(this.filtroStock.toLowerCase()) ||
                            (producto.categoria && producto.categoria.toLowerCase().includes(this.filtroStock.toLowerCase()));
                    });

                    // Actualizar gráficos con datos filtrados
                    this.actualizarGraficosStock();
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
                    }

                    this.loadingProductos = true;
                    this.cargarDatosProductosViaAjax(periodo);
                },

                abrirModalPersonalizadoProductos() {
                    if (!this.filtroFechasProductos.inicio || !this.filtroFechasProductos.fin) {
                        const ahora = new Date();
                        this.filtroFechasProductos.inicio = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth(), 1));
                        this.filtroFechasProductos.fin = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth() + 1, 0));
                    }
                    const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoModal'));
                    modal.show();
                },

                cargarDatosProductosViaAjax(periodo) {
                    let params = new URLSearchParams({ periodo: periodo, tipo: 'productos' });

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
                                    this.dashboardData[key] = data.productosData[key];
                                });

                                this.$nextTick(() => {
                                    this.inicializarGraficosProductos();
                                    this.loadingProductos = false;
                                });
                            } else {
                                alert('Error al cargar los datos: ' + data.message);
                                this.loadingProductos = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al cargar los datos');
                            this.loadingProductos = false;
                        });
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
                    }

                    this.loadingIngresos = true;
                    this.cargarDatosIngresosViaAjax(periodo);
                },

                abrirModalPersonalizadoIngresos() {
                    if (!this.filtroFechasIngresos.inicio || !this.filtroFechasIngresos.fin) {
                        const ahora = new Date();
                        this.filtroFechasIngresos.inicio = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth(), 1));
                        this.filtroFechasIngresos.fin = this.formatearFecha(new Date(ahora.getFullYear(), ahora.getMonth() + 1, 0));
                    }
                    const modal = new bootstrap.Modal(document.getElementById('periodoPersonalizadoModal'));
                    modal.show();
                },

                cargarDatosIngresosViaAjax(periodo) {
                    let params = new URLSearchParams({ periodo: periodo, tipo: 'ingresos-egresos' });

                    if (periodo === 'personalizado') {
                        params.append('fecha_inicio', this.filtroFechasIngresos.inicio);
                        params.append('fecha_fin', this.filtroFechasIngresos.fin);
                    }

                    fetch(`${_URL}/ajs/dashboard/datos-ingresos-egresos?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Actualizar datos de ingresos y egresos
                                Object.keys(data.ingresosEgresosData).forEach(key => {
                                    this.dashboardData[key] = data.ingresosEgresosData[key];
                                });

                                this.$nextTick(() => {
                                    this.inicializarGraficosIngresos();
                                    this.loadingIngresos = false;
                                });
                            } else {
                                alert('Error al cargar los datos: ' + data.message);
                                this.loadingIngresos = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al cargar los datos');
                            this.loadingIngresos = false;
                        });
                },


            },
            mounted() {
                console.log('Vue mounted, dashboard data inicial:', this.dashboardData);

                // Esperar a que Vue termine de renderizar
                this.$nextTick(() => {
                    setTimeout(() => {
                        console.log('Inicializando gráficos en mounted...');
                        this.inicializarGraficos();
                    }, 100);
                });
            }
        });
    </script>
</body>

</html>