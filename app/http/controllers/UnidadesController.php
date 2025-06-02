<?php

use Mpdf\Utils\Arrays;

require_once "app/models/Cliente.php";
require_once "utils/lib/exel/vendor/autoload.php";


class UnidadesController extends Controller
{

    private $client;
    private $conectar;

    public function __construct()
    {
        $this->client = new Cliente();
        $this->conectar = (new Conexion())->getConexion();
    }

    // unidades para almacen productos

    public function getUnidad()
    {
        $respuesta =[];
        $sql = "SELECT * FROM unidades";

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
    
    public function getOneUnidad()
    {
        $respuesta = [];
        $sql = "SELECT * FROM unidades where id = '{$_POST["id"]}'";

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


    public function saveUnidad()
    {
        $sql = "INSERT INTO unidades (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateUnidad()
    {
        $sql = "UPDATE unidades SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteUnidad()
    {
        $sql = "DELETE FROM unidades WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    // unidades para Repuestos
    public function getUnidadRepuesto()
    {
        $respuesta =[];
        $sql = "SELECT * FROM unidades_repuestos";

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
    
    public function getOneUnidaRepuesto()
    {
        $respuesta = [];
        $sql = "SELECT * FROM unidades_repuestos where id = '{$_POST["id"]}'";

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


    public function saveUnidadRepuesto()
    {
        $sql = "INSERT INTO unidades_repuestos (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateUnidadRepuesto()
    {
        $sql = "UPDATE unidades_repuestos SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteUnidadRepuesto()
    {
        $sql = "DELETE FROM unidades_repuestos WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
