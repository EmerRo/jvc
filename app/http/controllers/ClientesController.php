<?php

use Mpdf\Utils\Arrays;

require_once "app/models/Cliente.php";
require_once "utils/lib/exel/vendor/autoload.php";


class ClientesController extends Controller
{

    private $cliente;
    private $conectar;

    public function __construct()
    {
        $this->cliente = new Cliente();
        $this->conectar = (new Conexion())->getConexion();
    }



    public function insertarXLista()
    {
        /*   $lista = json_decode($_POST['lista'], true);
          echo json_encode($lista);
          die(); */
        $lista = json_decode($_POST['lista'], true);
        //var_dump($lista);
        $respuesta = ["res" => false];
        foreach ($lista as $item) {


            $datos = $item['datos'];
            $direccion = $item['direccion'];
            $direccion2 = $item['direccion2'];
            $sql = "INSERT into clientes set datos=?,
  documento='{$item['documento']}',
  direccion=?,
  direccion2=?,
  email='{$item['email']}',
  id_empresa='{$_SESSION['id_empresa']}',
  telefono='{$item['telefono']}',
  telefono2='{$item['telefono2']}'";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('sss', $datos, $direccion, $direccion2);
            if ($stmt->execute()) {
                $respuesta["res"] = true;
            }
        }
        return json_encode($respuesta);
    }
   // Modificación en ClientesController.php - método insertar
public function insertar()
{
    if (!empty($_POST)) {
        $errors = [];
        $response = ['status' => 'error'];
        
        try {
            $doc = trim(filter_var($_POST['documentoAgregar'], FILTER_SANITIZE_STRING));
            $datosAgregar = trim(filter_var($_POST['datosAgregar'], FILTER_SANITIZE_STRING));
            $direccionAgregar = trim(filter_var($_POST['direccionAgregar'], FILTER_SANITIZE_STRING));
            $direccionAgregar2 = trim(filter_var($_POST['direccionAgregar2'], FILTER_SANITIZE_STRING));
            $telefonoAgregar = trim(filter_var($_POST['telefonoAgregar'], FILTER_SANITIZE_STRING));
            $telefonoAgregar2 = trim(filter_var($_POST['telefonoAgregar2'], FILTER_SANITIZE_STRING));
            $email = trim(filter_var($_POST['direccion'], FILTER_SANITIZE_EMAIL));
            // Convertir email a minúsculas
            $email = strtolower($email);
            $rubroCliente = isset($_POST['rubroCliente']) ? trim(filter_var($_POST['rubroCliente'], FILTER_SANITIZE_STRING)) : null;

            // Validaciones
            if (empty($doc)) {
                $errors['documentoAgregar'] = "El documento es obligatorio";
            } elseif (strlen($doc) != 8 && strlen($doc) != 11) {
                $errors['documentoAgregar'] = "El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC)";
            } else {
                // Verificar si el documento ya existe
                if ($this->cliente->existeDocumento($doc, $_SESSION['id_empresa'])) {
                    $errors['documentoAgregar'] = "Ya existe un cliente registrado con este documento de identidad";
                }
            }
            
            if (empty($datosAgregar)) {
                $errors['datosAgregar'] = "El nombre/razón social es obligatorio";
            }
            
            if (!empty($telefonoAgregar) && strlen($telefonoAgregar) != 9) {
                $errors['telefonoAgregar'] = "El teléfono debe tener 9 dígitos";
            }
            
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "El formato del email no es válido";
            }
            
            // Si hay errores, devolver respuesta con errores
            if (!empty($errors)) {
                $response['errors'] = $errors;
                echo json_encode($response);
                return;
            }
            
            // Continuar con la inserción si no hay errores
            $this->cliente->setDocumento($doc);
            $this->cliente->setDatos($datosAgregar);
            $this->cliente->setDireccion($direccionAgregar);
            $this->cliente->setDireccion2($direccionAgregar2);
            $this->cliente->setTelefono($telefonoAgregar);
            $this->cliente->setTelefono2($telefonoAgregar2);
            $this->cliente->setEmail($email);
            $this->cliente->setRubro($rubroCliente);
            
            $save = $this->cliente->insertar();
            
            if ($save) {
                $response = [
                    'status' => 'success',
                    'message' => 'Cliente registrado correctamente',
                    'data' => $this->cliente->idLast()
                ];
            } else {
                throw new Exception("Error al guardar en la base de datos: " . $this->conectar->error);
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se recibieron datos del formulario'
        ]);
    }
}
public function render()
{
    $sql = "SELECT c.*, r.nombre as rubro_nombre 
            FROM clientes c 
            LEFT JOIN rubros r ON c.id_rubro = r.id_rubro 
            WHERE c.id_empresa = '{$_SESSION['id_empresa']}'
            ORDER BY c.id_cliente DESC"; // Ordenar por ID descendente para mostrar los más recientes primero
    
    $result = $this->conectar->query($sql);
    $clientes = [];
    
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    
    echo json_encode($clientes);
}
    public function getOne()
    {
        /* $presupuesto = new PresupuestosModel(); */
        $data = $_POST;
        $id = $data['id'];
        $getOne = $this->cliente->getOne($id);
        echo json_encode($getOne);
    }
    public function cuentasCobrar()
    {
        /* $presupuesto = new PresupuestosModel(); */

        $getAll = $this->cliente->cuentasCobrar();
        echo json_encode($getAll);
    }
    public function cuentasCobrarEstado()
    {
        $getAll = $this->cliente->cuentasCobrarEstado($_POST['id']);
        echo json_encode($getAll);
    }
    // Modificación en ClientesController.php - método editar
public function editar()
{
    if (!empty($_POST)) {
        try {
            $id = $_POST['idCliente'];
            $documento = $_POST['documentoEditar'];
            $datos = $_POST['datosEditar'];
            $direccion = $_POST['direccionEditar'];
            $direccion2 = isset($_POST['direccionEditar2']) ? $_POST['direccionEditar2'] : '';
            $telefono = isset($_POST['telefonoEditar']) ? $_POST['telefonoEditar'] : '';
            $telefono2 = isset($_POST['telefonoEditar2']) ? $_POST['telefonoEditar2'] : '';
            $email = isset($_POST['emailEditar']) ? strtolower($_POST['emailEditar']) : ''; // Convertir a minúsculas
            $rubro = isset($_POST['rubroClienteEditar']) ? $_POST['rubroClienteEditar'] : null;
            
            // Validaciones
            $errors = [];
            
            if (empty($documento)) {
                $errors['documento'] = "El documento es obligatorio";
            } elseif (strlen($documento) != 8 && strlen($documento) != 11) {
                $errors['documento'] = "El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC)";
            } else {
                // Verificar si el documento ya existe (excluyendo el cliente actual)
                if ($this->cliente->existeDocumento($documento, $_SESSION['id_empresa'], $id)) {
                    $errors['documento'] = "Ya existe otro cliente registrado con este documento de identidad";
                }
            }
            
            if (empty($datos)) {
                $errors['datos'] = "El nombre/razón social es obligatorio";
            }
            
            if (!empty($telefono) && strlen($telefono) != 9) {
                $errors['telefono'] = "El teléfono debe tener 9 dígitos";
            }
            
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "El formato del email no es válido";
            }
            
            // Si hay errores, devolver respuesta con errores
            if (!empty($errors)) {
                echo json_encode([
                    'status' => 'error',
                    'errors' => $errors
                ]);
                return;
            }
            
            // Continuar con la actualización si no hay errores
            $this->cliente->setDocumento($documento);
            $this->cliente->setDatos($datos);
            $this->cliente->setDireccion($direccion);
            $this->cliente->setDireccion2($direccion2);
            $this->cliente->setTelefono($telefono);
            $this->cliente->setTelefono2($telefono2);
            $this->cliente->setEmail($email); // Ya convertido a minúsculas
            $this->cliente->setRubro($rubro);
            
            $update = $this->cliente->editar($id);
            
            if ($update) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cliente actualizado correctamente'
                ]);
            } else {
                throw new Exception("Error al actualizar en la base de datos");
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se recibieron datos del formulario'
        ]);
    }
}
    public function borrar()
    {
        $dataId = $_POST["value"];
        $save = $this->cliente->delete($dataId);
        if ($save) {
            echo json_encode("nice");
        } else {
            echo json_encode("error");
        }
    }

    public function importarExcel()
    {
        $respuesta = ["res" => false];
        $filename = $_FILES['file']['name'];

        $path_parts = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = Tools::getToken(80);
        /* Location */
        $loc_ruta = "files/temp";
        if (!file_exists($loc_ruta)) {
            mkdir($loc_ruta, 0777, true);
        }
        $location = $loc_ruta . "/" . $newName . '.' . $path_parts;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            $nombre_logo = $newName . "." . $path_parts;

            $respuesta["res"] = true;
            $type = $path_parts;

            if ($type == "xlsx") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($type == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($type == "csv") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load("files/temp/" . $nombre_logo);

            $schdeules = $spreadsheet->getActiveSheet()->toArray();
            // array_shift($schdeules);
            $respuesta["data"] = $schdeules;

            unlink($location);
            //return $schdeules;
            /*   $last = $this->cliente->idLast();
            $arr = array($respuesta, $last); */
        }

        return json_encode($respuesta);
    }
    /*   public function importAdd(){
        echo json_encode($_POST);
    } */
}