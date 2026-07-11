<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once 'app/Services/CotizacionReporteService.php';
require_once 'app/helpers/ImageStorage.php';

use Luecano\NumeroALetras\NumeroALetras;

class ReportesCotizacionController extends Controller
{
  private $mpdf;
  private $conexion;
  private $service;

  public function __construct()
  {
    $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 0]);
    $this->conexion = (new Conexion())->getConexion();
    $this->service = new CotizacionReporteService();
  }

  private function getImagePath($imageName)
  {
    return PdfHelper::getImagePath($imageName);
  }

  public function comprobanteCotizacion($coti, $rutaGuardar = null)
  {
    // Suprimir warnings de mPDF
    $errorReportingAnterior = error_reporting();
    error_reporting(E_ERROR | E_PARSE);

    // Si $rutaGuardar es un objeto Request, significa que se llamó sin el parámetro
    if (is_object($rutaGuardar)) {
      $rutaGuardar = null;
    }

    // Log temporal para debug
    error_log("comprobanteCotizacion - coti: $coti, rutaGuardar: " . ($rutaGuardar ? $rutaGuardar : 'NULL'));

    // Modificar la consulta inicial para manejar tanto productos como repuestos
    // Usar COALESCE para priorizar nombres personalizados de la cotización
    $listaProd1 = $this->service->obtenerDetalleCotizacion($coti);
    $datoVenta = $this->service->obtenerCotizacion($coti);

    // Verificar que se obtuvo la cotización
    if (!$datoVenta) {
      throw new Exception("No se encontró la cotización con ID: " . $coti);
    }

    // Definir el símbolo de moneda al inicio
    $simbolfff22 = $datoVenta['moneda'] == 1 ? 'S/' : '$';

    //obtener el asunto
    $asunto = $this->service->obtenerAsunto($datoVenta['id_asunto'] ?? 0);
    $datoEmpresa = $this->service->obtenerEmpresaPorCotizacion($coti);

    // Verificar que id_cliente no sea null
    if (empty($datoVenta['id_cliente'])) {
      throw new Exception("La cotización no tiene un cliente asignado");
    }

    $resultC = $this->service->obtenerCliente($datoVenta['id_cliente']);

    if (!$datoEmpresa) {
      // Si no se encuentra la empresa, usar valores por defecto o mostrar error
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }
    $dataDocumento = strlen($resultC['documento']) == 8 ? "DNI" : (strlen($resultC['documento']) == 11 ? 'RUC' : '');

    error_log('SESSION: ' . print_r($_SESSION, true));

    $usuario_actual = $this->service->obtenerUsuario($datoVenta['id_usuario']) ?: [];
    
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);

    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    $sql_condiciones = "SELECT * FROM condiciones_cotizacion WHERE id_cotizacion = ?";
    $stmt_cond = $this->conexion->prepare($sql_condiciones);
    $stmt_cond->bind_param("i", $coti);
    $stmt_cond->execute();
    $result_condiciones = $stmt_cond->get_result();

    if ($result_condiciones && $result_condiciones->num_rows > 0) {
      $condicion = $result_condiciones->fetch_assoc();
      $condicion_texto = $condicion['condiciones'];
    } else {
      $condicion = $this->service->obtenerCondicionesDefault();
      $condicion_texto = $condicion['nombre'];
    }

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $resulTempCuo = $this->service->obtenerCuotas($coti);
      $contadorCuota = 0;
      foreach ($resulTempCuo as $cuotTemp) {
        // Saltar cuotas con monto 0 o vacío
        if (empty($cuotTemp['monto']) || floatval($cuotTemp['monto']) == 0) {
          continue;
        }

        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $montoCuota = $datoVenta['moneda'] == 2 ? $cuotTemp['monto'] / $datoVenta['cm_tc'] : $cuotTemp['monto'];
        $tempMonto = Tools::money($montoCuota);

        // Si es cuota inicial, mostrarla como "INICIAL" en lugar de número
        $etiquetaCuota = (isset($cuotTemp['tipo']) && $cuotTemp['tipo'] == 'inicial') ? 'INICIAL' : "Cuota $tempNum";

        $rowTempCuo .= "
       <tr style=''>
           <td style='padding: 3px; text-align: center;'>$etiquetaCuota</td>
           <td style='padding: 3px; text-align: center;'>$tempFecha</td>
           <td style='padding: 3px; text-align: center;'>$simbolfff22 $tempMonto</td>
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
    $totalSinDescuentoGeneral = 0;
    $contador = 0;
    $igv = 0;

    $rowHTML = '';
    $lastItemHTML = '';
    $lastItemIndex = mysqli_num_rows($listaProd1);

    // Verificar si algún producto tiene precio especial O si hay descuento general
    $hasSpecialPrices = false;
    $descuentoEspecialTotal = 0;
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $hasGeneralDiscount = $descuentoGeneral > 0;

    // Primero verificamos si algún producto tiene precio especial
    foreach ($listaProd1 as $prod) {
      if (!empty($prod['precioEspecial']) && $prod['precioEspecial'] > 0 && $prod['precioEspecial'] != $prod['precio']) {
        $hasSpecialPrices = true;
        break;
      }
    }

    // Mostrar columna COSTO C/DESC si hay precios especiales O descuento general
    $showDiscountColumn = $hasSpecialPrices || $hasGeneralDiscount;

    // Modificar el encabezado de la tabla según si hay precios especiales o no
    // Modificar el encabezado de la tabla para establecer anchos consistentes
    $tableHeader = "
    <tr style='border-collapse: collapse;'>
        <td style='width: 30px; font-size: 10px; font-family: Arial, Helvetica, sans-serif;text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>ITEM</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif; width: " . ($showDiscountColumn ? "240px" : "300px") . "; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>DESCRIPCIÓN</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif;width: 30px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>CANT</strong></td>
        <td style='font-family: Arial, Helvetica, sans-serif; width: 75px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>PRECIO<br>UNITARIO<br>" . ($datoVenta['aplicar_igv'] == 1 ? '(CON I.G.V.)' : '(SIN I.G.V.)') . "</strong></td>";

    if ($showDiscountColumn) {
      $tableHeader .= "
        <td style='font-family: Arial, Helvetica, sans-serif; width: 75px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>PRECIO<br>ESPECIAL<br>" . ($datoVenta['aplicar_igv'] == 1 ? '(CON I.G.V.)' : '(SIN I.G.V.)') . "</strong></td>";
    }

    $tableHeader .= "
        <td style='font-family: Arial, Helvetica, sans-serif; width: 75px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>PRECIO<br>TOTAL<br>" . ($datoVenta['aplicar_igv'] == 1 ? '(CON I.G.V.)' : '(SIN I.G.V.)') . "</strong>
</td>";

    $tableHeader .= "
        <td style='font-family: Arial, Helvetica, sans-serif;width: 80px; font-size: 10px; text-align: center; color: #fff; background-color: #CA3438; border: 1px solid #CA3438;'><strong>IMAGEN<br>REFERENCIAL</strong></td>
    </tr>";

    foreach ($listaProd1 as $prod) {
      $contador++;
      if ($datoVenta['moneda'] == 2) {
        $prod['precio'] = $prod['precio'] / $datoVenta['cm_tc'];
      }

      $precio = $prod['precio'];
      if (!empty($prod['precioEspecial']) && $prod['precioEspecial'] > 0) {
        $precioEspecial = $datoVenta['moneda'] == 2
          ? $prod['precioEspecial'] / $datoVenta['cm_tc']
          : $prod['precioEspecial'];
      } else {
        $precioEspecial = $precio;
      }

      // Calcular precio con descuento general (si aplica)
      $precioConDescuentoGeneral = $precioEspecial;
      if ($hasGeneralDiscount) {
        $descuentoUnitario = ($precioEspecial * $descuentoGeneral) / 100;
        $precioConDescuentoGeneral = $precioEspecial - $descuentoUnitario;
      }

      // Calcular el descuento por precio especial
      if ($precioEspecial < $precio) {
        $descuentoEspecialTotal += ($precio - $precioEspecial) * $prod['cantidad'];
      }

      // Usar el precio con descuento para el cálculo del importe final
      $importeBase = $precioConDescuentoGeneral * $prod['cantidad'];
      $importeSinDescuentoGeneral = $precioEspecial * $prod['cantidad'];
      $total += $importeBase;
      $totalSinDescuentoGeneral += $importeSinDescuentoGeneral;
      $totalDescuentoEspecial = 0;
      $tempDescuento = 0;

      if ($datoVenta['aplicar_igv'] == 0) {
        // Cliente exonerado: quitar el IGV que ya está incluido en los precios
        $precioFormateado = number_format($precio / 1.18, 2, '.', ',');
        $precioEspecialFormateado = number_format($precioEspecial / 1.18, 2, '.', ',');
        $precioConDescuentoGeneralFormateado = number_format($precioConDescuentoGeneral / 1.18, 2, '.', ',');
        $importeFormateado = number_format($importeBase / 1.18, 2, '.', ',');
      } else {
        // Cliente normal: usar precio con IGV ya incluido
        $precioFormateado = number_format($precio, 2, '.', ',');
        $precioEspecialFormateado = number_format($precioEspecial, 2, '.', ',');
        $precioConDescuentoGeneralFormateado = number_format($precioConDescuentoGeneral, 2, '.', ',');
        $importeFormateado = number_format($importeBase, 2, '.', ',');
      }

      $importe = number_format($importeBase, 2, '.', ','); // Mantener para compatibilidad
      $tempDescuento = number_format($tempDescuento, 2, '.', ',');
      $prod['codigo'] = trim($prod['codigo']);
      $detalle = nl2br($prod['descripcion']);

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
          $detalle .= '<br>' . $equipoInfo;
        }
      }

      // Mejorar la detección de líneas contando tanto \n como <br>
      $numLines = substr_count($prod['descripcion'], "\n") +
        substr_count($prod['descripcion'], "\r") +
        substr_count($detalle, '<br>') + 1;

      // Si es el último ítem, guárdalo en una variable separada
      if ($contador == $lastItemIndex) {
        // Corrección para el estilo del último ítem
        if ($contador == $lastItemIndex) {
          $lastItemHTML = "
<tr>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$contador</td>
    <td style='width: " . ($showDiscountColumn ? "240px" : "300px") . "; font-size: 10px; text-align: left; border: 1px solid #CA3438;'><strong>{$prod['nombre']}</strong><br>{$detalle}</td>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>{$prod['cantidad']}</td>
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$simbolfff22 $precioFormateado</td>
";

          if ($showDiscountColumn) {
            // Determinar qué precio mostrar y si aplicar fondo
            $precioDescuento = '';
            $aplicarFondo = false;

            if ($hasSpecialPrices && $precioEspecial < $precio) {
              $precioDescuento = "$simbolfff22 $precioEspecialFormateado";
              $aplicarFondo = true;
            } elseif ($hasGeneralDiscount) {
              $precioDescuento = "$simbolfff22 $precioConDescuentoGeneralFormateado";
              $aplicarFondo = true;
            }

            $lastItemHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438; " .
              ($aplicarFondo ? "background-color: #FFFDE7;" : "") . "'>" .
              $precioDescuento . "</td>";
          }

          $lastItemHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$simbolfff22 $importeFormateado</td>
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
              $lastItemHTML .= "";
            }
          } catch (Exception $e) {
            $lastItemHTML .= "";
          }

          $lastItemHTML .= "</td>
     </tr>";
        }
      } else {
        $rowHTML .= "
<tr>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$contador</td>
    <td style='width: " . ($showDiscountColumn ? "240px" : "300px") . "; font-size: 10px; text-align: left; border: 1px solid #CA3438;'><strong>{$prod['nombre']}</strong><br>{$detalle}</td>
    <td style='width: 30px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>{$prod['cantidad']}</td>
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$simbolfff22 $precioFormateado</td>
";

        if ($showDiscountColumn) {
          $precioDescuento = '';
          $aplicarFondo = false;

          if ($hasSpecialPrices && $precioEspecial < $precio) {
            $precioDescuento = "$simbolfff22 $precioEspecialFormateado";
            $aplicarFondo = true;
          } elseif ($hasGeneralDiscount) {
            $precioDescuento = "$simbolfff22 $precioConDescuentoGeneralFormateado";
            $aplicarFondo = true;
          }

          $rowHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438; " .
            ($aplicarFondo ? "background-color: #FFFDE7;" : "") . "'>" .
            $precioDescuento . "</td>";
        }

        $rowHTML .= "
    <td style='width: 80px; font-size: 10px; text-align: center; border: 1px solid #CA3438;'>$simbolfff22 $importeFormateado</td>
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
            $rowHTML .= "";
          }

        } catch (Exception $e) {
          $rowHTML .= "";
        }

        $rowHTML .= "</td></tr>";
      }
    }

// CORRECCIÓN: El total del foreach YA TIENE IGV INCLUIDO (precios de almacén)
$totalConIGV = $total;

// Calcular descuento general si existe (se aplica sobre el total con IGV)
// El descuento general YA fue aplicado por unidad en el foreach ($precioConDescuentoGeneral)
// NO volver a aplicarlo. Solo calcular el monto para mostrarlo en el resumen.
$descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
$montoDescuento = $totalSinDescuentoGeneral - $totalConIGV; // Diferencia = descuento real aplicado

// Calcular base gravable, IGV y total según si aplica IGV
if ($datoVenta['aplicar_igv'] == 1) {
  // Cliente normal: el total YA tiene IGV incluido
  $totalConDescuento = $totalConIGV;
  $baseGravable = $totalConIGV / 1.18;
  $igvCalculado = $baseGravable * 0.18;
  $subtotalMostrar = $baseGravable;
} else {
  // Cliente exonerado: quitar el IGV
  $totalConDescuento = $totalConIGV / 1.18;
  $baseGravable = $totalConDescuento;
  $igvCalculado = 0;
  $subtotalMostrar = $totalSinDescuentoGeneral / 1.18;
  $montoDescuento = $montoDescuento / 1.18; // Descuento también sin IGV
}

    // Asignar variables para mostrar en el reporte
    $totalOpgravado = $baseGravable;
    $igv = $igvCalculado;
    $total = number_format($totalConDescuento, 2, '.', ',');
    $totalFinal = $total;
    $igv = number_format($igv, 2, '.', ',');
    $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
    $subtotalParaReporte = number_format($subtotalMostrar, 2, '.', ',');
    $montoDescuento = number_format($montoDescuento, 2, '.', ',');
    $descuentoEspecialTotal = number_format($descuentoEspecialTotal, 2, '.', ',');

    $resumenPrecios = "
     <tr class='price-summary'>
         <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

    if ($showDiscountColumn) {
      $resumenPrecios .= "
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>" . ($datoVenta['aplicar_igv'] == 1 ? 'Gravada:' : 'Sub Total:') . "</td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 " . ($datoVenta['aplicar_igv'] == 1 ? $totalOpgravado : $subtotalParaReporte) . "</td>";
    } else {
      $resumenPrecios .= "
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>" . ($datoVenta['aplicar_igv'] == 1 ? 'Gravada:' : 'Sub Total:') . "</td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 " . ($datoVenta['aplicar_igv'] == 1 ? $totalOpgravado : $subtotalParaReporte) . "</td>
       <td style='border: none;'></td>";
    }

    $resumenPrecios .= "
       </tr>";


    // Mostrar el descuento por precio especial si existe
    if ($hasSpecialPrices && floatval(str_replace(',', '', $descuentoEspecialTotal)) > 0) {
      $resumenPrecios .= "
       <tr>
         <td colspan='" . ($showDiscountColumn ? '3' : '5') . "' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $resumenPrecios .= "
         <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #FFFDE7;'>Descuento Total:</td>
         <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #FFFDE7;'>$simbolfff22 $descuentoEspecialTotal</td>";
      } else {
        $resumenPrecios .= "
         <td colspan='2' style='border: 1px solid #CA3438; font-size: 10px; text-align: center; background-color: #FFFDE7;'>Descuento Total: $simbolfff22 $descuentoEspecialTotal</td>";
      }

      $resumenPrecios .= "
       </tr>";
    }

    // Solo agregar fila de IGV si se aplica IGV
    if ($datoVenta['aplicar_igv'] == 1) {
      $resumenPrecios .= "
     <tr>
       <td colspan='3' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $resumenPrecios .= "
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>IGV (18.00%):</td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 $igv</td>";
      } else {
        $resumenPrecios .= "
       <td colspan='2' style='border: 1px solid #CA3438; font-size: 10px; text-align: center; background-color: #ffffff;'>IGV (18.00%): $simbolfff22 $igv</td>";
      }

      $resumenPrecios .= "
     </tr>";
    }

    // Mostrar el descuento general si existe
    if (floatval(str_replace(',', '', $montoDescuento)) > 0) {
      $resumenPrecios .= "
     <tr>
       <td colspan='3' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $resumenPrecios .= "
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>DESCUENTO<br>APLICADO (" . intval($descuentoGeneral) . "%):</td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 $montoDescuento</td>";
      } else {
        $resumenPrecios .= "
       <td colspan='2' style='border: 1px solid #CA3438; font-size: 10px; text-align: center; background-color: #ffffff;'>DESCUENTO APLICADO (" . intval($descuentoGeneral) . "%): $simbolfff22 $montoDescuento</td>";
      }

      $resumenPrecios .= "
     </tr>";
    }

    $resumenPrecios .= "
     <tr>
       <td colspan='3' style='border: none;'></td>";

    if ($showDiscountColumn) {
      $resumenPrecios .= "
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #CA3438; color:white'><strong>Total:</strong></td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color:white'><strong>$simbolfff22 $totalFinal</strong></td>";
    } else {
      $resumenPrecios .= "
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #CA3438; color:white'><strong>Total:</strong></td>
       <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color:white'><strong>$simbolfff22 $totalFinal</strong></td>
       <td style='border: none;'></td>";
    }

    $resumenPrecios .= "
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
         <td colspan='" . ($showDiscountColumn ? '4' : '5') . "' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $resumenPrecios .= "
         <td style='border: 1px solid #363636; font-size: 12px; text-align: left;'>Total a Pagar</td>
         <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>$simbolfff $totalDolar</td>";
      } else {
        $resumenPrecios .= "
         <td colspan='2' style='border: 1px solid #363636; font-size: 12px; text-align: center;'>Total a Pagar: $simbolfff $totalDolar</td>";
      }

      $resumenPrecios .= "
       </tr>";
    }

    $totalLetras = $formatter->toInvoice(number_format($totalConDescuento, 2, '.', ''), 2, $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES');

    $tituloDocumento = $datoVenta['id_tido'] == 6 ? 'NOTA DE VENTA' : 'COTIZACIÓN';
    $htmlCuadroHead = "<div style='width: auto; text-align: center; margin-bottom: 10px; margin-top:30px'>
           <div style='padding: 5px; width: 70%; margin: 0 auto; border: 2px solid #1e1e1e; margin: left 65px;'>
             <span class='table-header' style='font-size: 14px; font-weight: bold;'>$tituloDocumento DE J.V.C. S.A.C. – N° " . str_pad($datoVenta['numero'], 3, "0", STR_PAD_LEFT) . "/" . date('Y') . "</span>
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
     <img src='public/assets/img/encabezado.svg' style='width: 100%; margin: 0; padding: 0; display: block;'>
     </div>";

    // Establecer el header y configurarlo para todas las páginas
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->WriteHTML('<div style="position: fixed; top: 0; right: 95px; z-index: 1000; margin: bottom 20px;">
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
           <img src="public/assets/img/pie_de_pagina.svg" style="width: 100%; display: block; margin: 0; padding: 0;">
       </div>';
    $this->mpdf->SetHTMLFooter($footerHTML);

    // Condiciones formateadas — soporta HTML (nuevo) y texto plano con • (legacy)
    $monedaVisual = $datoVenta['moneda'] == 1 ? 'SOLES' : 'DOLARES';
    if (strpos(trim($condicion_texto), '<') === 0) {
      $condicionHtml = $condicion_texto;
    } else {
      $lineas = array_filter(explode("\n", $condicion_texto), fn($l) => trim($l) !== '');
      $items = implode('', array_map(fn($l) => '<li>' . ltrim(trim($l), '• ') . '</li>', $lineas));
      $condicionHtml = "<ul>$items</ul>";
    }

    $condicionesFormateadas = "
       <div style='margin: 0; padding: 0;'>
       <p style='font-size: 11px; font-weight: bold; margin: 0; padding: 0;'>Condiciones:</p>
       <div style='font-size: 11px; margin: 0; padding-left: 5px; line-height: 1.4;'>
        $condicionHtml
       </div>
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
                 <td style='font-size: 11px; text-align: left;'>Asunto:</td>
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
    <col style='width: " . ($showDiscountColumn ? "240px" : "300px") . "'> <!-- DESCRIPCIÓN -->
    <col style='width: 30px'>                               <!-- CANT -->
    <col style='width: 75px'>                               <!-- COSTO UNIT. SIN I.G.V. -->
    " . ($showDiscountColumn ? "<col style='width: 75px'>" : "") . " <!-- COSTO C/DESC. (condicional) -->
    <col style='width: 75px'>                               <!-- COSTO TOTAL. SIN I.G.V. -->
    <col style='width: 80px'>                               <!-- IMAGEN REFERENCIAL -->
</colgroup>
         <thead>
             $tableHeader
         </thead>
         <tbody>
             $rowHTML
             $lastItemHTML
             <tr>
                 <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

    if ($showDiscountColumn) {
      $html .= "
               <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>" . ($datoVenta['aplicar_igv'] == 1 ? 'Gravada:' : 'Sub Total:') . "</td>
               <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 " . ($datoVenta['aplicar_igv'] == 1 ? $totalOpgravado : $subtotalParaReporte) . "</td>";
    } else {
      $html .= "
               <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>" . ($datoVenta['aplicar_igv'] == 1 ? 'Gravada:' : 'Sub Total:') . "</td>
               <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 " . ($datoVenta['aplicar_igv'] == 1 ? $totalOpgravado : $subtotalParaReporte) . "</td>
               <td style='border: none;'></td>";
    }

    $html .= "
             </tr>";

    // Mostrar el descuento por precio especial si existe
    if ($hasSpecialPrices && floatval(str_replace(',', '', $descuentoEspecialTotal)) > 0) {
      $html .= "
             <tr>
                 <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $html .= "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #FFFDE7;'>Descuento Total:</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #FFFDE7;'>$simbolfff22 $descuentoEspecialTotal</td>";
      } else {
        $html .= "
                 <td colspan='2' style='border: 1px solid #CA3438; font-size: 10px; text-align: center; background-color: #FFFDE7;'>Descuento Total: $simbolfff22 $descuentoEspecialTotal</td>";
      }

      $html .= "
             </tr>";
    }

    // Solo agregar fila de IGV si se aplica IGV
    if ($datoVenta['aplicar_igv'] == 1) {
      $html .= "
             <tr>
                 <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $html .= "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>IGV (18.00%):</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 $igv</td>";
      } else {
        $html .= "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>IGV (18.00%):</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 $igv</td>";
      }

      $html .= "
             </tr>";
    }

    // Mostrar el descuento general si existe
    if (floatval(str_replace(',', '', $montoDescuento)) > 0) {
      $html .= "
             <tr>
                 <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $html .= "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #ffffff;'>DESCUENTO<br>APLICADO (" . intval($descuentoGeneral) . "%):</td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #ffffff;'>$simbolfff22 $montoDescuento</td>";
      } else {
        $html .= "
                 <td colspan='2' style='border: 1px solid #CA3438; font-size: 10px; text-align: center; background-color: #ffffff;'>DESCUENTO APLICADO (" . intval($descuentoGeneral) . "%): $simbolfff22 $montoDescuento</td>";
      }

      $html .= "
             </tr>";
    }

    $html .= "
             <tr>
                 <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

    if ($showDiscountColumn) {
      $html .= "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #CA3438; color:white'><strong>Total:</strong></td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color:white'><strong>$simbolfff22 $totalFinal</strong></td>";
    } else {
      $html .= "
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: left; background-color: #CA3438; color:white'><strong>Total:</strong></td>
                 <td style='border: 1px solid #CA3438; font-size: 10px; text-align: right; background-color: #CA3438; color:white'><strong>$simbolfff22 $totalFinal</strong></td>
                 <td style='border: none;'></td>";
    }

    $html .= "
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
                 <td colspan='" . ($showDiscountColumn ? '4' : '3') . "' style='border: none;'></td>";

      if ($showDiscountColumn) {
        $html .= "
                 <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>Total a Pagar</td>
                 <td style='border: 1px solid #363636; font-size: 12px; text-align: right;'>$simbolfff $totalDolar</td>";
      } else {
        $html .= "
                 <td colspan='2' style='border: 1px solid #363636; font-size: 12px; text-align: center;'>Total a Pagar: $simbolfff $totalDolar</td>";
      }

      $html .= "
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
    // Usar COALESCE para priorizar nombres personalizados de la cotización
    $listaProd1 = $this->service->obtenerDetalleCotizacion($coti);

    $datoVenta = $this->service->obtenerCotizacion($coti);

    $datoEmpresa = $this->service->obtenerEmpresaPorCotizacion($coti);

    $resultC = $this->service->obtenerCliente($datoVenta['id_cliente']);

    if (!$datoEmpresa) {
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }

    $usuario_actual = $this->service->obtenerUsuario($datoVenta['id_usuario']) ?: [];
    
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);
    $tipo_pagoC = $datoVenta["id_tipo_pago"] == '1' ? 'CONTADO' : 'CREDITO';
    $tabla_cuotas = '';

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $resulTempCuo = $this->service->obtenerCuotas($coti);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $montoCuota = $datoVenta['moneda'] == 2 ? $cuotTemp['monto'] / $datoVenta['cm_tc'] : $cuotTemp['monto'];
        $tempMonto = Tools::money($montoCuota);
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

    // Calcular totales - CORRECCIÓN: Considerar si aplica IGV
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;

    // El total YA tiene IGV incluido (precios de almacén)
    if ($datoVenta['aplicar_igv'] == 1) {
      $totalOpgravado = $totalConDescuento / 1.18;
      $igv = $totalOpgravado * 0.18;
    } else {
      $totalOpgravado = $totalConDescuento / 1.18;
      $igv = 0;
      $totalConDescuento = $totalOpgravado;
    }

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
            <img style='max-width: 200px;' src='" . ImageStorage::url('empresas', $datoEmpresa['logo']) . "'>
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
    // Usar COALESCE para priorizar nombres personalizados de la cotización
    $listaProd1 = $this->service->obtenerDetalleCotizacion($coti);

    $datoVenta = $this->service->obtenerCotizacion($coti);

    $datoEmpresa = $this->service->obtenerEmpresaPorCotizacion($coti);

    $resultC = $this->service->obtenerCliente($datoVenta['id_cliente']);

    if (!$datoEmpresa) {
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }

    $usuario_actual = $this->service->obtenerUsuario($datoVenta['id_usuario']) ?: [];
    
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);
    $tipo_pagoC = '';

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $resulTempCuo = $this->service->obtenerCuotas($coti);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $montoCuota = $datoVenta['moneda'] == 2 ? $cuotTemp['monto'] / $datoVenta['cm_tc'] : $cuotTemp['monto'];
        $tempMonto = Tools::money($montoCuota);
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

    // Calcular totales - CORRECCIÓN: Considerar si aplica IGV
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;

    // El total YA tiene IGV incluido (precios de almacén)
    if ($datoVenta['aplicar_igv'] == 1) {
      $totalOpgravado = $totalConDescuento / 1.18;
      $igv = $totalOpgravado * 0.18;
    } else {
      $totalOpgravado = $totalConDescuento / 1.18;
      $igv = 0;
      $totalConDescuento = $totalOpgravado;
    }

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
            <img style='max-width: 60mm;' src='" . ImageStorage::url('empresas', $datoEmpresa['logo']) . "'>
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
    // Usar COALESCE para priorizar nombres personalizados de la cotización
    $listaProd1 = $this->service->obtenerDetalleCotizacion($coti);

    $datoVenta = $this->service->obtenerCotizacion($coti);

    $datoEmpresa = $this->service->obtenerEmpresaPorCotizacion($coti);

    $resultC = $this->service->obtenerCliente($datoVenta['id_cliente']);

    if (!$datoEmpresa) {
      throw new Exception("No se encontró la información de la empresa para esta cotización");
    }

    $usuario_actual = $this->service->obtenerUsuario($datoVenta['id_usuario']) ?: [];
    
    $fecha_emision = Tools::formatoFechaVisual($datoVenta['fecha']);
    $tipo_pagoC = '';

    if ($datoVenta["id_tipo_pago"] == '2') {
      $rowTempCuo = '';
      $resulTempCuo = $this->service->obtenerCuotas($coti);
      $contadorCuota = 0;

      foreach ($resulTempCuo as $cuotTemp) {
        $contadorCuota++;
        $tempNum = Tools::numeroParaDocumento($contadorCuota, 2);
        $tempFecha = Tools::formatoFechaVisual($cuotTemp['fecha']);
        $montoCuota = $datoVenta['moneda'] == 2 ? $cuotTemp['monto'] / $datoVenta['cm_tc'] : $cuotTemp['monto'];
        $tempMonto = Tools::money($montoCuota);
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

    // Calcular totales - CORRECCIÓN: Considerar si aplica IGV
    $descuentoGeneral = isset($datoVenta['descuento']) ? $datoVenta['descuento'] : 0;
    $montoDescuento = ($total * $descuentoGeneral) / 100;
    $totalConDescuento = $total - $montoDescuento;

    // El total YA tiene IGV incluido (precios de almacén)
    if ($datoVenta['aplicar_igv'] == 1) {
      $totalOpgravado = $totalConDescuento / 1.18;
      $igv = $totalOpgravado * 0.18;
    } else {
      $totalOpgravado = $totalConDescuento / 1.18;
      $igv = 0;
      $totalConDescuento = $totalOpgravado;
    }

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
            <img style='max-width: 40mm;' src='" . ImageStorage::url('empresas', $datoEmpresa['logo']) . "'>
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
