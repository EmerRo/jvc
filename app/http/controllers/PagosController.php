<?php

class PagosController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar=(new Conexion())->getConexion();
    }

    public function render(){
        try {
            $sql = "SELECT 
                com.id_compra,
                CONCAT(com.serie, ' | ' , com.numero) AS factura,
                com.moneda,
                com.fecha_emision,
                com.fecha_vencimiento,
                CONCAT(pro.ruc,' | ' ,pro.razon_social) AS cliente,
                com.total,
                COALESCE(
                    (SELECT SUM(dc.monto) 
                     FROM dias_compras dc 
                     WHERE dc.id_compra = com.id_compra AND dc.estado = '1'), 
                    0
                ) AS pagado,
                (com.total - COALESCE(
                    (SELECT SUM(dc.monto) 
                     FROM dias_compras dc 
                     WHERE dc.id_compra = com.id_compra AND dc.estado = '1'), 
                    0
                )) AS saldo
            FROM compras AS com
            INNER JOIN proveedores AS pro ON com.id_proveedor = pro.proveedor_id
            WHERE com.id_tipo_pago = 2 
                AND com.id_empresa = '{$_SESSION['id_empresa']}'
                AND com.sucursal = '{$_SESSION['sucursal']}'
            ORDER BY com.id_compra DESC";
            
            $fila = mysqli_query($this->conectar, $sql);
            
            if (!$fila) {
                throw new Exception("Error en la consulta: " . mysqli_error($this->conectar));
            }
            
            $result = mysqli_fetch_all($fila, MYSQLI_ASSOC);
            
            // Asegurar que todos los campos numéricos estén definidos
            foreach ($result as &$row) {
                $row['total'] = $row['total'] ?? '0';
                $row['pagado'] = $row['pagado'] ?? '0';
                $row['saldo'] = $row['saldo'] ?? $row['total'];
            }
            
            return json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error en PagosController::render(): " . $e->getMessage());
            return json_encode([]);
        }
    }

    public function getAllByIdCompra(){
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("ID de compra no proporcionado");
            }
            
            $id_compra = mysqli_real_escape_string($this->conectar, $_POST['id']);
            $sql = "SELECT * FROM dias_compras WHERE id_compra = '{$id_compra}' ORDER BY fecha ASC";
            
            $fila = mysqli_query($this->conectar, $sql);
            
            if (!$fila) {
                throw new Exception("Error en la consulta: " . mysqli_error($this->conectar));
            }
            
            return json_encode(mysqli_fetch_all($fila, MYSQLI_ASSOC));
            
        } catch (Exception $e) {
            error_log("Error en PagosController::getAllByIdCompra(): " . $e->getMessage());
            return json_encode([]);
        }
    }

    public function validarLista()
    {
        try {
            $listaPagos = json_decode($_POST['dias_lista'], true);
            return json_encode($listaPagos);
        } catch (Exception $e) {
            error_log("Error en PagosController::validarLista(): " . $e->getMessage());
            return json_encode([]);
        }
    }

    public function pagarCuota()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("ID de cuota no proporcionado");
            }
            
            $id = mysqli_real_escape_string($this->conectar, $_POST['id']);
            $sql = "UPDATE dias_compras SET estado = '1' WHERE dias_compra_id = '{$id}'";
            
            $result = $this->conectar->query($sql);
            
            if (!$result) {
                throw new Exception("Error al actualizar: " . mysqli_error($this->conectar));
            }
            
            return json_encode(['success' => true, 'affected_rows' => $this->conectar->affected_rows]);
            
        } catch (Exception $e) {
            error_log("Error en PagosController::pagarCuota(): " . $e->getMessage());
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function pagarCuotaVentas()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("ID de cuota de venta no proporcionado");
            }
            
            $id = mysqli_real_escape_string($this->conectar, $_POST['id']);
            $sql = "UPDATE dias_ventas SET estado = '1' WHERE dias_venta_id = '{$id}'";
            
            $result = $this->conectar->query($sql);
            
            if (!$result) {
                throw new Exception("Error al actualizar: " . mysqli_error($this->conectar));
            }
            
            return json_encode(['success' => true, 'affected_rows' => $this->conectar->affected_rows]);
            
        } catch (Exception $e) {
            error_log("Error en PagosController::pagarCuotaVentas(): " . $e->getMessage());
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}