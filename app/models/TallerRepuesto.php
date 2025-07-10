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

        // Preparar la consulta para insertar repuestos
        $sql = "INSERT INTO taller_repuestos_cotis 
            (id_coti, id_repuesto, cantidad, precio, costo, id_cotizacion_equipo) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);

        // Preparar la consulta para actualizar el stock
        $sqlUpdateStock = "UPDATE repuestos 
                      SET cantidad = cantidad - ? 
                      WHERE id_repuesto = ? 
                      AND cantidad >= ?";
        $stmtUpdateStock = $this->conectar->prepare($sqlUpdateStock);

        foreach ($productos as $producto) {
            // Verificar cantidad antes de la actualización
            $sqlCheckStock = "SELECT cantidad FROM repuestos WHERE id_repuesto = ?";
            $stmtCheckStock = $this->conectar->prepare($sqlCheckStock);
            $stmtCheckStock->bind_param("i", $producto['productoid']);
            $stmtCheckStock->execute();
            $resultStock = $stmtCheckStock->get_result();
            $stockActual = $resultStock->fetch_assoc()['cantidad'];

            // Verificar si hay suficiente cantidad
            if ($stockActual < $producto['cantidad']) {
                throw new Exception("Stock insuficiente para el producto: " . $producto['descripcion']);
            }

            // Obtener el ID del equipo según el índice del equipo activo
            $idEquipo = null;
            if (isset($producto['equipoActivo']) && isset($equiposIds[$producto['equipoActivo']])) {
                $idEquipo = $equiposIds[$producto['equipoActivo']];
                error_log("Asignando equipo ID: " . $idEquipo . " al producto: " . $producto['descripcion']);
            }

            // Insertar en taller_repuestos_cotis
            $stmt->bind_param(
                "iidddi",
                $idCoti,
                $producto['productoid'],
                $producto['cantidad'],
                $producto['precioVenta'],
                $producto['costo'],
                $idEquipo
            );
            $stmt->execute();

            // Actualizar el stock
            $stmtUpdateStock->bind_param(
                "dii",
                $producto['cantidad'],
                $producto['productoid'],
                $producto['cantidad']
            );
            $stmtUpdateStock->execute();

            if ($stmtUpdateStock->affected_rows == 0) {
                throw new Exception("No se pudo actualizar el stock del producto: " . $producto['descripcion']);
            }
        }
    }

    public function obtenerPorCotizacion($id_cotizacion)
    {
        $sql = "SELECT 
            trc.*,
            r.codigo as codigo_prod,
            r.nombre as descripcion,
            r.precio,
            r.precio2,
            r.precio_unidad
            FROM taller_repuestos_cotis trc
            JOIN repuestos r ON trc.id_repuesto = r.id_repuesto
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
}