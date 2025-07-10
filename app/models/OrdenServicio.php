<?php

class OrdenServicio
{
    private $id_orden_servicio;
    private $cliente_razon_social;
    private $cliente_ruc;
    private $direccion;
    private $atencion_encargado;
    private $fecha_ingreso;
    private $tiene_cotizacion;
    private $estado;
    private $observaciones;
    private $detalles;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
        $this->detalles = [];
    }

    // Getters y Setters
    public function getId_orden_servicio()
    {
        return $this->id_orden_servicio;
    }
    public function setId_orden_servicio($id_orden_servicio)
    {
        $this->id_orden_servicio = $id_orden_servicio;
    }

    public function getCliente_Razon_Social()
    {
        return $this->cliente_razon_social;
    }
    public function setCliente_Razon_Social($cliente_razon_social)
    {
        $this->cliente_razon_social = $cliente_razon_social;
    }

    public function getCliente_Ruc()
    {
        return $this->cliente_ruc;
    }
    public function setCliente_Ruc($cliente_ruc)
    {
        $this->cliente_ruc = $cliente_ruc;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function getAtencion_Encargado()
    {
        return $this->atencion_encargado;
    }
    public function setAtencion_Encargado($atencion_encargado)
    {
        $this->atencion_encargado = $atencion_encargado;
    }

    public function getFecha_Ingreso()
    {
        return $this->fecha_ingreso;
    }
    public function setFecha_Ingreso($fecha_ingreso)
    {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    public function getTiene_Cotizacion()
    {
        return $this->tiene_cotizacion;
    }
    public function setTiene_Cotizacion($tiene_cotizacion)
    {
        $this->tiene_cotizacion = $tiene_cotizacion;
    }

    public function getEstado()
    {
        return $this->estado;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
    }
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    public function getDetalles()
    {
        return $this->detalles;
    }
    public function setDetalles($detalles)
    {
        $this->detalles = $detalles;
    }

    public function insertar()
    {
        try {
            $this->conectar->begin_transaction();
            $numero = $this->generarNumero();

            // Insertar orden de servicio principal
            $sql = "INSERT INTO orden_servicio_pre (
                         numero,
                        cliente_razon_social, 
                        cliente_ruc, 
                        direccion, 
                        atencion_encargado, 
                        fecha_ingreso, 
                        estado, 
                        observaciones
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);
            $estado = $this->estado ?: 'PENDIENTE';

            $stmt->bind_param(
                "ssssssss",
                $numero,
                $this->cliente_razon_social,
                $this->cliente_ruc,
                $this->direccion,
                $this->atencion_encargado,
                $this->fecha_ingreso,
                $estado,
                $this->observaciones
            );

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar orden de servicio: " . $stmt->error);
            }

            $id_orden_servicio = $this->conectar->insert_id;

            // Insertar detalles de equipos
            if (!empty($this->detalles)) {
                $sql_detalle = "INSERT INTO orden_servicio_detalles (
                                    id_orden_servicio, 
                                    marca, 
                                    equipo, 
                                    modelo, 
                                    numero_serie
                                ) VALUES (?, ?, ?, ?, ?)";

                $stmt_detalle = $this->conectar->prepare($sql_detalle);

                foreach ($this->detalles as $detalle) {
                    $stmt_detalle->bind_param(
                        "issss",
                        $id_orden_servicio,
                        $detalle['marca'],
                        $detalle['equipo'],
                        $detalle['modelo'],
                        $detalle['numero_serie']
                    );

                    if (!$stmt_detalle->execute()) {
                        throw new Exception("Error al insertar detalle: " . $stmt_detalle->error);
                    }
                }
            }

            $this->conectar->commit();
            $this->id_orden_servicio = $id_orden_servicio;
            return true;

        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en OrdenServicio::insertar(): " . $e->getMessage());
            return false;
        }
    }

    public function getAllData()
    {
        try {
            $sql = "SELECT os.*, 
                           COUNT(osd.id_detalle) as total_equipos
                    FROM orden_servicio_pre os
                    LEFT JOIN orden_servicio_detalles osd ON os.id_orden_servicio = osd.id_orden_servicio
                    GROUP BY os.id_orden_servicio
                    ORDER BY os.fecha_ingreso DESC, os.created_at DESC";

            $result = $this->conectar->query($sql);

            if (!$result) {
                throw new Exception("Error en la consulta: " . $this->conectar->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return $data;

        } catch (Exception $e) {
            error_log("Error en OrdenServicio::getAllData(): " . $e->getMessage());
            return false;
        }
    }

    public function getOne($id)
    {
        try {
            $sql = "SELECT os.*, 
                           GROUP_CONCAT(
                               CONCAT_WS('|', osd.marca, osd.equipo, osd.modelo, osd.numero_serie) 
                               SEPARATOR '##'
                           ) as equipos
                    FROM orden_servicio_pre os
                    LEFT JOIN orden_servicio_detalles osd ON os.id_orden_servicio = osd.id_orden_servicio
                    WHERE os.id_orden_servicio = ?
                    GROUP BY os.id_orden_servicio";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data && $data['equipos']) {
                $equiposArray = [];
                $equipos = explode('##', $data['equipos']);
                foreach ($equipos as $equipo) {
                    list($marca, $tipo, $modelo, $serie) = explode('|', $equipo);
                    $equiposArray[] = [
                        'marca' => $marca,
                        'equipo' => $tipo,
                        'modelo' => $modelo,
                        'numero_serie' => $serie
                    ];
                }
                $data['equipos'] = $equiposArray;
            } else {
                $data['equipos'] = [];
            }

            return $data;

        } catch (Exception $e) {
            error_log("Error en OrdenServicio::getOne(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos, $equipos = [])
    {
        try {
            $this->conectar->begin_transaction();

            // Actualizar datos principales
            $sql = "UPDATE orden_servicio_pre SET 
                        cliente_razon_social = ?, 
                        cliente_ruc = ?, 
                        atencion_encargado = ?, 
                        fecha_ingreso = ?, 
                        observaciones = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id_orden_servicio = ?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param(
                "sssssi",
                $datos['cliente_razon_social'],
                $datos['cliente_ruc'],
                $datos['atencion_encargado'],
                $datos['fecha_ingreso'],
                $datos['observaciones'],
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar orden de servicio: " . $stmt->error);
            }

            // Eliminar detalles existentes
            $sql_delete = "DELETE FROM orden_servicio_detalles WHERE id_orden_servicio = ?";
            $stmt_delete = $this->conectar->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();

            // Insertar nuevos detalles
            if (!empty($equipos)) {
                $sql_detalle = "INSERT INTO orden_servicio_detalles (
                                    id_orden_servicio, 
                                    marca, 
                                    equipo, 
                                    modelo, 
                                    numero_serie
                                ) VALUES (?, ?, ?, ?, ?)";

                $stmt_detalle = $this->conectar->prepare($sql_detalle);

                foreach ($equipos as $equipo) {
                    $stmt_detalle->bind_param(
                        "issss",
                        $id,
                        $equipo['marca'],
                        $equipo['equipo'],
                        $equipo['modelo'],
                        $equipo['numero_serie']
                    );

                    if (!$stmt_detalle->execute()) {
                        throw new Exception("Error al insertar detalle: " . $stmt_detalle->error);
                    }
                }
            }

            $this->conectar->commit();
            return true;

        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en OrdenServicio::actualizar(): " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $sql = "DELETE FROM orden_servicio_pre WHERE id_orden_servicio = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en OrdenServicio::delete(): " . $e->getMessage());
            return false;
        }
    }

    public function culminarTrabajo($id)
    {
        try {
            $this->conectar->begin_transaction();

            // Obtener las series asociadas a esta orden de servicio
            $sqlSeries = "SELECT numero_serie FROM orden_servicio_detalles WHERE id_orden_servicio = ?";
            $stmtSeries = $this->conectar->prepare($sqlSeries);
            $stmtSeries->bind_param("i", $id);
            $stmtSeries->execute();
            $resultSeries = $stmtSeries->get_result();

            // Actualizar estado de la orden de servicio
            $sql = "UPDATE orden_servicio_pre SET estado = 'CULMINADO', updated_at = CURRENT_TIMESTAMP WHERE id_orden_servicio = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar el estado');
            }

            // Actualizar estado de series a 'culminado' en estado_prealerta
            while ($serie = $resultSeries->fetch_assoc()) {
                $this->actualizarEstadoSeriePreAlerta($serie['numero_serie'], 'culminado');
            }

            $this->conectar->commit();
            return true;

        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en OrdenServicio::culminarTrabajo(): " . $e->getMessage());
            return false;
        }
    }

    private function actualizarEstadoSeriePreAlerta($numero_serie, $estado = 'culminado')
    {
        $sql = "UPDATE detalle_serie SET estado_prealerta = ? WHERE numero_serie = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ss", $estado, $numero_serie);
        return $stmt->execute();
    }

    public function idLast()
    {
        return $this->id_orden_servicio;
    }
    private function generarNumero()
    {
        try {
            $sql = "SELECT numero FROM orden_servicio_pre WHERE numero LIKE 'OS-%' ORDER BY id_orden_servicio DESC LIMIT 1";
            $result = $this->conectar->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $ultimoNumero = $row['numero'];
                $numero = intval(substr($ultimoNumero, 3));
                $siguienteNumero = $numero + 1;
            } else {
                $siguienteNumero = 1;
            }

            return 'OS-' . str_pad($siguienteNumero, 2, '0', STR_PAD_LEFT);

        } catch (Exception $e) {
            error_log("Error al generar número: " . $e->getMessage());
            return 'OS-01';
        }
    }

}