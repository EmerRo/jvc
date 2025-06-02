<?php

class GestionActivos
{
    private $id;
    private $cliente_razon_social;
    private $motivo;
    private $marca;
    private $equipo;
    private $modelo;
    private $numero_serie;
    private $fecha_salida;
    private $fecha_ingreso;
    private $observaciones; // Campo no obligatorio
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getters y Setters

    public function getId()
    {
        return $this->id;
    }

    public function getClienteRazonSocial()
    {
        return $this->cliente_razon_social;
    }

    public function setClienteRazonSocial($cliente_razon_social)
    {
        $this->cliente_razon_social = strtoupper($cliente_razon_social);
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = strtoupper($motivo);
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    public function getEquipo()
    {
        return $this->equipo;
    }

    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getNumeroSerie()
    {
        return $this->numero_serie;
    }

    public function setNumeroSerie($numero_serie)
    {
        $this->numero_serie = $numero_serie;
    }

    public function getFechaSalida()
    {
        return $this->fecha_salida;
    }

    public function setFechaSalida($fecha_salida)
    {
        $this->fecha_salida = $fecha_salida;
    }

    public function getFechaIngreso()
    {
        return $this->fecha_ingreso;
    }

    public function setFechaIngreso($fecha_ingreso)
    {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
    }

    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    // Métodos CRUD

    public function insertar()
{
    $sql = "INSERT INTO gestion_activos (cliente_razon_social, motivo, marca, equipo, modelo, numero_serie, fecha_salida, fecha_ingreso, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conectar->prepare($sql);
    $stmt->bind_param("sssssssss", $this->cliente_razon_social, $this->motivo, $this->marca, $this->equipo, $this->modelo, $this->numero_serie, $this->fecha_salida, $this->fecha_ingreso, $this->observaciones);
    $result = $stmt->execute();
    if ($result) {
        $this->id = $this->conectar->insert_id;
    }
    return $result;
}
    public function modificar($id)
    {
        $sql = "UPDATE gestion_activos SET cliente_razon_social = ?, motivo = ?, marca = ?, equipo = ?, modelo = ?, numero_serie = ?, fecha_salida = ?, fecha_ingreso = ?, observaciones = ? WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("sssssssssi", $this->cliente_razon_social, $this->motivo, $this->marca, $this->equipo, $this->modelo, $this->numero_serie, $this->fecha_salida, $this->fecha_ingreso, $this->observaciones, $id);
        return $stmt->execute();
    }

    public function obtenerDatos($id)
    {
        $sql = "SELECT * FROM gestion_activos WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->cliente_razon_social = $result['cliente_razon_social'];
            $this->motivo = $result['motivo'];
            $this->marca = $result['marca'];
            $this->equipo = $result['equipo'];
            $this->modelo = $result['modelo'];
            $this->numero_serie = $result['numero_serie'];
            $this->fecha_salida = $result['fecha_salida'];
            $this->fecha_ingreso = $result['fecha_ingreso'];
            $this->observaciones = $result['observaciones'];
        }
        return $result;
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM gestion_activos ORDER BY id DESC";
        return $this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM gestion_activos WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
