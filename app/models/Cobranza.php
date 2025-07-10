<?php

class Cobranza
{
    private $conectar;
    
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }
    
    public function getAllCobranzas()
    {
        try {
            $sql = "SELECT 
                        v.id_venta,
                        CONCAT(v.serie, ' | ', v.numero) AS factura, 
                        v.fecha_emision,
                        v.fecha_vencimiento,
                        CONCAT(c.documento, ' | ', c.datos) AS cliente,
                        v.total,
                        COALESCE(SUM(CASE WHEN dv.estado = '1' THEN dv.monto ELSE 0 END), 0) AS pagado,
                        (v.total - COALESCE(SUM(CASE WHEN dv.estado = '1' THEN dv.monto ELSE 0 END), 0)) AS saldo
                    FROM ventas AS v
                    INNER JOIN clientes AS c ON v.id_cliente = c.id_cliente
                    LEFT JOIN dias_ventas AS dv ON v.id_venta = dv.id_venta 
                    WHERE v.estado = 1 
                        AND v.id_tipo_pago = 2 
                        AND v.sucursal = '{$_SESSION['sucursal']}' 
                        AND v.id_empresa = '{$_SESSION['id_empresa']}'
                    GROUP BY v.id_venta, v.serie, v.numero, v.fecha_emision, v.fecha_vencimiento, c.documento, c.datos, v.total
                    ORDER BY v.fecha_emision DESC";
            
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getAllCobranzas: " . $e->getMessage());
            return [];
        }
    }

    public function getAllByIdVenta($id)
    {
        try {
            $sql = "SELECT * FROM dias_ventas WHERE id_venta = '$id' ORDER BY fecha ASC";
            $fila = mysqli_query($this->conectar, $sql);
            return mysqli_fetch_all($fila, MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getAllByIdVenta: " . $e->getMessage());
            return [];
        }
    }
    
    public function pagarCuota($id)
    {
        try {
            $sql = "UPDATE dias_ventas SET estado = '1' WHERE dias_venta_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cuota pagada correctamente',
                    'affected_rows' => $stmt->affected_rows
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la cuota'
                ];
            }
        } catch (Exception $e) {
            error_log("Error en pagarCuota: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
}