<?php

require_once 'app/models/OrdenTrabajo.php';

class OrdenTrabajoController extends Controller
{
    private $ordenTrabajo;
    private $conectar;
  

    public function __construct()
    {
        $this->ordenTrabajo = new OrdenTrabajo();
        $this->conectar = (new Conexion())->getConexion();
    }

    private function actualizarEstadoSeriePreAlerta($numero_serie, $estado = 'en_trabajo')
    {
        $sql = "UPDATE detalle_serie SET estado_prealerta = ? WHERE numero_serie = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ss", $estado, $numero_serie);
        return $stmt->execute();
    }

    public function insertar()
    {
        if (!empty($_POST)) {
            try {
                error_log("=== DEBUGGING ORDEN TRABAJO ===");
                error_log("POST recibido: " . print_r($_POST, true));
                
                $cliente_Rsocial = isset($_POST['cliente_Rsocial']) ?
                    trim(filter_var($_POST['cliente_Rsocial'], FILTER_SANITIZE_STRING)) : null;
                $cliente_Ruc = isset($_POST['num_doc']) ?
                    trim(filter_var($_POST['num_doc'], FILTER_SANITIZE_STRING)) : null;

                $cliente_documento = isset($_POST['cliente_documento']) ?
                    trim(filter_var($_POST['cliente_documento'], FILTER_SANITIZE_STRING)) : $cliente_Ruc;
                
                $direccion = '';
                if (!empty($cliente_Ruc) && strlen($cliente_Ruc) === 11 && substr($cliente_Ruc, 0, 2) === '20') {
                    $direccion = isset($_POST['direccion']) ?
                        trim(filter_var($_POST['direccion'], FILTER_SANITIZE_STRING)) : '';
                }

                $atencion_Encargado = isset($_POST['atencion_Encargado']) ?
                    trim(filter_var($_POST['atencion_Encargado'], FILTER_SANITIZE_STRING)) : null;
                $fecha_ingreso = isset($_POST['fecha_ingreso']) ?
                    trim(filter_var($_POST['fecha_ingreso'], FILTER_SANITIZE_STRING)) : null;

                    $fecha_salida = isset($_POST['fecha_salida']) ?
    trim(filter_var($_POST['fecha_salida'], FILTER_SANITIZE_STRING)) : null;


                $observaciones = isset($_POST['observaciones']) ?
                    trim(filter_var($_POST['observaciones'], FILTER_SANITIZE_STRING)) : null;

                // Procesar equipos
                $equipos = isset($_POST['equipos']) ? $_POST['equipos'] : [];
                error_log("Equipos recibidos: " . print_r($equipos, true));
                
                $equiposCorregidos = [];
                foreach ($equipos as $equipo) {
                    $equiposCorregidos[] = [
                        'marca' => $equipo['marca'],
                        'modelo' => $equipo['modelo'],
                        'equipo' => isset($equipo['tipo']) ? $equipo['tipo'] : $equipo['equipo'],
                        'numero_serie' => isset($equipo['serie']) ? $equipo['serie'] : $equipo['numero_serie']
                    ];
                }
                
                error_log("Equipos corregidos: " . print_r($equiposCorregidos, true));

                if ($cliente_Rsocial && $atencion_Encargado && $fecha_ingreso && $fecha_salida && !empty($equiposCorregidos)) {
                    $this->ordenTrabajo->setCliente_Razon_Social($cliente_Rsocial);
                    $this->ordenTrabajo->setCliente_Ruc($cliente_documento);
                    $this->ordenTrabajo->setDireccion($direccion);
                    $this->ordenTrabajo->setAtencion_Encargado($atencion_Encargado);
                    $this->ordenTrabajo->setFecha_Ingreso($fecha_ingreso);
                    $this->ordenTrabajo->setFecha_Salida($fecha_salida);
                    $this->ordenTrabajo->setObservaciones($observaciones);
                    $this->ordenTrabajo->setDetalles($equiposCorregidos);

                    $save = $this->ordenTrabajo->insertar();
                    if ($save) {
                        // Actualizar el estado de las series a 'en_trabajo'
                        foreach ($equiposCorregidos as $equipo) {
                            if (!empty($equipo['numero_serie'])) {
                                $this->actualizarEstadoSeriePreAlerta($equipo['numero_serie'], 'en_trabajo');
                            }
                        }
                        
                        // Devolver solo el ID para Orden de Trabajo
                        echo $this->ordenTrabajo->idLast();
                        
                    } else {
                        error_log("Error al insertar en la base de datos");
                        echo json_encode("Ocurrió un error al guardar los datos");
                    }
                } else {
                    error_log("Validación fallida - datos faltantes");
                    echo json_encode('Llene el formulario correctamente');
                }
            } catch (Exception $e) {
                error_log("Excepción en insertar: " . $e->getMessage());
                echo json_encode("Error al procesar la solicitud: " . $e->getMessage());
            }
        } else {
            echo json_encode('Error: No se recibieron datos');
        }
    }

    public function editar()
    {
        if (!isset($_POST['id_orden_trabajo'])) {
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        $id = filter_var($_POST['id_orden_trabajo'], FILTER_SANITIZE_NUMBER_INT);
        $datos = [
            'cliente_razon_social' => $_POST['cliente_razon_social'],
            'cliente_ruc' => $_POST['cliente_ruc'],
            'atencion_encargado' => $_POST['atencion_encargado'],
            'fecha_ingreso' => $_POST['fecha_ingreso'],
            'fecha_salida' => $_POST['fecha_salida'],
            'observaciones' => $_POST['observaciones']
        ];
        $equipos = isset($_POST['equipos']) ? $_POST['equipos'] : [];

        $result = $this->ordenTrabajo->actualizar($id, $datos, $equipos);

        if ($result) {
            $updatedData = $this->ordenTrabajo->getOne($id);
            echo json_encode(['success' => true, 'data' => $updatedData]);
        } else {
            echo json_encode(['error' => 'Error al actualizar el registro']);
        }
    }

    public function render()
    {
        header('Content-Type: application/json');

        try {
            $getAll = $this->ordenTrabajo->getAllData();

            if ($getAll === false) {
                throw new Exception("Error al obtener los datos");
            }

            echo json_encode($getAll);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function getOne($id)
    {
        try {
            if (!is_numeric($id)) {
                echo json_encode(['error' => 'ID inválido']);
                return;
            }

            $data = $this->ordenTrabajo->getOne($id);

            if (empty($data)) {
                echo json_encode(['error' => 'No se encontró el registro']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode($data);

        } catch (Exception $e) {
            error_log("Error en getOne: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener los datos']);
        }
    }

    public function borrar()
    {
        $dataId = $_POST["value"];
        $save = $this->ordenTrabajo->delete($dataId);
        if ($save) {
            echo json_encode("nice");
        } else {
            echo json_encode("error");
        }
    }

    public function culminarTrabajo()
    {
        try {
            if (!isset($_POST['id_orden_trabajo'])) {
                throw new Exception('ID no proporcionado');
            }

            $id = filter_var($_POST['id_orden_trabajo'], FILTER_SANITIZE_NUMBER_INT);
            
            $result = $this->ordenTrabajo->culminarTrabajo($id);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Trabajo culminado exitosamente']);
            } else {
                throw new Exception('Error al actualizar el estado');
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function detalles()
    {
        if (!isset($_POST['id'])) {
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

        try {
            $data = $this->ordenTrabajo->getOne($id);

            if ($data) {
                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'No se encontró la orden de trabajo']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['error' => 'Error al obtener los detalles de la orden de trabajo']);
        }
    }

}