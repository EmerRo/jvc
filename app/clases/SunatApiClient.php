<?php

/**
 * SunatApiClient — reemplaza SunatApi y SunatApi2.
 * Delega la generación y firma de XML a la API centralizada (api-sunat-laravel).
 * Mantiene exactamente las mismas firmas públicas para no romper los controladores.
 */
class SunatApiClient
{
    private $mensaje = '';
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    // ─── HTTP helpers ─────────────────────────────────────────────────────────

    private function post(string $endpoint, array $data): array
    {
        $url = rtrim(URL_API_SUNAT, '/') . '/api/v1/' . ltrim($endpoint, '/');
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return ['estado' => false, 'mensaje' => 'Error de conexión con API SUNAT (' . URL_API_SUNAT . ')'];
        }
        $decoded = json_decode($response, true);
        return $decoded ?: ['estado' => false, 'mensaje' => 'Respuesta inválida de API SUNAT'];
    }

    // Sube el certificado .pem a la API (una vez por RUC por sesión).
    // En modo beta usa el cert demo de Greenter si no existe el cert real.
    private function subirCertificado(string $ruc, string $endpoint = 'beta'): void
    {
        if (!empty($_SESSION['cert_api_subido'][$ruc])) {
            return;
        }
        $cert_path = "files/facturacion/certificados/{$ruc}-cert.pem";
        if (!file_exists($cert_path)) {
            if ($endpoint === 'beta') {
                $cert_path = "files/facturacion/certificados/20000000001-cert.pem";
            }
            if (!file_exists($cert_path)) {
                return;
            }
        }
        $url = rtrim(URL_API_SUNAT, '/') . '/api/v1/guardar/certificado/' . $ruc;
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['certificado' => base64_encode(file_get_contents($cert_path))]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_exec($ch);
        curl_close($ch);
        $_SESSION['cert_api_subido'][$ruc] = true;
    }

    // ─── Helpers de mapeo ─────────────────────────────────────────────────────

    private function decodificarArrays(array &$data): void
    {
        foreach (['cliente', 'empresa', 'productos', 'dias_pagos', 'venta', 'transporte'] as $k) {
            if (isset($data[$k]) && is_string($data[$k])) {
                $data[$k] = json_decode($data[$k], true);
            }
        }
    }

    private function mapearEmpresa(array $emp): array
    {
        return [
            'ruc'          => $emp['ruc'],
            'usuario'      => $emp['usuario_sol'] ?? $emp['user_sol'] ?? '',
            'clave'        => $emp['clave_sol'],
            'razon_social' => $emp['razon_social'] ?? '',
            'direccion'    => $emp['direccion'] ?? '',
            'ubigeo'       => $emp['ubigeo'] ?? '',
            'distrito'     => $emp['distrito'] ?? '',
            'provincia'    => $emp['provincia'] ?? '',
            'departamento' => $emp['departamento'] ?? '',
        ];
    }

    /**
     * Mapea productos de JVC al formato "detalles" de la API.
     * Si $apliIgv=false genera los campos calculados para ítems exonerados (tipAfeIgv='20').
     */
    private function mapearDetalles(array $productos, bool $conPrecio = true, bool $apliIgv = true): array
    {
        $detalles = [];
        foreach ($productos as $p) {
            if (empty($p['cantidad']) || !is_numeric($p['cantidad'])) continue;
            if ($conPrecio && (empty($p['precio']) || !is_numeric($p['precio']))) continue;

            $d = [
                'cod_producto' => $p['cod_pro'] ?? $p['cod_producto'] ?? '',
                'cod_sunat'    => $p['cod_sunat'] ?? '',
                'unidad'       => 'NIU',
                'descripcion'  => $p['descripcion'],
                'cantidad'     => (float)$p['cantidad'],
            ];

            if ($conPrecio && isset($p['precio'])) {
                $precio   = (float)$p['precio'];
                $cantidad = (float)$p['cantidad'];
                $d['precio'] = $precio;

                if (!$apliIgv) {
                    // Exonerado: sin IGV, la API debe usar estos valores directamente
                    $d['tipAfeIgv']        = '20';
                    $d['igv']              = 0;
                    $d['mtoBaseIgv']       = 0;
                    $d['totalImpuestos']   = 0;
                    $d['mtoValorVenta']    = round($precio * $cantidad, 2);
                    $d['mtoValorUnitario'] = $precio;
                    $d['mtoPrecioUnitario']= $precio;
                }
            }
            $detalles[] = $d;
        }
        return $detalles;
    }

    private function guardarXML(string $ruc, string $nombre, string $contenido): void
    {
        $dir = "files/facturacion/xml/{$ruc}";
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        file_put_contents("{$dir}/{$nombre}.xml", $contenido);
    }

    private function guardarCDR(string $ruc, string $nombre, string $cdrBase64): void
    {
        $dir = "files/facturacion/cdr/{$ruc}";
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        file_put_contents("{$dir}/R-{$nombre}.zip", base64_decode($cdrBase64));
    }

    // ─── Generación de XML ────────────────────────────────────────────────────

    public function genFacturaXML(array $dataE): array
    {
        if (ob_get_level()) ob_clean();
        $this->decodificarArrays($dataE);
        $this->subirCertificado($dataE['empresa']['ruc'], $dataE['endpoints'] ?? 'beta');

        $apliIgv = (bool)($dataE['apli_igv'] ?? true);
        $total   = (float)$dataE['total'];

        $cuotas = [];
        foreach (($dataE['dias_pagos'] ?? []) as $dp) {
            $cuotas[] = ['fecha' => $dp['fecha'], 'monto' => (float)$dp['monto']];
        }

        $body = [
            'endpoint'          => $dataE['endpoints'],
            'documento'         => 'factura',
            'empresa'           => $this->mapearEmpresa($dataE['empresa']),
            'cliente'           => [
                'num_doc'    => $dataE['cliente']['doc_num'],
                'rzn_social' => $dataE['cliente']['nom_RS'] ?? '-',
                'tipo_doc'   => '6',
            ],
            'serie'             => $dataE['serie'],
            'numero'            => (string)$dataE['numero'],
            'fecha_emision'     => $dataE['fechaE'],
            'fecha_vencimiento' => $dataE['fechaV'] ?? $dataE['fechaE'],
            'moneda'            => $dataE['moneda'] ?? 'PEN',
            'total'             => $total,
            'forma_pago'        => ($dataE['tipo_pago'] == '1') ? 'contado' : 'credito',
            'cuotas_credito'    => $cuotas,
            'detalles'          => $this->mapearDetalles($dataE['productos'], true, $apliIgv),
        ];

        if (!$apliIgv) {
            $body['mtoOperGravadas']    = 0;
            $body['mtoIGV']             = 0;
            $body['totalImpuestos']     = 0;
            $body['valorVenta']         = $total;
            $body['subTotal']           = $total;
            $body['mtoImpVenta']        = $total;
            $body['mtoOperExoneradas']  = $total;
        }

        $res = $this->post('generar/comprobante', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($dataE['empresa']['ruc'], $res['data']['nombre_archivo'], $res['data']['contenido_xml']);
            return ['res' => true, 'data' => [
                'qr'             => $res['data']['qr_info'],
                'hash'           => $res['data']['hash'],
                'nombre_archivo' => $res['data']['nombre_archivo'],
            ]];
        }

        $this->mensaje = $res['mensaje'] ?? 'Error al generar factura XML';
        return ['res' => false, 'msg' => $this->mensaje];
    }

    public function genBoletaXML(array $dataE): array
    {
        if (ob_get_level()) ob_clean();
        $this->decodificarArrays($dataE);
        $this->subirCertificado($dataE['empresa']['ruc'], $dataE['endpoints'] ?? 'beta');

        $apliIgv  = (bool)($dataE['apli_igv'] ?? true);
        $total    = (float)$dataE['total'];
        $doc_num  = $dataE['cliente']['doc_num'];
        $tipo_doc = strlen($doc_num) == 8 ? '1' : '0';
        if ($tipo_doc === '0') $doc_num = '00000000';
        $nom_rs = $dataE['cliente']['nom_RS'] ?? '-';

        $cuotas = [];
        foreach (($dataE['dias_pagos'] ?? []) as $dp) {
            $cuotas[] = ['fecha' => $dp['fecha'], 'monto' => (float)$dp['monto']];
        }

        $body = [
            'endpoint'          => $dataE['endpoints'],
            'documento'         => 'boleta',
            'empresa'           => $this->mapearEmpresa($dataE['empresa']),
            'cliente'           => [
                'num_doc'    => $doc_num,
                'rzn_social' => ($nom_rs === '-') ? 'cliente' : $nom_rs,
                'tipo_doc'   => $tipo_doc,
            ],
            'serie'             => $dataE['serie'],
            'numero'            => (string)$dataE['numero'],
            'fecha_emision'     => $dataE['fechaE'],
            'fecha_vencimiento' => $dataE['fechaV'] ?? $dataE['fechaE'],
            'moneda'            => $dataE['moneda'] ?? 'PEN',
            'total'             => $total,
            'forma_pago'        => ($dataE['tipo_pago'] == '1') ? 'contado' : 'credito',
            'cuotas_credito'    => $cuotas,
            'detalles'          => $this->mapearDetalles($dataE['productos'], true, $apliIgv),
        ];

        if (!$apliIgv) {
            $body['mtoOperGravadas']   = 0;
            $body['mtoIGV']            = 0;
            $body['totalImpuestos']    = 0;
            $body['valorVenta']        = $total;
            $body['subTotal']          = $total;
            $body['mtoImpVenta']       = $total;
            $body['mtoOperExoneradas'] = $total;
        }

        $res = $this->post('generar/comprobante', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($dataE['empresa']['ruc'], $res['data']['nombre_archivo'], $res['data']['contenido_xml']);
            return ['res' => true, 'data' => [
                'qr'             => $res['data']['qr_info'],
                'hash'           => $res['data']['hash'],
                'nombre_archivo' => $res['data']['nombre_archivo'],
            ]];
        }

        $this->mensaje = $res['mensaje'] ?? 'Error al generar boleta XML';
        return ['res' => false, 'msg' => $this->mensaje];
    }

    public function genGuiaRemision(array $dataE): array
    {
        $this->decodificarArrays($dataE);
        $this->subirCertificado($dataE['empresa']['ruc'], $dataE['endpoints'] ?? 'beta');

        $serieRelacionado = null;
        if (!empty($dataE['venta']['serie']) && !empty($dataE['venta']['numero'])) {
            $serieRelacionado = $dataE['venta']['serie'] . '-' . $dataE['venta']['numero'];
        }

        $body = [
            'endpoint'                 => $dataE['endpoints'],
            'documento'                => 'remitente',
            'empresa'                  => $this->mapearEmpresa($dataE['empresa']),
            'cliente'                  => [
                'num_doc'    => $dataE['cliente']['doc_num'],
                'rzn_social' => $dataE['cliente']['nom_RS'],
                'direccion'  => '-',
            ],
            'serie'                    => $dataE['serie'],
            'numero'                   => (string)$dataE['numero'],
            'fecha_emision'            => $dataE['fecha'],
            'serie_numero_relacionado' => $serieRelacionado,
            'datos_envio'              => [
                'cod_traslado'      => '01',
                'mod_traslado'      => '01',
                'fecha_traslado'    => $dataE['fecha'],
                'peso_total'        => (float)($dataE['peso'] ?? 1),
                'unidad_medida'     => 'KGM',
                'ubigeo_salida'     => $dataE['empresa']['ubigeo'] ?? '',
                'direccion_salida'  => $dataE['empresa']['direccion'] ?? '',
                'ubigeo_llegada'    => $dataE['ubigeo'] ?? '',
                'direccion_llegada' => $dataE['direccion'] ?? '',
            ],
            'transportista'            => [
                'num_doc'    => $dataE['transporte']['ruc'],
                'rzn_social' => $dataE['transporte']['razon_social'],
                'nro_mtc'    => '0001',
            ],
            'detalles'                 => $this->mapearDetalles($dataE['productos'], false),
        ];

        $res = $this->post('generar/guia/remision', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($dataE['empresa']['ruc'], $res['data']['nombre_archivo'], $res['data']['contenido_xml']);
            return ['res' => true, 'data' => [
                'qr'             => $res['data']['qr_info'] ?? '',
                'hash'           => $res['data']['hash'] ?? '',
                'nombre_archivo' => $res['data']['nombre_archivo'],
            ]];
        }

        $this->mensaje = $res['mensaje'] ?? 'Error al generar guía de remisión XML';
        return ['res' => false, 'msg' => $this->mensaje];
    }

    public function genNotaElectronicaXML(array $dataE): array
    {
        $this->decodificarArrays($dataE);
        $this->subirCertificado($dataE['empresa']['ruc'], $dataE['endpoints'] ?? 'beta');

        $doc_num  = $dataE['cliente']['doc_num'];
        $tipo_doc = strlen($doc_num) == 8 ? '1' : (strlen($doc_num) == 11 ? '6' : '0');
        if ($tipo_doc === '0') $doc_num = '00000000';

        $documento    = ($dataE['cod_notaE'] === '07') ? 'credito' : 'debito';
        $doc_afectado = str_starts_with((string)$dataE['sn_afectado'], 'F') ? 'factura' : 'boleta';
        $nom_rs       = $dataE['cliente']['nom_RS'] ?? '-';

        $body = [
            'endpoint'              => $dataE['endpoints'],
            'documento'             => $documento,
            'empresa'               => $this->mapearEmpresa($dataE['empresa']),
            'cliente'               => [
                'num_doc'    => $doc_num,
                'rzn_social' => ($nom_rs === '-') ? 'cliente' : $nom_rs,
                'tipo_doc'   => $tipo_doc,
            ],
            'serie'                 => $dataE['serie'],
            'numero'                => (string)$dataE['numero'],
            'fecha_emision'         => date('Y-m-d'),
            'doc_afectado'          => $doc_afectado,
            'serie_numero_afectado' => $dataE['sn_afectado'],
            'cod_motivo'            => $dataE['cod_motivo'],
            'des_motivo'            => $dataE['des_motivo'],
            'moneda'                => $dataE['moneda'] ?? 'PEN',
            'total'                 => (float)$dataE['total'],
            'detalles'              => $this->mapearDetalles($dataE['productos']),
        ];

        $res = $this->post('generar/nota', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($dataE['empresa']['ruc'], $res['data']['nombre_archivo'], $res['data']['contenido_xml']);
            return ['res' => true, 'data' => [
                'qr'             => $res['data']['qr_info'] ?? '',
                'hash'           => $res['data']['hash'] ?? '',
                'nombre_archivo' => $res['data']['nombre_archivo'],
            ]];
        }

        $this->mensaje = $res['mensaje'] ?? 'Error al generar nota electrónica XML';
        return ['res' => false, 'msg' => $this->mensaje];
    }

    // ─── Envío a SUNAT ────────────────────────────────────────────────────────

    private function _enviarDocumento(string $nom_XML, array $empresa): bool
    {
        $xml_ruta = "files/facturacion/xml/{$empresa['ruc']}/{$nom_XML}.xml";
        if (!file_exists($xml_ruta)) {
            $this->mensaje = "No se encontró el XML: {$nom_XML}";
            return false;
        }

        $body = [
            'endpoint'            => $empresa['modo'] ?? 'beta',
            'ruc'                 => $empresa['ruc'],
            'usuario'             => $empresa['user_sol'],
            'clave'               => $empresa['clave_sol'],
            'nombre_documento'    => $nom_XML,
            'contenido_documento' => file_get_contents($xml_ruta),
        ];

        $res = $this->post('enviar/documento/electronico', $body);

        if (!empty($res['estado'])) {
            $this->guardarCDR($empresa['ruc'], $nom_XML, $res['cdr']);
            return true;
        }

        $this->mensaje = $res['mensaje'] ?? 'Error al enviar documento a SUNAT';
        return false;
    }

    public function envioIndividualDocumentoV(string $nom_XML): bool
    {
        $empresa = $this->conexion->query(
            "SELECT * FROM empresas WHERE id_empresa = '{$_SESSION['id_empresa']}'"
        )->fetch_assoc();
        return $this->_enviarDocumento($nom_XML, $empresa);
    }

    public function envioIndividualDocumentoVPorEmpresa(string $nom_XML, $id_empresa): bool
    {
        $empresa = $this->conexion->query(
            "SELECT * FROM empresas WHERE id_empresa = '$id_empresa'"
        )->fetch_assoc();
        return $this->_enviarDocumento($nom_XML, $empresa);
    }

    // ─── Resumen Diario y Comunicación de Baja ───────────────────────────────

    public function comunicacionBajaPorEmpresa($listaFac, $empresa, $fechaComuni, $fechaGene, $correlativo)
    {
        $emp = $this->conexion->query(
            "SELECT * FROM empresas WHERE id_empresa='$empresa'"
        )->fetch_assoc();

        $sql = "SELECT v.id_venta, ds.cod_sunat, v.serie, v.numero
                FROM ventas_anuladas AS va
                    INNER JOIN ventas AS v ON v.id_venta = va.id_venta
                    INNER JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                    INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                WHERE " . implode(' OR ', $listaFac);

        $result   = $this->conexion->query($sql);
        $detalles = [];
        while ($fila = $result->fetch_assoc()) {
            $detalles[] = [
                'tipo_doc'    => $fila['cod_sunat'],
                'serie'       => $fila['serie'],
                'correlativo' => $fila['numero'],
                'motivo'      => 'ERROR AL BUSCAR PRODUCTOS',
            ];
        }

        if (empty($detalles)) {
            return 'Sin item';
        }

        $this->subirCertificado($emp['ruc'], $emp['modo'] ?? 'beta');

        $body = [
            'endpoint'           => $emp['modo'] ?? 'beta',
            'correlativo'        => $emp['id_empresa'] . $correlativo,
            'fecha_generacion'   => $fechaGene,
            'fecha_comunicacion' => $fechaComuni,
            'empresa'            => $this->mapearEmpresa($emp),
            'detalles'           => $detalles,
        ];

        $res = $this->post('enviar/comunicacion/baja', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($emp['ruc'], $res['nombre_archivo'], $res['contenido_xml']);
            $this->guardarCDR($emp['ruc'], $res['nombre_archivo'], $res['cdr']);
            return $res['mensaje'] ?? '';
        }

        $this->mensaje = $res['mensaje'] ?? 'Error en comunicación de baja';
        return $res['mensaje'] ?? '';
    }

    public function resumenDiarioPorEmpresa($ventas, $empresa, $fechaGene, $fechaResu, $correlativo)
    {
        $emp = $this->conexion->query(
            "SELECT * FROM empresas WHERE id_empresa='$empresa'"
        )->fetch_assoc();

        $sql = "SELECT v.id_venta, ds.cod_sunat, v.serie, v.numero,
                       c.documento, v.total
                FROM ventas AS v
                    INNER JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                    INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                WHERE " . implode(' OR ', $ventas);

        $result    = $this->conexion->query($sql);
        $detalles  = [];
        $ids_venta = [];

        while ($fila = $result->fetch_assoc()) {
            $doc_cliente = '00000000';
            if (strlen($fila['documento']) == 8) {
                $tipo_doc    = 1;
                $doc_cliente = $fila['documento'];
            } elseif (strlen($fila['documento']) == 11) {
                $tipo_doc    = 6;
                $doc_cliente = $fila['documento'];
            } else {
                $tipo_doc = 0;
            }

            $total   = (float)$fila['total'];
            $subtotal = round($total / 1.18, 2);
            $igv      = round($total / 1.18 * 0.18, 2);

            $detalles[]  = [
                'tipo_doc'            => $fila['cod_sunat'],
                'serie_numero'        => $fila['serie'] . '-' . $fila['numero'],
                'estado'              => 1,
                'tipo_doc_cliente'    => $tipo_doc,
                'num_doc_cliente'     => $doc_cliente,
                'total'               => round($total, 2),
                'mto_oper_gravadas'   => $subtotal,
                'mto_igv'             => $igv,
                'mto_oper_exoneradas' => 0,
                'mto_oper_inafectas'  => 0,
                'mto_otros_cargos'    => 0,
            ];
            $ids_venta[] = $fila['id_venta'];
        }

        if (empty($detalles)) {
            return ['res' => false, 'msg' => 'Sin items para el resumen'];
        }

        $this->subirCertificado($emp['ruc'], $emp['modo'] ?? 'beta');

        $body = [
            'endpoint'         => $emp['modo'] ?? 'beta',
            'correlativo'      => $correlativo,
            'fecha_generacion' => $fechaGene,
            'fecha_resumen'    => $fechaResu,
            'empresa'          => $this->mapearEmpresa($emp),
            'detalles'         => $detalles,
        ];

        $res = $this->post('enviar/resumen', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($emp['ruc'], $res['nombre_archivo'], $res['contenido_xml']);
            $this->guardarCDR($emp['ruc'], $res['nombre_archivo'], $res['cdr']);

            foreach ($ids_venta as $id) {
                $this->conexion->query("UPDATE ventas SET enviado_sunat='1' WHERE id_venta='$id'");
            }

            $fecha    = date('Y-m-d');
            $ticket   = $this->conexion->real_escape_string($res['ticket'] ?? '');
            $cantidad = count($ids_venta);
            $this->conexion->query("INSERT INTO resumen_diario SET
                id_empresa='{$emp['id_empresa']}',
                fecha='$fecha',
                ticket='$ticket',
                cantidad_items='$cantidad',
                tipo='1'");

            return ['res' => true, 'msg' => $res['mensaje'] ?? 'Resumen procesado'];
        }

        return ['res' => false, 'msg' => $res['mensaje'] ?? 'Error al enviar resumen'];
    }

    public function resumenDiarioBajaPorEmpresa($ventas, $empresa, $fechaGene, $fechaResu, $correlativo)
    {
        $emp = $this->conexion->query(
            "SELECT * FROM empresas WHERE id_empresa='$empresa'"
        )->fetch_assoc();

        $sql = "SELECT v.id_venta, ds.cod_sunat, v.serie, v.numero,
                       c.documento, v.total
                FROM ventas_anuladas AS va
                    INNER JOIN ventas AS v ON v.id_venta = va.id_venta
                    INNER JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                    INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                WHERE " . implode(' OR ', $ventas);

        $result    = $this->conexion->query($sql);
        $detalles  = [];
        $ids_venta = [];

        while ($fila = $result->fetch_assoc()) {
            $doc_cliente = '00000000';
            if (strlen($fila['documento']) == 8) {
                $tipo_doc    = 1;
                $doc_cliente = $fila['documento'];
            } elseif (strlen($fila['documento']) == 11) {
                $tipo_doc    = 6;
                $doc_cliente = $fila['documento'];
            } else {
                $tipo_doc = 0;
            }

            $total    = (float)$fila['total'];
            $subtotal = round($total / 1.18, 2);
            $igv      = round($total / 1.18 * 0.18, 2);

            $detalles[]  = [
                'tipo_doc'            => $fila['cod_sunat'],
                'serie_numero'        => $fila['serie'] . '-' . $fila['numero'],
                'estado'              => 3,
                'tipo_doc_cliente'    => $tipo_doc,
                'num_doc_cliente'     => $doc_cliente,
                'total'               => round($total, 2),
                'mto_oper_gravadas'   => $subtotal,
                'mto_igv'             => $igv,
                'mto_oper_exoneradas' => 0,
                'mto_oper_inafectas'  => 0,
                'mto_otros_cargos'    => 0,
            ];
            $ids_venta[] = $fila['id_venta'];
        }

        if (empty($detalles)) {
            return ['res' => false, 'msg' => 'Sin items para el resumen de baja'];
        }

        $this->subirCertificado($emp['ruc'], $emp['modo'] ?? 'beta');

        $body = [
            'endpoint'         => $emp['modo'] ?? 'beta',
            'correlativo'      => $correlativo,
            'fecha_generacion' => $fechaGene,
            'fecha_resumen'    => $fechaResu,
            'empresa'          => $this->mapearEmpresa($emp),
            'detalles'         => $detalles,
        ];

        $res = $this->post('enviar/resumen', $body);

        if (!empty($res['estado'])) {
            $this->guardarXML($emp['ruc'], $res['nombre_archivo'], $res['contenido_xml']);
            $this->guardarCDR($emp['ruc'], $res['nombre_archivo'], $res['cdr']);

            foreach ($ids_venta as $id) {
                $this->conexion->query("UPDATE ventas SET enviado_sunat='1' WHERE id_venta='$id'");
            }

            $fecha    = date('Y-m-d');
            $ticket   = $this->conexion->real_escape_string($res['ticket'] ?? '');
            $cantidad = count($ids_venta);
            $this->conexion->query("INSERT INTO resumen_diario SET
                id_empresa='{$emp['id_empresa']}',
                fecha='$fecha',
                ticket='$ticket',
                cantidad_items='$cantidad',
                tipo='1'");

            return ['res' => true, 'msg' => $res['mensaje'] ?? 'Resumen de baja procesado'];
        }

        return ['res' => false, 'msg' => $res['mensaje'] ?? 'Error al enviar resumen de baja'];
    }

    // ─── Envío de guía ────────────────────────────────────────────────────────

    /** Reemplaza SunatApi2::envioIndividualGuiaRemi() */
    public function envioIndividualGuiaRemi(string $nom_XML): bool
    {
        $empresa = $this->conexion->query(
            "SELECT * FROM empresas WHERE id_empresa = '{$_SESSION['id_empresa']}'"
        )->fetch_assoc();

        $xml_ruta = "files/facturacion/xml/{$empresa['ruc']}/{$nom_XML}.xml";
        if (!file_exists($xml_ruta)) {
            $this->mensaje = "No se encontró el XML de guía: {$nom_XML}";
            return false;
        }

        $body = [
            'endpoint'            => $empresa['modo'] ?? 'beta',
            'ruc'                 => $empresa['ruc'],
            'usuario'             => $empresa['user_sol'],
            'clave'               => $empresa['clave_sol'],
            'client_id'           => $empresa['client_id_sunat'] ?? '',
            'secret_client'       => $empresa['client_secret_sunat'] ?? '',
            'nombre_documento'    => $nom_XML,
            'contenido_documento' => file_get_contents($xml_ruta),
        ];

        $res = $this->post('enviar/guia/remision', $body);

        if (!empty($res['estado'])) {
            return true;
        }

        $this->mensaje = $res['mensaje'] ?? 'Error al enviar guía a SUNAT';
        return false;
    }
}
