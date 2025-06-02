<?php

class GestionArchivo
{
    private $id_archivo;
    private $titulo;
    private $tipo;
    private $id_producto;
    private $version;
    private $estado;
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
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
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
        $sql = "INSERT INTO gestion_archivos (titulo, tipo, id_producto, version, estado, id_empresa, sucursal) 
                VALUES ('$this->titulo', '$this->tipo', " . ($this->id_producto ? "'$this->id_producto'" : "NULL") . ", 
                '$this->version', '$this->estado', '$this->id_empresa', '$this->sucursal')";
        
        if ($this->conectar->query($sql)) {
            return $this->conectar->insert_id;
        }
        return false;
    }

    public function modificar()
    {
        $sql = "UPDATE gestion_archivos 
                SET titulo = '$this->titulo', 
                    tipo = '$this->tipo', 
                    id_producto = " . ($this->id_producto ? "'$this->id_producto'" : "NULL") . ", 
                    version = '$this->version', 
                    estado = '$this->estado' 
                WHERE id_archivo = '$this->id_archivo'";
        
        return $this->conectar->query($sql);
    }

    public function eliminar()
    {
        $sql = "UPDATE gestion_archivos SET estado = '0' WHERE id_archivo = '$this->id_archivo'";
        return $this->conectar->query($sql);
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * FROM gestion_archivos WHERE id_archivo = '$this->id_archivo'";
        $fila = $this->conectar->query($sql)->fetch_assoc();
        
        if ($fila) {
            $this->titulo = $fila['titulo'];
            $this->tipo = $fila['tipo'];
            $this->id_producto = $fila['id_producto'];
            $this->version = $fila['version'];
            $this->estado = $fila['estado'];
            $this->id_empresa = $fila['id_empresa'];
            $this->sucursal = $fila['sucursal'];
            return true;
        }
        return false;
    }

    public function listarPorTipo($tipo)
    {
        $sql = "SELECT a.*, 
                (SELECT nombre_adjunto FROM gestion_adjuntos WHERE id_archivo = a.id_archivo AND es_principal = 1 LIMIT 1) as adjunto_principal 
                FROM gestion_archivos a 
                WHERE a.id_empresa = '$this->id_empresa' 
                AND a.sucursal = '$this->sucursal' 
                AND a.estado = '1' 
                AND a.tipo = '$tipo' 
                ORDER BY a.fecha_actualizacion DESC";
        
        return $this->conectar->query($sql);
    }

    public function listarPorProducto($id_producto)
    {
        $sql = "SELECT a.*, 
                (SELECT nombre_adjunto FROM gestion_adjuntos WHERE id_archivo = a.id_archivo AND es_principal = 1 LIMIT 1) as adjunto_principal 
                FROM gestion_archivos a 
                WHERE a.id_empresa = '$this->id_empresa' 
                AND a.sucursal = '$this->sucursal' 
                AND a.estado = '1' 
                AND a.id_producto = '$id_producto' 
                ORDER BY a.tipo, a.fecha_actualizacion DESC";
        
        return $this->conectar->query($sql);
    }

    public function buscarArchivos($termino, $tipo = null)
    {
        $condicionTipo = $tipo ? "AND a.tipo = '$tipo'" : "";
        
        $sql = "SELECT a.*, 
                (SELECT nombre_adjunto FROM gestion_adjuntos WHERE id_archivo = a.id_archivo AND es_principal = 1 LIMIT 1) as adjunto_principal 
                FROM gestion_archivos a 
                LEFT JOIN productos p ON a.id_producto = p.id_producto
                WHERE a.id_empresa = '$this->id_empresa' 
                AND a.sucursal = '$this->sucursal' 
                AND a.estado = '1' 
                $condicionTipo
                AND (a.titulo LIKE '%$termino%' OR p.nombre LIKE '%$termino%') 
                ORDER BY a.fecha_actualizacion DESC";
        
        return $this->conectar->query($sql);
    }
}