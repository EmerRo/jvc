<?php

require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';
require_once 'app/helpers/ImageStorage.php';

class GenerarReporte extends Controller
{
    private $conexion;
    /* private $mpdf; */

    public function __construct()
    {
        /* $this->mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']); */
        $this->conexion = (new Conexion())->getConexion();
    }

    public function ingresosEgresos($id)
    {
        $mpdf = new \Mpdf\Mpdf([
            'margin_bottom' => 5,
            'margin_top' => 10,
            'margin_left' => 4,
            'margin_right' => 4,
            'mode' => 'utf-8',
        ]);

        $empresa = $this->conexion->query("SELECT * FROM empresas WHERE id_empresa = '{$_SESSION['id_empresa']}'")->fetch_assoc();

        $sql = "SELECT ingreso_egreso.*, productos.nombre, productos.codigo, 
                       IF(ingreso_egreso.tipo = 'e', 'Egreso', 'Ingreso') AS tipoIntercambio 
                FROM ingreso_egreso 
                JOIN productos ON ingreso_egreso.id_producto = productos.id_producto 
                WHERE intercambio_id = '$id'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        $sql = "SELECT * FROM usuarios WHERE usuario_id = {$result['id_usuario']}";
        $resul2 = $this->conexion->query($sql)->fetch_assoc();

        // Obtener las observaciones o mostrar un texto por defecto si no hay
        $observaciones = !empty($result['observaciones']) ? $result['observaciones'] : 'Sin observaciones';

        $dominio = DOMINIO;
        $rowHmtl = "<tr>
            <td style='border-bottom:1px solid black;font-size: 11px'>{$result['cantidad']}</td>
            <td style='border-bottom:1px solid black;font-size: 11px'>Almacen {$result['almacen_ingreso']}</td>
            <td style='border-bottom:1px solid black;font-size: 11px'>Almacen {$result['almacen_egreso']}</td>
            <td style='border-bottom:1px solid black;font-size: 11px'>{$resul2['nombres']}</td>
        </tr>";

        $html = "
        <div style='width: 100%'>
            <table style='width:100%;margin-bottom: 10px'>
                <tr>
                    <td align='center'>
                        <img style='max-width: 85%;' src='" . ImageStorage::url('empresas', $empresa['logo']) . "'>
                    </td>
                </tr>
            </table>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 13px;font-weight: bold'>{$empresa['razon_social']}</span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>RUC: {$empresa['ruc']}</span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>{$empresa['direccion']}</span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>{$empresa['telefono']}</span>
            </div>
            <hr>
            <div style='width: 100%;text-align: center'>
                <table style='width:100%'>
                    <tr>
                        <td style='font-size: 11px;width: 25%'><strong>Código:</strong></td>
                        <td style='font-size: 11px;'>{$result['codigo']}</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px;width: 25%'><strong>Producto:</strong></td>
                        <td style='font-size: 11px;'>{$result['nombre']}</td>
                    </tr>
                    <tr>
                        <td style='font-size: 11px;width: 25%'><strong>Tipo:</strong></td>
                        <td style='font-size: 11px;'>{$result['tipoIntercambio']}</td>
                    </tr>
                </table>
            </div>
            <div style='width: 100%;'>
                <span style='font-size: 13px;'>---------------------- Detalle -----------------------</span>
            </div>
            <div style='width: 100%;'>
                <table style='width: 100%; text-align: center;'>
                    <thead>
                        <tr>
                            <th style='border-bottom:1px solid black;font-size: 11px'>Cantidad</th>
                            <th style='border-bottom:1px solid black;font-size: 11px'>Ingreso</th>
                            <th style='border-bottom:1px solid black;font-size: 11px'>Salida</th>
                            <th style='border-bottom:1px solid black;font-size: 11px'>Hecho por</th>
                        </tr>
                    </thead>
                    <tbody>
                        $rowHmtl
                    </tbody>
                </table>
            </div>
            <div style='width: 100%;'>
                <span style='font-size: 12px'><b>Observaciones:</b></span>
                <p style='font-size: 11px;margin-top:5px;margin-bottom:10px'>{$observaciones}</p>
            </div>
            <div style='width: 100%;text-align: center; margin-top:40px'>
                <span style='font-size: 12px'>Representación impresa del Intercambio de Productos <br>Este documento puede ser validado en $dominio</span>
            </div>
            <div style='width: 100%;text-align: center'>
                <span style='font-size: 12px'>Gracias por su preferencia....</span>
            </div>
        </div>";

        $mpdf->AddPageByArray([
            'orientation' => 'P',
            'newformat' => [80, 240 - 20]
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Reporte Intercambio-Productos.pdf', 'I');
    }

    public function generarExcelProducto()
    {
        $texto = $_GET['texto'];
        $sql = "select codigo, MAX(nombre) AS nombre, MAX(descripcion) AS descripcion, /* MAX(detalle) AS detalle, */
 MAX(costo) as costo,
       SUM(CASE WHEN almacen = 1 THEN cantidad ELSE 0 END) AS cantidad1,
       SUM(CASE WHEN almacen = 2 THEN cantidad ELSE 0 END) AS cantidad2,
       SUM(CASE WHEN almacen = 3 THEN cantidad ELSE 0 END) AS cantidad3
        from productos where (descripcion like '%$texto%' or codigo like '%$texto%' or nombre like '%$texto%') AND estado = '1' GROUP BY codigo ORDER BY codigo;";

        $result = $this->conexion->query($sql);

        $tbody = '';
        foreach ($result as $fila) {
            $tbody .= '
            <tr>
                <td>' . $fila['codigo'] . '</td>
                <td>' . $fila['nombre'] . '</td>
                <td>' . $fila['costo'] . '</td>
                <td>' . $fila['cantidad1'] . '</td>
                <td>' . $fila['cantidad2'] . '</td>
                <td>' . $fila['cantidad3'] . '</td>
            </tr>';
        }

        $tabla = "
        <table>
            <tr>
                    <th style='background-color: #90BFEB;width:10px'>Codigo</th>
                    <th style='background-color: #90BFEB;width:50px'>Nombre</th>
                    <th style='background-color: #90BFEB;width:7px'>Costo</th>
                    <th style='background-color: #90BFEB;width:7px'>CNT A1</th>
                    <th style='background-color: #90BFEB;width:7px'>CNT A2</th>
                    <th style='background-color: #90BFEB;width:7px'>CNT A3</th>
            </tr>
            <tbody>
                " . $tbody . '
            </tbody>
        </table>';

        // Generar nombre único con fecha y hora
        $nombre_exel = 'reporte-productos-stock-' . date('Y-m-d-His') . '.xlsx';

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Configurar headers para descarga directa sin guardar en servidor
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombre_exel . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Enviar directamente al navegador (no guardar en servidor)
        $writer->save('php://output');
        exit;
    }

    public function generarExcel($id)
    {
        $explodeFecha = explode('-', $id);
        $anio = $explodeFecha[0];
        $mes = $explodeFecha[1];
        $sql = 'SELECT *,CASE
        WHEN v1.cnt_pv > 0 THEN "VENTA DE MERCADERIA"
        ELSE "VENTA DE SERVICIO"
        END GLOSA
        FROM
        (SELECT v.id_venta,v.id_tido,ds.abreviatura,CONCAT(v.serie , "-" ,LPAD(v.numero,8,0)) AS documento, 
            v.fecha_emision,v.fecha_vencimiento,
            IF(ISNULL(c.documento), "", c.documento) AS codigocliente,IF(ISNULL(c.datos), "", c.datos) AS datos,
            IF(v.enviado_sunat = 1, "Si", "No") AS enviado,v.total,
            IF(ISNULL(gr.serie) ,"",CONCAT(gr.serie , "-" ,LPAD(v.numero,8,0))) AS guia,
            (
        SELECT COUNT(*) FROM productos_ventas pv WHERE pv.id_venta= v.id_venta
        ) cnt_pv,
        (SELECT COUNT(*) FROM ventas_servicios vs WHERE vs.id_venta= v.id_venta) cnt_sv
            FROM ventas AS v 
            JOIN documentos_sunat AS ds
            ON ds.id_tido=v.id_tido
            LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
            LEFT JOIN guia_remision AS gr ON gr.id_venta=v.id_venta
            WHERE  YEAR(v.fecha_emision) =' . $anio . ' AND MONTH(v.fecha_emision) = ' . $mes . ' AND v.id_empresa=' . $_SESSION['id_empresa'] . ')v1';

        $result = $this->conexion->query($sql);
        $tabla = '';
        $tbody = '';
        foreach ($result as $fila) {
            if ($fila['id_tido'] != '1' && $fila['id_tido'] != '2') {
                continue;
            }
            $tbody .= '
            <tr>
                <td>' . $fila['id_venta'] . '</td>            
                <td>' . $fila['abreviatura'] . '</td>            
                <td>' . $fila['documento'] . '</td>            
                <td>' . $fila['fecha_emision'] . '</td>            
                <td>' . $fila['fecha_vencimiento'] . '</td>            
                <td style="text-align:center">' . $fila['codigocliente'] . '</td>            
                <td>' . $fila['datos'] . '</td>            
                <td>' . $fila['enviado'] . '</td>            
                <td>S</td>            
                <td>' . $fila['total'] . '</td>            
                <td>' . $fila['total'] . '</td>            
                <td>' . $fila['GLOSA'] . '</td>            
                      
                <td>' . $fila['total'] . '</td>             
                <td>E</td>             
                <td>' . $fila['guia'] . '</td>             
                <td>Oficina</td>             
                <td>' . $fila['fecha_emision'] . '</td>             
                <td>' . $fila['fecha_emision'] . '</td>             
                <td>admin</td>             
                <td></td>             
                <td></td>             
                <td></td>             
            </tr>';
        }

        $tabla .= "
        <table>
            <tr>
                    <th style='background-color: #90BFEB;width:10px'>Nº Registro</th>
                    <th style='background-color: #90BFEB;width:10px'>Tipo Doc.</th>
                    <th style='background-color: #90BFEB;width:15px'>Documento</th>
                    <th style='background-color: #90BFEB;width:15px'>Fecha Registro</th>
                    <th style='background-color: #90BFEB;width:15px'>F. Vencimiento</th>
                    <th style='background-color: #90BFEB;width:16px;text-align:center'>Codigo Cliente</th>
                    <th style='background-color: #90BFEB;width:85px'>Nombre Cliente</th>
                    <th style='background-color: #90BFEB;width:7px'>Sunat</th>
                    <th style='background-color: #90BFEB;width:7px'>Moneda</th>
                    <th style='background-color: #90BFEB;width:8px'>Total</th>
                    <th style='background-color: #90BFEB;width:8px'>Saldo</th>
                    <th style='background-color: #90BFEB;width:22px'>Glosa</th>
                    <th style='background-color: #90BFEB;width:8px'>Total Convertido</th>
                    <th style='background-color: #90BFEB;width:5px'>E</th>
                    <th style='background-color: #90BFEB;width:14px'>Con Guía</th>
                    <th style='background-color: #90BFEB;width:10px'>Vendedor</th>
                    <th style='background-color: #90BFEB;width:12px'>Orden Compra</th>
                    <th style='background-color: #90BFEB;width:12px'>Fecha Crea.</th>
                    <th style='background-color: #90BFEB;width:10px'>Usuario Crea.</th>
                    <th style='background-color: #90BFEB;width:10px'>Fecha Act.</th>
                    <th style='background-color: #90BFEB;width:10px'>Usuario Act.</th>
                    <th style='background-color: #90BFEB;width:10px'>Historial</th>
            </tr>
            <tbody>
                " . $tbody . '
            </tbody>
        </table>';

        /* return ($arrayRes); */
        $nombre_exel = 'Venta de ' . $anio . '-' . $mes . '.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function generarExcelRVTA($fecha)
    {
        $explodeFecha = explode('-', $fecha);
        $anio = $explodeFecha[0];
        $mes = $explodeFecha[1];
        $sql = 'SELECT v.id_tido,v.id_venta,v.fecha_emision,v.fecha_vencimiento,ds.nombre AS tipoDocPago,v.serie,v.numero AS numeroVenta,v.enviado_sunat,v.igv,
       IF(v.enviado_sunat = 0,"No enviado","Enviado") AS enviadoSunat,
        (CASE 
        WHEN LENGTH(c.documento) = 8 THEN "DNI" 
         WHEN LENGTH(c.documento) = 11 THEN "RUC"
        END ) AS tipoDocumento,
        c.documento,c.datos AS cliente,v.total
         FROM ventas v 
        LEFT JOIN documentos_sunat ds ON v.id_tido=ds.id_tido
        LEFT JOIN clientes c ON c.id_cliente=v.id_cliente 
        WHERE  YEAR(v.fecha_emision) =' . $anio . ' AND MONTH(v.fecha_emision) = ' . $mes . ' AND v.id_empresa=' . $_SESSION['id_empresa'];

        /*
         * var_dump($sql);
         * die();
         */
        $result = $this->conexion->query($sql);
        $tabla = '';
        $tbody = '';
        $totalOpgravado = 0;
        /* $total = 0; */
        foreach ($result as $fila) {
            if ($fila['id_tido'] != '1' && $fila['id_tido'] != '2') {
                continue;
            }
            $igv = $fila['total'] / ($fila['igv'] + 1) * $fila['igv'];
            $totalOpgravado = $fila['total'] - $igv;
            $total = number_format((float) $fila['total'], 2, '.', '');
            $igv = number_format($igv, 2, '.', ',');
            $totalOpgravado = number_format($totalOpgravado, 2, '.', ',');
            $style = '';
            if ($fila['enviado_sunat'] == '0') {
                $style = 'red';
            } else {
                $style = 'green';
            }
            $tbody .= '
               <tr>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['id_venta'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['fecha_emision'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['fecha_vencimiento'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['tipoDocPago'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['serie'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['numeroVenta'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['tipoDocumento'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">' . $fila['documento'] . '</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">' . $fila['cliente'] . '</td>
               <td style="font-size: 10px;border:1px solid black;">0</td>
               <td style="font-size: 10px;border:1px solid black;">' . $totalOpgravado . '</td>
               <td style="font-size: 10px;border:1px solid black;">0</td>
               <td style="font-size: 10px;border:1px solid black;">0</td>
               <td style="font-size: 10px;border:1px solid black;"colspan="2"></td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">' . ($fila['igv'] * 100) . ' %</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">0</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2">' . $total . '</td>
               <td style="font-size: 10px;border:1px solid black;" colspan="2"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;"></td>
               <td style="font-size: 10px;border:1px solid black;width:10px;background-color:' . $style . '">' . $fila['enviadoSunat'] . '</td>
               </tr>
                ';
        }
        $tabla = '  <table style="width:100%">
        <tr>
        </tr>
        <tr>
            <th rowspan="3" colspan="1" style="font-weight:bold;border:1px solid black;text-align: center;font-size: 10px;width:20px;word-wrap: break-word">NUMERO DEL REGISTRO O CODIGO UNICO DE OPERACIÓN</th>
            <th rowspan="2" colspan="5" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">COMPROBANTE DE PAGO O DOCUMENTO</th>
            <th colspan="4" rowspan="1" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">INFORMACION DEL CLIENTE</th>
            <th rowspan="3" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">VALOR FACTURADO DE LA EXPORTACION</th>
            <th rowspan="3" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">BASE IMPONIBLE DE LA OPERACIÓN GRAVADA</th>
            <th rowspan="2" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">IMPORTE DE LA OPERACIÓN EXONERADA O INAFECTA</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">ISC</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">IGV Y/O IPM</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">OTROS TRIBUTOS Y CARGOS QUE NO FORMAN PARTE DE LA BASE IMPONIBLE</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">IMPORTE TOTAL DEL COMPROBANTE DE PAGO</th>
            <th rowspan="3" colspan="2" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;">TIPO DE CAMBIO</th>
            <th rowspan="2" colspan="4" style="text-align: center;font-weight:bold;border:1px solid black;font-size: 10px;width:20px;text-align: center;word-wrap: break-word">REF. COMPROBANTE DE PAGO QUE SE MODIFICA</th>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 10px;font-weight:bold;border:1px solid black;width:20px;text-align: center;word-wrap: break-word">DOC. IDENTIDAD</td>
            <td rowspan="2" colspan="2" style="font-size: 10px;font-weight:bold;border:1px solid black;width:30px;text-align: center;word-wrap: break-word">APELLIDOS Y NOMBRES O RAZON SOCIAL</td>
          
        </tr>
        <tr>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">FECHA DE EMISION</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:13px;text-align: center;word-wrap: break-word">FECHA DE VCTO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">TIPO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">SERIE</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">NUMERO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;width:8px;">TIPO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;width:13px;">NUMERO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;">EXONERADA</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;text-align: center;">INAFECTA</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:15px;text-align: center;">FECHA EMISION</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;">TIPO</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;">SERIE</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;">NUMERO</td>
        </tr>
       ' . $tbody . '
    </table>';

        $nombre_exel = 'RVTA ' . $anio . '-' . $mes . '.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
    }

    public function generarExcelProductoImporte()
    {
        $sql = "SELECT
                    nombre,
                    descripcion AS producto,
                    cantidad AS cnt,
                    costo,
                    precio_unidad,
                    precio,
                    precio_mayor,
                    precio_menor,
                    precio4,
                    codsunat,
                    almacen,
                    codigo,
                    detalle
                FROM
                    productos
                WHERE almacen = '{$_SESSION['sucursal']}' AND estado = 1
                ORDER BY codigo ASC";

        $result = $this->conexion->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Crear el Excel usando PhpSpreadsheet directamente para mejor control
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar encabezados
        $headers = [
            'A1' => 'Producto',
            'B1' => 'Detalle',
            'C1' => 'Cantidad',
            'D1' => 'Costo',
            'E1' => 'Precio Venta',
            'F1' => 'Precio Distribuidor',
            'G1' => 'Precio Mayorista',
            'H1' => 'Almacén',
            'I1' => 'Código'
        ];

        // Establecer encabezados
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Configurar estilos de encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '90BFEB']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Configurar anchos de columnas para evitar deformación
        $sheet->getColumnDimension('A')->setWidth(35);  // Producto - más ancho
        $sheet->getColumnDimension('B')->setWidth(50);  // Detalle - el más ancho para contenido largo
        $sheet->getColumnDimension('C')->setWidth(10);  // Cantidad
        $sheet->getColumnDimension('D')->setWidth(12);  // Costo
        $sheet->getColumnDimension('E')->setWidth(15);  // Precio Venta
        $sheet->getColumnDimension('F')->setWidth(18);  // Precio Distribuidor
        $sheet->getColumnDimension('G')->setWidth(18);  // Precio Mayorista
        $sheet->getColumnDimension('H')->setWidth(10);  // Almacén
        $sheet->getColumnDimension('I')->setWidth(15);  // Código

        // Llenar datos
        $row = 2;  // Empezar desde la fila 2 (después de encabezados)
        foreach ($data as $fila) {
            // Preservar espacios y saltos de línea en el detalle
            $detalle = $fila['detalle'] ? $fila['detalle'] : '';

            $sheet->setCellValue('A' . $row, $fila['nombre']);
            $sheet->setCellValue('B' . $row, $detalle);
            $sheet->setCellValue('C' . $row, $fila['cnt']);
            $sheet->setCellValue('D' . $row, $fila['costo']);
            $sheet->setCellValue('E' . $row, $fila['precio']);
            $sheet->setCellValue('F' . $row, $fila['precio_mayor']);
            $sheet->setCellValue('G' . $row, $fila['precio_menor']);
            $sheet->setCellValue('H' . $row, $fila['almacen']);
            $sheet->setCellValue('I' . $row, $fila['codigo']);

            // Configurar wrap text para Producto y Detalle, y ajustar altura de fila
            $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(-1);  // Auto-ajustar altura

            // Aplicar bordes a toda la fila
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'font' => ['size' => 10]
            ]);

            $row++;
        }

        // Configurar alineación para columnas numéricas (horizontal y vertical)
        if ($row > 2) {
            $sheet
                ->getStyle('C2:H' . ($row - 1))
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        // Aplicar alineación vertical centrada a las columnas de texto también
        if ($row > 2) {
            $sheet
                ->getStyle('A2:B' . ($row - 1))
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet
                ->getStyle('I2:I' . ($row - 1))
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        // Descargar archivo directamente al navegador
        $nombre_exel = 'plantilla-productos-importar.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function generarExcelCaja($id)
    {
        // 1. Obtener la fecha de la caja
        $fecha_caja = null;
        $sql_fecha = 'SELECT fecha FROM caja_empresa WHERE caja_id = ?';
        $stmt_fecha = $this->conexion->prepare($sql_fecha);
        $stmt_fecha->bind_param('i', $id);
        if ($stmt_fecha->execute()) {
            $result_fecha = $stmt_fecha->get_result();
            if ($row_fecha = $result_fecha->fetch_assoc()) {
                $fecha_caja = $row_fecha['fecha'];
            }
        }
        $stmt_fecha->close();

        if (!$fecha_caja) {
            echo 'Error: No se pudo encontrar la caja con el ID proporcionado.';
            return;
        }

        $listaTotal = [];

        // 2. Obtener movimientos manuales de caja_chica
        $sql1 = 'SELECT * FROM caja_chica WHERE id_caja_empresa = ? ORDER BY caja_chica_id DESC';
        $stmt1 = $this->conexion->prepare($sql1);
        $stmt1->bind_param('i', $id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1) {
            while ($row = $result1->fetch_assoc()) {
                $listaTotal[] = [
                    'detalle' => $row['detalle'],
                    'salida' => $row['salida'],
                    'entrada' => $row['entrada'],
                    'hora' => $row['hora'],
                    'metodo' => $row['metodo'],
                    'documento' => $row['documento'] ?? ''
                ];
            }
        }
        $stmt1->close();

        // 3. Obtener ventas en efectivo de la fecha correcta
        $sql2 = "SELECT v.id_venta, v.fecha_emision, CONCAT(ds.abreviatura, ' | ', v.serie, ' - ', v.numero) AS detalle, 
            v.total AS entrada, ds.nombre as tipo_documento, v.serie, v.numero
            FROM ventas AS v
            LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
            WHERE v.id_empresa = ? AND v.sucursal = ? 
            AND v.medoto_pago_id = '10' AND v.fecha_emision = ?
            ORDER BY v.id_venta DESC";

        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->bind_param('iis', $_SESSION['id_empresa'], $_SESSION['sucursal'], $fecha_caja);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2) {
            while ($row2 = $result2->fetch_assoc()) {
                $listaTotal[] = [
                    'detalle' => $row2['detalle'],
                    'salida' => 0,
                    'entrada' => $row2['entrada'],
                    'hora' => '-',
                    'metodo' => 1,
                    'documento' => $row2['tipo_documento'] . ' ' . $row2['serie'] . '-' . $row2['numero']
                ];
            }
        }
        $stmt2->close();

        // 4. Generar el Excel
        $tabla = '';
        $tbody = '';
        foreach ($listaTotal as $i => $fila) {
            $index = $i + 1;
            $tipo = '';
            if ($fila['metodo'] == 1) {
                $tipo = 'Efectivo';
            } else if ($fila['metodo'] == 2) {
                $tipo = 'Tarjetas';
            } else {
                $tipo = 'Transferencias';
            }
            $tbody .= '
                    <tr>
                            <td style="font-size: 10px;border:1px solid black;">' . $index . '</td>
                            <td style="font-size: 10px;border:1px solid black;">' . htmlspecialchars($fila['detalle']) . '</td>
                            <td style="font-size: 10px;border:1px solid black;">' . $fila['hora'] . '</td>
                            <td style="font-size: 10px;border:1px solid black;">' . $fila['entrada'] . '</td>
                            <td style="font-size: 10px;border:1px solid black;">' . $fila['salida'] . '</td>
                            <td style="font-size: 10px;border:1px solid black;">' . $tipo . '</td>
                            <td style="font-size: 10px;border:1px solid black;">' . (htmlspecialchars($fila['documento']) ?: '-') . '</td>
                    </tr>
                    ';
        }
        $tabla = '  <table style="width:100%">
                    <tr>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">Item</td>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:13px;text-align: center;word-wrap: break-word">Detalle</td>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">Hora</td>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">Entrada</td>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">Salida</td>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:15px;text-align: center;">Metodo</td>
                        <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:20px;text-align: center;">Documento</td>
                    </tr>
                    ' . $tbody . '
                    </table>';

        $nombre_exel = 'cierreDeCaja_' . $id . '_' . date('Y-m-d') . '.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
    }

    public function reporteVentaPorVendedor()
    {
        // Obtener parámetros del POST o GET
        $vendedor_id = isset($_POST['vendedor']) ? $_POST['vendedor'] : (isset($_GET['vendedor']) ? $_GET['vendedor'] : '0');
        $rango_fechas = isset($_POST['rangoFechas']) ? $_POST['rangoFechas'] : (isset($_GET['rangoFechas']) ? $_GET['rangoFechas'] : '');

        // Procesar el rango de fechas
        $fecha_inicio = '';
        $fecha_fin = '';

        if (!empty($rango_fechas)) {
            $fechas = explode(' - ', $rango_fechas);
            if (count($fechas) == 2) {
                $fecha_inicio = trim($fechas[0]);
                $fecha_fin = trim($fechas[1]);
            }
        }

        // Construir la consulta SQL - Simplificada sin JOIN problemáticos
        $sql = "SELECT
                    c.cotizacion_id,
                    c.numero,
                    c.fecha,
                    c.total,
                    c.estado,
                    c.moneda,
                    c.id_usuario,
                    c.id_cliente,
                    COALESCE((SELECT CONCAT(u.nombres, ' ', COALESCE(u.apellidos, '')) FROM usuarios u WHERE u.usuario_id = c.id_usuario), 'Sin Vendedor') AS vendedor,
                    COALESCE((SELECT cl.documento FROM clientes cl WHERE cl.id_cliente = c.id_cliente), 'Sin RUC') AS documento,
                    COALESCE((SELECT cl.datos FROM clientes cl WHERE cl.id_cliente = c.id_cliente), 'Sin Cliente') AS cliente,
                    CASE
                        WHEN c.estado = '0' THEN 'No Vendido'
                        WHEN c.estado = '1' THEN 'Vendido'
                        WHEN c.estado = '2' THEN 'Facturado'
                        ELSE 'Desconocido'
                    END AS estado_texto
                FROM cotizaciones c
                WHERE c.id_empresa = '12'";

        // Agregar filtros
        if ($vendedor_id != '0' && !empty($vendedor_id)) {
            $sql .= " AND c.id_usuario = '$vendedor_id'";
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $sql .= " AND c.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        }

        $sql .= ' ORDER BY c.fecha DESC, c.cotizacion_id DESC';

        // Debug: Mostrar la consulta SQL completa
        error_log('SQL Query: ' . $sql);
        error_log('ID empresa fijo: 12');
        error_log('Vendedor ID: ' . $vendedor_id);
        error_log('Fecha inicio: ' . $fecha_inicio);
        error_log('Fecha fin: ' . $fecha_fin);

        $result = $this->conexion->query($sql);

        if (!$result) {
            error_log('Error en la consulta SQL: ' . $this->conexion->error);
            die('Error en la consulta: ' . $this->conexion->error);
        }

        // Debug: Consulta simple para verificar datos
        $test_query = "SELECT COUNT(*) as total FROM cotizaciones WHERE id_empresa = '12'";
        $test_result = $this->conexion->query($test_query);
        $test_row = $test_result->fetch_assoc();
        error_log('Total cotizaciones en la empresa: ' . $test_row['total']);

        // Generar el contenido del Excel
        $tbody = '';
        $total_general_soles = 0;
        $total_general_dolares = 0;
        $contador = 0;
        $vendedor_actual = '';
        $total_vendedor_soles = 0;
        $total_vendedor_dolares = 0;

        // Versión simplificada para debug
        while ($fila = $result->fetch_assoc()) {
            $contador++;
            error_log("Procesando fila $contador: " . json_encode($fila));

            try {
                // Datos básicos con validación y escape HTML
                $numero = isset($fila['numero']) ? str_pad($fila['numero'], 3, '0', STR_PAD_LEFT) : 'N/A';
                $fecha = isset($fila['fecha']) ? date('d/m/Y', strtotime($fila['fecha'])) : 'N/A';
                $cliente = isset($fila['cliente']) ? htmlspecialchars($fila['cliente'], ENT_QUOTES, 'UTF-8') : 'Sin Cliente';
                $vendedor = isset($fila['vendedor']) ? htmlspecialchars($fila['vendedor'], ENT_QUOTES, 'UTF-8') : 'Sin Vendedor';
                $total = isset($fila['total']) ? $fila['total'] : 0;
                $moneda = isset($fila['moneda']) ? $fila['moneda'] : 1;
                $estado_texto = isset($fila['estado_texto']) ? $fila['estado_texto'] : 'N/A';

                // Símbolo de moneda
                $simbolo_moneda = ($moneda == 2) ? '$' : 'S/';

                // Acumular totales
                if ($moneda == 2) {
                    $total_general_dolares += $total;
                } else {
                    $total_general_soles += $total;
                }

                // Agregar fila al tbody
                $tbody .= "<tr>
                    <td style='font-size: 10px;'>$contador</td>
                    <td style='font-size: 10px;'>COT-$numero</td>
                    <td style='font-size: 10px;'>$fecha</td>
                    <td style='font-size: 10px;'>$cliente</td>
                    <td style='font-size: 10px;'>$vendedor</td>
                    <td style='font-size: 10px;'>$simbolo_moneda " . number_format($total, 2) . "</td>
                    <td style='font-size: 10px;'>$estado_texto</td>
                </tr>";

                error_log("Fila $contador agregada correctamente al tbody");
            } catch (Exception $e) {
                error_log("Error procesando fila $contador: " . $e->getMessage());
            }
        }

        // Agregar total general simplificado
        $total_general_text = '';
        if ($total_general_soles > 0) {
            $total_general_text .= 'S/ ' . number_format($total_general_soles, 2);
        }
        if ($total_general_dolares > 0) {
            if ($total_general_text != '')
                $total_general_text .= ' + ';
            $total_general_text .= '$ ' . number_format($total_general_dolares, 2);
        }

        $tbody .= "<tr style='background-color: #CA3438; color: white;'>
            <td colspan='5' style='font-weight: bold; text-align: right;'>TOTAL GENERAL:</td>
            <td style='font-weight: bold;'>$total_general_text</td>
            <td></td>
        </tr>";

        // Obtener información de la empresa
        $empresa = $this->conexion->query("SELECT * FROM empresas WHERE id_empresa = '12'")->fetch_assoc();

        // Obtener nombre del vendedor si se filtró por uno específico
        $nombre_vendedor = 'TODOS LOS VENDEDORES';
        if ($vendedor_id != '0' && !empty($vendedor_id)) {
            $vendedor_info = $this->conexion->query("SELECT nombres, apellidos FROM usuarios WHERE usuario_id = '$vendedor_id'")->fetch_assoc();
            if ($vendedor_info) {
                $nombre_vendedor = $vendedor_info['nombres'] . ' ' . ($vendedor_info['apellidos'] ?: '');
            }
        }

        $periodo = '';
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $periodo = 'DEL ' . date('d/m/Y', strtotime($fecha_inicio)) . ' AL ' . date('d/m/Y', strtotime($fecha_fin));
        } else {
            $periodo = 'TODAS LAS FECHAS';
        }

        $tabla = "
        <table>
            <tr>
                <td colspan='7' style='background-color: #CA3438; color: white; font-weight: bold; text-align: center; font-size: 14px;'>
                    REPORTE DE COTIZACIONES POR VENDEDOR
                </td>
            </tr>
            <tr>
                <td colspan='7' style='background-color: #f8f9fa; font-weight: bold; text-align: center; font-size: 12px;'>
                    EMPRESA: {$empresa['razon_social']} - RUC: {$empresa['ruc']}
                </td>
            </tr>
            <tr>
                <td colspan='7' style='background-color: #f8f9fa; font-weight: bold; text-align: center; font-size: 12px;'>
                    VENDEDOR: $nombre_vendedor
                </td>
            </tr>
            <tr>
                <td colspan='7' style='background-color: #f8f9fa; font-weight: bold; text-align: center; font-size: 12px;'>
                    PERÍODO: $periodo
                </td>
            </tr>
            <tr>
                <th style='background-color: #90BFEB; width: 8px; font-weight: bold;'>N°</th>
                <th style='background-color: #90BFEB; width: 15px; font-weight: bold;'>COTIZACIÓN</th>
                <th style='background-color: #90BFEB; width: 12px; font-weight: bold;'>FECHA</th>
                <th style='background-color: #90BFEB; width: 40px; font-weight: bold;'>CLIENTE</th>
                <th style='background-color: #90BFEB; width: 25px; font-weight: bold;'>VENDEDOR</th>
                <th style='background-color: #90BFEB; width: 15px; font-weight: bold;'>TOTAL</th>
                <th style='background-color: #90BFEB; width: 15px; font-weight: bold;'>ESTADO</th>
            </tr>
            <tbody>
                " . $tbody . '
            </tbody>
        </table>';

        // Debug: Verificar si hay datos
        error_log('Número de filas encontradas: ' . $result->num_rows);
        error_log('Contenido tbody: ' . substr($tbody, 0, 500));  // Primeros 500 caracteres
        error_log('Total general soles: ' . $total_general_soles);
        error_log('Total general dolares: ' . $total_general_dolares);

        // Si no hay datos, mostrar mensaje de error
        if (empty($tbody)) {
            die('No se encontraron cotizaciones para los filtros aplicados. Revisa el error.log para más detalles.');
        }

        // Generar el archivo Excel directamente (sin HTML)
        try {
            $nombre_exel = 'reporte_cotizaciones_vendedores_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Crear spreadsheet nuevo
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Encabezados del reporte
            $sheet->setCellValue('A1', 'REPORTE DE COTIZACIONES POR VENDEDOR');
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CA3438');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', "EMPRESA: {$empresa['razon_social']} - RUC: {$empresa['ruc']}");
            $sheet->mergeCells('A2:G2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', "VENDEDOR: $nombre_vendedor");
            $sheet->mergeCells('A3:G3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A4', "PERÍODO: $periodo");
            $sheet->mergeCells('A4:G4');
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Encabezados de columnas
            $fila = 6;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'COTIZACIÓN');
            $sheet->setCellValue('C' . $fila, 'FECHA');
            $sheet->setCellValue('D' . $fila, 'CLIENTE');
            $sheet->setCellValue('E' . $fila, 'VENDEDOR');
            $sheet->setCellValue('F' . $fila, 'TOTAL');
            $sheet->setCellValue('G' . $fila, 'ESTADO');

            // Estilo para encabezados
            $sheet->getStyle('A' . $fila . ':G' . $fila)->getFont()->setBold(true);
            $sheet->getStyle('A' . $fila . ':G' . $fila)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('90BFEB');

            // Agregar datos - volver a procesar el resultado
            $result->data_seek(0);  // Resetear el puntero del resultado
            $fila = 7;
            $contador = 0;
            $total_general_soles_excel = 0;
            $total_general_dolares_excel = 0;

            while ($fila_data = $result->fetch_assoc()) {
                $contador++;

                $numero = str_pad($fila_data['numero'], 3, '0', STR_PAD_LEFT);
                $fecha = date('d/m/Y', strtotime($fila_data['fecha']));
                $cliente = $fila_data['cliente'];
                $vendedor = $fila_data['vendedor'];
                $total = $fila_data['total'];
                $moneda = $fila_data['moneda'];
                $estado_texto = $fila_data['estado_texto'];

                $simbolo_moneda = ($moneda == 2) ? '$' : 'S/';

                // Acumular totales
                if ($moneda == 2) {
                    $total_general_dolares_excel += $total;
                } else {
                    $total_general_soles_excel += $total;
                }

                $sheet->setCellValue('A' . $fila, $contador);
                $sheet->setCellValue('B' . $fila, 'COT-' . $numero);
                $sheet->setCellValue('C' . $fila, $fecha);
                $sheet->setCellValue('D' . $fila, $cliente);
                $sheet->setCellValue('E' . $fila, $vendedor);
                $sheet->setCellValue('F' . $fila, $simbolo_moneda . ' ' . number_format($total, 2));
                $sheet->setCellValue('G' . $fila, $estado_texto);

                $fila++;
            }

            // Total general
            $sheet->setCellValue('A' . $fila, 'TOTAL GENERAL:');
            $sheet->mergeCells('A' . $fila . ':E' . $fila);
            $sheet->getStyle('A' . $fila)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A' . $fila)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CA3438');

            $total_general_text_excel = '';
            if ($total_general_soles_excel > 0) {
                $total_general_text_excel .= 'S/ ' . number_format($total_general_soles_excel, 2);
            }
            if ($total_general_dolares_excel > 0) {
                if ($total_general_text_excel != '')
                    $total_general_text_excel .= ' + ';
                $total_general_text_excel .= '$ ' . number_format($total_general_dolares_excel, 2);
            }

            $sheet->setCellValue('F' . $fila, $total_general_text_excel);
            $sheet->getStyle('F' . $fila)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('F' . $fila)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CA3438');

            // Ajustar ancho de columnas
            $sheet->getColumnDimension('A')->setWidth(8);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(40);
            $sheet->getColumnDimension('E')->setWidth(25);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);

            // Configurar headers para descarga directa
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombre_exel . '"');
            header('Cache-Control: max-age=0');

            // Crear writer y enviar
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            error_log('Error generando Excel: ' . $e->getMessage());
            die('Error generando el archivo Excel: ' . $e->getMessage());
        }
    }

    public function generarExcelRegistroCajas()
    {
        $sql = 'SELECT * FROM caja_empresa WHERE sucursal = ? AND id_empresa = ? ORDER BY caja_id DESC';
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['sucursal'], $_SESSION['id_empresa']);
        $stmt->execute();
        $result = $stmt->get_result();

        $tbody = '';
        foreach ($result as $fila) {
            $total = doubleval($fila['entrada']) - doubleval($fila['salida']);
            $fecha_apertura = (new DateTime($fila['fecha']))->format('d/m/Y');
            $fecha_cierre_val = $fila['fecha_cierre'];
            $fecha_cierre = ($fecha_cierre_val && $fecha_cierre_val != '0000-00-00 00:00:00') ? (new DateTime($fecha_cierre_val))->format('d/m/Y h:i A') : '<span style="color:green;">Abierta</span>';

            $tbody .= '
                <tr>
                    <td style="font-size: 10px;border:1px solid black;">' . htmlspecialchars($fila['numero'] ?: '-') . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . htmlspecialchars($fila['detalle']) . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fecha_apertura . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fecha_cierre . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . number_format(doubleval($fila['entrada']), 2) . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . number_format(doubleval($fila['salida']), 2) . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . number_format($total, 2) . '</td>
                </tr>';
        }
        $stmt->close();

        $tabla = '  <table style="width:100%">
                    <tr>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Código</th>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Detalle</th>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Apertura</th>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Cierre</th>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Entrada</th>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Salida</th>
                        <th style="font-size: 10px;font-weight:bold;border:1px solid black;background-color: #90BFEB;">Total</th>
                    </tr>
                    ' . $tbody . '
                    </table>';

        $nombre_exel = 'RegistroGeneralCajas_' . date('Y-m-d') . '.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
    }

    // Generar Plantilla Excel para Importar Repuestos
    public function generarExcelRepuestoImporte()
    {
        $sql = "SELECT
                    nombre AS repuesto,
                    detalle,
                    cantidad AS cnt,
                    costo,
                    precio_unidad,
                    precio,
                    precio2,
                    almacen,
                    codigo
                FROM
                    repuestos
                WHERE almacen = '{$_SESSION['sucursal']}' AND estado = 1";

        $result = $this->conexion->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Ordenar por código
        usort($data, function ($a, $b) {
            return strnatcmp($a['codigo'], $b['codigo']);
        });

        $tabla = '';
        $tbody = '';
        foreach ($data as $fila) {
            $tbody .= '
               <tr>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['repuesto'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['detalle'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['cnt'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['costo'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['precio_unidad'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['precio'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['precio2'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['almacen'] . '</td>
                    <td style="font-size: 10px;border:1px solid black;">' . $fila['codigo'] . '</td>
               </tr>
                ';
        }

        $tabla = '  <table style="width:100%">
        <tr>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">Repuesto</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">Detalle</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:13px;text-align: center;word-wrap: break-word">Cnt</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:16px;text-align: center;word-wrap: break-word">Costo</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:10px;text-align: center;word-wrap: break-word">Precio Venta</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:15px;text-align: center; word-wrap: break-word">Precio Distribuidor</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;word-wrap: break-word">Precio Mayorista</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;word-wrap: break-word">Almacen</td>
            <td style="font-size: 10px;font-weight:bold;border:1px solid black;width:12px;text-align: center;word-wrap: break-word">Codigo</td>
        </tr>
       ' . $tbody . '
        </table>';

        $nombre_exel = 'plantilla_repuestos.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
    }

    // Generar Excel de Repuestos con búsqueda
    public function generarExcelRepuesto()
    {
        $texto = $_GET['texto'] ?? '';

        // Consulta similar a productos pero para repuestos
        $sql = "SELECT codigo,
                MAX(nombre) AS nombre,
                MAX(detalle) AS detalle,
                MAX(costo) AS costo,
                MAX(precio_unidad) AS precio_unidad,
                MAX(precio) AS precio,
                MAX(precio2) AS precio2,
                SUM(CASE WHEN almacen = 1 THEN cantidad ELSE 0 END) AS cantidad1,
                SUM(CASE WHEN almacen = 2 THEN cantidad ELSE 0 END) AS cantidad2,
                SUM(CASE WHEN almacen = 3 THEN cantidad ELSE 0 END) AS cantidad3
                FROM repuestos
                WHERE (nombre LIKE '%$texto%' OR codigo LIKE '%$texto%' OR detalle LIKE '%$texto%')
                AND estado = '1'
                GROUP BY codigo
                ORDER BY codigo";

        $result = $this->conexion->query($sql);

        $tbody = '';
        foreach ($result as $fila) {
            $tbody .= '
            <tr>
                <td>' . $fila['codigo'] . '</td>
                <td>' . $fila['nombre'] . '</td>
                <td>' . $fila['detalle'] . '</td>
                <td>' . number_format($fila['costo'], 2) . '</td>
                <td>' . number_format($fila['precio_unidad'], 2) . '</td>
                <td>' . number_format($fila['precio'], 2) . '</td>
                <td>' . number_format($fila['precio2'], 2) . '</td>
                <td>' . $fila['cantidad1'] . '</td>
                <td>' . $fila['cantidad2'] . '</td>
                <td>' . $fila['cantidad3'] . '</td>
            </tr>';
        }

        $tabla = "
        <table>
            <tr>
                <th style='background-color: #90BFEB;width:10px'>Codigo</th>
                <th style='background-color: #90BFEB;width:50px'>Nombre</th>
                <th style='background-color: #90BFEB;width:30px'>Detalle</th>
                <th style='background-color: #90BFEB;width:10px'>Costo</th>
                <th style='background-color: #90BFEB;width:10px'>Precio Venta</th>
                <th style='background-color: #90BFEB;width:10px'>Precio Distribuidor</th>
                <th style='background-color: #90BFEB;width:10px'>Precio Mayorista</th>
                <th style='background-color: #90BFEB;width:7px'>CNT A1</th>
                <th style='background-color: #90BFEB;width:7px'>CNT A2</th>
                <th style='background-color: #90BFEB;width:7px'>CNT A3</th>
            </tr>
            <tbody>
                " . $tbody . '
            </tbody>
        </table>';

        $nombre_exel = 'reporterepuestosstock.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
    }

    public function historialStockExcel()
    {
        $id_empresa = $_SESSION['id_empresa'];

        // NUEVO: Obtener filtros de la URL
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
        $tipo_origen = isset($_GET['tipo_origen']) ? $_GET['tipo_origen'] : null;

        // Construir consulta con filtros
        $sql = "SELECT h.*, p.nombre as producto_nombre, p.codigo,
                    CASE
                        WHEN h.usuario REGEXP '^[0-9]+$' THEN TRIM(CONCAT(COALESCE(u.nombres,''), ' ', COALESCE(u.apellidos,'')))
                        ELSE h.usuario
                    END AS usuario_nombre
                FROM historial_stock h
                INNER JOIN productos p ON h.id_producto = p.id_producto
                LEFT JOIN usuarios u ON u.usuario_id = h.usuario
                WHERE p.id_empresa = '$id_empresa'";

        // Aplicar filtro por tipo de movimiento
        if ($tipo && $tipo !== 'todos') {
            $sql .= " AND h.tipo_movimiento = '" . $this->conexion->real_escape_string($tipo) . "'";
        }

        // Aplicar filtro por tipo de origen
        if ($tipo_origen) {
            $sql .= " AND h.tipo_origen = '" . $this->conexion->real_escape_string($tipo_origen) . "'";
        }

        $sql .= " ORDER BY h.fecha_movimiento DESC";

        $result = $this->conexion->query($sql);

        // Determinar título según filtros
        $titulo = 'Historial de Stock';
        if ($tipo === 'INGRESO') {
            $titulo = 'Historial de Stock - Solo Ingresos';
        } elseif ($tipo === 'EGRESO') {
            $titulo = 'Historial de Stock - Solo Egresos';
        } elseif ($tipo_origen === 'ORDEN_TRABAJO_INTERNA') {
            $titulo = 'Historial de Stock - Producción Interna';
        } elseif ($tipo_origen === 'ORDEN_TRABAJO_EXTERNA') {
            $titulo = 'Historial de Stock - Uso en Órdenes Externas';
        }

        // Crear tabla HTML para Excel (sin <thead> para evitar bug de fila 0 en PHPSpreadsheet)
        $tabla = '<table border="1">
            <tr style="background-color: #CA3438; color: white; font-weight: bold;">
                <th>Código</th>
                <th>Producto</th>
                <th>Movimiento</th>
                <th>Cantidad</th>
                <th>Costo Compra</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Tipo Origen</th>
                <th>Observaciones</th>
            </tr>';

        $totalRegistros = 0;
        if ($result) while ($row = $result->fetch_assoc()) {
            $costo = $row['costo_compra'] ? 'S/ ' . number_format($row['costo_compra'], 2) : '-';
            $fecha = date('d/m/Y H:i', strtotime($row['fecha_movimiento']));
            $observaciones = $row['observaciones'] ? $row['observaciones'] : '-';
            $tipoOrigen = $row['tipo_origen'] ? $row['tipo_origen'] : 'MANUAL';
            $colorMovimiento = $row['tipo_movimiento'] == 'INGRESO' ? '#28a745' : '#dc3545';
            $usuarioNombre = !empty($row['usuario_nombre']) ? trim($row['usuario_nombre']) : ($row['usuario'] ?? 'Sistema');

            $tabla .= '<tr>
                <td>' . htmlspecialchars($row['codigo']) . '</td>
                <td>' . htmlspecialchars($row['producto_nombre']) . '</td>
                <td style="background-color: ' . $colorMovimiento . '; color: white; font-weight: bold;">' . $row['tipo_movimiento'] . '</td>
                <td style="text-align: center;">' . $row['cantidad'] . '</td>
                <td style="text-align: right;">' . $costo . '</td>
                <td>' . $fecha . '</td>
                <td>' . htmlspecialchars($usuarioNombre) . '</td>
                <td>' . htmlspecialchars($tipoOrigen) . '</td>
                <td>' . htmlspecialchars($observaciones) . '</td>
            </tr>';
            $totalRegistros++;
        }

        $tabla .= '<tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="3" style="text-align: right;">TOTAL REGISTROS:</td>
                <td style="text-align: center;">' . $totalRegistros . '</td>
                <td colspan="5"></td>
            </tr>';
        $tabla .= '</table>';

        // Generar archivo Excel
        $nombre_exel = 'historial_stock_' . date('Y-m-d_His') . '.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);

        $sheet = $spreadsheet->getActiveSheet();

        // Insertar fila de título al principio (empuja todo una fila abajo)
        $sheet->insertNewRowBefore(1, 1);
        $sheet->setCellValue('A1', $titulo);
        $sheet->mergeCells('A1:I1');

        // Ajustar anchos de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(50);

        $titleStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 14],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CA3438']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CA3438']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ];
        $sheet->getStyle('A2:I2')->applyFromArray($headerStyle);
        $sheet->getRowDimension(2)->setRowHeight(25);

        $highestRow = $sheet->getHighestRow();
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];
        $sheet->getStyle('A1:I' . $highestRow)->applyFromArray($borderStyle);

        if ($highestRow >= 3) {
            $sheet->getStyle('A3:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C3:D' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B3:B' . $highestRow)->getAlignment()->setWrapText(true);
            $sheet->getStyle('I3:I' . $highestRow)->getAlignment()->setWrapText(true);
        }

        $sheet->freezePane('A3');

        if (ob_get_level()) ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function historialStockRepuestosExcel()
    {
        $id_empresa = $_SESSION['id_empresa'];

        // Consulta para obtener el historial de stock de repuestos
        $sql = "SELECT h.*, r.nombre as repuesto_nombre, r.codigo,
                    CASE
                        WHEN h.usuario REGEXP '^[0-9]+$' THEN TRIM(CONCAT(COALESCE(u.nombres,''), ' ', COALESCE(u.apellidos,'')))
                        ELSE h.usuario
                    END AS usuario_nombre
                FROM historial_stock_repuestos h
                INNER JOIN repuestos r ON h.id_repuesto = r.id_repuesto
                LEFT JOIN usuarios u ON u.usuario_id = h.usuario
                WHERE r.id_empresa = '$id_empresa'
                ORDER BY h.fecha_movimiento DESC";

        $result = $this->conexion->query($sql);

        // Crear tabla HTML para Excel (sin <thead> para evitar bug de fila 0 en PHPSpreadsheet)
        $tabla = '<table border="1">
            <tr style="background-color: #CA3438; color: white; font-weight: bold;">
                <th>Código</th>
                <th>Repuesto</th>
                <th>Movimiento</th>
                <th>Cantidad</th>
                <th>Costo Compra</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Observaciones</th>
            </tr>';

        if ($result) while ($row = $result->fetch_assoc()) {
            $costo = $row['costo_compra'] ? 'S/ ' . number_format($row['costo_compra'], 2) : '-';
            $fecha = date('d/m/Y H:i', strtotime($row['fecha_movimiento']));
            $observaciones = $row['observaciones'] ? $row['observaciones'] : '-';
            $colorMovimiento = $row['tipo_movimiento'] == 'INGRESO' ? '#28a745' : '#dc3545';
            $usuarioNombre = !empty($row['usuario_nombre']) ? trim($row['usuario_nombre']) : ($row['usuario'] ?? 'Sistema');

            $tabla .= '<tr>
                <td>' . htmlspecialchars($row['codigo']) . '</td>
                <td>' . htmlspecialchars($row['repuesto_nombre']) . '</td>
                <td style="background-color: ' . $colorMovimiento . '; color: white; font-weight: bold;">' . $row['tipo_movimiento'] . '</td>
                <td style="text-align: center;">' . $row['cantidad'] . '</td>
                <td style="text-align: right;">' . $costo . '</td>
                <td>' . $fecha . '</td>
                <td>' . htmlspecialchars($usuarioNombre) . '</td>
                <td>' . htmlspecialchars($observaciones) . '</td>
            </tr>';
        }

        $tabla .= '</table>';

        // Generar archivo Excel
        $nombre_exel = 'historial_stock_repuestos_' . date('Y-m-d_His') . '.xlsx';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);

        $sheet = $spreadsheet->getActiveSheet();

        // Ajustar anchos de columnas
        $sheet->getColumnDimension('A')->setWidth(15);  // Código
        $sheet->getColumnDimension('B')->setWidth(60);  // Repuesto (más ancho)
        $sheet->getColumnDimension('C')->setWidth(15);  // Movimiento
        $sheet->getColumnDimension('D')->setWidth(12);  // Cantidad
        $sheet->getColumnDimension('E')->setWidth(15);  // Costo
        $sheet->getColumnDimension('F')->setWidth(20);  // Fecha
        $sheet->getColumnDimension('G')->setWidth(18);  // Usuario
        $sheet->getColumnDimension('H')->setWidth(50);  // Observaciones (más ancho)

        // Aplicar estilos al encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CA3438']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Ajustar altura de la fila del encabezado
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Aplicar bordes a todas las celdas con datos
        $highestRow = $sheet->getHighestRow();
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:H' . $highestRow)->applyFromArray($borderStyle);

        // Centrar columnas específicas (solo si hay filas de datos)
        if ($highestRow >= 2) {
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:D' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setWrapText(true);
            $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setWrapText(true);
        }

        // Congelar la primera fila (encabezado)
        $sheet->freezePane('A2');

        if (ob_get_level()) ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre_exel . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
