<?php

require_once 'utils/lib/exel/vendor/autoload.php';

class ReporteExcelDashboardController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function generateExcelReport()
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
            
            // Inicializar PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Generar el contenido según el tipo de reporte
            switch ($tipo) {
                case 'ventas':
                    $this->generarExcelReporteVentas($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'productos':
                    $this->generarExcelReporteProductos($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'stock':
                    $this->generarExcelReporteStock($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'clientes':
                    $this->generarExcelReporteClientes($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'metas':
                    $this->generarExcelReporteMetas($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
                case 'completo':
                default:
                    $this->generarExcelReporteCompleto($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
                    break;
            }
            
            // Generar el Excel
            $sufijo_periodo = $periodo_tipo === 'anual' ? 'anual_' . ($_GET['anio'] ?? date('Y')) : date('Y-m-d');
            $nombreArchivo = "Reporte_Dashboard_{$tipo}_{$sufijo_periodo}.xlsx";
            
            // Crear el objeto Writer para Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Configurar headers para descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            // Enviar el archivo al navegador
            $writer->save('php://output');
            exit;
            
        } catch (Exception $e) {
            echo "Error generando el reporte Excel: " . $e->getMessage();
        }
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

    private function getHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'CA3438'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function getTitleStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'CA3438'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }

    private function getSubtitleStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];
    }

    private function getDataStyle()
    {
        return [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function getTotalStyle()
    {
        return [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'F2F2F2'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function generarExcelReporteVentas($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventas');
        
        // Título del reporte
        $sheet->setCellValue('A1', 'Reporte de Ventas');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());
        
        $sheet->setCellValue('A2', 'Período: ' . $titulo_periodo);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());
        
        // Resumen de ventas - IGUAL QUE EN EL PDF
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
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $resumen = $stmt->get_result()->fetch_assoc();
        
        // Tabla de resumen
        $sheet->setCellValue('A4', 'Resumen de Ventas');
        $sheet->mergeCells('A4:C4');
        $sheet->getStyle('A4')->applyFromArray($this->getSubtitleStyle());
        
        $sheet->setCellValue('A5', 'Concepto');
        $sheet->setCellValue('B5', 'Cantidad');
        $sheet->setCellValue('C5', 'Monto Total');
        $sheet->getStyle('A5:C5')->applyFromArray($this->getHeaderStyle());
        
        $sheet->setCellValue('A6', 'Total Ventas');
        $sheet->setCellValue('B6', number_format($resumen['cantidad_ventas'] ?? 0, 0));
        $sheet->setCellValue('C6', number_format($resumen['total_ventas'] ?? 0, 2));
        
        $sheet->setCellValue('A7', 'Boletas');
        $sheet->setCellValue('B7', number_format($resumen['cantidad_boletas'] ?? 0, 0));
        $sheet->setCellValue('C7', number_format($resumen['total_boletas'] ?? 0, 2));
        
        $sheet->setCellValue('A8', 'Facturas');
        $sheet->setCellValue('B8', number_format($resumen['cantidad_facturas'] ?? 0, 0));
        $sheet->setCellValue('C8', number_format($resumen['total_facturas'] ?? 0, 2));
        
        $sheet->getStyle('A6:C8')->applyFromArray($this->getDataStyle());
        
        // Para reportes anuales, mostrar ventas por mes - IGUAL QUE EN EL PDF
        $row = 10;
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
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
            }
            $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $ventasPorMes = $stmt->get_result();
            
            $sheet->setCellValue('A' . $row, 'Ventas por Mes');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($this->getSubtitleStyle());
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Mes');
            $sheet->setCellValue('B' . $row, 'Cantidad');
            $sheet->setCellValue('C' . $row, 'Monto Total');
            $sheet->setCellValue('D' . $row, 'Promedio por Venta');
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getHeaderStyle());
            $row++;
            
            $totalVentas = 0;
            $totalCantidad = 0;
            
            while ($mes = $ventasPorMes->fetch_assoc()) {
                $promedio = $mes['cantidad_mes'] > 0 ? $mes['total_mes'] / $mes['cantidad_mes'] : 0;
                $nombreMes = $this->obtenerNombreMes($mes['mes']);
                
                $sheet->setCellValue('A' . $row, $nombreMes . ' ' . $mes['anio']);
                $sheet->setCellValue('B' . $row, number_format($mes['cantidad_mes'], 0));
                $sheet->setCellValue('C' . $row, number_format($mes['total_mes'], 2));
                $sheet->setCellValue('D' . $row, number_format($promedio, 2));
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getDataStyle());
                
                $totalVentas += $mes['total_mes'];
                $totalCantidad += $mes['cantidad_mes'];
                $row++;
            }
            
            $promedioGeneral = $totalCantidad > 0 ? $totalVentas / $totalCantidad : 0;
            
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('B' . $row, number_format($totalCantidad, 0));
            $sheet->setCellValue('C' . $row, number_format($totalVentas, 2));
            $sheet->setCellValue('D' . $row, number_format($promedioGeneral, 2));
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getTotalStyle());
            $row += 2;
        } else {
            // Para reportes por rango, mostrar ventas por día - IGUAL QUE EN EL PDF
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
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
            }
            $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $ventasPorDia = $stmt->get_result();
            
            $sheet->setCellValue('A' . $row, 'Ventas por Día');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($this->getSubtitleStyle());
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Fecha');
            $sheet->setCellValue('B' . $row, 'Cantidad');
            $sheet->setCellValue('C' . $row, 'Monto Total');
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($this->getHeaderStyle());
            $row++;
            
            $totalVentas = 0;
            $totalCantidad = 0;
            
            while ($dia = $ventasPorDia->fetch_assoc()) {
                $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($dia['fecha_emision'])));
                $sheet->setCellValue('B' . $row, number_format($dia['cantidad_dia'], 0));
                $sheet->setCellValue('C' . $row, number_format($dia['total_dia'], 2));
                $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($this->getDataStyle());
                
                $totalVentas += $dia['total_dia'];
                $totalCantidad += $dia['cantidad_dia'];
                $row++;
            }
            
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('B' . $row, number_format($totalCantidad, 0));
            $sheet->setCellValue('C' . $row, number_format($totalVentas, 2));
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($this->getTotalStyle());
            $row += 2;
        }
        
        // Ventas por vendedor - USANDO LA TABLA usuarios REAL
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
        if (!$stmt) {
            // Si no existe la tabla usuarios, omitir esta sección
            $sheet->setCellValue('A' . $row, 'Ventas por Vendedor: No disponible (tabla usuarios no encontrada)');
        } else {
            $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $ventasPorVendedor = $stmt->get_result();
            
            $sheet->setCellValue('A' . $row, 'Ventas por Vendedor');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($this->getSubtitleStyle());
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Vendedor');
            $sheet->setCellValue('B' . $row, 'Cantidad');
            $sheet->setCellValue('C' . $row, 'Monto Total');
            $sheet->setCellValue('D' . $row, 'Promedio por Venta');
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getHeaderStyle());
            $row++;
            
            $totalVentas = 0;
            $totalCantidad = 0;
            
            while ($vendedor = $ventasPorVendedor->fetch_assoc()) {
                $promedio = $vendedor['cantidad_ventas'] > 0 ? $vendedor['total_ventas'] / $vendedor['cantidad_ventas'] : 0;
                
                $sheet->setCellValue('A' . $row, ($vendedor['nombres'] ?? '') . ' ' . ($vendedor['apellidos'] ?? ''));
                $sheet->setCellValue('B' . $row, number_format($vendedor['cantidad_ventas'], 0));
                $sheet->setCellValue('C' . $row, number_format($vendedor['total_ventas'], 2));
                $sheet->setCellValue('D' . $row, number_format($promedio, 2));
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getDataStyle());
                
                $totalVentas += $vendedor['total_ventas'];
                $totalCantidad += $vendedor['cantidad_ventas'];
                $row++;
            }
            
            $promedioGeneral = $totalCantidad > 0 ? $totalVentas / $totalCantidad : 0;
            
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('B' . $row, number_format($totalCantidad, 0));
            $sheet->setCellValue('C' . $row, number_format($totalVentas, 2));
            $sheet->setCellValue('D' . $row, number_format($promedioGeneral, 2));
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getTotalStyle());
        }
        
        // Ajustar anchos de columna automáticamente
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelReporteProductos($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Productos');
        
        // Título del reporte
        $sheet->setCellValue('A1', 'Reporte de Productos');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());
        
        $sheet->setCellValue('A2', 'Período: ' . $titulo_periodo);
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());
        
        // Productos más vendidos - USANDO LA TABLA productos_ventas REAL
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
        if (!$stmt) {
            $sheet->setCellValue('A4', 'Error en consulta de productos: ' . $this->conexion->error);
            return;
        }
        $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $productosMasVendidos = $stmt->get_result();
        
        $sheet->setCellValue('A4', 'Productos Más Vendidos');
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A4')->applyFromArray($this->getSubtitleStyle());
        
        $sheet->setCellValue('A5', 'Código');
        $sheet->setCellValue('B5', 'Producto');
        $sheet->setCellValue('C5', 'Unidades Vendidas');
        $sheet->setCellValue('D5', 'Total Ventas');
        $sheet->getStyle('A5:D5')->applyFromArray($this->getHeaderStyle());
        
        $row = 6;
        $totalUnidades = 0;
        $totalVentas = 0;
        
        while ($producto = $productosMasVendidos->fetch_assoc()) {
            $sheet->setCellValue('A' . $row, $producto['codigo'] ?? '');
            $sheet->setCellValue('B' . $row, $producto['nombre'] ?? '');
            $sheet->setCellValue('C' . $row, number_format($producto['total_vendido'] ?? 0, 0));
            $sheet->setCellValue('D' . $row, number_format($producto['total_ventas'] ?? 0, 2));
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getDataStyle());
            
            $totalUnidades += $producto['total_vendido'] ?? 0;
            $totalVentas += $producto['total_ventas'] ?? 0;
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, number_format($totalUnidades, 0));
        $sheet->setCellValue('D' . $row, number_format($totalVentas, 2));
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getTotalStyle());
        
        // Ajustar anchos de columna automáticamente
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelReporteStock($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock');
        
        // Título del reporte
        $sheet->setCellValue('A1', 'Reporte de Stock');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());
        
        $sheet->setCellValue('A2', 'Fecha de generación: ' . date('d/m/Y'));
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());
        
        // Productos con stock bajo - IGUAL QUE EN EL PDF
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
        if (!$stmt) {
            $sheet->setCellValue('A4', 'Error: No se pudo consultar la tabla productos');
            return;
        }
        
        $stmt->bind_param("i", $empresa);
        $stmt->execute();
        $productosStockBajo = $stmt->get_result();
        
        $sheet->setCellValue('A4', 'Productos con Stock Bajo');
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A4')->applyFromArray($this->getSubtitleStyle());
        
        $sheet->setCellValue('A5', 'Código');
        $sheet->setCellValue('B5', 'Producto');
        $sheet->setCellValue('C5', 'Stock Actual');
        $sheet->setCellValue('D5', 'Precio');
        $sheet->getStyle('A5:D5')->applyFromArray($this->getHeaderStyle());
        
        $row = 6;
        while ($producto = $productosStockBajo->fetch_assoc()) {
            $sheet->setCellValue('A' . $row, $producto['codigo'] ?? '');
            $sheet->setCellValue('B' . $row, $producto['nombre'] ?? '');
            $sheet->setCellValue('C' . $row, number_format($producto['cantidad'] ?? 0, 0));
            $sheet->setCellValue('D' . $row, number_format($producto['precio'] ?? 0, 2));
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getDataStyle());
            $row++;
        }
        
        // Movimientos de inventario - USANDO LAS TABLAS REALES
        $row += 2;
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
        LEFT JOIN 
            usuarios u ON ie.id_usuario = u.usuario_id
        WHERE 
            p.id_empresa = ?
            AND ie.estado != '0'
        ORDER BY 
            ie.intercambio_id DESC
        LIMIT 20";
        
        $stmt = $this->conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $empresa);
            $stmt->execute();
            $movimientos = $stmt->get_result();
            
            $sheet->setCellValue('A' . $row, 'Últimos Movimientos de Inventario');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($this->getSubtitleStyle());
            $row++;
            
            $sheet->setCellValue('A' . $row, 'Código');
            $sheet->setCellValue('B' . $row, 'Producto');
            $sheet->setCellValue('C' . $row, 'Tipo');
            $sheet->setCellValue('D' . $row, 'Cantidad');
            $sheet->setCellValue('E' . $row, 'Almacén');
            $sheet->setCellValue('F' . $row, 'Usuario');
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($this->getHeaderStyle());
            $row++;
            
            while ($movimiento = $movimientos->fetch_assoc()) {
                $tipo = $movimiento['tipo'] == 'i' ? 'Ingreso' : 'Egreso';
                $almacen = $movimiento['tipo'] == 'i' ? $movimiento['almacen_ingreso'] : $movimiento['almacen_egreso'];
                
                $sheet->setCellValue('A' . $row, $movimiento['codigo'] ?? '');
                $sheet->setCellValue('B' . $row, $movimiento['nombre'] ?? '');
                $sheet->setCellValue('C' . $row, $tipo);
                $sheet->setCellValue('D' . $row, number_format($movimiento['cantidad'] ?? 0, 0));
                $sheet->setCellValue('E' . $row, 'Almacén ' . ($almacen ?? 'N/A'));
                $sheet->setCellValue('F' . $row, $movimiento['usuario'] ?? 'N/A');
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($this->getDataStyle());
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Movimientos de inventario no disponibles');
        }
        
        // Ajustar anchos de columna automáticamente
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelReporteClientes($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Clientes');
        
        // Título del reporte
        $sheet->setCellValue('A1', 'Reporte de Clientes');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());
        
        $sheet->setCellValue('A2', 'Período: ' . $titulo_periodo);
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());
        
        // Clientes top por compras - IGUAL QUE EN EL PDF
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
        if (!$stmt) {
            $sheet->setCellValue('A4', 'Error: No se pudo consultar la tabla clientes');
            return;
        }
        
        $stmt->bind_param("iss", $empresa, $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $clientesTop = $stmt->get_result();
        
        $sheet->setCellValue('A4', 'Clientes con Mayor Compra');
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A4')->applyFromArray($this->getSubtitleStyle());
        
        $sheet->setCellValue('A5', 'Documento');
        $sheet->setCellValue('B5', 'Cliente');
        $sheet->setCellValue('C5', 'Cantidad de Compras');
        $sheet->setCellValue('D5', 'Total Compras');
        $sheet->getStyle('A5:D5')->applyFromArray($this->getHeaderStyle());
        
        $row = 6;
        $totalCompras = 0;
        $totalCantidad = 0;
        
        while ($cliente = $clientesTop->fetch_assoc()) {
            $sheet->setCellValue('A' . $row, $cliente['documento'] ?? '');
            $sheet->setCellValue('B' . $row, $cliente['datos'] ?? '');
            $sheet->setCellValue('C' . $row, number_format($cliente['num_compras'] ?? 0, 0));
            $sheet->setCellValue('D' . $row, number_format($cliente['total_compras'] ?? 0, 2));
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getDataStyle());
            
            $totalCompras += $cliente['total_compras'] ?? 0;
            $totalCantidad += $cliente['num_compras'] ?? 0;
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, number_format($totalCantidad, 0));
        $sheet->setCellValue('D' . $row, number_format($totalCompras, 2));
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($this->getTotalStyle());
        
        // Ajustar anchos de columna automáticamente
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelReporteMetas($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
{
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Metas de Ventas');
    
    // Título del reporte
    $sheet->setCellValue('A1', 'Reporte de Metas de Ventas');
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());
    
    $sheet->setCellValue('A2', 'Período: ' . $titulo_periodo);
    $sheet->mergeCells('A2:E2');
    $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());
    
    // Obtener la meta total de la empresa - IGUAL QUE EN EL PDF
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
    if (!$stmt) {
        $sheet->setCellValue('A4', 'Error en consulta de metas: ' . $this->conexion->error);
        return;
    }
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
    if (!$stmt) {
        $sheet->setCellValue('A4', 'Error en consulta de vendedores: ' . $this->conexion->error);
        return;
    }
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
    $sheet->setCellValue('A4', 'Resumen de Metas');
    $sheet->mergeCells('A4:B4');
    $sheet->getStyle('A4')->applyFromArray($this->getSubtitleStyle());
    
    $sheet->setCellValue('A5', 'Concepto');
    $sheet->setCellValue('B5', 'Valor');
    $sheet->getStyle('A5:B5')->applyFromArray($this->getHeaderStyle());
    
    $sheet->setCellValue('A6', 'Meta Total');
    $sheet->setCellValue('B6', number_format($meta_total, 2));
    
    $sheet->setCellValue('A7', 'Ventas Actuales');
    $sheet->setCellValue('B7', number_format($total_ventas, 2));
    
    $sheet->setCellValue('A8', 'Progreso');
    $sheet->setCellValue('B8', number_format($progreso_total, 2) . '%');
    
    $sheet->setCellValue('A9', 'Vendedores Activos');
    $sheet->setCellValue('B9', count($vendedores));
    
    $sheet->getStyle('A6:B9')->applyFromArray($this->getDataStyle());
    
    // Tabla de vendedores
    $row = 11;
    $sheet->setCellValue('A' . $row, 'Desempeño de Vendedores');
    $sheet->mergeCells('A' . $row . ':E' . $row);
    $sheet->getStyle('A' . $row)->applyFromArray($this->getSubtitleStyle());
    $row++;
    
    $sheet->setCellValue('A' . $row, 'Vendedor');
    $sheet->setCellValue('B' . $row, 'Tipo');
    $sheet->setCellValue('C' . $row, 'Ventas');
    $sheet->setCellValue('D' . $row, 'Contribución');
    $sheet->setCellValue('E' . $row, 'Posición');
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($this->getHeaderStyle());
    $row++;
    
    foreach ($vendedores as $index => $vendedor) {
        $sheet->setCellValue('A' . $row, $vendedor['nombres'] . ' ' . $vendedor['apellidos']);
        $sheet->setCellValue('B' . $row, $vendedor['tipo_usuario']);
        $sheet->setCellValue('C' . $row, number_format($vendedor['ventas_actuales'], 2));
        $sheet->setCellValue('D' . $row, number_format($vendedor['porcentaje_contribucion'], 2) . '%');
        $sheet->setCellValue('E' . $row, '#' . ($index + 1));
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($this->getDataStyle());
        $row++;
    }
    
    $sheet->setCellValue('A' . $row, 'TOTAL');
    $sheet->mergeCells('A' . $row . ':B' . $row);
    $sheet->setCellValue('C' . $row, number_format($total_ventas, 2));
    $sheet->setCellValue('D' . $row, number_format($progreso_total, 2) . '%');
    $sheet->setCellValue('E' . $row, '-');
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($this->getTotalStyle());
    
    // Ajustar anchos de columna automáticamente
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

    private function generarExcelReporteCompleto($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo)
    {
        // Crear hoja de resumen general
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen General');
        
        // Título del reporte
        $sheet->setCellValue('A1', 'Reporte Completo del Dashboard');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());
        
        $sheet->setCellValue('A2', 'Período: ' . $titulo_periodo);
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());
        
        // Resumen general - IGUAL QUE EN EL PDF
        $sql = "SELECT 
            SUM(total) as total_ventas,
            COUNT(*) as cantidad_ventas
        FROM ventas 
        WHERE id_empresa = ? 
            AND sucursal = ? 
            AND estado = '1' 
            AND fecha_emision BETWEEN ? AND ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            $sheet->setCellValue('A4', 'Error en consulta de resumen: ' . $this->conexion->error);
            return;
        }
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
        if ($stmt) {
            $stmt->bind_param("iiss", $empresa, $sucursal, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $clientes = $stmt->get_result()->fetch_assoc();
        } else {
            $clientes = ['total_clientes' => 0];
        }
        
        // Contar productos en stock
        $sql = "SELECT COUNT(*) as total_productos,
                       SUM(cantidad) as total_unidades
                FROM productos
                WHERE id_empresa = ? 
                    AND estado = '1'";
        
        $stmt = $this->conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $empresa);
            $stmt->execute();
            $productos = $stmt->get_result()->fetch_assoc();
        } else {
            $productos = ['total_productos' => 0, 'total_unidades' => 0];
        }
        
        // Tabla de resumen general
        $sheet->setCellValue('A4', 'Resumen General');
        $sheet->mergeCells('A4:B4');
        $sheet->getStyle('A4')->applyFromArray($this->getSubtitleStyle());
        
        $sheet->setCellValue('A5', 'Concepto');
        $sheet->setCellValue('B5', 'Valor');
        $sheet->getStyle('A5:B5')->applyFromArray($this->getHeaderStyle());
        
        $sheet->setCellValue('A6', 'Total Ventas');
        $sheet->setCellValue('B6', number_format($resumen['total_ventas'] ?? 0, 2));
        
        $sheet->setCellValue('A7', 'Cantidad de Ventas');
        $sheet->setCellValue('B7', number_format($resumen['cantidad_ventas'] ?? 0, 0));
        
        $sheet->setCellValue('A8', 'Clientes Atendidos');
        $sheet->setCellValue('B8', number_format($clientes['total_clientes'] ?? 0, 0));
        
        $sheet->setCellValue('A9', 'Productos en Sistema');
        $sheet->setCellValue('B9', number_format($productos['total_productos'] ?? 0, 0));
        
        $sheet->setCellValue('A10', 'Unidades en Stock');
        $sheet->setCellValue('B10', number_format($productos['total_unidades'] ?? 0, 0));
        
        $sheet->getStyle('A6:B10')->applyFromArray($this->getDataStyle());
        
        // Ajustar anchos de columna automáticamente
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        try {
            // Crear hojas adicionales para cada sección
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $this->generarExcelReporteVentas($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
            
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(2);
            $this->generarExcelReporteProductos($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
            
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(3);
            $this->generarExcelReporteStock($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
            
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(4);
            $this->generarExcelReporteClientes($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
            
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(5);
            $this->generarExcelReporteMetas($spreadsheet, $empresa, $sucursal, $fecha_inicio, $fecha_fin, $titulo_periodo, $periodo_tipo);
            
            // Volver a la primera hoja
            $spreadsheet->setActiveSheetIndex(0);
        } catch (Exception $e) {
            // Si hay error en alguna hoja, continuar con las demás
            error_log("Error generando hoja del reporte: " . $e->getMessage());
        }
    }
}
