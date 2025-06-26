<?php
// app/http/controllers/ConstanciaController.php

require_once "app/models/Constancia.php";
require_once "app/models/ConstanciaPlantilla.php";
require_once "app/http/controllers/ConstanciaPDF.php";
require_once "app/models/TipoConstancia.php";

class ConstanciaController extends Controller
{
    private $constancia;
    private $constanciaPlantilla;
    private $constanciaPDF;
    private $tipoConstancia;
    private $conectar;

    public function __construct()
    {
        $this->constancia = new Constancia();
        $this->constanciaPlantilla = new ConstanciaPlantilla();
        $this->constanciaPDF = new ConstanciaPDF();
        $this->tipoConstancia = new TipoConstancia();
        $this->conectar = (new Conexion())->getConexion();
    }

  public function render()
{
    try {
        // Obtener parámetros de filtro
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
        $tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : null;
        
        // Intentar obtener los datos
        $constancias = $this->constancia->listarConstancias($filtro, $tipo_busqueda);
        
        // IMPORTANTE: Devolver en el formato que espera el frontend
        $response = ['constancias' => $constancias ?: []];
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode([
            'error' => true,
            'message' => 'Error al procesar la solicitud',
            'debug_info' => $e->getMessage(),
            'constancias' => []
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
                    'cliente_id' => $this->constancia->getIdCliente(),
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
          $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
            
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
            error_log("Datos a insertar - Tipo: $tipo, Titulo: $titulo, Cliente ID: $id_cliente, Usuario ID: $usuario_id");
            
            // Configurar el objeto constancia
            $this->constancia->setTipo($tipo);
            $this->constancia->setTitulo($titulo);
            $this->constancia->setContenido($contenido);
            $this->constancia->setHeaderImage(isset($_POST['header_image']) ? $_POST['header_image'] : null);
            $this->constancia->setFooterImage(isset($_POST['footer_image']) ? $_POST['footer_image'] : null);
            $this->constancia->setIdCliente($id_cliente);
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
          $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
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
               $this->constancia->setIdCliente($id_cliente);
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
    public function obtenerTipoConstancias() 
    {
        try {
            $tipos =$this->tipoConstancia->obtenerTodos();
            echo json_encode(['success' => true, 'tipos' => $tipos]);
        }catch(Exception $e) {
            echo json_encode(['success'=> false, 'error' => $e->getMessage()]);
        }
    }
    public function insertarTipoConstancia () 
    {
        if (!empty($_POST)) {
            try {
                $nombre =isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                if (empty($nombre)) {
                    throw new Exception("El nombre del tipo es obligatorio");
                }
                $this->tipoConstancia->setNombre($nombre);
                if($this->tipoConstancia->insertar())
                {
                    echo json_encode(['success' => true, 'msg' => 'Tipo de constancia creada correctamente']);
                }else {
                    throw new Exception("Error al guardar el tipo de constancia");
                }
            }catch(Exception $e) {
                echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
            }
        }
    }
public function editarTipoConstancia()
{
    if (!empty($_POST)) {
        try {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            
            if (empty($id) || empty($nombre)) {
                throw new Exception("ID y nombre son obligatorios");
            }
            
            $this->tipoConstancia->setId($id);
            $this->tipoConstancia->setNombre($nombre);
            
            if ($this->tipoConstancia->actualizar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de constancia actualizado correctamente']);
            } else {
                throw new Exception("Error al actualizar el tipo de constancia");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}


public function eliminarTipoConstancia()
{
    if (isset($_POST['id'])) {
        try {
            $id = intval($_POST['id']);
            $this->tipoConstancia->setId($id);
            
            if ($this->tipoConstancia->eliminar()) {
                echo json_encode(['success' => true, 'msg' => 'Tipo de constancia eliminado correctamente']);
            } else {
                throw new Exception("Error al eliminar el tipo de constancia");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}
public function obtenerMembretes()
{
    try {
        // Obtener la plantilla actual que contiene los membretes
        $this->constanciaPlantilla->obtenerTemplateActual();
        
        $data = [
            'success' => true,
            'data' => [
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
public function guardarMembretes()
{
    if (!empty($_POST) || !empty($_FILES)) {
        try {
            // Obtener la plantilla actual
            if (!$this->constanciaPlantilla->obtenerTemplateActual()) {
                throw new Exception("No se pudo obtener la plantilla actual");
            }
            
            // Mantener valores actuales como respaldo
            $header_image = $this->constanciaPlantilla->getHeaderImage();
            $footer_image = $this->constanciaPlantilla->getFooterImage();
            
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
            $this->constanciaPlantilla->setHeaderImage($header_image);
            $this->constanciaPlantilla->setFooterImage($footer_image);
            
            // Guardar la plantilla actualizada
            $resultado = $this->constanciaPlantilla->actualizarTemplate();
            
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
}