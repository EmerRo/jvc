<?php

class ChoferController extends Controller {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getAll() {
        $sql = "SELECT * FROM guia_choferes ORDER BY nombre ASC";
        $resultado = $this->conectar->query($sql);
        $items = [];

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $items[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $items]);
    }

    public function save() {
        $nombre = $this->conectar->real_escape_string($_POST['nombre']);
        $sql = "INSERT INTO guia_choferes (nombre) VALUES ('$nombre')";

        if ($this->conectar->query($sql)) {
            $id = $this->conectar->insert_id;
            header('Content-Type: application/json');
            echo json_encode([
                'status' => true,
                'message' => 'Chofer guardado correctamente',
                'data' => ['id' => $id, 'nombre' => $nombre]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al guardar el chofer']);
        }
    }

    public function delete() {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $sql = "DELETE FROM guia_choferes WHERE id = '$id'";

        if ($this->conectar->query($sql)) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => true,
                'message' => 'Chofer eliminado correctamente'
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al eliminar el chofer']);
        }
    }
    public function update() {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $nombre = $this->conectar->real_escape_string($_POST['nombre']);
        $sql = "UPDATE guia_choferes SET nombre = '$nombre' WHERE id = '$id'";
    
        if ($this->conectar->query($sql)) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => true,
                'message' => 'Chofer actualizado correctamente',
                'data' => ['id' => $id, 'nombre' => $nombre]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al actualizar el chofer']);
        }
    }
}