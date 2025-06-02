<?php

use Mpdf\Utils\Arrays;

require_once "utils/lib/exel/vendor/autoload.php";


class EquiposController extends Controller
{

private $conectar;

    public function __construct()
    {
       
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getEquipo()
    {
        $respuesta =[];
        $sql = "SELECT * FROM equipos";

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
    
    public function getOneEquipo()
    {
        $respuesta = [];
        $sql = "SELECT * FROM equipos where id = '{$_POST["id"]}'";

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


    public function saveEquipo()
    {
        $sql = "INSERT INTO equipos (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateEquipo()
    {
        $sql = "UPDATE equipos SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteEquipo()
    {
        $sql = "DELETE FROM equipos WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
