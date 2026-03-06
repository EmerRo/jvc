<?php

class ProductoVenta
{
    private $id_producto;
    private $id_venta;
    private $cantidad;
    private $precio;
    private $costo;
    private $conectar;
    private $precio_usado;
    private $id_venta_equipo;
    private $id_cotizacion_equipo;


    private $sql;
    private $sql_error;
    /**
     * ProductoVenta constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param mixed $sql
     */
    public function setSql($sql): void
    {
        $this->sql = $sql;
    }

    /**
     * @return mixed
     */
    public function getSqlError()
    {
        return $this->sql_error;
    }

    /**
     * @param mixed $sql_error
     */
    public function setSqlError($sql_error): void
    {
        $this->sql_error = $sql_error;
    }

    /**
     * @return mixed
     */
    public function getIdProducto()
    {
        return $this->id_producto;
    }

    /**
     * @param mixed $id_producto
     */
    public function setIdProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    /**
     * @return mixed
     */
    public function getIdVenta()
    {
        return $this->id_venta;
    }

    /**
     * @param mixed $id_venta
     */
    public function setIdVenta($id_venta)
    {
        $this->id_venta = $id_venta;
    }

    /**
     * @return mixed
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param mixed $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return mixed
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param mixed $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    /**
     * @return mixed
     */
    public function getPrecioUsado()
    {
        return $this->precio_usado;
    }

    /**
     * @param mixed $precio
     */
    public function setPrecioUsado($precio_usado)
    {
        // Guardar solo 1 caracter (flag), compatible con schema char(1)
        $this->precio_usado = substr((string)$precio_usado, 0, 1);
    }

    public function getIdVentaEquipo()
    {
        return $this->id_venta_equipo;
    }

    public function setIdVentaEquipo($id_venta_equipo)
    {
        $this->id_venta_equipo = $id_venta_equipo;
    }

    public function getIdCotizacionEquipo()
    {
        return $this->id_cotizacion_equipo;
    }

    public function setIdCotizacionEquipo($id_cotizacion_equipo)
    {
        $this->id_cotizacion_equipo = $id_cotizacion_equipo;
    }

    /**
     * @return mixed
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * @param mixed $costo
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;
    }

    public function insertar()
    {
        $idVentaEquipo = $this->id_venta_equipo ? "'{$this->id_venta_equipo}'" : "NULL";
        $idCotiEquipo = $this->id_cotizacion_equipo ? "'{$this->id_cotizacion_equipo}'" : "NULL";
        $sql = "insert into productos_ventas 
        (id_producto, id_venta, cantidad, precio, costo, id_venta_equipo, id_cotizacion_equipo, precio_usado)
        values ('$this->id_producto', '$this->id_venta', '$this->cantidad', '$this->precio', '$this->costo', $idVentaEquipo, $idCotiEquipo, '$this->precio_usado')";
        //echo $sql;
        $this->sql=$sql;
        $result = $this->conectar->query($sql);

        if (!$result){
            $this->sql_error= $this->conectar->error;
        }

        $sql = "update productos set cantidad = cantidad-$this->cantidad where id_producto='$this->id_producto'";
        //echo $sql;
        $this->conectar->query($sql);

        // ✅ Registrar en historial de stock con serie y número
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Sistema';
        
        // Obtener serie y número de la venta
        $sqlVenta = "SELECT serie, numero FROM ventas WHERE id_venta = '$this->id_venta'";
        $resultVenta = $this->conectar->query($sqlVenta);
        $observacion = 'Venta ID: ' . $this->id_venta;
        
        if ($resultVenta && $rowVenta = $resultVenta->fetch_assoc()) {
            $serie = $rowVenta['serie'];
            $numero = $rowVenta['numero'];
            $observacion = "Venta {$serie}-{$numero}";
        }
        
        $sqlHistorial = "INSERT INTO historial_stock (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                         VALUES ('$this->id_producto', 'EGRESO', '$this->cantidad', NOW(), '$usuario', '$observacion')";
        $this->conectar->query($sqlHistorial);

        return $result;
    }

    public function eliminar($id_venta)
    {
        $sql = "delete from productos_ventas 
        where id_venta =  '$id_venta'";
        return $this->conectar->query($sql);
    }

    public function verFilas()
    {
        $sql = "select pv.id_producto, p.codigo, p.nombre, p.detalle, p.iscbp, pv.precio, pv.cantidad, pv.costo, p.codsunat 
        from productos_ventas as pv 
        inner join productos p on pv.id_producto = p.id_producto 
        where pv.id_venta = '$this->id_venta'";
        return $this->conectar->query($sql);
    }
}
