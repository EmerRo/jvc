<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';

class ReporteDashboardController extends Controller
{
    private $mpdf;
    private $conexion;

    public function __construct()
    {
        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,      // Sin márgenes laterales para header/footer
            'margin_right' => 0,     // Sin márgenes laterales para header/footer
            'margin_top' => 35,      // Espacio para el encabezado
            'margin_bottom' => 30,   // Espacio para el pie de página
            'margin_header' => 0,
            'margin_footer' => 0,
            'setAutoTopMargin' => false,
            'setAutoBottomMargin' => false
        ]);
        $this->conexion = (new Conexion())->getConexion();
    }

    public function generateReport()
    {
        try {
            // Obtener parámetros
            $tipo = $_GET['tipo'] ?? 'completo';
            $periodo_tipo = $_GET['periodo_tipo'] ?? 'rango';
            
            // Usar empresa fija ID 12
            $empresa = 12;
            $sucursal = $_SESSION['sucursal'] ?? 1;
            
            // Determinar fechas según el tipo de período
            if ($periodo_tipo === 'anual') {
                $anio = $_GET['anio'] ?? date('Y');
                $mes = $_GET['mes'] ?? null;
                
                if ($mes) {
                    // Reporte mensual de un año específico
                    $fecha_inicio = $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01';
                    $fecha_fin = date('Y-m-t', strtotime($fecha_inicio));
                    $titulo_periodo = $this->obtenerNombreMes($mes) . ' de ' . $anio;
                } else {
                    // Reporte anual completo
                    $fecha_inicio = $anio . '-01-01';
                    $fecha_fin = $anio . '-12-31';
                    $titulo_periodo = 'Año ' . $anio;
                }
            } else {
                // Reporte por rango de fechas
                $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
                $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
                $titulo_periodo = date('d/m/Y', strtotime($fecha_inicio)) . ' al ' . date('d/m/Y', strtotime($fecha_fin));
                
                // Validar formato de fechas
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
                    throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
                }
            }
            
            // Configurar el PDF
            $this->configurarPDF();
            
            // Definir estilos CSS globales para el PDF
            $this->mpdf->WriteHTML($this->getGlobalStyles());
            
            // Generar el contenido según el tipo de reporte
            switch ($tipo) {
                case 'ventas':
                    $this->generarReporteVentas($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'productos':
                    $this->generarReporteProductos($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'stock':
                    $this->generarReporteStock($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'clientes':
                    $this->generarReporteClientes($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'metas':
                    $this->generarReporteMetas($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'completo':
                default:
                    $this->generarReporteCompleto($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
            }
            
            // Generar el PDF
            $sufijo_periodo = $periodo_tipo === 'anual' ? 'anual_' . ($_GET['anio'] ?? date('Y')) : date('Y-m-d');
            $nombreArchivo = "Reporte_Dashboard_{$tipo}_{$sufijo_periodo}.pdf";
            $this->mpdf->Output($nombreArchivo, 'I');
            
        } catch (Exception $e) {
            echo "Error generando el reporte: " . $e->getMessage();
        }
    }

    private function getGlobalStyles()
    {
        return '
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                font-size: 10pt;
            }
            .content-wrapper {
                margin: 0 30px;
                padding-top: 0;
                padding-bottom: 30px;
                position: relative;
                font-size: 12px;
            }
            h1 {
                font-size: 16pt;
                color: #CA3438;
                margin-bottom: 5px;
                margin-top: 10px;
            }
            h2 {
                font-size: 14pt;
                color: #CA3438;
                margin-bottom: 5px;
            }
            h3 {
                font-size: 12pt;
                color: #CA3438;
                margin-bottom: 5px;
                margin-top: 15px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
                font-size: 9pt;
            }
            th {
                background-color: #CA3438;
                color: white;
                font-weight: bold;
                text-align: center;
                padding: 5px;
                font-size: 9pt;
                border: 1px solid #CA3438;
            }
            td {
                padding: 4px 5px;
                border: 1px solid #cccccc;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .total-row {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            .section {
                margin-bottom: 20px;
            }
        </style>
        ';
    }

    private function obtenerNombreMes($numeroMes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[intval($numeroMes)] ?? '';
    }

    private function configurarPDF()
    {
        // Verificar si las imágenes existen
        $rutaEncabezado = 'public/assets/img/encabezado.jpg';
        $rutaPie = 'public/assets/img/pie de pagina.jpg';
        
        // Configurar encabezado - igual que en el reporte taller
        if (file_exists($rutaEncabezado)) {
            $headerHTML = '<div style="width: 100%; margin: 0; padding: 0;">
                <img src="' . $rutaEncabezado . '" style="width: 100%; margin: 0; padding: 0; margin-left: 20px;">
            </div>';
        } else {
            // Encabezado de respaldo si no existe la imagen
            $headerHTML = '
            <div style="text-align: center; font-weight: bold; font-size: 14pt; color: #CA3438; padding: 10px;">
                REPORTE DEL DASHBOARD<br>
                <span style="font-size: 10pt;">Generado el ' . date('d/m/Y H:i:s') . '</span>
            </div>';
        }
        
        $this->mpdf->SetHTMLHeader($headerHTML);
        
        // Configurar pie de página - igual que en el reporte taller
        if (file_exists($rutaPie)) {
            $footerHTML = '<div style="position: absolute; bottom: 0; left: 0; right: 0; width: 100%;">
                <img src="' . $rutaPie . '" style="width: 100%; display: block; margin-right: 10px;">
            </div>';
        } else {
            // Pie de página de respaldo si no existe la imagen
            $footerHTML = '
            <div style="text-align: center; font-size: 8pt; color: #666; padding: 5px;">
                Página {PAGENO} de {nbpg} | Generado el ' . date('d/m/Y H:i:s') . '
            </div>';
        }
        
        $this->mpdf->SetHTMLFooter($footerHTML);
        
        // Configurar propiedades adicionales del PDF
        $this->mpdf->SetDisplayMode('fullpage');
    }
    
    private function generarReporteVentas($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        // Envolver todo el contenido en content-wrapper para márgenes laterales
        $html = '<div class="content-wrapper">';
        
        // Título del reporte
        $html .= '<h1 class="text-center">Reporte de Ventas</h1>';
        $html .= '<h3 class="text-center">Período: ' . $titulo_periodo . '</h3>';
        
        // Resumen de ventas
        $sql = "SELECT 
            SUM(total) as total_ventas,
            COUNT(*) as cantidad_ventas,
            SUM(CASE WHEN id_tido = 1 THEN total ELSE 0 END) as total_boletas,
            SUM(CASE WHEN id_tido = 2 THEN total ELSE 0 END) as total_facturas,
            COUNT(CASE WHEN id_tido = 1 THEN 1 ELSE NULL END) as cantidad_boletas,
            COUNT(CASE WHEN id_tido = 2 THEN 1 ELSE NULL END) as cantidad_facturas
        FROM ventas 
        WHERE id_empresa = ? 
            AND sucursal = ? 
            AND estado = '1' 
            AND fecha_emision BETWEEN ? AND ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $resumen = $stmt->get_result()->fetch_assoc();
        
        // Tabla de resumen
        $html .= '<div class="section">';
        $html .= '<h3>Resumen de Ventas</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Concepto</th><th>Cantidad</th><th>Monto Total</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Total Ventas</td>';
        $html .= '<td class="text-center">' . number_format($resumen['cantidad_ventas'], 0) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($resumen['total_ventas'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Boletas</td>';
        $html .= '<td class="text-center">' . number_format($resumen['cantidad_boletas'], 0) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($resumen['total_boletas'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Facturas</td>';
        $html .= '<td class="text-center">' . number_format($resumen['cantidad_facturas'], 0) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($resumen['total_facturas'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Para reportes anuales, mostrar ventas por mes
        if ($periodo_tipo === 'anual') {
            $sql = "SELECT 
                MONTH(fecha_emision) as mes,
                YEAR(fecha_emision) as anio,
                SUM(total) as total_mes,
                COUNT(*) as cantidad_mes
            FROM ventas 
            WHERE id_empresa = ? 
                AND sucursal = ? 
                AND estado = '1' 
                AND fecha_emision BETWEEN ? AND ?
            GROUP BY YEAR(fecha_emision), MONTH(fecha_emision)
            ORDER BY anio, mes";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $ventasPorMes = $stmt->get_result();
            
            $html .= '<div class="section">';
            $html .= '<h3>Ventas por Mes</h3>';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<th>Mes</th><th>Cantidad</th><th>Monto Total</th><th>Promedio por Venta</th>';
            $html .= '</tr>';
            
            $totalVentas = 0;
            $totalCantidad = 0;
            
            while ($mes = $ventasPorMes->fetch_assoc()) {
                $promedio = $mes['cantidad_mes'] > 0 ? $mes['total_mes'] / $mes['cantidad_mes'] : 0;
                $nombreMes = $this->obtenerNombreMes($mes['mes']);
                
                $html .= '<tr>';
                $html .= '<td>' . $nombreMes . ' ' . $mes['anio'] . '</td>';
                $html .= '<td class="text-center">' . number_format($mes['cantidad_mes'], 0) . '</td>';
                $html .= '<td class="text-right">S/ ' . number_format($mes['total_mes'], 2) . '</td>';
                $html .= '<td class="text-right">S/ ' . number_format($promedio, 2) . '</td>';
                $html .= '</tr>';
                
                $totalVentas += $mes['total_mes'];
                $totalCantidad += $mes['cantidad_mes'];
            }
            
            $promedioGeneral = $totalCantidad > 0 ? $totalVentas / $totalCantidad : 0;
            
            $html .= '<tr class="total-row">';
            $html .= '<td>TOTAL</td>';
            $html .= '<td class="text-center">' . number_format($totalCantidad, 0) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($totalVentas, 2) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($promedioGeneral, 2) . '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';
        } else {
            // Para reportes por rango, mostrar ventas por día
            $sql = "SELECT 
                fecha_emision,
                SUM(total) as total_dia,
                COUNT(*) as cantidad_dia
            FROM ventas 
            WHERE id_empresa = ? 
                AND sucursal = ? 
                AND estado = '1' 
                AND fecha_emision BETWEEN ? AND ?
            GROUP BY fecha_emision
            ORDER BY fecha_emision";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $ventasPorDia = $stmt->get_result();
            
            $html .= '<div class="section">';
            $html .= '<h3>Ventas por Día</h3>';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<th>Fecha</th><th>Cantidad</th><th>Monto Total</th>';
            $html .= '</tr>';
            
            $totalVentas = 0;
            $totalCantidad = 0;
            
            while ($dia = $ventasPorDia->fetch_assoc()) {
                $html .= '<tr>';
                $html .= '<td>' . date('d/m/Y', strtotime($dia['fecha_emision'])) . '</td>';
                $html .= '<td class="text-center">' . number_format($dia['cantidad_dia'], 0) . '</td>';
                $html .= '<td class="text-right">S/ ' . number_format($dia['total_dia'], 2) . '</td>';
                $html .= '</tr>';
                
                $totalVentas += $dia['total_dia'];
                $totalCantidad += $dia['cantidad_dia'];
            }
            
            $html .= '<tr class="total-row">';
            $html .= '<td>TOTAL</td>';
            $html .= '<td class="text-center">' . number_format($totalCantidad, 0) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($totalVentas, 2) . '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';
        }
        
        // Ventas por vendedor
        $sql = "SELECT 
            u.nombres,
            u.apellidos,
            COUNT(v.id_venta) as cantidad_ventas,
            SUM(v.total) as total_ventas
        FROM ventas v
        JOIN usuarios u ON v.id_vendedor = u.usuario_id
        WHERE v.id_empresa = ? 
            AND v.sucursal = ? 
            AND v.estado = '1' 
            AND v.fecha_emision BETWEEN ? AND ?
        GROUP BY u.usuario_id
        ORDER BY total_ventas DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $ventasPorVendedor = $stmt->get_result();
        
        $html .= '<div class="section">';
        $html .= '<h3>Ventas por Vendedor</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Vendedor</th><th>Cantidad</th><th>Monto Total</th><th>Promedio por Venta</th>';
        $html .= '</tr>';
        
        $totalVentas = 0;
        $totalCantidad = 0;
        
        while ($vendedor = $ventasPorVendedor->fetch_assoc()) {
            $promedio = $vendedor['cantidad_ventas'] > 0 ? $vendedor['total_ventas'] / $vendedor['cantidad_ventas'] : 0;
            
            $html .= '<tr>';
            $html .= '<td>' . $vendedor['nombres'] . ' ' . $vendedor['apellidos'] . '</td>';
            $html .= '<td class="text-center">' . number_format($vendedor['cantidad_ventas'], 0) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($vendedor['total_ventas'], 2) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($promedio, 2) . '</td>';
            $html .= '</tr>';
            
            $totalVentas += $vendedor['total_ventas'];
            $totalCantidad += $vendedor['cantidad_ventas'];
        }
        
        $promedioGeneral = $totalCantidad > 0 ? $totalVentas / $totalCantidad : 0;
        
        $html .= '<tr class="total-row">';
        $html .= '<td>TOTAL</td>';
        $html .= '<td class="text-center">' . number_format($totalCantidad, 0) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($totalVentas, 2) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($promedioGeneral, 2) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>'; // Cerrar content-wrapper
        
        $this->mpdf->WriteHTML($html);
    }
    
    private function generarReporteProductos($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $html = '<div class="content-wrapper">';
        
        // Título del reporte
        $html .= '<h1 class="text-center">Reporte de Productos</h1>';
        $html .= '<h3 class="text-center">Período: ' . $titulo_periodo . '</h3>';
        
        // Productos más vendidos
        $sql = "SELECT 
            p.id_producto,
            p.codigo,
            COALESCE(p.nombre, p.detalle) as nombre,
            SUM(pv.cantidad) as total_vendido,
            SUM(pv.precio * pv.cantidad) as total_ventas
        FROM productos_ventas pv
        JOIN productos p ON pv.id_producto = p.id_producto
        JOIN ventas v ON pv.id_venta = v.id_venta
        WHERE v.id_empresa = ? 
            AND v.sucursal = ? 
            AND v.estado = '1'
            AND v.fecha_emision BETWEEN ? AND ?
        GROUP BY p.id_producto
        ORDER BY total_vendido DESC
        LIMIT 20";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $productosMasVendidos = $stmt->get_result();
        
        $html .= '<div class="section">';
        $html .= '<h3>Productos Más Vendidos</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Código</th><th>Producto</th><th>Unidades Vendidas</th><th>Total Ventas</th>';
        $html .= '</tr>';
        
        $totalUnidades = 0;
        $totalVentas = 0;
        
        while ($producto = $productosMasVendidos->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $producto['codigo'] . '</td>';
            $html .= '<td>' . $producto['nombre'] . '</td>';
            $html .= '<td class="text-center">' . number_format($producto['total_vendido'], 0) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($producto['total_ventas'], 2) . '</td>';
            $html .= '</tr>';
            
            $totalUnidades += $producto['total_vendido'];
            $totalVentas += $producto['total_ventas'];
        }
        
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="2">TOTAL</td>';
        $html .= '<td class="text-center">' . number_format($totalUnidades, 0) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($totalVentas, 2) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $this->mpdf->WriteHTML($html);
    }
    
    private function generarReporteStock($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $html = '<div class="content-wrapper">';
        
        // Título del reporte
        $html .= '<h1 class="text-center">Reporte de Stock</h1>';
        $html .= '<h3 class="text-center">Fecha de generación: ' . date('d/m/Y') . '</h3>';
        
        // Productos con stock bajo
        $sql = "SELECT 
            id_producto, 
            codigo,
            COALESCE(nombre, detalle) as nombre, 
            cantidad, 
            precio 
        FROM 
            productos 
        WHERE 
            id_empresa = ? 
            AND cantidad <= 10 
            AND estado = '1'
        ORDER BY 
            cantidad ASC
        LIMIT 20";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $empresa);
        $stmt->execute();
        $productosStockBajo = $stmt->get_result();
        
        $html .= '<div class="section">';
        $html .= '<h3>Productos con Stock Bajo</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Código</th><th>Producto</th><th>Stock Actual</th><th>Precio</th>';
        $html .= '</tr>';
        
        while ($producto = $productosStockBajo->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $producto['codigo'] . '</td>';
            $html .= '<td>' . $producto['nombre'] . '</td>';
            $html .= '<td class="text-center">' . number_format($producto['cantidad'], 0) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($producto['precio'], 2) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        // Movimientos de inventario
        $sql = "SELECT 
            p.codigo,
            COALESCE(p.nombre, p.detalle) as nombre,
            ie.tipo,
            ie.cantidad,
            ie.almacen_ingreso,
            ie.almacen_egreso,
            u.nombres as usuario,
            ie.observaciones
        FROM 
            ingreso_egreso ie
        JOIN 
            productos p ON ie.id_producto = p.id_producto
        JOIN 
            usuarios u ON ie.id_usuario = u.usuario_id
        WHERE 
            p.id_empresa = ?
            AND ie.estado != '0'
        ORDER BY 
            ie.intercambio_id DESC
        LIMIT 20";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $empresa);
        $stmt->execute();
        $movimientos = $stmt->get_result();
        
        $html .= '<div class="section">';
        $html .= '<h3>Últimos Movimientos de Inventario</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Código</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Almacén</th><th>Usuario</th>';
        $html .= '</tr>';
        
        while ($movimiento = $movimientos->fetch_assoc()) {
            $tipo = $movimiento['tipo'] == 'i' ? 'Ingreso' : 'Egreso';
            $almacen = $movimiento['tipo'] == 'i' ? $movimiento['almacen_ingreso'] : $movimiento['almacen_egreso'];
            
            $html .= '<tr>';
            $html .= '<td>' . $movimiento['codigo'] . '</td>';
            $html .= '<td>' . $movimiento['nombre'] . '</td>';
            $html .= '<td>' . $tipo . '</td>';
            $html .= '<td class="text-center">' . number_format($movimiento['cantidad'], 0) . '</td>';
            $html .= '<td class="text-center">Almacén ' . $almacen . '</td>';
            $html .= '<td>' . $movimiento['usuario'] . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $this->mpdf->WriteHTML($html);
    }
    
    private function generarReporteClientes($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $html = '<div class="content-wrapper">';
        
        // Título del reporte
        $html .= '<h1 class="text-center">Reporte de Clientes</h1>';
        $html .= '<h3 class="text-center">Período: ' . $titulo_periodo . '</h3>';
        
        // Clientes top por compras
        $sql = "SELECT 
            c.id_cliente,
            c.documento,
            c.datos,
            COUNT(v.id_venta) as num_compras,
            SUM(v.total) as total_compras
        FROM 
            ventas v
        JOIN 
            clientes c ON v.id_cliente = c.id_cliente
        WHERE 
            v.id_empresa = ? 
            AND v.estado = '1'
            AND v.fecha_emision BETWEEN ? AND ?
        GROUP BY 
            c.id_cliente
        ORDER BY 
            total_compras DESC
        LIMIT 20";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $clientesTop = $stmt->get_result();
        
        $html .= '<div class="section">';
        $html .= '<h3>Clientes con Mayor Compra</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Documento</th><th>Cliente</th><th>Cantidad de Compras</th><th>Total Compras</th>';
        $html .= '</tr>';
        
        $totalCompras = 0;
        $totalCantidad = 0;
        
        while ($cliente = $clientesTop->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $cliente['documento'] . '</td>';
            $html .= '<td>' . $cliente['datos'] . '</td>';
            $html .= '<td class="text-center">' . number_format($cliente['num_compras'], 0) . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($cliente['total_compras'], 2) . '</td>';
            $html .= '</tr>';
            
            $totalCompras += $cliente['total_compras'];
            $totalCantidad += $cliente['num_compras'];
        }
        
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="2">TOTAL</td>';
        $html .= '<td class="text-center">' . number_format($totalCantidad, 0) . '</td>';
        $html .= '<td class="text-right">S/ ' . number_format($totalCompras, 2) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $this->mpdf->WriteHTML($html);
    }
    
    private function generarReporteMetas($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $html = '<div class="content-wrapper">';
        
        // Título del reporte
        $html .= '<h1 class="text-center">Reporte de Metas de Ventas</h1>';
        $html .= '<h3 class="text-center">Período: ' . $titulo_periodo . '</h3>';
        
        // Obtener la meta total de la empresa
        $mes_actual = date('m');
        $anio_actual = date('Y');
        
        $sql_meta_total = "SELECT meta_total 
                          FROM metas_empresa 
                          WHERE id_empresa = ? 
                          AND mes = ? 
                          AND anio = ? 
                          AND estado = '1'
                          ORDER BY fecha_creacion DESC 
                          LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql_meta_total);
        $stmt->bind_param("iii", $empresa, $mes_actual, $anio_actual);
        $stmt->execute();
        $result_meta = $stmt->get_result();
        $meta_empresa = $result_meta->fetch_assoc();
        
        $meta_total = $meta_empresa ? floatval($meta_empresa['meta_total']) : 0;
        
        // Obtener vendedores con ventas
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
                            AND v.fecha_emision BETWEEN ? AND ?
                            AND u.estado = '1'
                          GROUP BY u.usuario_id
                          HAVING ventas_actuales > 0
                          ORDER BY ventas_actuales DESC";
        
        $stmt = $this->conexion->prepare($sql_vendedores);
        $stmt->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $result_vendedores = $stmt->get_result();
        
        $vendedores = [];
        $total_ventas = 0;
        
        while ($vendedor = $result_vendedores->fetch_assoc()) {
            $ventas_actuales = floatval($vendedor['ventas_actuales']);
            $total_ventas += $ventas_actuales;
            
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
                'ventas_actuales' => $ventas_actuales,
                'porcentaje_contribucion' => $porcentaje_contribucion
            ];
        }
        
        // Calcular progreso total
        $progreso_total = $meta_total > 0 ? ($total_ventas / $meta_total) * 100 : 0;
        
        // Resumen de metas
        $html .= '<div class="section">';
        $html .= '<h3>Resumen de Metas</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Concepto</th><th>Valor</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Meta Total</td>';
        $html .= '<td class="text-right">S/ ' . number_format($meta_total, 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Ventas Actuales</td>';
        $html .= '<td class="text-right">S/ ' . number_format($total_ventas, 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Progreso</td>';
        $html .= '<td class="text-right">' . number_format($progreso_total, 2) . '%</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Vendedores Activos</td>';
        $html .= '<td class="text-right">' . count($vendedores) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Tabla de vendedores
        $html .= '<div class="section">';
        $html .= '<h3>Desempeño de Vendedores</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Vendedor</th><th>Tipo</th><th>Ventas</th><th>Contribución</th>';
        $html .= '</tr>';
        
        foreach ($vendedores as $index => $vendedor) {
            $html .= '<tr>';
            $html .= '<td>' . $vendedor['nombres'] . ' ' . $vendedor['apellidos'] . '</td>';
            $html .= '<td>' . $vendedor['tipo_usuario'] . '</td>';
            $html .= '<td class="text-right">S/ ' . number_format($vendedor['ventas_actuales'], 2) . '</td>';
            $html .= '<td class="text-right">' . number_format($vendedor['porcentaje_contribucion'], 2) . '%</td>';
            $html .= '</tr>';
        }
        
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="2">TOTAL</td>';
        $html .= '<td class="text-right">S/ ' . number_format($total_ventas, 2) . '</td>';
        $html .= '<td class="text-right">' . number_format($progreso_total, 2) . '%</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        $this->mpdf->WriteHTML($html);
    }
    
    private function generarReporteCompleto($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $html = '<div class="content-wrapper">';
        
        // Título del reporte
        $html .= '<h1 class="text-center">Reporte Completo del Dashboard</h1>';
        $html .= '<h3 class="text-center">Período: ' . $titulo_periodo . '</h3>';
        
        // Resumen general
        $sql = "SELECT 
            SUM(total) as total_ventas,
            COUNT(*) as cantidad_ventas
        FROM ventas 
        WHERE id_empresa = ? 
            AND sucursal = ? 
            AND estado = '1' 
            AND fecha_emision BETWEEN ? AND ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $resumen = $stmt->get_result()->fetch_assoc();
        
        // Contar clientes con compras en el período
        $sql = "SELECT COUNT(DISTINCT id_cliente) as total_clientes
                FROM ventas 
                WHERE id_empresa = ? 
                    AND sucursal = ? 
                    AND estado = '1' 
                    AND fecha_emision BETWEEN ? AND ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $clientes = $stmt->get_result()->fetch_assoc();
        
        // Contar productos vendidos en el período
        $sql = "SELECT COUNT(DISTINCT pv.id_producto) as total_productos,
                       SUM(pv.cantidad) as total_unidades
                FROM productos_ventas pv
                JOIN ventas v ON pv.id_venta = v.id_venta
                WHERE v.id_empresa = ? 
                    AND v.sucursal = ? 
                    AND v.estado = '1' 
                    AND v.fecha_emision BETWEEN ? AND ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $productos = $stmt->get_result()->fetch_assoc();
        
        // Tabla de resumen general
        $html .= '<div class="section">';
        $html .= '<h3>Resumen General</h3>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Concepto</th><th>Valor</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Total Ventas</td>';
        $html .= '<td class="text-right">S/ ' . number_format($resumen['total_ventas'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Cantidad de Ventas</td>';
        $html .= '<td class="text-right">' . number_format($resumen['cantidad_ventas'], 0) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Clientes Atendidos</td>';
        $html .= '<td class="text-right">' . number_format($clientes['total_clientes'], 0) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Productos Diferentes Vendidos</td>';
        $html .= '<td class="text-right">' . number_format($productos['total_productos'], 0) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Unidades Vendidas</td>';
        $html .= '<td class="text-right">' . number_format($productos['total_unidades'], 0) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Agregar secciones de cada reporte
        $this->mpdf->WriteHTML($html);
        
        // Agregar una nueva página para cada sección
        $this->mpdf->AddPage();
        $this->generarReporteVentas($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
        
        $this->mpdf->AddPage();
        $this->generarReporteProductos($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
        
        $this->mpdf->AddPage();
        $this->generarReporteStock($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
        
        $this->mpdf->AddPage();
        $this->generarReporteClientes($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
        
        $this->mpdf->AddPage();
        $this->generarReporteMetas($empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
    }
}