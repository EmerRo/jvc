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
    private $moneda;
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

    /**
     * @return mixed
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * @param mixed $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
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
        $this->moneda = $fila['moneda'];
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

    public function listarAlmacenes()
    {
        $sql = "SELECT * FROM almacenes WHERE id_empresa = '$this->id_empresa' AND estado = '1' ORDER BY principal DESC, id_almacen ASC";
        $result = $this->conectar->query($sql);
        $almacenes = [];
        while ($row = $result->fetch_assoc()) {
            $almacenes[] = $row;
        }
        return $almacenes;
    }

    public function crearVistasFaltantes()
    {
        $sql = "SELECT id_almacen, nombre FROM almacenes WHERE id_empresa = '$this->id_empresa' AND estado = '1'";
        $result = $this->conectar->query($sql);
        
        $creados = 0;
        while ($row = $result->fetch_assoc()) {
            $almacenId = $row['id_almacen'];
            $checkSql = "SELECT COUNT(*) as cnt FROM information_schema.views 
                         WHERE table_schema = DATABASE() AND table_name = 'view_repuestos_$almacenId'";
            $checkResult = $this->conectar->query($checkSql);
            $checkRow = $checkResult->fetch_assoc();
            
            if ($checkRow['cnt'] == 0) {
                if ($this->crearVista($almacenId)) {
                    $creados++;
                }
            }
        }
        return $creados;
    }

    private function crearVista($almacenId)
    {
        $checkSql = "SELECT COUNT(*) as cnt FROM information_schema.views 
                     WHERE table_schema = DATABASE() AND table_name = 'view_repuestos_$almacenId'";
        $checkResult = $this->conectar->query($checkSql);
        $checkRow = $checkResult->fetch_assoc();
        
        if ($checkRow['cnt'] > 0) {
            return true;
        }

        $sucursal = $_SESSION['sucursal'] ?? '1';
        
        $dropSql = "DROP VIEW IF EXISTS view_repuestos_$almacenId";
        $this->conectar->query($dropSql);
        
        $sql = "CREATE VIEW view_repuestos_$almacenId AS
            SELECT r.id_repuesto, r.codigo, r.nombre, r.detalle, r.precio, r.costo,
                   r.cantidad, r.almacen, r.unidad, r.iscbp, r.cod_barra,
                   r.id_empresa, r.sucursal, r.estado, r.precio_unidad,
                   r.precio2, r.precio3, r.precio4, r.precio_mayor, r.precio_menor,
                   r.moneda, r.codsunat, r.razon_social, r.ruc, r.usar_multiprecio,
                   r.usar_barra, r.ultima_salida, r.descripcion,
                   c.nombre AS categoria, u.nombre AS unidad_nombre
            FROM repuestos r
            LEFT JOIN categorias c ON c.id = r.categoria
            LEFT JOIN unidades u ON u.id = r.unidad
            WHERE r.id_empresa = '$this->id_empresa'
              AND r.sucursal = '$sucursal'
              AND r.estado = '1'
              AND r.almacen = '$almacenId'
            ORDER BY CASE WHEN r.codigo LIKE 'JVC%' THEN 0 ELSE 1 END, r.codigo ASC";

        $result = $this->conectar->query($sql);
        
        if ($result) {
            error_log("Vista view_repuestos_$almacenId creada exitosamente");
        } else {
            error_log("Error al crear vista view_repuestos_$almacenId: " . $this->conectar->error);
        }
        
        return $result;
    }
}