<?php
// app/models/Carta.php

require_once "app/models/BaseDocumento.php";

class Carta extends BaseDocumento
{
    protected $tableName = 'cartas';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    // Métodos específicos de Carta si los hay
    // Por ahora, toda la funcionalidad está en la clase base
    
    // Alias para mantener compatibilidad con código existente
    public function obtenerCarta($id)
    {
        return $this->obtenerDocumento($id);
    }
    
    public function insertarCarta()
    {
        return $this->insertarDocumento();
    }
    
    public function actualizarCarta()
    {
        return $this->actualizarDocumento();
    }
    
    public function eliminarCarta($id)
    {
        return $this->eliminarDocumento($id);
    }
    
    public function listarCartas($filtro = null, $tipo_busqueda = null)
    {
        return $this->listarDocumentos($filtro, $tipo_busqueda);
    }
    
    public function obtenerTiposCartas()
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