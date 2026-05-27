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
        $sql = "SELECT m.*, ma.nombre as marca_nombre FROM modelos m LEFT JOIN marcas ma ON ma.id = m.marca_id ORDER BY m.nombre";

        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    public function getModelosByMarca($marca_id)
    {
        $respuesta = [];
        $marca_id = intval($marca_id);
        $sql = "SELECT * FROM modelos WHERE marca_id = $marca_id ORDER BY nombre";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
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
        $nombre   = mysqli_real_escape_string($this->conectar, $_POST['nombre']);
        $marca_id = isset($_POST['marca_id']) && $_POST['marca_id'] !== '' ? intval($_POST['marca_id']) : 'NULL';
        $sql = "INSERT INTO modelos (nombre, marca_id) VALUES ('$nombre', $marca_id)";
        $this->conectar->query($sql);
        echo json_encode(['success' => true, 'id' => $this->conectar->insert_id]);
    }

    public function updateModelo()
    {
        $nombre   = mysqli_real_escape_string($this->conectar, $_POST['nombre']);
        $marca_id = isset($_POST['marca_id']) && $_POST['marca_id'] !== '' ? intval($_POST['marca_id']) : 'NULL';
        $id       = intval($_POST['id']);
        $sql = "UPDATE modelos SET nombre='$nombre', marca_id=$marca_id WHERE id = $id";
        $this->conectar->query($sql);
    }

    public function deleteModelo()
    {
        $sql = "DELETE FROM modelos WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
