<?php
// app/models/Rubro.php

class Rubro
{
    private $id_rubro;
    private $nombre;
    private $id_empresa;
    private $estado;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getIdRubro()
    {
        return $this->id_rubro;
    }

    public function setIdRubro($id_rubro)
    {
        $this->id_rubro = $id_rubro;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = strtoupper($nombre);
    }

    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    public function insertar()
    {
        try {
            // Preparar la consulta para evitar SQL injection
            $sql = "INSERT INTO rubros (nombre, id_empresa, estado) VALUES (?, ?, 1)";
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("si", $this->nombre, $_SESSION['id_empresa']);
            $result = $stmt->execute();

            if ($result) {
                $this->id_rubro = $this->conectar->insert_id;
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getAllData()
    {
        try {
            $sql = "SELECT id_rubro, nombre FROM rubros WHERE id_empresa = ? AND estado = 1";
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $_SESSION['id_empresa']);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("Error obteniendo resultados: " . $stmt->error);
            }

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getOne($id)
    {
        try {
            $sql = "SELECT * FROM rubros WHERE id_rubro = ? AND id_empresa = ?";
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("ii", $id, $_SESSION['id_empresa']);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("Error obteniendo resultados: " . $stmt->error);
            }

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function editar($id)
    {
        try {
            $sql = "UPDATE rubros SET nombre = ? WHERE id_rubro = ? AND id_empresa = ?";
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("sii", $this->nombre, $id, $_SESSION['id_empresa']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $sql = "UPDATE rubros SET estado = 0 WHERE id_rubro = ? AND id_empresa = ?";
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("ii", $id, $_SESSION['id_empresa']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}