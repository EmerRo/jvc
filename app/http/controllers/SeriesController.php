<?php

class SeriesController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getSeries()
    {
        $respuesta = [];
        // <CHANGE> Incluir campo numero en la consulta, asegurar que se use el ID de numero_series
$sql = "SELECT ns.id, ns.numero, ns.cliente_ruc_dni, ns.cliente_documento, ns.fecha_creacion, ns.tiene_cliente,
        CASE
            WHEN ds.numero_serie IS NOT NULL
            THEN JSON_LENGTH(ds.numero_serie)
            ELSE ns.cantidad_equipos
        END as cantidad_equipos
        FROM numero_series ns
        LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
        ORDER BY ns.numero DESC";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                // Si no tiene cliente, es un registro interno de la empresa
                if (empty($row['cliente_ruc_dni']) && empty($row['cliente_documento'])) {
                    $row['cliente_ruc_dni'] = null; // Mantener null para que el frontend maneje la visualización
                }
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    public function getOneSerie()
    {
        if (!isset($_POST["id"])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        $id = $_POST["id"];
        return $this->getSerieById($id);
    }

    public function getOneSerieById($id)
    {
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'error' => 'ID no válido']);
            return;
        }

        return $this->getSerieById($id);
    }

    private function getSerieById($id)
    {
        $sql = "SELECT ns.id, ns.numero, ns.cliente_ruc_dni, ns.cliente_documento, ns.fecha_creacion, ns.cantidad_equipos, ns.tiene_cliente,
                ds.id as detalle_id, ds.estado,
                ds.modelo as modelo_json,
                ds.marca as marca_json,
                ds.equipo as equipo_json,
                ds.numero_serie as numero_serie_json
                FROM numero_series ns
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE ns.id = ?";

        try {
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $serie = null;
            $equipos = [];

            if ($row = $resultado->fetch_assoc()) {
                $serie = [
                    'id' => $row['id'],
                    'cliente_ruc_dni' => $row['cliente_ruc_dni'] ?: '',
                    'cliente_documento' => $row['cliente_documento'] ?: '',
                    'fecha_creacion' => $row['fecha_creacion'],
                    'cantidad_equipos' => $row['cantidad_equipos'],
                    'tiene_cliente' => !empty($row['cliente_ruc_dni']) || !empty($row['cliente_documento'])
                ];

                if ($row['numero_serie_json']) {
                    // Decodificar los arrays JSON
                    $modelos = json_decode($row['modelo_json'], true) ?: [];
                    $marcas = json_decode($row['marca_json'], true) ?: [];
                    $equipos_tipos = json_decode($row['equipo_json'], true) ?: [];
                    $numeros_serie = json_decode($row['numero_serie_json'], true) ?: [];

                    // Crear array de equipos combinando los datos
                    for ($i = 0; $i < count($numeros_serie); $i++) {
                        $equipos[] = [
                            'id' => $row['detalle_id'] ?? $row['id'], // ID del detalle o ID principal si no hay detalle
                            'modelo' => $modelos[$i] ?? '',
                            'modelo_nombre' => $this->getNombreById('modelos', $modelos[$i] ?? ''),
                            'marca' => $marcas[$i] ?? '',
                            'marca_nombre' => $this->getNombreById('marcas', $marcas[$i] ?? ''),
                            'equipo' => $equipos_tipos[$i] ?? '',
                            'equipo_nombre' => $this->getNombreById('equipos', $equipos_tipos[$i] ?? ''),
                            'numero_serie' => $numeros_serie[$i] ?? '',
                            'estado' => $row['estado'] ?? 'disponible'
                        ];
                    }
                }
            }

            if ($serie) {
                $serie['equipos'] = $equipos;
                echo json_encode(['success' => true, 'data' => [$serie]]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Serie no encontrada']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function getNombreById($tabla, $id)
    {
        if (empty($id))
            return '';

        $stmt = $this->conectar->prepare("SELECT nombre FROM {$tabla} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {
            return $row['nombre'];
        }

        return '';
    }

    public function getSerieByNumero()
    {
        $respuesta = [];
        $numeroSerie = isset($_POST["numero_serie"]) ? $_POST["numero_serie"] : null;

        if ($numeroSerie) {
            $sql = "SELECT ns.*, ds.* FROM numero_series ns
                    LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                    WHERE JSON_CONTAINS(ds.numero_serie, JSON_QUOTE(?))";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("s", $numeroSerie);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $respuesta = $resultado->fetch_assoc();
                return json_encode(['res' => true, 'data' => $respuesta]);
            } else {
                return json_encode(['res' => false, 'msg' => 'Serie no encontrada']);
            }
        } else {
            return json_encode(['res' => false, 'msg' => 'Número de serie no proporcionado']);
        }
    }

    public function saveSerie()
    {
        error_log("Datos recibidos: " . print_r($_POST, true));

        // Modificar validación para permitir registros sin cliente
        if (!isset($_POST['fecha_creacion']) || !isset($_POST['equipos'])) {
            error_log("Faltan datos requeridos");
            return json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
        }

        $this->conectar->begin_transaction();

        try {
            // Decodificar equipos si viene como JSON string
            $equipos = is_string($_POST['equipos']) ? json_decode($_POST['equipos'], true) : $_POST['equipos'];

            if (!is_array($equipos)) {
                throw new Exception("El formato de equipos es inválido");
            }

            error_log("Equipos a procesar: " . print_r($equipos, true));

            // Extraer arrays para cada campo
            $modelos = [];
            $marcas = [];
            $equipos_tipos = [];
            $numeros_serie = [];

            foreach ($equipos as $equipo) {
                if (
                    !isset($equipo['modelo']) || !isset($equipo['marca']) ||
                    !isset($equipo['equipo']) || !isset($equipo['numero_serie'])
                ) {
                    throw new Exception("Datos de equipo incompletos");
                }

                $modelos[] = $equipo['modelo'];
                $marcas[] = $equipo['marca'];
                $equipos_tipos[] = $equipo['equipo'];
                $numeros_serie[] = $equipo['numero_serie'];
            }

            // Verificar si algún número de serie ya existe
            $placeholders = implode(',', array_fill(0, count($numeros_serie), 'JSON_QUOTE(?)'));
            $stmt_check = $this->conectar->prepare("
                SELECT JSON_UNQUOTE(JSON_EXTRACT(numero_serie, CONCAT('$[', idx.i, ']'))) as numero_serie_individual
                FROM detalle_serie ds
                CROSS JOIN (
                    SELECT 0 as i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
                    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                ) idx
                WHERE JSON_EXTRACT(ds.numero_serie, CONCAT('$[', idx.i, ']')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(numero_serie, CONCAT('$[', idx.i, ']'))) IN ({$placeholders})
            ");

            if (!$stmt_check) {
                throw new Exception("Error preparando consulta de verificación: " . $this->conectar->error);
            }

            $stmt_check->bind_param(str_repeat('s', count($numeros_serie)), ...$numeros_serie);
            $stmt_check->execute();
            $result = $stmt_check->get_result();

            $duplicados = [];
            while ($row = $result->fetch_assoc()) {
                $duplicados[] = $row['numero_serie_individual'];
            }

            if (!empty($duplicados)) {
                throw new Exception("Los siguientes números de serie ya existen: " . implode(', ', $duplicados));
            }

            // Contar la cantidad real de equipos
            $cantidad_equipos = count($equipos);

            // Determinar si es registro con cliente externo o interno (empresa)
            $cliente_ruc_dni = !empty($_POST['cliente_ruc_dni']) ? $_POST['cliente_ruc_dni'] : null;
            $cliente_documento = !empty($_POST['cliente_documento']) ? $_POST['cliente_documento'] : null;

            // Si el RUC es de la empresa, es un registro interno
            $es_registro_interno = ($cliente_documento === '20538381978' && $cliente_ruc_dni === 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');

            if ($es_registro_interno) {
                // Registro interno de la empresa - guardar como NULL
                $cliente_ruc_dni = null;
                $cliente_documento = null;
                $tiene_cliente = 0;
            } else {
                // Registro con cliente externo
                $tiene_cliente = !empty($cliente_ruc_dni) || !empty($cliente_documento) ? 1 : 0;
            }

            error_log("Insertando en numero_series - Cliente: {$cliente_ruc_dni}, Fecha: {$_POST['fecha_creacion']}, Cantidad: {$cantidad_equipos}");

            // Insertar en numero_series (modificado para incluir tiene_cliente)
           
            $sql_numero = "SELECT MAX(numero) as ultimo_numero FROM numero_series";
            $resultado_numero = $this->conectar->query($sql_numero);
            $numero = 1;

            if ($row_numero = $resultado_numero->fetch_assoc()) {
                $numero = ($row_numero['ultimo_numero'] ?? 0) + 1;
            }

            // Insertar en numero_series (modificado para incluir numero y tiene_cliente)
            $stmt = $this->conectar->prepare("INSERT INTO numero_series (numero, cliente_ruc_dni, cliente_documento, fecha_creacion, cantidad_equipos, tiene_cliente) VALUES (?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("isssii", $numero, $cliente_ruc_dni, $cliente_documento, $_POST['fecha_creacion'], $cantidad_equipos, $tiene_cliente);

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar en numero_series: " . $stmt->error);
            }

            // Obtener el ID insertado
            $serie_id = $stmt->insert_id;
            error_log("ID de serie insertada: " . $serie_id);

            if (!$serie_id) {
                throw new Exception("No se pudo obtener el ID de la serie insertada");
            }

            // Insertar en detalle_serie con arrays JSON
            $stmt_detalle = $this->conectar->prepare("
                INSERT INTO detalle_serie (numero_serie_id, modelo, marca, equipo, numero_serie) 
                VALUES (?, ?, ?, ?, ?)
            ");

            if (!$stmt_detalle) {
                throw new Exception("Error preparando consulta de detalle: " . $this->conectar->error);
            }

            // Convertir arrays a JSON
            $modelos_json = json_encode($modelos);
            $marcas_json = json_encode($marcas);
            $equipos_json = json_encode($equipos_tipos);
            $numeros_serie_json = json_encode($numeros_serie);

            $stmt_detalle->bind_param("issss", $serie_id, $modelos_json, $marcas_json, $equipos_json, $numeros_serie_json);

            if (!$stmt_detalle->execute()) {
                throw new Exception("Error al insertar en detalle_serie: " . $stmt_detalle->error);
            }

            error_log("Detalle insertado correctamente");

            // Confirmar la transacción
            $this->conectar->commit();
            error_log("Transacción completada exitosamente");

            return json_encode(['success' => true, 'id' => $serie_id]);

        } catch (Exception $e) {
            error_log("Error en saveSerie: " . $e->getMessage());
            $this->conectar->rollback();
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateSerie()
    {
        error_log("=== INICIO updateSerie ===");
        error_log("POST data: " . print_r($_POST, true));

        if (isset($_POST['id'], $_POST['fecha_creacion'], $_POST['equipos'])) {
            $this->conectar->begin_transaction();

            try {
                // Decodificar equipos si viene como JSON string
                $equipos = is_string($_POST['equipos']) ? json_decode($_POST['equipos'], true) : $_POST['equipos'];
                error_log("Equipos decodificados: " . print_r($equipos, true));

                if (!is_array($equipos)) {
                    throw new Exception("El formato de equipos es inválido");
                }

                // Extraer arrays para cada campo
                $modelos = [];
                $marcas = [];
                $equipos_tipos = [];
                $numeros_serie = [];

                foreach ($equipos as $equipo) {
                    if (
                        !isset($equipo['modelo']) || !isset($equipo['marca']) ||
                        !isset($equipo['equipo']) || !isset($equipo['numero_serie'])
                    ) {
                        throw new Exception("Datos de equipo incompletos");
                    }

                    $modelos[] = $equipo['modelo'];
                    $marcas[] = $equipo['marca'];
                    $equipos_tipos[] = $equipo['equipo'];
                    $numeros_serie[] = $equipo['numero_serie'];
                }

                // Obtener los números de serie actuales para este registro
                $stmt_current = $this->conectar->prepare("SELECT numero_serie FROM detalle_serie WHERE numero_serie_id = ?");
                $stmt_current->bind_param("i", $_POST['id']);
                $stmt_current->execute();
                $result_current = $stmt_current->get_result();

                $series_actuales = [];
                if ($row = $result_current->fetch_assoc()) {
                    $series_actuales = json_decode($row['numero_serie'], true) ?: [];
                }

                // Verificar si algún número de serie nuevo ya existe en otros registros
                $nuevos_numeros = array_diff($numeros_serie, $series_actuales);

                if (!empty($nuevos_numeros)) {
                    $placeholders = implode(',', array_fill(0, count($nuevos_numeros), 'JSON_QUOTE(?)'));
                    $stmt_check = $this->conectar->prepare("
                        SELECT JSON_UNQUOTE(JSON_EXTRACT(numero_serie, CONCAT('$[', idx.i, ']'))) as numero_serie_individual
                        FROM detalle_serie ds
                        CROSS JOIN (
                            SELECT 0 as i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
                            SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                        ) idx
                        WHERE JSON_EXTRACT(ds.numero_serie, CONCAT('$[', idx.i, ']')) IS NOT NULL
                        AND JSON_UNQUOTE(JSON_EXTRACT(numero_serie, CONCAT('$[', idx.i, ']'))) IN ({$placeholders})
                        AND ds.numero_serie_id != ?
                    ");

                    if (!$stmt_check) {
                        throw new Exception("Error preparando consulta de verificación: " . $this->conectar->error);
                    }

                    $params = array_merge($nuevos_numeros, [$_POST['id']]);
                    $types = str_repeat('s', count($nuevos_numeros)) . 'i';
                    $stmt_check->bind_param($types, ...$params);
                    $stmt_check->execute();
                    $result = $stmt_check->get_result();

                    $duplicados = [];
                    while ($row = $result->fetch_assoc()) {
                        $duplicados[] = $row['numero_serie_individual'];
                    }

                    if (!empty($duplicados)) {
                        throw new Exception("Los siguientes números de serie ya existen: " . implode(', ', $duplicados));
                    }
                }

                // Contar la cantidad real de equipos
                $cantidad_equipos = count($equipos);
                error_log("Cantidad de equipos calculada: " . $cantidad_equipos);

                // Determinar si es registro con cliente externo o interno (empresa)
                $cliente_ruc_dni = !empty($_POST['cliente_ruc_dni']) ? $_POST['cliente_ruc_dni'] : null;
                $cliente_documento = !empty($_POST['cliente_documento']) ? $_POST['cliente_documento'] : null;

                // Si el RUC es de la empresa, es un registro interno
                $es_registro_interno = ($cliente_documento === '20538381978' && $cliente_ruc_dni === 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');

                if ($es_registro_interno) {
                    // Registro interno de la empresa - guardar como NULL
                    $cliente_ruc_dni = null;
                    $cliente_documento = null;
                    $tiene_cliente = 0;
                } else {
                    // Registro con cliente externo
                    $tiene_cliente = !empty($cliente_ruc_dni) || !empty($cliente_documento) ? 1 : 0;
                }

                // Actualizar la tabla numero_series
                $stmt = $this->conectar->prepare("UPDATE numero_series SET cliente_ruc_dni = ?, cliente_documento = ?, fecha_creacion = ?, cantidad_equipos = ?, tiene_cliente = ? WHERE id = ?");
                $stmt->bind_param("sssiii", $cliente_ruc_dni, $cliente_documento, $_POST['fecha_creacion'], $cantidad_equipos, $tiene_cliente, $_POST['id']);

                // Verificar datos actuales antes de actualizar
                $stmt_check = $this->conectar->prepare("SELECT * FROM numero_series WHERE id = ?");
                $stmt_check->bind_param("i", $_POST['id']);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                $current_data = $result_check->fetch_assoc();
                error_log("Datos actuales en numero_series para ID " . $_POST['id'] . ": " . print_r($current_data, true));

                // Verificar si el registro existe
                if (!$current_data) {
                    error_log("¡ERROR! No se encontró registro con ID: " . $_POST['id']);

                    // Verificar qué registros existen realmente
                    $check_all = $this->conectar->query("SELECT id, numero FROM numero_series ORDER BY id");
                    $existing_ids = [];
                    while ($row = $check_all->fetch_assoc()) {
                        $existing_ids[] = "ID: " . $row['id'] . " (Número: " . $row['numero'] . ")";
                    }
                    error_log("Registros existentes en numero_series: " . implode(", ", $existing_ids));

                    throw new Exception("No se encontró el registro con ID: " . $_POST['id'] . ". Registros existentes: " . implode(", ", $existing_ids));
                }

                error_log("Actualizando numero_series con cantidad_equipos: " . $cantidad_equipos . " para ID: " . $_POST['id']);

                if (!$stmt->execute()) {
                    throw new Exception("Error al actualizar numero_series: " . $stmt->error);
                }

                error_log("Filas afectadas en numero_series: " . $stmt->affected_rows);

                // Convertir arrays a JSON primero
                $modelos_json = json_encode($modelos);
                $marcas_json = json_encode($marcas);
                $equipos_json = json_encode($equipos_tipos);
                $numeros_serie_json = json_encode($numeros_serie);

                // Verificar datos actuales en detalle_serie
                $stmt_check_detalle = $this->conectar->prepare("SELECT * FROM detalle_serie WHERE numero_serie_id = ?");
                $stmt_check_detalle->bind_param("i", $_POST['id']);
                $stmt_check_detalle->execute();
                $result_detalle = $stmt_check_detalle->get_result();
                $current_detalle = $result_detalle->fetch_assoc();
                error_log("Datos actuales en detalle_serie para numero_serie_id " . $_POST['id'] . ": " . print_r($current_detalle, true));

                if (!$current_detalle) {
                    error_log("¡ERROR! No se encontró detalle_serie para numero_serie_id: " . $_POST['id']);

                    // En lugar de lanzar excepción, crear un nuevo registro en detalle_serie
                    error_log("Insertando nuevo registro en detalle_serie...");
                    $stmt_insert = $this->conectar->prepare("INSERT INTO detalle_serie (numero_serie_id, modelo, marca, equipo, numero_serie, estado) VALUES (?, ?, ?, ?, ?, 'disponible')");
                    $stmt_insert->bind_param("issss", $_POST['id'], $modelos_json, $marcas_json, $equipos_json, $numeros_serie_json);

                    if (!$stmt_insert->execute()) {
                        throw new Exception("Error al insertar en detalle_serie: " . $stmt_insert->error);
                    }

                    error_log("Registro insertado en detalle_serie exitosamente");
                    $this->conectar->commit();
                    error_log("=== FIN updateSerie - ÉXITO (INSERT) ===");
                    return json_encode(['success' => true, 'affected_rows' => $stmt_insert->affected_rows]);
                }

                // Actualizar detalle_serie con arrays JSON
                $stmt_detalle = $this->conectar->prepare("
                    UPDATE detalle_serie
                    SET modelo = ?, marca = ?, equipo = ?, numero_serie = ?
                    WHERE numero_serie_id = ?
                ");

                error_log("Nuevos datos JSON para detalle_serie:");
                error_log("Modelos: " . $modelos_json);
                error_log("Marcas: " . $marcas_json);
                error_log("Equipos: " . $equipos_json);
                error_log("Números de serie: " . $numeros_serie_json);

                $stmt_detalle->bind_param("ssssi", $modelos_json, $marcas_json, $equipos_json, $numeros_serie_json, $_POST['id']);

                if (!$stmt_detalle->execute()) {
                    throw new Exception("Error al actualizar detalle_serie: " . $stmt_detalle->error);
                }

                error_log("Filas afectadas en detalle_serie: " . $stmt_detalle->affected_rows);

                $this->conectar->commit();
                error_log("=== FIN updateSerie - ÉXITO ===");
                return json_encode(['success' => true, 'affected_rows' => $stmt->affected_rows]);
            } catch (Exception $e) {
                $this->conectar->rollback();
                return json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
        }
    }

    public function deleteSerie()
    {
        if (isset($_POST['id'])) {
            $this->conectar->begin_transaction();

            try {
                // Primero eliminar registros de garantia que referencian detalle_serie
                // Usar JOIN para evitar problemas con subconsultas en DELETE
                $stmt_delete_garantia = $this->conectar->prepare("
                    DELETE g FROM garantia g 
                    INNER JOIN detalle_serie ds ON g.detalle_serie_id = ds.id 
                    WHERE ds.numero_serie_id = ?
                ");
                $stmt_delete_garantia->bind_param("i", $_POST['id']);
                $stmt_delete_garantia->execute();

                // Luego eliminar registros de detalle_serie
                $stmt_delete_detalle = $this->conectar->prepare("DELETE FROM detalle_serie WHERE numero_serie_id = ?");
                $stmt_delete_detalle->bind_param("i", $_POST['id']);
                $stmt_delete_detalle->execute();

                // Finalmente eliminar el registro principal de numero_series
                $stmt_delete_serie = $this->conectar->prepare("DELETE FROM numero_series WHERE id = ?");
                $stmt_delete_serie->bind_param("i", $_POST['id']);
                $stmt_delete_serie->execute();

                $this->conectar->commit();
                return json_encode(['success' => true, 'affected_rows' => $stmt_delete_serie->affected_rows]);
            } catch (Exception $e) {
                $this->conectar->rollback();
                return json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['success' => false, 'error' => 'Falta el ID para eliminar']);
        }
    }

    public function verificarNumeroSerie()
    {
        if (!isset($_POST['numero_serie'])) {
            return json_encode(['success' => false, 'error' => 'Número de serie no proporcionado']);
        }

        $numero_serie = $_POST['numero_serie'];

        // Verificar si el número de serie ya existe en algún array JSON
        $stmt = $this->conectar->prepare("
            SELECT COUNT(*) as total 
            FROM detalle_serie 
            WHERE JSON_CONTAINS(numero_serie, JSON_QUOTE(?))
        ");
        $stmt->bind_param("s", $numero_serie);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        return json_encode([
            'success' => true,
            'existe' => $row['total'] > 0
        ]);
    }

    public function getUltimoNumeroSerie()
    {
        // Consulta para obtener el último número de serie registrado
        $sql = "SELECT 
                    JSON_UNQUOTE(JSON_EXTRACT(ds.numero_serie, '$[0]')) as primer_numero_serie,
                    JSON_UNQUOTE(JSON_EXTRACT(ds.numero_serie, CONCAT('$[', JSON_LENGTH(ds.numero_serie) - 1, ']'))) as ultimo_numero_serie
                FROM detalle_serie ds 
                JOIN numero_series ns ON ds.numero_serie_id = ns.id 
                ORDER BY ns.id DESC, ds.id DESC 
                LIMIT 1";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            echo json_encode(['success' => true, 'numero_serie' => $row['ultimo_numero_serie']]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'No hay números de serie registrados']);
        }
    }


    // NUEVO: Método para generar un número de serie único de 5 dígitos
    public function generarNumeroSerie()
    {
        $intentos = 0;
        $maxIntentos = 100;

        do {
            // Generar número aleatorio de 5 dígitos (10000-99999)
            $numeroGenerado = rand(10000, 99999);

            // Verificar que no exista
            $stmt = $this->conectar->prepare("
            SELECT COUNT(*) as total 
            FROM detalle_serie 
            WHERE JSON_CONTAINS(numero_serie, JSON_QUOTE(?))
        ");
            $stmt->bind_param("s", $numeroGenerado);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();

            $existe = $row['total'] > 0;
            $intentos++;

        } while ($existe && $intentos < $maxIntentos);

        if ($existe) {
            echo json_encode(['success' => false, 'error' => 'No se pudo generar un número único después de ' . $maxIntentos . ' intentos']);
        } else {
            echo json_encode(['success' => true, 'numero_serie' => $numeroGenerado]);
        }
    }
    public function getProximoNumero(){
        // <CHANGE> Consulta simple sin filtro de empresa
        $sql = "SELECT MAX(numero) as ultimo_numero FROM numero_series";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            echo json_encode(['error' => 'Error en la consulta: ' . $this->conectar->error]);
            return;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $proximo_numero = 1;
        if ($row = $result->fetch_assoc()) {
            $proximo_numero = ($row['ultimo_numero'] ?? 0) + 1;
        }
        
        echo json_encode(['proximo_numero' => $proximo_numero]);
    }

    // NUEVO: Método para verificar si un registro tiene garantías relacionadas
    public function verificarGarantias()
    {
        error_log("verificarGarantias() llamado con ID: " . ($_POST['id'] ?? 'NO DEFINIDO'));
        
        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        try {
            error_log("Intentando preparar consulta para verificar garantías...");
            
            // Verificar que la conexión esté activa
            if (!$this->conectar || $this->conectar->connect_error) {
                throw new Exception("Error de conexión a la base de datos: " . ($this->conectar->connect_error ?? 'Conexión nula'));
            }
            
            // Consulta simplificada para debugging - verificar si las tablas existen
            $stmt = $this->conectar->prepare("
                SELECT COUNT(*) as total_garantias
                FROM garantia g 
                INNER JOIN detalle_serie ds ON g.detalle_serie_id = ds.id 
                WHERE ds.numero_serie_id = ?
            ");
            
            // Verificar si el prepare fue exitoso
            if (!$stmt) {
                error_log("Error en prepare(): " . $this->conectar->error);
                throw new Exception("Error preparando consulta: " . $this->conectar->error);
            }
            
            error_log("Consulta preparada exitosamente, ejecutando...");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            error_log("Consulta ejecutada, obteniendo resultado...");
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            error_log("Resultado obtenido: " . print_r($row, true));

            $tiene_garantias = $row['total_garantias'] > 0;
            
                         if ($tiene_garantias) {
                 // Si tiene garantías, hacer una consulta adicional para obtener detalles
                 // Primero verificar qué columnas existen en la tabla garantia
                 $stmt_detalle = $this->conectar->prepare("
                     SELECT g.*
                     FROM garantia g 
                     INNER JOIN detalle_serie ds ON g.detalle_serie_id = ds.id 
                     WHERE ds.numero_serie_id = ?
                     LIMIT 1
                 ");
                 
                 if (!$stmt_detalle) {
                     throw new Exception("Error preparando consulta de detalles: " . $this->conectar->error);
                 }
                 
                 $stmt_detalle->bind_param("i", $_POST['id']);
                 $stmt_detalle->execute();
                 $resultado_detalle = $stmt_detalle->get_result();
                 $row_detalle = $resultado_detalle->fetch_assoc();
                 
                 // Obtener todas las columnas disponibles para debugging
                 $columnas_disponibles = array_keys($row_detalle);
                 error_log("Columnas disponibles en tabla garantia: " . print_r($columnas_disponibles, true));
                 
                 // Ahora hacer la consulta completa para obtener todos los registros
                 $stmt_todas = $this->conectar->prepare("
                     SELECT g.*
                     FROM garantia g 
                     INNER JOIN detalle_serie ds ON g.detalle_serie_id = ds.id 
                     WHERE ds.numero_serie_id = ?
                 ");
                 
                 if (!$stmt_todas) {
                     throw new Exception("Error preparando consulta completa: " . $this->conectar->error);
                 }
                 
                 $stmt_todas->bind_param("i", $_POST['id']);
                 $stmt_todas->execute();
                 $resultado_todas = $stmt_todas->get_result();
                 
                 $ids_garantias = [];
                 $numeros_garantia = [];
                 
                 while ($row_garantia = $resultado_todas->fetch_assoc()) {
                     // Usar la primera columna como ID (asumiendo que existe)
                     $ids_garantias[] = $row_garantia[$columnas_disponibles[0]] ?? 'N/A';
                     
                     // Buscar una columna que contenga información del número de garantía
                     $numero_garantia = 'N/A';
                     foreach ($columnas_disponibles as $columna) {
                         if (stripos($columna, 'numero') !== false || stripos($columna, 'codigo') !== false) {
                             $numero_garantia = $row_garantia[$columna] ?? 'N/A';
                             break;
                         }
                     }
                     $numeros_garantia[] = $numero_garantia;
                 }
                 
                 $garantias_info = [
                     'total' => $row['total_garantias'],
                     'ids' => $ids_garantias,
                     'numeros' => $numeros_garantia,
                     'columnas_disponibles' => $columnas_disponibles // Para debugging
                 ];
                 
                 echo json_encode([
                     'success' => true,
                     'tiene_garantias' => true,
                     'garantias_info' => $garantias_info
                 ]);
             } else {
                 echo json_encode([
                     'success' => true,
                     'tiene_garantias' => false
                 ]);
             }
            
            error_log("verificarGarantias() completado exitosamente");
            
        } catch (Exception $e) {
            error_log("Error en verificarGarantias(): " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}