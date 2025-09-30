<?php

class CajaController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function cerrarCajaChica()
    {
        $respuesta = ["res" => false];
        $sql = "UPDATE caja_empresa SET estado = '0', entrada = ?, salida = ?, fecha_cierre = NOW() WHERE caja_id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ddi", $_POST['ingreso'], $_POST['egreso'], $_POST['caja']);

        if ($stmt->execute()) {
            $respuesta["res"] = true;
        }
        $stmt->close();
        return json_encode($respuesta);
    }

    public function agregarMovimiento()
    {
        $respuesta = ["res" => false];
        $documento = $_POST['documento'] ?? '';
        
        $entrada = 0;
        $salida = 0;
        
        if ($_POST['tipo'] == '1') { // Egreso
            $salida = $_POST['monto'];
        } else { // Ingreso
            $entrada = $_POST['monto'];
        }

        $sql = "INSERT INTO caja_chica (id_caja_empresa, hora, detalle, salida, entrada, metodo, documento) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("issddis", $_POST['caja'], $_POST['hora'], $_POST['detalle'], $salida, $entrada, $_POST['metodo'], $documento);

        if ($stmt->execute()) {
            $respuesta["res"] = true;
        }
        $stmt->close();
        return json_encode($respuesta);
    }

    public function listar()
    {
        $listaTotal = [];
        
        // Primera consulta a caja_chica
        $sql1 = "SELECT * FROM caja_chica WHERE id_caja_empresa = ? ORDER BY caja_chica_id DESC";
        $stmt1 = $this->conexion->prepare($sql1);
        $stmt1->bind_param("i", $_POST['cod']);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1) {
            while ($row = $result1->fetch_assoc()) {
                $listaTotal[] = [
                    'detalle' => $row['detalle'],
                    'salida' => $row['salida'],
                    'entrada' => $row['entrada'],
                    'hora' => $row['hora'],
                    'metodo' => $row['metodo'] ?? 1,
                    'documento' => $row['documento'] ?? '',
                    'caja_chica_id' => $row['caja_chica_id']
                ];
            }
        }
        $stmt1->close();

        // Segunda consulta a ventas
        $dateHoy = date('Y-m-d');
        $sql2 = "SELECT v.id_venta, v.fecha_emision, CONCAT( ds.abreviatura , ' | ' , v.serie , ' - ', v.numero) AS detalle, 
            v.total AS entrada, ds.nombre as tipo_documento, v.serie, v.numero 
            FROM ventas AS v
            LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
            WHERE v.id_empresa = ? AND v.sucursal = ? AND v.medoto_pago_id = '10' AND v.fecha_emision = ?
            ORDER BY v.id_venta DESC";
        
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->bind_param("iis", $_SESSION['id_empresa'], $_SESSION['sucursal'], $dateHoy);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        if ($result2) {
            while ($row2 = $result2->fetch_assoc()) {
                $listaTotal[] = [
                    'detalle' => $row2['detalle'],
                    'salida' => 0,
                    'entrada' => $row2['entrada'],
                    'hora' => '-',
                    'metodo' => 1,
                    'documento' => $row2['tipo_documento'] . ' ' . $row2['serie'] . '-' . $row2['numero'],
                    'id_venta' => $row2['id_venta']
                ];
            }
        }
        $stmt2->close();
        
        return json_encode($listaTotal);
    }

    public function aperturarCaja()
    {
        $respuesta = ["res" => false];
        
        // Obtener el siguiente número
        $sqlCount = "SELECT MAX(caja_id) as ultimo_id FROM caja_empresa WHERE id_empresa = ? AND sucursal = ?";
        $stmtCount = $this->conexion->prepare($sqlCount);
        $stmtCount->bind_param("ii", $_SESSION['id_empresa'], $_SESSION['sucursal']);
        $stmtCount->execute();
        $resultCount = $stmtCount->get_result();
        $rowCount = $resultCount->fetch_assoc();
        $ultimoId = $rowCount['ultimo_id'] ? $rowCount['ultimo_id'] : 0;
        $siguienteNumero = 'CA-' . str_pad($ultimoId + 1, 3, '0', STR_PAD_LEFT);
        $stmtCount->close();

        // Insertar en caja_empresa
        $sql = "INSERT INTO caja_empresa (id_empresa, sucursal, numero, detalle, fecha, entrada, salida) VALUES (?, ?, ?, ?, NOW(), '', '')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiss", $_SESSION['id_empresa'], $_SESSION['sucursal'], $siguienteNumero, $_POST['detalle']);

        if ($stmt->execute()) {
            $caja_id = $this->conexion->insert_id;
            $stmt->close();

            // Insertar el movimiento de apertura en caja_chica
            $sql_chica = "INSERT INTO caja_chica (id_caja_empresa, hora, detalle, tipo, entrada, salida, metodo) VALUES (?, ?, 'Apertura de caja', 'a', ?, 0, 1)";
            $stmt_chica = $this->conexion->prepare($sql_chica);
            $stmt_chica->bind_param("isd", $caja_id, $_POST['hora'], $_POST['monto']);
            if ($stmt_chica->execute()) {
                $respuesta["res"] = true;
            }
            $stmt_chica->close();
        } else {
            $stmt->close();
        }

        return json_encode($respuesta);
    }

    public function updateMovimiento()
    {
        $respuesta = ["res" => false];
        $documento = $_POST['documento'] ?? '';
        $id = $_POST['caja_chica_id'];
        $monto = $_POST['monto'];
        $detalle = $_POST['detalle'];
        $metodo = $_POST['metodo'];

        $entrada = 0;
        $salida = 0;

        if ($_POST['tipo'] == '1') { // Egreso
            $salida = $monto;
        } else { // Ingreso
            $entrada = $monto;
        }

        $sql = "UPDATE caja_chica SET detalle = ?, entrada = ?, salida = ?, metodo = ?, documento = ? WHERE caja_chica_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sddisi", $detalle, $entrada, $salida, $metodo, $documento, $id);

        if ($stmt->execute()) {
            $respuesta["res"] = true;
        }
        $stmt->close();
        return json_encode($respuesta);
    }

    public function deleteMovimiento()
    {
        $respuesta = ["res" => false];
        $id = $_POST['id'];

        $sql = "DELETE FROM caja_chica WHERE caja_chica_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $respuesta["res"] = true;
        }
        $stmt->close();
        return json_encode($respuesta);
    }
    
    public function obtenerSiguienteNumero()
    {
        $sql = "SELECT MAX(caja_id) as ultimo_id FROM caja_empresa WHERE id_empresa = ? AND sucursal = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['id_empresa'], $_SESSION['sucursal']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $ultimoId = $row['ultimo_id'] ? $row['ultimo_id'] : 0;
        $siguienteNumero = 'CA-' . str_pad($ultimoId + 1, 3, '0', STR_PAD_LEFT);
        $stmt->close();

        return json_encode(['numero' => $siguienteNumero]);
    }


}
