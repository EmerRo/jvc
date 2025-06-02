<?php
// app/models/ConstanciaPlantilla.php

class ConstanciaPlantilla
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
        $sql = "SELECT * FROM constancias_plantillas ORDER BY id DESC LIMIT 1";
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
            $this->titulo = "CONSTANCIA";
            $this->contenido = "<h2 style='text-align: center;'>CONSTANCIA</h2><p><br></p><p>Por medio de la presente, se hace constar que:</p><p><br></p><p style='text-align: center;'><strong>[NOMBRE DEL CLIENTE]</strong></p><p><br></p><p>Ha recibido el servicio de [TIPO DE SERVICIO] para el equipo [EQUIPO] con número de serie [NÚMERO DE SERIE], el día [FECHA].</p><p><br></p><p>Se extiende la presente constancia para los fines que el interesado considere conveniente.</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE DE LA EMPRESA]</p>";
            $this->header_image = null;
            $this->footer_image = null;
            $this->insertarTemplate();
            return $this->obtenerTemplateActual();
        }
    }
    
    public function insertarTemplate()
    {
        $sql = "INSERT INTO constancias_plantillas (titulo, contenido, header_image, footer_image) 
                VALUES (?, ?, ?, ?)";
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
        $sql = "UPDATE constancias_plantillas 
                SET titulo = ?, contenido = ?, header_image = ?, footer_image = ? 
                WHERE id = ?";
        
        $stmt = $this->conectar->prepare($sql);
        
        $stmt->bind_param("ssssi", $this->titulo, $this->contenido, $this->header_image, $this->footer_image, $this->id);
        
        return $stmt->execute();
    }
}