<?php

require_once "app/models/GestionArchivo.php";
require_once "app/models/GestionAdjunto.php";
require_once "app/models/Producto.php";

class GestionArchivosController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }


    public function listarArchivos()
    {
        $respuesta = ["res" => false, "archivos" => []];
        
        try {
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
            $termino = isset($_POST['termino']) ? $_POST['termino'] : null;
            
            $archivo = new GestionArchivo();
            $id_empresa = isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12;
            $sucursal = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : 1;
            
            $archivo->setIdEmpresa($id_empresa);
            $archivo->setSucursal($sucursal);
            
            if ($termino) {
                $resultado = $archivo->buscarArchivos($termino, $tipo);
            } else if ($tipo) {
                $resultado = $archivo->listarPorTipo($tipo);
            } else {
                // Si no hay tipo ni término, listar todos los archivos
                $sql = "SELECT a.*, 
                        (SELECT nombre_adjunto FROM gestion_adjuntos WHERE id_archivo = a.id_archivo AND es_principal = 1 LIMIT 1) as adjunto_principal 
                        FROM gestion_archivos a 
                        WHERE a.id_empresa = '$id_empresa' 
                        AND a.sucursal = '$sucursal' 
                        AND a.estado = '1' 
                        ORDER BY a.fecha_actualizacion DESC";
                $resultado = $this->conexion->query($sql);
            }
            
            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta["archivos"][] = $row;
                }
                $respuesta["res"] = true;
            }
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function guardarArchivo()
    {
        $respuesta = ["res" => false];
        
        try {
            $this->conexion->begin_transaction();
            
            // Datos del archivo
            $titulo = $_POST['titulo'];
            $tipo = $_POST['tipo'];
            $id_producto = isset($_POST['id_producto']) && !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
            
            // Crear el archivo
            $archivo = new GestionArchivo();
            $archivo->setTitulo($titulo);
            $archivo->setTipo($tipo);
            $archivo->setIdProducto($id_producto);
            $archivo->setVersion('1.0');
            $archivo->setEstado('1');
            $archivo->setIdEmpresa(isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12);
            $archivo->setSucursal(isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : 1);
            
            $id_archivo = $archivo->insertar();
            
            if (!$id_archivo) {
                throw new Exception("Error al guardar el archivo");
            }
            
            // Procesar archivos adjuntos
            $this->procesarAdjuntos($id_archivo);
            
            // Si es un archivo editable (informe, carta, constancia, etc.)
            if (isset($_POST['contenido'])) {
                $this->guardarVersion($id_archivo, $_POST['contenido']);
            }
            
            $this->conexion->commit();
            $respuesta["res"] = true;
            $respuesta["id_archivo"] = $id_archivo;
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function actualizarArchivo()
    {
        $respuesta = ["res" => false];
        
        try {
            $this->conexion->begin_transaction();
            
            $id_archivo = $_POST['id_archivo'];
            $titulo = $_POST['titulo'];
            
            // Actualizar el archivo
            $archivo = new GestionArchivo();
            $archivo->setIdArchivo($id_archivo);
            $archivo->obtenerDatos();
            $archivo->setTitulo($titulo);
            
            if (!$archivo->modificar()) {
                throw new Exception("Error al actualizar el archivo");
            }
            
            // Procesar nuevos adjuntos si existen
            if (isset($_FILES) && !empty($_FILES)) {
                $this->procesarAdjuntos($id_archivo);
            }
            
            // Si es un archivo editable y se envió contenido
            if (isset($_POST['contenido'])) {
                $this->guardarVersion($id_archivo, $_POST['contenido']);
            }
            
            $this->conexion->commit();
            $respuesta["res"] = true;
            
        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function eliminarArchivo()
    {
        $respuesta = ["res" => false];
        
        try {
            $id_archivo = $_POST['id_archivo'];
            
            $archivo = new GestionArchivo();
            $archivo->setIdArchivo($id_archivo);
            
            if ($archivo->eliminar()) {
                $respuesta["res"] = true;
            } else {
                throw new Exception("Error al eliminar el archivo");
            }
            
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function obtenerArchivo()
    {
        $respuesta = ["res" => false];
        
        try {
            $id_archivo = $_POST['id_archivo'];
            
            // Obtener datos del archivo
            $sql = "SELECT a.*, 
                    (SELECT p.nombre FROM productos p WHERE p.id_producto = a.id_producto) as nombre_producto 
                    FROM gestion_archivos a 
                    WHERE a.id_archivo = '$id_archivo'";
            
            $archivo = $this->conexion->query($sql)->fetch_assoc();
            
            if (!$archivo) {
                throw new Exception("Archivo no encontrado");
            }
            
            // Obtener adjuntos del archivo
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntos = $adjuntoObj->listarPorArchivo();
            
            $listaAdjuntos = [];
            if ($adjuntos && $adjuntos->num_rows > 0) {
                while ($row = $adjuntos->fetch_assoc()) {
                    $listaAdjuntos[] = $row;
                }
            }
            
            // Obtener última versión del contenido si es archivo editable
            $contenido = "";
            if (in_array($archivo['tipo'], ['informe', 'carta', 'constancia', 'interno', 'otro'])) {
                $sql = "SELECT contenido FROM gestion_versiones 
                        WHERE id_archivo = '$id_archivo' 
                        ORDER BY fecha_creacion DESC LIMIT 1";
                $version = $this->conexion->query($sql)->fetch_assoc();
                if ($version) {
                    $contenido = $version['contenido'];
                }
            }
            
            $respuesta["res"] = true;
            $respuesta["archivo"] = $archivo;
            $respuesta["adjuntos"] = $listaAdjuntos;
            $respuesta["contenido"] = $contenido;
            
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function listarProductos()
    {
        $respuesta = ["res" => false, "productos" => []];
        
        try {
            $termino = isset($_POST['termino']) ? $_POST['termino'] : "";
            
            $sql = "SELECT id_producto, nombre, codigo 
                    FROM productos 
                    WHERE estado = '1' 
                    AND id_empresa = '" . (isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12) . "' 
                    AND (nombre LIKE '%$termino%' OR codigo LIKE '%$termino%') 
                    ORDER BY nombre ASC";
            
            $resultado = $this->conexion->query($sql);
            
            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta["productos"][] = $row;
                }
                $respuesta["res"] = true;
            }
            
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function eliminarAdjunto()
    {
        $respuesta = ["res" => false];
        
        try {
            $id_adjunto = $_POST['id_adjunto'];
            
            $adjunto = new GestionAdjunto();
            $adjunto->setIdAdjunto($id_adjunto);
            
            if ($adjunto->eliminar()) {
                $respuesta["res"] = true;
            } else {
                throw new Exception("Error al eliminar el adjunto");
            }
            
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    public function establecerAdjuntoPrincipal()
    {
        $respuesta = ["res" => false];
        
        try {
            $id_adjunto = $_POST['id_adjunto'];
            $id_archivo = $_POST['id_archivo'];
            
            $adjunto = new GestionAdjunto();
            $adjunto->setIdAdjunto($id_adjunto);
            $adjunto->setIdArchivo($id_archivo);
            
            if ($adjunto->establecerComoPrincipal()) {
                $respuesta["res"] = true;
            } else {
                throw new Exception("Error al establecer el adjunto como principal");
            }
            
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }
        
        return json_encode($respuesta);
    }

    private function procesarAdjuntos($id_archivo)
    {
        // Procesar archivos PDF
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == UPLOAD_ERR_OK) {
            $this->guardarAdjunto($id_archivo, $_FILES['pdf'], 'pdf', true);
        }
        
        // Procesar archivos editables
        if (isset($_FILES['editable']) && $_FILES['editable']['error'] == UPLOAD_ERR_OK) {
            $this->guardarAdjunto($id_archivo, $_FILES['editable'], 'editable', false);
        }
        
        // Procesar imágenes (pueden ser múltiples)
        if (isset($_FILES['imagenes'])) {
            $total = count($_FILES['imagenes']['name']);
            
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['imagenes']['error'][$i] == UPLOAD_ERR_OK) {
                    $archivo = [
                        'name' => $_FILES['imagenes']['name'][$i],
                        'type' => $_FILES['imagenes']['type'][$i],
                        'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
                        'error' => $_FILES['imagenes']['error'][$i],
                        'size' => $_FILES['imagenes']['size'][$i]
                    ];
                    
                    $this->guardarAdjunto($id_archivo, $archivo, 'imagen', false);
                }
            }
        }
        
        // Guardar link de YouTube si existe
        if (isset($_POST['youtube_link']) && !empty($_POST['youtube_link'])) {
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntoObj->setNombreAdjunto($_POST['youtube_link']);
            $adjuntoObj->setTipoAdjunto('youtube');
            $adjuntoObj->setRutaAdjunto($_POST['youtube_link']);
            $adjuntoObj->setEsPrincipal('0');
            $adjuntoObj->insertar();
        }
    }

    private function guardarAdjunto($id_archivo, $archivo, $tipo, $esPrincipal)
    {
        $nombreOriginal = $archivo['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        
        // Definir la carpeta según el tipo de archivo
        $carpeta = 'files/gestion_archivos/' . $tipo . '/';
        
        // Crear la carpeta si no existe
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        
        $rutaCompleta = $carpeta . $nombreArchivo;
        
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntoObj->setNombreAdjunto($nombreOriginal);
            $adjuntoObj->setTipoAdjunto($tipo);
            $adjuntoObj->setRutaAdjunto($rutaCompleta);
            $adjuntoObj->setEsPrincipal($esPrincipal ? '1' : '0');
            
            return $adjuntoObj->insertar();
        }
        
        return false;
    }

    private function guardarVersion($id_archivo, $contenido)
    {
        // Obtener la última versión
        $sql = "SELECT version FROM gestion_archivos WHERE id_archivo = '$id_archivo'";
        $resultado = $this->conexion->query($sql);
        $archivo = $resultado->fetch_assoc();
        $version = $archivo['version'];
        
        // Guardar la nueva versión
        $sql = "INSERT INTO gestion_versiones (id_archivo, version, contenido, id_usuario) 
                VALUES ('$id_archivo', '$version', ?, '" . (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0) . "')";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $contenido);
        return $stmt->execute();
    }

  
}