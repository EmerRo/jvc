<?php
// app/models/TipoConstancia.php

class TipoConstancia
{
    private $id;
    private $nombre;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conectar;

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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    public function getFechaModificacion()
    {
        return $this->fecha_modificacion;
    }

    // Métodos CRUD
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM tipos_costancia ORDER BY nombre ASC";
        $result = $this->conectar->query($sql);
        
        $tipos = [];
        while ($fila = $result->fetch_assoc()) {
            $tipos[] = $fila;
        }
        
        return $tipos;
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM tipos_costancia WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($fila = $result->fetch_assoc()) {
            $this->id = $fila['id'];
            $this->nombre = $fila['nombre'];
            $this->fecha_creacion = $fila['fecha_creacion'];
            $this->fecha_modificacion = $fila['fecha_modificacion'];
            return true;
        }
        return false;
    }

    public function insertar()
    {
        $sql = "INSERT INTO tipos_costancia (nombre) VALUES (?)";
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
        $sql = "UPDATE tipos_costancia SET nombre = ? WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("si", $this->nombre, $this->id);
        return $stmt->execute();
    }

    public function eliminar()
    {
        $sql = "DELETE FROM tipos_costancia WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }
}