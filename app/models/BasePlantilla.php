<?php
// app/models/BasePlantilla.php

abstract class BasePlantilla
{
    protected $id;
    protected $titulo;
    protected $contenido;
    protected $header_image;
    protected $footer_image;
    protected $fecha_creacion;
    protected $fecha_modificacion;
    protected $conectar;
    
    // Debe ser definido en las clases hijas
    protected $tableName;
    protected $defaultTitle;
    protected $defaultContent;
    
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }
    
    // Getters y setters
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
    
    public function getHeaderImage()
    {
        return $this->header_image;
    }
    
    public function setHeaderImage($header_image)
    {
        $this->header_image = $header_image;
    }
    
    public function getFooterImage()
    {
        return $this->footer_image;
    }
    
    public function setFooterImage($footer_image)
    {
        $this->footer_image = $footer_image;
    }
    
    public function getHeaderImageUrl()
    {
        if ($this->header_image) {
            return $this->header_image;
        }
        return URL::to('public/img/garantia/header.png'); // Imagen por defecto
    }
    
    public function getFooterImageUrl()
    {
        if ($this->footer_image) {
            return $this->footer_image;
        }
        return URL::to('public/img/garantia/footer.png'); // Imagen por defecto
    }
    
    // Métodos CRUD
    public function obtenerTemplateActual()
    {
        $sql = $this->buildSelectQuery();
        $result = $this->conectar->query($sql);
        
        if ($fila = $result->fetch_assoc()) {
            $this->mapearDatos($fila);
            return true;
        } else {
            // Si no hay plantilla, crear una por defecto
            $this->titulo = $this->defaultTitle;
            $this->contenido = $this->defaultContent;
            $this->header_image = null;
            $this->footer_image = null;
            $this->insertarTemplate();
            return $this->obtenerTemplateActual();
        }
    }
    
    protected function buildSelectQuery()
    {
        if ($this->tableName === 'carta_templates') {
            return "SELECT * FROM {$this->tableName} WHERE activo = 1 ORDER BY id DESC LIMIT 1";
        } else {
            return "SELECT * FROM {$this->tableName} ORDER BY id DESC LIMIT 1";
        }
    }
    
    protected function mapearDatos($fila)
    {
        $this->id = $fila['id'];
        $this->titulo = $fila['titulo'];
        $this->contenido = $fila['contenido'];
        $this->header_image = $fila['header_image'];
        $this->footer_image = $fila['footer_image'];
        $this->fecha_creacion = $fila['fecha_creacion'];
        $this->fecha_modificacion = $fila['fecha_modificacion'];
    }
    
    public function insertarTemplate()
    {
        if ($this->tableName === 'carta_templates') {
            // Para carta_templates, desactivar plantillas anteriores
            $sql = "UPDATE {$this->tableName} SET activo = 0 WHERE activo = 1";
            $this->conectar->query($sql);
            
            $sql = "INSERT INTO {$this->tableName} (titulo, contenido, header_image, footer_image, activo) 
                    VALUES (?, ?, ?, ?, 1)";
        } else {
            $sql = "INSERT INTO {$this->tableName} (titulo, contenido, header_image, footer_image) 
                    VALUES (?, ?, ?, ?)";
        }
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ssss", $this->titulo, $this->contenido, $this->header_image, $this->footer_image);
        $result = $stmt->execute();
        
        if ($result) {
            $this->id = $this->conectar->insert_id;
        }
        return $result;
    }
    
    public function actualizarTemplate()
    {
        try {
            if ($this->tableName === 'carta_templates') {
                $sql = "UPDATE {$this->tableName} 
                        SET titulo = ?, contenido = ?, header_image = ?, footer_image = ?, 
                            fecha_modificacion = CURRENT_TIMESTAMP 
                        WHERE id = ?";
            } else {
                $sql = "UPDATE {$this->tableName} 
                        SET titulo = ?, contenido = ?, header_image = ?, footer_image = ? 
                        WHERE id = ?";
            }
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conectar->error);
            }
            
            $stmt->bind_param("ssssi", 
                $this->titulo, 
                $this->contenido, 
                $this->header_image, 
                $this->footer_image, 
                $this->id
            );
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }
            
            error_log("Template actualizado correctamente. ID: " . $this->id);
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en actualizarTemplate: " . $e->getMessage());
            throw $e;
        }
    }
}