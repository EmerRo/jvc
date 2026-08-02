<?php
// app/http/controllers/OtroArchivoController.php

require_once "app/models/OtroArchivo.php";
require_once "app/models/OtroArchivoPlantilla.php";
require_once "app/http/controllers/Reportes/OtroArchivoPDF.php";
require_once "app/models/TipoOtrosArchivos.php";
require_once "app/http/controllers/BaseDocumentoController.php";

class OtroArchivoController extends BaseDocumentoController
{

    protected $documentType = 'otroArchivo';


   
       public function __construct()
    {
        parent::__construct();
        $this->modelo = new OtroArchivo();
        $this->plantilla = new OtroArchivoPlantilla();
        $this->pdfGenerator = new OtroArchivoPDF();
        $this->tipoModelo = new TipoOtrosArchivos();
    }

    public function obtenerTiposOtrosArchivos()
    {
        return $this->obtenerTiposDocumentos();
    }

    public function insertarTipoOtroArchivo()
    {
        return $this->insertarTipoDocumento();
    }

    public function editarTipoOtroArchivo()
    {
        return $this->editarTipoDocumento();
    }

    public function eliminarTipoOtroArchivo()
    {
        return $this->eliminarTipoDocumento();
    }
    public function obtenerTipoOtroArchivos()
    {
        return $this->obtenerTiposOtrosArchivos();
    }

}