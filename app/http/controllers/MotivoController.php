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
        $respuesta = [];
        $sql = "SELECT * FROM motivo where id = '{$_POST["id"]}'";

        // Ejecutar la consulta
        $resultado = $this->conectar->query($sql);

        // Verificar si la consulta devolvi� resultados
        if ($resultado->num_rows > 0) {
            // Iterar sobre cada fila y agregarla al array de respuesta
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        // Devolver el resultado en formato JSON
        return json_encode($respuesta);
    }


public function getMotivo()
{
    $respuesta = [];
    $sql = "SELECT * FROM motivo ORDER BY nombre";
    $resultado = $this->conectar->query($sql);
    
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $respuesta[] = $row;
        }
    }
    
    // Devolver formato consistente
    return json_encode(['status' => true, 'data' => $respuesta]);
}

public function saveMotivo()
{
    $nombre = $this->conectar->real_escape_string($_POST['nombre']);
    $sql = "INSERT INTO motivo (nombre) VALUES ('$nombre')";
    
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
    $sql = "UPDATE motivo SET nombre='$nombre' WHERE id ='$id'";
    
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
    $sql = "DELETE FROM motivo WHERE id ='$id'";
    
    if ($this->conectar->query($sql)) {
        return json_encode([
            'status' => true,
            'message' => 'Motivo eliminado correctamente'
        ]);
    }
    
    return json_encode(['status' => false, 'message' => 'Error al eliminar el motivo']);
}
}
