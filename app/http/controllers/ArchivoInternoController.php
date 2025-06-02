<?php
// app/http/controllers/ArchivoInternoController.php

require_once "app/models/ArchivoInterno.php";
require_once "app/models/ArchivoInternoPlantilla.php";
require_once "app/http/controllers/ArchivoInternoPDF.php";

class ArchivoInternoController extends Controller
{
    private $archivoInterno;
    private $archivoInternoPlantilla;
    private $archivoInternoPDF;
    private $conectar;

    public function __construct()
    {
        $this->archivoInterno = new ArchivoInterno();
        $this->archivoInternoPlantilla = new ArchivoInternoPlantilla();
        $this->archivoInternoPDF = new ArchivoInternoPDF();
        $this->conectar = (new Conexion())->getConexion();
    }

    // Método para obtener todos los archivos internos (con filtro opcional)
    public function render()
    {
        try {
            // Obtener parámetros de filtro
            $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
            $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;

            // Intentar obtener los datos
            $archivos = $this->archivoInterno->listarArchivosInternos($filtro, $tipo_busqueda);

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

    // Método para obtener un archivo interno específico
    public function getOne()
    {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        if ($this->archivoInterno->obtenerArchivoInterno($id)) {
            $respuesta = [
                'success' => true,
                'data' => [
                    'id' => $this->archivoInterno->getId(),
                    'id_cliente' => $this->archivoInterno->getClienteId(),
                    'usuario_id' => $this->archivoInterno->getUsuarioId(),
                    'tipo' => $this->archivoInterno->getTipo(),
                    'titulo' => $this->archivoInterno->getTitulo(),
                    'contenido' => $this->archivoInterno->getContenido(),
                    'archivo_pdf' => $this->archivoInterno->getArchivoPdf(),
                    'header_image' => $this->archivoInterno->getHeaderImage(),
                    'footer_image' => $this->archivoInterno->getFooterImage(),
                    'es_pdf_subido' => $this->archivoInterno->getEsPdfSubido(),
                    'header_image_url' => $this->archivoInterno->getHeaderImageUrl(),
                    'footer_image_url' => $this->archivoInterno->getFooterImageUrl(),
                    'estado' => $this->archivoInterno->getEstado()
                ]
            ];

            echo json_encode($respuesta);
            return;
        }

        echo json_encode(['success' => false, 'error' => 'Archivo interno no encontrado']);
    }

    // Método para insertar un nuevo archivo interno
    public function insertar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
                $es_pdf_subido = isset($_POST['es_pdf_subido']) ? intval($_POST['es_pdf_subido']) : 0;
                // Depuración del ID del cliente
                $cliente_id_post = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : (isset($_POST['id_cliente']) ? $_POST['id_cliente'] : 'no definido');
                error_log("POST id_cliente/id_cliente: " . $cliente_id_post);
                error_log("POST id_cliente/id_cliente tipo: " . gettype($cliente_id_post));

                // Verificar si el cliente existe
                if (!empty($cliente_id_post)) {
                    $checkClientSql = "SELECT COUNT(*) as count FROM clientes WHERE id_cliente = ?";
                    $checkClientStmt = $this->conectar->prepare($checkClientSql);
                    $checkClientStmt->bind_param("i", $cliente_id_post);
                    $checkClientStmt->execute();
                    $result = $checkClientStmt->get_result();
                    $row = $result->fetch_assoc();
                    error_log("Cliente existe: " . ($row['count'] > 0 ? 'SÍ' : 'NO'));
                }
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
                        throw new Exception("No hay usuarios en la base de datos para asignar al archivo interno");
                    }
                } else {
                    $usuario_id = $_SESSION['usuario_id'];
                }
                // Validar que los campos obligatorios no estén vacíos
                if (empty($titulo)) {
                    throw new Exception("El título es obligatorio");
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

                // Configurar el objeto archivo interno
                $this->archivoInterno->setTipo($tipo);
                $this->archivoInterno->setTitulo($titulo);
                $this->archivoInterno->setContenido($contenido);
                $this->archivoInterno->setArchivoPdf($archivo_pdf);
                $this->archivoInterno->setHeaderImage($header_image);
                $this->archivoInterno->setFooterImage($footer_image);
                $this->archivoInterno->setClienteId($id_cliente);
                $this->archivoInterno->setUsuarioId($usuario_id);
                $this->archivoInterno->setEsPdfSubido($es_pdf_subido);
                $this->archivoInterno->setEstado('borrador');

                // Insertar el archivo interno
                if ($this->archivoInterno->insertarArchivoInterno()) {
                    echo json_encode([
                        'res' => true,
                        'msg' => 'Archivo interno creado correctamente',
                        'id' => $this->archivoInterno->getId()
                    ]);
                } else {
                    // Obtener el error detallado del modelo
                    $errorDetalle = $this->archivoInterno->getLastError();

                    // También obtener error de la conexión directa
                    $errorMySQL = $this->conectar->error;
                    $errorNumero = $this->conectar->errno;

                    // Construir mensaje de error detallado
                    $mensajeError = "Error al guardar el archivo interno en la base de datos.\n";
                    $mensajeError .= "Detalle: " . $errorDetalle . "\n";
                    $mensajeError .= "MySQL Error: " . $errorMySQL . " (Código: " . $errorNumero . ")";

                    error_log($mensajeError);
                    throw new Exception($mensajeError);
                }

            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para editar un archivo interno existente
    public function editar()
    {
        if (!empty($_POST)) {
            try {
                // Validar datos
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
                $es_pdf_subido = isset($_POST['es_pdf_subido']) ? intval($_POST['es_pdf_subido']) : 0;
                $estado = isset($_POST['estado']) ? $_POST['estado'] : 'borrador';

                // Validar que los campos obligatorios no estén vacíos
                if (empty($id) || empty($titulo)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }

                // Obtener el archivo interno actual
                $this->archivoInterno->setId($id);
                if (!$this->archivoInterno->obtenerArchivoInterno($id)) {
                    throw new Exception("El archivo interno no existe");
                }

                // Procesar contenido o archivo PDF según el tipo
                $contenido = $this->archivoInterno->getContenido();
                $archivo_pdf = $this->archivoInterno->getArchivoPdf();

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
                $header_image = $this->archivoInterno->getHeaderImage();
                $footer_image = $this->archivoInterno->getFooterImage();

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

                // Configurar el objeto archivo interno
                $this->archivoInterno->setTipo($tipo);
                $this->archivoInterno->setTitulo($titulo);
                $this->archivoInterno->setContenido($contenido);
                $this->archivoInterno->setArchivoPdf($archivo_pdf);
                $this->archivoInterno->setHeaderImage($header_image);
                $this->archivoInterno->setFooterImage($footer_image);
                $this->archivoInterno->setClienteId($id_cliente);
                $this->archivoInterno->setEsPdfSubido($es_pdf_subido);
                $this->archivoInterno->setEstado($estado);

                // Actualizar el archivo interno
                if ($this->archivoInterno->actualizarArchivoInterno()) {
                    echo json_encode([
                        'res' => true,
                        'msg' => 'Archivo interno actualizado correctamente'
                    ]);
                } else {
                    throw new Exception("Error al actualizar el archivo interno en la base de datos");
                }

            } catch (Exception $e) {
                echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['res' => false, 'msg' => 'No se recibieron datos']);
        }
    }

    // Método para eliminar un archivo interno
    public function borrar()
    {
        if (isset($_POST["id"])) {
            $id = intval($_POST["id"]);

            if ($this->archivoInterno->eliminarArchivoInterno($id)) {
                echo json_encode(["res" => true, "msg" => "Archivo interno eliminado correctamente"]);
            } else {
                echo json_encode(["res" => false, "msg" => "Ocurrió un error al eliminar el archivo interno"]);
            }
        } else {
            echo json_encode(["res" => false, "msg" => "ID de archivo interno no proporcionado"]);
        }
    }

    // Método para generar PDF
    public function generarPDF()
    {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->archivoInternoPDF->generarArchivoInternoPDF($id);
        } else {
            echo "ID de archivo interno no proporcionado";
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
                    $this->archivoInternoPlantilla->obtenerTemplateActual();
                    $header_image = $this->archivoInternoPlantilla->getHeaderImageUrl();
                }

                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                    $footer_image = $this->procesarImagen($_FILES['footer_image']);
                } else if (isset($_POST['footer_image']) && !empty($_POST['footer_image'])) {
                    $footer_image = $_POST['footer_image'];
                } else {
                    // Usar la imagen de la plantilla
                    if (!$this->archivoInternoPlantilla->getId()) {
                        $this->archivoInternoPlantilla->obtenerTemplateActual();
                    }
                    $footer_image = $this->archivoInternoPlantilla->getFooterImageUrl();
                }

                // Generar vista previa
                $pdfBase64 = $this->archivoInternoPDF->generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image);

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
            $this->archivoInternoPlantilla->obtenerTemplateActual();

            $data = [
                'success' => true,
                'data' => [
                    'id' => $this->archivoInternoPlantilla->getId(),
                    'titulo' => $this->archivoInternoPlantilla->getTitulo(),
                    'contenido' => $this->archivoInternoPlantilla->getContenido(),
                    'header_image' => $this->archivoInternoPlantilla->getHeaderImage(),
                    'footer_image' => $this->archivoInternoPlantilla->getFooterImage(),
                    'header_image_url' => $this->archivoInternoPlantilla->getHeaderImageUrl(),
                    'footer_image_url' => $this->archivoInternoPlantilla->getFooterImageUrl()
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
                $this->archivoInternoPlantilla->obtenerTemplateActual();

                // Procesar imágenes si se proporcionan
                $header_image = $this->archivoInternoPlantilla->getHeaderImage();
                $footer_image = $this->archivoInternoPlantilla->getFooterImage();

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
                $this->archivoInternoPlantilla->setTitulo($titulo);
                $this->archivoInternoPlantilla->setContenido($contenido);
                $this->archivoInternoPlantilla->setHeaderImage($header_image);
                $this->archivoInternoPlantilla->setFooterImage($footer_image);

                // Si se proporciona un ID, actualizar la plantilla existente
                if (!empty($_POST['id'])) {
                    $this->archivoInternoPlantilla->setId($_POST['id']);
                    $resultado = $this->archivoInternoPlantilla->actualizarTemplate();
                } else {
                    $resultado = $this->archivoInternoPlantilla->insertarTemplate();
                }

                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'mensaje' => 'Plantilla guardada correctamente',
                        'id' => $this->archivoInternoPlantilla->getId()
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

    // Método para obtener tipos de archivos internos para filtrado
    public function getTipos()
    {
        try {
            $tipos = $this->archivoInterno->obtenerTiposArchivosInternos();
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