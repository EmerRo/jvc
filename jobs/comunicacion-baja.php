<?php
// Vars compartidas desde enviar_sunat.php: $emp, $conexion, $fecha

$sql = "SELECT v.id_venta, ds.cod_sunat, v.serie, v.numero
        FROM ventas_anuladas AS va
            INNER JOIN ventas AS v ON v.id_venta = va.id_venta
            INNER JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        WHERE v.id_empresa = '{$emp['id_empresa']}'
          AND v.fecha_emision = '$fecha'
          AND v.id_tido = 2";

$a_anulados = $conexion->query($sql);
$detalles   = [];

foreach ($a_anulados as $value) {
    $detalles[] = [
        'tipo_doc'    => $value['cod_sunat'],
        'serie'       => $value['serie'],
        'correlativo' => $value['numero'],
        'motivo'      => 'ERROR AL BUSCAR PRODUCTOS',
    ];
}

if (empty($detalles)) {
    $respuesta = "no hay registros";
} else {
    $res = curlPostSunat('enviar/comunicacion/baja', [
        'endpoint'           => $emp['modo'] ?? 'beta',
        'correlativo'        => $emp['id_empresa'] . '1',
        'fecha_generacion'   => $fecha,
        'fecha_comunicacion' => $fecha,
        'empresa'            => [
            'ruc'          => $emp['ruc'],
            'usuario'      => $emp['user_sol'],
            'clave'        => $emp['clave_sol'],
            'razon_social' => $emp['razon_social'],
            'direccion'    => $emp['direccion'],
            'ubigeo'       => $emp['ubigeo'],
            'distrito'     => $emp['distrito'],
            'provincia'    => $emp['provincia'],
            'departamento' => $emp['departamento'],
        ],
        'detalles' => $detalles,
    ]);

    if (!empty($res['estado'])) {
        $fileDir = __DIR__ . '/../files/facturacion/xml/' . $emp['ruc'];
        if (!file_exists($fileDir)) mkdir($fileDir, 0777, true);
        file_put_contents($fileDir . DIRECTORY_SEPARATOR . $res['nombre_archivo'] . '.xml', $res['contenido_xml']);

        $fileDir = __DIR__ . '/../files/facturacion/cdr/' . $emp['ruc'];
        if (!file_exists($fileDir)) mkdir($fileDir, 0777, true);
        file_put_contents($fileDir . DIRECTORY_SEPARATOR . 'R-' . $res['nombre_archivo'] . '.zip', base64_decode($res['cdr']));

        $respuesta = $res['nombre_archivo'] . ' — ' . ($res['mensaje'] ?? '');
    } else {
        $respuesta = "Error: " . ($res['mensaje'] ?? 'desconocido');
    }
}

echo $respuesta;
