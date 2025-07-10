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
        $searchTerm = "%$termino%";
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
        $term = '%' . $term . '%';

        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.detalle, p.precio, p.precio2, 
                p.precio3, p.precio4, p.precio_unidad, p.costo, p.cantidad, p.descripcion,
                p.usar_multiprecio,p.precio_mayor, p.precio_menor, p.unidad
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
        $term = '%' . $term . '%';

        $sql = "SELECT r.id_repuesto, r.codigo, r.nombre, r.detalle, r.precio, r.precio2, 
                r.precio3, r.precio4, r.precio_unidad, r.costo, r.cantidad, r.descripcion,
                r.usar_multiprecio, r.precio_mayor, r.precio_menor, r.unidad
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

    // ===== FUNCIONES PARA BÚSQUEDA DE SERIES (CON LÓGICA JSON) =====

    public function buscarClientePorNombre($searchTerm)
    {
        $sql = "SELECT DISTINCT ns.id, ns.cliente_ruc_dni, ns.cliente_documento
                FROM numero_series ns 
                WHERE ns.cliente_ruc_dni LIKE ? 
                ORDER BY ns.cliente_ruc_dni ASC 
                LIMIT 50";

        $stmt = $this->conectar->prepare($sql);
        $searchParam = "%$searchTerm%";
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function obtenerSeriesPorCliente($cliente_id)
    {
        $sql = "SELECT ns.*, ds.*, ds.estado
                FROM numero_series ns
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE ns.id = ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Procesar los resultados para expandir los arrays JSON
        $series_expandidas = [];
        
        while ($row = $result->fetch_assoc()) {
            if ($row['numero_serie']) {
                // Decodificar los arrays JSON
                $numeros_serie = json_decode($row['numero_serie'], true) ?: [];
                $modelos = json_decode($row['modelo'], true) ?: [];
                $marcas = json_decode($row['marca'], true) ?: [];
                $equipos = json_decode($row['equipo'], true) ?: [];

                // Crear una entrada por cada número de serie
                for ($i = 0; $i < count($numeros_serie); $i++) {
                    // Obtener nombres de marca, modelo y equipo
                    $marca_id = $marcas[$i] ?? null;
                    $modelo_id = $modelos[$i] ?? null;
                    $equipo_id = $equipos[$i] ?? null;

                    $marca_nombre = $this->getNombreById('marcas', $marca_id);
                    $modelo_nombre = $this->getNombreById('modelos', $modelo_id);
                    $equipo_nombre = $this->getNombreById('equipos', $equipo_id);

                    $series_expandidas[] = [
                        'id' => $row['id'],
                        'numero_serie_id' => $row['numero_serie_id'],
                        'numero_serie' => $numeros_serie[$i],
                        'marca' => $marca_id,
                        'marca_nombre' => $marca_nombre,
                        'modelo' => $modelo_id,
                        'modelo_nombre' => $modelo_nombre,
                        'equipo' => $equipo_id,
                        'equipo_nombre' => $equipo_nombre,
                        'estado' => $row['estado'] ?? 'disponible',
                        'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                        'cliente_documento' => $row['cliente_documento'] ?? ''
                    ];
                }
            }
        }

        // Convertir a objeto mysqli_result simulado
        return $this->arrayToMysqliResult($series_expandidas);
    }

    public function obtenerSeriesDisponibles($limite = 100)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ns.cliente_documento, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, ds.estado
                FROM numero_series ns
                JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE ds.estado = 'disponible'
                ORDER BY ns.id DESC
                LIMIT ?";

        $stmt = $this->conectar->prepare($sql);

        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return $this->arrayToMysqliResult([]);
        }

        $stmt->bind_param("i", $limite);

        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return $this->arrayToMysqliResult([]);
        }

        $result = $stmt->get_result();
        $series_expandidas = [];

        while ($row = $result->fetch_assoc()) {
            // Decodificar los arrays JSON
            $numeros_serie = json_decode($row['numero_serie'], true) ?: [];
            $modelos = json_decode($row['modelo'], true) ?: [];
            $marcas = json_decode($row['marca'], true) ?: [];
            $equipos = json_decode($row['equipo'], true) ?: [];

            // Crear una entrada por cada número de serie
            for ($i = 0; $i < count($numeros_serie); $i++) {
                $marca_id = $marcas[$i] ?? null;
                $modelo_id = $modelos[$i] ?? null;
                $equipo_id = $equipos[$i] ?? null;

                $marca_nombre = $this->getNombreById('marcas', $marca_id);
                $modelo_nombre = $this->getNombreById('modelos', $modelo_id);
                $equipo_nombre = $this->getNombreById('equipos', $equipo_id);

                $series_expandidas[] = [
                    'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                    'cliente_documento' => $row['cliente_documento'] ?? '',
                    'numero_serie' => $numeros_serie[$i],
                    'marca' => $marca_id,
                    'marca_nombre' => $marca_nombre,
                    'modelo' => $modelo_id,
                    'modelo_nombre' => $modelo_nombre,
                    'equipo' => $equipo_id,
                    'equipo_nombre' => $equipo_nombre
                ];
            }
        }

        return $this->arrayToMysqliResult($series_expandidas);
    }

    public function buscarSerieDisponible($searchTerm)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ns.cliente_documento, ds.modelo, ds.marca, ds.equipo, ds.numero_serie, ds.estado
                FROM numero_series ns
                JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE JSON_SEARCH(ds.numero_serie, 'one', ?) IS NOT NULL 
                AND ds.estado = 'disponible'
                ORDER BY ns.id DESC
                LIMIT 100";

        $stmt = $this->conectar->prepare($sql);

        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return $this->arrayToMysqliResult([]);
        }

        $searchParam = "%$searchTerm%";
        $stmt->bind_param("s", $searchParam);

        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return $this->arrayToMysqliResult([]);
        }

        $result = $stmt->get_result();
        $series_expandidas = [];

        while ($row = $result->fetch_assoc()) {
            // Decodificar los arrays JSON
            $numeros_serie = json_decode($row['numero_serie'], true) ?: [];
            $modelos = json_decode($row['modelo'], true) ?: [];
            $marcas = json_decode($row['marca'], true) ?: [];
            $equipos = json_decode($row['equipo'], true) ?: [];

            // Filtrar solo las series que coinciden con la búsqueda
            for ($i = 0; $i < count($numeros_serie); $i++) {
                if (stripos($numeros_serie[$i], $searchTerm) !== false) {
                    $marca_id = $marcas[$i] ?? null;
                    $modelo_id = $modelos[$i] ?? null;
                    $equipo_id = $equipos[$i] ?? null;

                    $marca_nombre = $this->getNombreById('marcas', $marca_id);
                    $modelo_nombre = $this->getNombreById('modelos', $modelo_id);
                    $equipo_nombre = $this->getNombreById('equipos', $equipo_id);

                    $series_expandidas[] = [
                        'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                        'cliente_documento' => $row['cliente_documento'] ?? '',
                        'numero_serie' => $numeros_serie[$i],
                        'marca' => $marca_id,
                        'marca_nombre' => $marca_nombre,
                        'modelo' => $modelo_id,
                        'modelo_nombre' => $modelo_nombre,
                        'equipo' => $equipo_id,
                        'equipo_nombre' => $equipo_nombre
                    ];
                }
            }
        }

        return $this->arrayToMysqliResult($series_expandidas);
    }

    // ===== FUNCIONES PARA PRE-ALERTA =====

    public function obtenerSeriesDisponiblesPreAlerta($limite = 100)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ns.cliente_documento, ds.modelo, ds.marca, ds.equipo, ds.numero_serie
                FROM numero_series ns
                JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE ds.estado_prealerta = 'disponible'
                ORDER BY ns.id DESC
                LIMIT ?";
        
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return $this->arrayToMysqliResult([]);
        }
        
        $stmt->bind_param("i", $limite);
        
        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return $this->arrayToMysqliResult([]);
        }
        
        $result = $stmt->get_result();
        $series_expandidas = [];

        while ($row = $result->fetch_assoc()) {
            // Decodificar los arrays JSON
            $numeros_serie = json_decode($row['numero_serie'], true) ?: [];
            $modelos = json_decode($row['modelo'], true) ?: [];
            $marcas = json_decode($row['marca'], true) ?: [];
            $equipos = json_decode($row['equipo'], true) ?: [];

            // Crear una entrada por cada número de serie
            for ($i = 0; $i < count($numeros_serie); $i++) {
                $marca_id = $marcas[$i] ?? null;
                $modelo_id = $modelos[$i] ?? null;
                $equipo_id = $equipos[$i] ?? null;

                $marca_nombre = $this->getNombreById('marcas', $marca_id);
                $modelo_nombre = $this->getNombreById('modelos', $modelo_id);
                $equipo_nombre = $this->getNombreById('equipos', $equipo_id);

                $series_expandidas[] = [
                    'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                    'cliente_documento' => $row['cliente_documento'] ?? '',
                    'numero_serie' => $numeros_serie[$i],
                    'marca' => $marca_id,
                    'marca_nombre' => $marca_nombre,
                    'modelo' => $modelo_id,
                    'modelo_nombre' => $modelo_nombre,
                    'equipo' => $equipo_id,
                    'equipo_nombre' => $equipo_nombre
                ];
            }
        }

        return $this->arrayToMysqliResult($series_expandidas);
    }

    public function buscarSerieDisponiblePreAlerta($searchTerm)
    {
        $sql = "SELECT ns.cliente_ruc_dni, ns.cliente_documento, ds.modelo, ds.marca, ds.equipo, ds.numero_serie
                FROM numero_series ns
                JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE JSON_SEARCH(ds.numero_serie, 'one', ?) IS NOT NULL 
                AND ds.estado_prealerta = 'disponible'
                ORDER BY ns.id DESC
                LIMIT 100";
        
        $stmt = $this->conectar->prepare($sql);
        
        if ($stmt === false) {
            error_log("Error en preparación de consulta: " . $this->conectar->error);
            return $this->arrayToMysqliResult([]);
        }
        
        $searchParam = "%$searchTerm%";
        $stmt->bind_param("s", $searchParam);
        
        if (!$stmt->execute()) {
            error_log("Error en ejecución de consulta: " . $stmt->error);
            return $this->arrayToMysqliResult([]);
        }
        
        $result = $stmt->get_result();
        $series_expandidas = [];

        while ($row = $result->fetch_assoc()) {
            // Decodificar los arrays JSON
            $numeros_serie = json_decode($row['numero_serie'], true) ?: [];
            $modelos = json_decode($row['modelo'], true) ?: [];
            $marcas = json_decode($row['marca'], true) ?: [];
            $equipos = json_decode($row['equipo'], true) ?: [];

            // Filtrar solo las series que coinciden con la búsqueda
            for ($i = 0; $i < count($numeros_serie); $i++) {
                if (stripos($numeros_serie[$i], $searchTerm) !== false) {
                    $marca_id = $marcas[$i] ?? null;
                    $modelo_id = $modelos[$i] ?? null;
                    $equipo_id = $equipos[$i] ?? null;

                    $marca_nombre = $this->getNombreById('marcas', $marca_id);
                    $modelo_nombre = $this->getNombreById('modelos', $modelo_id);
                    $equipo_nombre = $this->getNombreById('equipos', $equipo_id);

                    $series_expandidas[] = [
                        'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                        'cliente_documento' => $row['cliente_documento'] ?? '',
                        'numero_serie' => $numeros_serie[$i],
                        'marca' => $marca_id,
                        'marca_nombre' => $marca_nombre,
                        'modelo' => $modelo_id,
                        'modelo_nombre' => $modelo_nombre,
                        'equipo' => $equipo_id,
                        'equipo_nombre' => $equipo_nombre
                    ];
                }
            }
        }

        return $this->arrayToMysqliResult($series_expandidas);
    }

    public function obtenerSeriesPorClientePreAlerta($cliente_id)
    {
        $sql = "SELECT ns.*, ds.*, ds.estado_prealerta
                FROM numero_series ns
                LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
                WHERE ns.id = ?";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Procesar los resultados para expandir los arrays JSON
        $series_expandidas = [];
        
        while ($row = $result->fetch_assoc()) {
            if ($row['numero_serie']) {
                // Decodificar los arrays JSON
                $numeros_serie = json_decode($row['numero_serie'], true) ?: [];
                $modelos = json_decode($row['modelo'], true) ?: [];
                $marcas = json_decode($row['marca'], true) ?: [];
                $equipos = json_decode($row['equipo'], true) ?: [];

                // Crear una entrada por cada número de serie
                for ($i = 0; $i < count($numeros_serie); $i++) {
                    // Obtener nombres de marca, modelo y equipo
                    $marca_id = $marcas[$i] ?? null;
                    $modelo_id = $modelos[$i] ?? null;
                    $equipo_id = $equipos[$i] ?? null;

                    $marca_nombre = $this->getNombreById('marcas', $marca_id);
                    $modelo_nombre = $this->getNombreById('modelos', $modelo_id);
                    $equipo_nombre = $this->getNombreById('equipos', $equipo_id);

                    $series_expandidas[] = [
                        'id' => $row['id'],
                        'numero_serie_id' => $row['numero_serie_id'],
                        'numero_serie' => $numeros_serie[$i],
                        'marca' => $marca_id,
                        'marca_nombre' => $marca_nombre,
                        'modelo' => $modelo_id,
                        'modelo_nombre' => $modelo_nombre,
                        'equipo' => $equipo_id,
                        'equipo_nombre' => $equipo_nombre,
                        'estado_prealerta' => $row['estado_prealerta'] ?? 'disponible',
                        'cliente_ruc_dni' => $row['cliente_ruc_dni'],
                        'cliente_documento' => $row['cliente_documento'] ?? ''
                    ];
                }
            }
        }
        
        return $this->arrayToMysqliResult($series_expandidas);
    }

    // ===== FUNCIONES AUXILIARES =====

    private function getNombreById($tabla, $id)
    {
        if (empty($id)) return '';
        
        $stmt = $this->conectar->prepare("SELECT nombre FROM {$tabla} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($row = $resultado->fetch_assoc()) {
            return $row['nombre'];
        }
        
        return '';
    }

    private function arrayToMysqliResult($array)
    {
        // Crear una clase que simule mysqli_result
        return new class($array) {
            private $data;
            private $position = 0;
            public $num_rows; // ✅ CAMBIO: Agregar como propiedad

            public function __construct($data) {
                $this->data = $data;
                $this->num_rows = count($data); // ✅ CAMBIO: Inicializar la propiedad
            }

            public function fetch_assoc() {
                if ($this->position < count($this->data)) {
                    return $this->data[$this->position++];
                }
                return null;
            }

            public function num_rows() {
                return count($this->data);
            }
        };
    }
}