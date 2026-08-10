<?php




class PreAlertaController extends Controller
{

    private $conectar;

    public function __construct()
    {
     
        $this->conectar = (new Conexion())->getConexion();
    }

    public function buscarDocInfo()
    {
        // Validar y sanitizar el documento
        $doc = htmlspecialchars(trim($_POST['doc']), ENT_QUOTES, 'UTF-8');

        require_once 'app/clases/ConsultaDocApi.php';
        $api = new ConsultaDocApi();
        $data = $api->buscar($doc);

        if (isset($data['success']) && $data['success']) {
            $data['data']['nombre'] = $data['nombre'];
        }

        echo json_encode($data);
    }


}

?>