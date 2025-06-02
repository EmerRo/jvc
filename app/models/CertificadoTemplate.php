<?php
// app/models/CertificadoTemplate.php

class CertificadoTemplate
{
    private $id;
    private $titulo;
    private $contenido;
    private $imagenesConfig; // Propiedad existente
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    // Getters y Setters existentes
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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
    
    public function getImagenesConfig()
    {
        return $this->imagenesConfig;
    }
    
    public function setImagenesConfig($imagenesConfig)
    {
        $this->imagenesConfig = $imagenesConfig;
    }

    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    public function getFechaModificacion()
    {
        return $this->fecha_modificacion;
    }

    // NUEVOS MÉTODOS PARA MANEJAR IMÁGENES
    
    // Obtener la imagen de encabezado
    public function getHeaderImage()
    {
        $config = $this->getImagenesConfigAsArray();
        return isset($config['header']) ? $config['header'] : null;
    }
    
    // Obtener la imagen de pie de página
    public function getFooterImage()
    {
        $config = $this->getImagenesConfigAsArray();
        return isset($config['footer']) ? $config['footer'] : null;
    }
    
    public function getHeaderImageUrl()
    {
        // Si hay una imagen guardada en la base de datos, devolverla
        if ($this->getHeaderImage()) {
            return $this->getHeaderImage();
        }
        
        // Si no hay imagen, devolver la imagen por defecto
        return URL::to('public/img/garantia/header.png');
    }
    
    public function getFooterImageUrl()
    {
        // Si hay una imagen guardada en la base de datos, devolverla
        if ($this->getFooterImage()) {
            return $this->getFooterImage();
        }
        
        // Si no hay imagen, devolver la imagen por defecto
        return URL::to('public/img/garantia/footer.png');
    }
    
    // Guardar la imagen de encabezado
    public function setHeaderImage($imageData)
    {
        $config = $this->getImagenesConfigAsArray();
        $config['header'] = $imageData;
        $this->setImagenesConfigFromArray($config);
    }
    
    // Guardar la imagen de pie de página
    public function setFooterImage($imageData)
    {
        $config = $this->getImagenesConfigAsArray();
        $config['footer'] = $imageData;
        $this->setImagenesConfigFromArray($config);
    }
    
    // Convertir el JSON de imagenesConfig a un array
    private function getImagenesConfigAsArray()
    {
        if (empty($this->imagenesConfig)) {
            return [];
        }
        
        $config = json_decode($this->imagenesConfig, true);
        return is_array($config) ? $config : [];
    }
    
    // Convertir un array a JSON y guardarlo en imagenesConfig
    private function setImagenesConfigFromArray($configArray)
    {
        $this->imagenesConfig = json_encode($configArray);
    }

    // Métodos existentes para interactuar con la base de datos
    public function obtenerCertificadoActual(): bool
    {
        try {
            // Modificar la consulta para obtener el certificado activo o el más reciente si no hay activo
            $query = "SELECT * FROM certificado_templates WHERE activo = 1 ORDER BY id DESC LIMIT 1";
            
            // Depuración
            error_log("Ejecutando consulta: " . $query);
            
            // Si estás usando PDO
            if ($this->conexion instanceof PDO) {
                $stmt = $this->conexion->prepare($query);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            } 
            // Si estás usando mysqli
            else if ($this->conexion instanceof mysqli) {
                $stmt = $this->conexion->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                $resultado = $result->fetch_assoc();
            }
            else {
                // Tipo de conexión no soportado
                error_log("Tipo de conexión no soportado");
                return false;
            }
            
            if ($resultado) {
                $this->id = $resultado['id'];
                $this->titulo = $resultado['titulo'];
                $this->contenido = $resultado['contenido'];
                $this->imagenesConfig = isset($resultado['imagenes_config']) ? $resultado['imagenes_config'] : '[]';
                $this->fecha_creacion = $resultado['fecha_creacion'];
                $this->fecha_modificacion = $resultado['fecha_modificacion'];
                
                // Depuración
                error_log("Certificado obtenido - ID: " . $this->id);
                error_log("Título: " . $this->titulo);
                error_log("Contenido (primeros 100 caracteres): " . substr($this->contenido, 0, 100) . "...");
                
                return true;
            }
            
            // Si no hay certificado activo, intentar obtener el más reciente
            $query = "SELECT * FROM certificado_templates ORDER BY id DESC LIMIT 1";
            error_log("No se encontró certificado activo, intentando: " . $query);
            
            if ($this->conexion instanceof PDO) {
                $stmt = $this->conexion->prepare($query);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            } else if ($this->conexion instanceof mysqli) {
                $stmt = $this->conexion->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                $resultado = $result->fetch_assoc();
            }
            
            if ($resultado) {
                $this->id = $resultado['id'];
                $this->titulo = $resultado['titulo'];
                $this->contenido = $resultado['contenido'];
                $this->imagenesConfig = isset($resultado['imagenes_config']) ? $resultado['imagenes_config'] : '[]';
                $this->fecha_creacion = $resultado['fecha_creacion'];
                $this->fecha_modificacion = $resultado['fecha_modificacion'];
                
                // Depuración
                error_log("Certificado más reciente obtenido - ID: " . $this->id);
                error_log("Título: " . $this->titulo);
                error_log("Contenido (primeros 100 caracteres): " . substr($this->contenido, 0, 100) . "...");
                
                return true;
            }
            
            error_log("No se encontró ningún certificado");
            return false;
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error en obtenerCertificadoActual: " . $e->getMessage());
            return false;
        }
    }

    public function guardarCertificado(): bool
    {
        try {
            // Primero desactivamos todos los certificados
            if ($this->conexion instanceof PDO) {
                $queryDesactivar = "UPDATE certificado_templates SET activo = 0";
                $this->conexion->exec($queryDesactivar);
                
                // Luego insertamos el nuevo certificado
                $query = "INSERT INTO certificado_templates (titulo, contenido, imagenes_config, fecha_creacion, fecha_modificacion, activo) 
                          VALUES (:titulo, :contenido, :imagenes_config, NOW(), NOW(), 1)";
                
                $stmt = $this->conexion->prepare($query);
                $stmt->bindParam(':titulo', $this->titulo);
                $stmt->bindParam(':contenido', $this->contenido);
                $stmt->bindParam(':imagenes_config', $this->imagenesConfig);
                
                return $stmt->execute();
            } 
            // Si estás usando mysqli
            else if ($this->conexion instanceof mysqli) {
                $queryDesactivar = "UPDATE certificado_templates SET activo = 0";
                $this->conexion->query($queryDesactivar);
                
                // Luego insertamos el nuevo certificado
                $query = "INSERT INTO certificado_templates (titulo, contenido, imagenes_config, fecha_creacion, fecha_modificacion, activo) 
                          VALUES (?, ?, ?, NOW(), NOW(), 1)";
                
                $stmt = $this->conexion->prepare($query);
                $stmt->bind_param("sss", $this->titulo, $this->contenido, $this->imagenesConfig);
                
                return $stmt->execute();
            }
            else {
                // Tipo de conexión no soportado
                return false;
            }
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error en guardarCertificado: " . $e->getMessage());
            return false;
        }
    }
    
    // NUEVO MÉTODO para actualizar un certificado existente
    public function actualizarCertificado(): bool
    {
        try {
            if ($this->conexion instanceof PDO) {
                $query = "UPDATE certificado_templates SET 
                          titulo = :titulo, 
                          contenido = :contenido, 
                          imagenes_config = :imagenes_config, 
                          fecha_modificacion = NOW() 
                          WHERE id = :id";
                
                $stmt = $this->conexion->prepare($query);
                $stmt->bindParam(':titulo', $this->titulo);
                $stmt->bindParam(':contenido', $this->contenido);
                $stmt->bindParam(':imagenes_config', $this->imagenesConfig);
                $stmt->bindParam(':id', $this->id);
                
                return $stmt->execute();
            } 
            else if ($this->conexion instanceof mysqli) {
                $query = "UPDATE certificado_templates SET 
                          titulo = ?, 
                          contenido = ?, 
                          imagenes_config = ?, 
                          fecha_modificacion = NOW() 
                          WHERE id = ?";
                
                $stmt = $this->conexion->prepare($query);
                $stmt->bind_param("sssi", $this->titulo, $this->contenido, $this->imagenesConfig, $this->id);
                
                return $stmt->execute();
            }
            else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en actualizarCertificado: " . $e->getMessage());
            return false;
        }
    }
}