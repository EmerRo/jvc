<?php

class GuiaDetalle
{
    private $guia_detalle_id;
    private $id_guia;
    private $id_producto;
    private $id_repuesto;
    private $tipo_item;
    private $id_guia_equipo;
    private $detalles;
    private $unidad;
    private $cantidad;
    private $precio;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getGuiaDetalleId()
    {
        return $this->guia_detalle_id;
    }

    /**
     * @param mixed $guia_detalle_id
     */
    public function setGuiaDetalleId($guia_detalle_id): void
    {
        $this->guia_detalle_id = $guia_detalle_id;
    }

    /**
     * @return mixed
     */
    public function getIdGuia()
    {
        return $this->id_guia;
    }

    /**
     * @param mixed $id_guia
     */
    public function setIdGuia($id_guia): void
    {
        $this->id_guia = $id_guia;
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
    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    /**
     * @return mixed
     */
    public function getDetalles()
    {
        return $this->detalles;
    }

    /**
     * @param mixed $detalles
     */
    public function setDetalles($detalles): void
    {
        $this->detalles = $detalles;
    }

    /**
     * @return mixed
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * @param mixed $unidad
     */
    public function setUnidad($unidad): void
    {
        $this->unidad = $unidad;
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
    public function setCantidad($cantidad): void
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
    public function setPrecio($precio): void
    {
        $this->precio = $precio;
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
    public function setIdRepuesto($id_repuesto): void
    {
        $this->id_repuesto = $id_repuesto;
    }

    /**
     * @return mixed
     */
    public function getTipoItem()
    {
        return $this->tipo_item;
    }

    /**
     * @param mixed $tipo_item
     */
    public function setTipoItem($tipo_item): void
    {
        $this->tipo_item = $tipo_item;
    }

    /**
     * @return mixed
     */
    public function getIdGuiaEquipo()
    {
        return $this->id_guia_equipo;
    }

    /**
     * @param mixed $id_guia_equipo
     */
    public function setIdGuiaEquipo($id_guia_equipo): void
    {
        $this->id_guia_equipo = $id_guia_equipo;
    }

  public function insertar() {
    try {
        // Registrar los datos que se intentan insertar
        error_log("=== INICIO DEPURACIÓN GUIA_DETALLES ===");
        error_log("ID Guía: " . $this->id_guia);
        error_log("ID Producto: " . $this->id_producto);
        error_log("Detalles: " . $this->detalles);
        error_log("Unidad: " . $this->unidad);
        error_log("Cantidad: " . $this->cantidad);
        error_log("Precio: " . $this->precio);
        
        // Escapar los valores para prevenir SQL injection
        $id_guia = $this->conectar->real_escape_string($this->id_guia);
        $id_producto = $this->id_producto ? $this->conectar->real_escape_string($this->id_producto) : 'NULL';
        $id_repuesto = $this->id_repuesto ? $this->conectar->real_escape_string($this->id_repuesto) : 'NULL';
        $id_guia_equipo = $this->id_guia_equipo ? $this->conectar->real_escape_string($this->id_guia_equipo) : 'NULL';
        $tipo_item = $this->conectar->real_escape_string($this->tipo_item ?: 'producto');
        $detalles = $this->conectar->real_escape_string($this->detalles);
        $unidad = $this->conectar->real_escape_string($this->unidad);
        $cantidad = $this->conectar->real_escape_string($this->cantidad);
        $precio = $this->conectar->real_escape_string($this->precio);

        $sql = "INSERT INTO guia_detalles (
            id_guia,
            id_producto,
            id_repuesto,
            id_guia_equipo,
            tipo_item,
            detalles,
            unidad,
            cantidad,
            precio
        ) VALUES (
            '$id_guia',
            " . ($this->id_producto ? "'$id_producto'" : 'NULL') . ",
            " . ($this->id_repuesto ? "'$id_repuesto'" : 'NULL') . ",
            " . ($this->id_guia_equipo ? "'$id_guia_equipo'" : 'NULL') . ",
            '$tipo_item',
            '$detalles',
            '$unidad',
            '$cantidad',
            '$precio'
        )";

        error_log("SQL a ejecutar: " . $sql);
        
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            error_log("ERROR al insertar: " . $this->conectar->error);
            error_log("Código de error: " . $this->conectar->errno);
            return false;
        }
        
        error_log("Inserción exitosa. ID insertado: " . $this->conectar->insert_id);
        error_log("=== FIN DEPURACIÓN GUIA_DETALLES ===");
        
        return true;
    } catch (Exception $e) {
        error_log("EXCEPCIÓN al insertar detalle de guía: " . $e->getMessage());
        error_log("Traza: " . $e->getTraceAsString());
        return false;
    }
}
    
    public function actualizarProducto()
    {
        if (!$this->guia_detalle_id) {
            throw new Exception("El ID de la guía detalle no está establecido");
        }

        $sql = "UPDATE guia_detalles SET 
                    id_guia = ?,
                    id_producto = ?,
                    detalles = ?,
                    unidad = ?,
                    cantidad = ?,
                    precio = ?
                WHERE guia_detalle_id = ?";

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
        }

        $stmt->bind_param("iissidi", 
            $this->id_guia,
            $this->id_producto,
            $this->detalles,
            $this->unidad,
            $this->cantidad,
            $this->precio,
            $this->guia_detalle_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception("No se encontró el producto para actualizar o no se realizaron cambios");
        }

        $stmt->close();

        return true;
    }
    public function obtenerDetalles() {
        $sql = "SELECT 
            guia_detalle_id,
            id_producto,
            detalles,
            unidad,
            cantidad,
            precio
        FROM guia_detalles 
        WHERE id_guia = ?";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
        }
        
        $stmt->bind_param("i", $this->id_guia);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
    
}