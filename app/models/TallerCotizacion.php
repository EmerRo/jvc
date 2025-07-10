<?php

class TallerCotizacion
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerInfoOrden($ordenId, $tipo)
    {
        try {
            error_log("Obteniendo información para ID: " . $ordenId . " y tipo: " . $tipo);

            // Determinar qué tabla usar según el tipo
            if ($tipo === 'ORD TRABAJO') {
                $query = "SELECT 
                    ot.id_orden_trabajo as id_original,
                    ot.cliente_razon_social,
                    ot.cliente_ruc,
                    ot.direccion,
                    ot.atencion_encargado,
                    ot.fecha_ingreso,
                    GROUP_CONCAT(DISTINCT otd.marca) as marcas,
                    GROUP_CONCAT(DISTINCT otd.equipo) as equipos,
                    GROUP_CONCAT(DISTINCT otd.modelo) as modelos,
                    GROUP_CONCAT(DISTINCT otd.numero_serie) as numeros_serie
                    FROM orden_trabajo_pre ot
                    LEFT JOIN orden_trabajo_detalles otd ON ot.id_orden_trabajo = otd.id_orden_trabajo
                    WHERE ot.id_orden_trabajo = ?
                    GROUP BY ot.id_orden_trabajo";
            } else {
                // ORD SERVICIO
                $query = "SELECT 
                    os.id_orden_servicio as id_original,
                    os.cliente_razon_social,
                    os.cliente_ruc,
                    os.direccion,
                    os.atencion_encargado,
                    os.fecha_ingreso,
                    GROUP_CONCAT(DISTINCT osd.marca) as marcas,
                    GROUP_CONCAT(DISTINCT osd.equipo) as equipos,
                    GROUP_CONCAT(DISTINCT osd.modelo) as modelos,
                    GROUP_CONCAT(DISTINCT osd.numero_serie) as numeros_serie
                    FROM orden_servicio_pre os
                    LEFT JOIN orden_servicio_detalles osd ON os.id_orden_servicio = osd.id_orden_servicio
                    WHERE os.id_orden_servicio = ?
                    GROUP BY os.id_orden_servicio";
            }

            $stmt = $this->conectar->prepare($query);
            $stmt->bind_param("i", $ordenId);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $orden = $resultado->fetch_assoc();

            if (!$orden) {
                return null;
            }

            return [
                'id_original' => $orden['id_original'],
                'cliente_nombre' => $orden['cliente_razon_social'],
                'cliente_doc' => $orden['cliente_ruc'],
                'cliente_direccion' => $orden['direccion'],
                'tecnico_nombre' => $orden['atencion_encargado'],
                'fecha_ingreso' => $orden['fecha_ingreso'],
                'marcas' => $orden['marcas'] ? explode(',', $orden['marcas']) : [],
                'equipos' => $orden['equipos'] ? explode(',', $orden['equipos']) : [],
                'modelos' => $orden['modelos'] ? explode(',', $orden['modelos']) : [],
                'numeros_serie' => $orden['numeros_serie'] ? explode(',', $orden['numeros_serie']) : [],
                'tipo_origen' => $tipo
            ];

        } catch (Exception $e) {
            error_log("Error en obtenerInfoOrden: " . $e->getMessage());
            throw $e;
        }
    }

    public function crear($data, $idCli, $numCoti, $ordenId, $descuento, $tipoOrigen = '')
    {
        $sql = "INSERT INTO taller_cotizaciones (
            id_tido, moneda, cm_tc, id_tipo_pago, fecha, 
            dias_pagos, direccion, id_cliente_taller, total, 
            numero, estado, usar_precio, sucursal, id_empresa, 
            id_usuario, id_prealerta, descuento, tipo_origen
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $estado = '0';

        $stmt = $this->conectar->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta: " . $this->conectar->error);
        }

        // CORREGIDO: String de tipos con 18 caracteres para 18 parámetros
        $stmt->bind_param(
            "iisissiidsiiiiiids",  // ✅ 18 caracteres para 18 parámetros
            $data['tipo_doc'],        // i - id_tido (integer)
            $data['moneda'],          // i - moneda (integer)  
            $data['tc'],              // s - cm_tc (string/decimal)
            $data['tipo_pago'],       // i - id_tipo_pago (integer)
            $data['fecha'],           // s - fecha (string)
            $data['dias_pago'],       // s - dias_pagos (string)
            $data['dir_pos'],         // s - direccion (string)
            $idCli,                   // i - id_cliente_taller (integer)
            $data['total'],           // i - total (decimal)
            $numCoti,                 // d - numero (integer)
            $estado,                  // s - estado (string)
            $data['usar_precio'],     // i - usar_precio (integer)
            $_SESSION['sucursal'],    // i - sucursal (integer)
            $_SESSION['id_empresa'],  // i - id_empresa (integer)
            $_SESSION['usuario_fac'], // i - id_usuario (integer)
            $ordenId,                 // i - id_prealerta (integer)
            $descuento,               // d - descuento (decimal)
            $tipoOrigen               // s - tipo_origen (string)
        );

        if ($stmt->execute()) {
            return $this->conectar->insert_id;
        }

        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    public function obtenerSiguienteNumero()
    {
        $sql = "SELECT MAX(numero) as ultimo FROM taller_cotizaciones WHERE id_empresa = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $_SESSION['id_empresa']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return ($row['ultimo'] ?? 0) + 1;
    }

    public function actualizarEstadoOrden($ordenId, $tipoOrigen)
    {
        try {
            if ($tipoOrigen === 'ORD TRABAJO') {
                $sqlUpdate = "UPDATE orden_trabajo_pre SET tiene_cotizacion = 1 WHERE id_orden_trabajo = ?";
            } else {
                $sqlUpdate = "UPDATE orden_servicio_pre SET tiene_cotizacion = 1 WHERE id_orden_servicio = ?";
            }
            
            $stmtUpdate = $this->conectar->prepare($sqlUpdate);
            if ($stmtUpdate === false) {
                throw new Exception("Error al preparar la actualización: " . $this->conectar->error);
            }
            
            $stmtUpdate->bind_param("i", $ordenId);
            if (!$stmtUpdate->execute()) {
                throw new Exception("Error al actualizar el estado: " . $stmtUpdate->error);
            }
            
            error_log("Estado actualizado correctamente para ID: " . $ordenId . " tipo: " . $tipoOrigen);
            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerDetalle($id_cotizacion)
    {
        try {
            // Consulta principal con JOIN a clientes_taller
            $sql = "SELECT 
                tc.*,
                ct.documento as num_doc,
                ct.datos as nom_cli,
                ct.direccion as dir_cli,
                ct.atencion as dir2_cli
                FROM taller_cotizaciones tc
                INNER JOIN clientes_taller ct ON tc.id_cliente_taller = ct.id_cliente_taller
                WHERE tc.id_cotizacion = ?";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->conectar->error);
            }

            $stmt->bind_param("i", $id_cotizacion);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $cotizacion = $result->fetch_assoc();

            if (!$cotizacion) {
                throw new Exception("Cotización no encontrada");
            }

            return $cotizacion;

        } catch (Exception $e) {
            error_log("Error en obtenerDetalle: " . $e->getMessage());
            throw $e;
        }
    }

    public function verificarExistencia($idOrden)
    {
        $sql = "SELECT id_cotizacion FROM taller_cotizaciones WHERE id_prealerta = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
        }
        
        $stmt->bind_param("i", $idOrden);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id_cotizacion'];
        }
        
        return false;
    }

    public function actualizar($idCotizacion, $data)
    {
        $sql = "UPDATE taller_cotizaciones SET 
            id_tido = ?, 
            moneda = ?, 
            cm_tc = ?, 
            id_tipo_pago = ?, 
            fecha = ?,
            dias_pagos = ?, 
            direccion = ?, 
            total = ?, 
            usar_precio = ?,
            descuento = ?
            WHERE id_cotizacion = ?";

        $stmt = $this->conectar->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparando la actualización: " . $this->conectar->error);
        }

        $stmt->bind_param(
            "sssssssdsdi",
            $data['tipo_doc'],
            $data['moneda'],
            $data['tc'],
            $data['tipo_pago'],
            $data['fecha'],
            $data['dias_pago'],
            $data['dir_pos'],
            $data['total'],
            $data['usar_precio'],
            $data['descuento'],
            $idCotizacion
        );

        if (!$stmt->execute()) {
            throw new Exception("Error actualizando la cotización: " . $stmt->error);
        }

        return true;
    }

    public function eliminar($id_cotizacion)
    {
        try {
            $this->conectar->begin_transaction();

            // Primero obtener los nombres de las fotos antes de eliminarlas de la BD
            $sqlFotos = "SELECT nombre_foto FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
            $stmtFotos = $this->conectar->prepare($sqlFotos);
            $stmtFotos->bind_param("i", $id_cotizacion);
            $stmtFotos->execute();
            $resultFotos = $stmtFotos->get_result();

            // Almacenar los nombres de las fotos
            $fotos = [];
            while ($row = $resultFotos->fetch_assoc()) {
                if ($row['nombre_foto']) {
                    $fotos[] = $row['nombre_foto'];
                }
            }

            // Delete related records first (due to foreign key constraints)
            // Delete from taller_cotizaciones_fotos
            $sql = "DELETE FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Delete from taller_repuestos_cotis
            $sql = "DELETE FROM taller_repuestos_cotis WHERE id_coti = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Delete from cuotas_cotizacion
            $sql = "DELETE FROM cuotas_cotizacion WHERE id_coti = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Delete condiciones and diagnosticos
            $sql = "DELETE FROM taller_condiciones_cotizacion WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            $sql = "DELETE FROM taller_diagnosticos_cotizacion WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Finally, delete the main record from taller_cotizaciones
            $sql = "DELETE FROM taller_cotizaciones WHERE id_cotizacion = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion);
            $stmt->execute();

            // Si todo fue exitoso, eliminar los archivos físicos
            $errores = [];
            foreach ($fotos as $foto) {
                $rutaFoto = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/' . $foto;
                if (file_exists($rutaFoto)) {
                    if (!unlink($rutaFoto)) {
                        $errores[] = "No se pudo eliminar el archivo: " . $foto;
                    }
                }
            }

            $this->conectar->commit();

            return [
                'success' => true,
                'warnings' => $errores
            ];

        } catch (Exception $e) {
            $this->conectar->rollback();
            throw $e;
        }
    }
}