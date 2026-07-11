<?php

class Proveedor
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        $id_empresa = $_SESSION['id_empresa'];
        $sql = "SELECT * FROM proveedores WHERE id_empresa = ? AND estado = 1 ORDER BY proveedor_id DESC";
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $id_empresa);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getOne($id)
    {
        $sql = "SELECT * FROM proveedores WHERE proveedor_id = ?";
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function existeDocumento($ruc, $id_empresa, $id_excluir = null)
    {
        $sql = "SELECT COUNT(*) as total FROM proveedores WHERE ruc = ? AND id_empresa = ?";
        if ($id_excluir !== null) {
            $sql .= " AND proveedor_id != ?";
        }
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) return true;
        if ($id_excluir !== null) {
            $stmt->bind_param('sii', $ruc, $id_empresa, $id_excluir);
        } else {
            $stmt->bind_param('si', $ruc, $id_empresa);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] > 0;
    }

    public function insertar($ruc, $razon_social, $direccion, $telefono, $email, $departamento, $provincia, $distrito, $ubigeo)
    {
        $id_empresa = $_SESSION['id_empresa'];
        $sql = "INSERT INTO proveedores (ruc, razon_social, direccion, telefono, email, id_empresa, departamento, provincia, distrito, ubigeo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('sssssissss', $ruc, $razon_social, $direccion, $telefono, $email, $id_empresa, $departamento, $provincia, $distrito, $ubigeo);
        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }
        return false;
    }

    public function editar($id, $ruc, $razon_social, $direccion, $telefono, $email, $departamento, $provincia, $distrito, $ubigeo)
    {
        $sql = "UPDATE proveedores SET ruc = ?, razon_social = ?, direccion = ?, telefono = ?, email = ?,
                departamento = ?, provincia = ?, distrito = ?, ubigeo = ? WHERE proveedor_id = ?";
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('sssssssssi', $ruc, $razon_social, $direccion, $telefono, $email, $departamento, $provincia, $distrito, $ubigeo, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "UPDATE proveedores SET estado = 0 WHERE proveedor_id = ?";
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
