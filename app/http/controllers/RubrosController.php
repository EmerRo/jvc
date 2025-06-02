<?php
// controllers/RubrosController.php

require_once "app/models/Rubro.php";

class RubrosController extends Controller
{
    private $rubro;
    private $conectar;

    public function __construct()
    {
        $this->rubro = new Rubro();
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        $getAll = $this->rubro->getAllData();
        echo json_encode($getAll);
    }

    public function insertar()
    {
        if (!empty($_POST)) {
            $nombre = trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING));
            
            if ($nombre !== "") {
                $this->rubro->setNombre($nombre);
                $save = $this->rubro->insertar();
                
                if ($save) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Ocurrió un Error"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "El nombre es requerido"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "No hay datos"]);
        }
    }

    public function getOne()
    {
        if (!empty($_POST)) {
            $id = $_POST['id'];
            $getOne = $this->rubro->getOne($id);
            echo json_encode($getOne);
        }
    }

    public function editar()
    {
        if (!empty($_POST)) {
            $nombre = trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING));
            $id = $_POST['id_rubro'];
            
            if ($nombre !== "") {
                $this->rubro->setNombre($nombre);
                $save = $this->rubro->editar($id);
                
                if ($save) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Ocurrió un Error"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "El nombre es requerido"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "No hay datos"]);
        }
    }

    public function borrar()
    {
        if (!empty($_POST)) {
            $id = $_POST['id_rubro'];
            $save = $this->rubro->delete($id);
            
            if ($save) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo eliminar"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "No hay ID"]);
        }
    }
}