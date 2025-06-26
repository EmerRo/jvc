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

  public function __construct()
  {
    $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 0]);
    $this->conexion = (new Conexion())->getConexion();
    $this->venta = new Venta();
  }
  private function getImagePath($imageName)
  {
    // Si no hay nombre de imagen o está vacío, retornar null
    if (empty($imageName)) {
      return null;
    }
    // Definir las extensiones posibles
    $extensions = ['jpg', 'jpeg', 'png'];

    // Ruta base donde están las imágenes
    $basePath = 'public/img/productos/';

    // Si el nombre de la imagen ya incluye la extensión
    if (pathinfo($imageName, PATHINFO_EXTENSION)) {
      $fullPath = $basePath . $imageName;
      if (file_exists($fullPath)) {
        return URL::to($fullPath);
      }
    } else {
      // Verificar cada extensión posible
      foreach ($extensions as $ext) {
        $fullPath = $basePath . $imageName . '.' . $ext;
        if (file_exists($fullPath)) {
          return URL::to($fullPath);
        }
      }
    }

    // Si no se encuentra la imagen, retornar null en lugar de la imagen por defecto
    return null;
  }


  public function comprobanteCotizacion($coti)
  {
    // Modificar la consulta inicial para manejar tanto productos como repuestos
    $listaProd1 = $this->conexion->query("
           SELECT 
               pc.*,
               CASE 
                   WHEN pc.tipo_producto = 'producto' THEN p.nombre 
                   WHEN pc.tipo_producto = 'repuesto' THEN r.nombre
               END as nombre,
               CASE 
                   WHEN pc.tipo_producto = 'producto' THEN p.detalle 
                   WHEN pc.tipo_producto = 'repuesto' THEN r.detalle
               END as descripcion,
               CASE 
                   WHEN pc.tipo_producto = 'producto' THEN TRIM(p.codigo)
                   WHEN pc.tipo_producto = 'repuesto' THEN TRIM(r.codigo)
               END as codigo,
               CASE 
                   WHEN pc.tipo_producto = 'producto' THEN p.imagen
                   WHEN pc.tipo_producto = 'repuesto' THEN r.imagen
               END as imagen
           FROM productos_cotis pc 
           LEFT JOIN productos p ON p.id_producto = pc.id_producto AND pc.tipo_producto = 'producto'
           LEFT JOIN repuestos r ON r.id_repuesto = pc.id_producto AND pc.tipo_producto = 'repuesto'
           WHERE pc.id_coti = '$coti' 
           ORDER BY codigo ASC
       ");

    $sql = "select * from cotizaciones where cotizacion_id=" . $coti;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    //obtener el asusnto
    $asunto = 'No especificado';
    if (!empty($datoVenta['id_asunto'])) {
      $resultAsunto = $this->conexion->query("SELECT nombre FROM asuntos_coti WHERE id_asunto = " . $datoVenta['id_asunto']);
      if ($resultAsunto && $resultAsunto->num_rows > 0) {
        $asuntoData = $resultAsunto->fetch_assoc();
        $asunto = $asuntoData['nombre'];
      }
    }
    $sql_empresa = "SELECT e.* FROM empresas e 
       INNER JOIN cotizaciones c ON c.id_empresa = e.id_empresa 
       WHERE c.cotizacion_id = " . $coti;
    $datoEmpresa = $this->conexion->query($sql_empresa)->fetch_assoc();
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();

    if (!$datoEmpresa) {
      // Si no se encuentra la empresa, usar valores por defecto o mostrar error
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : (strlen($resultC['documento']) == 11 ? 'RUC' : '');

    error_log('SESSION: ' . print_r($_SESSION, true));

    $usuario_actual = [];
    $query = "SELECT 
                   u.nombres,
                   u.telefono,
                   r.nombre as rol
                 FROM usuarios u
                 INNER JOIN roles r ON r.rol_id = u.id_rol
                 WHERE u.usuario_id = 40";

    error_log('QUERY: ' . $query);
    $result = $this->conexion->query($query);
    if ($result && $result->num_rows > 0) {
      $usuario_actual = $result->fetch_assoc();
      error_log('USUARIO ENCONTRADO: ' . print_r($usuario_actual, true));
    } else {
      error_log('NO SE ENCONTRÓ USUARIO');
    }

    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);

    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    $sql_condiciones = "SELECT * FROM condiciones_cotizacion WHERE id_cotizacion = '$coti'";
    $result_condiciones = $this->conexion->query($sql_condiciones);

    if ($result_condiciones && $result_condiciones->num_rows > 0) {
      // Si encuentra condiciones específicas para esta cotización, las usa
      $condicion = $result_condiciones->fetch_assoc();
      $condicion_texto = $condicion['condiciones'];
    } else {
      // Si no encuentra condiciones específicas, usa las predeterminadas
      $condicion = $this->conexion->query("select * from condicion")->fetch_assoc();
      $condicion_texto = $condicion['nombre'];
    }

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti='$coti'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;
      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
       <tr style=''>
           <td style='padding: 3px; text-align: center;'>Cuota $tempNum</td>
           <td style='padding: 3px; text-align: center;'>$tempFecha</td>
           <td style='padding: 3px; text-align: center;'>S/ $tempMonto</td>
       </tr>
   ";
      }


      // IMPORTANTE: Aseguramos que la tabla de cuotas tenga page-break-inside: avoid
      $tabla_cuotas = '
   <div style="width: 100%; margin: 1px 0; page-break-inside: avoid;">
       <table style="width: 50%; margin: auto; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10.5px; border: 1px solid #CA3438;">
           <thead>
               <tr style="background-color: #CA3438; ">
                   <th style="padding: 3px; text-align: center; color:#fff;">CUOTA</th>
                   <th style="padding: 3px; text-align: center; color:#fff;">FECHA</th>
                   <th style="padding: 3px; text-align: center; color:#fff;">MONTO</th>
               </tr>
           </thead>
           <tbody>
               ' . $rowTempCuo . '
           </tbody>
       </table>
   </div>';
    }

    $formatter = new NumeroALetras;

    $qrImage = '';
    $hash_Doc = '';

    $tipo_documeto_venta = "COTIZACIÓN #: ";

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
    $contador = 0;
    $igv = 0;

    $rowHTML = '';
    $lastItemHTML = '';
    $lastItemIndex = mysqli_num_rows($listaProd1);

    // Verificar si algún producto tiene precio especial
    $hasSpecialPrices = false;
    $descuentoEspecialTotal = 0;

    // Primero verificamos si algún producto tiene precio especial
    foreach ($listaProd1 as $prod) {
      if (!empty($prod['precioEspecial']) && $prod['precioEspecial'] > 0 && $prod['precioEspecial'] != $prod['precio']) {
        $hasSpecialPrices = true;
        break;
      }
    }

    // Modificar el encabezado de la tabla según si hay precios especiales o no
    // Modificar el encabezado de la tabla para establecer anchos consistentes
    $tableHeader = "
    <tr style='border-collapse: collapse;'>
        <td style='width: 30px; font-size: 10px; font-family: Arial, Helvetica, sans-serif;text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>ITEM</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif; width: " . ($hasSpecialPrices ? "240px" : "300px") . "; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>DESCRIPCIÓN</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif;width: 30px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>CANT</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif;width: 80px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>COSTO<br>UNIT. SIN<br>I.G.V.</strong></td>";

    if ($hasSpecialPrices) {
      $tableHeader .= "
        <td style='font-family: Arial, Helvetica, sans-serif;width: 80px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>COSTO<br>C/DESC.</strong></td>";
    }

    $tableHeader .= "
        <td style='font-family: Arial, Helvetica, sans-serif;width: 80px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>COSTO<br>TOTAL. SIN<br>I.G.V.</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif;width: 80px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>IMAGEN<br>REFERENCIAL</strong></td>
    </tr>";

    foreach ($listaProd1 as $prod) {
      $contador++;
      if ($datoVenta['moneda'] == 2) {
        $prod['precio'] = $prod['precio'] / $datoVenta['cm_tc'];
      }

      $precio = $prod['precio'];
      $precioEspecial = !empty($prod['precioEspecial']) && $prod['precioEspecial'] > 0 ? $prod['precioEspecial'] : $precio;

      // Calcular el descuento por precio especial
      if ($precioEspecial < $precio) {
        $descuentoEspecialTotal += ($precio - $precioEspecial) * $prod['cantidad'];
      }

      // Usar el precio especial para el cálculo del importe si existe
      $importe = $precioEspecial * $prod['cantidad'];
      $total += $importe;
      $totalDescuentoEspecial = 0;
      $tempDescuento = 0;
      $importe -= $tempDescuento;
      $totalDescuento += $tempDescuento;

      $precioFormateado = number_format($precio, 2, '.', ',');
      $precioEspecialFormateado = number_format($precioEspecial, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      $prod['codigo'] = trim($prod['codigo']);
      $detalle = nl2br($prod['descripcion']);

      // Mejorar la detección de líneas contando tanto \n como <br>
      $numLines = substr_count($prod['descripcion'], "\n") +
        substr_count($prod['descripcion'], "\r") +
        substr_count($detalle, '<br>') + 1;

      // Si es el último ítem, guárdalo en una variable separada
      if ($contador == $lastItemIndex) {
        // Modificar la generación de filas para mantener los mismos anchos
        // En el último ítem:

        // Corrección para el estilo del último ítem
        if ($contador == $lastItemIndex) {
          // Asegurarnos de que la estructura y los estilos sean 100% idénticos a los demás ítems

          $lastItemHTML = "
<tr>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$contador</td>
    <td style='width: " . ($hasSpecialPrices ? "240px" : "300px") . "; font-size: 10px; text-align: left; border: 1px solid #CA3438;'><strong>{$prod['nombre']}</strong><br>{$detalle}</td>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>{$prod['cantidad']}</td>
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>S/ $precioFormateado</td>";

          if ($hasSpecialPrices) {
            $lastItemHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438; " .
              ($precioEspecial < $precio ? "background-color: #FFFDE7;" : "") . "'>" .
              ($precioEspecial < $precio ? "S/ $precioEspecialFormateado" : "") . "</td>";
          }

          $lastItemHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>S/ $importe</td>
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>";

          try {
            $imagePath = $this->getImagePath($prod['imagen']);
            if ($imagePath !== null) {
              $rowHeight = max(80, $numLines * 10);
              $lastItemHTML .= "<div class='image-container'>
             <img style='max-width: 100%; height: {$rowHeight}px; width: auto; object-fit: contain;' 
                  src='" . $imagePath . "'>
         </div>";
            } else {
              // si no hay imagen dejar vacío 
              $lastItemHTML .= "";
            }
          } catch (Exception $e) {
            $lastItemHTML .= "";
          }

          $lastItemHTML .= "</td>
     </tr>";
        }
      } else {
        // Para todos los ítems excepto el último, agregar al HTML principal
        // Modificar la generación de filas para mantener los mismos anchos
        // En el último ítem:
        // Asegurémonos de que la imagen tenga el mismo estilo en todos los ítems
        // Modificar la generación de filas para los ítems regulares también para que sean consistentes
        // Para las filas normales
        $rowHTML .= "
<tr>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$contador</td>
    <td style='width: " . ($hasSpecialPrices ? "240px" : "300px") . "; font-size: 10px; text-align: left; border: 1px solid #CA3438;'><strong>{$prod['nombre']}</strong><br>{$detalle}</td>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>{$prod['cantidad']}</td>
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>S/ $precioFormateado</td>";

        if ($hasSpecialPrices) {
          $rowHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438; " .
            ($precioEspecial < $precio ? "background-color: #FFFDE7;" : "") . "'>" .
            ($precioEspecial < $precio ? "S/ $precioEspecialFormateado" : "") . "</td>";
        }

        $rowHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>S/ $importe</td>
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>";

        try {
          $imagePath = $this->getImagePath($prod['imagen']);
          if ($imagePath !== null) {
            $rowHeight = max(80, $numLines * 10);

            $rowHTML .= "<div class='image-container'>
           <img style='max-width: 100%; height: {$rowHeight}px; width: auto; object-fit: contain;' 
                src='" . $imagePath . "'>
       </div>";
          } else {
            // si no hay imagen dejar vacío 
            $rowHTML .= "";
          }

        } catch (Exception $e) {
          $rowHTML .= "";
        }

        $rowHTML .= "</td></tr>";
      }
    }

    // Calcular el descuento general
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;

    $igv = $totalConDescuento / 1.18 * 0.18;
    $totalOpgravado = $totalConDescuento - $igv;
    $total = number_format($totalConDescuento, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $montoDescuento = number_format($montoDescuento, 2, '.', ',');
    $descuentoEspecialTotal = number_format($descuentoEspecialTotal, 2, '.', ',');

    $simbolfff22 = $datoVenta['moneda'] == 1 ? 'S/' : '$';

    // Tabla de resumen de precios (ahora integrada directamente en la tabla principal)
    // Asegurar que el resumen de precios tenga el ancho correcto en la columna de descripción
    // Modificar la generación del resumen de precios

    $resumenPrecios = "
     <tr class='price-summary'>
         <td colspan='" . ($hasSpecialPrices ? "5" : "4") . "' style='border: none;'></td>
         <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>Gravada:</td>
         <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>S/ $totalOpgravado</td>
       </tr>";


    // Mostrar el descuento por precio especial si existe
    if ($hasSpecialPrices && floatval(str_replace(',', '', $descuentoEspecialTotal)) > 0) {
      $resumenPrecios .= "
       <tr>
         <td colspan='" . ($hasSpecialPrices ? "5" : "4") . "' style='border: none;'></td>
         <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #FFFDE7;'>Descuento Total:</td>
         <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #FFFDE7;'>S/ $descuentoEspecialTotal</td>
       </tr>";
    }

    $resumenPrecios .= "
     <tr>
       <td colspan='" . ($hasSpecialPrices ? "5" : "4") . "' style='border: none;'></td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>IGV (18.00%):</td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>S/ $igv</td>
     </tr>";

    // Mostrar el descuento general si existe
    if (floatval(str_replace(',', '', $montoDescuento)) > 0) {
      $resumenPrecios .= "
     <tr>
       <td colspan='" . ($hasSpecialPrices ? "5" : "4") . "' style='border: none;'></td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>Descuento General  " . intval($descuentoGeneral) . "%:</td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>S/ $montoDescuento</td>
     </tr>";
    }

    $resumenPrecios .= "
     <tr>
       <td colspan='" . ($hasSpecialPrices ? "5" : "4") . "' style='border: none;'></td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #CA3438; color:white'><strong>Total:</strong></td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color:white'><strong>$simbolfff22 $total</strong></td>
     </tr>";

    // Agregar conversión a dólares si es necesario
    if ($datoVenta['moneda'] == 2) {
      if ($datoVenta['moneda'] == 2) {
        $totalDolar = number_format($totalConDescuento * $datoVenta['cm_tc'], 2, '.', ",");
      } else {
        $totalDolar = number_format($totalConDescuento / $datoVenta['cm_tc'], 2, '.', ",");
      }
      $simbolfff = $datoVenta['moneda'] == 2 ? 'S/' : '$';
      $resumenPrecios .= "
       <tr>
         <td colspan='" . ($hasSpecialPrices ? "5" : "4") . "' style='border: none;'></td>
         <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>Total a Pagar</td>
         <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>$simbolfff $totalDolar</td>
       </tr>";
    }

    $totalLetras = $formatter->toInvoice(number_format($totalConDescuento, 2, '.', ''), 2, $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES');

    // Modificado: Título de cotización con número en formato centrado
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
           <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin: left 65px;'>
             <span class='table-header' style='font-size: 14px; font-weight: bold;'>COTIZACIÓN DE J.V.C. S.A.C. – N° " . str_pad($datoVenta['numero'], 8, "0", STR_PAD_LEFT) . "/" . date('Y') . "</span>
           </div>
       </div>";

    // Configuración de mPDF
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

    // Configurar el header
    $headerHTML = "
     <div style='width: 100%; margin: 0; padding: 0;'>
     <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . URL::to('files/logo/' . $datoEmpresa['logo']) . "'>
     </div>";

    // Establecer el header y configurarlo para todas las páginas
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin: botoom 20px;">
     <span style="font-size: 11px; color: #000;">Lima, ' . $fecha_emision . '</span>
     </div>');
    $this->mpdf->SetTopMargin(40);
    $this->mpdf->showImageErrors = true;

    // Configurar propiedades adicionales para el manejo de páginas
    $this->mpdf->SetDisplayMode('fullpage');
    $this->mpdf->useSubstitutions = false;
    $this->mpdf->shrink_tables_to_fit = 1;
    $this->mpdf->keep_table_proportions = true;

    // Establecer el pie de página para todas las páginas
    $footerHTML = '
       <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
           <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
       </div>';
    $this->mpdf->SetHTMLFooter($footerHTML);

    // Condiciones formateadas
    $condicion = nl2br($condicion_texto);
    $monedaVisual = $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES';

    $condicionesFormateadas = "
       <div style='margin: 0; padding: 0;'>
       <p style='font-size: 11px; font-weight: bold; margin: 0; padding: 0;'>Condiciones:</p>
       <ul style='list-style-type: disc; font-size: 11px; margin: 0; padding-left: 20px; line-height: 1.2;'>
        $condicion
       </ul>
       </div>
       <div style='margin-top: 10px; padding: 0;'>
       <p style='font-size: 12px; margin: 0; padding: 0;'>Esperando vernos favorecidos con su preferencia, nos despedimos.</p>
       <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Atentamente,</p>
       
       <div style='width: 100%; clear: both; padding-top: 5px;'>
         <table style='width: 100%;'>
           <tr>
             <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
               <strong>" . ($usuario_actual['nombres'] ?? 'Usuario vendedor') . "</strong><br>
               <strong>" . ($usuario_actual['rol'] ?? 'ADMIN') . "</strong><br>
               Teléfono: 355-4701<br>
               Cel: " . ($usuario_actual['telefono'] ?? '993321920') . "
             </td>
             <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
               <strong>Eduardo Crisóstomo P.</strong><br>
               <strong>Jefe de Ventas y Servicios</strong><br>
               Teléfono: 355-4701<br>
               Cel: 996246564 - 943140418
             </td>
           </tr>
         </table>
       </div>
       </div>";

    // Agregar estilos CSS para controlar el comportamiento de paginación
    $this->mpdf->WriteHTML('
     <style>
         /* Evitar que el último producto y el resumen se separen */
         .last-item-with-summary {
             page-break-inside: avoid !important;
             page-break-before: auto;
         }
         
         /* Permitir que la tabla de productos se divida entre páginas */
         .products-table {
             page-break-inside: auto;
         }
         
         /* Evitar que cada producto individual se divida */
         .product-item {
             page-break-inside: avoid;
         }
     </style>
     ');

    // Estructura principal del HTML con mejor manejo de espacio
    $html = "
     <div style='width: 100%;'>
      
         " . $htmlCuadroHead . "
         
         <div style='width: 100%; max-width: 1000px; margin: 0 auto;'>
           <div>
             <table style='width:100%'>
               <tr>
                 <td style='font-size: 11px; text-align: left;'>Señores:</td>
               </tr>
               <tr>
                 <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$resultC['datos']}</td>
               </tr>
               <tr>
                 <td style='font-size: 11px; text-align: left;'>Dirección:</td>
               </tr>
               <tr>
                 <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . ($resultC['direccion'] ?? 'No especificada') . "</td>
               </tr>
               <tr>
                 <td style='font-size: 11px; text-align: left;'>Asunto:
               </tr>
               <tr>
                 <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>" . $asunto . "</td>
               </tr>
             </table>
           </div>
           
           <div style='padding-right: 15px;'>
             <div>
               <table style='width:100%'>
                 <tr>
                   <td style='font-size: 11px;'>Por medio del presente documento nos dirigimos a ustedes para saludarlos cordialmente y asimismo hacerles llegar nuestra siguiente cotización:</td>
                 </tr>
               </table>
             </div>
             
        <!-- Tabla única para todos los productos y el resumen -->
        <table style='width:100%; border-collapse: collapse; margin-right:35px; table-layout: fixed;' class='products-table'>
       <colgroup>
    <col style='width: 30px'>                                <!-- ITEM -->
    <col style='width: " . ($hasSpecialPrices ? "240px" : "300px") . "'> <!-- DESCRIPCIÓN -->
    <col style='width: 30px'>                               <!-- CANT -->
    <col style='width: 80px'>                               <!-- COSTO UNIT. SIN I.G.V. -->
    " . ($hasSpecialPrices ? "<col style='width: 80px'>" : "") . " <!-- COSTO C/DESC. (condicional) -->
    <col style='width: 80px'>                               <!-- COSTO TOTAL. SIN I.G.V. -->
    <col style='width: 80px'>                               <!-- IMAGEN REFERENCIAL -->
</colgroup>
         <thead>
             $tableHeader
         </thead>
         <tbody>
             $rowHTML
             $lastItemHTML
             <tr>
                 <td colspan='3' style='border: none;'></td>
                 " . ($hasSpecialPrices ? "<td style='border: none;'></td>" : "") . "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>Gravada:</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>S/ $totalOpgravado</td>
             </tr>";

    // Mostrar el descuento por precio especial si existe
    if ($hasSpecialPrices && floatval(str_replace(',', '', $descuentoEspecialTotal)) > 0) {
      $html .= "
             <tr>
                 <td colspan='3' style='border: none;'></td>
                 " . ($hasSpecialPrices ? "<td style='border: none;'></td>" : "") . "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #FFFDE7;'>Descuento Total:</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #FFFDE7;'>S/ $descuentoEspecialTotal</td>
             </tr>";
    }

    $html .= "
             <tr>
                 <td colspan='3' style='border: none;'></td>
                 " . ($hasSpecialPrices ? "<td style='border: none;'></td>" : "") . "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>IGV (18.00%):</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>S/ $igv</td>
             </tr>";

    // Mostrar el descuento general si existe
    if (floatval(str_replace(',', '', $montoDescuento)) > 0) {
      $html .= "
             <tr>
                 <td colspan='3' style='border: none;'></td>
                 " . ($hasSpecialPrices ? "<td style='border: none;'></td>" : "") . "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>Descuento General " . intval($descuentoGeneral) . "%:</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>S/ $montoDescuento</td>
             </tr>";
    }

    $html .= "
             <tr>
                 <td colspan='3' style='border: none;'></td>
                 " . ($hasSpecialPrices ? "<td style='border: none;'></td>" : "") . "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #CA3438; color:white'><strong>Total:</strong></td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color:white'><strong>$simbolfff22 $total</strong></td>
             </tr>";

    // Agregar conversión a dólares si es necesario
    if ($datoVenta['moneda'] == 2) {
      if ($datoVenta['moneda'] == 2) {
        $totalDolar = number_format($totalConDescuento * $datoVenta['cm_tc'], 2, '.', ",");
      } else {
        $totalDolar = number_format($totalConDescuento / $datoVenta['cm_tc'], 2, '.', ",");
      }
      $simbolfff = $datoVenta['moneda'] == 2 ? 'S/' : '$';
      $html .= "
             <tr>
                 <td colspan='3' style='border: none;'></td>
                 " . ($hasSpecialPrices ? "<td style='border: none;'></td>" : "") . "
                 <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>Total a Pagar</td>
                 <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>$simbolfff $totalDolar</td>
             </tr>";
    }

    $html .= "
         </tbody>
         </table>
         
         <!-- IMPORTANTE: Aseguramos que la tabla de cuotas tenga suficiente espacio antes del pie de página -->
         <div style='width: 100%; margin-top: 10px; margin-bottom: 20px; page-break-before: auto;'>
           $tabla_cuotas
         </div>
         
         <!-- Condiciones con mejor manejo de espacio -->
         <div style='page-break-inside: avoid; margin-bottom: 30px;'>
           $condicionesFormateadas
         </div>
       </div>
     </div>
   </div>
 </div>
 ";

    // Escribir el HTML al documento
    $this->mpdf->WriteHTML($html);

    // Generar el PDF
    $this->mpdf->Output("COTIZACION JVC-{$datoVenta['numero']}.pdf", 'I');
  }

  public function reporteVentaPorProducto()
  {
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
          <td>{$row['descripcion']}</td>
          <td>{$row['nom_pago']}</td>
          <td>{$row['fecha_emision']}</td>
          <td>{$row['nombre_documento']}</td>
          <td>{$row['venta_sn']}</td>
          <td>{$row['cantidad']}</td>
          <td>{$row['precio']}</td>
            </tr>
          ";
    }

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE PRODUCTOS POR VENTA</h2>
              
        </div> 
        
        <div style='width: 100%; margin-top:40px;'>
            <table border='1' style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                  
                    <th style=''>Producto</th>
                    <th style=''>Pago</th>
                    <th style=''>Fecha</th>
                    <th style=''>Doc.</th>
                    <th style=''>S-N</th>
                    <th style=''>Cantidad</th>
                    <th style=''>Precio</th>
                  
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }


  public function reporteCompra($id)
  {
    // Consulta SQL para obtener datos de compra
    $sql = "SELECT c.fecha_emision, c.direccion, CONCAT(ds.abreviatura, ' | ', c.serie, ' - ', c.numero) AS factura, 
          p.razon_social, c.total, tp.nombre as tipoPago, c.dias_pagos, c.id_empresa, p.direccion as direccion_proveedor,
          YEAR(CURRENT_DATE()) as anio_actual
          FROM compras c
          LEFT JOIN documentos_sunat ds ON c.id_tido = ds.id_tido
          LEFT JOIN proveedores p ON p.proveedor_id = c.id_proveedor
          LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = c.id_tipo_pago 
          WHERE c.id_compra = $id";
    $result = $this->conexion->query($sql);
    $compra = $result->fetch_assoc();

    $esCredito = ($compra['tipoPago'] == 'Credito' || $compra['tipoPago'] == 'CREDITO');
    // // Obtener productos de la compra
    // $sqlProductos = "SELECT pc.id_producto_venta, pc.id_producto, pc.cantidad, pc.precio, p.nombre, p.descripcion
    //               FROM productos_compras pc
    //               LEFT JOIN productos p ON pc.id_producto = p.id_producto
    //               WHERE pc.id_compra = $id";

    // Obtener productos Y repuestos de la compra
    $sqlProductos = "
    (SELECT pc.id_producto_venta as id_item, pc.cantidad, pc.precio, p.nombre, p.descripcion, 'Producto' as tipo
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
           WHERE u.usuario_id = " . $_SESSION['usuario_id']; // Asumiendo que tienes el ID de usuario en la sesión

    $result = $this->conexion->query($query);
    if ($result && $result->num_rows > 0) {
      $usuario_actual = $result->fetch_assoc();
    }
    // Obtener observaciones de la compra
    $sqlObservaciones = "SELECT oc.detalle 
                      FROM observaciones_compra oc
                      WHERE oc.id_compra = $id";
    $resultObservaciones = $this->conexion->query($sqlObservaciones);
    $observaciones = '';

    if ($resultObservaciones && $resultObservaciones->num_rows > 0) {
      // Si encuentra observaciones específicas para esta compra, las usa
      $observacion = $resultObservaciones->fetch_assoc();
      $observaciones = $observacion['detalle'];
    } else {
      // Si no encuentra observaciones específicas, usa las predeterminadas
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

      // Array de meses para formatear la fecha
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

        // Formatear la fecha directamente en el controlador
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

      // Generar la tabla completa SOLO si es crédito
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

    // Configuración de mPDF similar a la de cotizaciones
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

    //Establecer el titulo del documento PDF 
    $numeroOrdenFormateado = sprintf("%08d", $id);
    $this->mpdf->SetTitle("OC° {$numeroOrdenFormateado}");

    // Configurar el header con el logo de la empresa
    $headerHTML = "
   <div style='width: 100%; margin: 0; padding: 0;'>
   <img style='width: auto; height: auto; display: block; margin-left: auto;' src='" . URL::to('files/logo/' . $empresa['logo']) . "'>
   </div>";

    // Establecer el header y configurarlo para todas las páginas
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin-bottom: 20px;">
   <span style="font-size: 11px; color: #000;">Lima, ' . Tools::formatoFechaVisual($compra['fecha_emision']) . '</span>
   </div>');
    $this->mpdf->SetTopMargin(40);
    $this->mpdf->showImageErrors = true;

    // Configurar propiedades adicionales para el manejo de páginas
    $this->mpdf->SetDisplayMode('fullpage');
    $this->mpdf->useSubstitutions = false;
    $this->mpdf->shrink_tables_to_fit = 1;
    $this->mpdf->keep_table_proportions = true;

    // Establecer el pie de página para todas las páginas
    $footerHTML = '
     <div style="position: absolute; bottom: 0; left: 0; right: 0; margin: 0; padding: 0; height: 145px;">
         <img src="public/assets/img/pie de pagina.jpg" style="width: 100%; display: block; margin: 0; padding: 0;">
     </div>';
    $this->mpdf->SetHTMLFooter($footerHTML);

    // Título de orden de compra con formato solicitado
    $numeroOrden = sprintf("%08d", $id) . "/" . $compra['anio_actual'];
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
         <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin-left: 65px;'>
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

    // Contenido HTML del reporte con el formato solicitado
    // Modificar la sección de la tabla de productos en el HTML
    $html = "
<div style='width: 100%;'>
  " . $htmlCuadroHead . "
  
  <div style='width: 100%; max-width: 1000px; margin: 0 auto;'>
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
          <td style='font-size: 11px; font-weight: bold; padding-left: 40px;'>{$compra['direccion_proveedor']}</td>
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
      
<!-- Tabla de productos centrada -->
<div style='margin-top: 15px; text-align: center;'>
    <table style='width:80%; border-collapse: collapse; margin: 0 auto;' cellpadding='0' cellspacing='0'>
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
        <table style='width:80%; border-collapse: collapse; margin: 0 auto;' cellpadding='0' cellspacing='0'>
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

      <!-- Sección de observaciones -->
      {$observacionesFormateadas}

      <!-- Sección de contactos -->
      {$contactosHtml}
    </div>
    
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
    $sql = "SELECT c.fecha_emision,c.direccion,CONCAT( ds.abreviatura , ' | ' , c.serie , ' - ', c.numero)AS factura,p.razon_social,
    c.total,tp.nombre as tipoPago,c.dias_pagos,c.id_empresa
     FROM compras c
     LEFT JOIN documentos_sunat ds ON c.id_tido = ds.id_tido
	    LEFT JOIN proveedores p ON p.proveedor_id = c.id_proveedor
	    LEFT JOIN tipo_pago tp ON tp.tipo_pago_id = c.id_tipo_pago";
    $result = $this->conexion->query($sql);


    $rowHmtl = "";
    $idEmpresa = "";
    foreach ($result as $fila) {
      $total = number_format($fila['total'], 2, ".", "");
      $idEmpresa = $fila['id_empresa'];
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$fila['direccion']}</td>
      <td style='font-size: 9px'>{$fila['factura']}</td>
      <td style='font-size: 9px'>{$fila['razon_social']}</td>
      <td style='font-size: 9px'>{$fila['tipoPago']}</td>
      <td style='font-size: 9px'>{$fila['dias_pagos']}</td>
      <td style='font-size: 9px'>{$total}</td>
  </tr>";
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $empresa = $this->conexion->query("SELECT * from empresas
    where id_empresa = '{$idEmpresa}'")->fetch_assoc();




    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR COMPRAS</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
            <tr>
            <td>EMPRESA:</td>
            <td>{$empresa["ruc"]} | {$empresa['razon_social']}</td>
        </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                  
                    <th style='width: 10%;'>Fecha</th>
                    <th style='width: auto;'>Dirección</th>
                    <th style='width: auto;'>Factura</th>
                    <th style='width: auto;'>Razon Social</th>
                    <th style='width: 10%;'>Tipo Pago</th>
                    <th style='width: 10%;'>Días Pagos</th>
                    <th style='width: 10%;'>Total</th>
                  
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
  }

  public function reporteCliente($id)
  {

    $sql = "SELECT *,metodo_pago.nombre AS metodoPago,tipo_pago.nombre AS tipoPago FROM VENTAS 
    LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=ventas.medoto_pago_id
    LEFT JOIN tipo_pago ON tipo_pago.tipo_pago_id=ventas.id_tipo_pago WHERE id_cliente = $id";
    $result = $this->conexion->query($sql);

    $rowHmtl = "";
    foreach ($result as $fila) {
      $total = number_format($fila['total'], 2, ".", "");
      $rowHmtl .= "<tr>
      <td style='font-size: 9px'>{$fila['id_venta']}</td>
      <td style='font-size: 9px'>{$fila['fecha_emision']}</td>
      <td style='font-size: 9px'>{$fila['direccion']}</td>
      <td style='font-size: 9px'>{$fila['tipoPago']}</td>
      <td style='font-size: 9px'>{$fila['dias_pagos']}</td>
      <td style='font-size: 9px'>{$total}</td>
      <td style='font-size: 9px'>{$fila['metodoPago']}</td>
  </tr>";
    }
    $this->mpdf->WriteHTML("
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    ", \Mpdf\HTMLParserMode::HEADER_CSS);


    $sql = "SELECT * FROM clientes WHERE id_cliente = $id";
    $result = $this->conexion->query($sql)->fetch_assoc();

    $html = "
     
    <div style='width: 100%; '>
        <div style='width: 100%; text-align: center;'>
                <h2 style=''>REPORTE DE VENTAS POR CLIENTE</h2>
              
        </div>
        <div style='width: 100%;'>
            <table style='width: 100%;'>
                <tr>
                    <td>Documento:</td>
                    <td>{$result['documento']}</td>
                </tr>
                <tr>
                    <td>Cliente:</td>
                    <td>{$result['datos']}</td>
                </tr>
                <tr>
                    <td>Dirección:</td>
                    <td>{$result['direccion']}</td>
                </tr>
                <tr>
                    <td>Dirección:</td>
                    <td>{$result['telefono']}</td>
                </tr>
            </table>
        </div>
        
        <div style='width: 100%; margin-top:40px;'>
            <table style='width: 100%; text-align: center;' >
                <thead>
                <tr>
                    <th style='width: 10%;'>Codigo</th>
                    <th style='width: 10%;'>Fecha</th>
                    <th style='width: auto;'>Dirección</th>
                    <th style='width: 10%;'>Tipo Pago</th>
                    <th style='width: 10%;'>Dias Pagos</th>
                    <th style='width: 10%;'>Total</th>
                    <th style='width:auto;'>Metodo Pago</th>
              
                </tr>
                </thead>
               <tbody>
                $rowHmtl
                </tbody>
            </table>
        </div>
        
    </div>
    ";
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
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

    $this->mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 15, 5, 150, 120);
    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 195, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Central Telefónica: </strong> {$datoEmpresa['telefono']}</span>", 15, 27, 210, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Email: </strong> info@grupoacosta.com.pe | Web: www.vallesport.pe</span>", 15, 32, 210, 130);
    $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 15, 37, 120, 130);



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



    $html = "<div style='width: 1000%;padding-top: 110px; overflow: hidden;clear: both;'>
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
  public function guiaRemision($guia, $nombreXML)
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

    try {
      // Check if user is logged in
      $isLoggedIn = isset($_SESSION['id_empresa']);

      // Obtener datos de la guía
      $sql = "SELECT gr.*, mg.nombre as motivo_traslado_nombre 
                  FROM guia_remision gr
                  LEFT JOIN motivos_guia mg ON gr.motivo_traslado = mg.id
                  WHERE gr.id_guia_remision = " . $guia;
      $datosGuia = $this->conexion->query($sql)->fetch_assoc();


      if (!$datosGuia) {
        throw new Exception("No se encontró la guía de remisión");
      }
      $S_N = $datosGuia['serie'] . '-' . Tools::numeroParaDocumento($datosGuia['numero'], 6);

      // Obtener datos de la empresa
      if ($isLoggedIn) {
        $datoEmpresa = $this->conexion->query("SELECT * FROM empresas WHERE id_empresa = " . $_SESSION['id_empresa'])->fetch_assoc();
      } else {
        $datoEmpresa = $this->conexion->query("SELECT * FROM empresas WHERE id_empresa = " . $datosGuia['id_empresa'])->fetch_assoc();
      }

      // Inicializar variables del cliente
      $nombreCliente = '';
      $numDoc = '';
      $direccionCliente = $datosGuia['dir_llegada'];

      // Obtener datos del cliente según el tipo de guía
      if ($datosGuia['id_venta']) {
        // Para guías normales (asociadas a venta)
        $sql = "SELECT v.*, c.* 
                      FROM ventas v 
                      JOIN clientes c ON v.id_cliente = c.id_cliente 
                      WHERE v.id_venta = " . $datosGuia['id_venta'];
        $datoVenta = $this->conexion->query($sql)->fetch_assoc();

        if ($datoVenta) {
          $nombreCliente = $datoVenta['datos'];
          $numDoc = strlen($datoVenta["documento"]) > 7 ? $datoVenta["documento"] : '';
        }
      } else {
        // Para guías manuales
        $nombreCliente = $datosGuia['destinatario_nombre'];
        $numDoc = $datosGuia['destinatario_documento'];
      }
      // Obtener datos SUNAT para QR y Hash
      $sql_sunat = "SELECT * FROM guia_sunat WHERE id_guia = " . $guia;
      $qrImage = '';
      $hash_Doc = '';
      $hashValue = 'N/A'; // Valor por defecto

      if ($rowSunat = $this->conexion->query($sql_sunat)->fetch_assoc()) {
        $hash_Doc = "HASH: " . $rowSunat['hash'] . "<br>";
        $hashValue = $rowSunat['hash'];

        try {
          $qrCode = new QrCode($rowSunat["qr_data"]);
          $qrCode->setSize(250);
          $image = $qrCode->writeString();
          $imageData = base64_encode($image);
          $qrImage = '<img style="width: 90px; height: 90px;" src="data:image/png;base64,' . $imageData . '">';
        } catch (Exception $e) {
          $qrImage = ''; // Si hay error, no mostrar QR
        }
      } else {
        // Si no hay datos SUNAT, crear QR básico con datos de la guía
        $qr_data = $datoEmpresa['ruc'] . '|09|' . $S_N . '|0.00|0.00|' .
          $datosGuia['fecha_emision'] . '|6|' . ($numDoc ?: '00000000');

        try {
          $qrCode = new QrCode($qr_data);
          $qrCode->setSize(250);
          $image = $qrCode->writeString();
          $imageData = base64_encode($image);
          $qrImage = '<img style="width: 60px; height: 60px;" src="data:image/png;base64,' . $imageData . '">';
        } catch (Exception $e) {
          $qrImage = '';
        }
      }
      // Obtener datos del usuario que creó la guía
      $usuario_creador = 'Emer Zapata'; // Valor por defecto
      $codigo_usuario = 'N/A';

      // Intentar obtener el usuario de la sesión actual
      if (isset($_SESSION['usuario_id'])) {
        $sql_usuario = "SELECT nombres, apellidos, codigo FROM usuarios WHERE usuario_id = " . $_SESSION['usuario_id'];
        $result_usuario = $this->conexion->query($sql_usuario);

        if ($result_usuario && $result_usuario->num_rows > 0) {
          $usuario_data = $result_usuario->fetch_assoc();
          $usuario_creador = $usuario_data['nombres'] . ' ' . ($usuario_data['apellidos'] ?: '');
          $codigo_usuario = $usuario_data['codigo'] ?: 'N/A';
        }
      }

      // Si no hay usuario en sesión, usar el valor por defecto
      if (empty(trim($usuario_creador)) || $usuario_creador == ' ') {
        $usuario_creador = 'Emer Zapata';
      }

      $dominio = DOMINIO . 'buscador';

      // Primero, vamos a verificar la estructura de la tabla 'productos'
      $checkProductosTable = "DESCRIBE productos";
      $result = $this->conexion->query($checkProductosTable);

      if ($result === false) {
        throw new Exception("Error al verificar la estructura de la tabla 'productos': " . $this->conexion->error);
      }

      $idColumnName = 'id'; // Asumimos que es 'id' por defecto
      while ($row = $result->fetch_assoc()) {
        if (strpos(strtolower($row['Field']), 'id') !== false) {
          $idColumnName = $row['Field'];
          break;
        }
      }

      $query = "SELECT gd.*, p.nombre,  p.codigo
                FROM guia_detalles gd
                LEFT JOIN productos p ON gd.id_producto = p.$idColumnName
                WHERE gd.id_guia = ?";

      $stmt = $this->conexion->prepare($query);

      if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
      }

      $stmt->bind_param("i", $guia);

      if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
      }

      $listaProductos = $stmt->get_result();

      if ($listaProductos === false) {
        throw new Exception("Error al obtener los resultados: " . $stmt->error);
      }

      // Formatear fecha
      $fechaEmision = Tools::formatoFechaNumero($datosGuia['fecha_emision']);

      // Generar número de documento
      $tipoDocNom = 'GUÍA DE REMISIÓN REMITENTE';

      // Generar cabecera del PDF
      $htmlCuadroHead = "<div style='width: 38%;text-align: center; background-color: #ffffff; float: right; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 12px;'>
              <div style='width: 100%; height: 100px;border-radius:10px; border: 1px solid #1e1e1e; ' >
                  <div style='margin-top:10px'></div>
                  <span> <strong> RUC: {$datoEmpresa['ruc']} </strong></span><br>

             <div style='margin-top: 10px '></div>
                  <div style='background-color: #CA3438 ; color:white; margin:0 ; padding: 20px;width: 100%;'>
                  <span > <strong> $tipoDocNom ELECTRONICA </strong></span>
                  </div>
                  
                  <br>
                
             <span style='display: block; text-align: center; font-size: 14px'>Nro. $S_N</span>
                 <div style='margin-top:10px'></div>
                </div>
              </div>
          </div>";

      // logo
      $this->mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 35, 8, 150, 120);
      $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 196, 130);
      // Información de la empresa
      $this->mpdf->WriteFixedPosHTML("<span style='font-family: Calibri, Helvetica Neue, sans-serif; font-size: 14px;margin: 1pt 2pt 3pt;'><strong>COMERCIAL & INDUSTRIAL J. V. C. S.A.C.
 </strong></span>", 25, 30, 210, 130);
      // dirección, teléfono y email
      $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'><span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 35, 36, 210, 130);

      $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'>Telf: {$datoEmpresa['telefono']} -Email:{$datoEmpresa['email']} </span>", 25, 39, 210, 130);

      $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'> Web: https://industriajvcsac.com</span>", 40, 43, 210, 130);


      // Generar filas de productos
      $rowHTML = '';
      $conradorRow = 1;
      while ($itemProd = $listaProductos->fetch_assoc()) {
        $rowHTML .= "
              <tr >
                    <td style='width: 5%; padding: 10px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                      $conradorRow 
                    </td>
                    <td style='width: 10%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      {$itemProd['codigo']} 
                    </td>
                    <td style='width: 71%; padding: 10px;  border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      <strong >{$itemProd['detalles']} </strong>
                    </td>
                    <td style='width: 8%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      {$itemProd['unidad']} 
                    </td>
                    <td style='width: 6%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      {$itemProd['cantidad']} 
                    </td>
                  </tr>";
        $conradorRow++;
      }

      // Generar HTML principal
      $html = "
      
      <div style='width: 100%;padding-top: 150px; overflow: hidden;clear: both;'>
              <!-- Sección Destinatario -->
            
      <div style='width: 100%; padding: 10px; border: 0.5px solid black; border-radius: 10px; margin-bottom: 20px; overflow: hidden;'>
  <table style='width: 100%; border-collapse: collapse; margin: -10px;'>
    <tr>
        <td style='width: 16.66%; padding: 8px; text-align: center; border-right: 0.5px solid black; vertical-align: top;'>
            <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Fecha de Emisión:</strong>
            <span style='font-size: 10px;'>{$fechaEmision}</span>
        </td>

        <td style='width: 16.66%; padding: 8px; text-align: center; border-right: 0.5px solid black; vertical-align: top;'>
            <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Fecha de Traslado:</strong>
            <span style='font-size: 10px;'>{$fechaEmision}</span>
        </td>

       <td style='width: 16.66%; padding: 8px; text-align: center; border-right: 0.5px solid black; vertical-align: top;'>
    <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Docs. Referencia: <br></strong>
    <span style='font-size: 10px;'>" . (isset($datosGuia['doc_referencia']) && !empty($datosGuia['doc_referencia']) ? $datosGuia['doc_referencia'] : '-') . "</span>
</td>
        <td style='width: 17.66%; padding: 8px; text-align: center; border-right: 0.5px solid black; vertical-align: top;'>
            <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Motivo de traslado:</strong>
            <span style='font-size: 10px;'>{$datosGuia['motivo_traslado_nombre']}</span>
        </td>
        <td style='width: 16.66%; padding: 8px; text-align: center; border-right: 0.5px solid black; vertical-align: top;'>
            <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Mod. Transporte:</strong>
            <span style='font-size: 10px;'>Transporte privado</span>
        </td>
        <td style='width: 16.66%; padding: 8px; text-align: center; vertical-align: top;'>
            <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Orden de Compra</strong>
            <strong style='font-size: 10px; display: block; margin-bottom: 4px;'> Nro:</strong> <br/>
            <span style='font-size: 10px;'>COT 128323 JVC</span>
        </td>
    </tr>
</table>
</div>

<div style='width: 100%; padding: 0; border-top: 0.5px solid #CA3438; border-bottom: 0.5px solid #000000; border-left: 0.5px solid #000000; border-right: 0.5px solid #000000; border-radius: 10px; margin-bottom: 20px; overflow: hidden;background-color: #CA3438;'>
<table style='width: 100%; border-collapse: collapse; margin:0; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
 <tr >
            <td style='width: 50%; padding: 10px; text-align: center; '>
                <strong style=' color: #fff;'>DIRECCIÓN DE PARTIDA</strong>
            </td>
            <td style='width: 50%; padding: 10px; text-align: center; '>
                <strong style='color: #fff;'>DIRECCIÓN DE LLEGADA</strong>
            </td>
        </tr>
</table>
<div style='width: 100%; padding: 0; overflow: hidden;background-color: #ffffff;'>
    <table style='width: 100%; border-collapse: collapse; margin:0;  font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
       
        <tr>
            <td style='width: 50%; padding: 8px; text-align: center;'>
                <span >{$datoEmpresa['direccion']}</span>
   
               
            </td>
            <td style='width: 50%; padding: 8px; text-align: center;'>
                <span >{$datosGuia['dir_llegada']}</span>
       
              
            </td>
        </tr>
    </table>
</div>
</div>

<div style='width: 100%; padding: 0; border-top: 0.5px solid #CA3438; border-bottom: 0.5px solid #000000; border-left: 0.5px solid #000000; border-right: 0.5px solid #000000; border-radius: 10px; margin-bottom: 20px; overflow: hidden; background-color: #CA3438; '>
<table style='width: 100%; border-collapse: collapse; margin:0; color: #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>

  <tr>
            <td style='width: 33.33%; padding: 10px; text-align: center;'>
                <strong >DESTINATARIO </strong>
            </td>
            <td style='width: 33.33%; padding: 10px; text-align: center;'>
                <strong >UNIDAD DE TRANSPORTE</strong>
            </td>
            <td style='width: 33.33%; padding: 10px; text-align: center;'>
                <strong>DATOS CONDUCTORES</strong>
            </td>
        </tr>
</table>
<div style='width: 100%; padding: 0; overflow: hidden;background-color: #ffffff;'>

    <table style='width: 100%; border-collapse: collapse; margin:0; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
      
        <tr>
            <td style='width: 33.33%; padding: 8px; text-align: center; '>
                <span>{$nombreCliente}</span> <br/>
                <span>Num.Doc.:{$numDoc}</span>
   
               
            </td>
            <td style='width: 33.33%; padding: 8px; text-align: center; '>
                <span>{$datosGuia['vehiculo']}</span>
            </td>
            <td style='width: 33.33%; padding: 8px; text-align: center;'>
                <span>{$datosGuia['chofer_datos']}</span>
            </td>
        </tr>
    </table>
</div>
</div>

<!-- Texto de remisión -->
<div style='width: 100%;  margin-bottom: 0; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; font-weight: bold;'>
    REMITIMOS A UD.(ES) EN BUENAS CONDICIONES LO SIGUIENTE:
</div>
              <!-- Sección Productos -->
            <div style='width: 100%; padding: 0; border: 0.5px solid black; border-radius: 10px; margin-bottom: 30px; overflow: hidden; background-color: #CA3438; '>
<table style='width: 100%; border-collapse: collapse; margin:0; color:#fff;'>

               <tr >
                <td style='width: 5%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>ITEM </strong>
            </td>
                <td style='width: 10%; padding: 8px; text-align: center;font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;  '>
                <strong style=' color: #ffffff;'>CODIGO </strong>
            </td>
                <td style='width: 71%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>DESCRIPCION </strong>
            </td>
                <td style='width: 8%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>UNID/MED </strong>
            </td>
                <td style='width: 6%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>CANT </strong>
            </td>
                         
                      </tr></table>
                      
                      <div style='width: 100%; padding: 0; overflow: hidden;background-color:#fff;'>
                      <table style='width: 100%; border-collapse: collapse; margin:0;'>
       
                      {$rowHTML}
                  </table>
              </div>
              <div style='width: 100%; padding: 0; overflow: hidden;background-color:#fff;'>
            <div style='width: 100%; padding: 5px; border-top: 0.5px solid black;font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;  '>
            <strong >Peso Bruto (KGM):</strong> <span> {$datosGuia['peso']}</span>
            </div>
            <div style='width: 100%; padding: 5px; border-top: 0.5px solid black; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;  '>
                     <strong>Numero de Bulltos o Pallets:</strong> <span> {$datosGuia['nro_bultos']}</span>

            </div>
           

              </div>
              </div>
                                         <!-- Sección Observaciones -->
           <div style='width: 100%; margin-top: 20px; overflow: hidden;'>
                  <div style='float: left; width: 100px; text-align: center; vertical-align: top; padding: 0;'>
                      {$qrImage}
                  </div>
                  <div style='float: left; margin-left: 5px; vertical-align: top; padding: 0;'>
                      <div style='font-family: Arial; font-size: 10px; line-height: 1.4; padding-top: 5px;'>
                          <div style='margin-bottom: 3px;'><strong>Consulte su documento electrónico en:</strong> {$dominio}<br></div>
                         <div style='margin-bottom: 3px;'><strong>HASH:</strong> {$hashValue}</div>
                          <div style='margin-bottom: 3px;'><strong>USUARIO:</strong> {$usuario_creador} (cod: {$codigo_usuario})</div>
                          <div style='margin-bottom: 0;'>
                              <span>Representación Impresa de la Guía de Remisión</span>
                              <br>
                             
                          </div>

                      <div style=' margin-top: 10px;'>  <span style='font-size: 10px; margin-left: 80px;'> 
                                  <strong>Observacion: </strong>" . (isset($datosGuia['observaciones']) && $datosGuia['observaciones'] ? $datosGuia['observaciones'] : 'No hay observaciones.') . "
                              </span>
                      </div>
                      </div>
                  </div>
              </div>
          </div>";


      // Escribir HTML y generar PDF
      $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);



      /*$this->mpdf->WriteHTML($htmlDOM,\Mpdf\HTMLParserMode::HTML_BODY);*/
      $dist = 'I'; // Initialize $dist variable
      if ($dist == 'I') {
        $this->mpdf->Output((is_string($nombreXML) ? $nombreXML : '') . ".pdf", $dist);
      } elseif ($dist == 'F') {
        $this->mpdf->Output(base64_decode((is_string($nombreXML) ? $nombreXML : '')), $dist);
      }

    } catch (Exception $e) {
      // Manejar cualquier error que pueda ocurrir
      error_log("Error en guiaRemision: " . $e->getMessage());
      echo "Error al generar el PDF: " . $e->getMessage();
    }
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



    $listaProd1 = $this->conexion->query("SELECT productos_ventas.*,p.descripcion,p.codigo FROM productos_ventas join productos p on p.id_producto = productos_ventas.id_producto WHERE id_venta=" . $venta);
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

    $listaProd1 = $this->conexion->query("SELECT productos_ventas.*,p.detalle as descripcion, p.imagen,p.nombre,p.codigo FROM productos_ventas 
      join productos p on p.id_producto = productos_ventas.id_producto WHERE id_venta=" . $venta);
    $listaProd2 = $this->conexion->query("SELECT * FROM ventas_servicios WHERE id_venta=" . $venta);
    $ventaSunat = $this->conexion->query("SELECT * FROM ventas_sunat WHERE id_venta=" . $venta)->fetch_assoc();

    $sql = "SELECT * FROM guia_remision where id_venta = $venta";
    if ($rowGuia = $this->conexion->query($sql)->fetch_assoc()) {
      $guiaRealionada = $rowGuia["serie"] . '-' . Tools::numeroParaDocumento($rowGuia["numero"], 6);
    }

    $sql = "select * from ventas where id_venta=" . $venta;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();
    $datoEmpresa = $this->conexion->query("select * from empresas where id_empresa=" . $datoVenta['id_empresa'])->fetch_assoc();

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
      $afectIgv = "Gravado";

      $rowHTML = $rowHTML . "
      <tr>
        <td style='width: 5%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; border-left: 1px solid #CA3438; padding-top: 6px; padding-bottom: 6px;'>{$contador}</td>
        <td style='width: 10%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['codigo']}</td>
        <td style='width: 6%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>{$prod['cantidad']}</td>
        <td style='width: 8%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: center; padding-top: 6px; padding-bottom: 6px;'>UNIDAD</td>
        <td style='width: 40%; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10px; text-align: left; padding-top: 6px; padding-bottom: 6px;'><strong>{$prod['nombre']}</strong></td>
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
    $this->mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 35, 8, 150, 120);

    $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 200, 130);
    $this->mpdf->WriteFixedPosHTML("<span style='font-size: 13px;margin: 1pt 2pt 3pt;'><strong>COMERCIAL & INDUSTRIAL J. V. C. S.A.C.
   </strong></span>", 25, 30, 210, 130);

    $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$datoVenta['sucursal']}' AND empresa_id=" . $datoVenta['id_empresa'])->fetch_assoc();
    if ($datoVenta['sucursal'] == '1') {
      $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'>Dirección:<span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 25, 36, 120, 130);
    } else {
      if (is_null($datoSucursal)) {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 25, 36, 120, 130);
      } else {
        $this->mpdf->WriteFixedPosHTML("<span style=' font-size: 12px'><strong>Dirección:</strong> <span style='font-size: 10px'>{$datoSucursal['direccion']}</span></span>", 25, 36, 120, 130);
      }
    }

    $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'>Telf: {$datoEmpresa['telefono']} -Email:{$datoEmpresa['email']} </span>", 25, 39, 210, 130);

    $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'> Web: https://industriajvcsac.com/</span>", 25, 42, 210, 130);

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
              <strong>Orden de Compra:</strong> {$datoVenta['doc_referencia']}
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
                            <div style="margin: 0;">Usuario: ' . $nombre_usuario . ' (cod: ' . $codigo_usuario . ')</div>
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
  public function comprobanteCotizacionMediaA4($coti)
  {
    // Configuración específica para media hoja A4
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_top' => 5,
      'margin_bottom' => 5,
      'margin_left' => 5,
      'margin_right' => 5,
      'format' => [210, 148], // A4 medio (horizontal)
    ]);

    // Consulta inicial para productos y repuestos
    $listaProd1 = $this->conexion->query("
        SELECT 
            pc.*,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.nombre 
                WHEN pc.tipo_producto = 'repuesto' THEN r.nombre
            END as nombre,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.detalle 
                WHEN pc.tipo_producto = 'repuesto' THEN r.detalle
            END as descripcion,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN TRIM(p.codigo)
                WHEN pc.tipo_producto = 'repuesto' THEN TRIM(r.codigo)
            END as codigo,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.imagen
                WHEN pc.tipo_producto = 'repuesto' THEN r.imagen
            END as imagen
        FROM productos_cotis pc 
        LEFT JOIN productos p ON p.id_producto = pc.id_producto AND pc.tipo_producto = 'producto'
        LEFT JOIN repuestos r ON r.id_repuesto = pc.id_producto AND pc.tipo_producto = 'repuesto'
        WHERE pc.id_coti = '$coti' 
        ORDER BY codigo ASC
    ");

    // Obtener datos de la cotización
    $sql = "select * from cotizaciones where cotizacion_id=" . $coti;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();

    // Obtener datos de la empresa
    $sql_empresa = "SELECT e.* FROM empresas e 
    INNER JOIN cotizaciones c ON c.id_empresa = e.id_empresa 
    WHERE c.cotizacion_id = " . $coti;
    $datoEmpresa = $this->conexion->query($sql_empresa)->fetch_assoc();

    // Obtener datos del cliente
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();

    if (!$datoEmpresa) {
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }

    // Obtener datos del usuario
    $usuario_actual = [];
    $query = "SELECT 
                u.nombres,
                u.telefono,
                r.nombre as rol
              FROM usuarios u
              INNER JOIN roles r ON r.rol_id = u.id_rol
              WHERE u.usuario_id = 40";

    $result = $this->conexion->query($query);
    if ($result && $result->num_rows > 0) {
      $usuario_actual = $result->fetch_assoc();
    }

    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);
    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    // Procesar cuotas si es pago a crédito
    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti='$coti'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
                <tr>
                    <td style='font-size: 10px;'>Cuota $tempNum</td>
                    <td style='font-size: 10px;'>$tempFecha</td>
                    <td style='font-size: 10px;'>S/ $tempMonto</td>
                </tr>";
      }

      $tabla_cuotas = "
            <div style='width: 100%; margin-top: 10px;'>
                <table style='width: 50%; margin: auto; border-collapse: collapse;'>
                    <thead>
                        <tr>
                            <th style='font-size: 10px; border: 1px solid #000;'>CUOTA</th>
                            <th style='font-size: 10px; border: 1px solid #000;'>FECHA</th>
                            <th style='font-size: 10px; border: 1px solid #000;'>MONTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        $rowTempCuo
                    </tbody>
                </table>
            </div>";
    }

    // Generar filas de productos
    $rowHTML = '';
    $total = 0;
    $hasSpecialPrices = false;

    // Verificar si hay precios especiales
    foreach ($listaProd1 as $prod) {
      if (!empty($prod['precioEspecial']) && $prod['precioEspecial'] > 0) {
        $hasSpecialPrices = true;
        break;
      }
    }

    // Generar encabezado de tabla
    $tableHeader = "
    <tr style='background-color: #CA3438;'>
        <td style='width: 8%; font-size: 10px; color: white; text-align: center; border: 1px solid #CA3438;'><strong>CANT</strong></td>
        <td style='width: 52%; font-size: 10px; color: white; text-align: center; border: 1px solid #CA3438;'><strong>DESCRIPCION</strong></td>
        <td style='width: 12%; font-size: 10px; color: white; text-align: center; border: 1px solid #CA3438;'><strong>PRECIO U</strong></td>
        <td style='width: 12%; font-size: 10px; color: white; text-align: center; border: 1px solid #CA3438;'><strong>IMPORTE</strong></td>";

    if ($hasSpecialPrices) {
      $tableHeader .= "<td style='width: 16%; font-size: 10px; color: white; text-align: center; border: 1px solid #CA3438;'><strong>PRECIO ESP.</strong></td>";
    }

    $tableHeader .= "</tr>";

    // Generar filas de productos
    foreach ($listaProd1 as $prod) {
      if ($datoVenta['moneda'] == 2) {
        $prod['precio'] = $prod['precio'] / $datoVenta['cm_tc'];
      }

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      $total += $importe;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');
      $precioEspecial = $prod['precioEspecial'] ? number_format($prod['precioEspecial'], 2, '.', ',') : '0.00';

      $rowHTML .= "
        <tr>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438;'>{$prod['cantidad']}</td>
            <td style='font-size: 10px; text-align: left; border: 1px solid #CA3438;'><strong>{$prod['nombre']}</strong><br>{$prod['descripcion']}</td>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$precio</td>
            <td style='font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$importe</td>";

      if ($hasSpecialPrices) {
        $rowHTML .= "<td style='font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$precioEspecial</td>";
      }

      $rowHTML .= "</tr>";
    }

    // Calcular totales
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;
    $igv = $totalConDescuento / 1.18 * 0.18;
    $totalOpgravado = $totalConDescuento - $igv;

    // Formatear números
    $montoDescuento = number_format($montoDescuento, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $total = number_format($totalConDescuento, 2, '.', ',');

    // Convertir total a letras
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalConDescuento, 2, '.', ''), 2, $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES');

    // Generar HTML
    $html = "
    <div style='width: 100%;'>
        <div style='text-align: center; margin-bottom: 10px;'>
            <img style='max-width: 200px;' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>
        </div>
        
        <div style='text-align: center; margin-bottom: 10px;'>
            <span style='font-size: 14px; font-weight: bold;'>{$datoEmpresa['razon_social']}</span><br>
            <span style='font-size: 12px;'>RUC: {$datoEmpresa['ruc']}</span><br>
            <span style='font-size: 12px;'>{$datoEmpresa['direccion']}</span><br>
            <span style='font-size: 12px;'>Teléfono: {$datoEmpresa['telefono']}</span>
        </div>

        <div style='text-align: center; margin-bottom: 10px;'>
            <span style='font-size: 14px; font-weight: bold;'>COTIZACIÓN N° {$datoVenta['numero']}</span>
        </div>

        <table style='width: 100%; margin-bottom: 10px; border-collapse: collapse;'>
            <tr>
                <td style='width: 50%; vertical-align: top;'>
                    <table style='width: 100%;'>
                        <tr>
                            <td style='font-size: 10px;'><strong>Cliente:</strong></td>
                            <td style='font-size: 10px;'>{$resultC['datos']}</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px;'><strong>RUC/DNI:</strong></td>
                            <td style='font-size: 10px;'>{$resultC['documento']}</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px;'><strong>Dirección:</strong></td>
                            <td style='font-size: 10px;'>{$resultC['direccion']}</td>
                        </tr>
                    </table>
                </td>
                <td style='width: 50%; vertical-align: top;'>
                    <table style='width: 100%;'>
                        <tr>
                            <td style='font-size: 10px;'><strong>Fecha:</strong></td>
                            <td style='font-size: 10px;'>$fecha_emision</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px;'><strong>Moneda:</strong></td>
                            <td style='font-size: 10px;'>" . ($datoVenta['moneda'] == 1 ? 'SOLES' : 'DÓLARES') . "</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px;'><strong>Forma de Pago:</strong></td>
                            <td style='font-size: 10px;'>$tipo_pagoC</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style='width: 100%; border-collapse: collapse; margin-bottom: 10px;'>
            $tableHeader
            $rowHTML
        </table>

        <table style='width: 100%; margin-bottom: 10px;'>
            <tr>
                <td style='width: 60%; vertical-align: top;'>
                    <div style='font-size: 10px;'>
                        <strong>Son:</strong> $totalLetras
                    </div>
                    $tabla_cuotas
                </td>
                <td style='width: 40%;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='font-size: 10px; text-align: right;'><strong>Op. Gravada:</strong></td>
                            <td style='font-size: 10px; text-align: right;'>$totalOpgravado</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px; text-align: right;'><strong>IGV:</strong></td>
                            <td style='font-size: 10px; text-align: right;'>$igv</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px; text-align: right;'><strong>Descuento:</strong></td>
                            <td style='font-size: 10px; text-align: right;'>$montoDescuento</td>
                        </tr>
                        <tr>
                            <td style='font-size: 10px; text-align: right;'><strong>Total:</strong></td>
                            <td style='font-size: 10px; text-align: right;'>$total</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style='margin-bottom: 10px;'>
            <span style='font-size: 10px;'><strong>Observaciones:</strong> {$datoVenta['observacion']}</span>
        </div>

        <table style='width: 100%; margin-top: 20px;'>
            <tr>
                <td style='width: 50%; text-align: center;'>
                    <div style='font-size: 10px;'>
                        <strong>{$usuario_actual['nombres']}</strong><br>
                        {$usuario_actual['rol']}<br>
                        Teléfono: {$usuario_actual['telefono']}
                    </div>
                </td>
                <td style='width: 50%; text-align: center;'>
                    <div style='font-size: 10px;'>
                        <strong>Eduardo Crisóstomo P.</strong><br>
                        Jefe de Ventas y Servicios<br>
                        Teléfono: 355-4701<br>
                        Cel: 996246564 - 943140418
                    </div>
                </td>
            </tr>
        </table>
    </div>";

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output("Cotizacion_Media_A4_{$datoVenta['numero']}.pdf", 'I');
  }
  public function comprobanteCotizacionVoucher8cm($coti)
  {
    // Configuración específica para voucher de 8cm
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_bottom' => 5,
      'margin_top' => 10,
      'margin_left' => 4,
      'margin_right' => 4,
      'mode' => 'utf-8',
    ]);

    // Consulta inicial para productos y repuestos
    $listaProd1 = $this->conexion->query("
        SELECT 
            pc.*,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.nombre 
                WHEN pc.tipo_producto = 'repuesto' THEN r.nombre
            END as nombre,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.detalle 
                WHEN pc.tipo_producto = 'repuesto' THEN r.detalle
            END as descripcion,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN TRIM(p.codigo)
                WHEN pc.tipo_producto = 'repuesto' THEN TRIM(r.codigo)
            END as codigo
        FROM productos_cotis pc 
        LEFT JOIN productos p ON p.id_producto = pc.id_producto AND pc.tipo_producto = 'producto'
        LEFT JOIN repuestos r ON r.id_repuesto = pc.id_producto AND pc.tipo_producto = 'repuesto'
        WHERE pc.id_coti = '$coti' 
        ORDER BY codigo ASC
    ");

    // Obtener datos de la cotización
    $sql = "select * from cotizaciones where cotizacion_id=" . $coti;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();

    // Obtener datos de la empresa
    $sql_empresa = "SELECT e.* FROM empresas e 
    INNER JOIN cotizaciones c ON c.id_empresa = e.id_empresa 
    WHERE c.cotizacion_id = " . $coti;
    $datoEmpresa = $this->conexion->query($sql_empresa)->fetch_assoc();

    // Obtener datos del cliente
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();

    if (!$datoEmpresa) {
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }

    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);
    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    // Procesar cuotas si es pago a crédito
    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti='$coti'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
                <tr>
                    <td style='font-size: 8px;'>Cuota $tempNum</td>
                    <td style='font-size: 8px;'>$tempFecha</td>
                    <td style='font-size: 8px;'>S/ $tempMonto</td>
                </tr>";
      }

      $tabla_cuotas = "
            <div style='width: 100%; text-align: center; margin-top: 5px;'>
                <strong><span style='font-size: 9px;'>Cuotas de pago</span></strong>
                <table style='width: 100%; margin: auto; border-collapse: collapse;'>
                    <thead>
                        <tr>
                            <th style='font-size: 8px;'>CUOTA</th>
                            <th style='font-size: 8px;'>FECHA</th>
                            <th style='font-size: 8px;'>MONTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        $rowTempCuo
                    </tbody>
                </table>
            </div>";
    }

    // Generar filas de productos
    $rowHTML = '';
    $total = 0;

    foreach ($listaProd1 as $prod) {
      if ($datoVenta['moneda'] == 2) {
        $prod['precio'] = $prod['precio'] / $datoVenta['cm_tc'];
      }

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      $total += $importe;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');

      $rowHTML .= "
        <tr>
            <td style='font-size: 8px; text-align: center;'>{$prod['cantidad']}</td>
            <td style='font-size: 8px; text-align: left;'>{$prod['codigo']} | {$prod['nombre']}</td>
            <td style='font-size: 8px; text-align: right;'>$precio</td>
            <td style='font-size: 8px; text-align: right;'>$importe</td>
        </tr>";
    }

    // Calcular totales
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;
    $igv = $totalConDescuento / 1.18 * 0.18;
    $totalOpgravado = $totalConDescuento - $igv;

    // Formatear números
    $montoDescuento = number_format($montoDescuento, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $total = number_format($totalConDescuento, 2, '.', ',');

    // Convertir total a letras
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalConDescuento, 2, '.', ''), 2, $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES');

    // Configurar tamaño de página
    $this->mpdf->AddPageByArray([
      "orientation" => "P",
      "newformat" => [80, 200]
    ]);

    // Generar HTML
    $html = "
    <div style='width: 100%;'>
        <div style='text-align: center; margin-bottom: 5px;'>
            <img style='max-width: 60mm;' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>
        </div>
        
        <div style='text-align: center; margin-bottom: 5px;'>
            <span style='font-size: 12px; font-weight: bold;'>{$datoEmpresa['razon_social']}</span><br>
            <span style='font-size: 9px;'>RUC: {$datoEmpresa['ruc']}</span><br>
            <span style='font-size: 9px;'>{$datoEmpresa['direccion']}</span><br>
            <span style='font-size: 9px;'>Teléfono: {$datoEmpresa['telefono']}</span>
        </div>

        <div style='text-align: center; margin-bottom: 5px;'>
            <span style='font-size: 11px; font-weight: bold;'>COTIZACIÓN N° {$datoVenta['numero']}</span>
        </div>

        <div style='margin-bottom: 5px;'>
            <table style='width: 100%;'>
                <tr>
                    <td style='font-size: 8px;'><strong>Fecha:</strong></td>
                    <td style='font-size: 8px;'>$fecha_emision</td>
                </tr>
                <tr>
                    <td style='font-size: 8px;'><strong>Cliente:</strong></td>
                    <td style='font-size: 8px;'>{$resultC['datos']}</td>
                </tr>
                <tr>
                    <td style='font-size: 8px;'><strong>RUC/DNI:</strong></td>
                    <td style='font-size: 8px;'>{$resultC['documento']}</td>
                </tr>
                <tr>
                    <td style='font-size: 8px;'><strong>Dirección:</strong></td>
                    <td style='font-size: 8px;'>{$resultC['direccion']}</td>
                </tr>
                <tr>
                    <td style='font-size: 8px;'><strong>Forma de Pago:</strong></td>
                    <td style='font-size: 8px;'>$tipo_pagoC</td>
                </tr>
            </table>
        </div>

        <div style='text-align: center; margin-bottom: 3px;'>
            <span style='font-size: 9px;'>==========================================</span>
        </div>

        <table style='width: 100%; border-collapse: collapse; margin-bottom: 5px;'>
            <tr>
                <td style='font-size: 8px; border-bottom: 1px solid #000;'><strong>CANT</strong></td>
                <td style='font-size: 8px; border-bottom: 1px solid #000;'><strong>DESCRIPCIÓN</strong></td>
                <td style='font-size: 8px; border-bottom: 1px solid #000;'><strong>P.UNIT</strong></td>
                <td style='font-size: 8px; border-bottom: 1px solid #000;'><strong>TOTAL</strong></td>
            </tr>
            $rowHTML
        </table>

        <div style='text-align: right; margin-bottom: 5px;'>
            <table style='width: 100%;'>
                <tr>
                    <td style='font-size: 8px; text-align: right;'><strong>Op. Gravada:</strong></td>
                    <td style='font-size: 8px; text-align: right;'>$totalOpgravado</td>
                </tr>
                <tr>
                    <td style='font-size: 8px; text-align: right;'><strong>IGV:</strong></td>
                    <td style='font-size: 8px; text-align: right;'>$igv</td>
                </tr>
                <tr>
                    <td style='font-size: 8px; text-align: right;'><strong>Descuento:</strong></td>
                    <td style='font-size: 8px; text-align: right;'>$montoDescuento</td>
                </tr>
                <tr>
                    <td style='font-size: 8px; text-align: right;'><strong>Total:</strong></td>
                    <td style='font-size: 8px; text-align: right;'>$total</td>
                </tr>
            </table>
        </div>

        <div style='margin-bottom: 5px;'>
            <span style='font-size: 8px;'><strong>Son:</strong> $totalLetras</span>
        </div>

        $tabla_cuotas

        <div style='margin-bottom: 5px;'>
            <span style='font-size: 8px;'><strong>Observaciones:</strong> {$datoVenta['observacion']}</span>
        </div>

        <div style='text-align: center; margin-top: 10px;'>
            <span style='font-size: 8px;'>¡Gracias por su preferencia!</span>
        </div>
    </div>";

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output("Cotizacion_Voucher_8cm_{$datoVenta['numero']}.pdf", 'I');
  }
  public function comprobanteCotizacionVoucher5_6cm($coti)
  {
    // Configuración específica para voucher de 5.6cm
    $this->mpdf = new \Mpdf\Mpdf([
      'margin_bottom' => 5,
      'margin_top' => 7,
      'margin_left' => 4,
      'margin_right' => 4,
      'mode' => 'utf-8',
    ]);

    // Consulta inicial para productos y repuestos
    $listaProd1 = $this->conexion->query("
        SELECT 
            pc.*,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.nombre 
                WHEN pc.tipo_producto = 'repuesto' THEN r.nombre
            END as nombre,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN p.detalle 
                WHEN pc.tipo_producto = 'repuesto' THEN r.detalle
            END as descripcion,
            CASE 
                WHEN pc.tipo_producto = 'producto' THEN TRIM(p.codigo)
                WHEN pc.tipo_producto = 'repuesto' THEN TRIM(r.codigo)
            END as codigo
        FROM productos_cotis pc 
        LEFT JOIN productos p ON p.id_producto = pc.id_producto AND pc.tipo_producto = 'producto'
        LEFT JOIN repuestos r ON r.id_repuesto = pc.id_producto AND pc.tipo_producto = 'repuesto'
        WHERE pc.id_coti = '$coti' 
        ORDER BY codigo ASC
    ");

    // Obtener datos de la cotización
    $sql = "select * from cotizaciones where cotizacion_id=" . $coti;
    $datoVenta = $this->conexion->query($sql)->fetch_assoc();

    // Obtener datos de la empresa
    $sql_empresa = "SELECT e.* FROM empresas e 
    INNER JOIN cotizaciones c ON c.id_empresa = e.id_empresa 
    WHERE c.cotizacion_id = " . $coti;
    $datoEmpresa = $this->conexion->query($sql_empresa)->fetch_assoc();

    // Obtener datos del cliente
    $resultC = $this->conexion->query("select * from clientes where id_cliente = " . $datoVenta['id_cliente'])->fetch_assoc();

    if (!$datoEmpresa) {
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }

    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);
    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    // Procesar cuotas si es pago a crédito
    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti='$coti'";
      $resulTempCuo = $this->conexion->query($sql);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $tempMonto = Tools::money($cuotTemp['monto']);
        $rowTempCuo .= "
                <tr>
                    <td style='font-size: 7px;'>Cuota $tempNum</td>
                    <td style='font-size: 7px;'>$tempFecha</td>
                    <td style='font-size: 7px;'>S/ $tempMonto</td>
                </tr>";
      }

      $tabla_cuotas = "
            <div style='width: 100%; text-align: center; margin-top: 3px;'>
                <strong><span style='font-size: 8px;'>Cuotas de pago</span></strong>
                <table style='width: 100%; margin: auto; border-collapse: collapse;'>
                    <thead>
                        <tr>
                            <th style='font-size: 7px;'>CUOTA</th>
                            <th style='font-size: 7px;'>FECHA</th>
                            <th style='font-size: 7px;'>MONTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        $rowTempCuo
                    </tbody>
                </table>
            </div>";
    }

    // Generar filas de productos
    $rowHTML = '';
    $total = 0;

    foreach ($listaProd1 as $prod) {
      if ($datoVenta['moneda'] == 2) {
        $prod['precio'] = $prod['precio'] / $datoVenta['cm_tc'];
      }

      $precio = $prod['precio'];
      $importe = $precio * $prod['cantidad'];
      $total += $importe;

      $precio = number_format($precio, 2, '.', ',');
      $importe = number_format($importe, 2, '.', ',');

      $rowHTML .= "
        <tr>
            <td style='font-size: 7px; text-align: center;'>{$prod['cantidad']}</td>
            <td style='font-size: 7px; text-align: left;'>{$prod['codigo']} | {$prod['nombre']}</td>
            <td style='font-size: 7px; text-align: right;'>$precio</td>
            <td style='font-size: 7px; text-align: right;'>$importe</td>
        </tr>";
    }

    // Calcular totales
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;
    $igv = $totalConDescuento / 1.18 * 0.18;
    $totalOpgravado = $totalConDescuento - $igv;

    // Formatear números
    $montoDescuento = number_format($montoDescuento, 2, '.', ',');
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $total = number_format($totalConDescuento, 2, '.', ',');

    // Convertir total a letras
    $formatter = new NumeroALetras;
    $totalLetras = $formatter->toInvoice(number_format($totalConDescuento, 2, '.', ''), 2, $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES');

    // Configurar tamaño de página
    $this->mpdf->AddPageByArray([
      "orientation" => "P",
      "newformat" => [56, 180]
    ]);

    // Generar HTML
    $html = "
    <div style='width: 100%;'>
        <div style='text-align: center; margin-bottom: 3px;'>
            <img style='max-width: 40mm;' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>
        </div>
        
        <div style='text-align: center; margin-bottom: 3px;'>
            <span style='font-size: 10px; font-weight: bold;'>{$datoEmpresa['razon_social']}</span><br>
            <span style='font-size: 8px;'>RUC: {$datoEmpresa['ruc']}</span><br>
            <span style='font-size: 8px;'>{$datoEmpresa['direccion']}</span><br>
            <span style='font-size: 8px;'>Teléfono: {$datoEmpresa['telefono']}</span>
        </div>

        <div style='text-align: center; margin-bottom: 3px;'>
            <span style='font-size: 9px; font-weight: bold;'>COTIZACIÓN N° {$datoVenta['numero']}</span>
        </div>

        <div style='margin-bottom: 3px;'>
            <table style='width: 100%;'>
                <tr>
                    <td style='font-size: 7px;'><strong>Fecha:</strong></td>
                    <td style='font-size: 7px;'>$fecha_emision</td>
                </tr>
                <tr>
                    <td style='font-size: 7px;'><strong>Cliente:</strong></td>
                    <td style='font-size: 7px;'>{$resultC['datos']}</td>
                </tr>
                <tr>
                    <td style='font-size: 7px;'><strong>RUC/DNI:</strong></td>
                    <td style='font-size: 7px;'>{$resultC['documento']}</td>
                </tr>
                <tr>
                    <td style='font-size: 7px;'><strong>Dirección:</strong></td>
                    <td style='font-size: 7px;'>{$resultC['direccion']}</td>
                </tr>
                <tr>
                    <td style='font-size: 7px;'><strong>Forma de Pago:</strong></td>
                    <td style='font-size: 7px;'>$tipo_pagoC</td>
                </tr>
            </table>
        </div>

        <div style='text-align: center; margin-bottom: 2px;'>
            <span style='font-size: 8px;'>================================</span>
        </div>

        <table style='width: 100%; border-collapse: collapse; margin-bottom: 3px;'>
            <tr>
                <td style='font-size: 7px; border-bottom: 1px solid #000;'><strong>CANT</strong></td>
                <td style='font-size: 7px; border-bottom: 1px solid #000;'><strong>DESCRIPCIÓN</strong></td>
                <td style='font-size: 7px; border-bottom: 1px solid #000;'><strong>P.UNIT</strong></td>
                <td style='font-size: 7px; border-bottom: 1px solid #000;'><strong>TOTAL</strong></td>
            </tr>
            $rowHTML
        </table>

        <div style='text-align: right; margin-bottom: 3px;'>
            <table style='width: 100%;'>
                <tr>
                    <td style='font-size: 7px; text-align: right;'><strong>Op. Gravada:</strong></td>
                    <td style='font-size: 7px; text-align: right;'>$totalOpgravado</td>
                </tr>
                <tr>
                    <td style='font-size: 7px; text-align: right;'><strong>IGV:</strong></td>
                    <td style='font-size: 7px; text-align: right;'>$igv</td>
                </tr>
                <tr>
                    <td style='font-size: 7px; text-align: right;'><strong>Descuento:</strong></td>
                    <td style='font-size: 7px; text-align: right;'>$montoDescuento</td>
                </tr>
                <tr>
                    <td style='font-size: 7px; text-align: right;'><strong>Total:</strong></td>
                    <td style='font-size: 7px; text-align: right;'>$total</td>
                </tr>
            </table>
        </div>

        <div style='margin-bottom: 3px;'>
            <span style='font-size: 7px;'><strong>Son:</strong> $totalLetras</span>
        </div>

        $tabla_cuotas

        <div style='margin-bottom: 3px;'>
            <span style='font-size: 7px;'><strong>Observaciones:</strong> {$datoVenta['observacion']}</span>
        </div>

        <div style='text-align: center; margin-top: 5px;'>
            <span style='font-size: 7px;'>¡Gracias por su preferencia!</span>
        </div>
    </div>";

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output("Cotizacion_Voucher_5_6cm_{$datoVenta['numero']}.pdf", 'I');
  }

}
