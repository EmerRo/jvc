<?php

require_once 'app/models/OrdenTrabajo.php';
require_once 'app/models/OrdenServicio.php';

class TallerController extends Controller
{
    private $ordenTrabajo;
    private $ordenServicio;
    private $conectar;

    public function __construct()
    {
        $this->ordenTrabajo = new OrdenTrabajo();
        $this->ordenServicio = new OrdenServicio();
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Renderizar vista unificada de órdenes de trabajo y servicio
     */
    public function renderUnificado()
    {
        header('Content-Type: application/json');

        try {
            $sql = "SELECT 
                        id_registro,
                        id_original,
                        origen,
                        cliente_razon_social,
                        cliente_ruc,
                        direccion,
                        atencion_encargado,
                        fecha_ingreso,
                        tiene_cotizacion,
                        estado,
                        observaciones,
                        created_at,
                        updated_at
                    FROM vista_ordenes_unificada
                    ORDER BY fecha_ingreso DESC, created_at DESC";

            $result = $this->conectar->query($sql);
            
            if (!$result) {
                throw new Exception("Error en la consulta: " . $this->conectar->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode($data);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Obtener detalles de una orden específica
     */
    public function detallesUnificado()
    {
        if (!isset($_POST['id']) || !isset($_POST['tipo'])) {
            echo json_encode(['error' => 'ID o tipo no proporcionado']);
            return;
        }

        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $tipo = filter_var($_POST['tipo'], FILTER_SANITIZE_STRING);

        try {
            if ($tipo === 'ORD TRABAJO') {
                $data = $this->ordenTrabajo->getOne($id);
            } elseif ($tipo === 'ORD SERVICIO') {
                $data = $this->ordenServicio->getOne($id);
            } else {
                throw new Exception('Tipo de orden no válido');
            }

            if ($data) {
                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'No se encontró la orden']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['error' => 'Error al obtener los detalles de la orden']);
        }
    }

    /**
     * Culminar trabajo unificado
     */
    public function culminarTrabajoUnificado()
    {
        try {
            if (!isset($_POST['id']) || !isset($_POST['tipo'])) {
                throw new Exception('ID o tipo no proporcionado');
            }

            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $tipo = filter_var($_POST['tipo'], FILTER_SANITIZE_STRING);
            
            if ($tipo === 'ORD TRABAJO') {
                $result = $this->ordenTrabajo->culminarTrabajo($id);
            } elseif ($tipo === 'ORD SERVICIO') {
                $result = $this->ordenServicio->culminarTrabajo($id);
            } else {
                throw new Exception('Tipo de orden no válido');
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Trabajo culminado exitosamente']);
            } else {
                throw new Exception('Error al actualizar el estado');
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Eliminar orden unificado
     */
    public function borrarUnificado()
    {
        try {
            if (!isset($_POST['id']) || !isset($_POST['tipo'])) {
                throw new Exception('ID o tipo no proporcionado');
            }

            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $tipo = filter_var($_POST['tipo'], FILTER_SANITIZE_STRING);
            
            if ($tipo === 'ORD TRABAJO') {
                $result = $this->ordenTrabajo->delete($id);
            } elseif ($tipo === 'ORD SERVICIO') {
                $result = $this->ordenServicio->delete($id);
            } else {
                throw new Exception('Tipo de orden no válido');
            }
            
            if ($result) {
                echo json_encode("nice");
            } else {
                echo json_encode("error");
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar estado de cotización unificado
     */
    public function actualizarEstadoCotizacionUnificado()
    {
        try {
            if (!isset($_POST['id']) || !isset($_POST['tipo'])) {
                throw new Exception('ID o tipo no proporcionado');
            }

            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $tipo = filter_var($_POST['tipo'], FILTER_SANITIZE_STRING);

            if ($tipo === 'ORD TRABAJO') {
                $tabla = 'orden_trabajo_pre';
                $campo_id = 'id_orden_trabajo';
            } elseif ($tipo === 'ORD SERVICIO') {
                $tabla = 'orden_servicio_pre';
                $campo_id = 'id_orden_servicio';
            } else {
                throw new Exception('Tipo de orden no válido');
            }

            $sql = "UPDATE {$tabla} SET tiene_cotizacion = 1 WHERE {$campo_id} = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $stmt->error]);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}