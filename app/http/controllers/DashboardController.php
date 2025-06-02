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
                switch($vendedor['id_rol']) {
                    case 1: $tipo_usuario = 'Administrador'; break;
                    case 7: $tipo_usuario = 'Vendedor'; break;
                    case 8: $tipo_usuario = 'Vendedor Senior'; break;
                    default: $tipo_usuario = 'Usuario'; break;
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


    // NUEVO MÉTODO para guardar meta total de empresa
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
            $empresa = $_SESSION['id_empresa'] ?? 12; // Para pruebas
            $sucursal = $_SESSION['sucursal'] ?? 1;   // Para pruebas
            
            // Obtener parámetros de fecha
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01'); // Primer día del mes por defecto
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');       // Último día del mes por defecto
            $periodo = $_GET['periodo'] ?? 'mes';                   // Período por defecto: mes
            
            // Validar formato de fechas
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
                throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
            }
            
            // Ajustar fechas según el período seleccionado si no es personalizado
            if ($periodo !== 'personalizado') {
                $ahora = new DateTime();
                
                switch ($periodo) {
                    case 'hoy':
                        $fecha_inicio = $ahora->format('Y-m-d');
                        $fecha_fin = $ahora->format('Y-m-d');
                        break;
                    case 'semana':
                        $diaSemana = $ahora->format('N'); // 1 (lunes) a 7 (domingo)
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
            }
            
            // Extraer año y mes para comparativas
            $fecha_inicio_obj = new DateTime($fecha_inicio);
            $anio1 = $fecha_inicio_obj->format('Y');
            $mes1 = $fecha_inicio_obj->format('m');
            
            // Calcular mes anterior para comparativa
            $fecha_anterior = clone $fecha_inicio_obj;
            $fecha_anterior->modify('-1 month');
            $anio2 = $fecha_anterior->format('Y');
            $mes2 = $fecha_anterior->format('m');
            
            // Conexión a la base de datos
            $conexion = (new Conexion())->getConexion();
            
            // Consulta principal adaptada al rango de fechas
            $sql = "SELECT 
              (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' AND estado = '1' and sucursal='$sucursal' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalv,
              (SELECT COUNT(*) FROM clientes WHERE id_empresa = '$empresa') cnt_cli,
              (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='$sucursal' and id_tido =2 AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalvF,
              (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='$sucursal' and id_tido =1 AND estado = '1' AND fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin') totalvB,
              (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='$sucursal' AND estado = '1' AND YEAR(fecha_emision)='$anio2' AND MONTH(fecha_emision)='$mes2') totalvMA,
              (SELECT productos.detalle FROM `productos_ventas` inner join productos on productos_ventas.id_producto = productos.id_producto 
               INNER JOIN ventas ON productos_ventas.id_venta = ventas.id_venta
               WHERE ventas.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
               GROUP BY productos.id_producto ORDER BY SUM(productos_ventas.cantidad) DESC limit 1) prodVen,
              (SELECT SUM(cantidad) FROM productos_ventas 
               INNER JOIN ventas ON productos_ventas.id_venta = ventas.id_venta
               WHERE ventas.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'
               GROUP BY id_producto ORDER BY SUM(cantidad) DESC limit 1) prodVenCan";
            
            $data = $conexion->query($sql)->fetch_assoc();
            
            // Consulta adaptada para ventas anuales (mantenemos los 12 meses pero filtramos por año)
            $dataListVen = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            
            $sql_ventas_anuales = "SELECT 
              MONTH(fecha_emision) mes,
              SUM(total) total
            FROM
              ventas 
            WHERE id_empresa = '$empresa' 
              AND estado = '1' 
              and sucursal='$sucursal'
              AND YEAR(fecha_emision) = '$anio1'
              GROUP BY mes";
            $resultList = $conexion->query($sql_ventas_anuales);
            
            foreach ($resultList as $dtTemp) {
                $tempValue = 0;
                if (doubleval($dtTemp['total']) > 0) {
                    $tempValue = doubleval($dtTemp['total']);
                }
                $dataListVen[intval($dtTemp['mes']) - 1] = $tempValue;
            }
            
            // Productos más vendidos en el rango de fechas
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
                    // Limpiar caracteres de tabulación y espacios extra
                    $productos_nombres[] = trim(str_replace(["\t", "\n", "\r"], '', $producto['nombre']));
                    $productos_cantidades[] = intval($producto['total_vendido']); // Convertir a entero
                }
                // Reiniciar el puntero del resultado para usarlo después
                $productos_top->data_seek(0);
            }
            
            // Clientes top en el rango de fechas
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
            
            // Datos para gráfico de ventas por período (adaptado al rango de fechas seleccionado)
            $periodos = ['Diario', 'Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Trimestral', 'Semestral', 'Anual'];
            
            // Calculamos según el período seleccionado
            $ventasPorPeriodo = [];
            $factor_base = 1; // Factor para cálculos aproximados
            
            // Determinar el factor base según el período
            switch ($periodo) {
                case 'hoy':
                    $factor_base = 1;
                    break;
                case 'semana':
                    $factor_base = 7;
                    break;
                case 'mes':
                    $factor_base = 30;
                    break;
                case 'anio':
                    $factor_base = 365;
                    break;
                default: // Personalizado: calcular días entre fechas
                    $inicio = new DateTime($fecha_inicio);
                    $fin = new DateTime($fecha_fin);
                    $factor_base = $inicio->diff($fin)->days + 1;
            }
            
            $ventasPorPeriodo = [
                number_format($data["totalv"] / $factor_base ?? 0, 2, ".", ""), // Diario (aproximado)
                number_format($data["totalv"] / ($factor_base / 7) ?? 0, 2, ".", ""),  // Semanal (aproximado)
                number_format($data["totalv"] / ($factor_base / 15) ?? 0, 2, ".", ""),  // Quincenal (aproximado)
                number_format($data["totalv"] / ($factor_base / 30) ?? 0, 2, ".", ""),  // Mensual (aproximado)
                number_format($data["totalv"] / ($factor_base / 60) ?? 0, 2, ".", ""),  // Bimestral (aproximado)
                number_format($data["totalv"] / ($factor_base / 90) ?? 0, 2, ".", ""),  // Trimestral (aproximado)
                number_format($data["totalv"] / ($factor_base / 180) ?? 0, 2, ".", ""),  // Semestral (aproximado)
                number_format($data["totalv"] / ($factor_base / 365) ?? 0, 2, ".", "") // Anual (aproximado)
            ];
            
            // Preparar los datos para el dashboard
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
                'gananciaMensual' => $ganancia_mensual,
                'periodo' => [
                    'tipo' => $periodo,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin
                ]
            ];
            
            echo json_encode([
                'success' => true,
                'dashboardData' => $dashboardData,
                'mensaje' => 'Datos filtrados correctamente para el período seleccionado'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage(),
                'debug' => [
                    'empresa' => $empresa ?? 'No definido',
                    'fecha_inicio' => $fecha_inicio ?? 'No definida',
                    'fecha_fin' => $fecha_fin ?? 'No definida',
                    'periodo' => $periodo ?? 'No definido'
                ]
            ]);
        }
    }
}