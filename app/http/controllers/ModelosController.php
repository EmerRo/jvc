<?php

use Mpdf\Utils\Arrays;

require_once "utils/lib/exel/vendor/autoload.php";

class ModelosController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getModelo()
    {
        $respuesta = [];
        $sql = "SELECT * FROM modelos";

        // consulta
        $resultado = $this->conectar->query($sql);
        // Verificar si la consulta devolvió resultados

        if ($resultado->num_rows > 0) {
            // Iterar sobre los resultados
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }
    
    public function getOneModelo()
    {
        $respuesta = [];
        $sql = "SELECT * FROM modelos WHERE id = '{$_POST["id"]}'";

        // Ejecutar la consulta
        $resultado = $this->conectar->query($sql);

        // Verificar si la consulta devolvió resultados
        if ($resultado->num_rows > 0) {
            // Iterar sobre cada fila y agregarla al array de respuesta
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        // Devolver el resultado en formato JSON
        return json_encode($respuesta);
    }

    public function saveModelo()
    {
        $sql = "INSERT INTO modelos (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateModelo()
    {
        $sql = "UPDATE modelos SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteModelo()
    {
        $sql = "DELETE FROM modelos WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
