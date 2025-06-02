<?php
// app/http/controllers/OtroArchivoController.php

require_once "app/models/OtroArchivo.php";
require_once "app/models/OtroArchivoPlantilla.php";
require_once "app/http/controllers/OtroArchivoPDF.php";

class OtroArchivoController extends Controller
{
    private $otroArchivo;
    private $otroArchivoPlantilla;
    private $otroArchivoPDF;
    private $conectar;

    public function __construct()
    {
        $this->otroArchivo = new OtroArchivo();
        $this->otroArchivoPlantilla = new OtroArchivoPlantilla();
        $this->otroArchivoPDF = new OtroArchivoPDF();
        $this->conectar = (new Conexion())->getConexion();
    }

    // Método para obtener todos los archivos (con filtro opcional)
    public function render()
    {
        try {
            // Obtener parámetros de filtro
            $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
            $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
            
            // Intentar obtener los datos
            $archivos = $this->otroArchivo->listarOtrosArchivos($filtro, $tipo_busqueda);
            
            // Devolver los datos en formato JSON
            echo json_encode($archivos ?: []);
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'message' => 'Error al procesar la solicitud',
                'debug_info' => $e->getMessage()
            ]);
        }
    }

    // Método para obtener un archivo específico
    public function getOne()
    {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }
        
        if ($this->otroArchivo->obtenerOtroArchivo($id)) {
            $respuesta = [
                'success' => true,
                'data' => [
                    'id' => $this->otroArchivo->getId(),
                    'cliente_id' => $this->otroArchivo->getClienteId(),
                    'usuario_id' => $this->otroArchivo->getUsuarioId(),
                    'tipo' => $this->otroArchivo->getTipo(),
                    'motivo' => $this->otroArchivo->getMotivo(),
                    'titulo' => $this->otroArchivo->getTitulo(),
                    'contenido' => $this->otroArchivo->getContenido(),
                    'archivo_pdf' => $this->otroArchivo->getArchivoPdf(),
                    'header_image' => $this->otroArchivo->getHeaderImage(),
                    'footer_image' => $this->otroArchivo->getFooterImage(),
                    'es_pdf_subido' => $this->otroArchivo->getEsPdfSubido(),
                    'header_image_url' => $this->otroArchivo->getHeaderImageUrl(),
                    'footer_image_url' => $this->otroArchivo->getFooterImageUrl(),
                    'estado' => $this->otroArchivo->getEstado()
                ]
            ];
            
            echo json_encode($respuesta);
            return;
        }
        
        echo json_encode(['success' => false, 'error' => 'Archivo no encontrado']);
    }

    // Método para insertar un nuevo archivo
    public function insertar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
                $es_pdf_subido = isset($_POST['es_pdf_subido']) ? intval($_POST['es_pdf_subido']) : 0;
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($titulo)) {
                    throw new Exception("El título es obligatorio");
                }
                
                if (empty($tipo)) {
                    throw new Exception("El tipo es obligatorio");
                }
                
                // Procesar contenido o archivo PDF según el tipo
                $contenido = null;
                $archivo_pdf = null;
                
                if ($es_pdf_subido) {
                    // Si es un PDF subido
                    if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                        $archivo_pdf = $this->procesarPDF($_FILES['archivo_pdf']);
                    } else if (isset($_POST['archivo_pdf']) && !empty($_POST['archivo_pdf'])) {
                        $archivo_pdf = $_POST['archivo_pdf'];
                    } else {
                        throw new Exception("Debe proporcionar un archivo PDF");
                    }
                } else {
                    // Si es un documento creado
                    $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
                    if (empty($contenido)) {
                        throw new Exception("El contenido es obligatorio para documentos creados");
                    }
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
                
                // Configurar el objeto archivo
                $this->otroArchivo->setTipo($tipo);
                $this->otroArchivo->setMotivo($motivo);
                $this->otroArchivo->setTitulo($titulo);
                $this->otroArchivo->setContenido($contenido);
                $this->otroArchivo->setArchivoPdf($archivo_pdf);
                $this->otroArchivo->setHeaderImage($header_image);
                $this->otroArchivo->setFooterImage($footer_image);
                $this->otroArchivo->setClienteId($cliente_id);
                $this->otroArchivo->setUsuarioId($_SESSION['usuario_id'] ?? 1); // Asumiendo que hay una sesión de usuario
                $this->otroArchivo->setEsPdfSubido($es_pdf_subido);
                $this->otroArchivo->setEstado('activo');
                
                // Insertar el archivo
                if ($this->otroArchivo->insertarOtroArchivo()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => 'Archivo creado correctamente',
                        'id' => $this->otroArchivo->getId()
                    ]);
                } else {
                    throw new Exception("Error al guardar el archivo en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para editar un archivo existente
    public function editar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
                $es_pdf_subido = isset($_POST['es_pdf_subido']) ? intval($_POST['es_pdf_subido']) : 0;
                $estado = isset($_POST['estado']) ? $_POST['estado'] : 'activo';
                
                // Validar que los campos obligatorios no estén vacíos
                if (empty($id) || empty($titulo) || empty($tipo)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }
                
                // Obtener el archivo actual
                $this->otroArchivo->setId($id);
                if (!$this->otroArchivo->obtenerOtroArchivo($id)) {
                    throw new Exception("El archivo no existe");
                }
                
                // Procesar contenido o archivo PDF según el tipo
                $contenido = $this->otroArchivo->getContenido();
                $archivo_pdf = $this->otroArchivo->getArchivoPdf();
                
                if ($es_pdf_subido) {
                    // Si es un PDF subido
                    if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                        $archivo_pdf = $this->procesarPDF($_FILES['archivo_pdf']);
                    } else if (isset($_POST['archivo_pdf']) && !empty($_POST['archivo_pdf'])) {
                        $archivo_pdf = $_POST['archivo_pdf'];
                    }
                } else {
                    // Si es un documento creado
                    if (isset($_POST['contenido'])) {
                        $contenido = $_POST['contenido'];
                    }
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->otroArchivo->getHeaderImage();
                $footer_image = $this->otroArchivo->getFooterImage();
                
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
                
                // Configurar el objeto archivo
                $this->otroArchivo->setTipo($tipo);
                $this->otroArchivo->setMotivo($motivo);
                $this->otroArchivo->setTitulo($titulo);
                $this->otroArchivo->setContenido($contenido);
                $this->otroArchivo->setArchivoPdf($archivo_pdf);
                $this->otroArchivo->setHeaderImage($header_image);
                $this->otroArchivo->setFooterImage($footer_image);
                $this->otroArchivo->setClienteId($cliente_id);
                $this->otroArchivo->setEsPdfSubido($es_pdf_subido);
                $this->otroArchivo->setEstado($estado);
                
                // Actualizar el archivo
                if ($this->otroArchivo->actualizarOtroArchivo()) {
                    echo json_encode([
                        'res' => true, 
                        'msg' => 'Archivo actualizado correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar el archivo en la base de datos");
                }
                
            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para eliminar un archivo
    public function borrar()
    {
        if (isset($_POST["id"])) {
            $id = intval($_POST["id"]);
            
            if ($this->otroArchivo->eliminarOtroArchivo($id)) {
                echo json_encode(["res" => true, "msg" => "Archivo eliminado correctamente"]);
            } else {
                echo json_encode(["res" => false, "msg" => "Ocurrió un error al eliminar el archivo"]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "ID de archivo no proporcionado"]);
        }
    }

    // Método para generar PDF
    public function generarPDF()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->otroArchivoPDF->generarOtroArchivoPDF($id);
        } else {
            echo "ID de archivo no proporcionado";
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
                $es_pdf_subido = isset($_POST['es_pdf_subido']) ? intval($_POST['es_pdf_subido']) : 0;
                
                // Si es un PDF subido, devolver el PDF directamente
                if ($es_pdf_subido && isset($_POST['archivo_pdf']) && !empty($_POST['archivo_pdf'])) {
                    echo json_encode([
                        'success' => true,
                        'pdfBase64' => str_replace('data:application/pdf;base64,', '', $_POST['archivo_pdf'])
                    ]);
                    return;
                }
                
                // Procesar imágenes si se proporcionan
                $header_image = null;
                $footer_image = null;
                
                if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                    $header_image = $this->procesarImagen($_FILES['header_image']);
                } else if (isset($_POST['header_image']) && !empty($_POST['header_image'])) {
                    $header_image = $_POST['header_image'];
                } else {
                    // Usar la imagen de la plantilla
                    $this->otroArchivoPlantilla->obtenerTemplateActual();
                    $header_image = $this->otroArchivoPlantilla->getHeaderImageUrl();
                }
                
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                    $footer_image = $_POST['footer_image'];
                } else {
                    // Usar la imagen de la plantilla
                    if (!$this->otroArchivoPlantilla->getId()) {
                        $this->otroArchivoPlantilla->obtenerTemplateActual();
                    }
                    $footer_image = $this->otroArchivoPlantilla->getFooterImageUrl();
                }
                
                // Generar vista previa
                $pdfBase64 = $this->otroArchivoPDF->generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image);
                
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
            $this->otroArchivoPlantilla->obtenerTemplateActual();
            
            $data = [
                'success' => true,
                'data' => [
                    'id' => $this->otroArchivoPlantilla->getId(),
                    'titulo' => $this->otroArchivoPlantilla->getTitulo(),
                    'contenido' => $this->otroArchivoPlantilla->getContenido(),
                    'header_image' => $this->otroArchivoPlantilla->getHeaderImage(),
                    'footer_image' => $this->otroArchivoPlantilla->getFooterImage(),
                    'header_image_url' => $this->otroArchivoPlantilla->getHeaderImageUrl(),
                    'footer_image_url' => $this->otroArchivoPlantilla->getFooterImageUrl()
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
                $this->otroArchivoPlantilla->obtenerTemplateActual();
                
                // Procesar imágenes si se proporcionan
                $header_image = $this->otroArchivoPlantilla->getHeaderImage();
                $footer_image = $this->otroArchivoPlantilla->getFooterImage();
                
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
                $this->otroArchivoPlantilla->setTitulo($titulo);
                $this->otroArchivoPlantilla->setContenido($contenido);
                $this->otroArchivoPlantilla->setHeaderImage($header_image);
                $this->otroArchivoPlantilla->setFooterImage($footer_image);
                
                // Si se proporciona un ID, actualizar la plantilla existente
                if (!empty($_POST['id'])) {
                    $this->otroArchivoPlantilla->setId($_POST['id']);
                    $resultado = $this->otroArchivoPlantilla->actualizarTemplate();
                } else {
                    $resultado = $this->otroArchivoPlantilla->insertarTemplate();
                }
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'mensaje' => 'Plantilla guardada correctamente',
                        'id' => $this->otroArchivoPlantilla->getId()
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

    // Método para obtener tipos de archivos para filtrado
    public function getTipos()
    {
        try {
            $tipos = $this->otroArchivo->obtenerTiposArchivos();
            echo json_encode(['success' => true, 'data' => $tipos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    // Método para obtener motivos de archivos para filtrado
    public function getMotivos()
    {
        try {
            $motivos = $this->otroArchivo->obtenerMotivosArchivos();
            echo json_encode(['success' => true, 'data' => $motivos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    // Método para compartir por WhatsApp
    public function compartirWhatsApp()
    {
        if (isset($_POST['id']) && isset($_POST['telefono'])) {
            try {
                $id = intval($_POST['id']);
                $telefono = trim($_POST['telefono']);
                $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
                
                // Validar el número de teléfono (formato básico)
                if (!preg_match('/^\+?[0-9]{8,15}$/', $telefono)) {
                    throw new Exception("Número de teléfono inválido");
                }
                
                // Obtener el archivo
                $this->otroArchivo->obtenerOtroArchivo($id);
                
                // Generar URL para compartir
                $pdfUrl = URL::to("ajs/otro-archivo/generarPDF?id={$id}");
                
                // Formatear el mensaje
                $titulo = $this->otroArchivo->getTitulo();
                $mensajeWhatsApp = "Hola, te comparto el documento: *{$titulo}*\n\n";
                
                if (!empty($mensaje)) {
                    $mensajeWhatsApp .= "{$mensaje}\n\n";
                }
                
                $mensajeWhatsApp .= "Puedes acceder al documento aquí: {$pdfUrl}";
                
                // Codificar el mensaje para URL
                $mensajeWhatsApp = urlencode($mensajeWhatsApp);
                
                // Formatear el número de teléfono (eliminar el + si existe)
                $telefono = ltrim($telefono, '+');
                
                // Generar el enlace de WhatsApp
                $whatsappUrl = "https://api.whatsapp.com/send?phone={$telefono}&text={$mensajeWhatsApp}";
                
                echo json_encode([
                    'success' => true,
                    'whatsappUrl' => $whatsappUrl
                ]);
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'ID o teléfono no proporcionados']);
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
    
    // Método auxiliar para procesar archivos PDF
    private function procesarPDF($file)
    {
        if ($file['type'] !== 'application/pdf') {
            throw new Exception("Tipo de archivo no permitido. Solo se permiten archivos PDF.");
        }
        
        // Leer el archivo y convertirlo a base64
        $pdfData = file_get_contents($file['tmp_name']);
        $base64 = 'data:application/pdf;base64,' . base64_encode($pdfData);
        
        return $base64;
    }
}