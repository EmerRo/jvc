<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Garantia.php";

class ReporteTallerController extends Controller
{
    private $mpdf;
    private $conexion;

    protected $request;

    public function __construct()
    {
        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            // 'margin_top' => 25, //margin inicial 
            'margin_bottom' => 30, // Margen para el pie de página
            'margin_header' => 0,
            'margin_footer' => 0,
            'setAutoTopMargin' => false
        ]);
        $this->conexion = (new Conexion())->getConexion();
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    private function formatearFechaEspanol($fecha)
    {
        $meses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $fecha = new DateTime($fecha);
        return $fecha->format('d') . ' de ' . $meses[$fecha->format('n') - 1] . ' del ' . $fecha->format('Y');
    }

    public function generateReport($id_cotizacion)
    {
        try {
            // Verificar permisos
            $permisos = $this->verificarPermisos();
            $puedeVerPrecios = $permisos['puedeVerPrecios'];
            $esRolOrdenTrabajo = $permisos['esRolOrdenTrabajo'];

            // Obtener datos de la cotización y cliente
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

            // En la consulta SQL, asegúrate de que seleccionas la columna como COALESCE para manejar valores NULL
            $sqlRepuestos = "SELECT trc.*, r.nombre, COALESCE(r.codigo, 'Sin Código') as codigo 
                FROM taller_repuestos_cotis trc 
                INNER JOIN repuestos r ON trc.id_repuesto = r.id_repuesto 
                WHERE trc.id_coti = ? AND trc.id_cotizacion_equipo = ?";
            $stmtRepuestos = $this->conexion->prepare($sqlRepuestos);
            if ($stmtRepuestos === false) {
                throw new Exception("Error al preparar la consulta de repuestos: " . $this->conexion->error);
            }

            // Obtener términos y condiciones específicos de la cotización
            $sqlTerminos = "SELECT condiciones FROM taller_condiciones_cotizacion WHERE id_cotizacion = ? LIMIT 1";
            $stmtTerminos = $this->conexion->prepare($sqlTerminos);
            if ($stmtTerminos === false) {
                throw new Exception("Error al preparar la consulta de términos: " . $this->conexion->error);
            }

            $stmtTerminos->bind_param("i", $id_cotizacion);
            if (!$stmtTerminos->execute()) {
                throw new Exception("Error al ejecutar la consulta de términos: " . $stmtTerminos->error);
            }

            $resultTerminos = $stmtTerminos->get_result();
            $terminos = $resultTerminos->fetch_assoc();

            // Si no hay términos específicos para esta cotización, usar los términos por defecto
            if (!$terminos) {
                $sqlTerminosDefault = "SELECT nombre FROM terminos_repuestos LIMIT 1";
                $stmtTerminosDefault = $this->conexion->prepare($sqlTerminosDefault);
                if ($stmtTerminosDefault === false) {
                    throw new Exception("Error al preparar la consulta de términos por defecto: " . $this->conexion->error);
                }

                if (!$stmtTerminosDefault->execute()) {
                    throw new Exception("Error al ejecutar la consulta de términos por defecto: " . $stmtTerminosDefault->error);
                }

                $terminosDefault = $stmtTerminosDefault->get_result()->fetch_assoc();
                $terminosText = $terminosDefault ? $terminosDefault['nombre'] : '';
            } else {
                $terminosText = $terminos['condiciones'];
            }

            // Obtener diagnóstico específico de la cotización
            $sqlDiagnostico = "SELECT diagnostico FROM taller_diagnosticos_cotizacion WHERE id_cotizacion = ? LIMIT 1";
            $stmtDiagnostico = $this->conexion->prepare($sqlDiagnostico);
            if ($stmtDiagnostico === false) {
                throw new Exception("Error al preparar la consulta de diagnóstico: " . $this->conexion->error);
            }

            $stmtDiagnostico->bind_param("i", $id_cotizacion);
            if (!$stmtDiagnostico->execute()) {
                throw new Exception("Error al ejecutar la consulta de diagnóstico: " . $stmtDiagnostico->error);
            }

            $resultDiagnostico = $stmtDiagnostico->get_result();
            $diagnostico = $resultDiagnostico->fetch_assoc();

            // Si no hay diagnóstico específico para esta cotización, usar el diagnóstico por defecto
            if (!$diagnostico) {
                $sqlDiagnosticoDefault = "SELECT nombre FROM diagnostico_repuestos LIMIT 1";
                $stmtDiagnosticoDefault = $this->conexion->prepare($sqlDiagnosticoDefault);
                if ($stmtDiagnosticoDefault === false) {
                    throw new Exception("Error al preparar la consulta de diagnóstico por defecto: " . $this->conexion->error);
                }

                if (!$stmtDiagnosticoDefault->execute()) {
                    throw new Exception("Error al ejecutar la consulta de diagnóstico por defecto: " . $stmtDiagnosticoDefault->error);
                }

                $diagnosticoDefault = $stmtDiagnosticoDefault->get_result()->fetch_all(MYSQLI_ASSOC);
                $diagnosticoText = $diagnosticoDefault ? $diagnosticoDefault[0]['nombre'] : '';
            } else {
                $diagnosticoText = $diagnostico['diagnostico'];
            }

            // Obtener observaciones para rol ORDEN TRABAJO
            $observacionesText = '';
            if ($esRolOrdenTrabajo) {
                $sqlObservaciones = "SELECT observaciones FROM taller_observaciones_cotizacion WHERE id_cotizacion = ? LIMIT 1";
                $stmtObservaciones = $this->conexion->prepare($sqlObservaciones);
                if ($stmtObservaciones === false) {
                    throw new Exception("Error al preparar la consulta de observaciones: " . $this->conexion->error);
                }

                $stmtObservaciones->bind_param("i", $id_cotizacion);
                if (!$stmtObservaciones->execute()) {
                    throw new Exception("Error al ejecutar la consulta de observaciones: " . $stmtObservaciones->error);
                }

                $resultObservaciones = $stmtObservaciones->get_result();
                $observaciones = $resultObservaciones->fetch_assoc();
                $observacionesText = $observaciones ? $observaciones['observaciones'] : 'No hay observaciones registradas.';
            }

            // Configurar MPDF con opciones mejoradas para tablas
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 35,
                'margin_bottom' => 30, // Margen para el pie de página
                'margin_header' => 0,
                'margin_footer' => 0,
                'setAutoTopMargin' => false, // Cambiar a false para evitar márgenes automáticos
                'setAutoBottomMargin' => false, // Cambiar a false para evitar márgenes automáticos
                'tabSpaces' => 4,
                'shrink_tables_to_fit' => 1,
                'use_kwt' => true, // Keep with table - evita que las tablas se dividan entre páginas
                'packTableData' => true,
                'dpi' => 96, // Resolución para cálculos de tamaño
                'tempDir' => sys_get_temp_dir() // Directorio temporal para archivos
            ]);
            // Establecer el encabezado HTML
            $headerHTML = '<div style="width: 100%; margin: 0; padding: 0;">
    <img src="public/assets/img/encabezado.jpg" style="width: 100%; margin: 0; padding: 0; margin-left: 20px;">
</div>';

            // Configurar el encabezado
            $mpdf->SetHTMLHeader($headerHTML);

            // Configurar margen superior para la primera página
            $mpdf->AddPageByArray([
                'margin-top' => 40,
                'resetpagenum' => 1,
                'suppress' => 'off'
            ]);

            // Configurar margen superior para las páginas siguientes usando SetTopMargin
            $mpdf->SetTopMargin(45);


            // Definir el pie de página HTML
            $footerHTML = '<div style="position: absolute; bottom: 0; left: 0; right: 0; width: 100%;">
           <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin-right: 10px;">
       </div>';
            $mpdf->SetHTMLFooter($footerHTML);


            // Procesar cada equipo
            foreach ($equipos as $index => $equipo) {
                // Obtener fotos del equipo actual
                $sqlFotos = "SELECT nombre_foto FROM taller_cotizaciones_fotos 
                 WHERE id_cotizacion = ? AND equipo_index = ?";
                $stmtFotos = $this->conexion->prepare($sqlFotos);
                if ($stmtFotos === false) {
                    throw new Exception("Error al preparar la consulta de fotos: " . $this->conexion->error);
                }

                // Aquí usamos el índice del equipo (0, 1, etc.)
                $equipoIndex = $index;
                $stmtFotos->bind_param("ii", $id_cotizacion, $equipoIndex);
                if (!$stmtFotos->execute()) {
                    throw new Exception("Error al ejecutar la consulta de fotos: " . $stmtFotos->error);
                }

                $resultFotos = $stmtFotos->get_result();
                $fotos = [];
                while ($row = $resultFotos->fetch_assoc()) {
                    $fotos[] = $row['nombre_foto'];
                }
                $equipo['fotos'] = $fotos;

                // Agregar nueva página si no es el primer equipo
                if ($index > 0) {
                    $mpdf->AddPageByArray([
                        'margin-top' => 45,
                        'resetpagenum' => 0,
                        'suppress' => 'off'
                    ]);
                }

                // Obtener repuestos del equipo actual
                $stmtRepuestos->bind_param("ii", $id_cotizacion, $equipo['id_cotizacion_equipo']);
                if (!$stmtRepuestos->execute()) {
                    throw new Exception("Error al ejecutar la consulta de repuestos: " . $stmtRepuestos->error);
                }
                $repuestos = $stmtRepuestos->get_result()->fetch_all(MYSQLI_ASSOC);

                // Generar HTML para la tabla y los totales
                $tablaHTML = $this->generateTablaHTML($data, $repuestos, $equipo, $index + 1, count($equipos), $puedeVerPrecios);
                $mpdf->WriteHTML($tablaHTML);

                // Para rol ORDEN TRABAJO, mostrar observaciones en lugar de diagnóstico y condiciones
                if ($esRolOrdenTrabajo) {
                    // Generar HTML para las observaciones
                    $observacionesHTML = $this->generateObservacionesHTML($observacionesText);
                    $mpdf->WriteHTML($observacionesHTML);
                } else {
                    // Generar HTML para el diagnóstico
                    $diagnosticoHTML = $this->generateDiagnosticoHTML($diagnosticoText);
                    $mpdf->WriteHTML($diagnosticoHTML);

                    // Generar HTML para las condiciones
                    $condicionesHTML = $this->generateCondicionesHTML($terminosText);
                    $mpdf->WriteHTML($condicionesHTML);
                }

                // Solo agregar página de fotos si hay fotos para mostrar
                if (!empty($fotos)) {
                    $mpdf->AddPageByArray([
                        'margin-top' => 45,
                        'resetpagenum' => 0,
                        'suppress' => 'off'
                    ]);
                    $htmlFotos = $this->generatePhotosPage($fotos, $equipo);
                    $mpdf->WriteHTML($htmlFotos);
                }
            }

            // Generar PDF
            $mpdf->Output("Cotizacion_Taller_JVC_{$id_cotizacion}.pdf", 'I');


        } catch (Exception $e) {
            error_log("Error generando reporte: " . $e->getMessage());
            echo "Error generando el reporte: " . $e->getMessage();
        }
    }

    // Método para generar el HTML de la tabla y los totales
    private function generateTablaHTML($data, $repuestos, $equipo, $equipoIndex, $totalEquipos, $puedeVerPrecios = true)
    {
        $total = 0;
        $repuestosHtml = '';
        $tipoDoc = strlen($data['documento']) === 8 ? 'DNI' : 'RUC';

        // Verificar si es rol ORDEN TRABAJO
        $esOrdenTrabajo = false;
        if (isset($_SESSION['id_rol'])) {
            $rolId = $_SESSION['id_rol'];
            $sqlRol = "SELECT nombre FROM roles WHERE rol_id = ?";
            $stmtRol = $this->conexion->prepare($sqlRol);
            $stmtRol->bind_param("i", $rolId);
            $stmtRol->execute();
            $resultRol = $stmtRol->get_result();

            if ($rowRol = $resultRol->fetch_assoc()) {
                $nombreRol = strtoupper($rowRol['nombre']);
                if ($nombreRol === 'ORDEN TRABAJO') {
                    $esOrdenTrabajo = true;
                }
            }
        }

        foreach ($repuestos as $index => $repuesto) {
            $subtotal = floatval($repuesto['cantidad']) * floatval($repuesto['precio']);
            $total += $subtotal;

            if ($puedeVerPrecios) {
                // Versión con precios (sin columna CÓDIGO)
                $repuestosHtml .= "
                <tr style='margin:0; padding:0;'>
                    <td style='border-right: 1px solid #C43438;border-left: 1px solid #C43438; padding: 1px; text-align: center'>" . ($index + 1) . "</td>
                    <td style='border-right: 1px solid #C43438; '>{$repuesto['nombre']}</td>
                    <td style='border-right: 1px solid #C43438;  text-align: center'>{$repuesto['cantidad']}</td>
                    <td style='border-right: 1px solid #C43438;  text-align: right'>S/ " . number_format($repuesto['precio'], 2) . "</td>
                    <td style='border-right: 1px solid #C43438; text-align: right'>S/ " . number_format($subtotal, 2) . "</td>
                </tr>";
            } else {
                // Versión sin precios
                if ($esOrdenTrabajo) {
                    // Para rol ORDEN TRABAJO - incluir columna CÓDIGO
                    $repuestosHtml .= "
                    <tr style='margin:0; padding:0; '>
                        <td style='border-right: 1px solid #C43438; border-left: 1px solid #C43438;  text-align: center'>" . ($index + 1) . "</td>
                        <td style='border-right: 1px solid #C43438; text-align: center;'>" . ($repuesto['codigo'] ?: 'Sin Código') . "</td>
                        <td style='border-right: 1px solid #C43438; '>{$repuesto['nombre']}</td>
                        <td style='border-right: 1px solid #C43438; border-left: 1px solid #C43438;  text-align: center'>{$repuesto['cantidad']}</td>
                    </tr>";
                } else {
                    // Para otros roles sin precios - sin columna CÓDIGO
                    $repuestosHtml .= "
                    <tr style='margin:0; padding:0;'>
                        <td style='border-right: 1px solid #C43438; border-left: 1px solid #C43438; padding: 5px; text-align: center'>" . ($index + 1) . "</td>
                        <td style='border-right: 1px solid #C43438; padding: 5px;'>{$repuesto['nombre']}</td>
                        <td style='border-right: 1px solid #C43438; border-left: 1px solid #C43438; padding: 5px; text-align: center'>{$repuesto['cantidad']}</td>
                    </tr>";
                }
            }
        }


        if (empty($repuestosHtml)) {
            if ($puedeVerPrecios) {
                $repuestosHtml = "
                <tr>
                    <td colspan='5' style='border: 1px solid #C43438; padding: 5px; text-align: center'>No hay repuestos registrados</td>
                </tr>";
            } else if ($esOrdenTrabajo) {
                $repuestosHtml = "
                <tr>
                    <td colspan='4' style='border: 1px solid #C43438; padding: 5px; text-align: center'>No hay repuestos registrados</td>
                </tr>";
            } else {
                $repuestosHtml = "
                <tr>
                    <td colspan='3' style='border: 1px solid #C43438; padding: 5px; text-align: center'>No hay repuestos registrados</td>
                </tr>";
            }
        }

        $cotizacionNumber = !empty($data['numero']) ? $data['numero'] : '21';
        $year = date('Y');
        $fechaActual = "Lima, " . $this->formatearFechaEspanol(date('Y-m-d'));

        $descuento = isset($data['descuento']) ? floatval($data['descuento']) : 0;
        $totalConDescuento = $total - ($total * ($descuento / 100));

        // Generar el HTML de la tabla
        $html = "
        <style>
        body {
            margin: 0;
            padding: 0;
        }
        .content-wrapper {
            margin: 0 30px;
            padding-top: 0;
            padding-bottom: 30px;
            position: relative;
            font-size: 12px;
            margin-top: -40px; /* Ajusta este valor según sea necesario para subir el contenido */
        }
        .header-wrapper {
            margin: 0;
            padding: 0;
            width: 100%;
            display: block;
            line-height: 0;
            font-size: 0; /* Elimina cualquier espacio en blanco */
        }
        .header-wrapper img {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
            vertical-align: top; /* Elimina espacio debajo de la imagen */
        }
        table {
            margin-bottom: 0px;
            font-size: 11px; 
        }
        .totales-row {
            page-break-inside: avoid; /* Mantiene las filas de totales juntas */
        }
        ul {
            margin: 0;
            padding-left: 20px;
        }
        h3 {
            font-size: 13px; 
        }
        th {
            font-size: 11px;
        }
        /* Estilos para repetir encabezados de tabla */
        thead {
            display: table-header-group;
        }
        .no-space {
            font-size: 0;
            line-height: 0;
            margin: 0;
            padding: 0;
            display: block;
        }
        </style>
    
        <div class='no-space'>
           
            
            <div class='content-wrapper'>
               <div style='position: absolute; top: 0; right: 30px; text-align: right; padding-right: 60px; '>
    {$fechaActual}
    </div>
               <div style='width: 70%; margin-left: auto; margin-right: auto; margin-top: 0;'>
                    <div style='text-align: center; border: 1px solid #000000; padding: 5px; margin-top: 20px;'>
                        <strong>COTIZACIÓN DE J.V.C. S.A.C. - N° {$cotizacionNumber}/{$year}</strong>
                    </div>
                </div>
    
                <div style='margin: 15px 0;'>
                    <p style='margin-bottom: 5px;'>
                        <strong>Señores: </strong>
                        <strong>{$data['datos']} - {$tipoDoc} N° {$data['documento']}</strong>
                    </p>
                    <p style='margin:1px;'>
                        <strong>Dirección: </strong>
                        {$data['direccion']}
                    </p>
                    <p style='margin:1px;'>
                        <strong>Atención: </strong>
                        <strong>{$data['atencion']}</strong>
                    </p>
                </div>
                <p>Por medio del presente documento nos dirigimos a ustedes para saludarlos cordialmente y asimismo hacerles llegar nuestra siguiente cotización:</p>";

        // Tabla con estructura diferente según permisos y rol
        if ($puedeVerPrecios) {
            // Tabla con 5 columnas (con precios, sin CÓDIGO)
            $html .= "
                <table style='width: 100%; border-collapse: collapse;' autosize='1'>
                    <thead>
                        <tr>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 8%;'>ITEM</th>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 52%;'>DESCRIPCIÓN</th>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 10%;'>CANT.</th>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 15%;'>COSTO UNIT SIN IGV</th>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 15%;'>COST TOTAL SIN IGV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style='border: 1px solid #C43438; padding: 5px; text-align: center;'> Eq. {$equipoIndex} de {$totalEquipos}</td>
                            <td style='border: 1px solid #C43438; padding: 0;'>
                                <table style='width: 100%; border-collapse: collapse; margin: 0; padding: 0;'>
                                    <tr>
                                        <td style='text-align: center; padding: 5px; border: none;'>
                                            <strong>{$equipo['equipo']} ({$equipo['marca']}) </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=' text-align: center; padding: 5px; border: none; margin: 0; width: 100%; border-spacing: 0;'>
                                            <strong>Modelo: {$equipo['modelo']} // Serie: {$equipo['numero_serie']}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style='border: 1px solid #C43438; padding: 5px; text-align: center;'></td>
                            <td style='border: 1px solid #C43438; padding: 5px; text-align: right;'></td>
                            <td style='border: 1px solid #C43438; padding: 5px; text-align: right;'></td>
                        </tr>
                        {$repuestosHtml}
                    </tbody>
                </table>
                
                <div class='totales-row'>
                    <table style='width: 100%; border-collapse: collapse; margin-top: 0; border: none;'>
                        <tr>
                            <td style='width: 70%; border-top: 1px solid #C43438'></td>
                            <td style='width: 15%; padding: 5px; border: 1px solid #C43438; text-align: start;'>
                                <strong>SUBTOTAL</strong>
                            </td>
                            <td style='width: 15%; padding: 5px; border: 1px solid #C43438; text-align: right;'>
                                <strong>S/ " . number_format($total, 2) . "</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style='width: 70%;'></td>
                            <td style='width: 15%; padding: 5px; border: 1px solid #C43438; text-align: start;'>
                                <strong>IGV</strong>
                            </td>
                            <td style='width: 15%; padding: 5px; border: 1px solid #C43438; text-align: right;'>
                                <strong>S/ " . number_format($total * 0.18, 2) . "</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style='width: 70%;'></td>
                            <td style='width: 15%; padding: 5px; border: 1px solid #C43438; background-color: #C43438; color: white; text-align: start;'>
                                <strong>TOTAL</strong>
                            </td>
                            <td style='width: 15%; padding: 5px; border: 1px solid #C43438; background-color: #C43438; color: white; text-align: right;'>
                                <strong>S/ " . number_format($totalConDescuento * 1.18, 2) . "</strong>
                            </td>
                        </tr>
                    </table>
                </div>";
        } else if ($esOrdenTrabajo) {
            // Tabla con 4 columnas para ORDEN TRABAJO (con CÓDIGO)
            $html .= "
                        <table style='width: 100%; border-collapse: collapse;' autosize='1'>
                            <thead>
                                <tr>
                                    <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 10%;'>ITEM</th>
                                    <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 9%;'>CÓDIGO</th>
                                    <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 71%;'>DESCRIPCIÓN</th>
                                    <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 10%;'>CANT.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style='border-bottom: 1px solid #C43438;'>
                                    <td style='border-right: 1px solid #C43438; border-left: 1px solid #C43438; border-bottom: 1px solid #C43438; padding: 5px; text-align: center;'>Eq. {$equipoIndex} de {$totalEquipos}</td>
            
                                    <td style='border-right: 1px solid #C43438; border-bottom: 1px solid #C43438; padding: 5px; text-align: center;'></td>
            
                                    <td style='border-right: 1px solid #C43438;border-bottom: 1px solid #C43438; padding: 5px;'>
                                       <table style='width:100%; border-collapse: collapse; margin: 0; padding: 0; '>
                                        <tr>
                                            <td style='text-align: center; padding: 5px; border: none;'>
                                                <strong>{$equipo['equipo']} ({$equipo['marca']}) </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: center; padding: 5px; border: none; margin: 0; width: 100%; border-spacing: 0;'>
                                                <strong>Modelo: {$equipo['modelo']} // Serie: {$equipo['numero_serie']}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                    </td>
                                    
                                    <td style='border-right: 1px solid #C43438;border-bottom: 1px solid #C43438; padding: 5px; text-align: center;'></td>
                                </tr>
                                
                                {$repuestosHtml}
                            </tbody>
                        </table>
                        <div style='border-bottom: 1px solid #C43438; margin-top: -1px; width: 100%;'></div>";
        } else {
            // Tabla con 3 columnas para otros roles sin precios (sin CÓDIGO)
            $html .= "
                <table style='width: 100%; border-collapse: collapse;' autosize='1'>
                    <thead>
                        <tr>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 10%;'>ITEM</th>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 70%;'>DESCRIPCIÓN</th>
                            <th style='background-color: #C43438; color: white; padding: 8px; border: 1px solid #C43438; text-align: center; width: 20%;'>CANT.</th>
                        </tr>
                    </thead>
                    <tbody >
                        <tr>
                            <td style='border: 1px solid #C43438; padding: 5px; text-align: center;'> Eq. {$equipoIndex} de {$totalEquipos}</td>
                            <td style='border: 1px solid #C43438; padding: 0;'>
                                <table style='width: 100%; border-collapse: collapse; margin: 0; padding: 0;'>
                                    <tr>
                                        <td style='text-align: center; padding: 5px; border: none;'>
                                            <strong>{$equipo['equipo']} ({$equipo['marca']}) </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=' text-align: center; padding: 5px; border: none; margin: 0; width: 100%; border-spacing: 0;'>
                                            <strong>Modelo: {$equipo['modelo']} // Serie: {$equipo['numero_serie']}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style='border: 1px solid #C43438; padding: 5px; text-align: center;'></td>
                        </tr>
                        {$repuestosHtml}
                        <tr>
                            <td colspan='3' style='border-bottom: 1px solid #C43438;'></td>
                        </tr>
                    </tbody>
                </table>";
        }

        $html .= "
            </div>
        </div>";

        return $html;
    }

    // Método para generar el HTML del diagnóstico
    private function generateDiagnosticoHTML($diagnostico)
    {
        return "
        <div style='margin-top: 0; page-break-before: avoid;'>
           
            
            <div class='content-wrapper' style='margin-top: 0; padding-top: 0;'>
                <div class='diagnostico' style='page-break-inside: avoid;'>
                    <h3 style='margin-bottom: 10px;'>Diagnóstico:</h3>
                    <ul>" . nl2br($diagnostico) . "</ul>
                </div>
            </div>
        </div>";
    }

    // Método para generar el HTML de las condiciones
    private function generateCondicionesHTML($terminos)
    {
        return "
        <div style='margin-top: 0; page-break-before: auto;'>
            
            
            <div class='content-wrapper' style='margin-top: 0; padding-top: 0;'>
                <div class='conditions' style='page-break-inside: avoid;'>
                    <h3 style='margin-bottom: 10px;'>Condiciones:</h3>
                    <ul>" . nl2br($terminos) . "</ul>
                </div>
            </div>
        </div>";
    }

    // Nuevo método para generar el HTML de las observaciones (para rol ORDEN TRABAJO)
    private function generateObservacionesHTML($observaciones)
    {
        return "
        <div style='margin-top: 0; page-break-before: avoid;'>
            <div class='content-wrapper' style='margin-top: 0; padding-top: 0;'>
                <div class='observaciones' style='page-break-inside: avoid;'>
                    <h3 style='margin-bottom: 10px;'>Observaciones:</h3>
                    <div style='border: 1px solid #C43438; padding: 10px; border-radius: 5px;'>" . nl2br($observaciones) . "</div>
                </div>
            </div>
        </div>";
    }

    // Método para generar el HTML de las fotos en una página separada
    private function generatePhotosPage($fotos, $equipo)
    {
        $html = "
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .content-wrapper {
            margin: 0 30px;
            padding-top: 0;
            padding-bottom: 30px; /* Espacio para el pie de página */
            position: relative;
            margin-top: -40px;
        }
        .header-wrapper {
            margin: 0;
            padding: 0;
            width: 100%;
            display: block;
            line-height: 0;
            font-size: 0; /* Elimina cualquier espacio en blanco */
        }
        .header-wrapper img {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
            vertical-align: top; /* Elimina espacio debajo de la imagen */
        }
        .no-space {
            font-size: 0;
            line-height: 0;
            margin: 0;
            padding: 0;
            display: block;
        }
    </style>
    
    <div class='no-space'>
      
        
        <div class='content-wrapper'>
            <h1 style='text-align: center; color: #C43438; font-size: 24px; font-weight: bold; margin: 10px 0; text-decoration: underline;'>
                FOTOS REALES - {$equipo['equipo']}
            </h1>
            <h2 style='text-align: center; font-size: 18px; margin: 5px 0;'>
                Marca: {$equipo['marca']} | Modelo: {$equipo['modelo']} | Serie: {$equipo['numero_serie']}
            </h2>
        
            <table style='width: 100%; border-collapse: collapse;'>";

        if (empty($fotos)) {
            $html .= "
            <tr>
                <td style='text-align: center; padding: 50px;'>
                    <p style='color: #666; font-size: 16px;'>No hay fotos disponibles para este equipo</p>
                </td>
            </tr>";
        } else {
            // Mostrar 4 imágenes por página (2 filas x 2 columnas)
            for ($i = 0; $i < count($fotos); $i += 4) {
                // Primera fila
                $html .= "<tr>";

                // Primera imagen de la fila
                if ($i < count($fotos)) {
                    $imagePath = 'public/assets/img/cotizaciones/' . $fotos[$i];
                    $html .= "
                    <td style='width: 50%; padding: 3px; text-align: center;'>
                        <img src='" . $imagePath . "' style='width: 320px; height: 320px; object-fit: contain; border: 1px solid #ddd;'>
                    </td>";
                } else {
                    $html .= "<td></td>";
                }

                // Segunda imagen de la fila
                if ($i + 1 < count($fotos)) {
                    $imagePath = 'public/assets/img/cotizaciones/' . $fotos[$i + 1];
                    $html .= "
                    <td style='width: 50%; padding: 3px; text-align: center;'>
                        <img src='" . $imagePath . "' style='width: 320px; height: 320px; object-fit: contain; border: 1px solid #ddd;'>
                    </td>";
                } else {
                    $html .= "<td></td>";
                }

                $html .= "</tr>";

                // Segunda fila
                $html .= "<tr>";

                // Tercera imagen
                if ($i + 2 < count($fotos)) {
                    $imagePath = 'public/assets/img/cotizaciones/' . $fotos[$i + 2];
                    $html .= "
                    <td style='width: 50%; padding: 3px; text-align: center;'>
                        <img src='" . $imagePath . "' style='width: 320px; height: 320px; object-fit: contain; border: 1px solid #ddd;'>
                    </td>";
                } else {
                    $html .= "<td></td>";
                }

                // Cuarta imagen
                if ($i + 3 < count($fotos)) {
                    $imagePath = 'public/assets/img/cotizaciones/' . $fotos[$i + 3];
                    $html .= "
                    <td style='width: 50%; padding: 3px; text-align: center;'>
                        <img src='" . $imagePath . "' style='width: 320px; height: 320px; object-fit: contain; border: 1px solid #ddd;'>
                    </td>";
                } else {
                    $html .= "<td></td>";
                }

                $html .= "</tr>";

                // Si hay más imágenes, agregar una nueva página
                if ($i + 4 < count($fotos)) {
                    $html .= "</table></div>";
                    $html .= "<pagebreak />";

                    $html .= "<h1 style='text-align: center; color: #C43438; font-size: 24px; font-weight: bold; margin: 10px 0; text-decoration: underline;'>FOTOS REALES - {$equipo['equipo']}</h1>";
                    $html .= "<h2 style='text-align: center; font-size: 18px; margin: 5px 0;'>Marca: {$equipo['marca']} | Modelo: {$equipo['modelo']} | Serie: {$equipo['numero_serie']}</h2>";
                    $html .= "<table style='width: 100%; border-collapse: collapse;'>";
                }
            }
        }

        $html .= "
        </table>
    </div>
</div>";

        return $html;
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
            'esRolOrdenTrabajo' => false
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
                if ($nombreRol === 'ORDEN TRABAJO') {
                    $permisos['puedeVerPrecios'] = false;
                    $permisos['esRolOrdenTrabajo'] = true;
                    return $permisos;
                } else if ($nombreRol === 'SERVICIO') {
                    $permisos['puedeVerPrecios'] = false;
                }
            }

            // Para otros roles, verificar permiso para ver precios
            $sql = "SELECT ver_precios FROM roles WHERE rol_id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $rolId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $permisos['puedeVerPrecios'] = (bool) $row['ver_precios'];
            }
        }

        return $permisos;
    }
}