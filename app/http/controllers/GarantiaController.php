<?php

require_once "app/models/Garantia.php";

require_once "utils/lib/exel/vendor/autoload.php";

class GarantiaController extends Controller
{
    private $garantia;
    private $conectar;

    public function __construct()
    {
        $this->garantia = new Garantia();
        $this->conectar = (new Conexion())->getConexion();
    }



public function insertar()
{
    // Verificar si se recibieron los datos necesarios
    if (!isset($_POST['cliente_nombre'])) {
        echo json_encode(['res' => false, 'msg' => 'Falta el nombre del cliente']);
        return;
    }

    // Iniciar transacción
    $this->conectar->begin_transaction();

    try {
        // Datos comunes para ambos formatos
        $cliente = $_POST['cliente_nombre'];
        $guia_remision = isset($_POST['guia_remision']) ? $_POST['guia_remision'] : '';
        $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d');
        $fecha_caducidad = isset($_POST['fecha_caducidad']) ? $_POST['fecha_caducidad'] : date('Y-m-d', strtotime('+1 year'));
        
        // Verificar si viene de garantia-add.php (con numero_serie_id)
        if (isset($_POST['numero_serie_id'])) {
            $numero_serie_id = $_POST['numero_serie_id'];
            
            // Verificar si hay equipos específicos seleccionados
            if (isset($_POST['equipos']) && !empty($_POST['equipos'])) {
                // Formato nuevo: equipos es un array JSON
                $equipos = is_string($_POST['equipos']) ? json_decode($_POST['equipos'], true) : $_POST['equipos'];
                
                if (!is_array($equipos)) {
                    throw new Exception("Formato de equipos inválido");
                }
                
                // Recolectar todos los detalle_serie_id
                $detalle_serie_ids = [];
                $series_data = [];
                
                foreach ($equipos as $equipo) {
                    // Obtener el detalle_serie_id correspondiente al número de serie
                    $stmt = $this->conectar->prepare("
                        SELECT id, marca, modelo, equipo, numero_serie FROM detalle_serie 
                        WHERE numero_serie_id = ? AND numero_serie = ?
                    ");
                    $stmt->bind_param("is", $numero_serie_id, $equipo['numero_serie']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 0) {
                        throw new Exception("No se encontró el detalle de serie para: " . $equipo['numero_serie']);
                    }
                    
                    $row = $result->fetch_assoc();
                    $detalle_serie_id = $row['id'];
                    $detalle_serie_ids[] = $detalle_serie_id;
                    
                    // Guardar datos completos de la serie
                    $series_data[] = [
                        'id' => $detalle_serie_id,
                        'numero_serie' => $row['numero_serie'],
                        'marca' => $row['marca'],
                        'modelo' => $row['modelo'],
                        'equipo' => $row['equipo']
                    ];
                    
                    // Verificar si ya existe una garantía para este detalle_serie_id
                    $stmt = $this->conectar->prepare("
                        SELECT id_garantia FROM garantia 
                        WHERE detalle_serie_id = ? OR JSON_CONTAINS(series_ids, ?)
                    ");
                    $json_id = json_encode($detalle_serie_id);
                    $stmt->bind_param("is", $detalle_serie_id, $json_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        throw new Exception("Ya existe una garantía para el número de serie: " . $equipo['numero_serie']);
                    }
                }
                
                // Usar el primer detalle_serie_id como referencia principal
                $primer_detalle_serie_id = $detalle_serie_ids[0];
                
                // Convertir el array de IDs a JSON
                $series_ids_json = json_encode($detalle_serie_ids);
                
                // Insertar una ÚNICA garantía para todas las series
                $stmt = $this->conectar->prepare("
                    INSERT INTO garantia (numero_serie_id, detalle_serie_id, series_ids, guia_remision, fecha_inicio, fecha_caducidad) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iissss", $numero_serie_id, $primer_detalle_serie_id, $series_ids_json, $guia_remision, $fecha_inicio, $fecha_caducidad);
                $stmt->execute();
                
                // Actualizar el estado de todas las series a 'en_garantia'
                foreach ($detalle_serie_ids as $id) {
                    $stmt = $this->conectar->prepare("
                        UPDATE detalle_serie 
                        SET estado = 'en_garantia' 
                        WHERE id = ?
                    ");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                }
            } else {
                // Formato antiguo: se registra una garantía para todos los equipos
                // Obtener todos los detalles de serie para este numero_serie_id
                $stmt = $this->conectar->prepare("
                    SELECT id FROM detalle_serie 
                    WHERE numero_serie_id = ? AND estado = 'disponible'
                ");
                $stmt->bind_param("i", $numero_serie_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception("No se encontraron detalles de serie disponibles para el ID: " . $numero_serie_id);
                }
                
                // Recolectar todos los detalle_serie_id
                $detalle_serie_ids = [];
                while ($row = $result->fetch_assoc()) {
                    $detalle_serie_ids[] = $row['id'];
                }
                
                // Usar el primer detalle_serie_id como referencia principal
                $primer_detalle_serie_id = $detalle_serie_ids[0];
                
                // Convertir el array de IDs a JSON
                $series_ids_json = json_encode($detalle_serie_ids);
                
                // Insertar una ÚNICA garantía para todas las series
                $stmt = $this->conectar->prepare("
                    INSERT INTO garantia (numero_serie_id, detalle_serie_id, series_ids, guia_remision, fecha_inicio, fecha_caducidad) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iissss", $numero_serie_id, $primer_detalle_serie_id, $series_ids_json, $guia_remision, $fecha_inicio, $fecha_caducidad);
                $stmt->execute();
                
                // Actualizar el estado de todas las series a 'en_garantia'
                foreach ($detalle_serie_ids as $id) {
                    $stmt = $this->conectar->prepare("
                        UPDATE detalle_serie 
                        SET estado = 'en_garantia' 
                        WHERE id = ?
                    ");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                }
            }
        } 
        // Verificar si viene de garantia-manual.php (con numero_serie)
        else if (isset($_POST['numero_serie'])) {
            $numero_serie = $_POST['numero_serie'];
            
            // Verificar si es una lista de series separadas por coma
            $series = explode(',', $numero_serie);
            $series = array_map('trim', $series);
            
            if (count($series) > 1) {
                // Múltiples series
                $detalle_serie_ids = [];
                $numero_serie_id = null;
                
                foreach ($series as $serie) {
                    // Buscar el detalle_serie_id y numero_serie_id correspondientes al número de serie
                    $stmt = $this->conectar->prepare("
                        SELECT ds.id as detalle_serie_id, ds.numero_serie_id 
                        FROM detalle_serie ds
                        WHERE ds.numero_serie = ? AND ds.estado = 'disponible'
                    ");
                    $stmt->bind_param("s", $serie);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 0) {
                        throw new Exception("No se encontró el número de serie disponible: " . $serie);
                    }
                    
                    $row = $result->fetch_assoc();
                    $detalle_serie_id = $row['detalle_serie_id'];
                    
                    // Asegurarse de que todas las series pertenecen al mismo cliente (numero_serie_id)
                    if ($numero_serie_id === null) {
                        $numero_serie_id = $row['numero_serie_id'];
                    } else if ($numero_serie_id != $row['numero_serie_id']) {
                        throw new Exception("Las series seleccionadas pertenecen a diferentes clientes");
                    }
                    
                    $detalle_serie_ids[] = $detalle_serie_id;
                    
                    // Verificar si ya existe una garantía para este detalle_serie_id
                    $stmt = $this->conectar->prepare("
                        SELECT id_garantia FROM garantia 
                        WHERE detalle_serie_id = ? OR JSON_CONTAINS(series_ids, ?)
                    ");
                    $json_id = json_encode($detalle_serie_id);
                    $stmt->bind_param("is", $detalle_serie_id, $json_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        throw new Exception("Ya existe una garantía para el número de serie: " . $serie);
                    }
                }
                
                // Usar el primer detalle_serie_id como referencia principal
                $primer_detalle_serie_id = $detalle_serie_ids[0];
                
                // Convertir el array de IDs a JSON
                $series_ids_json = json_encode($detalle_serie_ids);
                
                // Insertar una ÚNICA garantía para todas las series
                $stmt = $this->conectar->prepare("
                    INSERT INTO garantia (numero_serie_id, detalle_serie_id, series_ids, guia_remision, fecha_inicio, fecha_caducidad) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iissss", $numero_serie_id, $primer_detalle_serie_id, $series_ids_json, $guia_remision, $fecha_inicio, $fecha_caducidad);
                $stmt->execute();
                
                // Actualizar el estado de todas las series a 'en_garantia'
                foreach ($detalle_serie_ids as $id) {
                    $stmt = $this->conectar->prepare("
                        UPDATE detalle_serie 
                        SET estado = 'en_garantia' 
                        WHERE id = ?
                    ");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                }
            } else {
                // Una sola serie (código original)
                // Buscar el detalle_serie_id y numero_serie_id correspondientes al número de serie
                $stmt = $this->conectar->prepare("
                    SELECT ds.id as detalle_serie_id, ds.numero_serie_id 
                    FROM detalle_serie ds
                    WHERE ds.numero_serie = ?
                ");
                $stmt->bind_param("s", $numero_serie);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception("No se encontró el número de serie: " . $numero_serie);
                }
                
                $row = $result->fetch_assoc();
                $detalle_serie_id = $row['detalle_serie_id'];
                $numero_serie_id = $row['numero_serie_id'];
                
                // Verificar si ya existe una garantía para este detalle_serie_id
                $stmt = $this->conectar->prepare("
                    SELECT id_garantia FROM garantia 
                    WHERE detalle_serie_id = ? OR JSON_CONTAINS(series_ids, ?)
                ");
                $json_id = json_encode($detalle_serie_id);
                $stmt->bind_param("is", $detalle_serie_id, $json_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    throw new Exception("Ya existe una garantía para el número de serie: " . $numero_serie);
                }
                
                // Insertar la garantía
                $stmt = $this->conectar->prepare("
                    INSERT INTO garantia (numero_serie_id, detalle_serie_id, guia_remision, fecha_inicio, fecha_caducidad) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iisss", $numero_serie_id, $detalle_serie_id, $guia_remision, $fecha_inicio, $fecha_caducidad);
                $stmt->execute();
                
                // Actualizar el estado del número de serie a 'en_garantia'
                $stmt = $this->conectar->prepare("
                    UPDATE detalle_serie 
                    SET estado = 'en_garantia' 
                    WHERE id = ?
                ");
                $stmt->bind_param("i", $detalle_serie_id);
                $stmt->execute();
            }
        } else {
            throw new Exception("Falta el número de serie");
        }
        
        // Confirmar la transacción
        $this->conectar->commit();
        echo json_encode(['res' => true, 'msg' => 'Garantía registrada correctamente']);
        
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $this->conectar->rollback();
        echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
    }
}
    public function cargarDatosNumeroSerie()
    {
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Consulta modificada para obtener nombres en lugar de IDs
            $sql = "SELECT ns.*, ds.*, 
                mo.nombre AS modelo_nombre, 
                ma.nombre AS marca_nombre, 
                e.nombre AS equipo_nombre 
                FROM numero_series ns
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                LEFT JOIN modelos mo ON ds.modelo = mo.id
                LEFT JOIN marcas ma ON ds.marca = ma.id
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
                            'modelo' => $row['modelo'],
                            'modelo_nombre' => $row['modelo_nombre'],
                            'marca' => $row['marca'],
                            'marca_nombre' => $row['marca_nombre'],
                            'equipo' => $row['equipo'],
                            'equipo_nombre' => $row['equipo_nombre'],
                            'numero_serie' => $row['numero_serie']
                        ];
                    }
                }

                if ($serie) {
                    $serie['equipos'] = $equipos;
                    echo json_encode(['success' => true, 'data' => $serie]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Serie no encontrada']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
        }
    }
    public function render()
    {
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
        $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
        
        $getAll = $this->garantia->getAllData($filtro, $tipo_busqueda);
        echo json_encode($getAll);
    }

    public function getOne()
{
    if (isset($_POST['id_garantia'])) {
        $id = $_POST['id_garantia'];
        
        try {
            // Primero obtenemos la información básica de la garantía
            $sql = "SELECT g.*, ns.cliente_ruc_dni, g.series_ids
                    FROM garantia g
                    JOIN numero_series ns ON g.numero_serie_id = ns.id
                    WHERE g.id_garantia = ?";
            
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode(['error' => 'Garantía no encontrada']);
                return;
            }
            
            $garantia = $result->fetch_assoc();
            
            // Verificar si hay series_ids
            $series = [];
            if (!empty($garantia['series_ids'])) {
                $seriesIds = json_decode($garantia['series_ids'], true);
                
                if (is_array($seriesIds) && count($seriesIds) > 0) {
                    // Consultar los detalles de todas las series
                    $placeholders = implode(',', array_fill(0, count($seriesIds), '?'));
                    $sql = "SELECT ds.*, m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
                            FROM detalle_serie ds
                            LEFT JOIN marcas m ON ds.marca = m.id
                            LEFT JOIN modelos mo ON ds.modelo = mo.id
                            LEFT JOIN equipos e ON ds.equipo = e.id
                            WHERE ds.id IN ($placeholders)";
                    
                    $stmt = $this->conectar->prepare($sql);
                    
                    // Crear un array con los tipos de parámetros
                    $types = str_repeat('i', count($seriesIds));
                    
                    // Crear un array con los valores de los parámetros
                    $params = $seriesIds;
                    
                    // Añadir los tipos de parámetros al inicio del array
                    array_unshift($params, $types);
                    
                    // Pasar los parámetros usando call_user_func_array
                    call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
                    
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        $series[] = [
                            'id_garantia' => $garantia['id_garantia'],
                            'cliente_ruc_dni' => $garantia['cliente_ruc_dni'],
                            'guia_remision' => $garantia['guia_remision'],
                            'fecha_inicio' => $garantia['fecha_inicio'],
                            'fecha_caducidad' => $garantia['fecha_caducidad'],
                            'numero_serie' => $row['numero_serie'],
                            'marca' => $row['marca'],
                            'marca_nombre' => $row['marca_nombre'],
                            'modelo' => $row['modelo'],
                            'modelo_nombre' => $row['modelo_nombre'],
                            'equipo' => $row['equipo'],
                            'equipo_nombre' => $row['equipo_nombre']
                        ];
                    }
                }
            }
            
            // Si no hay series asociadas, devolver solo la garantía principal
            if (empty($series)) {
                // Obtener los detalles de la serie principal
                $sql = "SELECT ds.*, m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
                        FROM detalle_serie ds
                        LEFT JOIN marcas m ON ds.marca = m.id
                        LEFT JOIN modelos mo ON ds.modelo = mo.id
                        LEFT JOIN equipos e ON ds.equipo = e.id
                        WHERE ds.id = ?";
                
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("i", $garantia['detalle_serie_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $series[] = [
                        'id_garantia' => $garantia['id_garantia'],
                        'cliente_ruc_dni' => $garantia['cliente_ruc_dni'],
                        'guia_remision' => $garantia['guia_remision'],
                        'fecha_inicio' => $garantia['fecha_inicio'],
                        'fecha_caducidad' => $garantia['fecha_caducidad'],
                        'numero_serie' => $row['numero_serie'],
                        'marca' => $row['marca'],
                        'marca_nombre' => $row['marca_nombre'],
                        'modelo' => $row['modelo'],
                        'modelo_nombre' => $row['modelo_nombre'],
                        'equipo' => $row['equipo'],
                        'equipo_nombre' => $row['equipo_nombre']
                    ];
                }
            }
            
            echo json_encode($series);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'ID de garantía no proporcionado']);
    }
}

// Función auxiliar para pasar parámetros por referencia
private function refValues($arr)
{
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

    public function editar()
    {
        if (!empty($_POST)) {
            $id_garantia = trim(filter_var($_POST['id_garantia'], FILTER_SANITIZE_NUMBER_INT));
            $cliente = trim(filter_var($_POST['cliente'], FILTER_SANITIZE_NUMBER_INT));
            $marca = trim(filter_var($_POST['marca'], FILTER_SANITIZE_STRING));
            $modelo = trim(filter_var($_POST['modelo'], FILTER_SANITIZE_STRING));
            $numero_serie = trim(filter_var($_POST['numero_serie'], FILTER_SANITIZE_STRING));
            $guia_remision = trim(filter_var($_POST['guia_remision'], FILTER_SANITIZE_STRING));
            $fecha_inicio = trim(filter_var($_POST['fecha_inicio'], FILTER_SANITIZE_STRING));
            $fecha_caducidad = trim(filter_var($_POST['fecha_caducidad'], FILTER_SANITIZE_STRING));

            if ($id_garantia && $cliente && $marca && $modelo && $numero_serie) {
                $this->garantia->setIdGarantia($id_garantia);
                // $this->garantia->setCliente($cliente);
                // $this->garantia->setMarca($marca);
                // $this->garantia->setModelo($modelo);
                // $this->garantia->setNumeroSerie($numero_serie);
                $this->garantia->setGuiaRemision($guia_remision);
                $this->garantia->setFechaInicio($fecha_inicio);
                $this->garantia->setFechaCaducidad($fecha_caducidad);

                $save = $this->garantia->editar($this->garantia->getOne($id_garantia));

                if ($save) {
                    echo json_encode(["res" => true, "msg" => "Garantía actualizada correctamente"]);
                } else {
                    echo json_encode(["res" => false, "msg" => "Ocurrió un error al actualizar la garantía"]);
                }
            } else {
                echo json_encode(["res" => false, "msg" => "Llene el formulario correctamente"]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "Error: Datos vacíos"]);
        }
    }

public function borrar()
{
    $dataId = $_POST["value"];
    
    // Iniciar transacción
    $this->conectar->begin_transaction();
    
    try {
        // Obtener los detalle_serie_id antes de eliminar la garantía
        $stmt = $this->conectar->prepare("
            SELECT detalle_serie_id, series_ids FROM garantia WHERE id_garantia = ?
        ");
        $stmt->bind_param("i", $dataId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("No se encontró la garantía con ID: " . $dataId);
        }
        
        $row = $result->fetch_assoc();
        $detalle_serie_id = $row['detalle_serie_id'];
        $series_ids = json_decode($row['series_ids'] ?? '[]', true);
        
        // Si no hay series_ids, usar solo el detalle_serie_id
        if (empty($series_ids)) {
            $series_ids = [$detalle_serie_id];
        }
        
        // Eliminar la garantía
        $stmt = $this->conectar->prepare("
            DELETE FROM garantia WHERE id_garantia = ?
        ");
        $stmt->bind_param("i", $dataId);
        $stmt->execute();
        
        // Restaurar el estado de todas las series a 'disponible'
        foreach ($series_ids as $id) {
            $stmt = $this->conectar->prepare("
                UPDATE detalle_serie 
                SET estado = 'disponible' 
                WHERE id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        
        // Confirmar la transacción
        $this->conectar->commit();
        echo json_encode(["res" => true, "msg" => "Garantía eliminada correctamente"]);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $this->conectar->rollback();
        echo json_encode(["res" => false, "msg" => $e->getMessage()]);
    }
}

    public function importarExcel()
    {
        $respuesta = ["res" => false];
        $filename = $_FILES['file']['name'];

        $path_parts = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = Tools::getToken(80);
        $loc_ruta = "files/temp";

        if (!file_exists($loc_ruta)) {
            mkdir($loc_ruta, 0777, true);
        }

        $location = $loc_ruta . "/" . $newName . '.' . $path_parts;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            $nombre_logo = $newName . "." . $path_parts;

            $respuesta["res"] = true;
            $type = $path_parts;

            if ($type == "xlsx") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($type == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($type == "csv") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load("files/temp/" . $nombre_logo);
            $schdeules = $spreadsheet->getActiveSheet()->toArray();
            $respuesta["data"] = $schdeules;

            unlink($location);
        }

        return json_encode($respuesta);
    }
}
