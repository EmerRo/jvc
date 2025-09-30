<?php
// app/controllers/BaseDocumentoController.php

abstract class BaseDocumentoController extends Controller
{
    protected $modelo;
    protected $plantilla;
    protected $pdfGenerator;
    protected $tipoModelo;
    protected $conectar;
    protected $documentType; // 'carta' o 'constancia', archivos internos

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        try {
            // Obtener parámetros de filtro
            $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
            $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
            
            // Intentar obtener los datos
            $documentos = $this->modelo->listarDocumentos($filtro, $tipo_busqueda);
            
            // Devolver los datos en formato JSON
            $response = [$this->documentType . 's' => $documentos ?: []];
            echo json_encode($response);
        } catch (Exception $e) {
            error_log("Error en " . get_class($this) . "::render(): " . $e->getMessage());
            echo json_encode([
                'error' => true,
                'message' => 'Error al procesar la solicitud',
                'debug_info' => $e->getMessage(),
                $this->documentType . 's' => []
            ]);
        }
    }

    public function getOne()
    {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }
        
        if ($this->modelo->obtenerDocumento($id)) {
            $respuesta = [
                'success' => true,
                'data' => [
                    'id' => $this->modelo->getId(),
                    'id_cliente' => $this->modelo->getIdCliente(),
                    'id_usuario' => $this->modelo->getUsuarioId(),
                    'tipo' => $this->modelo->getTipo(),
                    'titulo' => $this->modelo->getTitulo(),
                    'contenido' => $this->modelo->getContenido(),
                    'header_image' => $this->modelo->getHeaderImage(),
                    'footer_image' => $this->modelo->getFooterImage(),
                    'header_image_url' => $this->modelo->getHeaderImageUrl(),
                    'footer_image_url' => $this->modelo->getFooterImageUrl(),
                    'cliente_nombre' => $this->modelo->getClienteNombre(),
                    'cliente_documento' => $this->modelo->getClienteDocumento(),
                    'cliente_direccion' => $this->modelo->getClienteDireccion(),
                    'estado' => $this->modelo->getEstado()
                ]
            ];
            
            echo json_encode($respuesta);
            return;
        }
        
        echo json_encode(['success' => false, 'error' => ucfirst($this->documentType) . ' no encontrada']);
    }

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
                        throw new Exception("No hay usuarios en la base de datos para asignar al " . $this->documentType);
                    }
                }
                
                // Configurar el objeto documento
                $this->modelo->setTipo($tipo);
                $this->modelo->setTitulo($titulo);
                $this->modelo->setContenido($contenido);
                $this->modelo->setHeaderImage($header_image);
                $this->modelo->setFooterImage($footer_image);
                $this->modelo->setIdCliente($id_cliente);
                $this->modelo->setUsuarioId($usuario_id);
                $this->modelo->setEstado('borrador');
                
                // Insertar el documento
                if ($this->modelo->insertarDocumento()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => ucfirst($this->documentType) . ' creada correctamente',
                        'id' => $this->modelo->getId()
                    ]);
                } else {
                    throw new Exception("Error al guardar el " . $this->documentType . " en la base de datos: Operación fallida");
                }
                
            } catch (Exception $e) {
                echo json_encode([
                    'res' => false, 
                    'msg' => "Error al guardar el " . $this->documentType . ": " . $e->getMessage(),
                    'debug' => [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ]);
                
                error_log("Error en " . get_class($this) . "::insertar: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

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
                
                // Obtener el documento actual
                $this->modelo->setId($id);
                if (!$this->modelo->obtenerDocumento($id)) {
                    throw new Exception("El " . $this->documentType . " no existe");
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->modelo->getHeaderImage();
                $footer_image = $this->modelo->getFooterImage();
                
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
                
                // Configurar el objeto documento
                $this->modelo->setTipo($tipo);
                $this->modelo->setTitulo($titulo);
                $this->modelo->setContenido($contenido);
                $this->modelo->setHeaderImage($header_image);
                $this->modelo->setFooterImage($footer_image);
                $this->modelo->setIdCliente($id_cliente);
                $this->modelo->setEstado($estado);
                
                // Actualizar el documento
                if ($this->modelo->actualizarDocumento()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => ucfirst($this->documentType) . ' actualizada correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar el " . $this->documentType . " en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    public function borrar()
    {
        if (isset($_POST["id"])) {
            $id = intval($_POST["id"]);
            
            if ($this->modelo->eliminarDocumento($id)) {
                echo json_encode(["res" => true, "msg" => ucfirst($this->documentType) . " eliminada correctamente"]);
            } else {
                echo json_encode(["res" => false, "msg" => "Ocurrió un error al eliminar el " . $this->documentType]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "ID de " . $this->documentType . " no proporcionado"]);
        }
    }

    public function generarPDF()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $methodName = 'generar' . ucfirst($this->documentType) . 'PDF';
            $this->pdfGenerator->$methodName($id);
        } else {
            echo "ID de " . $this->documentType . " no proporcionado";
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
                    $this->plantilla->obtenerTemplateActual();
                    $contenido = $this->plantilla->getContenido();
                    $titulo = $this->plantilla->getTitulo();
                }
                
                // Procesar imágenes
                $header_image = null;
                $footer_image = null;
                
                // Obtener imágenes de la plantilla actual
                $this->plantilla->obtenerTemplateActual();
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                    $header_image = $_POST['header_image'];
                } else {
                    $header_image = $this->plantilla->getHeaderImageUrl();
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                    $footer_image = $_POST['footer_image'];
                } else {
                    $footer_image = $this->plantilla->getFooterImageUrl();
                }
                
                // Generar vista previa
                $pdfBase64 = $this->pdfGenerator->generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image);
                
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

    public function obtenerTemplate()
    {
        try {
            $this->plantilla->obtenerTemplateActual();
            
            $data = [
                'success' => true,
                'data' => [
                    'id' => $this->plantilla->getId(),
                    'titulo' => $this->plantilla->getTitulo(),
                    'contenido' => $this->plantilla->getContenido(),
                    'header_image' => $this->plantilla->getHeaderImage(),
                    'footer_image' => $this->plantilla->getFooterImage(),
                    'header_image_url' => $this->plantilla->getHeaderImageUrl(),
                    'footer_image_url' => $this->plantilla->getFooterImageUrl()
                ]
            ];
            
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

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
                $this->plantilla->obtenerTemplateActual();
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->plantilla->getHeaderImage();
                $footer_image = $this->plantilla->getFooterImage();
                
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
                $this->plantilla->setTitulo($titulo);
                $this->plantilla->setContenido($contenido);
                $this->plantilla->setHeaderImage($header_image);
                $this->plantilla->setFooterImage($footer_image);
                
                // Si se proporciona un ID, actualizar la plantilla existente
                if (!empty($_POST['id'])) {
                    $this->plantilla->setId($_POST['id']);
                    $resultado = $this->plantilla->actualizarTemplate();
                } else {
                    $resultado = $this->plantilla->insertarTemplate();
                }
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'mensaje' => 'Plantilla guardada correctamente',
                        'id' => $this->plantilla->getId()
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

    public function getTipos()
    {
        try {
            $tipos = $this->modelo->obtenerTiposDocumentos();
            echo json_encode(['success' => true, 'data' => $tipos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function obtenerMembretes()
    {
        try {
            // Obtener la plantilla actual que contiene los membretes
            $this->plantilla->obtenerTemplateActual();
            
            $data = [
                'success' => true,
                'data' => [
                    'header_image' => $this->plantilla->getHeaderImage(),
                    'footer_image' => $this->plantilla->getFooterImage(),
                    'header_image_url' => $this->plantilla->getHeaderImageUrl(),
                    'footer_image_url' => $this->plantilla->getFooterImageUrl()
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
                if (!$this->plantilla->obtenerTemplateActual()) {
                    throw new Exception("No se pudo obtener la plantilla actual");
                }
                
                // Mantener valores actuales como respaldo
                $header_image = $this->plantilla->getHeaderImage();
                $footer_image = $this->plantilla->getFooterImage();
                
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
                $this->plantilla->setHeaderImage($header_image);
                $this->plantilla->setFooterImage($footer_image);
                
                // Guardar la plantilla actualizada
                $resultado = $this->plantilla->actualizarTemplate();
                
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

    // Métodos para tipos de documentos
    public function obtenerTiposDocumentos()
    {
        try {
            $tipos = $this->tipoModelo->obtenerTodos();
            echo json_encode(['success' => true, 'tipos' => $tipos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function insertarTipoDocumento()
    {
        if (!empty($_POST)) {
            try {
                $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                
                if (empty($nombre)) {
                    throw new Exception("El nombre del tipo es obligatorio");
                }
                
                $this->tipoModelo->setNombre($nombre);
                
                if ($this->tipoModelo->insertar()) {
                    echo json_encode(['success' => true, 'msg' => 'Tipo de ' . $this->documentType . ' creado correctamente']);
                } else {
                    throw new Exception("Error al guardar el tipo de " . $this->documentType);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
            }
        }
    }

    public function editarTipoDocumento()
    {
        if (!empty($_POST)) {
            try {
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                
                if (empty($id) || empty($nombre)) {
                    throw new Exception("ID y nombre son obligatorios");
                }
                
                $this->tipoModelo->setId($id);
                $this->tipoModelo->setNombre($nombre);
                
                if ($this->tipoModelo->actualizar()) {
                    echo json_encode(['success' => true, 'msg' => 'Tipo de ' . $this->documentType . ' actualizado correctamente']);
                } else {
                    throw new Exception("Error al actualizar el tipo de " . $this->documentType);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
            }
        }
    }

    public function eliminarTipoDocumento()
    {
        if (isset($_POST['id'])) {
            try {
                $id = intval($_POST['id']);
                $this->tipoModelo->setId($id);
                
                if ($this->tipoModelo->eliminar()) {
                    echo json_encode(['success' => true, 'msg' => 'Tipo de ' . $this->documentType . ' eliminado correctamente']);
                } else {
                    throw new Exception("Error al eliminar el tipo de " . $this->documentType);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
            }
        }
    }

    // Método auxiliar para procesar imágenes y guardarlas como archivos
    protected function procesarImagen($file)
    {
        return $this->procesarImagenEnDirectorio($file, 'files/' . $this->documentType . 's/');
    }
    
    // Método genérico para procesar imágenes en cualquier directorio (similar a InformeController)
    protected function procesarImagenEnDirectorio($file, $uploadDir)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG, GIF y WebP.");
        }
        
        // Verificar tamaño del archivo (máximo 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            throw new Exception("El archivo es demasiado grande. El tamaño máximo permitido es 10MB.");
        }
        
        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("No se pudo crear el directorio de imágenes: $uploadDir");
            }
        }
        
        // Generar nombre único para el archivo
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        $rutaCompleta = $uploadDir . $nombreArchivo;
        
        // Optimizar y guardar la imagen
        $imagenOptimizada = $this->optimizarImagenDocumento($file);
        
        // Guardar la imagen optimizada
        if (!file_put_contents($rutaCompleta, $imagenOptimizada)) {
            throw new Exception("No se pudo guardar la imagen.");
        }
        
        // Retornar solo la ruta relativa
        return $rutaCompleta;
    }
    
    /**
     * Optimiza una imagen para documentos manteniendo buena calidad
     */
    protected function optimizarImagenDocumento($file)
    {
        // Leer y analizar la imagen
        $imageData = file_get_contents($file['tmp_name']);
        $image = imagecreatefromstring($imageData);
        
        if ($image === false) {
            throw new Exception("No se pudo procesar la imagen.");
        }
        
        // Aplicar mejoras de calidad
        $image = $this->aplicarFiltrosCalidad($image);
        
        // Obtener dimensiones
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Redimensionar solo si es muy grande
        $maxWidth = 1200;
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
    protected function aplicarFiltrosCalidad($image)
    {
        // Aplicar filtro de nitidez muy suave
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

    // Método para generar PDF como base64 (para vista previa)
    public function generarPDFBase64()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $methodName = 'generar' . ucfirst($this->documentType) . 'PDFBase64';
            $pdfBase64 = $this->pdfGenerator->$methodName($id);
            
            // Devolver como JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'pdfBase64' => $pdfBase64
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'ID de ' . $this->documentType . ' no proporcionado'
            ]);
        }
    }

    // Método genérico para compartir por WhatsApp
    public function compartirWhatsApp()
    {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        try {
            // Validar que lleguen los parámetros necesarios
            $idKey = 'id_' . $this->documentType;
            if (!isset($_POST['id']) && !isset($_POST[$idKey])) {
                throw new Exception("ID de " . $this->documentType . " no proporcionado");
            }
            
            if (!isset($_POST['numero'])) {
                throw new Exception("Número de teléfono no proporcionado");
            }

            $id_documento = $_POST[$idKey] ?? $_POST['id'];
            $telefono = $_POST['numero'];

            // Validar el número de teléfono
            if (!preg_match('/^[0-9]{9}$/', $telefono)) {
                throw new Exception("Número de teléfono inválido");
            }

            // Obtener el documento usando consulta directa
            $tabla = $this->documentType . 's';
            $sql = "SELECT d.*, c.datos as cliente_nombre, c.documento as cliente_documento 
                    FROM {$tabla} d 
                    LEFT JOIN clientes c ON d.id_cliente = c.id_cliente 
                    WHERE d.id = ?";
            
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conectar->error);
            }
            
            $stmt->bind_param("i", $id_documento);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $documento = $resultado->fetch_assoc();
            $stmt->close();

            if (!$documento) {
                throw new Exception(ucfirst($this->documentType) . " no encontrado");
            }

            // Generar URL del PDF del documento
            $urlPDF = URL::to("ajs/{$this->documentType}/generarPDF?id=$id_documento");

            // Construir mensaje de WhatsApp
            $tipoDocumento = strtoupper($this->documentType);
            $mensaje = "📋 *" . $tipoDocumento . "*\n\n";
            $mensaje .= "📄 *" . $documento['titulo'] . "*\n\n";
            $mensaje .= "🗂️ *Tipo:* " . ($documento['tipo'] ?: 'General') . "\n\n";
            
            if ($documento['id_cliente']) {
                $mensaje .= "👤 *Cliente:* " . ($documento['cliente_nombre'] ?: 'Cliente') . "\n\n";
            }
            
            $mensaje .= "📅 *Fecha:* " . date('d/m/Y', strtotime($documento['fecha_creacion'])) . "\n\n";
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