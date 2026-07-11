<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';

use Endroid\QrCode\QrCode;

class ReporteGuiaController extends Controller
{
  private $mpdf;
  private $conexion;

  public function __construct()
  {
    $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 0]);
    $this->conexion = (new Conexion())->getConexion();
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
      } elseif ($datosGuia['id_cotizacion']) {
        // Para guías asociadas a cotización
        $sql = "SELECT cot.*, c.*
                      FROM cotizaciones cot
                      JOIN clientes c ON cot.id_cliente = c.id_cliente
                      WHERE cot.cotizacion_id = " . $datosGuia['id_cotizacion'];
        $datoCotizacion = $this->conexion->query($sql)->fetch_assoc();

        if ($datoCotizacion) {
          $nombreCliente = $datoCotizacion['datos'];
          $numDoc = strlen($datoCotizacion["documento"]) > 7 ? $datoCotizacion["documento"] : '';
        } else {
          $nombreCliente = $datosGuia['destinatario_nombre'] ?: 'N/A';
          $numDoc = $datosGuia['destinatario_documento'] ?: '';
        }
      } elseif ($datosGuia['id_cotizacion_taller']) {
        // ✅ NUEVO: Para guías de cotización taller
        $sql = "SELECT tc.*, c.* 
                      FROM taller_cotizaciones tc 
                      JOIN clientes c ON tc.id_cliente = c.id_cliente 
                      WHERE tc.id_cotizacion = " . $datosGuia['id_cotizacion_taller'];
        $datoTaller = $this->conexion->query($sql)->fetch_assoc();

        if ($datoTaller) {
          $nombreCliente = $datoTaller['datos'] ?: $datoTaller['nombre'];
          $numDoc = strlen($datoTaller["documento"]) > 7 ? $datoTaller["documento"] : '';
        } else {
          // Fallback: usar datos directos de la guía
          $nombreCliente = $datosGuia['destinatario_nombre'];
          $numDoc = $datosGuia['destinatario_documento'];
        }
      } else {
        // Para guías manuales
        $nombreCliente = $datosGuia['destinatario_nombre'] ?: 'N/A';
        $numDoc = $datosGuia['destinatario_documento'] ?: '';
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
          $qrImage = '<img style="width: 140px; height: 140px;" src="data:image/png;base64,' . $imageData . '">';
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
          $qrImage = '<img style="width: 140px; height: 140px;" src="data:image/png;base64,' . $imageData . '">';
        } catch (Exception $e) {
          $qrImage = '';
        }
      }
      // Obtener datos del usuario que creó la guía desde la BD
      $usuario_creador = 'Sin Asignar';
      $codigo_usuario = 'N/A';

      // Obtener el id_usuario de la guía
      $sql_guia_usuario = "SELECT id_usuario FROM guia_remision WHERE id_guia_remision = " . $datosGuia['id_guia_remision'];
      $result_guia_usuario = $this->conexion->query($sql_guia_usuario);

      if ($result_guia_usuario && $result_guia_usuario->num_rows > 0) {
        $guia_usuario = $result_guia_usuario->fetch_assoc();
        $id_usuario_guia = $guia_usuario['id_usuario'];

        if ($id_usuario_guia) {
          // Obtener datos del usuario desde la tabla usuarios
          $sql_usuario = "SELECT nombres, apellidos FROM usuarios WHERE usuario_id = " . $id_usuario_guia;
          $result_usuario = $this->conexion->query($sql_usuario);

          if ($result_usuario && $result_usuario->num_rows > 0) {
            $usuario_data = $result_usuario->fetch_assoc();
            $nombre_completo = trim($usuario_data['nombres'] . ' ' . ($usuario_data['apellidos'] ?: ''));
            $usuario_creador = $nombre_completo;

            // Generar código de usuario con iniciales
            $palabras = explode(' ', $nombre_completo);
            $codigo_usuario = '';
            foreach ($palabras as $palabra) {
              if (!empty($palabra)) {
                $codigo_usuario .= strtoupper(substr($palabra, 0, 1));
              }
            }
            if (empty($codigo_usuario)) {
              $codigo_usuario = 'N/A';
            }
          }
        }
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

      // ✅ CONSULTA CORREGIDA: Verificar si tiene equipos en la tabla guia_equipos
      $consultaEquipos = "SELECT COUNT(*) as total FROM guia_equipos WHERE id_guia = " . $guia;
      $resultadoEquipos = $this->conexion->query($consultaEquipos);
      $tieneEquipos = $resultadoEquipos->fetch_assoc()['total'] > 0;

      if ($tieneEquipos) {
        // Guía de cotización de taller - incluir información de equipos
        $query = "SELECT gd.*,
                    CASE 
                      WHEN gd.id_producto IS NOT NULL THEN p.nombre 
                      WHEN gd.id_repuesto IS NOT NULL THEN r.nombre 
                      ELSE gd.detalles
                    END as nombre,
                    CASE 
                      WHEN gd.id_producto IS NOT NULL THEN p.codigo 
                      WHEN gd.id_repuesto IS NOT NULL THEN r.codigo 
                      ELSE ''
                    END as codigo,
                    gd.tipo_item,
                    -- Información del equipo asociado CORREGIDO
                    ge.marca as equipo_marca,
                    ge.equipo as equipo_nombre,
                    ge.modelo as equipo_modelo,
                    ge.numero_serie as equipo_serie,
                    ge.id_guia_equipo
                  FROM guia_detalles gd
                  LEFT JOIN productos p ON gd.id_producto = p.$idColumnName
                  LEFT JOIN repuestos r ON gd.id_repuesto = r.id_repuesto
                  LEFT JOIN guia_equipos ge ON gd.id_guia_equipo = ge.id_guia_equipo
                  WHERE gd.id_guia = ?
                  ORDER BY ge.id_guia_equipo, gd.guia_detalle_id";
      } else {
        // Guía normal - comportamiento original
        $query = "SELECT gd.*, p.nombre, p.codigo, 'producto' as tipo_item
                  FROM guia_detalles gd
                  LEFT JOIN productos p ON gd.id_producto = p.$idColumnName
                  WHERE gd.id_guia = ?";
      }

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

      // Escribir encabezado de la empresa
      PdfHelper::escribirEncabezadoEmpresa($this->mpdf, $datoEmpresa, $htmlCuadroHead);
      // ✅ NUEVA LÓGICA: Generar filas de productos con equipos para guías de taller
      $rowHTML = '';
      $conradorRow = 1;

      if ($tieneEquipos) {
        // Guía de taller - agrupar por equipos usando id_guia_equipo
        $equiposYaImpresos = [];

        while ($itemProd = $listaProductos->fetch_assoc()) {
          // ✅ PRIMERO: Mostrar el producto/repuesto
          $descripcionProducto = $itemProd['detalles'];

          $rowHTML .= "
                <tr>
                      <td style='width: 5%; padding: 10px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                        $conradorRow
                      </td>
                      <td style='width: 10%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                        {$itemProd['codigo']} 
                      </td>
                      <td style='width: 71%; padding: 10px; text-align: left; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                        <strong>$descripcionProducto</strong>
                      </td>
                      <td style='width: 8%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                        {$itemProd['unidad']} 
                      </td>
                      <td style='width: 6%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                        {$itemProd['cantidad']} 
                      </td>
                    </tr>";

          $conradorRow++;

          // ✅ SEGUNDO: Mostrar el equipo asociado (si existe y no se ha mostrado ya)
          $equipoId = $itemProd['id_guia_equipo'];

          if ($itemProd['equipo_marca'] && !in_array($equipoId, $equiposYaImpresos)) {
            // Mostrar fila del equipo después del producto
            $equipoInfo = " EQUIPO: {$itemProd['equipo_marca']} {$itemProd['equipo_nombre']} - Modelo: {$itemProd['equipo_modelo']} - Serie: {$itemProd['equipo_serie']}";

            $rowHTML .= "
                  <tr style='background-color: #f8f9fa;'>
                        <td style='width: 5%; padding: 10px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; font-weight: bold;'>
                          
                        </td>
                        <td style='width: 10%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; font-weight: bold;'>
                          
                        </td>
                        <td style='width: 71%; padding: 10px; text-align: left; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; font-weight: bold; color: #666666;'>
                            $equipoInfo
                        </td>
                        <td style='width: 8%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                          
                        </td>
                        <td style='width: 6%; padding: 10px; text-align: center; border-left: 1px solid #ffffff; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                          
                        </td>
                      </tr>";

            $equiposYaImpresos[] = $equipoId;
          }
        }
      } else {
        // Guía normal - comportamiento original
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
            <span style='font-size: 10px;'>" . (isset($datosGuia['ref_orden_compra']) && !empty($datosGuia['ref_orden_compra']) ? $datosGuia['ref_orden_compra'] : '-') . "</span>
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
                  <div style='float: left; width: 150px; text-align: center; vertical-align: top; padding: 0;'>
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
      $dist = 'I';
      $pdfFilename = $S_N . '-' . $datoEmpresa['razon_social'] . '.pdf';
      if ($dist == 'I') {
        $this->mpdf->Output($pdfFilename, $dist);
      } elseif ($dist == 'F') {
        $this->mpdf->Output(base64_decode((is_string($nombreXML) ? $nombreXML : '')), $dist);
      }

    } catch (Exception $e) {
      // Manejar cualquier error que pueda ocurrir
      error_log("Error en guiaRemision: " . $e->getMessage());
      echo "Error al generar el PDF: " . $e->getMessage();
    }
  }
}