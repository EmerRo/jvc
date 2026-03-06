<?php
// app/models/BaseDocumento.php

abstract class BaseDocumento
{
    protected $id;
    protected $titulo;
    protected $tipo;
    protected $id_cliente;
    protected $usuario_id;
    protected $contenido;
    protected $header_image;
    protected $footer_image;
    protected $imagen1;
    protected $imagen2;
    protected $estado;
    protected $fecha_creacion;
    protected $fecha_modificacion;
    protected $conectar;
    protected $lastError = '';
    
    // Datos adicionales para mostrar
    protected $cliente_nombre;
    protected $cliente_documento;
    protected $cliente_direccion;
    
    // Debe ser definido en las clases hijas
    protected $tableName;
    
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }
    
    // Getters y setters comunes
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
    
    public function getImagen1()
    {
        return $this->imagen1;
    }
    
    public function setImagen1($imagen1)
    {
        $this->imagen1 = $imagen1;
    }
    
    public function getImagen2()
    {
        return $this->imagen2;
    }
    
    public function setImagen2($imagen2)
    {
        $this->imagen2 = $imagen2;
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
    
    public function getClienteNombre()
    {
        return $this->cliente_nombre;
    }
    
    public function setClienteNombre($cliente_nombre)
    {
        $this->cliente_nombre = $cliente_nombre;
    }
    
    public function getClienteDocumento()
    {
        return $this->cliente_documento;
    }
    
    public function setClienteDocumento($cliente_documento)
    {
        $this->cliente_documento = $cliente_documento;
    }
    
    public function getClienteDireccion()
    {
        return $this->cliente_direccion;
    }
    
    public function setClienteDireccion($cliente_direccion)
    {
        $this->cliente_direccion = $cliente_direccion;
    }
    
    public function getLastError()
    {
        return $this->lastError;
    }
    
    // Métodos para obtener URLs de imágenes
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
    
    // Métodos CRUD base
    public function obtenerDocumento($id)
    {
        $sql = "SELECT d.*, 
                cl.datos AS cliente_nombre, 
                cl.documento AS cliente_documento,
                cl.direccion AS cliente_direccion
                FROM {$this->tableName} d
                LEFT JOIN clientes cl ON d.id_cliente = cl.id_cliente
                WHERE d.id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($fila = $result->fetch_assoc()) {
            $this->mapearDatos($fila);
            return true;
        }
        return false;
    }
    
    protected function mapearDatos($fila)
    {
        $this->id = $fila['id'];
        $this->id_cliente = $fila['id_cliente'];
        $this->usuario_id = $fila['usuario_id'] ?? null;
        $this->tipo = $fila['tipo'];
        $this->titulo = $fila['titulo'];
        $this->contenido = $fila['contenido'];
        $this->header_image = $fila['header_image'];
        $this->footer_image = $fila['footer_image'];
        $this->estado = $fila['estado'];
        $this->fecha_creacion = $fila['fecha_creacion'];
        $this->fecha_modificacion = $fila['fecha_modificacion'];
        $this->cliente_nombre = $fila['cliente_nombre'] ?? null;
        $this->cliente_documento = $fila['cliente_documento'] ?? null;
        $this->cliente_direccion = $fila['cliente_direccion'] ?? null;
    }
    
    public function insertarDocumento()
    {
        try {
            // Verificar que usuario_id exista en la tabla usuarios
            if ($this->usuario_id) {
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
            }
            
            $sql = $this->buildInsertQuery();
            $stmt = $this->conectar->prepare($sql);
            
            if ($stmt === false) {
                $this->lastError = "Error en la preparación de la consulta: " . $this->conectar->error;
                error_log($this->lastError);
                return false;
            }
            
            $this->bindInsertParams($stmt);
            
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
    
    protected function buildInsertQuery()
    {
        if ($this->tableName === 'cartas') {
            return "INSERT INTO {$this->tableName} (id_cliente, id_usuario, tipo, titulo, contenido, header_image, footer_image, imagen1, imagen2, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        } else {
            return "INSERT INTO {$this->tableName} (titulo, tipo, id_cliente, usuario_id, contenido, header_image, footer_image, imagen1, imagen2, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }
    }
    
    protected function bindInsertParams($stmt)
    {
        if ($this->tableName === 'cartas') {
            $stmt->bind_param("iissssssss", 
                $this->id_cliente, 
                $this->usuario_id, 
                $this->tipo, 
                $this->titulo, 
                $this->contenido, 
                $this->header_image, 
                $this->footer_image,
                $this->imagen1,
                $this->imagen2,
                $this->estado
            );
        } else {
            $stmt->bind_param("ssiissssss", 
                $this->titulo, 
                $this->tipo, 
                $this->id_cliente,
                $this->usuario_id, 
                $this->contenido, 
                $this->header_image, 
                $this->footer_image,
                $this->imagen1,
                $this->imagen2,
                $this->estado
            );
        }
    }
    
    public function actualizarDocumento()
    {
        $sql = $this->buildUpdateQuery();
        $stmt = $this->conectar->prepare($sql);
        $this->bindUpdateParams($stmt);
        return $stmt->execute();
    }
    
    protected function buildUpdateQuery()
    {
        if ($this->tableName === 'cartas') {
            return "UPDATE {$this->tableName} 
                    SET id_cliente = ?, id_usuario = ?, tipo = ?, titulo = ?, 
                        contenido = ?, header_image = ?, footer_image = ?, 
                        imagen1 = ?, imagen2 = ?, estado = ? 
                    WHERE id = ?";
        } else {
            return "UPDATE {$this->tableName} 
                    SET titulo = ?, tipo = ?, id_cliente = ?, contenido = ?, header_image = ?, footer_image = ?, imagen1 = ?, imagen2 = ?, estado = ? 
                    WHERE id = ?";
        }
    }
    
    protected function bindUpdateParams($stmt)
    {
        if ($this->tableName === 'cartas') {
            $stmt->bind_param("iisssssssi", 
                $this->id_cliente, 
                $this->usuario_id, 
                $this->tipo, 
                $this->titulo, 
                $this->contenido, 
                $this->header_image, 
                $this->footer_image,
                $this->imagen1,
                $this->imagen2,
                $this->estado, 
                $this->id
            );
        } else {
            $stmt->bind_param("ssissssssi", 
                $this->titulo, 
                $this->tipo, 
                $this->id_cliente, 
                $this->contenido, 
                $this->header_image, 
                $this->footer_image,
                $this->imagen1,
                $this->imagen2,
                $this->estado, 
                $this->id
            );
        }
    }
    
    public function eliminarDocumento($id = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }
        
        try {
            $sql = "DELETE FROM {$this->tableName} WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function listarDocumentos($filtro = null, $tipo_busqueda = null)
    {
        try {
            $userColumn = $this->tableName === 'cartas' ? 'id_usuario' : 'usuario_id';
            
            $sql = "SELECT d.*, 
                    cl.datos AS cliente_nombre, 
                    cl.documento AS cliente_documento,
                    cl.direccion AS cliente_direccion,
                    u.usuario AS usuario_nombre
                    FROM {$this->tableName} d
                    LEFT JOIN clientes cl ON d.id_cliente = cl.id_cliente
                    LEFT JOIN usuarios u ON d.{$userColumn} = u.usuario_id
                    WHERE 1=1";
            
            $params = [];
            $types = "";
            
            // Aplicar filtros si existen
            if ($filtro && $tipo_busqueda) {
                if ($tipo_busqueda === 'titulo') {
                    $sql .= " AND d.titulo LIKE ?";
                    $params[] = "%$filtro%";
                    $types .= "s";
                } elseif ($tipo_busqueda === 'tipo') {
                    $sql .= " AND d.tipo LIKE ?";
                    $params[] = "%$filtro%";
                    $types .= "s";
                } elseif ($tipo_busqueda === 'cliente') {
                    $sql .= " AND cl.datos LIKE ?";
                    $params[] = "%$filtro%";
                    $types .= "s";
                }
            }
            
            $sql .= " ORDER BY d.fecha_creacion DESC";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
            }
            
            // Bind parameters if any
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $documentos = [];
            while ($row = $result->fetch_assoc()) {
                $documentos[] = $row;
            }
            
            return $documentos;
        } catch (Exception $e) {
            error_log("Error en listarDocumentos: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerTiposDocumentos()
    {
        $sql = "SELECT DISTINCT tipo FROM {$this->tableName} WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo";
        $result = $this->conectar->query($sql);
        
        $tipos = [];
        while ($fila = $result->fetch_assoc()) {
            $tipos[] = $fila['tipo'];
        }
        
        return $tipos;
    }
    
    public function generarNumeroCorrelativo($tipo)
    {
        // Obtener el año actual
        $anio = date('Y');
        
        // Contar cuántos documentos del mismo tipo existen en el año actual
        $sql = "SELECT COUNT(*) as total FROM {$this->tableName} 
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