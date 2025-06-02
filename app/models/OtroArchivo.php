<?php
// app/models/OtroArchivo.php

class OtroArchivo
{
    private $id;
    private $titulo;
    private $tipo;
    private $motivo;
    private $cliente_id;
    private $usuario_id;
    private $contenido;
    private $archivo_pdf;
    private $header_image;
    private $footer_image;
    private $es_pdf_subido;
    private $estado;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conectar;
    
    // Datos adicionales para mostrar
    private $cliente_nombre;
    
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }
    
    // Getters y setters
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getTitulo()
    {
        return $this->titulo;
    }
    
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    
    public function getTipo()
    {
        return $this->tipo;
    }
    
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    
    public function getMotivo()
    {
        return $this->motivo;
    }
    
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }
    
    public function getClienteId()
    {
        return $this->cliente_id;
    }
    
    public function setClienteId($cliente_id)
    {
        $this->cliente_id = $cliente_id;
    }
    
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }
    
    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }
    
    public function getContenido()
    {
        return $this->contenido;
    }
    
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }
    
    public function getArchivoPdf()
    {
        return $this->archivo_pdf;
    }
    
    public function setArchivoPdf($archivo_pdf)
    {
        $this->archivo_pdf = $archivo_pdf;
    }
    
    public function getHeaderImage()
    {
        return $this->header_image;
    }
    
    public function setHeaderImage($header_image)
    {
        $this->header_image = $header_image;
    }
    
    public function getFooterImage()
    {
        return $this->footer_image;
    }
    
    public function setFooterImage($footer_image)
    {
        $this->footer_image = $footer_image;
    }
    
    public function getEsPdfSubido()
    {
        return $this->es_pdf_subido;
    }
    
    public function setEsPdfSubido($es_pdf_subido)
    {
        $this->es_pdf_subido = $es_pdf_subido;
    }
    
    public function getEstado()
    {
        return $this->estado;
    }
    
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    
    public function getHeaderImageUrl()
    {
        if ($this->header_image) {
            return $this->header_image;
        }
        return URL::to('public/img/garantia/header.png'); // Imagen por defecto
    }
    
    public function getFooterImageUrl()
    {
        if ($this->footer_image) {
            return $this->footer_image;
        }
        return URL::to('public/img/garantia/footer.png'); // Imagen por defecto
    }
    
    // Métodos CRUD
    public function insertarOtroArchivo()
    {
        $sql = "INSERT INTO otros_archivos (titulo, tipo, motivo, cliente_id, usuario_id, contenido, archivo_pdf, header_image, footer_image, es_pdf_subido, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("sssiiisssss", $this->titulo, $this->tipo, $this->motivo, $this->cliente_id, $this->usuario_id, $this->contenido, $this->archivo_pdf, $this->header_image, $this->footer_image, $this->es_pdf_subido, $this->estado);
        $result = $stmt->execute();
        
        if ($result) {
            $this->id = $this->conectar->insert_id;
        }
        return $result;
    }
    
    public function actualizarOtroArchivo()
    {
        $sql = "UPDATE otros_archivos 
                SET titulo = ?, tipo = ?, motivo = ?, cliente_id = ?, contenido = ?, archivo_pdf = ?, header_image = ?, footer_image = ?, es_pdf_subido = ?, estado = ? 
                WHERE id = ?";
        
        $stmt = $this->conectar->prepare($sql);
        
        $stmt->bind_param("sssississsi", $this->titulo, $this->tipo, $this->motivo, $this->cliente_id, $this->contenido, $this->archivo_pdf, $this->header_image, $this->footer_image, $this->es_pdf_subido, $this->estado, $this->id);
        
        return $stmt->execute();
    }
    
    public function obtenerOtroArchivo($id = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }
        
        $sql = "SELECT a.*, cl.datos as cliente_nombre 
                FROM otros_archivos a
                LEFT JOIN clientes cl ON a.cliente_id = cl.id_cliente
                WHERE a.id = ?";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($fila = $result->fetch_assoc()) {
            $this->titulo = $fila['titulo'];
            $this->tipo = $fila['tipo'];
            $this->motivo = $fila['motivo'];
            $this->cliente_id = $fila['cliente_id'];
            $this->usuario_id = $fila['usuario_id'];
            $this->contenido = $fila['contenido'];
            $this->archivo_pdf = $fila['archivo_pdf'];
            $this->header_image = $fila['header_image'];
            $this->footer_image = $fila['footer_image'];
            $this->es_pdf_subido = $fila['es_pdf_subido'];
            $this->estado = $fila['estado'];
            $this->fecha_creacion = $fila['fecha_creacion'];
            $this->fecha_modificacion = $fila['fecha_modificacion'];
            $this->cliente_nombre = $fila['cliente_nombre'];
            return true;
        }
        return false;
    }
    
    public function listarOtrosArchivos($filtro = null, $tipo_busqueda = null)
    {
        try {
            // Construir la consulta SQL base
            $sql = "SELECT a.*, cl.datos as cliente_nombre, u.nombres as usuario_nombre 
                    FROM otros_archivos a
                    LEFT JOIN clientes cl ON a.cliente_id = cl.id_cliente
                    LEFT JOIN usuarios u ON a.usuario_id = u.usuario_id";
            
            // Si hay un filtro de búsqueda, añadimos la condición WHERE
            if ($filtro && $tipo_busqueda) {
                if ($tipo_busqueda == 'cliente') {
                    $sql .= " WHERE cl.datos LIKE ?";
                } else if ($tipo_busqueda == 'tipo') {
                    $sql .= " WHERE a.tipo LIKE ?";
                } else if ($tipo_busqueda == 'motivo') {
                    $sql .= " WHERE a.motivo LIKE ?";
                } else if ($tipo_busqueda == 'titulo') {
                    $sql .= " WHERE a.titulo LIKE ?";
                }
            }
            
            $sql .= " ORDER BY a.fecha_creacion DESC";
            
            if ($filtro && $tipo_busqueda) {
                $stmt = $this->conectar->prepare($sql);
                $param = "%$filtro%";
                $stmt->bind_param("s", $param);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->conectar->query($sql);
            }
            
            // Verificar si $result es un objeto válido
            if ($result === false) {
                error_log("Error en la consulta SQL: " . $this->conectar->error);
                return [];
            }
            
            // Todo está bien, devolver los resultados
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            // Capturar y registrar cualquier excepción
            error_log("Excepción en listarOtrosArchivos: " . $e->getMessage());
            return [];
        }
    }
    
    public function eliminarOtroArchivo($id = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }
        
        try {
            $sql = "DELETE FROM otros_archivos WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Obtener tipos de archivos únicos para filtrado
    public function obtenerTiposArchivos()
    {
        try {
            // Consulta para obtener tipos únicos
            $sql = "SELECT DISTINCT tipo FROM otros_archivos ORDER BY tipo";
            
            // Ejecutar la consulta
            $result = $this->conectar->query($sql);
            
            // Verificar si $result es un objeto válido
            if ($result === false) {
                error_log("Error en la consulta SQL: " . $this->conectar->error);
                return [];
            }
            
            // Todo está bien, devolver los resultados
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Excepción en obtenerTiposArchivos: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener motivos de archivos únicos para filtrado
    public function obtenerMotivosArchivos()
    {
        try {
            // Consulta para obtener motivos únicos
            $sql = "SELECT DISTINCT motivo FROM otros_archivos WHERE motivo IS NOT NULL AND motivo != '' ORDER BY motivo";
            
            // Ejecutar la consulta
            $result = $this->conectar->query($sql);
            
            // Verificar si $result es un objeto válido
            if ($result === false) {
                error_log("Error en la consulta SQL: " . $this->conectar->error);
                return [];
            }
            
            // Todo está bien, devolver los resultados
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Excepción en obtenerMotivosArchivos: " . $e->getMessage());
            return [];
        }
    }
}