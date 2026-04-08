<?php
require_once "app/models/Consultas.php";

class EmpresaController extends Controller
{
    private $consulta;

    public function __construct()
    {
        $this->consulta = new Consultas();
    }

    // Obtener información de la empresa
    public function infoEmpresa()
    {
        $id_empresa = $_SESSION['id_empresa'];
        $sql = "SELECT * FROM empresas WHERE id_empresa = $id_empresa";
        $resultado = $this->consulta->getConectar()->query($sql);
        $empresa = mysqli_fetch_assoc($resultado);
        echo json_encode($empresa);
    }

    // Actualizar datos de la empresa
    public function actualizarEmpresa()
    {
        $id_empresa = $_SESSION['id_empresa'];
        $con = $this->consulta->getConectar();

        $ruc = mysqli_real_escape_string($con, $_POST['ruc']);
        $razon_social = mysqli_real_escape_string($con, $_POST['razon_social']);
        $comercial = mysqli_real_escape_string($con, $_POST['comercial']);
        $direccion = mysqli_real_escape_string($con, $_POST['direccion']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $telefono = mysqli_real_escape_string($con, $_POST['telefono']);
        $telefono2 = mysqli_real_escape_string($con, $_POST['telefono2']);
        $telefono3 = mysqli_real_escape_string($con, $_POST['telefono3']);
        $user_sol = mysqli_real_escape_string($con, $_POST['user_sol']);
        $clave_sol = mysqli_real_escape_string($con, $_POST['clave_sol']);
        $ubigeo = mysqli_real_escape_string($con, $_POST['ubigeo']);
        $distrito = mysqli_real_escape_string($con, $_POST['distrito']);
        $provincia = mysqli_real_escape_string($con, $_POST['provincia']);
        $departamento = mysqli_real_escape_string($con, $_POST['departamento']);
        $igv = mysqli_real_escape_string($con, $_POST['igv']);
        $modo = mysqli_real_escape_string($con, $_POST['modo']);
        $propaganda = mysqli_real_escape_string($con, $_POST['propaganda']);

        // Manejo de logo
        $logoSQL = "";
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid() . '.' . $extension;
            $destino = "logos/" . $nombreArchivo;
            move_uploaded_file($_FILES['logo']['tmp_name'], $destino);
            $logoSQL = ", logo='$nombreArchivo'";
        }

        $sql = "UPDATE empresas SET 
            ruc='$ruc',
            razon_social='$razon_social',
            comercial='$comercial',
            direccion='$direccion',
            email='$email',
            telefono='$telefono',
            telefono2='$telefono2',
            telefono3='$telefono3',
            user_sol='$user_sol',
            clave_sol='$clave_sol',
            ubigeo='$ubigeo',
            distrito='$distrito',
            provincia='$provincia',
            departamento='$departamento',
            igv='$igv',
            modo='$modo',
            propaganda='$propaganda'
            $logoSQL
            WHERE id_empresa = $id_empresa";

        $this->consulta->exeSQL($sql);

        echo json_encode(["status" => "ok", "mensaje" => "Datos actualizados correctamente"]);
    }
}
