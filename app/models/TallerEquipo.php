<?php

class TallerEquipo
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function insertar($idCoti, $equipos)
    {
        if (!empty($equipos) && is_array($equipos)) {
            $sql = "INSERT INTO taller_cotizaciones_equipos (id_cotizacion, marca, equipo, modelo, numero_serie) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);

            foreach ($equipos as $equipo) {
                $stmt->bind_param(
                    "issss",
                    $idCoti,
                    $equipo['marca'],
                    $equipo['equipo'],
                    $equipo['modelo'],
                    $equipo['numero_serie']
                );
                $stmt->execute();
            }
        }
    }

    public function obtenerPorCotizacion($id_cotizacion)
    {
        $sql = "SELECT * FROM taller_cotizaciones_equipos WHERE id_cotizacion = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerIds($idCoti)
    {
        $equiposIds = [];
        $sql = "SELECT id_cotizacion_equipo, ROW_NUMBER() OVER (ORDER BY id_cotizacion_equipo) - 1 as indice 
                FROM taller_cotizaciones_equipos 
                WHERE id_cotizacion = ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $idCoti);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $equiposIds[$row['indice']] = $row['id_cotizacion_equipo'];
        }

        return $equiposIds;
    }
}