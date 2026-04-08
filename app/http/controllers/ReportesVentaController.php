<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Venta.php";
require_once "app/models/Cliente.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/ProductoVenta.php";
require_once "app/models/VentaServicio.php";
require_once "app/models/Varios.php";
require_once "app/models/VentaSunat.php";
require_once "app/models/VentaAnulada.php";
require_once "app/clases/SendURL.php";


use Endroid\QrCode\QrCode;
use Luecano\NumeroALetras\NumeroALetras;

class ReportesVentaController extends Controller
{
  private $mpdf;
  private $conexion;
     protected $venta;

  public function __construct()
  {
    $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 0]);
    $this->conexion = (new Conexion())->getConexion();
    $this->venta = new Venta();
  }
  private function getImagePath($imageName)
  {
    return PdfHelper::getImagePath($imageName);
  }
  private function escribirEncabezadoEmpresa($datoEmpresa, $htmlCuadroHead)
  {
    PdfHelper::escribirEncabezadoEmpresa($this->mpdf, $datoEmpresa, $htmlCuadroHead);
  }

  public function reporteVentaPorProducto()
  {
    // Configuración de mPDF igual que cotizaciones
    $this->mpdf = new \Mpdf\Mpdf([
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

    // Obtener datos de la empresa
    $empresa = $this->conexion->query("SELECT * FROM empresas WHERE id_empresa = '{$_SESSION['id_empresa']}'")->fetch_assoc();

    // Configurar el header igual que cotizaciones
    $headerHTML = "
    <div style='width: 100%; margin: 0; padding: 0;'>
    <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . URL::to('files/logo/' . $empresa['logo']) . "'>
    </div>";

    // Establecer el header y configurarlo para todas las páginas
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin-bottom: 20px;">
    <span style="font-size: 11px; color: #000;">Lima, ' . date('d/m/Y') . '</span>
    </div>');
    $this->mpdf->SetTopMargin(40);
    $this->mpdf->showImageErrors = true;

    // Configurar propiedades adicionales para el manejo de páginas
    $this->mpdf->SetDisplayMode('fullpage');
    $this->mpdf->useSubstitutions = false;
    $this->mpdf->shrink_tables_to_fit = 1;
    $this->mpdf->keep_table_proportions = true;

    // Establecer el pie de página igual que cotizaciones
    $footerHTML = '
    <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
        <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
    </div>';
    $this->mpdf->SetHTMLFooter($footerHTML);

    $sql = "";

    if (strlen($_GET['fecha2']) == 0) {
      $sql = "select  p.descripcion,v.fecha_emision,ds.nombre nombre_documento,concat(v.serie,'-',v.numero) venta_sn, pv.cantidad,pv.precio,pv.precio_usado ,tp.nombre nom_pago
            from productos_ventas pv
            join productos p on p.id_producto = pv.id_producto
            join ventas v on v.id_venta = pv.id_venta
            join tipo_pago tp on tp.tipo_pago_id = v.id_tipo_pago
            join documentos_sunat ds on v.id_tido = ds.id_tido
            where trim(p.codigo)='{$_GET['codprod']}' and v.fecha_emision >= '{$_GET['fecha1']}'  and v.estado<>'2'
                ";
    } else {
      $sql = "select  p.descripcion,v.fecha_emision,ds.nombre nombre_documento,concat(v.serie,'-',v.numero) venta_sn, pv.cantidad,pv.precio,pv.precio_usado ,tp.nombre nom_pago
            from productos_ventas pv
            join productos p on p.id_producto = pv.id_producto
            join ventas v on v.id_venta = pv.id_venta
            join tipo_pago tp on tp.tipo_pago_id = v.id_tipo_pago
            join documentos_sunat ds on v.id_tido = ds.id_tido
            where trim(p.codigo)='{$_GET['codprod']}' and v.fecha_emision between '{$_GET['fecha1']}' and '{$_GET['fecha2']}' and v.estado<>'2'";
    }

    $rowHmtl = '';
    $rows = $this->conexion->query($sql);

    foreach ($rows as $row) {
      $rowHmtl .= "
          <tr>
          <td style='font-size: 10px; text-align: left; border: 1px solid #CA3438; padding: 6px;'>{$row['descripcion']}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$row['nom_pago']}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$row['fecha_emision']}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$row['nombre_documento']}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$row['venta_sn']}</td>
          <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$row['cantidad']}</td>
          <td style='font-size: 10px; text-align: right; border: 1px solid #CA3438; padding: 6px;'>S/ {$row['precio']}</td>
            </tr>
          ";
    }

    // Título del reporte con formato similar a cotizaciones
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
        <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin-left: 65px;'>
            <span style='font-size: 14px; font-weight: bold;'>REPORTE DE PRODUCTOS POR VENTA</span>
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
                    <tr>
                        <td style='font-size: 11px; text-align: left;'>Producto:</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . ($_GET['codprod'] ?? 'Todos') . "</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px; text-align: left;'>Período:</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . ($_GET['fecha1'] ?? '') . (isset($_GET['fecha2']) && strlen($_GET['fecha2']) > 0 ? " al " . $_GET['fecha2'] : " en adelante") . "</td>
                    </tr>
                </table>
            </div>
            
            <div style='padding-right: 15px;'>
                <table style='width:100%; border-collapse: collapse; margin-right:35px; table-layout: fixed;'>
                    <colgroup>
                        <col style='width: 200px'>  <!-- Producto -->
                        <col style='width: 80px'>   <!-- Pago -->
                        <col style='width: 80px'>   <!-- Fecha -->
                        <col style='width: 100px'>  <!-- Doc -->
                        <col style='width: 80px'>   <!-- S-N -->
                        <col style='width: 70px'>   <!-- Cantidad -->
                        <col style='width: 80px'>   <!-- Precio -->
                    </colgroup>
                    <thead>
                        <tr style='background-color: #CA3438;'>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Producto</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Pago</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Fecha</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Doc.</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>S-N</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Cantidad</strong></th>
                            <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>Precio</strong></th>
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
    $this->mpdf->WriteHTML($html);

    // Generar el PDF
    $this->mpdf->Output();
  }




  public function reporteCliente($id)
  {
    // Obtener datos del cliente
    $sql = "SELECT * FROM clientes WHERE id_cliente = $id";
    $cliente = $this->conexion->query($sql)->fetch_assoc();

    if (!$cliente) {
      throw new Exception("Cliente no encontrado");
    }

    // Obtener ventas del cliente con información adicional
    $sql = "SELECT v.*, 
                   ds.nombre as tipo_documento,
                   ds.abreviatura,
                   tp.nombre as tipo_pago,
                   mp.nombre as metodo_pago,
                   CONCAT(v.serie, '-', LPAD(v.numero, 6, '0')) as documento_completo
            FROM ventas v
            LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            LEFT JOIN tipo_pago tp ON v.id_tipo_pago = tp.tipo_pago_id
            LEFT JOIN metodo_pago mp ON v.medoto_pago_id = mp.id_metodo_pago
            WHERE v.id_cliente = $id 
            AND v.estado != '2'
            ORDER BY v.fecha_emision DESC";

    $result = $this->conexion->query($sql);

    // Obtener empresa del cliente
    $sqlEmpresa = "SELECT e.* FROM empresas e 
                   INNER JOIN clientes c ON c.id_empresa = e.id_empresa 
                   WHERE c.id_cliente = $id";
    $empresa = $this->conexion->query($sqlEmpresa)->fetch_assoc();

    if (!$empresa) {
      throw new Exception("No se encontró la información de la empresa para este cliente");
    }

    // Configuración de mPDF igual que cotizaciones
    $this->mpdf = new \Mpdf\Mpdf([
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

    // Configurar el header igual que cotizaciones
    $headerHTML = "
     <div style='width: 100%; margin: 0; padding: 0;'>
     <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . URL::to('files/logo/' . $empresa['logo']) . "'>
     </div>";

    // Establecer el header y configurarlo para todas las páginas
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin-bottom: 20px;">
     <span style="font-size: 11px; color: #000;">Lima, ' . date('d/m/Y') . '</span>
     </div>');
    $this->mpdf->SetTopMargin(40);
    $this->mpdf->showImageErrors = true;

    // Configurar propiedades adicionales para el manejo de páginas
    $this->mpdf->SetDisplayMode('fullpage');
    $this->mpdf->useSubstitutions = false;
    $this->mpdf->shrink_tables_to_fit = 1;
    $this->mpdf->keep_table_proportions = true;

    // Establecer el pie de página igual que cotizaciones
    $footerHTML = '
       <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
           <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
       </div>';
    $this->mpdf->SetHTMLFooter($footerHTML);

    // Generar filas HTML para las ventas
    $rowHtml = "";
    $totalVentas = 0;
    $cantidadVentas = 0;

    while ($venta = $result->fetch_assoc()) {
      $total = number_format($venta['total'], 2, ".", ",");
      $totalVentas += floatval($venta['total']);
      $cantidadVentas++;

      $rowHtml .= "<tr>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$venta['documento_completo']}</td>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$venta['fecha_emision']}</td>
            <td style='font-size: 10px; text-align: left; border: 1px solid #CA3438; padding: 6px;'>{$venta['tipo_documento']}</td>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$venta['tipo_pago']}</td>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$venta['dias_pagos']}</td>
            <td style='font-size: 10px; text-align: right; border: 1px solid #CA3438; padding: 6px;'>S/ {$total}</td>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438; padding: 6px;'>{$venta['metodo_pago']}</td>
        </tr>";
    }

    // Formatear totales
    $totalVentasFormateado = number_format($totalVentas, 2, ".", ",");

    // Título del reporte con formato similar a cotizaciones
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
         <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin-left: 65px;'>
           <span style='font-size: 14px; font-weight: bold;'>REPORTE DE VENTAS POR CLIENTE - {$cliente['documento']}</span>
         </div>
     </div>";

    // HTML del reporte con el mismo estilo que cotizaciones
    $html = "
    <div style='width: 100%;'>
      
        " . $htmlCuadroHead . "
        
        <div style='width: 100%; max-width: 1000px; margin: 0 auto;'>
          <div>
            <table style='width:100%'>
              <tr>
                <td style='font-size: 11px; text-align: left;'>Cliente:</td>
              </tr>
              <tr>
                <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$cliente['datos']}</td>
              </tr>
              <tr>
                <td style='font-size: 11px; text-align: left;'>Documento:</td>
              </tr>
              <tr>
                <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$cliente['documento']}</td>
              </tr>
              <tr>
                <td style='font-size: 11px; text-align: left;'>Dirección:</td>
              </tr>
              <tr>
                <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . ($cliente['direccion'] ?? 'No especificada') . "</td>
              </tr>
              <tr>
                <td style='font-size: 11px; text-align: left;'>Teléfono:</td>
              </tr>
              <tr>
                <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . ($cliente['telefono'] ?? 'No especificado') . "</td>
              </tr>
              <tr>
                <td style='font-size: 11px; text-align: left;'>Email:</td>
              </tr>
              <tr>
                <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . ($cliente['email'] ?? 'No especificado') . "</td>
              </tr>
            </table>
          </div>
          
          <div style='padding-right: 15px;'>
            <div>
              <table style='width:100%'>
                <tr>
                  <td style='font-size: 11px;'>A continuación se presenta el historial de ventas realizadas a este cliente:</td>
                </tr>
              </table>
            </div>
            
            <!-- Tabla de ventas con el mismo estilo que cotizaciones -->
            <table style='width:100%; border-collapse: collapse; margin-right:35px; table-layout: fixed;' class='ventas-table'>
              <colgroup>
                <col style='width: 80px'>   <!-- DOCUMENTO -->
                <col style='width: 70px'>   <!-- FECHA -->
                <col style='width: 80px'>   <!-- TIPO DOC -->
                <col style='width: 70px'>   <!-- TIPO PAGO -->
                <col style='width: 60px'>   <!-- DÍAS PAGO -->
                <col style='width: 80px'>   <!-- TOTAL -->
                <col style='width: 100px'>  <!-- MÉTODO PAGO -->
              </colgroup>
              <thead>
                <tr style='background-color: #CA3438;'>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>DOCUMENTO</strong></th>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>FECHA</strong></th>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>TIPO DOC.</strong></th>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>TIPO PAGO</strong></th>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>DÍAS</strong></th>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>TOTAL</strong></th>
                  <th style='font-size: 10px; font-family: Arial, Helvetica, sans-serif; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438; padding: 8px;'><strong>MÉTODO PAGO</strong></th>
                </tr>
              </thead>
              <tbody>
                {$rowHtml}
                <!-- Fila de totales -->
                <tr>
                  <td colspan='5' style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color: white; padding: 6px;'><strong>TOTAL DE VENTAS ({$cantidadVentas}):</strong></td>
                  <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color: white; padding: 6px;'><strong>S/ {$totalVentasFormateado}</strong></td>
                  <td style='border: 1px solid #CA3438; background-color: #CA3438;'></td>
                </tr>
              </tbody>
            </table>
            
            <!-- Información adicional con mejor manejo de espacio -->
            <div style='page-break-inside: avoid; margin-bottom: 30px; margin-top: 20px;'>
              <div style='margin-top: 15px; padding: 0;'>
                <p style='font-size: 12px; margin: 0; padding: 0;'>Este reporte muestra el historial completo de ventas del cliente.</p>
                <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Generado el: " . date('d/m/Y H:i:s') . "</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>";

    // Escribir el HTML al documento
    $this->mpdf->WriteHTML($html);

    // Generar el PDF
    $this->mpdf->Output("Reporte_Cliente_{$cliente['documento']}.pdf", 'I');
  }


  public function reporteProductos($id)
  {
    $rpart = explode("-", $_GET["fecha"]);
    //var_dump($rpart);
    if ($rpart[1] == 'nn') {
      $sql = "SELECT pv.id_producto,c.datos,c.documento,v.id_venta,v.serie,v.numero,v.fecha_emision,pv.cantidad,pv.precio FROM ventas v 
    JOIN productos_ventas pv ON v.id_venta = pv.id_venta
    LEFT JOIN clientes c ON c.id_cliente= v.id_cliente 
    WHERE pv.id_producto= $id and concat(year(v.fecha_emision),month(v.fecha_emision))='" . $rpart[0] . "'";
    } else {
      $sql = "SELECT pv.id_producto,c.datos,c.documento,v.id_venta,v.serie,v.numero,v.fecha_emision,pv.cantidad,pv.precio FROM ventas v 
    JOIN productos_ventas pv ON v.id_venta = pv.id_venta
    LEFT JOIN clientes c ON c.id_cliente= v.id_cliente 
    WHERE pv.id_producto= $id and concat(year(v.fecha_emision),month(v.fecha_emision), day(v.fecha_emision))='" . $rpart[0] . $rpart[1] . "'";
    }
    //var_dump($sql);
    //die();

    $result = $this->conexion->query($sql);

    $rowHmtl = "";
    $totalSuma = 0;
    foreach ($result as $fila) {
      $cantidad = number_format($fila['cantidad'], 2, ".", "");
      $precio = number_format($fila['precio'], 2, ".", "");
      $total = $cantidad * $precio;
      $total = number_format($total, 2, ".", "");
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['documento']}</td>
      <td style='font-size: 9px'>{$fila['datos']}</td>
      <td style='font-size: 9px'>{$fila['id_venta']}</td>
      <td style='font-size: 9px'>{$fila['serie']}</td>
      <td style='font-size: 9px'>{$fila['numero']}</td>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$cantidad}</td>
      <td style='font-size: 9px'>{$precio}</td>
      <td style='font-size: 9px'>{$total}</td>
    </tr>";
      $totalSuma += $total;
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $sql = "SELECT * FROM productos WHERE id_producto = $id";
    $result = $this->conexion->query($sql)->fetch_assoc();

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR PRODUCTO</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
                <tr>
                    <td>Producto:</td>
                    <td>{$result['descripcion']}</td>
                </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                    <th style='width: 10%;'>Documento</th>
                    <th style='width: 10%;'>Datos</th>
                    <th style='width: auto;'>Id venta</th>
                    <th style='width: 10%;'>Serie</th>
                    <th style='width: 10%;'>Numero</th>
                    <th style='width: 10%;'>Fecha Emision</th>
                    <th style='width:auto;'>Cantidad</th>
                    <th style='width:auto;'>Precio</th>
                    <th style='width:auto;'>Total</th>
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
                <tfoot>
                <tr>
                <td colspan='8' style='text-align: right;font-size: 13px'>Total</td>
                <td  style='font-size: 13px'>$totalSuma</td>
                </tr>
                </tfoot>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

  public function comprobanteNotaE($venta, $nombreXML = '')
  {


    $sql = "SELECT ne.*,ds.nombre as 'nota_nombre',v.id_cliente FROM notas_electronicas ne
      join documentos_sunat ds on ne.tido = ds.id_tido
      join ventas v on ne.id_venta = v.id_venta
      where ne.nota_id =" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $_SESSION['id_empresa'])->fetch_assoc();

    $S_N = $datoVenta['serie'] . '-' . Tools::numeroParaDocumento($datoVenta['numero'], 6);
    $tipoDocNom = $datoVenta['nota_nombre'];
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);

    $formatter = new NumeroALetras;
    $sql = "SELECT * FROM notas_electronicas_sunat where id_notas_electronicas = '$venta' ";
    $qrImage = '';
    $hash_Doc = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $hash_Doc = "HASH: " . $rowVS['hash'] . "<br>";
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 100px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $tipo_documeto_venta = "";

    if ($datoVenta['tido'] == 3) {
      $tipo_documeto_venta = "NOTA DE CREDITO ELECTRÓNICA";
    } elseif ($datoVenta['tido'] == 4) {
      $tipo_documeto_venta = "NOTA DE DEBITO ELECTRÓNICA";
    }

    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';
    $listaProd1 = json_decode($datoVenta['productos'], true);

    foreach ($listaProd1 as $prod) {

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      // Determinar el tipo de documento y etiqueta basado en la longitud del documento
      $isRuc = strlen($resultC['documento']) == 11;
      $docLabel = $isRuc ? "R.U.C.:" : "DNI:";
      $clientLabel = $isRuc ? "Razón Social:" : "Cliente:";


      $rowHTML = $rowHTML . "
      <tr>
        <td style='width: 5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-left: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>$contador</td>
       <!-- <td style='width: 10%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['codigo']}</td> -->
        <td style='width: 6%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['cantidad']}</td>
        <td style='width: 40%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: left; padding-top: 6px; padding-bottom: 6px;'>{$prod['descripcion']}</td>
        <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>S/ $precio</td>
        <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-right: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>S/ $importe</td>
      </tr>";
      $contador++;
    }


    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, 'SOLES');

    $htmlCuadroHead = "<div style='width: 38%;text-align: center; background-color: #ffffff; float: right;font-family: Calibri, Helvetica Neue, sans-serif; font-size: 12px;'>
    <div style='width: 100%; height: 100px;border-radius:10px; border: 1px solid #373435' class=''>
        <div style='margin-top:10px'></div>
        <span> <strong> R.U.C: {$datoEmpresa['ruc']} </strong></span><br>

        <div style='margin-top: 10px '></div>
        <div style='background-color: #CA3438; color:white; margin:0 ; padding: 15px;width: 100%;'>
        <span ><strong>$tipoDocNom ELECTRONICA</strong></span>
        </div>
        
        <br>
      
   <span style='display: block; text-align: center; font-size: 14px'>Nro. $S_N</span>
     <div style='margin-top:10px'></div>
    </div>
</div>";

    $dominio = DOMINIO;

    // Escribir encabezado de la empresa
    $this->escribirEncabezadoEmpresa($datoEmpresa, $htmlCuadroHead);


    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / 1.18 * 0.18;
    $totalOpgravado = $total - $igv;
    $total = number_format($total, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');



    $html = "<div style='width: 1000%;padding-top: 150px; overflow: hidden;clear: both;'>
    <div style='width: 100%; border: 0.5px solid black; border-radius: 10px; margin-bottom: 10px; font-family: Calibri, Helvetica Neue, sans-serif;'>
       <table style='width: 100%; border-collapse: collapse;'>
         <tr>
               <!-- DOCUMENTO  -->
          <td style='width: 50%; padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
            <strong>{$docLabel}</strong> {$resultC['documento']}
           </td>
                 <!-- FECHA EMISION -->
          <td style='width: 50%; padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
          <strong>Fecha de Emisión:</strong> $fecha_emision
          </td>
         </tr>
 
         <!-- CLIENTE Y MONEDA -->
         <tr>
         <td style='width: 50%; padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
         <strong>{$clientLabel}</strong> {$resultC['datos']}
         </td>
         <td style='width: 50%; padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
         <strong>MONEDA:</strong> SOLES
         </td>
         </tr>
         
         <!-- DIRECCIÓN -->
         <tr>
        <td colspan='2' style='padding: 5px; font-size: 10px; font-family: Calibri, Helvetica Neue, sans-serif;'>
          <strong>Dirección:</strong> {$resultC['direccion']}
          </td>
         </tr>
       </table>
     </div>
     
     <div style='width: 100%; padding-top: 10px;'>
       <table style='width:100%; border-collapse: separate; border-spacing: 0; border-radius: 5px; overflow: hidden; margin-bottom: 0;'>
         <!-- ENCABEZADOS DE TABLA CON FONDO ROJO -->
         <tr style='background-color: #CA3438;'>
           <td style='width: 5%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>ITEM</strong></td>
    <!--
           <td style='width: 10%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>CÓDIGO</strong></td> -->

           <td style='width: 6%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>CANT.</strong></td>
           <td style='width: 40%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>DESCRIPCIÓN</strong></td>
           <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>P.UNITARIO</strong></td>
           <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>TOTAL</strong></td>
         </tr>
         $rowHTML
       </table>
       
       <!-- Sección SON con borde completo -->
       <table style='width: 100%; border-collapse: collapse; margin: 0; padding: 0;'>
         <tr>
           <td style='border: 1px solid #000000; padding: 5px; font-size: 11px; font-weight: bold; font-family: Calibri, Helvetica Neue, sans-serif;'>
             SON $totalLetras
           </td>
         </tr>
       </table>
       
       <!-- Tabla de totales alineada a la derecha -->
       <table style='width: 100%; border-collapse: collapse; margin: 0; padding: 0;'>
         <tr>
           <!-- Celda vacía para ocupar espacio a la izquierda -->
           <td style='width: 77%; padding: 0;'></td>
           
           <!-- Celda con la tabla de totales (28% = ancho de P.UNITARIO + TOTAL) -->
           <td style='width: 28%; padding: 0;'>
             <table style='width: 100%; border-collapse: collapse; margin: 0;'>
               <tr>
                 <td style='width: 50%; border-left: 1px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>Gravada:</td>
                 <td style='width: 50%; border-right: 1px solid #000000;border-left: 1px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $totalOpgravado</td>
               </tr>
               <tr>
                 <td style='border: 1px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>IGV (18.00%):</td>
                 <td style='border: 1px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $igv</td>
               </tr>
               <tr>
                 <td style='border: 1px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>Total:</td>
                 <td style='border: 1px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $total</td>
               </tr>
             </table>
           </td>
         </tr>
       </table>
     </div>
 ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->SetHTMLFooter("
    <div style='margin-top: 10px;'>
        <div style='border: 1px solid black; padding: 5px;'>
            <table style='width: 100%; border-spacing: 0; margin: 0;'>
                <tr>
                    <td style='width: 85%; padding: 0; vertical-align: middle;'>
                        <div style='font-family: Arial; font-size: 9px; line-height: 1.4;'>
                            <div style='margin: 0;'>Representación impresa de la $tipo_documeto_venta</div>
                            <div style='margin: 0;'>Usuario: EMER RODRIGO (cod: N/A)</div>
                            <div style='margin: 0;'>$hash_Doc</div>
                            <div style='margin: 0;'>Este documento puede ser validado en $dominio</div>
                        </div>
                    </td>
                    <td style='width: 15%; text-align: right; padding: 0; vertical-align: top;'>
                        <div style='width: 60px; height: 60px;'>$qrImage</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
");
    /*$this->mpdf->WriteHTML($htmlDOM,\Mpdf\HTMLParserMode::HTML_BODY);*/
    $this->mpdf->Output($nombreXML . ".pdf", 'I');
  }

  public function comprobanteVentaMa4($venta, $nombreXML = '-')
  {



    $this->mpdf = new \Mpdf\Mpdf([
      //"orientation"=>"P",
      //'margin_bottom' => 5,
      //'margin_top' => 2,
      //'margin_left' => 4,
      'format' => [210, 148],
      //'margin_right' => 4,
      'mode' => 'utf-8',
    ]);



    $listaProd1 = $this->conexion->query("SELECT productos_ventas.*,p.descripcion,p.codigo,ve.marca,ve.equipo,ve.modelo,ve.numero_serie FROM productos_ventas join productos p on p.id_producto = productos_ventas.id_producto LEFT JOIN ventas_equipos ve ON ve.id_venta_equipo = productos_ventas.id_venta_equipo WHERE productos_ventas.id_venta=" . $venta);
    $listaProd2 = $this->conexion->query("SELECT * FROM ventas_servicios WHERE id_venta=" . $venta);
    $ventaSunat = $this->conexion->query("SELECT * FROM ventas_sunat WHERE id_venta=" . $venta)->fetch_assoc();
    $guiaRealionada = '';
    $sql = "SELECT * FROM guia_remision where id_venta = $venta";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $sql = "select * from ventas where id_venta=" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $monedaVisual = $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLAR';
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $datoVenta['id_empresa'])->fetch_assoc();


    /*   var_dump("SELECT * FROM sucursales WHERE cod_sucursal ='{$_SESSION['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa']);
    die();  */
    /*   if (is_null($datoSucursal)) {
      var_dump('es nulo');
      die();
    } else {
      var_dump($datoSucursal);
      die();
    } */


    $igv_venta_sel = $datoVenta['igv'];

    $S_N = $datoVenta['serie'] . '-' . Tools::numeroParaDocumento($datoVenta['numero'], 6);
    $tipoDocNom = $datoVenta['id_tido'] == 1 ? 'BOLETA' : 'FACTURA';
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha_emision']);
    $fecha_vencimiento = Tools::formatoFechaVisual($datoVenta['fecha_vencimiento']);

    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    $menosRowsNumH = 0;

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$venta'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 1;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '<div style="width: 100%;">
        <table style="width:50%;margin:auto;display: block;text-align:center;font-size: 10px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $formatter = new NumeroALetras;


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$venta' ";
    $qrImage = '';
    $hash_Doc = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $hash_Doc = "HASH: " . $rowVS['hash'] . "<br>";
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 100px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $tipo_documeto_venta = "";

    if ($datoVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
    }

    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';

    foreach ($listaProd1 as $prod) {

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = $precio;
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');

      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 10px; text-align: left;border-left: 1px solid #363636;'>{$prod['codigo']} | {$prod['descripcion']}</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                 
                
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }
    foreach ($listaProd2 as $prod) {

      $precio = $prod['monto'];
      $importe = $precio * $prod['cantidad'];
      //$subtotal = $subtotal + $importe;
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');

      $rowHTML = $rowHTML . "
              <tr>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$contador</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>{$prod['cantidad']}</td>
                <td class='' style=' font-size: 10px; text-align: left;border-left: 1px solid #363636;'>{$prod['descripcion']}</td>
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;'>$precio</td>
                
                
                <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'>$importe</td>
              </tr>
            ";
      $contador++;
    }
    $cntRowEE = 9;
    $rowHTMLTERT = "";
    for ($tert = 0; $tert < ($cntRowEE - $contador) - $menosRowsNumH; $tert++) {
      $rowHTMLTERT = $rowHTMLTERT . " <tr>
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; color: white'>.</td>
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; '> </td>
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; '> </td> 
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636; '> </td>
        
        
        <td class='' style=' font-size: 10px; text-align: center;border-left: 1px solid #363636;border-right: 1px solid #363636;'> </td>
      </tr>";
    }




    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');

    $htmlCuadroHead = "<div style=' width: 34%;text-align: center; background-color: #ffffff ; float: right;'>

            <div style='padding: 5px;width: 100%; height: 70px; border: 2px solid #1e1e1e' class=''>
                <div style='margin-top:5px'></div>
            <span style='font-size: 12px;'>RUC: {$datoEmpresa['ruc']}</span><br>
            <div style='margin-top: 5px'></div>
            <span style='font-size: 12px;'><strong>$tipo_documeto_venta</strong></span><br>
            <div style='margin-top: 5px'></div>
            <span style='font-size: 12px;'>Nro. $S_N </span>
            </div>
            </div>
            </div>";


    $this->mpdf->WriteFixedPosHTML("<div ><img style='height: 95px;width: 360px;' src='" .
      URL::to('files/logos/' . $datoEmpresa['logo']) . "'></div>", 15, 5, 100, 120);

    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 195, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Central Telefónica: </strong> {$datoEmpresa['telefono']}</span>", 15, 32, 210, 130);




    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] == '1') {
      $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 36, 120, 130);
    } else {
      if (is_null($datoSucursal)) {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 36, 120, 130);
      } else {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoSucursal['direccion']}</span></span>", 15, 36, 120, 130);
      }
    }


    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Email: </strong> info@grupoacosta.com.pe | Web: www.vallesport.pe</span>", 15, 40, 210, 130);




    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / ($igv_venta_sel + 1) * $igv_venta_sel;
    $totalOpgravado = $total - $igv;
    $total = $total;
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');



    //$total = number_format($total, 2, '.', ',');
    /*   $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$_SESSION['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc(); */
    /*  $as = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='2' AND empresa_id=" . 28)->fetch_assoc();
    var_dump($as);
    die(); */

    if ($datoVenta['sucursal'] != '1') {
      if (is_null($datoSucursal)) {
        $resultC['direccion'] = $resultC['direccion'];
      } else {
        $resultC['direccion'] = $datoSucursal['direccion'];
      }
    }


    $html = "<div style='width: 100%;padding-top: 120px; overflow: hidden;clear: both;'>
        <div style='width: 100%;border: 1px solid black;'>
        <div style='width: 55%; float: left; '>
        
        <table style='width:100%'>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>RUC/DNI:</strong></td>
            <td style=' font-size: 10px;'>{$resultC['documento']}</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>CLIENTE:</strong></td>
            <td style=' font-size: 10px;'>{$resultC['datos']}</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>DIRECCIÓN:</strong></td>
            <td style=' font-size: 10px;'>{$resultC['direccion']}</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>NRO GUÍA:</strong></td>
            <td style=' font-size: 10px;'>$guiaRealionada</td>
          </tr>
        </table>
        </div>
        <div style='width: 45%; float: left'>
        <table style='width:100%'>
        
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>FECHA EMISIÓN:</strong></td>
            <td style=' font-size: 10px;'>$fecha_emision</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>FECHA VENCIMIENTO:</strong></td>
            <td style=' font-size: 10px;'>$fecha_vencimiento</td>
          </tr>
          
           <tr>
            <td style=' font-size: 10px;text-align: left'><strong>MONEDA:</strong></td>
            <td style=' font-size: 10px;'>$monedaVisual</td>
          </tr>
          <tr>
            <td style=' font-size: 10px;text-align: left'><strong>PAGO:</strong></td>
            <td style=' font-size: 10px;'>$tipo_pagoC</td>
          </tr>
        </table>
        </div>
        </div>
        
        
        </div>
        $tabla_cuotas
        <div style='width: 100%; padding-top: 5px;'>
        <table style='width:100%;border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <tr style='border-bottom: 1px solid #363636;border-collapse: collapse;'>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>ITEM</strong></td>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>CANT</strong></td>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>DESCRIPCION</strong></td>
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>PRECIO U.</strong></td> 
            <td style=' font-size: 10px;text-align: center; color: #000000;border: 1px solid #363636;border-collapse: collapse;'><strong>IMPORTE</strong></td>
            
          </tr>
          $rowHTML
          $rowHTMLTERT
             
         
        
        </table>
        </div>
        
        ";
    $dominio = DOMINIO . 'buscador';
    // Si la venta proviene de taller (con equipos), no renderizamos la página agregada general
    $esVentaConEquipos = false;
    try {
      $equiposVentaCheck = $this->conexion->query("SELECT 1 FROM ventas_equipos WHERE id_venta = " . intval($venta) . " LIMIT 1");
      $esVentaConEquipos = ($equiposVentaCheck && $equiposVentaCheck->num_rows > 0);
    } catch (\Throwable $e) {
      $esVentaConEquipos = false;
    }

    // Renderizar SIEMPRE la primera página (cabecera y bancario) y mover el detalle por equipo a páginas siguientes
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    /*$this->mpdf->SetHTMLFooter("<div style=' width: 100%;'>
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 9px;border: 1px solid black;'>. SON: | $totalLetras</div>
        <div style='width: 100%;margin-top: 5px;'>
                <div style='width: 18%;float: left;'>
                    $qrImage
                </div>
                <div style='width: 58%;float: left; font-size: 12px;'>
                     $hash_Doc
                        Detalle:<br>
                        Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
                </div>
                <div style='width: 24%;float: left; font-size: 12px;'>
                <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
                  <tr>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total Op. Gravado:</td>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$totalOpgravado</td>
                  </tr>
                  <tr>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>IGV:</td>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$igv</td>
                  </tr>

                  <tr>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total a Pagar</td>
                    <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$total</td>
                  </tr>

                </table>
                </div>
        </div>
 </div>");*/
    if ($datoVenta['apli_igv'] == '0') {
      $totalOpgravado = $total;
      $igv = '0.00';
    }
    //die();

    $this->mpdf->SetHTMLFooter("
        <div style='height: 3px; width:100%;'></div>
        <div style='height: 10px;width: 100%; padding-bottom: 0px;font-size: 9px;border: 1px solid black;'>. SON: | $totalLetras</div>
        
        
        <div style='width: 100%; height: 10px;  '>
        
        <div style='float: left; width: 20%; '>
        $qrImage
         
        
        </div>
         <div style='width: 50%; padding-bottom:  0px;font-size: 12px; float: left; padding-top: 5px;'>
            <div style='width: 100%'></div>
            <div style='width: 95%; padding: 3px; font-size: 10px;height: 90px '>
            $hash_Doc
            Detalle:<br>
            Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio
            <br><b>Observaciones:</b>{$datoVenta['observacion']}
            </div>
         </div>
         <div style='width: 30%; padding-top: 5px;'>
         <table style='width: 100%;border-top: 1px solid #363636;border-bottom: 1px solid #363636;border-right: 1px solid #363636;border-collapse: collapse;'>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total Op. Gravado:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$totalOpgravado</td>
          </tr>
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>IGV:</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$igv</td>
          </tr>
          
          <tr>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px; text-align: right'>Total a Pagar</td>
            <td style='border-left: 1px solid #363636;border-collapse: collapse; font-size: 10px;  text-align: right' >$total</td>
          </tr>
          
        </table>
            </div>
        </div> 
        ");

    $this->mpdf->Output($nombreXML . ".pdf", 'I');
  }
  public function comprobanteVenta($venta, $nombreXML = '-')
  {
    $this->comprobanteVentaGen("I", $venta, $nombreXML ? $nombreXML : '-');
  }

  public function comprobanteVentaBinario($venta, $nombreXML = '-')
  {
    $this->comprobanteVentaGen("F", $venta, $nombreXML ? $nombreXML : '-');
  }

  private function comprobanteVentaGen($dist, $venta, $nombreXML)
  {
    // Configuramos los márgenes del PDF
    $this->mpdf = new \Mpdf\Mpdf([
      'mode' => 'utf-8',
      'format' => 'A4',
      'margin_left' => 8,      // Margen izquierdo de 5mm
      'margin_right' => 8,     // Margen derecho de 5mm
      'margin_top' => 15,       // Margen superior de 5mm
      'margin_bottom' => 5,    // Margen inferior de 5mm
      'margin_header' => 0,    // Sin margen para el encabezado
      'margin_footer' => 8     // Sin margen para el pie de página
    ]);

    $guiaRealionada = '';
    $guiaId = null;
    $ordenCompra = '';

    // Primero verificar si existe una guía relacionada
    $sql = "SELECT * FROM guia_remision where id_venta = $venta";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
      $guiaId = $rowGuia["id_guia_remision"];
      // Si viene de guía, usar ref_orden_compra de la guía
      $ordenCompra = $rowGuia["ref_orden_compra"] ?? '';
    }

    // Si existe guía, obtener los productos con los nombres editados de guia_detalles
    if ($guiaId) {
      $listaProd1 = $this->conexion->query("SELECT
        productos_ventas.*,
        COALESCE(gd.detalles, p.detalle) as descripcion,
        p.imagen,
        COALESCE(gd.detalles, p.nombre) as nombre,
        p.codigo,
        ve.marca,
        ve.equipo,
        ve.modelo,
        ve.numero_serie
      FROM productos_ventas
      LEFT JOIN guia_detalles gd ON gd.id_producto = productos_ventas.id_producto AND gd.id_guia = $guiaId
      LEFT JOIN productos p ON p.id_producto = productos_ventas.id_producto
      LEFT JOIN ventas_equipos ve ON ve.id_venta_equipo = productos_ventas.id_venta_equipo
      WHERE productos_ventas.id_venta=" . $venta);
    } else {
      // Si no hay guía, usar el query original
      $listaProd1 = $this->conexion->query("SELECT productos_ventas.*,p.detalle as descripcion, p.imagen,p.nombre,p.codigo,ve.marca,ve.equipo,ve.modelo,ve.numero_serie FROM productos_ventas
        join productos p on p.id_producto = productos_ventas.id_producto LEFT JOIN ventas_equipos ve ON ve.id_venta_equipo = productos_ventas.id_venta_equipo WHERE productos_ventas.id_venta=" . $venta);
    }

    $listaProd2 = $this->conexion->query("SELECT * FROM ventas_servicios WHERE id_venta=" . $venta);
    $ventaSunat = $this->conexion->query("SELECT * FROM ventas_sunat WHERE id_venta=" . $venta)->fetch_assoc();

    $sql = "select * from ventas where id_venta=" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $datoVenta['id_empresa'])->fetch_assoc();

    // Si NO viene de guía (ordenCompra está vacío), usar doc_referencia de la venta
    if (empty($ordenCompra)) {
      $ordenCompra = $datoVenta['doc_referencia'] ?? '';
    }

    $igv_venta_sel = $datoVenta['igv'];

    $isSEgundoPago = false;
    $pagoData = '';
    if ($datoVenta['pagado2']) {
      $isSEgundoPago = true;
      $sql = "select *  from metodo_pago where id_metodo_pago='{$datoVenta['medoto_pago2_id']}'";
      $metodo2 = $this->conexion->query($sql)->fetch_assoc();
      $sql = "select *  from metodo_pago where id_metodo_pago='{$datoVenta['medoto_pago_id']}'";
      $metodo1 = $this->conexion->query($sql)->fetch_assoc();

      $pagoData = "<b>METODO DE PAGO 1 \"{$metodo1['nombre']}\"</b>: S/{$datoVenta['pagado']}, <b>Y METODO DE PAGO 2 \"{$metodo2['nombre']}\"</b>: S/{$datoVenta['pagado2']}";
    } else {
      $sql = "select *  from metodo_pago where id_metodo_pago='{$datoVenta['medoto_pago_id']}'";
      $metodo1 = $this->conexion->query($sql)->fetch_assoc();
      $montoPagadoooo = $datoVenta['pagado'] ? $datoVenta['pagado'] : $datoVenta["total"];
      $pagoData = "<b>METODO DE PAGO \"{$metodo1['nombre']}\"</b>: S/$montoPagadoooo";
    }


    $S_N = $datoVenta['serie'] . '-' . Tools::numeroParaDocumento($datoVenta['numero'], 6);
    $tipoDocNom = $datoVenta['id_tido'] == 1 ? 'BOLETA' : 'FACTURA';
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : strlen($resultC['documento'] == 11 ? 'RUC' : '');
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha_emision']);
    $fecha_vencimiento = Tools::formatoFechaVisual($datoVenta['fecha_vencimiento']);

    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    $menosRowsNumH = 0;

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$venta'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 1;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
              <tr>
                  <td>Cuota $tempNum</td>
                  <td>$tempFecha </td>
                  <td>S/ $tempMonto</td>
              </tr>
              ";
      }
      $tabla_cuotas = '<div style="width: 100%;">
          <table style="width:50%;margin:auto;display: block;text-align:center;font-size: 12px;">
                  <thead>
                  <tr>
                      <th>CUOTA</th>
                      <th>FECHA</th>
                      <th>MONTO</th>
                  </tr>
                  </thead>
                  <tbody>
                      ' . $rowTempCuo . '
                  </tbody>
          </table>
          </div>';
    }

    $formatter = new NumeroALetras;


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$venta' ";
    $qrImage = '';
    $hash_Doc = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $hash_Doc = "HASH: " . $rowVS['hash'] . "<br>";

      try {
        $qrCode = new QrCode($rowVS["qr_data"]);
        $qrCode->setSize(150);
        $image = $qrCode->writeString(); // Salida en formato de texto
        $imageData = base64_encode($image);
        $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
      } catch (Exception $e) {
        echo 'Error generando el código QR: ' . $e->getMessage();
      }
    } else {
      echo 'No se encontró el registro.';
    }


    $tipo_documeto_venta = "";

    if ($datoVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($datoVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
    }

    $htmlDOM = '';
    $totalLetras = 'SOLES';

    $totalOpGratuita = 0;
    $totalOpExonerada = 0;
    $totalOpinafec = 0;
    $totalOpgravado = 0;
    $totalDescuento = 0;
    $totalOpinafecta = 0;
    $SC = 0;
    $percepcion = 0;
    $total = 0;
    $contador = 1;
    $igv = 0;

    $rowHTML = '';
    $rowHTMLTERT = '';

    foreach ($listaProd1 as $prod) {
      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = $precio;
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      $detalle = nl2br($prod['descripcion']);

      // Construir información del producto y equipo
      $productoInfo = "<strong>{$prod['nombre']}</strong>";

      // Agregar información del equipo si está disponible
      if (!empty($prod['marca']) || !empty($prod['equipo']) || !empty($prod['modelo']) || !empty($prod['numero_serie'])) {
        $equipoInfo = '';
        if (!empty($prod['equipo'])) {
          $equipoInfo = 'EQUIPO: ';
          if (!empty($prod['marca'])) {
            $equipoInfo .= $prod['marca'] . ' ';
          }
          $equipoInfo .= $prod['equipo'];
          if (!empty($prod['modelo'])) {
            $equipoInfo .= ' - Modelo: ' . $prod['modelo'];
          }
          if (!empty($prod['numero_serie'])) {
            $equipoInfo .= ' - Serie: ' . $prod['numero_serie'];
          }
          $productoInfo .= '<br>' . $equipoInfo;
        }
      }

      $afectIgv = "Gravado";

      $rowHTML = $rowHTML . "
      <tr>
        <td style='width: 5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-left: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>{$contador}</td>
        <td style='width: 10%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['codigo']}</td>
        <td style='width: 6%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['cantidad']}</td>
        <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>UNIDAD</td>
        <td style='width: 40%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: left; padding-top: 6px; padding-bottom: 6px;'>{$productoInfo}</td>
        <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$afectIgv}</td>
        <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>S/ {$precio}</td>
        <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-right: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>S/ {$importe}</td>
      </tr>";
      $contador++;
    }

    foreach ($listaProd2 as $prod) {
      $precio = $prod['monto'];
      $importe = $precio * $prod['cantidad'];
      $total += $importe;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      $afectIgv = "Gravado";

      $detalle = nl2br($prod['descripcion']);

      $rowHTML = $rowHTML . "
       <tr>
        <td style='width: 5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-left: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>{$contador}</td>
        <td style='width: 10%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['codigo']}</td>
        <td style='width: 6%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['cantidad']}</td>
        <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>UNIDAD</td>
        <td style='width: 40%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: left; padding-top: 6px; padding-bottom: 6px;'><strong>{$prod['nombre']}</strong><br>{$detalle}</td>
        <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$afectIgv}</td>
        <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>S/ {$precio}</td>
        <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-right: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>S/ {$importe}</td>
      </tr>";
      $contador++;
    }

    // Eliminamos las filas vacías para que la tabla se adapte según los productos
    $rowHTMLTERT = "";

    $totalLetras = $formatter->toInvoice(number_format($total, 2, '.', ''), 2, $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');

    // Generar cabecera del PDF
    $htmlCuadroHead = "<div style='width: 38%;text-align: center; background-color: #ffffff; float: right;font-family: Calibri, Helvetica Neue, sans-serif; font-size: 12px;'>
                <div style='width: 100%; height: 100px;border-radius:10px; border: 1px solid #1e1e1e' >
                    <div style='margin-top:10px'></div>
                    <span> <strong> R.U.C: {$datoEmpresa['ruc']} </strong></span><br>
  
                    <div style='margin-top: 10px '></div>
                    <div style='background-color: #CA3438; color:white; margin:0 ; padding: 15px;width: 100%;'>
                    <span ><strong>$tipo_documeto_venta</strong></span>
                    </div>
                    
                    <br>
                  
               <span style='display: block; text-align: center; font-size: 14px'>Nro. $S_N</span>
                 <div style='margin-top:10px'></div>
                </div>
            </div>";

    /**/
    // Escribir encabezado de la empresa
    $this->escribirEncabezadoEmpresa($datoEmpresa, $htmlCuadroHead);
    $totalOpGratuita = number_format($totalOpGratuita, 2, '.', ',');
    $totalOpExonerada = number_format($totalOpExonerada, 2, '.', ',');
    $totalOpinafec = number_format($totalOpinafec, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $totalDescuento = number_format($totalDescuento, 2, '.', ',');
    $totalOpinafecta = number_format($totalOpinafecta, 2, '.', ',');
    $SC = number_format($SC, 2, '.', ',');
    $percepcion = number_format($percepcion, 2, '.', ',');
    $igv = $total / ($igv_venta_sel + 1) * $igv_venta_sel;
    $totalOpgravado = $total - $igv;
    $total_formateado = number_format($total, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');

    $monedaVisual = $datoVenta["moneda"] == "1" ? "SOLES" : 'DOLAR';

    // Crear la sección de detalle de forma de pago para crédito
    $detalle_forma_pago = '';
    if ($datoVenta["id_tipo_pago"] == '2') {
      $detalle_forma_pago = '
        <div style="width: 100%; margin-top: 10px; margin-bottom: 10px; text-align: center;">
          <div style="text-align: center; font-weight: bold; padding: 5px; margin-bottom: 0;font-family: Calibri, Helvetica Neue, sans-serif;font-size: 12px;">
            DETALLE DE LA FORMA DE PAGO: CRÉDITO
          </div>
          <div style="display: flex; justify-content: center;">
            <table style="width: 60%; border-collapse: collapse; table-layout: auto; margin: 0 auto;">
              <tr style="background-color: #CA3438; color: white;">
                <th style="border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px; white-space: nowrap;  color: #ffffff">N°</th>
                <th style="border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px; white-space: nowrap; color: #ffffff">Fecha de Vencimiento</th>
                <th style="border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px; white-space: nowrap; color: #ffffff">Moneda</th>
                <th style="border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px; white-space: nowrap; color: #ffffff">Monto</th>
                <th style="border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px; white-space: nowrap; color: #ffffff">Estado</th>
              </tr>';

      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$venta'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $detalle_forma_pago .= "
              <tr>
                <td style='border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px;'>$contadorCuota</td>
                <td style='border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px;'>$tempFecha</td>
                <td style='border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px;'>SOLES</td>
                <td style='border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px;'>$tempMonto</td>
                <td style='border: 1px solid #CA3438; padding: 3px; text-align: center; font-size: 10px;'>Pendiente</td>
              </tr>";
      }

      $detalle_forma_pago .= '
            </table>
          </div>
        </div>';
    }

    // Crear la sección de información bancaria con el formato correcto según la imagen
    $info_bancaria = '
<div style="margin-bottom: 0; padding-bottom: 7;">
<table style="width: 100%; border-collapse: collapse; border: 0.5px solid #373435; margin: 0; padding: 0;font-family: Calibri, Helvetica Neue, sans-serif; font-size: 9px;">
    <tr>
        <td colspan="3" style="text-align: end; border-bottom: 0.5px solid #373435; padding: 4px; ">
            USTED PUEDE HACER PAGOS DIRECTAMENTE EN NUESTRAS CUENTAS CORRIENTES
        </td>
    </tr>
    <tr>
        <td style="width: 33.33%;  padding: 4px;">
            BANCO: BANCO DE CREDITO DEL PERU - BCP<br>
            TITULAR: COMERCIAL & INDUSTRIAL JVC S.A.C.<br>
            NRO CUENTA (SOLES): 1912019937002<br>
            CCI: 00219100201993700252
        </td>
        <td style="width: 33.33%; padding: 4px; ">
            BANCO: BANCO DE CREDITO DEL PERU - BCP<br>
            TITULAR: COMERCIAL & INDUSTRIAL JVC S.A.C.<br>
            NRO CUENTA (DÓLARES): 1912363004136<br>
            CCI: 00219100236300413658
        </td>
        <td style="width: 33.33%;  padding: 4px;">
            BANCO: BANCO INTERBANK<br>
            TITULAR: COMERCIAL & INDUSTRIAL JVC S.A.C.<br>
            NRO CUENTA (SOLES): 0933001544118<br>
            CCI: 00309300300154411828
        </td>
    </tr>
    <tr>
        <td style="width: 33.33%; padding: 4px;">
            BANCO: BANCO DE LA NACION<br>
            TITULAR: COMERCIAL & INDUSTRIAL JVC S.A.C.<br>
            NRO CUENTA (SOLES): 00046079272<br>
            CCI: 00046079272
        </td>
        <td style="width: 33.33%;  padding: 4px; ">
            BANCO: BBVA - BANCO CONTINENTAL<br>
            TITULAR: COMERCIAL & INDUSTRIAL JVC S.A.C.<br>
            NRO CUENTA (SOLES): 00110484010001659432<br>
            CCI: 0114840001
        </td>
        <td style="width: 33.33%;  padding: 4px;">
        </td>
    </tr>
</table>
</div>';


    // Obtener información del usuario (vendedor)
    $sql = "SELECT u.* FROM usuarios u 
            JOIN ventas v ON u.usuario_id = v.id_vendedor 
            WHERE v.id_venta = '$venta'";
    $usuario_result = $this->conexion->query($sql);
    $usuario = $usuario_result ? $usuario_result->fetch_assoc() : null;

    // Preparar los datos del usuario para mostrar en el comprobante
    $nombre_usuario = isset($usuario['nombres']) ? $usuario['nombres'] . ' ' . (isset($usuario['apellidos']) ? $usuario['apellidos'] : '') : 'Usuario no registrado';
    $codigo_usuario = isset($usuario['codigo']) ? $usuario['codigo'] : 'N/A';

    // Crear la sección de observación
    $observacion = '';
    if (!empty($datoVenta['observacion'])) {
      $observacion = '<div style="margin-top: 5px; font-size: 10px;"><strong>Observación:</strong> ' . $datoVenta['observacion'] . '</div>';
    }
    // Determinar el tipo de documento y etiqueta basado en la longitud del documento
    $isRuc = strlen($resultC['documento']) == 11;
    $docLabel = $isRuc ? "R.U.C.:" : "DNI:";
    $clientLabel = $isRuc ? "Razón Social:" : "Cliente:";


    $html = "
      <div style='width: 1000%;padding-top: 150px; overflow: hidden;clear: both;'>
  
        <div style='width: 100%; padding: 10px; border: 0.5px solid black; border-radius: 10px; margin-bottom: 30px; overflow: hidden;'>
          <table style='width: 100%; border-collapse: collapse; margin: -10px;'>
            <tr>
                <td style='width: 16.66%; padding: 8px; text-align: center; font-family: Arial; border-right: 0.5px solid black;'>
                  <strong style='font-size: 10px; display: block; '>Fecha de Emisión:</strong>  <div style='height: 2px;'></div>
                  <span style='font-size: 10px;'>  $fecha_emision</span>
                </td>
                <td style='width: 16.66%; ;padding: 8px; text-align: center; font-family: Arial; border-right: 0.5px solid black;'>
                  <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Forma de Pago</strong>  <div style='height: 2px;'></div>
                  <span style='font-size: 10px;'>{$tipo_pagoC}</span>
                </td>
                <td style='width: 16.66%; padding: 8px; text-align: center; font-family: Arial; border-right: 0.5px solid black;'>
                  <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Moneda</strong>  <div style='height: 2px;'></div>
                  <span style='font-size: 10px;'>$monedaVisual</span>
                </td>
                <td style='width: 17.66%; padding: 8px; text-align: center; font-family: Arial; '>
                  <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Guía de Remisión N° <br> 
                  </strong> <div style='height: 2px;'></div>
                  <span style='font-size: 10px;'>$guiaRealionada</span>
                </td>
            </tr>
          </table>
        </div>
  

        <div style='width: 100%; border: 0.5px solid black; border-radius: 10px; margin-bottom: 10px; font-family: Calibri, Helvetica Neue, sans-serif;'>
          <table style='width: 100%; border-collapse: collapse;'>
            <tr>
             <td style='width: 50%; padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
               <strong>{$docLabel}</strong> {$resultC['documento']}
              </td>
              <td style='width: 50%; padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
              <strong>Orden de Compra:</strong> {$ordenCompra}
              </td>
            </tr>

            <!-- CLIENTE -->
            <tr>
            <td colspan='2' style='padding: 5px; font-size: 10px; border-bottom: 0.5px solid #000000; font-family: Calibri, Helvetica Neue, sans-serif;'>
            <strong>{$clientLabel}</strong> {$resultC['datos']}
            </td>
            </tr>
            <!-- DIRECCIÓN -->
            <tr>
           <td colspan='2' style='padding: 5px; font-size: 10px; font-family: Calibri, Helvetica Neue, sans-serif;'>
             <strong>Dirección:</strong> {$resultC['direccion']}
             </td>
            </tr>
          </table>
        </div>
          
          
      </div>
         <!-- $tabla_cuotas -->
<div style='width: 100%;'>
  <table style='width:100%; border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden; margin-bottom: 0;'>
    <tr style='background-color: #CA3438;'>
      <td style='width: 5%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>ITEM</strong></td>
      <td style='width: 10%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>CÓDIGO</strong></td>
      <td style='width: 6%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>CANT.</strong></td>
      <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>UNID.</strong></td>
      <td style='width: 40%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>DESCRIPCIÓN</strong></td>
      <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>AFECT <br> IGV.</strong></td> 
      <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>P.UNITARIO</strong></td> 
      <td style='width: 11.5%; font-family: Calibri, Helvetica Neue, sans-serif; text-align: center; color: #ffffff; padding: 4px; border: 1px solid #CA3438; font-size: 10px;'><strong>TOTAL</strong></td> 
    </tr>
    $rowHTML
  </table>

  <!-- Sección SON con borde completo - SIN MARGEN -->
  <table style='width: 100%; border-collapse: collapse; margin: 0; padding: 0;'>
    <tr>
      <td style='border: 1px solid #000000; padding: 5px; font-size: 11px; font-weight: bold; font-family: Calibri, Helvetica Neue, sans-serif;'>
        SON $totalLetras
      </td>
    </tr>
  </table>

  <!-- Tabla con observación y totales alineados con P.UNITARIO y TOTAL -->
  <table style='width: 100%; border-collapse: collapse; margin: 0; padding: 0;'>
    <tr>
      <td style='width: 77%; vertical-align: top; padding: 5px 0 0 0; font-size: 10px;'>
        $observacion
        <!--   $pagoData -->
        <!--  <strong>FECHA VENCIMIENTO:</strong> $fecha_vencimiento -->
      </td>
      <td style='width: 23%; vertical-align: top; padding: 0;'>
        <table style='width: 100%; border-collapse: collapse; margin: 0;'>
          <tr>
            <td style='width: 50%; border-left: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>Gravada:</td>
            <td style='width: 50%;  border-left: 0.5px solid #000000;  border-right: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $totalOpgravado</td>
          </tr>
          <tr>
            <td style='border: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>IGV (18.00%):</td>
            <td style='border: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $igv</td>
          </tr>
          <tr>
            <td style='border: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>Descuento Total:</td>
            <td style='border: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $totalDescuento</td>
          </tr>
          <tr>
            <td style='border: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: left;'>Total:</td>
            <td style='border: 0.5px solid #000000; padding: 3px; font-size: 10px; text-align: right;'>S/ $total_formateado</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  $detalle_forma_pago
</div>
       
         
          ";

    if ($datoVenta['apli_igv'] == '0') {
      $igv = '0.00';
      $totalOpgravado = $total;
    }
    $dominio = DOMINIO . 'buscador';
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    // Modificar el footer para alinear correctamente el QR y la información
    $this->mpdf->SetHTMLFooter('
    <div style="margin-top: 0; padding-top: 0;">
        ' . $info_bancaria . '
        <div style="border: 1px solid black; padding: 2px; margin-top: 5px;">
            <table style="width: 100%; border-spacing: 0; margin: 0;">
                <tr>
                    <td style="width: 85%; padding: 0;">
                        <div style="font-family: Arial; font-size: 8px; line-height: 1.2;">
                            <div style="margin: 0;">Representación impresa de la Factura Electrónica</div>
                            <div style="margin: 0;">Para Consultar el comprobante vista: '.$dominio.'</div>
                            <div style="margin: 0;"> <strong>Usuario:</strong> ' . $nombre_usuario . ' (cod: ' . $codigo_usuario . ')</div>
                            <div style="margin: 0;">HASH: ' . $rowVS['hash'] . '</div>
                        </div>
                    </td>
                    <td style="width: 15%; text-align: right; padding: 0; vertical-align: top;">
                        <img style="width: 60px; height: 60px;" src="data:image/png;base64,' . $imageData . '">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    ');
    if ($dist == 'I') {
      $this->mpdf->Output((is_string($nombreXML) ? $nombreXML : '') . ".pdf", $dist);
    } elseif ($dist == 'F') {
      $this->mpdf->Output(base64_decode((is_string($nombreXML) ? $nombreXML : '')), $dist);
    }
  }


  public function imprimirvoucher5_6cm($id)
  {
    $this->venta->setIdVenta($id);

    /* echo "<pre>"; */
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_bottom' => 5,
      'margin_top' => 7,
      'margin_left' => 4,
      'margin_right' => 4,
      'mode' => 'utf-8',
    ]);

    $this->venta->setIdVenta($id);
    $sql = "SELECT * FROM ventas where id_venta =$id ";
    $dataVenta = $this->conexion->query($sql)->fetch_assoc();
    $igv_venta_sel = $dataVenta['igv'];
    $sql = "SELECT * FROM empresas where id_empresa = '{$dataVenta['id_empresa']}' ";
    $dataEmpresa = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM clientes where id_cliente = '{$dataVenta['id_cliente']}' ";
    $dataCliente = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT pv.*,p.descripcion,p.codigo FROM productos_ventas pv join productos p on p.id_producto = pv.id_producto where pv.id_venta =$id ";
    $dataProVenta = $this->conexion->query($sql);

    $sql = "SELECT * FROM ventas_servicios where id_venta =$id ";
    $dataServVenta = $this->conexion->query($sql);

    $guiaRealionada = '';
    $sql = "SELECT * FROM guia_remision where id_venta = $id";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $clienteDoc = $dataCliente['documento'];

    $rowsHTML = '';
    $contador = 1;

    $tipo_pagoC = $dataVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';
    $menosRowsNumH = 0;

    $totalImporte = 0;

    if ($dataVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$id'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 10;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH += 11;
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '

<div style="width: 100%; text-align: center;margin-top:3px">
<strong><span style="font-size:10px">Cuotas de pago</span></strong>
</div>
<div style="width: 100%;">
        <table style="width:90%;margin:auto;display: block;text-align:center;font-size: 10px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $rowTamanioExtra = 0;

    foreach ($dataServVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['monto'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['monto'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 8px'>$cantidadss</td>
            <td style='font-size: 8px'>{$ser['descripcion']}</td>
            <td style='font-size: 8px'>$motoFor</td>
            <td style='font-size: 8px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 23;
    }

    foreach ($dataProVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['precio'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['precio'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 8px'>$cantidadss</td>
            <td style='font-size: 8px'>{$ser['codigo']} | {$ser['descripcion']}</td>
            <td style='font-size: 8px'>$motoFor</td>
            <td style='font-size: 8px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 23;
    }


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$id' ";
    $qrImage = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $data = '';
    $detalles = [];
    $fecha = date('d/m/Y', strtotime($dataVenta['fecha_emision']));
    $fechaVenc = date('d/m/Y', strtotime($dataVenta['fecha_vencimiento']));
    $vendedor = '';
    $cliente = $dataCliente['datos'];
    $telefono_ = '';
    $direccion_ = $dataVenta['direccion'];
    $puesto = '';
    $zona = '';

    $doc_S_N = $dataVenta["serie"] . "-" . Tools::numeroParaDocumento($dataVenta['numero'], 6);
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalImporte, 2, '.', ''), 2, $dataVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');
    $totalIGVNumeros = number_format($totalImporte / ($igv_venta_sel + 1) * $igv_venta_sel, 2, '.', '');
    $totalNumeros = number_format($totalImporte, 2, '.', '');

    $nom_emp = $dataEmpresa['razon_social'];
    $telefono = $dataEmpresa['telefono'];
    $direccion = $dataEmpresa['direccion'];
    $propaganda = $dataEmpresa['propaganda'];

    $tipo_documeto_venta = "";

    if ($dataVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
      $rowTamanioExtra -= 40;
    }


    $this->mpdf->AddPageByArray([
      "orientation" => "P",
      "newformat" => [56, 190 + $rowTamanioExtra + $menosRowsNumH]
    ]);
    $dominio = DOMINIO;


    if ($dataVenta['apli_igv'] == '0') {
      $totalIGVNumeros = '0.00';
    }
    /*var_dump($totalIGVNumeros);
      die();*/
    $sql = "select * from ventas where id_venta=" . $id;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] != '1') {
      if (!is_null($datoSucursal)) {
        $direccion_ = $datoSucursal['direccion'];
      }
    }


    $html = "
<div style='width: 100%'>
<table style='width:100%;margin-bottom: 10px'>
  <tr>
    <td align='center'>
      <img style=' max-width: 80%;' src='" . URL::to('files/logos/' . $dataEmpresa['logo']) . "'>
</td>
</tr>
</table>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 10px;font-weight: bold'>{$dataEmpresa["razon_social"]} </span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 9px'>RUC: {$dataEmpresa["ruc"]}</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 9px'>$direccion</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 9px'>$telefono</span>
    </div>
    
    <div style='width: 100%;text-align: center;margin-top: 10px;'>
    <span style='font-size: 9px;font-weight: bold'>$propaganda</span><br>
        <span style='font-size: 9px;font-weight: bold'>$tipo_documeto_venta</span><br>
        <span style='font-size: 9px;'>$doc_S_N</span>
        
    </div>
    <hr>
    <div style='width: 100%;text-align: center'>
        <table style='width:100%'>
          <tr>
            <td style='font-size: 8px;width: 25%'><strong>Fecha E:</strong></td>
            <td style='font-size: 8px;'>$fecha</td>
          </tr>
          <tr>
            <td style='font-size: 8px;width: 25%'><strong>Fecha V:</strong></td>
            <td style='font-size: 8px;'>$fechaVenc</td>
          </tr>
          <tr>
            <td style='font-size: 8px;width: 25%'><strong>RUC/DNI:</strong></td>
            <td style='font-size: 8px;'>$clienteDoc</td>
          </tr>
          <tr>
            <td style='font-size: 8px'><strong>Cliente:</strong></td>
            <td style='font-size: 8px'>$cliente</td>
          </tr>
          <tr>
            <td style='font-size: 7.5px'><strong>Dirección:</strong></td>
            <td style='font-size: 7.5px'>$direccion_</td>
          </tr>
           <tr>
            <td style='font-size: 7.5px'><strong>Pago:</strong></td>
            <td style='font-size: 7.5px'>$tipo_pagoC</td>
          </tr>
          <tr>
            <td style='font-size: 8px'><strong>Nro. Guia:</strong></td>
            <td style='font-size: 8px'>$guiaRealionada</td>
          </tr>
        </table>
    </div>
    
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 10px;'>--------------------- Productos --------------------</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <table style='width: 100%'>
            <tr>
                <td style='border-bottom:1px solid black;font-size: 8px'>CNT</td>
                <td style='border-bottom:1px solid black;font-size: 8px'>DESCRIPCION</td>
                <td style='border-bottom:1px solid black;font-size: 8px'>PR.U.</td>
                <td style='border-bottom:1px solid black;font-size: 8px;text-align: center'>IMPR.</td>
            </tr>
            $rowsHTML
            <tr>
                <td style='border-top:1px solid black; font-size: 8px;text-align: right' colspan='3'>IGV</td>
                <td style='border-top:1px solid black;font-size: 8px;text-align: center' >$totalIGVNumeros</td>
            </tr>
            <tr>
                <td style=' font-size: 8px;text-align: right' colspan='3'>Total</td>
                <td style='font-size: 8px;text-align: center' >$totalNumeros</td>
            </tr>
        </table>
    </div>
    <br>
    <div style='width: 100%;'>
        <span style='font-size: 8px'>SON: $totalLetras</span>
    </div>
    $tabla_cuotas
    <div style='width: 100%;'>
        <span style='font-size: 8px'><b>Observaciones:</b> {$dataVenta['observacion']}</span>
    </div>
    <br>
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 8px'>Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 8px'>Gracias por su preferencia....</span>
    </div>
    <div style='width: 100%; '>
        $qrImage
    </div>
    
    
</div>
";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }
  public function imprimirvoucher8cm($id)
  {
    $this->venta->setIdVenta($id);

    /* echo "<pre>"; */
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_bottom' => 5,
      'margin_top' => 10,
      'margin_left' => 4,
      'margin_right' => 4,
      'mode' => 'utf-8',
    ]);

    $this->venta->setIdVenta($id);
    $sql = "SELECT * FROM ventas where id_venta =$id ";
    $dataVenta = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM usuarios where usuario_id = '{$dataVenta["id_vendedor"]}' ";
    $cajero = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT u.nombres FROM cotizaciones c
    INNER JOIN usuarios u on u.usuario_id =  c.id_usuario
    where c.cotizacion_id = '{$dataVenta["id_coti"]}'";
    $vendor = $this->conexion->query($sql)->fetch_assoc();

    $trCajero = "";
    $trVendor = "";
    if ($cajero["nombres"]) {
      $trCajero = " <tr>
                <td style='font-size: 11px'><strong>Cajero:</strong></td>
                <td style='font-size: 11px'>{$cajero["nombres"]}</td>
              </tr>";
    }

    if ($vendor["nombres"]) {
      $trVendor = " <tr>
                <td style='font-size: 11px'><strong>Vendedor:</strong></td>
                <td style='font-size: 11px'>{$vendor["nombres"]}</td>
              </tr>";
    }

    $igv_venta_sel = $dataVenta['igv'];

    $sql = "SELECT * FROM empresas where id_empresa = '{$dataVenta['id_empresa']}' ";
    $dataEmpresa = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT * FROM clientes where id_cliente = '{$dataVenta['id_cliente']}' ";
    $dataCliente = $this->conexion->query($sql)->fetch_assoc();

    $sql = "SELECT pv.*,p.descripcion,p.codigo FROM productos_ventas pv join productos p on p.id_producto = pv.id_producto where pv.id_venta =$id ";
    $dataProVenta = $this->conexion->query($sql);

    $sql = "SELECT * FROM ventas_servicios where id_venta =$id ";
    $dataServVenta = $this->conexion->query($sql);

    $guiaRealionada = '';
    $sql = "SELECT * FROM guia_remision where id_venta = $id";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $rowsHTML = '';
    $contador = 1;

    $tipo_pagoC = $dataVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';
    $menosRowsNumH = 0;

    $totalImporte = 0;

    if ($dataVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM dias_ventas WHERE id_venta='$id'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      $menosRowsNumH = 10;
      foreach ($resulTempCuo as $cuotTemp) {
        $menosRowsNumH += 10;
        $menosRowsNumH++;
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
            <tr>
                <td>Cuota $tempNum</td>
                <td>$tempFecha </td>
                <td>S/ $tempMonto</td>
            </tr>
            ";
      }
      $tabla_cuotas = '

<div style="width: 100%; text-align: center;margin-top:3px;">
<strong><span  >Cuotas de pago</span></strong>
</div>
<div style="width: 100%;">
        <table style="width:90%;margin:auto;display: block;text-align:center;font-size: 10px;">
                <thead>
                <tr>
                    <th>CUOTA</th>
                    <th>FECHA</th>
                    <th>MONTO</th>
                </tr>
                </thead>
                <tbody>
                    ' . $rowTempCuo . '
                </tbody>
        </table>
        </div>';
    }

    $rowTamanioExtra = 0;

    foreach ($dataServVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['monto'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['monto'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 10px'>$cantidadss</td>
            <td style='font-size: 10px'>{$ser['descripcion']}</td>
            <td style='font-size: 10px'>$motoFor</td>
            <td style='font-size: 10px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 10;
    }

    foreach ($dataProVenta as $ser) {
      $totalM = $ser['cantidad'] * $ser['precio'];
      $totalImporte += $totalM;
      $motoFor = number_format($ser['precio'], 2, ".", "");
      $totalM = number_format($totalM, 2, ".", "");
      $cantidadss = number_format($ser['cantidad'], 0, "", "");
      $rowsHTML .= "<tr>
            <td style='font-size: 10px'>$cantidadss</td>
            <td style='font-size: 10px'>{$ser['codigo']} | {$ser['descripcion']}</td>
            <td style='font-size: 10px'>$motoFor</td>
            <td style='font-size: 10px'>$totalM</td>
            </tr>";
      $contador++;
      $rowTamanioExtra += 10;
    }


    $sql = "SELECT * FROM ventas_sunat where id_venta = '$id' ";
    $qrImage = '';
    if ($rowVS = $this->conexion->query($sql)->fetch_assoc()) {
      $qrCode = new QrCode($rowVS["qr_data"]);
      $qrCode->setSize(150);
      $image = $qrCode->writeString(); //Salida en formato de texto
      $imageData = base64_encode($image);
      $qrImage = '<img style="width: 130px;" src="data:image/png;base64,' . $imageData . '">';
    }

    $data = '';
    $detalles = [];
    $fecha = date('d/m/Y', strtotime($dataVenta['fecha_emision']));
    $fechaVenc = date('d/m/Y', strtotime($dataVenta['fecha_vencimiento']));
    $vendedor = '';
    $cliente = $dataCliente['datos'];

    $clienteDoc = $dataCliente['documento'];

    $telefono_ = '';
    $direccion_ = $dataVenta['direccion'];
    $puesto = '';
    $zona = '';

    $doc_S_N = $dataVenta["serie"] . "-" . Tools::numeroParaDocumento($dataVenta['numero'], 6);
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalImporte, 2, '.', ''), 2, $dataVenta["moneda"] == "1" ? "SOLES" : 'DOLARES');
    $totalIGVNumeros = number_format($totalImporte / ($igv_venta_sel + 1) * $igv_venta_sel, 2, '.', '');
    $totalNumeros = number_format($totalImporte, 2, '.', '');

    $nom_emp = $dataEmpresa['razon_social'];
    $telefono = $dataEmpresa['telefono'];
    $direccion = $dataEmpresa['direccion'];
    $propaganda = $dataEmpresa['propaganda'];
    $tipo_documeto_venta = "";

    if ($dataVenta['id_tido'] == 1) {
      $tipo_documeto_venta = "BOLETA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 2) {
      $tipo_documeto_venta = "FACTURA DE VENTA ELECTRÓNICA";
    } elseif ($dataVenta['id_tido'] == 6) {
      $qrImage = '';
      $tipo_documeto_venta = "NOTA DE VENTA  ELECTRÓNICA";
      $rowTamanioExtra -= 30;
    }

    $this->mpdf->AddPageByArray([
      "orientation" => "P",
      "newformat" => [80, 240 + $rowTamanioExtra + $menosRowsNumH]
    ]);
    $dominio = DOMINIO;

    if ($dataVenta['apli_igv'] == '0') {
      $totalIGVNumeros = '0.00';
    }

    $sql = "select * from ventas where id_venta=" . $id;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] != '1') {
      if (!is_null($datoSucursal)) {
        $direccion_ = $datoSucursal['direccion'];
      }
    }


    $html = "
<div style='width: 100%'>
<table style='width:100%;margin-bottom: 10px'>
  <tr>
    <td align='center'>
      <img style=' max-width: 85%;' src='" . URL::to('files/logos/' . $dataEmpresa['logo']) . "'>
</td>
</tr>
</table>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 13px;font-weight: bold'>{$dataEmpresa["razon_social"]} </span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>RUC: {$dataEmpresa["ruc"]}</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>$direccion</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>$telefono</span>
    </div>
    
    <div style='width: 100%;text-align: center;margin-top: 10px;'>
        <span style='font-size: 13px;font-weight: bold'>$propaganda</span><br>
        <span style='font-size: 13px;font-weight: bold'>$tipo_documeto_venta</span><br>
        <span style='font-size: 13px;'>$doc_S_N</span>
        
    </div>
    <hr>
    <div style='width: 100%;text-align: center'>
        <table style='width:100%'>
          <tr>
            <td style='font-size: 11px;width: 25%'><strong>Fecha E:</strong></td>
            <td style='font-size: 11px;'>$fecha</td>
          </tr>
          <tr>
            <td style='font-size: 11px;width: 25%'><strong>Fecha V:</strong></td>
            <td style='font-size: 11px;'>$fechaVenc</td>
          </tr>
           <tr>
            <td style='font-size: 11px;width: 25%'><strong>RUC/DNI:</strong></td>
            <td style='font-size: 11px;'>$clienteDoc</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Cliente:</strong></td>
            <td style='font-size: 11px'>$cliente</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Dirección:</strong></td>
            <td style='font-size: 11px'>$direccion_</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Pago:</strong></td>
            <td style='font-size: 11px'>$tipo_pagoC</td>
          </tr>
          <tr>
            <td style='font-size: 11px'><strong>Nro. Guia:</strong></td>
            <td style='font-size: 11px'>$guiaRealionada</td>
          </tr>
          $trCajero
          $trVendor
        </table>
    </div>
    
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 13px;'>---------------------- Productos -----------------------</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <table style='width: 100%'>
            <tr>
                <td style='border-bottom:1px solid black;font-size: 11px'>CNT</td>
                <td style='border-bottom:1px solid black;font-size: 11px'>DESCRIPCION</td>
                <td style='border-bottom:1px solid black;font-size: 11px'>PR.U.</td>
                <td style='border-bottom:1px solid black;font-size: 11px;text-align: center'>IMPR.</td>
            </tr>
            $rowsHTML
            <tr>
                <td style='border-top:1px solid black; font-size: 11px;text-align: right' colspan='3'>IGV</td>
                <td style='border-top:1px solid black;font-size: 11px;text-align: center' >$totalIGVNumeros</td>
            </tr>
            <tr>
                <td style=' font-size: 11px;text-align: right' colspan='3'>Total</td>
                <td style='font-size: 11px;text-align: center' >$totalNumeros</td>
            </tr>
        </table>
    </div>
    <br>
    <div style='width: 100%;'>
        <span style='font-size: 11px'>SON: $totalLetras</span>
    </div>
    $tabla_cuotas
     <div style='width: 100%;'>
        <span style='font-size: 12px'><b>Observaciones:</b> {$dataVenta['observacion']}</span>
    </div>
    <br>
     <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>Representación impresa de la $tipo_documeto_venta <br>Este documento puede ser validado en $dominio</span>
    </div>
    <div style='width: 100%;text-align: center'>
        <span style='font-size: 12px'>Gracias por su preferencia....</span>
    </div>
    <div style='width: 100%; '>
        $qrImage
    </div>
    
    
</div>
";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

}
