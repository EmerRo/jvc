<?php

require_once "app/models/Venta.php";
require_once "app/models/Varios.php";
require_once "app/models/DocumentoSunat.php";
require_once 'utils/lib/mpdf/vendor/autoload.php';



class GeneradoresController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }
 public function reportePeriodoVentaGanancias($periodo)
{
    // Configuración de mPDF igual que cotizaciones
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 0,
        'margin_top' => 30,
        'margin_bottom' => 35,
        'margin_header' => 0,
        'margin_footer' => 0,
        'setAutoBottomMargin' => 'stretch'
    ]);

    $empresa = $this->conexion->query("select * from empresas
    where id_empresa = '{$_SESSION['id_empresa']}'")->fetch_assoc();

    // Configurar el header igual que cotizaciones
    $headerHTML = "
    <div style='width: 100%; margin: 0; padding: 0;'>
    <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . URL::to('files/logo/' . $empresa['logo']) . "'>
    </div>";

    // Establecer el header y configurarlo para todas las páginas
    $mpdf->SetHTMLHeader($headerHTML);
    $mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin-bottom: 20px;">
    <span style="font-size: 11px; color: #000;">Lima, ' . date('d/m/Y') . '</span>
    </div>');
    $mpdf->SetTopMargin(40);
    $mpdf->showImageErrors = true;

    // Configurar propiedades adicionales para el manejo de páginas
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->useSubstitutions = false;
    $mpdf->shrink_tables_to_fit = 1;
    $mpdf->keep_table_proportions = true;

    // Establecer el pie de página igual que cotizaciones
    $footerHTML = '
    <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
        <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
    </div>';
    $mpdf->SetHTMLFooter($footerHTML);

    $c_venta = new Venta();
    $c_varios = new Varios();
    $c_venta->setIdEmpresa($_SESSION['id_empresa']);
    $a_ventas = $c_venta->verFilasPeriodoGanancia($periodo);
    $item = 1;
    $suma_total = 0;
    $suma_costo = 0;
    $suma_ganancia = 0;
    $rowHmtl = "";
    $temoAr = explode('-', $periodo);

    if ($temoAr[2] == 'nn') {
        $periodo = $temoAr[0] . $temoAr[1];
    } elseif ($temoAr[1] == '00') {
        $periodo = $temoAr[0];
    } else {
        $periodo = $temoAr[0] . $temoAr[1] . $temoAr[2];
    }
    foreach ($a_ventas as $fila) {
        $total = 0;
        $costo = 0;
        $cliente = "          **** DOCUMENTO ANULADO **** ";
        if ($fila['estado'] == 1) {
            $total = $fila['total'];
            $costo = $fila['costo'];
            $cliente = $fila['documento'] . " | " . utf8_decode($fila['datos']);
        }
        $ganancias = $total - $costo;
        $suma_total += $total;
        $suma_costo += $costo;
        $suma_ganancia += $ganancias;
        $codigo = $periodo . $c_varios->zerofill($item, 3);
        $documento_venta = $fila['abreviatura'] . " | " . $fila['serie'] . " - " . $c_varios->zerofill($fila['numero'], 3);

        $metodo = $fila['metodo'];
        $subtotal = number_format($total / 1.18, 2);
        $igv = number_format($total / 1.18 * 0.18, 2);
        $total = number_format($total, 2);
        $costo = number_format($costo, 2);
        $ganancias = number_format($ganancias, 2);
        $cliente = utf8_encode($cliente);
        $rowHmtl .= "<tr>
                    <td style='font-size: 9px'>$codigo</td>
                    <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
                    <td style='font-size: 9px'>$documento_venta</td>
                    <td style='font-size: 9px'>$cliente</td>
                    <td style='font-size: 9px'>$subtotal</td>
                    <td style='font-size: 9px'>$igv</td>
                    <td style='font-size: 9px'>$total</td>
                    <td style='font-size: 9px'>$costo</td>
                    <td style='font-size: 9px'>$ganancias</td>
                </tr>";
        $item++;
    }
    $suma_total = number_format($suma_total, 2);
    $suma_costo = number_format($suma_costo, 2);
    $suma_ganancia = number_format($suma_ganancia, 2);
    
    // Título del reporte con formato similar a cotizaciones
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
        <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin-left: 65px;'>
            <span style='font-size: 14px; font-weight: bold;'>REPORTE DE VENTAS Y GANANCIAS - PERIODO: $periodo</span>
        </div>
    </div>";

    $html = "
    <div style='width: 100%;'>
        " . $htmlCuadroHead . "
        
        <div style='width: 100%; max-width: 1000px; margin: 0 auto;'>
            <div style='width: 100%; margin-bottom: 20px;'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='font-size: 11px; text-align: left;'>Empresa:</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$empresa["ruc"]} | {$empresa['razon_social']}</td>
                    </tr>
                </table>
            </div>
            
            <div style='padding-right: 15px;'>
                <table style='width:100%; border-collapse: collapse; margin-right:35px; table-layout: fixed;'>
                    <colgroup>
                        <col style='width: 80px'>   <!-- Codigo -->
                        <col style='width: 70px'>   <!-- Fecha -->
                        <col style='width: 100px'>  <!-- Documento -->
                        <col style='width: 150px'>  <!-- Cliente -->
                        <col style='width: 70px'>   <!-- SubTotal -->
                        <col style='width: 60px'>   <!-- IGV -->
                        <col style='width: 70px'>   <!-- Total -->
                        <col style='width: 70px'>   <!-- Costo -->
                        <col style='width: 70px'>   <!-- Ganancia -->
                    </colgroup>
                    <thead>
                        <tr style='background-color: #CA3438;'>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Código</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Fecha</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Documento</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Cliente</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>SubTotal</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>IGV</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Total</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Costo</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Ganan.</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        $rowHmtl
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='6' style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color: white; padding: 6px;'><strong>TOTALES:</strong></td>
                            <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color: white; padding: 6px;'><strong>$suma_total</strong></td>
                            <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color: white; padding: 6px;'><strong>$suma_costo</strong></td>
                            <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color: white; padding: 6px;'><strong>$suma_ganancia</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    ";
    
    // Escribir el HTML al documento
    $mpdf->WriteHTML($html);
    
    // Generar el PDF
    $mpdf->Output();
}

public function reportePeriodoVenta($periodo)
{
    // Configuración de mPDF igual que cotizaciones
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 0,
        'margin_top' => 30,
        'margin_bottom' => 35,
        'margin_header' => 0,
        'margin_footer' => 0,
        'setAutoBottomMargin' => 'stretch'
    ]);

    $empresa = $this->conexion->query("select * from empresas
    where id_empresa = '{$_SESSION['id_empresa']}'")->fetch_assoc();

    // Configurar el header igual que cotizaciones
    $headerHTML = "
    <div style='width: 100%; margin: 0; padding: 0;'>
    <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . URL::to('files/logo/' . $empresa['logo']) . "'>
    </div>";

    // Establecer el header y configurarlo para todas las páginas
    $mpdf->SetHTMLHeader($headerHTML);
    $mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin-bottom: 20px;">
    <span style="font-size: 11px; color: #000;">Lima, ' . date('d/m/Y') . '</span>
    </div>');
    $mpdf->SetTopMargin(40);
    $mpdf->showImageErrors = true;

    // Configurar propiedades adicionales para el manejo de páginas
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->useSubstitutions = false;
    $mpdf->shrink_tables_to_fit = 1;
    $mpdf->keep_table_proportions = true;

    // Establecer el pie de página igual que cotizaciones
    $footerHTML = '
    <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
        <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
    </div>';
    $mpdf->SetHTMLFooter($footerHTML);

    $c_venta = new Venta();
    $c_varios = new Varios();
    $c_venta->setIdEmpresa($_SESSION['id_empresa']);
    $a_ventas = $c_venta->verFilasPeriodo($periodo);
    $item = 1;
    $suma_total = 0;
    $rowHmtl = "";
    $temoAr = explode('-', $periodo);

    if ($temoAr[2] == 'nn') {
        $periodo = $temoAr[0] . $temoAr[1];
    } elseif ($temoAr[1] == '00') {
        $periodo = $temoAr[0];
    } else {
        $periodo = $temoAr[0] . $temoAr[1] . $temoAr[2];
    }
    foreach ($a_ventas as $fila) {
        //Tools::prettyPrint($fila);
        //if (!($fila['id_tido']=='1'||$fila['id_tido']=='2')){
        //    continue;
        //}
        $total = 0;
        $cliente = "          **** DOCUMENTO ANULADO **** ";
        if ($fila['estado'] == 1) {
            $total = $fila['total'];
            $cliente = $fila['documento'] . " | " . utf8_decode($fila['datos']);
        }
        $suma_total += $total;
        $codigo = $periodo . $c_varios->zerofill($item, 3);
        $documento_venta = $fila['abreviatura'] . " | " . $fila['serie'] . " - " . $c_varios->zerofill($fila['numero'], 3);
        $metodo2 = '';
        if (!is_null($fila['pagado2']) && strlen($fila['pagado2']) > 0) {
            $metodo = $fila['metodo'] . ": S/ " . ($fila['pagado']);
            $metodo2 = $fila['metodo2'] . ': S/' . $fila['pagado2'];
        } else {
            $metodo = $fila['metodo'] . ": S/ " . ($fila['pagado'] ? $fila['pagado'] : $fila['total']);
        }

        $subtotal = number_format($total / 1.18, 2);
        $igv = number_format($total / 1.18 * 0.18, 2);
        $total = number_format($total, 2);
        $cliente = utf8_encode($cliente);
        $rowHmtl .= "<tr>
                    <td style='font-size: 9px'>$codigo</td>
                    <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
                    <td style='font-size: 9px'>$documento_venta</td>
                    <td style='font-size: 9px'>$cliente</td>
                    <td style='font-size: 9px'>$metodo</td>
                    <td style='font-size: 9px'>$metodo2</td>
                    <td style='font-size: 9px'>$subtotal</td>
                    <td style='font-size: 9px'>$igv</td>
                    <td style='font-size: 9px'>$total</td>
                </tr>";
        $item++;
    }

    // Título del reporte con formato similar a cotizaciones
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
        <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin-left: 65px;'>
            <span style='font-size: 14px; font-weight: bold;'>REPORTE DE VENTAS - PERIODO: $periodo</span>
        </div>
    </div>";

    $html = "
    <div style='width: 100%;'>
        " . $htmlCuadroHead . "
        
        <div style='width: 100%; max-width: 1000px; margin: 0 auto;'>
            <div style='width: 100%; margin-bottom: 20px;'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='font-size: 11px; text-align: left;'>Empresa:</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$empresa["ruc"]} | {$empresa['razon_social']}</td>
                    </tr>
                </table>
            </div>
            
            <div style='padding-right: 15px;'>
                <table style='width:100%; border-collapse: collapse; margin-right:35px; table-layout: fixed;'>
                    <colgroup>
                        <col style='width: 80px'>   <!-- Codigo -->
                        <col style='width: 70px'>   <!-- Fecha -->
                        <col style='width: 120px'>  <!-- Documento -->
                        <col style='width: 150px'>  <!-- Cliente -->
                        <col style='width: 100px'>  <!-- Metodo 1 -->
                        <col style='width: 100px'>  <!-- Metodo 2 -->
                        <col style='width: 70px'>   <!-- SubTotal -->
                        <col style='width: 60px'>   <!-- IGV -->
                        <col style='width: 70px'>   <!-- Total -->
                    </colgroup>
                    <thead>
                        <tr style='background-color: #CA3438;'>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Código</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Fecha</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Documento</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Cliente</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Método 1</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Método 2</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>SubTotal</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>IGV</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Total</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        $rowHmtl
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    ";
    
    // Escribir el HTML al documento
    $mpdf->WriteHTML($html);
    
    // Generar el PDF
    $mpdf->Output();
}

    public function generarTextLibroVentas()
    {
        // Limpiar completamente cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        try {
            // Configurar headers para respuesta JSON antes de cualquier procesamiento
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            
            // Validar datos de entrada
            if (!isset($_POST["anio"]) || !isset($_POST["mes"])) {
                echo json_encode([
                    "error" => true,
                    "message" => "Faltan parámetros requeridos (año y mes)"
                ]);
                exit;
            }

            $anio = $_POST["anio"];
            $mes = $_POST["mes"];

            $cl_venta = new Venta();
            $cl_varios = new Varios();
            $cl_notaventa = new Venta();
            $cl_tido = new DocumentoSunat();

            $periodo = $anio . $mes;

            // Obtener datos de la empresa
            $empresa = $this->conexion->query("select * from empresas 
            where id_empresa = '{$_SESSION['id_empresa']}'")->fetch_assoc();
            
            if (!$empresa) {
                echo json_encode([
                    "error" => true,
                    "message" => "No se pudo obtener información de la empresa"
                ]);
                exit;
            }

            $cl_venta->setIdEmpresa($empresa['id_empresa']);
            $a_ventas = $cl_venta->verFilasPeriodo($periodo);

            $contar = 0;
            $file_txt = "LE" . $empresa["ruc"] . $periodo . "00140100001111.txt";
            $contenido = '';

            // Procesar ventas
            foreach ($a_ventas as $value) {
                $contar++;

                $fecha_doc = isset($value['fecha_emision']) ? $value['fecha_emision'] : date('Y-m-d');
                $date = new DateTime($fecha_doc);
                $formato_fecha_doc = $date->format('Ymd');
                $fecha_periodo = $periodo . "00";
                
                if ($formato_fecha_doc < $fecha_periodo) {
                    $estado = 6;
                } else {
                    $estado = 1;
                }

                $documento_proveedor = isset($value['documento']) ? $value['documento'] : '';

                if (strlen($documento_proveedor) == 11) {
                    $tipo_doc_proveedor = 6;
                } elseif (strlen($documento_proveedor) == 8) {
                    $tipo_doc_proveedor = 1;
                } else {
                    $tipo_doc_proveedor = 0;
                }

                $fecha_amarre = "";
                $doc_amarre = "";
                $serie_amarre = "";
                $numero_amarre = "";

                $moneda = "PEN";

                $cl_tido->setIdTido($value['id_tido']);
                $cl_tido->obtenerDatos();
                $serie_doc = isset($value['serie']) ? $value['serie'] : '';

                $cod_sunat = $cl_tido->getCodSunat();
                $serie_doc = $cl_varios->zerofill($value['serie'], 4);

                $monto_total_soles = (isset($value['total']) ? $value['total'] : 0) * 1;

                $base = $monto_total_soles / 1.18;
                $igv = $base * 0.18;

                $contenido .= $periodo . "00|" .
                    $cl_varios->zerofill($value['id_venta'], 4) . "|" .
                    "M" . $cl_varios->zerofill($contar, 3) . "|" .
                    $cl_varios->fecha_tabla(isset($value['fecha_emision']) ? $value['fecha_emision'] : date('Y-m-d')) . "|" .
                    "|" .
                    $cl_varios->zerofill($cod_sunat, 2) . "|" .
                    strtoupper($serie_doc) . "|" .
                    $cl_varios->zerofill(isset($value['numero']) ? $value['numero'] : 0, 4) . "|" .
                    "|" .
                    $tipo_doc_proveedor . "|" .
                    $documento_proveedor . "|" .
                    utf8_decode(isset($value['datos']) ? $value['datos'] : '') . "|" .
                    "|" .
                    number_format($base, 2, ".", "") . "|" .
                    "0.00|" .
                    number_format($igv, 2, ".", "") . "|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    number_format($monto_total_soles, 2, ".", "") . "|" .
                    $moneda . "|" .
                    "1.000" .
                    "|" . $fecha_amarre .
                    "|" . $doc_amarre .
                    "|" . $serie_amarre .
                    "|" . $numero_amarre .
                    "|" .
                    "|" .
                    "|" .
                    "|" .
                    $estado . "|" . PHP_EOL;
            }

            // Procesar notas electrónicas
            $sql = "select ne.*, ds.abreviatura,c.documento, c.datos,nes.nombre_xml
            from notas_electronicas ne 
            join documentos_sunat ds on ne.tido = ds.id_tido
            join clientes c on ne.id_empresa = c.id_empresa
            join notas_electronicas_sunat nes on ne.nota_id = nes.id_notas_electronicas 
            where ne.id_empresa = '{$_SESSION['id_empresa']}' and ne.sucursal='{$_SESSION['sucursal']}' 
            and  concat(year(ne.fecha), LPAD(month(ne.fecha), 2, 0)) = '$periodo' 
            order by ne.fecha asc, ne.numero asc";

            $listaNotas = $this->conexion->query($sql);

            foreach ($listaNotas as $value) {
                $contar++;

                $fecha_doc = $value['fecha'];
                $date = new DateTime($fecha_doc);
                $formato_fecha_doc = $date->format('Ymd');
                $fecha_periodo = $periodo . "00";
                
                if ($formato_fecha_doc < $fecha_periodo) {
                    $estado = 6;
                } else {
                    $estado = 1;
                }

                $documento_proveedor = isset($value['documento']) ? $value['documento'] : '';
                if (strlen($documento_proveedor) == 11) {
                    $tipo_doc_proveedor = 6;
                } elseif (strlen($documento_proveedor) == 8) {
                    $tipo_doc_proveedor = 1;
                } else {
                    $tipo_doc_proveedor = 0;
                }

                $fecha_amarre = "";
                $doc_amarre = "";
                $serie_amarre = "";
                $numero_amarre = "";

                if ($value['tido'] == 3 || $value['tido'] == 4) {
                    $sql = "select * from ventas where id_venta = '{$value['id_venta']}'";
                    $ventaRef = $this->conexion->query($sql)->fetch_assoc();

                    $fecha_amarre = $cl_varios->fecha_tabla($ventaRef["fecha_emision"]);
                    if ($ventaRef['id_tido'] == 1) {
                        $doc_amarre = "03";
                    }
                    if ($ventaRef['id_tido'] == 2) {
                        $doc_amarre = "01";
                    }
                    $serie_amarre = $ventaRef['serie'];
                    $numero_amarre = $ventaRef['numero'];
                }

                $moneda = "PEN";

                $cl_tido->setIdTido($value['tido']);
                $cl_tido->obtenerDatos();
                $serie_doc = isset($value['serie']) ? $value['serie'] : '';

                $cod_sunat = $cl_tido->getCodSunat();
                $serie_doc = $cl_varios->zerofill($value['serie'], 4);

                $monto_total_soles = $value['monto'] * 1;
                $base = $monto_total_soles / 1.18;
                $igv = $base * 0.18;

                $contenido .= $periodo . "00|" .
                    $cl_varios->zerofill($value['nota_id'], 4) . "|" .
                    "M" . $cl_varios->zerofill($contar, 3) . "|" .
                    $cl_varios->fecha_tabla($value['fecha']) . "|" .
                    "|" .
                    $cl_varios->zerofill($cod_sunat, 2) . "|" .
                    strtoupper($serie_doc) . "|" .
                    $cl_varios->zerofill(isset($value['numero']) ? $value['numero'] : 0, 4) . "|" .
                    "|" .
                    $tipo_doc_proveedor . "|" .
                    $documento_proveedor . "|" .
                    utf8_decode(isset($value['datos']) ? $value['datos'] : '') . "|" .
                    "|" .
                    number_format($base, 2, ".", "") . "|" .
                    "0.00|" .
                    number_format($igv, 2, ".", "") . "|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    "0.00|" .
                    number_format($monto_total_soles, 2, ".", "") . "|" .
                    $moneda . "|" .
                    "1.000" .
                    "|" . $fecha_amarre .
                    "|" . $doc_amarre .
                    "|" . $serie_amarre .
                    "|" . $numero_amarre .
                    "|" .
                    "|" .
                    "|" .
                    "|" .
                    $estado . "|" . PHP_EOL;
            }

            // Verificar que el directorio existe
            $directorio = "files/temp/";
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Escribir archivo
            $file = fopen($directorio . $file_txt, "w");
            
            if (!$file) {
                echo json_encode([
                    "error" => true,
                    "message" => "No se pudo crear el archivo"
                ]);
                exit;
            }

            $result = fwrite($file, $contenido);
            fclose($file);

            if ($result === false) {
                echo json_encode([
                    "error" => true,
                    "message" => "Error al escribir el archivo"
                ]);
                exit;
            }

            // Respuesta exitosa
            echo json_encode([
                "file" => $file_txt,
                "success" => true,
                "message" => "Archivo generado correctamente"
            ]);

        } catch (Exception $e) {
            echo json_encode([
                "error" => true,
                "message" => "Error interno: " . $e->getMessage()
            ]);
        }
        exit;
    }
}