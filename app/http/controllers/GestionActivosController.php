<?php
use Mpdf\Utils\Arrays;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim;
require_once 'app/models/GestionActivos.php';
require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
class GestionActivosController extends Controller {

    private $conexion;
    private $gestion_activos;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }
    public function insertar()
    {
        try {
            if (!empty($_POST)) {
                $errores = [];
                
                // Filtrado y sanitización de los datos
                $cliente_razon_social = trim(filter_var($_POST['cliente_razon_social'], FILTER_SANITIZE_STRING));
                $motivo = trim(filter_var($_POST['motivo'], FILTER_SANITIZE_STRING));
                $marca = trim(filter_var($_POST['marca'], FILTER_SANITIZE_STRING));
                $equipo = trim(filter_var($_POST['equipo'], FILTER_SANITIZE_STRING));
                $modelo = trim(filter_var($_POST['modelo'], FILTER_SANITIZE_STRING));
                $numero_serie = trim(filter_var($_POST['numero_serie'], FILTER_SANITIZE_STRING));
                $fecha_ingreso = trim(filter_var($_POST['fecha_ingreso'], FILTER_SANITIZE_STRING));
                $fecha_salida = trim(filter_var($_POST['fecha_salida'], FILTER_SANITIZE_STRING));
                $observaciones = isset($_POST['observaciones']) ? trim(filter_var($_POST['observaciones'], FILTER_SANITIZE_STRING)) : null;
    
                // Validaciones individuales de campos requeridos
                if (empty($cliente_razon_social)) {
                    $errores['cliente_razon_social'] = "El nombre del cliente es requerido";
                }
                
                if (empty($motivo)) {
                    $errores['motivo'] = "El motivo es requerido";
                }
                
                if (empty($marca)) {
                    $errores['marca'] = "La marca es requerida";
                }
                
                if (empty($equipo)) {
                    $errores['equipo'] = "El equipo es requerido";
                }
                
                if (empty($modelo)) {
                    $errores['modelo'] = "El modelo es requerido";
                }
                
                if (empty($numero_serie)) {
                    $errores['numero_serie'] = "El número de serie es requerido";
                }
    
                // Validación de fechas
                $dateIngreso = DateTime::createFromFormat('Y-m-d', $fecha_ingreso);
                $dateSalida = DateTime::createFromFormat('Y-m-d', $fecha_salida);
                $hoy = new DateTime();
    
                // if (empty($fecha_ingreso)) {
                //     $errores['fecha_ingreso'] = "La fecha de ingreso es requerida";
                // }
                
                if (empty($fecha_salida)) {
                    $errores['fecha_salida'] = "La fecha de salida es requerida";
                }
    
               
    
                // Si hay errores, devolver array de errores
                if (!empty($errores)) {
                    echo json_encode([
                        "res" => false,
                        "errores" => $errores
                    ]);
                    return;
                }
    
                // Si no hay errores, proceder con la inserción
                $gestion_activos = new GestionActivos();
                $gestion_activos->setClienteRazonSocial($cliente_razon_social);
                $gestion_activos->setMotivo($motivo);
                $gestion_activos->setMarca($marca);
                $gestion_activos->setEquipo($equipo);
                $gestion_activos->setModelo($modelo);
                $gestion_activos->setNumeroSerie($numero_serie);
                $gestion_activos->setFechaIngreso($fecha_ingreso);
                $gestion_activos->setFechaSalida($fecha_salida);
                $gestion_activos->setObservaciones($observaciones);
    
                // Intentar insertar el activo
                $resultado = $gestion_activos->insertar();
    
                if ($resultado) {
                    echo json_encode([
                        "res" => true,
                        "msg" => "Activo agregado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "res" => false,
                        "msg" => "Error al agregar el activo"
                    ]);
                }
            } else {
                echo json_encode([
                    "res" => false,
                    "msg" => "Error: No se recibieron datos"
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "res" => false,
                "msg" => "Error al agregar el activo: " . $e->getMessage()
            ]);
        }
    }

    // Método para obtener todos los activos
    public function listarActivos()
{
    try {
        $gestion_activos = new GestionActivos();
        $data = $gestion_activos->verFilas();
        header('Content-Type: application/json');
        echo json_encode($data);
    } catch (Exception $e) {
        echo json_encode(["error" => "Error al listar los activos: " . $e->getMessage()]);
    }
}

    // Método para obtener un activo por ID
    public function obtenerActivoPorId($id)
    {
        try {
            $gestion_activos = new GestionActivos();
            return $gestion_activos->obtenerDatos($id);
        } catch (Exception $e) {
            echo "Error al obtener el activo: " . $e->getMessage();
        }
    }

    // Método para actualizar un activo
    public function actualizarActivo($id, $datos)
    {
        try {
            $gestion_activos = new GestionActivos();
            $gestion_activos->setClienteRazonSocial($datos['cliente_razon_social']);
            $gestion_activos->setMotivo($datos['motivo']);
            $gestion_activos->setMarca($datos['marca']);
            $gestion_activos->setEquipo($datos['equipo']);
            $gestion_activos->setModelo($datos['modelo']);
            $gestion_activos->setNumeroSerie($datos['numero_serie']);
            $gestion_activos->setFechaSalida($datos['fecha_salida']);
            $gestion_activos->setFechaIngreso($datos['fecha_ingreso']);
            $gestion_activos->setObservaciones($datos['observaciones']);
            
            return $gestion_activos->modificar($id);
        } catch (Exception $e) {
            echo "Error al actualizar el activo: " . $e->getMessage();
        }
    }

    // Método para eliminar un activo
    public function eliminarActivo()
    {
        try {
            // Verificar si se ha enviado el id del activo a eliminar
            if (isset($_POST['idDelete']) && !empty($_POST['idDelete'])) {
                $id = $_POST['idDelete'];
                
                // Crear una instancia de GestionActivos
                $gestion_activos = new GestionActivos();
                
                // Llamar al método eliminar, que debería manejar la eliminación del activo
                $resultado = $gestion_activos->eliminar($id);
    
                // Verificar si la eliminación fue exitosa
                if ($resultado) {
                    echo json_encode(["res" => true, "msg" => "Activo eliminado correctamente"]);
                } else {
                    echo json_encode(["res" => false, "msg" => "Error al eliminar el activo"]);
                }
            } else {
                echo json_encode(["res" => false, "msg" => "ID no proporcionado"]);
            }
        } catch (Exception $e) {
            echo json_encode(["res" => false, "msg" => "Error al eliminar el activo: " . $e->getMessage()]);
        }
    }

    public function confirmarActivo()
    {
        try {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $id = $_POST['id'];
                
                // Primero verificamos si el registro tiene fecha de ingreso
                $gestion_activos = new GestionActivos();
                $datos = $gestion_activos->obtenerDatos($id);
                
                // Verificar si la fecha de ingreso está vacía o es 0000-00-00
                if (empty($datos['fecha_ingreso']) || $datos['fecha_ingreso'] == '0000-00-00') {
                    if (!isset($_POST['fecha_ingreso']) || empty($_POST['fecha_ingreso'])) {
                        echo json_encode([
                            "success" => false,
                            "requiresFechaIngreso" => true,
                            "msg" => "Este activo requiere fecha de ingreso"
                        ]);
                        return;
                    }
                    $fecha_ingreso = $_POST['fecha_ingreso'];
                } else {
                    $fecha_ingreso = $datos['fecha_ingreso'];
                }
                
                // Iniciamos una transacción
                $this->conexion->begin_transaction();
                
                try {
                    // Actualizamos el estado en gestion_activos
                    $sql1 = "UPDATE gestion_activos SET 
                            estado = 'CONFIRMADO',
                            fecha_ingreso = ? 
                            WHERE id = ?";
                    
                    $stmt1 = $this->conexion->prepare($sql1);
                    $stmt1->bind_param("si", $fecha_ingreso, $id);
                    $stmt1->execute();
                    
                    // Actualizamos el estado de la máquina
                    $sql2 = "UPDATE maquina SET 
                            estado = 'NO DISPONIBLE' 
                            WHERE numero_serie = (
                                SELECT numero_serie 
                                FROM gestion_activos 
                                WHERE id = ?
                            )";
                    
                    $stmt2 = $this->conexion->prepare($sql2);
                    $stmt2->bind_param("i", $id);
                    $stmt2->execute();
                    
                    // Confirmamos la transacción
                    $this->conexion->commit();
                    
                    echo json_encode([
                        "success" => true,
                        "msg" => "Activo confirmado correctamente"
                    ]);
                } catch (Exception $e) {
                    // Si hay error, revertimos los cambios
                    $this->conexion->rollback();
                    throw $e;
                }
            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "ID no proporcionado"
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => "Error al confirmar el activo: " . $e->getMessage()
            ]);
        }
    }
public function obtenerActivo()
{
    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            $gestion_activos = new GestionActivos();
            $datos = $gestion_activos->obtenerDatos($id);
            echo json_encode($datos);
        } else {
            echo json_encode(["error" => "ID no proporcionado"]);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Error al obtener los datos: " . $e->getMessage()]);
    }
}
public function descargarPDF($id)
{
    try {
        $gestion_activos = new GestionActivos();
        $datos = $gestion_activos->obtenerDatos($id);
        
        // Crear instancia de MPDF
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);
        
        // Generar correlativo
        $year = date('Y');
        $correlativo = sprintf('%06d/%d', $id, $year);
        
        // Construir el HTML del PDF
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 20px; }
            .correlativo { font-size: 16px; margin-bottom: 20px; }
            .details { margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f8f9fa; }
        </style>
        
        <div class="header">
            <h1>GESTIÓN DE ACTIVOS</h1>
            <div class="correlativo">' . $correlativo . '</div>
        </div>
        ';
        
        // Agregar el contenido
        $html .= $this->generarContenidoPDF($datos);
        
        // Escribir el HTML
        $mpdf->WriteHTML($html);
        
        // Generar nombre del archivo
        $filename = 'gestion-activos-' . $correlativo . '.pdf';
        
        // Descargar el PDF
        $mpdf->Output($filename, 'D');
        
    } catch (Exception $e) {
        echo "Error al generar el PDF: " . $e->getMessage();
    }
}
private function generarContenidoPDF($datos)
{
    return '
    <div class="details">
        <p><strong>Cliente:</strong> ' . $datos['cliente_razon_social'] . '</p>
        <p><strong>Motivo:</strong> ' . $datos['motivo'] . '</p>
        <p><strong>Observaciones:</strong> ' . ($datos['observaciones'] ?? 'Sin observaciones') . '</p>
    </div>
    
    <table>
        <tr>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Equipo</th>
            <th>Número de Serie</th>
        </tr>
        <tr>
            <td>' . $datos['marca'] . '</td>
            <td>' . $datos['modelo'] . '</td>
            <td>' . $datos['equipo'] . '</td>
            <td>' . $datos['numero_serie'] . '</td>
        </tr>
    </table>
    
    <div class="details">
        <p><strong>Fecha de Salida:</strong> ' . $datos['fecha_salida'] . '</p>
        <p><strong>Fecha de Ingreso:</strong> ' . $datos['fecha_ingreso'] . '</p>
    </div>
    ';
}
}

