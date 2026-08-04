<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';


use Endroid\QrCode\QrCode;
use Luecano\NumeroALetras\NumeroALetras;


class ReporteOrdenCompraController extends Controller
{
    private $mpdf;
    private $conexion;
    public function __construct()
    {
        $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 0]);
        $this->conexion = (new Conexion())->getConexion();
        // $this->venta = new Venta();
    }

    public function reporteCompra($id)
    {
        // Consulta SQL para obtener datos de compra
        $sql = "SELECT c.fecha_emision, c.direccion, CONCAT(ds.abreviatura, ' | ', c.serie, ' - ', c.numero) AS factura, 
              p.razon_social, c.total, tp.nombre as tipoPago, c.dias_pagos, c.id_empresa, p.direccion as direccion_proveedor,
              YEAR(CURRENT_DATE()) as anio_actual, c.numero
              FROM compras c
              LEFT JOIN documentos_sunat ds ON c.id_tido = ds.id_tido
              LEFT JOIN proveedores p ON p.proveedor_id = c.id_proveedor
              LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = c.id_tipo_pago 
              WHERE c.id_compra = $id";
        $result = $this->conexion->query($sql);
        $compra = $result->fetch_assoc();

        $esCredito = ($compra['tipoPago'] == 'Credito' || $compra['tipoPago'] == 'CREDITO');

        // Obtener productos Y repuestos de la compra
        $sqlProductos = "
        (SELECT pc.id_producto_venta as id_item, pc.cantidad, pc.precio, p.nombre, p.descripcion COLLATE utf8mb3_spanish_ci as descripcion, 'Producto' as tipo
         FROM productos_compras pc
         LEFT JOIN productos p ON pc.id_producto = p.id_producto
         WHERE pc.id_compra = $id)
        UNION ALL
        (SELECT rc.id_repuesto_compra as id_item, rc.cantidad, rc.precio, r.nombre, r.detalle as descripcion, 'Repuesto' as tipo
         FROM repuestos_compras rc
         LEFT JOIN repuestos r ON rc.id_repuesto = r.id_repuesto
         WHERE rc.id_compra = $id)
        ORDER BY tipo, nombre";
        $resultProductos = $this->conexion->query($sqlProductos);

        // Obtener detalles de pago a crédito
        $sqlPagos = "SELECT dc.dias_compra_id, dc.monto, dc.fecha, dc.estado
                  FROM dias_compras dc
                  WHERE dc.id_compra = $id";
        $resultPagos = $this->conexion->query($sqlPagos);

        // Obtener información del usuario actual
        $usuario_actual = [];
        $query = "SELECT 
                 u.nombres,
                 u.telefono,
                 r.nombre as rol
               FROM usuarios u
               INNER JOIN roles r ON r.rol_id = u.id_rol
               WHERE u.usuario_id = " . $_SESSION['usuario_id'];

        $result = $this->conexion->query($query);
        if ($result && $result->num_rows > 0) {
            $usuario_actual = $result->fetch_assoc();
        }

        // Obtener observaciones de la compra
        $sqlObservaciones = "SELECT observaciones as detalle 
                          FROM observaciones_compra oc
                          WHERE oc.id_compra = $id";
        $resultObservaciones = $this->conexion->query($sqlObservaciones);
        $observaciones = '';

        if ($resultObservaciones && $resultObservaciones->num_rows > 0) {
            $observacion = $resultObservaciones->fetch_assoc();
            $observaciones = $observacion['detalle'];
        } else {
            $observacionDefault = $this->conexion->query("SELECT detalle FROM observacion LIMIT 1")->fetch_assoc();
            $observaciones = $observacionDefault ? $observacionDefault['detalle'] : '';
        }

        // Generar filas de productos
        $productosHtml = "";
        $item = 1;
        $subtotal = 0;

        while ($producto = $resultProductos->fetch_assoc()) {
            $precio = number_format($producto['precio'], 2, ".", ",");
            $subtotal += floatval($producto['precio']) * intval($producto['cantidad']);

            $productosHtml .= "<tr>
      <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$item}</td>
      <td style='font-size: 10px; text-align: left; border: 1px solid #CA3438; padding: 6px;'>
       {$producto['nombre']}<br>{$producto['descripcion']}
      </td>
      <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$producto['cantidad']}</td>
      <td style='font-size: 10px; text-align: right; border: 1px solid #CA3438; padding: 6px;'>S/ {$precio}</td>
    </tr>";
            $item++;
        }

        // Calcular totales
        $subtotalFormateado = number_format($subtotal, 2, ".", ",");
        $igv = $subtotal * 0.18;
        $igvFormateado = number_format($igv, 2, ".", ",");
        $totalFormateado = number_format($subtotal + $igv, 2, ".", ",");

        // Generar filas de pagos a crédito SOLO si es crédito
        $pagosHtml = "";
        $tablaPagosHtml = "";

        if ($esCredito) {
            $numPago = 1;
            $meses = array(
                '01' => 'ENERO',
                '02' => 'FEBRERO',
                '03' => 'MARZO',
                '04' => 'ABRIL',
                '05' => 'MAYO',
                '06' => 'JUNIO',
                '07' => 'JULIO',
                '08' => 'AGOSTO',
                '09' => 'SEPTIEMBRE',
                '10' => 'OCTUBRE',
                '11' => 'NOVIEMBRE',
                '12' => 'DICIEMBRE'
            );

            while ($pago = $resultPagos->fetch_assoc()) {
                $montoFormateado = number_format($pago['monto'], 2, ".", ",");
                $fecha_obj = new DateTime($pago['fecha']);
                $mes = $fecha_obj->format('m');
                $dia = $fecha_obj->format('d');
                $anio = $fecha_obj->format('Y');
                $fechaFormateada = $meses[$mes] . " " . $dia . " del, " . $anio;
                $estado = ($pago['estado'] == '0') ? 'Pendiente' : 'Pagado';

                $pagosHtml .= "<tr>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$numPago}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$fechaFormateada}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>SOLES</td>
          <td style='font-size: 10px; text-align: right; border: 1px solid #CA3438; padding: 6px;'>{$montoFormateado}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$estado}</td>
        </tr>";
                $numPago++;
            }

            $tablaPagosHtml = "
          <div style='margin-top: 20px; text-align: center;'>
            <table style='width:55%; border-collapse: collapse; margin: 15px auto 0;' cellpadding='0' cellspacing='0'>
              <thead>
                <tr>
                  <th colspan='5' style='font-size: 11px; text-align: center; padding: 6px; background-color: #FFFFFF; border: none;'><strong>DETALLE DE LA FORMA DE PAGO: CRÉDITO</strong></th>
                </tr>
                <tr style='background-color: #CA3438; color: white;'>
                  <th style='width: 5%; font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>N°</strong></th>
                  <th style='width: 20%; font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Fecha de Vencimiento</strong></th>
                  <th style='width: 10%; font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Moneda</strong></th>
                  <th style='width: 10%; font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Monto</strong></th>
                  <th style='width: 10%; font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Estado</strong></th>
                </tr>
              </thead>
              <tbody>
                {$pagosHtml}
              </tbody>
            </table>
          </div>";
        }

        // Obtener datos de la empresa
        $empresa = $this->conexion->query("SELECT * from empresas
      where id_empresa = '{$compra['id_empresa']}'")->fetch_assoc();

        // Configuración de mPDF
        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 30,
            'margin_bottom' => 35,
            'margin_header' => 0,
            'margin_footer' => 0,
            'setAutoBottomMargin' => 'stretch'
        ]);

        // CORREGIDO: Formato del número sin ceros a la izquierda
        $numeroOrden = $compra['numero'] . "/" . $compra['anio_actual'];
        $this->mpdf->SetTitle("OC° {$numeroOrden}");

        // Configurar el header con el encabezado de la empresa (igual que cotización)
        $headerHTML = "
       <div style='width: 100%; margin: 0; padding: 0;'>
       <img src='public/assets/img/encabezado.svg' style='width: 100%; margin: 0; padding: 0; display: block;'>
       </div>";

        $this->mpdf->SetHTMLHeader($headerHTML);
        $this->mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin-bottom: 20px;">
       <span style="font-size: 11px; color: #000;">Lima, ' . Tools::formatoFechaVisual($compra['fecha_emision']) . '</span>
       </div>');
        $this->mpdf->SetTopMargin(40);
        $this->mpdf->showImageErrors = true;

        $this->mpdf->SetDisplayMode('fullpage');
        $this->mpdf->useSubstitutions = false;
        $this->mpdf->shrink_tables_to_fit = 1;
        $this->mpdf->keep_table_proportions = true;

        // Establecer el pie de página (igual que cotización)
        $footerHTML = '
         <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
             <img src="public/assets/img/pie_de_pagina.svg" style="width: 100%; display: block; margin: 0; padding: 0;">
         </div>';
        $this->mpdf->SetHTMLFooter($footerHTML);

        // CORREGIDO: Título con formato mejorado y centrado
        $htmlCuadroHead = "<div style='width: 100%; text-align: center; margin-bottom: 10px; margin-top:30px'>
             <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e;'>
               <span style='font-size: 14px; font-weight: bold;'>ORDEN DE COMPRA DE J.V.C. S.A.C. - OC° {$numeroOrden}</span>
             </div>
         </div>";

        // Formatear las observaciones
        $observacionesFormateadas = "";
        if (!empty($observaciones)) {
            $observacionesFormateadas = "
        <div style='margin-top: 20px; page-break-inside: avoid;'>
          <p style='font-size: 11px; font-weight: bold; margin: 0; padding: 0;'>Observaciones:</p>
          <div style='font-size: 11px; margin: 0; padding-left: 20px; line-height: 1.2;'>
            " . nl2br($observaciones) . "
          </div>
        </div>";
        }

        // Agregar la sección de contactos
        $contactosHtml = "
    <div style='margin-top: 15px; padding: 0;'>
      <p style='font-size: 12px; margin: 0; padding: 0;'>Esperando vernos favorecidos con su preferencia, nos despedimos.</p>
      <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Atentamente,</p>
      
      <table style='width: 100%; margin-top: 20px;'>
        <tr>
          <td style='width: 50%; text-align: center;'>
            <div style='font-size: 10px; color: #033668;'>
              <strong>" . ($usuario_actual['nombres'] ?? 'Usuario vendedor') . "</strong><br>
              " . ($usuario_actual['rol'] ?? 'ADMIN') . "<br>
              Teléfono: 355-4701<br>
              Cel: " . ($usuario_actual['telefono'] ?? '993321920') . "
            </div>
          </td>
          <td style='width: 50%; text-align: center;'>
            <div style='font-size: 10px; color: #033668;'>
              <strong>Eduardo Crisóstomo P.</strong><br>
              Jefe de Ventas y Servicios<br>
              Teléfono: 355-4701<br>
              Cel: 996246564 - 943140418
            </div>
          </td>
        </tr>
      </table>
    </div>";

        // Contenido HTML del reporte
        $html = "
    <div style='width: 100%;'>
      " . $htmlCuadroHead . "

      <div style='width: 100%; max-width: 1000px; margin: 0 40px;'>
        <div>
          <table style='width:100%; border-collapse: collapse; margin-bottom: 5px;'>
            <tr>
              <td style='font-size: 11px; text-align: left;'>Señores:</td>
            </tr>
            <tr>
              <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$compra['razon_social']}</td>
            </tr>
            <tr>
              <td style='font-size: 11px; text-align: left;'>Dirección:</td>
            </tr>
            <tr>
              <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . (!empty($compra['direccion_proveedor']) ? $compra['direccion_proveedor'] : 'No hay dirección registrada') . "</td>
            </tr>
          </table>
        </div>
        
        <div style='padding-right: 15px;'>
          <div>
            <table style='width:100%'>
              <tr>
                <td style='font-size: 11px;'>Por medio del presente documento nos dirigimos a ustedes para saludarlos cordialmente y asimismo hacerles llegar nuestra siguiente orden de compra:</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
          
      <!-- Tabla de productos centrada (fuera del contenedor con márgenes) -->
      <div style='margin-top: 15px; text-align: center; width: 100%;'>
        <table style='width:75%; border-collapse: collapse; margin: 15px auto 0; margin-left: auto; margin-right: auto;' cellpadding='0' cellspacing='0'>
              <thead>
                <tr style='background-color: #CA3438;'>
                  <th style='width: 10%; font-size: 10px; text-align: center; color: white; border: 1px solid #CA3438; padding: 8px;'>ITEM</th>
                  <th style='width: 40%; font-size: 10px; text-align: center; color: white; border: 1px solid #CA3438; padding: 8px;'>DESCRIPCIÓN</th>
                  <th style='width: 15%; font-size: 10px; text-align: center; color: white; border: 1px solid #CA3438; padding: 8px;'>CANT.</th>
                  <th style='width: 15%; font-size: 10px; text-align: center; color: white; border: 1px solid #CA3438; padding: 8px;'>COSTO UNIT. <br> SIN I.G.V.</th>
                </tr>
              </thead>
              <tbody>
                {$productosHtml}
              </tbody>
            </table>
            
            <!-- Tabla separada para totales alineada con las columnas de la tabla principal -->
            <table style='width:75%; border-collapse: collapse; margin: 5px auto 0; margin-left: auto; margin-right: auto;' cellpadding='0' cellspacing='0'>
              <tr>
                <td style='width: 70%; border: none;'></td>
                <td style='width: 15%; font-size: 10px; text-align: left; border-left: 1px solid #CA3438; padding: 6px; white-space: nowrap; overflow: hidden;'>
                  <strong>SUBTOTAL:</strong>
                </td>
                <td style='width: 15%; font-size: 10px; text-align: right; border-right: 1px solid #CA3438;right; border-left: 1px solid #CA3438; padding: 6px;'>
                  S/ {$subtotalFormateado}
                </td>
              </tr>
              <tr>
                <td style='border: none;'></td>
                <td style='font-size: 10px;text-align: left; border: 1px solid #CA3438; padding: 6px; white-space: nowrap; overflow: hidden;'>
                  <strong>IGV (18%):</strong>
                </td>
                <td style='font-size: 10px; text-align: right; border: 1px solid #CA3438; padding: 6px;'>
                  S/ {$igvFormateado}
                </td>
              </tr>
              <tr>
                <td style='border: none;'></td>
                <td style='font-size: 10px; text-align: left; border: 1px solid #CA3438; padding: 6px; background-color: #CA3438; color: #ffffff; white-space: nowrap; overflow: hidden;'>
                  <strong>TOTAL:</strong>
                </td>
                <td style='font-size: 10px; text-align: right; border: 1px solid #CA3438; padding: 6px; background-color: #CA3438; color: #ffffff;'>
                  <strong>S/ {$totalFormateado}</strong>
                </td>
              </tr>
            </table>
      </div>
         
      <!-- Tabla de detalle de pago -->
      {$tablaPagosHtml}

      <!-- Contenido final con márgenes -->
      <div style='width: 100%; max-width: 1000px; margin: 40px;'>
        <!-- Sección de observaciones -->
        {$observacionesFormateadas}

        <!-- Sección de contactos -->
        {$contactosHtml}
      </div>
        
    </div>
    ";

        // Escribir el HTML al documento
        $this->mpdf->WriteHTML($html);

        // Generar el PDF
        $this->mpdf->Output("ORDEN_COMPRA_{$numeroOrden}.pdf", 'I');
    }

   public function reporteCompraAll()
{
    // Obtener filtros de fecha si existen
    $whereClause = "";
    if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $fecha_inicio = $_GET['fecha_inicio'];
        $fecha_fin = $_GET['fecha_fin'];
        $whereClause = " WHERE c.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }

    $sql = "SELECT c.id_compra, c.fecha_emision, c.direccion, CONCAT(ds.abreviatura, ' | ', c.serie, ' - ', c.numero) AS factura,
            p.razon_social, p.ruc as proveedor_ruc, c.total, tp.nombre as tipoPago, c.id_empresa, c.id_tipo_pago
            FROM compras c
            LEFT JOIN documentos_sunat ds ON c.id_tido = ds.id_tido
            LEFT JOIN proveedores p ON p.proveedor_id = c.id_proveedor
            LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = c.id_tipo_pago
            {$whereClause}
            ORDER BY c.fecha_emision DESC";

    $result = $this->conexion->query($sql);

    $rowHtml = "";
    $idEmpresa = "";
    $totalGeneral = 0;
    $contador = 1;

    foreach ($result as $fila) {
        $total = floatval($fila['total']);
        $totalFormateado = number_format($total, 2, ".", ",");
        $totalGeneral += $total;
        $idEmpresa = $fila['id_empresa'];

        // Obtener días de pago si es crédito
        $diasPago = "";
        if ($fila['id_tipo_pago'] == 2) {
            $sqlPagos = "SELECT COUNT(*) as total_cuotas, GROUP_CONCAT(DATE_FORMAT(fecha, '%d/%m/%Y') ORDER BY fecha SEPARATOR ', ') as fechas
                         FROM dias_compras
                         WHERE id_compra = {$fila['id_compra']}";
            $resultPagos = $this->conexion->query($sqlPagos);
            if ($resultPagos && $resultPagos->num_rows > 0) {
                $pagosData = $resultPagos->fetch_assoc();
                $diasPago = $pagosData['total_cuotas'] . " cuota(s): " . $pagosData['fechas'];
            }
        } else {
            $diasPago = "-";
        }

        $rowHtml .= "<tr>
            <td style='font-size: 9px; text-align: center; border: 1px solid #333; padding: 4px;'>{$contador}</td>
            <td style='font-size: 9px; text-align: center; border: 1px solid #333; padding: 4px;'>{$fila['fecha_emision']}</td>
            <td style='font-size: 9px; text-align: left; border: 1px solid #333; padding: 4px;'>{$fila['proveedor_ruc']}</td>
            <td style='font-size: 9px; text-align: center; border: 1px solid #333; padding: 4px;'>{$fila['factura']}</td>
            <td style='font-size: 9px; text-align: left; border: 1px solid #333; padding: 4px;'>{$fila['razon_social']}</td>
            <td style='font-size: 9px; text-align: center; border: 1px solid #333; padding: 4px;'>{$fila['tipoPago']}</td>
            <td style='font-size: 8px; text-align: left; border: 1px solid #333; padding: 4px;'>{$diasPago}</td>
            <td style='font-size: 9px; text-align: right; border: 1px solid #333; padding: 4px;'>S/ {$totalFormateado}</td>
        </tr>";
        $contador++;
    }

    // Fila de total
    $totalGeneralFormateado = number_format($totalGeneral, 2, ".", ",");
    $rowHtml .= "<tr style='background-color: #CA3438; font-weight: bold;'>
        <td colspan='7' style='font-size: 10px; text-align: right; border: 1px solid #333; padding: 6px; color: #ffffff;'><strong>TOTAL GENERAL:</strong></td>
        <td style='font-size: 10px; text-align: right; border: 1px solid #333; padding: 6px; color: #ffffff;'><strong>S/ {$totalGeneralFormateado}</strong></td>
    </tr>";

    // Obtener datos de la empresa
    $empresa = $this->conexion->query("SELECT * FROM empresas WHERE id_empresa = '{$idEmpresa}'")->fetch_assoc();

    // SOLUCIÓN: Usar exactamente la misma configuración que reporteCompra()
    $this->mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 5,
        'margin_right' => 0,      // IGUAL que reporteCompra()
        'margin_top' => 30,
        'margin_bottom' => 35,
        'margin_header' => 0,
        'margin_footer' => 0,
        'setAutoBottomMargin' => 'stretch'
    ]);

    $this->mpdf->SetTitle("Reporte General de Compras");

    // SOLUCIÓN: Usar exactamente el mismo header que reporteCompra()
    $headerHTML = "
    <div style='width: 100%; margin: 0; padding: 0;'>
        <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . ImageStorage::url('empresas', $empresa['logo']) . "'>
    </div>";

    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->SetTopMargin(40);
    $this->mpdf->showImageErrors = true;

    // SOLUCIÓN: Usar exactamente el mismo footer que reporteCompra()
    $footerHTML = '
    <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
        <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
    </div>';
    $this->mpdf->SetHTMLFooter($footerHTML);

    // Fecha actual para el reporte
    $fechaActual = date('d/m/Y H:i:s');
    $filtroFecha = "";
    if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $filtroFecha = "<p style='font-size: 11px; margin: 5px 0;'><strong>Período:</strong> Del " . Tools::formatoFechaVisual($_GET['fecha_inicio']) . " al " . Tools::formatoFechaVisual($_GET['fecha_fin']) . "</p>";
    }

    // AJUSTE: Contenido con padding-right para compensar el margin_right: 0
    $html = "
    <div style='width: 100%; margin-top: 20px; padding-right: 15px;'>
        <div style='width: 100%; text-align: center; margin-bottom: 20px;'>
            <h2 style='font-size: 16px; font-weight: bold; margin: 10px 0; color: #CA3438;'>REPORTE GENERAL DE COMPRAS</h2>
            <div style='font-size: 11px; margin: 10px 0;'>
                <p style='margin: 2px 0;'><strong>Empresa:</strong> {$empresa['ruc']} | {$empresa['razon_social']}</p>
                <p style='margin: 2px 0;'><strong>Fecha de generación:</strong> {$fechaActual}</p>
                {$filtroFecha}
            </div>
        </div>
        
        <div style='width: 100%; margin-top: 20px;'>
            <table style='width: 100%; border-collapse: collapse; margin: 0 auto;'>
                <thead>
                    <tr style='background-color: #CA3438;'>
                        <th style='width: 5%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>N°</strong></th>
                        <th style='width: 8%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>Fecha</strong></th>
                        <th style='width: 10%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>RUC Proveedor</strong></th>
                        <th style='width: 12%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>Documento</strong></th>
                        <th style='width: 20%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>Proveedor</strong></th>
                        <th style='width: 8%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>Tipo Pago</strong></th>
                        <th style='width: 22%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>Fechas de Pago</strong></th>
                        <th style='width: 10%; font-size: 10px; text-align: center; border: 1px solid #333; padding: 8px; color: white;'><strong>Total</strong></th>
                    </tr>
                </thead>
                <tbody>
                    {$rowHtml}
                </tbody>
            </table>
        </div>
        
        <div style='margin-top: 30px; text-align: center;'>
            <p style='font-size: 10px; color: #666;'>Reporte generado automáticamente por el sistema de gestión J.V.C. S.A.C.</p>
        </div>
    </div>";

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output("Reporte_Compras_General.pdf", 'I');
}
}