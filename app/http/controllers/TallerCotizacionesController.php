<?php

require_once 'app/models/TallerCotizacion.php';
require_once 'app/models/TallerEquipo.php';
require_once 'app/models/TallerRepuesto.php';
require_once 'app/models/TallerCliente.php';
require_once 'app/models/TallerCuota.php';
require_once 'app/models/TallerFoto.php';

class TallerCotizacionesController extends Controller
{
    private $conectar;
    protected $request;
    private $tallerCotizacion;
    private $tallerEquipo;
    private $tallerRepuesto;
    private $tallerCliente;
    private $tallerCuota;
    private $tallerFoto;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
        $this->tallerCotizacion = new TallerCotizacion();
        $this->tallerEquipo = new TallerEquipo();
        $this->tallerRepuesto = new TallerRepuesto();
        $this->tallerCliente = new TallerCliente();
        $this->tallerCuota = new TallerCuota();
        $this->tallerFoto = new TallerFoto();
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function obtenerInfoPreAlerta()
    {
        try {
            $ordenId = $_POST['id'];
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : (isset($_GET['tipo']) ? $_GET['tipo'] : '');

            error_log("Obteniendo información para ID: " . $ordenId . " y tipo: " . $tipo);

            $data = $this->tallerCotizacion->obtenerInfoOrden($ordenId, $tipo);

            if (!$data) {
                error_log("No se encontró registro con ID: " . $ordenId . " y tipo: " . $tipo);
                echo json_encode([
                    'res' => false,
                    'error' => 'Registro no encontrado'
                ]);
                return;
            }

            $response = [
                'res' => true,
                'data' => $data
            ];

            error_log("Enviando respuesta: " . json_encode($response));
            echo json_encode($response);

        } catch (Exception $e) {
            error_log("Error en obtenerInfoPreAlerta: " . $e->getMessage());
            echo json_encode([
                'res' => false,
                'error' => 'Error al procesar la información: ' . $e->getMessage()
            ]);
        }
    }

    public function agregar()
    {
        $respuesta = ["res" => false];

        try {
            // Verificar permisos
            // $permisos = $this->verificarPermisos();

            // if (!$permisos['puedeEditar']) {
            //     throw new Exception("No tiene permisos para crear o editar cotizaciones");
            // }

            $this->conectar->begin_transaction();

            $data = $_POST;
            error_log("Datos POST recibidos: " . print_r($data, true));

            // Obtener ID de orden y tipo
            $ordenId = isset($data['id_prealerta']) ? intval($data['id_prealerta']) : (isset($_GET['id']) ? intval($_GET['id']) : null);
            $tipoOrigen = isset($data['tipo_origen']) ? $data['tipo_origen'] : (isset($_GET['tipo']) ? $_GET['tipo'] : '');
            
            error_log("ID de orden capturado: " . ($ordenId ?? 'null'));
            error_log("Tipo de origen: " . $tipoOrigen);

            // Gestionar cliente
            $idCli = $this->tallerCliente->gestionar($data);

            // Obtener siguiente número de cotización
            $numCoti = $this->tallerCotizacion->obtenerSiguienteNumero();

            // Obtener el descuento
            $descuento = isset($data['descuento']) ? floatval($data['descuento']) : 0;

            // Insertar cotización
            $idCoti = $this->tallerCotizacion->crear($data, $idCli, $numCoti, $ordenId, $descuento, $tipoOrigen);

            if ($idCoti) {
                // Insertar equipos
                $equipos = json_decode($data['equipos'], true);
                $this->tallerEquipo->insertar($idCoti, $equipos);

                // Insertar cuotas si existen
                $this->tallerCuota->insertar($idCoti, $data);

                // Insertar repuestos
                if (!empty($data['listaPro'])) {
                    $productos = json_decode($data['listaPro'], true);
                    $equiposIds = $this->tallerEquipo->obtenerIds($idCoti);
                    $this->tallerRepuesto->insertar($idCoti, $productos, $equiposIds);
                }

                // Manejar las fotos
                if (isset($_FILES['fotos']) && is_array($_FILES['fotos'])) {
                    $fotosEquipo = isset($_POST['fotos_equipo']) ? $_POST['fotos_equipo'] : [];
                    $this->tallerFoto->manejar($idCoti, $_FILES['fotos'], $fotosEquipo);
                }

                // Guardar condiciones y diagnóstico si existen en sesión
                $this->guardarCondicionesDiagnosticoTaller($idCoti);
                $this->guardarObservacionesTaller($idCoti);
                
                // Actualizar el estado según el tipo
                if ($ordenId && $tipoOrigen) {
                    $this->tallerCotizacion->actualizarEstadoOrden($ordenId, $tipoOrigen);
                }

                $this->conectar->commit();
                
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
                    'id_prealerta' => $ordenId
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

    // MÉTODOS CORREGIDOS PARA TÉRMINOS Y CONDICIONES
    public function getTerminosRepuestos()
    {
        header('Content-Type: application/json');
        
        try {
            $respuesta = [];
            $sql = "SELECT id, nombre, activo, fecha_creacion FROM terminos_repuestos WHERE activo = 1 ORDER BY fecha_creacion DESC";
            $resultado = $this->conectar->query($sql);
            
            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta[] = $row;
                }
            } else {
                error_log("No se encontraron términos en la base de datos");
            }
            
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en getTerminosRepuestos: " . $e->getMessage());
            echo json_encode(['error' => 'Error al obtener términos'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getDiagnosticoRepuestos()
    {
        header('Content-Type: application/json');
        
        try {
            $respuesta = [];
            $sql = "SELECT id, nombre, activo, fecha_creacion FROM diagnostico_repuestos WHERE activo = 1 ORDER BY fecha_creacion DESC";
            $resultado = $this->conectar->query($sql);
            
            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta[] = $row;
                }
            } else {
                error_log("No se encontraron diagnósticos en la base de datos");
            }
            
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en getDiagnosticoRepuestos: " . $e->getMessage());
            echo json_encode(['error' => 'Error al obtener diagnósticos'], JSON_UNESCAPED_UNICODE);
        }
    }

    // Métodos para condiciones específicas por cotización
    public function getCondicionCotizacionTaller($id_cotizacion = null)
    {
        header('Content-Type: application/json');
        
        if (!$id_cotizacion && isset($this->params['id'])) {
            $id_cotizacion = $this->params['id'];
        } else if (!$id_cotizacion && isset($_GET['id'])) {
            $id_cotizacion = $_GET['id'];
        }

        if (!$id_cotizacion) {
            echo json_encode(['error' => 'ID de cotización no proporcionado']);
            return;
        }

        $respuesta = [];
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

        echo json_encode($respuesta);
    }

    public function saveCondicionCotizacionTaller()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['cotizacion_id']) || !isset($_POST['condiciones'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $id_cotizacion = $_POST['cotizacion_id'];
        $condiciones = $_POST['condiciones'];

        try {
            $this->guardarCondiciones($id_cotizacion, $condiciones);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error al guardar condiciones de taller: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function saveCondicionTempTaller()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['condiciones'])) {
            echo json_encode(['success' => false, 'error' => 'No se proporcionaron condiciones']);
            return;
        }

        $_SESSION['temp_condiciones_taller'] = $_POST['condiciones'];
        echo json_encode(['success' => true]);
    }

    // Métodos para condiciones globales
    public function saveCondicionGlobalTaller()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['condiciones']) || !isset($_POST['nombre'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $nombre = $_POST['nombre'];
        $condiciones = $_POST['condiciones'];

        try {
            $sql = "INSERT INTO taller_condiciones_globales (nombre, condiciones) VALUES (?, ?)";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("ss", $nombre, $condiciones);
            $stmt->execute();

            // También actualizar la tabla de términos por defecto
            $sqlUpdate = "UPDATE terminos_repuestos SET nombre = ? WHERE id = 1";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("s", $condiciones);
            $stmtUpdate->execute();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error al guardar condiciones globales: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Métodos para diagnósticos específicos por cotización
    public function getDiagnosticoCotizacionTaller($id_cotizacion = null)
    {
        header('Content-Type: application/json');
        
        if (!$id_cotizacion && isset($this->params['id'])) {
            $id_cotizacion = $this->params['id'];
        } else if (!$id_cotizacion && isset($_GET['id'])) {
            $id_cotizacion = $_GET['id'];
        }

        if (!$id_cotizacion) {
            echo json_encode(['error' => 'ID de cotización no proporcionado']);
            return;
        }

        $respuesta = [];
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

        echo json_encode($respuesta);
    }

    public function saveDiagnosticoCotizacionTaller()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['cotizacion_id']) || !isset($_POST['diagnostico'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $id_cotizacion = $_POST['cotizacion_id'];
        $diagnostico = $_POST['diagnostico'];

        try {
            $this->guardarDiagnostico($id_cotizacion, $diagnostico);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error al guardar diagnóstico de taller: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function saveDiagnosticoTempTaller()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['diagnostico'])) {
            echo json_encode(['success' => false, 'error' => 'No se proporcionó diagnóstico']);
            return;
        }

        $_SESSION['temp_diagnostico_taller'] = $_POST['diagnostico'];
        echo json_encode(['success' => true]);
    }

    // Métodos para diagnósticos globales
    public function saveDiagnosticoGlobalTaller()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['diagnostico']) || !isset($_POST['nombre'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $nombre = $_POST['nombre'];
        $diagnostico = $_POST['diagnostico'];

        try {
            $sql = "INSERT INTO taller_diagnosticos_globales (nombre, diagnostico) VALUES (?, ?)";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("ss", $nombre, $diagnostico);
            $stmt->execute();

            // También actualizar la tabla de diagnósticos por defecto
            $sqlUpdate = "UPDATE diagnostico_repuestos SET nombre = ? WHERE id = 1";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("s", $diagnostico);
            $stmtUpdate->execute();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error al guardar diagnóstico global: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Métodos privados auxiliares
    private function guardarCondiciones($id_cotizacion, $condiciones)
    {
        $sqlUpdate = "UPDATE taller_condiciones_cotizacion SET condiciones = ? WHERE id_cotizacion = ?";
        $stmtUpdate = $this->conectar->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $condiciones, $id_cotizacion);
        $stmtUpdate->execute();

        if ($stmtUpdate->affected_rows == 0) {
            $sqlInsert = "INSERT INTO taller_condiciones_cotizacion (id_cotizacion, condiciones) VALUES (?, ?)";
            $stmtInsert = $this->conectar->prepare($sqlInsert);
            $stmtInsert->bind_param("is", $id_cotizacion, $condiciones);
            $stmtInsert->execute();
        }
    }

    private function guardarDiagnostico($id_cotizacion, $diagnostico)
    {
        $sqlUpdate = "UPDATE taller_diagnosticos_cotizacion SET diagnostico = ? WHERE id_cotizacion = ?";
        $stmtUpdate = $this->conectar->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $diagnostico, $id_cotizacion);
        $stmtUpdate->execute();

        if ($stmtUpdate->affected_rows == 0) {
            $sqlInsert = "INSERT INTO taller_diagnosticos_cotizacion (id_cotizacion, diagnostico) VALUES (?, ?)";
            $stmtInsert = $this->conectar->prepare($sqlInsert);
            $stmtInsert->bind_param("is", $id_cotizacion, $diagnostico);
            $stmtInsert->execute();
        }
    }

    private function guardarObservaciones($id_cotizacion, $observaciones)
    {
        $sql = "SELECT id_observacion FROM taller_observaciones_cotizacion WHERE id_cotizacion = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_observacion = $row['id_observacion'];
            
            $sqlUpdate = "UPDATE taller_observaciones_cotizacion SET observaciones = ?, fecha_actualizacion = NOW() WHERE id_observacion = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $observaciones, $id_observacion);
            $stmtUpdate->execute();
        } else {
            $sqlInsert = "INSERT INTO taller_observaciones_cotizacion (id_cotizacion, observaciones, fecha_creacion, fecha_actualizacion) VALUES (?, ?, NOW(), NOW())";
            $stmtInsert = $this->conectar->prepare($sqlInsert);
            $stmtInsert->bind_param("is", $id_cotizacion, $observaciones);
            $stmtInsert->execute();
        }
    }

    private function guardarCondicionesDiagnosticoTaller($idCoti)
    {
        try {
            if (isset($_SESSION['temp_condiciones_taller'])) {
                $this->guardarCondiciones($idCoti, $_SESSION['temp_condiciones_taller']);
                unset($_SESSION['temp_condiciones_taller']);
            }

            if (isset($_SESSION['temp_diagnostico_taller'])) {
                $this->guardarDiagnostico($idCoti, $_SESSION['temp_diagnostico_taller']);
                unset($_SESSION['temp_diagnostico_taller']);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al guardar condiciones/diagnóstico de taller: " . $e->getMessage());
            return false;
        }
    }

    private function guardarObservacionesTaller($idCoti)
    {
        try {
            if (isset($_SESSION['temp_observaciones_taller'])) {
                $this->guardarObservaciones($idCoti, $_SESSION['temp_observaciones_taller']);
                unset($_SESSION['temp_observaciones_taller']);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al guardar observaciones de taller: " . $e->getMessage());
            return false;
        }
    }

    private function verificarPermisos()
    {
        if (!isset($_SESSION['usuario_fac'])) {
            return [
                'puedeVerPrecios' => false,
                'puedeEditar' => false,
                'puedeEliminar' => false
            ];
        }

        $permisos = [
            'puedeVerPrecios' => true,
            'puedeEditar' => true,
            'puedeEliminar' => true
        ];

        if (isset($_SESSION['id_rol'])) {
            $rolId = $_SESSION['id_rol'];

            if ($rolId == 1) {
                return $permisos;
            }

            $sqlRol = "SELECT nombre FROM roles WHERE rol_id = ?";
            $stmtRol = $this->conectar->prepare($sqlRol);
            $stmtRol->bind_param("i", $rolId);
            $stmtRol->execute();
            $resultRol = $stmtRol->get_result();
            $esRolOrdenTrabajo = false;
            if ($rowRol = $resultRol->fetch_assoc()) {
                $esRolOrdenTrabajo = (strtoupper($rowRol['nombre']) === 'ORDEN TRABAJO');
            }

            if ($esRolOrdenTrabajo) {
                $permisos['puedeVerPrecios'] = false;
                $permisos['puedeEditar'] = true;
                return $permisos;
            }

            $sql = "SELECT ver_precios, puede_eliminar FROM roles WHERE rol_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $rolId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $permisos['puedeVerPrecios'] = (bool) $row['ver_precios'];
                $permisos['puedeEliminar'] = (bool) $row['puede_eliminar'];
            }

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

    // Resto de métodos existentes...
    public function obtenerDetalleCotizacion()
    {
        try {
            if (!isset($_POST['id'])) {
                throw new Exception("ID de cotización no proporcionado");
            }

            $id_cotizacion = intval($_POST['id']);
            error_log("Buscando cotización con ID: " . $id_cotizacion);

            $cotizacion = $this->tallerCotizacion->obtenerDetalle($id_cotizacion);
            $equipos = $this->tallerEquipo->obtenerPorCotizacion($id_cotizacion);
            $repuestos = $this->tallerRepuesto->obtenerPorCotizacion($id_cotizacion);
            $fotos = $this->tallerFoto->obtenerPorCotizacion($id_cotizacion);

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

    public function editar()
    {
        try {
            $this->conectar->begin_transaction();

            $data = $_POST;
            $idCotizacion = isset($data['id_cotizacion']) ? intval($data['id_cotizacion']) : null;

            if (!$idCotizacion) {
                throw new Exception("ID de cotización no proporcionado");
            }

            // Restaurar stock de productos antiguos y eliminarlos
            $this->tallerRepuesto->eliminarPorCotizacion($idCotizacion);

            // Actualizar la cotización principal
            $this->tallerCotizacion->actualizar($idCotizacion, $data);

            // Insertar nuevos repuestos y actualizar cantidades
            if (!empty($data['listaPro'])) {
                $productos = json_decode($data['listaPro'], true);
                $equiposIds = $this->tallerEquipo->obtenerIds($idCotizacion);
                $this->tallerRepuesto->insertar($idCotizacion, $productos, $equiposIds);
            }

            // Manejar las fotos si existen
            if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name'])) {
                $fotosEquipo = isset($_POST['fotos_equipo']) ? $_POST['fotos_equipo'] : [];
                $this->tallerFoto->manejar($idCotizacion, $_FILES['fotos'], $fotosEquipo);
            }

            // Guardar condiciones y diagnóstico si se proporcionaron
            if (isset($data['condiciones']) && !empty(trim($data['condiciones']))) {
                $this->guardarCondiciones($idCotizacion, $data['condiciones']);
            }

            if (isset($data['diagnostico']) && !empty(trim($data['diagnostico']))) {
                $this->guardarDiagnostico($idCotizacion, $data['diagnostico']);
            }

            if (isset($data['observaciones'])) {
                $this->guardarObservaciones($idCotizacion, $data['observaciones']);
            }

            // Obtener el número de cotización de la base de datos
            $sqlNumero = "SELECT numero FROM taller_cotizaciones WHERE id_cotizacion = ?";
            $stmtNumero = $this->conectar->prepare($sqlNumero);
            $stmtNumero->bind_param("i", $idCotizacion);
            $stmtNumero->execute();
            $resultNumero = $stmtNumero->get_result();
            $rowNumero = $resultNumero->fetch_assoc();
            $numero = $rowNumero['numero'];

            $this->conectar->commit();

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

    public function eliminarCotizacion()
    {
        try {
            $id_cotizacion = $_POST['cod'];
            $resultado = $this->tallerCotizacion->eliminar($id_cotizacion);

            if (empty($resultado['warnings'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cotización y archivos eliminados correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cotización eliminada pero con advertencias',
                    'warnings' => $resultado['warnings']
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la cotización: ' . $e->getMessage()
            ]);
        }
    }

    public function verificarCotizacion()
    {
        try {
            if (!isset($_POST['id_prealerta'])) {
                echo json_encode(['success' => false, 'message' => 'ID de orden no proporcionado']);
                return;
            }

            $idOrden = $_POST['id_prealerta'];
            $idCotizacion = $this->tallerCotizacion->verificarExistencia($idOrden);
            
            if ($idCotizacion) {
                echo json_encode(['success' => true, 'id_cotizacion' => $idCotizacion]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró cotización para esta orden']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
