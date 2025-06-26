<?php
class ConductorConfiguracionController extends Controller {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

   public function getAll() {
    $sql = "SELECT cc.*, v.marca 
            FROM guia_conductor_configuraciones cc
            LEFT JOIN guia_vehiculos v ON cc.vehiculo_placa = v.placa
            ORDER BY cc.fecha_registro DESC";
    
    $result = $this->conectar->query($sql);
    $configuraciones = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $configuraciones[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => true, 'data' => $configuraciones]);
}


  public function getConfiguracionesPorChofer() {
    if (!isset($_POST['chofer_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'ID de chofer requerido']);
        return;
    }

    $chofer_id = $_POST['chofer_id'];
    $sql = "SELECT cc.*, v.marca 
            FROM guia_conductor_configuraciones cc
            LEFT JOIN guia_vehiculos v ON cc.vehiculo_placa = v.placa
            WHERE cc.chofer_id = ?
            ORDER BY cc.fecha_registro DESC";
    
    $stmt = $this->conectar->prepare($sql);
    $stmt->bind_param("i", $chofer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $configuraciones = [];
    while ($row = $result->fetch_assoc()) {
        $configuraciones[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => true, 'data' => $configuraciones]);
}

    public function save() {
        // Primero, verificar si el chofer ya existe o crearlo
        $chofer_nombre = $this->conectar->real_escape_string($_POST['chofer_nombre']);
        $chofer_dni = $this->conectar->real_escape_string($_POST['chofer_dni']);
        
        // Buscar si el chofer ya existe
        $sql_chofer = "SELECT id FROM guia_choferes WHERE dni = ?";
        $stmt = $this->conectar->prepare($sql_chofer);
        $stmt->bind_param("s", $chofer_dni);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $chofer = $result->fetch_assoc();
            $chofer_id = $chofer['id'];
            
            // Actualizar el nombre si es diferente
            $sql_update = "UPDATE guia_choferes SET nombre = ? WHERE id = ?";
            $stmt_update = $this->conectar->prepare($sql_update);
            $stmt_update->bind_param("si", $chofer_nombre, $chofer_id);
            $stmt_update->execute();
        } else {
            // Crear nuevo chofer
            $sql_insert = "INSERT INTO guia_choferes (nombre, dni) VALUES (?, ?)";
            $stmt_insert = $this->conectar->prepare($sql_insert);
            $stmt_insert->bind_param("ss", $chofer_nombre, $chofer_dni);
            
            if ($stmt_insert->execute()) {
                $chofer_id = $this->conectar->insert_id;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Error al crear chofer']);
                return;
            }
        }

        // Ahora guardar la configuración
        $vehiculo_placa = $this->conectar->real_escape_string($_POST['vehiculo_placa']);
        $vehiculo_marca = $this->conectar->real_escape_string($_POST['vehiculo_marca']);
        $licencia_numero = $this->conectar->real_escape_string($_POST['licencia_numero']);
        
        // Verificar si ya existe esta configuración
        $sql_check = "SELECT id FROM guia_conductor_configuraciones 
                      WHERE chofer_id = ? AND vehiculo_placa = ? AND licencia_numero = ? AND activo = 1";
        $stmt_check = $this->conectar->prepare($sql_check);
        $stmt_check->bind_param("iss", $chofer_id, $vehiculo_placa, $licencia_numero);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Esta configuración ya existe']);
            return;
        }

        // Insertar nueva configuración
        $sql = "INSERT INTO guia_conductor_configuraciones 
                (chofer_id, chofer_nombre, chofer_dni, vehiculo_placa, vehiculo_marca, licencia_numero) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("isssss", $chofer_id, $chofer_nombre, $chofer_dni, $vehiculo_placa, $vehiculo_marca, $licencia_numero);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => true, 'message' => 'Configuración guardada correctamente']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al guardar la configuración']);
        }
    }
public function update() {
    if (!isset($_POST['config_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'ID de configuración requerido']);
        return;
    }

    $config_id = $_POST['config_id'];
    $chofer_nombre = $this->conectar->real_escape_string($_POST['chofer_nombre']);
    $chofer_dni = $this->conectar->real_escape_string($_POST['chofer_dni']);
    $vehiculo_placa = $this->conectar->real_escape_string($_POST['vehiculo_placa']);
    $vehiculo_marca = $this->conectar->real_escape_string($_POST['vehiculo_marca']);
    $licencia_numero = $this->conectar->real_escape_string($_POST['licencia_numero']);

    // Primero actualizar o crear el chofer
    $sql_chofer = "SELECT id FROM guia_choferes WHERE dni = ?";
    $stmt = $this->conectar->prepare($sql_chofer);
    
    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Error en la consulta: ' . $this->conectar->error]);
        return;
    }
    
    $stmt->bind_param("s", $chofer_dni);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $chofer = $result->fetch_assoc();
        $chofer_id = $chofer['id'];
        
        // Actualizar el nombre
        $sql_update = "UPDATE guia_choferes SET nombre = ? WHERE id = ?";
        $stmt_update = $this->conectar->prepare($sql_update);
        
        if (!$stmt_update) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al preparar actualización de chofer: ' . $this->conectar->error]);
            return;
        }
        
        $stmt_update->bind_param("si", $chofer_nombre, $chofer_id);
        $stmt_update->execute();
    } else {
        // Crear nuevo chofer
        $sql_insert = "INSERT INTO guia_choferes (nombre, dni) VALUES (?, ?)";
        $stmt_insert = $this->conectar->prepare($sql_insert);
        
        if (!$stmt_insert) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al preparar inserción de chofer: ' . $this->conectar->error]);
            return;
        }
        
        $stmt_insert->bind_param("ss", $chofer_nombre, $chofer_dni);
        
        if ($stmt_insert->execute()) {
            $chofer_id = $this->conectar->insert_id;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Error al crear chofer: ' . $stmt_insert->error]);
            return;
        }
    }

    // Verificar que no exista otra configuración igual (excluyendo la actual)
    $sql_check = "SELECT id FROM guia_conductor_configuraciones 
                  WHERE chofer_id = ? AND vehiculo_placa = ? AND licencia_numero = ? 
                  AND id != ?";
    $stmt_check = $this->conectar->prepare($sql_check);
    
    if (!$stmt_check) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Error al verificar duplicados: ' . $this->conectar->error]);
        return;
    }
    
    $stmt_check->bind_param("issi", $chofer_id, $vehiculo_placa, $licencia_numero, $config_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Ya existe otra configuración con estos datos']);
        return;
    }

    // Actualizar la configuración
    $sql = "UPDATE guia_conductor_configuraciones 
            SET chofer_id = ?, chofer_nombre = ?, chofer_dni = ?, 
                vehiculo_placa = ?, vehiculo_marca = ?, licencia_numero = ?
            WHERE id = ?";
    
    $stmt_final = $this->conectar->prepare($sql);
    
    if (!$stmt_final) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Error al preparar actualización: ' . $this->conectar->error]);
        return;
    }
    
    $stmt_final->bind_param("isssssi", $chofer_id, $chofer_nombre, $chofer_dni, 
                     $vehiculo_placa, $vehiculo_marca, $licencia_numero, $config_id);
    
    if ($stmt_final->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'message' => 'Configuración actualizada correctamente']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Error al actualizar la configuración: ' . $stmt_final->error]);
    }
}

 

    public function delete() {
    if (!isset($_POST['config_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'ID de configuración requerido']);
        return;
    }

    $config_id = $_POST['config_id'];
    
    // Primero obtenemos el chofer_id antes de eliminar la configuración
    $sql_get_chofer = "SELECT chofer_id FROM guia_conductor_configuraciones WHERE id = ?";
    $stmt_get = $this->conectar->prepare($sql_get_chofer);
    $stmt_get->bind_param("i", $config_id);
    $stmt_get->execute();
    $result_chofer = $stmt_get->get_result();
    
    if ($result_chofer->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Configuración no encontrada']);
        return;
    }
    
    $chofer_data = $result_chofer->fetch_assoc();
    $chofer_id = $chofer_data['chofer_id'];
    
    // Iniciar transacción para asegurar consistencia
    $this->conectar->begin_transaction();
    
    try {
        // Eliminar físicamente la configuración específica
        $sql_delete_config = "DELETE FROM guia_conductor_configuraciones WHERE id = ?";
        $stmt_delete_config = $this->conectar->prepare($sql_delete_config);
        $stmt_delete_config->bind_param("i", $config_id);
        
        if (!$stmt_delete_config->execute()) {
            throw new Exception("Error al eliminar la configuración");
        }
        
        // Verificar si el chofer tiene otras configuraciones
        $sql_check_other = "SELECT COUNT(*) as total FROM guia_conductor_configuraciones WHERE chofer_id = ?";
        $stmt_check = $this->conectar->prepare($sql_check_other);
        $stmt_check->bind_param("i", $chofer_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $count_data = $result_check->fetch_assoc();
        
        // Si no tiene más configuraciones, eliminar también el chofer
        if ($count_data['total'] == 0) {
            $sql_delete_chofer = "DELETE FROM guia_choferes WHERE id = ?";
            $stmt_delete_chofer = $this->conectar->prepare($sql_delete_chofer);
            $stmt_delete_chofer->bind_param("i", $chofer_id);
            
            if (!$stmt_delete_chofer->execute()) {
                throw new Exception("Error al eliminar el chofer");
            }
        }
        
        // Confirmar la transacción
        $this->conectar->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'message' => 'Configuración eliminada correctamente']);
        
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $this->conectar->rollback();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
    }
}

}
?>
