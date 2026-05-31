<?php

require_once "app/models/Informe.php";
require_once "app/models/InformeTemplate.php";
require_once "app/models/TipoInforme.php";
require_once "app/http/controllers/InformePDF.php";
require_once 'app/helpers/ImageStorage.php';

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

    // Método para obtener un informe específico CON imágenes (para edición)
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
                    'imagen1' => $this->informe->getImagen1Url(), // Usar URL en lugar de base64 para web
                    'imagen2' => $this->informe->getImagen2Url(), // Usar URL en lugar de base64 para web
                    'cliente_id' => $this->informe->getClienteId(),
                    'persona_entregar' => $this->informe->getPersonaEntregar(),
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
                $persona_entregar = isset($_POST['persona_entregar']) ? trim($_POST['persona_entregar']) : '';
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($tipo) || empty($titulo) || empty($contenido)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Validar que el cliente sea requerido
                $this->informe->validarClienteRequerido($cliente_id);
                
                // Procesar imágenes si se proporcionan
                $imagen1 = null;
                $imagen2 = null;
                
                if (isset($_FILES['imagen1']) && $_FILES['imagen1']['error'] === UPLOAD_ERR_OK) {
                    $imagen1 = $this->procesarImagen($_FILES['imagen1']);
                } else if (isset($_POST['imagen1_base64']) && !empty($_POST['imagen1_base64'])) {
                    $imagen1 = $_POST['imagen1_base64'];
                }

                if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] === UPLOAD_ERR_OK) {
                    $imagen2 = $this->procesarImagen($_FILES['imagen2']);
                } else if (isset($_POST['imagen2_base64']) && !empty($_POST['imagen2_base64'])) {
                    $imagen2 = $_POST['imagen2_base64'];
                }
                
                // Configurar el objeto informe
                $this->informe->setTipo($tipo);
                $this->informe->setTitulo($titulo);
                $this->informe->setContenido($contenido);
                $this->informe->setImagen1($imagen1);
                $this->informe->setImagen2($imagen2);
                $this->informe->setClienteId($cliente_id);
                $this->informe->setPersonaEntregar($persona_entregar);
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
                $persona_entregar = isset($_POST['persona_entregar']) ? trim($_POST['persona_entregar']) : '';
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($id_informe) || empty($tipo) || empty($titulo) || empty($contenido)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Validar que el cliente sea requerido
                $this->informe->validarClienteRequerido($cliente_id);
                
                // Obtener el informe actual
                $this->informe->setIdInforme($id_informe);
                if (!$this->informe->obtenerInforme()) {
                    throw new Exception("El informe no existe");
                }
                
                // Procesar imágenes si se proporcionan
                $imagen1 = $this->informe->getImagen1();
                $imagen2 = $this->informe->getImagen2();
                
                if (isset($_FILES['imagen1']) && $_FILES['imagen1']['error'] === UPLOAD_ERR_OK) {
                    $imagen1 = $this->procesarImagen($_FILES['imagen1']);
                } else if (isset($_POST['imagen1_base64']) && !empty($_POST['imagen1_base64'])) {
                    $imagen1 = $_POST['imagen1_base64'];
                }
                
                if (isset($_FILES['imagen2']) && $_FILES['imagen2']['error'] === UPLOAD_ERR_OK) {
                    $imagen2 = $this->procesarImagen($_FILES['imagen2']);
                } else if (isset($_POST['imagen2_base64']) && !empty($_POST['imagen2_base64'])) {
                    $imagen2 = $_POST['imagen2_base64'];
                }
                
                // Configurar el objeto informe
                $this->informe->setTipo($tipo);
                $this->informe->setTitulo($titulo);
                $this->informe->setContenido($contenido);
                $this->informe->setImagen1($imagen1);
                $this->informe->setImagen2($imagen2);
                $this->informe->setClienteId($cliente_id);
                $this->informe->setPersonaEntregar($persona_entregar);
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

    // Método para generar PDF como base64 (para vista previa)
    public function generarPDFBase64()
    {
        // Suprimir warnings/notices que rompen el JSON
        @ini_set('display_errors', '0');
        error_reporting(0);
        ob_start();

        header('Content-Type: application/json');

        if (isset($_GET['id'])) {
            try {
                $id_informe = intval($_GET['id']);
                ob_clean();
                $pdfBase64 = $this->informePDF->generarInformePDFBase64($id_informe);
                ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'pdfBase64' => $pdfBase64
                ]);
            } catch (Exception $e) {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'error' => 'ID de informe no proporcionado'
            ]);
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
                    // Eliminar imagen anterior si existe
                    $this->eliminarImagenAnterior($header_image);
                    $header_image = $this->procesarImagenMembrete($_FILES['header_image']);
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    // Eliminar imagen anterior si existe  
                    $this->eliminarImagenAnterior($footer_image);
                    $footer_image = $this->procesarImagenMembrete($_FILES['footer_image']);
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
                    $header_image = $this->procesarImagenMembrete($_FILES['header_image']);
                } else if (isset($_POST['header_image_base64']) && !empty($_POST['header_image_base64'])) {
                    $header_image = $_POST['header_image_base64'];
                } else {
                    // Usar la imagen de la plantilla
                    $this->informeTemplate->obtenerTemplateActual();
                    $header_image = $this->informeTemplate->getHeaderImageUrl();
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagenMembrete($_FILES['footer_image']);
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

    // Método auxiliar para procesar imágenes (usado para imágenes del informe)
    private function procesarImagen($file)
    {
        return ImageStorage::save($file, 'informes');
    }
    
    // Método auxiliar para procesar imágenes de membretes
    private function procesarImagenMembrete($file)
    {
        return ImageStorage::save($file, 'informes');
    }
    
    // Método genérico para procesar imágenes en cualquier directorio (mantenido para compatibilidad)
    private function procesarImagenEnDirectorio($file, $uploadDir)
    {
        return ImageStorage::save($file, 'informes');
    }
    
    /**
     * Elimina una imagen anterior del sistema de archivos
     */
    private function eliminarImagenAnterior($filename)
    {
        if (!$filename) {
            return;
        }
        
        // No eliminar si es una imagen base64 o URL externa
        if (strpos($filename, 'data:image/') === 0 || strpos($filename, 'http') === 0) {
            return;
        }
        
        // No eliminar imágenes por defecto del sistema
        if (strpos($filename, 'public/img/garantia/') !== false) {
            return;
        }
        
        // Extraer solo el nombre de archivo (soporta tanto rutas viejas como nuevos nombres)
        $basename = basename($filename);
        
        // Eliminar si es un archivo de informes (nuevo formato: solo filename, o viejo formato: ruta files/informes/)
        if (preg_match('/^\d+_[a-f0-9]+\.(jpg|jpeg|png|gif)$/i', $basename) ||
            strpos($filename, 'files/informes/') === 0) {
            // Intentar eliminar vía ImageStorage (nuevo storage)
            ImageStorage::delete('informes', $basename);
            // Fallback: si el archivo todavía existe en la ubicación antigua, eliminarlo
            if (strpos($filename, 'files/informes/') === 0 && file_exists($filename)) {
                @unlink($filename);
            }
        }
    }
    
    /**
     * Optimiza una imagen para informes manteniendo buena calidad
     */
    private function optimizarImagenInforme($file)
    {
        // Leer y analizar la imagen
        $imageData = file_get_contents($file['tmp_name']);
        $image = imagecreatefromstring($imageData);
        
        if ($image === false) {
            throw new Exception("No se pudo procesar la imagen.");
        }
        
        // Aplicar mejoras de calidad
        $image = $this->aplicarFiltrosCalidadInforme($image);
        
        // Obtener dimensiones
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Redimensionar solo si es muy grande (para informes puede ser más grande)
        $maxWidth = 1000;
        $maxHeight = 800;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            // Calcular ratio manteniendo proporción
            $ratioW = $maxWidth / $width;
            $ratioH = $maxHeight / $height;
            $ratio = min($ratioW, $ratioH);
            
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
            
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            
            // Mantener transparencia para PNG
            if ($file['type'] === 'image/png') {
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefill($resizedImage, 0, 0, $transparent);
            } else {
                $white = imagecolorallocate($resizedImage, 255, 255, 255);
                imagefill($resizedImage, 0, 0, $white);
            }
            
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resizedImage;
        }
        
        // Generar imagen optimizada
        ob_start();
        if ($file['type'] === 'image/png') {
            imagepng($image, null, 6); // Compresión PNG nivel 6
        } else {
            imagejpeg($image, null, 88); // Alta calidad JPEG
        }
        $optimizedData = ob_get_clean();
        
        imagedestroy($image);
        
        return $optimizedData;
    }
    
    /**
     * Aplica filtros para mejorar la calidad de la imagen
     */
    private function aplicarFiltrosCalidadInforme($image)
    {
        // Aplicar filtro de nitidez muy suave para informes
        $sharpenMatrix = array(
            array(0, -0.3, 0),
            array(-0.3, 2.2, -0.3),
            array(0, -0.3, 0)
        );
        imageconvolution($image, $sharpenMatrix, 1, 0);
        
        // Mejorar contraste muy sutilmente
        imagefilter($image, IMG_FILTER_CONTRAST, -2);
        
        return $image;
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

    public function compartirWhatsApp()
    {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        try {
            // Validar que lleguen los parámetros necesarios
            if (!isset($_POST['id']) && !isset($_POST['id_informe'])) {
                throw new Exception("ID de informe no proporcionado");
            }
            
            // El frontend envía 'numero' no 'telefono'
            if (!isset($_POST['numero'])) {
                throw new Exception("Número de teléfono no proporcionado");
            }

            $id_informe = $_POST['id_informe'] ?? $_POST['id'];
            $telefono = $_POST['numero']; // Cambié de 'telefono' a 'numero'

            // Validar el número de teléfono
            if (!preg_match('/^[0-9]{9}$/', $telefono)) {
                throw new Exception("Número de teléfono inválido");
            }

            // Obtener el informe usando consulta directa con la conexión heredada
            $sql = "SELECT i.*, c.datos as cliente_nombre, c.documento as cliente_documento 
                    FROM informes i 
                    LEFT JOIN clientes c ON i.cliente_id = c.id_cliente 
                    WHERE i.id_informe = ?";
            
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conectar->error);
            }
            
            $stmt->bind_param("i", $id_informe);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $informe = $resultado->fetch_assoc();
            $stmt->close();

            if (!$informe) {
                throw new Exception("Informe no encontrado");
            }

            // Generar URL del PDF del informe
            $urlPDF = URL::to("ajs/informe/generarPDF?id=$id_informe");

            // Construir mensaje de WhatsApp
            $mensaje = "📋 *INFORME TÉCNICO*\n\n";
            $mensaje .= "📄 *" . $informe['titulo'] . "*\n\n";
            $mensaje .= "🗂️ *Tipo:* " . ($informe['tipo'] ?: 'General') . "\n\n";
            
            if ($informe['cliente_id']) {
                $mensaje .= "👤 *Cliente:* " . ($informe['cliente_nombre'] ?: 'Cliente') . "\n\n";
            }
            
            $mensaje .= "📅 *Fecha:* " . date('d/m/Y', strtotime($informe['fecha_creacion'])) . "\n\n";
            $mensaje .= "📄 *Ver PDF:* " . $urlPDF . "\n\n";
            $mensaje .= "📱 *Compartido desde JVC*\n";
            $mensaje .= "🌐 " . $_SERVER['HTTP_HOST'];

            // Generar URL de WhatsApp
            $mensajeCodificado = urlencode($mensaje);
            $urlWhatsApp = "https://wa.me/51$telefono?text=$mensajeCodificado";

            $respuesta = [
                "res" => true,
                "whatsapp_url" => $urlWhatsApp,
                "mensaje" => $mensaje
            ];

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
    }
}