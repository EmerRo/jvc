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
        $sql = "SELECT ns.*, COUNT(ds.id) as cantidad_equipos 
                FROM numero_series ns 
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id 
                GROUP BY ns.id";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    // Método para manejar solicitudes POST (desde la interfaz web)
    public function getOneSerie()
    {
        if (!isset($_POST["id"])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        $id = $_POST["id"];
        return $this->getSerieById($id);
    }

    // Método para manejar solicitudes GET (desde Postman o API)
    public function getOneSerieById($id)
    {
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'error' => 'ID no válido']);
            return;
        }

        return $this->getSerieById($id);
    }

    // Método común para obtener la serie por ID
    private function getSerieById($id)
    {
        $sql = "SELECT ns.*, ds.*, ds.estado,
        m.nombre as marca_nombre, 
        mo.nombre as modelo_nombre, 
        e.nombre as equipo_nombre 
        FROM numero_series ns
        LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
        LEFT JOIN marcas m ON ds.marca = m.id
        LEFT JOIN modelos mo ON ds.modelo = mo.id
        LEFT JOIN equipos e ON ds.equipo = e.id
        WHERE ns.id = ?";

        try {
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $serie = null;
            $equipos = [];

            while ($row = $resultado->fetch_assoc()) {
                if (!$serie) {
                    $serie = [
                        'id' => $row['id'],
                        'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                        'fecha_creacion' => $row['fecha_creacion'],
                        'cantidad_equipos' => $row['cantidad_equipos']
                    ];
                }
                if ($row['numero_serie']) {
                    $equipos[] = [
                        'id' => $row['id'], // ID del detalle
                        'modelo' => $row['modelo'],
                        'modelo_nombre' => $row['modelo_nombre'],
                        'marca' => $row['marca'],
                        'marca_nombre' => $row['marca_nombre'],
                        'equipo' => $row['equipo'],
                        'equipo_nombre' => $row['equipo_nombre'],
                        'numero_serie' => $row['numero_serie'],
                        'estado' => $row['estado'] ?? 'disponible'
                    ];
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
    public function getSerieByNumero()
    {
        $respuesta = [];
        $numeroSerie = isset($_POST["numero_serie"]) ? $_POST["numero_serie"] : null;

        if ($numeroSerie) {
            $sql = "SELECT ns.*, ds.* FROM numero_series ns
                    LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                    WHERE ds.numero_serie = ?";
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
            error_log("Faltan datos requeridos:");
            error_log("cliente_ruc_dni: " . (isset($_POST['cliente_ruc_dni']) ? "presente" : "falta"));
            error_log("fecha_creacion: " . (isset($_POST['fecha_creacion']) ? "presente" : "falta"));
            error_log("equipos: " . (isset($_POST['equipos']) ? "presente" : "falta"));
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

            // Verificar si algún número de serie ya existe
            $numeros_serie = array_column($equipos, 'numero_serie');
            $placeholders = implode(',', array_fill(0, count($numeros_serie), '?'));

            $stmt_check = $this->conectar->prepare("
                SELECT numero_serie FROM detalle_serie 
                WHERE numero_serie IN ($placeholders)
            ");

            if (!$stmt_check) {
                throw new Exception("Error preparando consulta de verificación: " . $this->conectar->error);
            }

            $types = str_repeat('s', count($numeros_serie));
            $stmt_check->bind_param($types, ...$numeros_serie);
            $stmt_check->execute();
            $result = $stmt_check->get_result();

            $duplicados = [];
            while ($row = $result->fetch_assoc()) {
                $duplicados[] = $row['numero_serie'];
            }

            if (!empty($duplicados)) {
                throw new Exception("Los siguientes números de serie ya existen: " . implode(', ', $duplicados));
            }

            // Contar la cantidad real de equipos
            $cantidad_equipos = count($equipos);

            error_log("Insertando en numero_series - Cliente: {$_POST['cliente_ruc_dni']}, Fecha: {$_POST['fecha_creacion']}, Cantidad: {$cantidad_equipos}");

            $stmt = $this->conectar->prepare("INSERT INTO numero_series (cliente_ruc_dni, cliente_documento, fecha_creacion, cantidad_equipos) VALUES (?, ?, ?, ?)");

            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conectar->error);
            }

           $stmt->bind_param("sssi", $_POST['cliente_ruc_dni'], $_POST['cliente_documento'], $_POST['fecha_creacion'], $cantidad_equipos);

            // Ejecutar la consulta
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar en numero_series: " . $stmt->error);
            }

            // Obtener el ID insertado
            $serie_id = $stmt->insert_id;
            error_log("ID de serie insertada: " . $serie_id);

            // Verificar que se obtuvo un ID válido
            if (!$serie_id) {
                throw new Exception("No se pudo obtener el ID de la serie insertada");
            }

            // Preparar la consulta para insertar en detalle_serie
            $stmt_detalle = $this->conectar->prepare("
                INSERT INTO detalle_serie (numero_serie_id, modelo, marca, equipo, numero_serie) 
                VALUES (?, ?, ?, ?, ?)
            ");

            if (!$stmt_detalle) {
                throw new Exception("Error preparando consulta de detalle: " . $this->conectar->error);
            }

            // Insertar cada equipo
            foreach ($equipos as $equipo) {
                error_log("Procesando equipo: " . print_r($equipo, true));

                if (
                    !isset($equipo['modelo']) || !isset($equipo['marca']) ||
                    !isset($equipo['equipo']) || !isset($equipo['numero_serie'])
                ) {
                    throw new Exception("Datos de equipo incompletos");
                }

                $stmt_detalle->bind_param(
                    "issss",
                    $serie_id,
                    $equipo['modelo'],
                    $equipo['marca'],
                    $equipo['equipo'],
                    $equipo['numero_serie']
                );

                if (!$stmt_detalle->execute()) {
                    // Si hay un error de duplicado, capturarlo específicamente
                    if ($this->conectar->errno == 1062) { // Error de duplicado en MySQL
                        throw new Exception("El número de serie '{$equipo['numero_serie']}' ya existe en la base de datos");
                    }
                    throw new Exception("Error al insertar en detalle_serie: " . $stmt_detalle->error);
                }

                error_log("Equipo insertado correctamente");
            }

            // Confirmar la transacción
            $this->conectar->commit();
            error_log("Transacción completada exitosamente");

            // Devolver éxito y el ID de la serie
            return json_encode(['success' => true, 'id' => $serie_id]);

        } catch (Exception $e) {
            error_log("Error en saveSerie: " . $e->getMessage());
            // Revertir la transacción en caso de error
            $this->conectar->rollback();
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Método updateSerie con las mismas mejoras
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

                // Obtener los números de serie actuales para este registro
                $stmt_current = $this->conectar->prepare("
                    SELECT numero_serie FROM detalle_serie WHERE numero_serie_id = ?
                ");
                $stmt_current->bind_param("i", $_POST['id']);
                $stmt_current->execute();
                $result_current = $stmt_current->get_result();

                $series_actuales = [];
                while ($row = $result_current->fetch_assoc()) {
                    $series_actuales[] = $row['numero_serie'];
                }

                // Verificar si algún número de serie nuevo ya existe en otros registros
                $numeros_serie = array_column($equipos, 'numero_serie');
                $nuevos_numeros = array_diff($numeros_serie, $series_actuales);

                if (!empty($nuevos_numeros)) {
                    $placeholders = implode(',', array_fill(0, count($nuevos_numeros), '?'));

                    $stmt_check = $this->conectar->prepare("
                        SELECT numero_serie FROM detalle_serie 
                        WHERE numero_serie IN ($placeholders) 
                        AND numero_serie_id != ?
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
                        $duplicados[] = $row['numero_serie'];
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

                // Eliminar detalles existentes
                $stmt_delete = $this->conectar->prepare("DELETE FROM detalle_serie WHERE numero_serie_id = ?");
                $stmt_delete->bind_param("i", $_POST['id']);

                if (!$stmt_delete->execute()) {
                    throw new Exception("Error al eliminar registros de detalle_serie: " . $stmt_delete->error);
                }

                // Insertar nuevos detalles
                $stmt_detalle = $this->conectar->prepare("
                    INSERT INTO detalle_serie (numero_serie_id, modelo, marca, equipo, numero_serie) 
                    VALUES (?, ?, ?, ?, ?)
                ");

                foreach ($equipos as $equipo) {
                    if (isset($equipo['modelo'], $equipo['marca'], $equipo['equipo'], $equipo['numero_serie'])) {
                        $stmt_detalle->bind_param("issss", $_POST['id'], $equipo['modelo'], $equipo['marca'], $equipo['equipo'], $equipo['numero_serie']);

                        if (!$stmt_detalle->execute()) {
                            // Si hay un error de duplicado, capturarlo específicamente
                            if ($this->conectar->errno == 1062) { // Error de duplicado en MySQL
                                throw new Exception("El número de serie '{$equipo['numero_serie']}' ya existe en la base de datos");
                            }
                            throw new Exception("Error al insertar en detalle_serie: " . $stmt_detalle->error);
                        }
                    }
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

        // Verificar si el número de serie ya existe
        $stmt = $this->conectar->prepare("SELECT COUNT(*) as total FROM detalle_serie WHERE numero_serie = ?");
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
        $sql = "SELECT ds.numero_serie 
            FROM detalle_serie ds 
            JOIN numero_series ns ON ds.numero_serie_id = ns.id 
            ORDER BY ns.id DESC, ds.id DESC 
            LIMIT 1";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            echo json_encode(['success' => true, 'numero_serie' => $row['numero_serie']]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'No hay números de serie registrados']);
        }
    }
}