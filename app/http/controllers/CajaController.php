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
        $sql = "update caja_empresa set estado ='0',
         entrada='{$_POST['ingreso']}', salida='{$_POST['egreso']}' where caja_id='{$_POST['caja']}'";
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
        }
        return json_encode($respuesta);
    }

    public function agregarMovimiento()
    {
        $respuesta = ["res" => false];
        $documento = $_POST['documento'] ?? ''; // Nuevo campo para documento
        
        $sql = '';
        if ($_POST['tipo'] == '1') {
            $sql = "insert into caja_chica set id_caja_empresa='{$_POST['caja']}',
              hora='{$_POST['hora']}',
              detalle='{$_POST['detalle']}',
              salida='{$_POST['monto']}',
              metodo='{$_POST['metodo']}',
              documento='{$documento}',
              entrada=0";
        } else {
            $sql = "insert into caja_chica set id_caja_empresa='{$_POST['caja']}',
              hora='{$_POST['hora']}',
              detalle='{$_POST['detalle']}',
              salida=0,
              metodo='{$_POST['metodo']}',
              documento='{$documento}',
              entrada='{$_POST['monto']}'";
        }
    
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
        }
        return json_encode($respuesta);
    }
    public function listar()
    {
        $listaTotal = [];
        $sql = "select * from caja_chica where id_caja_empresa ='{$_POST['cod']}' ORDER BY caja_chica_id DESC";
        $result = $this->conexion->query($sql);
        
        // Verificar si la consulta fue exitosa y devolvió resultados
        if ($result && $result->num_rows > 0) {
            foreach ($result as $row) {
                $listaTotal[] = [
                    'detalle' => $row['detalle'], 
                    'salida' => $row['salida'], 
                    'entrada' => $row['entrada'], 
                    'hora' => $row['hora'], 
                    'metodo' => $row['metodo'] ?? 1,
                    'documento' => $row['documento'] ?? '',
                    'caja_chica_id' => $row['caja_chica_id'] // Añadimos el ID real de la tabla
                ];
            }
        }
    
        $dateHoy = date('Y-m-d');
    
        $sql = "SELECT v.id_venta, v.fecha_emision, CONCAT( ds.abreviatura , ' | ' , v.serie , ' - ', v.numero) AS detalle, 
            v.total AS entrada, ds.nombre as tipo_documento, v.serie, v.numero 
            FROM ventas AS v
                     LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                     LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
                 WHERE v.id_empresa = '{$_SESSION['id_empresa']}' AND v.sucursal='{$_SESSION['sucursal']}'  AND v.medoto_pago_id = '10' AND v.fecha_emision ='$dateHoy'
                 ORDER BY v.id_venta DESC";
    
        $result2 = $this->conexion->query($sql);
        
        // Verificar si la segunda consulta fue exitosa y devolvió resultados
        if ($result2 && $result2->num_rows > 0) {
            foreach ($result2 as $row2) {
                $listaTotal[] = [
                    'detalle' => $row2['detalle'], 
                    'salida' => 0, 
                    'entrada' => $row2['entrada'], 
                    'hora' => '-',
                    'metodo' => 1,
                    'documento' => $row2['tipo_documento'] . ' ' . $row2['serie'] . '-' . $row2['numero'],
                    'id_venta' => $row2['id_venta'] // Añadimos el ID de venta
                ];
            }
        }
        
        // Eliminamos el ordenamiento por usort ya que las consultas SQL ya están ordenadas
        // Si aún necesitas ordenar, puedes usar una clave que exista en ambos arrays
        
        return json_encode($listaTotal);
    }

    public function aperturarCaja()
    {
        $respuesta = ["res" => false];
        $sql = "insert into caja_empresa set id_empresa='{$_SESSION['id_empresa']}',
  sucursal='{$_SESSION['sucursal']}',
  detalle='{$_POST['detalle']}',
  fecha=NOW(),
  entrada='',
  salida=''";
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
            $caja_id = $this->conexion->insert_id;
            $sql = "insert into caja_chica set id_caja_empresa='$caja_id',
              hora='{$_POST['hora']}',
              detalle='Apertura de caja',
              tipo='a',
              entrada='{$_POST['monto']}',
              salida=0, metodo = 1";
            $this->conexion->query($sql);
        }

        return json_encode($respuesta);
    }
}
