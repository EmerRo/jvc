<?php




class PreAlertaController extends Controller
{

    private $conectar;
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InN5c3RlbWNyYWZ0LnBlQGdtYWlsLmNvbSJ9.yuNS5hRaC0hCwymX_PjXRoSZJWLNNBeOdlLRSUGlHGA';

    public function __construct()
    {
     
        $this->conectar = (new Conexion())->getConexion();
    }

    public function buscarDocInfo()
    {
        // Validar y sanitizar el documento
        $doc = filter_var($_POST['doc'], FILTER_SANITIZE_STRING);

        if (strlen($doc) == 8) {
            $url = 'https://dniruc.apisperu.com/api/v1/dni/' . $doc . '?token=' . $this->token;
        } else {
            $url = 'https://dniruc.apisperu.com/api/v1/ruc/' . $doc . '?token=' . $this->token;
        }

        $data = $this->apiRequest($url);

        if (isset($data['data'])) {
            if (strlen($doc) == 8) {

                if (strlen($doc) == 8) {
                    $data["data"]["nombre"] = $data["data"]["nombres"] . " " . $data["data"]["apellidoPaterno"] . " " . $data["data"]["apellidoMaterno"];
                } else {
                    $data["data"]["nombre"] = $data["data"]["razonSocial"];
                }
            }
        }

        echo json_encode($data);
    }

    public function apiRequest($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }


}

?>