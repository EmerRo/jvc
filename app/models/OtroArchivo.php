<?php
// app/models/Carta.php

require_once "app/models/BaseDocumento.php";

class OtroArchivo extends BaseDocumento
{
    protected $tableName = 'otros_archivos';
    protected $motivo;

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    protected function mapearDatos($fila)
    {
        parent::mapearDatos($fila);
        $this->motivo = $fila['motivo'] ?? null;
    }
    
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerOtroArchivo($id)
    {
        return $this->obtenerDocumento($id);
    }
    
    public function insertarOtroArchivo()
    {
        return $this->insertarDocumento();
    }

    protected function buildInsertQuery()
    {
        return "INSERT INTO {$this->tableName} (titulo, tipo, motivo, id_cliente, usuario_id, contenido, header_image, footer_image, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }

    protected function bindInsertParams($stmt)
    {
        $stmt->bind_param("sssiissss", 
            $this->titulo, 
            $this->tipo, 
            $this->motivo,
            $this->id_cliente,
            $this->usuario_id, 
            $this->contenido, 
            $this->header_image, 
            $this->footer_image, 
            $this->estado
        );
    }

    public function actualizarOtroArchivo()
    {
        return $this->actualizarDocumento();
    }

    protected function buildUpdateQuery()
    {
        return "UPDATE {$this->tableName} 
                SET titulo = ?, tipo = ?, motivo = ?, id_cliente = ?, contenido = ?, header_image = ?, footer_image = ?, estado = ? 
                WHERE id = ?";
    }

    protected function bindUpdateParams($stmt)
    {
        $stmt->bind_param("sssissssi", 
            $this->titulo, 
            $this->tipo, 
            $this->motivo,
            $this->id_cliente, 
            $this->contenido, 
            $this->header_image, 
            $this->footer_image, 
            $this->estado, 
            $this->id
        );
    }
    
    public function eliminarOtroArchivo($id)
    {
        return $this->eliminarDocumento($id);
    }
    
    public function listarOtrosArchivos($filtro = null, $tipo_busqueda = null)
    {
        return $this->listarDocumentos($filtro, $tipo_busqueda);
    }

    public function obtenerTiposOtrosArchivos()
    {
        return $this->obtenerTiposDocumentos();
    }

    // Getter específico para otros archivos (compatibilidad)
    public function getIdUsuario()
    {
        return $this->usuario_id;
    }
    
    public function setIdUsuario($id_usuario)
    {
        $this->usuario_id = $id_usuario;
    }
}