<?php
// Vars compartidas desde enviar_sunat.php: $emp, $conexion, $fecha

$sql = "SELECT vs.* FROM ventas v
        JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
        WHERE v.fecha_emision = '$fecha'
          AND v.id_empresa = '{$emp['id_empresa']}'
          AND v.enviado_sunat = '0'
          AND v.id_tido = 2";

$tempListaFac = $conexion->query($sql);

foreach ($tempListaFac as $fac) {
    $nombre_archivo = $fac['nombre_xml'];
    $xml_ruta = __DIR__ . "/../files/facturacion/xml/" . $emp['ruc'] . '/' . $nombre_archivo . ".xml";

    if (!file_exists($xml_ruta)) {
        echo "XML no encontrado: $nombre_archivo\n<br>";
        continue;
    }

    $res = curlPostSunat('enviar/documento/electronico', [
        'endpoint'            => $emp['modo'] ?? 'beta',
        'ruc'                 => $emp['ruc'],
        'usuario'             => $emp['user_sol'],
        'clave'               => $emp['clave_sol'],
        'nombre_documento'    => $nombre_archivo,
        'contenido_documento' => file_get_contents($xml_ruta),
    ]);

    if (!empty($res['estado'])) {
        $fileDir = __DIR__ . '/../files/facturacion/cdr/' . $emp['ruc'];
        if (!file_exists($fileDir)) mkdir($fileDir, 0777, true);
        file_put_contents($fileDir . DIRECTORY_SEPARATOR . 'R-' . $nombre_archivo . '.zip', base64_decode($res['cdr']));
        echo "Exito: {$nombre_archivo}\n<br>";
        $conexion->query("UPDATE ventas SET enviado_sunat='1' WHERE id_venta='{$fac['id_venta']}'");
    } else {
        echo "Error {$nombre_archivo}: " . ($res['mensaje'] ?? 'desconocido') . "\n<br>";
    }

    sleep(2);
}
