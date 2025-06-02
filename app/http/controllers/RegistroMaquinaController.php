<?php

class RegistroMaquinaController extends Controller {
    private $conectar;
    private $consulta;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getMaquinas()
    {
        $respuesta = [];
        $sql = "SELECT m.*, 
                CASE 
                    WHEN m.numero_serie IN (
                        SELECT ga.numero_serie 
                        FROM gestion_activos ga 
                        WHERE ga.estado = 'CONFIRMADO'
                    ) THEN 'NO DISPONIBLE'
                    ELSE m.estado
                END as estado_actual 
                FROM maquina m";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->get_result();
    
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }
    
        return json_encode($respuesta);
    }
    public function getOneMaquina()
    {
        $sql = "SELECT * FROM maquina WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $_POST["id"]);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Retornar solo el primer registro, no un array
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return json_encode($row);
        }
        
        return json_encode(null);
    }

    public function saveMaquina()
{
    // Verificamos si ya existe una máquina con el mismo número de serie
    $sql_check = "SELECT COUNT(*) as count FROM maquina WHERE numero_serie = ?";
    $stmt_check = $this->conectar->prepare($sql_check);
    $stmt_check->bind_param("s", $_POST['numero_serie']);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $count = $result->fetch_assoc()['count'];

    if ($count > 0) {
        return json_encode([
            "status" => "error",
            "message" => "Ya existe una máquina registrada con este número de serie."
        ]);
    }

    // Si no existe, procedemos con la inserción
    $sql = "INSERT INTO maquina (equipo, marca, modelo, numero_serie, estado) VALUES (?, ?, ?, ?, 'DISPONIBLE')";
    $stmt = $this->conectar->prepare($sql);
    $stmt->bind_param("ssss", 
        $_POST['equipo'], 
        $_POST['marca'], 
        $_POST['modelo'], 
        $_POST['numero_serie']
    );
    
    if ($stmt->execute()) {
        return json_encode([
            "status" => "success",
            "message" => "Máquina registrada correctamente."
        ]);
    }
    
    return json_encode([
        "status" => "error",
        "message" => "Error al registrar la máquina: " . $stmt->error
    ]);
}

    public function updateMaquina()
    {
        $sql = "UPDATE maquina SET equipo = ?, marca = ?, modelo = ?, numero_serie = ? WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ssssi", 
            $_POST['equipo'], 
            $_POST['marca'], 
            $_POST['modelo'], 
            $_POST['numero_serie'], 
            $_POST['id']
        );
        
        if ($stmt->execute()) {
            return json_encode([
                "status" => "success",
                "message" => "Máquina actualizada correctamente."
            ]);
        }
        
        return json_encode([
            "status" => "error",
            "message" => "Error al actualizar la máquina."
        ]);
    }

    public function deleteMaquina()
    {
        $sql = "DELETE FROM maquina WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $_POST['id']);
        
        if ($stmt->execute()) {
            return json_encode([
                "status" => "success",
                "message" => "Máquina eliminada correctamente."
            ]);
        }
        
        return json_encode([
            "status" => "error",
            "message" => "Error al eliminar la máquina."
        ]);
    }
    public function gestionActivosSerie($searchTerm) 
    {
        $sql = "SELECT m.*, 
                CASE 
                    WHEN m.numero_serie IN (
                        SELECT ga.numero_serie 
                        FROM gestion_activos ga 
                        WHERE ga.estado = 'CONFIRMADO'
                    ) THEN 'NO DISPONIBLE'
                    ELSE 'DISPONIBLE'
                END as estado
                FROM maquina m 
                WHERE m.numero_serie LIKE ?";
                
        $searchTerm = "%$searchTerm%";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $resultados = array();
        while ($row = $resultado->fetch_assoc()) {
            if ($row['estado'] === 'DISPONIBLE') {
                $resultados[] = $row;
            }
        }
        
        return $resultados;
    }
    public function buscarDataMaquina()
    {
        $searchTerm = filter_input(INPUT_GET, 'term');
        $resultados = $this->gestionActivosSerie($searchTerm);
    
        $array_resultado = array();
        foreach ($resultados as $value) {
            $fila = array();
            $fila['value'] = $value['numero_serie'];
            $fila['label'] = $value['numero_serie'] . ' / ' .
                $value['marca'] . ' / ' .
                $value['equipo'] . ' / ' .
                $value['modelo'];
            $fila['equipo'] = $value['equipo'];
            $fila['marca'] = $value['marca'];
            $fila['modelo'] = $value['modelo'];
            array_push($array_resultado, $fila);
        }
    
        return json_encode($array_resultado);
    }
        
    
}