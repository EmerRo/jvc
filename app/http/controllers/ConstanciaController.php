<?php
// app/controllers/ConstanciaController.php

require_once "app/models/Constancia.php";
require_once "app/models/BasePlantilla.php";
require_once "app/models/TipoConstancia.php";
require_once "app/http/controllers/ConstanciaPDF.php";
require_once "app/http/controllers/BaseDocumentoController.php";

// Plantilla específica para constancias


class ConstanciaController extends BaseDocumentoController
{
    protected $documentType = 'constancia';

    public function __construct()
    {
        parent::__construct();
        $this->modelo = new Constancia();
        $this->plantilla = new ConstanciaPlantilla();
        $this->pdfGenerator = new ConstanciaPDF();
        $this->tipoModelo = new TipoConstancia();
    }

    // Métodos específicos para constancias que usan nombres diferentes
    public function obtenerTipoConstancias()
    {
        return $this->obtenerTiposDocumentos();
    }

    public function insertarTipoConstancia()
    {
        return $this->insertarTipoDocumento();
    }

    public function editarTipoConstancia()
    {
        return $this->editarTipoDocumento();
    }

    public function eliminarTipoConstancia()
    {
        return $this->eliminarTipoDocumento();
    }
}