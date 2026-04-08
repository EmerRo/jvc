<?php

class DetalleSerie
{
    private $id;
    private $numero_serie_id;
    private $modelo_id;
    private $marca_id;
    private $equipo_id;
    private $id_producto;   // NUEVO: vínculo a productos del almacén
    private $numero_serie;
    private $estado;
    private $estado_prealerta;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNumeroSerieId()
    {
        return $this->numero_serie_id;
    }

    public function setNumeroSerieId($numero_serie_id)
    {
        $this->numero_serie_id = $numero_serie_id;
    }

    public function getModeloId()
    {
        return $this->modelo_id;
    }

    public function setModeloId($modelo_id)
    {
        $this->modelo_id = $modelo_id;
    }

    public function getMarcaId()
    {
        return $this->marca_id;
    }

    public function setMarcaId($marca_id)
    {
        $this->marca_id = $marca_id;
    }

    public function getEquipoId()
    {
        return $this->equipo_id;
    }

    public function setEquipoId($equipo_id)
    {
        $this->equipo_id = $equipo_id;
    }

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    public function getNumeroSerie()
    {
        return $this->numero_serie;
    }

    public function setNumeroSerie($numero_serie)
    {
        $this->numero_serie = $numero_serie;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getEstadoPrealerta()
    {
        return $this->estado_prealerta;
    }

    public function setEstadoPrealerta($estado_prealerta)
    {
        $this->estado_prealerta = $estado_prealerta;
    }

    /**
     * Obtener todos los detalles de una serie con JOINs a las tablas relacionadas
     */
    public function getByNumeroSerieId($numero_serie_id)
    {
        $sql = "SELECT ds.*,
                m.nombre as modelo_nombre,
                ma.nombre as marca_nombre,
                e.nombre as equipo_nombre,
                p.codigo as producto_codigo,
                p.nombre as producto_nombre,
                p.precio as producto_precio,
                p.cantidad as producto_stock
                FROM detalle_serie ds
                LEFT JOIN modelos m ON ds.modelo_id = m.id
                LEFT JOIN marcas ma ON ds.marca_id = ma.id
                LEFT JOIN equipos e ON ds.equipo_id = e.id
                LEFT JOIN productos p ON ds.id_producto = p.id_producto
                WHERE ds.numero_serie_id = ?
                ORDER BY ds.id ASC";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $numero_serie_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $detalles = [];
        while ($row = $resultado->fetch_assoc()) {
            $detalles[] = $row;
        }

        return $detalles;
    }

    /**
     * Obtener un detalle por ID
     */
    public function getById($id)
    {
        $sql = "SELECT ds.*,
                m.nombre as modelo_nombre,
                ma.nombre as marca_nombre,
                e.nombre as equipo_nombre,
                p.codigo as producto_codigo,
                p.nombre as producto_nombre,
                p.precio as producto_precio,
                p.cantidad as producto_stock
                FROM detalle_serie ds
                LEFT JOIN modelos m ON ds.modelo_id = m.id
                LEFT JOIN marcas ma ON ds.marca_id = ma.id
                LEFT JOIN equipos e ON ds.equipo_id = e.id
                LEFT JOIN productos p ON ds.id_producto = p.id_producto
                WHERE ds.id = ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {
            return $row;
        }

        return null;
    }

    /**
     * Buscar por número de serie
     */
    public function getByNumeroSerie($numero_serie)
    {
        $sql = "SELECT ds.*,
                ns.numero, ns.cliente_ruc_dni, ns.cliente_documento,
                m.nombre as modelo_nombre,
                ma.nombre as marca_nombre,
                e.nombre as equipo_nombre,
                p.codigo as producto_codigo,
                p.nombre as producto_nombre
                FROM detalle_serie ds
                INNER JOIN numero_series ns ON ds.numero_serie_id = ns.id
                LEFT JOIN modelos m ON ds.modelo_id = m.id
                LEFT JOIN marcas ma ON ds.marca_id = ma.id
                LEFT JOIN equipos e ON ds.equipo_id = e.id
                LEFT JOIN productos p ON ds.id_producto = p.id_producto
                WHERE ds.numero_serie = ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $numero_serie);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {
            return $row;
        }

        return null;
    }

    /**
     * Verificar si un número de serie ya existe
     */
    public function existeNumeroSerie($numero_serie, $excluir_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM detalle_serie WHERE numero_serie = ?";

        if ($excluir_id !== null) {
            $sql .= " AND id != ?";
        }

        $stmt = $this->conectar->prepare($sql);

        if ($excluir_id !== null) {
            $stmt->bind_param("si", $numero_serie, $excluir_id);
        } else {
            $stmt->bind_param("s", $numero_serie);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        return $row['total'] > 0;
    }

    /**
     * Insertar nuevo detalle
     */
    public function insertar()
    {
        try {
            $sql = "INSERT INTO detalle_serie (numero_serie_id, modelo_id, marca_id, equipo_id, id_producto, numero_serie, estado, estado_prealerta)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param(
                "iiiiisss",
                $this->numero_serie_id,
                $this->modelo_id,
                $this->marca_id,
                $this->equipo_id,
                $this->id_producto,
                $this->numero_serie,
                $this->estado,
                $this->estado_prealerta
            );

            if ($stmt->execute()) {
                $this->id = $this->conectar->insert_id;
                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error al insertar detalle serie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar detalle existente
     */
    public function actualizar()
    {
        try {
            $sql = "UPDATE detalle_serie
                    SET modelo_id = ?,
                        marca_id = ?,
                        equipo_id = ?,
                        id_producto = ?,
                        numero_serie = ?,
                        estado = ?,
                        estado_prealerta = ?
                    WHERE id = ?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param(
                "iiiisssi",
                $this->modelo_id,
                $this->marca_id,
                $this->equipo_id,
                $this->id_producto,
                $this->numero_serie,
                $this->estado,
                $this->estado_prealerta,
                $this->id
            );

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al actualizar detalle serie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar detalle
     */
    public function eliminar()
    {
        try {
            $sql = "DELETE FROM detalle_serie WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar detalle serie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar todos los detalles de una serie
     */
    public function eliminarPorNumeroSerieId($numero_serie_id)
    {
        try {
            $sql = "DELETE FROM detalle_serie WHERE numero_serie_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $numero_serie_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar detalles: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar número de serie único de 5 dígitos
     */
    public function generarNumeroSerieUnico()
    {
        $intentos = 0;
        $maxIntentos = 100;

        do {
            $numeroGenerado = rand(10000, 99999);
            $existe = $this->existeNumeroSerie($numeroGenerado);
            $intentos++;
        } while ($existe && $intentos < $maxIntentos);

        if ($existe) {
            return null;
        }

        return $numeroGenerado;
    }

    /**
     * Obtener el último número de serie registrado
     */
    public function getUltimoNumeroSerie()
    {
        $sql = "SELECT numero_serie 
                FROM detalle_serie 
                WHERE numero_serie IS NOT NULL 
                  AND numero_serie != '' 
                  AND numero_serie NOT LIKE '[%'
                ORDER BY CAST(numero_serie AS UNSIGNED) DESC 
                LIMIT 1";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return $row['numero_serie'];
        }

        return null;
    }
}
