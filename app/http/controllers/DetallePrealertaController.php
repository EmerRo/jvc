<?php
require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/PreAlerta.php";

class DetallePreAlertaController extends Controller
{
    private $mpdf;
    private $preAlerta;
    private $conexion;

    public function __construct()
    {
        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0
        ]);
        $this->conexion = (new Conexion())->getConexion();
        $this->preAlerta = new PreAlerta();
    }

    public function generarPDF($id_preAlerta)
    {
        try {
            // Obtener datos de la pre-alerta
            $sql = "SELECT pa.*, 
                           GROUP_CONCAT(CONCAT_WS('|', pad.marca, pad.equipo, pad.modelo, pad.numero_serie) SEPARATOR '##') as equipos
                    FROM pre_alerta pa
                    LEFT JOIN pre_alerta_detalles pad ON pa.id_preAlerta = pad.id_preAlerta
                    WHERE pa.id_preAlerta = ?
                    GROUP BY pa.id_preAlerta";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id_preAlerta);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if (!$data) {
                throw new Exception('No se encontró la orden de servicio');
            }

            // Procesar equipos
            $equiposArray = [];
            if ($data['equipos']) {
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
            }

            // Configurar PDF
            $this->mpdf->SetTitle("Orden de Servicio - " . $data['cliente_razon_social']);
            $this->mpdf->SetAutoPageBreak(true, 0);

            // Estilos CSS
            $this->mpdf->WriteHTML('
            <style>
                body { 
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .header img, .footer img {
                    width: 100%;
                    display: block;
                    margin: 0;
                    padding: 0;
                }
                .content {
                    padding: 20px 40px;
                    margin-bottom: 100px;
                }
                .info-table {
                    width: 100%;
                    margin-bottom: 20px;
                }
                .info-table td {
                    padding: 5px;
                    vertical-align: top;
                }
                .info-label {
                    font-weight: bold;
                    white-space: nowrap;
                }
                .data-table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin: 10px 0; 
                }
                .data-table th, .data-table td { 
                    border: 1px solid #ddd; 
                    padding: 8px; 
                    text-align: left; 
                }
                .data-table th { 
                    background-color: #C41E3A;
                    color: white;
                }
                h2 {
                    text-align: center;
                    margin: 20px 0;
                    color: #333;
                    font-size: 24px;
                    text-transform: uppercase;
                }
                h3 {
                    color: #333;
                    margin: 20px 0 10px 0;
                }
            </style>
            ');

            // Contenido del PDF
            $html = "
                <div class='header'>
                    <img src='" . URL::to('public/assets/img/encabezado.jpg') . "'>
                </div>

                <div class='content'>
                    <h2>ORDEN DE SERVICIO</h2>

                    <table class='info-table' border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td width='50%'>
                                <span class='info-label'>Cliente:</span> " . $data['cliente_razon_social'] . "
                            </td>
                            <td width='50%'>
                                <span class='info-label'>Documento:</span> " . $data['cliente_ruc'] . "
                            </td>
                        </tr>
                        <tr>
                            <td width='50%'>
                                <span class='info-label'>Técnico Asignado:</span> " . $data['atencion_encargado'] . "
                            </td>
                            <td width='50%'>
                                <span class='info-label'>Fecha de Ingreso:</span> " . $data['fecha_ingreso'] . "
                            </td>
                        </tr>
                    </table>

                    <h3>Equipos Registrados</h3>
                    <table class='data-table'>
                        <thead>
                            <tr>
                                <th style='width: 5%'>#</th>
                                <th style='width: 25%'>Marca</th>
                                <th style='width: 25%'>Modelo</th>
                                <th style='width: 25%'>Equipo</th>
                                <th style='width: 20%'>Número de Serie</th>
                            </tr>
                        </thead>
                        <tbody>";

            foreach ($equiposArray as $index => $equipo) {
                $html .= "
                    <tr>
                        <td>" . ($index + 1) . "</td>
                        <td>" . $equipo['marca'] . "</td>
                        <td>" . $equipo['modelo'] . "</td>
                        <td>" . $equipo['equipo'] . "</td>
                        <td>" . $equipo['numero_serie'] . "</td>
                    </tr>";
            }

            $html .= "
                        </tbody>
                    </table>
                </div>

                <div class='footer' style='position: fixed; bottom: 0; left: 0; right: 0; margin: 0; padding: 0;'>
                    <img src='" . URL::to('public/assets/img/pie de pagina.jpg') . "' style='margin: 0; padding: 0;'>
                </div>
            ";

            $this->mpdf->WriteHTML($html);
            $this->mpdf->Output("Orden_Servicio_" . $id_preAlerta . ".pdf", "I");

        } catch (Exception $e) {
            error_log("Error generando PDF: " . $e->getMessage());
            echo "Error generando el PDF: " . $e->getMessage();
        }
    }
}