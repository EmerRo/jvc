<?php

class TallerRepuesto
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

public function insertar($idCoti, $productos, $equiposIds)
{
    if (empty($productos)) {
        return;
    }

    error_log("=== INICIO INSERTAR PRODUCTOS ===");
    error_log("ID Cotización: " . $idCoti);
    error_log("Productos recibidos: " . json_encode($productos));
    error_log("Equipos IDs: " . json_encode($equiposIds));

    // NUEVO: Verificar si es un registro interno (RUC de la empresa)
    $sqlCheckCliente = "SELECT c.documento 
                        FROM taller_cotizaciones tc
                        JOIN clientes c ON tc.id_cliente = c.id_cliente
                        WHERE tc.id_cotizacion = ?";
    $stmtCheckCliente = $this->conectar->prepare($sqlCheckCliente);
    $stmtCheckCliente->bind_param("i", $idCoti);
    $stmtCheckCliente->execute();
    $resultCliente = $stmtCheckCliente->get_result();
    $clienteData = $resultCliente->fetch_assoc();
    
    $esRegistroInterno = ($clienteData && $clienteData['documento'] === '20538381978');
    error_log("¿Es registro interno? " . ($esRegistroInterno ? 'SÍ' : 'NO') . " (RUC: " . ($clienteData['documento'] ?? 'N/A') . ")");

    // Preparar la consulta para insertar repuestos/productos
    $sql = "INSERT INTO taller_repuestos_cotis 
        (id_coti, id_repuesto, id_producto, tipo_item, cantidad, precio, costo, id_cotizacion_equipo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conectar->prepare($sql);

    // Preparar las consultas para actualizar el stock
    // MODIFICADO: Si es registro interno, AUMENTAR stock; si no, REDUCIR stock
    if ($esRegistroInterno) {
        // Para registros internos: AUMENTAR stock
        $sqlUpdateStockRepuesto = "UPDATE repuestos 
                              SET cantidad = cantidad + ? 
                              WHERE id_repuesto = ?";
        $stmtUpdateStockRepuesto = $this->conectar->prepare($sqlUpdateStockRepuesto);

        $sqlUpdateStockProducto = "UPDATE productos 
                              SET cantidad = cantidad + ? 
                              WHERE id_producto = ?";
        $stmtUpdateStockProducto = $this->conectar->prepare($sqlUpdateStockProducto);
        
        error_log("MODO: AUMENTAR STOCK (Registro Interno)");
    } else {
        // Para registros externos: REDUCIR stock (comportamiento normal)
        $sqlUpdateStockRepuesto = "UPDATE repuestos 
                              SET cantidad = cantidad - ? 
                              WHERE id_repuesto = ? 
                              AND cantidad >= ?";
        $stmtUpdateStockRepuesto = $this->conectar->prepare($sqlUpdateStockRepuesto);

        $sqlUpdateStockProducto = "UPDATE productos 
                              SET cantidad = cantidad - ? 
                              WHERE id_producto = ? 
                              AND cantidad >= ?";
        $stmtUpdateStockProducto = $this->conectar->prepare($sqlUpdateStockProducto);
        
        error_log("MODO: REDUCIR STOCK (Registro Externo)");
    }

    foreach ($productos as $index => $producto) {
        error_log("--- Procesando producto " . ($index + 1) . " ---");
        error_log("Producto data: " . json_encode($producto));

        // NUEVA LÓGICA: Determinar si es producto o repuesto
        $esProducto = false;
        $tipoItem = 'repuesto';
        $idRepuesto = null;
        $idProducto = null;

  // 1. Verificar si viene de productos_existentes (tiene tipo_item)
if (isset($producto['tipo_item'])) {
    $esProducto = ($producto['tipo_item'] === 'producto');
    error_log("Tipo detectado por tipo_item: " . $producto['tipo_item']);
}

error_log("Producto completo recibido: " . json_encode($producto));
error_log("¿Tiene tipo_item? " . (isset($producto['tipo_item']) ? 'SÍ' : 'NO'));
if (isset($producto['tipo_item'])) {
    error_log("Valor de tipo_item: " . $producto['tipo_item']);
}


// 2. Si no tiene tipo_item, verificar en la base de datos directamente
else {
    // Buscar primero en productos por ID
    $sqlCheckProducto = "SELECT id_producto FROM productos WHERE id_producto = ?";
    $stmtCheckProducto = $this->conectar->prepare($sqlCheckProducto);
    $stmtCheckProducto->bind_param("i", $producto['productoid']);
    $stmtCheckProducto->execute();
    $resultProducto = $stmtCheckProducto->get_result();
    
    if ($resultProducto->num_rows > 0) {
        $esProducto = true;
        error_log("Tipo detectado por BD: producto (ID " . $producto['productoid'] . " encontrado en tabla productos)");
    } else {
        $esProducto = false;
        error_log("Tipo detectado por BD: repuesto (ID " . $producto['productoid'] . " no encontrado en tabla productos)");
    }
}

        // Asignar valores según el tipo
        if ($esProducto) {
            $tipoItem = 'producto';
            $idProducto = $producto['productoid'];
            $idRepuesto = null;
        } else {
            $tipoItem = 'repuesto';
            $idRepuesto = $producto['productoid'];
            $idProducto = null;
        }

        error_log("Tipo final: " . $tipoItem);
        error_log("ID Producto: " . ($idProducto ?? 'null'));
        error_log("ID Repuesto: " . ($idRepuesto ?? 'null'));

        // Verificar stock solo si NO es registro interno
        if (!$esRegistroInterno) {
            if ($esProducto) {
                $sqlCheckStock = "SELECT cantidad FROM productos WHERE id_producto = ?";
                $stmtCheckStock = $this->conectar->prepare($sqlCheckStock);
                $stmtCheckStock->bind_param("i", $idProducto);
            } else {
                $sqlCheckStock = "SELECT cantidad FROM repuestos WHERE id_repuesto = ?";
                $stmtCheckStock = $this->conectar->prepare($sqlCheckStock);
                $stmtCheckStock->bind_param("i", $idRepuesto);
            }
            
            $stmtCheckStock->execute();
            $resultStock = $stmtCheckStock->get_result();
            
            if ($resultStock->num_rows === 0) {
                error_log("ERROR: No se encontró el item en la tabla correspondiente");
                continue; // Saltar este producto
            }
            
            $stockData = $resultStock->fetch_assoc();
            $stockActual = $stockData['cantidad'];
            error_log("Stock actual: " . $stockActual);

            // Verificar si hay suficiente cantidad
            if ($stockActual < $producto['cantidad']) {
                $mensajeError = sprintf(
                    "Stock insuficiente para el producto: %s\n" .
                    "Stock disponible: %s\n" .
                    "Cantidad solicitada: %s",
                    $producto['descripcion'],
                    $stockActual,
                    $producto['cantidad']
                );
                throw new Exception($mensajeError);
            }
        } else {
            error_log("Registro interno: NO se verifica stock (se aumentará)");
        }

        // Obtener el ID del equipo: preferir id_cotizacion_equipo si viene; si no, usar equipoActivo→equiposIds
        $idEquipo = null;
        if (isset($producto['id_cotizacion_equipo']) && !empty($producto['id_cotizacion_equipo'])) {
            $idEquipo = (int)$producto['id_cotizacion_equipo'];
            error_log("ID Equipo asignado directamente (id_cotizacion_equipo): " . $idEquipo);
        } elseif (isset($producto['equipoActivo']) && isset($equiposIds[$producto['equipoActivo']])) {
            $idEquipo = $equiposIds[$producto['equipoActivo']];
            error_log("ID Equipo asignado por mapa equipoActivo→equiposIds: " . $idEquipo);
        } else {
            error_log("No se pudo asignar equipo. equipoActivo: " . ($producto['equipoActivo'] ?? 'null') . ", id_cotizacion_equipo: " . ($producto['id_cotizacion_equipo'] ?? 'null'));
        }

        // Insertar en taller_repuestos_cotis
        error_log("Insertando en taller_repuestos_cotis...");
        $stmt->bind_param(
            "iiisdddi",
            $idCoti,
            $idRepuesto,
            $idProducto,
            $tipoItem,
            $producto['cantidad'],
            $producto['precioVenta'],
            $producto['costo'],
            $idEquipo
        );
        
        if (!$stmt->execute()) {
            error_log("ERROR al insertar: " . $stmt->error);
            throw new Exception("Error al insertar producto: " . $stmt->error);
        } else {
            error_log("Producto insertado correctamente");
        }

        // Actualizar el stock correspondiente
        if ($esProducto) {
            error_log("Actualizando stock de producto...");
            if ($esRegistroInterno) {
                // Registro interno: AUMENTAR stock
                $stmtUpdateStockProducto->bind_param(
                    "di",
                    $producto['cantidad'],
                    $idProducto
                );
            } else {
                // Registro externo: REDUCIR stock
                $stmtUpdateStockProducto->bind_param(
                    "dii",
                    $producto['cantidad'],
                    $idProducto,
                    $producto['cantidad']
                );
            }
            
            if (!$stmtUpdateStockProducto->execute()) {
                error_log("ERROR al actualizar stock producto: " . $stmtUpdateStockProducto->error);
            } else {
                error_log("Stock producto actualizado");
                
                // NUEVO: Registrar movimiento en historial_stock
                $this->registrarMovimientoStock(
                    $idProducto,
                    $esRegistroInterno ? 'INGRESO' : 'EGRESO',
                    $producto['cantidad'],
                    $producto['costo'] ?? 0,
                    $esRegistroInterno ? 'Producción interna - Orden de Trabajo' : 'Uso en Orden de Trabajo',
                    $idCoti,
                    $esRegistroInterno ? 'ORDEN_TRABAJO_INTERNA' : 'ORDEN_TRABAJO_EXTERNA'
                );
            }
        } else {
            error_log("Actualizando stock de repuesto...");
            if ($esRegistroInterno) {
                // Registro interno: AUMENTAR stock
                $stmtUpdateStockRepuesto->bind_param(
                    "di",
                    $producto['cantidad'],
                    $idRepuesto
                );
            } else {
                // Registro externo: REDUCIR stock
                $stmtUpdateStockRepuesto->bind_param(
                    "dii",
                    $producto['cantidad'],
                    $idRepuesto,
                    $producto['cantidad']
                );
            }
            
            if (!$stmtUpdateStockRepuesto->execute()) {
                error_log("ERROR al actualizar stock repuesto: " . $stmtUpdateStockRepuesto->error);
            } else {
                error_log("Stock repuesto actualizado");
                
                // NUEVO: Registrar movimiento en historial_stock (repuestos también se registran)
                $this->registrarMovimientoStock(
                    $idRepuesto,
                    $esRegistroInterno ? 'INGRESO' : 'EGRESO',
                    $producto['cantidad'],
                    $producto['costo'] ?? 0,
                    $esRegistroInterno ? 'Producción interna - Orden de Trabajo' : 'Uso en Orden de Trabajo',
                    $idCoti,
                    $esRegistroInterno ? 'ORDEN_TRABAJO_INTERNA' : 'ORDEN_TRABAJO_EXTERNA'
                );
            }
        }
    }
    
    error_log("=== FIN INSERTAR PRODUCTOS ===");
}


  public function obtenerPorCotizacion($id_cotizacion)
{
    $sql = "SELECT 
        trc.*,
        CASE 
            WHEN trc.tipo_item = 'producto' THEN p.codigo
            WHEN trc.tipo_item = 'repuesto' THEN r.codigo
        END as codigo_prod,
        CASE 
            WHEN trc.tipo_item = 'producto' THEN p.nombre
            WHEN trc.tipo_item = 'repuesto' THEN r.nombre
        END as descripcion,
        CASE 
            WHEN trc.tipo_item = 'producto' THEN p.precio
            WHEN trc.tipo_item = 'repuesto' THEN r.precio
        END as precio_base,
        CASE 
            WHEN trc.tipo_item = 'producto' THEN p.precio2
            WHEN trc.tipo_item = 'repuesto' THEN r.precio2
        END as precio2,
        CASE 
            WHEN trc.tipo_item = 'producto' THEN p.precio_unidad
            WHEN trc.tipo_item = 'repuesto' THEN r.precio_unidad
        END as precio_unidad
        FROM taller_repuestos_cotis trc
        LEFT JOIN repuestos r ON trc.id_repuesto = r.id_repuesto AND trc.tipo_item = 'repuesto'
        LEFT JOIN productos p ON trc.id_producto = p.id_producto AND trc.tipo_item = 'producto'
        WHERE trc.id_coti = ?";

    $stmt = $this->conectar->prepare($sql);
    $stmt->bind_param("i", $id_cotizacion);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


    public function eliminarPorCotizacion($id_cotizacion)
    {
        // Obtener los productos antiguos para restaurar la cantidad
        $sqlOldProducts = "SELECT id_repuesto, cantidad FROM taller_repuestos_cotis WHERE id_coti = ?";
        $stmtOldProducts = $this->conectar->prepare($sqlOldProducts);
        $stmtOldProducts->bind_param("i", $id_cotizacion);
        $stmtOldProducts->execute();
        $resultOldProducts = $stmtOldProducts->get_result();
        $oldProducts = $resultOldProducts->fetch_all(MYSQLI_ASSOC);

        // Restaurar la cantidad de los productos antiguos
        $sqlRestoreCantidad = "UPDATE repuestos SET cantidad = cantidad + ? WHERE id_repuesto = ?";
        $stmtRestoreCantidad = $this->conectar->prepare($sqlRestoreCantidad);
        foreach ($oldProducts as $oldProduct) {
            $stmtRestoreCantidad->bind_param("di", $oldProduct['cantidad'], $oldProduct['id_repuesto']);
            $stmtRestoreCantidad->execute();
        }

        // Eliminar repuestos antiguos
        $sqlDeleteRepuestos = "DELETE FROM taller_repuestos_cotis WHERE id_coti = ?";
        $stmtDeleteRepuestos = $this->conectar->prepare($sqlDeleteRepuestos);
        $stmtDeleteRepuestos->bind_param("i", $id_cotizacion);
        $stmtDeleteRepuestos->execute();
    }

    public function obtenerPorOrden($id_orden)
    {
        $sqlCotizacion = "SELECT id_cotizacion FROM taller_cotizaciones WHERE id_prealerta = ?";
        $stmtCotizacion = $this->conectar->prepare($sqlCotizacion);

        if (!$stmtCotizacion) {
            throw new Exception("Error preparando consulta: " . $this->conectar->error);
        }

        $stmtCotizacion->bind_param("i", $id_orden);
        $stmtCotizacion->execute();
        $resultCotizacion = $stmtCotizacion->get_result();
        $cotizacion = $resultCotizacion->fetch_assoc();

        if (!$cotizacion) {
            throw new Exception("No se encontró cotización para esta orden");
        }

        $id_cotizacion = $cotizacion['id_cotizacion'];

        $repuestos = $this->obtenerPorCotizacion($id_cotizacion);

        return array_map(function ($repuesto) {
            return [
                'productoid' => $repuesto['id_repuesto'],
                'codigo_prod' => $repuesto['codigo_prod'],
                'descripcion' => $repuesto['descripcion'],
                'cantidad' => $repuesto['cantidad'],
                'precio' => $repuesto['precio'],
                'precio2' => $repuesto['precio2'],
                'precio_unidad' => $repuesto['precio_unidad'],
                'precioVenta' => $repuesto['precio'],
                'costo' => $repuesto['costo']
            ];
        }, $repuestos);
    }

    /**
     * Registrar movimiento en historial_stock
     */
    private function registrarMovimientoStock($idProducto, $tipoMovimiento, $cantidad, $costo, $observaciones, $idOrdenTrabajo, $tipoOrigen)
    {
        try {
            $usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Sistema';
            
            $sql = "INSERT INTO historial_stock 
                    (id_producto, tipo_movimiento, cantidad, costo_compra, fecha_movimiento, usuario, observaciones, id_orden_trabajo, tipo_origen) 
                    VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
            
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param(
                "isidssis",
                $idProducto,
                $tipoMovimiento,
                $cantidad,
                $costo,
                $usuario,
                $observaciones,
                $idOrdenTrabajo,
                $tipoOrigen
            );
            
            if ($stmt->execute()) {
                error_log("Movimiento registrado en historial_stock: Producto $idProducto, Tipo: $tipoMovimiento, Cantidad: $cantidad");
            } else {
                error_log("ERROR al registrar movimiento en historial_stock: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Excepción al registrar movimiento: " . $e->getMessage());
        }
    }
}
        