<?php

class GestionArchivo
{
    private $id_archivo;
    private $titulo;
    private $tipo;
    private $id_producto;
    private $version;
    private $id_empresa;
    private $sucursal;
    private $conectar;

    /**
     * GestionArchivo constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdArchivo()
    {
        return $this->id_archivo;
    }

    /**
     * @param mixed $id_archivo
     */
    public function setIdArchivo($id_archivo)
    {
        $this->id_archivo = $id_archivo;
    }

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param mixed $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @return mixed
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param mixed $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return mixed
     */
    public function getIdProducto()
    {
        return $this->id_producto;
    }

    /**
     * @param mixed $id_producto
     */
    public function setIdProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
    }

    public function insertar()
    {
        if ($this->id_producto) {
            $sql = "INSERT INTO gestion_archivos (titulo, tipo, id_producto, version, id_empresa, sucursal, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                error_log("Error al preparar consulta: " . $this->conectar->error);
                return false;
            }
            
            $titulo = $this->titulo ?: '';
            $tipo = $this->tipo ?: '';
            $id_producto = $this->id_producto;
            $version = $this->version ?: '1.0';
            $id_empresa = $this->id_empresa ?: 12;
            $sucursal = $this->sucursal ?: '1';
            $fecha_creacion = date('Y-m-d H:i:s');
            
            // CORREGIDO: 7 tipos para 7 parámetros (sin estado)
            $stmt->bind_param("ssissss", 
                $titulo,
                $tipo,
                $id_producto,
                $version,
                $id_empresa,
                $sucursal,
                $fecha_creacion
            );
        } else {
            $sql = "INSERT INTO gestion_archivos (titulo, tipo, version, id_empresa, sucursal, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                error_log("Error al preparar consulta: " . $this->conectar->error);
                return false;
            }
            
            $titulo = $this->titulo ?: '';
            $tipo = $this->tipo ?: '';
            $version = $this->version ?: '1.0';
            $id_empresa = $this->id_empresa ?: 12;
            $sucursal = $this->sucursal ?: '1';
            $fecha_creacion = date('Y-m-d H:i:s');
            
            $stmt->bind_param("ssssss", 
                $titulo,
                $tipo,
                $version,
                $id_empresa,
                $sucursal,
                $fecha_creacion
            );
        }
        
        if ($stmt->execute()) {
            $this->id_archivo = $this->conectar->insert_id;
            $stmt->close();
            error_log("Archivo insertado exitosamente con ID: " . $this->id_archivo);
            return $this->id_archivo;
        }
        
        $error = $stmt->error;
        $stmt->close();
        error_log("Error al ejecutar consulta: " . $error);
        return false;
    }

    public function modificar()
    {
        $sql = "UPDATE gestion_archivos 
                SET titulo = ?, 
                    tipo = ?, 
                    id_producto = ?, 
                    version = ? 
                WHERE id_archivo = ?";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar consulta modificar: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("ssssi", 
            $this->titulo,
            $this->tipo,
            $this->id_producto,
            $this->version,
            $this->id_archivo
        );
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function eliminar()
    {
        // NUEVO: Eliminar completamente el registro en lugar de solo cambiar estado
        $sql = "DELETE FROM gestion_archivos WHERE id_archivo = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            error_log("Error al preparar consulta de eliminación: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("i", $this->id_archivo);
        $resultado = $stmt->execute();
        $stmt->close();
        
        if ($resultado) {
            error_log("Archivo eliminado completamente con ID: " . $this->id_archivo);
            return true;
        } else {
            error_log("Error al eliminar archivo con ID: " . $this->id_archivo);
            return false;
        }
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * FROM gestion_archivos WHERE id_archivo = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            error_log("Error al preparar consulta obtenerDatos: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("i", $this->id_archivo);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($fila) {
            $this->titulo = $fila['titulo'];
            $this->tipo = $fila['tipo'];
            $this->id_producto = $fila['id_producto'];
            $this->version = $fila['version'];
            $this->id_empresa = $fila['id_empresa'];
            $this->sucursal = $fila['sucursal'];
            return true;
        }
        return false;
    }

    // NUEVO: Método para obtener archivo por ID (usado en el controlador)
    public function obtenerPorId()
    {
        $sql = "SELECT * FROM gestion_archivos WHERE id_archivo = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            error_log("Error al preparar consulta obtenerPorId: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("i", $this->id_archivo);
        $stmt->execute();
        
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($resultado) {
            $this->titulo = $resultado['titulo'];
            $this->tipo = $resultado['tipo'];
            $this->id_producto = $resultado['id_producto'];
            $this->version = $resultado['version'];
            $this->id_empresa = $resultado['id_empresa'];
            $this->sucursal = $resultado['sucursal'];
            return $resultado;
        }
        
        return false;
    }

    public function listarPorTipo($tipo)
    {
        // NUEVO: Quitar filtro de estado ya que eliminamos completamente
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM gestion_adjuntos WHERE id_archivo = a.id_archivo) as total_adjuntos 
                FROM gestion_archivos a 
                WHERE a.id_empresa = ? 
                AND a.sucursal = ? 
                AND a.tipo = ? 
                ORDER BY a.fecha_creacion DESC";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar consulta listarPorTipo: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("sss", $this->id_empresa, $this->sucursal, $tipo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
        
        return $resultado;
    }

    public function listarPorProducto($id_producto)
    {
        // NUEVO: Quitar filtro de estado ya que eliminamos completamente
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM gestion_adjuntos WHERE id_archivo = a.id_archivo) as total_adjuntos 
                FROM gestion_archivos a 
                WHERE a.id_empresa = ? 
                AND a.sucursal = ? 
                AND a.id_producto = ? 
                ORDER BY a.tipo, a.fecha_creacion DESC";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar consulta listarPorProducto: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("ssi", $this->id_empresa, $this->sucursal, $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
        
        return $resultado;
    }

    public function buscarArchivos($termino, $tipo = null)
    {
        $condicionTipo = $tipo ? "AND a.tipo = ?" : "";
        
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM gestion_adjuntos WHERE id_archivo = a.id_archivo) as total_adjuntos 
                FROM gestion_archivos a 
                LEFT JOIN productos p ON a.id_producto = p.id_producto
                WHERE a.id_empresa = ? 
                AND a.sucursal = ? 
                $condicionTipo
                AND (a.titulo LIKE ? OR p.nombre LIKE ?) 
                ORDER BY a.fecha_creacion DESC";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar consulta buscarArchivos: " . $this->conectar->error);
            return false;
        }
        
        $terminoLike = "%$termino%";
        
        if ($tipo) {
            $stmt->bind_param("sssss", $this->id_empresa, $this->sucursal, $tipo, $terminoLike, $terminoLike);
        } else {
            $stmt->bind_param("ssss", $this->id_empresa, $this->sucursal, $terminoLike, $terminoLike);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
        
        return $resultado;
    }
}