<?php

use Mpdf\Utils\Arrays;

require_once "app/models/Cliente.php";
require_once "utils/lib/exel/vendor/autoload.php";


class CategoriasController extends Controller
{

    private $cliente;
    private $conectar;

    public function __construct()
    {
        $this->cliente = new Cliente();
        $this->conectar = (new Conexion())->getConexion();
    }


//es para almacen productos
    public function getCategoria()
    {
        $respuesta = [];
        $sql = "SELECT * FROM categorias";

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

    public function getOneCategoria()
    {
        $respuesta = [];
        $sql = "SELECT * FROM categorias where id = '{$_POST["id"]}'";

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


    public function saveCategoria()
    {
        $sql = "INSERT INTO categorias (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }

    public function updateCategoria()
    {
        $sql = "UPDATE categorias SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    public function deleteCategoria()
    {
        $sql = "DELETE FROM categorias WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }

    //categorias para para repuestos
    public function getCategoriaRepuesto()
    {
        $respuesta = [];
        $sql = "SELECT * FROM categorias_repuestos";

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
    public function getOneCategoriaRepuesto()
    {
        $respuesta = [];
        $sql = "SELECT * FROM categorias_repuestos where id = '{$_POST["id"]}'";

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
    public function saveCategoriaRepuesto()
    {
        $sql = "INSERT INTO categorias_repuestos (nombre) VALUES ('{$_POST['nombre']}')";
        $this->conectar->query($sql);
    }
    public function updateCategoriaRepuesto()
    {
        $sql = "UPDATE categorias_repuestos SET nombre='{$_POST['nombre']}' WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
    public function deleteCategoriaRepuesto()
    {
        $sql = "DELETE FROM categorias_repuestos WHERE id ='{$_POST['id']}'";
        $this->conectar->query($sql);
    }
    // subcategorias para repuestos
public function getSubcategoriaRepuesto()
{
    $respuesta = [];
    $sql = "SELECT s.*, c.nombre as categoria_nombre 
            FROM subcategorias_repuestos s
            JOIN categorias_repuestos c ON s.categoria_id = c.id";

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

public function getSubcategoriasByCategoria()
{
    $respuesta = [];
    $sql = "SELECT * FROM subcategorias_repuestos WHERE categoria_id = '{$_POST["categoria_id"]}'";

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

public function getOneSubcategoriaRepuesto()
{
    $respuesta = [];
    $sql = "SELECT * FROM subcategorias_repuestos WHERE id = '{$_POST["id"]}'";

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

public function saveSubcategoriaRepuesto()
{
    $sql = "INSERT INTO subcategorias_repuestos (nombre, categoria_id) 
            VALUES ('{$_POST['nombre']}', '{$_POST['categoria_id']}')";
    $this->conectar->query($sql);
}

public function updateSubcategoriaRepuesto()
{
    $sql = "UPDATE subcategorias_repuestos 
            SET nombre='{$_POST['nombre']}', categoria_id='{$_POST['categoria_id']}' 
            WHERE id ='{$_POST['id']}'";
    $this->conectar->query($sql);
}

public function deleteSubcategoriaRepuesto()
{
    $sql = "DELETE FROM subcategorias_repuestos WHERE id ='{$_POST['id']}'";
    $this->conectar->query($sql);
}
}
