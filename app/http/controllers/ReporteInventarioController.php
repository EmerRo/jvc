<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';

class ReporteInventarioController extends Controller
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

    public function generarReporteInventarioPDF()
    {
        try {
            // Verificar permisos
            $permisos = $this->verificarPermisos();
            $esRolOrdenTrabajo = $permisos['esRolOrdenTrabajo'];

            // Si no es rol ORDEN TRABAJO o ADMIN, redirigir o mostrar error
            if (!$esRolOrdenTrabajo) {
                echo "No tiene permisos para acceder a este reporte.";
                return;
            }

            // Obtener parámetros
            $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'hoy';
            $tipoOrden = isset($_GET['tipo_orden']) ? $_GET['tipo_orden'] : 'todos';
            $idPreAlerta = isset($_GET['id']) ? $_GET['id'] : null;

            // Calcular fechas según el período seleccionado
            list($fechaDesde, $fechaHasta) = $this->calcularFechasPeriodo($periodo);

            // Construir consulta SQL según los filtros
            // Modifica la consulta SQL así:
            $sql = "SELECT trc.*, r.nombre, r.codigo, r.cantidad + trc.cantidad as stock_actual, 
'repuestos' as tipo_tabla, p.cliente_razon_social, p.origen, p.fecha_ingreso,
tce.equipo, tce.marca, tce.modelo, tce.numero_serie
FROM taller_repuestos_cotis trc 
INNER JOIN repuestos r ON trc.id_repuesto = r.id_repuesto 
INNER JOIN taller_cotizaciones tc ON trc.id_coti = tc.id_cotizacion
INNER JOIN taller_cotizaciones_equipos tce ON trc.id_cotizacion_equipo = tce.id_cotizacion_equipo
INNER JOIN pre_alerta p ON tc.id_prealerta = p.id_preAlerta ";

            if ($idPreAlerta) {
                $sql .= " WHERE tc.id_prealerta = ? ";
            } else {
                if ($fechaDesde && $fechaHasta) {
                    $sql .= " WHERE p.fecha_ingreso BETWEEN ? AND ? ";
                }
                if ($tipoOrden !== 'todos') {
                    $sql .= $fechaDesde ? " AND " : " WHERE ";
                    $sql .= " p.origen = ? ";
                }
            }

            // Y lo mismo para la segunda parte del UNION:
            $sql .= " UNION 
SELECT trc.*, p2.nombre, p2.codigo, p2.cantidad + trc.cantidad as stock_actual, 
'productos' as tipo_tabla, pa.cliente_razon_social, pa.origen, pa.fecha_ingreso,
tce.equipo, tce.marca, tce.modelo, tce.numero_serie
FROM taller_repuestos_cotis trc 
INNER JOIN productos p2 ON trc.id_repuesto = p2.id_producto 
INNER JOIN taller_cotizaciones tc ON trc.id_coti = tc.id_cotizacion
INNER JOIN taller_cotizaciones_equipos tce ON trc.id_cotizacion_equipo = tce.id_cotizacion_equipo
INNER JOIN pre_alerta pa ON tc.id_prealerta = pa.id_preAlerta ";

            if ($idPreAlerta) {
                $sql .= " WHERE tc.id_prealerta = ? ";
            } else {
                if ($fechaDesde && $fechaHasta) {
                    $sql .= " WHERE pa.fecha_ingreso BETWEEN ? AND ? ";
                }
                if ($tipoOrden !== 'todos') {
                    $sql .= $fechaDesde ? " AND " : " WHERE ";
                    $sql .= " pa.origen = ? ";
                }
            }

            $sql .= " ORDER BY fecha_ingreso DESC";


            $params = [];
            $types = "";

            if ($idPreAlerta) {
                $params[] = $idPreAlerta;
                $params[] = $idPreAlerta; // Para la segunda parte del UNION
                $types .= "ii"; // dos integers
            } else {
                if ($fechaDesde && $fechaHasta) {
                    $params[] = $fechaDesde;
                    $params[] = $fechaHasta;
                    $params[] = $fechaDesde;
                    $params[] = $fechaHasta;
                    $types .= "ssss"; // cuatro strings
                }
                if ($tipoOrden !== 'todos') {
                    $params[] = $tipoOrden;
                    $params[] = $tipoOrden;
                    $types .= "ss"; // dos strings
                }
            }

            // Preparar y ejecutar la consulta
            $stmt = $this->conexion->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            error_log("SQL Query: " . $sql);
            error_log("Params: " . print_r($params, true));
            // Ejecutar consulta
            $stmt = $this->conexion->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $resultado = $stmt->get_result();
            $inventario = [];

            while ($row = $resultado->fetch_assoc()) {
                $inventario[] = $row;
            }

            // Verificar si hay datos
            if (empty($inventario)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No hay datos disponibles para el período seleccionado.']);
                return;
            }

            // Generar PDF con los datos
            $this->generarPDF($inventario, $tipoOrden, $periodo, $idPreAlerta);

        } catch (Exception $e) {
            error_log("Error generando reporte de inventario: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error generando el reporte: ' . $e->getMessage()]);
        }
    }

    private function calcularFechasPeriodo($periodo)
    {
        $fechaDesde = null;
        $fechaHasta = date('Y-m-d'); // Hoy por defecto

        switch ($periodo) {
            case 'hoy':
                $fechaDesde = date('Y-m-d');
                break;

            case 'ayer':
                $fechaDesde = date('Y-m-d', strtotime('-1 day'));
                $fechaHasta = date('Y-m-d', strtotime('-1 day'));
                break;

            case 'esta_semana':
                $fechaDesde = date('Y-m-d', strtotime('monday this week'));
                break;

            case 'semana_pasada':
                $fechaDesde = date('Y-m-d', strtotime('monday last week'));
                $fechaHasta = date('Y-m-d', strtotime('sunday last week'));
                break;

            case 'este_mes':
                $fechaDesde = date('Y-m-01');
                break;

            case 'mes_pasado':
                $fechaDesde = date('Y-m-01', strtotime('first day of last month'));
                $fechaHasta = date('Y-m-t', strtotime('last day of last month'));
                break;

            default:
                // Si es un mes específico (formato: mes_numero, ej: mes_1 para enero)
                if (preg_match('/^mes_(\d{1,2})$/', $periodo, $matches)) {
                    $mes = (int) $matches[1];
                    if ($mes >= 1 && $mes <= 12) {
                        $anio = date('Y');
                        $fechaDesde = date('Y-m-d', strtotime("$anio-$mes-01"));
                        $fechaHasta = date('Y-m-t', strtotime("$anio-$mes-01"));
                    }
                }
                break;
        }

        return [$fechaDesde, $fechaHasta];
    }

    private function generarPDF($inventario, $tipoOrden, $periodo, $idPreAlerta)
    {
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
    
        // Establecer el encabezado HTML
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
        $html = $this->generarInventarioHTML($inventario, $tipoOrden, $periodo, $idPreAlerta);
        $mpdf->WriteHTML($html);
    
        // Generar PDF
        $filename = "Reporte_Inventario_Taller_JVC";
        if ($idPreAlerta) {
            $filename .= "_{$idPreAlerta}";
        } else {
            $filename .= "_{$periodo}";
        }
        $mpdf->Output("{$filename}.pdf", 'I');
    }

    private function generarInventarioHTML($inventario, $tipoOrden, $periodo, $idPreAlerta)
    {
        $fechaActual = "Lima, " . $this->formatearFechaEspanol(date('Y-m-d'));
        $year = date('Y');
    
        // Determinar el título según los filtros
        $titulo = "REPORTE DE INVENTARIO";
        if ($tipoOrden !== 'todos') {
            $titulo .= " - " . $tipoOrden;
        }
    
        // Agregar información del período
        $periodoTexto = $this->obtenerTextoPeriodo($periodo);
        if ($periodoTexto) {
            $titulo .= " - " . $periodoTexto;
        }
    
        // Si es un reporte individual, obtener datos del cliente
        $clienteInfo = '';
        if ($idPreAlerta && !empty($inventario)) {
            $clienteInfo = "
            <div class='client-info'>
                <p><strong>Cliente:</strong> {$inventario[0]['cliente_razon_social']}</p>
                <p><strong>Origen:</strong> {$inventario[0]['origen']}</p>
                <p><strong>Fecha de Ingreso:</strong> " . date('d/m/Y', strtotime($inventario[0]['fecha_ingreso'])) . "</p>
            </div>";
        }
    
        $html = "
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                font-size: 12px;
            }
            .content-wrapper {
                margin: 0 10px;
                padding-top: 10px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                font-size: 8px; /* Tamaño de fuente reducido a 8px */
                border: 1px solid #cc0000; /* Solo borde exterior */
            }
            th {
                background-color: #cc0000;
                color: white;
                font-weight: bold;
                padding: 3px; /* Padding reducido */
                text-align: left;
            }
            td {
                padding: 3px; /* Padding reducido */
                text-align: left;
              
            }
            /* Agregar borde inferior solo a la última fila */
            tr:last-child td {
                border-bottom: 1px solid #cc0000;
            }
            /* Agregar borde superior a la primera fila (encabezados) */
            thead tr:first-child th {
                border-top: 1px solid #cc0000;
            }
            /* Agregar bordes a los encabezados */
            th {
                border-left: 1px solid #cc0000;
                border-right: 1px solid #cc0000;
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
            /* Estilo para filas alternas para mejor legibilidad */
            tr:nth-child(even) {
                background-color:#fdf5e6;
            }
        </style>
        
        <div class='content-wrapper'>
            <div style='text-align: right; margin-bottom: 10px;'>
                {$fechaActual}
            </div>
            
            <div class='title'>
                {$titulo}
            </div>
            
            {$clienteInfo}
            
            <p>A continuación se detalla el inventario de repuestos utilizados:</p>
            
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
        foreach ($inventario as $index => $repuesto) {
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
                <p>Este reporte muestra los repuestos y productos utilizados en las órdenes de trabajo. El stock final es el resultado de restar la cantidad utilizada del stock actual.</p>
            </div>
        </div>";
    
        return $html;
    }
    private function obtenerTextoPeriodo($periodo)
    {
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
        
        switch ($periodo) {
            case 'hoy':
                return 'HOY (' . date('d/m/Y') . ')';
            case 'ayer':
                return 'AYER (' . date('d/m/Y', strtotime('-1 day')) . ')';
            case 'esta_semana':
                return 'ESTA SEMANA (' . date('d/m/Y', strtotime('monday this week')) . ' - ' . date('d/m/Y') . ')';
            case 'semana_pasada':
                return 'SEMANA PASADA (' . date('d/m/Y', strtotime('monday last week')) . ' - ' . date('d/m/Y', strtotime('sunday last week')) . ')';
            case 'este_mes':
                $nombreMes = $this->obtenerNombreMes(date('n'));
                return 'MES ACTUAL (' . $nombreMes . ' ' . date('Y') . ')';
            case 'mes_pasado':
                $nombreMes = $this->obtenerNombreMes(date('n', strtotime('first day of last month')));
                return 'MES PASADO (' . $nombreMes . ' ' . date('Y', strtotime('first day of last month')) . ')';
            default:
                // Si es un mes específico (formato: mes_numero, ej: mes_1 para enero)
                if (preg_match('/^mes_(\d{1,2})$/', $periodo, $matches)) {
                    $mes = (int) $matches[1];
                    if ($mes >= 1 && $mes <= 12) {
                        $nombresMeses = [
                            1 => 'ENERO',
                            2 => 'FEBRERO',
                            3 => 'MARZO',
                            4 => 'ABRIL',
                            5 => 'MAYO',
                            6 => 'JUNIO',
                            7 => 'JULIO',
                            8 => 'AGOSTO',
                            9 => 'SEPTIEMBRE',
                            10 => 'OCTUBRE',
                            11 => 'NOVIEMBRE',
                            12 => 'DICIEMBRE'
                        ];
                        return $nombresMeses[$mes] . ' ' . date('Y');
                    }
                }
                return '';
        }
    }
    
    // Función auxiliar para obtener el nombre del mes en español
    private function obtenerNombreMes($mes)
    {
        $nombresMeses = [
            1 => 'ENERO',
            2 => 'FEBRERO',
            3 => 'MARZO',
            4 => 'ABRIL',
            5 => 'MAYO',
            6 => 'JUNIO',
            7 => 'JULIO',
            8 => 'AGOSTO',
            9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVIEMBRE',
            12 => 'DICIEMBRE'
        ];
        
        return $nombresMeses[$mes];
    }

    public function generarReporteInventarioExcel()
    {
        try {
            // Verificar permisos
            $permisos = $this->verificarPermisos();
            $esRolOrdenTrabajo = $permisos['esRolOrdenTrabajo'];

            // Si no es rol ORDEN TRABAJO o ADMIN, redirigir o mostrar error
            if (!$esRolOrdenTrabajo) {
                echo json_encode(['success' => false, 'message' => 'No tiene permisos para acceder a este reporte.']);
                return;
            }

            // Obtener parámetros
            $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'hoy';
            $tipoOrden = isset($_GET['tipo_orden']) ? $_GET['tipo_orden'] : 'todos';
            $idPreAlerta = isset($_GET['id']) ? $_GET['id'] : null;

            // Calcular fechas según el período seleccionado
            list($fechaDesde, $fechaHasta) = $this->calcularFechasPeriodo($periodo);

            // Construir consulta SQL según los filtros (igual que en generarReportePDF)
            $sql = "SELECT trc.*, r.nombre, r.codigo, r.cantidad + trc.cantidad as stock_actual, 
            'repuestos' as tipo_tabla, p.cliente_razon_social, p.origen, p.fecha_ingreso,
            tce.equipo, tce.marca, tce.modelo, tce.numero_serie
            FROM taller_repuestos_cotis trc 
            INNER JOIN repuestos r ON trc.id_repuesto = r.id_repuesto 
            INNER JOIN taller_cotizaciones tc ON trc.id_coti = tc.id_cotizacion
            INNER JOIN taller_cotizaciones_equipos tce ON trc.id_cotizacion_equipo = tce.id_cotizacion_equipo
            INNER JOIN pre_alerta p ON tc.id_prealerta = p.id_preAlerta ";

            if ($idPreAlerta) {
                $sql .= " WHERE tc.id_prealerta = ? ";
            } else {
                if ($fechaDesde && $fechaHasta) {
                    $sql .= " WHERE p.fecha_ingreso BETWEEN ? AND ? ";
                }
                if ($tipoOrden !== 'todos') {
                    $sql .= $fechaDesde ? " AND " : " WHERE ";
                    $sql .= " p.origen = ? ";
                }
            }

            $sql .= " UNION 
            SELECT trc.*, p2.nombre, p2.codigo, p2.cantidad + trc.cantidad as stock_actual, 
            'productos' as tipo_tabla, pa.cliente_razon_social, pa.origen, pa.fecha_ingreso,
            tce.equipo, tce.marca, tce.modelo, tce.numero_serie
            FROM taller_repuestos_cotis trc 
            INNER JOIN productos p2 ON trc.id_repuesto = p2.id_producto 
            INNER JOIN taller_cotizaciones tc ON trc.id_coti = tc.id_cotizacion
            INNER JOIN taller_cotizaciones_equipos tce ON trc.id_cotizacion_equipo = tce.id_cotizacion_equipo
            INNER JOIN pre_alerta pa ON tc.id_prealerta = pa.id_preAlerta ";

            if ($idPreAlerta) {
                $sql .= " WHERE tc.id_prealerta = ? ";
            } else {
                if ($fechaDesde && $fechaHasta) {
                    $sql .= " WHERE pa.fecha_ingreso BETWEEN ? AND ? ";
                }
                if ($tipoOrden !== 'todos') {
                    $sql .= $fechaDesde ? " AND " : " WHERE ";
                    $sql .= " pa.origen = ? ";
                }
            }

            $sql .= " ORDER BY fecha_ingreso DESC";


            $params = [];
            $types = "";
            if ($idPreAlerta) {
                $params[] = $idPreAlerta;
                $params[] = $idPreAlerta; // Para la segunda parte del UNION
                $types .= "ii"; // dos integers
            } else {
                if ($fechaDesde && $fechaHasta) {
                    $params[] = $fechaDesde;
                    $params[] = $fechaHasta;
                    $params[] = $fechaDesde;
                    $params[] = $fechaHasta;
                    $types .= "ssss"; // cuatro strings
                }
                if ($tipoOrden !== 'todos') {
                    $params[] = $tipoOrden;
                    $params[] = $tipoOrden;
                    $types .= "ss"; // dos strings
                }
            }

            // Preparar y ejecutar la consulta
            $stmt = $this->conexion->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $resultado = $stmt->get_result();
            $inventario = [];

            while ($row = $resultado->fetch_assoc()) {
                $inventario[] = $row;
            }

            // Verificar si hay datos
            if (empty($inventario)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No hay datos disponibles para el período seleccionado.']);
                return;
            }

            // Generar Excel con los datos
            $this->generarExcel($inventario, $tipoOrden, $periodo, $idPreAlerta);

        } catch (Exception $e) {
            error_log("Error generando Excel de inventario: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error generando el Excel: ' . $e->getMessage()]);
        }
    }

    private function generarExcel($inventario, $tipoOrden, $periodo, $idPreAlerta)
    {
        // Crear archivo Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Determinar el título según los filtros
        $titulo = "REPORTE DE INVENTARIO";
        if ($tipoOrden !== 'todos') {
            $titulo .= " - " . $tipoOrden;
        }

        // Agregar información del período
        $periodoTexto = $this->obtenerTextoPeriodo($periodo);
        if ($periodoTexto) {
            $titulo .= " - " . $periodoTexto;
        }

        // Establecer título
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Si es un reporte individual, agregar información del cliente
        $row = 3;
        if ($idPreAlerta && !empty($inventario)) {
            $sheet->setCellValue('A3', 'Cliente:');
            $sheet->setCellValue('B3', $inventario[0]['cliente_razon_social']);
            $sheet->mergeCells('B3:G3');

            $sheet->setCellValue('A4', 'Origen:');
            $sheet->setCellValue('B4', $inventario[0]['origen']);
            $sheet->mergeCells('B4:G4');

            $sheet->setCellValue('A5', 'Fecha de Ingreso:');
            $sheet->setCellValue('B5', date('d/m/Y', strtotime($inventario[0]['fecha_ingreso'])));
            $sheet->mergeCells('B5:G5');

            $row = 7;
        }

        // Encabezados de tabla
        $sheet->setCellValue('A' . $row, 'ITEM');
        $sheet->setCellValue('B' . $row, 'CÓDIGO');
        $sheet->setCellValue('C' . $row, 'DESCRIPCIÓN');
        $sheet->setCellValue('D' . $row, 'EQUIPO');
        $sheet->setCellValue('E' . $row, 'CANTIDAD');
        $sheet->setCellValue('F' . $row, 'STOCK ACTUAL');
        $sheet->setCellValue('G' . $row, 'STOCK FINAL');

        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CC0000');
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->getColor()->setRGB('FFFFFF');

        // Datos de repuestos
        $row++;
        $totalItems = 0;

        foreach ($inventario as $index => $repuesto) {
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
        $sheet->getStyle('A' . ($row - count($inventario)) . ':G' . $row)->applyFromArray($styleArray);

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
        $filename = "Reporte_Inventario_Taller_JVC";
        if ($idPreAlerta) {
            $filename .= "_{$idPreAlerta}";
        } else {
            $filename .= "_{$periodo}";
        }
        $filename .= ".xlsx";

        // Configurar headers para descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function verificarDatosDisponibles()
    {
        try {
            // Obtener parámetros
            $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'hoy';
            $tipoOrden = isset($_GET['tipo_orden']) ? $_GET['tipo_orden'] : 'todos';

            // Calcular fechas según el período seleccionado
            list($fechaDesde, $fechaHasta) = $this->calcularFechasPeriodo($periodo);

            // Construir consulta SQL para verificar si hay datos
            $sql = "SELECT COUNT(*) as total
                    FROM taller_repuestos_cotis trc 
                    INNER JOIN taller_cotizaciones tc ON trc.id_coti = tc.id_cotizacion
                    INNER JOIN pre_alerta p ON tc.id_prealerta = p.id_preAlerta
                    WHERE p.fecha_ingreso BETWEEN ? AND ?";

            if ($tipoOrden !== 'todos') {
                $sql .= " AND p.origen = ?";
            }

            // Ejecutar consulta
            $stmt = $this->conexion->prepare($sql);

            // Verificar si la preparación fue exitosa
            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
            }

            if ($tipoOrden !== 'todos') {
                $stmt->bind_param("sss", $fechaDesde, $fechaHasta, $tipoOrden);
            } else {
                $stmt->bind_param("ss", $fechaDesde, $fechaHasta);
            }

            $stmt->execute();
            $resultado = $stmt->get_result();
            $data = $resultado->fetch_assoc();

            // Verificar si hay datos
            if ($data['total'] > 0) {
                echo json_encode(['success' => true, 'total' => $data['total']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No hay datos disponibles para el período seleccionado.']);
            }
        } catch (Exception $e) {
            error_log("Error verificando datos disponibles: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al verificar datos: ' . $e->getMessage()]);
        }
    }
}

