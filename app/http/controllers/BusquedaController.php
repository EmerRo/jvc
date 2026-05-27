<?php
require_once "app/models/Consultas.php";
require_once "app/helpers/ThumbnailHelper.php";

class BusquedaController extends Controller
{
    private $consulta;

    public function __construct()
    {
        $this->consulta = new Consultas();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function puedeVerPrecios(): bool
    {
        if (!isset($_SESSION['id_rol'])) return true;

        $rolId = $_SESSION['id_rol'];
        $conexion = (new Conexion())->getConexion();

        $stmt = $conexion->prepare("SELECT nombre, ver_precios FROM roles WHERE rol_id = ?");
        $stmt->bind_param("i", $rolId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) return true;
        if (strtoupper($row['nombre']) === 'ORDEN TRABAJO') return false;
        return (bool) $row['ver_precios'];
    }

    // ─── Productos / Repuestos ─────────────────────────────────────────────────

    public function buscarProducto($almacen)
    {
        $term      = filter_input(INPUT_GET, 'term');
        $resultados = $this->consulta->buscarProducto($_SESSION['id_empresa'], $term, $almacen);

        $resultado = [];
        foreach ($resultados as $value) {
            $simbolo   = ($value['moneda'] === 'USD') ? '$' : 'S/';
            $thumbnail = ThumbnailHelper::ensureThumbnailExists($value['imagen']);
            $resultado[] = [
                'value'           => "{$value['codigo']} | {$value['nombre']} | P.Venta {$simbolo}: {$value['precio']} | Stock: {$value['cantidad']}",
                'codigo'          => $value['id_producto'],
                'codigo_pp'       => $value['codigo'],
                'detalle'         => $value['detalle'],
                'nombre'          => $value['nombre'],
                'precio'          => $value['precio'],
                'cnt'             => $value['cantidad'],
                'costo'           => $value['costo'],
                'precio_mayor'    => $value['precio_mayor'],
                'precio_menor'    => $value['precio_menor'],
                'imagen'          => $thumbnail ?: $value['imagen'],
                'moneda'          => $value['moneda'],
                'usar_multiprecio'=> $value['usar_multiprecio'],
                'unidad_id'       => $value['unidad'],
            ];
        }
        return json_encode($resultado);
    }

    public function buscarRepuesto($almacen)
    {
        $verPrecios = $this->puedeVerPrecios();
        $term       = filter_input(INPUT_GET, 'term');
        $resultados = $this->consulta->buscarRepuesto($_SESSION['id_empresa'], $term, $almacen);

        $resultado = [];
        foreach ($resultados as $value) {
            $fila = [
                'value'    => $verPrecios
                    ? "{$value['codigo']} | {$value['nombre']} | P.Venta S/: {$value['precio']} | Stock: {$value['cantidad']}"
                    : "{$value['codigo']} | {$value['nombre']} | Stock: {$value['cantidad']}",
                'codigo'          => $value['id_repuesto'],
                'codigo_pp'       => $value['codigo'],
                'descripcion'     => $value['detalle'],
                'nombre'          => $value['nombre'],
                'precio'          => $verPrecios ? $value['precio']         : '0',
                'precio_mayor'    => $verPrecios ? $value['precio_mayor']   : '0',
                'precio_menor'    => $verPrecios ? $value['precio_menor']   : '0',
                'usar_multiprecio'=> $verPrecios ? $value['usar_multiprecio'] : '0',
                'cnt'             => $value['cantidad'],
                'costo'           => $verPrecios ? $value['costo']          : '0',
                'unidad_id'       => $value['unidad'],
            ];
            $resultado[] = $fila;
        }
        return json_encode($resultado);
    }

    public function buscarProductoCoti()
    {
        $term      = filter_input(INPUT_GET, 'term');
        $resultados = $this->consulta->buscarProductoCoti($_SESSION['id_empresa'], $term);

        $resultado = [];
        foreach ($resultados as $value) {
            $simbolo = ($value['moneda'] === 'USD') ? '$' : 'S/';
            $resultado[] = [
                'value'        => "{$value['codigo']} | {$value['descripcion']} | P.Venta {$simbolo}: {$value['precio']} | Stock: {$value['cantidad']} - Almacen {$value['almacen']}",
                'codigo'       => $value['id_producto'],
                'codigo_pp'    => $value['codigo'],
                'descripcion'  => $value['descripcion'],
                'nombre'       => $value['nombre'],
                'precio'       => $value['precio'],
                'cnt'          => $value['cantidad'],
                'costo'        => $value['costo'],
                'precio2'      => $value['precio2'],
                'precio3'      => $value['precio3'],
                'precio4'      => $value['precio4'],
                'precio_unidad'=> $value['precio_unidad'],
                'almacen'      => $value['almacen'],
                'imagen'       => $value['imagen'],
                'moneda'       => $value['moneda'],
            ];
        }
        return json_encode($resultado);
    }

    // ─── Compras (muestra costo en lugar de P.Venta) ──────────────────────────

    public function buscarProductoCompra($almacen)
    {
        $verPrecios = $this->puedeVerPrecios();
        $term       = filter_input(INPUT_GET, 'term');
        $resultados = $this->consulta->buscarProducto($_SESSION['id_empresa'], $term, $almacen);

        $resultado = [];
        foreach ($resultados as $value) {
            $resultado[] = [
                'value'           => $verPrecios
                    ? "{$value['codigo']} | {$value['nombre']} | Costo S/: {$value['costo']} | Stock: {$value['cantidad']}"
                    : "{$value['codigo']} | {$value['nombre']} | Stock: {$value['cantidad']}",
                'codigo'          => $value['id_producto'],
                'codigo_pp'       => $value['codigo'],
                'detalle'         => $value['detalle'],
                'nombre'          => $value['nombre'],
                'precio'          => $value['precio'],
                'cnt'             => $value['cantidad'],
                'costo'           => $value['costo'],
                'precio_mayor'    => $value['precio_mayor'],
                'precio_menor'    => $value['precio_menor'],
                'usar_multiprecio'=> $value['usar_multiprecio'],
                'unidad_id'       => $value['unidad'],
            ];
        }
        return json_encode($resultado);
    }

    public function buscarRepuestoCompra($almacen)
    {
        $verPrecios = $this->puedeVerPrecios();
        $term       = filter_input(INPUT_GET, 'term');
        $resultados = $this->consulta->buscarRepuesto($_SESSION['id_empresa'], $term, $almacen);

        $resultado = [];
        foreach ($resultados as $value) {
            $resultado[] = [
                'value'           => $verPrecios
                    ? "{$value['codigo']} | {$value['nombre']} | Costo S/: {$value['costo']} | Stock: {$value['cantidad']}"
                    : "{$value['codigo']} | {$value['nombre']} | Stock: {$value['cantidad']}",
                'codigo'          => $value['id_repuesto'],
                'codigo_pp'       => $value['codigo'],
                'descripcion'     => $value['detalle'],
                'nombre'          => $value['nombre'],
                'precio'          => $verPrecios ? $value['precio']         : '0',
                'precio_mayor'    => $verPrecios ? $value['precio_mayor']   : '0',
                'precio_menor'    => $verPrecios ? $value['precio_menor']   : '0',
                'usar_multiprecio'=> $verPrecios ? $value['usar_multiprecio'] : '0',
                'cnt'             => $value['cantidad'],
                'costo'           => $verPrecios ? $value['costo']          : '0',
                'unidad_id'       => $value['unidad'],
            ];
        }
        return json_encode($resultado);
    }

    // ─── Stock ────────────────────────────────────────────────────────────────

    public function consultaStockAlmacen()
    {
        $almacen = $_POST['almacen'];
        $producto = $_POST['producto'];
        $sql  = "SELECT * FROM productos
                 WHERE id_producto = $producto
                   AND almacen = $almacen
                   AND id_empresa = '{$_SESSION['id_empresa']}'
                   AND sucursal = '{$_SESSION['sucursal']}'";
        $datos = $this->consulta->exeSQL($sql)->fetch_assoc();
        echo json_encode($datos);
    }
}
