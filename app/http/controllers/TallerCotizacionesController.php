<?php
class TallerCotizacionesController extends Controller
{
    private $conectar;
    protected $request;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function obtenerInfoPreAlerta()
    {
        try {
            $preAlertaId = $_POST['id'];

            error_log("Obteniendo información de pre_alerta para ID: " . $preAlertaId);

            $query = "SELECT 
            pa.id_preAlerta,
            pa.cliente_razon_social,
            pa.cliente_ruc,
            pa.direccion,
            pa.atencion_encargado,
            pa.fecha_ingreso,
            GROUP_CONCAT(DISTINCT pad.marca) as marcas,
            GROUP_CONCAT(DISTINCT pad.equipo) as equipos,
            GROUP_CONCAT(DISTINCT pad.modelo) as modelos,
            GROUP_CONCAT(DISTINCT pad.numero_serie) as numeros_serie
            FROM pre_alerta pa
            LEFT JOIN pre_alerta_detalles pad ON pa.id_preAlerta = pad.id_preAlerta
            WHERE pa.id_preAlerta = ?
            GROUP BY pa.id_preAlerta";

            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $preAlertaId);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $preAlerta = $resultado->fetch_assoc();

            if (!$preAlerta) {
                error_log("No se encontró pre_alerta con ID: " . $preAlertaId);
                echo json_encode([
                    'res' => false,
                    'error' => 'Pre alerta no encontrada'
                ]);
                return;
            }


            $response = [
                'res' => true,
                'data' => [
                    'id_preAlerta' => $preAlerta['id_preAlerta'],
                    'cliente_nombre' => $preAlerta['cliente_razon_social'],
                    'cliente_doc' => $preAlerta['cliente_ruc'],
                    'cliente_direccion' => $preAlerta['direccion'],
                    'tecnico_nombre' => $preAlerta['atencion_encargado'],
                    'fecha_ingreso' => $preAlerta['fecha_ingreso'],
                    'marcas' => explode(', ', $preAlerta['marcas']),
                    'equipos' => explode(', ', $preAlerta['equipos']),
                    'modelos' => explode(', ', $preAlerta['modelos']),
                    'numeros_serie' => explode(', ', $preAlerta['numeros_serie'])
                ]
            ];


            error_log("Enviando respuesta: " . json_encode($response));
            echo json_encode($response);

        } catch (Exception $e) {
            error_log("Error en obtenerInfoPreAlerta: " . $e->getMessage());
            echo json_encode([
                'res' => false,
                'error' => 'Error al procesar la pre alerta: ' . $e->getMessage()
            ]);
        }
    }

    public function agregar()
    {
        $respuesta = ["res" => false];

        try {
            // Verificar permisos
            $permisos = $this->verificarPermisos();

            // Si no puede editar, no permitir guardar
            if (!$permisos['puedeEditar']) {
                throw new Exception("No tiene permisos para crear o editar cotizaciones");
            }

            $this->conectar->begin_transaction();

            $data = $_POST;
            error_log("Datos POST recibidos: " . print_r($data, true));
            error_log("Archivos recibidos: " . print_r($_FILES, true));

            // Obtener ID de pre-alerta
            $preAlertaId = isset($data['id_prealerta']) ? intval($data['id_prealerta']) : (isset($_GET['id']) ? intval($_GET['id']) : null);
            error_log("ID de pre-alerta capturado: " . ($preAlertaId ?? 'null'));

            // Verificar si el cliente existe o crear uno nuevo
            $idCli = $this->gestionarCliente($data);

            // Obtener siguiente número de cotización
            $numCoti = $this->obtenerSiguienteNumeroCotizacion();

            // Obtener el descuento
            $descuento = isset($data['descuento']) ? floatval($data['descuento']) : 0;

            // Insertar cotización
            $idCoti = $this->insertarCotizacion($data, $idCli, $numCoti, $preAlertaId, $descuento);

            if ($idCoti) {
                // Insertar equipos
                $equipos = json_decode($data['equipos'], true);
                $this->insertarEquipos($idCoti, $equipos);

                // Insertar cuotas si existen
                $this->insertarCuotas($idCoti, $data);

                // Registrar los productos en el log para depuración
                if (!empty($data['listaPro'])) {
                    error_log("Productos a insertar: " . $data['listaPro']);
                } else {
                    error_log("No hay productos para insertar");
                }

                // Insertar repuestos
                $this->insertarRepuestos($idCoti, $data);

                // Manejar las fotos
                if (isset($_FILES['fotos']) && is_array($_FILES['fotos'])) {
                    $fotosEquipo = isset($_POST['fotos_equipo']) ? $_POST['fotos_equipo'] : [];
                    $this->manejarFotosAsync($idCoti, $_FILES['fotos'], $fotosEquipo);
                }

                // Guardar condiciones y diagnóstico si existen en sesión
                $this->guardarCondicionesDiagnosticoTaller($idCoti);
                //guardar observaciones 
                $this->guardarObservacionesTaller($idCoti);
                // Actualizar el estado de la pre-alerta
                if ($preAlertaId) {
                    $this->actualizarEstadoPreAlerta($preAlertaId);
                }

                $this->conectar->commit();
                // Enviar una única respuesta JSON combinada
                header('Content-Type: application/json');
                echo json_encode([
                    'res' => true,
                    'cotizacion' => [
                        'numero' => $numCoti,
                        'cotizacion_id' => $idCoti,
                        'pdfUrl' => URL::to('/r/taller/reporte/' . $idCoti)
                    ],
                    'msg' => 'Cotización guardada correctamente',
                    'id' => $idCoti,
                    'id_prealerta' => $preAlertaId
                ]);
                return;
            } else {
                throw new Exception("Error al insertar la cotización");
            }
        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en TallerCotizacionesController::agregar: " . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode([
                'res' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    private function insertarEquipos($idCoti, $equipos)
    {
        if (!empty($equipos) && is_array($equipos)) {
            $sql = "INSERT INTO taller_cotizaciones_equipos (id_cotizacion, marca, equipo, modelo, numero_serie) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);

            foreach ($equipos as $equipo) {
                $stmt->bind_param(
                    "issss",
                    $idCoti,
                    $equipo['marca'],
                    $equipo['equipo'],
                    $equipo['modelo'],
                    $equipo['numero_serie']
                );
                $stmt->execute();
            }
        }
    }
    private function gestionarCliente($data)
    {
        $sql = "SELECT * FROM clientes_taller WHERE documento = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $data['num_doc']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($rowCl = $result->fetch_assoc()) {
            $idCli = $rowCl['id_cliente_taller'];
            $sqlUpdate = "UPDATE clientes_taller SET datos = ?, direccion = ?, atencion = ? WHERE id_cliente_taller = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssi", $data['nom_cli'], $data['dir_cli'], $data['dir2_cli'], $idCli);
            $stmtUpdate->execute();
        } else {
            $sqlInsert = "INSERT INTO clientes_taller (documento, datos, direccion, atencion, id_empresa) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $this->conectar->prepare($sqlInsert);
            $stmtInsert->bind_param("ssssi", $data['num_doc'], $data['nom_cli'], $data['dir_cli'], $data['dir2_cli'], $_SESSION['id_empresa']);
            $stmtInsert->execute();
            $idCli = $this->conectar->insert_id;
        }

        return $idCli;
    }

    private function obtenerSiguienteNumeroCotizacion()
    {
        $sql = "SELECT MAX(numero) as ultimo FROM taller_cotizaciones WHERE id_empresa = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $_SESSION['id_empresa']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return ($row['ultimo'] ?? 0) + 1;
    }

    private function insertarCotizacion($data, $idCli, $numCoti, $preAlertaId, $descuento)
    {
        $sql = "INSERT INTO taller_cotizaciones (
        id_tido, moneda, cm_tc, id_tipo_pago, fecha, 
        dias_pagos, direccion, id_cliente_taller, total, 
        numero, estado, usar_precio, sucursal, id_empresa, 
        id_usuario, id_prealerta, descuento
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

        $estado = '0';

        $stmt = $this->conectar->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
        }

        $stmt->bind_param(
            "ssssssisdisisiiid",
            $data['tipo_doc'],
            $data['moneda'],
            $data['tc'],
            $data['tipo_pago'],
            $data['fecha'],
            $data['dias_pago'],
            $data['dir_pos'],
            $idCli,
            $data['total'],
            $numCoti,
            $estado,
            $data['usar_precio'],
            $_SESSION['sucursal'],
            $_SESSION['id_empresa'],
            $_SESSION['usuario_fac'],
            $preAlertaId,
            $descuento
        );

        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }

        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    private function insertarCuotas($idCoti, $data)
    {
        if (!empty($data['dias_lista'])) {
            $listaCuotas = json_decode($data['dias_lista'], true);
            $sql = "INSERT INTO cuotas_cotizacion (id_coti, monto, fecha) VALUES (?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);
            foreach ($listaCuotas as $cuota) {
                $stmt->bind_param("ids", $idCoti, $cuota['monto'], $cuota['fecha']);
                $stmt->execute();
            }
        }
    }

    private function insertarRepuestos($idCoti, $data)
    {
        // Primero verificamos si hay productos en listaPro
        if (!empty($data['listaPro'])) {
            $productos = json_decode($data['listaPro'], true);

            // Obtener los IDs de los equipos recién insertados
            $equiposIds = [];
            $sql = "SELECT id_cotizacion_equipo, ROW_NUMBER() OVER (ORDER BY id_cotizacion_equipo) - 1 as indice 
                    FROM taller_cotizaciones_equipos 
                    WHERE id_cotizacion = ?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $idCoti);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $equiposIds[$row['indice']] = $row['id_cotizacion_equipo'];
            }

            error_log("IDs de equipos obtenidos: " . print_r($equiposIds, true));

            // Preparar la consulta para insertar repuestos
            $sql = "INSERT INTO taller_repuestos_cotis 
                (id_coti, id_repuesto, cantidad, precio, costo, id_cotizacion_equipo) 
                VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);

            // Preparar la consulta para actualizar el stock
            $sqlUpdateStock = "UPDATE repuestos 
                          SET cantidad = cantidad - ? 
                          WHERE id_repuesto = ? 
                          AND cantidad >= ?";
            $stmtUpdateStock = $this->conectar->prepare($sqlUpdateStock);

            foreach ($productos as $producto) {
                // Verificar cantidad antes de la actualización
                $sqlCheckStock = "SELECT cantidad FROM repuestos WHERE id_repuesto = ?";
                $stmtCheckStock = $this->conectar->prepare($sqlCheckStock);
                $stmtCheckStock->bind_param("i", $producto['productoid']);
                $stmtCheckStock->execute();
                $resultStock = $stmtCheckStock->get_result();
                $stockActual = $resultStock->fetch_assoc()['cantidad'];

                // Verificar si hay suficiente cantidad
                if ($stockActual < $producto['cantidad']) {
                    throw new Exception("Stock insuficiente para el producto: " . $producto['descripcion']);
                }

                // Obtener el ID del equipo según el índice del equipo activo
                $idEquipo = null;
                if (isset($producto['equipoActivo']) && isset($equiposIds[$producto['equipoActivo']])) {
                    $idEquipo = $equiposIds[$producto['equipoActivo']];
                    error_log("Asignando equipo ID: " . $idEquipo . " al producto: " . $producto['descripcion']);
                }

                // Insertar en taller_repuestos_cotis
                $stmt->bind_param(
                    "iidddi",
                    $idCoti,
                    $producto['productoid'],
                    $producto['cantidad'],
                    $producto['precioVenta'],
                    $producto['costo'],
                    $idEquipo
                );
                $stmt->execute();

                // Actualizar el stock
                $stmtUpdateStock->bind_param(
                    "dii",
                    $producto['cantidad'],
                    $producto['productoid'],
                    $producto['cantidad']
                );
                $stmtUpdateStock->execute();

                if ($stmtUpdateStock->affected_rows == 0) {
                    throw new Exception("No se pudo actualizar el stock del producto: " . $producto['descripcion']);
                }
            }
        }
    }

    private function obtenerIdCotizacionEquipo($idCoti, $equipoIndex)
    {
        $sql = "SELECT id_cotizacion_equipo 
                FROM taller_cotizaciones_equipos 
                WHERE id_cotizacion = ? 
                ORDER BY id_cotizacion_equipo 
                LIMIT ?, 1";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ii", $idCoti, $equipoIndex);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? $row['id_cotizacion_equipo'] : null;
    }

    private function manejarFotosAsync($idCoti, $fotos, $fotosEquipo = [])
    {
        if (!isset($fotos) || !is_array($fotos) || empty($fotos['name'])) {
            error_log("No hay fotos para procesar en la cotización ID: " . $idCoti);
            return;
        }

        try {
            $uploadDir = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/';

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para las fotos");
                }
            }

            if (!is_writable($uploadDir)) {
                throw new Exception("El directorio de fotos no tiene permisos de escritura");
            }

            $uploadedFiles = [];
            $errors = [];

            foreach ($fotos['tmp_name'] as $key => $tmp_name) {
                if ($fotos['error'][$key] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($fotos['name'][$key], PATHINFO_EXTENSION);
                    $fileName = uniqid('img_') . '_' . time() . '.' . $extension;
                    $targetFilePath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmp_name, $targetFilePath)) {
                        $uploadedFiles[] = [
                            'nombre' => $fileName,
                            'equipo_index' => isset($fotosEquipo[$key]) ? intval($fotosEquipo[$key]) : 0
                        ];
                    } else {
                        $errors[] = "No se pudo mover el archivo: " . $fotos['name'][$key];
                    }
                } else {
                    $errors[] = "Error al subir el archivo: " . $fotos['name'][$key];
                }
            }

            if (!empty($uploadedFiles)) {
                $this->conectar->begin_transaction();

                try {
                    // Comentamos la eliminación de fotos existentes
                    // $sqlDelete = "DELETE FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
                    // $stmtDelete = $this->conectar->prepare($sqlDelete);
                    // if ($stmtDelete === false) {
                    //     throw new Exception("Error preparando la consulta DELETE: " . $this->conectar->error);
                    // }
                    // $stmtDelete->bind_param("i", $idCoti);
                    // $stmtDelete->execute();

                    // Verificar si la columna equipo_index existe
                    $columnExists = $this->checkIfColumnExists('taller_cotizaciones_fotos', 'equipo_index');

                    foreach ($uploadedFiles as $file) {
                        if ($columnExists) {
                            $sqlInsert = "INSERT INTO taller_cotizaciones_fotos (id_cotizacion, nombre_foto, equipo_index) VALUES (?, ?, ?)";
                            $stmtInsert = $this->conectar->prepare($sqlInsert);
                            if ($stmtInsert === false) {
                                throw new Exception("Error preparando la consulta INSERT: " . $this->conectar->error);
                            }

                            if (!$stmtInsert->bind_param("isi", $idCoti, $file['nombre'], $file['equipo_index'])) {
                                throw new Exception("Error al vincular parámetros: " . $stmtInsert->error);
                            }
                        } else {
                            $sqlInsert = "INSERT INTO taller_cotizaciones_fotos (id_cotizacion, nombre_foto) VALUES (?, ?)";
                            $stmtInsert = $this->conectar->prepare($sqlInsert);
                            if ($stmtInsert === false) {
                                throw new Exception("Error preparando la consulta INSERT: " . $this->conectar->error);
                            }

                            if (!$stmtInsert->bind_param("is", $idCoti, $file['nombre'])) {
                                throw new Exception("Error al vincular parámetros: " . $stmtInsert->error);
                            }
                        }

                        if (!$stmtInsert->execute()) {
                            throw new Exception("Error al ejecutar la inserción: " . $stmtInsert->error);
                        }

                        $stmtInsert->close();
                    }

                    $this->conectar->commit();
                    error_log("Fotos guardadas correctamente para la cotización ID: " . $idCoti);
                } catch (Exception $e) {
                    $this->conectar->rollback();
                    foreach ($uploadedFiles as $file) {
                        $filePath = $uploadDir . $file['nombre'];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    throw $e;
                }
            }

            if (!empty($errors)) {
                error_log("Errores al procesar fotos: " . implode(", ", $errors));
            }

        } catch (Exception $e) {
            error_log("Error en manejarFotosAsync: " . $e->getMessage());
            throw $e;
        }
    }
    private function checkIfColumnExists($table, $column)
    {
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
        $result = $this->conectar->query($sql);
        return $result->num_rows > 0;
    }


    private function actualizarEstadoPreAlerta($preAlertaId)
    {
        $sqlUpdatePreAlerta = "UPDATE pre_alerta SET tiene_cotizacion = 1 WHERE id_preAlerta = ?";
        $stmtUpdatePreAlerta = $this->conectar->prepare($sqlUpdatePreAlerta);
        if ($stmtUpdatePreAlerta === false) {
            throw new Exception("Error al preparar la actualización de pre-alerta: " . $this->conectar->error);
        }
        $stmtUpdatePreAlerta->bind_param("i", $preAlertaId);
        if (!$stmtUpdatePreAlerta->execute()) {
            throw new Exception("Error al actualizar el estado de la pre-alerta: " . $stmtUpdatePreAlerta->error);
        }
        error_log("Pre-alerta actualizada correctamente: " . $preAlertaId);
    }
    public function guardarFotos()
    {
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['id_cotizacion'])) {
                throw new Exception('No se recibió ID de cotización');
            }

            $id_cotizacion = filter_var($_POST['id_cotizacion'], FILTER_SANITIZE_NUMBER_INT);

            // Validar que la cotización existe
            $stmt = $this->conectar->prepare("SELECT id_cotizacion FROM taller_cotizaciones WHERE id_cotizacion = ?");
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            if (!$stmt->get_result()->fetch_assoc()) {
                throw new Exception('La cotización no existe');
            }

            $uploadDir = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/';

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception('No se pudo crear el directorio de uploads');
                }
            }

            if (!is_writable($uploadDir)) {
                throw new Exception('El directorio de uploads no tiene permisos de escritura');
            }

            if (empty($_FILES['images'])) {
                throw new Exception('No se recibieron archivos');
            }

            $uploadedFiles = [];
            $fileNames = [];

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $tmp_name);
                finfo_close($finfo);

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($mime_type, $allowed_types)) {
                    continue;
                }

                $fileName = uniqid('img_') . '_' . basename($_FILES['images']['name'][$key]);
                $targetFilePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $uploadedFiles[] = $targetFilePath;
                    $fileNames[] = $fileName;
                }
            }

            if (empty($fileNames)) {
                throw new Exception('No se pudo guardar ningún archivo');
            }

            // Obtener fotos existentes
            $stmt = $this->conectar->prepare("SELECT nombre_foto FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?");
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $existingPhotos = [];
            if ($row && $row['nombre_foto']) {
                $existingPhotos = json_decode($row['nombre_foto'], true) ?: [];
            }

            // Combinar fotos existentes con nuevas
            $allPhotos = array_merge($existingPhotos, $fileNames);
            $fotosJson = json_encode($allPhotos);

            // Actualizar o insertar el registro
            if ($row) {
                $stmt = $this->conectar->prepare("UPDATE taller_cotizaciones_fotos SET nombre_foto = ? WHERE id_cotizacion = ?");
                $stmt->bind_param("si", $fotosJson, $id_cotizacion);
            } else {
                $stmt = $this->conectar->prepare("INSERT INTO taller_cotizaciones_fotos (id_cotizacion, nombre_foto) VALUES (?, ?)");
                $stmt->bind_param("is", $id_cotizacion, $fotosJson);
            }

            if (!$stmt->execute()) {
                // Si hay error, eliminar las imágenes subidas
                foreach ($uploadedFiles as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
                throw new Exception('Error al guardar en la base de datos');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Fotos guardadas correctamente',
                'files' => $allPhotos
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFotos($id_cotizacion)
    {
        header('Content-Type: application/json');

        try {
            $id_cotizacion = filter_var($id_cotizacion, FILTER_SANITIZE_NUMBER_INT);

            $sql = "SELECT nombre_foto FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();

            $row = $result->fetch_assoc();
            $fotos = $row ? json_decode($row['nombre_foto'], true) : [];

            echo json_encode([
                'success' => true,
                'fotos' => $fotos
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarFoto()
    {
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['id_cotizacion']) || !isset($_POST['nombre_foto'])) {
                throw new Exception('Faltan parámetros requeridos');
            }

            $id_cotizacion = filter_var($_POST['id_cotizacion'], FILTER_SANITIZE_NUMBER_INT);
            $nombre_foto = filter_var($_POST['nombre_foto'], FILTER_SANITIZE_STRING);
            $equipo_index = isset($_POST['equipo_index']) ? filter_var($_POST['equipo_index'], FILTER_SANITIZE_NUMBER_INT) : null;

            // 1. Eliminar el archivo físico
            $ruta_foto = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/' . $nombre_foto;
            if (file_exists($ruta_foto)) {
                if (!unlink($ruta_foto)) {
                    throw new Exception('No se pudo eliminar el archivo físico');
                }
            }

            // 2. Eliminar de la base de datos
            $sql = "DELETE FROM taller_cotizaciones_fotos 
                    WHERE id_cotizacion = ? 
                    AND nombre_foto = ?";

            if ($equipo_index !== null) {
                $sql .= " AND equipo_index = ?";
            }

            $stmt = $this->conectar->prepare($sql);

            if ($equipo_index !== null) {
                $stmt->bind_param("isi", $id_cotizacion, $nombre_foto, $equipo_index);
            } else {
                $stmt->bind_param("is", $id_cotizacion, $nombre_foto);
            }

            if (!$stmt->execute()) {
                throw new Exception('Error al eliminar el registro de la base de datos');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Foto eliminada correctamente'
            ]);

        } catch (Exception $e) {
            error_log("Error al eliminar foto: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarCotizacion()
    {
        try {
            $id_cotizacion = $_POST['cod'];

            $this->conectar->begin_transaction();

            // Primero obtener los nombres de las fotos antes de eliminarlas de la BD
            $sqlFotos = "SELECT nombre_foto FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
            $stmtFotos = $this->conectar->prepare($sqlFotos);
            $stmtFotos->bind_param("i", $id_cotizacion);
            $stmtFotos->execute();
            $resultFotos = $stmtFotos->get_result();

            // Almacenar los nombres de las fotos
            $fotos = [];
            while ($row = $resultFotos->fetch_assoc()) {
                if ($row['nombre_foto']) {
                    $fotos[] = $row['nombre_foto'];
                }
            }

            // Delete related records first (due to foreign key constraints)
            // Delete from taller_cotizaciones_fotos
            $sql = "DELETE FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Delete from taller_repuestos_cotis
            $sql = "DELETE FROM taller_repuestos_cotis WHERE id_coti = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Delete from cuotas_cotizacion
            $sql = "DELETE FROM cuotas_cotizacion WHERE id_coti = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Finally, delete the main record from taller_cotizaciones
            $sql = "DELETE FROM taller_cotizaciones WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Si todo fue exitoso, eliminar los archivos físicos
            $errores = [];
            foreach ($fotos as $foto) {
                $rutaFoto = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/' . $foto;
                if (file_exists($rutaFoto)) {
                    if (!unlink($rutaFoto)) {
                        $errores[] = "No se pudo eliminar el archivo: " . $foto;
                    }
                }
            }

            $this->conectar->commit();

            if (empty($errores)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cotización y archivos eliminados correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cotización eliminada pero con advertencias',
                    'warnings' => $errores
                ]);
            }

        } catch (Exception $e) {
            $this->conectar->rollback();
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la cotización: ' . $e->getMessage()
            ]);
        }
    }
    // En PreAlertaController.php
    public function actualizarEstadoCotizacion($id_preAlerta)
    {
        try {
            // Verificar que el ID de pre-alerta existe
            $sqlCheck = "SELECT id_preAlerta FROM pre_alerta WHERE id_preAlerta = ?";
            $stmtCheck = $this->conectar->prepare($sqlCheck);

            if ($stmtCheck === false) {
                throw new Exception("Error al preparar la consulta de verificación: " . $this->conectar->error);
            }

            $stmtCheck->bind_param("i", $id_preAlerta);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Pre-alerta no encontrada");
            }

            // Actualizar el estado de la pre-alerta
            $sqlUpdate = "UPDATE pre_alerta SET tiene_cotizacion = 1 WHERE id_preAlerta = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);

            if ($stmtUpdate === false) {
                throw new Exception("Error al preparar la consulta de actualización: " . $this->conectar->error);
            }

            $stmtUpdate->bind_param("i", $id_preAlerta);

            if (!$stmtUpdate->execute()) {
                throw new Exception("Error al actualizar el estado: " . $stmtUpdate->error);
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en actualizarEstadoCotizacion: " . $e->getMessage());
            return false;
        }
    }


    public function obtenerDetalleCotizacion()
    {
        try {
            // Verificar que recibimos el ID
            if (!isset($_POST['id'])) {
                throw new Exception("ID de cotización no proporcionado");
            }

            $id_cotizacion = intval($_POST['id']);

            // Agregar log para debug
            error_log("Buscando cotización con ID: " . $id_cotizacion);

            // Consulta principal con JOIN a clientes_taller
            $sql = "SELECT 
                tc.*,
                ct.documento as num_doc,
                ct.datos as nom_cli,
                ct.direccion as dir_cli,
                ct.atencion as dir2_cli
                FROM taller_cotizaciones tc
                INNER JOIN clientes_taller ct ON tc.id_cliente_taller = ct.id_cliente_taller
                WHERE tc.id_cotizacion = ?";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $id_cotizacion);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $cotizacion = $result->fetch_assoc();

            if (!$cotizacion) {
                error_log("No se encontró la cotización con ID: " . $id_cotizacion);
                throw new Exception("Cotización no encontrada");
            }

            // Obtener equipos
            $sqlEquipos = "SELECT * FROM taller_cotizaciones_equipos WHERE id_cotizacion = ?";
            $stmtEquipos = $this->conectar->prepare($sqlEquipos);
            $stmtEquipos->bind_param("i", $id_cotizacion);
            $stmtEquipos->execute();
            $resultEquipos = $stmtEquipos->get_result();
            $equipos = $resultEquipos->fetch_all(MYSQLI_ASSOC);

            // Obtener repuestos
            $sqlRepuestos = "SELECT 
                trc.*,
                r.codigo as codigo_prod,
                r.nombre as descripcion,
                r.precio,
                r.precio2,
                r.precio_unidad
                FROM taller_repuestos_cotis trc
                JOIN repuestos r ON trc.id_repuesto = r.id_repuesto
                WHERE trc.id_coti = ?";

            $stmtRepuestos = $this->conectar->prepare($sqlRepuestos);
            $stmtRepuestos->bind_param("i", $id_cotizacion);
            $stmtRepuestos->execute();
            $resultRepuestos = $stmtRepuestos->get_result();
            $repuestos = $resultRepuestos->fetch_all(MYSQLI_ASSOC);

            // Obtener fotos
            $sqlFotos = "SELECT * FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
            $stmtFotos = $this->conectar->prepare($sqlFotos);
            $stmtFotos->bind_param("i", $id_cotizacion);
            $stmtFotos->execute();
            $resultFotos = $stmtFotos->get_result();
            $fotos = $resultFotos->fetch_all(MYSQLI_ASSOC);
            // Obtener condiciones específicas
            $sqlCondiciones = "SELECT condiciones FROM taller_condiciones_cotizacion WHERE id_cotizacion = ?";
            $stmtCondiciones = $this->conectar->prepare($sqlCondiciones);
            $stmtCondiciones->bind_param("i", $id_cotizacion);
            $stmtCondiciones->execute();
            $resultCondiciones = $stmtCondiciones->get_result();
            $condiciones = $resultCondiciones->fetch_assoc();

            // Obtener diagnóstico específico
            $sqlDiagnostico = "SELECT diagnostico FROM taller_diagnosticos_cotizacion WHERE id_cotizacion = ?";
            $stmtDiagnostico = $this->conectar->prepare($sqlDiagnostico);
            $stmtDiagnostico->bind_param("i", $id_cotizacion);
            $stmtDiagnostico->execute();
            $resultDiagnostico = $stmtDiagnostico->get_result();
            $diagnostico = $resultDiagnostico->fetch_assoc();

            // Obtener observaciones
            $sqlObservaciones = "SELECT observaciones FROM taller_observaciones_cotizacion WHERE id_cotizacion = ?";
            $stmtObservaciones = $this->conectar->prepare($sqlObservaciones);
            $stmtObservaciones->bind_param("i", $id_cotizacion);
            $stmtObservaciones->execute();
            $resultObservaciones = $stmtObservaciones->get_result();
            $observaciones = $resultObservaciones->fetch_assoc();

        
            $response = [
                'res' => true,
                'data' => [
                    'id_cotizacion' => $cotizacion['id_cotizacion'],
                    'tipo_doc' => $cotizacion['id_tido'],
                    'moneda' => $cotizacion['moneda'],
                    'tc' => $cotizacion['cm_tc'],
                    'tipo_pago' => $cotizacion['id_tipo_pago'],
                    'fecha' => $cotizacion['fecha'],
                    'dias_pago' => $cotizacion['dias_pagos'],
                    'dir_pos' => $cotizacion['direccion'],
                    'num_doc' => $cotizacion['num_doc'],
                    'nom_cli' => $cotizacion['nom_cli'],
                    'dir_cli' => $cotizacion['dir_cli'],
                    'dir2_cli' => $cotizacion['dir2_cli'],
                    'total' => $cotizacion['total'],
                    'usar_precio' => $cotizacion['usar_precio'],
                    'equipos' => $equipos,
                    'condiciones' => $condiciones ? $condiciones['condiciones'] : null,
                    'diagnostico' => $diagnostico ? $diagnostico['diagnostico'] : null,
                    'observaciones' => $observaciones ? $observaciones['observaciones'] : null,
                    'productos' => array_map(function ($repuesto) {
                        return [
                            'productoid' => $repuesto['id_repuesto'],
                            'codigo_prod' => $repuesto['codigo_prod'],
                            'descripcion' => $repuesto['descripcion'],
                            'cantidad' => $repuesto['cantidad'],
                            'precioVenta' => $repuesto['precio'],
                            'costo' => $repuesto['costo'],
                            'id_cotizacion_equipo' => $repuesto['id_cotizacion_equipo'],
                            'editable' => false
                        ];
                    }, $repuestos),
                    'fotos' => $fotos
                ]
            ];

            error_log("Respuesta: " . json_encode($response));
            echo json_encode($response);

        } catch (Exception $e) {
            error_log("Error en obtenerDetalleCotizacion: " . $e->getMessage());
            echo json_encode([
                'res' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // En TallerCotizacionesController.php

    public function obtenerRepuestosCotizacion()
    {
        try {
            $id_prealerta = isset($_POST['id']) ? intval($_POST['id']) : 0;

            if ($id_prealerta <= 0) {
                throw new Exception("ID de pre-alerta inválido");
            }

            error_log("Obteniendo repuestos para pre-alerta ID: " . $id_prealerta);

            $sqlCotizacion = "SELECT id_cotizacion FROM taller_cotizaciones WHERE id_prealerta = ?";
            $stmtCotizacion = $this->conectar->prepare($sqlCotizacion);

            if (!$stmtCotizacion) {
                throw new Exception("Error preparando consulta: " . $this->conectar->error);
            }

            $stmtCotizacion->bind_param("i", $id_prealerta);
            $stmtCotizacion->execute();
            $resultCotizacion = $stmtCotizacion->get_result();
            $cotizacion = $resultCotizacion->fetch_assoc();

            if (!$cotizacion) {
                throw new Exception("No se encontró cotización para esta pre-alerta");
            }

            $id_cotizacion = $cotizacion['id_cotizacion'];
            error_log("ID de cotización encontrado: " . $id_cotizacion);

            $sql = "SELECT 
                trc.*,
                r.codigo as codigo_prod,
                r.nombre as descripcion,
                r.precio,
                r.precio2,
                r.precio_unidad
            FROM taller_repuestos_cotis trc
            JOIN repuestos r ON trc.id_repuesto = r.id_repuesto
            WHERE trc.id_coti = ?";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparando consulta de repuestos: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();

            $repuestos = [];
            while ($row = $result->fetch_assoc()) {
                $repuestos[] = [
                    'productoid' => $row['id_repuesto'],
                    'codigo_prod' => $row['codigo_prod'],
                    'descripcion' => $row['descripcion'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio'],
                    'precio2' => $row['precio2'],
                    'precio_unidad' => $row['precio_unidad'],
                    'precioVenta' => $row['precio'],
                    'costo' => $row['costo']
                ];
            }

            error_log("Número de repuestos encontrados: " . count($repuestos));

            echo json_encode([
                'res' => true,
                'data' => $repuestos,
                'debug' => [
                    'id_prealerta' => $id_prealerta,
                    'id_cotizacion' => $id_cotizacion
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerRepuestosCotizacion: " . $e->getMessage());
            echo json_encode([
                'res' => false,
                'error' => $e->getMessage()
            ]);
        } finally {
            if (isset($stmtCotizacion))
                $stmtCotizacion->close();
            if (isset($stmt))
                $stmt->close();
        }
    }

    public function editar()
    {
        try {
            $this->conectar->begin_transaction();

            $data = $_POST;
            $idCotizacion = isset($data['id_cotizacion']) ? intval($data['id_cotizacion']) : null;

            if (!$idCotizacion) {
                throw new Exception("ID de cotización no proporcionado");
            }

            // Obtener los productos antiguos para restaurar la cantidad
            $sqlOldProducts = "SELECT id_repuesto, cantidad FROM taller_repuestos_cotis WHERE id_coti = ?";
            $stmtOldProducts = $this->conectar->prepare($sqlOldProducts);
            $stmtOldProducts->bind_param("i", $idCotizacion);
            $stmtOldProducts->execute();
            $resultOldProducts = $stmtOldProducts->get_result();
            $oldProducts = $resultOldProducts->fetch_all(MYSQLI_ASSOC);

            // Restaurar la cantidad de los productos antiguos
            $sqlRestoreCantidad = "UPDATE repuestos SET cantidad = cantidad + ? WHERE id_repuesto = ?";
            $stmtRestoreCantidad = $this->conectar->prepare($sqlRestoreCantidad);
            foreach ($oldProducts as $oldProduct) {
                $stmtRestoreCantidad->bind_param("di", $oldProduct['cantidad'], $oldProduct['id_repuesto']);
                $stmtRestoreCantidad->execute();
            }

            // Actualizar la cotización principal
            $sql = "UPDATE taller_cotizaciones SET 
                id_tido = ?, 
                moneda = ?, 
                cm_tc = ?, 
                id_tipo_pago = ?, 
                fecha = ?,
                dias_pagos = ?, 
                direccion = ?, 
                total = ?, 
                usar_precio = ?,
                descuento = ?
                WHERE id_cotizacion = ?";

            $stmt = $this->conectar->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparando la actualización: " . $this->conectar->error);
            }

            $stmt->bind_param(
                "sssssssdsdi",
                $data['tipo_doc'],
                $data['moneda'],
                $data['tc'],
                $data['tipo_pago'],
                $data['fecha'],
                $data['dias_pago'],
                $data['dir_pos'],
                $data['total'],
                $data['usar_precio'],
                $data['descuento'],
                $idCotizacion
            );

            if (!$stmt->execute()) {
                throw new Exception("Error actualizando la cotización: " . $stmt->error);
            }

            // Eliminar repuestos antiguos
            $sqlDeleteRepuestos = "DELETE FROM taller_repuestos_cotis WHERE id_coti = ?";
            $stmtDeleteRepuestos = $this->conectar->prepare($sqlDeleteRepuestos);
            $stmtDeleteRepuestos->bind_param("i", $idCotizacion);
            $stmtDeleteRepuestos->execute();

            // Insertar nuevos repuestos y actualizar cantidades
            if (!empty($data['listaPro'])) {
                $productos = json_decode($data['listaPro'], true);
                $sqlInsertRepuesto = "INSERT INTO taller_repuestos_cotis 
                    (id_coti, id_repuesto, cantidad, precio, costo, id_cotizacion_equipo) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInsertRepuesto = $this->conectar->prepare($sqlInsertRepuesto);

                // Preparar consulta para actualizar cantidad
                $sqlUpdateCantidad = "UPDATE repuestos SET cantidad = cantidad - ? WHERE id_repuesto = ? AND cantidad >= ?";
                $stmtUpdateCantidad = $this->conectar->prepare($sqlUpdateCantidad);

                foreach ($productos as $producto) {
                    // Verificar cantidad disponible
                    $sqlCheckCantidad = "SELECT cantidad FROM repuestos WHERE id_repuesto = ?";
                    $stmtCheckCantidad = $this->conectar->prepare($sqlCheckCantidad);
                    $stmtCheckCantidad->bind_param("i", $producto['productoid']);
                    $stmtCheckCantidad->execute();
                    $resultCantidad = $stmtCheckCantidad->get_result();
                    $cantidadActual = $resultCantidad->fetch_assoc()['cantidad'];

                    if ($cantidadActual < $producto['cantidad']) {
                        throw new Exception("Cantidad insuficiente para el producto: " . $producto['descripcion']);
                    }

                    // Insertar en taller_repuestos_cotis
                    $stmtInsertRepuesto->bind_param(
                        "iidddi",
                        $idCotizacion,
                        $producto['productoid'],
                        $producto['cantidad'],
                        $producto['precioVenta'],
                        $producto['costo'],
                        $producto['id_cotizacion_equipo']
                    );
                    $stmtInsertRepuesto->execute();

                    // Actualizar la cantidad
                    $stmtUpdateCantidad->bind_param(
                        "dii",
                        $producto['cantidad'],
                        $producto['productoid'],
                        $producto['cantidad']
                    );
                    $stmtUpdateCantidad->execute();

                    if ($stmtUpdateCantidad->affected_rows == 0) {
                        throw new Exception("No se pudo actualizar la cantidad del producto: " . $producto['descripcion']);
                    }
                }
            }

            // Manejar las fotos si existen
            if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name'])) {
                $fotosEquipo = isset($_POST['fotos_equipo']) ? $_POST['fotos_equipo'] : [];
                $this->manejarFotosAsync($idCotizacion, $_FILES['fotos'], $fotosEquipo);
            }
            // Inside the editar method, replace the conditions and diagnosis section with this:

            if (isset($data['condiciones']) && !empty(trim($data['condiciones']))) {
                // Primero intentar actualizar
                $sqlCondiciones = "UPDATE taller_condiciones_cotizacion SET condiciones = ? WHERE id_cotizacion = ?";
                $stmtCondiciones = $this->conectar->prepare($sqlCondiciones);
                $stmtCondiciones->bind_param("si", $data['condiciones'], $idCotizacion);
                $stmtCondiciones->execute();

                // Si no se actualizó ninguna fila, entonces insertar
                if ($stmtCondiciones->affected_rows == 0) {
                    $sqlCondiciones = "INSERT INTO taller_condiciones_cotizacion (id_cotizacion, condiciones) VALUES (?, ?)";
                    $stmtCondiciones = $this->conectar->prepare($sqlCondiciones);
                    $stmtCondiciones->bind_param("is", $idCotizacion, $data['condiciones']);
                    $stmtCondiciones->execute();
                }
            }

            // Only update diagnosis if it's explicitly provided and not empty
            if (isset($data['diagnostico']) && !empty(trim($data['diagnostico']))) {
                // Primero intentar actualizar
                $sqlDiagnostico = "UPDATE taller_diagnosticos_cotizacion SET diagnostico = ? WHERE id_cotizacion = ?";
                $stmtDiagnostico = $this->conectar->prepare($sqlDiagnostico);
                $stmtDiagnostico->bind_param("si", $data['diagnostico'], $idCotizacion);
                $stmtDiagnostico->execute();

                // Si no se actualizó ninguna fila, entonces insertar
                if ($stmtDiagnostico->affected_rows == 0) {
                    $sqlDiagnostico = "INSERT INTO taller_diagnosticos_cotizacion (id_cotizacion, diagnostico) VALUES (?, ?)";
                    $stmtDiagnostico = $this->conectar->prepare($sqlDiagnostico);
                    $stmtDiagnostico->bind_param("is", $idCotizacion, $data['diagnostico']);
                    $stmtDiagnostico->execute();
                }
            }

            // Guardar observaciones si se proporcionaron
            if (isset($data['observaciones'])) {
                $sqlObservaciones = "UPDATE taller_observaciones_cotizacion SET observaciones = ? WHERE id_cotizacion = ?";
                $stmtObservaciones = $this->conectar->prepare($sqlObservaciones);
                $stmtObservaciones->bind_param("si", $data['observaciones'], $idCotizacion);
                $stmtObservaciones->execute();

                if ($stmtObservaciones->affected_rows == 0) {
                    $sqlInsertObs = "INSERT INTO taller_observaciones_cotizacion (id_cotizacion, observaciones) VALUES (?, ?)";
                    $stmtInsertObs = $this->conectar->prepare($sqlInsertObs);
                    $stmtInsertObs->bind_param("is", $idCotizacion, $data['observaciones']);
                    $stmtInsertObs->execute();
                }
            }


            // Obtener el número de cotización de la base de datos
            $sqlNumero = "SELECT numero FROM taller_cotizaciones WHERE id_cotizacion = ?";
            $stmtNumero = $this->conectar->prepare($sqlNumero);
            $stmtNumero->bind_param("i", $idCotizacion);
            $stmtNumero->execute();
            $resultNumero = $stmtNumero->get_result();
            $rowNumero = $resultNumero->fetch_assoc();
            $numero = $rowNumero['numero'];

            // Confirmar la transacción
            $this->conectar->commit();

            // Enviar una única respuesta JSON con todos los datos necesarios
            echo json_encode([
                'res' => true,
                'cotizacion' => [
                    'numero' => $numero,
                    'cotizacion_id' => $idCotizacion,
                    'pdfUrl' => URL::to('/r/taller/reporte/' . $idCotizacion)
                ],
                'msg' => 'Cotización actualizada correctamente'
            ]);

        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en TallerCotizacionesController::editar: " . $e->getMessage());
            echo json_encode(['res' => false, 'error' => $e->getMessage()]);
        }
    }
    // Método para obtener condiciones específicas de una cotización de taller
    public function getCondicionCotizacionTaller($id_cotizacion = null)
    {
        // Verificar si el ID viene como parte de la URL (parámetro de ruta)
        if (!$id_cotizacion && isset($this->params['id'])) {
            $id_cotizacion = $this->params['id'];
        }
        // Verificar si el ID viene como parámetro GET
        else if (!$id_cotizacion && isset($_GET['id'])) {
            $id_cotizacion = $_GET['id'];
        }

        // Validar que tenemos un ID
        if (!$id_cotizacion) {
            return json_encode(['error' => 'ID de cotización no proporcionado']);
        }

        $respuesta = [];

        // Usar consulta preparada para evitar inyección SQL
        $sql = "SELECT * FROM taller_condiciones_cotizacion WHERE id_cotizacion = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    // Método para guardar condiciones específicas de una cotización de taller
    public function saveCondicionCotizacionTaller()
    {
        // Validar que tenemos los datos necesarios
        if (!isset($_POST['cotizacion_id']) || !isset($_POST['condiciones'])) {
            return json_encode(['success' => false, 'error' => 'Datos incompletos']);
        }

        $id_cotizacion = $_POST['cotizacion_id'];
        $condiciones = $_POST['condiciones'];

        try {
            // Primero intentar actualizar, si existe
            $sqlUpdate = "UPDATE taller_condiciones_cotizacion SET condiciones = ? WHERE id_cotizacion = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $condiciones, $id_cotizacion);
            $stmtUpdate->execute();

            // Si no se actualizó ninguna fila, entonces insertar
            if ($stmtUpdate->affected_rows == 0) {
                $sqlInsert = "INSERT INTO taller_condiciones_cotizacion (id_cotizacion, condiciones) VALUES (?, ?)";
                $stmtInsert = $this->conectar->prepare($sqlInsert);
                $stmtInsert->bind_param("is", $id_cotizacion, $condiciones);
                $stmtInsert->execute();
            }

            return json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error al guardar condiciones de taller: " . $e->getMessage());
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Método para guardar condiciones temporales en sesión
    public function saveCondicionTempTaller()
    {
        if (!isset($_POST['condiciones'])) {
            return json_encode(['success' => false, 'error' => 'No se proporcionaron condiciones']);
        }

        $_SESSION['temp_condiciones_taller'] = $_POST['condiciones'];
        return json_encode(['success' => true]);
    }

    // Método para obtener diagnóstico específico de una cotización de taller
    public function getDiagnosticoCotizacionTaller($id_cotizacion = null)
    {
        // Verificar si el ID viene como parte de la URL (parámetro de ruta)
        if (!$id_cotizacion && isset($this->params['id'])) {
            $id_cotizacion = $this->params['id'];
        }
        // Verificar si el ID viene como parámetro GET
        else if (!$id_cotizacion && isset($_GET['id'])) {
            $id_cotizacion = $_GET['id'];
        }

        // Validar que tenemos un ID
        if (!$id_cotizacion) {
            return json_encode(['error' => 'ID de cotización no proporcionado']);
        }

        $respuesta = [];

        // Usar consulta preparada para evitar inyección SQL
        $sql = "SELECT * FROM taller_diagnosticos_cotizacion WHERE id_cotizacion = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    // Método para guardar diagnóstico específico de una cotización de taller
    public function saveDiagnosticoCotizacionTaller()
    {
        // Validar que tenemos los datos necesarios
        if (!isset($_POST['cotizacion_id']) || !isset($_POST['diagnostico'])) {
            return json_encode(['success' => false, 'error' => 'Datos incompletos']);
        }

        $id_cotizacion = $_POST['cotizacion_id'];
        $diagnostico = $_POST['diagnostico'];

        try {
            // Primero intentar actualizar, si existe
            $sqlUpdate = "UPDATE taller_diagnosticos_cotizacion SET diagnostico = ? WHERE id_cotizacion = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $diagnostico, $id_cotizacion);
            $stmtUpdate->execute();

            // Si no se actualizó ninguna fila, entonces insertar
            if ($stmtUpdate->affected_rows == 0) {
                $sqlInsert = "INSERT INTO taller_diagnosticos_cotizacion (id_cotizacion, diagnostico) VALUES (?, ?)";
                $stmtInsert = $this->conectar->prepare($sqlInsert);
                $stmtInsert->bind_param("is", $id_cotizacion, $diagnostico);
                $stmtInsert->execute();
            }

            return json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error al guardar diagnóstico de taller: " . $e->getMessage());
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Método para guardar diagnóstico temporal en sesión
    public function saveDiagnosticoTempTaller()
    {
        if (!isset($_POST['diagnostico'])) {
            return json_encode(['success' => false, 'error' => 'No se proporcionó diagnóstico']);
        }

        $_SESSION['temp_diagnostico_taller'] = $_POST['diagnostico'];
        return json_encode(['success' => true]);
    }

    // Método para guardar condiciones y diagnóstico desde la sesión al guardar la cotización
    private function guardarCondicionesDiagnosticoTaller($idCoti)
    {
        try {
            // Guardar condiciones si existen en sesión
            if (isset($_SESSION['temp_condiciones_taller'])) {
                $condiciones = $_SESSION['temp_condiciones_taller'];

                // Primero intentar actualizar
                $sqlUpdate = "UPDATE taller_condiciones_cotizacion SET condiciones = ? WHERE id_cotizacion = ?";
                $stmtUpdate = $this->conectar->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $condiciones, $idCoti);
                $stmtUpdate->execute();

                // Si no se actualizó ninguna fila, entonces insertar
                if ($stmtUpdate->affected_rows == 0) {
                    $sqlInsert = "INSERT INTO taller_condiciones_cotizacion (id_cotizacion, condiciones) VALUES (?, ?)";
                    $stmtInsert = $this->conectar->prepare($sqlInsert);
                    $stmtInsert->bind_param("is", $idCoti, $condiciones);
                    $stmtInsert->execute();
                }

                // Limpiar la sesión
                unset($_SESSION['temp_condiciones_taller']);
            }

            // Guardar diagnóstico si existe en sesión
            if (isset($_SESSION['temp_diagnostico_taller'])) {
                $diagnostico = $_SESSION['temp_diagnostico_taller'];

                // Primero intentar actualizar
                $sqlUpdate = "UPDATE taller_diagnosticos_cotizacion SET diagnostico = ? WHERE id_cotizacion = ?";
                $stmtUpdate = $this->conectar->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $diagnostico, $idCoti);
                $stmtUpdate->execute();

                // Si no se actualizó ninguna fila, entonces insertar
                if ($stmtUpdate->affected_rows == 0) {
                    $sqlInsert = "INSERT INTO taller_diagnosticos_cotizacion (id_cotizacion, diagnostico) VALUES (?, ?)";
                    $stmtInsert = $this->conectar->prepare($sqlInsert);
                    $stmtInsert->bind_param("is", $idCoti, $diagnostico);
                    $stmtInsert->execute();
                }

                // Limpiar la sesión
                unset($_SESSION['temp_diagnostico_taller']);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al guardar condiciones/diagnóstico de taller: " . $e->getMessage());
            return false;
        }
    }
    private function verificarPermisos()
    {
        // Verificar si el usuario tiene sesión
        if (!isset($_SESSION['usuario_fac'])) {
            return [
                'puedeVerPrecios' => false,
                'puedeEditar' => false,
                'puedeEliminar' => false
            ];
        }

        // Por defecto, asumimos que tiene permisos
        $permisos = [
            'puedeVerPrecios' => true,
            'puedeEditar' => true,
            'puedeEliminar' => true
        ];

        // Verificar permisos específicos según el rol
        if (isset($_SESSION['id_rol'])) {
            $rolId = $_SESSION['id_rol'];

            // Si es administrador (rol_id = 1), siempre tiene todos los permisos
            if ($rolId == 1) {
                return $permisos;
            }

            // Verificar si es rol orden trabajo
            $sqlRol = "SELECT nombre FROM roles WHERE rol_id = ?";
            $stmtRol = $this->conectar->prepare($sqlRol);
            $stmtRol->bind_param("i", $rolId);
            $stmtRol->execute();
            $resultRol = $stmtRol->get_result();
            $esRolOrdenTrabajo = false;
            if ($rowRol = $resultRol->fetch_assoc()) {
                $esRolOrdenTrabajo = (strtoupper($rowRol['nombre']) === 'ORDEN TRABAJO');
            }

            // Si es rol orden trabajo, permitir editar pero no ver precios
            if ($esRolOrdenTrabajo) {
                $permisos['puedeVerPrecios'] = false;
                $permisos['puedeEditar'] = true;
                return $permisos;
            }

            // Para otros roles, verificar permiso para ver precios y eliminar
            $sql = "SELECT ver_precios, puede_eliminar FROM roles WHERE rol_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $rolId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $permisos['puedeVerPrecios'] = (bool) $row['ver_precios'];
                $permisos['puedeEliminar'] = (bool) $row['puede_eliminar'];
            }

            // Verificar permiso para editar órdenes
            $sqlPermisos = "SELECT COUNT(*) as tiene_permiso FROM rol_submodulo rs 
                            INNER JOIN submodulos s ON rs.submodulo_id = s.submodulo_id 
                            WHERE rs.rol_id = ? AND s.nombre IN ('ORDEN DE SERVICIO', 'ORDEN DE TRABAJO')";
            $stmtPermisos = $this->conectar->prepare($sqlPermisos);
            $stmtPermisos->bind_param("i", $rolId);
            $stmtPermisos->execute();
            $resultPermisos = $stmtPermisos->get_result();
            if ($rowPermisos = $resultPermisos->fetch_assoc()) {
                $permisos['puedeEditar'] = $rowPermisos['tiene_permiso'] > 0;
            }
        }

        return $permisos;
    }
    public function getObservaciones()
    {
        try {
            if (!isset($_POST['id_cotizacion'])) {
                throw new Exception('ID de cotización no proporcionado');
            }

            $id_cotizacion = intval($_POST['id_cotizacion']);

            $sql = "SELECT observaciones FROM taller_observaciones_cotizacion WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            echo json_encode([
                'res' => true,
                'observaciones' => $row ? $row['observaciones'] : ''
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'res' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function saveObservaciones()
    {
        try {
            if (!isset($_POST['id_cotizacion']) || !isset($_POST['observaciones'])) {
                throw new Exception('Datos incompletos');
            }
    
            $id_cotizacion = $_POST['id_cotizacion'];
            $observaciones = $_POST['observaciones'];
            
            // Verificar si ya existe una entrada para esta cotización
            $sql = "SELECT id_observacion FROM taller_observaciones_cotizacion WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Actualizar observación existente
                $row = $result->fetch_assoc();
                $id_observacion = $row['id_observacion'];
                
                $sqlUpdate = "UPDATE taller_observaciones_cotizacion SET observaciones = ?, fecha_actualizacion = NOW() WHERE id_observacion = ?";
                $stmtUpdate = $this->conectar->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $observaciones, $id_observacion);
                $stmtUpdate->execute();
            } else {
                // Insertar nueva observación
                $sqlInsert = "INSERT INTO taller_observaciones_cotizacion (id_cotizacion, observaciones, fecha_creacion, fecha_actualizacion) VALUES (?, ?, NOW(), NOW())";
                $stmtInsert = $this->conectar->prepare($sqlInsert);
                $stmtInsert->bind_param("is", $id_cotizacion, $observaciones);
                $stmtInsert->execute();
            }
            
            echo json_encode(['res' => true]);
    
        } catch (Exception $e) {
            error_log("Error en saveObservaciones: " . $e->getMessage());
            echo json_encode([
                'res' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


public function saveObservacionesTempTaller()
{
    if (!isset($_POST['observaciones'])) {
        return json_encode(['success' => false, 'error' => 'No se proporcionaron observaciones']);
    }

    $_SESSION['temp_observaciones_taller'] = $_POST['observaciones'];
    return json_encode(['success' => true]);
}

    // Modificar el método agregar() para incluir las observaciones
    private function guardarObservacionesTaller($idCoti)
{
    try {
        // Guardar observaciones si existen en sesión
        if (isset($_SESSION['temp_observaciones_taller'])) {
            $observaciones = $_SESSION['temp_observaciones_taller'];

            // Primero intentar actualizar
            $sqlUpdate = "UPDATE taller_observaciones_cotizacion SET observaciones = ? WHERE id_cotizacion = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $observaciones, $idCoti);
            $stmtUpdate->execute();

            // Si no se actualizó ninguna fila, entonces insertar
            if ($stmtUpdate->affected_rows == 0) {
                $sqlInsert = "INSERT INTO taller_observaciones_cotizacion (id_cotizacion, observaciones) VALUES (?, ?)";
                $stmtInsert = $this->conectar->prepare($sqlInsert);
                $stmtInsert->bind_param("is", $idCoti, $observaciones);
                $stmtInsert->execute();
            }

            // Limpiar la sesión
            unset($_SESSION['temp_observaciones_taller']);
        }

        return true;
    } catch (Exception $e) {
        error_log("Error al guardar observaciones de taller: " . $e->getMessage());
        return false;
    }
}
public function verificarCotizacion()
{
    try {
        // Verificar si se recibió el ID de pre-alerta
        if (!isset($_POST['id_prealerta'])) {
            echo json_encode(['success' => false, 'message' => 'ID de pre-alerta no proporcionado']);
            return;
        }

        $idPreAlerta = $_POST['id_prealerta'];
        
        // Consultar si existe una cotización para esta pre-alerta
        $sql = "SELECT id_cotizacion FROM taller_cotizaciones WHERE id_prealerta = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
        }
        
        $stmt->bind_param("i", $idPreAlerta);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['success' => true, 'id_cotizacion' => $row['id_cotizacion']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró cotización para esta pre-alerta']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}


}

