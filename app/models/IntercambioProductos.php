<?php
class IntercambioProductos {
    private $conectar;

    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerProductos() {
        $sql = "SELECT ie.*, p.nombre, p.codigo 
                FROM ingreso_egreso ie 
                LEFT JOIN productos p ON ie.id_producto = p.id_producto 
                ORDER BY ie.intercambio_id DESC";
        
        return $this->conectar->query($sql);
    }

    public function ingresoAlmacen() {
        $respuesta['res'] = false;
        
        $sql = "INSERT INTO ingreso_egreso SET 
                id_producto = '{$_POST['productoid']}', 
                tipo = '{$_POST['tipo']}',
                cantidad = '{$_POST['cantidad']}', 
                id_usuario = '{$_SESSION['usuario_fac']}', 
                almacen_ingreso = '{$_POST['almacen']}'";
                
        if ($this->conectar->query($sql)) {
            $sql = "UPDATE productos 
                   SET cantidad = cantidad + '{$_POST['cantidad']}' 
                   WHERE id_producto = '{$_POST['productoid']}'";
            
            $this->conectar->query($sql);
            $respuesta['res'] = true;
        }
        
        echo json_encode($respuesta);
    }
}