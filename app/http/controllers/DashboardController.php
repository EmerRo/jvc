<?php

class DashboardController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function getClienteDetalle()
    {
        try {
            $id_cliente = $_GET['id'] ?? 0;
            // Para pruebas en Postman, usar un valor fijo
            $empresa = $_SESSION['id_empresa'] ?? 12;

            if (!$id_cliente) {
                throw new Exception('ID de cliente requerido');
            }

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            $sql = "SELECT 
                c.*,
                COUNT(v.id_venta) as num_compras,
                COALESCE(SUM(v.total), 0) as total_compras,
                MAX(v.fecha_emision) as ultima_venta,
                COALESCE(AVG(v.total), 0) as promedio_compra
            FROM clientes c
            LEFT JOIN ventas v ON c.id_cliente = v.id_cliente AND v.estado = '1'
            WHERE c.id_cliente = ? AND c.id_empresa = ?
            GROUP BY c.id_cliente";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta: ' . $this->conexion->error);
            }

            $stmt->bind_param("ii", $id_cliente, $empresa);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($cliente = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true,
                    'cliente' => $cliente
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'id_cliente' => $id_cliente ?? 'No definido',
                    'empresa' => $empresa ?? 'No definido',
                    'session' => $_SESSION ?? 'No session'
                ]
            ]);
        }
    }

    public function getClienteEstadisticas()
    {
        try {
            $id_cliente = $_GET['id'] ?? 0;
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $anio_actual = date('Y');

            if (!$id_cliente) {
                throw new Exception('ID de cliente requerido');
            }

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            $sql = "SELECT 
                MONTH(fecha_emision) as mes,
                SUM(total) as total_mes,
                COUNT(*) as num_ventas
            FROM ventas 
            WHERE id_cliente = ? 
                AND id_empresa = ? 
                AND estado = '1' 
                AND YEAR(fecha_emision) = ?
            GROUP BY MONTH(fecha_emision)
            ORDER BY mes";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta: ' . $this->conexion->error);
            }

            $stmt->bind_param("iii", $id_cliente, $empresa, $anio_actual);
            $stmt->execute();
            $result = $stmt->get_result();

            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $montos = array_fill(0, 12, 0);
            $total_anual = 0;
            $mejor_mes = '';
            $max_monto = 0;

            while ($row = $result->fetch_assoc()) {
                $montos[$row['mes'] - 1] = floatval($row['total_mes']);
                $total_anual += $row['total_mes'];

                if ($row['total_mes'] > $max_monto) {
                    $max_monto = $row['total_mes'];
                    $mejor_mes = $meses[$row['mes'] - 1];
                }
            }

            // Obtener compra máxima
            $sql_max = "SELECT MAX(total) as compra_maxima FROM ventas WHERE id_cliente = ? AND id_empresa = ? AND estado = '1'";
            $stmt_max = $this->conexion->prepare($sql_max);
            $stmt_max->bind_param("ii", $id_cliente, $empresa);
            $stmt_max->execute();
            $compra_maxima = $stmt_max->get_result()->fetch_assoc()['compra_maxima'] ?? 0;

            echo json_encode([
                'success' => true,
                'estadisticas' => [
                    'total_anual' => $total_anual,
                    'mejor_mes' => $mejor_mes ?: 'Sin datos',
                    'compra_maxima' => $compra_maxima
                ],
                'grafico' => [
                    'meses' => $meses,
                    'montos' => $montos
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'id_cliente' => $id_cliente ?? 'No definido',
                    'empresa' => $empresa ?? 'No definido',
                    'anio_actual' => $anio_actual ?? 'No definido'
                ]
            ]);
        }
    }

    public function getProductoDetalle()
    {
        try {
            $id_producto = $_GET['id'] ?? 0;
            $empresa = $_SESSION['id_empresa'] ?? 12;

            if (!$id_producto) {
                throw new Exception('ID de producto requerido');
            }

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            $sql = "SELECT 
                p.*,
                COALESCE(SUM(pv.cantidad), 0) as total_vendido,
                COALESCE(SUM(pv.precio * pv.cantidad), 0) as total_ventas,
                COALESCE(AVG(pv.cantidad), 0) as promedio_mensual
            FROM productos p
            LEFT JOIN productos_ventas pv ON p.id_producto = pv.id_producto
            LEFT JOIN ventas v ON pv.id_venta = v.id_venta AND v.estado = '1' AND v.id_empresa = ?
            WHERE p.id_producto = ? AND p.id_empresa = ?
            GROUP BY p.id_producto";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta: ' . $this->conexion->error);
            }

            $stmt->bind_param("iii", $empresa, $id_producto, $empresa);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($producto = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true,
                    'producto' => $producto
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'id_producto' => $id_producto ?? 'No definido',
                    'empresa' => $empresa ?? 'No definido'
                ]
            ]);
        }
    }

    public function getProductoEstadisticas()
    {
        try {
            $id_producto = $_GET['id'] ?? 0;
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $anio_actual = date('Y');

            if (!$id_producto) {
                throw new Exception('ID de producto requerido');
            }

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            $sql = "SELECT 
                MONTH(v.fecha_emision) as mes,
                SUM(pv.cantidad) as total_mes
            FROM productos_ventas pv
            JOIN ventas v ON pv.id_venta = v.id_venta
            WHERE pv.id_producto = ? 
                AND v.id_empresa = ? 
                AND v.estado = '1' 
                AND YEAR(v.fecha_emision) = ?
            GROUP BY MONTH(v.fecha_emision)
            ORDER BY mes";

            $stmt = $this->conexion->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta: ' . $this->conexion->error);
            }

            $stmt->bind_param("iii", $id_producto, $empresa, $anio_actual);
            $stmt->execute();
            $result = $stmt->get_result();

            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $cantidades = array_fill(0, 12, 0);

            while ($row = $result->fetch_assoc()) {
                $cantidades[$row['mes'] - 1] = intval($row['total_mes']);
            }

            echo json_encode([
                'success' => true,
                'estadisticas' => [
                    'total_anual' => array_sum($cantidades)
                ],
                'grafico' => [
                    'meses' => $meses,
                    'cantidades' => $cantidades
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'id_producto' => $id_producto ?? 'No definido',
                    'empresa' => $empresa ?? 'No definido',
                    'anio_actual' => $anio_actual ?? 'No definido'
                ]
            ]);
        }
    }

    public function vendedoresMetasAction()
    {
        // HARDCODEADO para testing
        $empresa = 12;
        $mes_actual = date('m');
        $anio_actual = date('Y');

        try {
            $conexion = (new Conexion())->getConexion();

            // 1. Obtener la META TOTAL de la empresa
            $sql_meta_total = "SELECT meta_total 
                              FROM metas_empresa 
                              WHERE id_empresa = ? 
                              AND mes = ? 
                              AND anio = ? 
                              AND estado = '1'
                              ORDER BY fecha_creacion DESC 
                              LIMIT 1";

            $stmt = $conexion->prepare($sql_meta_total);
            $stmt->bind_param("iii", $empresa, $mes_actual, $anio_actual);
            $stmt->execute();
            $result_meta = $stmt->get_result();
            $meta_empresa = $result_meta->fetch_assoc();

            $meta_total = $meta_empresa ? floatval($meta_empresa['meta_total']) : 0;

            // 2. CORREGIDO: Obtener TODOS los usuarios que tienen ventas (incluye admin)
            $sql_vendedores = "SELECT 
                                u.usuario_id,
                                u.usuario,
                                u.nombres,
                                u.apellidos,
                                u.id_rol,
                                SUM(v.total) as ventas_actuales
                              FROM ventas v
                              JOIN usuarios u ON v.id_vendedor = u.usuario_id
                              WHERE v.id_empresa = ? 
                                AND v.estado = '1'
                                AND MONTH(v.fecha_emision) = ?
                                AND YEAR(v.fecha_emision) = ?
                                AND u.estado = '1'
                              GROUP BY u.usuario_id
                              HAVING ventas_actuales > 0
                              ORDER BY ventas_actuales DESC";

            $stmt = $conexion->prepare($sql_vendedores);
            $stmt->bind_param("iii", $empresa, $mes_actual, $anio_actual);
            $stmt->execute();
            $result_vendedores = $stmt->get_result();

            $vendedores = [];
            $total_ventas_mes = 0;

            while ($vendedor = $result_vendedores->fetch_assoc()) {
                $ventas_actuales = floatval($vendedor['ventas_actuales']);
                $total_ventas_mes += $ventas_actuales;

                // Calcular porcentaje de contribución a la meta total
                $porcentaje_contribucion = $meta_total > 0 ? ($ventas_actuales / $meta_total) * 100 : 0;

                // Determinar el tipo de usuario
                $tipo_usuario = '';
                switch ($vendedor['id_rol']) {
                    case 1:
                        $tipo_usuario = 'Administrador';
                        break;
                    case 7:
                        $tipo_usuario = 'Vendedor';
                        break;
                    case 8:
                        $tipo_usuario = 'Vendedor Senior';
                        break;
                    default:
                        $tipo_usuario = 'Usuario';
                        break;
                }

                $vendedores[] = [
                    'usuario_id' => $vendedor['usuario_id'],
                    'usuario' => $vendedor['usuario'],
                    'nombres' => $vendedor['nombres'],
                    'apellidos' => $vendedor['apellidos'],
                    'tipo_usuario' => $tipo_usuario,
                    'id_rol' => $vendedor['id_rol'],
                    'ventas_actuales' => $ventas_actuales,
                    'meta_total_empresa' => $meta_total,
                    'porcentaje_contribucion' => $porcentaje_contribucion,
                    'posicion' => 0 // Se calculará después
                ];
            }

            // Asignar posiciones basadas en ventas
            for ($i = 0; $i < count($vendedores); $i++) {
                $vendedores[$i]['posicion'] = $i + 1;
            }

            // 3. Calcular resumen
            $progreso_total = $meta_total > 0 ? ($total_ventas_mes / $meta_total) * 100 : 0;
            $vendedores_activos = count($vendedores);

            $resumen = [
                'meta_total_empresa' => $meta_total,
                'total_ventas_mes' => $total_ventas_mes,
                'progreso_total' => $progreso_total,
                'vendedores_activos' => $vendedores_activos,
                'meta_alcanzada' => $progreso_total >= 100,
                'dias_restantes' => date('t') - date('j')
            ];

            return json_encode([
                'success' => true,
                'vendedores' => $vendedores,
                'resumen' => $resumen,
                'tiene_meta' => $meta_total > 0,
                'debug' => [
                    'empresa' => $empresa,
                    'mes_actual' => $mes_actual,
                    'anio_actual' => $anio_actual,
                    'meta_total' => $meta_total,
                    'total_vendedores_encontrados' => count($vendedores),
                    'consulta_explicacion' => 'Ahora incluye TODOS los usuarios con ventas, no solo roles 7 y 8'
                ]
            ]);

        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }

    public function guardarMetaTotalAction()
    {
        // HARDCODEADO para testing
        $empresa = 12;
        // $empresa = $_SESSION['id_empresa'];

        try {
            $mes = intval($_POST['mes']);
            $anio = intval($_POST['anio']);
            $meta_total = floatval($_POST['meta_total']);

            if ($meta_total <= 0) {
                return json_encode([
                    'success' => false,
                    'message' => 'La meta debe ser mayor a 0'
                ]);
            }

            $conexion = (new Conexion())->getConexion();

            // Verificar si ya existe una meta para este mes/año
            $sql_check = "SELECT id_meta_empresa FROM metas_empresa 
                         WHERE id_empresa = ? AND mes = ? AND anio = ? AND estado = '1'";
            $stmt = $conexion->prepare($sql_check);
            $stmt->bind_param("iii", $empresa, $mes, $anio);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();

            if ($existing) {
                // Actualizar meta existente
                $sql_update = "UPDATE metas_empresa 
                              SET meta_total = ?, fecha_actualizacion = NOW() 
                              WHERE id_meta_empresa = ?";
                $stmt = $conexion->prepare($sql_update);
                $stmt->bind_param("di", $meta_total, $existing['id_meta_empresa']);
                $stmt->execute();
            } else {
                // Crear nueva meta
                $sql_insert = "INSERT INTO metas_empresa (id_empresa, meta_total, mes, anio, fecha_creacion, estado) 
                              VALUES (?, ?, ?, ?, NOW(), '1')";
                $stmt = $conexion->prepare($sql_insert);
                $stmt->bind_param("idii", $empresa, $meta_total, $mes, $anio);
                $stmt->execute();
            }

            // Contar vendedores activos (SIN SUCURSAL)
            $sql_vendedores = "SELECT COUNT(DISTINCT u.usuario_id) as total_vendedores
                              FROM usuarios u 
                              WHERE u.id_empresa = ? 
                              AND u.estado = '1' 
                              AND u.id_rol IN (7, 8)";
            $stmt = $conexion->prepare($sql_vendedores);
            $stmt->bind_param("i", $empresa);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $total_vendedores = $result['total_vendedores'];

            $meta_promedio = $total_vendedores > 0 ? $meta_total / $total_vendedores : 0;

            return json_encode([
                'success' => true,
                'message' => 'Meta total establecida correctamente',
                'meta_total' => number_format($meta_total, 2),
                'total_vendedores' => $total_vendedores,
                'meta_promedio' => number_format($meta_promedio, 2)
            ]);

        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'message' => 'Error al guardar la meta: ' . $e->getMessage()
            ]);
        }
    }

    // Nuevo método para obtener datos del dashboard filtrados por fecha
    public function getDatos()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $sucursal = $_SESSION['sucursal'] ?? 1;

            $periodo_actual = $_GET['periodo'] ?? 'mes';
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;

            // Configurar fechas y textos según el período
            $ahora = new DateTime();
            $textos_periodo = [];
            $categorias_grafico = [];

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
                    $diaSemana = $ahora->format('N');
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
                        $fecha_inicio_formato = $inicio->format('d/m/Y');
                        $fecha_fin_formato = $fin->format('d/m/Y');

                        $textos_periodo = [
                            'titulo_principal' => "Ventas del $fecha_inicio_formato - $fecha_fin_formato",
                            'comparativa' => 'vs. Período Anterior',
                            'periodo_comparativo' => 'periodo_anterior'
                        ];
                        $categorias_grafico = ['Período Personalizado'];
                        $periodo_actual = 'personalizado';
                    }
                    break;
            }

            // **CORREGIR**: Configuración de fechas para comparativas
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

            $conexion = (new Conexion())->getConexion();

            // **CORREGIR**: Consulta principal con fechas comparativas correctas
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

            // **AGREGAR**: Generar datos para el gráfico según el período (usa las funciones del archivo original)
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

            // **AGREGAR**: Función para utilidad bruta
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

            $dataListVen = generarDatosGrafico($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion);
            $dataUtilidadBruta = generarDatosUtilidadBruta($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion);

            // Productos más vendidos (mantener igual pero con las fechas correctas)
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
          AND v.sucursal = '$sucursal'
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

            // Ingresos y Egresos
            $ingresos_mensuales = $data["totalv"] ?? 0;
            $egresos_mensuales = $ingresos_mensuales * 0.6;
            $ganancia_mensual = $ingresos_mensuales - $egresos_mensuales;

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

            // **CORREGIR**: Estructura de respuesta completa
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
                // **AGREGAR**: campos de utilidad bruta que faltaban
                'utilidadBrutaActual' => $data["utilidad_bruta_actual"] ?? 0,
                'utilidadBrutaAnterior' => $data["utilidad_bruta_anterior"] ?? 0,
                'utilidadBrutaPorPeriodo' => $dataUtilidadBruta
            ];

            echo json_encode([
                'success' => true,
                'dashboardData' => $dashboardData,
                'debug' => [
                    'periodo' => $periodo_actual,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'fecha_inicio_comparativa' => $fecha_inicio_comparativa,
                    'fecha_fin_comparativa' => $fecha_fin_comparativa
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }
    public function getProductosPorEstado()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $estado = $_GET['estado'] ?? '';

            if (!$estado) {
                throw new Exception('Estado requerido');
            }

            // Definir rangos de stock según el estado
            $condicionStock = '';
            switch ($estado) {
                case 'Óptimo':
                    $condicionStock = 'p.cantidad > 20';
                    break;
                case 'Normal':
                    $condicionStock = 'p.cantidad BETWEEN 11 AND 20';
                    break;
                case 'Bajo':
                    $condicionStock = 'p.cantidad BETWEEN 6 AND 10';
                    break;
                case 'Crítico':
                    $condicionStock = 'p.cantidad <= 5';
                    break;
                default:
                    throw new Exception('Estado no válido');
            }

            $sql = "SELECT 
                    p.id_producto,
                    p.codigo,
                    COALESCE(p.nombre, p.detalle) as nombre,
                    p.detalle,
                    p.cantidad as stock,
                    p.precio,
                    p.fecha_registro,
                    c.nombre as categoria
                FROM productos p
               LEFT JOIN categorias c ON p.categoria = c.id
                WHERE p.id_empresa = ? 
                    AND p.estado = '1'
                    AND $condicionStock
                ORDER BY p.cantidad ASC, p.nombre ASC";

            // PREPARACIÓN DE LA CONSULTA CON VALIDACIÓN
            $stmt = $this->conexion->prepare($sql);

            // ¡VALIDACIÓN CRUCIAL AQUÍ!
            if ($stmt === false) {
                throw new Exception("Error al preparar consulta: " . $this->conexion->error);
            }

            $stmt->bind_param("i", $empresa);
            $stmt->execute();
            $result = $stmt->get_result();

            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }

            echo json_encode([
                'success' => true,
                'productos' => $productos,
                'estado' => $estado,
                'total' => count($productos)
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getEstadisticasStock()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;

            $sql = "SELECT 
            SUM(CASE WHEN cantidad <= 5 THEN 1 ELSE 0 END) as critico,
            SUM(CASE WHEN cantidad BETWEEN 6 AND 10 THEN 1 ELSE 0 END) as bajo,
            SUM(CASE WHEN cantidad BETWEEN 11 AND 20 THEN 1 ELSE 0 END) as normal,
            SUM(CASE WHEN cantidad > 20 THEN 1 ELSE 0 END) as optimo,
            COUNT(*) as total
        FROM productos 
        WHERE id_empresa = ? AND estado = '1'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $empresa);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $total = $result['total'];

            echo json_encode([
                'success' => true,
                'estadisticas' => [
                    'critico' => [
                        'cantidad' => $result['critico'],
                        'porcentaje' => $total > 0 ? round(($result['critico'] / $total) * 100, 1) : 0
                    ],
                    'bajo' => [
                        'cantidad' => $result['bajo'],
                        'porcentaje' => $total > 0 ? round(($result['bajo'] / $total) * 100, 1) : 0
                    ],
                    'normal' => [
                        'cantidad' => $result['normal'],
                        'porcentaje' => $total > 0 ? round(($result['normal'] / $total) * 100, 1) : 0
                    ],
                    'optimo' => [
                        'cantidad' => $result['optimo'],
                        'porcentaje' => $total > 0 ? round(($result['optimo'] / $total) * 100, 1) : 0
                    ]
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function getDatosProductos()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $sucursal = $_SESSION['sucursal'] ?? 1;

            $periodo_actual = $_GET['periodo'] ?? 'mes';
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;

            // Configurar fechas según el período (similar a getDatos)
            $ahora = new DateTime();

            switch ($periodo_actual) {
                case 'hoy':
                    $fecha_inicio = $ahora->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;

                case 'semana':
                    $diaSemana = $ahora->format('N');
                    $inicioSemana = clone $ahora;
                    $inicioSemana->modify('-' . ($diaSemana - 1) . ' days');
                    $fecha_inicio = $inicioSemana->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;

                case 'mes':
                    $inicioMes = new DateTime($ahora->format('Y-m-01'));
                    $fecha_inicio = $inicioMes->format('Y-m-d');
                    $finMes = new DateTime($ahora->format('Y-m-t'));
                    $fecha_fin = $finMes->format('Y-m-d');
                    break;

                case 'anio':
                    $inicioAnio = new DateTime($ahora->format('Y-01-01'));
                    $fecha_inicio = $inicioAnio->format('Y-m-d');
                    $finAnio = new DateTime($ahora->format('Y-12-31'));
                    $fecha_fin = $finAnio->format('Y-m-d');
                    break;
            }

            // Fechas para comparativa
            $fecha_inicio_comparativa = '';
            $fecha_fin_comparativa = '';

            switch ($periodo_actual) {
                case 'hoy':
                    $ayer = clone $ahora;
                    $ayer->modify('-1 day');
                    $fecha_inicio_comparativa = $ayer->format('Y-m-d');
                    $fecha_fin_comparativa = $ayer->format('Y-m-d');
                    break;

                case 'semana':
                    $inicioSemanaAnterior = clone $ahora;
                    $inicioSemanaAnterior->modify('-1 week')->modify('-' . ($ahora->format('N') - 1) . ' days');
                    $finSemanaAnterior = clone $inicioSemanaAnterior;
                    $finSemanaAnterior->modify('+6 days');
                    $fecha_inicio_comparativa = $inicioSemanaAnterior->format('Y-m-d');
                    $fecha_fin_comparativa = $finSemanaAnterior->format('Y-m-d');
                    break;

                case 'mes':
                    $mesAnterior = clone $ahora;
                    $mesAnterior->modify('-1 month');
                    $fecha_inicio_comparativa = $mesAnterior->format('Y-m-01');
                    $fecha_fin_comparativa = $mesAnterior->format('Y-m-t');
                    break;

                case 'anio':
                    $anioAnterior = clone $ahora;
                    $anioAnterior->modify('-1 year');
                    $fecha_inicio_comparativa = $anioAnterior->format('Y-01-01');
                    $fecha_fin_comparativa = $anioAnterior->format('Y-12-31');
                    break;
            }

            $conexion = (new Conexion())->getConexion();

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
            AND v.sucursal = '$sucursal'
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
            $total_productos_vendidos = 0;

            if ($productos_top && $productos_top->num_rows > 0) {
                while ($producto = $productos_top->fetch_assoc()) {
                    $productos_nombres[] = trim(str_replace(["\t", "\n", "\r"], '', $producto['nombre']));
                    $productos_cantidades[] = intval($producto['total_vendido']);
                    $total_productos_vendidos += intval($producto['total_vendido']);
                }
            }


            // Productos por categoría - SIN LÍMITE para mostrar todas las categorías
            $sql_categorias = "SELECT 
    c.nombre as categoria,
    SUM(pv.cantidad) as total_vendido
FROM 
    productos_ventas pv
JOIN 
    productos p ON pv.id_producto = p.id_producto
JOIN 
    categorias c ON p.categoria = c.id
JOIN 
    ventas v ON pv.id_venta = v.id_venta
WHERE 
    v.id_empresa = '$empresa' 
    AND v.estado = '1'
    AND v.sucursal = '$sucursal'
    AND v.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
GROUP BY 
    c.id, c.nombre
HAVING 
    total_vendido > 0
ORDER BY 
    total_vendido DESC";

            // Productos por categoría - PERÍODO ANTERIOR para comparativa
            $sql_categorias_anterior = "SELECT 
    c.nombre as categoria,
    SUM(pv.cantidad) as total_vendido_anterior
FROM 
    productos_ventas pv
JOIN 
    productos p ON pv.id_producto = p.id_producto
JOIN 
    categorias c ON p.categoria = c.id
JOIN 
    ventas v ON pv.id_venta = v.id_venta
WHERE 
    v.id_empresa = '$empresa' 
    AND v.estado = '1'
    AND v.sucursal = '$sucursal'
    AND v.fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa'
GROUP BY 
    c.id, c.nombre
HAVING 
    total_vendido_anterior > 0
ORDER BY 
    total_vendido_anterior DESC";


            $categorias_result = $conexion->query($sql_categorias);
            $categorias_anterior_result = $conexion->query($sql_categorias_anterior);

            $categorias = [];
            $productos_por_categoria = [];
            $productos_por_categoria_anterior = [];
            $total_categorias_activas = 0;

            // Procesar categorías actuales
            if ($categorias_result && $categorias_result->num_rows > 0) {
                while ($cat = $categorias_result->fetch_assoc()) {
                    $categorias[] = $cat['categoria'];
                    $productos_por_categoria[] = intval($cat['total_vendido']);
                    $total_categorias_activas++;
                }
            }

            // Procesar categorías del período anterior
            $categorias_anteriores = [];
            if ($categorias_anterior_result && $categorias_anterior_result->num_rows > 0) {
                while ($cat_ant = $categorias_anterior_result->fetch_assoc()) {
                    $categorias_anteriores[$cat_ant['categoria']] = intval($cat_ant['total_vendido_anterior']);
                }
            }

            // Alinear datos para comparativa (asegurar que ambos arrays tengan las mismas categorías)
            foreach ($categorias as $categoria) {
                $productos_por_categoria_anterior[] = isset($categorias_anteriores[$categoria]) ? $categorias_anteriores[$categoria] : 0;
            }


            // Comparativa con período anterior
            $sql_anterior = "SELECT 
            SUM(pv.cantidad) as total_anterior
        FROM 
            productos_ventas pv
        JOIN 
            ventas v ON pv.id_venta = v.id_venta
        WHERE 
            v.id_empresa = '$empresa' 
            AND v.estado = '1'
            AND v.sucursal = '$sucursal'
            AND v.fecha_emision BETWEEN '$fecha_inicio_comparativa' AND '$fecha_fin_comparativa'";

            $result_anterior = $conexion->query($sql_anterior);
            $total_productos_vendidos_anterior = 0;

            if ($result_anterior) {
                $row_anterior = $result_anterior->fetch_assoc();
                $total_productos_vendidos_anterior = intval($row_anterior['total_anterior'] ?? 0);
            }

            // Rotación promedio (simulada)
            $rotacion_promedio = $total_productos_vendidos > 0 ? round(30 / ($total_productos_vendidos / 10), 1) : 0;

            $productosData = [
                'productosNombres' => $productos_nombres,
                'productosCantidades' => $productos_cantidades,
                'categorias' => $categorias,
                'productosPorCategoria' => $productos_por_categoria,
                'productosPorCategoriaAnterior' => $productos_por_categoria_anterior, // NUEVO
                'totalProductosVendidos' => $total_productos_vendidos,
                'totalProductosVendidosAnterior' => $total_productos_vendidos_anterior,
                'totalCategoriasActivas' => $total_categorias_activas,
                'rotacionPromedio' => $rotacion_promedio
            ];


            echo json_encode([
                'success' => true,
                'productosData' => $productosData,
                'debug' => [
                    'periodo' => $periodo_actual,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos de productos: ' . $e->getMessage()
            ]);
        }
    }
  public function getDatosIngresosEgresos()
{
    try {
        $empresa = $_SESSION['id_empresa'] ?? 12;
        $sucursal = $_SESSION['sucursal'] ?? 1;

        $periodo_actual = $_GET['periodo'] ?? 'mes';
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;

        // Configurar fechas según el período (reutilizar lógica existente)
        $ahora = new DateTime();

        switch ($periodo_actual) {
            case 'hoy':
                $fecha_inicio = $ahora->format('Y-m-d');
                $fecha_fin = $ahora->format('Y-m-d');
                break;

            case 'semana':
                $diaSemana = $ahora->format('N');
                $inicioSemana = clone $ahora;
                $inicioSemana->modify('-' . ($diaSemana - 1) . ' days');
                $fecha_inicio = $inicioSemana->format('Y-m-d');
                $fecha_fin = $ahora->format('Y-m-d');
                break;

            case 'mes':
                $inicioMes = new DateTime($ahora->format('Y-m-01'));
                $fecha_inicio = $inicioMes->format('Y-m-d');
                $finMes = new DateTime($ahora->format('Y-m-t'));
                $fecha_fin = $finMes->format('Y-m-d');
                break;

            case 'anio':
                $inicioAnio = new DateTime($ahora->format('Y-01-01'));
                $fecha_inicio = $inicioAnio->format('Y-m-d');
                $finAnio = new DateTime($ahora->format('Y-12-31'));
                $fecha_fin = $finAnio->format('Y-m-d');
                break;
        }

        $conexion = (new Conexion())->getConexion();

        // Obtener datos reales de ingresos (ventas) y egresos (compras)
        $sql_ingresos = "SELECT SUM(total) as total_ingresos 
                        FROM ventas 
                        WHERE id_empresa='$empresa' 
                        AND estado = '1' 
                        AND sucursal='$sucursal' 
                        AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        $sql_egresos = "SELECT SUM(total) as total_egresos 
                       FROM compras 
                       WHERE id_empresa='$empresa' 
                       AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        $ingresos_result = $conexion->query($sql_ingresos);
        $egresos_result = $conexion->query($sql_egresos);

        $ingresos_data = $ingresos_result->fetch_assoc();
        $egresos_data = $egresos_result->fetch_assoc();

        $ingresos_mensuales = $ingresos_data['total_ingresos'] ?? 0;
        $egresos_mensuales = $egresos_data['total_egresos'] ?? 0;
        $ganancia_mensual = $ingresos_mensuales - $egresos_mensuales;

        // Generar datos por período (reutilizar funciones existentes)
        $dataIngresos = $this->generarDatosIngresosPorPeriodo($periodo_actual, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion);
        $dataEgresos = $this->generarDatosEgresosPorPeriodo($periodo_actual, $fecha_inicio, $fecha_fin, $empresa, $conexion);

        $ingresosEgresosData = [
            'ingresosMensuales' => $ingresos_mensuales,
            'egresosMensuales' => $egresos_mensuales,
            'gananciaMensual' => $ganancia_mensual,
            'ingresosPorPeriodo' => $dataIngresos,
            'egresosPorPeriodo' => $dataEgresos
        ];

        echo json_encode([
            'success' => true,
            'ingresosData' => $ingresosEgresosData
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener datos de ingresos y egresos: ' . $e->getMessage()
        ]);
    }
}

    public function getDatosCotizaciones()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $sucursal = $_SESSION['sucursal'] ?? 1;

            // Obtener parámetros de filtro
            $periodo = $_GET['periodo'] ?? 'mes';
            $fecha_inicio = $_GET['fecha_inicio'] ?? '';
            $fecha_fin = $_GET['fecha_fin'] ?? '';

            // Determinar fechas según el período
            $ahora = new DateTime();
            switch ($periodo) {
                case 'hoy':
                    $fecha_inicio = $ahora->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;
                case 'mes':
                    $fecha_inicio = $ahora->format('Y-m-01');
                    $fecha_fin = $ahora->format('Y-m-t');
                    break;
                case 'trimestre':
                    $mes_actual = $ahora->format('n');
                    $trimestre = ceil($mes_actual / 3);
                    $primer_mes_trimestre = ($trimestre - 1) * 3 + 1;
                    $fecha_inicio = $ahora->format('Y-' . sprintf('%02d', $primer_mes_trimestre) . '-01');
                    $ultimo_mes_trimestre = $primer_mes_trimestre + 2;
                    $fecha_fin = $ahora->format('Y-' . sprintf('%02d', $ultimo_mes_trimestre) . '-t');
                    break;
                case 'semestre':
                    $mes_actual = $ahora->format('n');
                    if ($mes_actual <= 6) {
                        $fecha_inicio = $ahora->format('Y-01-01');
                        $fecha_fin = $ahora->format('Y-06-30');
                    } else {
                        $fecha_inicio = $ahora->format('Y-07-01');
                        $fecha_fin = $ahora->format('Y-12-31');
                    }
                    break;
                case 'año':
                    $fecha_inicio = $ahora->format('Y-01-01');
                    $fecha_fin = $ahora->format('Y-12-31');
                    break;
                default: // personalizado
                    if (!$fecha_inicio || !$fecha_fin) {
                        $fecha_inicio = $ahora->format('Y-m-01');
                        $fecha_fin = $ahora->format('Y-m-t');
                    }
                    break;
            }

            $conexion = (new Conexion())->getConexion();

            // 1. Total de cotizaciones
            $sql_total = "SELECT COUNT(*) as total_cotizaciones 
                         FROM cotizaciones 
                         WHERE id_empresa = ? AND fecha BETWEEN ? AND ?";
            $stmt_total = $conexion->prepare($sql_total);
            
            if (!$stmt_total) {
                throw new Exception('Error en consulta total_cotizaciones: ' . $conexion->error);
            }
            
            $stmt_total->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_total->execute();
            $total_cotizaciones = $stmt_total->get_result()->fetch_assoc()['total_cotizaciones'];
            

            // 2. Productos más cotizados
            $sql_productos = "SELECT 
                                p.id_producto,
                                p.nombre,
                                COUNT(pc.id_producto) as total_cotizado,
                                AVG(pc.precio) as precio_promedio
                             FROM productos_cotis pc
                             INNER JOIN productos p ON pc.id_producto = p.id_producto
                             INNER JOIN cotizaciones c ON pc.id_coti = c.cotizacion_id
                             WHERE c.id_empresa = ? AND c.fecha BETWEEN ? AND ?
                             GROUP BY p.id_producto, p.nombre
                             ORDER BY total_cotizado DESC
                             LIMIT 10";
            
            $stmt_productos = $conexion->prepare($sql_productos);
            
            if (!$stmt_productos) {
                throw new Exception('Error en consulta productos_mas_cotizados: ' . $conexion->error);
            }
            
            $stmt_productos->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_productos->execute();
            $result_productos = $stmt_productos->get_result();

            $productos_mas_cotizados = [];
            while ($producto = $result_productos->fetch_assoc()) {
                $productos_mas_cotizados[] = [
                    'id_producto' => $producto['id_producto'],
                    'nombre' => $producto['nombre'],
                    'total_cotizado' => $producto['total_cotizado'],
                    'precio_promedio' => $producto['precio_promedio']
                ];
            }

            // 3. Top clientes con más cotizaciones
            $sql_clientes_top = "SELECT 
                                   cl.datos,
                                   COUNT(c.cotizacion_id) as total_cotizaciones,
                                   SUM(c.total) as valor_total
                                FROM cotizaciones c
                                INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
                                WHERE c.id_empresa = ? AND c.fecha BETWEEN ? AND ?
                                GROUP BY c.id_cliente, cl.datos
                                ORDER BY total_cotizaciones DESC
                                LIMIT 5";
            
            $stmt_clientes = $conexion->prepare($sql_clientes_top);
            
            if (!$stmt_clientes) {
                throw new Exception('Error en consulta clientes_top: ' . $conexion->error);
            }
            
            $stmt_clientes->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_clientes->execute();
            $result_clientes = $stmt_clientes->get_result();

            $top_clientes = [];
            while ($cliente = $result_clientes->fetch_assoc()) {
                $top_clientes[] = [
                    'datos' => $cliente['datos'],
                    'total_cotizaciones' => $cliente['total_cotizaciones'],
                    'valor_total' => $cliente['valor_total']
                ];
            }

            // 4. Estados de cotizaciones
            $sql_estados = "SELECT 
                              CASE 
                                WHEN estado = '0' THEN 'Pendiente'
                                WHEN estado = '1' THEN 'Vendida'
                                WHEN estado = '2' THEN 'Enviada'
                                ELSE 'Otros'
                              END as estado_nombre,
                              COUNT(*) as cantidad
                            FROM cotizaciones
                            WHERE id_empresa = ? AND fecha BETWEEN ? AND ?
                            GROUP BY estado";
            
            $stmt_estados = $conexion->prepare($sql_estados);
            
            if (!$stmt_estados) {
                throw new Exception('Error en consulta estados: ' . $conexion->error);
            }
            
            $stmt_estados->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_estados->execute();
            $result_estados = $stmt_estados->get_result();

            $estados_cotizaciones = [];
            while ($estado = $result_estados->fetch_assoc()) {
                $estados_cotizaciones[] = $estado;
            }

            // 5. Evolución mensual (para gráfico)
            $sql_evolucion = "SELECT 
                                MONTH(fecha) as mes,
                                COUNT(*) as total_mes
                             FROM cotizaciones
                             WHERE id_empresa = ? AND YEAR(fecha) = YEAR(CURDATE())
                             GROUP BY MONTH(fecha)
                             ORDER BY mes";
            
            $stmt_evolucion = $conexion->prepare($sql_evolucion);
            
            if (!$stmt_evolucion) {
                throw new Exception('Error en consulta evolucion: ' . $conexion->error);
            }
            
            $stmt_evolucion->bind_param("i", $empresa);
            $stmt_evolucion->execute();
            $result_evolucion = $stmt_evolucion->get_result();

            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $cotizaciones_por_mes = array_fill(0, 12, 0);

            while ($row = $result_evolucion->fetch_assoc()) {
                $cotizaciones_por_mes[$row['mes'] - 1] = intval($row['total_mes']);
            }

            // 6. Cotizaciones recientes (usando la vista que ya tiene el JOIN correcto)
            $sql_recientes = "SELECT 
                                cotizacion_id,
                                numero,
                                fecha,
                                total,
                                estado,
                                datos as cliente_datos,
                                vendedor
                             FROM view_cotizaciones
                             ORDER BY fecha DESC, cotizacion_id DESC
                             LIMIT 10";
            
            $stmt_recientes = $conexion->prepare($sql_recientes);
            
            if (!$stmt_recientes) {
                throw new Exception('Error en consulta recientes: ' . $conexion->error);
            }
            
            $stmt_recientes->execute();
            $result_recientes = $stmt_recientes->get_result();

            $cotizaciones_recientes = [];
            while ($cotizacion = $result_recientes->fetch_assoc()) {
                $cotizaciones_recientes[] = $cotizacion;
            }

            echo json_encode([
                'success' => true,
                'cotizaciones' => [
                    'total_cotizaciones' => $total_cotizaciones,
                    'productos_mas_cotizados' => $productos_mas_cotizados,
                    'top_clientes' => $top_clientes,
                    'estados_cotizaciones' => $estados_cotizaciones,
                    'evolucion_mensual' => [
                        'meses' => $meses,
                        'cantidades' => $cotizaciones_por_mes
                    ],
                    'cotizaciones_recientes' => $cotizaciones_recientes
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos de cotizaciones: ' . $e->getMessage()
            ]);
        }
    }

    public function getDatosGuias()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $sucursal = $_SESSION['sucursal'] ?? 1;

            // Obtener parámetros de filtro
            $periodo = $_GET['periodo'] ?? 'mes';
            $fecha_inicio = $_GET['fecha_inicio'] ?? '';
            $fecha_fin = $_GET['fecha_fin'] ?? '';

            // Determinar fechas según el período
            $ahora = new DateTime();
            switch ($periodo) {
                case 'hoy':
                    $fecha_inicio = $ahora->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;
                case 'mes':
                    $fecha_inicio = $ahora->format('Y-m-01');
                    $fecha_fin = $ahora->format('Y-m-t');
                    break;
                case 'trimestre':
                    $mes_actual = $ahora->format('n');
                    $trimestre = ceil($mes_actual / 3);
                    $primer_mes_trimestre = ($trimestre - 1) * 3 + 1;
                    $fecha_inicio = $ahora->format('Y-' . sprintf('%02d', $primer_mes_trimestre) . '-01');
                    $ultimo_mes_trimestre = $primer_mes_trimestre + 2;
                    $fecha_fin = $ahora->format('Y-' . sprintf('%02d', $ultimo_mes_trimestre) . '-t');
                    break;
                case 'semestre':
                    $mes_actual = $ahora->format('n');
                    if ($mes_actual <= 6) {
                        $fecha_inicio = $ahora->format('Y-01-01');
                        $fecha_fin = $ahora->format('Y-06-30');
                    } else {
                        $fecha_inicio = $ahora->format('Y-07-01');
                        $fecha_fin = $ahora->format('Y-12-31');
                    }
                    break;
                case 'año':
                    $fecha_inicio = $ahora->format('Y-01-01');
                    $fecha_fin = $ahora->format('Y-12-31');
                    break;
                default: // personalizado
                    if (!$fecha_inicio || !$fecha_fin) {
                        $fecha_inicio = $ahora->format('Y-m-01');
                        $fecha_fin = $ahora->format('Y-m-t');
                    }
                    break;
            }

            $conexion = (new Conexion())->getConexion();

            // 1. Total de guías
            $sql_total = "SELECT COUNT(*) as total_guias 
                         FROM guia_remision 
                         WHERE id_empresa = ? AND fecha_emision BETWEEN ? AND ?";
            $stmt_total = $conexion->prepare($sql_total);
            
            if (!$stmt_total) {
                throw new Exception('Error en consulta total_guias: ' . $conexion->error);
            }
            
            $stmt_total->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_total->execute();
            $total_guias = $stmt_total->get_result()->fetch_assoc()['total_guias'];

            // 2. Productos más transportados (en lugar de peso total)
            $sql_productos = "SELECT 
                                p.id_producto,
                                p.nombre,
                                COUNT(gd.id_producto) as total_transportado,
                                SUM(gd.cantidad) as cantidad_total,
                                AVG(gd.precio) as precio_promedio
                             FROM guia_detalles gd
                             INNER JOIN productos p ON gd.id_producto = p.id_producto
                             INNER JOIN guia_remision gr ON gd.id_guia = gr.id_guia_remision
                             WHERE gr.id_empresa = ? AND gr.fecha_emision BETWEEN ? AND ?
                             GROUP BY p.id_producto, p.nombre
                             ORDER BY total_transportado DESC
                             LIMIT 6";
            
            $stmt_productos = $conexion->prepare($sql_productos);
            
            if (!$stmt_productos) {
                throw new Exception('Error en consulta productos_mas_transportados: ' . $conexion->error);
            }
            
            $stmt_productos->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_productos->execute();
            $result_productos = $stmt_productos->get_result();

            $productos_mas_transportados = [];
            while ($producto = $result_productos->fetch_assoc()) {
                $productos_mas_transportados[] = [
                    'id_producto' => $producto['id_producto'],
                    'nombre' => $producto['nombre'],
                    'total_transportado' => $producto['total_transportado'],
                    'cantidad_total' => $producto['cantidad_total'],
                    'peso_promedio' => round($producto['precio_promedio'] ?: 0, 2)
                ];
            }

            // 3. Top clientes con más guías
            $sql_clientes_top = "SELECT 
                                   gr.destinatario_nombre as datos,
                                   gr.destinatario_nombre as nombre,
                                   COUNT(gr.id_guia_remision) as total_guias,
                                   SUM(gr.peso) as peso_total
                                FROM guia_remision gr
                                WHERE gr.id_empresa = ? AND gr.fecha_emision BETWEEN ? AND ?
                                AND gr.destinatario_nombre IS NOT NULL AND gr.destinatario_nombre != ''
                                GROUP BY gr.destinatario_nombre
                                ORDER BY total_guias DESC
                                LIMIT 5";
            
            $stmt_clientes = $conexion->prepare($sql_clientes_top);
            
            if (!$stmt_clientes) {
                throw new Exception('Error en consulta clientes_top_guias: ' . $conexion->error);
            }
            
            $stmt_clientes->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_clientes->execute();
            $result_clientes = $stmt_clientes->get_result();

            $top_clientes = [];
            while ($cliente = $result_clientes->fetch_assoc()) {
                $top_clientes[] = [
                    'datos' => $cliente['datos'],
                    'nombre' => $cliente['nombre'],
                    'total_guias' => $cliente['total_guias'],
                    'peso_total' => round($cliente['peso_total'] ?: 0, 2)
                ];
            }

            // 4. Estados de guías
            $sql_estados = "SELECT 
                              CASE 
                                WHEN estado = '0' THEN 'Pendiente'
                                WHEN estado = '1' THEN 'Enviada'
                                ELSE 'Otros'
                              END as estado_nombre,
                              COUNT(*) as cantidad
                            FROM guia_remision
                            WHERE id_empresa = ? AND fecha_emision BETWEEN ? AND ?
                            GROUP BY estado";
            
            $stmt_estados = $conexion->prepare($sql_estados);
            
            if (!$stmt_estados) {
                throw new Exception('Error en consulta estados_guias: ' . $conexion->error);
            }
            
            $stmt_estados->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt_estados->execute();
            $result_estados = $stmt_estados->get_result();

            $estados_guias = [];
            while ($estado = $result_estados->fetch_assoc()) {
                $estados_guias[] = $estado;
            }

            // 5. Evolución mensual de guías
            $sql_evolucion = "SELECT 
                                MONTH(fecha_emision) as mes,
                                COUNT(*) as total_mes
                             FROM guia_remision
                             WHERE id_empresa = ? AND YEAR(fecha_emision) = YEAR(CURDATE())
                             GROUP BY MONTH(fecha_emision)
                             ORDER BY mes";
            
            $stmt_evolucion = $conexion->prepare($sql_evolucion);
            
            if (!$stmt_evolucion) {
                throw new Exception('Error en consulta evolucion_guias: ' . $conexion->error);
            }
            
            $stmt_evolucion->bind_param("i", $empresa);
            $stmt_evolucion->execute();
            $result_evolucion = $stmt_evolucion->get_result();

            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $guias_por_mes = array_fill(0, 12, 0);

            while ($row = $result_evolucion->fetch_assoc()) {
                $guias_por_mes[$row['mes'] - 1] = intval($row['total_mes']);
            }

            // 6. Guías recientes
            $sql_recientes = "SELECT 
                                gr.id_guia_remision,
                                gr.serie,
                                gr.numero,
                                gr.fecha_emision,
                                gr.destinatario_nombre as cliente_datos,
                                gr.destinatario_documento,
                                gr.motivo_traslado as motivo_nombre,
                                gr.peso,
                                gr.nro_bultos,
                                gr.estado,
                                'N/A' as vendedor
                             FROM guia_remision gr
                             WHERE gr.id_empresa = ?
                             ORDER BY gr.fecha_emision DESC, gr.id_guia_remision DESC
                             LIMIT 10";
            
            $stmt_recientes = $conexion->prepare($sql_recientes);
            
            if (!$stmt_recientes) {
                throw new Exception('Error en consulta recientes_guias: ' . $conexion->error);
            }
            
            $stmt_recientes->bind_param("i", $empresa);
            $stmt_recientes->execute();
            $result_recientes = $stmt_recientes->get_result();

            $guias_recientes = [];
            while ($guia = $result_recientes->fetch_assoc()) {
                $guias_recientes[] = $guia;
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_guias' => $total_guias,
                    'productos_mas_transportados' => $productos_mas_transportados,
                    'top_clientes' => $top_clientes,
                    'estados_guias' => $estados_guias,
                    'evolucion_mensual' => [
                        'meses' => $meses,
                        'cantidades' => $guias_por_mes
                    ],
                    'guias_recientes' => $guias_recientes
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos de guías: ' . $e->getMessage()
            ]);
        }
    }

    public function getDatosClientes()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            
            // Obtener parámetros de período
            $periodo = $_GET['periodo'] ?? 'mes';
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;
            
            // Calcular fechas según el período
            $ahora = new DateTime();
            switch ($periodo) {
                case 'hoy':
                    $fecha_inicio = $ahora->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;
                case 'semana':
                    $diaSemana = $ahora->format('N');
                    $inicioSemana = clone $ahora;
                    $inicioSemana->modify('-' . ($diaSemana - 1) . ' days');
                    $fecha_inicio = $inicioSemana->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;
                case 'mes':
                    $inicioMes = new DateTime($ahora->format('Y-m-01'));
                    $fecha_inicio = $inicioMes->format('Y-m-d');
                    $finMes = new DateTime($ahora->format('Y-m-t'));
                    $fecha_fin = $finMes->format('Y-m-d');
                    break;
                case 'anio':
                    $inicioAnio = new DateTime($ahora->format('Y-01-01'));
                    $fecha_inicio = $inicioAnio->format('Y-m-d');
                    $finAnio = new DateTime($ahora->format('Y-12-31'));
                    $fecha_fin = $finAnio->format('Y-m-d');
                    break;
                default: // personalizado
                    if (!$fecha_inicio || !$fecha_fin) {
                        $fecha_inicio = date('Y-m-01');
                        $fecha_fin = date('Y-m-t');
                    }
                    break;
            }

            $conexion = (new Conexion())->getConexion();

            // Consulta para obtener top clientes por compras en el período
            $sql_clientes_top = "SELECT 
                c.datos as cliente_nombre,
                c.id_cliente,
                SUM(v.total) as total_compras,
                COUNT(v.id_venta) as num_compras
            FROM ventas v
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
            WHERE v.id_empresa = ? 
                AND v.estado = '1'
                AND v.fecha_emision BETWEEN ? AND ?
            GROUP BY c.id_cliente, c.datos
            ORDER BY total_compras DESC
            LIMIT 10";

            $stmt = $conexion->prepare($sql_clientes_top);
            if (!$stmt) {
                throw new Exception("Error en la preparación de consulta de clientes: " . $conexion->error);
            }
            $stmt->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $result = $stmt->get_result();

            $clientes_nombres = [];
            $clientes_compras = [];

            while ($cliente = $result->fetch_assoc()) {
                $clientes_nombres[] = $cliente['cliente_nombre'];
                $clientes_compras[] = floatval($cliente['total_compras']);
            }

            $clientesData = [
                'clientesNombres' => $clientes_nombres,
                'clientesCompras' => $clientes_compras
            ];

            echo json_encode([
                'success' => true,
                'clientesData' => $clientesData
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos de clientes: ' . $e->getMessage()
            ]);
        }
    }

    private function generarDatosIngresosPorPeriodo($periodo, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion)
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
                // Obtener ingresos por día de la semana
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
                // Obtener ingresos por semana del mes
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
                // Ingresos por meses del año
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

            case 'personalizado':
                // Para períodos personalizados, agrupar por días si es corto, por semanas si es largo
                $inicio = new DateTime($fecha_inicio);
                $fin = new DateTime($fecha_fin);
                $diff = $inicio->diff($fin);

                if ($diff->days == 0) {
                    // Un solo día
                    $sql = "SELECT SUM(total) as total FROM ventas 
                       WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                       AND fecha_emision = '$fecha_inicio'";
                    $result = $conexion->query($sql);
                    $row = $result->fetch_assoc();
                    $datos = [$row['total'] ?? 0];
                } elseif ($diff->days <= 7) {
                    // Hasta 7 días, mostrar por día
                    for ($i = 0; $i <= $diff->days; $i++) {
                        $fecha_temp = clone $inicio;
                        $fecha_temp->modify("+$i days");
                        $fecha_str = $fecha_temp->format('Y-m-d');

                        $sql = "SELECT SUM(total) as total FROM ventas 
                           WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                           AND fecha_emision = '$fecha_str'";
                        $result = $conexion->query($sql);
                        $row = $result->fetch_assoc();
                        $datos[] = floatval($row['total'] ?? 0);
                    }
                } else {
                    // Más de 7 días, agrupar por semanas
                    $fecha_actual = clone $inicio;
                    while ($fecha_actual <= $fin) {
                        $fin_semana = clone $fecha_actual;
                        $fin_semana->modify('+6 days');
                        if ($fin_semana > $fin) {
                            $fin_semana = $fin;
                        }

                        $sql = "SELECT SUM(total) as total FROM ventas 
                           WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                           AND fecha_emision BETWEEN '{$fecha_actual->format('Y-m-d')}' AND '{$fin_semana->format('Y-m-d')}'";
                        $result = $conexion->query($sql);
                        $row = $result->fetch_assoc();
                        $datos[] = floatval($row['total'] ?? 0);

                        $fecha_actual->modify('+7 days');
                    }
                }
                break;

            default:
                // Caso por defecto: total del período
                $sql = "SELECT SUM(total) as total FROM ventas 
                   WHERE id_empresa = '$empresa' AND estado = '1' AND sucursal = '$sucursal' 
                   AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos = [$row['total'] ?? 0];
                break;
        }

        return $datos;
    }

    private function generarDatosEgresosPorPeriodo($periodo, $fecha_inicio, $fecha_fin, $empresa, $conexion)
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
                // Obtener egresos por día de la semana
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
                // Obtener egresos por semana del mes
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
                // Egresos por meses del año
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

            case 'personalizado':
                // Para períodos personalizados, agrupar por días si es corto, por semanas si es largo
                $inicio = new DateTime($fecha_inicio);
                $fin = new DateTime($fecha_fin);
                $diff = $inicio->diff($fin);

                if ($diff->days == 0) {
                    // Un solo día
                    $sql = "SELECT SUM(total) as total FROM compras 
                       WHERE id_empresa = '$empresa' 
                       AND fecha_emision = '$fecha_inicio'";
                    $result = $conexion->query($sql);
                    $row = $result->fetch_assoc();
                    $datos = [$row['total'] ?? 0];
                } elseif ($diff->days <= 7) {
                    // Hasta 7 días, mostrar por día
                    for ($i = 0; $i <= $diff->days; $i++) {
                        $fecha_temp = clone $inicio;
                        $fecha_temp->modify("+$i days");
                        $fecha_str = $fecha_temp->format('Y-m-d');

                        $sql = "SELECT SUM(total) as total FROM compras 
                           WHERE id_empresa = '$empresa' 
                           AND fecha_emision = '$fecha_str'";
                        $result = $conexion->query($sql);
                        $row = $result->fetch_assoc();
                        $datos[] = floatval($row['total'] ?? 0);
                    }
                } else {
                    // Más de 7 días, agrupar por semanas
                    $fecha_actual = clone $inicio;
                    while ($fecha_actual <= $fin) {
                        $fin_semana = clone $fecha_actual;
                        $fin_semana->modify('+6 days');
                        if ($fin_semana > $fin) {
                            $fin_semana = $fin;
                        }

                        $sql = "SELECT SUM(total) as total FROM compras 
                           WHERE id_empresa = '$empresa' 
                           AND fecha_emision BETWEEN '{$fecha_actual->format('Y-m-d')}' AND '{$fin_semana->format('Y-m-d')}'";
                        $result = $conexion->query($sql);
                        $row = $result->fetch_assoc();
                        $datos[] = floatval($row['total'] ?? 0);

                        $fecha_actual->modify('+7 days');
                    }
                }
                break;

            default:
                // Caso por defecto: total del período
                $sql = "SELECT SUM(total) as total FROM compras 
                   WHERE id_empresa = '$empresa' 
                   AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                $result = $conexion->query($sql);
                $row = $result->fetch_assoc();
                $datos = [$row['total'] ?? 0];
                break;
        }

        return $datos;
    }

    public function getDatosStock()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            
            // Obtener parámetros de período
            $periodo = $_GET['periodo'] ?? 'mes';
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;
            
            // Calcular fechas según el período
            $ahora = new DateTime();
            switch ($periodo) {
                case 'hoy':
                    $fecha_inicio = $ahora->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;
                case 'semana':
                    $diaSemana = $ahora->format('N');
                    $inicioSemana = clone $ahora;
                    $inicioSemana->modify('-' . ($diaSemana - 1) . ' days');
                    $fecha_inicio = $inicioSemana->format('Y-m-d');
                    $fecha_fin = $ahora->format('Y-m-d');
                    break;
                case 'mes':
                    $inicioMes = new DateTime($ahora->format('Y-m-01'));
                    $fecha_inicio = $inicioMes->format('Y-m-d');
                    $finMes = new DateTime($ahora->format('Y-m-t'));
                    $fecha_fin = $finMes->format('Y-m-d');
                    break;
                case 'anio':
                    $inicioAnio = new DateTime($ahora->format('Y-01-01'));
                    $fecha_inicio = $inicioAnio->format('Y-m-d');
                    $finAnio = new DateTime($ahora->format('Y-12-31'));
                    $fecha_fin = $finAnio->format('Y-m-d');
                    break;
                default: // personalizado
                    if (!$fecha_inicio || !$fecha_fin) {
                        $fecha_inicio = date('Y-m-01');
                        $fecha_fin = date('Y-m-t');
                    }
                    break;
            }

            $conexion = (new Conexion())->getConexion();

            // 1. Obtener productos para rotación de inventario
            $sql_productos = "SELECT 
                id_producto,
                COALESCE(nombre, detalle) as nombre,
                cantidad,
                precio,
                costo,
                CASE 
                    WHEN cantidad <= 5 THEN DATEDIFF(NOW(), ultima_salida)
                    WHEN cantidad <= 20 THEN FLOOR(RAND() * 15) + 5
                    ELSE FLOOR(RAND() * 10) + 15
                END as dias_rotacion
            FROM productos 
            WHERE id_empresa = ? AND estado = '1' AND cantidad > 0
            ORDER BY cantidad DESC
            LIMIT 10";

            $stmt = $conexion->prepare($sql_productos);
            if (!$stmt) {
                throw new Exception("Error en la preparación de consulta de productos: " . $conexion->error);
            }
            $stmt->bind_param("i", $empresa);
            $stmt->execute();
            $result_productos = $stmt->get_result();

            $productos_nombres = [];
            $rotacion_dias = [];

            while ($producto = $result_productos->fetch_assoc()) {
                $productos_nombres[] = $producto['nombre'];
                $rotacion_dias[] = intval($producto['dias_rotacion']);
            }

            // 2. Obtener movimientos de inventario para el período seleccionado
            $sql_movimientos = "SELECT 
                DATE_FORMAT(hs.fecha_movimiento, '%Y-%m') as mes,
                hs.tipo_movimiento,
                SUM(hs.cantidad) as total_cantidad
            FROM historial_stock hs
            WHERE hs.id_producto IN (
                SELECT id_producto FROM productos WHERE id_empresa = ?
            )
            AND hs.fecha_movimiento BETWEEN ? AND ?
            GROUP BY mes, hs.tipo_movimiento
            ORDER BY mes";

            $stmt2 = $conexion->prepare($sql_movimientos);
            if (!$stmt2) {
                throw new Exception("Error en la preparación de consulta de movimientos: " . $conexion->error);
            }
            $stmt2->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
            $stmt2->execute();
            $result_movimientos = $stmt2->get_result();

            $movimientos_por_mes = [];
            while ($mov = $result_movimientos->fetch_assoc()) {
                $mes = $mov['mes'];
                if (!isset($movimientos_por_mes[$mes])) {
                    $movimientos_por_mes[$mes] = ['INGRESO' => 0, 'SALIDA' => 0];
                }
                $movimientos_por_mes[$mes][$mov['tipo_movimiento']] = intval($mov['total_cantidad']);
            }

            // Generar arrays para los últimos 6 meses
            $meses = [];
            $entradas = [];
            $salidas = [];

            for ($i = 5; $i >= 0; $i--) {
                $fecha = new DateTime();
                $fecha->modify("-$i months");
                $mes_key = $fecha->format('Y-m');
                $mes_nombre = $fecha->format('M');
                
                $meses[] = $mes_nombre;
                $entradas[] = isset($movimientos_por_mes[$mes_key]) ? $movimientos_por_mes[$mes_key]['INGRESO'] : 0;
                $salidas[] = isset($movimientos_por_mes[$mes_key]) ? $movimientos_por_mes[$mes_key]['SALIDA'] : 0;
            }

            // Formatear datos de rotación para JavaScript
            $rotacion_data = [];
            for ($i = 0; $i < count($productos_nombres); $i++) {
                $rotacion_data[] = [
                    'nombre' => $productos_nombres[$i],
                    'dias_rotacion' => $rotacion_dias[$i]
                ];
            }

            // Formatear datos de movimientos para JavaScript
            $movimientos_data = [];
            for ($i = 0; $i < count($meses); $i++) {
                $movimientos_data[] = [
                    'mes' => date('Y-m', strtotime($meses[$i] . '-01')),
                    'tipo_movimiento' => 'INGRESO',
                    'total_cantidad' => $entradas[$i]
                ];
                $movimientos_data[] = [
                    'mes' => date('Y-m', strtotime($meses[$i] . '-01')),
                    'tipo_movimiento' => 'SALIDA', 
                    'total_cantidad' => $salidas[$i]
                ];
            }

            $stockData = [
                'rotacion' => $rotacion_data,
                'movimientos' => $movimientos_data
            ];

            echo json_encode([
                'success' => true,
                'data' => $stockData
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos de stock: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== GESTIÓN DE VENDEDORES Y METAS INDIVIDUALES ====================
    
    /**
     * Obtener todos los vendedores con sus datos de ventas y metas individuales
     */
    public function getTodosVendedores()
    {
        try {
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $mes_actual = date('m');
            $anio_actual = date('Y');

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            // Consulta para obtener vendedores con ventas y metas individuales
            $sql = "SELECT 
                u.usuario_id,
                u.usuario,
                u.nombres,
                u.apellidos,
                u.id_rol,
                u.email,
                r.nombre as tipo_usuario,
                COALESCE(ventas_mes.ventas_actuales, 0) as ventas_actuales,
                COALESCE(mv.meta_individual, 0) as meta_individual,
                me.meta_total as meta_total_empresa
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.rol_id
            LEFT JOIN (
                SELECT 
                    v.id_vendedor,
                    SUM(v.total) as ventas_actuales
                FROM ventas v
                WHERE v.id_empresa = ?
                    AND MONTH(v.fecha_emision) = ?
                    AND YEAR(v.fecha_emision) = ?
                    AND v.estado = '1'
                    AND v.id_vendedor IS NOT NULL
                GROUP BY v.id_vendedor
            ) ventas_mes ON u.usuario_id = ventas_mes.id_vendedor
            LEFT JOIN metas_vendedores mv ON u.usuario_id = mv.usuario_id 
                AND mv.mes = ? AND mv.anio = ? AND mv.estado = '1'
            LEFT JOIN metas_empresa me ON u.id_empresa = me.id_empresa 
                AND me.mes = ? AND me.anio = ? AND me.estado = '1'
            WHERE u.id_empresa = ? 
                AND u.estado = '1'
                AND u.id_rol IN (1, 2, 3) -- Admin, Usuario, Vendedor
            ORDER BY ventas_mes.ventas_actuales DESC";

            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta: ' . $this->conexion->error);
            }

            $stmt->bind_param("iiiiiiii", $empresa, $mes_actual, $anio_actual, $mes_actual, $anio_actual, $mes_actual, $anio_actual, $empresa);
            $stmt->execute();
            $result = $stmt->get_result();

            $vendedores = [];
            $meta_total_empresa = 0;
            
            while ($row = $result->fetch_assoc()) {
                $vendedores[] = $row;
                if ($row['meta_total_empresa'] && !$meta_total_empresa) {
                    $meta_total_empresa = $row['meta_total_empresa'];
                }
            }

            // Calcular resumen
            $total_vendedores = count($vendedores);
            $total_ventas_mes = array_sum(array_column($vendedores, 'ventas_actuales'));
            $vendedores_con_metas = count(array_filter($vendedores, function($v) { return $v['meta_individual'] > 0; }));

            echo json_encode([
                'success' => true,
                'vendedores' => $vendedores,
                'resumen' => [
                    'meta_total_empresa' => $meta_total_empresa,
                    'total_vendedores' => $total_vendedores,
                    'total_ventas_mes' => $total_ventas_mes,
                    'vendedores_con_metas' => $vendedores_con_metas,
                    'mes_actual' => $mes_actual,
                    'anio_actual' => $anio_actual
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener vendedores: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Guardar o actualizar meta individual de un vendedor
     */
    public function guardarMetaIndividual()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $empresa = $_SESSION['id_empresa'] ?? 12;
            
            $usuario_id = $input['usuario_id'] ?? 0;
            $meta_individual = $input['meta_individual'] ?? 0;
            $mes = $input['mes'] ?? date('m');
            $anio = $input['anio'] ?? date('Y');

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            if (!$usuario_id || $meta_individual < 0) {
                throw new Exception('Datos inválidos');
            }

            // Verificar si ya existe una meta para este vendedor en este período
            $sql_check = "SELECT id_meta_vendedor FROM metas_vendedores 
                         WHERE usuario_id = ? AND mes = ? AND anio = ? AND id_empresa = ?";
            
            $stmt_check = $this->conexion->prepare($sql_check);
            $stmt_check->bind_param("iiii", $usuario_id, $mes, $anio, $empresa);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // Actualizar meta existente
                $sql = "UPDATE metas_vendedores 
                       SET meta_individual = ?, fecha_actualizacion = NOW() 
                       WHERE usuario_id = ? AND mes = ? AND anio = ? AND id_empresa = ?";
                
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("diiii", $meta_individual, $usuario_id, $mes, $anio, $empresa);
            } else {
                // Insertar nueva meta
                $sql = "INSERT INTO metas_vendedores (id_empresa, usuario_id, meta_individual, mes, anio) 
                       VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("iidii", $empresa, $usuario_id, $meta_individual, $mes, $anio);
            }

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Meta individual guardada exitosamente'
                ]);
            } else {
                throw new Exception('Error al guardar la meta: ' . $this->conexion->error);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar meta individual: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Distribuir metas automáticamente entre vendedores
     */
    public function distribuirMetasAutomaticas()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $empresa = $_SESSION['id_empresa'] ?? 12;
            $mes = $input['mes'] ?? date('m');
            $anio = $input['anio'] ?? date('Y');

            if (!$empresa) {
                throw new Exception('Sesión no válida');
            }

            // Obtener meta total de la empresa
            $sql_meta_empresa = "SELECT meta_total FROM metas_empresa 
                               WHERE id_empresa = ? AND mes = ? AND anio = ? AND estado = '1'";
            
            $stmt_meta = $this->conexion->prepare($sql_meta_empresa);
            $stmt_meta->bind_param("iii", $empresa, $mes, $anio);
            $stmt_meta->execute();
            $result_meta = $stmt_meta->get_result();
            
            if ($result_meta->num_rows === 0) {
                throw new Exception('No hay meta total definida para este período');
            }
            
            $meta_total = $result_meta->fetch_assoc()['meta_total'];

            // Obtener vendedores activos con ventas en los últimos 3 meses
            $sql_vendedores = "SELECT 
                u.usuario_id,
                u.nombres,
                COALESCE(AVG(ventas_historicas.ventas_mes), 0) as promedio_ventas
            FROM usuarios u
            LEFT JOIN (
                SELECT 
                    v.id_vendedor,
                    SUM(v.total) as ventas_mes
                FROM ventas v
                WHERE v.id_empresa = ?
                    AND v.fecha_emision >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    AND v.estado = '1'
                    AND v.id_vendedor IS NOT NULL
                GROUP BY v.id_vendedor, YEAR(v.fecha_emision), MONTH(v.fecha_emision)
            ) ventas_historicas ON u.usuario_id = ventas_historicas.id_vendedor
            WHERE u.id_empresa = ? 
                AND u.estado = '1'
                AND u.id_rol IN (1, 2, 3)
            GROUP BY u.usuario_id
            HAVING promedio_ventas > 0
            ORDER BY promedio_ventas DESC";

            $stmt_vendedores = $this->conexion->prepare($sql_vendedores);
            $stmt_vendedores->bind_param("ii", $empresa, $empresa);
            $stmt_vendedores->execute();
            $result_vendedores = $stmt_vendedores->get_result();

            $vendedores = [];
            $total_promedio = 0;
            
            while ($row = $result_vendedores->fetch_assoc()) {
                $vendedores[] = $row;
                $total_promedio += $row['promedio_ventas'];
            }

            if (count($vendedores) === 0) {
                throw new Exception('No hay vendedores con historial de ventas');
            }

            // Distribuir metas proporcionalmente
            $vendedores_actualizados = 0;
            
            foreach ($vendedores as $vendedor) {
                $porcentaje_participacion = $total_promedio > 0 ? ($vendedor['promedio_ventas'] / $total_promedio) : (1 / count($vendedores));
                $meta_individual = $meta_total * $porcentaje_participacion;

                // Verificar si ya existe meta para este vendedor
                $sql_check = "SELECT id_meta_vendedor FROM metas_vendedores 
                             WHERE usuario_id = ? AND mes = ? AND anio = ? AND id_empresa = ?";
                
                $stmt_check = $this->conexion->prepare($sql_check);
                $stmt_check->bind_param("iiii", $vendedor['usuario_id'], $mes, $anio, $empresa);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    // Actualizar meta existente
                    $sql_update = "UPDATE metas_vendedores 
                                  SET meta_individual = ?, fecha_actualizacion = NOW() 
                                  WHERE usuario_id = ? AND mes = ? AND anio = ? AND id_empresa = ?";
                    
                    $stmt_update = $this->conexion->prepare($sql_update);
                    $stmt_update->bind_param("diiii", $meta_individual, $vendedor['usuario_id'], $mes, $anio, $empresa);
                    $stmt_update->execute();
                } else {
                    // Insertar nueva meta
                    $sql_insert = "INSERT INTO metas_vendedores (id_empresa, usuario_id, meta_individual, mes, anio) 
                                  VALUES (?, ?, ?, ?, ?)";
                    
                    $stmt_insert = $this->conexion->prepare($sql_insert);
                    $stmt_insert->bind_param("iidii", $empresa, $vendedor['usuario_id'], $meta_individual, $mes, $anio);
                    $stmt_insert->execute();
                }
                
                $vendedores_actualizados++;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Metas distribuidas exitosamente',
                'vendedores_actualizados' => $vendedores_actualizados,
                'meta_total' => $meta_total
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al distribuir metas: ' . $e->getMessage()
            ]);
        }
    }

}