<?php
// app/http/controllers/ConstanciaController.php

require_once "app/models/Constancia.php";
require_once "app/models/ConstanciaPlantilla.php";
require_once "app/http/controllers/ConstanciaPDF.php";

class ConstanciaController extends Controller
{
    private $constancia;
    private $constanciaPlantilla;
    private $constanciaPDF;
    private $conectar;

    public function __construct()
    {
        $this->constancia = new Constancia();
        $this->constanciaPlantilla = new ConstanciaPlantilla();
        $this->constanciaPDF = new ConstanciaPDF();
        $this->conectar = (new Conexion())->getConexion();
    }

    // Método para obtener todas las constancias (con filtro opcional)
    public function render()
    {
        try {
            // Obtener parámetros de filtro
            $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
            $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
            
            // Intentar obtener los datos
            $constancias = $this->constancia->listarConstancias($filtro, $tipo_busqueda);
            
            // Devolver los datos en formato JSON
            echo json_encode($constancias ?: []);
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'message' => 'Error al procesar la solicitud',
                'debug_info' => $e->getMessage()
            ]);
        }
    }

    // Método para obtener una constancia específica
    public function getOne()
    {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }
        
        if ($this->constancia->obtenerConstancia($id)) {
            $respuesta = [
                'success' => true,
                'data' => [
                    'id' => $this->constancia->getId(),
                    'cliente_id' => $this->constancia->getClienteId(),
                    'usuario_id' => $this->constancia->getUsuarioId(),
                    'tipo' => $this->constancia->getTipo(),
                    'titulo' => $this->constancia->getTitulo(),
                    'contenido' => $this->constancia->getContenido(),
                    'header_image' => $this->constancia->getHeaderImage(),
                    'footer_image' => $this->constancia->getFooterImage(),
                    'header_image_url' => $this->constancia->getHeaderImageUrl(),
                    'footer_image_url' => $this->constancia->getFooterImageUrl(),
                    'estado' => $this->constancia->getEstado()
                ]
            ];
            
            echo json_encode($respuesta);
            return;
        }
        
        echo json_encode(['success' => false, 'error' => 'Constancia no encontrada']);
    }

public function insertar()
{
    if (!empty($_POST)) {
        try {
            // Validar datos
            $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
            $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
            $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
            $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
            
            // Verificar si el usuario está en sesión
            if (!isset($_SESSION['usuario_id'])) {
                // Buscar un usuario válido en la base de datos
                $stmt = $this->conectar->prepare("SELECT usuario_id FROM usuarios LIMIT 1");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $usuario_id = $row['usuario_id'];
                } else {
                    throw new Exception("No hay usuarios en la base de datos para asignar a la constancia");
                }
            } else {
                $usuario_id = $_SESSION['usuario_id'];
            }
            
            // Agregar información de depuración
            error_log("Datos a insertar - Tipo: $tipo, Titulo: $titulo, Cliente ID: $cliente_id, Usuario ID: $usuario_id");
            
            // Configurar el objeto constancia
            $this->constancia->setTipo($tipo);
            $this->constancia->setTitulo($titulo);
            $this->constancia->setContenido($contenido);
            $this->constancia->setHeaderImage(isset($_POST['header_image']) ? $_POST['header_image'] : null);
            $this->constancia->setFooterImage(isset($_POST['footer_image']) ? $_POST['footer_image'] : null);
            $this->constancia->setClienteId($cliente_id);
            $this->constancia->setUsuarioId($usuario_id);
            $this->constancia->setEstado('borrador');
            
            // Insertar la constancia
            if ($this->constancia->insertarConstancia()) {
                echo json_encode([
                    'res' => true, 
                    'msg' => 'Constancia creada correctamente',
                    'id' => $this->constancia->getId()
                ]);
            } else {
                // Obtener el error específico
                $errorDetalle = $this->constancia->getLastError();
                
                // También obtener error de la conexión directa
                $errorMySQL = $this->conectar->error;
                $errorNumero = $this->conectar->errno;
                
                // Construir mensaje de error detallado
                $mensajeError = "Error al guardar la constancia en la base de datos.\n";
                $mensajeError .= "Detalle: " . $errorDetalle . "\n";
                $mensajeError .= "MySQL Error: " . $errorMySQL . " (Código: " . $errorNumero . ")";
                
                error_log($mensajeError);
                throw new Exception($mensajeError);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'res' => false, 
                'msg' => $e->getMessage(),
                'debug' => [
                    'cliente_id' => $cliente_id ?? 'no definido',
                    'usuario_id' => $usuario_id ?? 'no definido',
                    'tipo' => $tipo ?? 'no definido',
                    'titulo' => $titulo ?? 'no definido'
                ]
            ]);
        }
    } else {
        echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
    }
}
    // Método para editar una constancia existente
    public function editar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
                $estado = isset($_POST['estado']) ? $_POST['estado'] : 'borrador';
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($id) || empty($titulo) || empty($contenido)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Obtener la constancia actual
                $this->constancia->setId($id);
                if (!$this->constancia->obtenerConstancia($id)) {
                    throw new Exception("La constancia no existe");
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->constancia->getHeaderImage();
                $footer_image = $this->constancia->getFooterImage();
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                    $header_image = $_POST['header_image'];
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                    $footer_image = $_POST['footer_image'];
                }
                
                // Configurar el objeto constancia
                $this->constancia->setTipo($tipo);
                $this->constancia->setTitulo($titulo);
                $this->constancia->setContenido($contenido);
                $this->constancia->setHeaderImage($header_image);
                $this->constancia->setFooterImage($footer_image);
                $this->constancia->setClienteId($cliente_id);
                $this->constancia->setEstado($estado);
                
                // Actualizar la constancia
                if ($this->constancia->actualizarConstancia()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => 'Constancia actualizada correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar la constancia en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para eliminar una constancia
    public function borrar()
    {
        if (isset($_POST["id"])) {
            $id = intval($_POST["id"]);
            
            if ($this->constancia->eliminarConstancia($id)) {
                echo json_encode(["res" => true, "msg" => "Constancia eliminada correctamente"]);
            } else {
                echo json_encode(["res" => false, "msg" => "Ocurrió un error al eliminar la constancia"]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "ID de constancia no proporcionado"]);
        }
    }

    // Método para generar PDF
    public function generarPDF()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->constanciaPDF->generarConstanciaPDF($id);
        } else {
            echo "ID de constancia no proporcionado";
        }
    }

    // Método para generar vista previa
    public function vistaPreviaPDF()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                
                // Procesar imágenes si se proporcionan
                $header_image = null;
                $footer_image = null;
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                    $header_image = $_POST['header_image'];
                } else {
                    // Usar la imagen de la plantilla
                    $this->constanciaPlantilla->obtenerTemplateActual();
                    $header_image = $this->constanciaPlantilla->getHeaderImageUrl();
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                    $footer_image = $_POST['footer_image'];
                } else {
                    // Usar la imagen de la plantilla
                    if (!$this->constanciaPlantilla->getId()) {
                        $this->constanciaPlantilla->obtenerTemplateActual();
                    }
                    $footer_image = $this->constanciaPlantilla->getFooterImageUrl();
                }
                
                // Generar vista previa
                $pdfBase64 = $this->constanciaPDF->generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image);
                
                echo json_encode([
                    'success' => true,
                    'pdfBase64' => $pdfBase64
                ]);
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para obtener la plantilla actual
    public function obtenerTemplate()
    {
        try {
            $this->constanciaPlantilla->obtenerTemplateActual();
            
            $data = [
                'success' => true,
                'data' => [
                    'id' => $this->constanciaPlantilla->getId(),
                    'titulo' => $this->constanciaPlantilla->getTitulo(),
                    'contenido' => $this->constanciaPlantilla->getContenido(),
                    'header_image' => $this->constanciaPlantilla->getHeaderImage(),
                    'footer_image' => $this->constanciaPlantilla->getFooterImage(),
                    'header_image_url' => $this->constanciaPlantilla->getHeaderImageUrl(),
                    'footer_image_url' => $this->constanciaPlantilla->getFooterImageUrl()
                ]
            ];
            
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Método para guardar la plantilla
    public function guardarTemplate()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($titulo)) {
                    throw new Exception("El título no puede estar vacío");
                }
                
                // Obtener la plantilla actual
                $this->constanciaPlantilla->obtenerTemplateActual();
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->constanciaPlantilla->getHeaderImage();
                $footer_image = $this->constanciaPlantilla->getFooterImage();
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                    $header_image = $_POST['header_image'];
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                    $footer_image = $_POST['footer_image'];
                }
                
                // Configurar el objeto template
                $this->constanciaPlantilla->setTitulo($titulo);
                $this->constanciaPlantilla->setContenido($contenido);
                $this->constanciaPlantilla->setHeaderImage($header_image);
                $this->constanciaPlantilla->setFooterImage($footer_image);
                
                // Si se proporciona un ID, actualizar la plantilla existente
                if (!empty($_POST['id'])) {
                    $this->constanciaPlantilla->setId($_POST['id']);
                    $resultado = $this->constanciaPlantilla->actualizarTemplate();
                } else {
                    $resultado = $this->constanciaPlantilla->insertarTemplate();
                }
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'mensaje' => 'Plantilla guardada correctamente',
                        'id' => $this->constanciaPlantilla->getId()
                    ]);
                } else {
                    throw new Exception("Error al guardar la plantilla en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para obtener tipos de constancias para filtrado
    public function getTipos()
    {
        try {
            $tipos = $this->constancia->obtenerTiposConstancias();
            echo json_encode(['success' => true, 'data' => $tipos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Método auxiliar para procesar imágenes
    private function procesarImagen($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF.");
        }
        
        // Leer el archivo y convertirlo a base64
        $imageData = file_get_contents($file['tmp_name']);
        $base64 = 'data:' . $file['type'] . ';base64,' . base64_encode($imageData);
        
        return $base64;
    }
}