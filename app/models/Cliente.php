<?php

class Cliente
{
    private $id_cliente;
    private $documento;
    private $datos;
    private $direccion;
    private $direccion2;
    private $id_empresa;
    private $telefono;
    private $telefono2;
    private $email;
    private $total_venta;
    private $ultima_venta;
    private $rubro;
    private $conectar;

    /**
     * Cliente constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getDireccion2()
    {
        return $this->direccion2;
    }

    /**
     * @param mixed $direccion2
     */
    public function setDireccion2($direccion2): void
    {
        $this->direccion2 = $direccion2;
    }

    /**
     * @return mixed
     */
    public function getTelefono2()
    {
        return $this->telefono2;
    }

    /**
     * @param mixed $telefono2
     */
    public function setTelefono2($telefono2): void
    {
        $this->telefono2 = $telefono2;
    }



    /**
     * @return mixed
     */
    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    /**
     * @param mixed $id_cliente
     */
    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    /**
     * @return mixed
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * @param mixed $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * @return mixed
     */
    public function getDatos()
    {
        return $this->datos;
    }

    /**
     * @param mixed $datos
     */
    public function setTelefono($telefono)
    {
        $this->telefono = strtoupper($telefono);
    }
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param mixed $datos
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);
    }
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $datos
     */
    public function setDatos($datos)
    {
        $this->datos = strtoupper($datos);
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = strtoupper($direccion);
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
    public function getTotalVenta()
    {
        return $this->total_venta;
    }

    /**
     * @param mixed $total_venta
     */
    public function setTotalVenta($total_venta)
    {
        $this->total_venta = $total_venta;
    }

    /**
     * @return mixed
     */
    public function getUltimaVenta()
    {
        return $this->ultima_venta;
    }

    /**
     * @param mixed $ultima_venta
     */
    public function setUltimaVenta($ultima_venta)
    {
        $this->ultima_venta = $ultima_venta;
    }

    public function setRubro($rubro) {
        $this->rubro = $rubro;
    }
    
    public function getRubro() {
        return $this->rubro;
    }

    /**
     * Verificar si ya existe un cliente con el mismo documento en la empresa
     * @param string $documento
     * @param int $id_empresa
     * @param int|null $id_cliente_excluir - Para excluir un cliente específico (útil en edición)
     * @return bool
     */
    public function existeDocumento($documento, $id_empresa, $id_cliente_excluir = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM clientes 
                    WHERE documento = ? AND id_empresa = ?";
            
            // Si se proporciona un ID de cliente a excluir (para edición)
            if ($id_cliente_excluir !== null) {
                $sql .= " AND id_cliente != ?";
            }
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
            }
            
            if ($id_cliente_excluir !== null) {
                $stmt->bind_param("sii", $documento, $id_empresa, $id_cliente_excluir);
            } else {
                $stmt->bind_param("si", $documento, $id_empresa);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return $row['total'] > 0;
        } catch (Exception $e) {
            error_log("Error al verificar documento: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function insertar() {
        try {
            $sql = "INSERT INTO clientes (
                documento, datos, direccion, direccion2, telefono, telefono2, 
                email, id_empresa, ultima_venta, total_venta, id_rubro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
            }
            
            $ultima_venta = '1000-01-01';
            $total_venta = 0;
            $id_empresa = $_SESSION['id_empresa'];
            
            $stmt->bind_param(
                "sssssssisdi", 
                $this->documento, 
                $this->datos, 
                $this->direccion, 
                $this->direccion2, 
                $this->telefono, 
                $this->telefono2, 
                $this->email, 
                $id_empresa, 
                $ultima_venta, 
                $total_venta, 
                $this->rubro
            );
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
            
            $this->id_cliente = $this->conectar->insert_id;
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error al insertar cliente: " . $e->getMessage());
            throw $e;
        }
    }
    public function modificar($documento, $datos, $id_cliente)
    {

        $sql = "update clientes 
        set documento = '$documento', datos = '$datos' 
        where id_cliente = '$id_cliente'";
        $result =  $this->conectar->query($sql);
        if ($result) {
            $this->id_cliente = $this->conectar->insert_id;
        }
        return $result;
    }

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_cliente) + 1, 1) as codigo from clientes";
        $this->id_cliente = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerDatos()
    {
        $sql = "select * 
        from clientes 
        where id_cliente = '$this->id_cliente'";
        $fila = $this->conectar->query($sql)->fetch_assoc();
        $this->documento = $fila['documento'];
        $this->datos = $fila['datos'];
        $this->direccion = $fila['direccion'];
        $this->id_empresa = $fila['id_empresa'];
        $this->ultima_venta = $fila['ultima_venta'];
        $this->total_venta = $fila['total_venta'];
    }

    public function verificarDocumento()
    {
        $sql = "select *
        from clientes 
        where documento = '$this->documento' and id_empresa = '$this->id_empresa'";
        $result = $this->conectar->query($sql);
        if ($row = $result->fetch_assoc()) {
            $this->id_cliente = $row['id_cliente'];
            $this->datos = $row['datos'];
            $this->documento = $row['documento'];
            $this->email = $row['email'];
            $this->telefono = $row['telefono'];
            return true;
        }
        return false;
    }

    public function verFilas()
    {
        $sql = "select * from clientes where id_empresa = '$this->id_empresa'";
        return $this->conectar->query($sql);
    }

    public function buscarClientes($termino)
    {
        $sql = "select * from clientes 
        where id_empresa = '$this->id_empresa' and (datos like '%$termino%' or documento like '%$termino%') 
        order by datos asc";
        return $this->conectar->query($sql);
    }
    public function idLast()
    {

        try {
            $sql = "SELECT id_cliente,documento,datos,direccion,telefono,email,ultima_venta,total_venta FROM clientes  ORDER BY id_cliente DESC LIMIT 1";
            $fila = $this->conectar->query($sql)->fetch_object();
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function getAllData()
    {
        try {
            $sql = "SELECT id_cliente,documento,datos,email,telefono,ultima_venta,total_venta FROM clientes where id_empresa='{$_SESSION['id_empresa']}'";
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
  // En el modelo Cliente.php - método getOne
public function getOne($id)
{
    try {
        $sql = "SELECT c.*, r.nombre as rubro_nombre 
                FROM clientes c 
                LEFT JOIN rubros r ON c.id_rubro = r.id_rubro 
                WHERE c.id_cliente = ?";
        
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cliente = [];
        while ($row = $result->fetch_assoc()) {
            $cliente[] = $row;
        }
        
        $stmt->close();
        
        return $cliente;
    } catch (Exception $e) {
        error_log("Error al obtener cliente: " . $e->getMessage());
        throw $e;
    }
}
    public function cuentasCobrar()
    {
        try {
            $sql = "SELECT ventas.id_venta,ventas.fecha_emision,ventas.fecha_vencimiento,c.datos,dv.estado,dv.dias_venta_id FROM ventas LEFT JOIN dias_ventas AS dv ON
            ventas.id_venta=dv.id_venta 
            LEFT JOIN clientes AS c ON 
            ventas.id_cliente = c.id_cliente 
            WHERE ventas.id_tipo_pago = 2";
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function cuentasCobrarEstado($id)
    {
        try {
            $sql = "UPDATE dias_ventas set estado = 0 WHERE dias_venta_id = $id";
            $result =  $this->conectar->query($sql);
            return $result;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    public function editar($id)
    {
        try {
            $sql = "UPDATE clientes SET 
                    documento = ?, 
                    datos = ?, 
                    direccion = ?,
                    direccion2 = ?,
                    telefono = ?,
                    telefono2 = ?,
                    email = ?,
                    id_rubro = ?
                    WHERE id_cliente = ?";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
            }
            
            // Convertir el rubro a NULL si está vacío
            $rubro = empty($this->rubro) ? null : $this->rubro;
            
            $stmt->bind_param(
                "sssssssii", 
                $this->documento, 
                $this->datos, 
                $this->direccion, 
                $this->direccion2, 
                $this->telefono, 
                $this->telefono2, 
                $this->email, 
                $rubro,
                $id
            );
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
            
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error al editar cliente: " . $e->getMessage());
            throw $e;
        }
    }
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM clientes WHERE  id_cliente = '$id' ";
            $fila = mysqli_query($this->conectar, $sql);
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}