<?php

class GestionAdjunto
{
    private $id_adjunto;
    private $id_archivo;
    private $nombre_adjunto;
    private $tipo_adjunto;
    private $ruta_adjunto;
    private $es_principal;
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
    public function getTipoAdjunto()
    {
        return $this->tipo_adjunto;
    }

    /**
     * @param mixed $tipo_adjunto
     */
    public function setTipoAdjunto($tipo_adjunto)
    {
        $this->tipo_adjunto = $tipo_adjunto;
    }

    /**
     * @return mixed
     */
    public function getRutaAdjunto()
    {
        return $this->ruta_adjunto;
    }

    /**
     * @param mixed $ruta_adjunto
     */
    public function setRutaAdjunto($ruta_adjunto)
    {
        $this->ruta_adjunto = $ruta_adjunto;
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

    public function insertar()
    {
        $sql = "INSERT INTO gestion_adjuntos (id_archivo, nombre_adjunto, tipo_adjunto, ruta_adjunto, es_principal) 
                VALUES ('$this->id_archivo', '$this->nombre_adjunto', '$this->tipo_adjunto', '$this->ruta_adjunto', '$this->es_principal')";
        
        if ($this->conectar->query($sql)) {
            return $this->conectar->insert_id;
        }
        return false;
    }

    public function eliminar()
    {
        $sql = "DELETE FROM gestion_adjuntos WHERE id_adjunto = '$this->id_adjunto'";
        return $this->conectar->query($sql);
    }

    public function eliminarPorArchivo()
    {
        $sql = "DELETE FROM gestion_adjuntos WHERE id_archivo = '$this->id_archivo'";
        return $this->conectar->query($sql);
    }

    public function listarPorArchivo()
    {
        $sql = "SELECT * FROM gestion_adjuntos WHERE id_archivo = '$this->id_archivo' ORDER BY es_principal DESC, fecha_subida DESC";
        return $this->conectar->query($sql);
    }

    public function listarPorTipo()
    {
        $sql = "SELECT * FROM gestion_adjuntos WHERE id_archivo = '$this->id_archivo' AND tipo_adjunto = '$this->tipo_adjunto' ORDER BY fecha_subida DESC";
        return $this->conectar->query($sql);
    }

    public function obtenerAdjuntoPrincipal()
    {
        $sql = "SELECT * FROM gestion_adjuntos WHERE id_archivo = '$this->id_archivo' AND es_principal = '1' LIMIT 1";
        return $this->conectar->query($sql)->fetch_assoc();
    }

    public function establecerComoPrincipal()
    {
        // Primero quitar el principal actual
        $sql1 = "UPDATE gestion_adjuntos SET es_principal = '0' WHERE id_archivo = '$this->id_archivo'";
        $this->conectar->query($sql1);
        
        // Establecer el nuevo principal
        $sql2 = "UPDATE gestion_adjuntos SET es_principal = '1' WHERE id_adjunto = '$this->id_adjunto'";
        return $this->conectar->query($sql2);
    }
}