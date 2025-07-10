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
        $sql = "SELECT ns.*, 
                CASE 
                    WHEN ds.numero_serie IS NOT NULL 
                    THEN JSON_LENGTH(ds.numero_serie)
                    ELSE 0 
                END as cantidad_equipos 
                FROM numero_series ns 
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
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
        $sql = "SELECT ns.*, ds.*, ds.estado,
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
                    'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                    'fecha_creacion' => $row['fecha_creacion'],
                    'cantidad_equipos' => $row['cantidad_equipos']
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
                            'id' => $row['id'], // ID del detalle
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
        if (empty($id)) return '';
        
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

        if (!isset($_POST['cliente_ruc_dni']) || !isset($_POST['cliente_documento']) || !isset($_POST['fecha_creacion']) || !isset($_POST['equipos'])) {
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
                if (!isset($equipo['modelo']) || !isset($equipo['marca']) || 
                    !isset($equipo['equipo']) || !isset($equipo['numero_serie'])) {
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

            error_log("Insertando en numero_series - Cliente: {$_POST['cliente_ruc_dni']}, Fecha: {$_POST['fecha_creacion']}, Cantidad: {$cantidad_equipos}");

            // Insertar en numero_series
            $stmt = $this->conectar->prepare("INSERT INTO numero_series (cliente_ruc_dni, cliente_documento, fecha_creacion, cantidad_equipos) VALUES (?, ?, ?, ?)");

            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("sssi", $_POST['cliente_ruc_dni'], $_POST['cliente_documento'], $_POST['fecha_creacion'], $cantidad_equipos);

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
        if (isset($_POST['id'], $_POST['cliente_ruc_dni'], $_POST['cliente_documento'], $_POST['fecha_creacion'], $_POST['equipos'])) {
            $this->conectar->begin_transaction();

            try {
                // Decodificar equipos si viene como JSON string
                $equipos = is_string($_POST['equipos']) ? json_decode($_POST['equipos'], true) : $_POST['equipos'];

                if (!is_array($equipos)) {
                    throw new Exception("El formato de equipos es inválido");
                }

                // Extraer arrays para cada campo
                $modelos = [];
                $marcas = [];
                $equipos_tipos = [];
                $numeros_serie = [];

                foreach ($equipos as $equipo) {
                    if (!isset($equipo['modelo']) || !isset($equipo['marca']) || 
                        !isset($equipo['equipo']) || !isset($equipo['numero_serie'])) {
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

                // Actualizar la tabla numero_series
                $stmt = $this->conectar->prepare("UPDATE numero_series SET cliente_ruc_dni = ?, cliente_documento = ?, fecha_creacion = ?, cantidad_equipos = ? WHERE id = ?");
                $stmt->bind_param("sssii", $_POST['cliente_ruc_dni'], $_POST['cliente_documento'], $_POST['fecha_creacion'], $cantidad_equipos, $_POST['id']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error al actualizar numero_series: " . $stmt->error);
                }

                // Actualizar detalle_serie con arrays JSON
                $stmt_detalle = $this->conectar->prepare("
                    UPDATE detalle_serie 
                    SET modelo = ?, marca = ?, equipo = ?, numero_serie = ?
                    WHERE numero_serie_id = ?
                ");

                // Convertir arrays a JSON
                $modelos_json = json_encode($modelos);
                $marcas_json = json_encode($marcas);
                $equipos_json = json_encode($equipos_tipos);
                $numeros_serie_json = json_encode($numeros_serie);

                $stmt_detalle->bind_param("ssssi", $modelos_json, $marcas_json, $equipos_json, $numeros_serie_json, $_POST['id']);

                if (!$stmt_detalle->execute()) {
                    throw new Exception("Error al actualizar detalle_serie: " . $stmt_detalle->error);
                }

                $this->conectar->commit();
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
                $stmt_delete_detalle = $this->conectar->prepare("DELETE FROM detalle_serie WHERE numero_serie_id = ?");
                $stmt_delete_detalle->bind_param("i", $_POST['id']);
                $stmt_delete_detalle->execute();

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
}