<?php

class CotizacionReporteService
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function obtenerDetalleCotizacion($idCotizacion)
    {
        $sql = "SELECT
                pc.*,
                COALESCE(
                    pc.nombre_producto,
                    CASE WHEN pc.tipo_producto = 'producto' THEN p.nombre
                         WHEN pc.tipo_producto = 'repuesto' THEN r.nombre END
                ) as nombre,
                COALESCE(
                    pc.descripcion_producto,
                    CASE WHEN pc.tipo_producto = 'producto' THEN p.detalle
                         WHEN pc.tipo_producto = 'repuesto' THEN r.detalle END
                ) as descripcion,
                CASE WHEN pc.tipo_producto = 'producto' THEN TRIM(p.codigo)
                     WHEN pc.tipo_producto = 'repuesto' THEN TRIM(r.codigo) END as codigo,
                CASE WHEN pc.tipo_producto = 'producto' THEN p.imagen
                     WHEN pc.tipo_producto = 'repuesto' THEN r.imagen END as imagen
            FROM productos_cotis pc
            LEFT JOIN productos p ON p.id_producto = pc.id_producto AND pc.tipo_producto = 'producto'
            LEFT JOIN repuestos r ON r.id_repuesto = pc.id_producto AND pc.tipo_producto = 'repuesto'
            WHERE pc.id_coti = ?
            ORDER BY codigo ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCotizacion);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerCotizacion($idCotizacion)
    {
        $sql = "SELECT *, aplicar_igv FROM cotizaciones WHERE cotizacion_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerCliente($idCliente)
    {
        $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerEmpresaPorCotizacion($idCotizacion)
    {
        $sql = "SELECT e.* FROM empresas e
                INNER JOIN cotizaciones c ON c.id_empresa = e.id_empresa
                WHERE c.cotizacion_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerUsuario($idUsuario)
    {
        $sql = "SELECT u.nombres, u.telefono, r.nombre as rol
                FROM usuarios u
                INNER JOIN roles r ON r.rol_id = u.id_rol
                WHERE u.usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerAsunto($idAsunto)
    {
        $sql = "SELECT nombre FROM asuntos_coti WHERE id_asunto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idAsunto);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return $data['nombre'];
        }
        return 'No especificado';
    }

    public function obtenerCondiciones($idCotizacion)
    {
        $sql = "SELECT * FROM condiciones_cotizacion WHERE id_cotizacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function obtenerCondicionesDefault()
    {
        $sql = "SELECT * FROM condicion LIMIT 1";
        $result = $this->conexion->query($sql);
        return $result->fetch_assoc();
    }

    public function obtenerCuotas($idCotizacion)
    {
        $sql = "SELECT * FROM cuotas_cotizacion WHERE id_coti = ? ORDER BY fecha ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCotizacion);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerListaProductosServicio($idCotizacion)
    {
        $sql = "SELECT cp.*, p.nombre, p.descripcion as detalle, s.nombre as servicio_nombre
                FROM cotizacion_productos cp
                LEFT JOIN productos p ON cp.producto_id = p.id_producto
                LEFT JOIN servicios s ON cp.servicio_id = s.id_servicio
                WHERE cp.cotizacion_id = ?
                ORDER BY cp.id ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCotizacion);
        $stmt->execute();
        return $stmt->get_result();
    }
}
