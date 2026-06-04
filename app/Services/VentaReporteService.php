<?php

class VentaReporteService
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function obtenerEmpresa($idEmpresa)
    {
        $sql = "SELECT * FROM empresas WHERE id_empresa = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idEmpresa);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerVenta($idVenta)
    {
        $sql = "SELECT * FROM ventas WHERE id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerCliente($idCliente)
    {
        $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerProductosVenta($idVenta, $guiaId = null)
    {
        if ($guiaId) {
            $sql = "SELECT productos_ventas.*,
                    COALESCE(gd.detalles, p.detalle) as descripcion, p.imagen,
                    COALESCE(gd.detalles, p.nombre) as nombre, p.codigo,
                    ve.marca, ve.equipo, ve.modelo, ve.numero_serie
                FROM productos_ventas
                LEFT JOIN guia_detalles gd ON gd.id_producto = productos_ventas.id_producto AND gd.id_guia = ?
                LEFT JOIN productos p ON p.id_producto = productos_ventas.id_producto
                LEFT JOIN ventas_equipos ve ON ve.id_venta_equipo = productos_ventas.id_venta_equipo
                WHERE productos_ventas.id_venta = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $guiaId, $idVenta);
        } else {
            $sql = "SELECT productos_ventas.*, COALESCE(p.detalle, '') as descripcion, p.imagen,
                    COALESCE(p.nombre, r.nombre, '') as nombre, COALESCE(p.codigo, '') as codigo,
                    ve.marca, ve.equipo, ve.modelo, ve.numero_serie
                FROM productos_ventas
                LEFT JOIN productos p ON p.id_producto = productos_ventas.id_producto
                LEFT JOIN repuestos r ON r.id_repuesto = productos_ventas.id_producto
                LEFT JOIN ventas_equipos ve ON ve.id_venta_equipo = productos_ventas.id_venta_equipo
                WHERE productos_ventas.id_venta = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idVenta);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerProductosVentaSimple($idVenta)
    {
        $sql = "SELECT productos_ventas.*, COALESCE(p.descripcion, '') as descripcion, COALESCE(p.codigo, '') as codigo,
                ve.marca, ve.equipo, ve.modelo, ve.numero_serie
            FROM productos_ventas
            LEFT JOIN productos p ON p.id_producto = productos_ventas.id_producto
            LEFT JOIN ventas_equipos ve ON ve.id_venta_equipo = productos_ventas.id_venta_equipo
            WHERE productos_ventas.id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerServiciosVenta($idVenta)
    {
        $sql = "SELECT * FROM ventas_servicios WHERE id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerVentaSunat($idVenta)
    {
        $sql = "SELECT * FROM ventas_sunat WHERE id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerGuiaRemision($idVenta)
    {
        $sql = "SELECT * FROM guia_remision WHERE id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerCuotasVenta($idVenta)
    {
        $sql = "SELECT * FROM dias_ventas WHERE id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerMetodoPago($idMetodoPago)
    {
        $sql = "SELECT * FROM metodo_pago WHERE id_metodo_pago = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idMetodoPago);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerSucursal($codSucursal, $idEmpresa)
    {
        $sql = "SELECT * FROM sucursales WHERE cod_sucursal = ? AND empresa_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $codSucursal, $idEmpresa);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerNotaElectronica($idNota)
    {
        $sql = "SELECT ne.*, ds.nombre as nota_nombre, v.id_cliente
            FROM notas_electronicas ne
            JOIN documentos_sunat ds ON ne.tido = ds.id_tido
            JOIN ventas v ON ne.id_venta = v.id_venta
            WHERE ne.nota_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idNota);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerNotaElectronicaSunat($idNota)
    {
        $sql = "SELECT * FROM notas_electronicas_sunat WHERE id_notas_electronicas = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idNota);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerClientePorId($idCliente)
    {
        return $this->obtenerCliente($idCliente);
    }

    public function verificarVentaTieneEquipos($idVenta)
    {
        $sql = "SELECT 1 FROM ventas_equipos WHERE id_venta = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function obtenerReporteVentasPorProducto($codigo, $fecha1, $fecha2 = null)
    {
        if (empty($fecha2)) {
            $sql = "SELECT p.descripcion, v.fecha_emision, ds.nombre nombre_documento,
                    CONCAT(v.serie,'-',v.numero) venta_sn, pv.cantidad, pv.precio, pv.precio_usado,
                    tp.nombre nom_pago
                FROM productos_ventas pv
                JOIN productos p ON p.id_producto = pv.id_producto
                JOIN ventas v ON v.id_venta = pv.id_venta
                JOIN tipo_pago tp ON tp.tipo_pago_id = v.id_tipo_pago
                JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                WHERE TRIM(p.codigo) = ? AND v.fecha_emision >= ? AND v.estado <> '2'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ss", $codigo, $fecha1);
        } else {
            $sql = "SELECT p.descripcion, v.fecha_emision, ds.nombre nombre_documento,
                    CONCAT(v.serie,'-',v.numero) venta_sn, pv.cantidad, pv.precio, pv.precio_usado,
                    tp.nombre nom_pago
                FROM productos_ventas pv
                JOIN productos p ON p.id_producto = pv.id_producto
                JOIN ventas v ON v.id_venta = pv.id_venta
                JOIN tipo_pago tp ON tp.tipo_pago_id = v.id_tipo_pago
                JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                WHERE TRIM(p.codigo) = ? AND v.fecha_emision BETWEEN ? AND ? AND v.estado <> '2'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("sss", $codigo, $fecha1, $fecha2);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerVentasPorContacto($idCliente, $idTido)
    {
        $sql = "SELECT v.id_venta, v.fecha_emision, v.serie, v.numero, v.total, v.estado,
                ds.nombre, v.moneda, v.cod_sunat
            FROM ventas v
            JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            WHERE v.id_cliente = ?
            ORDER BY v.fecha_emision DESC, v.numero DESC
            LIMIT 20";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerReporteProductos($id)
    {
        $sql = "SELECT * FROM ventas WHERE id_venta = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerUsuario($idUsuario)
    {
        $sql = "SELECT u.nombres, u.apellidos FROM usuarios u WHERE u.usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
