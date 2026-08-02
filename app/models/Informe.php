<?php

class Informe
{
    private $id_informe;
    private $tipo;
    private $titulo;
    private $contenido;
    private $imagen1;
    private $imagen2;
    private $cliente_id;
    private $usuario_id;
    private $numero;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conectar;
    private $cliente_documento;
    private $cliente_direccion;
    private $empresa_razon_social;
    private $empresa_comercial;
    private $empresa_ruc;
    private $empresa_direccion;
    private $empresa_telefono;
    private $empresa_email;
    private $persona_entregar;


    // Datos adicionales para mostrar
    private $cliente_nombre;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // Getters y setters
    public function getIdInforme()
    {
        return $this->id_informe;
    }

    public function setIdInforme($id_informe)
    {
        $this->id_informe = $id_informe;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getContenido()
    {
        return $this->contenido;
    }

    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }

    public function getImagen1()
    {
        return $this->imagen1;
    }

    public function setImagen1($imagen1)
    {
        $this->imagen1 = $imagen1;
    }

    public function getImagen2()
    {
        return $this->imagen2;
    }

    public function setImagen2($imagen2)
    {
        $this->imagen2 = $imagen2;
    }

    public function getClienteId()
    {
        return $this->cliente_id;
    }

    public function setClienteId($cliente_id)
    {
        $this->cliente_id = $cliente_id;
    }

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }

    public function getClienteNombre()
    {
        return $this->cliente_nombre;
    }
    public function getClienteDocumento()
    {
        return $this->cliente_documento;
    }
    public function getClienteDireccion()
    {
        return $this->cliente_direccion;
    }
    public function getEmpresaRazonSocial()
    {
        return $this->empresa_razon_social;
    }
    public function getEmpresaComercial()
    {
        return $this->empresa_comercial;
    }
    public function getEmpresaRuc()
    {
        return $this->empresa_ruc;
    }
    public function getEmpresaDireccion()
    {
        return $this->empresa_direccion;
    }
    public function getEmpresaTelefono()
    {
        return $this->empresa_telefono;
    }
    public function getEmpresaEmail()
    {
        return $this->empresa_email;
    }
    public function getFechaCreacion()
{
    return $this->fecha_creacion;
}
public function getPersonaEntregar()
{
    return $this->persona_entregar;
}

public function setPersonaEntregar($persona_entregar)
{
    $this->persona_entregar = $persona_entregar;
}

public function getNumero()
{
    return $this->numero;
}

public function setNumero($numero)
{
    $this->numero = $numero;
}

   public function insertar()
{
    $sql = "INSERT INTO informes (tipo, titulo, contenido, imagen1, imagen2, cliente_id, usuario_id, persona_entregar, numero) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conectar->prepare($sql);
    
    $stmt->bind_param("sssssiisi", $this->tipo, $this->titulo, $this->contenido, $this->imagen1, $this->imagen2, $this->cliente_id, $this->usuario_id, $this->persona_entregar, $this->numero);
    
    $result = $stmt->execute();

    if ($result) {
        $this->id_informe = $this->conectar->insert_id;
    }
    return $result;
}

public function editar()
{
    $sql = "UPDATE informes 
            SET tipo = ?, titulo = ?, contenido = ?, imagen1 = ?, imagen2 = ?, cliente_id = ?, persona_entregar = ?, usuario_id = ? 
            WHERE id_informe = ?";

    $stmt = $this->conectar->prepare($sql);
    
    // CORREGIR ESTA LÍNEA - Cambiar tipos de datos
    $stmt->bind_param("sssssissi", $this->tipo, $this->titulo, $this->contenido, $this->imagen1, $this->imagen2, $this->cliente_id, $this->persona_entregar, $this->usuario_id, $this->id_informe);

    return $stmt->execute();
}

    public function obtenerInforme()
    {
        $sql = "SELECT i.*, 
                   c.datos as cliente_nombre,
                   c.documento as cliente_documento,
                   c.direccion as cliente_direccion,
                   e.razon_social as empresa_razon_social,
                   e.comercial as empresa_comercial,
                   e.ruc as empresa_ruc,
                   e.direccion as empresa_direccion,
                   e.telefono as empresa_telefono,
                   e.email as empresa_email
            FROM informes i
            LEFT JOIN clientes c ON i.cliente_id = c.id_cliente
            LEFT JOIN empresas e ON e.id_empresa = 12  -- Siempre la empresa JVC
            WHERE i.id_informe = ?";

        $stmt = $this->conectar->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta: " . $this->conectar->error);
            return false;
        }

        $stmt->bind_param("i", $this->id_informe);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($fila = $result->fetch_assoc()) {
            $this->numero = $fila['numero'];
            $this->tipo = $fila['tipo'];
            $this->titulo = $fila['titulo'];
            $this->contenido = $fila['contenido'];
            $this->imagen1 = $fila['imagen1'];
            $this->imagen2 = $fila['imagen2'];
            $this->cliente_id = $fila['cliente_id'];
            $this->usuario_id = $fila['usuario_id'];
            $this->fecha_creacion = $fila['fecha_creacion'];
            $this->fecha_modificacion = $fila['fecha_modificacion'];

            // Datos del cliente
            $this->cliente_nombre = $fila['cliente_nombre'];
            $this->cliente_documento = $fila['cliente_documento'];
            $this->cliente_direccion = $fila['cliente_direccion'];
            $this->persona_entregar = $fila['persona_entregar'];

            // Datos de la empresa
            $this->empresa_razon_social = $fila['empresa_razon_social'];
            $this->empresa_comercial = $fila['empresa_comercial'];
            $this->empresa_ruc = $fila['empresa_ruc'];
            $this->empresa_direccion = $fila['empresa_direccion'];
            $this->empresa_telefono = $fila['empresa_telefono'];
            $this->empresa_email = $fila['empresa_email'];

            return true;
        }
        return false;
    }
    public function getAllData($filtro = null, $tipo = null)
    {
        try {
            // Construir la consulta SQL base SIN las columnas de imágenes para mejorar rendimiento
            $sql = "SELECT i.id_informe, i.tipo, i.titulo, i.contenido, i.cliente_id, i.persona_entregar, 
                          i.usuario_id, i.fecha_creacion, i.fecha_modificacion,
                          c.datos as cliente_nombre, u.nombres as usuario_nombre 
                FROM informes i
                LEFT JOIN clientes c ON i.cliente_id = c.id_cliente
                LEFT JOIN usuarios u ON i.usuario_id = u.usuario_id";

            // Si hay un filtro de búsqueda, añadimos la condición WHERE
            if ($filtro && $tipo) {
                if ($tipo == 'cliente') {
                    $sql .= " WHERE c.datos LIKE ?";
                } else if ($tipo == 'tipo') {
                    $sql .= " WHERE i.tipo LIKE ?";
                } else if ($tipo == 'titulo') {
                    $sql .= " WHERE i.titulo LIKE ?";
                }
            }

            $sql .= " ORDER BY i.fecha_creacion DESC";

            // No imprimir comentarios de depuración en respuestas JSON
            // echo "<!-- DEBUG: SQL Query: " . $sql . " -->\n";

            if ($filtro && $tipo) {
                $stmt = $this->conectar->prepare($sql);
                $param = "%$filtro%";
                $stmt->bind_param("s", $param);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->conectar->query($sql);
            }

            // Verificar si $result es un objeto válido antes de llamar a fetch_all()
            if ($result === false) {
                // La consulta falló, registrar el error pero no imprimir
                $error_msg = "Error en la consulta SQL: " . $this->conectar->error;
                error_log($error_msg);
                // echo "<!-- DEBUG: " . $error_msg . " -->\n";
                return [];
            }

            // No imprimir comentarios de depuración
            // echo "<!-- DEBUG: Número de filas encontradas: " . $result->num_rows . " -->\n";

            // Todo está bien, devolver los resultados
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            // Capturar y registrar cualquier excepción
            $error_msg = "Excepción en getAllData: " . $e->getMessage();
            error_log($error_msg);
            // echo "<!-- DEBUG: " . $error_msg . " -->\n";
            return [];
        }
    }

    public function delete()
    {
        try {
            $sql = "DELETE FROM informes WHERE id_informe = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $this->id_informe);
            return $stmt->execute();
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            return false;
        }
    }

    // Obtener tipos de informes únicos para filtrado
    public function getTiposInforme()
    {
        try {
            // Consulta para obtener tipos únicos
            $sql = "SELECT DISTINCT tipo FROM informes ORDER BY tipo";

            // Depuración: Mostrar la consulta SQL
            // echo "<!-- DEBUG: SQL Query: " . $sql . " -->\n";

            // Ejecutar la consulta
            $result = $this->conectar->query($sql);

            // Verificar si $result es un objeto válido
            if ($result === false) {
                // La consulta falló, registrar el error
                $error_msg = "Error en la consulta SQL: " . $this->conectar->error;
                error_log($error_msg);
                // echo "<!-- DEBUG: " . $error_msg . " -->\n";
                return [];
            }

            // Depuración: Mostrar número de tipos encontrados
            $num_rows = $result->num_rows;
            // echo "<!-- DEBUG: Número de tipos encontrados: " . $num_rows . " -->\n";

            // Si no hay resultados, devolver array vacío
            if ($num_rows == 0) {
                // echo "<!-- DEBUG: No se encontraron tipos de informes -->\n";
                return [];
            }

            // Todo está bien, devolver los resultados
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            // Capturar y registrar cualquier excepción
            $error_msg = "Excepción en getTiposInforme: " . $e->getMessage() . "\n" . $e->getTraceAsString();
            error_log($error_msg);
            // echo "<!-- DEBUG: " . $error_msg . " -->\n";
            return [];
        }
    }
    public function verificarTablas()
    {
        $tablas = ['informes', 'clientes', 'usuarios'];
        $tablas_faltantes = [];

        foreach ($tablas as $tabla) {
            $sql = "SHOW TABLES LIKE '$tabla'";
            $result = $this->conectar->query($sql);

            if ($result === false) {
                // echo "<!-- DEBUG: Error al verificar tabla $tabla: " . $this->conectar->error . " -->\n";
                continue;
            }

            if ($result->num_rows == 0) {
                $tablas_faltantes[] = $tabla;
            } else if ($tabla == 'usuarios') {
                // Verificar que la columna 'nombres' existe en la tabla usuarios
                $checkColumn = "SHOW COLUMNS FROM usuarios LIKE 'nombres'";
                $columnResult = $this->conectar->query($checkColumn);
                if ($columnResult->num_rows == 0) {
                    // echo "<!-- DEBUG: La columna 'nombres' no existe en la tabla usuarios -->\n";
                }
            }
        }

        if (!empty($tablas_faltantes)) {
            // echo "<!-- DEBUG: Tablas faltantes: " . implode(", ", $tablas_faltantes) . " -->\n";
            return false;
        }

        return true;
    }
    public function generarNumeroCorrelativo($tipo)
    {
        // Obtener el año actual
        $anio = date('Y');

        // Contar cuántos informes del mismo tipo existen en el año actual
        $sql = "SELECT COUNT(*) as total FROM informes 
            WHERE tipo = ? AND YEAR(fecha_creacion) = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("si", $tipo, $anio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // El siguiente número será el total + 1
        $numero = $row['total'] + 1;

        // Formatear el número correlativo: NRO.015-2025-JVC
        return sprintf("NRO.%03d-%d-JVC", $numero, $anio);
    }

    // Método para validar que el cliente sea requerido
    public function validarClienteRequerido($cliente_id)
    {
        if (empty($cliente_id) || $cliente_id <= 0) {
            throw new Exception("El cliente es requerido para crear un informe");
        }
        return true;
    }

    /**
     * Convierte la imagen1 de ruta a base64 para usar en PDF
     */
    public function getImagen1Base64()
    {
        if (!$this->imagen1 || strpos($this->imagen1, 'data:image/') === 0) {
            // Ya es base64 o está vacía
            return $this->imagen1;
        }
        
        // Es una ruta de archivo, convertir a base64
        return $this->convertirArchivoABase64($this->imagen1);
    }

    /**
     * Convierte la imagen2 de ruta a base64 para usar en PDF
     */
    public function getImagen2Base64()
    {
        if (!$this->imagen2 || strpos($this->imagen2, 'data:image/') === 0) {
            // Ya es base64 o está vacía
            return $this->imagen2;
        }
        
        // Es una ruta de archivo, convertir a base64
        return $this->convertirArchivoABase64($this->imagen2);
    }

    /**
     * Convierte un archivo de imagen a base64
     */
    private function convertirArchivoABase64($rutaArchivo)
    {
        if (!file_exists($rutaArchivo)) {
            error_log("Archivo de imagen no encontrado: " . $rutaArchivo);
            return null;
        }
        
        try {
            // Leer el archivo
            $imageData = file_get_contents($rutaArchivo);
            if ($imageData === false) {
                error_log("No se pudo leer el archivo de imagen: " . $rutaArchivo);
                return null;
            }
            
            // Detectar el tipo MIME
            $mimeType = mime_content_type($rutaArchivo);
            if (!$mimeType) {
                // Fallback basado en extensión
                $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        $mimeType = 'image/jpeg';
                        break;
                    case 'png':
                        $mimeType = 'image/png';
                        break;
                    case 'gif':
                        $mimeType = 'image/gif';
                        break;
                    default:
                        $mimeType = 'image/jpeg';
                }
            }
            
            // Convertir a base64
            $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            
            return $base64;
            
        } catch (Exception $e) {
            error_log("Error convirtiendo archivo a base64: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene la URL pública de la imagen1 para mostrar en web
     */
    public function getImagen1Url()
    {
        if (!$this->imagen1) {
            return null;
        }
        
        // Si ya es base64, devolverla tal como está
        if (strpos($this->imagen1, 'data:image/') === 0) {
            return $this->imagen1;
        }
        
        // Si es una ruta de archivo, crear URL
        return '/' . $this->imagen1;
    }

    /**
     * Obtiene la URL pública de la imagen2 para mostrar en web
     */
    public function getImagen2Url()
    {
        if (!$this->imagen2) {
            return null;
        }
        
        // Si ya es base64, devolverla tal como está
        if (strpos($this->imagen2, 'data:image/') === 0) {
            return $this->imagen2;
        }
        
        // Si es una ruta de archivo, crear URL
        return '/' . $this->imagen2;
    }

    public function generarSiguienteNumero()
    {
        $sql = "SELECT COALESCE(MAX(numero), 0) + 1 FROM informes";
        $result = $this->conectar->query($sql);
        $row = $result->fetch_row();
        return (int)$row[0];
    }

}