<?php

class DocumentoEmpresa
{
    private $id_empresa;
    private $id_tido;
    private $serie;
    private $numero;
    private $conectar;

    /**
     * DocumentoEmpresa constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
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
    public function getIdTido()
    {
        return $this->id_tido;
    }

    /**
     * @param mixed $id_tido
     */
    public function setIdTido($id_tido)
    {
        $this->id_tido = $id_tido;
    }

    /**
     * @return mixed
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param mixed $serie
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function exeSQL($sql){
        return $this->conectar->query($sql);
    }

    public function insertar()
    {
        $sql = "insert into documentos_empresas 
        values ('$this->id_empresa', '$this->id_tido', '$this->serie', '$this->numero')";
        return $this->conectar->ejecutar_idu($sql);
    }

    public function modificar()
    {
        $sql = "update documentos_empresas 
        set serie = '$this->serie', numero = '$this->numero'
        where id_empresa = '$this->id_empresa' and id_tido = '$this->id_tido'";
        return $this->conectar->ejecutar_idu($sql);
    }
    public function obtenerDatos()
    {
        try {
            // Iniciar transacción
            $this->conectar->begin_transaction();
            
            // Obtener el número actual con bloqueo
            $sql = "SELECT serie, numero 
                    FROM documentos_empresas 
                    WHERE id_empresa = '$this->id_empresa' 
                    AND id_tido = '$this->id_tido' 
                    AND sucursal = '{$_SESSION['sucursal']}'
                    FOR UPDATE";
            
            $fila = $this->conectar->query($sql)->fetch_assoc();
            if ($fila) {
                // Guardar los valores actuales
                $this->serie = $fila['serie'];
                $this->numero = $fila['numero'];
                
                // Incrementar el número para la siguiente venta
                $nuevoNumero = $this->numero + 1;
                
                // Actualizar el número en la base de datos
                $sqlUpdate = "UPDATE documentos_empresas 
                             SET numero = $nuevoNumero
                             WHERE id_empresa = '$this->id_empresa' 
                             AND id_tido = '$this->id_tido'
                             AND sucursal = '{$_SESSION['sucursal']}'";
                
                $this->conectar->query($sqlUpdate);
                
                // Confirmar la transacción
                $this->conectar->commit();
                
                return true;
            } else {
                $this->conectar->rollback();
                throw new Exception("No se encontró la configuración del documento");
            }
        } catch (Exception $e) {
            // En caso de error, hacer rollback
            $this->conectar->rollback();
            throw $e;
        }
    }
    public function verFilas($texto)
    {
        $sql = "select de.id_tido, ds.abreviatura, ds.cod_sunat, ds.nombre from documentos_empresas as de 
        inner join documentos_sunat ds on de.id_tido = ds.id_tido 
        where de.id_empresa = '$this->id_empresa' and de.id_tido in ($texto)";
        return $this->conectar->get_Cursor($sql);
    }
    public function consultarProveedor($doc)
    {
        $sql = "SELECT  
        proveedor_id FROM proveedores 
        where ruc = '$doc'";
        return $this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function insertarProveedor($ruc, $razon_social)
    {
        $sql = "INSERT INTO proveedores (ruc,razon_social)values ('$ruc', '$razon_social') ";
        $result = $this->conectar->query($sql);

        if ($result) {
            return $this->conectar->insert_id;
        }
    }
}