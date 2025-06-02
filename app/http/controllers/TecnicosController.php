<?php

use Mpdf\Utils\Arrays;

require_once "utils/lib/exel/vendor/autoload.php";


class TecnicosController extends Controller
{

private $conectar;

    public function __construct()
    {
       
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getTecnico()
    {
        $respuesta =[];
        $sql = "SELECT * FROM tecnicos";

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
    
    public function getOneTecnico()
    {
        $respuesta = [];
        $sql = "SELECT * FROM tecnicos where id = '{$_POST["id"]}'";

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


    public function saveTecnico()
    {
        $sql = "INSERT INTO tecnicos (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateTecnico()
    {
        $sql = "UPDATE tecnicos SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteTecnico()
    {
        $sql = "DELETE FROM tecnicos WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
