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
        $respuesta = [];
        $sql = "SELECT e.*, ma.nombre as marca_nombre, mo.nombre as modelo_nombre
                FROM equipos e
                LEFT JOIN marcas ma ON ma.id = e.marca_id
                LEFT JOIN modelos mo ON mo.id = e.modelo_id
                ORDER BY e.nombre";

        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    public function getEquiposByModelo($modelo_id)
    {
        $respuesta = [];
        $modelo_id = intval($modelo_id);
        $sql = "SELECT * FROM equipos WHERE modelo_id = $modelo_id ORDER BY nombre";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    public function getOneEquipo()
    {
        $respuesta = [];
        $sql = "SELECT * FROM equipos WHERE id = '{$_POST["id"]}'";

        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }


    public function saveEquipo()
    {
        $nombre    = mysqli_real_escape_string($this->conectar, $_POST['nombre']);
        $marca_id  = isset($_POST['marca_id'])  && $_POST['marca_id']  !== '' ? intval($_POST['marca_id'])  : 'NULL';
        $modelo_id = isset($_POST['modelo_id']) && $_POST['modelo_id'] !== '' ? intval($_POST['modelo_id']) : 'NULL';
        $sql = "INSERT INTO equipos (nombre, marca_id, modelo_id) VALUES ('$nombre', $marca_id, $modelo_id)";
        $this->conectar->query($sql);
        echo json_encode(['success' => true, 'id' => $this->conectar->insert_id]);
    }

    public function updateEquipo()
    {
        $nombre    = mysqli_real_escape_string($this->conectar, $_POST['nombre']);
        $marca_id  = isset($_POST['marca_id'])  && $_POST['marca_id']  !== '' ? intval($_POST['marca_id'])  : 'NULL';
        $modelo_id = isset($_POST['modelo_id']) && $_POST['modelo_id'] !== '' ? intval($_POST['modelo_id']) : 'NULL';
        $id        = intval($_POST['id']);
        $sql = "UPDATE equipos SET nombre='$nombre', marca_id=$marca_id, modelo_id=$modelo_id WHERE id = $id";
        $this->conectar->query($sql);
    }

    public function deleteEquipo()
    {
        $sql = "DELETE FROM equipos WHERE id = '{$_POST['id']}'";
        $this->conectar->query($sql);
    }
}
