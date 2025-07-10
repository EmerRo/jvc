<?php

class GestionActivos
{
    private $id;
    private $numero; // NUEVO CAMPO
    private $cliente_razon_social;
    private $motivo;
    private $marca;
    private $equipo;
    private $modelo;
    private $numero_serie;
    private $fecha_salida;
    private $fecha_ingreso;
    private $observaciones;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // NUEVO MÉTODO PARA GENERAR NÚMERO CORRELATIVO
    private function generarNumero()
    {
        try {
            // Obtener el último número de gestión de activos
            $sql = "SELECT numero FROM gestion_activos WHERE numero LIKE 'GA-%' ORDER BY id DESC LIMIT 1";
            $result = $this->conectar->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $ultimoNumero = $row['numero'];
                // Extraer el número (GA-01 -> 01)
                $numero = intval(substr($ultimoNumero, 3));
                $siguienteNumero = $numero + 1;
            } else {
                $siguienteNumero = 1;
            }
            
            // Formatear con ceros a la izquierda (01, 02, etc.)
            return 'GA-' . str_pad($siguienteNumero, 2, '0', STR_PAD_LEFT);
            
        } catch (Exception $e) {
            error_log("Error al generar número: " . $e->getMessage());
            return 'GA-01'; // Valor por defecto
        }
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function getNumero() // NUEVO GETTER
    {
        return $this->numero;
    }

    public function getClienteRazonSocial()
    {
        return $this->cliente_razon_social;
    }

    public function setClienteRazonSocial($cliente_razon_social)
    {
        $this->cliente_razon_social = strtoupper($cliente_razon_social);
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = strtoupper($motivo);
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    public function getEquipo()
    {
        return $this->equipo;
    }

    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getNumeroSerie()
    {
        return $this->numero_serie;
    }

    public function setNumeroSerie($numero_serie)
    {
        $this->numero_serie = $numero_serie;
    }

    public function getFechaSalida()
    {
        return $this->fecha_salida;
    }

    public function setFechaSalida($fecha_salida)
    {
        $this->fecha_salida = $fecha_salida;
    }

    public function getFechaIngreso()
    {
        return $this->fecha_ingreso;
    }

    public function setFechaIngreso($fecha_ingreso)
    {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
    }

    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    // MÉTODO INSERTAR MODIFICADO
    public function insertar()
    {
        // Generar número correlativo
        $numero = $this->generarNumero();
        
        $sql = "INSERT INTO gestion_activos (numero, cliente_razon_social, motivo, marca, equipo, modelo, numero_serie, fecha_salida, fecha_ingreso, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ssssssssss", $numero, $this->cliente_razon_social, $this->motivo, $this->marca, $this->equipo, $this->modelo, $this->numero_serie, $this->fecha_salida, $this->fecha_ingreso, $this->observaciones);
        $result = $stmt->execute();
        if ($result) {
            $this->id = $this->conectar->insert_id;
            $this->numero = $numero; // Guardar el número generado
        }
        return $result;
    }

    // MÉTODO MODIFICAR ACTUALIZADO
    public function modificar($id)
    {
        $sql = "UPDATE gestion_activos SET cliente_razon_social = ?, motivo = ?, marca = ?, equipo = ?, modelo = ?, numero_serie = ?, fecha_salida = ?, fecha_ingreso = ?, observaciones = ? WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("sssssssssi", $this->cliente_razon_social, $this->motivo, $this->marca, $this->equipo, $this->modelo, $this->numero_serie, $this->fecha_salida, $this->fecha_ingreso, $this->observaciones, $id);
        return $stmt->execute();
    }

    public function obtenerDatos($id)
    {
        $sql = "SELECT * FROM gestion_activos WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->cliente_razon_social = $result['cliente_razon_social'];
            $this->motivo = $result['motivo'];
            $this->marca = $result['marca'];
            $this->equipo = $result['equipo'];
            $this->modelo = $result['modelo'];
            $this->numero_serie = $result['numero_serie'];
            $this->fecha_salida = $result['fecha_salida'];
            $this->fecha_ingreso = $result['fecha_ingreso'];
            $this->observaciones = $result['observaciones'];
        }
        return $result;
    }

    // MÉTODO MEJORADO PARA INCLUIR CÁLCULO DE DÍAS
    public function verFilas()
    {
        $sql = "SELECT *, 
                CASE 
                    WHEN estado = 'CONFIRMADO' THEN 0
                    WHEN fecha_ingreso IS NULL OR fecha_ingreso = '0000-00-00' THEN NULL
                    ELSE DATEDIFF(fecha_ingreso, CURDATE())
                END as dias_restantes,
                CASE 
                    WHEN estado = 'CONFIRMADO' THEN 'CONFIRMADO'
                    WHEN fecha_ingreso IS NULL OR fecha_ingreso = '0000-00-00' THEN 'SIN_FECHA'
                    WHEN DATEDIFF(fecha_ingreso, CURDATE()) < 0 THEN 'VENCIDO'
                    WHEN DATEDIFF(fecha_ingreso, CURDATE()) <= 3 THEN 'URGENTE'
                    ELSE 'NORMAL'
                END as estado_dias
                FROM gestion_activos 
                ORDER BY id DESC";
        return $this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM gestion_activos WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // NUEVO MÉTODO PARA OBTENER EL ÚLTIMO ID
    public function idLast()
    {
        return $this->id;
    }

    // NUEVO MÉTODO PARA OBTENER ESTADÍSTICAS DE DÍAS
    public function obtenerEstadisticasDias()
    {
        $sql = "SELECT 
                COUNT(CASE WHEN estado = 'CONFIRMADO' THEN 1 END) as confirmados,
                COUNT(CASE WHEN estado = 'PENDIENTE' AND DATEDIFF(fecha_ingreso, CURDATE()) < 0 THEN 1 END) as vencidos,
                COUNT(CASE WHEN estado = 'PENDIENTE' AND DATEDIFF(fecha_ingreso, CURDATE()) BETWEEN 0 AND 3 THEN 1 END) as urgentes,
                COUNT(CASE WHEN estado = 'PENDIENTE' AND DATEDIFF(fecha_ingreso, CURDATE()) > 3 THEN 1 END) as normales
                FROM gestion_activos 
                WHERE fecha_ingreso IS NOT NULL AND fecha_ingreso != '0000-00-00'";
        
        $result = $this->conectar->query($sql);
        return $result->fetch_assoc();
    }
}