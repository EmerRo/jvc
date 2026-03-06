<?php

class Garantia
{
    private $id_garantia;
    private $numero; // NUEVO CAMPO
    private $numero_serie_id;
    private $guia_remision;
    private $fecha_inicio;
    private $fecha_caducidad;
    private $conectar;

    //datos para la garantia 
    private $cliente;
    private $marca;
    private $modelo;
    private $numero_serie;
    private $detalle_serie_id;
    private $series_ids; // Para almacenar múltiples series
    private $equipo; // Para almacenar el ID del equipo

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // NUEVO MÉTODO PARA GENERAR NÚMERO CORRELATIVO
    private function generarNumero()
    {
        try {
            // Obtener el último número de garantía
            $sql = "SELECT numero FROM garantia WHERE numero LIKE 'GR-%' ORDER BY id_garantia DESC LIMIT 1";
            $result = $this->conectar->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $ultimoNumero = $row['numero'];
                // Extraer el número (GR-01 -> 01)
                $numero = intval(substr($ultimoNumero, 3));
                $siguienteNumero = $numero + 1;
            } else {
                $siguienteNumero = 1;
            }

            // Formatear con ceros a la izquierda (01, 02, etc.)
            return 'GR-' . str_pad($siguienteNumero, 2, '0', STR_PAD_LEFT);

        } catch (Exception $e) {
            error_log("Error al generar número: " . $e->getMessage());
            return 'GR-01'; // Valor por defecto
        }
    }

    // Getters y setters

    public function getIdGarantia()
    {
        return $this->id_garantia;
    }

    public function setIdGarantia($id_garantia)
    {
        $this->id_garantia = $id_garantia;
    }

    public function getNumero() // NUEVO GETTER
    {
        return $this->numero;
    }

    public function getNumeroSerieId()
    {
        return $this->numero_serie_id;
    }

    public function setNumeroSerieId($numero_serie_id)
    {
        $this->numero_serie_id = $numero_serie_id;
    }

    public function getGuiaRemision()
    {
        return $this->guia_remision;
    }

    public function setGuiaRemision($guia_remision)
    {
        $this->guia_remision = $guia_remision;
    }

    public function getFechaInicio()
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio($fecha_inicio)
    {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function getFechaCaducidad()
    {
        return $this->fecha_caducidad;
    }

    public function setFechaCaducidad($fecha_caducidad)
    {
        $this->fecha_caducidad = $fecha_caducidad;
    }
    public function getCliente()
    {
        return $this->cliente;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function getNumeroSerie()
    {
        return $this->numero_serie;
    }
    public function getDetalleSerieId()
    {
        return $this->detalle_serie_id;
    }
    public function setDetalleSerieId($detalle_serie_id)
    {
        $this->detalle_serie_id = $detalle_serie_id;
    }
    public function getSeriesIds()
    {
        return $this->series_ids;
    }

    public function setSeriesIds($series_ids)
    {
        $this->series_ids = $series_ids;
    }

    public function getEquipo()
    {
        return $this->equipo;
    }

    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;
    }

    // <CHANGE> Agregar campo id_cliente y propiedad
    private $id_cliente;

    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    public function insertar()
    {
        // Generar número correlativo
        $numero = $this->generarNumero();

        $sql = "INSERT INTO garantia (numero, numero_serie_id, detalle_serie_id, id_cliente, guia_remision, fecha_inicio, fecha_caducidad) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("siiisss", $numero, $this->numero_serie_id, $this->detalle_serie_id, $this->id_cliente, $this->guia_remision, $this->fecha_inicio, $this->fecha_caducidad);
        $result = $stmt->execute();

        if ($result) {
            $this->id_garantia = $this->conectar->insert_id;
            $this->numero = $numero; // Guardar el número generado
        }
        return $result;
    }

    public function editar($id_garantia)
    {
        $sql = "UPDATE garantia 
                SET numero_serie_id = ?, detalle_serie_id = ?, guia_remision = ?, fecha_inicio = ?, fecha_caducidad = ? 
                WHERE id_garantia = ?";

        $stmt = $this->conectar->prepare($sql);

        $stmt->bind_param('iisssi', $this->numero_serie_id, $this->detalle_serie_id, $this->guia_remision, $this->fecha_inicio, $this->fecha_caducidad, $id_garantia);

        return $stmt->execute();
    }


    public function obtenerId()
    {
        $sql = "SELECT IFNULL(MAX(id_garantia) + 1, 1) AS codigo FROM garantia";
        $this->id_garantia = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerGarantia()
    {
        try {
            // Consulta simplificada que obtiene datos de garantia y cliente
            $sql = "SELECT g.*, ns.cliente_ruc_dni,
                    CASE 
                        WHEN g.id_cliente IS NOT NULL THEN c.datos
                        ELSE ns.cliente_ruc_dni
                    END as cliente
                    FROM garantia g
                    JOIN numero_series ns ON g.numero_serie_id = ns.id
                    LEFT JOIN clientes c ON g.id_cliente = c.id_cliente
                    WHERE g.id_garantia = ?";

            if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("i", $this->id_garantia);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($fila = $result->fetch_assoc()) {
                    $this->numero = $fila['numero'] ?? null;
                    $this->cliente = $fila['cliente'];
                    $this->id_cliente = $fila['id_cliente'] ?? null;
                    $this->guia_remision = $fila['guia_remision'];
                    $this->fecha_inicio = $fila['fecha_inicio'];
                    $this->fecha_caducidad = $fila['fecha_caducidad'];
                    $this->numero_serie_id = $fila['numero_serie_id'];
                }
            } else if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($sql);
                $stmt->execute([$this->id_garantia]);

                if ($fila = $stmt->fetch()) {
                    $this->numero = $fila['numero'] ?? null;
                    $this->cliente = $fila['cliente'];
                    $this->id_cliente = $fila['id_cliente'] ?? null;
                    $this->guia_remision = $fila['guia_remision'];
                    $this->fecha_inicio = $fila['fecha_inicio'];
                    $this->fecha_caducidad = $fila['fecha_caducidad'];
                    $this->numero_serie_id = $fila['numero_serie_id'];
                }
            }
        } catch (Exception $e) {
            error_log("Error en obtenerGarantia: " . $e->getMessage());
        }
    }

    public function verificarNumeroSerie()
    {
        $sql = "SELECT * FROM garantia WHERE numero_serie = '$this->numero_serie' AND guia_remision = '$this->guia_remision'";
        $result = $this->conectar->query($sql);
        if ($row = $result->fetch_assoc()) {
            $this->id_garantia = $row['id_garantia'];
            return true;
        }
        return false;
    }

    public function verFilas()
    {
        $sql = "SELECT * FROM garantia WHERE guia_remision = '$this->guia_remision'";
        return $this->conectar->query($sql);
    }

    public function buscarGarantias($termino)
    {
        $sql = "SELECT * FROM garantia 
                WHERE guia_remision = '$this->guia_remision' AND (marca LIKE '%$termino%' OR modelo LIKE '%$termino%') 
                ORDER BY marca ASC";
        return $this->conectar->query($sql);
    }

    public function idLast()
    {
        try {
            $sql = "SELECT * FROM garantia ORDER BY id_garantia DESC LIMIT 1";
            $fila = $this->conectar->query($sql)->fetch_object();
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function getAllData($filtro = null, $tipo_busqueda = null)
    {
        try {
            // Construir la consulta SQL base con JOIN a detalle_garantia
            $sql = "SELECT g.id_garantia, g.numero, g.numero_serie_id, g.guia_remision, 
                    g.fecha_inicio, g.fecha_caducidad, g.id_cliente,
                    ns.cliente_ruc_dni,
                    CASE 
                        WHEN g.id_cliente IS NOT NULL THEN c.datos
                        ELSE ns.cliente_ruc_dni
                    END as cliente_nombre,
                    COUNT(dg.id) as total_series,
                    GROUP_CONCAT(dg.numero_serie ORDER BY dg.id SEPARATOR ', ') as numeros_serie
                FROM garantia g
                JOIN numero_series ns ON g.numero_serie_id = ns.id
                LEFT JOIN clientes c ON g.id_cliente = c.id_cliente
                LEFT JOIN detalle_garantia dg ON g.id_garantia = dg.id_garantia";

            // Si hay un filtro de búsqueda, añadimos la condición WHERE
            $whereAdded = false;
            if ($filtro && $tipo_busqueda) {
                if ($tipo_busqueda == 'serie') {
                    $sql .= " WHERE dg.numero_serie LIKE ?";
                    $whereAdded = true;
                } else if ($tipo_busqueda == 'cliente') {
                    $sql .= " WHERE ns.cliente_ruc_dni LIKE ?";
                    $whereAdded = true;
                }
            }

            // Agrupar por garantía
            $sql .= " GROUP BY g.id_garantia, g.numero, g.numero_serie_id, g.guia_remision, 
                      g.fecha_inicio, g.fecha_caducidad, g.id_cliente, ns.cliente_ruc_dni, c.datos";
            
            $sql .= " ORDER BY g.id_garantia DESC";

            // Ejecutar la consulta
            if ($filtro && $tipo_busqueda) {
                $stmt = $this->conectar->prepare($sql);
                $param = "%$filtro%";
                $stmt->bind_param("s", $param);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->conectar->query($sql);
            }

            // Verificar si la consulta fue exitosa
            if ($result === false) {
                error_log("Error en la consulta SQL getAllData: " . $this->conectar->error);
                return [];
            }

            // Procesar los resultados
            $garantias = [];
            while ($row = $result->fetch_assoc()) {
                $garantias[] = $row;
            }

            return $garantias;
        } catch (Exception $e) {
            error_log("Excepción en getAllData: " . $e->getMessage());
            return [];
        }
    }


    public function getOne($id)
    {
        try {

            $sql = "SELECT g.*, ns.cliente_ruc_dni, ds.modelo, ds.marca, ds.numero_serie,
        CASE 
            WHEN g.id_cliente IS NOT NULL THEN c.datos
            ELSE ns.cliente_ruc_dni
        END as cliente_nombre
        FROM garantia g
        JOIN numero_series ns ON g.numero_serie_id = ns.id
        JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
        LEFT JOIN clientes c ON g.id_cliente = c.id_cliente
        WHERE g.id_garantia = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function delete($id)
    {
        try {
            $sql = "DELETE FROM garantia WHERE id_garantia = '$id'";
            $fila = mysqli_query($this->conectar, $sql);
            return $fila;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    // Método para obtener todas las series de una garantía desde detalle_garantia
    public function obtenerSeries()
    {
        try {
            // Consultar desde detalle_garantia usando el id_garantia
            $query = "SELECT dg.numero_serie, dg.marca_id, dg.modelo_id, dg.equipo_id,
                     m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
                     FROM detalle_garantia dg
                     LEFT JOIN marcas m ON dg.marca_id = m.id
                     LEFT JOIN modelos mo ON dg.modelo_id = mo.id
                     LEFT JOIN equipos e ON dg.equipo_id = e.id
                     WHERE dg.id_garantia = ?
                     ORDER BY dg.id";

            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->id_garantia]);
                $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $series;
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->id_garantia);
                $stmt->execute();
                $result = $stmt->get_result();
                $series = $result->fetch_all(MYSQLI_ASSOC);
                return $series;
            }

            return [];
        } catch (Exception $e) {
            error_log("Error en obtenerSeries: " . $e->getMessage());
            return [];
        }
    }

    // Método auxiliar para pasar parámetros por referencia a bind_param
    private function refValues($arr)
    {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    // Método para obtener el nombre de la marca
    public function getMarcaNombre()
    {
        try {
            if (empty($this->marca)) {
                return '';
            }

            $query = "SELECT m.nombre FROM marcas m WHERE m.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->marca]);
                $result = $stmt->fetch();
                return (is_array($result) && isset($result['nombre'])) ? $result['nombre'] : '';
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->marca);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result ? $result['nombre'] : '';
            }

            return '';
        } catch (Exception $e) {
            error_log("Error en getMarcaNombre: " . $e->getMessage());
            return '';
        }
    }

    // Método para obtener el nombre del modelo
    public function getModeloNombre()
    {
        try {
            if (empty($this->modelo)) {
                return '';
            }

            $query = "SELECT m.nombre FROM modelos m WHERE m.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->modelo]);
                $result = $stmt->fetch();
                return (is_array($result) && isset($result['nombre'])) ? $result['nombre'] : '';
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->modelo);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result ? $result['nombre'] : '';
            }

            return '';
        } catch (Exception $e) {
            error_log("Error en getModeloNombre: " . $e->getMessage());
            return '';
        }
    }

    // Método para obtener el nombre del equipo
    public function getEquipoNombre()
    {
        try {
            if (empty($this->equipo)) {
                return '';
            }

            $query = "SELECT e.nombre FROM equipos e WHERE e.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->equipo]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['nombre'] : '';
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->equipo);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result ? $result['nombre'] : '';
            }

            return '';
        } catch (Exception $e) {
            error_log("Error en getEquipoNombre: " . $e->getMessage());
            return '';
        }
    }

    // Método para cargar la propiedad equipo desde la base de datos
    public function cargarEquipo()
    {
        try {
            $query = "SELECT equipo FROM detalle_serie WHERE id = ?";

            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->detalle_serie_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $this->equipo = $result['equipo'];
                }
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->detalle_serie_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                if ($result) {
                    $this->equipo = $result['equipo'];
                }
            }
        } catch (Exception $e) {
            error_log("Error en cargarEquipo: " . $e->getMessage());
        }
    }
    public function getNumeroSerieFormateado()
    {
        return $this->numero_serie;
    }


}