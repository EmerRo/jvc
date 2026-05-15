<?php

class Almacen
{
    private $id_empresa;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    public function listar()
    {
        $sql = "SELECT * FROM almacenes WHERE id_empresa = '$this->id_empresa' AND estado = '1' ORDER BY principal DESC, id_almacen ASC";
        $result = $this->conectar->query($sql);
        $almacenes = [];
        while ($row = $result->fetch_assoc()) {
            $almacenes[] = $row;
        }
        return $almacenes;
    }

    public function obtener($id)
    {
        $sql = "SELECT * FROM almacenes WHERE id_almacen = '$id' AND id_empresa = '$this->id_empresa'";
        $result = $this->conectar->query($sql);
        return $result->fetch_assoc();
    }

    public function tieneProductos($id)
    {
        $sql = "SELECT COUNT(*) as cnt FROM productos WHERE almacen = '$id' AND estado = '1'";
        $result = $this->conectar->query($sql);
        $row = $result->fetch_assoc();
        return $row['cnt'] > 0;
    }

    public function actualizar($id, $nombre)
    {
        $nombre = $this->conectar->real_escape_string($nombre);
        $sql = "UPDATE almacenes SET nombre = '$nombre' WHERE id_almacen = '$id' AND id_empresa = '$this->id_empresa'";
        return $this->conectar->query($sql);
    }

    public function eliminar($id)
    {
        // Primero verificar si tiene productos
        if ($this->tieneProductos($id)) {
            return ['success' => false, 'error' => 'No se puede eliminar. El almacén tiene productos.'];
        }

        // Eliminar la vista primero
        $this->eliminarVista($id);

        // Desactivar el almacén (soft delete)
        $sql = "UPDATE almacenes SET estado = '0' WHERE id_almacen = '$id' AND id_empresa = '$this->id_empresa'";
        $result = $this->conectar->query($sql);
        
        return ['success' => true, 'message' => 'Almacén eliminado correctamente'];
    }

    public function marcarPrincipal($id)
    {
        // Primero desmarcar todos los demás como principales
        $sql = "UPDATE almacenes SET principal = '0' WHERE id_empresa = '$this->id_empresa' AND estado = '1'";
        $this->conectar->query($sql);

        // Marcar el seleccionado como principal
        $sql = "UPDATE almacenes SET principal = '1' WHERE id_almacen = '$id' AND id_empresa = '$this->id_empresa'";
        return $this->conectar->query($sql);
    }

    public function agregar($nombre)
    {
        $nombre = $this->conectar->real_escape_string($nombre);

        // Verificar si es el primer almacén (marcarlo como principal por defecto)
        $checkSql = "SELECT COUNT(*) as cnt FROM almacenes WHERE id_empresa = '$this->id_empresa' AND estado = '1'";
        $checkResult = $this->conectar->query($checkSql);
        $checkRow = $checkResult->fetch_assoc();
        $esPrincipal = ($checkRow['cnt'] == 0) ? 1 : 0;

        $sql = "INSERT INTO almacenes (nombre, id_empresa, principal) VALUES ('$nombre', '$this->id_empresa', '$esPrincipal')";
        $this->conectar->query($sql);
        $id = $this->conectar->insert_id;

        if ($id) {
            // Crear la vista SQL para el nuevo almacén
            $resultado = $this->crearVista($id);
            if (!$resultado) {
                error_log("Error al crear vista para almacén ID: $id");
            }
        }

        return $id;
    }

    public function crearVistasFaltantes() {
        // Crear vistas para almacenes que no la tienen
        $sql = "SELECT id_almacen, nombre FROM almacenes WHERE id_empresa = '$this->id_empresa' AND estado = '1'";
        $result = $this->conectar->query($sql);
        
        $creados = 0;
        while ($row = $result->fetch_assoc()) {
            $almacenId = $row['id_almacen'];
            // Verificar si la vista ya existe
            $checkSql = "SELECT COUNT(*) as cnt FROM information_schema.views 
                         WHERE table_schema = DATABASE() AND table_name = 'view_productos_$almacenId'";
            $checkResult = $this->conectar->query($checkSql);
            $checkRow = $checkResult->fetch_assoc();
            
            if ($checkRow['cnt'] == 0) {
                if ($this->crearVista($almacenId)) {
                    $creados++;
                }
            }
        }
        return $creados;
    }

    public function eliminarVista($almacenId)
    {
        $sql = "DROP VIEW IF EXISTS view_productos_$almacenId";
        return $this->conectar->query($sql);
    }

    private function crearVista($almacenId)
    {
        // Verificar que no exista primero
        $checkSql = "SELECT COUNT(*) as cnt FROM information_schema.views 
                     WHERE table_schema = DATABASE() AND table_name = 'view_productos_$almacenId'";
        $checkResult = $this->conectar->query($checkSql);
        $checkRow = $checkResult->fetch_assoc();
        
        if ($checkRow['cnt'] > 0) {
            return true; // Ya existe, no recrear
        }

        $sucursal = $_SESSION['sucursal'] ?? '1';
        
        // Usar DROP VIEW IF EXISTS + CREATE VIEW para mayor compatibilidad
        $dropSql = "DROP VIEW IF EXISTS view_productos_$almacenId";
        $this->conectar->query($dropSql);
        
        $sql = "CREATE VIEW view_productos_$almacenId AS
            SELECT p.id_producto, p.cod_barra, p.nombre, p.precio, p.costo, p.cantidad,
                   p.iscbp, p.id_empresa, p.sucursal, p.ultima_salida, p.codsunat,
                   p.usar_barra, p.precio_mayor, p.precio_menor, p.razon_social, p.ruc,
                   p.estado, p.almacen, p.precio2, p.precio3, p.precio4, p.precio_unidad,
                   p.codigo, p.imagen, p.detalle, p.usar_multiprecio,
                   c.nombre AS categoria, u.nombre AS unidad, p.moneda
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria
            LEFT JOIN unidades u ON u.id = p.unidad
            WHERE p.id_empresa = '$this->id_empresa'
              AND p.sucursal = '$sucursal'
              AND p.estado = '1'
              AND p.almacen = '$almacenId'
            ORDER BY CASE WHEN p.codigo LIKE 'JVC%' THEN 0 ELSE 1 END, p.codigo ASC";

        $result = $this->conectar->query($sql);
        
        if ($result) {
            error_log("Vista view_productos_$almacenId creada exitosamente");
        } else {
            error_log("Error al crear vista view_productos_$almacenId: " . $this->conectar->error);
        }
        
        return $result;
    }
}
