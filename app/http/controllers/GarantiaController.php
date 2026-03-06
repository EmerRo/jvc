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
            // <CHANGE> Procesar datos del cliente y obtener/crear id_cliente
            $cliente_nombre = $_POST['cliente_nombre'];
            $cliente_documento = isset($_POST['cliente_documento']) ? $_POST['cliente_documento'] : '';
            $id_cliente = $this->procesarCliente($cliente_nombre, $cliente_documento);
            error_log("ID Cliente procesado: " . $id_cliente); // <CHANGE> Debug log

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

                    // Obtener TODOS los detalle_serie_id para este numero_serie_id
                    $stmt = $this->conectar->prepare("
                        SELECT ds.id, ds.numero_serie, ds.marca_id, ds.modelo_id, ds.equipo_id
                        FROM detalle_serie ds
                        WHERE ds.numero_serie_id = ?
                        ORDER BY ds.id
                    ");
                    $stmt->bind_param("i", $numero_serie_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 0) {
                        throw new Exception("No se encontraron detalles de serie para el ID: " . $numero_serie_id);
                    }

                    // Crear un array con todos los detalle_serie
                    $detalles_serie = [];
                    while ($row = $result->fetch_assoc()) {
                        $detalles_serie[] = $row;
                    }

                    // Verificar si ya existe una garantía para este numero_serie_id
                    $stmt = $this->conectar->prepare("
                        SELECT id_garantia FROM garantia WHERE numero_serie_id = ?
                    ");
                    $stmt->bind_param("i", $numero_serie_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        throw new Exception("Ya existe una garantía para este registro de series");
                    }

                    // CREAR UNA SOLA GARANTÍA
                    $this->garantia->setNumeroSerieId($numero_serie_id);
                    $this->garantia->setDetalleSerieId(null); // Ya no se usa este campo para múltiples equipos
                    $this->garantia->setIdCliente($id_cliente);
                    $this->garantia->setGuiaRemision($guia_remision);
                    $this->garantia->setFechaInicio($fecha_inicio);
                    $this->garantia->setFechaCaducidad($fecha_caducidad);

                    // Insertar la garantía principal
                    if (!$this->garantia->insertar()) {
                        throw new Exception("Error al insertar garantía");
                    }

                    // Obtener el ID de la garantía recién creada
                    $id_garantia = $this->garantia->getIdGarantia();
                    error_log("Garantía creada con ID: " . $id_garantia);

                    // INSERTAR TODOS LOS EQUIPOS EN detalle_garantia
                    $stmt_detalle = $this->conectar->prepare("
                        INSERT INTO detalle_garantia 
                        (id_garantia, detalle_serie_id, numero_serie, marca_id, modelo_id, equipo_id) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");

                    $equipos_insertados = 0;
                    foreach ($detalles_serie as $detalle) {
                        $stmt_detalle->bind_param(
                            "iisiii",
                            $id_garantia,
                            $detalle['id'],
                            $detalle['numero_serie'],
                            $detalle['marca_id'],
                            $detalle['modelo_id'],
                            $detalle['equipo_id']
                        );

                        if (!$stmt_detalle->execute()) {
                            throw new Exception("Error al insertar detalle de garantía para serie: " . $detalle['numero_serie']);
                        }

                        // Actualizar el estado del equipo a 'en_garantia'
                        $stmt_update = $this->conectar->prepare("
                            UPDATE detalle_serie 
                            SET estado = 'en_garantia' 
                            WHERE id = ?
                        ");
                        $stmt_update->bind_param("i", $detalle['id']);
                        $stmt_update->execute();

                        $equipos_insertados++;
                    }

                    error_log("Total de equipos insertados en detalle_garantia: " . $equipos_insertados);
                } else {
                    // Formato antiguo: se registra una garantía para todos los equipos
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

                    $row = $result->fetch_assoc();
                    $detalle_serie_id = $row['id'];

                    // USAR EL MODELO para insertar
                    $this->garantia->setNumeroSerieId($numero_serie_id);
                    $this->garantia->setDetalleSerieId($detalle_serie_id);
                    $this->garantia->setGuiaRemision($guia_remision);
                    $this->garantia->setFechaInicio($fecha_inicio);
                    $this->garantia->setFechaCaducidad($fecha_caducidad);
                    $this->garantia->setIdCliente($id_cliente);

                    if (!$this->garantia->insertar()) {
                        throw new Exception("Error al insertar garantía");
                    }

                    // Actualizar el estado a 'en_garantia'
                    $stmt = $this->conectar->prepare("
                    UPDATE detalle_serie 
                    SET estado = 'en_garantia' 
                    WHERE id = ?
                ");
                    $stmt->bind_param("i", $detalle_serie_id);
                    $stmt->execute();
                }
            }
            // Verificar si viene de garantia-manual.php (con numero_serie)
            else if (isset($_POST['numero_serie'])) {
                $numero_serie = $_POST['numero_serie'];

                // Verificar si es una lista de series separadas por coma
                $series = explode(',', $numero_serie);
                $series = array_map('trim', $series);

                if (count($series) > 1) {
                    // Múltiples series - buscar el primer cliente que contenga todas las series
                    $numero_serie_id = null;
                    $detalle_serie_id = null;

                    foreach ($series as $serie) {
                        $stmt = $this->conectar->prepare("
                        SELECT ds.id, ds.numero_serie_id 
                        FROM detalle_serie ds
                        WHERE ds.numero_serie = ? 
                        AND ds.estado = 'disponible'
                    ");
                        $stmt->bind_param("s", $serie);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            throw new Exception("No se encontró el número de serie disponible: " . $serie);
                        }

                        $row = $result->fetch_assoc();

                        if ($numero_serie_id === null) {
                            $numero_serie_id = $row['numero_serie_id'];
                            $detalle_serie_id = $row['id'];
                        } else if ($numero_serie_id != $row['numero_serie_id']) {
                            throw new Exception("Las series seleccionadas pertenecen a diferentes clientes");
                        }
                    }

                    // USAR EL MODELO
                    $this->garantia->setNumeroSerieId($numero_serie_id);
                    $this->garantia->setDetalleSerieId($detalle_serie_id);
                    $this->garantia->setGuiaRemision($guia_remision);
                    $this->garantia->setFechaInicio($fecha_inicio);
                    $this->garantia->setFechaCaducidad($fecha_caducidad);
                    $this->garantia->setIdCliente($id_cliente);
                    if (!$this->garantia->insertar()) {
                        throw new Exception("Error al insertar garantía");
                    }

                    // Actualizar estado
                    $stmt = $this->conectar->prepare("
                    UPDATE detalle_serie 
                    SET estado = 'en_garantia' 
                    WHERE id = ?
                ");
                    $stmt->bind_param("i", $detalle_serie_id);
                    $stmt->execute();
                } else {
                    // Una sola serie
                    $stmt = $this->conectar->prepare("
                    SELECT ds.id, ds.numero_serie_id 
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
                    $detalle_serie_id = $row['id'];
                    $numero_serie_id = $row['numero_serie_id'];

                    // USAR EL MODELO
                    $this->garantia->setNumeroSerieId($numero_serie_id);
                    $this->garantia->setDetalleSerieId($detalle_serie_id);
                    $this->garantia->setGuiaRemision($guia_remision);
                    $this->garantia->setFechaInicio($fecha_inicio);
                    $this->garantia->setFechaCaducidad($fecha_caducidad);

                    if (!$this->garantia->insertar()) {
                        throw new Exception("Error al insertar garantía");
                    }

                    // Actualizar estado
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

            $sql = "SELECT ns.id, ns.cliente_ruc_dni, ns.fecha_creacion, ns.cantidad_equipos,
                    ds.numero_serie, ds.modelo_id, ds.marca_id, ds.equipo_id,
                    m.nombre as modelo_nombre, ma.nombre as marca_nombre, e.nombre as equipo_nombre
                FROM numero_series ns
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                LEFT JOIN modelos m ON ds.modelo_id = m.id
                LEFT JOIN marcas ma ON ds.marca_id = ma.id
                LEFT JOIN equipos e ON ds.equipo_id = e.id
                WHERE ns.id = ?";

            try {
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $resultado = $stmt->get_result();

                $serie = null;
                $equipos = [];

                while ($row = $resultado->fetch_assoc()) {
                    if ($serie === null) {
                        $serie = [
                            'id' => $row['id'],
                            'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                            'fecha_creacion' => $row['fecha_creacion'],
                            'cantidad_equipos' => $row['cantidad_equipos']
                        ];
                    }

                    if ($row['numero_serie']) {
                        $equipos[] = [
                            'modelo' => $row['modelo_id'],
                            'modelo_nombre' => $row['modelo_nombre'] ?: '',
                            'marca' => $row['marca_id'],
                            'marca_nombre' => $row['marca_nombre'] ?: '',
                            'equipo' => $row['equipo_id'],
                            'equipo_nombre' => $row['equipo_nombre'] ?: '',
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
                // Consultar datos básicos de la garantía
                $sql = "SELECT g.*, ns.cliente_ruc_dni,
                    CASE 
                        WHEN g.id_cliente IS NOT NULL THEN c.datos
                        ELSE ns.cliente_ruc_dni
                    END as cliente_nombre
                    FROM garantia g
                    JOIN numero_series ns ON g.numero_serie_id = ns.id
                    LEFT JOIN clientes c ON g.id_cliente = c.id_cliente
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

                // Consultar todos los equipos desde detalle_garantia
                $sql_equipos = "SELECT dg.numero_serie, dg.marca_id, dg.modelo_id, dg.equipo_id,
                    m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
                    FROM detalle_garantia dg
                    LEFT JOIN marcas m ON dg.marca_id = m.id
                    LEFT JOIN modelos mo ON dg.modelo_id = mo.id
                    LEFT JOIN equipos e ON dg.equipo_id = e.id
                    WHERE dg.id_garantia = ?
                    ORDER BY dg.id";

                $stmt_equipos = $this->conectar->prepare($sql_equipos);
                $stmt_equipos->bind_param("i", $id);
                $stmt_equipos->execute();
                $result_equipos = $stmt_equipos->get_result();

                // Crear array con todos los equipos
                $series = [];
                while ($equipo = $result_equipos->fetch_assoc()) {
                    $series[] = [
                        'id_garantia' => $garantia['id_garantia'],
                        'cliente_ruc_dni' => $garantia['cliente_ruc_dni'],
                        'cliente_nombre' => $garantia['cliente_nombre'],
                        'guia_remision' => $garantia['guia_remision'],
                        'fecha_inicio' => $garantia['fecha_inicio'],
                        'fecha_caducidad' => $garantia['fecha_caducidad'],
                        'numero_serie' => $equipo['numero_serie'],
                        'marca' => $equipo['marca_id'],
                        'marca_nombre' => $equipo['marca_nombre'] ?: '-',
                        'modelo' => $equipo['modelo_id'],
                        'modelo_nombre' => $equipo['modelo_nombre'] ?: '-',
                        'equipo' => $equipo['equipo_id'],
                        'equipo_nombre' => $equipo['equipo_nombre'] ?: '-'
                    ];
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
            // Obtener todos los detalle_serie_id de la garantía desde detalle_garantia
            $stmt = $this->conectar->prepare("
                SELECT detalle_serie_id FROM detalle_garantia WHERE id_garantia = ?
            ");
            $stmt->bind_param("i", $dataId);
            $stmt->execute();
            $result = $stmt->get_result();

            $detalle_serie_ids = [];
            while ($row = $result->fetch_assoc()) {
                $detalle_serie_ids[] = $row['detalle_serie_id'];
            }

            if (empty($detalle_serie_ids)) {
                throw new Exception("No se encontraron detalles de garantía para el ID: " . $dataId);
            }

            // Eliminar los detalles de garantía
            $stmt = $this->conectar->prepare("
                DELETE FROM detalle_garantia WHERE id_garantia = ?
            ");
            $stmt->bind_param("i", $dataId);
            $stmt->execute();

            // Eliminar la garantía
            $stmt = $this->conectar->prepare("
                DELETE FROM garantia WHERE id_garantia = ?
            ");
            $stmt->bind_param("i", $dataId);
            $stmt->execute();

            // Restaurar el estado a 'disponible' para todos los equipos
            $placeholders = implode(',', array_fill(0, count($detalle_serie_ids), '?'));
            $stmt = $this->conectar->prepare("
                UPDATE detalle_serie 
                SET estado = 'disponible' 
                WHERE id IN ($placeholders)
            ");
            
            $types = str_repeat('i', count($detalle_serie_ids));
            $stmt->bind_param($types, ...$detalle_serie_ids);
            $stmt->execute();

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
    /**
     * Procesa el cliente: busca en la tabla clientes o crea uno nuevo desde API externa
     * @param string $cliente_nombre
     * @param string $cliente_documento
     * @return int $id_cliente
     */
    private function procesarCliente($cliente_nombre, $cliente_documento)
    {
        require_once "app/models/Cliente.php";
        $clienteModel = new Cliente();

        // Si hay documento, buscar primero en la tabla clientes
        if (!empty($cliente_documento)) {
            $clienteModel->setDocumento($cliente_documento);
            $clienteModel->setIdEmpresa($_SESSION['id_empresa']);

            // Verificar si ya existe el cliente
            if ($clienteModel->verificarDocumento()) {
                $idCliente = $clienteModel->getIdCliente();
                error_log("Cliente encontrado con ID: " . $idCliente); // <CHANGE> Debug log
                return $idCliente;
            }

            // Si no existe, crear nuevo cliente con los datos proporcionados
            $clienteModel->setDatos($cliente_nombre);
            $clienteModel->setDireccion(''); // Dirección vacía por defecto
            $clienteModel->setTelefono('');
            $clienteModel->setEmail('');
            $clienteModel->setRubro(null);

            if ($clienteModel->insertar()) {
                return $clienteModel->getIdCliente();
            } else {
                throw new Exception("Error al crear el cliente");
            }
        }

        // Si no hay documento, buscar por nombre exacto
        $stmt = $this->conectar->prepare("
        SELECT id_cliente FROM clientes 
        WHERE datos = ? AND id_empresa = ?
    ");
        $stmt->bind_param("si", $cliente_nombre, $_SESSION['id_empresa']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['id_cliente'];
        }

        // Si no existe, crear cliente solo con nombre
        $clienteModel->setDocumento(''); // Sin documento
        $clienteModel->setDatos($cliente_nombre);
        $clienteModel->setDireccion('');
        $clienteModel->setTelefono('');
        $clienteModel->setEmail('');
        $clienteModel->setRubro(null);

        if ($clienteModel->insertar()) {
            return $clienteModel->getIdCliente();
        } else {
            throw new Exception("Error al crear el cliente");
        }
    }
}
