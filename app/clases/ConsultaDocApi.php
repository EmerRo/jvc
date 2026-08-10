<?php

/**
 * ConsultaDocApi
 *
 * Consulta de documentos (DNI/RUC) y tipo de cambio SUNAT con doble proveedor:
 * primero ApisPeru (dniruc.apisperu.com) y, si falla, Decolecta (api.decolecta.com).
 *
 * Shape normalizado de salida (buscar):
 *   success, data{...}, nombres, apellidoPaterno, apellidoMaterno, nombre,
 *   razonSocial, ruc, numero, direccion, departamento, provincia, distrito, ubigeo
 * En caso de fallo total: ['success' => false].
 */
class ConsultaDocApi
{
    private const TOKEN_APISPERU = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InN5c3RlbWNyYWZ0LnBlQGdtYWlsLmNvbSJ9.yuNS5hRaC0hCwymX_PjXRoSZJWLNNBeOdlLRSUGlHGA';
    private const TOKEN_DECOLECTA = 'sk_1718.l1qUpcviPwgiLxHrsBnaWOHk3MqGnuKp';

    private const TIMEOUT = 8;

    /**
     * Consulta DNI (8 dígitos) o RUC (11 dígitos) con fallback ApisPeru -> Decolecta.
     *
     * @param string $doc
     * @return array
     */
    public function buscar($doc)
    {
        $resultado = $this->consultarApisPeru($doc);
        if ($resultado === null) {
            $resultado = $this->consultarDecolecta($doc);
        }

        return $resultado !== null ? $resultado : ['success' => false];
    }

    /**
     * Obtiene el tipo de cambio SUNAT (compra/venta) con fallback apis.net.pe -> Decolecta.
     * Devuelve ['success' => true, 'data' => ['compra', 'venta', 'fecha', ...]] o ['success' => false].
     *
     * @return array
     */
    public function tipoCambio()
    {
        $resultado = $this->consultarTipoCambioApisNetPe();
        if ($resultado === null) {
            $resultado = $this->consultarTipoCambioDecolecta();
        }

        return $resultado !== null ? $resultado : ['success' => false];
    }

    /**
     * @param string $doc
     * @return array|null Shape normalizado o null si falla
     */
    private function consultarApisPeru($doc)
    {
        $esDni = strlen($doc) === 8;
        $url = 'https://dniruc.apisperu.com/api/v1/'
            . ($esDni ? 'dni/' : 'ruc/')
            . rawurlencode($doc)
            . '?token=' . self::TOKEN_APISPERU;

        $data = $this->requestJson($url);
        if ($data === null || empty($data['success'])) {
            return null;
        }

        $info = $data['data'] ?? [];
        if ($esDni) {
            $nombres = $info['nombres'] ?? '';
            $paterno = $info['apellidoPaterno'] ?? '';
            $materno = $info['apellidoMaterno'] ?? '';
            $nombre = $info['nombre'] ?? trim($nombres . ' ' . $paterno . ' ' . $materno);

            return [
                'success' => true,
                'data' => $info,
                'numero' => $info['numero'] ?? $doc,
                'nombre' => $nombre,
                'nombres' => $nombres,
                'apellidoPaterno' => $paterno,
                'apellidoMaterno' => $materno,
                'razonSocial' => '',
                'ruc' => '',
                'direccion' => '',
                'departamento' => '',
                'provincia' => '',
                'distrito' => '',
                'ubigeo' => '',
            ];
        }

        return [
            'success' => true,
            'data' => $info,
            'ruc' => $info['ruc'] ?? $doc,
            'numero' => $info['ruc'] ?? $doc,
            'razonSocial' => $info['razonSocial'] ?? '',
            'nombre' => $info['razonSocial'] ?? '',
            'nombres' => '',
            'apellidoPaterno' => '',
            'apellidoMaterno' => '',
            'direccion' => $info['direccion'] ?? '',
            'departamento' => $info['departamento'] ?? '',
            'provincia' => $info['provincia'] ?? '',
            'distrito' => $info['distrito'] ?? '',
            'ubigeo' => $info['ubigeo'] ?? '',
        ];
    }

    /**
     * @param string $doc
     * @return array|null Shape normalizado o null si falla
     */
    private function consultarDecolecta($doc)
    {
        $esDni = strlen($doc) === 8;
        $url = 'https://api.decolecta.com/v1/'
            . ($esDni ? 'reniec/dni' : 'sunat/ruc')
            . '?numero=' . rawurlencode($doc);

        $data = $this->requestJson($url, self::TOKEN_DECOLECTA);
        if ($data === null || empty($data['document_number']) && empty($data['numero_documento'])) {
            return null;
        }

        if ($esDni) {
            $nombres = $data['first_name'] ?? '';
            $paterno = $data['first_last_name'] ?? '';
            $materno = $data['second_last_name'] ?? '';
            $nombre = $data['full_name'] ?? trim($nombres . ' ' . $paterno . ' ' . $materno);

            return [
                'success' => true,
                'data' => $data,
                'numero' => $data['document_number'] ?? $doc,
                'nombre' => $nombre,
                'nombres' => $nombres,
                'apellidoPaterno' => $paterno,
                'apellidoMaterno' => $materno,
                'razonSocial' => '',
                'ruc' => '',
                'direccion' => '',
                'departamento' => '',
                'provincia' => '',
                'distrito' => '',
                'ubigeo' => '',
            ];
        }

        return [
            'success' => true,
            'data' => $data,
            'ruc' => $data['numero_documento'] ?? $doc,
            'numero' => $data['numero_documento'] ?? $doc,
            'razonSocial' => $data['razon_social'] ?? '',
            'nombre' => $data['razon_social'] ?? '',
            'nombres' => '',
            'apellidoPaterno' => '',
            'apellidoMaterno' => '',
            'direccion' => $data['direccion'] ?? '',
            'departamento' => $data['departamento'] ?? '',
            'provincia' => $data['provincia'] ?? '',
            'distrito' => $data['distrito'] ?? '',
            'ubigeo' => $data['ubigeo'] ?? '',
        ];
    }

    /**
     * @return array|null ['success' => true, 'data' => [...]] o null si falla
     */
    private function consultarTipoCambioApisNetPe()
    {
        $data = $this->requestJson('https://api.apis.net.pe/v1/tipo-cambio-sunat');
        if ($data === null || empty($data['venta'])) {
            return null;
        }

        return ['success' => true, 'data' => $data];
    }

    /**
     * @return array|null ['success' => true, 'data' => ['compra', 'venta', 'fecha']] o null si falla
     */
    private function consultarTipoCambioDecolecta()
    {
        $data = $this->requestJson(
            'https://api.decolecta.com/v1/tipo-cambio/sunat',
            self::TOKEN_DECOLECTA
        );
        if ($data === null || empty($data['sell_price'])) {
            return null;
        }

        return [
            'success' => true,
            'data' => [
                'compra' => $data['buy_price'] ?? '',
                'venta' => $data['sell_price'] ?? '',
                'fecha' => $data['date'] ?? '',
                'moneda' => $data['base_currency'] ?? 'USD',
                'fuente' => 'Decolecta',
            ],
        ];
    }

    /**
     * GET JSON con cURL. Devuelve el array decodificado o null ante cualquier error.
     *
     * @param string $url
     * @param string|null $tokenSi se envía, se agrega header Authorization: Bearer
     * @return array|null
     */
    private function requestJson($url, $token = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);

        $headers = ['Accept: application/json'];
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }
}
