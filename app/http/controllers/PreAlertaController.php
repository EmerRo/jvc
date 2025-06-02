<?php


require_once 'app/models/PreAlerta.php';

class PreAlertaController extends Controller
{
    private $preAlerta;
    private $conectar;
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InN5c3RlbWNyYWZ0LnBlQGdtYWlsLmNvbSJ9.yuNS5hRaC0hCwymX_PjXRoSZJWLNNBeOdlLRSUGlHGA';

    public function __construct()
    {
        $this->preAlerta = new PreAlerta();
        $this->conectar = (new Conexion())->getConexion();
    }

    public function insertarXLista()
    {
        $lista = json_decode($_POST['lista'], true);
        $respuesta = ["res" => false];
        foreach ($lista as $item) {
            // Aquí puedes agregar la lógica de inserción en bloque si lo necesitas.
        }
        return json_encode($respuesta);
    }

    private function actualizarEstadoSerie($numero_serie, $estado = 'en_trabajo')
    {
        $sql = "UPDATE detalle_serie SET estado = ? WHERE numero_serie = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ss", $estado, $numero_serie);
        return $stmt->execute();
    }
    private function actualizarEstadoSeriePreAlerta($numero_serie, $estado = 'en_trabajo')
    {
        $sql = "UPDATE detalle_serie SET estado_prealerta = ? WHERE numero_serie = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ss", $estado, $numero_serie);
        return $stmt->execute();
    }


public function insertar()
{
    if (!empty($_POST)) {
        try {
            // DEBUGGING: Agregar logs
            error_log("=== DEBUGGING PREALERTA ===");
            error_log("POST recibido: " . print_r($_POST, true));
            
            $cliente_Rsocial = isset($_POST['cliente_Rsocial']) ?
                trim(filter_var($_POST['cliente_Rsocial'], FILTER_SANITIZE_STRING)) : null;
            $cliente_Ruc = isset($_POST['num_doc']) ?
                trim(filter_var($_POST['num_doc'], FILTER_SANITIZE_STRING)) : null;

            $cliente_documento = isset($_POST['cliente_documento']) ?
                trim(filter_var($_POST['cliente_documento'], FILTER_SANITIZE_STRING)) : $cliente_Ruc;
            
            $direccion = '';

            if (!empty($cliente_Ruc) && strlen($cliente_Ruc) === 11 && substr($cliente_Ruc, 0, 2) === '20') {
                $direccion = isset($_POST['direccion']) ?
                    trim(filter_var($_POST['direccion'], FILTER_SANITIZE_STRING)) : '';
            }

            $atencion_Encargado = isset($_POST['atencion_Encargado']) ?
                trim(filter_var($_POST['atencion_Encargado'], FILTER_SANITIZE_STRING)) : null;
            $fecha_ingreso = isset($_POST['fecha_ingreso']) ?
                trim(filter_var($_POST['fecha_ingreso'], FILTER_SANITIZE_STRING)) : null;
            $origen = isset($_POST['origen']) ?
                trim(filter_var($_POST['origen'], FILTER_SANITIZE_STRING)) : null;

            $observaciones = isset($_POST['observaciones']) ?
                trim(filter_var($_POST['observaciones'], FILTER_SANITIZE_STRING)) : null;

            // Procesar equipos
            $equipos = isset($_POST['equipos']) ? $_POST['equipos'] : [];
            error_log("Equipos recibidos: " . print_r($equipos, true));
            
            // CORREGIR: Mapear los nombres de campos correctamente
            $equiposCorregidos = [];
            foreach ($equipos as $equipo) {
                $equiposCorregidos[] = [
                    'marca' => $equipo['marca'],
                    'modelo' => $equipo['modelo'],
                    'equipo' => isset($equipo['tipo']) ? $equipo['tipo'] : $equipo['equipo'],
                    'numero_serie' => isset($equipo['serie']) ? $equipo['serie'] : $equipo['numero_serie']
                ];
            }
            
            error_log("Equipos corregidos: " . print_r($equiposCorregidos, true));

            if ($cliente_Rsocial && $atencion_Encargado && $fecha_ingreso && !empty($equiposCorregidos)) {
                $this->preAlerta->setCliente_Rsocial($cliente_Rsocial);
                $this->preAlerta->setCliente_Ruc($cliente_documento);
                $this->preAlerta->setDireccion($direccion);
                $this->preAlerta->setAtencion_Encargado($atencion_Encargado);
                $this->preAlerta->setFecha_Ingreso($fecha_ingreso);
                $this->preAlerta->setOrigen($origen);
                $this->preAlerta->setObservaciones($observaciones);
                $this->preAlerta->setDetalles($equiposCorregidos);

                $save = $this->preAlerta->insertar();
                if ($save) {
                    // Actualizar el estado de las series a 'en_trabajo'
                    foreach ($equiposCorregidos as $equipo) {
                        if (!empty($equipo['numero_serie'])) {
                            $this->actualizarEstadoSeriePreAlerta($equipo['numero_serie'], 'en_trabajo');
                        }
                    }
                    
                    // 🔥 RESPUESTA SEGÚN EL ORIGEN
                    if ($origen === 'Ord Trabajo') {
                        // Para Orden de Trabajo: solo el ID (número)
                        echo $this->preAlerta->idLast();
                    } else {
                        // Para Orden de Servicio: objeto JSON completo
                        echo json_encode($this->preAlerta->idLast());
                    }
                    
                } else {
                    error_log("Error al insertar en la base de datos");
                    echo json_encode("Ocurrió un error al guardar los datos");
                }
            } else {
                error_log("Validación fallida - datos faltantes");
                error_log("cliente_Rsocial: " . ($cliente_Rsocial ? 'OK' : 'FALTA'));
                error_log("atencion_Encargado: " . ($atencion_Encargado ? 'OK' : 'FALTA'));
                error_log("fecha_ingreso: " . ($fecha_ingreso ? 'OK' : 'FALTA'));
                error_log("equipos: " . (empty($equiposCorregidos) ? 'FALTA' : 'OK'));
                echo json_encode('Llene el formulario correctamente');
            }
        } catch (Exception $e) {
            error_log("Excepción en insertar: " . $e->getMessage());
            echo json_encode("Error al procesar la solicitud: " . $e->getMessage());
        }
    } else {
        echo json_encode('Error: No se recibieron datos');
    }
}  
public function editar()
    {
        if (!isset($_POST['id_preAlerta'])) {
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        $id = filter_var($_POST['id_preAlerta'], FILTER_SANITIZE_NUMBER_INT);
        $datos = [
            'cliente_razon_social' => $_POST['cliente_razon_social'],
            'cliente_ruc' => $_POST['cliente_ruc'],
            'atencion_encargado' => $_POST['atencion_encargado'],
            'fecha_ingreso' => $_POST['fecha_ingreso'],
            'observaciones' => $_POST['observaciones']
        ];
        $equipos = isset($_POST['equipos']) ? $_POST['equipos'] : [];

        $result = $this->preAlerta->actualizarPreAlerta($id, $datos, $equipos);

        if ($result) {
            $updatedData = $this->preAlerta->obtenerDatosConDetalles($id);
            echo json_encode(['success' => true, 'data' => $updatedData]);
        } else {
            echo json_encode(['error' => 'Error al actualizar el registro']);
        }
    }

    public function render()
    {
        header('Content-Type: application/json');

        try {
            $getAll = $this->preAlerta->getAllData();

            if ($getAll === false) {
                throw new Exception("Error al obtener los datos");
            }

            echo json_encode($getAll);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }


    public function getOne($id)
    {
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id)) {
                echo json_encode(['error' => 'ID inválido']);
                return;
            }

            // Obtener los datos
            $data = $this->preAlerta->getOne($id);

            // Verificar si se encontraron datos
            if (empty($data)) {
                echo json_encode(['error' => 'No se encontró el registro']);
                return;
            }

            // Devolver los datos como JSON
            header('Content-Type: application/json');
            echo json_encode($data);

        } catch (Exception $e) {
            // Log del error
            error_log("Error en getOne: " . $e->getMessage());

            // Devolver error como JSON
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener los datos']);
        }
    }


    public function borrar()
    {
        $dataId = $_POST["value"];
        $save = $this->preAlerta->delete($dataId);
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
            $respuesta["data"] = $schdeules;

            unlink($location);
        }

        return json_encode($respuesta);
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


    // En PreAlertaController.php
    public function actualizarEstadoCotizacion()
    {
        $id_preAlerta = $_POST['id_preAlerta'];

        $sql = "UPDATE pre_alerta SET tiene_cotizacion = 1 WHERE id_preAlerta = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_preAlerta);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    }
   public function culminarTrabajo()
{
    try {
        if (!isset($_POST['id_preAlerta'])) {
            throw new Exception('ID no proporcionado');
        }

        $id = filter_var($_POST['id_preAlerta'], FILTER_SANITIZE_NUMBER_INT);
        
        // Obtener las series asociadas a esta pre-alerta
        $sqlSeries = "SELECT numero_serie FROM pre_alerta_detalles WHERE id_preAlerta = ?";
        $stmtSeries = $this->conectar->prepare($sqlSeries);
        $stmtSeries->bind_param("i", $id);
        $stmtSeries->execute();
        $resultSeries = $stmtSeries->get_result();
        
        // Actualizar estado de pre-alerta
        $sql = "UPDATE pre_alerta SET estado = 'CULMINADO' WHERE id_preAlerta = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta');
        }

        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Actualizar estado de series a 'culminado' en estado_prealerta
            while ($serie = $resultSeries->fetch_assoc()) {
                $this->actualizarEstadoSeriePreAlerta($serie['numero_serie'], 'culminado');
            }
            
            echo json_encode(['success' => true, 'message' => 'Trabajo culminado exitosamente']);
        } else {
            throw new Exception('Error al actualizar el estado');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
    public function detalles()
    {
        if (!isset($_POST['id'])) {
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }

        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

        try {
            $sql = "SELECT pa.*, 
                       GROUP_CONCAT(CONCAT_WS('|', pad.marca, pad.equipo, pad.modelo, pad.numero_serie) SEPARATOR '##') as equipos
                FROM pre_alerta pa
                LEFT JOIN pre_alerta_detalles pad ON pa.id_preAlerta = pad.id_preAlerta
                WHERE pa.id_preAlerta = ?
                GROUP BY pa.id_preAlerta";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                // Procesar los equipos
                if ($data['equipos']) {
                    $equiposArray = [];
                    $equipos = explode('##', $data['equipos']);
                    foreach ($equipos as $equipo) {
                        list($marca, $tipo, $modelo, $serie) = explode('|', $equipo);
                        $equiposArray[] = [
                            'marca' => $marca,
                            'equipo' => $tipo,
                            'modelo' => $modelo,
                            'numero_serie' => $serie
                        ];
                    }
                    $data['equipos'] = $equiposArray;
                } else {
                    $data['equipos'] = [];
                }

                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'No se encontró la pre-alerta']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['error' => 'Error al obtener los detalles de la pre-alerta']);
        }
    }

}

?>