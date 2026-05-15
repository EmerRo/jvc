<?php

class Almacen
{
    private $id_empresa;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    public function listar()
    {
        $sql = "SELECT * FROM almacenes WHERE id_empresa = '$this->id_empresa' AND estado = '1' ORDER BY id_almacen ASC";
        $result = $this->conectar->query($sql);
        $almacenes = [];
        while ($row = $result->fetch_assoc()) {
            $almacenes[] = $row;
        }
        return $almacenes;
    }

    public function agregar($nombre)
    {
        $nombre = $this->conectar->real_escape_string($nombre);

        $sql = "INSERT INTO almacenes (nombre, id_empresa) VALUES ('$nombre', '$this->id_empresa')";
        $this->conectar->query($sql);
        $id = $this->conectar->insert_id;

        if ($id) {
            // Crear la vista SQL para el nuevo almacén
            $this->crearVista($id);
        }

        return $id;
    }

    private function crearVista($almacenId)
    {
        $sucursal = $_SESSION['sucursal'] ?? '1';
        $sql = "CREATE OR REPLACE VIEW view_productos_$almacenId AS
            SELECT p.id_producto, p.cod_barra, p.nombre, p.precio, p.costo, p.cantidad,
                   p.iscbp, p.id_empresa, p.sucursal, p.ultima_salida, p.codsunat,
                   p.usar_barra, p.precio_mayor, p.precio_menor, p.razon_social, p.ruc,
                   p.estado, p.almacen, p.precio2, p.precio3, p.precio4, p.precio_unidad,
                   p.codigo, p.imagen, p.detalle,
                   c.nombre AS categoria, u.nombre AS unidad, p.moneda
            FROM productos p
            LEFT JOIN categorias ON categorias.id = p.categoria
            LEFT JOIN unidades u ON u.id = p.unidad
            WHERE p.id_empresa = '$this->id_empresa'
              AND p.sucursal = '$sucursal'
              AND p.estado = '1'
              AND p.almacen = '$almacenId'
            ORDER BY p.id_producto";

        $this->conectar->query($sql);
    }
}
