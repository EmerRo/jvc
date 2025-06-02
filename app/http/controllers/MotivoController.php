<?php






class MotivoController extends Controller
{

    private $conectar;

    public function __construct()
    {
     
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getMotivo()
    {
        $respuesta =[];
        $sql = "SELECT * FROM motivo";

        // consulta
        $resultado = $this->conectar->query($sql);
         // Verificar si la consulta devolvi� resultados

         if ($resultado->num_rows > 0) {
            // Iterar sobre los resultados 
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            
            }
         }

         return json_encode($respuesta);

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


    public function saveMotivo()
    {
        $sql = "INSERT INTO motivo (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateMotivo()
    {
        $sql = "UPDATE motivo SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteMotivo()
    {
        $sql = "DELETE FROM motivo WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
