<?php
// app/models/Constancia.php

class Constancia
{
    private $id;
    private $titulo;
    private $tipo;
    private $id_cliente;
    private $usuario_id;
    private $contenido;
    private $header_image;
    private $footer_image;
    private $estado;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conectar;
    private $lastError = '';
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
    
   public function getIdCliente()
    {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
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
    
    public function getEstado()
    {
        return $this->estado;
    }
    
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
        public function getFechaCreacion()
{
    return $this->fecha_creacion;
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
    public function getLastError() {
        return $this->lastError;
    }
    
public function insertarConstancia()
{
    try {
       
        // Verificar que usuario_id exista en la tabla usuarios
        $checkUserSql = "SELECT COUNT(*) as count FROM usuarios WHERE usuario_id = ?";
        $checkUserStmt = $this->conectar->prepare($checkUserSql);
        $checkUserStmt->bind_param("i", $this->usuario_id);
        $checkUserStmt->execute();
        $result = $checkUserStmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $this->lastError = "El usuario_id {$this->usuario_id} no existe en la tabla usuarios";
            error_log($this->lastError);
            return false;
        }
        
        $sql = "INSERT INTO constancias (titulo, tipo, id_cliente, usuario_id, contenido, header_image, footer_image, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt === false) {
            $this->lastError = "Error en la preparación de la consulta: " . $this->conectar->error;
            error_log($this->lastError);
            return false;
        }
        
        // Usar 'i' para enteros y 's' para strings
        $bindResult = $stmt->bind_param("ssiissss", 
            $this->titulo, 
            $this->tipo, 
          $this->id_cliente,
            $this->usuario_id, 
            $this->contenido, 
            $this->header_image, 
            $this->footer_image, 
            $this->estado
        );
        
        if ($bindResult === false) {
            $this->lastError = "Error en bind_param: " . $stmt->error;
            error_log($this->lastError);
            return false;
        }
        
        $executeResult = $stmt->execute();
        
        if ($executeResult) {
            $this->id = $this->conectar->insert_id;
            return true;
        } else {
            $this->lastError = "Error al ejecutar la consulta: " . $stmt->error . " (Código: " . $stmt->errno . ")";
            error_log($this->lastError);
            return false;
        }
    } catch (Exception $e) {
        $this->lastError = "Excepción: " . $e->getMessage();
        error_log($this->lastError);
        return false;
    }
}
    
    public function actualizarConstancia()
    {
        $sql = "UPDATE constancias 
                SET titulo = ?, tipo = ?, id_cliente = ?, contenido = ?, header_image = ?, footer_image = ?, estado = ? 
                WHERE id = ?";
        
        $stmt = $this->conectar->prepare($sql);
        
        $stmt->bind_param("sssisssi", $this->titulo, $this->tipo, $this->id_cliente, $this->contenido, $this->header_image, $this->footer_image, $this->estado, $this->id);
        
        return $stmt->execute();
    }
    
     public function obtenerConstancia($id)
    {
        $sql = "SELECT * FROM constancias WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($fila = $result->fetch_assoc()) {
            $this->id = $fila['id'];
            $this->id_cliente = $fila['id_cliente'];
            $this->id_usuario = $fila['id_usuario'];
            $this->tipo = $fila['tipo'];
            $this->titulo = $fila['titulo'];
            $this->contenido = $fila['contenido'];
            $this->header_image = $fila['header_image'];
            $this->footer_image = $fila['footer_image'];
            $this->estado = $fila['estado'];
            $this->fecha_creacion = $fila['fecha_creacion'];
            $this->fecha_modificacion = $fila['fecha_modificacion'];
            return true;
        }
        return false;
    }

   public function listarConstancias($filtro = null, $tipo_busqueda = null)
{
    try {
        // Construir la consulta SQL base
        $sql = "SELECT c.*, cl.datos as cliente_nombre, u.nombres as usuario_nombre 
                FROM constancias c
                LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
                LEFT JOIN usuarios u ON c.usuario_id = u.usuario_id";
        
        // Si hay un filtro de búsqueda, añadimos la condición WHERE
        if ($filtro && $tipo_busqueda) {
            if ($tipo_busqueda == 'cliente') {
                $sql .= " WHERE cl.datos LIKE ?";
            } else if ($tipo_busqueda == 'tipo') {
                $sql .= " WHERE c.tipo LIKE ?";
            } else if ($tipo_busqueda == 'titulo') {
                $sql .= " WHERE c.titulo LIKE ?";
            }
        }
        
        $sql .= " ORDER BY c.fecha_creacion DESC";
        
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
        error_log("Excepción en listarConstancias: " . $e->getMessage());
        return [];
    }
}
    
    public function eliminarConstancia($id = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }
        
        try {
            $sql = "DELETE FROM constancias WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Obtener tipos de constancias únicos para filtrado
    public function obtenerTiposConstancias()
    {
        try {
            // Consulta para obtener tipos únicos
            $sql = "SELECT DISTINCT tipo FROM constancias ORDER BY tipo";
            
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
            error_log("Excepción en obtenerTiposConstancias: " . $e->getMessage());
            return [];
        }
    }
       public function generarNumeroCorrelativo($tipo)
{
    // Obtener el año actual
    $anio = date('Y');
    
    // Contar cuántos constancias del mismo tipo existen en el año actual
    $sql = "SELECT COUNT(*) as total FROM constancias 
            WHERE tipo = ? AND YEAR(fecha_creacion) = ?";
    $stmt = $this->conectar->prepare($sql);
    $stmt->bind_param("si", $tipo, $anio);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // El siguiente número será el total + 1
    $numero = $row['total'] + 1;
    
    // Formatear el número correlativo: NRO.015-2025-JVC
    return sprintf("NRO.%03d-%d-JVC", $numero, $anio);
}
}