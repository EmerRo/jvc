<?php
// app/http/controllers/CartaController.php

require_once "app/models/Carta.php";
require_once "app/models/CartaTemplate.php";
require_once "app/http/controllers/CartaPDF.php";
require_once "app/models/TipoCarta.php";

class CartaController extends Controller
{
    private $carta;
    private $cartaTemplate;
    private $cartaPDF;
    private $tipoCarta;
    private $conectar;

    public function __construct()
    {
        $this->carta = new Carta();
        $this->cartaTemplate = new CartaTemplate();
        $this->cartaPDF = new CartaPDF();
        $this->tipoCarta= new TipoCarta();
        $this->conectar = (new Conexion())->getConexion();
    }

public function render()
{
    try {
        // Obtener parámetros de filtro
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
        $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
        
        // Intentar obtener los datos
        $cartas = $this->carta->listarCartas($filtro, $tipo_busqueda);
        
        // Verificar el tipo de datos devuelto
        error_log("Tipo de datos de cartas: " . gettype($cartas));
        error_log("Contenido de cartas: " . json_encode($cartas));
        
        // Asegurarse de que cartas sea un array
        if ($cartas === false) {
            $cartas = [];
        }
        
        // Devolver los datos en formato JSON
        $response = ['cartas' => $cartas];
        error_log("Respuesta final: " . json_encode($response));
        echo json_encode($response);
    } catch (Exception $e) {
        error_log("Error en CartaController::render(): " . $e->getMessage());
        echo json_encode([
            'error' => true,
            'message' => 'Error al procesar la solicitud',
            'debug_info' => $e->getMessage()
        ]);
    }
}

    // Método para obtener una carta específica
    public function getOne()
    {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['error' => 'ID no proporcionado']);
            return;
        }
        
        if ($this->carta->obtenerCarta($id)) {
            $respuesta = [
                'success' => true,
                'data' => [
                    'id' => $this->carta->getId(),
                    'id_cliente' => $this->carta->getIdCliente(),
                    'id_usuario' => $this->carta->getIdUsuario(),
                    'tipo' => $this->carta->getTipo(),
                    'titulo' => $this->carta->getTitulo(),
                    'contenido' => $this->carta->getContenido(),
                    'header_image' => $this->carta->getHeaderImage(),
                    'footer_image' => $this->carta->getFooterImage(),
                    'header_image_url' => $this->carta->getHeaderImageUrl(),
                    'footer_image_url' => $this->carta->getFooterImageUrl(),
                    'estado' => $this->carta->getEstado()
                ]
            ];
            
            echo json_encode($respuesta);
            return;
        }
        
        echo json_encode(['error' => 'Carta no encontrada']);
    }

    // Método para insertar una nueva carta
  public function insertar()
{
    if (!empty($_POST)) {
        try {
            // Validar datos
            $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
            $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
            $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
            $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
            
            // Validar que los campos obligatorios no estén vacíos
            if (empty($titulo) || empty($contenido)) {
                throw new Exception("Todos los campos obligatorios deben ser completados");
            }
            
            // Procesar imágenes si se proporcionan
            $header_image = null;
            $footer_image = null;
            
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
            
            // Obtener un ID de usuario válido
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            
            // Si no hay usuario en sesión, buscar uno válido en la base de datos
            if (!$usuario_id) {
                $stmt = $this->conectar->prepare("SELECT usuario_id FROM usuarios LIMIT 1");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $usuario_id = $row['usuario_id'];
                } else {
                    throw new Exception("No hay usuarios en la base de datos para asignar a la carta");
                }
            }
            
            // Configurar el objeto carta
            $this->carta->setTipo($tipo);
            $this->carta->setTitulo($titulo);
            $this->carta->setContenido($contenido);
            $this->carta->setHeaderImage($header_image);
            $this->carta->setFooterImage($footer_image);
            $this->carta->setIdCliente($id_cliente);
            $this->carta->setIdUsuario($usuario_id);
            $this->carta->setEstado('borrador');
            
            // Insertar la carta
            if ($this->carta->insertarCarta()) {
                echo json_encode([
                    'res' => true, 
                    'msg' => 'Carta creada correctamente',
                    'id' => $this->carta->getId()
                ]);
            } else {
                throw new Exception("Error al guardar la carta en la base de datos: Operación fallida");
            }
            
        } catch (Exception $e) {
            // Mostrar mensaje de error detallado
            echo json_encode([
                'res' => false, 
                'msg' => "Error al guardar la carta: " . $e->getMessage(),
                'debug' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
            
            // Registrar el error para depuración
            error_log("Error en CartaController::insertar: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
        }
    } else {
        echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
    }
}

    // Método para editar una carta existente
    public function editar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
                $estado = isset($_POST['estado']) ? $_POST['estado'] : 'borrador';
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($id) || empty($titulo) || empty($contenido)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Obtener la carta actual
                $this->carta->setId($id);
                if (!$this->carta->obtenerCarta($id)) {
                    throw new Exception("La carta no existe");
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->carta->getHeaderImage();
                $footer_image = $this->carta->getFooterImage();
                
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
                
                // Configurar el objeto carta
                $this->carta->setTipo($tipo);
                $this->carta->setTitulo($titulo);
                $this->carta->setContenido($contenido);
                $this->carta->setHeaderImage($header_image);
                $this->carta->setFooterImage($footer_image);
                $this->carta->setIdCliente($id_cliente);
                $this->carta->setEstado($estado);
                
                // Actualizar la carta
                if ($this->carta->actualizarCarta()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => 'Carta actualizada correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar la carta en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para eliminar una carta
    public function borrar()
    {
        if (isset($_POST["id"])) {
            $id = intval($_POST["id"]);
            
            if ($this->carta->eliminarCarta($id)) {
                echo json_encode(["res" => true, "msg" => "Carta eliminada correctamente"]);
            } else {
                echo json_encode(["res" => false, "msg" => "Ocurrió un error al eliminar la carta"]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "ID de carta no proporcionado"]);
        }
    }

    // Método para generar PDF
    public function generarPDF()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->cartaPDF->generarCartaPDF($id);
        } else {
            echo "ID de carta no proporcionado";
        }
    }

    public function vistaPreviaPDF()
{
    if (!empty($_POST)) {
        try {
            // Validar datos
            $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : 'Vista Previa';
            $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
            
            // Si no hay contenido, usar el de la plantilla
            if (empty($contenido)) {
                $this->cartaTemplate->obtenerTemplateActual();
                $contenido = $this->cartaTemplate->getContenido();
                $titulo = $this->cartaTemplate->getTitulo();
            }
            
            // Procesar imágenes
            $header_image = null;
            $footer_image = null;
            
            // Obtener imágenes de la plantilla actual
            $this->cartaTemplate->obtenerTemplateActual();
            
            if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                $header_image = $this->procesarImagen($_FILES['header_image']);
            } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                $header_image = $_POST['header_image'];
            } else {
                $header_image = $this->cartaTemplate->getHeaderImageUrl();
            }
            
            if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                $footer_image = $this->procesarImagen($_FILES['footer_image']);
            } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                $footer_image = $_POST['footer_image'];
            } else {
                $footer_image = $this->cartaTemplate->getFooterImageUrl();
            }
            
            // Generar vista previa
            $pdfBase64 = $this->cartaPDF->generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image);
            
            echo json_encode([
                'success' => true,
                'pdfBase64' => $pdfBase64
            ]);
            
        } catch (Exception $e) {
            error_log("Error en vistaPreviaPDF: " . $e->getMessage());
            echo json_encode(['success' => false, 'msg' => 'Error al generar vista previa: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'msg' => 'No se recibieron datos']);
    }
}
    // Método para obtener la plantilla actual
    public function obtenerTemplate()
    {
        try {
            $this->cartaTemplate->obtenerTemplateActual();
            
            $data = [
                'success' => true,
                'data' => [
                    'id' => $this->cartaTemplate->getId(),
                    'titulo' => $this->cartaTemplate->getTitulo(),
                    'contenido' => $this->cartaTemplate->getContenido(),
                    'header_image' => $this->cartaTemplate->getHeaderImage(),
                    'footer_image' => $this->cartaTemplate->getFooterImage(),
                    'header_image_url' => $this->cartaTemplate->getHeaderImageUrl(),
                    'footer_image_url' => $this->cartaTemplate->getFooterImageUrl()
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
                $this->cartaTemplate->obtenerTemplateActual();
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->cartaTemplate->getHeaderImage();
                $footer_image = $this->cartaTemplate->getFooterImage();
                
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
                $this->cartaTemplate->setTitulo($titulo);
                $this->cartaTemplate->setContenido($contenido);
                $this->cartaTemplate->setHeaderImage($header_image);
                $this->cartaTemplate->setFooterImage($footer_image);
                
                // Si se proporciona un ID, actualizar la plantilla existente
                if (!empty($_POST['id'])) {
                    $this->cartaTemplate->setId($_POST['id']);
                    $resultado = $this->cartaTemplate->actualizarTemplate();
                } else {
                    $resultado = $this->cartaTemplate->insertarTemplate();
                }
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'mensaje' => 'Plantilla guardada correctamente',
                        'id' => $this->cartaTemplate->getId()
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

    // Método para obtener tipos de cartas para filtrado
    public function getTipos()
    {
        try {
            $tipos = $this->carta->obtenerTiposCartas();
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
    // Método para obtener membretes
public function obtenerMembretes()
{
    try {
        // Obtener la plantilla actual que contiene los membretes
        $this->cartaTemplate->obtenerTemplateActual();
        
        $data = [
            'success' => true,
            'data' => [
                'header_image' => $this->cartaTemplate->getHeaderImage(),
                'footer_image' => $this->cartaTemplate->getFooterImage(),
                'header_image_url' => $this->cartaTemplate->getHeaderImageUrl(),
                'footer_image_url' => $this->cartaTemplate->getFooterImageUrl()
            ]
        ];
        
        echo json_encode($data);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
public function guardarMembretes()
{
    if (!empty($_POST) || !empty($_FILES)) {
        try {
            // Obtener la plantilla actual
            if (!$this->cartaTemplate->obtenerTemplateActual()) {
                throw new Exception("No se pudo obtener la plantilla actual");
            }
            
            // Mantener valores actuales como respaldo
            $header_image = $this->cartaTemplate->getHeaderImage();
            $footer_image = $this->cartaTemplate->getFooterImage();
            
            // Verificar archivos de imagen PRIMERO
            if (isset($_FILES['header_image_file']) && $_FILES['header_image_file']['error'] === UPLOAD_ERR_OK) {
                $header_image = $this->procesarImagen($_FILES['header_image_file']);
                error_log("Nueva imagen de cabecera procesada desde archivo");
            } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                $header_image = $_POST['header_image'];
                error_log("Nueva imagen de cabecera desde POST data");
            }
            
            if (isset($_FILES['footer_image_file']) && $_FILES['footer_image_file']['error'] === UPLOAD_ERR_OK) {
                $footer_image = $this->procesarImagen($_FILES['footer_image_file']);
                error_log("Nueva imagen de pie procesada desde archivo");
            } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                $footer_image = $_POST['footer_image'];
                error_log("Nueva imagen de pie desde POST data");
            }
            
            // Actualizar solo las imágenes de la plantilla
            $this->cartaTemplate->setHeaderImage($header_image);
            $this->cartaTemplate->setFooterImage($footer_image);
            
            // Guardar la plantilla actualizada
            $resultado = $this->cartaTemplate->actualizarTemplate();
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'mensaje' => 'Membretes guardados correctamente'
                ]);
            } else {
                throw new Exception("Error al actualizar la plantilla en la base de datos");
            }
            
        } catch (Exception $e) {
            error_log("Error en guardarMembretes: " . $e->getMessage());
            echo json_encode(['success' => false, 'msg' => 'Error al guardar los membretes: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'msg' => 'No se recibieron datos']);
    }
}
public function obtenerTiposCartas()
{
    try {
        $tipos = $this->tipoCarta->obtenerTodos();
        echo json_encode(['success' => true, 'tipos' => $tipos]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Método para insertar tipo de carta
public function insertarTipoCarta()
{
    if (!empty($_POST)) {
        try {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            
            if (empty($nombre)) {
                throw new Exception("El nombre del tipo es obligatorio");
            }
            
            $this->tipoCarta->setNombre($nombre);
            
            if ($this->tipoCarta->insertar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de carta creado correctamente']);
            } else {
                throw new Exception("Error al guardar el tipo de carta");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}

// Método para editar tipo de carta
public function editarTipoCarta()
{
    if (!empty($_POST)) {
        try {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            
            if (empty($id) || empty($nombre)) {
                throw new Exception("ID y nombre son obligatorios");
            }
            
            $this->tipoCarta->setId($id);
            $this->tipoCarta->setNombre($nombre);
            
            if ($this->tipoCarta->actualizar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de carta actualizado correctamente']);
            } else {
                throw new Exception("Error al actualizar el tipo de carta");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}

// Método para eliminar tipo de carta
public function eliminarTipoCarta()
{
    if (isset($_POST['id'])) {
        try {
            $id = intval($_POST['id']);
            $this->tipoCarta->setId($id);
            
            if ($this->tipoCarta->eliminar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de carta eliminado correctamente']);
            } else {
                throw new Exception("Error al eliminar el tipo de carta");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}
}