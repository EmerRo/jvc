<?php
// Vars compartidas desde enviar_sunat.php: $emp, $conexion, $fecha

$sql = "SELECT gr.id_guia_remision, gs.nombre_xml
        FROM guia_remision gr
            JOIN guia_sunat gs ON gr.id_guia_remision = gs.id_guia
        WHERE gr.enviado_sunat = '0'
          AND gr.id_empresa = '{$emp['id_empresa']}'";

$listaGuia = $conexion->query($sql);

foreach ($listaGuia as $guia) {
    $nombre_archivo = $guia['nombre_xml'];
    $xml_ruta = __DIR__ . "/../files/facturacion/xml/" . $emp['ruc'] . '/' . $nombre_archivo . ".xml";

    if (!file_exists($xml_ruta)) {
        echo "XML guía no encontrado: $nombre_archivo\n<br>";
        continue;
    }

    $res = curlPostSunat('enviar/guia/remision', [
        'endpoint'            => $emp['modo'] ?? 'beta',
        'ruc'                 => $emp['ruc'],
        'usuario'             => $emp['user_sol'],
        'clave'               => $emp['clave_sol'],
        'client_id'           => $emp['client_id_sunat'] ?? '',
        'secret_client'       => $emp['client_secret_sunat'] ?? '',
        'nombre_documento'    => $nombre_archivo,
        'contenido_documento' => file_get_contents($xml_ruta),
    ]);

    if (!empty($res['estado'])) {
        echo "Enviado: $nombre_archivo\n<br>";
        $conexion->query("UPDATE guia_remision SET enviado_sunat='1' WHERE id_guia_remision='{$guia['id_guia_remision']}'");
    } else {
        echo "Error $nombre_archivo: " . ($res['mensaje'] ?? 'desconocido') . "\n<br>";
    }
}
