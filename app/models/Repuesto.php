<?php

class Repuesto
{
    private $id_repuesto;
    private $descripcion;
    private $precio;
    private $costo;
    private $iscbp;
    private $id_empresa;
    private $ultima_salida;
    private $codsunat;
    private $conectar;

    /**
     * Repuesto constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdRepuesto()
    {
        return $this->id_repuesto;
    }

    /**
     * @param mixed $id_repuesto
     */
    public function setIdRepuesto($id_repuesto)
    {
        $this->id_repuesto = $id_repuesto;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
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

    /**
     * @return mixed
     */
    public function getIscbp()
    {
        return $this->iscbp;
    }

    /**
     * @param mixed $iscbp
     */
    public function setIscbp($iscbp)
    {
        $this->iscbp = $iscbp;
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    /**
     * @return mixed
     */
    public function getUltimaSalida()
    {
        return $this->ultima_salida;
    }

    /**
     * @param mixed $ultima_salida
     */
    public function setUltimaSalida($ultima_salida)
    {
        $this->ultima_salida = $ultima_salida;
    }

    /**
     * @return mixed
     */
    public function getCodsunat()
    {
        return $this->codsunat;
    }

    /**
     * @param mixed $codsunat
     */
    public function setCodsunat($codsunat)
    {
        $this->codsunat = $codsunat;
    }

    public function insertar()
    {
        $sql = "insert into repuestos 
        values ('$this->id_repuesto', '$this->descripcion', '$this->precio', '$this->costo', '$this->iscbp', '$this->id_empresa','{$_SESSION['sucursal']}' ,'$this->ultima_salida', '$this->codsunat')";

        return $this->conectar->ejecutar_idu($sql);
    }

    public function modificar()
    {
        $sql = "update repuestos 
        set descripcion = '$this->descripcion', precio = '$this->precio', costo = '$this->costo', iscbp = '$this->iscbp', codsunat = '$this->codsunat'  
        where id_repuesto = '$this->id_repuesto'";
        return $this->conectar->ejecutar_idu($sql);
    }

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_repuesto) + 1, 1) as codigo 
            from repuestos";
        $this->id_repuesto = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerDatos()
    {
        $sql = "select * 
        from repuestos 
        where id_repuesto = '$this->id_repuesto'";
        $fila = $this->conectar->get_Row($sql);
        $this->descripcion = $fila['descripcion'];
        $this->precio = $fila['precio'];
        $this->costo = $fila['costo'];
        $this->iscbp = $fila['iscbp'];
        $this->id_empresa = $fila['id_empresa'];
        $this->ultima_salida = $fila['ultima_salida'];
        $this->codsunat = $fila['codsunat'];
    }

    public function verFilas($almacen)
    {
        $sql = "SELECT * from repuestos where id_empresa = '$this->id_empresa' and sucursal = '{$_SESSION['sucursal']}' and estado = '1' AND almacen=$almacen order by id_repuesto DESC";

        return $this->conectar->query($sql);
    }

    public function verFilasId($id)
    {
        $sql = "SELECT * from repuestos where id_empresa = '$this->id_empresa' and sucursal = '{$_SESSION['sucursal']}' and estado = '1' AND id_repuesto=$id order by id_repuesto DESC";

        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function BuscarRepuestos($term)
    {
        $sql = "select * from repuestos 
        where id_empresa = '$this->id_empresa' and descripcion like '%$term%' 
        order by descripcion asc";
        return $this->conectar->get_Cursor($sql);
    }
}