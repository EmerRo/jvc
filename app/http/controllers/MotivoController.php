<?php

class MotivoController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }
    
    public function getOneMotivo()
    {
        try {
            if (!isset($_POST["id"]) || empty($_POST["id"])) {
                echo json_encode(['status' => false, 'message' => 'ID no proporcionado']);
                return;
            }

            $id = $this->conectar->real_escape_string($_POST["id"]);
            $sql = "SELECT * FROM motivo WHERE id = '$id'";
            $resultado = $this->conectar->query($sql);

            if ($resultado->num_rows > 0) {
                $row = $resultado->fetch_assoc();
                echo json_encode(['status' => true, 'data' => $row]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Motivo no encontrado']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getMotivo()
    {
        try {
            $respuesta = [];
            $sql = "SELECT * FROM motivo ORDER BY nombre";
            $resultado = $this->conectar->query($sql);
            
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta[] = $row;
                }
            }
            
            echo json_encode(['status' => true, 'data' => $respuesta]);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function saveMotivo()
    {
        try {
            if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
                echo json_encode(['status' => false, 'message' => 'El nombre del motivo es requerido']);
                return;
            }

            $nombre = $this->conectar->real_escape_string(trim($_POST['nombre']));
            
            // Verificar si ya existe
            $sqlCheck = "SELECT id FROM motivo WHERE nombre = '$nombre'";
            $resultCheck = $this->conectar->query($sqlCheck);
            
            if ($resultCheck->num_rows > 0) {
                echo json_encode(['status' => false, 'message' => 'Ya existe un motivo con ese nombre']);
                return;
            }

            $sql = "INSERT INTO motivo (nombre) VALUES ('$nombre')";
            
            if ($this->conectar->query($sql)) {
                $id = $this->conectar->insert_id;
                echo json_encode([
                    'status' => true,
                    'message' => 'Motivo guardado correctamente',
                    'data' => ['id' => $id, 'nombre' => $nombre]
                ]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al guardar el motivo: ' . $this->conectar->error]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function updateMotivo()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode(['status' => false, 'message' => 'ID es requerido']);
                return;
            }

            if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
                echo json_encode(['status' => false, 'message' => 'El nombre del motivo es requerido']);
                return;
            }

            $id = $this->conectar->real_escape_string($_POST['id']);
            $nombre = $this->conectar->real_escape_string(trim($_POST['nombre']));
            
            // Verificar si ya existe otro motivo con el mismo nombre
            $sqlCheck = "SELECT id FROM motivo WHERE nombre = '$nombre' AND id != '$id'";
            $resultCheck = $this->conectar->query($sqlCheck);
            
            if ($resultCheck->num_rows > 0) {
                echo json_encode(['status' => false, 'message' => 'Ya existe otro motivo con ese nombre']);
                return;
            }

            $sql = "UPDATE motivo SET nombre='$nombre' WHERE id='$id'";
            
            if ($this->conectar->query($sql)) {
                if ($this->conectar->affected_rows > 0) {
                    echo json_encode([
                        'status' => true,
                        'message' => 'Motivo actualizado correctamente'
                    ]);
                } else {
                    echo json_encode(['status' => false, 'message' => 'No se realizaron cambios o el motivo no existe']);
                }
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al actualizar el motivo: ' . $this->conectar->error]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function deleteMotivo()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode(['status' => false, 'message' => 'ID es requerido']);
                return;
            }

            $id = $this->conectar->real_escape_string($_POST['id']);
            
            // Verificar si el motivo está siendo usado en gestion_activos
            $sqlCheck = "SELECT COUNT(*) as count FROM gestion_activos WHERE motivo = (SELECT nombre FROM motivo WHERE id = '$id')";
            $resultCheck = $this->conectar->query($sqlCheck);
            $row = $resultCheck->fetch_assoc();
            
            if ($row['count'] > 0) {
                echo json_encode(['status' => false, 'message' => 'No se puede eliminar el motivo porque está siendo usado en registros de activos']);
                return;
            }

            $sql = "DELETE FROM motivo WHERE id='$id'";
            
            if ($this->conectar->query($sql)) {
                if ($this->conectar->affected_rows > 0) {
                    echo json_encode([
                        'status' => true,
                        'message' => 'Motivo eliminado correctamente'
                    ]);
                } else {
                    echo json_encode(['status' => false, 'message' => 'El motivo no existe']);
                }
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al eliminar el motivo: ' . $this->conectar->error]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}