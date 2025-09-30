<?php
/**
 * Funciones auxiliares para el Dashboard Home
 * Extraído de home.php para mejor organización
 */

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

// Generar datos para los gráficos
$dataListVen = generarDatosGrafico($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion);
$dataUtilidadBruta = generarDatosUtilidadBruta($periodo_actual, $categorias_grafico, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion);

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
?>