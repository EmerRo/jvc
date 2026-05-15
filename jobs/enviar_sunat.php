<?php
date_default_timezone_set('America/Lima');

require_once __DIR__ . "/../utils/config.php";
require_once __DIR__ . "/../config/Conexion.php";

function curlPostSunat(string $endpoint, array $data): array
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

function subirCertificadoApiJob(string $ruc): void
{
    $cert_path = __DIR__ . "/../files/facturacion/certificados/{$ruc}-cert.pem";
    if (!file_exists($cert_path)) return;

    $url = rtrim(URL_API_SUNAT, '/') . '/api/v1/guardar/certificado/' . $ruc;
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['certificado' => base64_encode(file_get_contents($cert_path))]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_exec($ch);
    curl_close($ch);
}

$conexion = (new Conexion())->getConexion();
$fecha    = date("Y-m-d");

$sql    = "SELECT e.* FROM usuarios u JOIN empresas e ON e.id_empresa = u.id_empresa GROUP BY e.id_empresa";
$result = $conexion->query($sql);

foreach ($result as $emp) {
    ob_start();

    if (file_exists(__DIR__ . "/../files/facturacion/certificados/" . $emp['ruc'] . "-cert.pem")) {

        if ($emp['ruc'] != '') {
            subirCertificadoApiJob($emp['ruc']);

            echo "Enviando facturas......\n<br>";
            require __DIR__ . '/facturas.php';

            echo "Enviando resumen diario......<br>\n";
            sleep(2);
            require __DIR__ . '/resumen_diario.php';

            echo "Enviando resumen diario anulados......<br>\n";
            sleep(2);
            require __DIR__ . '/resumen_diario_baja.php';

            echo "Enviando comunicacion baja......<br>\n";
            sleep(2);
            require __DIR__ . '/comunicacion-baja.php';

            echo "Enviando Guias Remision......<br>\n";
            sleep(2);
            require __DIR__ . '/guias_remision.php';
        }

    } else {
        echo "\n<br>Alerta: el RUC " . $emp['ruc'] . " no cuenta con certificado digital";
    }

    sleep(2);

    $output = ob_get_contents();
    ob_end_clean();

    $dirSaveFile = __DIR__ . "/../files/log/sunat/" . date("Y_m_d");
    if (!file_exists($dirSaveFile)) {
        mkdir($dirSaveFile, 0777, true);
    }
    file_put_contents($dirSaveFile . DIRECTORY_SEPARATOR . $emp["ruc"] . ".log", $output);
}

echo "\n<br> termino.....";
