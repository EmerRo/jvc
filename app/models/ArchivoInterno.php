<?php
// app/models/Constancia.php

require_once "app/models/BaseDocumento.php";

class ArchivoInterno extends BaseDocumento
{
    protected $tableName = 'archivos_internos';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    
    // Alias para mantener compatibilidad con código existente
    public function obtenerArchivoInterno($id)
    {
        return $this->obtenerDocumento($id);
    }
    
    public function insertarArchivoInterno()
    {
        return $this->insertarDocumento();
    }

    public function actualizarArchivoInterno()
    {
        return $this->actualizarDocumento();
    }

    public function eliminarArchivoInterno($id)
    {
        return $this->eliminarDocumento($id);
    }
    
    public function listarArchivosInternos($filtro = null, $tipo_busqueda = null)
    {
        return $this->listarDocumentos($filtro, $tipo_busqueda);
    }

    public function obtenerTiposArchivosInternos()
    {
        return $this->obtenerTiposDocumentos();
    }
     // Getter específico para cartas (compatibilidad)
    public function getIdUsuario()
    {
        return $this->usuario_id;
    }
    
    public function setIdUsuario($id_usuario)
    {
        $this->usuario_id = $id_usuario;
    }
}