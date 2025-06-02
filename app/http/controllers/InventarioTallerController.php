<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php'; 

class InventarioTallerController extends Controller
{
    private $mpdf;
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    private function formatearFechaEspanol($fecha)
    {
        $meses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $fecha = new DateTime($fecha);
        return $fecha->format('d') . ' de ' . $meses[$fecha->format('n') - 1] . ' del ' . $fecha->format('Y');
    }

    private function verificarPermisos()
    {
        // Verificar si el usuario tiene sesión
        if (!isset($_SESSION['usuario_fac'])) {
            return [
                'puedeVerPrecios' => false,
                'esRolOrdenTrabajo' => false
            ];
        }
    
        // Por defecto, asumimos que tiene permisos
        $permisos = [
            'puedeVerPrecios' => true,
            'esRolOrdenTrabajo' => true  // Permitir reportes para todos por defecto
        ];
    
        // Verificar permisos específicos según el rol
        if (isset($_SESSION['id_rol'])) {
            $rolId = $_SESSION['id_rol'];
    
            // Si es administrador (rol_id = 1), siempre tiene todos los permisos
            if ($rolId == 1) {
                return $permisos;
            }
    
            // Verificar si es rol orden trabajo o servicio
            $sqlRol = "SELECT nombre FROM roles WHERE rol_id = ?";
            $stmtRol = $this->conexion->prepare($sqlRol);
            $stmtRol->bind_param("i", $rolId);
            $stmtRol->execute();
            $resultRol = $stmtRol->get_result();
    
            if ($rowRol = $resultRol->fetch_assoc()) {
                $nombreRol = strtoupper($rowRol['nombre']);
                if ($nombreRol === 'ORDEN TRABAJO' || $nombreRol === 'SERVICIO') {
                    $permisos['puedeVerPrecios'] = false;
                    // Ambos roles pueden ver reportes
                    return $permisos;
                }
            }
        }
    
        return $permisos;
    }

    public function generateInventarioReport($id_cotizacion)
    {
        try {
            // Verificar permisos
            $permisos = $this->verificarPermisos();
            $esRolOrdenTrabajo = $permisos['esRolOrdenTrabajo'];
            
            // Si no es rol ORDEN TRABAJO, redirigir o mostrar error
            if (!$esRolOrdenTrabajo) {
                echo "No tiene permisos para acceder a este reporte.";
                return;
            }
            
            // Obtener datos de la cotización
            $sql = "SELECT tc.*, ct.documento, ct.datos, ct.direccion, ct.atencion
                    FROM taller_cotizaciones tc
                    LEFT JOIN clientes_taller ct ON tc.id_cliente_taller = ct.id_cliente_taller
                    WHERE tc.id_cotizacion = ?";

            $stmt = $this->conexion->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta de cotización: " . $this->conexion->error);
            }

            $stmt->bind_param("i", $id_cotizacion);
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta de cotización: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if (!$data) {
                throw new Exception("Cotización no encontrada");
            }

            // Obtener equipos de la cotización
            $sqlEquipos = "SELECT * FROM taller_cotizaciones_equipos WHERE id_cotizacion = ?";
            $stmtEquipos = $this->conexion->prepare($sqlEquipos);
            if ($stmtEquipos === false) {
                throw new Exception("Error al preparar la consulta de equipos: " . $this->conexion->error);
            }

            $stmtEquipos->bind_param("i", $id_cotizacion);
            if (!$stmtEquipos->execute()) {
                throw new Exception("Error al ejecutar la consulta de equipos: " . $stmtEquipos->error);
            }

            $equipos = $stmtEquipos->get_result()->fetch_all(MYSQLI_ASSOC);

            // Obtener repuestos de la cotización
            $repuestos = [];
            foreach ($equipos as $equipo) {
                // Consulta para obtener repuestos
                $sqlRepuestos = "SELECT trc.*, r.nombre, r.codigo, r.cantidad + trc.cantidad as stock_actual, 
                'repuestos' as tipo_tabla
                FROM taller_repuestos_cotis trc 
                INNER JOIN repuestos r ON trc.id_repuesto = r.id_repuesto 
                WHERE trc.id_coti = ? AND trc.id_cotizacion_equipo = ?
                
                UNION
                
                SELECT trc.*, p.nombre, p.codigo, p.cantidad + trc.cantidad as stock_actual, 
                'productos' as tipo_tabla
                FROM taller_repuestos_cotis trc 
                INNER JOIN productos p ON trc.id_repuesto = p.id_producto 
                WHERE trc.id_coti = ? AND trc.id_cotizacion_equipo = ?";
                
                $stmtRepuestos = $this->conexion->prepare($sqlRepuestos);
                if ($stmtRepuestos === false) {
                    throw new Exception("Error al preparar la consulta de repuestos: " . $this->conexion->error);
                }

                $stmtRepuestos->bind_param("iiii", $id_cotizacion, $equipo['id_cotizacion_equipo'], $id_cotizacion, $equipo['id_cotizacion_equipo']);
                if (!$stmtRepuestos->execute()) {
                    throw new Exception("Error al ejecutar la consulta de repuestos: " . $stmtRepuestos->error);
                }

                $repuestosEquipo = $stmtRepuestos->get_result()->fetch_all(MYSQLI_ASSOC);
                foreach ($repuestosEquipo as $repuesto) {
                    $repuesto['equipo'] = $equipo['equipo'];
                    $repuesto['marca'] = $equipo['marca'];
                    $repuesto['modelo'] = $equipo['modelo'];
                    $repuesto['numero_serie'] = $equipo['numero_serie'];
                    $repuestos[] = $repuesto;
                }
            }

            // Configurar MPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 40,
                'margin_bottom' => 30,
                'margin_header' => 0,
                'margin_footer' => 0
            ]);

            // Establecer el encabezado HTML (modificado para eliminar el espacio)
            $headerHTML = '<div style="width: 100%; position: absolute; top: 0; left: 0; right: 0; margin: 0; padding: 0;">
            <img src="public/assets/img/encabezado.jpg" style="width: 100%; margin: 0; padding: 0; display: block;">
            </div>';
            $mpdf->SetHTMLHeader($headerHTML);

            // Definir el pie de página HTML
            $footerHTML = '<div style="position: absolute; bottom: 0; left: 0; right: 0; width: 100%;">
                <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block;">
            </div>';
            $mpdf->SetHTMLFooter($footerHTML);

            // Generar el HTML del reporte
            $html = $this->generateInventarioHTML($data, $repuestos);
            $mpdf->WriteHTML($html);

            // Generar PDF
            $mpdf->Output("Reporte_Inventario_Taller_JVC_{$id_cotizacion}.pdf", 'I');

        } catch (Exception $e) {
            error_log("Error generando reporte de inventario: " . $e->getMessage());
            echo "Error generando el reporte de inventario: " . $e->getMessage();
        }
    }

    private function generateInventarioHTML($data, $repuestos)
{
    $tipoDoc = strlen($data['documento']) === 8 ? 'DNI' : 'RUC';
    $fechaActual = "Lima, " . $this->formatearFechaEspanol(date('Y-m-d'));
    $cotizacionNumber = !empty($data['numero']) ? $data['numero'] : '';
    $year = date('Y');

    $html = "
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }
        .content-wrapper {
            margin: 0 10px;
            padding-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cc0000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #cc0000;
            color: white;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
        }
        .client-info {
            margin-bottom: 20px;
        }
        .client-info p {
            margin: 5px 0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
    
    <div class='content-wrapper'>
        <div style='text-align: right; margin-bottom: 10px;'>
            {$fechaActual}
        </div>
        
        <div class='title'>
            REPORTE DE INVENTARIO - ORDEN DE TRABAJO N° {$cotizacionNumber}/{$year}
        </div>
        
        <div class='client-info'>
            <p><strong>Cliente:</strong> {$data['datos']} - {$tipoDoc} N° {$data['documento']}</p>
            <p><strong>Dirección:</strong> {$data['direccion']}</p>
            <p><strong>Atención:</strong> {$data['atencion']}</p>
        </div>
        
        <p>A continuación se detalla el inventario de repuestos utilizados en la orden de trabajo:</p>
        
        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>CÓDIGO</th>
                    <th>DESCRIPCIÓN</th>
                    <th>EQUIPO</th>
                    <th>CANTIDAD</th>
                    <th>STOCK ACTUAL</th>
                    <th>STOCK FINAL</th>
                </tr>
            </thead>
            <tbody>";
    
    $totalItems = 0;
    foreach ($repuestos as $index => $repuesto) {
        $stockFinal = $repuesto['stock_actual'] - $repuesto['cantidad'];
        $stockFinal = $stockFinal < 0 ? 0 : $stockFinal;
        $totalItems += $repuesto['cantidad'];
        
        $html .= "
            <tr>
                <td class='text-center'>" . ($index + 1) . "</td>
                <td>" . ($repuesto['codigo'] ?: 'Sin Código') . "</td>
                <td>{$repuesto['nombre']}</td>
                <td>{$repuesto['equipo']} ({$repuesto['marca']}) - {$repuesto['modelo']}</td>
                <td class='text-center'>{$repuesto['cantidad']}</td>
                <td class='text-center'>{$repuesto['stock_actual']}</td>
                <td class='text-center'>{$stockFinal}</td>
            </tr>";
    }
    
    $html .= "
            </tbody>
            <tfoot>
                <tr>
                    <td colspan='4' class='text-right'><strong>TOTAL ITEMS:</strong></td>
                    <td class='text-center'><strong>{$totalItems}</strong></td>
                    <td colspan='2'></td>
                </tr>
            </tfoot>
        </table>
        
        <div style='margin-top: 30px;'>
            <p><strong>Observaciones:</strong></p>
            <p>Este reporte muestra los repuestos y productos utilizados en la orden de trabajo. El stock final es el resultado de restar la cantidad utilizada del stock actual.</p>
        </div>
    </div>";
    
    return $html;
}

    public function exportInventarioExcel($id_cotizacion)
    {
        try {
            // Verificar permisos
            $permisos = $this->verificarPermisos();
            $esRolOrdenTrabajo = $permisos['esRolOrdenTrabajo'];
            
            // Si no es rol ORDEN TRABAJO, redirigir o mostrar error
            if (!$esRolOrdenTrabajo) {
                echo json_encode(['success' => false, 'message' => 'No tiene permisos para acceder a este reporte.']);
                return;
            }
            
            // Obtener datos de la cotización
            $sql = "SELECT tc.*, ct.documento, ct.datos, ct.direccion, ct.atencion
                    FROM taller_cotizaciones tc
                    LEFT JOIN clientes_taller ct ON tc.id_cliente_taller = ct.id_cliente_taller
                    WHERE tc.id_cotizacion = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Cotización no encontrada']);
                return;
            }

            // Obtener equipos de la cotización
            $sqlEquipos = "SELECT * FROM taller_cotizaciones_equipos WHERE id_cotizacion = ?";
            $stmtEquipos = $this->conexion->prepare($sqlEquipos);
            $stmtEquipos->bind_param("i", $id_cotizacion);
            $stmtEquipos->execute();
            $equipos = $stmtEquipos->get_result()->fetch_all(MYSQLI_ASSOC);

            // Obtener repuestos de la cotización
            $repuestos = [];
            foreach ($equipos as $equipo) {
                // Consulta para obtener repuestos
                $sqlRepuestos = "SELECT trc.*, r.nombre, r.codigo, r.cantidad + trc.cantidad as stock_actual, 
                'repuestos' as tipo_tabla
                FROM taller_repuestos_cotis trc 
                INNER JOIN repuestos r ON trc.id_repuesto = r.id_repuesto 
                WHERE trc.id_coti = ? AND trc.id_cotizacion_equipo = ?
                
                UNION
                
                SELECT trc.*, p.nombre, p.codigo, p.cantidad + trc.cantidad as stock_actual, 
                'productos' as tipo_tabla
                FROM taller_repuestos_cotis trc 
                INNER JOIN productos p ON trc.id_repuesto = p.id_producto 
                WHERE trc.id_coti = ? AND trc.id_cotizacion_equipo = ?";
                
                $stmtRepuestos = $this->conexion->prepare($sqlRepuestos);
                $stmtRepuestos->bind_param("iiii", $id_cotizacion, $equipo['id_cotizacion_equipo'], $id_cotizacion, $equipo['id_cotizacion_equipo']);
                $stmtRepuestos->execute();
                $repuestosEquipo = $stmtRepuestos->get_result()->fetch_all(MYSQLI_ASSOC);
                
                foreach ($repuestosEquipo as $repuesto) {
                    $repuesto['equipo'] = $equipo['equipo'];
                    $repuesto['marca'] = $equipo['marca'];
                    $repuesto['modelo'] = $equipo['modelo'];
                    $repuesto['numero_serie'] = $equipo['numero_serie'];
                    $repuestos[] = $repuesto;
                }
            }

            // Crear archivo Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Establecer título
            $sheet->setCellValue('A1', 'REPORTE DE INVENTARIO - ORDEN DE TRABAJO N° ' . $data['numero'] . '/' . date('Y'));
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Información del cliente
            $sheet->setCellValue('A3', 'Cliente:');
            $sheet->setCellValue('B3', $data['datos'] . ' - ' . (strlen($data['documento']) === 8 ? 'DNI' : 'RUC') . ' N° ' . $data['documento']);
            $sheet->mergeCells('B3:G3');
            
            $sheet->setCellValue('A4', 'Dirección:');
            $sheet->setCellValue('B4', $data['direccion']);
            $sheet->mergeCells('B4:G4');
            
            $sheet->setCellValue('A5', 'Atención:');
            $sheet->setCellValue('B5', $data['atencion']);
            $sheet->mergeCells('B5:G5');
            
            // Encabezados de tabla
            $sheet->setCellValue('A7', 'ITEM');
            $sheet->setCellValue('B7', 'CÓDIGO');
            $sheet->setCellValue('C7', 'DESCRIPCIÓN');
            $sheet->setCellValue('D7', 'EQUIPO');
            $sheet->setCellValue('E7', 'CANTIDAD');
            $sheet->setCellValue('F7', 'STOCK ACTUAL');
            $sheet->setCellValue('G7', 'STOCK FINAL');
            
            $sheet->getStyle('A7:G7')->getFont()->setBold(true);
            $sheet->getStyle('A7:G7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CC0000');
            $sheet->getStyle('A7:G7')->getFont()->getColor()->setRGB('FFFFFF');
            
            // Datos de repuestos
            $row = 8;
            $totalItems = 0;
            
            foreach ($repuestos as $index => $repuesto) {
                $stockFinal = $repuesto['stock_actual'] - $repuesto['cantidad'];
                $stockFinal = $stockFinal < 0 ? 0 : $stockFinal;
                $totalItems += $repuesto['cantidad'];
                
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $repuesto['codigo'] ?: 'Sin Código');
                $sheet->setCellValue('C' . $row, $repuesto['nombre']);
                $sheet->setCellValue('D' . $row, $repuesto['equipo'] . ' (' . $repuesto['marca'] . ') - ' . $repuesto['modelo']);
                $sheet->setCellValue('E' . $row, $repuesto['cantidad']);
                $sheet->setCellValue('F' . $row, $repuesto['stock_actual']);
                $sheet->setCellValue('G' . $row, $stockFinal);
                
                $row++;
            }
            
            // Total
            $sheet->setCellValue('A' . $row, 'TOTAL ITEMS:');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->setCellValue('E' . $row, $totalItems);
            
            // Estilos de tabla
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A7:G' . $row)->applyFromArray($styleArray);
            
            // Ajustar anchos de columna
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(40);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            
            // Guardar archivo
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = "Reporte_Inventario_Taller_JVC_{$id_cotizacion}.xlsx";
            
            // Configurar headers para descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            error_log("Error generando Excel de inventario: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error generando el Excel: ' . $e->getMessage()]);
        }
    }
}