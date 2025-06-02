<?php

class LicenciaController extends Controller {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getAll() {
        $sql = "SELECT * FROM guia_licencias ORDER BY numero ASC";
        $resultado = $this->conectar->query($sql);
        $items = [];

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $items[] = $row;
            }
        }

        return json_encode(['status' => true, 'data' => $items]);
    }

    public function save() {
        $numero = $this->conectar->real_escape_string($_POST['numero']);
        $sql = "INSERT INTO guia_licencias (numero) VALUES ('$numero')";

        if ($this->conectar->query($sql)) {
            $id = $this->conectar->insert_id;
            return json_encode([
                'status' => true,
                'message' => 'Licencia guardada correctamente',
                'data' => ['id' => $id, 'numero' => $numero]
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al guardar la licencia']);
    }

    public function delete() {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $sql = "DELETE FROM guia_licencias WHERE id = '$id'";

        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Licencia eliminada correctamente'
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al eliminar la licencia']);
    }
    public function update() {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $numero = $this->conectar->real_escape_string($_POST['numero']);
        $sql = "UPDATE guia_licencias SET numero = '$numero' WHERE id = '$id'";
    
        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Licencia actualizada correctamente',
                'data' => ['id' => $id, 'numero' => $numero]
            ]);
        }
    
        return json_encode(['status' => false, 'message' => 'Error al actualizar la licencia']);
    }
}