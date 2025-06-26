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
      $S_N = $datosGuia['serie'] . '-' . Tools::numeroParaDocumento($datosGuia['numero'], 6);
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

      // Escribir logo y cabecera
      $this->mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 35, 8, 150, 120);
      $this->mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 196, 130);

      $this->mpdf->WriteFixedPosHTML("<span style='font-family: Calibri, Helvetica Neue, sans-serif; font-size: 14px;margin: 1pt 2pt 3pt;'><strong>COMERCIAL & INDUSTRIAL J. V. C. S.A.C.
 </strong></span>", 25, 30, 210, 130);


      $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'><strong></strong> <span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 25, 36, 120, 130);

      $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'>Telf: {$datoEmpresa['telefono']} -Email:{$datoEmpresa['email']} </span>", 25, 39, 210, 130);

      $this->mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'> Web: https://industriajvcsac.com/</span>", 25, 42, 210, 130);


      // Generar filas de productos
      $rowHTML = '';
      $conradorRow = 1;
      while ($itemProd = $listaProductos->fetch_assoc()) {
        $rowHTML .= "
              <tr >
                    <td style='width: 5%; padding: 10px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                      $conradorRow 
                    </td>
                    <td style='width: 10%; padding: 10px; text-align: center; border-left: 1px solid #363636; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      {$itemProd['codigo']} 
                    </td>
                    <td style='width: 65%; padding: 10px;  border-left: 1px solid #363636; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      <strong style='font-size: 12px;'>{$itemProd['nombre']} </strong>
                    </td>
                    <td style='width: 10%; padding: 10px; text-align: center; border-left: 1px solid #363636; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      {$itemProd['unidad']} 
                    </td>
                    <td style='width: 10%; padding: 10px; text-align: center; border-left: 1px solid #363636; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px;'>
                      {$itemProd['cantidad']} 
                    </td>
                  </tr>";
        $conradorRow++;
      }

      // Generar HTML principal
      $html = "
      
      <div style='width: 100%;padding-top: 170px; overflow: hidden;clear: both;'>
              <!-- Sección Destinatario -->
            
      <div style='width: 100%; padding: 10px; border: 1px solid black; border-radius: 10px; margin-bottom: 30px; overflow: hidden;'>
    <table style='width: 100%; border-collapse: collapse; margin: -10px;'>
        <tr>
            <td style='width: 16.66%; padding: 8px; text-align: center; border-right: 1px solid black;'>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Fecha de Emisión:</strong>
                <span style='font-size: 10px;'>{$fechaEmision}</span>
            </td>
            <td style='width: 16.66%; ;padding: 8px; text-align: center;  border-right: 1px solid black;'>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Fecha de Traslado:</strong>
                <span style='font-size: 10px;'>{$fechaEmision}</span>
            </td>
            <td style='width: 16.66%; padding: 8px; text-align: center; ; border-right: 1px solid black;'>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Docs. Referencia:</strong>
                <span style='font-size: 10px;'>-</span>
            </td>
            <td style='width: 17.66%; padding: 8px; text-align: center;  border-right: 1px solid black;'>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Motivo de traslado:</strong>
                <span style='font-size: 10px;'>{$datosGuia['motivo_traslado_nombre']}</span>
            </td>
            <td style='width: 16.66%; padding: 8px; text-align: center;  border-right: 1px solid black;'>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Mod. Transporte:</strong>
                <span style='font-size: 10px;'>Transporte privado</span>
            </td>
            <td style='width: 16.66%; padding: 8px; text-align: center;'>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'>Orden de Compra</strong>
                <strong style='font-size: 10px; display: block; margin-bottom: 4px;'> Nro:</strong> <br/>
                <span style='font-size: 10px;'>COT 128323 JVC</span>
            </td>
        </tr>
    </table>
</div>

<div style='width: 100%; padding: 0; border: 1px solid black; border-radius: 10px; margin-bottom: 30px; overflow: hidden;background-color: #CA3438;'>
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

<div style='width: 100%; padding: 0; border: 1px solid black; border-radius: 10px; margin-bottom: 20px; overflow: hidden; background-color: #CA3438; '>
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
                <td style='width: 65%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>DESCRIPCION </strong>
            </td>
                <td style='width: 10%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>UNIDAD </strong>
            </td>
                <td style='width: 10%; padding: 8px; text-align: center; font-family: Calibri, Helvetica Neue, sans-serif; font-size: 10.5px; '>
                <strong style='color: #ffffff;'>CANTIDAD </strong>
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
              <div style='width: 100%; margin-top: 20px;'>
                  <table style='width:100%;border: 0.5px solid black;'>
                      <tr>
                          <td style='font-size: 11px;padding: 5px;'><strong>Observaciones: </strong>{$datosGuia['observaciones']}</td>
                      </tr>
                  </table>
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