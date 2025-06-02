<?php

class VehiculoController extends Controller {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getAll() {
        $sql = "SELECT * FROM guia_vehiculos ORDER BY placa ASC";
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
        $placa = $this->conectar->real_escape_string($_POST['placa']);
        $sql = "INSERT INTO guia_vehiculos (placa) VALUES ('$placa')";

        if ($this->conectar->query($sql)) {
            $id = $this->conectar->insert_id;
            return json_encode([
                'status' => true,
                'message' => 'Vehículo guardado correctamente',
                'data' => ['id' => $id, 'placa' => $placa]
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al guardar el vehículo']);
    }

    public function delete() {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $sql = "DELETE FROM guia_vehiculos WHERE id = '$id'";

        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Vehículo eliminado correctamente'
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al eliminar el vehículo']);
    }
    public function update() {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $placa = $this->conectar->real_escape_string($_POST['placa']);
        $sql = "UPDATE guia_vehiculos SET placa = '$placa' WHERE id = '$id'";
    
        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Vehículo actualizado correctamente',
                'data' => ['id' => $id, 'placa' => $placa]
            ]);
        }
    
        return json_encode(['status' => false, 'message' => 'Error al actualizar el vehículo']);
    }
}