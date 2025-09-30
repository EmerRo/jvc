<?php

class GestionAdjunto
{
    private $id_adjunto;
    private $id_archivo;
    private $nombre_adjunto;
    private $url_pdf;
    private $url_editable;
    private $url_imagen;
    private $url_youtube;
    private $es_principal;
    private $fecha_creacion;
    private $conectar;

    /**
     * GestionAdjunto constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdAdjunto()
    {
        return $this->id_adjunto;
    }

    /**
     * @param mixed $id_adjunto
     */
    public function setIdAdjunto($id_adjunto)
    {
        $this->id_adjunto = $id_adjunto;
    }

    /**
     * @return mixed
     */
    public function getIdArchivo()
    {
        return $this->id_archivo;
    }

    /**
     * @param mixed $id_archivo
     */
    public function setIdArchivo($id_archivo)
    {
        $this->id_archivo = $id_archivo;
    }

    /**
     * @return mixed
     */
    public function getNombreAdjunto()
    {
        return $this->nombre_adjunto;
    }

    /**
     * @param mixed $nombre_adjunto
     */
    public function setNombreAdjunto($nombre_adjunto)
    {
        $this->nombre_adjunto = $nombre_adjunto;
    }

    /**
     * @return mixed
     */
    public function getUrlPdf()
    {
        return $this->url_pdf;
    }

    /**
     * @param mixed $url_pdf
     */
    public function setUrlPdf($url_pdf)
    {
        $this->url_pdf = $url_pdf;
    }

    /**
     * @return mixed
     */
    public function getUrlEditable()
    {
        return $this->url_editable;
    }

    /**
     * @param mixed $url_editable
     */
    public function setUrlEditable($url_editable)
    {
        $this->url_editable = $url_editable;
    }

    /**
     * @return mixed
     */
    public function getUrlImagen()
    {
        return $this->url_imagen;
    }

    /**
     * @param mixed $url_imagen
     */
    public function setUrlImagen($url_imagen)
    {
        $this->url_imagen = $url_imagen;
    }

    /**
     * @return mixed
     */
    public function getUrlYoutube()
    {
        return $this->url_youtube;
    }

    /**
     * @param mixed $url_youtube
     */
    public function setUrlYoutube($url_youtube)
    {
        $this->url_youtube = $url_youtube;
    }

    /**
     * @return mixed
     */
    public function getEsPrincipal()
    {
        return $this->es_principal;
    }

    /**
     * @param mixed $es_principal
     */
    public function setEsPrincipal($es_principal)
    {
        $this->es_principal = $es_principal;
    }

    /**
     * @return mixed
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    /**
     * @param mixed $fecha_creacion
     */
    public function setFechaCreacion($fecha_creacion)
    {
        $this->fecha_creacion = $fecha_creacion;
    }

    public function insertar()
    {
        $sql = "INSERT INTO gestion_adjuntos 
                (id_archivo, nombre_adjunto, url_pdf, url_editable, url_imagen, url_youtube, es_principal, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("issssss", 
            $this->id_archivo,
            $this->nombre_adjunto,
            $this->url_pdf,
            $this->url_editable,
            $this->url_imagen,
            $this->url_youtube,
            $this->es_principal,
            $this->fecha_creacion
        );
        
        if ($stmt->execute()) {
            $this->id_adjunto = $this->conectar->insert_id;
            $stmt->close();
            return $this->id_adjunto;
        }
        
        $stmt->close();
        return false;
    }

    public function modificar()
    {
        $sql = "UPDATE gestion_adjuntos 
                SET nombre_adjunto = ?, 
                    url_pdf = ?, 
                    url_editable = ?, 
                    url_imagen = ?, 
                    url_youtube = ?, 
                    es_principal = ?, 
                    fecha_creacion = ? 
                WHERE id_adjunto = ?";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("sssssssi", 
            $this->nombre_adjunto,
            $this->url_pdf,
            $this->url_editable,
            $this->url_imagen,
            $this->url_youtube,
            $this->es_principal,
            $this->fecha_creacion,
            $this->id_adjunto
        );
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function eliminar()
    {
        $sql = "DELETE FROM gestion_adjuntos WHERE id_adjunto = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_adjunto);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function eliminarPorArchivo()
    {
        $sql = "DELETE FROM gestion_adjuntos WHERE id_archivo = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_archivo);
        
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function listarPorArchivo()
    {
        $sql = "SELECT * FROM gestion_adjuntos 
                WHERE id_archivo = ? 
                ORDER BY es_principal DESC, fecha_creacion ASC";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_archivo);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $stmt->close();
        
        return $resultado;
    }

    public function obtenerAdjuntoPrincipal()
    {
        $sql = "SELECT * FROM gestion_adjuntos 
                WHERE id_archivo = ? AND es_principal = '1' 
                LIMIT 1";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_archivo);
        $stmt->execute();
        
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $resultado;
    }

    public function establecerComoPrincipal()
    {
        // Primero quitar el principal actual
        $sql1 = "UPDATE gestion_adjuntos SET es_principal = '0' WHERE id_archivo = ?";
        $stmt1 = $this->conectar->prepare($sql1);
        $stmt1->bind_param("i", $this->id_archivo);
        $stmt1->execute();
        $stmt1->close();
        
        // Establecer el nuevo principal
        $sql2 = "UPDATE gestion_adjuntos SET es_principal = '1' WHERE id_adjunto = ?";
        $stmt2 = $this->conectar->prepare($sql2);
        $stmt2->bind_param("i", $this->id_adjunto);
        
        $resultado = $stmt2->execute();
        $stmt2->close();
        
        return $resultado;
    }

    // NUEVOS MÉTODOS PARA LOS CAMPOS ESPECÍFICOS
    public function obtenerPorTipo($tipo)
    {
        $campo = '';
        switch ($tipo) {
            case 'pdf':
                $campo = 'url_pdf';
                break;
            case 'editable':
                $campo = 'url_editable';
                break;
            case 'imagen':
                $campo = 'url_imagen';
                break;
            case 'youtube':
                $campo = 'url_youtube';
                break;
            default:
                return false;
        }

        $sql = "SELECT * FROM gestion_adjuntos 
                WHERE id_archivo = ? AND $campo IS NOT NULL 
                ORDER BY es_principal DESC, fecha_creacion ASC";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_archivo);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $stmt->close();
        
        return $resultado;
    }

    public function obtenerPorId()
    {
        $sql = "SELECT * FROM gestion_adjuntos WHERE id_adjunto = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $this->id_adjunto);
        $stmt->execute();
        
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($resultado) {
            $this->id_archivo = $resultado['id_archivo'];
            $this->nombre_adjunto = $resultado['nombre_adjunto'];
            $this->url_pdf = $resultado['url_pdf'];
            $this->url_editable = $resultado['url_editable'];
            $this->url_imagen = $resultado['url_imagen'];
            $this->url_youtube = $resultado['url_youtube'];
            $this->es_principal = $resultado['es_principal'];
            $this->fecha_creacion = $resultado['fecha_creacion'];
            return true;
        }
        
        return false;
    }
}