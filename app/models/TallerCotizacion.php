<?php

require_once 'app/helpers/ImageStorage.php';

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
            // CAMBIO PRINCIPAL: Quitar GROUP_CONCAT y DISTINCT para mantener el orden
            $query = "SELECT 
                ot.id_orden_trabajo as id_original,
                ot.cliente_razon_social,
                ot.cliente_ruc,
                ot.direccion,
                ot.atencion_encargado,
                ot.fecha_ingreso,
                GROUP_CONCAT(otd.marca ORDER BY otd.id_detalle) as marcas,
                GROUP_CONCAT(otd.equipo ORDER BY otd.id_detalle) as equipos,
                GROUP_CONCAT(otd.modelo ORDER BY otd.id_detalle) as modelos,
                GROUP_CONCAT(otd.numero_serie ORDER BY otd.id_detalle) as numeros_serie
                FROM orden_trabajo_pre ot
                LEFT JOIN orden_trabajo_detalles otd ON ot.id_orden_trabajo = otd.id_orden_trabajo
                WHERE ot.id_orden_trabajo = ?
                GROUP BY ot.id_orden_trabajo";
        } else {
            // ORD SERVICIO - aplicar el mismo cambio
            $query = "SELECT 
                os.id_orden_servicio as id_original,
                os.cliente_razon_social,
                os.cliente_ruc,
                os.direccion,
                os.atencion_encargado,
                os.fecha_ingreso,
                GROUP_CONCAT(osd.marca ORDER BY osd.id_detalle) as marcas,
                GROUP_CONCAT(osd.equipo ORDER BY osd.id_detalle) as equipos,
                GROUP_CONCAT(osd.modelo ORDER BY osd.id_detalle) as modelos,
                GROUP_CONCAT(osd.numero_serie ORDER BY osd.id_detalle) as numeros_serie
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

        // NUEVO: Obtener productos/repuestos de la orden
        $productos = $this->obtenerProductosOrden($ordenId, $tipo);

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
            'productos_existentes' => $productos, // NUEVO CAMPO
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
            dias_pagos, direccion, id_cliente, total, 
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
            $idCli,                   // i - id_cliente (integer)
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
        // Consulta principal con JOIN a clientes
        $sql = "SELECT 
            tc.*,
            c.documento as num_doc,
            c.datos as nom_cli,
            c.direccion as dir_cli,
            c.direccion2 as dir2_cli
            FROM taller_cotizaciones tc
            INNER JOIN clientes c ON tc.id_cliente = c.id_cliente
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
        error_log("verificarExistencia llamado con ID: $idOrden");
        
        $sql = "SELECT id_cotizacion FROM taller_cotizaciones WHERE id_prealerta = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            error_log("Error al preparar consulta verificarExistencia: " . $this->conectar->error);
            throw new Exception("Error al preparar la consulta: " . $this->conectar->error);
        }
        
        $stmt->bind_param("i", $idOrden);
        
        if (!$stmt->execute()) {
            error_log("Error al ejecutar consulta verificarExistencia: " . $stmt->error);
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Cotización encontrada: " . $row['id_cotizacion'] . " para orden: $idOrden");
            return $row['id_cotizacion'];
        }
        
        error_log("No se encontró cotización para orden: $idOrden");
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

            // Delete from taller_cotizaciones_equipos - ✅ AGREGADO
            $sql = "DELETE FROM taller_cotizaciones_equipos WHERE id_cotizacion = ?";
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

            // Delete from taller_observaciones_cotizacion - ✅ AGREGADO
            $sql = "DELETE FROM taller_observaciones_cotizacion WHERE id_cotizacion = ?";
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
                ImageStorage::delete('cotizaciones-taller', $foto);
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

    public function obtenerProductosOrden($ordenId, $tipo)
{
    try {
        $productos = [];
        
        if ($tipo === 'ORD TRABAJO') {
            // Consulta corregida para la nueva estructura de BD
            $sql = "SELECT 
                otr.cantidad,
                otr.precio_unitario as precioVenta,
                otr.tipo_item,
                otr.id_detalle_maquina,
                otd.marca,
                otd.equipo,
                otd.modelo,
                otd.numero_serie,
                CASE
                    WHEN otr.tipo_item = 'producto' THEN otr.id_producto
                    WHEN otr.tipo_item = 'repuesto' THEN otr.id_repuesto
                END as productoid,
                CASE
                    WHEN otr.tipo_item = 'producto' THEN p.codigo
                    WHEN otr.tipo_item = 'repuesto' THEN r.codigo
                END as codigo_prod,
                CASE
                    WHEN otr.tipo_item = 'producto' THEN p.nombre
                    WHEN otr.tipo_item = 'repuesto' THEN r.nombre
                END as descripcion,
                CASE 
                    WHEN otr.tipo_item = 'producto' THEN p.costo
                    WHEN otr.tipo_item = 'repuesto' THEN r.costo
                    ELSE 0
                END as costo
            FROM orden_trabajo_repuestos otr
            INNER JOIN orden_trabajo_detalles otd ON otr.id_detalle_maquina = otd.id_detalle
            LEFT JOIN productos p ON otr.id_producto = p.id_producto AND otr.tipo_item = 'producto'
            LEFT JOIN repuestos r ON otr.id_repuesto = r.id_repuesto AND otr.tipo_item = 'repuesto'
            WHERE otr.id_orden_trabajo = ?
            ORDER BY otr.id_detalle_maquina, otr.fecha_agregado";
        } else {
            // Para orden de servicio, la lógica se mantiene si no ha cambiado
            return [];
        }

        $stmt = $this->conectar->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
        }

        $stmt->bind_param("i", $ordenId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $productos[] = [
                'productoid' => $row['productoid'],
                'codigo_prod' => $row['codigo_prod'],
                'descripcion' => $row['descripcion'],
                'cantidad' => $row['cantidad'],
                'precioVenta' => $row['precioVenta'],
                'costo' => $row['costo'],
                'tipo_item' => $row['tipo_item'],
                'equipoInfo' => [
                    'marca' => $row['marca'],
                    'equipo' => $row['equipo'],
                    'modelo' => $row['modelo'],
                    'numero_serie' => $row['numero_serie']
                ],
                'id_detalle_maquina' => $row['id_detalle_maquina'],
                'editable' => false
            ];
        }

        return $productos;

    } catch (Exception $e) {
        error_log("Error en obtenerProductosOrden: " . $e->getMessage());
        return [];
    }
}

}