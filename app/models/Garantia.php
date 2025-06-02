<?php

class Garantia
{
    private $id_garantia;
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

    // Getters y setters

    public function getIdGarantia()
    {
        return $this->id_garantia;
    }

    public function setIdGarantia($id_garantia)
    {
        $this->id_garantia = $id_garantia;
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
    public function insertar()
    {
        $sql = "INSERT INTO garantia (numero_serie_id, detalle_serie_id, guia_remision, fecha_inicio, fecha_caducidad) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("iisss", $this->numero_serie_id, $this->detalle_serie_id, $this->guia_remision, $this->fecha_inicio, $this->fecha_caducidad);
        $result = $stmt->execute();
    
        if ($result) {
            $this->id_garantia = $this->conectar->insert_id;
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
            // Verificar si la columna series_ids existe en la tabla garantia
            $checkColumnSql = "SHOW COLUMNS FROM garantia LIKE 'series_ids'";
            $columnExists = false;
            
            if ($this->conectar instanceof mysqli) {
                $columnResult = $this->conectar->query($checkColumnSql);
                $columnExists = $columnResult && $columnResult->num_rows > 0;
            } else if ($this->conectar instanceof PDO) {
                $columnResult = $this->conectar->query($checkColumnSql);
                $columnExists = $columnResult && $columnResult->rowCount() > 0;
            }
            
            if ($columnExists) {
                // Si existe la columna series_ids, usarla para obtener múltiples series
                $sql = "SELECT g.*, g.series_ids, ns.cliente_ruc_dni as cliente, ds.marca, ds.modelo, ds.equipo, ds.numero_serie 
                        FROM garantia g
                        JOIN numero_series ns ON g.numero_serie_id = ns.id
                        JOIN detalle_serie ds ON g.detalle_serie_id = ds.id
                        WHERE g.id_garantia = ?";
            } else {
                // Si no existe, usar la consulta original
                $sql = "SELECT g.*, ns.cliente_ruc_dni as cliente, ds.marca, ds.modelo, ds.equipo, ds.numero_serie 
                        FROM garantia g
                        JOIN numero_series ns ON g.numero_serie_id = ns.id
                        JOIN detalle_serie ds ON g.detalle_serie_id = ds.id
                        WHERE g.id_garantia = ?";
            }

            if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("i", $this->id_garantia);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($fila = $result->fetch_assoc()) {
                    $this->cliente = $fila['cliente'];
                    $this->marca = $fila['marca'];
                    $this->modelo = $fila['modelo'];
                    $this->equipo = $fila['equipo'];
                    $this->numero_serie = $fila['numero_serie'];
                    $this->guia_remision = $fila['guia_remision'];
                    $this->fecha_inicio = $fila['fecha_inicio'];
                    $this->fecha_caducidad = $fila['fecha_caducidad'];
                    
                    if ($columnExists && isset($fila['series_ids'])) {
                        $this->series_ids = $fila['series_ids'];
                    }
                }
            } else if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($sql);
                $stmt->execute([$this->id_garantia]);
                
                if ($fila = $stmt->fetch()) {
                    $this->cliente = $fila['cliente'];
                    $this->marca = $fila['marca'];
                    $this->modelo = $fila['modelo'];
                    $this->equipo = $fila['equipo'];
                    $this->numero_serie = $fila['numero_serie'];
                    $this->guia_remision = $fila['guia_remision'];
                    $this->fecha_inicio = $fila['fecha_inicio'];
                    $this->fecha_caducidad = $fila['fecha_caducidad'];
                    
                    if ($columnExists && isset($fila['series_ids'])) {
                        $this->series_ids = $fila['series_ids'];
                    }
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
        // Verificar si la columna series_ids existe en la tabla garantia
        $checkColumnSql = "SHOW COLUMNS FROM garantia LIKE 'series_ids'";
        $columnExists = $this->conectar->query($checkColumnSql)->num_rows > 0;
        
        // Construir la consulta SQL base
        $sql = "SELECT g.*, ns.cliente_ruc_dni, ds.numero_serie, 
                m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre 
                FROM garantia g
                JOIN numero_series ns ON g.numero_serie_id = ns.id
                JOIN detalle_serie ds ON g.detalle_serie_id = ds.id
                LEFT JOIN marcas m ON ds.marca = m.id
                LEFT JOIN modelos mo ON ds.modelo = mo.id
                LEFT JOIN equipos e ON ds.equipo = e.id";
        
        // Si la columna series_ids existe, modificar la consulta para usarla
        if ($columnExists) {
            $sql = "SELECT g.*, ns.cliente_ruc_dni, 
                    GROUP_CONCAT(DISTINCT ds.numero_serie SEPARATOR ', ') as numeros_serie,
                    g.series_ids,
                    m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre 
                    FROM garantia g
                    JOIN numero_series ns ON g.numero_serie_id = ns.id
                    LEFT JOIN detalle_serie ds ON (g.detalle_serie_id = ds.id)
                    LEFT JOIN marcas m ON ds.marca = m.id
                    LEFT JOIN modelos mo ON ds.modelo = mo.id
                    LEFT JOIN equipos e ON ds.equipo = e.id";
        }
        
        // Si hay un filtro de búsqueda, añadimos la condición WHERE
        if ($filtro && $tipo_busqueda) {
            if ($tipo_busqueda == 'serie') {
                $sql .= " WHERE ds.numero_serie LIKE ?";
            } else if ($tipo_busqueda == 'cliente') {
                $sql .= " WHERE ns.cliente_ruc_dni LIKE ?";
            }
        }
        
        $sql .= " GROUP BY g.id_garantia ORDER BY g.id_garantia DESC";
        
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
            // Si la consulta falló, registrar el error y devolver un array vacío
            error_log("Error en la consulta SQL: " . $this->conectar->error);
            return [];
        }
        
        // Devolver los resultados
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        // Registrar el error y devolver un array vacío
        error_log("Excepción en getAllData: " . $e->getMessage());
        return [];
    }
}


    public function getOne($id)
    {
        try {
            $sql = "SELECT g.*, ns.cliente_ruc_dni, ds.modelo, ds.marca, ds.numero_serie 
                    FROM garantia g
                    JOIN numero_series ns ON g.numero_serie_id = ns.id
                    JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
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
    // Añadir este método a la clase Garantia
public function obtenerSeries()
    {
        try {
            // Verificar si tenemos series_ids (para garantías múltiples)
            if (!empty($this->series_ids)) {
                $series = [];
                $seriesIds = json_decode($this->series_ids, true);
                
                if (is_array($seriesIds) && count($seriesIds) > 0) {
                    // Consulta para obtener los detalles de todas las series
                    $placeholders = implode(',', array_fill(0, count($seriesIds), '?'));
                    $query = "SELECT ds.id as id_serie, ds.numero_serie, 
                             m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
                             FROM detalle_serie ds
                             LEFT JOIN marcas m ON ds.marca = m.id
                             LEFT JOIN modelos mo ON ds.modelo = mo.id
                             LEFT JOIN equipos e ON ds.equipo = e.id
                             WHERE ds.id IN ($placeholders)";
                    
                    if ($this->conectar instanceof PDO) {
                        $stmt = $this->conectar->prepare($query);
                        foreach ($seriesIds as $key => $id) {
                            $stmt->bindValue($key + 1, $id);
                        }
                        $stmt->execute();
                        $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else if ($this->conectar instanceof mysqli) {
                        $stmt = $this->conectar->prepare($query);
                        $types = str_repeat('i', count($seriesIds));
                        $params = array_merge([$types], $seriesIds);
                        call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $series = $result->fetch_all(MYSQLI_ASSOC);
                    }
                    
                    return $series;
                }
            }
            
            // Si no hay series_ids o está vacío, intentar obtener la serie única
            $query = "SELECT ds.id as id_serie, ds.numero_serie, 
                     m.nombre as marca_nombre, mo.nombre as modelo_nombre, e.nombre as equipo_nombre
                     FROM detalle_serie ds
                     LEFT JOIN marcas m ON ds.marca = m.id
                     LEFT JOIN modelos mo ON ds.modelo = mo.id
                     LEFT JOIN equipos e ON ds.equipo = e.id
                     WHERE ds.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->detalle_serie_id]);
                $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $series;
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->detalle_serie_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $series = $result->fetch_all(MYSQLI_ASSOC);
                return $series;
            }
            
            // Si no se encontró nada, devolver un array vacío
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
            $query = "SELECT m.nombre FROM marcas m WHERE m.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->marca]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['nombre'] : $this->marca;
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->marca);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result ? $result['nombre'] : $this->marca;
            }
            
            return $this->marca;
        } catch (Exception $e) {
            error_log("Error en getMarcaNombre: " . $e->getMessage());
            return $this->marca;
        }
    }

    // Método para obtener el nombre del modelo
    public function getModeloNombre()
    {
        try {
            $query = "SELECT m.nombre FROM modelos m WHERE m.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->modelo]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['nombre'] : $this->modelo;
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->modelo);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result ? $result['nombre'] : $this->modelo;
            }
            
            return $this->modelo;
        } catch (Exception $e) {
            error_log("Error en getModeloNombre: " . $e->getMessage());
            return $this->modelo;
        }
    }

    // Método para obtener el nombre del equipo
    public function getEquipoNombre()
    {
        try {
            $query = "SELECT e.nombre FROM equipos e WHERE e.id = ?";
            
            if ($this->conectar instanceof PDO) {
                $stmt = $this->conectar->prepare($query);
                $stmt->execute([$this->equipo]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['nombre'] : $this->equipo;
            } else if ($this->conectar instanceof mysqli) {
                $stmt = $this->conectar->prepare($query);
                $stmt->bind_param('i', $this->equipo);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result ? $result['nombre'] : $this->equipo;
            }
            
            return $this->equipo;
        } catch (Exception $e) {
            error_log("Error en getEquipoNombre: " . $e->getMessage());
            return $this->equipo;
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
}
