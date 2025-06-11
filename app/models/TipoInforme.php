<?php

class TipoInforme
{
    private $id;
    private $nombre;
    // private $descripcion;
    private $activo;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getters y setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    
    // public function getDescripcion() { return $this->descripcion; }
    // public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    
    public function getActivo() { return $this->activo; }
    public function setActivo($activo) { $this->activo = $activo; }

    // Métodos CRUD
    public function insertar()
    {
        $sql = "INSERT INTO tipos_informe (nombre) VALUES (?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $this->nombre);
        $result = $stmt->execute();
        
        if ($result) {
            $this->id = $this->conectar->insert_id;
        }
        return $result;
    }

    public function actualizar()
    {
        $sql = "UPDATE tipos_informe SET nombre = ? WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("si", $this->nombre, $this->id);
        return $stmt->execute();
    }

    public function eliminar()
    {
        $sql = "DELETE FROM tipos_informe WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }

    public function obtenerTodos()
    {
        $sql = "SELECT * FROM tipos_informe WHERE activo = 1 ORDER BY nombre";
        $result = $this->conectar->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM tipos_informe WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($fila = $result->fetch_assoc()) {
            $this->id = $fila['id'];
            $this->nombre = $fila['nombre'];
            // $this->descripcion = $fila['descripcion'];
            $this->activo = $fila['activo'];
            return true;
        }
        return false;
    }
}