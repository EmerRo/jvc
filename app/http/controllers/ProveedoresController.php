<?php

require_once "app/models/Proveedor.php";

class ProveedoresController extends Controller
{
    private $proveedor;
    private $conectar;

    public function __construct()
    {
        $this->proveedor = new Proveedor();
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        echo json_encode($this->proveedor->render());
    }

    public function getOne()
    {
        $id = $_POST['id'] ?? 0;
        echo json_encode($this->proveedor->getOne($id));
    }

    public function insertar()
    {
        if (empty($_POST)) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
            return;
        }

        $errors = [];

        $ruc = trim($_POST['rucAgregar'] ?? '');
        $razon_social = trim($_POST['razonSocialAgregar'] ?? '');
        $direccion = trim($_POST['direccionAgregar'] ?? '');
        $telefono = trim($_POST['telefonoAgregar'] ?? '');
        $email = trim($_POST['emailAgregar'] ?? '');
        $departamento = trim($_POST['departamentoAgregar'] ?? '');
        $provincia = trim($_POST['provinciaAgregar'] ?? '');
        $distrito = trim($_POST['distritoAgregar'] ?? '');
        $ubigeo = trim($_POST['ubigeoAgregar'] ?? '');

        if (empty($ruc)) {
            $errors['rucAgregar'] = 'El RUC es obligatorio';
        } elseif (strlen($ruc) !== 11) {
            $errors['rucAgregar'] = 'El RUC debe tener 11 dígitos';
        } elseif ($this->proveedor->existeDocumento($ruc, $_SESSION['id_empresa'])) {
            $errors['rucAgregar'] = 'Ya existe un proveedor con este RUC';
        }

        if (empty($razon_social)) {
            $errors['razonSocialAgregar'] = 'La razón social es obligatoria';
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        $save = $this->proveedor->insertar($ruc, $razon_social, $direccion, $telefono, $email, $departamento, $provincia, $distrito, $ubigeo);

        if ($save) {
            echo json_encode(['status' => 'success', 'message' => 'Proveedor registrado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el proveedor']);
        }
    }

    public function editar()
    {
        if (empty($_POST)) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
            return;
        }

        $id = $_POST['idProveedor'] ?? 0;
        $ruc = trim($_POST['rucEditar'] ?? '');
        $razon_social = trim($_POST['razonSocialEditar'] ?? '');
        $direccion = trim($_POST['direccionEditar'] ?? '');
        $telefono = trim($_POST['telefonoEditar'] ?? '');
        $email = trim($_POST['emailEditar'] ?? '');
        $departamento = trim($_POST['departamentoEditar'] ?? '');
        $provincia = trim($_POST['provinciaEditar'] ?? '');
        $distrito = trim($_POST['distritoEditar'] ?? '');
        $ubigeo = trim($_POST['ubigeoEditar'] ?? '');

        $errors = [];

        if (empty($ruc)) {
            $errors['rucEditar'] = 'El RUC es obligatorio';
        } elseif (strlen($ruc) !== 11) {
            $errors['rucEditar'] = 'El RUC debe tener 11 dígitos';
        } elseif ($this->proveedor->existeDocumento($ruc, $_SESSION['id_empresa'], $id)) {
            $errors['rucEditar'] = 'Ya existe otro proveedor con este RUC';
        }

        if (empty($razon_social)) {
            $errors['razonSocialEditar'] = 'La razón social es obligatoria';
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        $update = $this->proveedor->editar($id, $ruc, $razon_social, $direccion, $telefono, $email, $departamento, $provincia, $distrito, $ubigeo);

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Proveedor actualizado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el proveedor']);
        }
    }

    public function borrar()
    {
        $id = $_POST['value'] ?? 0;
        if ($this->proveedor->delete($id)) {
            echo json_encode('nice');
        } else {
            echo json_encode('error');
        }
    }
}
