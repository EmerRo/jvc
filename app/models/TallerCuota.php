<?php

class TallerCuota
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function insertar($idCoti, $data)
    {
        if (!empty($data['dias_lista'])) {
            $listaCuotas = json_decode($data['dias_lista'], true);
            $sql = "INSERT INTO cuotas_cotizacion (id_coti, monto, fecha) VALUES (?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);
            foreach ($listaCuotas as $cuota) {
                $stmt->bind_param("ids", $idCoti, $cuota['monto'], $cuota['fecha']);
                $stmt->execute();
            }
        }
    }
}