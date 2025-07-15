<?php
// app/models/Constancia.php

require_once "app/models/BaseDocumento.php";

class Constancia extends BaseDocumento
{
    protected $tableName = 'constancias';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    // Métodos específicos de Constancia si los hay
    // Por ahora, toda la funcionalidad está en la clase base
    
    // Alias para mantener compatibilidad con código existente
    public function obtenerConstancia($id)
    {
        return $this->obtenerDocumento($id);
    }
    
    public function insertarConstancia()
    {
        return $this->insertarDocumento();
    }
    
    public function actualizarConstancia()
    {
        return $this->actualizarDocumento();
    }
    
    public function eliminarConstancia($id)
    {
        return $this->eliminarDocumento($id);
    }
    
    public function listarConstancias($filtro = null, $tipo_busqueda = null)
    {
        return $this->listarDocumentos($filtro, $tipo_busqueda);
    }
    
    public function obtenerTiposConstancias()
    {
        return $this->obtenerTiposDocumentos();
    }
}