<?php

require_once "app/models/Informe.php";
require_once "app/models/InformeTemplate.php";
require_once "app/models/TipoInforme.php";
require_once "app/http/controllers/InformePDF.php";

class InformeController extends Controller
{
    private $informe;
    private $informeTemplate;
    private $informePDF;
    private $tipoInforme;
    private $conectar;

    public function __construct()
    {
        $this->informe = new Informe();
        $this->informeTemplate = new InformeTemplate();
        $this->informePDF = new InformePDF();
        $this->tipoInforme = new TipoInforme();
        $this->conectar = (new Conexion())->getConexion();
    }

    // Método para obtener todos los informes (con filtro opcional)
public function render()
{
    try {
        // Verificar que las tablas necesarias existan
        if (!$this->informe->verificarTablas()) {
            echo json_encode([
                'error' => true,
                'message' => 'Error: Faltan tablas en la base de datos. Verifique la instalación.'
            ]);
            return;
        }
        
        // Obtener parámetros de filtro
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
        $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
        
        // Intentar obtener los datos (sin comentarios de depuración)
        $informes = $this->informe->getAllData($filtro, $tipo_busqueda);
        
        // Devolver los datos en formato JSON
        echo json_encode($informes ?: []);
    } catch (Exception $e) {
        echo json_encode([
            'error' => true,
            'message' => 'Error al procesar la solicitud',
            'debug_info' => $e->getMessage()
        ]);
    }
}

    // Método para obtener un informe específico
    public function getOne()
    {
        if (isset($_POST['id_informe'])) {
            $id = $_POST['id_informe'];
            
            $this->informe->setIdInforme($id);
            if ($this->informe->obtenerInforme()) {
                $data = [
                    'id_informe' => $this->informe->getIdInforme(),
                    'tipo' => $this->informe->getTipo(),
                    'titulo' => $this->informe->getTitulo(),
                    'contenido' => $this->informe->getContenido(),
                    'header_image' => $this->informe->getHeaderImage(),
                    'footer_image' => $this->informe->getFooterImage(),
                    'cliente_id' => $this->informe->getClienteId(),
                    'cliente_nombre' => $this->informe->getClienteNombre(),
                    'usuario_id' => $this->informe->getUsuarioId()
                ];
                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'Informe no encontrado']);
            }
        } else {
            echo json_encode(['error' => 'ID de informe no proporcionado']);
        }
    }

    // Método para insertar un nuevo informe
    public function insertar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($tipo) || empty($titulo) || empty($contenido)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = null;
                $footer_image = null;
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image_base64']) && !empty($_POST['header_image_base64'])) {
                    $header_image = $_POST['header_image_base64'];
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image_base64']) && !empty($_POST['footer_image_base64'])) {
                    $footer_image = $_POST['footer_image_base64'];
                }
                
                // Configurar el objeto informe
                $this->informe->setTipo($tipo);
                $this->informe->setTitulo($titulo);
                $this->informe->setContenido($contenido);
                $this->informe->setHeaderImage($header_image);
                $this->informe->setFooterImage($footer_image);
                $this->informe->setClienteId($cliente_id);
                $this->informe->setUsuarioId($_SESSION['usuario_id'] ?? 1); // Asumiendo que hay una sesión de usuario
                
                // Insertar el informe
                if ($this->informe->insertar()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => 'Informe creado correctamente',
                        'id_informe' => $this->informe->getIdInforme()
                    ]);
                } else {
                    throw new Exception("Error al guardar el informe en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para editar un informe existente
    public function editar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $id_informe = isset($_POST['id_informe']) ? intval($_POST['id_informe']) : 0;
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($id_informe) || empty($tipo) || empty($titulo) || empty($contenido)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Obtener el informe actual
                $this->informe->setIdInforme($id_informe);
                if (!$this->informe->obtenerInforme()) {
                    throw new Exception("El informe no existe");
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->informe->getHeaderImage();
                $footer_image = $this->informe->getFooterImage();
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image_base64']) && !empty($_POST['header_image_base64'])) {
                    $header_image = $_POST['header_image_base64'];
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image_base64']) && !empty($_POST['footer_image_base64'])) {
                    $footer_image = $_POST['footer_image_base64'];
                }
                
                // Configurar el objeto informe
                $this->informe->setTipo($tipo);
                $this->informe->setTitulo($titulo);
                $this->informe->setContenido($contenido);
                $this->informe->setHeaderImage($header_image);
                $this->informe->setFooterImage($footer_image);
                $this->informe->setClienteId($cliente_id);
                $this->informe->setUsuarioId($_SESSION['usuario_id'] ?? 1); // Asumiendo que hay una sesión de usuario
                
                // Actualizar el informe
                if ($this->informe->editar()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => 'Informe actualizado correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar el informe en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para eliminar un informe
    public function borrar()
    {
        if (isset($_POST["id_informe"])) {
            $id_informe = intval($_POST["id_informe"]);
            
            $this->informe->setIdInforme($id_informe);
            if ($this->informe->delete()) {
                echo json_encode(["res" => true, "msg" => "Informe eliminado correctamente"]);
            } else {
                echo json_encode(["res" => false, "msg" => "Ocurrió un error al eliminar el informe"]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "ID de informe no proporcionado"]);
        }
    }

    // Método para generar PDF
    public function generarPDF()
    {
        if (isset($_GET['id'])) {
            $id_informe = intval($_GET['id']);
            $this->informePDF->generarInformePDF($id_informe);
        } else {
            echo "ID de informe no proporcionado";
        }
    }

    // Método para obtener la plantilla actual
    public function obtenerTemplate()
    {
        try {
            $this->informeTemplate->obtenerTemplateActual();
            
            $data = [
                'success' => true,
                'titulo' => $this->informeTemplate->getTitulo(),
                'contenido' => $this->informeTemplate->getContenido(),
                'header_image' => $this->informeTemplate->getHeaderImageUrl(),
                'footer_image' => $this->informeTemplate->getFooterImageUrl()
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
                $this->informeTemplate->obtenerTemplateActual();
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->informeTemplate->getHeaderImage();
                $footer_image = $this->informeTemplate->getFooterImage();
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                }
                
                // Configurar el objeto template
                $this->informeTemplate->setTitulo($titulo);
                $this->informeTemplate->setContenido($contenido);
                $this->informeTemplate->setHeaderImage($header_image);
                $this->informeTemplate->setFooterImage($footer_image);
                
                // Actualizar la plantilla
                if ($this->informeTemplate->actualizarTemplate()) {
                    echo json_encode([
                        'success' => true, 
                        'msg' => 'Plantilla guardada correctamente',
                        'header_image' => $this->informeTemplate->getHeaderImageUrl(),
                        'footer_image' => $this->informeTemplate->getFooterImageUrl()
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
                } else if (isset($_POST['header_image_base64']) && !empty($_POST['header_image_base64'])) {
                    $header_image = $_POST['header_image_base64'];
                } else {
                    // Usar la imagen de la plantilla
                    $this->informeTemplate->obtenerTemplateActual();
                    $header_image = $this->informeTemplate->getHeaderImageUrl();
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image_base64']) && !empty($_POST['footer_image_base64'])) {
                    $footer_image = $_POST['footer_image_base64'];
                } else {
                    // Usar la imagen de la plantilla
                    if (!$this->informeTemplate->getId()) {
                        $this->informeTemplate->obtenerTemplateActual();
                    }
                    $footer_image = $this->informeTemplate->getFooterImageUrl();
                }
                
                // Generar vista previa
                $pdfBase64 = $this->informePDF->generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image);
                
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

    // Método para obtener tipos de informes para filtrado
    public function getTipos()
    {
        try {
            $tipos = $this->informe->getTiposInforme();
            echo json_encode(['success' => true, 'tipos' => $tipos]);
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
    // Nuevos métodos para gestionar tipos de informe
public function obtenerTiposInforme()
{
    try {
        $tipos = $this->tipoInforme->obtenerTodos();
        echo json_encode(['success' => true, 'tipos' => $tipos]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

public function insertarTipoInforme()
{
    if (!empty($_POST)) {
        try {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            
            if (empty($nombre)) {
                throw new Exception("El nombre del tipo es obligatorio");
            }
            
            $this->tipoInforme->setNombre($nombre);
            // $this->tipoInforme->setDescripcion($descripcion);
            
            if ($this->tipoInforme->insertar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de informe creado correctamente']);
            } else {
                throw new Exception("Error al guardar el tipo de informe");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}

public function editarTipoInforme()
{
    if (!empty($_POST)) {
        try {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            
            if (empty($id) || empty($nombre)) {
                throw new Exception("ID y nombre son obligatorios");
            }
            
            $this->tipoInforme->setId($id);
            $this->tipoInforme->setNombre($nombre);
            // $this->tipoInforme->setDescripcion($descripcion);
            
            if ($this->tipoInforme->actualizar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de informe actualizado correctamente']);
            } else {
                throw new Exception("Error al actualizar el tipo de informe");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}

public function eliminarTipoInforme()
{
    if (isset($_POST['id'])) {
        try {
            $id = intval($_POST['id']);
            $this->tipoInforme->setId($id);
            
            if ($this->tipoInforme->eliminar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de informe eliminado correctamente']);
            } else {
                throw new Exception("Error al eliminar el tipo de informe");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}

}