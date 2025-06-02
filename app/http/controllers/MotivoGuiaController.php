<?php

class MotivoGuiaController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getMotivo()
    {
        $respuesta = [];
        $sql = "SELECT * FROM motivos_guia ORDER BY nombre";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode(['status' => true, 'data' => $respuesta]);
    }
    
    public function getOneMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $sql = "SELECT * FROM motivos_guia WHERE id = '$id'";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return json_encode(['status' => true, 'data' => $row]);
        }

        return json_encode(['status' => false, 'message' => 'Motivo no encontrado']);
    }

    public function saveMotivo()
    {
        $nombre = $this->conectar->real_escape_string($_POST['nombre']);
        $sql = "INSERT INTO motivos_guia (nombre) VALUES ('$nombre')";

        if ($this->conectar->query($sql)) {
            $id = $this->conectar->insert_id;
            return json_encode([
                'status' => true,
                'message' => 'Motivo guardado correctamente',
                'data' => ['id' => $id, 'nombre' => $nombre]
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al guardar el motivo']);
    }

    public function updateMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $nombre = $this->conectar->real_escape_string($_POST['nombre']);
        $sql = "UPDATE motivos_guia SET nombre='$nombre' WHERE id ='$id'";

        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Motivo actualizado correctamente'
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al actualizar el motivo']);
    }

    public function deleteMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $sql = "DELETE FROM motivos_guia WHERE id ='$id'";

        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Motivo eliminado correctamente'
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al eliminar el motivo']);
    }
}