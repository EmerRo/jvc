<?php
// app/models/CartaTemplate.php

class CartaTemplate
{
    private $id;
    private $titulo;
    private $contenido;
    private $header_image;
    private $footer_image;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $conectar;

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
        $sql = "SELECT * FROM carta_templates WHERE activo = 1 ORDER BY id DESC LIMIT 1";
        $result = $this->conectar->query($sql);

        if ($fila = $result->fetch_assoc()) {
            $this->id = $fila['id'];
            $this->titulo = $fila['titulo'];
            $this->contenido = $fila['contenido'];
            $this->header_image = $fila['header_image'];
            $this->footer_image = $fila['footer_image'];
            $this->fecha_creacion = $fila['fecha_creacion'];
            $this->fecha_modificacion = $fila['fecha_modificacion'];
            return true;
        } else {
            // Si no hay plantilla, crear una por defecto
            $this->titulo = "CARTA";
            $this->contenido = "<p>Contenido de la carta por defecto.</p>";
            $this->header_image = null;
            $this->footer_image = null;
            $this->insertarTemplate();
            return $this->obtenerTemplateActual();
        }
    }

    public function insertarTemplate()
    {
        $sql = "UPDATE carta_templates SET activo = 0 WHERE activo = 1";
        $this->conectar->query($sql);
        
        $sql = "INSERT INTO carta_templates (titulo, contenido, header_image, footer_image, activo) 
                VALUES (?, ?, ?, ?, 1)";
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
        $sql = "UPDATE carta_templates 
                SET titulo = ?, contenido = ?, header_image = ?, footer_image = ?, 
                    fecha_modificacion = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
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