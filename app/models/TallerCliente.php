<?php

class TallerCliente
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function gestionar($data)
    {
        $sql = "SELECT * FROM clientes_taller WHERE documento = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $data['num_doc']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($rowCl = $result->fetch_assoc()) {
            $idCli = $rowCl['id_cliente_taller'];
            $sqlUpdate = "UPDATE clientes_taller SET datos = ?, direccion = ?, atencion = ? WHERE id_cliente_taller = ?";
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssi", $data['nom_cli'], $data['dir_cli'], $data['dir2_cli'], $idCli);
            $stmtUpdate->execute();
        } else {
            $sqlInsert = "INSERT INTO clientes_taller (documento, datos, direccion, atencion, id_empresa) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $this->conectar->prepare($sqlInsert);
            $stmtInsert->bind_param("ssssi", $data['num_doc'], $data['nom_cli'], $data['dir_cli'], $data['dir2_cli'], $_SESSION['id_empresa']);
            $stmtInsert->execute();
            $idCli = $this->conectar->insert_id;
        }

        return $idCli;
    }
}