<?php

class Consultas
{
    private $conectar;

    private $ultimoId;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getUltimoId()
    {
        return $this->ultimoId;
    }

    /**
     * @param mixed $ultimoId
     */
    public function setUltimoId($ultimoId): void
    {
        $this->ultimoId = $ultimoId;
    }

    /**
     * @return mysqli
     */
    public function getConectar(): mysqli
    {
        return $this->conectar;
    }
    public function exeSQLInsert($sql)
    {
        $result = $this->conectar->query($sql);
        if ($result) {
            $this->ultimoId = $this->conectar->insert_id;
        }
        return $result;
    }
    public function exeSQL($sql)
    {
        return $this->conectar->query($sql);
    }
    public function buscarProveedor($termino, $empres)
    {
        $sql = "select * from proveedores 
        where  (proveedores.razon_social like '%$termino%' or proveedores.ruc like '%$termino%') 
        order by razon_social asc";
        return $this->conectar->query($sql);
    }
    public function buscarClientes($termino, $empres)
    {
        $sql = "select * from clientes 
        where id_empresa = '$empres' and (datos like '%$termino%' or documento like '%$termino%') 
        order by datos asc";
        return $this->conectar->query($sql);
    }
    public function buscarSerie($termino)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ds.modelo, ds.marca, ds.equipo, ds.numero_serie,
                       e.nombre as equipo_nombre,
                       ma.nombre as marca_nombre,
                       mo.nombre as modelo_nombre
                FROM numero_series ns
                JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                LEFT JOIN equipos e ON ds.equipo = e.id
                LEFT JOIN marcas ma ON ds.marca = ma.id
                LEFT JOIN modelos mo ON ds.modelo = mo.id
                WHERE ds.numero_serie LIKE ? 
                   OR ns.cliente_ruc_dni LIKE ? 
                   OR ds.modelo LIKE ? 
                   OR ds.marca LIKE ?
                   OR ds.equipo LIKE ?
                   OR e.nombre LIKE ?
                ORDER BY ds.numero_serie ASC";

        $stmt = $this->conectar->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return [];
        }

        $searchTerm = "%$termino%";
        $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return [];
        }

        return $stmt->get_result();
    }

    // public function obtenerTodasLasSeries($limite = 100)
    // {
    //     $sql = "SELECT ns.cliente_ruc_dni, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, 
    //                    e.nombre as equipo_nombre,
    //                    ma.nombre as marca_nombre,
    //                    mo.nombre as modelo_nombre
    //             FROM numero_series ns
    //             JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
    //             LEFT JOIN equipos e ON ds.equipo = e.id
    //             LEFT JOIN marcas ma ON ds.marca = ma.id
    //             LEFT JOIN modelos mo ON ds.modelo = mo.id
    //             ORDER BY ds.numero_serie ASC
    //             LIMIT ?";

    //     $stmt = $this->conectar->prepare($sql);

    //     // Verificar si la preparación fue exitosa
    //     if ($stmt === false) {
    //         error_log("Error en preparación de consulta: " . $this->conectar->error);
    //         return [];
    //     }

    //     $stmt->bind_param("i", $limite);

    //     if (!$stmt->execute()) {
    //         error_log("Error en ejecución de consulta: " . $stmt->error);
    //         return [];
    //     }

    //     return $stmt->get_result();
    // }
    public function gestionActivosSerie($termino)
    {
        $sql = "SELECT equipo, marca, modelo, numero_serie 
                FROM maquina
                WHERE numero_serie LIKE ? OR
                      marca LIKE ? OR 
                      equipo LIKE ? OR
                      modelo LIKE ?
                ORDER BY numero_serie ASC";

        $stmt = $this->conectar->prepare($sql);
        $searchTerm = "%$termino%"; // Modificado para buscar en cualquier parte
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        $stmt->execute();
        return $stmt->get_result();
    }


    function buscarProductoCoti($id_empresa, $term)
    {
        $sql = "select * from productos 
        where id_empresa = '$id_empresa' and (descripcion like '%$term%' OR codigo like '%$term%') and sucursal='{$_SESSION['sucursal']}' and estado='1'
        order by descripcion asc";
        return $this->conectar->query($sql);
    }
    function buscarProducto($id_empresa, $term, $alma)
    {
        // Asegurarse de que el término de búsqueda esté correctamente formateado para LIKE
        $term = '%' . $term . '%';

        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.detalle, p.precio, p.precio2, 
                p.precio3, p.precio4, p.precio_unidad, p.costo, p.cantidad, p.descripcion,
                p.usar_multiprecio,p.precio_mayor, p.precio_menor  /* Añadir esta columna */
                FROM productos p
                WHERE p.id_empresa = '$id_empresa' 
                AND (p.nombre LIKE '$term' OR p.descripcion LIKE '$term' OR p.codigo LIKE '$term') 
                AND p.sucursal = '{$_SESSION['sucursal']}'
                AND p.almacen = '$alma' 
                AND p.estado = '1' 
                ORDER BY p.codigo ASC 
                LIMIT 500";

        return $this->conectar->query($sql);
    }

    function buscarRepuesto($id_empresa, $term, $alma)
    {
        // Asegurarse de que el término de búsqueda esté correctamente formateado para LIKE
        $term = '%' . $term . '%';

        $sql = "SELECT r.id_repuesto, r.codigo, r.nombre, r.detalle, r.precio, r.precio2, 
                r.precio3, r.precio4, r.precio_unidad, r.costo, r.cantidad, r.descripcion,
                r.usar_multiprecio, r.precio_mayor, r.precio_menor
                FROM repuestos r
                WHERE r.id_empresa = '$id_empresa' 
                AND (r.nombre LIKE '$term' OR r.descripcion LIKE '$term' OR r.codigo LIKE '$term') 
                AND r.sucursal = '{$_SESSION['sucursal']}'
                AND r.almacen = '$alma' 
                AND r.estado = '1' 
                ORDER BY r.nombre ASC 
                LIMIT 500";

        return $this->conectar->query($sql);
    }
    function buscarSNdoc($empresa, $doc)
    {
        $sql = "select * from documentos_empresas where id_empresa='$empresa' and id_tido='$doc' and sucursal='{$_SESSION['sucursal']}'";
        $resp = $this->conectar->query($sql);
        $result = ["serie" => "", "numero" => "",];
        if ($row = $resp->fetch_assoc()) {
            $result["serie"] = $row["serie"];
            $result["numero"] = $row["numero"];
        }
        return $result;
    }
public function buscarClientePorNombre($termino, $limite = 100)
{
    if (empty($termino)) {
        $sql = "SELECT DISTINCT id, cliente_ruc_dni, cliente_documento FROM numero_series ORDER BY cliente_ruc_dni ASC LIMIT ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $limite);
    } else {
        $sql = "SELECT DISTINCT id, cliente_ruc_dni, cliente_documento FROM numero_series WHERE cliente_ruc_dni LIKE ? ORDER BY cliente_ruc_dni ASC LIMIT ?";
        $stmt = $this->conectar->prepare($sql);
        $searchTerm = "%$termino%";
        $stmt->bind_param("si", $searchTerm, $limite);
    }

    $stmt->execute();
    return $stmt->get_result();
}

    // public function obtenerSeriesPorCliente($cliente_id)
// {
//     if (empty($cliente_id)) {
//         return false;
//     }

    //     $sql = "SELECT ds.id, ds.numero_serie_id, ds.numero_serie, 
//                    ma.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
//             FROM detalle_serie ds
//             JOIN numero_series ns ON ds.numero_serie_id = ns.id
//             LEFT JOIN marcas ma ON ds.marca = ma.id
//             LEFT JOIN modelos mo ON ds.modelo = mo.id
//             LEFT JOIN equipos e ON ds.equipo = e.id
//             WHERE ns.id = ?
//             ORDER BY ds.numero_serie ASC";

    //     $stmt = $this->conectar->prepare($sql);
//     $stmt->bind_param("i", $cliente_id);
//     $stmt->execute();
//     return $stmt->get_result();
// }


    public function obtenerSeriesPorCliente($cliente_id)
    {
        $sql = "SELECT ds.*, 
            m.nombre as marca_nombre, 
            mo.nombre as modelo_nombre, 
            e.nombre as equipo_nombre,
            IFNULL(ds.estado, 'disponible') as estado
            FROM numero_series ns
            LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
            LEFT JOIN marcas m ON ds.marca = m.id
            LEFT JOIN modelos mo ON ds.modelo = mo.id
            LEFT JOIN equipos e ON ds.equipo = e.id
            WHERE ns.id = ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function obtenerSeriesDisponibles($limite = 100)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, 
                   e.nombre as equipo_nombre,
                   ma.nombre as marca_nombre,
                   mo.nombre as modelo_nombre
            FROM numero_series ns
            JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
            LEFT JOIN equipos e ON ds.equipo = e.id
            LEFT JOIN marcas ma ON ds.marca = ma.id
            LEFT JOIN modelos mo ON ds.modelo = mo.id
            WHERE ds.estado = 'disponible'
            ORDER BY ds.numero_serie ASC
            LIMIT ?";

        $stmt = $this->conectar->prepare($sql);

        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return [];
        }

        $stmt->bind_param("i", $limite);

        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return [];
        }

        return $stmt->get_result();
    }

    public function buscarSerieDisponible($searchTerm)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, 
                   e.nombre as equipo_nombre,
                   ma.nombre as marca_nombre,
                   mo.nombre as modelo_nombre
            FROM numero_series ns
            JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
            LEFT JOIN equipos e ON ds.equipo = e.id
            LEFT JOIN marcas ma ON ds.marca = ma.id
            LEFT JOIN modelos mo ON ds.modelo = mo.id
            WHERE ds.numero_serie LIKE ? AND ds.estado = 'disponible'
            ORDER BY ds.numero_serie ASC
            LIMIT 100";

        $stmt = $this->conectar->prepare($sql);

        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return [];
        }

        $searchParam = "%$searchTerm%";
        $stmt->bind_param("s", $searchParam);

        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return [];
        }

        return $stmt->get_result();
    }

    public function obtenerSeriesDisponiblesPreAlerta($limite = 100)
{
   $sql = "SELECT ns.cliente_ruc_dni, ns.cliente_documento, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, 
               e.nombre as equipo_nombre,
               ma.nombre as marca_nombre,
               mo.nombre as modelo_nombre
        FROM numero_series ns
        JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
        LEFT JOIN equipos e ON ds.equipo = e.id
        LEFT JOIN marcas ma ON ds.marca = ma.id
        LEFT JOIN modelos mo ON ds.modelo = mo.id
        WHERE ds.estado_prealerta = 'disponible'
        ORDER BY ds.numero_serie ASC
        LIMIT ?";
    
    $stmt = $this->conectar->prepare($sql);
    
    if ($stmt === false) {
        error_log("Error en preparación de consulta: " . $this->conectar->error);
        return [];
    }
    
    $stmt->bind_param("i", $limite);
    
    if (!$stmt->execute()) {
        error_log("Error en ejecución de consulta: " . $stmt->error);
        return [];
    }
    
    return $stmt->get_result();
}

public function buscarSerieDisponiblePreAlerta($searchTerm)
{
    $sql = "SELECT ns.cliente_ruc_dni, ns.cliente_documento, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, 
                   e.nombre as equipo_nombre,
                   ma.nombre as marca_nombre,
                   mo.nombre as modelo_nombre
            FROM numero_series ns
            JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
            LEFT JOIN equipos e ON ds.equipo = e.id
            LEFT JOIN marcas ma ON ds.marca = ma.id
            LEFT JOIN modelos mo ON ds.modelo = mo.id
            WHERE ds.numero_serie LIKE ? AND ds.estado_prealerta = 'disponible'
            ORDER BY ds.numero_serie ASC
            LIMIT 100";
    
    $stmt = $this->conectar->prepare($sql);
    
    if ($stmt === false) {
        error_log("Error en preparación de consulta: " . $this->conectar->error);
        return [];
    }
    
    $searchParam = "%$searchTerm%";
    $stmt->bind_param("s", $searchParam);
    
    if (!$stmt->execute()) {
        error_log("Error en ejecución de consulta: " . $stmt->error);
        return [];
    }
    
    return $stmt->get_result();
}

public function obtenerSeriesPorClientePreAlerta($cliente_id)
{
   $sql = "SELECT ds.*, ns.cliente_documento,
        m.nombre as marca_nombre, 
        mo.nombre as modelo_nombre, 
        e.nombre as equipo_nombre,
        IFNULL(ds.estado_prealerta, 'disponible') as estado_prealerta
        FROM numero_series ns
        LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
        LEFT JOIN marcas m ON ds.marca = m.id
        LEFT JOIN modelos mo ON ds.modelo = mo.id
        LEFT JOIN equipos e ON ds.equipo = e.id
        WHERE ns.id = ?";
    
    $stmt = $this->conectar->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    
    return $stmt->get_result();
}
}
