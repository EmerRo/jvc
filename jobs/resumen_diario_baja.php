<?php
// Vars compartidas desde enviar_sunat.php: $emp, $conexion, $fecha

$sql = "SELECT v.id_venta, ds.cod_sunat, v.serie, v.numero, c.documento, v.total
        FROM ventas_anuladas AS va
            INNER JOIN ventas AS v ON v.id_venta = va.id_venta
            INNER JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        WHERE v.id_empresa = '{$emp['id_empresa']}'
          AND v.fecha_emision = '$fecha'
          AND v.id_tido = 1";

$resultado_empresa = $conexion->query($sql);
$detalles  = [];
$ids_venta = [];

foreach ($resultado_empresa as $fila) {
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

    echo $fila['cod_sunat'] . ' | ' . $fila['serie'] . '-' . $fila['numero'] . PHP_EOL;

    $detalles[]  = [
        'tipo_doc'         => $fila['cod_sunat'],
        'serie_numero'     => $fila['serie'] . '-' . $fila['numero'],
        'estado'           => 3,
        'tipo_doc_cliente' => $tipo_doc,
        'num_doc_cliente'  => $doc_cliente,
        'total'            => round($total, 2),
        'mto_oper_gravadas'=> $subtotal,
        'mto_igv'          => $igv,
    ];
    $ids_venta[] = $fila['id_venta'];
}

if (empty($detalles)) {
    echo "\nResumen baja '$fecha' empresa {$emp['ruc']} sin items\n<br>";
} else {
    $res = curlPostSunat('enviar/resumen', [
        'endpoint'         => $emp['modo'] ?? 'beta',
        'correlativo'      => '002',
        'fecha_generacion' => $fecha,
        'fecha_resumen'    => $fecha,
        'empresa'          => [
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

        $conexion->query("INSERT INTO resumen_diario SET
            id_empresa='{$emp['id_empresa']}',
            fecha='$fecha',
            ticket='" . $conexion->real_escape_string($res['ticket'] ?? '') . "',
            cantidad_items='" . count($detalles) . "',
            tipo='1'");

        echo "\n" . $res['nombre_archivo'] . "\n" . ($res['mensaje'] ?? '') . "\n<br>";
    } else {
        echo "Error resumen baja: " . ($res['mensaje'] ?? 'desconocido') . "\n<br>";
    }
}
