<?php

use Mpdf\Utils\Arrays;

require_once "utils/lib/exel/vendor/autoload.php";


class MarcasController extends Controller
{

private $conectar;

    public function __construct()
    {
       
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getMarca()
    {
        $respuesta =[];
        $sql = "SELECT * FROM marcas";

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
    
    public function getOneMarca()
    {
        $respuesta = [];
        $sql = "SELECT * FROM marcas where id = '{$_POST["id"]}'";

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


    public function saveMarca()
    {
        $sql = "INSERT INTO marcas (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateMarca()
    {
        $sql = "UPDATE marcas SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteMarca()
    {
        $sql = "DELETE FROM marcas WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
