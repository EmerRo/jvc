<?php

class NumeroSerie
{
    private $id;
    private $numero;
    private $cliente_ruc_dni;
    private $cliente_documento;
    private $fecha_creacion;
    private $cantidad_equipos;
    private $tipo_maquina;
    private $tiene_cliente;
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

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function getClienteRucDni()
    {
        return $this->cliente_ruc_dni;
    }

    public function setClienteRucDni($cliente_ruc_dni)
    {
        $this->cliente_ruc_dni = $cliente_ruc_dni;
    }

    public function getClienteDocumento()
    {
        return $this->cliente_documento;
    }

    public function setClienteDocumento($cliente_documento)
    {
        $this->cliente_documento = $cliente_documento;
    }

    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion($fecha_creacion)
    {
        $this->fecha_creacion = $fecha_creacion;
    }

    public function getCantidadEquipos()
    {
        return $this->cantidad_equipos;
    }

    public function setCantidadEquipos($cantidad_equipos)
    {
        $this->cantidad_equipos = $cantidad_equipos;
    }

    public function getTieneCliente()
    {
        return $this->tiene_cliente;
    }

    public function setTieneCliente($tiene_cliente)
    {
        $this->tiene_cliente = $tiene_cliente;
    }

    public function getTipoMaquina()
    {
        return $this->tipo_maquina;
    }

    public function setTipoMaquina($tipo_maquina)
    {
        $this->tipo_maquina = $tipo_maquina;
    }

    /**
     * Obtener todas las series con sus detalles
     */
    public function getAll()
    {
        $sql = "SELECT ns.id, ns.numero, ns.cliente_ruc_dni, ns.cliente_documento, 
                ns.fecha_creacion, ns.tiene_cliente, ns.cantidad_equipos, ns.tipo_maquina
                FROM numero_series ns
                ORDER BY ns.numero DESC";

        $resultado = $this->conectar->query($sql);
        $series = [];

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $series[] = $row;
            }
        }

        return $series;
    }

    /**
     * Obtener una serie por ID con sus equipos
     */
    public function getById($id)
    {
        $sql = "SELECT ns.*, 
                (SELECT COUNT(*) FROM detalle_serie WHERE numero_serie_id = ns.id) as total_equipos
                FROM numero_series ns
                WHERE ns.id = ?";

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
     * Insertar nueva serie
     */
    public function insertar()
    {
        try {
            // Obtener el próximo número
            $sql_numero = "SELECT MAX(numero) as ultimo_numero FROM numero_series";
            $resultado_numero = $this->conectar->query($sql_numero);
            $numero = 1;

            if ($row_numero = $resultado_numero->fetch_assoc()) {
                $numero = ($row_numero['ultimo_numero'] ?? 0) + 1;
            }

            $sql = "INSERT INTO numero_series (numero, cliente_ruc_dni, cliente_documento, fecha_creacion, cantidad_equipos, tipo_maquina, tiene_cliente) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param(
                "isssssi",
                $numero,
                $this->cliente_ruc_dni,
                $this->cliente_documento,
                $this->fecha_creacion,
                $this->cantidad_equipos,
                $this->tipo_maquina,
                $this->tiene_cliente
            );

            if ($stmt->execute()) {
                $this->id = $this->conectar->insert_id;
                $this->numero = $numero;
                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error al insertar serie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar serie existente
     */
    public function actualizar()
    {
        try {
            $sql = "UPDATE numero_series 
                    SET cliente_ruc_dni = ?, 
                        cliente_documento = ?, 
                        fecha_creacion = ?, 
                        cantidad_equipos = ?, 
                        tipo_maquina = ?,
                        tiene_cliente = ? 
                    WHERE id = ?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param(
                "sssissi",
                $this->cliente_ruc_dni,
                $this->cliente_documento,
                $this->fecha_creacion,
                $this->cantidad_equipos,
                $this->tipo_maquina,
                $this->tiene_cliente,
                $this->id
            );

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al actualizar serie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar serie (CASCADE eliminará los detalles automáticamente)
     */
    public function eliminar()
    {
        try {
            $sql = "DELETE FROM numero_series WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar serie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener el próximo número de serie
     */
    public function getProximoNumero()
    {
        $sql = "SELECT MAX(numero) as ultimo_numero FROM numero_series";
        $resultado = $this->conectar->query($sql);

        $proximo_numero = 1;
        if ($row = $resultado->fetch_assoc()) {
            $proximo_numero = ($row['ultimo_numero'] ?? 0) + 1;
        }

        return $proximo_numero;
    }

    /**
     * Verificar si tiene garantías relacionadas
     */
    public function tieneGarantias()
    {
        $sql = "SELECT COUNT(*) as total
                FROM garantia g 
                WHERE g.numero_serie_id = ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        return $row['total'] > 0;
    }

    /**
     * Obtener información de garantías relacionadas
     */
    public function getGarantiasInfo()
    {
        $sql = "SELECT g.*, COUNT(dg.id) as total_equipos
                FROM garantia g 
                LEFT JOIN detalle_garantia dg ON g.id_garantia = dg.id_garantia
                WHERE g.numero_serie_id = ?
                GROUP BY g.id_garantia";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $garantias = [];
        while ($row = $resultado->fetch_assoc()) {
            $garantias[] = $row;
        }

        return $garantias;
    }
}
