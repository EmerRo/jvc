<?php

class CotizacionesController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }
    public function eliminarCotizacion()
    {
        //var_dump($_POST);
        $sql = "update cotizaciones set estado = '2' where cotizacion_id = '{$_POST['cod']}';";
        $this->conexion->query($sql);
        echo "{}";
    }
    public function actualizar()
    {
        $respuesta = ["res" => false];
        try {
            // Validar cliente
            $sql = "SELECT * from clientes where documento ='{$_POST['num_doc']}'";
            $idCli = '';

            if ($rowCl = $this->conexion->query($sql)->fetch_assoc()) {
                $idCli = $rowCl['id_cliente'];

                // Actualizar datos del cliente existente
                $sql = "UPDATE clientes SET 
                    datos = '" . $this->conexion->real_escape_string($_POST['nom_cli']) . "',
                    direccion = '" . $this->conexion->real_escape_string($_POST['dir_cli']) . "',
                    id_empresa = '{$_SESSION['id_empresa']}'
                    WHERE id_cliente = '$idCli'";

                if (!$this->conexion->query($sql)) {
                    error_log("Error actualizando cliente: " . $this->conexion->error);
                }
            } else {
                // Crear nuevo cliente si no existe
                $sql = "INSERT INTO clientes SET 
                    documento = '" . $this->conexion->real_escape_string($_POST['num_doc']) . "',
                    datos = '" . $this->conexion->real_escape_string($_POST['nom_cli']) . "',
                    direccion = '" . $this->conexion->real_escape_string($_POST['dir_cli']) . "',
                    id_empresa = '{$_SESSION['id_empresa']}'";

                if (!$this->conexion->query($sql)) {
                    throw new Exception("Error al crear cliente: " . $this->conexion->error);
                }
                $idCli = $this->conexion->insert_id;
            }

            // Manejar el asunto
            $idAsunto = null;
            if (!empty($_POST['asunto'])) {
                // Verificar si el asunto ya existe
                $asuntoNombre = $this->conexion->real_escape_string($_POST['asunto']);
                $sql = "SELECT id_asunto FROM asuntos_coti WHERE nombre = '$asuntoNombre' AND id_empresa = '{$_SESSION['id_empresa']}'";
                $resultAsunto = $this->conexion->query($sql);

                if ($rowAsunto = $resultAsunto->fetch_assoc()) {
                    $idAsunto = $rowAsunto['id_asunto'];
                } else {
                    // Crear nuevo asunto
                    $sql = "INSERT INTO asuntos_coti (nombre, id_empresa) VALUES ('$asuntoNombre', '{$_SESSION['id_empresa']}')";
                    $this->conexion->query($sql);
                    $idAsunto = $this->conexion->insert_id;
                }
            }

            // Actualizar cotización
            $sql = "UPDATE cotizaciones SET 
                id_tipo_pago = '{$_POST['tipo_pago']}',
                fecha = '{$_POST['fecha']}',
                dias_pagos = '{$_POST['dias_pago']}',
                direccion = '{$_POST['dir_pos']}',
                id_cliente = '$idCli',
                usar_precio = '{$_POST['usar_precio']}',
                total = '{$_POST['total']}',
                descuento = '" . (isset($_POST['descuentoGeneral']) ? $_POST['descuentoGeneral'] : 0) . "',
                id_asunto = " . ($idAsunto ? "'$idAsunto'" : "NULL") . "
                WHERE cotizacion_id = '{$_POST['cotiId']}'";

            if (!$this->conexion->query($sql)) {
                throw new Exception("Error al actualizar cotización: " . $this->conexion->error);
            }

            // Eliminar productos y cuotas existentes
            $this->conexion->query("DELETE FROM productos_cotis WHERE id_coti = '{$_POST['cotiId']}'");
            $this->conexion->query("DELETE FROM cuotas_cotizacion WHERE id_coti = '{$_POST['cotiId']}'");

            // Si hay pago inicial, insertarlo primero
            if (isset($_POST['tiene_inicial']) && $_POST['tiene_inicial'] && isset($_POST['monto_inicial'])) {
                $stmt = $this->conexion->prepare("INSERT INTO cuotas_cotizacion (id_coti, monto, fecha, tipo) VALUES (?, ?, ?, 'inicial')");
                $stmt->bind_param('ids', $_POST['cotiId'], $_POST['monto_inicial'], $_POST['fecha']);
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar pago inicial: " . $stmt->error);
                }
                $stmt->close();
            }

            // Insertar cuotas actualizadas
            $cuotas = json_decode($_POST['dias_lista'], true);
            foreach ($cuotas as $cuota) {
                $stmt = $this->conexion->prepare("INSERT INTO cuotas_cotizacion (id_coti, monto, fecha, tipo) VALUES (?, ?, ?, 'cuota')");
                $stmt->bind_param('ids', $_POST['cotiId'], $cuota['monto'], $cuota['fecha']);
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar cuota: " . $stmt->error);
                }
                $stmt->close();
            }

            // Insertar productos actualizados
            $productos = json_decode($_POST['listaPro'], true);

            // Insertar productos actualizados
            foreach ($productos as $prod) {
                $tipo = isset($prod['tipo']) ? $prod['tipo'] : 'producto';

                // Determinar la tabla correcta para actualizar
                $tabla = ($tipo === 'repuesto') ? 'repuestos' : 'productos';
                $id_campo = ($tipo === 'repuesto') ? 'id_repuesto' : 'id_producto';

                // Primero actualizar el producto/repuesto principal
                $sql_update = "UPDATE {$tabla} SET 
                    nombre = '" . $this->conexion->real_escape_string($prod['nombre']) . "',
                    detalle = '" . $this->conexion->real_escape_string($prod['detalle'] ?? '') . "'
                    WHERE {$id_campo} = " . intval($prod['productoid']);

                if (!$this->conexion->query($sql_update)) {
                    error_log("Error actualizando {$tabla}: " . $this->conexion->error);
                }

                // Luego insertar en productos_cotis
                $precioEspecial = isset($prod['precioEspecial']) && $prod['precioEspecial'] !== '' ?
                    $this->conexion->real_escape_string($prod['precioEspecial']) : 'NULL';

                $precioVenta = isset($prod['precioVenta']) ?
                    $this->conexion->real_escape_string($prod['precioVenta']) :
                    $this->conexion->real_escape_string($prod['precio']);

                $cantidad = $this->conexion->real_escape_string($prod['cantidad']);
                $costo = isset($prod['costo']) ? $this->conexion->real_escape_string($prod['costo']) : '0';

                $sql = "INSERT INTO productos_cotis 
                    (id_producto, id_coti, cantidad, precio, costo, precioEspecial, tipo_producto) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param(
                    'iidddss',
                    $prod['productoid'],
                    $_POST['cotiId'],
                    $cantidad,
                    $precioVenta,
                    $costo,
                    $precioEspecial,
                    $tipo
                );

                if (!$stmt->execute()) {
                    error_log("Error al insertar producto/repuesto: " . $stmt->error);
                    error_log("Datos del producto/repuesto: " . json_encode($prod));
                    throw new Exception("Error al insertar producto/repuesto: " . $stmt->error);
                }

                $stmt->close();
            }

            $respuesta['res'] = true;
            $respuesta['cotizacion'] = [
                'numero' => $_POST['numero'],
                'cotizacion_id' => $_POST['cotiId'],
                'pdfUrl' => URL::to('/r/cotizaciones/reporte/' . $_POST['cotiId'])
            ];

        } catch (Exception $e) {
            $respuesta['error'] = $e->getMessage();
            error_log("Error en actualizar(): " . $e->getMessage());
        }

        return json_encode($respuesta);
    }


    // treer datos de la cotizacion
    public function getInformacion()
    {
        error_log("=== Inicio de getInformacion() ===");

        try {
            if (!isset($_POST['coti'])) {
                throw new Exception('ID de cotización no proporcionado');
            }

            $cotizacion = $_POST['coti'];
            error_log("Cotización ID: " . $cotizacion);

            $data = [];

            // Obtener datos de cotización
            $sql = "SELECT c.*, a.nombre as asunto_nombre 
                    FROM cotizaciones c 
                    LEFT JOIN asuntos_coti a ON c.id_asunto = a.id_asunto 
                    WHERE c.cotizacion_id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('Cotización no encontrada');
            }

            $data = $result->fetch_assoc();
            $data['asunto'] = $data['asunto_nombre']; // Agregar el asunto a los datos

            // Obtener cuotas
            $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $cotizacion);
            $stmt->execute();
            $cuotasR = $stmt->get_result();

            $data["cuotas"] = [];
            while ($cuota = $cuotasR->fetch_assoc()) {
                $data["cuotas"][] = [
                    'cuotaid' => $cuota['cuota_coti_id'],
                    'fecha' => $cuota['fecha'],
                    'monto' => $cuota['monto']
                ];
            }

            // Obtener datos del cliente
            if (!empty($data['id_cliente'])) {
                $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param("i", $data['id_cliente']);
                $stmt->execute();
                $clienteR = $stmt->get_result();

                if ($cliente = $clienteR->fetch_assoc()) {
                    $data["cliente_doc"] = $cliente['documento'] ?? '';
                    $data["cliente_nom"] = $cliente['datos'] ?? '';
                    $data["cliente_dir1"] = $cliente['direccion'] ?? '';
                }
            }

            // Obtener productos
            $sql = "SELECT 
                pc.*,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.codigo
                    WHEN pc.tipo_producto = 'repuesto' THEN r.codigo
                END as codigo_pp,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.nombre
                    WHEN pc.tipo_producto = 'repuesto' THEN r.nombre
                END as nombre,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.descripcion
                    WHEN pc.tipo_producto = 'repuesto' THEN r.detalle
                END as descripcion,
                pc.id_producto as productoid,
                 CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.detalle
                    WHEN pc.tipo_producto = 'repuesto' THEN r.detalle
                END as detalle,
                pc.id_producto as productoid,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.precio
                    WHEN pc.tipo_producto = 'repuesto' THEN r.precio
                END as precio_original,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.precio2
                    WHEN pc.tipo_producto = 'repuesto' THEN r.precio2
                END as precio2,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.precio3
                    WHEN pc.tipo_producto = 'repuesto' THEN r.precio3
                END as precio3,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.precio4
                    WHEN pc.tipo_producto = 'repuesto' THEN r.precio4
                END as precio4,
                CASE 
                    WHEN pc.tipo_producto = 'producto' THEN p.precio_unidad
                    WHEN pc.tipo_producto = 'repuesto' THEN r.precio_unidad
                END as precio_unidad
            FROM productos_cotis pc
            LEFT JOIN productos p ON p.id_producto = pc.id_producto AND pc.tipo_producto = 'producto'
            LEFT JOIN repuestos r ON r.id_repuesto = pc.id_producto AND pc.tipo_producto = 'repuesto'
            WHERE pc.id_coti = ?";

            error_log("Consultando productos de cotización: $cotizacion");

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $cotizacion);
            $stmt->execute();
            $productosR = $stmt->get_result();

            $data["productos"] = [];

            while ($pro = $productosR->fetch_assoc()) {
                $nombre = !empty($pro['nombre']) ? $pro['nombre'] : (!empty($pro['descripcion']) ? $pro['descripcion'] : 'Sin nombre');
                $cantidad = $pro['cantidad'];

                $data["productos"][] = [
                    "codigo_pp" => $pro['codigo_pp'] ?? '',
                    "productoid" => $pro['productoid'],
                    "descripcion" => $nombre,
                    "nombre" => $nombre,
                    "nom_prod" => $nombre,
                    "cantidad" => $cantidad,
                    "stock" => 0,
                    "precioVenta" => number_format((float) ($pro['precio'] ?? 0), 2, '.', ''),
                    "precio" => number_format((float) ($pro['precio'] ?? 0), 2, '.', ''),
                    "precio2" => number_format((float) ($pro['precio2'] ?? 0), 2, '.', ''),
                    "precio3" => number_format((float) ($pro['precio3'] ?? 0), 2, '.', ''),
                    "precio4" => number_format((float) ($pro['precio4'] ?? 0), 2, '.', ''),
                    "precio_unidad" => number_format((float) ($pro['precio_unidad'] ?? 0), 2, '.', ''),
                    "codigo" => $pro['codigo_pp'] ?? '',
                    "codsunat" => $pro['codsunat'] ?? '',
                    "costo" => number_format((float) ($pro['costo'] ?? 0), 2, '.', ''),
                    "precio_usado" => $data['usar_precio'] ?? '5',
                    "edicion" => false,
                    "tipo" => $pro['tipo_producto'],
                    "precioEspecial" => number_format((float) ($pro['precioEspecial'] ?? 0), 2, '.', ''),
                    "detalle" => $pro['detalle'] ?? ''
                ];
            }

            error_log("Datos recuperados exitosamente para la cotización: $cotizacion");
            return json_encode($data);

        } catch (Exception $e) {
            error_log("Error en getInformacion: " . $e->getMessage());
            return json_encode([
                'error' => true,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function agregar()
    {
        error_log("Iniciando agregar cotización");
        error_log("POST data: " . print_r($_POST, true));
        // Inicializar respuesta
        $respuesta = ["res" => false];

        try {
            // SECCIÓN 1: MANEJO DEL CLIENTE
            // Asegurarse que el descuento tenga un valor válido
            $descuento = isset($_POST['descuento']) && $_POST['descuento'] !== ''
                ? $this->conexion->real_escape_string($_POST['descuento'])
                : 0;

            // Escapar datos del cliente para prevenir inyección SQL
            $num_doc = $this->conexion->real_escape_string($_POST['num_doc']);
            $nom_cli = $this->conexion->real_escape_string($_POST['nom_cli']);
            $dir_cli = $this->conexion->real_escape_string($_POST['dir_cli']);
            $departamento = $this->conexion->real_escape_string($_POST['departamento'] ?? '');
            $provincia = $this->conexion->real_escape_string($_POST['provincia'] ?? '');
            $distrito = $this->conexion->real_escape_string($_POST['distrito'] ?? '');
            $ubigeo = $this->conexion->real_escape_string($_POST['ubigeo'] ?? '');

            // Buscar si el cliente existe
            $sql = "SELECT * FROM clientes WHERE documento = '$num_doc'";
            $result = $this->conexion->query($sql);
            $idCli = '';

            if ($rowCl = $result->fetch_assoc()) {
                // Si el cliente existe, obtener su ID
                $idCli = $rowCl['id_cliente'];

                // Actualizar la información del cliente existente
                $sql = "UPDATE clientes SET 
                    datos = '$nom_cli',
                    direccion = '$dir_cli',
                    departamento = '$departamento',
                    provincia = '$provincia',
                    distrito = '$distrito',
                    ubigeo = '$ubigeo'
                    WHERE id_cliente = '$idCli'";

                if (!$this->conexion->query($sql)) {
                    error_log("Error actualizando cliente: " . $this->conexion->error);
                    throw new Exception("Error actualizando cliente: " . $this->conexion->error);
                }
            } else {
                // Si el cliente no existe, crear uno nuevo
                $sql = "INSERT INTO clientes SET 
                    documento = '$num_doc',
                    datos = '$nom_cli',
                    direccion = '$dir_cli',
                    departamento = '$departamento',
                    provincia = '$provincia',
                    distrito = '$distrito',
                    ubigeo = '$ubigeo',
                    id_empresa = '{$_SESSION['id_empresa']}'";

                if (!$this->conexion->query($sql)) {
                    error_log("Error creando cliente: " . $this->conexion->error);
                    throw new Exception("Error creando cliente: " . $this->conexion->error);
                }

                $idCli = $this->conexion->insert_id;
                error_log("Nuevo cliente creado con ID: $idCli");
            }

            // SECCIÓN 1.5: MANEJO DEL ASUNTO
            $idAsunto = null;
            if (!empty($_POST['asunto'])) {
                // Verificar si el asunto ya existe
                $asuntoNombre = $this->conexion->real_escape_string($_POST['asunto']);
                $sql = "SELECT id_asunto FROM asuntos_coti WHERE nombre = '$asuntoNombre' AND id_empresa = '{$_SESSION['id_empresa']}'";
                $resultAsunto = $this->conexion->query($sql);

                if ($rowAsunto = $resultAsunto->fetch_assoc()) {
                    $idAsunto = $rowAsunto['id_asunto'];
                } else {
                    // Crear nuevo asunto
                    $sql = "INSERT INTO asuntos_coti (nombre, id_empresa) VALUES ('$asuntoNombre', '{$_SESSION['id_empresa']}')";
                    $this->conexion->query($sql);
                    $idAsunto = $this->conexion->insert_id;
                }
            }

            // SECCIÓN 2: MANEJO DE LA COTIZACIÓN
            // Obtener el número de cotización
            $sql = "SELECT * FROM cotizaciones WHERE id_empresa='{$_SESSION['id_empresa']}' ORDER BY numero DESC LIMIT 1";
            $numCoti = 1;

            if ($roTemp = $this->conexion->query($sql)->fetch_assoc()) {
                $numCoti = $roTemp["numero"] + 1;
            }

            // Escapar datos de la cotización
            $tipo_doc = $this->conexion->real_escape_string($_POST['tipo_doc']);
            $moneda = $this->conexion->real_escape_string($_POST['moneda']);
            $tc = $this->conexion->real_escape_string($_POST['tc']);
            $tipo_pago = $this->conexion->real_escape_string($_POST['tipo_pago']);
            $fecha = $this->conexion->real_escape_string($_POST['fecha']);
            $dias_pago = $this->conexion->real_escape_string($_POST['dias_pago']);
            $dir_pos = $this->conexion->real_escape_string($_POST['dir_pos']);
            $total = $this->conexion->real_escape_string($_POST['total']);
            $usar_precio = $this->conexion->real_escape_string($_POST['usar_precio']);

            // Insertar la nueva cotización
            $sql = "INSERT INTO cotizaciones SET 
                id_tido = '$tipo_doc',
                moneda = '$moneda',
                cm_tc = '$tc',
                id_tipo_pago = '$tipo_pago',
                fecha = '$fecha',
                dias_pagos = '$dias_pago',
                direccion = '$dir_pos',
                id_cliente = '$idCli',
                total = '$total',
                descuento = '$descuento',
                numero = '$numCoti',
                estado = '0',
                usar_precio = '$usar_precio',
                sucursal = '{$_SESSION['sucursal']}',
                id_empresa = '{$_SESSION['id_empresa']}',
                id_usuario = '{$_SESSION['usuario_fac']}',
                id_asunto = " . ($idAsunto ? "'$idAsunto'" : "NULL");

            if ($this->conexion->query($sql)) {

                // Obtener el ID de la cotización recién creada
                $idCoti = $this->conexion->insert_id;
                error_log("Nueva cotización creada con ID: $idCoti");

                if (isset($_SESSION['temp_condiciones'])) {
                    $condiciones = $this->conexion->real_escape_string($_SESSION['temp_condiciones']);
                    $sql = "INSERT INTO condiciones_cotizacion (id_cotizacion, condiciones) VALUES ('$idCoti', '$condiciones')";
                    $this->conexion->query($sql);

                    // Limpiar las condiciones temporales
                    unset($_SESSION['temp_condiciones']);
                } else {
                    // Si no hay condiciones temporales, copiar las predeterminadas
                    $sql = "SELECT nombre FROM condicion LIMIT 1";
                    $resultado = $this->conexion->query($sql);
                    if ($row = $resultado->fetch_assoc()) {
                        $condiciones = $this->conexion->real_escape_string($row['nombre']);
                        $sql = "INSERT INTO condiciones_cotizacion (id_cotizacion, condiciones) VALUES ('$idCoti', '$condiciones')";
                        $this->conexion->query($sql);
                    }
                }



                // SECCIÓN 3: MANEJO DE CUOTAS
                $listaCuotas = json_decode($_POST['dias_lista'], true);

                // Si hay pago inicial, insertarlo primero
                if (isset($_POST['tiene_inicial']) && $_POST['tiene_inicial'] && isset($_POST['monto_inicial'])) {
                    $monto_inicial = $this->conexion->real_escape_string($_POST['monto_inicial']);
                    $stmt = $this->conexion->prepare("INSERT INTO cuotas_cotizacion (id_coti, monto, fecha, tipo) VALUES (?, ?, ?, 'inicial')");
                    $stmt->bind_param('ids', $idCoti, $monto_inicial, $fecha);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar pago inicial: " . $stmt->error);
                    }
                    $stmt->close();
                }

                // Insertar el resto de las cuotas
                foreach ($listaCuotas as $cuota) {
                    $monto_cuota = $this->conexion->real_escape_string($cuota['monto']);
                    $fecha_cuota = $this->conexion->real_escape_string($cuota['fecha']);

                    $stmt = $this->conexion->prepare("INSERT INTO cuotas_cotizacion (id_coti, monto, fecha, tipo) VALUES (?, ?, ?, 'cuota')");
                    $stmt->bind_param('ids', $idCoti, $monto_cuota, $fecha_cuota);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar cuota: " . $stmt->error);
                    }
                    $stmt->close();
                }

                // SECCIÓN 4: MANEJO DE PRODUCTOS
                // Decodificar los productos del JSON
                $listaProd = json_decode($_POST['listaPro'], true);

                // Insertar cada producto
                foreach ($listaProd as $prod) {
                    try {
                        // Determinar si es producto o repuesto
                        $tipo = isset($prod['tipo']) ? $prod['tipo'] : 'producto';
                        $id_campo = ($tipo === 'repuesto') ? 'id_repuesto' : 'id_producto';
                        $id_valor = isset($prod[$id_campo]) ? $prod[$id_campo] : $prod['productoid'];

                        // Preparar la consulta SQL con placeholders
                        $sql = "INSERT INTO productos_cotis 
                            (id_coti, id_producto, cantidad, precio, costo, precioEspecial, tipo_producto) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";

                        // Preparar el statement
                        $stmt = $this->conexion->prepare($sql);
                        if (!$stmt) {
                            throw new Exception("Error en prepare: " . $this->conexion->error);
                        }

                        // Procesar precioEspecial
                        $precioEspecial = null; // Valor por defecto NULL
                        if (isset($prod['precioEspecial']) && $prod['precioEspecial'] !== '' && $prod['precioEspecial'] !== null) {
                            $precioEspecial = number_format((float) $prod['precioEspecial'], 2, '.', '');
                        }

                        // Obtener valores para los parámetros
                        $cantidad = isset($prod['cantidad']) ? $prod['cantidad'] : 0;
                        $precioVenta = isset($prod['precioVenta']) ? $prod['precioVenta'] : (isset($prod['precio']) ? $prod['precio'] : 0);
                        $costo = isset($prod['costo']) ? $prod['costo'] : 0;

                        // Vincular los parámetros
                        $stmt->bind_param(
                            'iidddss',
                            $idCoti,                    // id_coti (i)
                            $id_valor,                  // id_producto (i)
                            $cantidad,                  // cantidad (i)
                            $precioVenta,               // precio (d)
                            $costo,                     // costo (d)
                            $precioEspecial,            // precioEspecial (d)
                            $tipo                       // tipo_producto (s)
                        );

                        // Ejecutar la consulta
                        if (!$stmt->execute()) {
                            throw new Exception("Error al insertar producto: " . $stmt->error);
                        }

                        // Cerrar el statement
                        $stmt->close();

                    } catch (Exception $e) {
                        // Log del error específico del producto
                        error_log("Error en inserción de producto: " . $e->getMessage());
                        throw $e; // Re-lanzar la excepción para el manejo superior
                    }
                }

                // Si llegamos aquí, todo se insertó correctamente
                $respuesta['res'] = true;
                $respuesta['cotizacion'] = [
                    'numero' => $numCoti,
                    'cotizacion_id' => $idCoti,
                    'pdfUrl' => URL::to('/r/cotizaciones/reporte/' . $idCoti)
                ];
            } else {
                throw new Exception("Error al insertar cotización: " . $this->conexion->error);
            }

        } catch (Exception $e) {
            // Capturar cualquier error y agregarlo a la respuesta
            $respuesta['error'] = $e->getMessage();
            error_log("Error en agregar(): " . $e->getMessage());
        }

        // Devolver la respuesta en formato JSON
        return json_encode($respuesta);
    }

    public function listar()
    {
        $sql = "select v.cotizacion_id,v.numero, v.fecha,v.moneda,v.cm_tc, 
           v.id_tido, c.documento, c.datos, v.total, v.estado
            from cotizaciones as v
                LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
                LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            where v.id_empresa = '12'  and v.sucursal ='{$_SESSION['sucursal']}' and v.estado<>'2'
            order by v.fecha asc ";
        //echo $sql;

        $rest = $this->conexion->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $lista[] = $row;
        }
        return json_encode($lista);
    }

    public function getVendedores()
    {
        $sql = "SELECT usuario_id,nombres from usuarios where id_rol =  1";
        //echo $sql;

        $rest = $this->conexion->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $lista[] = $row;
        }
        return json_encode($lista);
    }
    public function getAsuntos()
    {
        $sql = "SELECT id_asunto, nombre FROM asuntos_coti WHERE id_empresa = '{$_SESSION['id_empresa']}' ORDER BY nombre ASC";
        $result = $this->conexion->query($sql);
        $asuntos = [];

        while ($row = $result->fetch_assoc()) {
            $asuntos[] = $row;
        }

        return json_encode($asuntos);
    }


    // Agregar método para crear un nuevo asunto
    public function crearAsunto()
    {
        if (!isset($_POST['nombre']) || empty($_POST['nombre'])) {
            return json_encode(['success' => false, 'message' => 'El nombre del asunto es requerido']);
        }

        $nombre = $this->conexion->real_escape_string($_POST['nombre']);
        $id_empresa = $_SESSION['id_empresa'];

        $sql = "INSERT INTO asuntos_coti (nombre, id_empresa) VALUES ('$nombre', '$id_empresa')";

        if ($this->conexion->query($sql)) {
            $id_asunto = $this->conexion->insert_id;
            return json_encode(['success' => true, 'id_asunto' => $id_asunto, 'nombre' => $nombre]);
        } else {
            return json_encode(['success' => false, 'message' => 'Error al crear el asunto: ' . $this->conexion->error]);
        }
    }
    public function cargarProductoPrecios($id_producto = null)
    {
        if (!$id_producto) {
            $id_producto = $_GET['id'] ?? null;
        }
        
        if (!$id_producto) {
            return json_encode([]);
        }
        
        $sql = "SELECT id, id_producto, nombre, precio 
                FROM producto_precios 
                WHERE id_producto = ?";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $precios = [];
        while ($row = $result->fetch_assoc()) {
            $precios[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'precio' => number_format((float)$row['precio'], 2, '.', '')
            ];
        }
        
        return json_encode($precios);
    }
    public function cargarRepuestoPrecios($id_repuesto = null) 
    {
        if (!$id_repuesto) {
            $id_repuesto = $_GET['id'] ?? null;
        }
        
        if (!$id_repuesto) {
            return json_encode([]);
        }
        
        $sql = "SELECT id, id_repuesto, nombre, precio 
                FROM repuesto_precios 
                WHERE id_repuesto = ?";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_repuesto);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $precios = [];
        while ($row = $result->fetch_assoc()) {
            $precios[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'precio' => number_format((float)$row['precio'], 2, '.', '')
            ];
        }
        
        return json_encode($precios);
    }
public function ultimoNumero()
{
    $sql = "SELECT MAX(numero) as ultimo_numero FROM cotizaciones WHERE id_empresa = '{$_SESSION['id_empresa']}'";
    $result = $this->conexion->query($sql);
    $row = $result->fetch_assoc();
    
    return json_encode([
        'ultimo_numero' => (int)($row['ultimo_numero'] ?? 0)  // ← AGREGAR (int) aquí
    ]);
}
    
}