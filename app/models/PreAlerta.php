<?php

class PreAlerta
{
    private $id_preAlerta;
    private $cliente_razon_social;
    private $cliente_ruc;
    private $direccion;
    private $atencion_encargado;
    private $marca;
    private $modelo;
    private $equipo;
    private $numero_serie;
    private $fecha_ingreso;
    private $origen; 

    private $observaciones;
    private $detalles = array();
    private $conectar;


    /**
     * Cliente constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getters y Setters existentes...

    public function getId_preAlerta()
    {
        return $this->id_preAlerta;
    }

    public function setId_preAlerta($id_preAlerta)
    {
        $this->id_preAlerta = $id_preAlerta;
    }

    public function getCliente_Rsocial()
    {
        return $this->cliente_razon_social;
    }

    public function setCliente_Rsocial($cliente_razon_social)
    {
        $this->cliente_razon_social = strtoupper($cliente_razon_social);
    }

    public function getCliente_ruc()
    {
        return $this->cliente_ruc;
    }

    public function setCliente_Ruc($cliente_ruc)
    {
        $this->cliente_ruc = strtoupper($cliente_ruc);
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = strtoupper($direccion);
    }

    public function getAtencion_Encargado()
    {
        return $this->atencion_encargado;
    }

    public function setAtencion_Encargado($atencion_encargado)
    {
        $this->atencion_encargado = strtoupper($atencion_encargado);
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    public function getEquipo()
    {
        return $this->equipo;
    }

    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getNumero_Serie()
    {
        return $this->numero_serie;
    }

    public function setNumero_Serie($numero_serie)
    {
        $this->numero_serie = $numero_serie;
    }

    public function getFecha_Ingreso()
    {
        return $this->fecha_ingreso;
    }

    public function setFecha_Ingreso($fecha_ingreso)
    {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    // Nuevos métodos para la propiedad 'origen'
    public function getOrigen()
    {
        return $this->origen;
    }

    public function setOrigen($origen)
    {
        $this->origen = strtoupper($origen);
    }
    public function setDetalles($detalles) {
        $this->detalles = $detalles;
    }
    
    public function getDetalles() {
        return $this->detalles;
    }

    public function getObservaciones() {
        return $this->observaciones;
    }
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }


  public function insertar() {
    try {
        error_log("=== DEBUGGING MODELO ===");
        error_log("Detalles a insertar: " . print_r($this->detalles, true));
        
        $this->conectar->begin_transaction();

        // Insertar en pre_alerta
        $sql = "INSERT INTO pre_alerta (cliente_razon_social, cliente_ruc, direccion, 
                atencion_encargado, fecha_ingreso, origen,observaciones) 
                VALUES (?, ?, ?, ?, ?, ?,?)";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("sssssss", 
            $this->cliente_razon_social, 
            $this->cliente_ruc, 
            $this->direccion, 
            $this->atencion_encargado, 
            $this->fecha_ingreso, 
            $this->origen,
            $this->observaciones
        );
        
        if (!$stmt->execute()) {
            error_log("Error al insertar pre_alerta: " . $stmt->error);
            throw new Exception("Error al insertar pre_alerta: " . $stmt->error);
        }
        
        $id_preAlerta = $this->conectar->insert_id;
        error_log("ID pre_alerta insertado: " . $id_preAlerta);
        
        // Insertar detalles
        if (!empty($this->detalles)) {
            $sqlDetalle = "INSERT INTO pre_alerta_detalles (id_preAlerta, marca, equipo, modelo, numero_serie) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmtDetalle = $this->conectar->prepare($sqlDetalle);
            
            foreach ($this->detalles as $detalle) {
                error_log("Insertando detalle: " . print_r($detalle, true));
                
                // Verificar que existan las claves necesarias
                if (!isset($detalle['marca']) || !isset($detalle['equipo']) || 
                    !isset($detalle['modelo']) || !isset($detalle['numero_serie'])) {
                    error_log("Detalle incompleto: " . print_r($detalle, true));
                    throw new Exception("Detalle de equipo incompleto");
                }
                
                $stmtDetalle->bind_param("issss", 
                    $id_preAlerta,
                    $detalle['marca'],
                    $detalle['equipo'],
                    $detalle['modelo'],
                    $detalle['numero_serie']
                );
                
                if (!$stmtDetalle->execute()) {
                    error_log("Error SQL al insertar detalle: " . $stmtDetalle->error);
                    throw new Exception("Error al insertar detalle: " . $stmtDetalle->error);
                }
                
                error_log("Detalle insertado correctamente");
            }
        }
        
        $this->conectar->commit();
        $this->id_preAlerta = $id_preAlerta;
        error_log("Inserción completada exitosamente");
        return true;
        
    } catch (Exception $e) {
        $this->conectar->rollback();
        error_log("Error en modelo insertar: " . $e->getMessage());
        return false;
    }
}

    // Métodos existentes...

    public function modificar($cliente_razon_social, $cliente_ruc, $id_preAlerta)
    {
        $sql = "UPDATE pre_alerta SET cliente_razon_social = ?, cliente_ruc = ? WHERE id_preAlerta = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ssi", $cliente_razon_social, $cliente_ruc, $id_preAlerta);
        return $stmt->execute();
    }

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_preAlerta) + 1, 1) as codigo from pre_alerta";
        $this->id_preAlerta = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerDatosConDetalles($id_preAlerta)
    {
        $sql = "SELECT pa.*, 
                       GROUP_CONCAT(CONCAT_WS('|', pad.id, pad.marca, pad.equipo, pad.modelo, pad.numero_serie) SEPARATOR '##') as detalles_equipos
                FROM pre_alerta pa
                LEFT JOIN pre_alerta_detalles pad ON pa.id_preAlerta = pad.id_preAlerta
                WHERE pa.id_preAlerta = ?
                GROUP BY pa.id_preAlerta";
        
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->conectar->error);
            return false;
        }
        
        $stmt->bind_param("i", $id_preAlerta);
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            // Procesar los detalles de equipos
            $equipos = [];
            if (!empty($data['detalles_equipos'])) {
                $detalles = explode('##', $data['detalles_equipos']);
                foreach ($detalles as $detalle) {
                    list($id, $marca, $equipo, $modelo, $serie) = explode('|', $detalle);
                    $equipos[] = [
                        'id' => $id,
                        'marca' => $marca,
                        'equipo' => $equipo,
                        'modelo' => $modelo,
                        'numero_serie' => $serie
                    ];
                }
            }
            $data['equipos'] = $equipos;
            unset($data['detalles_equipos']);
        }
        
        return $data;
    }

    public function verFilas()
    {
        $sql = "select * from pre_alerta";
        return $this->conectar->query($sql);
    }

    public function buscarCliente($termino)
    {
        $sql = "select * from pre_alerta
        where cliente_razon_social like '%$termino%' or cliente_ruc like '%$termino%'
        order by cliente_razon_social asc";
        return $this->conectar->query($sql);
    }
   
    public function idLast()
    {
        try {
            $sql = "SELECT id_preAlerta,cliente_razon_social,cliente_ruc,direccion,atencion_encargado, origen FROM pre_alerta ORDER BY id_preAlerta DESC LIMIT 1"; // Incluye 'origen'
           $fila = $this->conectar->query($sql)->fetch_assoc();
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function getAllData($sql = null) {
        try {
            if ($sql === null) {
                $sql = "SELECT 
                        pa.*,
                        GROUP_CONCAT(
                            CONCAT(
                                pad.marca, '|',
                                pad.equipo, '|',
                                pad.modelo, '|',
                                pad.numero_serie
                            ) SEPARATOR '##'
                        ) as detalles_equipos
                    FROM pre_alerta pa
                    LEFT JOIN pre_alerta_detalles pad ON pa.id_preAlerta = pad.id_preAlerta
                    GROUP BY pa.id_preAlerta
                    ORDER BY pa.id_preAlerta DESC";
            }
            
            $result = $this->conectar->query($sql);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            
            // Procesar los detalles agrupados
            foreach ($rows as &$row) {
                if (!empty($row['detalles_equipos'])) {
                    $equipos = [];
                    $detalles = explode('##', $row['detalles_equipos']);
                    foreach ($detalles as $detalle) {
                        list($marca, $equipo, $modelo, $serie) = explode('|', $detalle);
                        $equipos[] = [
                            'marca' => $marca,
                            'equipo' => $equipo,
                            'modelo' => $modelo,
                            'numero_serie' => $serie
                        ];
                    }
                    $row['equipos'] = $equipos;
                    unset($row['detalles_equipos']);
                }
            }
            
            return $rows;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
   
    public function getOne($id) {
        try {
            $sql = "SELECT * FROM pre_alerta WHERE id_preAlerta = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta");
            }
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function actualizarPreAlerta($id_preAlerta, $datos, $equipos)
    {
        $this->conectar->begin_transaction();
        
        try {
            // Actualizar datos principales
            $sql = "UPDATE pre_alerta SET 
                    cliente_razon_social = ?,
                    cliente_ruc = ?,
                    atencion_encargado = ?,
                    fecha_ingreso = ?,
                    observaciones= ?
                    WHERE id_preAlerta = ?";
            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la actualización principal: " . $this->conectar->error);
            }
            
            $stmt->bind_param("sssssi", 
                $datos['cliente_razon_social'],
                $datos['cliente_ruc'],
                $datos['atencion_encargado'],
                $datos['fecha_ingreso'],
                $datos['observaciones'],
                $id_preAlerta
            );
            $stmt->execute();
            
            // Primero, eliminar todos los equipos existentes
            $sqlDelete = "DELETE FROM pre_alerta_detalles WHERE id_preAlerta = ?";
            $stmtDelete = $this->conectar->prepare($sqlDelete);
            if (!$stmtDelete) {
                throw new Exception("Error en la preparación del borrado: " . $this->conectar->error);
            }
            
            $stmtDelete->bind_param("i", $id_preAlerta);
            $stmtDelete->execute();
            
            // Luego, insertar los nuevos equipos
            if (!empty($equipos)) {
                $sqlInsert = "INSERT INTO pre_alerta_detalles 
                            (id_preAlerta, marca, equipo, modelo, numero_serie) 
                            VALUES (?, ?, ?, ?, ?)";
                $stmtInsert = $this->conectar->prepare($sqlInsert);
                if (!$stmtInsert) {
                    throw new Exception("Error en la preparación de la inserción: " . $this->conectar->error);
                }
                
                foreach ($equipos as $equipo) {
                    $stmtInsert->bind_param("issss", 
                        $id_preAlerta,
                        $equipo['marca'],
                        $equipo['equipo'],
                        $equipo['modelo'],
                        $equipo['numero_serie']
                    );
                    $stmtInsert->execute();
                }
            }
            
            $this->conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en actualizarPreAlerta: " . $e->getMessage());
            return false;
        }
    }    public function delete($id)
    {
        try {
            $this->conectar->begin_transaction();
            
            // Primero eliminar los detalles relacionados
            $sqlDetalles = "DELETE FROM pre_alerta_detalles WHERE id_preAlerta = ?";
            $stmtDetalles = $this->conectar->prepare($sqlDetalles);
            $stmtDetalles->bind_param("i", $id);
            $stmtDetalles->execute();
            
            // Luego eliminar la pre-alerta
            $sql = "DELETE FROM pre_alerta WHERE id_preAlerta = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            
            $this->conectar->commit();
            return $result;
        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error al eliminar pre-alerta: " . $e->getMessage());
            return false;
        }
    }
  

}

